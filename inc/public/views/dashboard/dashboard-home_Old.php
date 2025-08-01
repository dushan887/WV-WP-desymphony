<?php
/**
 * Dashboard “home” view
 *
 * @package Wv_Addon
 */

use Desymphony\Helpers\DS_Utils as Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="container container-1024 px-0 py-64">

    <div class="row align-items-center justify-content-center">
        <div class="col-lg-6 text-center">
            <h1><?php esc_html_e( 'Hello & Welcome!', 'wv-addon' ); ?></h1>
            <p class="fs-24">
                <strong><?php esc_html_e( 'This is Your New Exhibitor Account.', 'wv-addon' ); ?></strong><br/>
                <?php esc_html_e( 'Your next tasks are to start filling in the 2025 Exhibitor Application Form & Invite Your Co‑Exhibitors to Join.', 'wv-addon' ); ?>
            </p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <?php include DS_THEME_DIR . '/inc/public/views/partials/modules/explore-form.php'; ?>
        </div>
        <div class="col-lg-6">
            <?php include DS_THEME_DIR . '/inc/public/views/partials/modules/invite-co-exhibitors.php'; ?>
        </div>
    </div>

    <?php if ( Utils::is_administrator() || Utils::is_exhibitor() ) : ?>
        <div class="row mt-24">
            <div class="col-lg-6">
                <?php include DS_THEME_DIR . '/inc/public/views/partials/modules/invite-co-exhibitors.php'; ?>
            </div>
            <div class="col-lg-6">
                <?php include DS_THEME_DIR . '/inc/public/views/partials/modules/meeting-requests-received.php'; ?>
                <div class="d-block" style="margin: 16px 0;"></div>
                <?php include DS_THEME_DIR . '/inc/public/views/partials/modules/meeting-requests-sent.php'; ?>
            </div>
        </div>
    <?php endif; ?>

</div>
