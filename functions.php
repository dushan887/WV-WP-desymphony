<?php
/**
 * Desymphony Theme Functions
 *
 * @package Desymphony
 */

defined( 'ABSPATH' ) || exit;

/**
 * ----------------------------------------------------------------------------
 * 1) THEME CONSTANTS
 * ----------------------------------------------------------------------------
 */
define( 'DS_THEME_VERSION',    '1.0.0' );
define( 'DS_THEME_TEXTDOMAIN', 'desymphony' );
define( 'DS_THEME_DIR',        get_template_directory() );
define( 'DS_THEME_URI',        get_template_directory_uri() );

/**
 * Load environment variables from .env file
 */
$env_file = DS_THEME_DIR . '/.env';
if ( file_exists( $env_file ) ) {
    $env_vars = file( $env_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );
    foreach ( $env_vars as $line ) {
        if ( strpos( trim( $line ), '#' ) === 0 ) continue; // Skip comments
        if ( strpos( $line, '=' ) !== false ) {
            list( $key, $value ) = explode( '=', $line, 2 );
            $key = trim( $key );
            $value = trim( $value );
            if ( ! defined( $key ) && ! empty( $key ) ) {
                define( $key, $value );
            }
        }
    }
}

// Fallback values if .env file doesn't exist or keys are missing
if ( ! defined( 'EUR_TO_RSD' ) ) {
    define( 'EUR_TO_RSD', 117.5337 );
}
if ( ! defined( 'WV_RECAP_SITE_KEY' ) ) {
    define( 'WV_RECAP_SITE_KEY', '' );
}
if ( ! defined( 'WV_RECAP_SECRET_KEY' ) ) {
    define( 'WV_RECAP_SECRET_KEY', '' );
}


spl_autoload_register( function( $class ) {
    // only our namespace
    $prefix = 'Desymphony\\';
    if ( strncmp( $prefix, $class, strlen( $prefix ) ) !== 0 ) {
        return;
    }
    // convert namespace to file path
    $relative = str_replace( '\\', '/', substr( $class, strlen( $prefix ) ) );
    $file = __DIR__ . '/inc/' . $relative . '.php';
    if ( file_exists( $file ) ) {
        require $file;
    }
} );


/**
 * ----------------------------------------------------------------------------
 * 2) COMPOSER AUTOLOAD (PSR-4)
 * ----------------------------------------------------------------------------
 */
$autoload = DS_THEME_DIR . '/vendor/autoload.php';
if ( file_exists( $autoload ) ) {
    require_once $autoload;
}

/**
 * ----------------------------------------------------------------------------
 * 3) CORE CLASSES
 * ----------------------------------------------------------------------------
 */
// Theme core
require_once DS_THEME_DIR . '/inc/theme/class-ds-setup.php';
require_once DS_THEME_DIR . '/inc/theme/class-ds-enqueue.php';
require_once DS_THEME_DIR . '/inc/theme/class-ds-blocks.php';
require_once DS_THEME_DIR . '/inc/theme/class-ds-cpt-registrar.php';
require_once DS_THEME_DIR . '/inc/theme/class-ds-taxonomy-registrar.php';
require_once DS_THEME_DIR . '/inc/theme/class-ds-secondary-image.php';
require_once DS_THEME_DIR . '/inc/theme/class-ds-lecutrer-meta.php';
require_once DS_THEME_DIR . '/inc/theme/class-ds-card-meta.php';
require_once DS_THEME_DIR . '/inc/theme/class-ds-podcast-meta.php';
require_once DS_THEME_DIR . '/inc/theme/class-ds-award-meta.php';

// Admin
require_once DS_THEME_DIR . '/inc/admin/class-ds-admin.php';

// Main orchestrator
require_once DS_THEME_DIR . '/inc/class-ds-main.php';

/**
 * ----------------------------------------------------------------------------
 * 4) BOOTSTRAP
 * ----------------------------------------------------------------------------
 */
