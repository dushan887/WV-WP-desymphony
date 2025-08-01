<?php

/**
 * Block Name: ds-temp1
 *
 * This is the template that displays the Instagram feed block.
 *
 * @package WordPress
 * @subpackage Desymphony
 * @since 1.0.0
 */

$class = 'ds-temp1';
if ( ! empty( $block['className'] ) ) {
    $class .= ' ' . esc_attr( $block['className'] );
}

if ( ! empty( $block['align'] ) ) {
    $class .= ' align' . esc_attr( $block['align'] );
}

?>

<style>
.stand-rental-section {
background: url(/wp-content/themes/desymphony/src/images/blocks/DSK_Explore_Custom.png);
  background-size: cover;
  background-position: center;
}
.wv-button-c_10, .wv-badge-c_10 {
    background-color: var(--wv-c_10);
    color: var(--wv-c);
    border-color: var(--wv-c_10);
}
.wv-button-c_10:hover, .wv-button-c_10.active {
    background-color: var(--wv-c);
    color: var(--wv-w);
    border-color: var(--wv-c);
}
.wv-button-c_80, .wv-badge-c_80 {
    background-color: var(--wv-c_80);
    color: var(--wv-w);
    border-color: var(--wv-c_80);
}
.wv-button-c_80:hover, .wv-button-c_80.active {
    background-color: var(--wv-c);
    color: var(--wv-w);
    border-color: var(--wv-c);
}
.wv-button-w, .wv-badge-w {
    background-color: var(--wv-w);
    color: var(--wv-c);
    border-color: var(--wv-w);
}
.wv-button-w:hover, .wv-button-w.active {
    background-color: var(--wv-c_10);
    color: var(--wv-c);
    border-color: var(--wv-c_10);
}
.wv-c-w .path1:before {
  color: var(--wv-w);
}
.wv-c-w .path2:before {
  color: var(--wv-c);
}
.wv-c-r .path1:before {
  color: var(--wv-r);
}
.wv-c-r .path2:before {
  color: var(--wv-w);
}
.wv-c-g .path1:before {
  color: var(--wv-g);
}
.wv-c-r .path2:before {
  color: var(--wv-w);
}

</style>

<section style="background: var(--wv-exhibitor-gradient-light)">
    <div class="container">
        <div class="row">
            <div class="col-12 py-48">
                <h1 class="display-5 wv-color-w text-center fw-600">Explore the 2025<br />
                Exhibitor Application Form</h1>
            </div>
        </div>
    </div>
</section>

<div class="d-block text-center p-64 fs-64">Exhibitor Section</div>

<!-- Showcase Your Products -->
<section class="d-block" style="background: var(--wv-v_dark);">
    <div class="container">
        <div class="row align-items-center justify-content-center text-center py-16">
            <div class="col-4">
                <span class="fs-18 fw-400 wv-color-w ls-4">STEP 1</span>
            </div>
            <div class="col-4">
                <span class="fs-18 fw-400 wv-color-w ls-4">EXHIBITION CONTENT ></span>
                <span class="fs-18 fw-600 wv-color-w ls-4">ADVERTISING
                </span>
            </div>
            <div class="col-4">
                <span class="fs-18 fw-600 wv-color-v_50 ls-4 ">COMPULSORY</span>
            </div>
        </div>
    </div>
</section>

