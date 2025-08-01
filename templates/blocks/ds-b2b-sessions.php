<?php

/**
 * Block Name:  ds-b2b-sessions
 *
 * @package WordPress
 * @subpackage Desymphony
 * @since 1.0.0
 */

$class = 'ds-b2b-sessions';
if ( ! empty( $block['className'] ) ) {
    $class .= ' ' . esc_attr( $block['className'] );
}
if ( ! empty( $block['align'] ) ) {
    $class .= ' align' . esc_attr( $block['align'] );
}

?>
<style>
    .ds-b2b-sessions {
        background: url('https://winevisionfair.com/wp-content/uploads/2025/06/DSK_B2B_Bck.jpg') center center / cover no-repeat;
    }
    @media screen and (max-width: 768px) {
        .ds-b2b-sessions {
            background: url('https://winevisionfair.com/wp-content/uploads/2025/06/MOB_B2B_Bck.jpg') center center / cover no-repeat;
        }
    }
    /* smooth, continuous scroll */
    .wv-img-carousel .swiper-wrapper{
        transition-timing-function:linear!important;
    }
    .wv-img-carousel .swiper-slide{width:auto} 
    .wv-img-carousel .swiper-slide img {
        max-height: 50vh;
    }
</style>
<section class="d-block position-relative wv-bg-w py-64 wv-section-box-shadow  <?php echo esc_attr( $class ); ?>">
    <div class="container container-1024">
        <div class="row align-items-center justify-content-center">
            <div class="col-12">
                <h6 class="fw-600 ls-4 wv-color-c_20 mb-24">B2B SESSIONS</h6>
                <h1 class="fw-700 wv-color-w">Our ultimate mission:<br /> support new partnerships</h1>
            </div>
        </div>
    </div>
    <div class="d-none d-lg-block border-top border-bottom wv-bc-c_70 my-32"></div>
    
    <div class="container container-1024 d-none d-lg-block">
        <div class="row align-items-center justify-content-center">
            <div class="col-lg-12">
                <p class="fs-20 fw-600 wv-color-c_20">In parallel with the exhibitions, B2B Meetings are a vital segment of Wine Vision
                by Open Balkan. Aiming to foster new partnerships, these B2B sessions represent
                a solid and reliable business platform for establishing new trade deals among
                professional buyers, visitors, and exhibitors.</p>
            </div>
        </div>
    </div>

    <div class="swiper wv-img-carousel py-48 py-lg-64">
        <div class="swiper-wrapper">
            <div class="swiper-slide">
            <img src="https://winevisionfair.com/wp-content/uploads/2025/06/B2B_img_1.png"
                alt="Wine Vision B2B image 1" loading="lazy">
            </div>

            <div class="swiper-slide">
            <img src="https://winevisionfair.com/wp-content/uploads/2025/06/B2B_img_2.png"
                alt="Wine Vision B2B image 2" loading="lazy">
            </div>

            <div class="swiper-slide">
            <img src="https://winevisionfair.com/wp-content/uploads/2025/06/B2B_img_3.png"
                alt="Wine Vision B2B image 3" loading="lazy">
            </div>

            <div class="swiper-slide">
            <img src="https://winevisionfair.com/wp-content/uploads/2025/06/B2B_img_4.png"
                alt="Wine Vision B2B image 4" loading="lazy">
            </div>

            <div class="swiper-slide">
            <img src="https://winevisionfair.com/wp-content/uploads/2025/06/B2B_img_5.png"
                alt="Wine Vision B2B image 5" loading="lazy">
            </div>

            <div class="swiper-slide">
            <img src="https://winevisionfair.com/wp-content/uploads/2025/06/B2B_img_6.png"
                alt="Wine Vision B2B image 6" loading="lazy">
            </div>
        </div>
    </div>

    
    <div class="container container-1024 d-block d-lg-none">
        <div class="row align-items-center justify-content-center">
            <div class="col-lg-12">
                <p class="fs-20 fw-600 wv-color-c_20">In parallel with the exhibitions, B2B Meetings are a vital segment of Wine Vision
                by Open Balkan. Aiming to foster new partnerships, these B2B sessions represent
                a solid and reliable business platform for establishing new trade deals among
                professional buyers, visitors, and exhibitors.</p>
                
                <div class="d-block border-top border-top border-bottom wv-bc-c_70 my-24"></div>
            </div>
        </div>
    </div>
    

    <div class="container container-1024">
        <div class="row align-items-center justify-content-center">
            <div class="col-lg-6">
                <p class="wv-color-w">By connecting importers, wholesalers, retailers, sales agents, and industry promoters on one side with winemakers, distillers, food producers, distributors, and professional equipment manufacturers on the other, the B2B sessions platform matches participants with compatible interests. This helps them achieve business goals by making new trade deals.</p>
            </div>
            <div class="col-lg-6">
                <p class="wv-color-w">These B2B meetings, accompanied by presentations, workshops, and tasting sessions, are held during the first two days of the fair within a dedicated space that provides comfort and privacy. By October 2025, all participants will have a mobile app at their disposal, offering a meeting scheduling calendar, messaging, and many other professional services.</p>
            </div>
        </div>
    </div>


<!-- 
    <figure class="d-none d-lg-block p-0 m-0">
        <img src="https://winevisionfair.com/wp-content/uploads/2025/06/DSK_About_Header_IMG.jpg" alt="About Us" class="img-fluid w-100">
    </figure>
    <figure class="d-block d-lg-none p-0 m-0">
        <img src="https://winevisionfair.com/wp-content/uploads/2025/06/MOB_About_Header_IMG.jpg" alt="About Us" class="img-fluid w-100">
    </figure>
     -->
    
</section>