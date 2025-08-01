<?php

/**
 * Block Name: ds-8-zones-wine-experience
 *
 * A block showcasing the 8 Zones of Wine Experience.
 *
 * @package WordPress
 * @subpackage Desymphony
 * @since 1.0.0
 */

$class = 'ds-8-zones-wine-experience';
if ( ! empty( $block['className'] ) ) {
    $class .= ' ' . esc_attr( $block['className'] );
}

if ( ! empty( $block['align'] ) ) {
    $class .= ' align' . esc_attr( $block['align'] );
}


?>
<style>
    .wv-8-zones-section {
        background-color: var(--wv-v_dark);
        background-image: url(https://winevisionfair.com/wp-content/uploads/2025/06/DSK_8_Zones_LANDING_Bck.jpg);
    }
    @media screen and (max-width: 768px) {
        .wv-8-zones-section {
            background-image: url(https://winevisionfair.com/wp-content/uploads/2025/06/MOB_8_Zones_LANDING_Bck.jpg);
        }
    }
</style>
<section class="wv-8-zones-section wv-section-box-shadow bg-image py-48 <?php echo esc_attr( $class ); ?>" >
    <div class="container text-center">
        <div class="row align-items-center justify-content-center mb-24 mb-lg-48">
            <div class="col-lg-8 text-white">
                <h2 class="fw-600 display-5">
                    Zones of unique experience
                </h2>
                <p class="text-uppercase fs-18 my-24 fw-700 ls-4">
                    2025 VENUE CONCEPT
                </p>
                <p class="fw-300 d-none d-lg-block">Much like an epicenter radiating energy, the fair's zones act as interactive hubs for knowledge, experiences, and emotions. They engage visitors on every level through their atmosphere and design: intellectually with education, emotionally with experience, and physically with participation in numerous activities.</p>
            </div>
        </div>
    </div>
    
    <div class="container-fluid px-0 overflow-hidden py-32">
        <div class="d-block ds-w-100-128" >
            <!-- Carousel Markup -->
            <div class="swiper-container overlap-carousel">
                <div class="swiper-wrapper">
                    <div class="swiper-slide" style="background-image: url('/wp-content/themes/desymphony/src/images/blocks/8_Zones_RING_Hall_1.png')">
                        <a href="/2025-fair-concept/">
                            <img src="https://winevisionfair.com/wp-content/uploads/2025/07/8_Zones_HEAD_Hall_1.svg" alt="" class="inner-img">
                        </a>                    
                    </div>
                    <div class="swiper-slide" style="background-image: url('/wp-content/themes/desymphony/src/images/blocks/8_Zones_RING_Hall_1A.png')">
                        <a href="/2025-fair-concept/">
                            <img src="https://winevisionfair.com/wp-content/uploads/2025/07/8_Zones_HEAD_Hall_1A.svg" alt="" class="inner-img">
                        </a>                    
                    </div>
                    <div class="swiper-slide" style="background-image: url('/wp-content/themes/desymphony/src/images/blocks/8_Zones_RING_Hall_2A_2C.png')">
                        <a href="/2025-fair-concept/">
                            <img src="https://winevisionfair.com/wp-content/uploads/2025/07/8_Zones_HEAD_Hall_2A_2C.svg" alt="" class="inner-img">
                        </a>                    
                    </div>
                    <div class="swiper-slide" style="background-image: url('/wp-content/themes/desymphony/src/images/blocks/8_Zones_RING_Hall_2B.png')">
                        <a href="/2025-fair-concept/">
                            <img src="https://winevisionfair.com/wp-content/uploads/2025/07/8_Zones_HEAD_Hall_2B.svg" alt="" class="inner-img">
                        </a>                    
                    </div>
                    <div class="swiper-slide" style="background-image: url('/wp-content/themes/desymphony/src/images/blocks/8_Zones_RING_Hall_3.png')">
                        <a href="/2025-fair-concept/">
                            <img src="https://winevisionfair.com/wp-content/uploads/2025/07/8_Zones_HEAD_Hall_3.svg" alt="" class="inner-img">
                        </a>                    
                    </div>
                    <div class="swiper-slide" style="background-image: url('/wp-content/themes/desymphony/src/images/blocks/8_Zones_RING_Hall_3A.png')">
                        <a href="/2025-fair-concept/">
                            <img src="https://winevisionfair.com/wp-content/uploads/2025/07/8_Zones_HEAD_Hall_3A.svg" alt="" class="inner-img">
                        </a>                    
                    </div>
                    <div class="swiper-slide" style="background-image: url('/wp-content/themes/desymphony/src/images/blocks/8_Zones_RING_Hall_4A.png')">
                        <a href="/2025-fair-concept/">
                            <img src="https://winevisionfair.com/wp-content/uploads/2025/07/8_Zones_HEAD_Hall_4A.svg" alt="" class="inner-img">
                        </a>                    
                    </div>
                    <div class="swiper-slide" style="background-image: url('/wp-content/themes/desymphony/src/images/blocks/8_Zones_RING_Hall_4B.png')">
                        <a href="/2025-fair-concept/">
                            <img src="https://winevisionfair.com/wp-content/uploads/2025/07/8_Zones_HEAD_Hall_4B.svg" alt="" class="inner-img">
                        </a>                    
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container text-center d-lg-none">
        <div class="row align-items-center justify-content-center mt-24">
            <div class="col-lg-8 text-white">
                <p class="fw-300">Much like an epicenter radiating energy, the fair's zones act as interactive hubs for knowledge, experiences, and
                    emotions. They engage visitors on every level through their atmosphere and design: intellectually with education,
                    emotionally with experience, and physically with participation in numerous activities.</p>
            </div>
        </div>
    </div>

    
</section>