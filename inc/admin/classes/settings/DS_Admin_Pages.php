<?php
namespace Desymphony\Admin\Settings;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Desymphony\Admin\DS_Admin_View_Loader;

/**
 * Renders content for each admin page by calling a view loader.
 *
 * This class handles the rendering of:
 * - General Settings
 * - Auth Settings
 * - Dashboard Settings (User Dashboard)
 * - Help
 */
class DS_Admin_Pages {

    public static function render_general_settings(): void {
        DS_Admin_View_Loader::render( 'general-settings' );
    }

    public static function render_auth_settings(): void {
        DS_Admin_View_Loader::render( 'auth-settings' );
    }

    public static function render_user_dashboard_settings(): void {
        DS_Admin_View_Loader::render( 'user-dashboard-settings' );
    }

    public static function render_stand_import(): void {
        DS_Admin_View_Loader::render( 'stand-import' );
    }

    public static function render_help_page(): void {
        DS_Admin_View_Loader::render( 'help' );
    }
}
