<?php
/**
 * Cancel + house‑keep ageing orders (pending, failed, etc.).
 *
 *  • Manual trigger:  /wp-admin/admin-post.php?action=ds_clean_pending&minutes=60&_wpnonce=<nonce>
 *  • Scheduled job  : runs hourly via Action Scheduler (hooked on woocommerce_init).
 *
 * @package Desymphony\Woo
 */

namespace Desymphony\Woo;

use WC_Order;
use WP_CLI;

defined( 'ABSPATH' ) || exit;

class DS_Woo_Order_Cleanup {

    public static function init() {      
		self::boot();                   
	}   

	/** Minimum WC status set we treat as “unpaid / can be released”. */
	private const TARGET_STATUSES = [ 'pending', 'failed' ];

	/** Boot once – called from DS_Main. */
	public static function boot() {

		// ① admin‑post endpoint (manual button / URL)
		add_action( 'admin_post_ds_clean_pending', [ __CLASS__, 'handle_manual_request' ] );

		// ② schedule recurring scan **after** Action Scheduler is ready
		add_action( 'woocommerce_init', [ __CLASS__, 'maybe_schedule_recurring_job' ] );

		// ③ callback that the cron job calls
		add_action( 'ds_cleanup_stale_orders',  [ __CLASS__, 'run_cleanup' ] );
	}

	/* --------------------------------------------------------------------- *
	 *  ADMIN‑POST  (manual trigger – protected by nonce & caps)
	 * --------------------------------------------------------------------- */
	public static function handle_manual_request() {

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_die( __( 'You are not allowed to do this.', 'ds-theme' ), 403 );
		}

		check_admin_referer( 'ds_clean_pending' );

		$minutes = max( 1, absint( $_GET['minutes'] ?? 60 ) );
		self::run_cleanup( $minutes, true );        // true = echo summary

		exit;
	}

	/* --------------------------------------------------------------------- *
	 *  CRON WRAPPER
	 * --------------------------------------------------------------------- */
	public static function maybe_schedule_recurring_job() {

		if ( ! function_exists( 'as_has_scheduled_action' ) ) {
			return;                                 // Action Scheduler missing
		}
	}

	/* --------------------------------------------------------------------- *
	 *  CORE CLEANER  – can be called by cron or manually
	 * --------------------------------------------------------------------- */
	public static function run_cleanup( $minutes = 60, $verbose = false ) {

		global $wpdb;

		$threshold = gmdate( 'Y-m-d H:i:s', time() - ( $minutes * MINUTE_IN_SECONDS ) );

		$placeholders = implode( ',', array_fill( 0, count( self::TARGET_STATUSES ), '%s' ) );

		$query = $wpdb->prepare(
			"SELECT ID
			   FROM {$wpdb->posts}
			  WHERE post_type   = 'shop_order'
			    AND post_status IN ($placeholders)
			    AND post_date    < %s",
			[ ...self::TARGET_STATUSES, $threshold ]
		);

		$order_ids = $wpdb->get_col( $query );

		$cancelled = 0;

		foreach ( $order_ids as $oid ) {

			$order = wc_get_order( $oid );
			if ( ! $order instanceof WC_Order ) {
				continue;
			}

			// Cancel + release stands / custom meta only ONCE
			if ( ! $order->has_status( self::TARGET_STATUSES ) ) {
				continue;
			}

			$order->update_status( 'cancelled', __( 'Automatically cancelled – payment timeout.', 'ds-theme' ), true );
			$cancelled ++;
		}

		if ( $verbose || defined( 'WP_CLI' ) ) {
			$msg = sprintf( '[DS_Woo_Order_Cleanup] cancelled %d stale orders (older than %d min).', $cancelled, $minutes );
			error_log( $msg );
			if ( defined( 'WP_CLI' ) && WP_CLI ) {
				\WP_CLI::success( $msg );
			} else {
				echo esc_html( $msg );
			}
		}
	}

}
