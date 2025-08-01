<?php
/**
 * Renders a single hall’s SVG + stands listing + navigation.
 *
 * Available variables (set by ds-halls.php or AJAX) in this scope:
 *
 *   $hall_svg         => string, the raw <svg>…</svg> markup for the current hall
 *   $stands           => array of stands for this hall
 *   $current_slug     => string, e.g. "3" or "3A"
 *   $prev_hall_slug   => string, e.g. "2B"
 *   $next_hall_slug   => string, e.g. "4A"
 *   $current_hall_label => string, e.g. "Hall 3"
 */

// If you have any “active stands” to highlight:
$active_stands = [];

// Optionally inject “active” class into <g> tags in the SVG if needed
if ( ! empty( $active_stands ) ) {
    foreach ( $active_stands as $stand_id ) {
        $pattern     = '/(<g\b[^>]*\bid="' . preg_quote( $stand_id, '/' ) . '"[^>]*class=")([^"]*)(")/';
        $replacement = '$1$2 active$3';
        $hall_svg    = preg_replace( $pattern, $replacement, $hall_svg );
    }
}
?>

<div class="wv-bg-w wv-section-box-shadow">
  <!-- Hall SVG -->
  <div class="hall hall-svg-container mb-16">
    <div class="container container-1024 pb-24 d-flex justify-content-center align-items-center">
      <div class="svg-wrap"> 
        <?php
          // Output the entire SVG markup previously loaded via file_get_contents()
          echo $hall_svg;
        ?>
       </div>
    </div>
  </div>

  <!-- Next/Prev Navigation (AJAX-ready) -->
<div class="container-fluid border-top py-12">
  <div class="wv-hall-nav-bottom position-relative d-flex justify-content-center align-items-center">

    <!-- Previous button (AJAX) -->
    <button
      type="button"
      class="wv-hall-nav-button wv-icon-button fs-30 wv-icon-button-prev"
      data-action="prev"
      data-href="<?php echo esc_url( '?hall=' . $prev_hall_slug ); ?>"
      aria-label="Previous Hall"
    >
      <span class="wv wv_point-50-f rotate-180"></span>
    </button>

    <!-- Fallback for no-JS: a plain link inside <noscript> -->
    <noscript>
      <a
        class="wv-hall-nav-button wv-icon-button fs-30 wv-icon-button-prev"
        href="<?php echo esc_url( '?hall=' . $prev_hall_slug ); ?>"
        aria-label="Previous Hall"
      >
        <span class="wv wv_point-50-f rotate-180"></span>
      </a>
    </noscript>

    <!-- Hall label -->
    <div class="hall-label d-inline-block text-center px-32">
      <?php if ( ! empty( $current_hall_label ) ) : ?>
        <h4 class="my-0 h5"><?php echo esc_html( $current_hall_label ); ?></h4>
      <?php else : ?>
        <h4 class="my-0 h5">Hall <?php echo esc_html( $current_slug ); ?></h4>
      <?php endif; ?>
    </div>

    <!-- Next button (AJAX) -->
    <button
      type="button"
      class="wv-hall-nav-button wv-icon-button fs-30 wv-icon-button-next"
      data-action="next"
      data-href="<?php echo esc_url( '?hall=' . $next_hall_slug ); ?>"
      aria-label="Next Hall"
    >
      <span class="wv wv_point-50-f"></span>
    </button>

    <!-- Fallback for no-JS: a plain link inside <noscript> -->
    <noscript>
      <a
        class="wv-hall-nav-button wv-icon-button fs-30 wv-icon-button-next"
        href="<?php echo esc_url( '?hall=' . $next_hall_slug ); ?>"
        aria-label="Next Hall"
      >
        <span class="wv wv_point-50-f"></span>
      </a>
    </noscript>

  </div>
</div>


</div>

<!-- Stands Listing -->
<div class="py-48 wb-bg-c_10">
  <div class="container container-1024">
    <div class="stands-list-container">
      <?php
        $total   = count( $stands );
        $per_col = $total ? (int) ceil( $total / 3 ) : 1;
        $columns = array_chunk( $stands, $per_col );
      ?>
      <div class="row g-4">
        <?php foreach ( $columns as $col ) : ?>
          <div class="col-md-4">
            <?php foreach ( $col as $item ) :
              $sid    = $item['id']    ?? '';
              $number = $item['stand'] ?? '';
              $label  = $item['label'] ?? '';
            ?>
              <div
                class="stand-item wv-stand-item-list py-2"
                data-stand-id="<?php echo esc_attr($sid); ?>"
                <?php if (isset($item['product_id'])): ?>
                  data-product-id="<?php echo esc_attr($item['product_id']); ?>"
                <?php endif; ?>
              >
                <strong class="me-12"><?php echo esc_html($number); ?></strong>
                <?php echo $label ? ' ' . esc_html($label) : ''; ?>
              </div>


            <?php endforeach; ?>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</div>



<div id="stand-cart-container">
    <?php \Desymphony\Theme\DS_Enqueue::ds_render_fair_stands_cart(); ?>
</div>
