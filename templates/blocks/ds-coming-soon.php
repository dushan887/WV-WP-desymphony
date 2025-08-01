<?php
/**
 * Template for the DS Coming Soon block.
 *
 * @package Desymphony
 */


if (! defined('ABSPATH')) exit;

$class = 'ds-coming-soon';
if ( ! empty( $block['className'] ) ) {
    $class .= ' ' . esc_attr( $block['className'] );
}

if ( ! empty( $block['align'] ) ) {
    $class .= ' align' . esc_attr( $block['align'] );
}

?>

<section class="wv-full-section" style="background: url(/wp-content/themes/desymphony/src/images/background/DSK_Coming_Soon.jpg) center center / cover no-repeat; height: 100vh;">
    <div class="container p-64 text-center">
        <div class="row align-items-center justify-content-center">
            <div class="col-lg-6">
                <h2 class="display-2 fw-700 wv-color-c mb-24" ><?php esc_html_e( 'Coming Soon', DS_THEME_TEXTDOMAIN ); ?></h2>
                <p class="fs-24 wv-color-c mb-32 d-none" >
                    <span class="fw-500 d-block"><?php esc_html_e( 'This functionality will be available soon.', DS_THEME_TEXTDOMAIN ); ?></span>
                    <span><?php esc_html_e( 'We will inform you when it\'s ready!', DS_THEME_TEXTDOMAIN ); ?></span>
                </p>
                <div class="d-flex align-items-center justify-content-center gap-12">
                    <a class="wv-button wv-button-w wv-button-lg" href="/">Home Page</a>
                    <?php if ( is_user_logged_in() ) : ?>                        
                        <a class="wv-button wv-button-v wv-button-lg" href="/wv-profile/">My Account</a>
                    <?php endif; ?>
                </div>
        </div>
    </div>
</section>
