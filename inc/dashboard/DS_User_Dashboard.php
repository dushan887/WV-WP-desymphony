<?php
namespace Desymphony\Dashboard;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Desymphony\Dashboard\DS_Dashboard_View_Loader;


/**
 * Class DS_User_Dashboard
 *
 * Registers the global [wv_dashboard] shortcode and all individual shortcodes for the
 * dashboard sections. Each shortcode uses the DS_Dashboard_View_Loader to render the appropriate
 * view file.
 *
 * Registered shortcodes:
 *   [wv_dashboard] – global dashboard (GET parameter 'section' defaults to “home”)
 *   [wv_dashboard_home] – dashboard home section
 *   [wv_meeting] – meeting requests section
 *   [wv_calendar] – calendar section
 *   [wv_products] – products section
 *   [wv_services] – services section
 *   [wv_co_ex] – co-exhibitors section
 *   [wv_profile] – profile management section
 *   [wv_application] – exhibition application form section
*  [wv_saved] – saved items section
*  [wv_messages] – messages section
* [wv_events] – events section
* [wv_members] – members section
* [wv_notifications] – notifications section
 * 
 */
class DS_User_Dashboard {

    /**
     * Constructor.
     *
     * Registers all the shortcodes.
     */
    public function __construct() {
        // Global dashboard – uses the GET parameter 'section' to determine which section to load.
        add_shortcode( 'wv_dashboard', [ $this, 'render_dashboard' ] );

        // Individual section shortcodes.
        add_shortcode( 'wv_dashboard_home', [ $this, 'render_home' ] );        
        add_shortcode( 'wv_profile', [ $this, 'render_profile' ] );
        add_shortcode( 'wv_meeting', [ $this, 'render_meetings' ] );
        add_shortcode( 'wv_calendar', [ $this, 'render_calendar' ] );
        add_shortcode( 'wv_products', [ $this, 'render_products' ] );
        add_shortcode( 'wv_co_ex', [ $this, 'render_co_ex' ] );
        add_shortcode( 'wv_services', [ $this, 'render_services' ] );
        add_shortcode( 'wv_saved', [ $this, 'render_saved' ] );
        add_shortcode( 'wv_messages', [ $this, 'render_messages' ] );
        add_shortcode( 'wv_application', [ $this, 'render_application' ] );
        add_shortcode( 'wv_events', [ $this, 'render_events' ] );
        add_shortcode( 'wv_members', [ $this, 'render_members' ] );
        add_shortcode( 'wv_notifications', [ $this, 'render_notifications' ] );

        
    }

    public function render_home( $atts = [], $content = null ) {
        return $this->render_section( 'home' );
    }

    public function render_profile( $atts = [], $content = null ) {
        return $this->render_section( 'profile' );
    }

    public function render_meetings( $atts = [], $content = null ) {
        return $this->render_section( 'meetings' );
    }

    public function render_calendar( $atts = [], $content = null ) {
        return $this->render_section( 'calendar' );
    }

    public function render_products( $atts = [], $content = null ) {
        return $this->render_section( 'products' );
    }

    public function render_co_ex( $atts = [], $content = null ) {
        return $this->render_section( 'co_ex' );
    }
    
    public function render_services( $atts = [], $content = null ) {
        return $this->render_section( 'services' );
    }

    public function render_saved( $atts = [], $content = null ) {
        return $this->render_section( 'saved' );
    }

    public function render_messages( $atts = [], $content = null ) {
        return $this->render_section( 'messages' );
    }

    public function render_application( $atts = [], $content = null ) {
        return $this->render_section( 'application' );
    }

    public function render_events( $atts = [], $content = null ) {
        return $this->render_section( 'events' );
    }

    public function render_members( $atts = [], $content = null ) {
        return $this->render_section( 'members' );
    }

    public function render_notifications( $atts = [], $content = null ) {
        return $this->render_section( 'notifications' );
    }


    /**
     * Renders the global dashboard.
     *
     * Reads the GET parameter "section" (defaults to "home") and loads that section.
     *
     * @return string The complete dashboard HTML.
     */
    public function render_dashboard() : string {
        ob_start();
        $section = isset( $_GET['section'] )
            ? sanitize_text_field( wp_unslash( $_GET['section'] ) )
            : 'home';
        $loader = new DS_Dashboard_View_Loader();
        $loader->render( $section );
        return ob_get_clean();
    }

    /**
     * Helper method that loads a specific dashboard section view.
     *
     * @param string $section The section key (expects a file named dashboard-{section}.php).
     * @return string The rendered section HTML.
     */
    private function render_section( string $section ) : string {
        ob_start();
        $loader = new DS_Dashboard_View_Loader();
        $loader->render( $section );
        return ob_get_clean();
    }
}