<section class="wv-temp-1 pt-48 text-center" style="background: linear-gradient(var(--wv-v_10), var(--wv-w))">

    <div class="container container-768">
        <h2 class="mb-12 h1 fw-700 wv-color-v">Showcase Your Products</h2>
        <p class="fs-20 fw-600 mb-24">Upload & Showcase up to 20 of Your Products with <br />Images & Detailed Specifications</p>
        <p>Faccus voluptatius, ut as molores sitius, optur sequiduntur sanditatin cusae est, qui dundantium hiliqui
        ipsuntiur, tet es inctateniet peribust ex es idigent qui uta idi optibusam, ommossectam quis doluptaspe
        dolutectem nate ne ne nime cullant, sequunt, quasi delest et rem quiduci endelignat aut.
        </p>
    </div>

    <div class="container-fluid">

        <div class="d-flex justify-content-center">
            
            <img src="/wp-content/themes/desymphony/src/images/blocks/DSK_Explore_PRODUCTS.png" class="img-fluid" alt="">
            
        </div>
    </div>

    <div class="py-32 border-top">
        <div class="container container-1024">
            <div class="row g-12 align-items-center justify-content-center">
                <div class="col-12 text-center">
                    <p class="fs-14 fw-600 ls-3 mb-0" style="color:var(--wv-c_50)">IMPORTANT STEP RULES</p>
                </div>
                <div class="col-lg-6 d-none">
                    <a href="#" class="wv-button wv-button-pill wv-button-sm wv-button-c_80 d-flex align-items-center justify-content-between px-8"> 
                        <span class="wv wv_info me-4 fs-20 wv-c-w" style="margin: -4px"><span class="path1"></span><span class="path2"></span></span>
                        Step Completion Implies Compulsory Online Payment
                    </a>
                </div>
                <div class="col-lg-3">
                    <a href="#" class="wv-button wv-button-pill wv-button-sm wv-button-c_10 d-flex align-items-center justify-content-between px-8">    
                        Save Progress & Leave                
                        <span class="wv wv_check-70-sq ms-4 fs-20 wv-c-g" style="margin: -4px"><span class="path1"></span><span class="path2"></span></span>
                    </a>
                </div>
                <div class="col-lg-3">
                    <a href="#" class="wv-button wv-button-pill wv-button-sm wv-button-c_10 d-flex align-items-center justify-content-between px-8">    
                        Edit / Revert Actions                
                        <span class="wv wv_check-70-sq ms-4 fs-20 wv-c-g" style="margin: -4px"><span class="path1"></span><span class="path2"></span></span>
                    </a>
                </div>
                <div class="col-12 text-center" style="color: var(--wv-c_50);">
                    <p class="fs-12">Click the Tab for Detailed Rules Overview</p>
                </div>
            </div>
        </div>
    </div>

</section>

<!-- Enlist in the 2025 Catalogue -->
<section class="d-block" style="background: var(--wv-v_dark);">
    <div class="container">
        <div class="row align-items-center justify-content-center text-center py-16">
            <div class="col-4">
                <span class="fs-18 fw-400 wv-color-w ls-4">STEP 1</span>
            </div>
            <div class="col-4">
                <span class="fs-18 fw-400 wv-color-w ls-4">EXHIBITION CONTENT ></span>
                <span class="fs-18 fw-600 wv-color-w ls-4">ADVERTISING
                </span>
            </div>
            <div class="col-4">
                <span class="fs-18 fw-600 wv-color-v_50 ls-4 ">COMPULSORY</span>
            </div>
        </div>
    </div>
</section>

