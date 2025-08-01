<?php
namespace Desymphony\Auth\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class DS_Auth_Email_Confirmation {

	public function __construct() {
		add_shortcode( 'wv_addon_email_confirmation', [ $this, 'render_page' ] );
		add_action( 'init', [ $this, 'maybe_confirm_email' ] );
	}

	public function render_page(): string {
		ob_start();
		$file = DS_THEME_DIR . 'public/views/partials/forms/auth-email-confirmation-page.php';
		if ( file_exists( $file ) ) {
			include $file;
		} else {
			echo '<p>' . esc_html__( 'Email confirmation page not found.', DS_THEME_TEXTDOMAIN ) . '</p>';
		}
		return ob_get_clean();
	}

	public function maybe_confirm_email(): void {
		if ( isset( $_GET['wv_confirm_email'] ) && isset( $_GET['token'] ) ) {
			$user_id = absint( $_GET['wv_confirm_email'] );
			$token   = sanitize_text_field( $_GET['token'] );
			$saved   = get_user_meta( $user_id, 'wv_email_confirmation_token', true );
			if ( $saved && hash_equals( $saved, $token ) ) {
				update_user_meta( $user_id, 'wv_email_confirmed', true );
				delete_user_meta( $user_id, 'wv_email_confirmation_token' );
				wp_redirect( add_query_arg( 'wv_confirm_status', 'success', remove_query_arg( 'token' ) ) );
				exit;
			} else {
				wp_redirect( add_query_arg( 'wv_confirm_status', 'failed', remove_query_arg( 'token' ) ) );
				exit;
			}
		}
	}
}
