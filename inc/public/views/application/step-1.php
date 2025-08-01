<?php
/**
 * Step 2: Advertisements with Tabs & limited picks per category.
 *
 * Requirements Recap:
 *  - 4 categories: catalogue, billboard, flag, digital
 *  - "catalogue","billboard","flag" => up to 1 item each
 *  - "digital" => up to 5 items
 *  - Show products in 4 TABS, each tab = one category
 *  - Each product card has:
 *      * Feature image
 *      * Title, short desc
 *      * Price
 *      * 2 attributes: size, surface (if present)
 *      * Buttons: [View] -> opens modal with bigger info, [Select This Item]
 *  - A "Picked Items" area below that shows their selections, with Remove buttons
 *  - On final form submit, store picks in hidden JSON input => session
 */

if ( ! defined('ABSPATH') ) { exit; }
if ( ! class_exists('\WC_Product') ) {
    echo '<p>WooCommerce not available or not active.</p>';
    return;
}

// 1) Category definitions & pick limits
$ad_categories = [
    'catalogue' => [ 'label' => 'Catalogue',  'limit'=>1 ],
    'billboard' => [ 'label' => 'Billboard',  'limit'=>1 ],
    'flag'      => [ 'label' => 'Flag',       'limit'=>1 ],
    'digital'   => [ 'label' => 'Digital',    'limit'=>5 ],
];

// 2) Existing picks from session
$saved_ads = isset($_SESSION['wv_app_ads']) && is_array($_SESSION['wv_app_ads'])
    ? $_SESSION['wv_app_ads']
    : [];

// Ensure each category key exists in the array
foreach($ad_categories as $cat_slug=>$info) {
    if (!isset($saved_ads[$cat_slug])) {
        $saved_ads[$cat_slug] = [];
    }
}

// 3) Query WooCommerce products for each category
$category_products_map = [];
foreach ($ad_categories as $cat_slug => $cat_data) {
    $args = [
        'category' => [ $cat_slug ], // the category slug(s)
        'limit'    => -1,
        'status'   => 'publish',
        'type'     => [ 'simple' ], // or remove if you have variable, etc.
    ];
    $products = wc_get_products($args);
    $category_products_map[$cat_slug] = $products;
}

