<?php
/**
 * Stand‑assignment service.
 *
 * • keeps product‑meta  `wv_assigned_users`    → array<int> user‑IDs (one slot = one element)
 * • keeps user‑meta     `wv_assigned_products` → array<int> product‑IDs
 *
 * All mutation goes through this class – never touch the meta keys directly.
 *
 * @package Desymphony
 */

namespace Desymphony\Dashboard;

use Desymphony\Helpers\DS_Utils;

defined( 'ABSPATH' ) || exit;

class DS_Stand_Assign {
	

	/* ──────────────────────────────────────────────────────────────
	 *  CONSTANTS
	 * ─────────────────────────────────────────────────────────── */
	private const META_KEY  = 'wv_assigned_users';
	private const USER_META = 'wv_assigned_products';

	/** How many user “slots” a stand of a given size provides */
	private const CAPACITY = [
		'9'      => 1,
		'12'     => 1,
		'24'     => 2,
		'49'     => 3,
		'custom' => 1,
	];

	/* ──────────────────────────────────────────────────────────────
	 *  BOOT
	 * ─────────────────────────────────────────────────────────── */
	public static function init(): void {

		// Purchaser auto‑assignment once the order is completed
		add_action( 'woocommerce_order_status_completed',
			[ __CLASS__, 'auto_assign_purchaser' ], 10, 1 );

		// Admin‑AJAX end‑points (dashboard only, logged‑in)
		add_action( 'wp_ajax_wv_get_stand_state',  [ __CLASS__, 'ajax_get_state'   ] );
		add_action( 'wp_ajax_wv_assign_stand',     [ __CLASS__, 'ajax_assign'      ] );
		add_action( 'wp_ajax_wv_unassign_stand',   [ __CLASS__, 'ajax_unassign'    ] );
	}

	/* ──────────────────────────────────────────────────────────────
	 *  INTERNAL HELPERS
	 * ─────────────────────────────────────────────────────────── */

	/* =======================================================================
	* Detect numeric stand size robustly, no matter how it was imported
	* ===================================================================== */
	private static function detect_size( int $product_id ) : int {

		/* 1) primary meta created by our import script or the back‑office  */
		$raw = get_post_meta( $product_id, 'wv_stand_size', true );

		/* 2) fall back to both attribute slugs, and to the low‑level meta
			that WP‑All‑Import sometimes leaves behind                   */
		if ( $raw === '' ) {
			$p = wc_get_product( $product_id );

			$candidates = [
				$p ? $p->get_attribute( 'pa_stand-size' )    : '',
				$p ? $p->get_attribute( 'pa_stand_size' )    : '',
				get_post_meta( $product_id, 'attribute_pa_stand-size', true ),
				get_post_meta( $product_id, 'attribute_pa_stand_size', true ),
			];

			foreach ( $candidates as $c ) {
				if ( $c !== '' ) { $raw = $c; break; }
			}
		}

		/* 3) extract *anything* that looks like a number – “49”, “49m²”,
			“ 49.0 ”, “49m2” … all become integer 49                     */
		if ( preg_match( '/(\d+)/', (string) $raw, $m ) ) {
			return (int) $m[1];
		}

		/* 4) unrecognised → treat as “custom” (capacity = 1)              */
		return 0;
	}

	/* =======================================================================
	* Map numeric size to capacity once and for all
	* ===================================================================== */
	private static function capacity_for( int $product_id ) : int {

		switch ( self::detect_size( $product_id ) ) {
			case 9 :
			case 12:  return 1;

			case 24:  return 2;

			case 49:  return 3;

			default:  return 1;   // “custom” or anything we don’t know
		}
	}




	private static function add_to_user( int $user_id, int $product_id ): void {
		$list   = get_user_meta( $user_id, self::USER_META, true );
		$list   = is_array( $list ) ? $list : [];
		$list[] = $product_id;
		update_user_meta( $user_id, self::USER_META, array_values( array_unique( $list ) ) );
	}

	private static function remove_from_user( int $user_id, int $product_id ): void {
		$list = get_user_meta( $user_id, self::USER_META, true );
		if ( ! is_array( $list ) ) {
			return;
		}

		$list = array_diff( $list, [ $product_id ] );

		// If the array is now empty → remove the key completely
		if ( empty( $list ) ) {
			delete_user_meta( $user_id, self::USER_META );
		} else {
			update_user_meta( $user_id, self::USER_META, array_values( $list ) );
		}
	}

	public static function get_assigned_users( int $product_id ): array {
		$raw = get_post_meta( $product_id, self::META_KEY, true );
		return is_array( $raw ) ? array_map( 'intval', $raw ) : [];
	}

	private static function save_assigned_users( int $product_id, array $ids ): void {
		update_post_meta(
			$product_id,
			self::META_KEY,
			array_values( array_unique( array_map( 'intval', $ids ) ) ),
		);
	}

	private static function has_free_slot( int $product_id ): bool {
		return count( self::get_assigned_users( $product_id ) ) < self::capacity_for( $product_id );
	}

	/* ──────────────────────────────────────────────────────────────
	 *  PUBLIC API
	 * ─────────────────────────────────────────────────────────── */

