<?php
/**
 * Step 2: Advertisements in a Grid with limited picks per category.
 *
 * Requirements:
 *  - We have 4 categories: catalogue, billboard, flag, digital
 *  - "catalogue", "billboard", "flag" => max 1 item each
 *  - "digital" => max 5 items
 *
 * We'll display them in a grid. Each product card:
 *   - Feature image
 *   - Title
 *   - Short desc
 *   - 2 attributes: size, surface
 *   - Price
 *   - A "View" button => opens modal with bigger details & "Select"
 *
 * Below the grid, a "Picked Items" area showing chosen products from each category + remove.
 *
 * We'll store picks in the user's session ( $_SESSION['wv_app_ads'] ) but our approach is:
 *   1) We load any existing picks from the session to show them in "picked items"
 *   2) The user manipulates picks via JS on the front-end
 *   3) On final form submit, we store them (we do hidden input with the final picks).
 *
 * This is demonstration code. Tweak as needed.
 */

if ( ! defined('ABSPATH') ) { exit; }
if ( ! class_exists('\WC_Product') ) {
    echo '<p>WooCommerce not available.</p>';
    return;
}

// 1) The categories we want + their pick limits
$ad_categories = [
    'catalogue'  => [ 'label'=>'Catalogue',  'limit'=>1  ],
    'billboard'  => [ 'label'=>'Billboard',  'limit'=>1  ],
    'flag'       => [ 'label'=>'Flag',       'limit'=>1  ],
    'digital'    => [ 'label'=>'Digital',    'limit'=>5  ], // "media"
];

// 2) Get existing picks from session: structure => [ 'catalogue'=>[p1], 'billboard'=>[], 'flag'=>[], 'digital'=>[pX,pY], ... ]
$saved_ads = isset($_SESSION['wv_app_ads']) && is_array($_SESSION['wv_app_ads'])
    ? $_SESSION['wv_app_ads']
    : [];

// Ensure all categories exist in $saved_ads
foreach($ad_categories as $cat_slug=>$info) {
    if (!isset($saved_ads[$cat_slug])) {
        $saved_ads[$cat_slug] = [];
    }
}

// 3) Query each category's products. We'll do a single query for each slug. (If large store, optimize.)
$category_products_map = [];
foreach ($ad_categories as $cat_slug => $data) {
    $args = [
        'category' => [ $cat_slug ],
        'limit'    => -1,
        'status'   => 'publish',
        'type'     => [ 'simple' ], // or remove if you have variable, etc.
    ];
    $products = wc_get_products($args);
    $category_products_map[$cat_slug] = $products;
}
?>

<h2>Step 2: Advertisements</h2>

<!-- Show category-limits message above each category: e.g. "1 Item From Catalogue Section Available." -->
<?php foreach ($ad_categories as $slug=>$info): ?>
    <p><strong><?php echo esc_html($info['limit']); ?> Item(s) from <?php echo esc_html($info['label']); ?> Section available</strong></p>
<?php endforeach; ?>

<p>You may select up to one item in each category (except Digital => up to 5). Below are the products in a grid.</p>

<form method="post">
<?php wp_nonce_field('wv_ex_app_step_2'); ?>

<!-- We'll store final picks in a hidden input. E.g. JSON encoded. The user modifies it with JS. -->
<input type="hidden" id="wv-picked-ads" name="wv_picked_ads_json" value="" />

