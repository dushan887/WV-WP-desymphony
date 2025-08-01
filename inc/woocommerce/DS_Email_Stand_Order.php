<?php
namespace Desymphony\Woo;

use Desymphony\Helpers\DS_Utils;
use WC_Email;
use WC_Order;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Styled confirmation e‑mail for paid stand orders (customer + admin).
 * Stock Woo e‑mails are suppressed for these orders so nothing doubles up.
 */
class DS_Email_Stand_Order extends WC_Email {

	/* ───────────────────────────── Boot ──────────────────────────── */
	public function __construct() {

		$this->id             = 'ds_stand_order';
		$this->title          = 'Stand Order – Customer';
		$this->heading        = 'Your stand reservation';
		$this->customer_email = true;   // appears in Woo settings list
		$this->template_html  = '';
		$this->template_plain = '';
		$this->template_base  = '';

		/* Fire once the order is paid or manually marked as such */
		add_action( 'woocommerce_payment_complete',        [ $this, 'maybe_send' ], 20, 1 );
		add_action( 'woocommerce_order_status_processing', [ $this, 'maybe_send' ], 20, 2 );
		add_action( 'woocommerce_order_status_completed',  [ $this, 'maybe_send' ], 20, 2 );

		parent::__construct();

		/* Auto‑enable so merchant doesn’t have to tick the box */
		if ( ! $this->get_option( 'enabled' ) ) {
			$this->settings['enabled'] = 'yes';
			update_option( $this->get_option_key(), $this->settings );
		}

		/* ── Disable Woo stock e‑mails for stand orders ───────── */

		// 1. Customer “Completed order”
		add_filter(
			'woocommerce_email_enabled_customer_completed_order',
			[ $this, 'maybe_disable_default' ],
			20,
			2
		);

		// 2. Admin “New order”
		add_filter(
			'woocommerce_email_enabled_new_order',
			[ $this, 'maybe_disable_default' ],
			20,
			2
		);
	}

	/**
	 * Return false to switch off Woo’s own template when the order
	 * contains a stand product.
	 */
	public function maybe_disable_default( $enabled, $order ) {
		if ( ! $enabled || ! $order instanceof WC_Order ) {
			return $enabled;
		}
		foreach ( $order->get_items() as $it ) {
			if ( has_term( 'stand', 'product_cat', $it->get_product_id() ) ) {
				return false;
			}
		}
		return $enabled;
	}

	/* --------------------------------------------------------------
	 * Main sender
	 * ----------------------------------------------------------- */
	public function maybe_send( $order_or_id, $posted = [] ) {

		$order = is_a( $order_or_id, 'WC_Order' )
			? $order_or_id
			: wc_get_order( $order_or_id );
		if ( ! $order instanceof WC_Order ) {
			return;
		}

		/* ─── guard clauses ───────────────────────────────────── */
		if ( $order->get_meta( '_ds_stand_mail_sent', true ) ) {
			return;                               // already sent
		}
		if ( in_array( $order->get_status(), [ 'pending', 'failed', 'cancelled' ], true ) ) {
			return;                               // unpaid / aborted
		}
		$has_stand = false;
		foreach ( $order->get_items() as $it ) {
			if ( has_term( 'stand', 'product_cat', $it->get_product_id() ) ) {
				$has_stand = true;
				break;
			}
		}
		if ( ! $has_stand ) {
			return;                               // not a stand order
		}

		/* ─── build message parts ─────────────────────────────── */
		$rate          = DS_Woo_Stand_Extras::rate();
		$order_summary = $this->build_order_table( $order, $rate );
		$billing_block = $this->build_billing_block( $order );

		[ $subject, $html ] = DS_Utils::email_template(
			'Your stand reservation',
			[
				'title'        => 'Dear Exhibitor',
				'bg'           => '#0b051c',
				'logo_variant' => 'W',
			],
			[
				'title' => 'Thank you for your purchase!',
				'html'  =>
					'<p>Your stand order <strong>#' . $order->get_order_number() . '</strong> has been received.</p>' .
					$order_summary .
					$billing_block,
				'note'         => 'Want to make changes or view your details?<br><strong>Go back to your profile.</strong>',
				'btn_text'     => 'Go to profile',
				'btn_link'     => home_url( '/wv-profile/' ),
				'btn_bg'       => '#0b051c',
				'btn_text_color' => '#ffffff',
			]
		);

		/* ─── send once to customer ───────────────────────────── */
		$this->recipient = $order->get_billing_email();
		if ( $this->is_enabled() && $this->get_recipient() ) {
			$this->send( $this->get_recipient(), $subject, $html, $this->get_headers(), '' );
		}

		/* ─── send once to admin(s) (styled) ──────────────────── */
		$admin_recips = apply_filters(
			'ds_stand_order_admin_recipients',
			[ 'sales@winevisionfair.com', 'ob-winefair@sajam.rs' ]
		);
		foreach ( $admin_recips as $addr ) {
			if ( is_email( $addr ) ) {
				$this->send( $addr, 'New stand order #' . $order->get_order_number(), $html, $this->get_headers(), '' );
			}
		}

		$order->update_meta_data( '_ds_stand_mail_sent', 'yes' );
		$order->save_meta_data();
	}

