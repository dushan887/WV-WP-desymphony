<?php
namespace Desymphony\Admin\Settings;

use Desymphony\Admin\Protection\DS_Admin_Dashboard_Page_Protection;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles Dashboard Pages assignment and auto-generation logic.
 * This class is used for the super admin feature to manage which WP pages
 * serve as the front-end dashboard (and, in future, additional dashboard modules).
 */
class DS_Admin_Dashboard_Pages {

	const OPTION_KEY = 'wv_addon_dashboard_pages';

	/**
	 * Initialize the AJAX endpoints.
	 */
	public function init(): void {
		add_action( 'wp_ajax_wv_addon_save_dashboard_pages', [ $this, 'ajax_save_dashboard_pages' ] );
		add_action( 'wp_ajax_wv_addon_regenerate_dashboard_pages', [ $this, 'ajax_regenerate_dashboard_pages' ] );
		add_action( 'wp_ajax_wv_addon_create_update_missing_dashboard_pages', [ $this, 'ajax_create_update_missing_dashboard_pages' ] );
	}

	/**
	 * AJAX: Save the dashboard page assignments.
	 */
	public function ajax_save_dashboard_pages(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( [ 'message' => __( 'Unauthorized', DS_THEME_TEXTDOMAIN ) ], 403 );
		}
		check_ajax_referer( 'wv_addon_dashboard_settings', 'nonce' );

		$default_pages = $this->get_default_dashboard_pages();
		$page_assignments = get_option( self::OPTION_KEY, [] );

		// Loop through each default dashboard page.
		foreach ( $default_pages as $key => $data ) {
			$page_key = 'wv_addon_dashboard_page_' . $key;
			$page_id  = isset( $_POST[ $page_key ] ) ? intval( $_POST[ $page_key ] ) : 0;
			$page_assignments[ $key ] = $page_id;
		}
		update_option( self::OPTION_KEY, $page_assignments );

