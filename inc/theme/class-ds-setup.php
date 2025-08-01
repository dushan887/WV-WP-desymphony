<?php
/**
 * Theme setup: supports, menus, templates, media enqueue.
 *
 * @package Desymphony
 */

namespace Desymphony\Theme;

use WP_REST_Request;
use WP_REST_Response;

defined( 'ABSPATH' ) || exit;

class DS_Setup {

	/**
	 * Hook into WP to register theme features.
	 */
	public static function init() {

		add_action( 'after_setup_theme',    [ __CLASS__, 'theme_supports' ] );
		add_action( 'after_setup_theme',    [ __CLASS__, 'register_menus' ] );
		add_filter( 'template_include',     [ __CLASS__, 'custom_templates' ] );
		add_action( 'admin_enqueue_scripts',[ __CLASS__, 'enqueue_media' ] );
		add_filter( 'upload_mimes',               [ __CLASS__, 'allow_svg_uploads' ] );
		add_filter( 'wp_check_filetype_and_ext',  [ __CLASS__, 'fix_svg_filetype' ], 10, 4 );

		add_action( 'init', [ __CLASS__, 'redirect_init' ] );
		add_action( 'pre_get_posts', [ __CLASS__, 'limit_search_to_posts_and_pages' ] );
		add_action( 'template_redirect', [ __CLASS__, 'redirect_stand_thankyou' ], 5 );

		/* Admin scope field in user profile */
		add_action( 'edit_user_profile', [ __CLASS__, 'render_admin_scope_field' ] );
		add_action( 'show_user_profile', [ __CLASS__, 'render_admin_scope_field' ] );
		/* Persist on save */
		add_action( 'personal_options_update', [ __CLASS__, 'save_admin_scope' ] );
		add_action( 'edit_user_profile_update', [ __CLASS__, 'save_admin_scope' ] );

		add_action( 'template_redirect', 'ds_block_woocommerce_pages', 999 );

		add_action( 'admin_init', [ __CLASS__, 'register_eur_rate_setting' ] );

		/* ➊  register CORS proxy for html2pdf / html2canvas ---------------- */
		add_action( 'rest_api_init', [ __CLASS__, 'register_cors_proxy_route' ] );
	}

	private const THANKYOU_SLUG = '/wv-application/';

	/* =======================================================================
	 *  0. Theme basics
	 * ===================================================================== */

