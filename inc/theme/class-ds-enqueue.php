<?php
namespace Desymphony\Theme;
use Desymphony\Helpers\DS_Utils;
defined( 'ABSPATH' ) || exit;

/**
 * Registers / enqueues public & admin assets.
 *
 *  • Keeps localisation objects DRY (no duplicate `wvDashboardData` calls)
 *  • Loads page‑specific bundles only when needed (auth, products, co‑exhibitors)
 *  • Adds the new `co‑ex.js` build created earlier
 */
class DS_Enqueue {

	/* -------------------------------------------------------------------------
	 *  Boot
	 * ---------------------------------------------------------------------- */
	public static function init(): void {

		add_action( 'wp_enqueue_scripts',    [ __CLASS__, 'enqueue_frontend' ] );
		add_action( 'admin_enqueue_scripts', [ __CLASS__, 'enqueue_admin'    ] );

		// AJAX: avatar / image cropper
		add_action( 'wp_ajax_wv_crop_upload',        [ __CLASS__, 'crop_upload_handler' ] );
		add_action( 'wp_ajax_nopriv_wv_crop_upload', [ __CLASS__, 'crop_upload_handler' ] );
	}

	/* -------------------------------------------------------------------------
	 *  Helpers to detect special pages
	 * ---------------------------------------------------------------------- */
	private static function is_auth_page(): bool {
		return in_array( $GLOBALS['pagenow'], [ 'wp-login.php' ], true )
		       || is_page( [ 'login', 'register' ] );
	}

	private static function is_products_page(): bool {
		return is_page( 'wv-products' );
	}

	private static function is_coex_page(): bool {
		return is_page( 'wv-co-ex' );
	}

	private static function is_application_page(): bool {
		return is_page( 'wv-application' );
	}

	private static function is_profile_page(): bool {
		return is_page( 'wv-profile' );
	}

	private static function is_registered_users_page(): bool {
		return is_page( 'registered-users' );
	}

	/* -------------------------------------------------------------------------
	 *  FRONT‑END
	 * ---------------------------------------------------------------------- */
	public static function enqueue_frontend(): void {

		/* ---------- shared paths ---------- */
		$dist = get_stylesheet_directory_uri() . '/dist';
		$src  = get_stylesheet_directory_uri() . '/src';

		/* ---------- CSS ---------- */
		wp_enqueue_style(
			'desymphony-style',
			"{$dist}/css/style.css",
			[],
			filemtime( get_stylesheet_directory() . '/dist/css/style.css' )
		);

		// temporary dev sheet
		wp_enqueue_style(
			'desymphony-temp',
			"{$src}/scss/temp.css",
			[],
			filemtime( get_stylesheet_directory() . '/src/scss/temp.css' )
		);

		/* ---------- Core JS ---------- */
		wp_enqueue_script(
			'desymphony-main',
			"{$dist}/js/main.js",
			[ 'jquery' ],
			filemtime( get_stylesheet_directory() . '/dist/js/main.js' ),
			true
		);

		// Generic AJAX helper
		wp_localize_script(
			'desymphony-main',
			'wvAddonAjax',
			[ 'ajaxUrl' => admin_url( 'admin-ajax.php' ) ]
		);

		/* ---------- Dashboard bundle ---------- */
		wp_enqueue_script(
			'desymphony-dashboard',
			"{$dist}/js/dashboard.js",
			[ 'jquery' ],
			filemtime( get_stylesheet_directory() . '/dist/js/dashboard.js' ),
			true
		);

		/* Single localisation object reused by *dashboard* + *co‑ex* + *products* */
		$dashboard_data = [
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'wv_dashboard_nonce' ),
			'slots'   => DS_Utils::get_coexhibitor_slots( get_current_user_id() )['slots'],
			'role'    => get_user_meta( get_current_user_id(), 'wv_participationModel', true ) ?: 'Head Exhibitor',
			'checkoutUrl' => wc_get_checkout_url(),
		];

		wp_localize_script( 'desymphony-dashboard', 'wvDashboardData', $dashboard_data );

		/* ---------- Auth pages ---------- */
		if ( self::is_auth_page() ) {
            $token = sanitize_text_field(
                $_GET['coex_token']
                ?? get_query_var( 'coex_token' )
                ?? ''
            );

			wp_enqueue_script(
				'desymphony-auth',
				"{$dist}/js/auth.js",
				[ 'jquery' ],
				filemtime( get_stylesheet_directory() . '/dist/js/auth.js' ),
				true
			);
			wp_localize_script(
				'desymphony-auth',
				'WVRegisterData',
				[
					'ajaxUrl' => admin_url( 'admin-ajax.php' ),
					'nonce'   => wp_create_nonce( 'wv_register_nonce' ),
                    'coex_token' => $token, 
				]
			);
		}

		/* ---------- Exhibitor products page ---------- */
		if ( self::is_products_page() || self::is_application_page() ) {
			wp_enqueue_script(
				'desymphony-products',
				"{$dist}/js/products.js",
				[ 'jquery' ],
				filemtime( get_stylesheet_directory() . '/dist/js/products.js' ),
				true
			);
			wp_localize_script( 'desymphony-products', 'wvDashboardData', $dashboard_data );
		}

