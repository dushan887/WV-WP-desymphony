<?php
/**
 * Template: ds-halls
 */
use Desymphony\Woo\DS_Woo_Stand_Map;

if (! defined('ABSPATH')) exit;

use Desymphony\Helpers\DS_Utils as Utils;


if ((Utils::get_exhibitor_participation() !== 'Head Exhibitor' && Utils::get_exhibitor_participation() !== 'Solo Exhibitor') || !Utils::is_admin_verified() || Utils::get_status() !== 'Pending' ) {
    wp_safe_redirect(home_url('/wv-dashboard/'));
    exit;
}
$user_id = get_current_user_id();

if ( !Utils::is_ex_stage1_verified() && !Utils::is_ex_stage2_verified() ) {

// 1) Load the master order of halls
$halls_order = require get_theme_file_path('inc/public/views/halls/halls-order.php');

// 2) Figure out which hall is requested (via ?hall=slug)
$current_slug = (isset($_GET['hall']) && in_array($_GET['hall'], $halls_order, true))
    ? sanitize_text_field($_GET['hall'])
    : ''; // If not provided or invalid, empty means "no hall selected"

?>

<style>
.ds-hall-counter.order-2 {order: 1 !important; }

<?php
if ( Utils::has_reserved_stand() ) {
  ?>
  .ds-stand:not(.wv-current-user) .cls-3 {
    pointer-events: none !important;
    fill: var(--wv-c_20) !important;
  }
  .ds-stand:not(.wv-current-user) .cls-1 {
    display: none !important
  }
  .hall-svg-container svg g.wv-stand-9m2 .cls-3 { fill: var(--wv-9m2_30); }
  .hall-svg-container svg g.wv-stand-12m2 .cls-3 { fill: var(--wv-12m2_30); }
  .hall-svg-container svg g.wv-stand-24m2 .cls-3 { fill: var(--wv-24m2_30); }
  .hall-svg-container svg g.wv-stand-49m2 .cls-3 { fill: var(--wv-49m2_30); }
  .hall-svg-container svg g.wv-stand-custom .cls-3 { fill: var(--wv-custom_30); }

  .hall-svg-container svg g.wv-stand-9m2 .cls-1,
  .hall-svg-container svg g.wv-stand-12m2 .cls-1,
  .hall-svg-container svg g.wv-stand-24m2 .cls-1,
  .hall-svg-container svg g.wv-stand-49m2 .cls-1,
  .hall-svg-container svg g.wv-stand-custom:not(:hover) .cls-1 { fill: var(--wv-c); }

  .hall-svg-container svg g.wv-stand-9m2:hover .cls-3 { fill: var(--wv-9m2); }
  .hall-svg-container svg g.wv-stand-12m2:hover .cls-3 { fill: var(--wv-12m2); }
  .hall-svg-container svg g.wv-stand-24m2:hover .cls-3 { fill: var(--wv-24m2); }
  .hall-svg-container svg g.wv-stand-49m2:hover .cls-3 { fill: var(--wv-49m2); }
  .hall-svg-container svg g.wv-stand-custom:hover .cls-3 { fill: var(--wv-custom); }  
  <?php }?>
  <?php if ( Utils::get_exhibitor_field() === 'Spirits') : ?>
    .hall-svg:not(#wv_hall_1G) .ds-stand .cls-3 {
      pointer-events: none !important;
      fill: var(--wv-c_20) !important;
    }
    .hall-svg:not(#wv_hall_1G) .ds-stand .cls-1 {
      display: none !important
    }
  <?php endif; ?>
  <?php if ( Utils::get_exhibitor_field() === 'Wine') : ?>
    #wv_hall_1G .ds-stand .cls-3,
    #wv_hall_4B .ds-stand .cls-3 {
      pointer-events: none !important;
      fill: var(--wv-c_20) !important;
    }
    #wv_hall_1G .ds-stand .cls-1,
    #wv_hall_4B .ds-stand .cls-1 {
      display: none !important
    }
  <?php endif; ?>
  <?php if ( Utils::get_exhibitor_field() === 'Food') : ?>
    .hall-svg:not(#wv_hall_4B) .ds-stand .cls-3 {
      pointer-events: none !important;
      fill: var(--wv-c_20) !important;
    }
    .hall-svg:not(#wv_hall_4B) .ds-stand .cls-1 {
      display: none !important
    }
  <?php endif; ?>
  
