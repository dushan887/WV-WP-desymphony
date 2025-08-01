<?php

/**
 * Block Name:  competitions-food
 *
 * @package WordPress
 * @subpackage Desymphony
 * @since 1.0.0
 */

$class = 'competitions-food';
if ( ! empty( $block['className'] ) ) {
    $class .= ' ' . esc_attr( $block['className'] );
}
if ( ! empty( $block['align'] ) ) {
    $class .= ' align' . esc_attr( $block['align'] );
}

?>
<style>
    .competitions-food {
        background: url('https://winevisionfair.com/wp-content/uploads/2025/06/DSK_FVOB_Bck.jpg') center center / cover no-repeat;
    }
    @media screen and (max-width: 768px) {
        .competitions-food {
            background: url('https://winevisionfair.com/wp-content/uploads/2025/06/MOB_FVOB_Bck.jpg') center center / cover no-repeat;
        }
    }
</style>
<section class="d-block position-relative wv-bg-w py-64 wv-section-box-shadow  <?php echo esc_attr( $class ); ?>">
    <div class="container container-1024">
        <div class="row align-items-center justify-content-center">
            <div class="col-12">
                <h6 class="fw-600 ls-4 wv-color-f_80 mb-24">FOOD VISION BY OPEN BALKAN</h6>
                <h1 class="fw-700 wv-color-w">An amazing blend<br /> of flavors & knowledge</h1>
            </div>
        </div>
    </div>
    <div class="d-none d-lg-block border-top border-bottom wv-bc-f_80 my-32"></div>
    
    <div class="container container-1024 d-none d-lg-block">
        <div class="row align-items-center justify-content-center">
            <div class="col-lg-12">
                <p class="fs-20 fw-600 wv-color-c_20">Food Vision offers a rich spectrum of activities to exhibitors, professional buyers, professional visitors, and guests across three program segments: a young and upcoming chefs’ competition, a set of exclusive events showcasing restaurants and hotels preparing their three-course sets, and professional masterclass sessions.</p>
            </div>
        </div>
    </div>

    <div class="swiper wv-img-carousel py-48 py-lg-64">
        <div class="swiper-wrapper">
            <div class="swiper-slide">
            <img src="https://winevisionfair.com/wp-content/uploads/2025/06/FVOB_img_1.png"
                alt="Wine Vision FVOB image 1" loading="lazy">
            </div>

            <div class="swiper-slide">
            <img src="https://winevisionfair.com/wp-content/uploads/2025/06/FVOB_img_2.png"
                alt="Wine Vision FVOB image 2" loading="lazy">
            </div>

            <div class="swiper-slide">
            <img src="https://winevisionfair.com/wp-content/uploads/2025/06/FVOB_img_3.png"
                alt="Wine Vision FVOB image 3" loading="lazy">
            </div>

            <div class="swiper-slide">
            <img src="https://winevisionfair.com/wp-content/uploads/2025/06/FVOB_img_4.png"
                alt="Wine Vision FVOB image 4" loading="lazy">
            </div>

            <div class="swiper-slide">
            <img src="https://winevisionfair.com/wp-content/uploads/2025/06/FVOB_img_5.png"
                alt="Wine Vision FVOB image 5" loading="lazy">
            </div>

            <div class="swiper-slide">
            <img src="https://winevisionfair.com/wp-content/uploads/2025/06/FVOB_img_6.png"
                alt="Wine Vision FVOB image 6" loading="lazy">
            </div>

            <div class="swiper-slide">
            <img src="https://winevisionfair.com/wp-content/uploads/2025/06/FVOB_img_6.png"
                alt="Wine Vision FVOB image 7" loading="lazy">
            </div>
        </div>
    </div>

    
    <div class="container container-1024 d-block d-lg-none">
        <div class="row align-items-center justify-content-center">
            <div class="col-lg-12">
                <p class="fs-20 fw-600 wv-color-c_20">Food Vision offers a rich spectrum of activities to exhibitors, professional buyers, professional visitors, and guests across three program segments: a young and upcoming chefs’ competition, a set of exclusive events showcasing restaurants and hotels preparing their three-course sets, and professional masterclass sessions.</p>
                
                <div class="d-block border-top border-bottom wv-bc-f_80 my-24"></div>
            </div>
        </div>
    </div>
    

    <div class="container container-1024">
        <div class="row align-items-center justify-content-center">
            <div class="col-lg-6">
                <p class="wv-color-w">Food Vision by Open Balkan is a meeting point for food experts, eminent chefs, international food critics, and representatives of leading restaurants and hotels, as well as food and hospitality equipment suppliers and manufacturers. As such, Food Vision by Open Balkan leverages its relevance to support the most promising young chefs in their efforts to establish themselves in the region’s vibrant and dynamic hospitality market.</p>
            </div>
            <div class="col-lg-6">
                <p class="wv-color-w">This year’s Food Vision by Open Balkan program offers professional and public visitors an exclusive opportunity to learn about Balkan countries’ culinary trends—both traditional and contemporary—in an engaging manner. This is achieved through a series of masterclasses, presentations, and workshops, all held by the best chefs of the region, including Michelin-starred chefs. After all, to experience the Balkans is to experience its food and hospitality.</p>
            </div>
        </div>
    </div>


    
</section>