<section class="wv-temp-1 pt-48 text-center" style="background: linear-gradient(var(--wv-v_10), var(--wv-w))">

    <div class="container container-768">
        <h2 class="mb-12 h1 fw-700 wv-color-v">Enlist in the 2025 Catalogue</h2>
        <p class="fs-20 fw-600 mb-24">Everspis a videm laci aut aliaspe rfernat. Occulliquo modis molestia estis dollit eosseca ecuscipsam, omnihic to illorporem.</p>
        <p>As an Addition to Step 1, 2025 Wine Vision by Open Balkan Fair offers his Exhibitors a unique opportunity
        to advertise their Brand through whole vast scope of advertising mediums, both indoor & out, digital &
        print, 2 or 3D. Be a significant visual at one of the most branded fairs of Europe!
        </p>
    </div>

    <div class="container pt-24 pb-48">

        <div class="row g-12">
            <div class="col-lg-6">
               <div class="ds-concept-card bg-image aspect-ratio-16-9" style="background-image: url(https://placehold.co/1024.jpg);">
                    <div class="ds-concept-card-overlay"></div>
                    <div class="ds-concept-card-content">
                        <h3 class="h5 wv-color-w m-0">
                            Publish Your Ad in 2025<br />
                            Official Exhibitors Catalogue
                        </h3>
                    </div>
               </div>
            </div>
           
             <div class="col-lg-6">
               <div class="ds-concept-card bg-image aspect-ratio-16-9" style="background-image: url(https://placehold.co/1024.jpg);">
                    <div class="ds-concept-card-overlay"></div>
                    <div class="ds-concept-card-content">
                        <h3 class="h5 wv-color-w m-0">
                            Advertise on Large Formats<br />
                            at the Fair Venue
                        </h3>
                    </div>
               </div>
            </div>

            <div class="col-lg-6">
               <div class="ds-concept-card bg-image aspect-ratio-16-9" style="background-image: url(https://placehold.co/1024.jpg);">
                    <div class="ds-concept-card-overlay"></div>
                    <div class="ds-concept-card-content">
                        <h3 class="h5 wv-color-w m-0">
                            Place Your Brand on over 200 <br />
                            Venue Flags & Banderolas
                        </h3>
                    </div>
               </div>
            </div>
           
             <div class="col-lg-6">
               <div class="ds-concept-card bg-image aspect-ratio-16-9" style="background-image: url(https://placehold.co/1024.jpg);">
                    <div class="ds-concept-card-overlay"></div>
                    <div class="ds-concept-card-content">
                        <h3 class="h5 wv-color-w m-0">
                            Advertise on Digital Screens <br />
                            Inside Exhibition Halls
                        </h3>
                    </div>
               </div>
            </div>
            
        </div>
    </div>

    <div class="py-32 border-top">
        <div class="container container-1024">
            <div class="row g-12">
                <div class="col-12 text-center">
                    <p class="fs-14 fw-600 ls-3 mb-0" style="color:var(--wv-c_50)">IMPORTANT STEP RULES</p>
                </div>
                <div class="col-lg-6">
                    <a href="#" class="wv-button wv-button-pill wv-button-sm wv-button-c_80 d-flex align-items-center justify-content-between px-8"> 
                        <span class="wv wv_info me-4 fs-20 wv-c-w" style="margin: -4px"><span class="path1"></span><span class="path2"></span></span>
                        Step Completion Implies Compulsory Online Payment
                    </a>
                </div>
                <div class="col-lg-3">
                    <a href="#" class="wv-button wv-button-pill wv-button-sm wv-button-c_10 d-flex align-items-center justify-content-between px-8">    
                        Save Progress & Leave                
                        <span class="wv wv_alert ms-4 fs-20 wv-c-r" style="margin: -4px"><span class="path1"></span><span class="path2"></span></span>
                    </a>
                </div>
                <div class="col-lg-3">
                    <a href="#" class="wv-button wv-button-pill wv-button-sm wv-button-c_10 d-flex align-items-center justify-content-between px-8">    
                        Edit / Revert Actions                
                        <span class="wv wv_alert ms-4 fs-20 wv-c-r" style="margin: -4px"><span class="path1"></span><span class="path2"></span></span>
                    </a>
                </div>
                <div class="col-12 text-center" style="color: var(--wv-c_50);">
                    <p class="fs-12">Click the Tab for Detailed Rules Overview</p>
                </div>
            </div>
        </div>
    </div>

</section>

<!-- Complete Step Exhibitor 1 -->
<section class="wv-temp-1 py-48" style="background: var(--wv-g)">
    <div class="container container-1024 ">
        <div class="row align-items-center justify-content-center">
            <div class="col-lg-4 text-center">
                <div class="wv-button br-8 wv-button-w wv-button-lg d-flex align-items-center justify-content-between px-24 fs-16">
                    Complete Step 1
                    <span class="wv wv_arrow-70 ms-4 fs-24 wv-c-g" style="margin: -4px"><span class="path1"></span><span class="path2"></span></span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Find a Stand that suits Your Needs -->
