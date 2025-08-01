<?php
namespace Desymphony\Woo;

use WC_Order;
use WC_Order_Query;

defined( 'ABSPATH' ) || exit;

/**
 * Handle the “/failed/” return URL after an aborted payment.
 *
 * • Any of the current user’s *pending* orders are marked “failed”.
 * • User is then redirected to /wv-dashboard/.
 */
class DS_Woo_Failed_Return {

	/* -----------------------------------------------------------------
	 * Boot
	 * ---------------------------------------------------------------- */
	public static function boot() : void {
		// runs early enough to catch the request yet late enough
		// for WooCommerce + user session to be ready
		add_action( 'template_redirect', [ __CLASS__, 'maybe_handle' ], 20 );
	}

	/* -----------------------------------------------------------------
	 * Main handler
	 * ---------------------------------------------------------------- */
	public static function maybe_handle() : void {

		/* 1. Are we on /failed/? ------------------------------------ */
		if ( ! self::is_failed_endpoint() ) {
			return;                    // nope – let WP load normally
		}

		/* 2. Logged‑in customer? ------------------------------------ */
		$user_id = get_current_user_id();
		if ( ! $user_id ) {
			self::redirect();          // still send them away
		}

		/* 3. Grab all *pending* orders ------------------------------ */
		$orders = wc_get_orders( [
			'customer_id' => $user_id,
			'status'      => 'pending',
			'limit'       => -1,
		] );

		/* 4. Flip each to “failed” ---------------------------------- */
		foreach ( $orders as $order ) {
			/** @var WC_Order $order */
			$order->update_status(
				'failed',
				__( 'Marked failed after customer returned to /failed/.', 'desymphony' ),
				true               // trigger notifications if any
			);
		}

		/* 5. Off you go --------------------------------------------- */
		self::redirect();
	}

	/* -----------------------------------------------------------------
	 * Helpers
	 * ---------------------------------------------------------------- */

	/** True when the current request URL is exactly “/failed/”. */
	private static function is_failed_endpoint() : bool {
		global $wp;
		return trim( $wp->request, '/' ) === 'failed';
	}

	/** Safe redirect and end execution. */
	private static function redirect() : void {
		wp_safe_redirect( home_url( '/wv-dashboard/' ), 302 );
		exit;
	}
}
