<?php
// templates/search.php
get_template_part( 'templates/header' ); ?> 
<div class="container container-1024 py-32 py-lg-48">
  <div class="row pb-32">
      <div class="col-12">
        <h1 class="my-0 px-24 display-5 fw-600">
        <?php printf( esc_html__('Search: %s', 'desymphony'), get_search_query() ); ?>        </h1>
      </div>
    </div>
  <div class="row">
  <div class="col-12">
    <?php if ( have_posts() ) : ?>
      <?php while ( have_posts() ) : the_post(); ?>
        <article <?php post_class('mb-4'); ?>>
          <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
          <?php the_excerpt(); ?>
        </article>
      <?php endwhile; ?>
      <?php the_posts_pagination(); ?>
    <?php else : ?>
      <p><?php esc_html_e('No results found.', 'desymphony'); ?></p>
    <?php endif; ?>
  </div>
</div>
</div>

<?php get_template_part( 'templates/footer' ); ?>
