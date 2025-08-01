<?php
namespace Desymphony\CLI;

use WP_User;
use WP_CLI;

/**
 * Usage:  wp ds seed-demo
 *
 * Creates:
 *   • 1 Head, 1 Solo, 3 Co‑Ex users
 *   • 6 stand products spread across sizes (hall 2C)
 *   • completed orders so Woo marks them “sold”
 *   • pending invites so the Head exhibitor can assign
 */
class DS_CLI_Seed_Demo {

	public static function register(): void {
		WP_CLI::add_command( 'ds seed-demo', [ __CLASS__, 'handle' ] );
	}

	public static function handle(): void {

		/* ── users ───────────────────────────────────────────── */
		$head = self::user( 'head@example.com', 'Head', 'Exhib', 'Head Exhibitor' );
		$solo = self::user( 'solo@example.com', 'Solo', 'Exhib', 'Solo Exhibitor' );

		$co1  = self::user( 'co1@example.com', 'Co', 'One', '' );
		$co2  = self::user( 'co2@example.com', 'Co', 'Two', '' );
		$co3  = self::user( 'co3@example.com', 'Co', 'Three', '' );

		/* ── products ───────────────────────────────────────── */
		$stand_ids = [];
		foreach ( [  [ '40', '49' ], [ '48', '24' ], [ '22', '12' ], [ '30', '9' ] ] as $pair ) {
			[$no,$size] = $pair;
			$stand_ids[] = self::stand_product( $no, $size );   // hall 2C
		}

		/* ── orders (1 stand each) ─────────────────────────── */
		self::complete_order( $head->ID, $stand_ids[0] );      // 49 m²
		self::complete_order( $solo->ID, $stand_ids[1] );      // 24 m²

		/* ── invites ───────────────────────────────────────── */
		$repo = new \Desymphony\Database\DS_CoEx_Repository();
		foreach ( [ $co1, $co2, $co3 ] as $u ) {
			$token = wp_generate_password( 32, false, false );
			$repo->insert_invite( $head->ID, $u->user_email, $token );
			$repo->update_invite( $repo->wpdb->insert_id, [
				'status' => 'accepted',
				'co_id'  => $u->ID,
			] );
		}

		WP_CLI::success( 'Demo data created.' );
	}

	/* ---------- helpers ---------- */

	private static function user( $mail, $first, $last, $role ) : WP_User {
		if ( $u = get_user_by( 'email', $mail ) ) return $u;

		$id = wp_insert_user( [
			'user_login' => $mail,
			'user_email' => $mail,
			'user_pass'  => 'demo1234',
			'first_name' => $first,
			'last_name'  => $last,
			'role'       => 'exhibitor',
		] );
		if ( $role ) update_user_meta( $id, 'wv_participationModel', $role );
		return get_user_by( 'ID', $id );
	}

	private static function stand_product( $no, $size ) : int {
		$title = "Hall 2C Stand {$no}";
		$id = wp_insert_post( [
			'post_title'  => $title,
			'post_status' => 'publish',
			'post_type'   => 'product',
		] );
		wp_set_object_terms( $id, 'stand', 'product_cat' );
		update_post_meta( $id, 'wv_hall_only',  '2C' );
		update_post_meta( $id, 'wv_stand_no',   $no );
		update_post_meta( $id, 'wv_stand_size', $size );
		update_post_meta( $id, 'wv_stand_status', 'sold' ); // will be overwritten by order
		return $id;
	}

	private static function complete_order( $user_id, $product_id ) : void {
		$order = wc_create_order( [ 'customer_id' => $user_id ] );
		$order->add_product( wc_get_product( $product_id ), 1 );
		$order->calculate_totals();
		$order->set_status( 'completed' );
		$order->save();
	}
}

DS_CLI_Seed_Demo::register();
