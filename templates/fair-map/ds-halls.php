<?php
/**
 * Template: ds-halls
 * This shows the single “active” hall plus the ds-exhibitors-tab at the top.
 */
use Desymphony\Woo\DS_Woo_Stand_Map;
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
// Gather classes 
$classes = [ 'ds-halls' ];
if (! empty( $block['className'] )) {
    $classes[] = $block['className'];
}
if (! empty( $block['align'] )) {
    $classes[] = 'align' . $block['align'];
}

// 1) Load the master order of halls
$halls_order = require get_theme_file_path( 'inc/public/views/halls/halls-order.php' );

// 2) Load the stands map
$halls_stands = \Desymphony\Woo\DS_Woo_Stand_Map::get_map();

// Figure out which hall is requested (via ?hall=slug, or default to the first)
$current_slug = isset( $_GET['hall'] ) && in_array( $_GET['hall'], $halls_order, true )
    ? sanitize_text_field( $_GET['hall'] )
    : null;


// Compute indexes
$current_index = array_search( $current_slug, $halls_order, true );
$prev_index    = ( $current_index > 0 ) ? $current_index - 1 : count( $halls_order ) - 1;
$next_index    = ( $current_index < count( $halls_order ) - 1 ) ? $current_index + 1 : 0;

// Prepare prev/next slugs
$prev_hall_slug = $halls_order[ $prev_index ];
$next_hall_slug = $halls_order[ $next_index ];

// Load the relevant hall’s SVG file
$svg_file = get_theme_file_path( "inc/public/views/halls/hall-{$current_slug}.svg" );
$hall_svg = file_exists( $svg_file ) ? file_get_contents( $svg_file ) : '';

// Fetch stands for this hall
$stands = $halls_stands[ $current_slug ] ?? [];

// Optional: A label like "Hall 3A"
$current_hall_label = "Hall " . $current_slug;

require DS_THEME_DIR . '/templates/blocks/ds-fair-map-ptb.php';
?>

<div class="tab-content ds-hall-root ds-hall--list" id="tabNavContent">
  <div
    class="tab-pane fade"
    id="venue-map"
    role="tabpanel"
    aria-labelledby="venue-map-tab"
  >
    <?php require DS_THEME_DIR . '/templates/fair-map/ds-venu-map-tab.php'; ?>
  </div>

    <div
      class="tab-pane fade show active"
      id="exhibitors-tab"
      role="tabpanel"
      aria-labelledby="exhibitors-tab"
    >
      <!-- Top navigation (the big nav-hall SVG) -->
      <?php require DS_THEME_DIR . '/templates/fair-map/ds-exhibitors-tab.php'; ?>

      <!-- Everything inside #hall-content will be replaced via AJAX -->
      <div id="hall-content" data-hall-slug="<?php echo esc_attr( $current_slug ); ?>">
        <section class="d-block <?php echo esc_attr( implode( ' ', $classes ) ); ?>">
          <?php
            /**
             * Provide these variables to the partial:
             *   $hall_svg, $stands, $prev_hall_slug, $next_hall_slug, $current_slug, $current_hall_label
             */
            require __DIR__ . '/partials/hall-template.php';
          ?>
        </section>
      </div>

    </div>
  </div>
</div>