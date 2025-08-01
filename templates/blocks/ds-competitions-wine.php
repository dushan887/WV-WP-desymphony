<?php

/**
 * Block Name:  competitions-wine
 *
 * @package WordPress
 * @subpackage Desymphony
 * @since 1.0.0
 */

$class = 'competitions-wine';
if ( ! empty( $block['className'] ) ) {
    $class .= ' ' . esc_attr( $block['className'] );
}
if ( ! empty( $block['align'] ) ) {
    $class .= ' align' . esc_attr( $block['align'] );
}

?>
<style>
    .competitions-wine {
        background: url('https://winevisionfair.com/wp-content/uploads/2025/06/DSK_OBWT_Bck.jpg') center center / cover no-repeat;
    }
    @media screen and (max-width: 768px) {
        .competitions-wine {
            background: url('https://winevisionfair.com/wp-content/uploads/2025/06/MOB_OBWT_Bck.jpg') center center / cover no-repeat;
        }
    }
</style>
<section class="d-block position-relative wv-bg-w py-64 wv-section-box-shadow <?php echo esc_attr( $class ); ?>">
    <div class="container container-1024">
        <div class="row align-items-center justify-content-center">
            <div class="col-12">
                <h6 class="fw-600 ls-4 wv-color-w_80 mb-24">OPEN BALKAN WINE TROPHY</h6>
                <h1 class="fw-700 wv-color-w">Balkan wines:<br /> globally recognized excellence</h1>
            </div>
        </div>
    </div>
    <div class="d-none d-lg-block border-top border-bottom wv-bc-w_80 my-32"></div>
    
    <div class="container container-1024 d-none d-lg-block">
        <div class="row align-items-center justify-content-center">
            <div class="col-lg-12">
                <p class="fs-20 fw-600 wv-color-c_20">The Open Balkan Wine Trophy judges wines based on systems and standards applied in major global wine competitions. It strives to provide due recognition and acknowledgment of the ever-growing standards, quality, and winemaking mastery demonstrated by wineries from Serbia, Albania, and North Macedonia.</p>
            </div>
        </div>
    </div>

    <div class="swiper wv-img-carousel py-48 py-lg-64">
        <div class="swiper-wrapper">
            <div class="swiper-slide">
            <img src="https://winevisionfair.com/wp-content/uploads/2025/06/OBWT_img_1.png"
                alt="Wine Vision OBWT image 1" loading="lazy">
            </div>

            <div class="swiper-slide">
            <img src="https://winevisionfair.com/wp-content/uploads/2025/06/OBWT_img_2.png"
                alt="Wine Vision OBWT image 2" loading="lazy">
            </div>

            <div class="swiper-slide">
            <img src="https://winevisionfair.com/wp-content/uploads/2025/06/OBWT_img_3.png"
                alt="Wine Vision OBWT image 3" loading="lazy">
            </div>

            <div class="swiper-slide">
            <img src="https://winevisionfair.com/wp-content/uploads/2025/06/OBWT_img_4.png"
                alt="Wine Vision OBWT image 4" loading="lazy">
            </div>

            <div class="swiper-slide">
            <img src="https://winevisionfair.com/wp-content/uploads/2025/06/OBWT_img_5.png"
                alt="Wine Vision OBWT image 5" loading="lazy">
            </div>

            <div class="swiper-slide">
            <img src="https://winevisionfair.com/wp-content/uploads/2025/06/OBWT_img_6.png"
                alt="Wine Vision OBWT image 6" loading="lazy">
            </div>

            <div class="swiper-slide">
            <img src="https://winevisionfair.com/wp-content/uploads/2025/06/OBWT_img_7.png"
                alt="Wine Vision OBWT image 7" loading="lazy">
            </div>
        </div>
    </div>

    
    <div class="container container-1024 d-block d-lg-none">
        <div class="row align-items-center justify-content-center">
            <div class="col-lg-12">
                <p class="fs-20 fw-600 wv-color-c_20">The Open Balkan Wine Trophy judges wines based on systems and standards applied in major global wine competitions. It strives to provide due recognition and acknowledgment of the ever-growing standards, quality, and winemaking mastery demonstrated by wineries from Serbia, Albania, and North Macedonia.</p>
                
                <div class="d-block border-top border-bottom wv-bc-w_80 my-24"></div>
            </div>
        </div>
    </div>
    

    <div class="container container-1024">
        <div class="row align-items-center justify-content-center">
            <div class="col-lg-6">
                <p class="wv-color-w">For the past three years, more than 1.200 wines from Serbia, Albania, and North Macedonia have been submitted to compete in the Open Balkan Wine Trophy. Awards will be offered in seven categories: white, red, rosé, sparkling, sweet, orange, and fortified wine. The competition will also offer five special prizes to wines created with indigenous grape varieties from the Balkans, as well as to the best wines from Serbia, Albania, and North Macedonia.</p>
            </div>
            <div class="col-lg-6">
                <p class="wv-color-w">The Open Balkan Wine Trophy judges’ panel consists of renowned professionals, including seven Masters of Wine. They will judge the wines, applying the top global wine competition standards. The president of the jury is Caroline Gilby MW, a prominent expert on Balkan and Southeast European wines. Given the jury’s credibility and the increased interest in this event, we are committed to an ever-growing number of wines in the years to come.</p>
            </div>
        </div>
    </div>


</section>