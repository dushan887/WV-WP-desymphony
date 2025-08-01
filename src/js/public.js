/********************************************************
 * GLOBAL SPINNER & AJAX WRAPPER
 * (You can also define Spinner in main.js if you prefer)
 ********************************************************/
const Spinner = {
  show() {
    const el = document.getElementById('globalSpinner');
    if (el) el.classList.add('active');
  },
  hide() {
    const el = document.getElementById('globalSpinner');
    if (el) el.classList.remove('active');
  }
};

/**
 * ajaxRequest - a wrapper for standard WP AJAX calls
 * Using form-encoded data for admin-ajax compatibility.
 */
function ajaxRequest(url, data, onSuccess, onError, method = 'POST') {
  Spinner.show();

  // Convert JS object -> URL form-encoded string
  const formData = new URLSearchParams();
  for (const key in data) {
    formData.append(key, data[key]);
  }

  fetch(url, {
    method,
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: formData.toString()
  })
  .then(response => response.json())
  .then(result => {
    Spinner.hide();
    if (result.success) {
      onSuccess(result.data);
    } else {
      // WordPress typically returns { success: false, data: {...} }
      onError(result.data);
    }
  })
  .catch(error => {
    Spinner.hide();
    console.error('AJAX Error:', error);
    onError(error);
  });
}

/********************************************************
 * MAIN PUBLIC.JS CODE
 ********************************************************/
