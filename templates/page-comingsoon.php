<?php
/**
 * Template Name: Page Coming Soon
 */
get_template_part( 'templates/header' );
?>

<style>
    #wv-wrap::before {
        background-image: url(https://winevisionfair.com/wp-content/uploads/2025/06/DSK_winevisionfair_Bck.jpg)!important;
    }
    @media screen and (max-width: 768px) {
        #wv-wrap::before {
            background-image: url(https://winevisionfair.com/wp-content/uploads/2025/06/MOB_winevisionfair_Bck.jpg)!important;
        }
    }
     .wv-divider{
        width:8rem;height:1px;margin:2rem auto;
        background:var(--wv-c);
    }
    header, footer {
        display: none !important;
    }
    #wv-main {
        padding: 0 !important;
    }
    /* https://winevisionfair.com/wp-content/uploads/2025/06/winevisionfair_FOOT_LOGO.svg */
    /* https://winevisionfair.com/wp-content/uploads/2025/06/winevisionfair_See_you.svg */
</style>
<div id="wv-wrap" class="py-0 d-flex align-items-center justify-content-center">
    <section class="position-relative">
        <div class="container container-768 py-48">
            <div class="row">
                <div class="col-12 text-center">
                    <h1 class="my-0 lh-1 fw-600 pb-48 display-5">Hello & Welcome!</h1>

                    <p class="fs-20 mb-0">
                        <span class="fw-600">The new version of our website will be fully operational from July 1st 2025.</span><br class="d-none d-md-block">
                        This year, exhibitors, professional buyers, and visitors will be able to apply<br class="d-none d-md-block">
                        for participation in the Wine Vision by Open Balkan fair <span class="fw-600">online</span>, by<br class="d-none d-md-block">
                        registering their accounts here, on our new website.
                    </p>

                    <div class="wv-divider"></div>

                    <p class="h5 fw-700 mb-16 fs-24" style="line-height: 1.5 !important;">
                        Registration will begin on&nbsp;July&nbsp;1<sup>st</sup> and will<br class="d-none d-md-block">
                        be available until&nbsp;October&nbsp;15<sup>th</sup>, 2025.
                    </p>

                    <div class="wv-divider"></div>

                     <img src="https://winevisionfair.com/wp-content/uploads/2025/06/winevisionfair_See_you.svg"
                        alt="Wine Vision Logo" class="img-fluid mb-3" style="max-width:180px;">

                    <div class="d-block py-24"></div>

                    <img src="https://winevisionfair.com/wp-content/uploads/2025/06/winevisionfair_FOOT_LOGO.svg"
                        alt="Wine Vision Logo" class="img-fluid mb-3" style="max-width:220px;">
                </div>
            </div>
        </div>              

    </section>
</div>


<?php
get_template_part( 'templates/footer' );
