<?php
namespace Desymphony\Admin\Protection;

use WP_Post;
use WP_User;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Protects and labels assigned Dashboard pages.
 *
 * This class is used to:
 * - Label dashboard pages in the WP admin page list.
 * - Preserve the title/slug of assigned dashboard pages.
 * - Block deletion of pages assigned as dashboard pages.
 *
 * Dashboard pages are stored in the option 'wv_addon_dashboard_pages'.
 */
class DS_Admin_Dashboard_Page_Protection {

	const OPTION_KEY = 'wv_addon_dashboard_pages';

	/**
	 * Initialize the protection filters.
	 */
	public static function init(): void {
		add_filter( 'display_post_states', [ __CLASS__, 'label_assigned_dashboard_pages' ], 10, 2 );
		add_filter( 'wp_insert_post_data', [ __CLASS__, 'preserve_dashboard_page_data' ], 10, 2 );
		add_filter( 'page_row_actions', [ __CLASS__, 'remove_dashboard_page_row_actions' ], 10, 2 );
		add_filter( 'user_has_cap', [ __CLASS__, 'block_dashboard_page_deletion' ], 10, 4 );
	}

	/**
	 * Labels dashboard pages in the WP pages list.
	 *
	 * @param array $states Existing post states.
	 * @param WP_Post $post Current page.
	 * @return array
	 */
	public static function label_assigned_dashboard_pages( array $states, WP_Post $post ): array {
		$labels = [
			'dashboard'   => __( 'DS User - Dashboard Home', DS_THEME_TEXTDOMAIN ),
			'meetings'    => __( 'DS User - Meeting Requests', DS_THEME_TEXTDOMAIN ),
			'calendar'    => __( 'DS User - Calendar', DS_THEME_TEXTDOMAIN ),
			'products'    => __( 'DS User - Products', DS_THEME_TEXTDOMAIN ),
			'services'    => __( 'DS User - Services', DS_THEME_TEXTDOMAIN ),
			'co-ex'      => __( 'DS User - Co-Exhibitors', DS_THEME_TEXTDOMAIN ),
			'profile'     => __( 'DS User - Profile Management', DS_THEME_TEXTDOMAIN ),
			'saved'       => __( 'DS User - Saved', DS_THEME_TEXTDOMAIN ),
			'messages'    => __( 'DS User - Messages', DS_THEME_TEXTDOMAIN ),
			'application' => __( 'DS User - Exhibition Application Form', DS_THEME_TEXTDOMAIN ),
			'events'     => __( 'DS User - Events', DS_THEME_TEXTDOMAIN ),
			'members'     => __( 'DS User - Members', DS_THEME_TEXTDOMAIN ),
			'notifications' => __( 'DS User - Notifications', DS_THEME_TEXTDOMAIN ),
		];

		$options = get_option( self::OPTION_KEY, [] );
		if ( ! is_array( $options ) ) {
			return $states;
		}
		foreach ( $options as $slug => $page_id ) {
			if ( (int) $page_id === $post->ID && isset( $labels[ $slug ] ) ) {
				$states[] = $labels[ $slug ];
			}
		}
		return $states;
	}

	/**
	 * Preserves data for dashboard pages so that their title and slug cannot be changed.
	 *
	 * @param array $data Post data.
	 * @param array $postarr Original post array.
	 * @return array
	 */
	public static function preserve_dashboard_page_data( array $data, array $postarr ): array {
		if ( 'page' !== $data['post_type'] || empty( $postarr['ID'] ) ) {
			return $data;
		}
		$post_id = (int)$postarr['ID'];
		$dashboard_pages = get_option( self::OPTION_KEY, [] );
		$locked_ids = array_map('intval', array_values($dashboard_pages));
		if ( in_array($post_id, $locked_ids, true) ) {
			$original_post = get_post($post_id);
			if ($original_post) {
				$data['post_title'] = $original_post->post_title;
				$data['post_name']  = $original_post->post_name;
			}
		}
		return $data;
	}

	/**
	 * Removes row actions for dashboard pages (prevent deletion or trashing).
	 *
	 * @param array $actions Existing row actions.
	 * @param WP_Post $post Current page post.
	 * @return array
	 */
	public static function remove_dashboard_page_row_actions( array $actions, WP_Post $post ): array {
		$dashboard_pages = get_option( self::OPTION_KEY, [] );
		$locked_ids = array_map('intval', array_values($dashboard_pages));
		if ( in_array($post->ID, $locked_ids, true) ) {
			unset($actions['trash'], $actions['delete']);
		}
		return $actions;
	}

	/**
	 * Blocks deletion capabilities for dashboard pages.
	 *
	 * @param array $allcaps All capabilities.
	 * @param array $caps Specific capabilities.
	 * @param array $args Additional args.
	 * @param WP_User $user The user object.
	 * @return array
	 */
	public static function block_dashboard_page_deletion( array $allcaps, array $caps, array $args, WP_User $user ): array {
		if ( isset($args[0]) && in_array($args[0], ['delete_post', 'delete_page'], true) ) {
			$post_id = $args[2] ?? 0;
			if ($post_id) {
				$dashboard_pages = get_option( self::OPTION_KEY, [] );
				$locked_ids = array_map('intval', array_values($dashboard_pages));
				if ( in_array((int)$post_id, $locked_ids, true) ) {
					$allcaps[$args[0]] = false;
				}
			}
		}
		return $allcaps;
	}
}