<section class="d-block" style="background: var(--wv-v_dark);">
    <div class="container">
        <div class="row align-items-center justify-content-center text-center py-16">
            <div class="col-4">
                <span class="fs-18 fw-400 wv-color-w ls-4">STEP S</span>
            </div>
            <div class="col-4">
                <span class="fs-18 fw-400 wv-color-w ls-4">EXHIBITION SPACE ></span>
                <span class="fs-18 fw-600 wv-color-w ls-4">STAND RENTAL
                </span>
            </div>
            <div class="col-4">
                <span class="fs-18 fw-600 wv-color-v_50 ls-4 ">COMPULSORY</span>
            </div>
        </div>
    </div>
</section>

<section class="wv-temp-1 py-48 text-center" style="background: linear-gradient(var(--wv-v_10), var(--wv-w))">

    <div class="container container-768">
        <h2 class="mb-12 h1 fw-700 wv-color-v">Find a Stand that suits Your Needs</h2>
        <p class="fs-20 fw-600 mb-24">Choose between 4 preset stand options and maximize. <br />
                                    Your appearance at the 2025 Wine Vision by Open Balkan Fair!</p>
        <p>As an Addition to Step 1, 2025 Wine Vision by Open Balkan Fair offers his Exhibitors a unique opportunity
            to advertise their Brand through whole vast scope of advertising mediums, both indoor & out, digital &
            print, 2 or 3D. Be a significant visual at one of the most branded fairs of Europe!
        </p>
    </div>

    <div class="container-fluid pt-24 pb-48">

        <!-- stand cards -->
        <div class="row g-12 justify-content-center">
            <!-- repeat this col for each option -->
            <div class="col-6 col-sm-4 col-lg-2">
                <div class="card wv-color-w border-0 overflow-hidden">
                <img src="/wp-content/themes/desymphony/src/images/blocks/DSK_Explore_9.png" class="card-img" alt="9m²">
                <div class="card-img-overlay d-flex align-items-end justify-content-center p-0">
                    <h5 class="card-title h1 mb-48" style="maring-top: -24px">9 m²</h5>
                </div>
                </div>
            </div>

            <div class="col-6 col-sm-4 col-lg-2">
                <div class="card wv-color-w border-0 overflow-hidden">
                <img src="/wp-content/themes/desymphony/src/images/blocks/DSK_Explore_12.png" class="card-img" alt="12m²">
                <div class="card-img-overlay d-flex align-items-end justify-content-center p-0">
                    <h5 class="card-title h1 mb-48" style="maring-top: -24px">12 m²</h5>
                </div>
                </div>
            </div>

            <div class="col-6 col-sm-4 col-lg-2">
                <div class="card wv-color-w border-0 overflow-hidden">
                <img src="/wp-content/themes/desymphony/src/images/blocks/DSK_Explore_24.png" class="card-img" alt="24m²">
                <div class="card-img-overlay d-flex align-items-end justify-content-center p-0">
                    <h5 class="card-title h1 mb-48" style="maring-top: -24px">24 m²</h5>
                </div>
                </div>
            </div>

            <div class="col-6 col-sm-4 col-lg-2">
                <div class="card wv-color-w border-0 overflow-hidden">
                <img src="/wp-content/themes/desymphony/src/images/blocks/DSK_Explore_49.png" class="card-img" alt="49m²">
                <div class="card-img-overlay d-flex align-items-end justify-content-center p-0">
                    <h5 class="card-title h1 mb-48" style="maring-top: -24px">49 m²</h5>
                </div>
                </div>
            </div>

            <div class="col-6 col-sm-4 col-lg-2">
                <div class="card wv-color-w border-0 overflow-hidden">
                <img src="/wp-content/themes/desymphony/src/images/blocks/DSK_Explore_Custom.png" class="card-img" alt="Custom">
                <div class="card-img-overlay d-flex align-items-end justify-content-center p-0">
                    <h5 class="card-title h1 mb-48" style="maring-top: -24px">Custom</h5>
                </div>
                </div>
            </div>

            
        </div>
    </div>

    <div class="py-32 border-top">
        <div class="container container-768 pb-32">
            <h2 class="mb-12 h5 fw-600">Choose the Exhibition Hall</h2>
            <p>As an Addition to Step 1, 2025 Wine Vision by Open Balkan Fair offers his Exhibitors a unique opportunity
                to advertise their Brand through whole vast scope of advertising mediums, both indoor & out, digital &
                print, 2 or 3D. Be a significant visual at one of the most branded fairs of Europe!
            </p>
        </div>

        <div class="d-block py-24 wv-bg-c_10">
            <div class="container container-1024 ds-concept-map">
                <?php include DS_THEME_DIR. '/inc/public/views/halls/hall-nav.svg'; ?>
            </div>
        </div>
    </div>

    <div class="py-32 border-top">
        <div class="container container-1024">
            <div class="row g-12">
                <div class="col-12 text-center">
                    <p class="fs-14 fw-600 ls-3 mb-0" style="color:var(--wv-c_50)">IMPORTANT STEP RULES</p>
                </div>
                <div class="col-lg-6">
                    <a href="#" class="wv-button wv-button-pill wv-button-sm wv-button-c_80 d-flex align-items-center justify-content-between px-8"> 
                        <span class="wv wv_info me-4 fs-20 wv-c-w" style="margin: -4px"><span class="path1"></span><span class="path2"></span></span>
                        Step Completion Implies Compulsory Online Payment
                    </a>
                </div>
                <div class="col-lg-3">
                    <a href="#" class="wv-button wv-button-pill wv-button-sm wv-button-c_10 d-flex align-items-center justify-content-between px-8">    
                        Save Progress & Leave                
                        <span class="wv wv_alert ms-4 fs-20 wv-c-r" style="margin: -4px"><span class="path1"></span><span class="path2"></span></span>
                    </a>
                </div>
                <div class="col-lg-3">
                    <a href="#" class="wv-button wv-button-pill wv-button-sm wv-button-c_10 d-flex align-items-center justify-content-between px-8">    
                        Edit / Revert Actions                
                        <span class="wv wv_alert ms-4 fs-20 wv-c-r" style="margin: -4px"><span class="path1"></span><span class="path2"></span></span>
                    </a>
                </div>
                <div class="col-12 text-center" style="color: var(--wv-c_50);">
                    <p class="fs-12">Click the Tab for Detailed Rules Overview</p>
                </div>
            </div>
        </div>
    </div>
  
    

