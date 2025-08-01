<?php
/**
 * templates/single.php
 */

defined( 'ABSPATH' ) || exit;

// 1) Header
get_template_part( 'templates/header' );

// 2) PTB toggle bar with categories
require DS_THEME_DIR . '/templates/blocks/ds-news-ptb.php';
?>

<div class="container container-1024 py-32">
  <?php if ( have_posts() ) : the_post(); ?>
    <div class="row gx-48">
      
      <!-- LEFT COLUMN: Main Post -->
      <div class="col-12 col-lg-8 mb-32 border-lg-right">
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
          <header class="entry-header mb-24">
            <?php
            if ( has_post_thumbnail() ) {
                the_post_thumbnail( 'large', [ 'class' => 'img-fluid mb-3' ] );
            } else {
                echo '<img src="https://placehold.co/1024.jpg" class="img-fluid mb-3" alt="Placeholder">';
            }
            ?>
            <div class="entry-meta mt-24">
              <span class="news-date"><?php echo get_the_date( 'd/m/Y' ); ?></span>
              <span class="news-cat">#<?php echo esc_html( get_the_category()[0]->slug ); ?></span>
            </div>
            <h1 class="entry-title h2 my-24"><?php the_title(); ?></h1>
          </header>

          <div class="entry-content">
            <?php the_content(); ?>
          </div>
        </article>
      </div>

      <!-- RIGHT COLUMN: Related News -->
      <aside class="col-12 col-lg-4">
        <h4 class="related-news-title mb-24 text-uppercase ls-3 fs-20"><?php esc_html_e( 'Related News', 'desymphony' ); ?></h4>
        <?php
        $related = new WP_Query( [
          'posts_per_page'   => 4,
          'post__not_in'     => [ get_the_ID() ],
          'category__in'     => wp_get_post_categories( get_the_ID() ),
          'ignore_sticky_posts' => true,
        ] );

        if ( $related->have_posts() ) :
          while ( $related->have_posts() ) : $related->the_post();
        ?>
            <div class="related-post mb-24">
              <div class="d-flex align-items-end text-decoration-none pb-24 border-bottom">
                <div class="pe-8">
                  <div class="entry-meta mb-12">
                    <span class="news-date"><?php echo get_the_date( 'd/m/Y' ); ?></span>
                    <span class="news-cat">#<?php echo esc_html( get_the_category()[0]->slug ); ?></span>
                  </div>
                  <a class="mb-0 h5 related-title fs-18 color--wv-c d-block" href="<?php the_permalink(); ?>">
                    <?php echo wp_trim_words( get_the_title(), 15, '...' ); ?>
                  </a>
                </div>
                <span class="ms-auto">
                  <a class="wv wv_arrow-70 d-flex align-items-center justify-content-center fs-20" href="<?php the_permalink(); ?>">
                    <span class="path1 color--wv-c_20"></span>
                    <span class="path2 color--wv-c_70"></span>
                </a>
                </span>
              </div>
            </div>
        <?php
          endwhile;
          wp_reset_postdata();
        endif;
        ?>
      </aside>

    </div>
  <?php endif; ?>
</div>

<?php
// 3) Footer
get_template_part( 'templates/footer' );