// 4) Print out step instructions
?>
<div class="container container-1200">
    <h2>Step 2: Advertisements</h2>

    <!-- Show the "X item(s) from X Section" messages -->
    <?php foreach ($ad_categories as $slug=>$info): ?>
        <p><strong><?php echo esc_html($info['limit']); ?> Item(s) from <?php echo esc_html($info['label']); ?> Section available</strong></p>
    <?php endforeach; ?>

    <p>
        You may select up to one item in Catalogue, Billboard, Flag,
        and up to 5 items in Digital. Choose them below:
    </p>

    <form method="post">
    <?php wp_nonce_field('wv_ex_app_step_2'); ?>

    <!-- We'll store picks as JSON here -->
    <input type="hidden" id="wv-picked-ads" name="wv_picked_ads_json" value="" />

    <!-- TABS NAV -->
    <div class="wv-ad-tabs-nav" style="margin-bottom: 16px;">
    <?php
    // Output a tab link for each category
    $is_first = true;
    foreach ($ad_categories as $cat_slug => $cat_data):
        $cat_label = $cat_data['label'];
        $active_class = $is_first ? 'active' : '';
        $is_first = false;
        ?>
        <button
            type="button"
            class="wv-ad-tab-link <?php echo esc_attr($active_class); ?>"
            data-tab="<?php echo esc_attr($cat_slug); ?>"
            style="margin-right:8px;padding:6px 12px;cursor:pointer;"
        >
            <?php echo esc_html($cat_label); ?>
        </button>
    <?php endforeach; ?>
    </div>

    <!-- TABS CONTENT -->
    <div class="wv-ad-tabs-content">
        <?php
        // For each category => a tab panel with .wv-ad-tab-content
        $is_first2 = true;
        foreach ($ad_categories as $cat_slug => $cat_data):
            $cat_label = $cat_data['label'];
            $products  = $category_products_map[$cat_slug];
            $div_class = $is_first2 ? 'wv-ad-tab-content active' : 'wv-ad-tab-content';
            $is_first2 = false;
            ?>
            <div
                id="wv-tab-<?php echo esc_attr($cat_slug); ?>"
                class="<?php echo esc_attr($div_class); ?>"
                style="display: <?php echo ($div_class==='wv-ad-tab-content active' ? 'block' : 'none'); ?>;"
            >
                <h3><?php echo esc_html($cat_label); ?></h3>
                <?php if ( empty($products) ): ?>
                    <p>No products found in this category.</p>
                <?php else: ?>
                    <!-- We'll do a grid layout for the products -->
                    <div class="wv-ads-grid" style="display:flex;flex-wrap:wrap;gap:16px;">
                    <?php foreach ($products as $p):
                        /** @var \WC_Product $p */
                        $pid     = $p->get_id();
                        $title   = $p->get_name();
                        // Price (HTML) could show currency & sale price
                        $price   = $p->get_price_html(); 
                        $desc    = $p->get_short_description();
                        $img_id  = $p->get_image_id();
                        $img_url = $img_id ? wp_get_attachment_image_url($img_id, 'medium') : wc_placeholder_img_src();

                        // fetch "Size" and "Surface" from attributes
                        $size = $p->get_attribute('pa_size');     // or your attribute slug
                        $surf = $p->get_attribute('pa_surface');
                        ?>
                        <div
                            class="wv-ads-card"
                            data-pid="<?php echo esc_attr($pid); ?>"
                            data-category="<?php echo esc_attr($cat_slug); ?>"
                            style="border:1px solid #ccc;padding:8px;width:250px;position:relative;"
                        >
                            <img
                                src="<?php echo esc_url($img_url); ?>"
                                alt=""
                                style="width:100%;height:auto;"
                            />
                            <h4 style="margin:8px 0;"><?php echo esc_html($title); ?></h4>
                            <p style="font-size:0.9em;margin-bottom:4px;">
                                <?php echo wp_kses_post($desc); ?>
                            </p>
                            <p style="margin-bottom:4px;">
                                <strong>Price:</strong> <?php echo $price; ?>
                            </p>
                            <?php if ($size || $surf): ?>
                            <p style="font-size:0.8em;">
                                <?php if($size): ?>
                                  <strong>Size:</strong> <?php echo esc_html($size); ?><br/>
                                <?php endif; ?>
                                <?php if($surf): ?>
                                  <strong>Surface:</strong> <?php echo esc_html($surf); ?>
                                <?php endif; ?>
                            </p>
                            <?php endif; ?>

                            <!-- View & Select Buttons -->
                            <button
                                type="button"
                                class="wv-ads-view-btn"
                                style="background:#0073aa;color:#fff;padding:6px 8px;margin-top:6px;border:none;cursor:pointer;"
                            >
                                View
                            </button>
                            <button
                                type="button"
                                class="wv-ads-select-btn"
                                data-pid="<?php echo esc_attr($pid); ?>"
                                data-category="<?php echo esc_attr($cat_slug); ?>"
                                style="background:#28a745;color:#fff;padding:6px 8px;margin-top:6px;border:none;cursor:pointer;"
                            >
                                Select This Item
                            </button>
                        </div>
                    <?php endforeach; ?>
                    </div><!-- .wv-ads-grid -->
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div><!-- .wv-ad-tabs-content -->

    <br/>
    <h3>Picked Items</h3>
    <div id="wv-picked-list" style="border:1px solid #999;padding:8px;min-height:50px;">
        <!-- We'll display selected items here with remove btn -->
    </div>

    <div class="wv-form-nav" style="margin-top:16px;">
        <button type="submit" name="navigation" value="prev">← Back</button>
        <button type="submit" name="navigation" value="next">Next →</button>
    </div>
    </form>
</div><!-- .container container-1200 -->

<!-- "View" Modal (read-only) -->
<div
    id="wv-view-modal"
    style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.4);z-index:999;"
>
  <div style="background:#fff;width:600px;max-width:90%;margin:50px auto;padding:16px;position:relative;">
    <button
        type="button"
        id="wv-modal-close-btn"
        style="position:absolute;top:8px;right:8px;"
    >X</button>

    <div id="wv-modal-content" style="display:flex;gap:16px;">
      <img
        id="wv-modal-img"
        src=""
        alt=""
        style="width:300px;height:auto;"
      />
      <div style="flex:1;">
        <h4 id="wv-modal-title"></h4>
        <p id="wv-modal-desc"></p>
        <p><strong>Price:</strong> <span id="wv-modal-price"></span></p>
        <div id="wv-modal-attrs" style="font-size:0.9em;"></div>
      </div>
    </div>
  </div>
</div>

