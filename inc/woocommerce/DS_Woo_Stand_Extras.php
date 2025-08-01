<?php
/**
 * Stand extras – add‑ons, dual‑currency & mail (Woo Blocks‑ready).
 *
 * © Desymphony 2025
 */
namespace Desymphony\Woo;

use WC_Cart;
use WC_Order;
use WP_REST_Request;
use Desymphony\Helpers\DS_Utils as Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class DS_Woo_Stand_Extras {

	/* ───────────────────────── Boot ───────────────────────── */

	public static function init() : void {

		/* 0.  Compatibility shim – Raiffeisen may POST via GET */
		add_action( 'plugins_loaded', [ __CLASS__, 'shim_ecc_get_to_post' ], 5 );

		/* 1.  Functional hooks (unchanged)                     */
		add_filter( 'woocommerce_get_item_data',                      [ __CLASS__, 'cart_item_addons' ], 10, 2 );
		add_filter( 'woocommerce_order_item_get_formatted_meta_data', [ __CLASS__, 'order_item_meta' ], 10, 2 );
		add_action( 'woocommerce_before_calculate_totals',            [ __CLASS__, 'fix_cart_labels' ] );
		add_action( 'woocommerce_before_calculate_totals',            [ __CLASS__, 'apply_addon_price' ], 15 );

		add_filter( 'woocommerce_cart_totals_subtotal_html',          [ __CLASS__, 'dual_html' ] );
		add_filter( 'woocommerce_cart_totals_taxes_total_html',       [ __CLASS__, 'dual_html' ] );
		add_filter( 'woocommerce_cart_totals_order_total_html',       [ __CLASS__, 'dual_html' ] );

		add_filter( 'woocommerce_store_api_cart_response',            [ __CLASS__, 'store_api_rewrite_prices' ], 15, 2 );
		add_action( 'wp_footer',                                      [ __CLASS__, 'footer_patch_totals' ], 120 );

		add_action( 'woocommerce_admin_order_totals_after_tax',       [ __CLASS__, 'admin_dual_totals' ] );

		add_filter( 'woocommerce_email_classes',                      [ __CLASS__, 'register_email_class' ] );
		add_action( 'woocommerce_init',                               [ __CLASS__, 'ensure_email' ], 0 );
	}

	/* ───────────── 0‑a) GET→POST shim ───────────── */

	public static function shim_ecc_get_to_post() : void {
		if ( isset( $_GET['OrderID'] ) && ! isset( $_POST['OrderID'] ) ) {
			foreach ( [
				'MerchantID', 'TerminalID', 'OrderID', 'Currency', 'TotalAmount',
				'XID', 'PurchaseTime', 'ApprovalCode', 'SD', 'TranCode',
			] as $key ) {
				if ( isset( $_GET[ $key ] ) ) {
					$_POST[ $key ] = sanitize_text_field( wp_unslash( $_GET[ $key ] ) );
				}
			}
		}
	}

	/* ───────────── Rate helper (EUR → RSD) ───────────── */

	public static function rate() : float {

		$cache = get_option( 'ds_eur_rsd_rate_cache', [] );
		if ( isset( $cache['rate'], $cache['ts'] )
		     && time() - $cache['ts'] < 12 * HOUR_IN_SECONDS ) {
			return (float) $cache['rate'];
		}

		$url  = 'https://api.exchangerate.host/latest?base=EUR&symbols=RSD';
		$res  = wp_remote_get( $url, [ 'timeout' => 10 ] );

		$rate = Utils::eur_to_rsd();
		error_log( $rate );
		if ( ! is_wp_error( $res ) && 200 === wp_remote_retrieve_response_code( $res ) ) {
			$data = json_decode( wp_remote_retrieve_body( $res ), true );
			if ( isset( $data['rates']['RSD'] ) ) {
				$rate = (float) $data['rates']['RSD'];
			}
		}
		update_option( 'ds_eur_rsd_rate_cache',
			[ 'rate' => $rate, 'ts' => time() ], false );

		return $rate;
	}

	/* ───────────── Add‑ons / cart / blocks ───────────── */

	private static function label_for( string $slug ) : string {
		static $map = null;
		if ( $map === null ) {
			$all = array_merge(
				DS_Woo_Stand_Addons::get_addons_for_size( 9 ),
				DS_Woo_Stand_Addons::get_addons_for_size( 15 )
			);
			foreach ( $all as $a ) { $map[ $a['slug'] ] = $a['label']; }
		}
		return $map[ $slug ] ?? $slug;
	}

	public static function cart_item_addons( array $out, array $ci ) : array {
		foreach ( $ci['stand_addons'] ?? [] as $a ) {
			if ( $a['qty'] > 0 ) {
				$out[] = [
					'name'  => sprintf( '%s × %d', self::label_for( $a['slug'] ), $a['qty'] ),
					'value' => wc_price( $a['price'] * $a['qty'] ),
				];
			}
		}
		return $out;
	}

	public static function order_item_meta( array $meta, \WC_Order_Item $it ) : array {
		foreach ( $it->get_meta( 'stand_addons', true ) ?: [] as $a ) {
			if ( $a['qty'] > 0 ) {
				$l  = self::label_for( $a['slug'] );
				$fv = sprintf( '%d × %s', $a['qty'], wc_format_localized_price( $a['price'] ) );
				$meta[] = (object) [
					'key' => $l, 'display_key' => $l,
					'value' => $fv, 'display_value' => $fv,
				];
			}
		}
		return $meta;
	}

	public static function fix_cart_labels( $cart ) : void {
		if ( ! $cart instanceof WC_Cart ) return;
		foreach ( $cart->get_cart() as $k => $ci ) {
			foreach ( $ci['stand_addons'] ?? [] as &$a ) {
				$a['label'] = self::label_for( $a['slug'] );
			}
			$cart->cart_contents[ $k ] = $ci;
		}
	}

	public static function apply_addon_price( WC_Cart $cart ) : void {
		foreach ( $cart->get_cart() as $ci ) {
			if ( ! empty( $ci['stand_addons'] ) ) {
				$base = (float) $ci['data']->get_regular_price();
				$add  = array_sum( array_map(
					static fn( $a ) => $a['price'] * $a['qty'], $ci['stand_addons']
				) );
				$ci['stand_base_price'] = $base;
				$ci['data']->set_price( $base + $add );
			}
		}
	}

	public static function dual_html( string $html ) : string {
		$eur = floatval( wp_strip_all_tags( $html ) );
		return $eur ? $html . '<br><small style="opacity:.75;">(≈ ' .
		       number_format_i18n( $eur * self::rate(), 2 ) . '&nbsp;RSD)</small>' : $html;
	}

	public static function store_api_rewrite_prices( array $resp, WP_REST_Request $req ) : array {
		$cart = WC()->cart;
		if ( ! $cart || empty( $resp['items'] ) ) return $resp;

		foreach ( $resp['items'] as &$it ) {
			if ( isset( $it['key'] ) && ( $ci = $cart->get_cart_item( $it['key'] ) )
			     && isset( $ci['stand_base_price'] ) ) {

				$rent = (float) $ci['stand_base_price'];
				foreach ( [ 'price','regular_price','subtotal','total','single' ] as $f ) {
					if ( isset( $it['prices'][ $f ] ) ) {
						$it['prices'][ $f ] = $rent;
					}
				}
				if ( isset( $it['prices']['raw_prices']['precision'] ) ) {
					foreach ( [ 'price','subtotal','total' ] as $f ) {
						$it['prices']['raw_prices'][ $f ] = $rent;
					}
					$it['prices']['raw_prices']['subtotal_tax'] = 0;
					$it['prices']['raw_prices']['total_tax']    = 0;
				}
			}
		}
		return $resp;
	}

	public static function footer_patch_totals() : void {
		if ( ! is_checkout() && ! is_cart() ) return;
		$rate = self::rate();
		?>
		<script>
		(() => {
			const RATE = <?php echo json_encode( $rate ); ?>;
			if ( ! RATE ) return;
			const rsd = n => n.toLocaleString('sr-RS',
					{minimumFractionDigits:2,maximumFractionDigits:2});
			const patch = () => {
				document.querySelectorAll('.wc-block-components-totals-item')
				.forEach(row => {
					if ( row.querySelector('.ds-rsd') ) return;
					const val = row.querySelector('.wc-block-components-totals-item__value');
					if ( ! val ) return;
					const num = parseFloat(
						val.textContent.replace(/[^0-9,\.]/g,'')
						               .replace(/\./g,'')
						               .replace(/,/g,'.') );
					if ( isNaN(num) ) return;
					const div = document.createElement('div');
					div.className = 'ds-rsd';
					div.style.cssText =
						'font-size:12px;opacity:.65;line-height:1;';
					div.textContent = '≈ ' + rsd(num * RATE) + ' RSD';
					val.after(div);
				});
			};
			window.addEventListener('load', patch, {once:true});
			document.body.addEventListener('wc-blocks_cart_update', patch);
			document.body.addEventListener('wc-blocks_checkout_update', patch);
			(new MutationObserver(patch))
				.observe(document.body,{childList:true,subtree:true});
		})();
		</script><?php
	}

	/* ───────────── Back‑office helper ───────────── */

	public static function admin_dual_totals( $order_id ) : void {
		$o = wc_get_order( $order_id ); if ( ! $o ) return;
		$r = self::rate();
		echo '<tr><td colspan="3"></td><td>';
		foreach ( [
			'Items Subtotal' => $o->get_subtotal(),
			'Tax'            => $o->get_total_tax(),
			'Order Total'    => $o->get_total(),
		] as $lbl => $eur ) {
			printf(
				'<div style="font-size:11px;opacity:.7;">%s<br>≈ %s RSD</div>',
				esc_html( $lbl ), number_format_i18n( $eur * $r, 2 )
			);
		}
		echo '</td></tr>';
	}

	/* ───────────── Mail helpers (unchanged) ───────────── */

	public static function register_email_class( $e ) {
		require_once __DIR__ . '/DS_Email_Stand_Order.php';
		$e['DS_Email_Stand_Order'] = new DS_Email_Stand_Order();
		return $e;
	}

	public static function ensure_email() : void {
		$m = WC()->mailer();
		if ( empty( $m->emails['DS_Email_Stand_Order'] ) ) {
			require_once __DIR__ . '/DS_Email_Stand_Order.php';
			$m->emails['DS_Email_Stand_Order'] = new DS_Email_Stand_Order();
		}
	}
}
