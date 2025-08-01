<?php
namespace Desymphony\Admin\Settings;

use Desymphony\Admin\Protection\DS_Admin_Auth_Page_Protection; // For removing/re-adding filters

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles Auth pages assignment and auto-generation logic, plus improved notifications.
 */
class DS_Admin_Auth_Pages {

	public function init(): void {
		// Existing actions
		add_action( 'wp_ajax_wv_addon_save_auth_pages', [ $this, 'ajax_save_auth_pages' ] );

		// New actions for specialized buttons
		add_action( 'wp_ajax_wv_addon_regenerate_auth_pages', [ $this, 'ajax_regenerate_auth_pages' ] );
		add_action( 'wp_ajax_wv_addon_create_update_missing_pages', [ $this, 'ajax_create_update_missing_pages' ] );

	}

	/**
	 * AJAX: Save the page assignments & success messages.
	 */
	public function ajax_save_auth_pages(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( [ 'message' => __( 'Unauthorized', DS_THEME_TEXTDOMAIN ) ], 403 );
		}
		check_ajax_referer( 'wv_addon_auth_settings', 'nonce' );

		$auth_slugs       = [ 'login', 'register', 'reset_password', 'set_password', 'email_confirm', '2fa', 'thank_you' ];
		$page_assignments = get_option( 'wv_addon_auth_pages', [] );
		$messages         = get_option( 'wv_addon_auth_messages', [] );

		foreach ( $auth_slugs as $slug ) {
			$page_key     = 'wv_addon_auth_page_' . $slug;
			$message_key  = 'wv_addon_auth_success_msg_' . $slug;
			$page_id      = isset( $_POST[ $page_key ] ) ? intval( $_POST[ $page_key ] ) : 0;
			$success_msg  = isset( $_POST[ $message_key ] ) ? wp_kses_post( $_POST[ $message_key ] ) : '';

			$page_assignments[ $slug ] = $page_id;
			$messages[ $slug ]         = $success_msg;
		}

		update_option( 'wv_addon_auth_pages', $page_assignments );
		update_option( 'wv_addon_auth_messages', $messages );

