<?php
namespace Desymphony\Exhibitor;

use Desymphony\Helpers\DS_Utils; // for is_exhibitor, get_exhibitor_participation, etc.

// If you want to reference your existing product manager or other plugin classes:
// use Desymphony\Dashboard\DS_Exhibitor_Products_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class DS_Exhibitor_Application {

	public function __construct() {
		self::init();           
	}

	/**
	 * Initialize (hook shortcode, session check, etc.).
	 */
	public static function init() {
		// Start session if not active
		if ( session_status() !== PHP_SESSION_ACTIVE ) {
			// session_start();
		}

		// Register our shortcode
		add_shortcode( 'wv_exhibitor_application', [ __CLASS__, 'render_application_shortcode' ] );

		add_action( 'wp_ajax_ds_finish_application', [ __CLASS__, 'ajax_finish_application' ] );

	}

	/**
	 * The shortcode callback:
	 *   [wv_exhibitor_application]
	 */
	public static function render_application_shortcode( $atts = [], $content = null ) {
		ob_start();
		self::handle_application_wizard();
		return ob_get_clean();
	}


	/* --------------------------------------------------------------------- */
	/*  Ajax: mark application complete                                      */
	/* --------------------------------------------------------------------- */
	public static function ajax_finish_application() : void {

		check_ajax_referer( 'ds_finish_application', 'nonce' );

		$user_id = get_current_user_id();
		if ( ! $user_id ) {
			wp_send_json_error( __( 'Not logged in.', DS_THEME_TEXTDOMAIN ) );
		}

		/* ⇢ need ≥ 1 product **only** if the exhibitor said they will exhibit products */
		if ( DS_Utils::is_exhibiting_products( $user_id ) &&
			! DS_Utils::exhibitor_has_products( $user_id ) ) {
			wp_send_json_error( __( 'You must add at least one product before finishing the application.', DS_THEME_TEXTDOMAIN ) );
		}

		if ( ! DS_Utils::is_admin_verified( $user_id ) ) {
			wp_send_json_error( __( 'Your account is still pending admin verification.', DS_THEME_TEXTDOMAIN ) );
		}

		update_user_meta( $user_id, 'wv_status',             'Active' );
		update_user_meta( $user_id, 'wv_ex_stage2_verified', '1' );

		wp_send_json_success();
	}


	
}
