<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
use Desymphony\Helpers\DS_Utils as Utils;
// If $dashboard_view is not set or is empty, use a default fallback.
$dashboard_class = '';
if ( ! empty( $dashboard_view ) ) {
    // Extract the filename without extension.
    $dashboard_class = 'wv-' . basename( $dashboard_view, '.php' );
}
?>

<div id="wv-dashboard-wrapper" class="<?php echo esc_attr( $dashboard_class ); ?>">

    <header id="wv-dashboard-header" class="wv-dashboard-header">
        <?php 
            $header_file = DS_THEME_DIR . '/inc/public/views/partials/modules/dashboard-header.php';
            if ( file_exists( $header_file ) ) {
                // include $header_file;
            }

            if ($dashboard_class == "wv-dashboard-profile") {
                $subnav_file = DS_THEME_DIR . '/inc/public/views/partials/modules/dashboard-subnav-general.php';
                if ( file_exists( $subnav_file ) && Utils::is_exhibitor()) {
                    include $subnav_file;
                }
            } elseif ($dashboard_class == "wv-dashboard-products") {
                $subnav_file = DS_THEME_DIR . '/inc/public/views/partials/modules/dashboard-subnav-products.php';
                if ( file_exists( $subnav_file ) ) {
                    include $subnav_file;
                }
            } elseif ($dashboard_class == "wv-dashboard-saved") {
                $subnav_file = DS_THEME_DIR . '/inc/public/views/partials/modules/dashboard-subnav-saved.php';
                if ( file_exists( $subnav_file ) ) {
                    include $subnav_file;
                }
            }

        ?>        
    </header>

    <!-- Global Notification Area -->
    <div id="wv-dashboard-notifications">
        <!-- Notifications injected via AJAX or PHP -->
    </div>
    
    <!-- Dashboard Content Area -->
    <div id="wv-dashboard-content" style="background-color: var(--wv-c_10)">
        
            <!-- <h1><?php esc_html_e( 'Dashboard', 'wv-addon' ); ?></h1> -->
            <?php 
            if ( ! empty( $dashboard_view ) && file_exists( $dashboard_view ) ) {
                include $dashboard_view;
            } else {
                echo '<p>' . esc_html__( 'Dashboard section not found.', 'wv-addon' ) . '</p>';
            }
            ?>
    </div>

    <!-- Dashboard Footer -->
    <footer id="wv-dashboard-footer">
        <!-- Optional footer content -->
    </footer>
</div><!-- #wv-dashboard-wrapper -->