		/* ---------- Co‑Exhibitors (members) page ---------- */
		if ( self::is_coex_page() ) {
			wp_enqueue_script(
				'desymphony-coex',
				"{$dist}/js/coex.js",                 // ← new split bundle
				[ 'jquery' ],
				filemtime( get_stylesheet_directory() . '/dist/js/coex.js' ),
				true
			);
			wp_localize_script( 'desymphony-coex', 'wvDashboardData', $dashboard_data );
		}

		if ( self::is_profile_page() || self::is_registered_users_page() ) {
			wp_enqueue_script(
				'desymphony-stand-assign',            // ← unified handle
				"{$dist}/js/standassign.js",
				[ 'jquery', 'desymphony-main' ],
				filemtime( get_stylesheet_directory() . '/dist/js/standassign.js' ),
				true
			);
			wp_localize_script(
				'desymphony-stand-assign',
				'wvDashboardData',
				[
					'ajaxUrl' => admin_url( 'admin-ajax.php' ),
					'nonce'   => wp_create_nonce( 'wv_dashboard_nonce' ),
				]
			);
		}

		if ( self::is_profile_page() ) {
            wp_enqueue_script(
                'ds-profile-flash',
                "{$dist}/js/profile-flash.js",
                [ 'jquery' ],
                filemtime( get_stylesheet_directory() . '/dist/js/profile-flash.js' ),
                true
            );
            wp_localize_script(
                'ds-profile-flash',
                'DSProfileData',
                [
                    'ajaxUrl' => admin_url( 'admin-ajax.php' ),
                    'nonce'   => wp_create_nonce( 'wv_dashboard_nonce' ),
                ]
            );
        }


		// Finish-application Ajax
		wp_localize_script(
			'desymphony-main',
			'wvFinishApp',
			[
				'ajax'  => admin_url( 'admin-ajax.php' ),
				'nonce' => wp_create_nonce( 'ds_finish_application' ),
			]
		);

		// Cropper
		wp_localize_script(
			'desymphony-main',
			'wvCropperData',
			[
				'ajaxUrl'      => admin_url( 'admin-ajax.php' ),
				'nonce'        => wp_create_nonce( 'wv_addon_cropper_nonce' ),
				'uploadAction' => 'wv_crop_upload',
			]
		);

		// Halls order (static PHP include)
		$halls_order = require get_theme_file_path( 'inc/public/views/halls/halls-order.php' );
		wp_localize_script(
			'desymphony-main',
			'DSHallsData',
			[
				'hallsOrder' => array_map( 'strval', $halls_order ),
				'ajaxUrl'    => admin_url( 'admin-ajax.php' ),
			]
		);

		// Cart
		wp_localize_script(
			'desymphony-main',
			'wvCartData',
			[
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'wv_cart_nonce' ),
			]
		);
	}

	/* -------------------------------------------------------------------------
	 *  ADMIN
	 * ---------------------------------------------------------------------- */
	public static function enqueue_admin(): void {

		$dist = get_stylesheet_directory_uri() . '/dist';

		wp_enqueue_style(
			'desymphony-admin',
			"{$dist}/css/admin-style.css",
			[],
			filemtime( get_stylesheet_directory() . '/dist/css/admin-style.css' )
		);

		wp_enqueue_script(
			'desymphony-admin',
			"{$dist}/js/admin.js",
			[ 'jquery' ],
			filemtime( get_stylesheet_directory() . '/dist/js/admin.js' ),
			true
		);

		wp_localize_script(
			'desymphony-admin',
			'wvAddonAdmin',
			[
				'auth_nonce'      => wp_create_nonce( 'wv_addon_auth_settings' ),
				'dashboard_nonce' => wp_create_nonce( 'wv_addon_dashboard_settings' ),
				'links_nonce'     => wp_create_nonce( 'wv_addon_install_exhibitor_links_table' ),
				'products_nonce'  => wp_create_nonce( 'wv_addon_install_exhibitor_products_table' ),
				'favorites_nonce' => wp_create_nonce( 'wv_addon_install_favorites_table' ),
			]
		);
	}

	/* -------------------------------------------------------------------------
	 *  AJAX: Image crop / upload
	 * ---------------------------------------------------------------------- */
	public static function crop_upload_handler(): void {

		check_ajax_referer( 'wv_addon_cropper_nonce', 'security' );

		$profile_key  = sanitize_text_field( $_POST['profile_key'] ?? '' );
		$image_data   = $_POST['image_data'] ?? '';

		$placeholders = isset( $_POST['placeholders'] ) && is_array( $_POST['placeholders'] )
			? array_map( 'sanitize_text_field', $_POST['placeholders'] )
			: [];

		$handler = new \Desymphony\Helpers\DS_Media_Handler();
		$result  = $handler->process_image_upload( [
			'profile_key'  => $profile_key,
			'image_data'   => $image_data,
			'placeholders' => $placeholders,
		] );

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( [ 'message' => $result->get_error_message() ] );
		}
		wp_send_json_success( $result );
	}
}
