<?php
/**
 * "Saved" dashboard partial, showing Exhibitors, Buyers, and Products in 4 categories (Wine, Rakija, Food, Other).
 *
 * Each tab is a grid of "cards." For users: 
 *  - Profile image (wv_user_profilePhoto or placeholder)
 *  - Company name or "FirstName LastName"
 *  - Link to #, plus "Remove" button
 * For products:
 *  - Product image
 *  - Title
 *  - "View" -> opens a modal for more details
 *  - "Remove" button
 */

use Desymphony\Helpers\DS_Utils;
use Desymphony\Favorites\DS_Favorites_Manager;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// 1) Make sure user is logged in
$current_user_id = get_current_user_id();
if ( ! $current_user_id ) {
    echo '<p>You must be logged in to view favorites.</p>';
    return;
}

// 2) Fetch favorites from DB
$all_favs = DS_Favorites_Manager::get_user_favorites( $current_user_id );

// We'll separate them by role (exhibitor/buyer) or product type
$exhibitors = [];
$buyers     = [];
$products   = [
    'wine'   => [],
    'rakija' => [],
    'food'   => [],
    'other'  => [],
];

// We'll do a DB query for product info. You have wv_products table with "type".
global $wpdb;
$table_products = $wpdb->prefix . 'wv_products';

// 3) Partition the favorites
foreach ( $all_favs as $fav ) {
    if ( $fav->target_type === 'user' ) {
        $u = get_userdata( $fav->target_id );
        if ( $u ) {
            $roles = (array) $u->roles;
            $role  = reset($roles) ?: '';
            if ( $role === 'exhibitor' ) {
                $exhibitors[] = $u;
            } elseif ( $role === 'buyer' ) {
                $buyers[] = $u;
            }
        }
    }
    elseif ( $fav->target_type === 'product' ) {
        // Fetch real "type" from wv_products table
        $row = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM $table_products WHERE id=%d", $fav->target_id)
        );
        if ( $row ) {
            // If "type" is one of [wine,rakija,food], else "other"
            $ptype = in_array($row->type, ['wine','rakija','food']) ? $row->type : 'other';
            $products[$ptype][] = $row; // store the entire DB row for usage
        }
    }
}

// 4) We'll pass counts to subnav-saved. So define them now:
$ex_count    = count($exhibitors);
$by_count    = count($buyers);
$wine_count  = count($products['wine']);
$rak_count   = count($products['rakija']);
$food_count  = count($products['food']);
$oth_count   = count($products['other']);

// We'll store them in a global array so the subnav partial can read them
$GLOBALS['wv_saved_counts'] = [
    'exhibitors' => $ex_count,
    'buyers'     => $by_count,
    'wine'       => $wine_count,
    'rakija'     => $rak_count,
    'food'       => $food_count,
    'other'      => $oth_count,
];

// 5) We'll create a helper to get user "company name or 'First Last'".
function wv_get_user_display_name( \WP_User $u ) {
    $cid = get_user_meta( $u->ID, 'wv_company_name', true );
    if ( $cid ) {
        return $cid;
    }
    // else fallback
    $first = get_user_meta($u->ID, 'first_name', true );
    $last  = get_user_meta($u->ID, 'last_name',  true );
    $combined = trim("$first $last");
    return $combined ?: $u->display_name; // ultimate fallback
}

// 6) Now we output the actual HTML for the 6 tabs: Exhibitors, Buyers, Wine, Rakija, Food, Other
// We'll do a grid approach for each tab
?>

