<?php
namespace Desymphony\Woo;

use Desymphony\Helpers\DS_Utils;

defined( 'ABSPATH' ) || exit;

/**
 * Purchasable “Member Slot” product for Solo Exhibitors.
 */
class DS_Woo_CoEx_Slots {

	/** DB option that stores the product ID */
	private const OPT_KEY = 'ds_coex_slot_product_id';

	/** Price (net, EUR) */
	private const PRICE   = 70.00;

	/* -------------------------------------------------------------------------
	 * Boot
	 * ---------------------------------------------------------------------- */
	public static function init(): void {

		add_action( 'init', [ __CLASS__, 'maybe_create_product' ] );

		add_filter( 'woocommerce_add_to_cart_validation',
			[ __CLASS__, 'validate_add_to_cart' ], 10, 3 );

		add_filter( 'woocommerce_add_to_cart_redirect',
			[ __CLASS__, 'redirect_to_checkout' ], 10, 2 );

		add_filter( 'woocommerce_quantity_input_args',
			[ __CLASS__, 'qty_input_args'      ], 10, 2 );

		add_action( 'woocommerce_order_status_completed',
			[ __CLASS__, 'order_completed' ] );

		add_action( 'template_redirect', [ __CLASS__, 'handle_direct_buy' ], 1 );
	}

	/* -------------------------------------------------------------------------
	 *  Product bootstrap
	 * ---------------------------------------------------------------------- */
	private static function product_exists(): bool {
		return (int) get_option( self::OPT_KEY, 0 ) > 0;
	}

	public static function maybe_create_product(): void {

		if ( self::product_exists() ) {
			return;
		}

		$id = wp_insert_post( [
			'post_title'   => 'Member Slot',
			'post_status'  => 'publish',
			'post_type'    => 'product',
			'post_content' => 'Additional member invitation slots.',
		] );

		if ( $id && ! is_wp_error( $id ) ) {
			update_post_meta( $id, '_regular_price', self::PRICE );
			update_post_meta( $id, '_price',         self::PRICE );
			update_post_meta( $id, '_virtual',       'yes' );
			update_post_meta( $id, '_sold_individually', 'no' );
			update_post_meta( $id, '_tax_status',    'taxable' );
			update_option( self::OPT_KEY, $id );
		}
	}

	public static function get_product_id(): int {
		return (int) get_option( self::OPT_KEY );
	}

	/* -------------------------------------------------------------------------
	 *  Cart / Checkout validation
	 * ---------------------------------------------------------------------- */
	public static function validate_add_to_cart( $passed, $pid, $qty ): bool {

		if ( $pid !== self::get_product_id() ) {
			return $passed;
		}

		$user_id = get_current_user_id();
		if ( ! DS_Utils::is_exhibitor( $user_id ) ) {
			wc_add_notice( __( 'Only exhibitors may purchase member slots.', 'wv-addon' ), 'error' );
			return false;
		}

		$participation = DS_Utils::get_exhibitor_participation( $user_id );
		if ( $participation !== 'Solo Exhibitor' ) {
			wc_add_notice( __( 'You already have unlimited member slots.', 'wv-addon' ), 'error' );
			return false;
		}

		/* block repeat purchases completely */
		$purchased = (int) get_user_meta( $user_id, 'wv_coex_slots_purchased', true );
		if ( $purchased > 0 ) {
			wc_add_notice( __( 'You have already completed your slot purchase.', 'wv-addon' ), 'error' );
			return false;
		}

		$max       = DS_Utils::solo_ex_max_slots( $user_id );       // 1 or 2
		$in_cart   = self::quantity_in_cart();

		if ( $qty + $in_cart > $max ) {
			wc_add_notice(
				sprintf( __( 'You may purchase up to %d slot(s) in total.', 'wv-addon' ), $max ),
				'error'
			);
			return false;
		}
		return $passed;
	}

	/**
	 * Redirect straight to checkout after product is added.
	 */
	public static function redirect_to_checkout( $url, $added_id ) {
		return $added_id === self::get_product_id() ? wc_get_checkout_url() : $url;
	}

	/**
	 * Constrain quantity selector on the product page.
	 */
	public static function qty_input_args( $args, $product ) {

		if ( $product->get_id() !== self::get_product_id() ) {
			return $args;
		}

		$user_id   = get_current_user_id();
		$max       = DS_Utils::solo_ex_max_slots( $user_id );
		$purchased = (int) get_user_meta( $user_id, 'wv_coex_slots_purchased', true );

		$args['max_value'] = max( 1, $max - $purchased );
		$args['min_value'] = 1;

		return $args;
	}

	private static function quantity_in_cart(): int {
		if ( ! WC()->cart ) return 0;
		foreach ( WC()->cart->get_cart() as $item ) {
			if ( (int) $item['product_id'] === self::get_product_id() ) {
				return (int) $item['quantity'];
			}
		}
		return 0;
	}