</section>

<!-- Get Additional Services -->
<section class="d-block" style="background: var(--wv-v_dark);">
    <div class="container">
        <div class="row align-items-center justify-content-center text-center py-16">
            <div class="col-4">
                <span class="fs-18 fw-400 wv-color-w ls-4">STEP S</span>
            </div>
            <div class="col-4">
                <span class="fs-18 fw-400 wv-color-w ls-4">EXHIBITION SPACE ></span>
                <span class="fs-18 fw-600 wv-color-w ls-4">CUSTOM OPTIONS
                </span>
            </div>
            <div class="col-4">
                <span class="fs-18 fw-600 wv-color-v_50 ls-4 ">OPTIONAL</span>
            </div>
        </div>
    </div>
</section>

<section class="wv-temp-1 pt-48 text-center" style="background: linear-gradient(var(--wv-v_10), var(--wv-w))">

    <div class="container container-768">
        <h2 class="mb-12 h1 fw-700 wv-color-v">Get Additional Services</h2>
        <p class="fs-20 fw-600 mb-24">Everspis a videm laci aut aliaspe rfernat. Occulliquo modis molestia estis dollit eosseca ecuscipsam, omnihic to illorporem.</p>
        <p>As an Addition to Step 1, 2025 Wine Vision by Open Balkan Fair offers his Exhibitors a unique opportunity
        to advertise their Brand through whole vast scope of advertising mediums, both indoor & out, digital &
        print, 2 or 3D. Be a significant visual at one of the most branded fairs of Europe!
        </p>
    </div>

    <div class="container pt-24 pb-48">

        <div class="row g-12">
            <div class="col-lg-6">
               <div class="ds-concept-card bg-image aspect-ratio-16-9" style="background-image: url(https://placehold.co/1024.jpg);">
                    <div class="ds-concept-card-overlay"></div>
                    <div class="ds-concept-card-content">
                        <h3 class="h5 wv-color-w m-0">
                            Get the additional benefits with <br /> professional equipment
                        </h3>
                    </div>
               </div>
            </div>
           
             <div class="col-lg-6">
               <div class="ds-concept-card bg-image aspect-ratio-16-9" style="background-image: url(https://placehold.co/1024.jpg);">
                    <div class="ds-concept-card-overlay"></div>
                    <div class="ds-concept-card-content">
                        <h3 class="h5 wv-color-w m-0">
                            Get the additional benefits with <br />professional services
                        </h3>
                    </div>
               </div>
            </div>
            
        </div>
    </div>

    <div class="py-32 border-top">
        <div class="container container-1024">
            <div class="row g-12">
                <div class="col-12 text-center">
                    <p class="fs-14 fw-600 ls-3 mb-0" style="color:var(--wv-c_50)">IMPORTANT STEP RULES</p>
                </div>
                <div class="col-lg-6">
                    <a href="#" class="wv-button wv-button-pill wv-button-sm wv-button-c_80 d-flex align-items-center justify-content-between px-8"> 
                        <span class="wv wv_info me-4 fs-20 wv-c-w" style="margin: -4px"><span class="path1"></span><span class="path2"></span></span>
                        Step Completion Implies Compulsory Online Payment
                    </a>
                </div>
                <div class="col-lg-3">
                    <a href="#" class="wv-button wv-button-pill wv-button-sm wv-button-c_10 d-flex align-items-center justify-content-between px-8">    
                        Save Progress & Leave                
                        <span class="wv wv_alert ms-4 fs-20 wv-c-r" style="margin: -4px"><span class="path1"></span><span class="path2"></span></span>
                    </a>
                </div>
                <div class="col-lg-3">
                    <a href="#" class="wv-button wv-button-pill wv-button-sm wv-button-c_10 d-flex align-items-center justify-content-between px-8">    
                        Edit / Revert Actions                
                        <span class="wv wv_alert ms-4 fs-20 wv-c-r" style="margin: -4px"><span class="path1"></span><span class="path2"></span></span>
                    </a>
                </div>
                <div class="col-12 text-center" style="color: var(--wv-c_50);">
                    <p class="fs-12">Click the Tab for Detailed Rules Overview</p>
                </div>
            </div>
        </div>
    </div>

