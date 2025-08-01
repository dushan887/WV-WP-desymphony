<?php
/**
 * Template Name: Full Width Page
 */
get_template_part( 'templates/header' );
?>

<div class="container-fluid px-0">
  <?php the_content(); ?>
</div>

<?php
get_template_part( 'templates/footer' );
