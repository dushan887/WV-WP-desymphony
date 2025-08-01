<?php
namespace Desymphony\Public;

use Desymphony\Woo\DS_Woo_Stand_Map;
use Desymphony\Dashboard\DS_CoEx_Manager;

defined( 'ABSPATH' ) || exit;

/**
 * Front‑end initialisation: registers AJAX hooks and the public “/wv‑coex‑response/”
 * endpoint that invitees use to Accept / Decline.
 */
class DS_Public_Init {

	/** @var string */
	private string $version;

	public function __construct( string $version ) {
		$this->version = $version;
	}

	/**
	 * Bind all public‑facing hooks.
	 */
	public function init(): void {

		/* -------- AJAX (logged‑in) -------- */
		add_action( 'wp_ajax_wv_send_coex_invite',   [ DS_CoEx_Manager::class, 'ajax_send_invite'   ] );
		add_action( 'wp_ajax_wv_get_coex_invites',   [ DS_CoEx_Manager::class, 'ajax_get_invites'   ] );
		add_action( 'wp_ajax_wv_delete_coex_invite', [ DS_CoEx_Manager::class, 'ajax_delete_invite' ] );
		add_action( 'wp_ajax_wv_assign_coex_stand',  [ DS_CoEx_Manager::class, 'ajax_assign_stand'  ] );
        

		/* -------- Public acceptance / decline -------- */
		add_action( 'template_redirect', [ $this, 'maybe_handle_public_response' ] );
        add_action( 'wp_ajax_wv_coex_flash', [ $this, 'ajax_get_flash' ] );

	}

	
    /**
     * Catch `?coex_token=…&coex_action=accept|decline` anywhere on the site.
     * Always redirect to /wv‑profile/ with a notice.
     */
    public function maybe_handle_public_response(): void {

        $token  = sanitize_text_field( $_GET['coex_token']  ?? '' );
        $action = sanitize_text_field( $_GET['coex_action'] ?? '' );
        if ( ! $token || ! $action ) {
            return; // nothing to do
        }

        $result = DS_CoEx_Manager::public_handle_response( $token, $action );

        if ( is_wp_error( $result ) ) {
            wp_die( esc_html( $result->get_error_message() ) );
        }

        // store flash‑message and redirect
        set_transient( 'ds_coex_flash_'.md5( $token ), $result, 60 );
        wp_safe_redirect( home_url( '/wv-profile/?coex_msg='.rawurlencode( $token ) ) );
        exit;
    }

    public function ajax_get_flash(): void {
        check_ajax_referer( 'wv_dashboard_nonce', 'security' );
        $key = sanitize_text_field( $_POST['key'] ?? '' );
        wp_send_json_success( DS_Utils::get_and_clear_flash( $key ) );
    }


}
