<?php
// templates/page.php
get_template_part( 'templates/header' ); ?>
<div class="row">
  <div class="col-md-8">
    <?php while ( have_posts() ) : the_post(); ?>
      <h1><?php the_title(); ?></h1>
      <?php the_content(); ?>
    <?php endwhile; ?>
  </div>
  <div class="col-md-4">
    <?php get_sidebar(); ?>
  </div>
</div>

page.php
<?php get_template_part( 'templates/footer' ); ?>
