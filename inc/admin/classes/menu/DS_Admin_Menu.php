<?php
namespace Desymphony\Admin\Menu;

defined( 'ABSPATH' ) || exit;

use Desymphony\Admin\Settings\DS_Admin_Pages;

/**
 * Registers the Desymphony admin menu & submenus.
 */
class DS_Admin_Menu {

    public function init(): void {
        add_action( 'admin_menu', [ $this, 'register_menu' ] );
    }

    public function register_menu(): void {
        $capability = 'manage_options';

        // Top‐level “Desymphony” menu
        add_menu_page(
            __( 'Desymphony', 'desymphony' ),          // Page title
            __( 'Desymphony', 'desymphony' ),          // Menu title
            $capability,                               // Capability
            'desymphony',                              // Menu slug
            [ DS_Admin_Pages::class, 'render_general_settings' ], // Callback
            'dashicons-admin-generic',                 // Icon
            59                                         // Position
        );

        // “General Settings” submenu
        add_submenu_page(
            'desymphony',
            __( 'General Settings', 'desymphony' ),
            __( 'General Settings', 'desymphony' ),
            $capability,
            'desymphony-general-settings',
            [ DS_Admin_Pages::class, 'render_general_settings' ]
        );

        // “Auth Settings” submenu
        add_submenu_page(
            'desymphony',
            __( 'Auth Settings', 'desymphony' ),
            __( 'Auth Settings', 'desymphony' ),
            $capability,
            'desymphony-auth-settings',
            [ DS_Admin_Pages::class, 'render_auth_settings' ]
        );

        // “User Dashboard” submenu
        add_submenu_page(
            'desymphony',
            __( 'User Dashboard', 'desymphony' ),
            __( 'User Dashboard', 'desymphony' ),
            $capability,
            'desymphony-user-dashboard-settings',
            [ DS_Admin_Pages::class, 'render_user_dashboard_settings' ]
        );

        // “Stand Import” submenu
        add_submenu_page(
            'desymphony',
            __( 'Stand Import', 'desymphony' ),
            __( 'Stand Import', 'desymphony' ),
            $capability,
            'desymphony-stand-import',
            [ DS_Admin_Pages::class, 'render_stand_import' ]
        );

        // “Registrations” submenu
        add_submenu_page(
            'desymphony',
            __( 'Registrations', 'desymphony' ),
            __( 'Registrations', 'desymphony' ),
            $capability,
            'desymphony-registrations',
            [ DS_Admin_Pages::class, 'render_registrations' ]
        );


        // “Help” submenu
        add_submenu_page(
            'desymphony',
            __( 'Help', 'desymphony' ),
            __( 'Help', 'desymphony' ),
            $capability,
            'desymphony-help',
            [ DS_Admin_Pages::class, 'render_help_page' ]
        );
    }
}
