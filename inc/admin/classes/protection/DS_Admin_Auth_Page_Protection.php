<?php
namespace Desymphony\Admin\Protection;

use WP_Post;
use WP_User;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Protects and labels assigned Auth pages (Login, Register, etc.).
 */
class DS_Admin_Auth_Page_Protection {
	const OPTION_KEY = 'wv_addon_auth_pages';

	public static function init(): void {
		add_filter( 'display_post_states', [ __CLASS__, 'label_assigned_auth_pages' ], 10, 2 );
		add_filter( 'wp_insert_post_data', [ __CLASS__, 'preserve_auth_page_data' ], 10, 2 );
		add_filter( 'page_row_actions', [ __CLASS__, 'remove_auth_page_row_actions' ], 10, 2 );
		add_filter( 'user_has_cap', [ __CLASS__, 'block_auth_page_deletion' ], 10, 4 );
	}

	public static function label_assigned_auth_pages( array $states, WP_Post $post ): array {
		$labels = [
			'login'          => __( 'DS Auth - Login Page', DS_THEME_TEXTDOMAIN ),
			'register'       => __( 'DS Auth - Registration Page', DS_THEME_TEXTDOMAIN ),
			'reset_password' => __( 'DS Auth - Password Reset Page', DS_THEME_TEXTDOMAIN ),
			'set_password'   => __( 'DS Auth - Set New Password Page', DS_THEME_TEXTDOMAIN ),
			'email_confirm'  => __( 'DS Auth - Email Confirmation Page', DS_THEME_TEXTDOMAIN ),
			'2fa'            => __( 'DS Auth - Two-Factor Auth Page', DS_THEME_TEXTDOMAIN ),
			'thank_you'      => __( 'DS Auth - Thank You Page', DS_THEME_TEXTDOMAIN ),
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

	public static function preserve_auth_page_data( array $data, array $postarr ): array {
		if ( 'page' !== $data['post_type'] || empty( $postarr['ID'] ) ) {
			return $data;
		}
		$post_id    = (int) $postarr['ID'];
		$auth_pages = get_option( self::OPTION_KEY, [] );
		$locked_ids = array_map( 'intval', array_values( $auth_pages ) );
		if ( in_array( $post_id, $locked_ids, true ) ) {
			$original_post = get_post( $post_id );
			if ( $original_post ) {
				$data['post_title'] = $original_post->post_title;
				$data['post_name']  = $original_post->post_name;
			}
		}
		return $data;
	}

	public static function remove_auth_page_row_actions( array $actions, WP_Post $post ): array {
		$auth_pages = get_option( self::OPTION_KEY, [] );
		$locked_ids = array_map( 'intval', array_values( $auth_pages ) );
		if ( in_array( $post->ID, $locked_ids, true ) ) {
			unset( $actions['trash'], $actions['delete'] );
		}
		return $actions;
	}

	public static function block_auth_page_deletion( array $allcaps, array $caps, array $args, WP_User $user ): array {
		if ( isset( $args[0] ) && in_array( $args[0], [ 'delete_post', 'delete_page' ], true ) ) {
			$post_id = $args[2] ?? 0;
			if ( $post_id ) {
				$auth_pages = get_option( self::OPTION_KEY, [] );
				$locked_ids = array_map( 'intval', array_values( $auth_pages ) );
				if ( in_array( (int) $post_id, $locked_ids, true ) ) {
					$allcaps[ $args[0] ] = false;
				}
			}
		}
		return $allcaps;
	}
}