<div class="wv-ads-grid-container">
<?php
// We'll show each category in its own heading, or do a single big grid. Let's do a single big grid, sorted by category
foreach ($ad_categories as $cat_slug => $data):
    $cat_label = $data['label'];
    $limit     = $data['limit'];
    $products  = $category_products_map[$cat_slug];
    if (empty($products)) {
        echo '<h3>' . esc_html($cat_label) . '</h3>';
        echo '<p>No products found in this category.</p>';
        continue;
    }

    echo '<h3>' . esc_html($cat_label) . '</h3>';
    echo '<div class="wv-ads-grid" style="display:flex;flex-wrap:wrap;gap:16px;">';
    foreach ($products as $p) {
        /** @var \WC_Product $p */
        $pid = $p->get_id();
        $title = $p->get_name();
        $price = $p->get_price_html(); // or get_price() + your formatting
        $desc  = $p->get_short_description(); // or get_description()
        $img_id= $p->get_image_id();
        $img_url= $img_id ? wp_get_attachment_image_url($img_id,'medium') : wc_placeholder_img_src();

        // fetch attributes (size & surface)
        $size = $p->get_attribute('pa_size');     // or whatever your attribute slug is
        $surf = $p->get_attribute('pa_surface');

        // We'll show them in a card
        ?>
        <div class="wv-ads-card" data-pid="<?php echo esc_attr($pid); ?>" data-category="<?php echo esc_attr($cat_slug); ?>" style="border:1px solid #ccc;padding:8px;width:250px;position:relative;">
            <img src="<?php echo esc_url($img_url); ?>" alt="" style="width:100%;height:auto;" />
            <h4 style="margin:8px 0;"><?php echo esc_html($title); ?></h4>
            <p style="font-size:0.9em;"><?php echo wp_kses_post($desc); ?></p>
            <p><strong>Price:</strong> <?php echo $price; ?></p>
            <?php if($size || $surf): ?>
            <p style="font-size:0.8em;">
              <?php if($size): ?>
                <strong>Size:</strong> <?php echo esc_html($size); ?><br/>
              <?php endif; ?>
              <?php if($surf): ?>
                <strong>Surface:</strong> <?php echo esc_html($surf); ?>
              <?php endif; ?>
            </p>
            <?php endif; ?>

            <button type="button" class="wv-ads-view-btn" data-pid="<?php echo esc_attr($pid); ?>" style="background:#0073aa;color:#fff;padding:6px 8px;margin-top:6px;border:none;cursor:pointer;">
                View
            </button>
        </div>
        <?php
    }
    echo '</div><!-- .wv-ads-grid -->';
endforeach;
?>
</div><!-- .wv-ads-grid-container -->

<br/>
<h3>Picked Items</h3>
<div id="wv-picked-list" style="border:1px solid #999;padding:8px;min-height:50px;">
    <!-- We'll display the selected items from session + real-time selections here. -->
</div>

<div class="wv-form-nav" style="margin-top:16px;">
    <button type="submit" name="navigation" value="prev">← Back</button>
    <button type="submit" name="navigation" value="next">Next →</button>
</div>
</form>

<!-- The "View" modal -->
<div id="wv-view-modal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.4);z-index:999;">
  <div style="background:#fff;width:600px;max-width:90%;margin:50px auto;padding:16px;position:relative;">
    <button type="button" id="wv-modal-close-btn" style="position:absolute;top:8px;right:8px;">X</button>

    <div id="wv-modal-content" style="display:flex;gap:16px;">
      <!-- We show the large image on left, attributes and "Select" button on right -->
      <img id="wv-modal-img" src="" alt="" style="width:300px;height:auto;" />
      <div style="flex:1;">
        <h4 id="wv-modal-title"></h4>
        <p id="wv-modal-desc"></p>
        <p><strong>Price:</strong> <span id="wv-modal-price"></span></p>
        <div id="wv-modal-attrs" style="font-size:0.9em;"></div>

        <br/>
        <button type="button" id="wv-modal-select-btn" style="background:#2ea2cc;color:#fff;padding:8px 12px;border:none;cursor:pointer;">Select This Item</button>
      </div>
    </div>
  </div>
</div>