<div class="px-0 py-32" data-nonce="<?php echo esc_attr( wp_create_nonce('wv_favorite_nonce') ); ?>">

  <!-- Exhibitors tab content -->
  <div class="wv-tab-content active" id="wv-saved-exhibitors" style="display:block;">

    <div class="wv-container-1024 px-0">
        <div class="row pb-32">

            <?php if ( empty($exhibitors) ): ?>
                <div class="col-12">
                    <p>No exhibitors saved.</p>
                </div>
            <?php else: ?>
                <?php foreach ( $exhibitors as $ex ): 
                    $uid = $ex->ID;
                    $photo = get_user_meta($uid, 'wv_user_profilePhoto', true );
                    $wv_userCategory = get_user_meta($uid, 'wv_userCategory', true );
                    $wv_company_country = get_user_meta($uid, 'wv_company_country', true );

                    if ( ! $photo ) {
                        $photo = 'https://placehold.co/120'; // fallback
                    }
                    $dname = wv_get_user_display_name($ex);
                    ?>
                    
                    <div class="col-12 my-8">
                        <div class="wv-saved-card d-flex wv-100 br-12 wv-shadow-sm wv-position-relative p-24 wv-bg-w" data-id="<?php echo esc_attr( $uid ); ?>">
                            <div class="d-flex">
                                <div class="d-inline-block wv-position-relative wv-z-10 br-8 wv-bg-w">
                                    <img class="d-block br-8" src="<?php echo esc_url($photo); ?>" width="120" height="120" alt="">
                                </div>
                            </div>
                            <div class="ps-16 wv-w-100 d-flex wv-flex-column wv-justify-between">
                                <div class="d-flex wv-align-start wv-justify-between wv-w-100 pb-8">
                                    <div class="fs-32 wv-fw-500 wv-lh-1-2"><?php echo esc_html($dname); ?></div>
                                    <div class="d-flex">
                                        <button class="wv-remove-favorite-btn wv-button wv-button-badge wv-button-light-danger br-4" data-target-type="user"
                                        data-target-id="<?php echo esc_attr( $uid ); ?>"><i class="fas fa-times"></i></button>
                                    </div>
                                </div>
                                <div class="d-block wv-w-100 pt-8">
                                    <div class="row">
                                        <div class="col-lg-6 my-0">                                                
                                            <div class="fs-16 wv-ls-1 wv-text-uppercase wv-lh-1-5 wv-fw-300 color-c_50"><?php echo esc_html($wv_userCategory); ?></div>
                                            <div class="fs-14 wv-lh-1-5 wv-fw-500 color-c_50"><?php echo esc_html($wv_company_country); ?></div>
                                        </div>
                                        <div class="col-lg-6 my-0 d-flex wv-align-end wv-justify-end">
                                            <div class="color-c wv-text-uppercase d-flex align-items-center fs-14 wv-lh-1-5 wv-fw-500 wv-ls-1 me-16">
                                                <i class="fas fa-calendar-check me-4"></i> REQUESTED MEETING
                                            </div>
                                            <div class="color-c wv-text-uppercase d-flex align-items-center fs-14 wv-lh-1-5 wv-fw-500 wv-ls-1">
                                                <i class="fas fa-map-marker me-4"></i> 2A/22-24
                                            </div>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>                        
                    </div>



                <?php endforeach; ?>
            <?php endif; ?>

        </div><!-- .row -->
    </div><!-- .wv-container-1024 -->
  </div><!-- #wv-saved-exhibitors -->

  <!-- Buyers tab content -->
  <div class="wv-tab-content" id="wv-saved-buyers" style="display:none;">
  
    <div class="wv-container-1024 px-0">
        <div class="row pb-32">

            <?php if ( empty($buyers) ): ?>
                <div class="col-12">
                    <p>No buyers saved.</p>
                </div>
            <?php else: ?>
                <?php foreach ( $buyers as $b ): 
                    $uid = $b->ID;
                    $photo = get_user_meta($uid, 'wv_user_profilePhoto', true );
                    $wv_userCategory = get_user_meta($uid, 'wv_userCategory', true );
                    $wv_company_country = get_user_meta($uid, 'wv_company_country', true );

                    if ( ! $photo ) {
                        $photo = 'https://placehold.co/120'; // fallback
                    }
                    $dname = wv_get_user_display_name($b);
                    ?>
                    
                    <div class="col-12 my-8">
                        <div class="wv-saved-card d-flex wv-100 br-12 wv-shadow-sm wv-position-relative p-24 wv-bg-w" data-id="<?php echo esc_attr( $uid ); ?>">
                            <div class="d-flex">
                                <div class="d-inline-block wv-position-relative wv-z-10 br-8 wv-bg-w">
                                    <img class="d-block br-8" src="<?php echo esc_url($photo); ?>" width="120" height="120" alt="">
                                </div>
                            </div>
                            <div class="ps-16 wv-w-100 d-flex wv-flex-column wv-justify-between">
                                <div class="d-flex wv-align-start wv-justify-between wv-w-100 pb-8">
                                    <div class="fs-32 wv-fw-500 wv-lh-1-2"><?php echo esc_html($dname); ?></div>
                                    <div class="d-flex">
                                        <button class="wv-remove-favorite-btn wv-button wv-button-badge wv-button-light-danger br-4" data-target-type="user"
                                        data-target-id="<?php echo esc_attr( $uid ); ?>"><i class="fas fa-times"></i></button>
                                    </div>
                                </div>
                                <div class="d-block wv-w-100 pt-8">
                                    <div class="row">
                                        <div class="col-lg-6 my-0">                                                
                                            <div class="fs-16 wv-ls-1 wv-text-uppercase wv-lh-1-5 wv-fw-300 color-c_50"><?php echo esc_html($wv_userCategory); ?></div>
                                            <div class="fs-14 wv-lh-1-5 wv-fw-500 color-c_50"><?php echo esc_html($wv_company_country); ?></div>
                                        </div>
                                        <div class="col-lg-6 my-0 d-flex wv-align-end wv-justify-end">
                                            <div class="color-c wv-text-uppercase d-flex align-items-center fs-14 wv-lh-1-5 wv-fw-500 wv-ls-1">
                                                <i class="fas fa-calendar-check me-4"></i> REQUESTED MEETING
                                            </div>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>                        
                    </div>



                <?php endforeach; ?>
            <?php endif; ?>

        </div><!-- .row -->
    </div><!-- .wv-container-1024 -->

  </div><!-- #wv-saved-buyers -->

  <!-- Wine products -->
  <div class="wv-tab-content" id="wv-saved-wine" style="display:none;">

    <div class="wv-container-1400 px-0">
        <?php if ( empty($products['wine']) ): ?>
            <div class="row">
                <div class="col-12">
                    <p>No wine products saved.</p>
                </div>
            </div>
        <?php else: ?>
            <div class="d-grid wv-gap-12 wv-grid-responsive-6" >
            <?php for ($i = 0; $i < 1; $i++): ?>
                <?php foreach ( $products['wine'] as $p ): 
                    // $p is a DB row from wv_products
                    $pid    = $p->id;
                    $title  = $p->title;
                    $img    = $p->image_url ?: 'https://placehold.co/360x640';
                    ?>
                    <div class="wv-saved-card product-card d-block"

                        data-pid="<?php echo esc_attr($pid); ?>"
                        data-title="<?php echo esc_attr($title); ?>"
                        data-category="<?php echo esc_attr($p->type); ?>"
                        data-variety="<?php echo esc_attr($p->variety); ?>"
                        data-region="<?php echo esc_attr($p->region); ?>"
                        data-year="<?php echo esc_attr($p->year); ?>"
                        data-volume="<?php echo esc_attr($p->volume_ml); ?>"
                        data-alcohol="<?php echo esc_attr($p->alcohol_pct); ?>"
                        data-sugar="<?php echo esc_attr($p->sugar_pct); ?>"
                        data-annual="<?php echo esc_attr($p->annual_production_l); ?>"
                        data-current-stock="<?php echo esc_attr($p->current_stock_l); ?>"
                        data-bneck="<?php echo esc_attr($p->bottle_neck); ?>"
                    >
                        <div class="d-block br-8 wv-bg-c wv-position-relative wv-z-10 wv-shadow-sm">
                            <a href="#" class="product-view-btn">
                                <img src="<?php echo esc_url($img); ?>" alt="" width="360" height="640" class="br-8" />
                            </a>
                            <button class="wv-remove-favorite-btn wv-button wv-button-badge wv-button-light-danger br-4 wv-position-absolute wv-top-12 wv-right-12" data-target-type="user"
                                            data-target-id="<?php echo esc_attr( $uid ); ?>"><i class="fas fa-times"></i></button>
                        </div>
                        <div class="d-block">                        
                            <h4 class="my-0 py-8"><?php echo esc_html($title); ?></h4>
                        </div>
                        
                    </div>
                <?php endforeach; ?>
            <?php endfor; ?>
            </div>
        <?php endif; ?>
    </div><!-- .wv-container-1024 -->
  </div><!-- #wv-saved-wine -->

  <!-- Rakija products -->
  <div class="wv-tab-content" id="wv-saved-rakija" style="display:none;">
    <?php if ( empty($products['rakija']) ): ?>
        <p>No rakija products saved.</p>
    <?php else: ?>
        <div style="display:flex;flex-wrap:wrap;gap:16px;">
        <?php foreach ( $products['rakija'] as $p ): 
            $pid    = $p->id;
            $title  = $p->title;
            $img    = $p->image_url ?: 'https://via.placeholder.com/200';
            ?>
            <div class="wv-saved-card product-card"
                 style="border:1px solid #ccc;padding:8px;width:240px;position:relative;"
                 data-pid="<?php echo esc_attr($pid); ?>"
                 data-title="<?php echo esc_attr($title); ?>"
                 data-category="<?php echo esc_attr($p->type); ?>"
                 data-variety="<?php echo esc_attr($p->variety); ?>"
                 data-region="<?php echo esc_attr($p->region); ?>"
                 data-year="<?php echo esc_attr($p->year); ?>"
                 data-volume="<?php echo esc_attr($p->volume_ml); ?>"
                 data-alcohol="<?php echo esc_attr($p->alcohol_pct); ?>"
                 data-sugar="<?php echo esc_attr($p->sugar_pct); ?>"
                 data-annual="<?php echo esc_attr($p->annual_production_l); ?>"
                 data-current-stock="<?php echo esc_attr($p->current_stock_l); ?>"
                 data-bneck="<?php echo esc_attr($p->bottle_neck); ?>"
            >
                <img src="<?php echo esc_url($img); ?>" alt="" style="width:100%;height:auto;" />
                <h4 style="margin:8px 0;"><?php echo esc_html($title); ?></h4>
                <button
                    type="button"
                    class="product-view-btn"
                    style="background:#0073aa;color:#fff;border:none;padding:6px 10px;cursor:pointer;"
                >
                    View
                </button>
                <button
                    class="wv-remove-favorite-btn"
                    data-target-type="product"
                    data-target-id="<?php echo esc_attr($pid); ?>"
                    style="position:absolute;top:8px;right:8px;background:#ff4545;color:#fff;border:none;padding:4px 6px;cursor:pointer;"
                >
                    X
                </button>
            </div>
        <?php endforeach; ?>
        </div>
    <?php endif; ?>
  </div><!-- #wv-saved-rakija -->

  <!-- Food products -->
  <div class="wv-tab-content" id="wv-saved-food" style="display:none;">
    <?php if ( empty($products['food']) ): ?>
        <p>No food products saved.</p>
    <?php else: ?>
        <div style="display:flex;flex-wrap:wrap;gap:16px;">
        <?php foreach ( $products['food'] as $p ): 
            $pid    = $p->id;
            $title  = $p->title;
            $img    = $p->image_url ?: 'https://via.placeholder.com/200';
            ?>
            <div class="wv-saved-card product-card"
                 style="border:1px solid #ccc;padding:8px;width:240px;position:relative;"
                 data-pid="<?php echo esc_attr($pid); ?>"
                 data-title="<?php echo esc_attr($title); ?>"
                 data-category="<?php echo esc_attr($p->type); ?>"
                 data-variety="<?php echo esc_attr($p->variety); ?>"
                 data-region="<?php echo esc_attr($p->region); ?>"
                 data-year="<?php echo esc_attr($p->year); ?>"
                 data-volume="<?php echo esc_attr($p->volume_ml); ?>"
                 data-alcohol="<?php echo esc_attr($p->alcohol_pct); ?>"
                 data-sugar="<?php echo esc_attr($p->sugar_pct); ?>"
                 data-annual="<?php echo esc_attr($p->annual_production_l); ?>"
                 data-current-stock="<?php echo esc_attr($p->current_stock_l); ?>"
                 data-bneck="<?php echo esc_attr($p->bottle_neck); ?>"
            >
                <img src="<?php echo esc_url($img); ?>" alt="" style="width:100%;height:auto;" />
                <h4 style="margin:8px 0;"><?php echo esc_html($title); ?></h4>
                <button
                    type="button"
                    class="product-view-btn"
                    style="background:#0073aa;color:#fff;border:none;padding:6px 10px;cursor:pointer;"
                >
                    View
                </button>
                <button
                    class="wv-remove-favorite-btn"
                    data-target-type="product"
                    data-target-id="<?php echo esc_attr($pid); ?>"
                    style="position:absolute;top:8px;right:8px;background:#ff4545;color:#fff;border:none;padding:4px 6px;cursor:pointer;"
                >
                    X
                </button>
            </div>
        <?php endforeach; ?>
        </div>
    <?php endif; ?>
  </div><!-- #wv-saved-food -->

  <!-- Other products -->
  <div class="wv-tab-content" id="wv-saved-other" style="display:none;">
    <?php if ( empty($products['other']) ): ?>
        <p>No other products saved.</p>
    <?php else: ?>
        <div style="display:flex;flex-wrap:wrap;gap:16px;">
        <?php foreach ( $products['other'] as $p ): 
            $pid    = $p->id;
            $title  = $p->title;
            $img    = $p->image_url ?: 'https://via.placeholder.com/200';
            ?>
            <div class="wv-saved-card product-card"
                 style="border:1px solid #ccc;padding:8px;width:240px;position:relative;"
                 data-pid="<?php echo esc_attr($pid); ?>"
                 data-title="<?php echo esc_attr($title); ?>"
                 data-category="<?php echo esc_attr($p->type); ?>"
                 data-variety="<?php echo esc_attr($p->variety); ?>"
                 data-region="<?php echo esc_attr($p->region); ?>"
                 data-year="<?php echo esc_attr($p->year); ?>"
                 data-volume="<?php echo esc_attr($p->volume_ml); ?>"
                 data-alcohol="<?php echo esc_attr($p->alcohol_pct); ?>"
                 data-sugar="<?php echo esc_attr($p->sugar_pct); ?>"
                 data-annual="<?php echo esc_attr($p->annual_production_l); ?>"
                 data-current-stock="<?php echo esc_attr($p->current_stock_l); ?>"
                 data-bneck="<?php echo esc_attr($p->bottle_neck); ?>"
            >
                <img src="<?php echo esc_url($img); ?>" alt="" style="width:100%;height:auto;" />
                <h4 style="margin:8px 0;"><?php echo esc_html($title); ?></h4>
                <button
                    type="button"
                    class="product-view-btn"
                    style="background:#0073aa;color:#fff;border:none;padding:6px 10px;cursor:pointer;"
                >
                    View
                </button>
                <button
                    class="wv-remove-favorite-btn"
                    data-target-type="product"
                    data-target-id="<?php echo esc_attr($pid); ?>"
                    style="position:absolute;top:8px;right:8px;background:#ff4545;color:#fff;border:none;padding:4px 6px;cursor:pointer;"
                >
                    X
                </button>
            </div>
        <?php endforeach; ?>
        </div>
    <?php endif; ?>
  </div><!-- #wv-saved-other -->

  <!-- The product View Modal -->
  <div id="product-modal" class="wv-modal" style="display:none;">
    <div class="wv-modal-backdrop"></div>
    <div class="wv-modal-content" style="background:#fff;width:600px;max-width:90%;padding:16px;">
      <button type="button" class="wv-modal-close" style="float:right;">X</button>
      <div style="display:flex;gap:16px;">
        <img id="wv-pm-img" src="" alt="" style="width:300px;height:auto;" />
        <div style="flex:1;">
          <h3 id="wv-pm-title"></h3>
          <ul id="wv-pm-details" style="list-style:none;padding-left:0;margin-top:8px;">
            <!-- We'll fill these details via JS -->
          </ul>
        </div>
      </div>
    </div>
  </div><!-- #product-modal -->

