<?php
// index.php in your theme root
get_template_part( 'templates/header' );
?>

<main class="site-main">
  <?php
  if ( have_posts() ) {
    while ( have_posts() ) {
      the_post();
      the_content();
    }
  } else {
    echo '<p>No content found.</p>';
  }
  ?>
</main>


<?php
get_template_part( 'templates/footer' );