<script>
(function($){
  'use strict';

  // Category limits
  const catLimits = {
    <?php
    // e.g. catalogue => 1, billboard=>1, flag=>1, digital=>5
    // in JS object
    foreach($ad_categories as $slug=>$info){
      echo "'$slug': " . intval($info['limit']) . ", ";
    }
    ?>
  };

  // We'll keep an internal state: picks = { category_slug: [ productId1, productId2,... ], ... }
  // We read from PHP session initially
  let picks = <?php echo json_encode($saved_ads); ?>;

  // On doc ready
  $(function(){

    // 1) Display existing picks from session
    renderPickedList();

    // 2) "View" button => open modal
    $('.wv-ads-view-btn').on('click', function(e){
      e.preventDefault();
      const $card = $(this).closest('.wv-ads-card');
      const pid   = $card.data('pid');
      const cat   = $card.data('category');

      // gather info from the card
      const imgUrl = $card.find('img').attr('src');
      const title  = $card.find('h4').text();
      const desc   = $card.find('p').first().html(); // hack: first <p> is short desc
      const price  = $card.find('strong:contains("Price")').parent().text().replace('Price:','').trim();

      // "Size" & "Surface" we can store in data attrs or parse. For simplicity let's parse:
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

      // Fill modal
      $('#wv-modal-img').attr('src', imgUrl);
      $('#wv-modal-title').text(title);
      $('#wv-modal-desc').html(desc);
      $('#wv-modal-price').text(price);

      let attrHtml = '';
      if(size) attrHtml += '<p><strong>Size:</strong> '+size+'</p>';
      if(surface) attrHtml += '<p><strong>Surface:</strong> '+surface+'</p>';
      $('#wv-modal-attrs').html(attrHtml);

      // store pid/cat in select button
      $('#wv-modal-select-btn').data('pid', pid);
      $('#wv-modal-select-btn').data('cat', cat);

      // show modal
      $('#wv-view-modal').show();
    });

    // 3) Close modal
    $('#wv-modal-close-btn').on('click', function(){
      $('#wv-view-modal').hide();
    });

    // 4) "Select This Item" => we add to picks if not exceeding limit
    $('#wv-modal-select-btn').on('click', function(){
      const pid = $(this).data('pid');
      const cat = $(this).data('cat');

      if(!catLimits[cat]) {
        alert('Unknown category limit? ' + cat);
        return;
      }

      // check how many we have in that cat
      let arr = picks[cat] || [];
      // if already selected, do nothing
      if(arr.includes(pid)){
        alert('You already selected this product in '+cat);
        return;
      }

      // check limit
      if(cat === 'digital'){
        if(arr.length >= catLimits[cat]) {
          alert('You can select max '+catLimits[cat]+' items in '+cat);
          return;
        }
      } else {
        // one item only
        if(arr.length >= catLimits[cat]) {
          alert('You can only select '+catLimits[cat]+' item in '+cat+' category.');
          return;
        }
      }

      // add
      arr.push(pid);
      picks[cat] = arr;
      // re-render picks
      renderPickedList();

      // hide modal
      $('#wv-view-modal').hide();
    });

    // 5) Removing an item
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

    // 6) On form submit => store picks in hidden input as JSON
    $('form').on('submit', function(){
      // convert picks obj => JSON
      const jsonStr = JSON.stringify(picks);
      $('#wv-picked-ads').val(jsonStr);
      // proceed
      return true;
    });

  }); // end doc ready

  function renderPickedList(){
    // We'll build an HTML structure listing each item with remove button
    let html = '';

    // For each category
    Object.keys(picks).forEach(cat => {
      const arr = picks[cat];
      if(!arr || arr.length===0) return;

      html += '<h4>'+cat+'</h4><ul>';
      arr.forEach(pid => {
        html += '<li>Product ID '+pid+' <button type="button" class="wv-remove-picked" data-cat="'+cat+'" data-pid="'+pid+'">Remove</button></li>';
      });
      html += '</ul>';
    });

    if(!html){
      html = '<p>No items selected yet.</p>';
    }
    $('#wv-picked-list').html(html);
  }

})(jQuery);
</script>
