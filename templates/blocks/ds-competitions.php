<?php

/**
 * Block Name:  competitions
 *
 * @package WordPress
 * @subpackage Desymphony
 * @since 1.0.0
 */

$class = 'competitions';
if ( ! empty( $block['className'] ) ) {
    $class .= ' ' . esc_attr( $block['className'] );
}
if ( ! empty( $block['align'] ) ) {
    $class .= ' align' . esc_attr( $block['align'] );
}

?>
<style>
    #wv-main { 
        background: var(--wv-w);
    }
</style>
<section class="d-block position-relative wv-bg-w py-32 py-lg-64  <?php echo esc_attr( $class ); ?>">
    <div class="container d-none d-lg-block">
        <div class="row">
            <div class="col-lg-4">
                <div class="ds-competitions-card position-relative br-24 py-64 px-32 text-center" style="background: url('https://winevisionfair.com/wp-content/uploads/2025/06/DSK_OBWT_Banner_Bck.jpg') center center / cover no-repeat;">
                    <img src="https://winevisionfair.com/wp-content/uploads/2025/06/OBWT_Logo.svg" class="img-fluid w-100" alt="">
                    <div class="d-block w-50 border-top border-bottom my-32 mx-auto wv-bc-w_80"></div>
                    <h3 class="wv-color-w mb-32 fw-700 h2">Balkan wines: <br />globally recognized excellence</h3>
                    <a href="/competitions/wine-throphy/" class="wv-button wv-button-pill fw-400 wv-button-w-op fs-14 py-12 px-24">More</a>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="ds-competitions-card position-relative br-24 py-64 px-32 text-center" style="background: url('https://winevisionfair.com/wp-content/uploads/2025/06/DSK_OBRT_Banner_Bck.jpg') center center / cover no-repeat;">
                    <img src="https://winevisionfair.com/wp-content/uploads/2025/06/OBRT_Logo.svg" class="img-fluid w-100" alt="">
                    <div class="d-block w-50 border-top border-bottom my-32 mx-auto wv-bc-s_80"></div>
                    <h3 class="wv-color-w mb-32 fw-700 h2">Awarding today’s <br />Balkan traditional craft masters</h3>
                    <a href="/competitions/rakija-throphy/" class="wv-button wv-button-pill fw-400 wv-button-w-op fs-14 py-12 px-24">More</a>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="ds-competitions-card position-relative br-24 py-64 px-32 text-center" style="background: url('https://winevisionfair.com/wp-content/uploads/2025/06/DSK_FVOB_Banner_Bck.jpg') center center / cover no-repeat;">
                    <img src="https://winevisionfair.com/wp-content/uploads/2025/06/FVOB_Logo.svg" class="img-fluid w-100" alt="">
                    <div class="d-block w-50 border-top border-bottom my-32 mx-auto wv-bc-f_80"></div>
                    <h3 class="wv-color-w mb-32 fw-700 h2">An amazing<br /> blend of flavors & knowledge</h3>
                    <a href="/competitions/food-throphy/" class="wv-button wv-button-pill fw-400 wv-button-w-op fs-14 py-12 px-24">More</a>
                </div>
            </div>
        </div>
    </div>

    <div class="container d-lg-none">

       <a href="/competitions/wine-throphy/"
        class="d-block mb-12 ds-competitions-card position-relative br-12 py-24"
        style="background:url('https://winevisionfair.com/wp-content/uploads/2025/06/MOB_OBWT_Banner_Bck.jpg') center/cover no-repeat;">
        <div class="row g-0 h-100 align-items-stretch">
            <div class="col-6 d-flex justify-content-center align-items-center border-end wv-bc-w_80">
            <img src="https://winevisionfair.com/wp-content/uploads/2025/06/OBWT_Logo.svg"
                class="img-fluid w-100" alt="Wine Trophy logo">
            </div>
            <div class="col-6 d-flex flex-column justify-content-between px-24 border-start wv-bc-w_80">
            <h3 class="wv-color-w mb-32 fw-700 h4">
                Balkan wines:<br>globally recognized excellence
            </h3>
            <div class="d-flex justify-content-between align-items-center wv-color-w fs-12 lh-1">
                <span class="opacity-75">Read More</span>
                <span class="wv wv_point-50-f me-12 fs-14 mt-4"></span>
            </div>
            </div>
        </div>
        </a>

        <a href="/competitions/rakija-throphy/"
        class="d-block mb-12 ds-competitions-card position-relative br-12 py-24"
        style="background:url('https://winevisionfair.com/wp-content/uploads/2025/06/MOB_OBRT_Banner_Bck.jpg') center/cover no-repeat;">
        <div class="row g-0 h-100 align-items-stretch">
            <div class="col-6 d-flex justify-content-center align-items-center border-end wv-bc-s_80">
            <img src="https://winevisionfair.com/wp-content/uploads/2025/06/OBRT_Logo.svg"
                class="img-fluid w-100" alt="Rakija Trophy logo">
            </div>
            <div class="col-6 d-flex flex-column justify-content-between px-24 border-start wv-bc-s_80">
            <h3 class="wv-color-w mb-32 fw-700 h4">
                Awarding today’s<br>Balkan traditional craft masters
            </h3>
            <div class="d-flex justify-content-between align-items-center wv-color-w fs-12 lh-1">
                <span class="opacity-75">Read More</span>
                <span class="wv wv_point-50-f me-12 fs-14 mt-4"></span>
            </div>
            </div>
        </div>
        </a>

        <a href="/competitions/food-throphy/"
        class="d-block mb-12 ds-competitions-card position-relative br-12 py-24"
        style="background:url('https://winevisionfair.com/wp-content/uploads/2025/06/MOB_FVOB_Banner_Bck.jpg') center/cover no-repeat;">
        <div class="row g-0 h-100 align-items-stretch">
            <div class="col-6 d-flex justify-content-center align-items-center border-end wv-bc-f_80">
            <img src="https://winevisionfair.com/wp-content/uploads/2025/06/FVOB_Logo.svg"
                class="img-fluid w-100" alt="Food Vision Trophy logo">
            </div>
            <div class="col-6 d-flex flex-column justify-content-between px-24 border-start wv-bc-f_80">
            <h3 class="wv-color-w mb-32 fw-700 h4">
                An amazing<br>blend of flavors &amp; knowledge
            </h3>
            <div class="d-flex justify-content-between align-items-center wv-color-w fs-12 lh-1">
                <span class="opacity-75">Read More</span>
                <span class="wv wv_point-50-f me-12 fs-14 mt-4"></span>
            </div>
            </div>
        </div>
        </a>


    </div>
    
</section>