
<?php
/**
 * Dashboard Header
 * Roleâ€based navigation menu.
 *
 * @package Wv_Addon
 */

use Desymphony\Helpers\DS_Utils as Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$user_id    = get_current_user_id();
$user_roles = Utils::current_user_roles();

// Company name (meta or first+last)
$company_name = Utils::get_company_name( $user_id );

// Profile URL
$dashboard_pages = (array) get_option( 'DS_dashboard_pages', [] );
$profile_page_id = isset( $dashboard_pages['profile'] ) ? absint( $dashboard_pages['profile'] ) : 0;
$profile_url     = ( $profile_page_id && get_post( $profile_page_id ) )
    ? get_permalink( $profile_page_id )
    : '#';

// Avatar URL
$profile_picture = get_user_meta( $user_id, 'wv_profile_picture', true );
$avatar_url      = $profile_picture ?: 'https://placehold.co/80';

// Decide menu sections by role
if ( Utils::is_administrator() || Utils::is_exhibitor() ) {
    $menu_sections = [
        'dashboard'   => 'Home',
        'meetings'    => 'Meeting Requests',
        'calendar'    => 'Calendar',
        'co_ex'      => 'Co-Exhibitors',
        'products'    => 'Products',
        'messages'    => 'Messages',
        'saved'       => 'Saved',
        'services'    => 'Services',
        'application' => 'Application',
    ];
} elseif ( Utils::is_buyer() || ( Utils::is_visitor() && ( Utils::get_visitor_endeavour() === 'Official Company' ) ) ) {
    $menu_sections = [
        'dashboard' => 'Home',
        'meetings'  => 'Meeting Requests',
        'calendar'  => 'Calendar',
        'messages' => 'Messages',
    ];
} elseif ( Utils::is_visitor() ) {
    $menu_sections = [
        'dashboard'   => 'Home',
    ];
} else {
    // Fallback for unexpected cases
    $menu_sections = [
        'dashboard' => 'Home',
    ];
}

// Build nav links
$nav_links = [];
foreach ( $menu_sections as $key => $label ) {
    $page_id = isset( $dashboard_pages[ $key ] ) ? absint( $dashboard_pages[ $key ] ) : 0;
    if ( $page_id && get_post( $page_id ) ) {
        $nav_links[ $key ] = [
            'label' => $label,
            'url'   => get_permalink( $page_id ),
        ];
    }
}
?>

<div class="wv-container-1320 px-0 wv-z-1000 wv-position-relative">
    <div class="row">
        <!-- Left: Avatar + Company Name -->
        <div class="col-lg-4 d-flex align-items-center">
            <div class="wv-avatar-circle">
                <a href="<?php echo esc_url( $profile_url ); ?>">
                    <img src="<?php echo esc_url( $avatar_url ); ?>" alt="<?php esc_attr_e( 'User Avatar', 'wv-addon' ); ?>" />
                </a>
            </div>
            <div class="wv-company-name ms-12">
                <h4 class="m-0 color-w pt-12">
                    <?php echo esc_html( $company_name ); ?>
                </h4>
            </div>
        </div>

        <!-- Right: Navigation -->
        <div class="col-lg-8 pt-12 d-flex wv-justify-end">
            <nav id="wv-dashboard-nav" class="d-flex align-items-center">
                <ul>
                    <?php foreach ( $nav_links as $link ) : ?>
                        <li>
                            <a href="<?php echo esc_url( $link['url'] ); ?>">
                                <?php echo esc_html( $link['label'] ); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                    <li>
                        <a href="<?php echo esc_url( wp_logout_url( get_permalink() ) ); ?>">
                            <?php esc_html_e( 'Logout', 'wv-addon' ); ?>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>
