<?php
namespace Desymphony\Admin\Tables;

defined( 'ABSPATH' ) || exit;

/**
 * Handles AJAX endpoints for creating/updating custom tables.
 */
class DS_Admin_Tables {

    public static function init(): void {
        add_action(
            'wp_ajax_wv_addon_install_exhibitor_links_table',
            [ self::class, 'ajax_install_exhibitor_links_table' ]
        );
        add_action(
            'wp_ajax_wv_addon_install_exhibitor_products_table',
            [ self::class, 'ajax_install_exhibitor_products_table' ]
        );
        add_action(
            'wp_ajax_wv_addon_install_favorites_table',
            [ self::class, 'ajax_install_favorites_table' ]
        );
    }

    /**
	 * AJAX: (Re)create a **clean** exhibitor ↔ co‑exhibitor links table
	 * – Dev‑only helper – wipes any existing data before creating the fresh schema.
	 *
	 * Hook: add_action( 'wp_ajax_wv_addon_install_exhibitor_links_table', [ __CLASS__, 'ajax_install_exhibitor_links_table' ] );
	 */
	public static function ajax_install_exhibitor_links_table(): void {

		/* ── perms & nonce ───────────────────────────────────────────── */
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( [ 'message' => __( 'Unauthorized', DS_THEME_TEXTDOMAIN ) ], 403 );
		}
		check_ajax_referer( 'wv_addon_install_exhibitor_links_table', 'nonce' );

		/* ── build DDL ───────────────────────────────────────────────── */
		global $wpdb;

		$table   = $wpdb->prefix . 'wv_exhibitor_links';
		$charset = $wpdb->get_charset_collate();

		/* *** DEV ONLY: start from scratch for a tidy slate *** */
		$wpdb->query( "DROP TABLE IF EXISTS {$table}" );

		$sql = "
		CREATE TABLE {$table} (
		id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
		exhibitor_id    BIGINT UNSIGNED NOT NULL,
		coemail         VARCHAR(191)      NOT NULL,
		co_id           BIGINT UNSIGNED   DEFAULT NULL,
		status          ENUM('pending','accepted','declined') NOT NULL DEFAULT 'pending',
		token           CHAR(32)          NOT NULL,
		stand_code      VARCHAR(20)       DEFAULT NULL,
		date_invited    DATETIME          NOT NULL DEFAULT CURRENT_TIMESTAMP,
		date_responded  DATETIME          NULL,
		PRIMARY KEY   (id),
		UNIQUE KEY exhibitor_coemail (exhibitor_id, coemail),
		UNIQUE KEY token_unique       (token)
		) {$charset};";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );

		wp_send_json_success( [
			'message' => __( 'Exhibitor ↔ Co‑Exhibitor table (re)created cleanly.', DS_THEME_TEXTDOMAIN )
		] );
	}

	
	/**
	 * AJAX: Create or update the products table.
	 */
	public static function ajax_install_exhibitor_products_table(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( [ 'message' => __( 'Unauthorized', DS_THEME_TEXTDOMAIN ) ], 403 );
		}

		// CHECK the products-table‐nonce, not the auth_settings one
		check_ajax_referer( 'wv_addon_install_exhibitor_products_table', 'nonce' );

		global $wpdb;
		$table_name = $wpdb->prefix . 'wv_products';
		$charset    = $wpdb->get_charset_collate();

		$sql = "
		CREATE TABLE $table_name (
			id                   BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			sort_order           INT            NOT NULL DEFAULT 0,
			exhibitor_id         BIGINT UNSIGNED NOT NULL,
			title                VARCHAR(255)   NOT NULL,
			type                 VARCHAR(100)   DEFAULT '',
			category             VARCHAR(100)   DEFAULT '',
			variety              VARCHAR(255)   DEFAULT '',
			fruit_base           VARCHAR(100)   DEFAULT '',
			region               VARCHAR(100)   DEFAULT '',
			country              VARCHAR(100)   DEFAULT '',
			vintage_year         YEAR           DEFAULT NULL,
			volume_ml            INT            DEFAULT NULL,
			alcohol_pct          DECIMAL(5,2)   DEFAULT NULL,
			sugar_pct            DECIMAL(5,2)   DEFAULT NULL,
			acidity_pct          DECIMAL(5,2)   DEFAULT NULL,
			annual_production_l  VARCHAR(50)    DEFAULT '',
			current_stock_l      DECIMAL(10,2)  DEFAULT NULL,
			aging_process        VARCHAR(50)    DEFAULT '',
			distillation_method  VARCHAR(50)    DEFAULT '',
			special_certification VARCHAR(50)   DEFAULT '',
			submit_for_trophy    TINYINT(1)     NOT NULL DEFAULT 0,
			image_url            VARCHAR(255)   DEFAULT '',
			description          TEXT           DEFAULT NULL,
			date_created         DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
			date_updated         DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY  (id),
			KEY exhibitor_idx (exhibitor_id)
		) $charset;
		";


		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );

		wp_send_json_success( [
			'message' => __( 'Products table created/updated.', DS_THEME_TEXTDOMAIN ),
		] );
	}

	/**
	 * AJAX: Create or update the favorites table.
	 */
	public static function ajax_install_favorites_table(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( [ 'message' => __( 'Unauthorized', DS_THEME_TEXTDOMAIN ) ], 403 );
		}

		// CHECK the favorites-table‐nonce, not the auth_settings one
		check_ajax_referer( 'wv_addon_install_favorites_table', 'nonce' );

		global $wpdb;
		$table_name = $wpdb->prefix . 'wv_favorites';
		$charset    = $wpdb->get_charset_collate();

		$sql = "
		CREATE TABLE $table_name (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			user_id BIGINT UNSIGNED NOT NULL,
			target_type VARCHAR(20) NOT NULL,
			target_id BIGINT UNSIGNED NOT NULL,
			date_added DATETIME DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			KEY user_id (user_id),
			KEY target_id (target_id)
		) $charset;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );

		wp_send_json_success( [
			'message' => __( 'Favorites table created/updated.', DS_THEME_TEXTDOMAIN ),
		] );
	}
}