	/* -------------------------------------------------------------------------
	 *  Fulfilment: add slots to user meta
	 * ---------------------------------------------------------------------- */
	public static function order_completed( $order_id ): void {

		$order = wc_get_order( $order_id );
		if ( ! $order ) return;

		$user_id = $order->get_user_id();
		if ( ! $user_id ) return;

		$added = 0;
		foreach ( $order->get_items() as $item ) {
			if ( (int) $item->get_product_id() === self::get_product_id() ) {
				$added += (int) $item->get_quantity();
			}
		}

		if ( $added > 0 ) {
			update_user_meta( $user_id, 'wv_coex_slots_purchased', $added );			

			$user   = get_user_by( 'id', $user_id );
			$to     = $user ? $user->user_email : '';
			$name   = $user ? $user->display_name : '';

			/* --- invoice figures ------------------------------------------------ */
			$net   = 70.00 * $added;
			$vat   = $net * 0.20;
			$gross = $net + $vat;

			/* --- e‑mail body ---------------------------------------------------- */
			[$subject, $html] = \Desymphony\Helpers\DS_Utils::email_template(
				/* SUBJECT */
				sprintf( __( 'Member‑Slot purchase confirmation (%d slot%s)', 'wv-addon' ),
						$added, $added === 1 ? '' : 's' ),

				/* HEADER BAR */
				[
					'title'        => __( 'Thank you for your purchase', 'wv-addon' ),
					'bg'           => '#0b051c',
					'logo_variant' => 'W',          // white logotype
				],

				/* MAIN CONTENT */
				[
					'title' => sprintf(
						/* translators: 1: user display name, 2: slot count */
						__( 'Dear %1$s, we have received your payment for %2$d member slot(s).',
						'wv-addon' ),
						esc_html( $name ),
						$added
					),

					'html' => sprintf(
						'<p>%s</p>
						<table role="presentation" cellpadding="0" cellspacing="0" style="margin:24px 0 0">
						<tr>
							<td style="padding: 4px 12px 4px 0;">%s × %s&nbsp;€</td>
							<td align="right" style="padding: 4px 0 4px 12px;">%s&nbsp;€</td>
						</tr>
						<tr>
							<td style="padding: 4px 12px 4px 0;">%s&nbsp;20%%</td>
							<td align="right" style="padding: 4px 0 4px 12px;">%s&nbsp;€</td>
						</tr>
						<tr>
							<td style="font-weight:700; padding: 8px 12px 8px 0;">%s</td>
							<td align="right" style="font-weight:700; padding: 8px 0 8px 12px;">%s&nbsp;€</td>
						</tr>
						</table>',
						__( 'Your invitation quota has been increased automatically – you can send new invites right away from your dashboard.',
						'wv-addon' ),
						esc_html( $added ),
						number_format_i18n( 70, 2 ),
						number_format_i18n( $net, 2 ),
						__( 'VAT', 'wv-addon' ),
						number_format_i18n( $vat, 2 ),
						__( 'Total charged', 'wv-addon' ),
						number_format_i18n( $gross, 2 )
					),

					'note'           => __( 'Need help? Reply to this e‑mail and our team will assist you.',
											'wv-addon' ),
					'btn_text'       => __( 'Go to dashboard', 'wv-addon' ),
					'btn_link'       => home_url( '/wv-co-ex/' ),
					'btn_bg'         => '#6e0fd7',
					'btn_text_color' => '#ffffff',
				]
			);

			/* --- dispatch ------------------------------------------------------- */
			wp_mail( $to, $subject, $html, [ 'Content-Type: text/html; charset=UTF-8' ] );
		}
	}

	/* ---------------------------------------------------------------------
     *  QUICK‑BUY  ( ?coex_slot_qty=1|2 )
     * ------------------------------------------------------------------ */
	public static function handle_direct_buy(): void {

		if ( ! isset( $_GET['coex_slot_qty'] ) ) {
			return;                       // nothing to do
		}

		// must be logged‑in Solo Exhibitor
		$user_id = get_current_user_id();
		if ( ! DS_Utils::is_exhibitor( $user_id )
			 || DS_Utils::get_exhibitor_participation( $user_id ) !== 'Solo Exhibitor' ) {
			wp_safe_redirect( home_url( '/' ) );
			exit;
		}

		$qty = (int) $_GET['coex_slot_qty'];
		$qty = min( max( $qty, 1 ), DS_Utils::solo_ex_max_slots( $user_id ) ); // clamp 1–2

		// empty & repopulate the cart
		if ( function_exists( 'WC' ) && $qty > 0 ) {
			WC()->cart->empty_cart();
			$added = WC()->cart->add_to_cart( self::get_product_id(), $qty );

			// make the change visible to the very next page‑load
			if ( $added ) {
				WC()->cart->calculate_totals();   // totals / taxes
				WC()->cart->set_session();        // flush to the session table
			}
		}

		// ❷ If we’re already on the Checkout page avoid a loop
		if ( is_checkout() ) {
			return;
		}

		wp_safe_redirect( wc_get_checkout_url() );
		exit;
	}
}
