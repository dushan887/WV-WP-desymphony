<?php
namespace Desymphony\Auth\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class DS_Auth_2FA {

	public function __construct() {
		add_shortcode( 'wv_addon_2fa_form', [ $this, 'render_form' ] );
		add_action( 'wp_ajax_nopriv_wv_addon_2fa_verify', [ $this, 'verify_2fa_code' ] );
		add_action( 'wp_ajax_wv_addon_2fa_verify', [ $this, 'verify_2fa_code' ] );
	}

	public function render_form(): string {
		ob_start();
		$form_file = DS_THEME_DIR . 'public/views/partials/forms/auth-2fa-form.php';
		if ( file_exists( $form_file ) ) {
			include $form_file;
		} else {
			echo '<p>' . esc_html__( '2FA form not found.', DS_THEME_TEXTDOMAIN ) . '</p>';
		}
		return ob_get_clean();
	}

	public function verify_2fa_code(): void {
		check_ajax_referer( 'wv_2fa_nonce', 'nonce' );
		$code = isset( $_POST['code'] ) ? sanitize_text_field( $_POST['code'] ) : '';
		// Example validation; replace with real logic:
		if ( $code === '123456' ) {
			wp_send_json_success( [ 'message' => __( '2FA verified successfully.', DS_THEME_TEXTDOMAIN ) ] );
		}
		wp_send_json_error( [ 'message' => __( 'Invalid code.', DS_THEME_TEXTDOMAIN ) ] );
	}
}