add_action( 'after_setup_theme', function() {
    // a) load translations
    load_theme_textdomain( DS_THEME_TEXTDOMAIN, DS_THEME_DIR . '/languages' );
    
    // b) initialize theme features
    \Desymphony\Theme\DS_Setup::init();
    \Desymphony\Theme\DS_CPT_Registrar::init();
    \Desymphony\Theme\DS_Blocks::init();
    \Desymphony\Theme\DS_Taxonomy_Registrar::init();
    \Desymphony\Theme\DS_Secondary_Image::init();
    \Desymphony\Theme\DS_Lecturer_Meta::init();
    \Desymphony\Theme\DS_Card_Meta::init();
    \Desymphony\Theme\DS_Podcast_Meta::init();
    \Desymphony\Theme\DS_Award_Meta::init(); 

}, 0 );

// c) enqueue front-end assets
add_action( 'init', function() {
    \Desymphony\Theme\DS_Enqueue::init();
});


// d) enqueue admin assets is handled by DS_Admin itself

// e) run main`
add_action( 'after_setup_theme', function () {
    $main = new \Desymphony\DS_Main();
    $main->run();
}, 99 );   


/**
 * Force a “coming-soon” splash for everyone except allow-listed IPs
 * and logged-in admins.  Works for all WooCommerce pages.
 * Drop into functions.php or, better, a must-use plugin.
 */
function ds_force_coming_soon() {

	/* ───── 0. Allow-listed IPs ───── */
	$allow_ips = [ '109.72.63.342' ];                       // add/remove as needed
	$remote_ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ??
	             $_SERVER['REMOTE_ADDR']        ?? '';

	if ( in_array( trim( explode( ',', $remote_ip )[0] ), $allow_ips, true ) ) {
		return;
	}

	/* ───── 1. Skip redirects for admins & system calls ───── */
	if ( ( is_user_logged_in() && current_user_can( 'manage_options' ) ) // admins only
	     || is_admin()                                                   // wp-admin/*
	     || ( defined( 'DOING_AJAX' ) && DOING_AJAX )                    // admin-ajax / wc-ajax
	     || ( defined( 'REST_REQUEST' ) && REST_REQUEST )                // WP & WC REST
	     || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
		return;
	}

	/* ───── 2. Allow the splash page & core auth screens ───── */
	$coming_soon_path = '/coming-soon';                   // adjust slug if needed
	$current_path     = trim( $_SERVER['REQUEST_URI'], '/' );

	if ( strpos( $current_path, ltrim( $coming_soon_path, '/' ) ) === 0
	     || preg_match( '#^wp-(login|register)\.php#', $current_path ) ) {
		return;
	}

	/* ───── 3. Redirect everyone else ───── */
	wp_safe_redirect( site_url( $coming_soon_path ), 302 );
	exit;
}
// add_action( 'template_redirect', 'ds_force_coming_soon', 1 );

/**
 * Block every WooCommerce front-end page (shop, products, cart,
 * checkout, account, endpoints, product-only searches, etc.)
 * for anyone who is NOT an admin.
 */
function ds_block_woocommerce_pages() {

	/* 0. Allow back-end, WP-CLI, and admins */
	if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) || current_user_can( 'manage_options' ) ) {
		return;
	}

	/* 1. Bail if WooCommerce isn’t active */
	if ( ! function_exists( 'is_woocommerce' ) ) {
		return;
	}

	/* 2. Helper: detect a “products only” search */
	$is_product_search = is_search() && (
		/* Search URL like ?s=foo&post_type=product */
		( get_query_var( 'post_type' ) === 'product' ) ||
		( is_array( get_query_var( 'post_type' ) ) && in_array( 'product', get_query_var( 'post_type' ), true ) )
	);

	/* 3. Is the current request *any* WooCommerce context? */
	if (
		// is_woocommerce()            || 
		is_shop()                   ||
		is_singular( 'product' )    ||
		is_product_category()       ||
		is_product_tag()            ||
		// is_cart()                   ||
		// is_checkout()               ||
		is_account_page()           ||
		// is_wc_endpoint_url()        || 
		$is_product_search
	) {
		wp_safe_redirect( home_url( '/wv-profile/' ), 302 ); 
		exit;
	}
}

add_action( 'template_redirect', 'ds_block_woocommerce_pages', 999 );