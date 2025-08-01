<?php

/**
 * Block Name:  ds-about
 *
 * @package WordPress
 * @subpackage Desymphony
 * @since 1.0.0
 */

$class = 'ds-about';
if ( ! empty( $block['className'] ) ) {
    $class .= ' ' . esc_attr( $block['className'] );
}
if ( ! empty( $block['align'] ) ) {
    $class .= ' align' . esc_attr( $block['align'] );
}

?>

<section class="d-block position-relative wv-bg-w pb-64 pb-lg-128 <?php echo esc_attr( $class ); ?>">
    <figure class="d-none d-lg-block p-0 m-0">
        <img src="https://winevisionfair.com/wp-content/uploads/2025/06/DSK_About_Header_IMG.jpg" alt="About Us" class="img-fluid w-100">
    </figure>
    <figure class="d-block d-lg-none p-0 m-0">
        <img src="https://winevisionfair.com/wp-content/uploads/2025/06/MOB_About_Header_IMG.jpg" alt="About Us" class="img-fluid w-100">
    </figure>
    <div class="d-block position-absolute start-0 end-0 bottom-0">
        <div class="container text-center pb-64 pb-lg-0">
            <div class="row align-items-center justify-content-center">
                <div class="col-lg-8">
                    <h2 class="h1 fw-700">Welcome to the largest and most impactful wine, spirits, food, and tourism fair in Southeastern Europe!</h2>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="d-block position-relative wv-bg-w">
    <figure class="d-none d-lg-block p-0 m-0">
        <img src="https://winevisionfair.com/wp-content/uploads/2025/06/DSK_About_IMG.jpg" alt="About Us" class="img-fluid w-100">
        <div class="d-block position-absolute top-0 start-0 end-0 bottom-0 text-center">
            <img src="https://winevisionfair.com/wp-content/uploads/2025/06/DSK_About_TXT.svg" alt="" class="h-100 w-auto">
        </div>
    </figure>

    <figure class="d-block d-lg-none p-0 m-0">
        <img src="https://winevisionfair.com/wp-content/uploads/2025/06/MOB_About_IMG.jpg" alt="About Us" class="img-fluid w-100">
        <div class="d-block position-absolute top-0 start-0 end-0 text-center">
            <img src="https://winevisionfair.com/wp-content/uploads/2025/06/MOB_About_TXT.svg" alt="" class="w-100 h-auto">
        </div>
    </figure>
    
</section>

<section class="d-block position-relative wv-bg-w pt-64 pt-lg-0 pb-64">
    <div class="container">
        <div class="row align-items-center justify-content-center">
            <div class="col-lg-8">
                <p class="fs-20 fw-700 my-0">Over the past four years, the Wine Vision by Open Balkan fair transcended the boundaries of a conventional trade exhibition. It has become a dynamic platform where unique tastes, individuals and business opportunities converge and thrive. </p>
                <div class="d-block border-top wv-bc-c_70 my-24"></div>
            </div>
            <div class="clear"></div>
            <div class="col-lg-4">
                <p>This year, emerging under the slogan Unique, the event is set to once again bring together leading producers of wine, spirits, and food from across the region and around the globe, alongside an international assembly of buyers, distributors, HORECA professionals and global industry experts. 2025 Wine Vision by Open Balkan fair grants exhibitors, professional buyers and visitors access to a rapidly growing market.</p>
            </div>
            <div class="col-lg-4">
                <p>Beyond the commercial opportunities, 2025 Wine Vision by Open Balkan fair is a unique forum for building relationships, opening new pathways for collaboration. Whether you are seeking new suppliers, exploring emerging trends, or positioning your business for future growth, 2025 Wine Vision by Open Balkan fair offers an unparalleled setting where deals are made, ideas are exchanged and lasting partnerships are formed.</p>
            </div>
        </div>
    </div>
</section>

<section class="d-block position-relative wv-bg-w wv-section-box-shadow">
    <figure class="d-none d-lg-block p-0 m-0">
        <img src="https://winevisionfair.com/wp-content/uploads/2025/06/DSK_About_NUMBERS_Bck.jpg" alt="About Us" class="img-fluid w-100">
        <div class="d-block position-absolute top-0 start-0 end-0 bottom-0 text-center">
            <img src="https://winevisionfair.com/wp-content/uploads/2025/06/DSK_About_Numbers_OPT_2.svg" alt="" class="h-100 w-auto">
        </div>
    </figure>

    <div class="d-block d-lg-none p-0 m-0" 
        style="background: url('https://winevisionfair.com/wp-content/uploads/2025/06/MOB_About_NUMBERS_Bck.jpg') center center / cover no-repeat;">
        <div class="ds-scrollbar ds-scrollbar--x z-1">
            <img src="https://winevisionfair.com/wp-content/uploads/2025/06/MOB_About_Numbers.svg" alt="" class="h-auto" style="width: 1024px">
        </div>
    </div>
    
</section>