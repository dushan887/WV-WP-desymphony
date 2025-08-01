<?php
namespace Desymphony\Favorites;

use Desymphony\Database\DS_Favorites_Repository; 
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class DS_Favorites_Manager {

	public static function init() {
		// Register AJAX for add/remove
		add_action( 'wp_ajax_wv_add_favorite',   [ __CLASS__, 'ajax_add_favorite' ] );
		add_action( 'wp_ajax_wv_remove_favorite',[ __CLASS__, 'ajax_remove_favorite' ] );
		// If you want non-logged in to see partial functionality, add wp_ajax_nopriv_...
	}

	/**
	 * Add a favorite. If it already exists, do nothing.
	 */
	public static function add_favorite( int $user_id, string $target_type, int $target_id ): bool {
		$repo = new DS_Favorites_Repository();
		return $repo->add_favorite( $user_id, $target_type, $target_id );
	}

	/**
	 * Remove a favorite. If not exist, do nothing.
	 */
	public static function remove_favorite( int $user_id, string $target_type, int $target_id ): bool {
		$repo = new DS_Favorites_Repository();
		return $repo->remove_favorite( $user_id, $target_type, $target_id );
	}

	/**
	 * Check if user has this item favorited
	 */
	public static function is_favorited( int $user_id, string $target_type, int $target_id ): bool {
		$repo = new DS_Favorites_Repository();
		return $repo->is_favorited( $user_id, $target_type, $target_id );
	}

	/**
	 * Fetch a user's favorites. If you want them separated by type, pass $target_type.
	 */
	public static function get_user_favorites( int $user_id, ?string $target_type = null ): array {
		$repo = new DS_Favorites_Repository();
		return $repo->get_user_favorites( $user_id, $target_type );
	}

	/* =====================
	 * AJAX handlers below
	 * =====================*/

	public static function ajax_add_favorite() {
		check_ajax_referer( 'wv_favorite_nonce', 'security' );

		if ( ! is_user_logged_in() ) {
			wp_send_json_error( [ 'message' => 'Not logged in' ] );
		}

		$user_id     = get_current_user_id();
		$target_type = isset($_POST['target_type']) ? sanitize_text_field($_POST['target_type']) : '';
		$target_id   = isset($_POST['target_id'])   ? (int) $_POST['target_id'] : 0;

		if ( ! $target_type || ! $target_id ) {
			wp_send_json_error( [ 'message' => 'Invalid data' ] );
		}

		$success = self::add_favorite( $user_id, $target_type, $target_id );
		if ( $success ) {
			wp_send_json_success( [ 'message' => 'Added to favorites' ] );
		} else {
			wp_send_json_error( [ 'message' => 'DB error adding favorite' ] );
		}
	}

	public static function ajax_remove_favorite() {
		check_ajax_referer( 'wv_favorite_nonce', 'security' );

		if ( ! is_user_logged_in() ) {
			wp_send_json_error( [ 'message' => 'Not logged in' ] );
		}

		$user_id     = get_current_user_id();
		$target_type = isset($_POST['target_type']) ? sanitize_text_field($_POST['target_type']) : '';
		$target_id   = isset($_POST['target_id'])   ? (int) $_POST['target_id'] : 0;

		if ( ! $target_type || ! $target_id ) {
			wp_send_json_error( [ 'message' => 'Invalid data' ] );
		}

		$success = self::remove_favorite( $user_id, $target_type, $target_id );
		if ( $success ) {
			wp_send_json_success( [ 'message' => 'Removed from favorites' ] );
		} else {
			wp_send_json_error( [ 'message' => 'DB error removing favorite or item not found' ] );
		}
	}
}
