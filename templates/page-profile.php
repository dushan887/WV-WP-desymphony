<?php
/**
 * Template Name: Page Profile
 */
get_template_part( 'templates/header' );
?>
<style>
  @media screen and (min-width: 992px) {
    .ds-p-t-76 {
      padding-top: 76px !important;
    }
  }
</style>
<div class="container-fluid px-0 ds-p-t-76">
   
  <?php the_content(); ?>
</div>

<?php
get_template_part( 'templates/footer' );