	/**
	 * Assign <user> to <product>.
	 *
	 * @return bool  true = success, false = rule violated / stand full.
	 */
	public static function assign_user( int $product_id, int $user_id ): bool {

		/* ────────────────────────────────────────────────────────────
		 *  One‑stand‑per‑Co‑Exhibitor rule
		 *  (ignore the *currently selected* stand so “share” works)
		 * ───────────────────────────────────────────────────────── */
		if ( DS_Utils::get_exhibitor_participation( $user_id ) === 'Co-Exhibitor' ) {
			// Filter out stale product‑IDs that no longer exist
			$other = array_filter(
				array_diff( self::stands_for_user( $user_id ), [ $product_id ] ),
				static fn ( $pid ) => get_post_status( $pid )
			);
			if ( $other ) {
				return false;                // still occupies another *valid* stand
			}
		}

		$list = self::get_assigned_users( $product_id );
		if ( in_array( $user_id, $list, true ) ) {
			return true;                    // already in – nothing to do
		}
		if ( ! self::has_free_slot( $product_id ) ) {
			return false;                   // stand is full
		}

		$list[] = $user_id;
		self::save_assigned_users( $product_id, $list );
		self::add_to_user( $user_id, $product_id );
		return true;
	}

	/**
	 * Remove <user> from <product>.
	 */
	public static function unassign_user( int $product_id, int $user_id ): void {

		self::save_assigned_users(
			$product_id,
			array_diff( self::get_assigned_users( $product_id ), [ $user_id ] )
		);
		self::remove_from_user( $user_id, $product_id );

		/* dashboard “Free up” must be able to clear the stand completely;
		   keep the fallback only when we are called from system hooks  */
		if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {
			self::auto_fill_if_empty( $product_id );
		}
	}

	/**
	 * All stand product‑IDs this user is currently assigned to.
	 */
	public static function stands_for_user( int $user_id = 0 ): array {
		$user_id = $user_id ?: get_current_user_id();
		$ids     = get_user_meta( $user_id, self::USER_META, true );
		return is_array( $ids ) ? array_map( 'intval', $ids ) : [];
	}

	/* ──────────────────────────────────────────────────────────────
	 *  AUTO‑ASSIGNMENT / FALLBACKS
	 * ─────────────────────────────────────────────────────────── */

	/**
	 * If a stand has **no** assigned users → put the reservation_user back in.
	 */
	public static function auto_fill_if_empty( int $product_id ): void {

		if ( self::get_assigned_users( $product_id ) ) {
			return;                         // already has someone
		}

		$owner = (int) get_post_meta( $product_id, 'wv_reservation_user', true );
		if ( $owner ) {
			self::assign_user( $product_id, $owner );
		}
	}

	/**
	 * Buyer is automatically assigned once an order is marked “Completed”.
	 */
	public static function auto_assign_purchaser( int $order_id ): void {

		$order = wc_get_order( $order_id );
		if ( ! $order ) {
			return;
		}

		$buyer_id = $order->get_customer_id();
		if ( ! $buyer_id ) {
			return;
		}

		foreach ( $order->get_items() as $it ) {
			$pid = $it->get_product_id();
			if ( has_term( 'stand', 'product_cat', $pid ) ) {
				self::assign_user( $pid, $buyer_id );
			}
		}
	}

	/* ──────────────────────────────────────────────────────────────
	 *  AJAX HELPERS
	 * ─────────────────────────────────────────────────────────── */

	private static function can_manage(): bool {
		return in_array(
			DS_Utils::get_exhibitor_participation(),
			[ 'Head Exhibitor', 'Solo Exhibitor' ],
			true
		);
	}

	public static function ajax_get_state(): void {
		check_ajax_referer( 'wv_dashboard_nonce', 'security' );
		if ( ! self::can_manage() ) {
			wp_send_json_error( [ 'msg' => 'Not allowed' ], 403 );
		}

		$pid = absint( $_POST['product_id'] ?? 0 );
		wp_send_json_success( [
			'users'    => self::get_assigned_users( $pid ),
			'capacity' => self::capacity_for( $pid ),
		] );
	}

	public static function ajax_assign(): void {
		check_ajax_referer( 'wv_dashboard_nonce', 'security' );
		if ( ! self::can_manage() ) {
			wp_send_json_error( [ 'msg' => 'Not allowed' ], 403 );
		}

		$pid = absint( $_POST['product_id'] ?? 0 );
		$uid = absint( $_POST['user_id']    ?? 0 );

		if ( ! $pid || ! $uid ) {
			wp_send_json_success( [ 'ok' => false, 'msg' => 'Missing data.' ] );
		}

		/* ---- business‑rule check handled by assign_user() ---- */
		$ok = self::assign_user( $pid, $uid );

		if ( ! $ok ) {
			$msg = self::has_free_slot( $pid )
				? 'A Co‑Exhibitor can occupy only one stand.'
				: 'Stand is already full.';
			wp_send_json_success( [ 'ok' => false, 'msg' => $msg ] );
		}

		wp_send_json_success( [ 'ok' => true ] );
	}

	public static function ajax_unassign(): void {
		check_ajax_referer( 'wv_dashboard_nonce', 'security' );
		if ( ! self::can_manage() ) {
			wp_send_json_error( [ 'msg' => 'Not allowed' ], 403 );
		}

		$pid = absint( $_POST['product_id'] ?? 0 );
		$uid = absint( $_POST['user_id']    ?? 0 );

		if ( ! $pid || ! $uid ) {
			wp_send_json_success( [ 'ok' => false, 'msg' => 'Missing data.' ] );
		}

		self::unassign_user( $pid, $uid );
		wp_send_json_success( [ 'ok' => true ] );
	}
}
