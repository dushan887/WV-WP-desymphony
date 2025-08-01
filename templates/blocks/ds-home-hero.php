<?php

/**
 * Block Name: ds-home-hero
 *
 * This is the template that displays the hero section on the homepage.
 *
 * @package WordPress
 * @subpackage Desymphony
 * @since 1.0.0
 */

$class = 'ds-home-hero';
if ( ! empty( $block['className'] ) ) {
    $class .= ' ' . esc_attr( $block['className'] );
}

if ( ! empty( $block['align'] ) ) {
    $class .= ' align' . esc_attr( $block['align'] );
}

?>
<style>
  .wv-home-card-1 {
      background-color: var(--wv-v);
      background-image: url(https://winevisionfair.com/wp-content/uploads/2025/06/DSK_Exhibitor_BANNER_Bck.jpg);
  }
  .wv-home-card-2 {
      background-color: var(--wv-y);
      background-image: url(https://winevisionfair.com/wp-content/uploads/2025/06/DSK_Buyer_BANNER_Bck.jpg);
  }
  .wv-home-card-3 {
      background-color: var(--wv-r_dark);
      background-image: url(https://winevisionfair.com/wp-content/uploads/2025/06/DSK_Visitor_BANNER_Bck.jpg);
  }
</style>
 <!-- Hero Section -->
<section class="wv-apply-section pt-48 mb-12 <?php echo esc_attr( $class ); ?>" style="background: var(--wv-header-gradient);">
  <div class="container">
    <!-- Heading -->
    <div class="text-center mb-24">
      <h2 class="fw-600 display-5">
        Apply for 2025 Wine Vision by<br/>
        Open Balkan online!
      </h2>
      <p class="text-uppercase fs-18 my-24 fw-700 ls-4">
        APPLICATION DEALINE OCTOBER 1<sup>ST</sup>, 2025.
      </p>
    </div>

    <!-- Cards row -->
    <div class="row gx-12 mt-12 card-heros overflow-hidden">
      <!-- Exhibit Card -->
      <div class="col-lg-4 mb-0">
        <div class="card card-hero exhibit h-100 text-white br-12 br-b-0 border-0 pb-32 bg-image wv-home-card-1">
          <div class="pt-0 pt-lg-12 px-12 d-flex flex-column justify-content-between align-items-center">
            <img src="https://winevisionfair.com/wp-content/uploads/2025/06/DSK_Exhibitor_BANNER_Head.svg" class="img-fluid w-100 mx-auto d-block" alt="">
            <div class="d-none d-lg-block w-100 text-center">
              <p class="card-text text-cetner text-center mb-24">
                Showcase your products<br/>
                <span class="fw-600">Create Exhibitor's account</span>
              </p>
              <a href="/register" class="wv-button wv-button-pill wv-button-dark-op">Register now!</a>
            </div>
            <div class="d-flex d-lg-none align-items-center justify-content-between w-100 pb-24 px-12">
              <p class="card-text text-cetner text-start mb-0 fs-14">
                Showcase your products<br/>
                <span class="fw-600">Create Exhibitor's account</span>
              </p>
              <a href="/register" class="wv-button wv-button-pill wv-button-w px-12 fs-14">Register now!</a>
            </div>
          </div>
        </div>
      </div>

      <!-- Trade Card -->
      <div class="col-lg-4 mb-0">
        <div class="card card-hero exhibit h-100 text-white br-12 br-b-0 border-0 pb-32 bg-image wv-home-card-2">
          <div class="pt-0 pt-lg-12 px-12 d-flex flex-column justify-content-between align-items-center">
            <img src="https://winevisionfair.com/wp-content/uploads/2025/06/DSK_Buyer_BANNER_Head.svg" class="img-fluid w-100 mx-auto d-block" alt="">
            <div class="d-none d-lg-block w-100 text-center">
              <p class="card-text text-cetner text-center mb-24">
                Expand your professional network<br/>
                <span class="fw-600">Create Professional Buyer's account</span>
              </p>
              <a href="/register" class="wv-button wv-button-pill wv-button-dark-op">Register now!</a>
            </div>
            <div class="d-flex d-lg-none align-items-center justify-content-between w-100 pb-24 px-12">
              <p class="card-text text-cetner text-start mb-0 fs-14">
                Expand your professional network<br/>
                <span class="fw-600">Create Professional Buyer's account</span>
              </p>
              <a href="/register" class="wv-button wv-button-pill wv-button-w px-12 fs-14">Register now!</a>
            </div>
          </div>
        </div>
      </div>

      <!-- Visit Card -->
      <div class="col-lg-4 mb-0">
        <div class="card card-hero exhibit h-100 text-white br-12 br-b-0 border-0 pb-32 bg-image wv-home-card-3">
          <div class="pt-0 pt-lg-12 px-12 d-flex flex-column justify-content-between align-items-center">
            <img src="https://winevisionfair.com/wp-content/uploads/2025/06/DSK_Visitor_BANNER_Head.svg" class="img-fluid w-100 mx-auto d-block" alt="">
            <div class="d-none d-lg-block w-100 text-center">
              <p class="card-text text-cetner text-center mb-24">
                Experience new flavors<br/>
                <span class="fw-600">Create Visitor's account</span>
              </p>
              <a href="/register" class="wv-button wv-button-pill wv-button-dark-op">Register now!</a>
            </div>
            <div class="d-flex d-lg-none align-items-center justify-content-between w-100 pb-24 px-12">
              <p class="card-text text-cetner text-start mb-0 fs-14">
                Experience new flavors<br/>
                <span class="fw-600">Create Visitor's account</span>
              </p>
              <a href="/register" class="wv-button wv-button-pill wv-button-w px-12 fs-14">Register now!</a>
            </div>

          </div>
        </div>
      </div>
    </div>
    <!-- /row -->
  </div>
  <!-- /container -->
</section>
<!-- /section -->

<style>
  .video-bg-section{
    position:relative;
    width:100%;
    aspect-ratio:16/9;
    overflow:hidden;
  }
  .video-bg-section iframe{
    position:absolute;
    inset:0;
    width:100%;
    height:100%;
    border:0;
    pointer-events:none;          /* nothing is clickable */
  }
</style>

<section class="video-bg-section mb-12 wv-bg-w">
  <iframe
    src="https://www.youtube.com/embed/-nBW9_wLPVw?autoplay=1&mute=1&loop=1&playlist=-nBW9_wLPVw&controls=0&modestbranding=1&playsinline=1&disablekb=1&rel=0"
    allow="autoplay; encrypted-media"
    loading="lazy"
    title="background video">
  </iframe>
</section>