<script>
(function($){
  'use strict';

  // 1) Category limits => JS object
  const catLimits = {
    <?php
    // e.g. "catalogue":1, "billboard":1, "flag":1, "digital":5
    foreach($ad_categories as $slug => $info){
      echo "'$slug': " . intval($info['limit']) . ", ";
    }
    ?>
  };

  // 2) We read picks from session (JSON-encoded by PHP).
  let picks = <?php echo json_encode($saved_ads); ?>;

  $(document).ready(function(){

    // A) Tab switching
    // each .wv-ad-tab-link => show/hide .wv-ad-tab-content
    $('.wv-ad-tab-link').on('click', function(e){
      e.preventDefault();
      // remove active from all links
      $('.wv-ad-tab-link').removeClass('active');
      // remove active from all contents, hide them
      $('.wv-ad-tab-content').removeClass('active').hide();

      // add active to this link
      $(this).addClass('active');
      const slug = $(this).data('tab');
      // show the content
      const $panel = $('#wv-tab-'+slug);
      $panel.addClass('active').show();
    });

    // B) Render any existing picks from session into #wv-picked-list
    renderPickedList();

    // C) "View" button => open modal
    $('.wv-ads-view-btn').on('click', function(){
      const $card = $(this).closest('.wv-ads-card');
      const pid   = $card.data('pid');
      const cat   = $card.data('category');

      // gather info from the card
      const imgUrl = $card.find('img').attr('src');
      const title  = $card.find('h4').text();
      // first <p> is the short desc
      const desc   = $card.find('p').first().html();
      const price  = $card.find('strong:contains("Price")').parent().text().replace('Price:','').trim();

      // parse "Size" & "Surface" from the second <p> if present
      let size    = '';
      let surface = '';
      $card.find('p').each(function(){
        const txt = $(this).text();
        if(txt.includes('Size:')){
          size = txt.split('Size:')[1].split('\n')[0].trim();
        }
        if(txt.includes('Surface:')){
          surface = txt.split('Surface:')[1].trim();
        }
      });

      // fill modal
      $('#wv-modal-img').attr('src', imgUrl);
      $('#wv-modal-title').text(title);
      $('#wv-modal-desc').html(desc);
      $('#wv-modal-price').text(price);

      let attrHtml = '';
      if(size){
        attrHtml += '<p><strong>Size:</strong> '+size+'</p>';
      }
      if(surface){
        attrHtml += '<p><strong>Surface:</strong> '+surface+'</p>';
      }
      $('#wv-modal-attrs').html(attrHtml);

      // show modal
      $('#wv-view-modal').show();
    });

    // D) Close modal
    $('#wv-modal-close-btn').on('click', function(){
      $('#wv-view-modal').hide();
    });

    // E) "Select This Item" => .wv-ads-select-btn
    $('.wv-ads-select-btn').on('click', function(){
      const pid = $(this).data('pid');
      const cat = $(this).data('category');

      if(!catLimits[cat]){
        alert('Unknown category: '+cat);
        return;
      }

      let arr = picks[cat] || [];
      // if already in picks => skip
      if(arr.includes(pid)){
        alert('You already selected this product in '+cat);
        return;
      }

      // check limit
      if(cat === 'digital'){
        if(arr.length >= catLimits[cat]){
          alert('You can select up to '+catLimits[cat]+' in '+cat);
          return;
        }
      } else {
        // single item only
        if(arr.length >= catLimits[cat]){
          alert('You can only select '+catLimits[cat]+' item in '+cat);
          return;
        }
      }

      // add pid
      arr.push(pid);
      picks[cat] = arr;
      // re-render
      renderPickedList();
    });

    // F) Removing a picked item => .wv-remove-picked
    $(document).on('click', '.wv-remove-picked', function(){
      const pid = $(this).data('pid');
      const cat = $(this).data('cat');
      let arr = picks[cat] || [];
      const idx = arr.indexOf(pid);
      if(idx > -1){
        arr.splice(idx,1);
        picks[cat] = arr;
        renderPickedList();
      }
    });

    // G) On form submit => store picks in hidden input as JSON
    $('form').on('submit', function(){
      const jsonStr = JSON.stringify(picks);
      $('#wv-picked-ads').val(jsonStr);
      return true;
    });

  }); // end doc ready

  function renderPickedList(){
    let html = '';
    // for each cat in picks
    Object.keys(picks).forEach(cat => {
      const arr = picks[cat];
      if(!arr || arr.length === 0) return;
      html += '<h4>'+cat+'</h4><ul>';
      arr.forEach(pid => {
        html += '<li>Product ID '+pid+' <button type="button" class="wv-remove-picked" data-cat="'+cat+'" data-pid="'+pid+'">Remove</button></li>';
      });
      html += '</ul>';
    });
    if(!html) html = '<p>No items selected yet.</p>';
    $('#wv-picked-list').html(html);
  }
})(jQuery);
</script>