jQuery(document).ready(function($) {

  /********************************************************
   * CORE UTILS: Clear selection, highlight stands, etc.
   ********************************************************/
  function clearAllStandSelections() {
    $('.stand-item.active').removeClass('active');
    $('.hall-svg-container svg g.ds-stand.active').removeClass('active');
  }

  function selectStandFromList(standId) {
    clearAllStandSelections();
    var $listItem = $('.stand-item[data-stand-id="' + standId + '"]');
    var $svgItem  = $('.hall-svg-container svg g#' + standId);
    $listItem.addClass('active');
    $svgItem.addClass('active');
    // Scroll to SVG
    var svgContainer = document.querySelector('.hall-svg-container');
    if (svgContainer) {
      svgContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
  }

  function selectStandFromSvg(standId) {
    clearAllStandSelections();
    var $listItem = $('.stand-item[data-stand-id="' + standId + '"]');
    var $svgItem  = $('.hall-svg-container svg g#' + standId);
    $listItem.addClass('active');
    $svgItem.addClass('active');
    // Scroll to list
    var listContainer = document.querySelector('.stands-list-container');
    if (listContainer) {
      listContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  }
  
  const standConfig = {
    "Hall 1G": {
      "9m2":    { modalId: "dsStandModal-9m2_Hall_1G",    branding_img: "thumbs-per-hall/dsk/9/DSK_HpH_Hall_1G_9m.jpg" },
      "custom": { modalId: "dsStandModal-custom",         branding_img: "custom/DSK_HpH_Custom_1000x890.jpg" }
    },
    "Hall 1": {
      "12m2":   { modalId: "dsStandModal-12m2_Hall_1",   branding_img: "thumbs-per-hall/dsk/12/DSK_HpH_Hall_1_12m.jpg" },
      "24m2":   { modalId: "dsStandModal-24m2_Hall_1",   branding_img: "thumbs-per-hall/dsk/24/DSK_HpH_Hall_1R_24m.jpg" },
      "49m2":   { modalId: "dsStandModal-49m2_Hall_1",   branding_img: "thumbs-per-hall/dsk/49/DSK_HpH_Hall_1_49m.jpg" },
      "custom": { modalId: "dsStandModal-custom",        branding_img: "custom/DSK_HpH_Custom_1000x890.jpg" }
    },
    "Hall 3": {
      "12m2":   { modalId: "dsStandModal-12m2_Hall_3",   branding_img: "thumbs-per-hall/dsk/12/DSK_HpH_Hall_3_12m.jpg" },
      "24m2":   { modalId: "dsStandModal-24m2_Hall_3",   branding_img: "thumbs-per-hall/dsk/24/DSK_HpH_Hall_3_24m.jpg" },
      "49m2":   { modalId: "dsStandModal-49m2_Hall_3",   branding_img: "thumbs-per-hall/dsk/49/DSK_HpH_Hall_3_49m.jpg" },
      "custom": { modalId: "dsStandModal-custom",        branding_img: "custom/DSK_HpH_Custom_1000x890.jpg" }
    },
    "Hall 1A": {
      "12m2":   { modalId: "dsStandModal-12m2_Hall_1A",  branding_img: "thumbs-per-hall/dsk/12/DSK_HpH_Hall_1A_12m.jpg" },
      "24m2":   { modalId: "dsStandModal-24m2_Hall_1A",  branding_img: "thumbs-per-hall/dsk/24/DSK_HpH_Hall_1A_24m.jpg" },
      "custom": { modalId: "dsStandModal-custom",        branding_img: "custom/DSK_HpH_Custom_1000x890.jpg" }
    },
    "Hall 3A": {
      "12m2":   { modalId: "dsStandModal-12m2_Hall_3A",  branding_img: "thumbs-per-hall/dsk/12/DSK_HpH_Hall_3A_12m.jpg" },
      "24m2":   { modalId: "dsStandModal-24m2_Hall_3A",  branding_img: "thumbs-per-hall/dsk/24/DSK_HpH_Hall_3A_24m.jpg" },
      "custom": { modalId: "dsStandModal-custom",        branding_img: "custom/DSK_HpH_Custom_1000x890.jpg" }
    },
    "Hall 2B": {
      "12m2":   { modalId: "dsStandModal-12m2_Halls_2B_2A_2C", branding_img: "thumbs-per-hall/dsk/12/DSK_HpH_Hall_2B_2A_2C_12m.jpg" },
      "24m2":   { modalId: "dsStandModal-24m2_Halls_2B_2A_2C", branding_img: "thumbs-per-hall/dsk/24/DSK_HpH_Hall_2B_2A_2C_24m.jpg" },
      "49m2":   { modalId: "dsStandModal-49m2_Halls_2B_2A_2C", branding_img: "thumbs-per-hall/dsk/49/DSK_HpH_Hall_2B_2A_2C_49m.jpg" },
      "custom": { modalId: "dsStandModal-custom",            branding_img: "custom/DSK_HpH_Custom_1000x890.jpg" }
    },
    "Hall 2A": {
      "12m2":   { modalId: "dsStandModal-12m2_Halls_2B_2A_2C", branding_img: "thumbs-per-hall/dsk/12/DSK_HpH_Hall_2B_2A_2C_12m.jpg" },
      "24m2":   { modalId: "dsStandModal-24m2_Halls_2B_2A_2C", branding_img: "thumbs-per-hall/dsk/24/DSK_HpH_Hall_2B_2A_2C_24m.jpg" },
      "49m2":   { modalId: "dsStandModal-49m2_Halls_2B_2A_2C", branding_img: "thumbs-per-hall/dsk/49/DSK_HpH_Hall_2B_2A_2C_49m.jpg" },
      "custom": { modalId: "dsStandModal-custom",            branding_img: "custom/DSK_HpH_Custom_1000x890.jpg" }
    },
    "Hall 2C": {
      "12m2":   { modalId: "dsStandModal-12m2_Halls_2B_2A_2C", branding_img: "thumbs-per-hall/dsk/12/DSK_HpH_Hall_2B_2A_2C_12m.jpg" },
      "24m2":   { modalId: "dsStandModal-24m2_Halls_2B_2A_2C", branding_img: "thumbs-per-hall/dsk/24/DSK_HpH_Hall_2B_2A_2C_24m.jpg" },
      "49m2":   { modalId: "dsStandModal-49m2_Halls_2B_2A_2C", branding_img: "thumbs-per-hall/dsk/49/DSK_HpH_Hall_2B_2A_2C_49m.jpg" },
      "custom": { modalId: "dsStandModal-custom",            branding_img: "custom/DSK_HpH_Custom_1000x890.jpg" }
    },
    "Hall 4A": {
      "12m2":   { modalId: "dsStandModal-12m2_Hall_4A",  branding_img: "thumbs-per-hall/dsk/12/DSK_HpH_Hall_4A_12m.jpg" },
      "24m2":   { modalId: "dsStandModal-24m2_Hall_4A",  branding_img: "thumbs-per-hall/dsk/24/DSK_HpH_Hall_4A_24m.jpg" },
      "custom": { modalId: "dsStandModal-custom",        branding_img: "custom/DSK_HpH_Custom_1000x890.jpg" }
    }
  };

  function updateModalNavs(data) {
    const hall = data.hall_label;
    const config = standConfig[hall] || {};
    const imgBase = '/wp-content/themes/desymphony/src/images/stands/';

    Object.entries(config).forEach(([sizeSlug, { modalId, branding_img }]) => {
      const key   = sizeSlug === 'custom' ? 'other' : sizeSlug.replace('m2','');
      const count = (data.size_groups[key] || []).length;
      const $card = $(`#ds-stands-${sizeSlug}`);
      const $btn  = $card.find('button');

      $card.find(`.wv-count-${sizeSlug}`).text(count);
      $card.toggleClass('opacity-50', count === 0);

      // image logic
      if (sizeSlug === 'custom') {
        const imgName = count > 0
          ? 'custom/DSK_HpH_Custom_1000x890.jpg'
          : 'custom/DSK_HI_Custom_1000x890.jpg';
        $card.find('img').attr('src', imgBase + imgName);
      } else {
        $card.find('img').attr('src', imgBase + branding_img);
      }

      if (count > 0) {
        $btn.removeClass('d-none').attr('data-bs-target', `#${modalId}`);
      } else {
        $btn.addClass('d-none');
      }

      $('.ds-stand-info-box .ds-stand-info-val').removeClass('d-none');

      if ($('#hall-content').length) {
        const el = $('#hall-content')[0];
        const rect = el.getBoundingClientRect();
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        const top = rect.top + scrollTop - 191;
        window.scrollTo({ top, behavior: 'smooth' });
      }
    });
  }


  /**
   * highlightGroups: add classes (size, sold, etc.)highlightGroups after AJAX load
   */
  function highlightGroups(data) {
    // console.log('Hall explorer →', data); 
    
      // Size groups
      (data.size_groups['9'] || []).forEach(function(id){
          $('.stand-item[data-stand-id="'+id+'"]').addClass('wv-stand-9m2');
          $('.hall-svg-container svg g#'+id).addClass('wv-stand-9m2');
      });
      (data.size_groups['12'] || []).forEach(function(id){
          $('.stand-item[data-stand-id="'+id+'"]').addClass('wv-stand-12m2');
          $('.hall-svg-container svg g#'+id).addClass('wv-stand-12m2');
      });
      (data.size_groups['24'] || []).forEach(function(id){
          $('.stand-item[data-stand-id="'+id+'"]').addClass('wv-stand-24m2');
          $('.hall-svg-container svg g#'+id).addClass('wv-stand-24m2');
      });
      (data.size_groups['49'] || []).forEach(function(id){
          $('.stand-item[data-stand-id="'+id+'"]').addClass('wv-stand-49m2');
          $('.hall-svg-container svg g#'+id).addClass('wv-stand-49m2');
      });
      (data.size_groups['other'] || []).forEach(function(id){
          $('.stand-item[data-stand-id="'+id+'"]').addClass('wv-stand-custom');
          $('.hall-svg-container svg g#'+id).addClass('wv-stand-custom');
      });

      const counts = {
          'wv-count-9m2': (data.size_groups['9'] || []).length,
          'wv-count-12m2': (data.size_groups['12'] || []).length,
          'wv-count-24m2': (data.size_groups['24'] || []).length,
          'wv-count-49m2': (data.size_groups['49'] || []).length,
          'wv-count-custom': (data.size_groups['other'] || []).length,
      };

      // Update DOM elements
      Object.entries(counts).forEach(([cls, count]) => {
          $('.' + cls).each(function() {
              const $element = $(this);
              $element.text(count);

              if (count === 0) {
                  $element.parent().addClass('opacity-50');
              } else {
                  $element.parent().removeClass('opacity-50');
              }
          });
      });
      
      // Status groups
      (data.status_groups['sold'] || []).forEach(function(id){
          $('.stand-item[data-stand-id="'+id+'"]').addClass('stand-sold');
          $('.hall-svg-container svg g#'+id).addClass('stand-sold');
      });
      (data.status_groups['reserved'] || []).forEach(function(id){
          $('.stand-item[data-stand-id="'+id+'"]').addClass('stand-reserved');
          $('.hall-svg-container svg g#'+id).addClass('stand-reserved');
      });
      (data.reserved_current_user_ids || []).forEach(function(id) {
          $('.stand-item[data-stand-id="'+id+'"]').removeClass('stand-reserved').addClass('wv-current-user');
          $('.hall-svg-container svg g#'+id).removeClass('stand-reserved').addClass('wv-current-user');
      });
      (data.purchased_ids || []).forEach(function(id){
          $('.stand-item[data-stand-id="'+id+'"]').addClass('stand-purchased');
          $('.hall-svg-container svg g#'+id).addClass('stand-purchased');
      });
      (data.in_cart_ids || []).forEach(function(id){
          $('.stand-item[data-stand-id="'+id+'"]').addClass('stand-in-cart');
          $('.hall-svg-container svg g#'+id).addClass('stand-in-cart');
      });
  }

   
  /* ===========================================================
  *  USER-DASHBOARD → highlight stands I actually own
  *  Called right after we inject the hall SVG (dashboardPurchasedStands)
  * =========================================================== */
  function highlightGroupsProfile (data) {
    // console.log('Dashboard →', data); 

    /* 0️⃣  Get a flat array with every stand id that exists in this hall SVG */
    const allIds = []
    Object.values(data.size_groups || {}).forEach(arr => { allIds.push(...arr) })

    /* 1️⃣  Build a Set with the ones the CURRENT visitor owns */
    const owned = new Set([
      ...(data.purchased_ids            || []),
      ...(data.reserved_current_user_ids|| [])
    ])

    /* 2️⃣  First wipe any previous markings */
    $('.hall-svg-container svg g.ds-stand')
        .removeClass('wv-current-user wv-not-current-user')

    /* 3️⃣  Tag each stand */
    allIds.forEach(id => {
      const $g = $(`.hall-svg-container svg g#${id}`)
      if (!$g.length) return          // not present – safety

      if (owned.has(id)) {
        $g.addClass('wv-current-user')
      } else {
        $g.addClass('wv-not-current-user')
      }
    })

    /* 4️⃣  Counters / list mode are not needed here */
    $('.ds-hall-counter, .wv-count-9m2, .wv-count-12m2, .wv-count-24m2,' +
      '.wv-count-49m2, .wv-count-custom').remove()
  }


  /********************************************************
   * MODE A: LIST MODE (Multiple Selection + direct cart)
   ********************************************************/
  function initStandListMode() {
    $(document).off('click.ds-stand-list');
    $(document).on('click.ds-stand-list', '.ds-hall-root.ds-hall--list .stand-item', function(){
      var sid = $(this).data('stand-id');
      selectStandFromList(sid);
    });

    $(document).off('click.ds-stand-svg-list');
    $(document).on('click.ds-stand-svg-list', '.ds-hall-root.ds-hall--list .hall-svg-container svg g.ds-stand', function(){
      var sid = $(this).attr('id');
      selectStandFromSvg(sid);
    });
  }

  function initStandListCartActions() {
    // SVG click => add/remove cart
    $(document).off('click.list-cart-svg');
    $(document).on('click.list-cart-svg', '.ds-hall-root.ds-hall--cart.ds-hall--list .hall-svg-container svg g.ds-stand', function(){
      if ($(this).hasClass('stand-sold') || $(this).hasClass('stand-reserved')) return;
      var standId = $(this).attr('id');
      var $listItem = $('.stand-item[data-stand-id="'+standId+'"]');
      var productId = $listItem.data('product-id');
      if (productId) addRemoveStandToCart(productId, standId);
    });

    // List item click => add/remove cart
    $(document).off('click.list-cart-item');
    $(document).on('click.list-cart-item', '.ds-hall-root.ds-hall--cart.ds-hall--list .stand-item', function(){
      if ($(this).hasClass('stand-sold') || $(this).hasClass('stand-reserved')) return;
      var standId = $(this).data('stand-id');
      var productId = $(this).data('product-id');
      if (productId) addRemoveStandToCart(productId, standId);
    });
  }

  /********************************************************
   * MODE B: SINGLE SELECT CART (".ds-hall--cart" alone)
   ********************************************************/
  let singleSelectedStand = null;

  function initStandSingleCartMode() {
    // Single stand selection on click (SVG or list)
    $(document).off('click.single-cart-svg');
    $(document).on('click.single-cart-svg', '.ds-hall-root.ds-hall--cart:not(.ds-hall--list) .hall-svg-container svg g.ds-stand', function(){
      if ($(this).hasClass('stand-sold') || $(this).hasClass('stand-reserved')) return;
      let standId = $(this).attr('id');
      singleSelectStand(standId);
    });

    $(document).off('click.single-cart-list');
    $(document).on('click.single-cart-list', '.ds-hall-root.ds-hall--cart:not(.ds-hall--list) .stand-item', function(){
      if ($(this).hasClass('stand-sold') || $(this).hasClass('stand-reserved')) return;
      let standId = $(this).data('stand-id');
      singleSelectStand(standId);
    });

    // The old #wv-add-stand-btn logic: toggles cart
    $(document).off('click.single-add');
    $(document).on('click.single-add', '#wv-add-stand-btn', function(){
      if (!singleSelectedStand) return;
      addRemoveStandToCart(singleSelectedStand.productId, singleSelectedStand.standId);
      hideSingleStandBar();
    });

    // Cancel
    $(document).off('click.single-cancel');
    $(document).on('click.single-cancel', '#wv-cancel-stand-btn', function(){
      clearAllStandSelections();
      singleSelectedStand = null;
      hideSingleStandBar();
    });
  }


  /**
   * singleSelectStand: highlight only that stand,
   * then do optional add-ons, etc.
   */
  function singleSelectStand(standId) {
    clearAllStandSelections();
    let $listItem = $('.stand-item[data-stand-id="'+standId+'"]');
    let $svgItem  = $('.hall-svg-container svg g#'+standId);
    $listItem.addClass('active');
    $svgItem.addClass('active');

    let hallSlug = $('#hall-content').data('hall-slug') || '';
    let productId = $listItem.data('product-id') || null;
    const inCart = $listItem.hasClass('stand-in-cart');

    // Determine if we can add to cart
    let canAddToCart = true;
    if ($listItem.hasClass('stand-reserved') && !$listItem.hasClass('wv-current-user')) {
      canAddToCart = false;
    } else if ($listItem.hasClass('stand-sold')) {
      canAddToCart = false;
    }

    singleSelectedStand = {
      standId: standId,
      productId: productId,
      hallSlug: hallSlug,
      canAddToCart: canAddToCart,
      inCart                                                
    };

    showSingleStandBar(singleSelectedStand);
  }


  /**
   * loadStandAddonsAjax - example AJAX for stand add-ons
   */
  function loadStandAddonsAjax(standSize, callback) {
    if (!wvCartData || !wvCartData.nonce) {
      alert('Security error: Nonce is missing. Please reload the page.');
      return;
    }
    ajaxRequest(
      wvCartData.ajaxUrl,
      {
        action: 'ds_load_stand_addons',
        stand_size: standSize,
        nonce: wvCartData.nonce
      },
      function(data) {
        if (typeof callback === 'function' && data.addons) {
          callback(data.addons);
        }
      },
      function() {
        // If no addons or error
        $('#wv-stand-info').append('<p class="text-danger">No add-ons available.</p>');
      }
    );
  }

  /**
   * buildAddOnUI - show add-ons in #wv-stand-info
   */
  function buildAddOnUI(addons) {
    let html = '<table class="table table-sm table-bordered wv-addon-table">';
    html += '<thead><tr><th>Add-On</th><th>Price (€)</th><th>Qty</th></tr></thead><tbody>';
    addons.forEach(function(addon){
      html += `
        <tr data-addon-slug="${addon.slug}">
          <td>${addon.label}</td>
          <td>${addon.price}</td>
          <td><input type="number" class="addon-qty" value="0" min="0" data-price="${addon.price}" style="width:60px;" /></td>
        </tr>
      `;
    });
    html += '</tbody></table>';
    html += `
      <button class="button button-primary" id="wv-stand-confirm-btn">Confirm Stand</button>
      <button class="button" id="wv-stand-cancel2-btn">Cancel</button>
    `;

    $('#wv-stand-info').append(html);

    // Cancel
    $('#wv-stand-cancel2-btn').off('click').on('click', function(){
      clearAllStandSelections();
      hideSingleStandBar();
    });

    // Confirm
    $('#wv-stand-confirm-btn').off('click').on('click', function(){
      let chosenAddons = [];
      $('.addon-qty').each(function(){
        let qty = parseInt($(this).val(), 10) || 0;
        if (qty > 0) {
          let slug  = $(this).closest('tr').data('addon-slug');
          let label = $(this).closest('tr').find('td:first').text();
          let price = parseFloat($(this).data('price'));
          chosenAddons.push({ slug, label, qty, price });
        }
      });
      // console.log('User selected add-ons:', chosenAddons);
      alert('Add-ons chosen (not yet added to cart). See console.');
    });
  }
  /**
   * showSingleStandBar
   */
  function showSingleStandBar(standData) {
    const lastSegment = standData.standId.split('_').pop();
    $('#wv-stand-info').text(`Stand ${lastSegment}`);
    // If stand is not available, disable button
    $('#wv-add-stand-btn')
    .text(standData.inCart ? 'Remove Stand' : 'Add Stand')
    .prop('disabled', !standData.canAddToCart);
    $('#wv-single-stand-bar').show();
  }

  function hideSingleStandBar() {
    $('#wv-single-stand-bar').hide();
    $('#wv-stand-info').empty();
  }


  /********************************************************
   * SHARED: Add/Remove cart via unified wrapper
   ********************************************************/
  function addRemoveStandToCart(productId, standId) {
    if (!wvCartData || !wvCartData.nonce) {
      alert('Security error: Nonce is missing. Please reload the page.');
      return;
    }
    ajaxRequest(
      wvCartData.ajaxUrl,
      {
        action: 'ds_add_stand_to_cart',
        product_id: productId,
        nonce: wvCartData.nonce
      },
      function(data) {
        $('#stand-cart-container').html(data.html);

        const $listItem = $('.stand-item[data-stand-id="' + standId + '"]');
        const $svgItem  = $('.hall-svg-container svg g#' + standId);

        $listItem.toggleClass('stand-in-cart', data.in_cart);      // ⬅️ NEW
        $svgItem.toggleClass('stand-in-cart', data.in_cart);

        /* keep button label in sync if bar is visible */
        if (singleSelectedStand && singleSelectedStand.standId === standId) {
          singleSelectedStand.inCart = data.in_cart;
          $('#wv-add-stand-btn').text(data.in_cart ? 'Remove Stand' : 'Add Stand');
        }
      },
      function(errData) {
        alert((errData && errData.message) || 'Error adding to cart');
      }
    );
  }

  /* ------------------------------------------------------------------
   * UI helper – show “Hall XX Selected” in the header
   * ------------------------------------------------------------------*/
  const updateSelectedHallLabel = (label) => {
    // normalise – ensure we start with the word “Hall”
    const normalised = label.trim().startsWith('Hall')
      ? label.trim()
      : `Hall ${label.trim()}`;

    $('#wv-selected-hall').text(`${normalised} Selected`);
  };

  /********************************************************
   * AJAX LOAD / MAIN INIT
   ********************************************************/
  function loadHallByIndex(newIndex) {
    if (newIndex < 0) newIndex = hallsOrder.length - 1;
    if (newIndex >= hallsOrder.length) newIndex = 0;
    var slug = hallsOrder[newIndex];

    ajaxRequest(
      ajaxUrl,
      { action: 'ds_load_fair_hall', hall: slug },
      function(data) {
        // success
        $('#hall-content').replaceWith(data.html);

        updateSelectedHallLabel(data.hall_label || slug);
        

        clearAllStandSelections();
        initModeSwitch();
        highlightGroups(data);
        updateModalNavs(data);
        $('.hall-nav-svg g.wv-nav-hall.active:not(.wv-nav-hall-zones)').removeClass('active');
        $('.hall-nav-svg g.wv-nav-hall[data-name="' + slug + '"]:not(.wv-nav-hall-zones)').addClass('active');
        currentHallIndex = newIndex;
      },
      function() {
        alert('Error loading hall data. Try again.');
      }
    );
  }

  function initModeSwitch() {
    // If .ds-hall--list => multiple selection
    if ($('.ds-hall-root.ds-hall--list').length) {
      initStandListMode();
      // If also .ds-hall--cart => multi-cart mode
      if ($('.ds-hall-root.ds-hall--list.ds-hall--cart').length) {
        initStandListCartActions();
      }
    }
    // Else single select cart mode
    else if ($('.ds-hall-root.ds-hall--cart').length) {
      initStandSingleCartMode();
    }
  }

  // ===== PAGE LOAD =====
  var hallsOrder = (window.DSHallsData && DSHallsData.hallsOrder) || [];
  var ajaxUrl    = (window.DSHallsData && DSHallsData.ajaxUrl)    || '';
  if (!hallsOrder.length) return;

  var currentHallIndex = 0;
  var initialSlug = $('#hall-content').attr('data-hall-slug');
  if (initialSlug && hallsOrder.indexOf(initialSlug) !== -1) {
    currentHallIndex = hallsOrder.indexOf(initialSlug);
  }

  // Init any existing hall
  initModeSwitch();

  // Nav Buttons
  $(document).on('click', '#exhibitors-tab button[data-action]', function(e) {
    e.preventDefault();
    var action = $(this).data('action');
    if (action === 'prev') {
      loadHallByIndex(currentHallIndex - 1);
    } else if (action === 'next') {
      loadHallByIndex(currentHallIndex + 1);
    }
  });

  // SVG Nav
  $(document).on('click', '.hall-nav-svg g.wv-nav-hall:not(.wv-nav-hall-zones)', function() {
    var slug = $(this).attr('data-name');
    var idx = hallsOrder.indexOf(slug);
    if (idx !== -1) {
      loadHallByIndex(idx);
    }
  });

  $(document).on('change', '.ds-addon-check', function () {
    const $qty = $(this).closest('tr').find('.ds-addon-qty');
    this.checked ? $qty.val(Math.max(1, +$qty.val() || 1)) : $qty.val(0);
  });


  // toggle qty between 0 and 1 (or keep current) whenever the checkbox flips
  $(document).on('change', '.ds-addon-check', function () {
    const $qty = $(this).closest('div.row').find('.ds-addon-qty');
    this.checked ? $qty.val(Math.max(1, +$qty.val() || 1)) : $qty.val(0);
  });


  /* -----------------------------------------------------------
   Confirm Stand  – sequentially update each add-on, then refresh
----------------------------------------------------------- */
  $(document).on('click.ds-confirm-stand', '.ds-confirm-stand', function (e) {
    e.preventDefault();

    const cartKey = $(this).data('cart-key');
    const $card   = $(this).closest('.card');          // scope = this stand
    const $qtyInp = $card.find('.ds-addon-qty');       // all qty inputs
    const addons  = [];

    /* build a work list */
    $qtyInp.each(function () {
      const $q   = $(this);
      const slug = $q.data('addon-slug');
      const price= parseFloat($q.data('addon-price'));
      const checked = $card.find(`.ds-addon-check[data-addon-slug="${slug}"]`).prop('checked');
      const qty  = checked ? (parseInt($q.val(), 10) || 1) : 0;   // unchecked → 0
      addons.push({ slug, price, qty });
    });

    /* sequential ajax runner */
    function run(idx, latestHTML = '') {
      if (idx >= addons.length) {
        // all done → refresh block once & close
        $('#stand-cart-container').html(latestHTML);
        $card.find('.collapse').collapse('hide');
        return;
      }

      const ad = addons[idx];
      ajaxRequest(
        wvCartData.ajaxUrl,
        {
          action:      'ds_update_stand_addon',
          nonce:       wvCartData.nonce,
          cart_key:    cartKey,
          addon_slug:  ad.slug,
          addon_price: ad.price,
          addon_qty:   ad.qty
        },
        data => run(idx + 1, data.html),      // chain next
        err  => { alert(err.message || 'Add-on error'); }
      );
    }

    run(0);                                   // kick off chain
  });

  /* -----------------------------------------------------------
    Leave / refresh protection: warn + clear stand cart
  ----------------------------------------------------------- */
  let wvSkipStandClear = false;

  /* mark flag ASAP (mousedown / touchstart / click CAPTURE) */
  document.addEventListener('mousedown',  markSkip, true);
  document.addEventListener('touchstart', markSkip, true);
  document.addEventListener('click',      markSkip, true);

  function markSkip(e) {
    if ( e.target.closest('[data-skip-clear]') ) {
      wvSkipStandClear = true;
    }
  }


  (function () {
    if ( ! window.wvCartData || ! navigator.sendBeacon ) return;

    /*  ⬇️  Add this line */
    if ( ! window.location.pathname.includes('/wv-application') ) return;
    /*  ---------------------------------- */

    function clearStandCart() {
      if ( wvSkipStandClear ) return;          // going to checkout – do nothing
      navigator.sendBeacon(
        wvCartData.ajaxUrl,
        new URLSearchParams({ action: 'ds_clear_stand_cart' })
      );
    }

    window.addEventListener('pagehide', clearStandCart, { passive: true });
    window.addEventListener('beforeunload', function (e) {
      if ( wvSkipStandClear ) return;        // navigating via checkout → no prompt
      clearStandCart();
      e.preventDefault();                    // still needed for some browsers
      e.returnValue =
        'If you leave this page your stand selections will be cleared.';
    });

    

  })();


  (function dashboardPurchasedStands () {
  const $sel = $('#ds-hall-select');
  if ( !$sel.length ) return;        // not on this page

  // ① load first hall, if any
  if ( $sel.val() ) {
    loadDashboardHall( $sel.val() );
  }

  // ② dropdown change
  $sel.on('change', function () {
    this.value ? loadDashboardHall(this.value)
               : $('#hall-content').empty();
  });

  // ③ ajax loader
  function loadDashboardHall (hallSlug) {
    ajaxRequest(
      DSHallsData.ajaxUrl,
      { action: 'ds_load_fair_hall', hall: hallSlug },
      data => {
        // insert *inside* the existing wrapper, keep it alive
        $('#hall-content').html( data.html );
        highlightGroupsProfile( data );
      },
      err  => alert( err.message || 'Could not load hall' )
    );
  }
})();

$(document).on('click', '#ds-finish-application', function (e) {
	e.preventDefault();

	ajaxRequest(
		wvFinishApp.ajax,
		{ action: 'ds_finish_application', nonce: wvFinishApp.nonce },
		() => window.location.href = this.href,          // success → profile
		err => alert( (err && err.message) || 'Unable to finish application.' )
	);
});




}); // END document.ready

