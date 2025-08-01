<?php

/**
 * Block Name: ds-partners-grid
 *
 * This is the template that displays the Partners Grid block.
 *
 * @package WordPress
 * @subpackage Desymphony
 * @since 1.0.0
 */

$class = 'ds-partners-grid';

if ( ! empty( $block['className'] ) ) {
    $class .= ' ' . esc_attr( $block['className'] );
}

if ( ! empty( $block['align'] ) ) {
    $class .= ' align' . esc_attr( $block['align'] );
}

?>

<section class="wv-open-podcast-carousel pt-48 pb-48 <?php echo esc_attr( $class ); ?>" style="background-color: var(--wv-w)">
    <div class="container container-1024 pb-24">
        <div class="d-flex align-items-center justify-content-between">
            <h3 class="h1 fw-700">Official partner</h3>
        </div>
    </div>

    <div class="container container-1024">        
        <a href="https://mts.rs/" class="d-none d-lg-block bg-image br-16" style="background-image: url(https://winevisionfair.com/wp-content/uploads/2025/06/DSK_Telekom_BANNER_Bck.jpg);" target="_blank">
            <img src="https://winevisionfair.com/wp-content/uploads/2025/06/DSK_Telekom_BANNER_logo.svg" class="img-fluid w-100" alt="Telekom Srbija">
        </a>

         <a href="https://mts.rs/" class="d-block d-lg-none bg-image br-16" style="background-image: url(https://winevisionfair.com/wp-content/uploads/2025/06/MOB_Telekom_BANNER_Bck.jpg);" target="_blank">
            <img src="https://winevisionfair.com/wp-content/uploads/2025/06/MOB_Telekom_BANNER_logo.svg" class="img-fluid w-100" alt="Telekom Srbija">
        </a>
    </div>

</section>