		wp_send_json_success( [ 'message' => __( 'Dashboard pages saved successfully.', DS_THEME_TEXTDOMAIN ) ] );
	}

	/**
	 * AJAX: Regenerate (delete and recreate) dashboard pages.
	 */
	public function ajax_regenerate_dashboard_pages(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( [ 'message' => __( 'Unauthorized', DS_THEME_TEXTDOMAIN ) ], 403 );
		}
		check_ajax_referer( 'wv_addon_dashboard_settings', 'nonce' );

		$page_assignments = get_option( self::OPTION_KEY, [] );
		$deleted_slugs = [];
		$errors = [];

		// Delete each assigned page if it exists.
		foreach ( $page_assignments as $slug => $page_id ) {
			if ( $page_id && get_post( $page_id ) ) {
				$delete_result = wp_delete_post( $page_id, true );
				if ( $delete_result ) {
					$deleted_slugs[] = $slug;
				} else {
					$errors[] = sprintf( __( 'Failed deleting page for: %s', DS_THEME_TEXTDOMAIN ), $slug );
				}
			}
		}

		// Clear the stored assignments.
		update_option( self::OPTION_KEY, [] );

		// Recreate the pages.
		$result = $this->create_or_update_dashboard_pages( true );
		if ( ! empty( $errors ) ) {
			wp_send_json_error( [ 'message' => implode( ' | ', $errors ) ] );
		}
		if ( $result['success'] ) {
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
				$msg_html .= $result['details'];
			}
			if ( empty( $deleted_slugs ) && empty( $result['details'] ) ) {
				$msg_html = __( 'Regenerated dashboard pages successfully.', DS_THEME_TEXTDOMAIN );
			}
			wp_send_json_success( [ 'message' => $msg_html ] );
		} else {
			wp_send_json_error( [ 'message' => $result['error'] ], 500 );
		}
	}

	/**
	 * AJAX: Create or update missing dashboard pages.
	 */
	public function ajax_create_update_missing_dashboard_pages(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( [ 'message' => __( 'Unauthorized', DS_THEME_TEXTDOMAIN ) ], 403 );
		}
		check_ajax_referer( 'wv_addon_dashboard_settings', 'nonce' );

		$result = $this->create_or_update_dashboard_pages( false );
		if ( $result['success'] ) {
			if ( empty( $result['details'] ) ) {
				wp_send_json_success( [ 'message' => __( 'No changes needed; all dashboard pages are set.', DS_THEME_TEXTDOMAIN ) ] );
			} else {
				wp_send_json_success( [ 'message' => $result['details'] ] );
			}
		} else {
			wp_send_json_error( [ 'message' => $result['error'] ], 500 );
		}
	}

	/**
	 * Internal method to create or update dashboard pages.
	 *
	 * @param bool $overwrite If true, existing pages are overwritten.
	 * @return array Associative array with keys: success (bool), details (string), error (string|false)
	 */
	private function create_or_update_dashboard_pages( bool $overwrite ): array {
		$default_pages = $this->get_default_dashboard_pages();
		$page_assignments = get_option( self::OPTION_KEY, [] );
		$changes = [];
	
		foreach ( $default_pages as $key => $data ) {
			$page_id  = isset( $page_assignments[ $key ] ) ? intval( $page_assignments[ $key ] ) : 0;
			$title    = $data['title'];
			$slug     = $data['slug'];
			$shortcode = $data['shortcode'];
			if ( $page_id && get_post( $page_id ) ) {
				if ( $overwrite ) {
					$updated = wp_update_post( [
						'ID'           => $page_id,
						'post_title'   => $title,
						'post_name'    => $slug,
						'post_content' => $this->get_default_page_content( $shortcode ),
					] );
					if ( ! is_wp_error( $updated ) ) {
						// If the template exists, update the page meta.
						if ( locate_template( 'templates/page-profile.php' ) ) {
							update_post_meta( $page_id, '_wp_page_template', 'templates/page-profile.php' );
						}
						$changes[] = sprintf( __( '<strong>%s</strong> page updated.', DS_THEME_TEXTDOMAIN ), $key );
					}
				}
			} else {
				// Try to find an existing page with the same slug.
				$existing = get_page_by_path( $slug );
				if ( $existing && 'page' === $existing->post_type ) {
					$page_assignments[ $key ] = $existing->ID;
					if ( $overwrite ) {
						$updated = wp_update_post( [
							'ID'           => $existing->ID,
							'post_title'   => $title,
							'post_content' => $this->get_default_page_content( $shortcode ),
						] );
						if ( ! is_wp_error( $updated ) ) {
							if ( locate_template( 'templates/page-profile.php' ) ) {
								update_post_meta( $existing->ID, '_wp_page_template', 'templates/page-profile.php' );
							}
							$changes[] = sprintf( __( 'Found existing page for <strong>%s</strong> and updated.', DS_THEME_TEXTDOMAIN ), $key );
						}
					} else {
						$changes[] = sprintf( __( 'Found existing page for <strong>%s</strong> and assigned.', DS_THEME_TEXTDOMAIN ), $key );
					}
				} else {
					// Create a new page.
					$new_page_id = wp_insert_post( [
						'post_type'    => 'page',
						'post_title'   => $title,
						'post_name'    => $slug,
						'post_status'  => 'publish',
						'post_content' => $this->get_default_page_content( $shortcode ),
					] );
					if ( is_wp_error( $new_page_id ) ) {
						return [
							'success' => false,
							'error'   => $new_page_id->get_error_message()
						];
					}
					$page_assignments[ $key ] = $new_page_id;
					// Set the page template if available.
					if ( locate_template( 'templates/page-profile.php' ) ) {
						update_post_meta( $new_page_id, '_wp_page_template', 'templates/page-profile.php' );
					}
					$changes[] = sprintf( __( 'Created new page for <strong>%s</strong>.', DS_THEME_TEXTDOMAIN ), $key );
				}
			}
		}
		update_option( self::OPTION_KEY, $page_assignments );
	
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
	 * Returns the default dashboard pages.
	 *
	 * Defines each section's title, slug, and the shortcode that will be used
	 * on the front end to render that dashboard module.
	 *
	 * @return array Associative array keyed by dashboard section.
	 */
	private function get_default_dashboard_pages() : array {
		$pages = [
			'dashboard'    => [ 'Dashboard Home', 'wv-dashboard', 'wv_dashboard_home' ],
			'meetings'     => [ 'Meeting Requests', 'wv-meeting', 'wv_meeting' ],
			'calendar'     => [ 'Calendar', 'wv-calendar', 'wv_calendar' ],
			'products'     => [ 'Products', 'wv-products', 'wv_products' ],
			'services'     => [ 'Services', 'wv-services', 'wv_services' ],
			'co-ex'       => [ 'Co-Exhibitors', 'wv-co-ex', 'wv_co_ex' ],
			'profile'      => [ 'Profile Management', 'wv-profile', 'wv_profile' ],
			'saved'        => [ 'Saved', 'wv-saved', 'wv_saved' ],
			'messages'     => [ 'Messages', 'wv-messages', 'wv_messages' ],
			'application'  => [ 'Exhibition Application Form', 'wv-application', 'wv_application' ],
			'events'       => [ 'Events', 'wv-events', 'wv_events' ],
			'members'      => [ 'Members', 'wv-members', 'wv_members' ],
			'notifications' => [ 'Notifications', 'wv-notifications', 'wv_notifications' ],
		];

		$default_pages = [];
		foreach ( $pages as $key => $data ) {
			$default_pages[ $key ] = [
				'title'     => __( $data[0], DS_THEME_TEXTDOMAIN ),
				'slug'      => $data[1],
				'shortcode' => $this->generate_shortcode( $data[2] ),
			];
		}

		return $default_pages;
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

	/**
	 * Returns the default page content for a dashboard page.
	 *
	 * In this implementation, it simply returns the shortcode.
	 *
	 * @param string $shortcode The shortcode to display.
	 * @return string
	 */
	private function get_default_page_content( string $shortcode ) : string {
		return $shortcode;
	}
}