	public static function theme_supports() {
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'automatic-feed-links' );
	}

	public static function register_menus() {
		register_nav_menus( [
			'primary' => __( 'Primary Menu', DS_THEME_TEXTDOMAIN ),
			'footer'  => __( 'Footer Menu', DS_THEME_TEXTDOMAIN ),
		] );
	}

	/* =======================================================================
	 *  1. Template overrides
	 * ===================================================================== */

	public static function custom_templates( $template ) {
		if ( is_home() || is_category() ) {
			$custom = get_template_directory() . '/templates/home.php';
			if ( file_exists( $custom ) ) {
				return $custom;
			}
		}

		if ( is_singular( 'post' ) ) {
			$custom = get_template_directory() . '/templates/single.php';
			if ( file_exists( $custom ) ) {
				return $custom;
			}
		}

		if ( is_search() ) {
			$custom = get_template_directory() . '/templates/search.php';
			if ( file_exists( $custom ) ) {
				return $custom;
			}
		}

		return $template;
	}

	/* =======================================================================
	 *  2. Admin enqueue / SVG mime helpers
	 * ===================================================================== */

	public static function enqueue_media( $hook ) {
		if ( in_array( $hook, [ 'post.php', 'post-new.php' ], true ) ) {
			wp_enqueue_media();
		}
	}

	public static function allow_svg_uploads( array $mimes ): array {
		$mimes['svg']  = 'image/svg+xml';
		$mimes['svgz'] = 'image/svg+xml';
		return $mimes;
	}

	public static function fix_svg_filetype( $data, $file, $filename, $mimes ) {
		if ( false !== strpos( $filename, '.svg' ) ) {
			$data['ext']  = 'svg';
			$data['type'] = 'image/svg+xml';
		}
		return $data;
	}

	/* =======================================================================
	 *  3. Front‑end redirects & search tweaks
	 * ===================================================================== */

	public static function redirect_init() {
		add_action( 'template_redirect', [ __CLASS__, 'account_redirect' ] );
	}

	public static function account_redirect() {
		$request_uri = untrailingslashit( $_SERVER['REQUEST_URI'] ?? '' );
		if ( '/my-account' === $request_uri ) {
			wp_redirect( home_url( '/wv-profile/' ) );
			exit;
		}
	}

	public static function limit_search_to_posts_and_pages( $query ) : void {
		if ( $query->is_main_query() && $query->is_search() && ! is_admin() ) {
			$query->set( 'post_type', [ 'post', 'page' ] );
		}
	}

	public static function redirect_stand_thankyou() : void {

		if ( ! function_exists( 'is_order_received_page' ) || ! is_order_received_page() ) {
			return;
		}

		if ( untrailingslashit( $_SERVER['REQUEST_URI'] ?? '' ) === untrailingslashit( self::THANKYOU_SLUG ) ) {
			return;
		}

		$order_id = absint( get_query_var( 'order-received' ) );
		if ( ! $order_id ) {
			return;
		}
		$order = wc_get_order( $order_id );
		if ( ! $order ) {
			return;
		}

		foreach ( $order->get_items() as $item ) {
			if ( has_term( 'stand', 'product_cat', $item->get_product_id() ) ) {
				wp_safe_redirect( home_url( self::THANKYOU_SLUG ), 302 );
				exit;
			}
		}
	}

	/* =======================================================================
	 *  4. Per‑user admin scope field
	 * ===================================================================== */

	public static function render_admin_scope_field( \WP_User $user ): void {

		if ( user_can( $user, 'administrator' ) ) {
			return;
		}

		if ( ! current_user_can( 'manage_wv_addon' ) && ! current_user_can( 'administrator' ) ) {
			return;
		}

		$scope = get_user_meta( $user->ID, 'wv_admin_scope', true ) ?: 'buyers_visitors';
		?>
		<h2><?php esc_html_e( 'Wine Vision admin permissions', DS_THEME_TEXTDOMAIN ); ?></h2>
		<table class="form-table" role="presentation">
			<tr>
				<th><label for="wv_admin_scope"><?php esc_html_e( 'Scope', DS_THEME_TEXTDOMAIN ); ?></label></th>
				<td>
					<select name="wv_admin_scope" id="wv_admin_scope">
						<option value="all"             <?php selected( $scope, 'all' ); ?>><?php esc_html_e( 'Can manage ALL', DS_THEME_TEXTDOMAIN ); ?></option>
						<option value="exhibitors"      <?php selected( $scope, 'exhibitors' ); ?>><?php esc_html_e( 'Can manage Exhibitors', DS_THEME_TEXTDOMAIN ); ?></option>
						<option value="buyers_visitors" <?php selected( $scope, 'buyers_visitors' ); ?>><?php esc_html_e( 'Can manage Buyers and Visitors', DS_THEME_TEXTDOMAIN ); ?></option>
					</select>
					<p class="description"><?php esc_html_e( 'Controls which user rows are visible/editable in the Admin Users table.', DS_THEME_TEXTDOMAIN ); ?></p>
				</td>
			</tr>
		</table>
		<?php
	}

	public static function save_admin_scope( int $user_id ): void {
		if ( ! current_user_can( 'manage_wv_addon' ) && ! current_user_can( 'administrator' ) ) {
			return;
		}

		if ( ! isset( $_POST['wv_admin_scope'] ) ) {
			return;
		}

		$scope = sanitize_text_field( $_POST['wv_admin_scope'] );
		if ( ! in_array( $scope, [ 'all', 'exhibitors', 'buyers_visitors' ], true ) ) {
			return;
		}

		update_user_meta( $user_id, 'wv_admin_scope', $scope );
	}

	/* =======================================================================
	 *  5.  REST CORS proxy for cross‑origin SVGs
	 * ===================================================================== */

	/**
	 * Registers GET /wp-json/ds/v1/cors?url=<encoded>
	 * Allows front‑end JS to fetch remote SVG/PNG/JPG and receive it
	 * with Access‑Control‑Allow-Origin:* so html2canvas can read pixels.
	 */
	public static function register_cors_proxy_route() : void {

		register_rest_route(
			'ds/v1',
			'/cors',
			[
				'methods'  => 'GET',
				'permission_callback' => '__return_true', // public, read‑only
				'args'     => [
					'url' => [
						'required' => true,
						'sanitize_callback' => 'esc_url_raw',
					],
				],
				'callback' => [ __CLASS__, 'cors_proxy_callback' ],
			]
		);
	}

	/**
	 * Callback: stream the remote file back with CORS header.
	 *
	 * @param WP_REST_Request $req
	 * @return WP_REST_Response
	 */
	public static function cors_proxy_callback( WP_REST_Request $req ) : WP_REST_Response {

		$url = $req->get_param( 'url' );
		if ( ! $url ) {
			return new WP_REST_Response( null, 400 );
		}

		$response = wp_remote_get( $url, [ 
            'timeout' => 15,
	        'sslverify' => false, ] );
		if ( is_wp_error( $response ) ) {
			return new WP_REST_Response( null, 502 );
		}

		$status = wp_remote_retrieve_response_code( $response );
		if ( 200 !== $status ) {
			return new WP_REST_Response( null, $status ?: 502 );
		}

		$body = wp_remote_retrieve_body( $response );
		$ct   = wp_remote_retrieve_header( $response, 'content-type' ) ?: 'application/octet-stream';

		return new WP_REST_Response(
			$body,
			200,
			[
				'Content-Type'                => $ct,
				'Access-Control-Allow-Origin' => '*',
				'Cache-Control'               => 'public, max-age=604800', // 7 days
			]
		);
	}

	/**
	 * EUR → RSD option (kept with four decimals).
	 */
	public static function register_eur_rate_setting() : void {

		register_setting(
			'desymphony_general_settings',   // must match <form> → settings_fields()
			'ds_eur_to_rsd_rate',
			[
				'type'              => 'string',           // store as string → exact 4 dec
				'default'           => '117.5337',
				'sanitize_callback' => static function ( $value ) : string {
					$value = str_replace( ',', '.', $value ); // allow “117,5283”
					return number_format( (float) $value, 4, '.', '' );
				},
			]
		);
	}

}
