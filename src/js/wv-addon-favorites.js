(function($){
  'use strict';

  $(document).ready(function(){

    // 1) Clicking the "favorite" button => add favorite
    $(document).on('click', '.wv-favorite-btn', function(e){
      e.preventDefault();
      const $btn = $(this);
      const targetType = $btn.data('target-type');
      const targetId   = parseInt($btn.data('target-id'), 10) || 0;

      if(!targetType || !targetId){
        return alert('Invalid favorite data');
      }

      // We can pick up the nonce from a parent container or from wvFavoritesData
      // For example, the parent container might have class .wv-real-users-list or .wv-real-products-list
      let $container = $btn.closest('[data-nonce]');
      if (!$container.length) {
        $container = $('body'); // fallback
      }
      const nonce = $container.data('nonce') || wvFavoritesData.nonce;

      // Optional confirm
      // if(!confirm('Add this to your favorites?')) return;

      $.ajax({
        url: wvFavoritesData.ajaxUrl,
        method: 'POST',
        dataType: 'json',
        data: {
          action: 'wv_add_favorite',
          security: nonce,
          target_type: targetType,
          target_id: targetId
        },
        success(resp){
          if(resp.success){
            showFavoriteModal('Added to favorites successfully.');

            // Toggle the button to "Remove"
            $btn.removeClass('wv-favorite-btn')
                .addClass('wv-remove-favorite-btn')
                .text('Remove');

          } else {
            alert(resp.data.message || 'Error adding favorite');
          }
        },
        error(xhr, status, err){
          console.error('[Favorites] add_favorite error:', err);
        }
      });
    });


    // 2) Clicking the "remove" button => remove favorite
    $(document).on('click', '.wv-remove-favorite-btn', function(e){
      e.preventDefault();
      const $btn = $(this);
      const targetType = $btn.data('target-type');
      const targetId   = parseInt($btn.data('target-id'), 10) || 0;

      if(!targetType || !targetId){
        return alert('Invalid favorite data');
      }

      // For the nonce, same approach
      let $container = $btn.closest('[data-nonce]');
      if(!$container.length){
        $container = $('body');
      }
      const nonce = $container.data('nonce') || wvFavoritesData.nonce;

      // Optional confirm
      // if(!confirm('Remove this from your favorites?')) return;

      $.ajax({
        url: wvFavoritesData.ajaxUrl,
        method: 'POST',
        dataType: 'json',
        data: {
          action: 'wv_remove_favorite',
          security: nonce,
          target_type: targetType,
          target_id: targetId
        },
        success(resp){
          if(resp.success){
            showFavoriteModal('Removed from favorites successfully.');

            // If we're on the "saved" dashboard view => remove item from DOM
            // Check if we're inside .wv-saved-items-wrapper
            if( $btn.closest('.wv-saved-items-wrapper').length ){
              // remove entire card or list item
              // e.g. if in <li> or in a .wv-saved-card
              $btn.closest('li, .wv-saved-card, .wv-product-card').fadeOut(200, function(){
                $(this).remove();
              });
            } else {
              // Otherwise, just toggle back to a "Favorite" button
              $btn.removeClass('wv-remove-favorite-btn')
                  .addClass('wv-favorite-btn')
                  .html('&#x2764; Favorite');
            }

          } else {
            alert(resp.data.message || 'Error removing favorite');
          }
        },
        error(xhr, status, err){
          console.error('[Favorites] remove_favorite error:', err);
        }
      });
    });


    // 3) Show the "favorite modal" with a message
    function showFavoriteModal(msg){
      $('#wv-favorite-modal .wv-favorite-message').text(msg);
      $('#wv-favorite-modal').show();
    }

    // 4) Hide the modal on close
    $(document).on('click', '.wv-close-modal-btn', function(e){
      e.preventDefault();
      $('#wv-favorite-modal').hide();
    });

  });
})(jQuery);