</section>

<!-- Present Privately at the Fair -->
<section class="d-block" style="background: var(--wv-v_dark);">
    <div class="container">
        <div class="row align-items-center justify-content-center text-center py-16">
            <div class="col-4">
                <span class="fs-18 fw-400 wv-color-w ls-4">STEP S</span>
            </div>
            <div class="col-4">
                <span class="fs-18 fw-400 wv-color-w ls-4">EXHIBITION SPACE ></span>
                <span class="fs-18 fw-600 wv-color-w ls-4">CONFERENCE HALL RENTAL
                </span>
            </div>
            <div class="col-4">
                <span class="fs-18 fw-600 wv-color-v_50 ls-4 ">OPTIONAL</span>
            </div>
        </div>
    </div>
</section>

<section class="wv-temp-1 pt-48 text-center" style="background: linear-gradient(var(--wv-v_10), var(--wv-w))">

    <div class="container container-768">
        <h2 class="mb-12 h1 fw-700 wv-color-v">Present Privately at the Fair</h2>
        <p class="fs-20 fw-600 mb-24">Rent Additional Presentation Space with included Equipment in the secluded part of venue for private occasions</p>
        <p>As an Addition to Step 1, 2025 Wine Vision by Open Balkan Fair offers his Exhibitors a unique opportunity
        to advertise their Brand through whole vast scope of advertising mediums, both indoor & out, digital &
        print, 2 or 3D. Be a significant visual at one of the most branded fairs of Europe!
        </p>
    </div>

    <div class="container pt-24 pb-48">

        <div class="row g-12">
            <div class="col-lg-4">
               <div class="ds-concept-card ds-concept-card2 bg-image aspect-ratio-16-9" style="background-image: url(https://placehold.co/1024.jpg);">
                    <div class="ds-concept-card-overlay"></div>
                    <div class="ds-concept-card-content">
                        <div class="d-flex fs-20 align-items-center justify-content-between wv-color-w w-100">
                            <span class="fw-600">Small Hall</span>
                            <span class="fw-400">50 Seats</span>
                        </div>
                    </div>
               </div>
            </div>

            <div class="col-lg-4">
               <div class="ds-concept-card ds-concept-card2 bg-image aspect-ratio-16-9" style="background-image: url(https://placehold.co/1024.jpg);">
                    <div class="ds-concept-card-overlay"></div>
                    <div class="ds-concept-card-content">
                        <div class="d-flex fs-20 align-items-center justify-content-between wv-color-w w-100">
                            <span class="fw-600">Large Hall</span>
                            <span class="fw-400">100 Seats</span>
                        </div>
                    </div>
               </div>
            </div>

            <div class="col-lg-4">
               <div class="ds-concept-card ds-concept-card2 bg-image aspect-ratio-16-9" style="background-image: url(https://placehold.co/1024.jpg);">
                    <div class="ds-concept-card-overlay"></div>
                    <div class="ds-concept-card-content">
                        <div class="d-flex fs-20 align-items-center justify-content-between wv-color-w w-100">
                            <span class="fw-600">Festive Hall</span>
                            <span class="fw-400">150 Seats</span>
                        </div>
                    </div>
               </div>
            </div>
            
        </div>
    </div>

    <div class="py-32 border-top">
        <div class="container container-1024">
            <div class="row g-12">
                <div class="col-12 text-center">
                    <p class="fs-14 fw-600 ls-3 mb-0" style="color:var(--wv-c_50)">IMPORTANT STEP RULES</p>
                </div>
                <div class="col-lg-6">
                    <a href="#" class="wv-button wv-button-pill wv-button-sm wv-button-c_80 d-flex align-items-center justify-content-between px-8"> 
                        <span class="wv wv_info me-4 fs-20 wv-c-w" style="margin: -4px"><span class="path1"></span><span class="path2"></span></span>
                        Step Completion Implies Compulsory Online Payment
                    </a>
                </div>
                <div class="col-lg-3">
                    <a href="#" class="wv-button wv-button-pill wv-button-sm wv-button-c_10 d-flex align-items-center justify-content-between px-8">    
                        Save Progress & Leave                
                        <span class="wv wv_alert ms-4 fs-20 wv-c-r" style="margin: -4px"><span class="path1"></span><span class="path2"></span></span>
                    </a>
                </div>
                <div class="col-lg-3">
                    <a href="#" class="wv-button wv-button-pill wv-button-sm wv-button-c_10 d-flex align-items-center justify-content-between px-8">    
                        Edit / Revert Actions                
                        <span class="wv wv_alert ms-4 fs-20 wv-c-r" style="margin: -4px"><span class="path1"></span><span class="path2"></span></span>
                    </a>
                </div>
                <div class="col-12 text-center" style="color: var(--wv-c_50);">
                    <p class="fs-12">Click the Tab for Detailed Rules Overview</p>
                </div>
            </div>
        </div>
    </div>

</section>

<!-- Complete Step Exhibitor 2 -->
<section class="wv-temp-1 py-48" style="background: var(--wv-g)">
    <div class="container container-1024 ">
        <div class="row align-items-center justify-content-center">
            <div class="col-lg-4 text-center">
                <div class="wv-button br-8 wv-button-w wv-button-lg d-flex align-items-center justify-content-between px-24 fs-16">
                    Complete Step 2
                    <span class="wv wv_arrow-70 ms-4 fs-24 wv-c-g" style="margin: -4px"><span class="path1"></span><span class="path2"></span></span>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- ---------------------- -->

<div class="d-block text-center p-64 fs-64">Co-Exhibitor Section</div>