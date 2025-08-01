<?php
// templates/archive.php
get_template_part( 'templates/header' ); ?> ?>
<div class="row">
  <div class="col-md-8">
    <?php if ( have_posts() ) : ?>
      <h1><?php the_archive_title(); ?></h1>
      <?php while ( have_posts() ) : the_post(); ?>
        <article <?php post_class('mb-4'); ?>>
          <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
          <?php the_excerpt(); ?>
        </article>
      <?php endwhile; ?>
      <?php the_posts_pagination(); ?>
    <?php else : ?>
      <p><?php esc_html_e('No posts found.', 'desymphony'); ?></p>
    <?php endif; ?>
  </div>
  <div class="col-md-4">
    <?php get_sidebar(); ?>
  </div>
</div>
<?php get_template_part( 'templates/footer' ); ?>

