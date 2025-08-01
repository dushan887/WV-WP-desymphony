<?php
namespace Desymphony;

use Desymphony\Dashboard\DS_User_Dashboard;
use Desymphony\Dashboard\DS_User_Dashboard_Profile;
use Desymphony\Dashboard\DS_CoEx_Manager;
use Desymphony\Dashboard\DS_Exhibitor_Products_Manager;
use Desymphony\Dashboard\DS_Stand_Assign;

use Desymphony\Favorites\DS_Favorites_Manager;
use Desymphony\Exhibitor\DS_Exhibitor_Application;

use Desymphony\Public\DS_Public_Init;

use Desymphony\Woo\DS_Woo_Stand_Setup;
use Desymphony\Woo\DS_Woo_Stand_Map;
use Desymphony\Woo\DS_Woo_Stand_Cart;
use Desymphony\Woo\DS_Woo_Stand_Extras;
use Desymphony\Woo\DS_Woo_Failed_Return;
use Desymphony\Woo\DS_Woo_Order_Cleanup;
use Desymphony\Woo\DS_Woo_Stand_PDF;
use Desymphony\Woo\DS_Woo_CoEx_Slots;




defined('ABSPATH') || exit;

/**
 * Main aggregator class for ex-plugin code.
 * Example usage: $main = new DS_Main(); $main->run();
 */
class DS_Main {

    public function run() {

        if ( is_admin() ) {
            new Admin\DS_Admin( DS_THEME_VERSION );
        }

        new Auth\DS_Auth_Init( DS_THEME_VERSION );
        new Dashboard\DS_User_Dashboard( DS_THEME_VERSION );
        new Dashboard\DS_User_Dashboard_Profile( DS_THEME_VERSION );
        new Dashboard\DS_CoEx_Manager( DS_THEME_VERSION );
        Dashboard\DS_Exhibitor_Products_Manager::init();
        new Favorites\DS_Favorites_Manager( DS_THEME_VERSION );
        new Exhibitor\DS_Exhibitor_Application( DS_THEME_VERSION );
        
        $public_init = new DS_Public_Init( DS_THEME_VERSION );
        $public_init->init();

        // Initialize WooCommerce stands setup if WooCommerce is active
       
        // DS_Woo_Stand_Setup::init();

        if ( ! class_exists( DS_Woo_Failed_Return::class, false ) ) {
			// adjust the path if you keep the file elsewhere
			require_once get_template_directory() . '/inc/woocommerce/DS_Woo_Failed_Return.php';
		}
		DS_Woo_Failed_Return::boot();

        if ( ! class_exists( DS_Woo_Order_Cleanup::class, false ) ) {
            require_once get_template_directory() . '/inc/woocommerce/DS_Woo_Order_Cleanup.php';
        }
        DS_Woo_Order_Cleanup::init();
       
        if (class_exists('\Desymphony\Woo\DS_Woo_Stand_Setup')) {
            \Desymphony\Woo\DS_Woo_Stand_Setup::init();
        }

        if (class_exists('\Desymphony\Woo\DS_Woo_Stand_Cart')) {
            \Desymphony\Woo\DS_Woo_Stand_Cart::init();
        }

        if ( class_exists( '\Desymphony\Woo\DS_Woo_Stand_Extras' ) ) {
            \Desymphony\Woo\DS_Woo_Stand_Extras::init();
        }

        if ( class_exists( '\Desymphony\Woo\DS_Woo_CoEx_Slots' ) ) {
            \Desymphony\Woo\DS_Woo_CoEx_Slots::init();
        }

        if ( class_exists( '\Desymphony\Dashboard\DS_Stand_Assign' ) ) {
            \Desymphony\Dashboard\DS_Stand_Assign::init();
        }
        
        if ( class_exists( '\Desymphony\Woo\DS_Woo_Stand_PDF' ) ) {
            \Desymphony\Woo\DS_Woo_Stand_PDF::init();
        }

        if ( class_exists( 'WP_CLI' ) ) {
            require_once get_template_directory() . '/inc/cli/cli-init.php';
        }

        

    }

    public function init_modules() {
        // Initialize any submodules after WP/Plugins are loaded.
    }

}
