<?php

/**
 * Block Name:  Awards
 *
 * @package WordPress
 * @subpackage Desymphony
 * @since 1.0.0
 */

$class = 'wv-awards';
if ( ! empty( $block['className'] ) ) {
    $class .= ' ' . esc_attr( $block['className'] );
}
if ( ! empty( $block['align'] ) ) {
    $class .= ' align' . esc_attr( $block['align'] );
}

?>
<style>
    .wv-awards {
        background: url('https://winevisionfair.com/wp-content/uploads/2025/06/DSK_Awards_Bck.jpg') center center / cover no-repeat;
        min-height: 100vh;
    }
    @media screen and (max-width: 768px) {
        .wv-awards {
            background: url('https://winevisionfair.com/wp-content/uploads/2025/06/MOB_Awards_Bck.jpg') center center / cover no-repeat;
        }
    }
</style>
<section class="d-block position-relative wv-bg-w py-32 py-lg-64  <?php echo esc_attr( $class ); ?>">
    <div class="container d-none d-lg-block">
        <div class="row">

            <div class="col-lg-4">
                <div class="ds-competitions-card position-relative br-24 text-center" style="background: url('https://winevisionfair.com/wp-content/uploads/2025/06/DSK_WT_Awards_Bck.jpg') center center / cover no-repeat;">
                    <img src="https://winevisionfair.com/wp-content/uploads/2025/06/DSK_WT_Awards_Heading.svg" class="img-fluid w-100" alt="">
                    <div class="position-absolute bottom-0 start-0 end-0 pb-48">
                        <a href="/awards/wine-trophy/" class="wv-button wv-button-pill fw-400 wv-button-dark-op fs-14 py-12 px-24">Awards</a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="ds-competitions-card position-relative br-24 text-center" style="background: url('https://winevisionfair.com/wp-content/uploads/2025/06/DSK_RT_Awards_Bck.jpg') center center / cover no-repeat;">
                    <img src="https://winevisionfair.com/wp-content/uploads/2025/06/DSK_RT_Awards_Heading.svg" class="img-fluid w-100" alt="">
                    <div class="position-absolute bottom-0 start-0 end-0 pb-48">
                        <a href="/awards/rakija-trophy/" class="wv-button wv-button-pill fw-400 wv-button-dark-op fs-14 py-12 px-24">Awards</a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="ds-competitions-card position-relative br-24 text-center" style="background: url('https://winevisionfair.com/wp-content/uploads/2025/06/DSK_CC_Awards_Bck.jpg') center center / cover no-repeat;">
                    <img src="https://winevisionfair.com/wp-content/uploads/2025/06/DSK_CC_Awards_Heading.svg" class="img-fluid w-100" alt="">
                    <div class="position-absolute bottom-0 start-0 end-0 pb-48">
                        <a href="/awards/culinary-challenge/" class="wv-button wv-button-pill fw-400 wv-button-dark-op fs-14 py-12 px-24">Awards</a>
                    </div>
                </div>
            </div>

           
        </div>
    </div>

    <div class="container d-lg-none">

        <div class="row">

            <div class="col-12 mb-12">
                <div class="ds-competitions-card position-relative br-24 text-center" style="background: url('https://winevisionfair.com/wp-content/uploads/2025/06/MOB_WT_Awards_Bck.jpg') center center / cover no-repeat;">
                    <img src="https://winevisionfair.com/wp-content/uploads/2025/06/MOB_WT_Awards_Heading.svg" class="img-fluid w-100" alt="">
                    <div class="position-absolute bottom-0 start-0 p-16">
                        <a href="/awards/wine-trophy/" class="wv-button wv-button-pill fw-400 wv-button-dark-op fs-14 py-12 px-24">Awards</a>
                    </div>
                </div>
            </div>

            <div class="col-12 mb-12">
                <div class="ds-competitions-card position-relative br-24 text-center" style="background: url('https://winevisionfair.com/wp-content/uploads/2025/06/MOB_RT_Awards_Bck.jpg') center center / cover no-repeat;">
                    <img src="https://winevisionfair.com/wp-content/uploads/2025/06/MOB_RT_Awards_Heading.svg" class="img-fluid w-100" alt="">
                    <div class="position-absolute bottom-0 start-0 p-16">
                        <a href="/awards/rakija-trophy/" class="wv-button wv-button-pill fw-400 wv-button-dark-op fs-14 py-12 px-24">Awards</a>
                    </div>
                </div>
            </div>

            <div class="col-12 mb-12">
                <div class="ds-competitions-card position-relative br-24 text-center" style="background: url('https://winevisionfair.com/wp-content/uploads/2025/06/MOB_CC_Awards_Bck.jpg') center center / cover no-repeat;">
                    <img src="https://winevisionfair.com/wp-content/uploads/2025/06/MOB_CC_Awards_Heading.svg" class="img-fluid w-100" alt="">
                    <div class="position-absolute bottom-0 start-0 p-16">
                        <a href="/awards/culinary-challenge/" class="wv-button wv-button-pill fw-400 wv-button-dark-op fs-14 py-12 px-24">Awards</a>
                    </div>
                </div>
            </div>

           
        </div>

    </div>
    
</section>