<?php
/**
 * Top navigation bar showing all halls as SVG “bullets”.
 * The current hall gets an extra “active” class.
 */

// $current_hall_slug must be available in scope. When called via AJAX,
// it is defined by DS_Public_Init; on initial page load, ds-halls.php
// has set the wrapper <div id="hall-content" data-hall-slug="…">. We can read it here:

$current_hall_slug = '';
if ( isset( $_GET['hall'] ) && sanitize_text_field( $_GET['hall'] ) ) {
    $candidate = sanitize_text_field( $_GET['hall'] );
    $halls_order = require get_theme_file_path( 'inc/public/views/halls/halls-order.php' );
    if ( in_array( $candidate, $halls_order, true ) ) {
        $current_hall_slug = $candidate;
    }
}

// Load the raw SVG
$raw_nav_svg = file_get_contents( get_theme_file_path('inc/public/views/halls/hall-nav.svg') );

// Inject “active” class on the <g id="wv-nav-hall_{slug}"> that matches $current_hall_slug.
if ( $current_hall_slug !== '' ) {
    // Use a regex to find: <g ... id="wv-nav-hall_{slug}" ... class="...">
    $pattern = '/(<g\b[^>]*\bid="wv-nav-hall_' . preg_quote( $current_hall_slug, '/' ) . '"[^>]*class=")([^"]*)(")/';
    $replacement = '$1$2 active$3';
    $modified_svg = preg_replace( $pattern, $replacement, $raw_nav_svg );
    // If that <g> had no class attribute, do a fallback to insert class="active":
    if ( $modified_svg === $raw_nav_svg ) {
        // Look for <g id="wv-nav-hall_{slug}" and insert class="active" immediately:
        $pattern2 = '/<g\b([^>]*\bid="wv-nav-hall_' . preg_quote( $current_hall_slug, '/' ) . '"[^>]*)>/';
        $replacement2 = '<g$1 class="active">';
        $modified_svg = preg_replace( $pattern2, $replacement2, $raw_nav_svg );
    }
} else {
    // If no slug found, show raw SVG
    $modified_svg = $raw_nav_svg;
}
?>

<section class="container-fluid wv-bg-carbon-gradient-down">
  <div class="hall hall_nav d-flex justify-content-center align-items-center pt-24 pb-12">
      <?php echo $modified_svg; ?>      
  </div>
  <div class="d-flex justify-content-center align-items-center flex-column pb-24">
    <h4 id="wv-selected-hall" class="my-0 fs-20 ls-4 lh-1 wv-color-w text-uppercase">Select Hall</h4>
  </div>
</section>