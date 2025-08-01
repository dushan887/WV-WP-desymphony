<?php

/**
 * Block Name: ds-ig-feed
 *
 * This is the template that displays the Instagram feed block.
 *
 * @package WordPress
 * @subpackage Desymphony
 * @since 1.0.0
 */

$class = 'ds-ig-feed';
if ( ! empty( $block['className'] ) ) {
    $class .= ' ' . esc_attr( $block['className'] );
}

if ( ! empty( $block['align'] ) ) {
    $class .= ' align' . esc_attr( $block['align'] );
}

?>

<section class="wv-open-podcast-carousel pt-48 pb-12 <?php echo esc_attr( $class ); ?>" style="background-color: var(--wv-w)">
    <div class="container container-1024 pb-24">
        <div class="d-flex align-items-center justify-content-between">
            <h3 class="h1 fw-700">Social media</h3>
            <a href="https://www.instagram.com/winevisionbyopenbalkan/?hl=en" class="wv-button wv-button-pill wv-button-blue fw-500 ls-2" target="_blank">
                <i class="wv wv_ig fs-24 me-8"></i>
            Follow us
            </a>
        </div>
    </div>

    <div class="container-fluid px-12">        
        <?php echo do_shortcode( '[instagram-feed feed=1]' ); ?>
    </div>

</section>