</style>
<div class="ds-124"></div>
<div id="wv-app-step-1" class="wv-app-form ds-hall-root ds-hall--cart">
  <?php 
  // We can still show the top nav if you want:
    require DS_THEME_DIR . '/inc/public/views/dashboard/partial/ds-stand-hall-nav.php'; 
    require DS_THEME_DIR . '/inc/public/views/dashboard/partial/ds-stand-prices.php'; 
    require DS_THEME_DIR . '/inc/public/views/dashboard/partial/ds-stand-modal-nav.php'; 
  ?>

  <?php if ($current_slug === ''): ?>
    <!-- No hall chosen, show a placeholder message or nothing -->
    <div id="hall-content"></div>
  <?php else: ?>
    <?php
      // 3) Load stands just for that hall
      $map_for_hall = DS_Woo_Stand_Map::get_map_for_hall($current_slug);
      $stands = $map_for_hall[$current_slug] ?? [];

      var_dump('<pre>', $stands, '</pre>');
      // 4) Next/prev
      $current_index = array_search($current_slug, $halls_order, true);
      $prev_index    = ($current_index > 0) ? $current_index - 1 : count($halls_order) - 1;
      $next_index    = ($current_index < count($halls_order) - 1) ? $current_index + 1 : 0;

      $prev_hall_slug = $halls_order[$prev_index];
      $next_hall_slug = $halls_order[$next_index];

      // 5) Load the relevant hall’s SVG
      $svg_file = get_theme_file_path("inc/public/views/halls/hall-{$current_slug}.svg");
      $hall_svg = file_exists($svg_file) ? file_get_contents($svg_file) : '';
      $current_hall_label = "Hall " . $current_slug;

    ?>
    
    
    <div id="hall-content" data-hall-slug="<?php echo esc_attr($current_slug); ?>">
        <?php require DS_THEME_DIR . '/inc/public/views/dashboard/partial/ds-hall-apply.php'; ?>
    </div>
  <?php endif; ?>
</div>
<div id="wv-single-stand-bar" class="py-24 wv-bg-w" style="display:none;">
    <div class="container container-1024">
        <div class="row">
            <div class="col-12 col-lg-4 text-center text-lg-start">
                <span id="wv-stand-info" class="fw-600 fs-16"></span> Selected
            </div>
            <div class="d-block pt-24 d-lg-none"></div>
            <div class="col-6 col-lg-4 text-end text-lg-center">
                <button id="wv-add-stand-btn" class="wv-button wv-button-pill wv-button-sm">Add Stand</button>
            </div>
            <div class="col-6 col-lg-4 text-start text-lg-end">
                <button id="wv-cancel-stand-btn" class="wv-button wv-button-pill wv-button-danger wv-button-sm">Cancel</button>
            </div>
        </div>
    </div>
</div>



<?php \Desymphony\Woo\DS_Woo_Stand_Cart::ds_render_fair_stands_cart(); ?>

<?php 
  // We can still show the top nav if you want:
require DS_THEME_DIR . '/inc/public/views/dashboard/partial/ds-stand-modal.php'; 
?>