		wp_send_json_success( [
			'message' => __( 'Auth pages saved successfully.', DS_THEME_TEXTDOMAIN ),
		] );
	}

	/**
	 * AJAX: Completely delete existing assigned pages, then recreate them.
	 */
	public function ajax_regenerate_auth_pages(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( [ 'message' => __( 'Unauthorized', DS_THEME_TEXTDOMAIN ) ], 403 );
		}
		check_ajax_referer( 'wv_addon_auth_settings', 'nonce' );

		$page_assignments = get_option( 'wv_addon_auth_pages', [] );
		$deleted_slugs    = [];
		$errors           = [];

		// Temporarily remove page-protection so plugin can delete assigned pages
		remove_filter( 'user_has_cap', [ DS_Admin_Auth_Page_Protection::class, 'block_auth_page_deletion' ], 10 );

		// Delete each assigned page if it exists
		foreach ( $page_assignments as $slug => $page_id ) {
			if ( $page_id && get_post( $page_id ) ) {
				$delete_result = wp_delete_post( $page_id, true ); // force delete
				if ( $delete_result ) {
					$deleted_slugs[] = $slug;
				} else {
					$errors[] = sprintf( __( 'Failed deleting page for: %s', DS_THEME_TEXTDOMAIN ), $slug );
				}
			}
		}

		// Clear the assignments (since we just deleted them).
		update_option( 'wv_addon_auth_pages', [] );

		// Re-add the page-protection filter
		add_filter( 'user_has_cap', [ DS_Admin_Auth_Page_Protection::class, 'block_auth_page_deletion' ], 10, 4 );

		// Now create them fresh
		$result = $this->create_or_update_auth_pages( true );

		if ( ! empty( $errors ) ) {
			wp_send_json_error( [ 'message' => implode( ' | ', $errors ) ] );
		}

		if ( $result['success'] ) {
			// Build an HTML message with bullet points
			$msg_html = '<div style="margin-bottom:8px;">';
			if ( ! empty( $deleted_slugs ) ) {
				$msg_html .= '<strong>' . __( 'Deleted pages for:', DS_THEME_TEXTDOMAIN ) . '</strong>';
				$msg_html .= '<ul style="margin-top:4px;">';
				foreach ( $deleted_slugs as $slug ) {
					$msg_html .= '<li>' . esc_html( $slug ) . '</li>';
				}
				$msg_html .= '</ul>';
			}
			$msg_html .= '</div>';

			if ( ! empty( $result['details'] ) ) {
				// $result['details'] is already a bulleted list, so just append
				$msg_html .= $result['details'];
			}

			// If nothing to show, fallback
			if ( ! $deleted_slugs && empty( $result['details'] ) ) {
				$msg_html = __( 'Regenerated Auth Pages successfully.', DS_THEME_TEXTDOMAIN );
			}

			wp_send_json_success( [ 'message' => $msg_html ] );
		} else {
			wp_send_json_error( [ 'message' => $result['error'] ], 500 );
		}
	}

	/**
	 * AJAX: Creates or updates missing pages. If a page isn't assigned but
	 * we find a matching slug in WP, we link it. Otherwise, we create a new page.
	 * If everything is assigned already, return "No changes needed."
	 */
	public function ajax_create_update_missing_pages(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( [ 'message' => __( 'Unauthorized', DS_THEME_TEXTDOMAIN ) ], 403 );
		}
		check_ajax_referer( 'wv_addon_auth_settings', 'nonce' );

		$result = $this->create_or_update_auth_pages( false );
		if ( $result['success'] ) {
			if ( empty( $result['details'] ) ) {
				wp_send_json_success( [
					'message' => __( 'No changes needed; all Auth Pages are set.', DS_THEME_TEXTDOMAIN ),
				] );
			}
			wp_send_json_success( [ 'message' => $result['details'] ] );
		} else {
			wp_send_json_error( [ 'message' => $result['error'] ], 500 );
		}
	}

	/**
	 * Internal function to create or update pages for each auth flow.
	 *
	 * @param bool $overwrite If true, pages are updated even if they exist.
	 * @return array [ 'success' => bool, 'error' => ?string, 'details' => ?string ]
	 */
	private function create_or_update_auth_pages( bool $overwrite ): array {
		$auth_slugs = [
			'login'          => [ 'title' => __( 'Login',           DS_THEME_TEXTDOMAIN ), 'slug' => 'login' ],
			'register'       => [ 'title' => __( 'Register',        DS_THEME_TEXTDOMAIN ), 'slug' => 'register' ],
			'reset_password' => [ 'title' => __( 'Forgot Password', DS_THEME_TEXTDOMAIN ), 'slug' => 'forgot-password' ],
			'set_password'   => [ 'title' => __( 'Set Password',    DS_THEME_TEXTDOMAIN ), 'slug' => 'reset-password' ], // ← NEW
			'email_confirm'  => [ 'title' => __( 'Email Confirm',   DS_THEME_TEXTDOMAIN ), 'slug' => 'email-confirmation' ],
			'2fa'            => [ 'title' => __( 'Two-Factor',      DS_THEME_TEXTDOMAIN ), 'slug' => '2fa' ],
			'thank_you'      => [ 'title' => __( 'Thank You',       DS_THEME_TEXTDOMAIN ), 'slug' => 'thank-you' ],
		];
	
		$page_assignments = get_option( 'wv_addon_auth_pages', [] );
		$changes = [];
	
		foreach ( $auth_slugs as $slug => $data ) {
			$page_id  = isset( $page_assignments[ $slug ] ) ? (int) $page_assignments[ $slug ] : 0;
			$title    = $data['title'];
			$postSlug = $data['slug'];
	
			// CASE 1: Already assigned page.
			if ( $page_id && get_post( $page_id ) ) {
				if ( $overwrite ) {
					$updated = wp_update_post( [
						'ID'           => $page_id,
						'post_title'   => $title,
						'post_name'    => $postSlug,
						'post_content' => $this->get_default_page_content( $slug ),
					] );
					if ( ! is_wp_error( $updated ) ) {
						if ( locate_template( 'templates/page-auth.php' ) ) {
							update_post_meta( $page_id, '_wp_page_template', 'templates/page-auth.php' );
						}
						$changes[] = sprintf(
							__( '<strong>%s</strong> page updated.', DS_THEME_TEXTDOMAIN ),
							esc_html( $slug )
						);
					}
				}
			} else {
				// CASE 2: Possibly an existing page with the same slug.
				$existing = get_page_by_path( $postSlug );
				if ( $existing && 'page' === $existing->post_type ) {
					$page_assignments[ $slug ] = $existing->ID;
					if ( $overwrite ) {
						$updated = wp_update_post( [
							'ID'           => $existing->ID,
							'post_title'   => $title,
							'post_content' => $this->get_default_page_content( $slug ),
						] );
						if ( ! is_wp_error( $updated ) ) {
							if ( locate_template( 'templates/page-auth.php' ) ) {
								update_post_meta( $existing->ID, '_wp_page_template', 'templates/page-auth.php' );
							}
							$changes[] = sprintf(
								__( 'Found existing page for <strong>%s</strong> and updated.', DS_THEME_TEXTDOMAIN ),
								esc_html( $slug )
							);
						}
					} else {
						$changes[] = sprintf(
							__( 'Found existing page for <strong>%s</strong> and assigned.', DS_THEME_TEXTDOMAIN ),
							esc_html( $slug )
						);
					}
				} else {
					// CASE 3: Create a new page.
					$new_page_id = wp_insert_post( [
						'post_type'    => 'page',
						'post_title'   => $title,
						'post_name'    => $postSlug,
						'post_status'  => 'publish',
						'post_content' => $this->get_default_page_content( $slug ),
					] );
					if ( is_wp_error( $new_page_id ) ) {
						return [
							'success' => false,
							'error'   => $new_page_id->get_error_message()
						];
					}
					$page_assignments[ $slug ] = $new_page_id;
					if ( locate_template( 'templates/page-auth.php' ) ) {
						update_post_meta( $new_page_id, '_wp_page_template', 'templates/page-auth.php' );
					}
					$changes[] = sprintf(
						__( 'Created new page for <strong>%s</strong>.', DS_THEME_TEXTDOMAIN ),
						esc_html( $slug )
					);
				}
			}
		}
		update_option( 'wv_addon_auth_pages', $page_assignments );
	
		$details_html = '';
		if ( ! empty( $changes ) ) {
			$details_html .= '<ul>';
			foreach ( $changes as $change ) {
				$details_html .= '<li>' . $change . '</li>';
			}
			$details_html .= '</ul>';
		}
	
		return [
			'success' => true,
			'details' => $details_html,
		];
	}
	

	/**
	 * Returns default content (shortcodes) for each slug.
	 *
	 * @param string $slug
	 * @return string
	 */
	private function get_default_page_content( string $slug ): string {
		switch ( $slug ) {
			case 'login':
				return $this->generate_shortcode( 'wv_addon_login_form' );

			case 'register':
				return $this->generate_shortcode( 'wv_addon_register_form step="1"' );

			case 'reset_password':   
				return $this->generate_shortcode( 'wv_addon_password_reset_form' );

			case 'set_password':     
				return $this->generate_shortcode( 'wv_addon_set_password_form' );

			case 'email_confirm':
				return $this->generate_shortcode( 'wv_addon_email_confirmation' );

			case '2fa':
				return $this->generate_shortcode( 'wv_addon_2fa_form' );

			case 'thank_you':
				return $this->generate_shortcode( 'wv_addon_thank_you' );
		}
		return '';
	}


	/**
	 * Generates the shortcode content for a given shortcode tag.
	 *
	 * @param string $shortcode_tag The shortcode tag to wrap.
	 * @return string The generated shortcode content.
	 */
	private function generate_shortcode( string $shortcode_tag ) : string {
		return sprintf( '[%s]', $shortcode_tag );
	}

	
	
	
}
