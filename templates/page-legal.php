<?php
/**
 * Template Name: Legal Page
 */
get_template_part( 'templates/header' );
?>

<div class="d-block py-32 py-lg-48" style="margin-top: 76px;">

  <div class="container container-1024">
    <div class="row pb-32">
      <div class="col-12">
        <h1 class="my-0 px-24 display-5 fw-600">
        <?php the_title(); ?>
        </h1>
      </div>
    </div>
    
    <div class="row">
      <div class="col-12">
          <?php the_content(); ?>
      </div>  
    </div>

  </div>
</div>

<?php
get_template_part( 'templates/footer' );