<?php } else if (Utils::is_ex_stage1_verified() && ! Utils::is_ex_stage2_verified() && Utils::is_exhibiting_products() ) {?>  
  <div class="ds-124"></div> 
  <?php
  require DS_THEME_DIR . '/inc/public/views/partials/modules/dashboard-subnav-products.php';
  require DS_THEME_DIR . '/inc/public/views/dashboard/dashboard-products.php';
  ?>
  <section class="wv-bg-w py-24">    
    <div class="container">
      <div class="row">
        <div class="col-12 text-center">
           <a id="ds-finish-application"
                href="/wv-products/"
                class="wv-button wv-button-default wv-button-lg px-lg-64 py-lg-24"
                data-skip-clear>
                <?php esc_html_e( 'Finish and review products', DS_THEME_TEXTDOMAIN ); ?>
              </a>
        </div>
      </div>
    </div>
  </section>

    
<?php } else if (Utils::is_ex_stage1_verified() && ! Utils::is_ex_stage2_verified() && ! Utils::is_exhibiting_products() ) { ?>  
  
  <div id="wv-final-step" class="wv-auth-wrapper position-relative">
      <div class="wv-auth-form d-block">

        <div  class="wv-auth-container container container-768 my-48 mx-auto br-16 px-0" data-current-step="final">
          <div class="wv-step wv-step-terms" id="final">

            <!-- Step Header -->
            <div id="wv-step-header" class="px-16 py-24 text-center position-relative">
              <h6 class="my-0 text-uppercase ls-3 fw-600 wv-color-v_dark d-inline-flex align-items-center gap-4">
                <?php esc_html_e( 'COMPLETE', DS_THEME_TEXTDOMAIN ); ?>
                <span class="wv wv_check-12-sq fs-12 wv-i-gw"><span class="path1"></span><span class="path2"></span></span>
              </h6>
            </div>

            <!-- Step Body -->
            <div id="wv-step-body" class="position-relative py-48 wv-color-v_dark wv-reg-complete" style="padding-inline:0!important;min-height:100px">
              <div class="container container-1024 px-0 text-center">

                <h2 class="fs-30 fw-400 wv-color-v_dark mb-12">
                  <?php esc_html_e( 'Congratulations!', DS_THEME_TEXTDOMAIN ); ?>
                </h2>

                <h3 class="fs-24 fw-600 wv-color-v_dark mt-0 mb-24 px-lg-32">
                  <?php esc_html_e( 'You have successfully completed', DS_THEME_TEXTDOMAIN ); ?><br />
                  <?php esc_html_e( 'your 2025 Exhibitor Application Form.', DS_THEME_TEXTDOMAIN ); ?>
                </h3>

                <p class="wv-color-v_dark fs-18 ">
                  <span class="d-block fw-600"><?php esc_html_e( 'You are all set to exhibit at the 2025 Wine Vision by Open Balkan Fair!', DS_THEME_TEXTDOMAIN ); ?></span>
                  <?php esc_html_e( 'All the information from your completed application form is added to your profile.', DS_THEME_TEXTDOMAIN ); ?>
                </p>

              </div>
            </div>

            <!-- Step Footer -->
            <div id="wv-step-footer" class="wv-step-footer d-flex py-32 px-64 position-relative justify-content-center align-items-center">
              <a id="ds-finish-application"
                href="/wv-profile/"
                class="wv-button wv-button-default wv-button-lg px-lg-64 py-lg-24"
                data-skip-clear>
                <?php esc_html_e( 'Go to my profile', DS_THEME_TEXTDOMAIN ); ?>
              </a>
            </div>



        </div>

      </div>
    </div>      
  <?php
  
  ?>
<?php } else {
    // If the user is not an exhibitor or has not completed the verification steps, redirect to the dashboard

    ?> 
    <div class="ds-124"></div>  
    <div class="wv-button">Finish and review products</div> <?php
    // wp_safe_redirect(home_url('/wv-co-ex/'));
    // exit;

}

if ( Utils::get_exhibitor_field() === 'Spirits' ) : ?>
<script>
jQuery(document).ready(function($) {
  setTimeout(function() {
    var btn = $('#wv-nav-hall_1G');
    if (btn.length) {
      btn.click();
    }
  }, 1000);
});
</script>

<?php endif;