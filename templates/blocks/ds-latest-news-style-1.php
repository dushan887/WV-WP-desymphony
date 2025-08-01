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

$class = 'ds-latest-news-style-1';
if ( ! empty( $block['className'] ) ) {
    $class .= ' ' . esc_attr( $block['className'] );
}

if ( ! empty( $block['align'] ) ) {
    $class .= ' align' . esc_attr( $block['align'] );
}

?>
<style>
  .wv-home-card-4 {
      background-image: url(https://winevisionfair.com/wp-content/uploads/2025/06/DSK_WT_BANNER_Bck.jpg);
  }
  .wv-home-card-5 {
      background-image: url(https://winevisionfair.com/wp-content/uploads/2025/06/DSK_RT_BANNER_Bck.jpg);
  }
  .wv-home-card-6 {
      background-image: url(https://winevisionfair.com/wp-content/uploads/2025/06/DSK_B2B_BANNER_Bck.jpg);
  }
</style>
<section class="pt-48 pb-12 wv-bg-w <?php echo esc_attr( $class ); ?>">
    <div class="container container-1024 pb-24">
        <div class="d-flex align-items-center justify-content-between">
            <h3 class="h1 fw-700">Latest news</h3>
            <a href="<?php echo esc_url( get_permalink( get_option( 'page_for_posts' ) ) ); ?>" class="wv-button wv-button-pill wv-button-grey fw-500 ls-2">
                All news
            </a>
        </div>
    </div>

    <div class="container-fluid px-0">
        <?php
        // in your theme template (e.g. template-parts/news-carousel.php)
        $news_query = new WP_Query( [
        'post_type'      => 'post',
        'posts_per_page' => 8,
        'orderby'        => 'date',
        'order'          => 'DESC',
        ] );
        if ( $news_query->have_posts() ) : ?>
        <div class="wv-h-news-carousel-wrapper">
            <div class="swiper-container wv-h-news-carousel">
            <div class="swiper-wrapper">
                <?php while ( $news_query->have_posts() ) : $news_query->the_post(); ?>
                <div class="swiper-slide wv-h-swiper-slide">
                    <div class="wv-h-news-item">
                        <div class="wv-h-news-bg" style="background-image:url('<?php echo esc_url( get_the_post_thumbnail_url( get_the_ID(), 'large' ) ); ?>')"
                        ></div>
                        <div class="wv-h-news-content p-32">
                            <div class="d-block" style="margin-top: -48px;">
                                <div class="entry-meta mb-24" >
                                    <span class="news-date"><?php echo get_the_date( 'd/m/Y' ); ?></span>
                                    <span class="news-cat">#<?php echo esc_html( get_the_category()[0]->slug ); ?></span>
                                </div>
                            </div>
                            <a class="d-flex align-items-end justify-content-between news-title" href="<?php the_permalink(); ?>">
                                <h3 class="my-0 h2 fw-500 text-white w-75"><?php the_title(); ?></h3>                                
                                <span class="wv wv_arrow-50-o fs-20 color--wv-c_50"></span>
                            </a> 
                        </div>
                    </div>
                </div>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
            </div>
        </div>
        <?php endif; ?>

    </div>
</section>

<section class="wv-apply-section pt-48 mb-12 <?php echo esc_attr( $class ); ?>" style="background: var(--wv-header-gradient);">
  <div class="container">
    <!-- Heading -->
    <div class="text-center">
      <h2 class="fw-600 display-5 mb-0">
        Compleate or trade at the 2025 Fair
      </h2>
      <p class="text-uppercase fs-18 mt-12 mb-32 fw-700 ls-4">
        Apply for professional activities
      </p>
    </div>

     <!-- Cards row -->
    <div class="row gx-12 mt-12 card-heros overflow-hidden">

      <div class="col-lg-4 mb-0">
        <div class="card card-hero exhibit h-100 text-white br-12 br-b-0 border-0 pb-32 bg-image wv-home-card-4">
          <div class="pt-0 pt-lg-12 px-12 d-flex flex-column justify-content-between align-items-center">
            <img src="https://winevisionfair.com/wp-content/uploads/2025/06/WT_Logo.svg" class="img-fluid w-100 mx-auto d-block mt-32 mt-lg-24" alt="">
            <div class="d-block w-75 border-top border-bottom my-24 my-lg-32 mx-auto wv-bc-w_80"></div>
              <p class="card-text text-cetner text-center mb-12 mb-lg-32 h2 fw-700 opacity-75 lh-1">
                APPLY FOR WINE<br/>
                COMPETITION
              </p>
              <div class="d-none d-lg-block">
                <a href="/register" class="wv-button wv-button-pill wv-button-w-op fw-400">Register & Apply</a>
              </div>
              <div class="d-lg-none pb-24">
                <a href="/register" class="wv-button wv-button-pill wv-icon-button wv-button-w-op fw-400 fs-32"><span class="wv wv_arrow-70"><span class="path1"></span><span class="path2"></span></span></a>
              </div>

          </div>
        </div>
      </div>

      <div class="col-lg-4 mb-0">
        <div class="card card-hero exhibit h-100 text-white br-12 br-b-0 border-0 pb-32 bg-image wv-home-card-5">
          <div class="pt-0 pt-lg-12 px-12 d-flex flex-column justify-content-between align-items-center">
            <img src="https://winevisionfair.com/wp-content/uploads/2025/06/RT_Logo.svg" class="img-fluid w-100 mx-auto d-block mt-32 mt-lg-24" alt="">
            <div class="d-block w-75 border-top border-bottom my-24 my-lg-32 mx-auto wv-bc-w_80"></div>
              <p class="card-text text-cetner text-center mb-12 mb-lg-32 h2 fw-700 opacity-75 lh-1">
                APPLY FOR RAKIJA<br/>
                COMPETITION
              </p>
              <div class="d-none d-lg-block">
                <a href="/register" class="wv-button wv-button-pill wv-button-w-op fw-400">Register & Apply</a>
              </div>
              <div class="d-lg-none pb-24">
                <a href="/register" class="wv-button wv-button-pill wv-icon-button wv-button-w-op fw-400 fs-32"><span class="wv wv_arrow-70"><span class="path1"></span><span class="path2"></span></span></a>
              </div>

          </div>
        </div>
      </div>

      <div class="col-lg-4 mb-0">
        <div class="card card-hero exhibit h-100 text-white br-12 br-b-0 border-0 pb-32 bg-image wv-home-card-6">
          <div class="pt-0 pt-lg-12 px-12 d-flex flex-column justify-content-between align-items-center">
            <img src="https://winevisionfair.com/wp-content/uploads/2025/06/B2B_Logo.svg" class="img-fluid w-100 mx-auto d-block mt-32 mt-lg-24" alt="">
            <div class="d-block w-75 border-top border-bottom my-24 my-lg-32 mx-auto wv-bc-w_80"></div>
              <p class="card-text text-cetner text-center mb-12 mb-lg-32 h2 fw-700 opacity-75 lh-1">
                APPLY FOR B2B<br/>
                COMPETITION
              </p>
              <div class="d-none d-lg-block">
                <a href="/register" class="wv-button wv-button-pill wv-button-w-op fw-400">Register & Apply</a>
              </div>
              <div class="d-lg-none pb-24">
                <a href="/register" class="wv-button wv-button-pill wv-icon-button wv-button-w-op fw-400 fs-32"><span class="wv wv_arrow-70"><span class="path1"></span><span class="path2"></span></span></a>
              </div>

          </div>
        </div>
      </div>


    </div>
    <!-- /row -->
  </div>
  <!-- /container -->
</section>