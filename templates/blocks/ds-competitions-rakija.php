<?php

/**
 * Block Name:  competitions-rakija
 *
 * @package WordPress
 * @subpackage Desymphony
 * @since 1.0.0
 */

$class = 'competitions-rakija';
if ( ! empty( $block['className'] ) ) {
    $class .= ' ' . esc_attr( $block['className'] );
}
if ( ! empty( $block['align'] ) ) {
    $class .= ' align' . esc_attr( $block['align'] );
}

?>
<style>
    .competitions-rakija {
        background: url('https://winevisionfair.com/wp-content/uploads/2025/06/DSK_OBRT_Bck.jpg') center center / cover no-repeat;
    }
    @media screen and (max-width: 768px) {
        .competitions-rakija {
            background: url('https://winevisionfair.com/wp-content/uploads/2025/06/MOB_OBRT_Bck.jpg') center center / cover no-repeat;
        }
    }
</style>
<section class="d-block position-relative wv-bg-w py-64 wv-section-box-shadow <?php echo esc_attr( $class ); ?>">
    <div class="container container-1024">
        <div class="row align-items-center justify-content-center">
            <div class="col-12">
                <h6 class="fw-600 ls-4 wv-color-s_80 mb-24">OPEN BALKAN RAKIJA TROPHY</h6>
                <h1 class="fw-700 wv-color-w">Awarding today’s<br /> Balkan traditional craft masters</h1>
            </div>
        </div>
    </div>
    <div class="d-none d-lg-block border-top border-bottom wv-bc-s_80 my-32"></div>
    
    <div class="container container-1024 d-none d-lg-block">
        <div class="row align-items-center justify-content-center">
            <div class="col-lg-12">
                <p class="fs-20 fw-600 wv-color-c_20">In Serbia and the Balkans, the concept of ‘Unique’ finds its quintessential expression in Rakija. The Open Balkan Rakija Trophy rigorously evaluates and commends the remarkable quality of this traditional fruit-based spirit, elevating it to global prominence as one of the world’s most esteemed spirits.</p>
            </div>
        </div>
    </div>

    <div class="swiper wv-img-carousel py-48 py-lg-64">
        <div class="swiper-wrapper">
            <div class="swiper-slide">
            <img src="https://winevisionfair.com/wp-content/uploads/2025/06/OBRT_img_1.png"
                alt="Wine Vision OBRT image 1" loading="lazy">
            </div>

            <div class="swiper-slide">
            <img src="https://winevisionfair.com/wp-content/uploads/2025/06/OBRT_img_2.png"
                alt="Wine Vision OBRT image 2" loading="lazy">
            </div>

            <div class="swiper-slide">
            <img src="https://winevisionfair.com/wp-content/uploads/2025/06/OBRT_img_3.png"
                alt="Wine Vision OBRT image 3" loading="lazy">
            </div>

            <div class="swiper-slide">
            <img src="https://winevisionfair.com/wp-content/uploads/2025/06/OBRT_img_4.png"
                alt="Wine Vision OBRT image 4" loading="lazy">
            </div>

            <div class="swiper-slide">
            <img src="https://winevisionfair.com/wp-content/uploads/2025/06/OBRT_img_5.png"
                alt="Wine Vision OBRT image 5" loading="lazy">
            </div>

            <div class="swiper-slide">
            <img src="https://winevisionfair.com/wp-content/uploads/2025/06/OBRT_img_6.png"
                alt="Wine Vision OBRT image 6" loading="lazy">
            </div>

            <div class="swiper-slide">
            <img src="https://winevisionfair.com/wp-content/uploads/2025/06/OBRT_img_7.png"
                alt="Wine Vision OBRT image 7" loading="lazy">
            </div>
        </div>
    </div>

    
    <div class="container container-1024 d-block d-lg-none">
        <div class="row align-items-center justify-content-center">
            <div class="col-lg-12">
                <p class="fs-20 fw-600 wv-color-c_20">In Serbia and the Balkans, the concept of ‘Unique’ finds its quintessential expression in Rakija. The Open Balkan Rakija Trophy rigorously evaluates and commends the remarkable quality of this traditional fruit-based spirit, elevating it to global prominence as one of the world’s most esteemed spirits.</p>
                
                <div class="d-block border-top border-bottom wv-bc-s_80 my-24"></div>
            </div>
        </div>
    </div>
    

    <div class="container container-1024">
        <div class="row align-items-center justify-content-center">
            <div class="col-lg-6">
                <p class="wv-color-w">Considered a national brand of Serbia, Rakija is the finest artisanal spirit made from fruit. Enlisted in the UNESCO Intangible Cultural Heritage list, its time-honored distillation method, passed down through generations, is an essential component of the nation’s identity. Once again, the Wine Vision by Open Balkan fair will gather all relevant distillers to present the best of their products and compete.</p>
            </div>
            <div class="col-lg-6">
                <p class="wv-color-w">The craftsmanship of Rakija distilling is acknowledged and awarded by the Open Balkan Rakija Trophy in four general categories, defined by the fruit from which the Rakija is made: Plum, Quince, Grape, and Other Fruit. We take pride in the fact that for the fourth year in a row, laureates of the Open Balkan Rakija Trophy unquestionably represent the region’s finest selection.</p>
            </div>
        </div>
    </div>
    
</section>