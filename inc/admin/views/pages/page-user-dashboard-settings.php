<?php
/**
 * Content for User Dashboard Settings page.
 *
 * Allows assigning WP pages for the following Dashboard sections:
 * - Dashboard Home
 * - Meeting Requests
 * - Calendar
 * - Products
 * - Services
 * - Co-Exhibitors
 * - Profile Management
 * - Saved
 * - Messages
 * - Exhibition Application Form
 * - Events
 * - Members
 * - Notifications
 * 
 *
 * Provides buttons to:
 * - Regenerate (Delete & Recreate) Dashboard Pages
 * - Create or Update Missing Dashboard Pages
 * - Save Settings
 *
 * @package WV_Addon
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define the list of Dashboard sections we manage.
$dashboard_pages = [
    'dashboard'   => __( 'Dashboard Home', DS_THEME_TEXTDOMAIN ),
    'meetings'    => __( 'Meeting Requests', DS_THEME_TEXTDOMAIN ),
    'calendar'    => __( 'Calendar', DS_THEME_TEXTDOMAIN ),
    'products'    => __( 'Products', DS_THEME_TEXTDOMAIN ),
    'services'    => __( 'Services', DS_THEME_TEXTDOMAIN ),
    'co-ex'      => __( 'Co-Exhibitors', DS_THEME_TEXTDOMAIN ),
    'profile'     => __( 'Profile Management', DS_THEME_TEXTDOMAIN ),
    'saved'       => __( 'Saved', DS_THEME_TEXTDOMAIN ),
    'messages'    => __( 'Messages', DS_THEME_TEXTDOMAIN ),
    'application' => __( 'Exhibition Application Form', DS_THEME_TEXTDOMAIN ),
    'events'      => __( 'Events', DS_THEME_TEXTDOMAIN ),
    'members'     => __( 'Members', DS_THEME_TEXTDOMAIN ),
    'notifications' => __( 'Notifications', DS_THEME_TEXTDOMAIN ),
];

// Retrieve stored assignments.
$stored_pages = get_option( 'wv_addon_dashboard_pages', [] );
?>

<h2><?php esc_html_e( 'User Dashboard Settings', DS_THEME_TEXTDOMAIN ); ?></h2>
<p class="description">
    <?php esc_html_e( 'Configure which WordPress pages will be used to display each section of the user dashboard.', DS_THEME_TEXTDOMAIN ); ?>
</p>

<div id="wv-addon-dashboard-settings">

    <!-- TOP BUTTONS -->
    <div class="wv-addon-dashboard-page-actions" style="margin-bottom: 15px;">
        <button type="button" class="button button-primary" id="wv-addon-regenerate-dashboard-pages">
            <?php esc_html_e( 'Regenerate (Delete & Recreate) Dashboard Pages', DS_THEME_TEXTDOMAIN ); ?>
        </button>
        &nbsp;
        <button type="button" class="button" id="wv-addon-create-update-missing-dashboard-pages">
            <?php esc_html_e( 'Create or Update Missing Dashboard Pages', DS_THEME_TEXTDOMAIN ); ?>
        </button>
    </div>

    <!-- TABLE OF DASHBOARD PAGE SELECTIONS -->
    <table class="form-table">
        <thead>
            <tr>
                <th><?php esc_html_e( 'Dashboard Section', DS_THEME_TEXTDOMAIN ); ?></th>
                <th><?php esc_html_e( 'Assigned Page', DS_THEME_TEXTDOMAIN ); ?></th>
                <th><?php esc_html_e( 'Actions', DS_THEME_TEXTDOMAIN ); ?></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ( $dashboard_pages as $key => $label ) :
            $selected_page_id = ! empty( $stored_pages[ $key ] ) ? (int) $stored_pages[ $key ] : 0;
        ?>
            <tr>
                <td>
                    <strong><?php echo esc_html( $label ); ?></strong>
                </td>
                <td>
                    <?php
                    wp_dropdown_pages( [
                        'name'              => "wv_addon_dashboard_page_{$key}",
                        'selected'          => $selected_page_id,
                        'show_option_none'  => __( '— Select a page —', DS_THEME_TEXTDOMAIN ),
                        'option_none_value' => 0,
                    ] );
                    ?>
                </td>
                <td>
                    <?php if ( $selected_page_id ) : ?>
                        <a href="<?php echo esc_url( get_edit_post_link( $selected_page_id ) ); ?>" 
                           target="_blank" 
                           class="button button-secondary">
                            <?php esc_html_e( 'Edit Page', DS_THEME_TEXTDOMAIN ); ?>
                        </a>
                        <a href="<?php echo esc_url( get_permalink( $selected_page_id ) ); ?>" 
                           target="_blank" 
                           class="button button-secondary">
                            <?php esc_html_e( 'View Page', DS_THEME_TEXTDOMAIN ); ?>
                        </a>
                    <?php else : ?>
                        <span class="description"><?php esc_html_e( 'No page selected.', DS_THEME_TEXTDOMAIN ); ?></span>
                    <?php endif; ?>
				</td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <hr/>

    <!-- SAVE SETTINGS BUTTON -->
    <div class="wv-addon-save-dashboard-settings">
        <button type="button" class="button button-primary" id="wv-addon-save-dashboard-settings">
            <?php esc_html_e( 'Save Settings', DS_THEME_TEXTDOMAIN ); ?>
        </button>
    </div>

</div>

<p class="description" style="margin-top:15px;">
    <?php esc_html_e( 'Please remember to save changes before regenerating or creating missing pages.', DS_THEME_TEXTDOMAIN ); ?>
</p>