</div><!-- .wv-saved-items-wrapper -->

<script>
(function($){
  'use strict';

  // "View" product => open modal with details from data- attributes
  $(document).on('click', '.product-view-btn', function(e){
    e.preventDefault();
    const $card = $(this).closest('.product-card');
    const pid   = $card.data('pid');
    const title = $card.data('title') || '';
    const img   = $card.find('img').attr('src') || '';
    // gather extra data
    const category  = $card.data('category') || '';
    const variety   = $card.data('variety') || '';
    const region    = $card.data('region') || '';
    const year      = $card.data('year') || '';
    const volume    = $card.data('volume') || '';
    const alcohol   = $card.data('alcohol') || '';
    const sugar     = $card.data('sugar') || '';
    const annual    = $card.data('annual') || '';
    const cstock    = $card.data('current-stock') || '';
    const bneck     = $card.data('bneck') || '';

    // fill modal
    $('#wv-pm-img').attr('src', img);
    $('#wv-pm-title').text(title);

    let detailsHtml = '';
    detailsHtml += '<li><strong>Type:</strong> '+ category +'</li>';
    if(variety) detailsHtml += '<li><strong>Variety:</strong> '+ variety +'</li>';
    if(region)  detailsHtml += '<li><strong>Region:</strong> '+ region +'</li>';
    if(year)    detailsHtml += '<li><strong>Year:</strong> '+ year +'</li>';
    if(volume)  detailsHtml += '<li><strong>Volume (ml):</strong> '+ volume +'</li>';
    if(alcohol) detailsHtml += '<li><strong>Alcohol (%):</strong> '+ alcohol +'</li>';
    if(sugar)   detailsHtml += '<li><strong>Sugar (%):</strong> '+ sugar +'</li>';
    if(annual)  detailsHtml += '<li><strong>Annual Prod (L):</strong> '+ annual +'</li>';
    if(cstock)  detailsHtml += '<li><strong>Current Stock (L):</strong> '+ cstock +'</li>';
    if(bneck)   detailsHtml += '<li><strong>Bottle Neck:</strong> '+ bneck +'</li>';

    $('#wv-pm-details').html(detailsHtml);

    // show modal
    $('#product-modal').show();
  });

  // close modal
  $(document).on('click', '.wv-modal-close', function(){
    $('#product-modal').hide();
  });

})(jQuery);
</script>
