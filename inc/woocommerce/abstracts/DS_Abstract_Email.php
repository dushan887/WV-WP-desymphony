<?php
namespace Desymphony\Woo;

use WC_Email;
use WC_Order;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Base class for every Desymphony‑styled Woo e‑mail.
 *
 * Sub‑classes set:
 *   – $this->id, $title, $heading, $customer_email
 *   – hook their own triggers (add_action / add_filter) in __construct()
 *
 * Then call ->send_with_template( $order, $content_args ).
 */
abstract class DS_Abstract_Email extends WC_Email {

	/* Make the EUR + RSD string available to every child */
	protected static function price_pair( float $eur, float $rate ) : string {
		return sprintf(
			'%s&nbsp;€ <span style="opacity:.65;">(≈ %s&nbsp;RSD)</span>',
			wc_format_localized_price( $eur ),
			number_format_i18n( $eur * $rate, 2 )
		);
	}

	/**
	 * Send one styled e‑mail to any recipient without touching Woo templates.
	 *
	 * @param WC_Order $order
	 * @param array    $block_args  Arguments passed to DS_Utils::email_template()
	 *                              – must include keys 'subject' & 'html'.
	 * @param string   $to
	 */
	protected function send_with_template( WC_Order $order, array $block_args, string $to ) : void {

		[ $subject, $body ] = DS_Utils::email_template(
			$block_args['branding'] ?? 'WooCommerce',
			$block_args['header']   ?? [],
			$block_args['content']  ?? []
		);

		$this->send( $to, $subject, $body, $this->get_headers(), '' );
	}

	/* No file‑based templates needed */
	public function get_content_html()  { return ''; }
	public function get_content_plain() { return ''; }
}