	/* --------------------------------------------------------------
	 * Helper builders
	 * ----------------------------------------------------------- */

	private static function price_pair( float $eur, float $rate ) : string {
		return sprintf(
			'%s&nbsp;€ <span style="opacity:.65;">(≈ %s&nbsp;RSD)</span>',
			wc_format_localized_price( $eur ),
			number_format_i18n( $eur * $rate, 2 )
		);
	}

	private function build_order_table( WC_Order $order, float $rate ) : string {

		$rows = '';

		foreach ( $order->get_items() as $item ) {
			if ( ! has_term( 'stand', 'product_cat', $item->get_product_id() ) ) {
				continue;
			}

			$rows .= sprintf(
				'<tr><td>%s</td><td style="text-align:right;">%s</td></tr>',
				esc_html( $item->get_name() ),
				self::price_pair( $item->get_total(), $rate )
			);

			foreach ( $item->get_meta( 'stand_addons', true ) ?: [] as $a ) {
				if ( (int) $a['qty'] < 1 ) {
					continue;
				}
				$rows .= sprintf(
					'<tr>
						<td style="padding-left:16px;font-size:14px;">
							<strong>%s ×&nbsp;%d</strong>
						</td>
						<td style="text-align:right;font-size:14px;">
							%s
						</td>
					</tr>',
					esc_html( $a['label'] ?? $a['slug'] ),
					(int) $a['qty'],
					self::price_pair( $a['price'] * $a['qty'], $rate )
				);
			}
		}

		$rows .= '
			<tr><td colspan="2" style="border-top:1px solid #e0e0e0;height:8px;"></td></tr>
			<tr><td><strong>Subtotal</strong></td>
				<td style="text-align:right;">' . self::price_pair( $order->get_subtotal(),   $rate ) . '</td></tr>
			<tr><td><strong>Tax</strong></td>
				<td style="text-align:right;">' . self::price_pair( $order->get_total_tax(), $rate ) . '</td></tr>
			<tr>
				<td style="font-size:17px;"><strong>Total</strong></td>
				<td style="text-align:right;font-size:17px;"><strong>' .
					self::price_pair( $order->get_total(), $rate ) .
				'</strong></td>
			</tr>';

		return '<table cellspacing="0" cellpadding="6" style="width:100%;font-family:sans-serif;margin-bottom:24px;">' .
		       $rows .
		       '</table>';
	}

	private function build_billing_block( WC_Order $order ) : string {

		$lines = array_filter( [
			$order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
			$order->get_billing_company(),
			$order->get_billing_address_1(),
			$order->get_billing_address_2(),
			trim( $order->get_billing_postcode() . ' ' . $order->get_billing_city() ),
			wc()->countries->countries[ $order->get_billing_country() ] ?? '',
			$order->get_billing_phone(),
			$order->get_billing_email(),
		] );

		return '<h3 style="margin-top:24px;margin-bottom:8px;">Billing information</h3><p>' .
		       implode( '<br>', array_map( 'esc_html', $lines ) ) .
		       '</p>';
	}

	public function get_content_html()  { return ''; }
	public function get_content_plain() { return ''; }
}
