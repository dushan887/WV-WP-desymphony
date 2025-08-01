<?php
/**
 * Template for displaying the DS Explore 2025 block.
 *
 * @package Desymphony
 */

use Desymphony\Woo\DS_Woo_Stand_Map;

if (! defined('ABSPATH')) exit;

// 1) Load the master order of halls
$halls_order = require get_theme_file_path('inc/public/views/halls/halls-order.php');

// 2) Figure out which hall is requested (via ?hall=slug)
$current_slug = (isset($_GET['hall']) && in_array($_GET['hall'], $halls_order, true))
    ? sanitize_text_field($_GET['hall'])
    : ''; // If not provided or invalid, empty means "no hall selected"


$class = 'ds-explore-2025';
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

<section class="wv-bg-exhibitor-gradient-light">
    <div class="container">
        <div class="row">
            <div class="col-12 pt-48 pb-12">
                <h1 class="h3 wv-color-w text-center fw-600">Explore the 2025 Exhibitor Application Form</h1>
            </div>
        </div>
    </div>
</section>


<div class="d-block wposition-relative wv-section-box-shadow wv-z-50 py-24 wv-bg-w position-relative ds-scrollbar ds-scrollbar--x z-1">    
    <div class="container" style="min-width: 960px;">
        <div class="row">
            <div class="col-12 d-flex align-items-center">
                <div class="wv-tab-header tab-nav nav">
                    <button class="wv-tab-link wv-text-uppercase wv-button wv-button-pill wv-button-md active"
                            data-bs-toggle="tab" data-bs-target="#wv-sizes">
                        STAND SIZES & PRICES
                    </button>
                    <button class="wv-tab-link wv-text-uppercase wv-button wv-button-pill wv-button-md"
                            data-bs-toggle="tab" data-bs-target="#wv-availability">
                        STANDS AVAILABILITY
                    </button>
                    <button class="wv-tab-link wv-text-uppercase wv-button wv-button-pill wv-button-md"
                            data-bs-toggle="tab" data-bs-target="#wv-managing">
                        STANDS MANAGING
                    </button>
                    <button class="wv-tab-link wv-text-uppercase wv-button wv-button-pill wv-button-md"
                        data-bs-toggle="tab" data-bs-target="#wv-add-products">
                        ADD YOUR PRODUCTS
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-0 tab-content">
     
    <div id="wv-sizes" class="tab-pane fade px-0 show active">

       <div class="wv-temp-1 pt-48 text-center" style="background: linear-gradient(var(--wv-v_10), var(--wv-w))">
            <div class="container container-768">
                <h2 class="mb-12 h1 fw-700 wv-color-v_dark">Choose your stand</h2>
                <p class="fs-20 fw-600 mb-24">Extending across eight impressive exhibition halls, the fourth Wine Vision by Open
                Balkan fair in 2025 provides its potential exhibitors with a set of four predefined
                and carefully designed exhibition stand options, concluding this year’s stand rental
                offer with an exclusive fifth option—a fully customizable stand.
                </p>
            </div>

            <div class="container-fluid">
                <div class="row flex-wrap justify-content-between align-items-center my-0 py-12 g-12 g-lg-24">


                <div class="col-6 col-xxl my-12 py-0">
                    <div class="card br-12 border-0 p-0 br-12 overflow-hidden">
                        <img src="/wp-content/themes/desymphony/src/images/explore/STANDS_HERO_IMG/DSK_EXPLORE_Stands/DSK_HpH_Hall_1G_9m.jpg" alt="" class="w-100 m-auto d-none d-lg-block">
                        <img src="/wp-content/themes/desymphony/src/images/explore/STANDS_HERO_IMG/MOB_EXPLORE_Stands/mob_app_HpH_Hall_1G_9m.jpg" alt="" class="w-100 m-auto d-block d-lg-none">
                        <div class="block d-lg-none position-absolute top-0 start wv-bg-9m2">
                            <div class="ds-stand-info-box ds-stand-info-box-sm d-block br-4 wv-bc-t" style="width: 60px">
                                <span class="ds-stand-info-label wv-color-w fw-600 text-center fs-16 px-0">9m<sup>2</sup></span>
                            </div>
                        </div>
                        <div class="block d-lg-none position-absolute bottom-0 end-0 wv-bg-9m2 p-4">
                             <button
                                type="button"
                                class="wv-button wv-icon-button fs-30 bg-transparent wv-color-w "
                                data-bs-toggle="modal"
                                data-bs-target="#dsStandModal-9m2_Hall_1G"
                                >
                            <span class="wv wv_point-50-f"></span>
                            </button>
                        </div>
                        <div class="card-body p-16 position-absolute bottom-0 start-0 end-0 wv-bg-9m2 d-none d-lg-block">
                            <div class="d-flex align-items-center justify-content-between gap-12">
                                <div class="ds-stand-info-box ds-stand-info-box-sm d-block br-4 wv-bc-w" style="width: 100px">
                                    <span class="ds-stand-info-label wv-color-w fw-600 text-center">9m<sup>2</sup></span>
                                </div>
                                <button
                                    type="button"
                                    class="wv-button wv-button-c opacity-75"
                                    data-bs-toggle="modal"
                                    data-bs-target="#dsStandModal-9m2_Hall_1G"
                                    >
                                Specifications
                                </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-6 col-xxl my-12 py-0">
                        <div class="card br-12 border-0 p-0 br-12 overflow-hidden">
                            <img src="/wp-content/themes/desymphony/src/images/explore/STANDS_HERO_IMG/DSK_EXPLORE_Stands/DSK_HpH_Hall_1_12m.jpg" alt="" class="w-100 m-auto d-none d-lg-block">
                            <img src="/wp-content/themes/desymphony/src/images/explore/STANDS_HERO_IMG/MOB_EXPLORE_Stands/mob_app_HpH_Hall_1_12m.jpg" alt="" class="w-100 m-auto d-block d-lg-none">
                            <div class="block d-lg-none position-absolute top-0 start wv-bg-12m2">
                                <div class="ds-stand-info-box ds-stand-info-box-sm d-block br-4 wv-bc-t" style="width: 60px">
                                    <span class="ds-stand-info-label wv-color-w fw-600 text-center fs-16 px-0">12m<sup>2</sup></span>
                                </div>
                            </div>
                            <div class="block d-lg-none position-absolute bottom-0 end-0 wv-bg-12m2 p-4">
                                <button
                                    type="button"
                                    class="wv-button wv-icon-button fs-30 bg-transparent wv-color-w "
                                    data-bs-toggle="modal"
                                    data-bs-target="#dsStandModal-12m2_Hall_1"
                                    >
                                <span class="wv wv_point-50-f"></span>
                                </button>
                            </div>
                            <div class="card-body p-16 position-absolute bottom-0 start-0 end-0 wv-bg-12m2 d-none d-lg-block">
                                <div class="d-flex align-items-center justify-content-between gap-12">
                                    <div class="ds-stand-info-box ds-stand-info-box-sm d-block br-4 wv-bc-w" style="width: 100px">
                                        <span class="ds-stand-info-label wv-color-w fw-600 text-center">12m<sup>2</sup></span>
                                    </div>
                                    <button
                                        type="button"
                                        class="wv-button wv-button-c opacity-75"
                                        data-bs-toggle="modal"
                                        data-bs-target="#dsStandModal-12m2_Hall_1"
                                        >
                                    Specifications
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-xxl my-12 py-0">
                        <div class="card br-12 border-0 p-0 br-12 overflow-hidden">
                            <img src="/wp-content/themes/desymphony/src/images/explore/STANDS_HERO_IMG/DSK_EXPLORE_Stands/DSK_HpH_Hall_1R_24m.jpg" alt="" class="w-100 m-auto d-none d-lg-block">
                            <img src="/wp-content/themes/desymphony/src/images/explore/STANDS_HERO_IMG/MOB_EXPLORE_Stands/mob_app_HpH_Hall_1R_24m.jpg" alt="" class="w-100 m-auto d-block d-lg-none">
                            <div class="block d-lg-none position-absolute top-0 start wv-bg-24m2">
                                <div class="ds-stand-info-box ds-stand-info-box-sm d-block br-4 wv-bc-t" style="width: 60px">
                                    <span class="ds-stand-info-label wv-color-w fw-600 text-center fs-16 px-0">24m<sup>2</sup></span>
                                </div>
                            </div>
                            <div class="block d-lg-none position-absolute bottom-0 end-0 wv-bg-24m2 p-4">
                                <button
                                    type="button"
                                    class="wv-button wv-icon-button fs-30 bg-transparent wv-color-w "
                                    data-bs-toggle="modal"
                                    data-bs-target="#dsStandModal-24m2_Hall_1"
                                    >
                                <span class="wv wv_point-50-f"></span>
                                </button>
                            </div>
                            <div class="card-body p-16 position-absolute bottom-0 start-0 end-0 wv-bg-24m2 d-none d-lg-block">
                                <div class="d-flex align-items-center justify-content-between gap-12">
                                    <div class="ds-stand-info-box ds-stand-info-box-sm d-block br-4 wv-bc-w" style="width: 100px">
                                        <span class="ds-stand-info-label wv-color-w fw-600 text-center">24m<sup>2</sup></span>
                                    </div>
                                    <button
                                        type="button"
                                        class="wv-button wv-button-c opacity-75"
                                        data-bs-toggle="modal"
                                        data-bs-target="#dsStandModal-24m2_Hall_1"
                                        >
                                    Specifications
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-6 col-xxl my-12 py-0">
                        <div class="card br-12 border-0 p-0 br-12 overflow-hidden">
                            <img src="/wp-content/themes/desymphony/src/images/explore/STANDS_HERO_IMG/DSK_EXPLORE_Stands/DSK_HpH_Hall_2B_2A_2C_49m.jpg" alt="" class="w-100 m-auto d-none d-lg-block">
                            <img src="/wp-content/themes/desymphony/src/images/explore/STANDS_HERO_IMG/MOB_EXPLORE_Stands/mob_app_HpH_Hall_2B_2A_2C_49m.jpg" alt="" class="w-100 m-auto d-block d-lg-none">
                            <div class="block d-lg-none position-absolute top-0 start wv-bg-49m2">
                                <div class="ds-stand-info-box ds-stand-info-box-sm d-block br-4 wv-bc-t" style="width: 60px">
                                    <span class="ds-stand-info-label wv-color-w fw-600 text-center fs-16 px-0">49m<sup>2</sup></span>
                                </div>
                            </div>
                            <div class="block d-lg-none position-absolute bottom-0 end-0 wv-bg-49m2 p-4">
                                <button
                                    type="button"
                                    class="wv-button wv-icon-button fs-30 bg-transparent wv-color-w "
                                    data-bs-toggle="modal"
                                    data-bs-target="#dsStandModal-49m2_Hall_1"
                                    >
                                <span class="wv wv_point-50-f"></span>
                                </button>
                            </div>
                            <div class="card-body p-16 position-absolute bottom-0 start-0 end-0 wv-bg-49m2 d-none d-lg-block">
                                <div class="d-flex align-items-center justify-content-between gap-12">
                                    <div class="ds-stand-info-box ds-stand-info-box-sm d-block br-4 wv-bc-w" style="width: 100px">
                                        <span class="ds-stand-info-label wv-color-w fw-600 text-center">49m<sup>2</sup></span>
                                    </div>
                                    <button
                                        type="button"
                                        class="wv-button wv-button-c opacity-75"
                                        data-bs-toggle="modal"
                                        data-bs-target="#dsStandModal-49m2_Hall_1"
                                        >
                                    Specifications
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-6 col-xxl my-12 py-0">
                        <div class="card br-12 border-0 p-0 br-12 overflow-hidden">
                            <img src="/wp-content/themes/desymphony/src/images/explore/STANDS_HERO_IMG/DSK_EXPLORE_Stands/DSK_HpH_Custom_1000x890.jpg" alt="" class="w-100 m-auto d-none d-lg-block">
                            <img src="/wp-content/themes/desymphony/src/images/explore/STANDS_HERO_IMG/MOB_EXPLORE_Stands/mob_app_HpH_Custom.jpg" alt="" class="w-100 m-auto d-block d-lg-none">
                            <div class="block d-lg-none position-absolute top-0 start wv-bg-custom">
                                <div class="ds-stand-info-box ds-stand-info-box-sm d-block br-4 wv-bc-t" style="width: 80px">
                                    <span class="ds-stand-info-label wv-color-w fw-600 text-center fs-16 px-0">Custom</span>
                                </div>
                            </div>
                            <div class="block d-lg-none position-absolute bottom-0 end-0 wv-bg-custom p-4">
                                <button
                                    type="button"
                                    class="wv-button wv-icon-button fs-30 bg-transparent wv-color-w "
                                    data-bs-toggle="modal"
                                    data-bs-target="#dsStandModal-custom"
                                    >
                                <span class="wv wv_point-50-f"></span>
                                </button>
                            </div>
                            <div class="card-body p-16 position-absolute bottom-0 start-0 end-0 wv-bg-custom d-none d-lg-block">
                                <div class="d-flex align-items-center justify-content-between gap-12">
                                    <div class="ds-stand-info-box ds-stand-info-box-sm d-block br-4 wv-bc-w" style="width: 100px">
                                        <span class="ds-stand-info-label wv-color-w fw-600 text-center">Custom</span>
                                    </div>
                                    <button
                                        type="button"
                                        class="wv-button wv-button-c opacity-75"
                                        data-bs-toggle="modal"
                                        data-bs-target="#dsStandModal-custom"
                                        >
                                    Specifications
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>

            <div class="py-32">
                <div class="container container-1024">
                    <div class="row g-12 align-items-center justify-content-center">
                        <div class="col-12 text-center">
                            <p>Each of these four exhibition stand options comes equipped with professional furnishings, included in the rental price. Additionally, for those seeking a more personalized approach, a custom stand option is also available to meet the unique needs and preferences of 2025 exhibitors. With the custom stand option, an exhibitor secures dedicated raw space,
                            offering unparalleled creative freedom. Exhibitors have the opportunity to contract with Belgrade Fair’s architectural experts and production team, which can provide a turnkey service for constructing a unique and fully customized exhibition stand, designed to maximize their brand’s presence and ensure outstanding performance at the 2025 fair.
                            </p>
                        </div>

                        <div class="col-12">
                            <div class="d-block border wv-bc-c py-12 ps-40 pe-12 position-relative text-start br-12">
                                <span class="wv wv_info position-absolute top-0 start-0 mt-8 ms-8 fs-24"><span class="path1"></span><span class="path2"></span></span>
                                <p class="fs-14 m-0"><strong>Important:</strong> The custom stand option exclusively implies the rental of a designated size raw space, not the pre-constructed exhibition stand. Upon renting a custom stand, a representative from the Belgrade Fair Production Department team will contact you as soon as possible.</p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
       </div>

       <div id="wv-stand-prices" class="border-top wv-bg-w wv-bc-c_50 pt-32 pb-64">
            <div class="container-fluid px-lg-128">
                <h2 class="text-center ls-3 my-0 pb-24 h3 fw-600">STAND PRICES PER HALL*</h2>
                <div class="d-block w-100">
                    <img src="https://winevisionfair.com/wp-content/uploads/2025/07/DSK_Stand_Prices.svg" alt="" class="img-fluid m-auto d-none d-lg-block">
                    <img src="https://winevisionfair.com/wp-content/uploads/2025/07/MOB_Stand_Prices.svg" alt="" class="img-fluid m-auto d-block d-lg-none">
                </div>
            </div>

            <div class="py-32">
                <div class="container container-1024">
                    <div class="row g-12 align-items-center justify-content-center">
                        

                        <div class="col-12">
                            <div class="d-block border wv-bc-v py-12 ps-40 pe-12 position-relative text-start br-12">
                                <span class="wv wv_asterix position-absolute top-0 start-0 mt-8 ms-8 fs-24 wv-color-v"><span class="path1"></span><span class="path2"></span></span>
                                <p class="fs-14 m-0"><strong>Please keep the following in mind:</strong> Online payment of all exhibiting expenses is compulsory. Belgrade Fair reserves the right to adjust exhibition space rental prices. Prices not included in the application can be found in the Belgrade Fair price list. All prices are subject to change in accordance with market fluctuations. VAT will be added as per law.</p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        
        </div>

    </div>

    <div id="wv-availability" class="tab-pane fade ds-hall-root px-0">       
        <?php require DS_THEME_DIR . '/inc/public/views/dashboard/partial/ds-stand-hall-nav-light.php'; ?>
        
        <div class="d-block border-top pt-24 wv-bc-c"></div>

        <style>
            .hall-nav-svg .cls-2 {
                fill: var(--wv-w);
            }
            .hall-nav-svg .wv-nav-hall:not(.active):not(:hover) .cls-3 {
                fill: var(--wv-c_10) !important;
            }
            .wv-color-v .path1::before {
                color: var(--wv-v);
            }
        </style>
          

        <?php if ($current_slug === ''): ?>
            <div id="hall-content"></div>
        <?php else: ?>
            <?php
            // 3) Load stands just for that hall
            $map_for_hall = DS_Woo_Stand_Map::get_map_for_hall($current_slug);
            $stands = $map_for_hall[$current_slug] ?? [];
            // 4) Next/prev
            $current_index = array_search($current_slug, $halls_order, true);
            $prev_index    = ($current_index > 0) ? $current_index - 1 : count($halls_order) - 1;
            $next_index    = ($current_index < count($halls_order) - 1) ? $current_index + 1 : 0;

            $prev_hall_slug = $halls_order[$prev_index];
            $next_hall_slug = $halls_order[$next_index];

            // 5) Load the relevant hall’s SVG
            $svg_file = get_theme_file_path("inc/public/views/halls/hall-{$current_slug}.svg");
            $hall_svg = file_exists($svg_file) ? file_get_contents($svg_file) : '';
            $current_hall_label = "Hall " . $current_slug;

            ?>
            
            <div id="hall-content" data-hall-slug="<?php echo esc_attr($current_slug); ?>">
                <style>.ds-hall-counter.order-2 {order: 1 !important; }</style>
                <?php require DS_THEME_DIR . '/inc/public/views/dashboard/partial/ds-hall-apply.php'; ?>
            </div>
        <?php endif; ?>

        <div class="py-32">
            <div class="container container-1024">
                <div class="row g-12 align-items-center justify-content-center">
                    

                    <div class="col-12">
                        <div class="d-block border wv-bc-v py-12 ps-40 pe-12 position-relative text-start br-12">
                            <span class="wv wv_asterix position-absolute top-0 start-0 mt-8 ms-8 fs-24 wv-color-v"><span class="path1"></span><span class="path2"></span></span>
                            <p class="fs-14 m-0"><strong>Please keep the following in mind:</strong> Stands availability may vary due to the ongoing application process. Stands marked in gray are not available.</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        

    </div>

    <div id="wv-managing" class="tab-pane fade px-0">    

       <div class="pt-48 text-center" style="background: linear-gradient(var(--wv-v_10), var(--wv-w))">
            <div class="container container-768">
                <h2 class="mb-12 h1 fw-700 wv-color-v_dark">Share or assign stands</h2>
                <p class="fs-20 fw-600 mb-24">Depending on the selected participation model or rented stand type, an Association exhibitor may rent multiple stands and assign them to invited members. Also, winemakers who rent 24m2 or 49m2 stands can share their stands with invited co-exhibitors.
                </p>
            </div>

            <div class="container container-1024">
                <div class="row g-12">
                    <div class="col-lg-6">
                        <img src="https://winevisionfair.com/wp-content/uploads/2025/06/EXPLORE_24m_Stand_SHARE.jpg" class="img-fluid br-12" alt="">
                    </div>
                    <div class="col-lg-6">
                        <img src="https://winevisionfair.com/wp-content/uploads/2025/06/EXPLORE_49m_Stand_SHARE.jpg" class="img-fluid br-12" alt="">
                    </div>
                </div>
            </div>

            <div class="py-32">
                <div class="container container-1024">
                    <div class="row g-12 align-items-center justify-content-center">
                        <div class="d-block border wv-bc-v py-12 ps-40 pe-12 position-relative text-start br-12">
                            <span class="wv wv_asterix position-absolute top-0 start-0 mt-8 ms-8 fs-24 wv-color-v"><span class="path1"></span><span class="path2"></span></span>
                            <p class="fs-14 m-0"><strong>Please keep the following in mind:</strong> For an exhibitor to invite a co-exhibitor, the inviting exhibitor must pay the compulsory 70€ participation fee online for each co-exhibitor he or she wishes to invite. Once this fee is paid, an invitation is sent. After the co-exhibitor accepts the invitation and registers an account, the inviting exhibitor’s exhibition stand is automatically shared with his or hers newly registered co-exhibitor.</p>
                        </div>
                    </div>
                </div>
            </div>
       </div>

       <div class="pt-48 text-center wv-bg-w border-top wv-bc-c_80" >
            <div class="container container-768">
                <h2 class="mb-32 h4 fw-500">Head exhibitors can assign multiple exhibition stands to members by following these four simple steps for each stand assignment:</h2>
            </div>

            <div class="container container-1024 pb-64">
                <div class="row g-12">
                    <div class="col-lg-6">
                        <img src="https://winevisionfair.com/wp-content/uploads/2025/06/EXPLORE_Assign_BANNER_4.svg" class="img-fluid br-12" alt="">
                    </div>
                    <div class="col-lg-6">
                        <img src="https://winevisionfair.com/wp-content/uploads/2025/06/EXPLORE_Assign_BANNER_2.svg" class="img-fluid br-12" alt="">
                    </div>
                    <div class="col-lg-6">
                        <img src="https://winevisionfair.com/wp-content/uploads/2025/06/EXPLORE_Assign_BANNER_3.svg" class="img-fluid br-12" alt="">
                    </div>
                    <div class="col-lg-6">
                        <img src="https://winevisionfair.com/wp-content/uploads/2025/06/EXPLORE_Assign_BANNER_1.svg" class="img-fluid br-12" alt="">
                    </div>
                </div>
            </div>

            
       </div>

    </div>

    <div id="wv-add-products" class="tab-pane fade px-0">
        <div class="pt-48 text-center wv-bg-w">
            <div class="container container-768">
                <h2 class="mb-12 h1 fw-700 wv-color-v_dark">Showcase your products</h2>
                <p class="fs-20 fw-600 mb-24">
                    Enlist your products across more than 40 categories of wine and spirits, detailing all their attributes through over 10 professional parameters. Present each product with an image of its packaging and highlight your Exhibitor’s Portfolio at the 2025 Wine Vision by Open Balkan fair.
                </p>
            </div>
            
            <div class="d-flex justify-content-center align-items-center">
                <img src="https://winevisionfair.com/wp-content/uploads/2025/06/DSK_EXPLORE_Products_img.jpg" alt="" class="img-fluid m-auto d-none d-lg-block w-100">
                <img src="https://winevisionfair.com/wp-content/uploads/2025/06/MOB_EXPLORE_Products_img.jpg" alt="" class="img-fluid m-auto d-block d-lg-none">
            </div>
            
            
        </div>


        <div class="pt-48 text-center wv-bg-w border-top wv-bc-c_80" >
            <div class="container container-768">
                <h2 class="pb-32 my-0 h4 fw-500">You can submit products from your Exhibitor’s Portfolio to Open Balkan Wine or Rakija Trophy with just one click!</h2>
            </div>            
       </div>
       
            
        <div class="d-flex justify-content-center align-items-center">
            <img src="https://winevisionfair.com/wp-content/uploads/2025/06/DSK_EXPLORE_WT_RT_img.jpg" alt="" class="img-fluid m-auto d-none d-lg-block w-100">
            <img src="https://winevisionfair.com/wp-content/uploads/2025/06/MOB_EXPLORE_WT_RT_img.jpg" alt="" class="img-fluid m-auto d-block d-lg-none">
        </div>

    </div>

</div>

<section class="wv-bg-g d-none">
    <div class="container container-768 py-48">
        <a href="#" class="wv-button wv-button-w d-flex w-100 justify-content-between align-items-center wv-button-lg px-24 px-lg-48"> 
            <span>Start 2025 Exhibitor Application Form</span>
            <i class="wv wv_arrow-80 fs-32 ms-8 wv-c-g"><span class="path1"></span><span class="path2"></span></i>
        </a>
    </div>
</section>

<?php require DS_THEME_DIR . '/inc/public/views/dashboard/partial/ds-stand-modal.php';  ?> 

<script>
jQuery('[data-bs-target="#wv-availability"]').on('click', () =>
    jQuery('#wv-nav-hall_3').trigger('click')
);



</script>