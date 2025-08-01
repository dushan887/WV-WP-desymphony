/* global Cropper, wvCropperData */

import Cropper from 'cropperjs';

(function($){
  'use strict';

  const ALLOWED_TYPES = ['image/jpeg', 'image/png', 'image/webp'];
  const MAX_SIZE      = 2 * 1024 * 1024; // 2MB

  /**
   * We'll maintain a single "active" cropper instance at a time,
   * because typically you won't open multiple modals at once.
   */
  let cropperInstance = null; // The Cropper.js instance
  let $activeFileUpload = null; // The .wv-file-upload container currently being edited

  /* -------------------------------------------------------------
    Unified helper: update all preview elements inside a
    .wv-file-upload wrapper to reflect a given URL.
  ------------------------------------------------------------- */
  function paintPreview($fu, url = '') {
    const $img  = $fu.find('.wv-placeholder-img');
    const $svg  = $fu.find('.svg-placeholder');
    const $bg   = $fu.find('.wv-file-preview-container');

    if (url) {
     $img.attr('src', url).removeClass('d-none');
     $svg.addClass('d-none');
     $bg.css('background-image', `url("${url}")`);
    } else {
     $img.addClass('d-none');
     $svg.removeClass('d-none');
     $bg.css('background-image', '');
    }
  }
  /**
   * Helper to initialize Cropper with the specified aspect ratio.
   * It reads from data-aspect-ratio on $activeFileUpload (e.g. "1:1" or "9:16").
   */
  function initCropper($imgElement) {
    // Destroy any existing instance
    if (cropperInstance) {
      cropperInstance.destroy();
      cropperInstance = null;
    }

    const aspectRatioStr = String($activeFileUpload.data('aspect-ratio') || '1');
    let ratio = 1;
    if (aspectRatioStr.includes(':')) {
      const parts = aspectRatioStr.split(':');
      ratio = parseFloat(parts[0]) / parseFloat(parts[1]);
    } else {
      ratio = parseFloat(aspectRatioStr) || 1;
    }

    // Create a new Cropper instance
    cropperInstance = new Cropper($imgElement[0], {
      aspectRatio: ratio,
      viewMode: 1,
      autoCropArea: 1.0
    });
  }

  /**
   * 1) Clicking "Upload Image" => open hidden <input type="file">
   */
  $(document).on('click', '.wv-upload-btn', function(e){
    e.preventDefault();

    $activeFileUpload = $(this).closest('.wv-file-upload');
    const $fileInput  = $activeFileUpload.find('.wv-crop-input[type="file"]');

    // Trigger the file picker
    $fileInput.trigger('click');
  });

  /**
   * 2) On file input "change" => read file & open the dedicated modal
   */
  $(document).on('change', '.wv-file-upload .wv-crop-input[type="file"]', function(e){
    const file = e.target.files[0];
    if (!file) return;

    if (!ALLOWED_TYPES.includes(file.type)) {
      alert('Only JPG, PNG or WEBP files are allowed.');
      e.target.value = '';
      return;
    }
    if (file.size > MAX_SIZE) {
      alert('Max file size is 2 MB.');
      e.target.value = '';
      return;
    }

    $activeFileUpload = $(this).closest('.wv-file-upload');

    // Show filename & "remove" button
    const $preview = $activeFileUpload.find('.wv-file-preview');
    $preview.find('.wv-file-name').text(file.name).show();
    $preview.find('.wv-remove-file').show();

    // Gather the unique IDs from data attributes
    const modalId     = $activeFileUpload.data('modal-id');
    const dropAreaId  = $activeFileUpload.data('drop-area-id');
    const cropImageId = $activeFileUpload.data('crop-image-id');

    const $modal     = $('#'+modalId);
    const $dropArea  = $('#'+dropAreaId);
    const $cropImage = $('#'+cropImageId);

    // Read the file, then show the modal + init Cropper
    const reader = new FileReader();
    reader.onload = function(evt){
      $modal.show();
      $dropArea.hide();
      $cropImage.attr('src', evt.target.result);

      initCropper($cropImage);
    };
    reader.readAsDataURL(file);
  });

  /**
   * 3) "Save Crop" button => do AJAX upload
   */
  $(document).on('click', '.wv-crop-modal button[id$="-crop-save"]', function(e){
    e.preventDefault();
    if (!cropperInstance || !$activeFileUpload) return;

    const ajaxUrl   = (window.wvCropperData && wvCropperData.ajaxUrl) ? wvCropperData.ajaxUrl : '';
    const ajaxNonce = (window.wvCropperData && wvCropperData.nonce)   ? wvCropperData.nonce : '';

    const uploadAction = $activeFileUpload.data('upload-action') || 'wv_crop_upload';
    const profileKey   = $activeFileUpload.data('profile-key')   || 'profile';

    // If you want placeholders (like "id","slug") from data-* attributes:
    const placeholders = {};
    // if ($activeFileUpload.data('product-id')) {
    //   placeholders.id = String($activeFileUpload.data('product-id'));
    // }
    if (!placeholders.id) {
      const pid = $('#wv-product-id').val();
      if (pid) placeholders.id = String(pid);
    }
    if ($activeFileUpload.data('user-id')) {
      placeholders.id = String($activeFileUpload.data('user-id'));
    }
    if ($activeFileUpload.data('slug')) {
      placeholders.slug = String($activeFileUpload.data('slug'));
    }

    const canvas     = cropperInstance.getCroppedCanvas();
    const base64data = canvas.toDataURL('image/jpeg', 0.9);

    // Hidden input that will store the final URL
    const hiddenSelector = $activeFileUpload.data('hidden-input');
    const $hiddenField   = hiddenSelector ? $(hiddenSelector) : null;

    $.ajax({
      url: ajaxUrl,
      type: 'POST',
      dataType: 'json',
      data: {
        action:       uploadAction,
        security:     ajaxNonce,
        profile_key:  profileKey,
        image_data:   base64data,
        placeholders: placeholders
      },
      success: function(resp){

        if (resp.success) {
          // Typically returns { outputs: [ {name:..., url:...}, ... ] } or { final: ... }
          let finalUrl = '';
          const outputs = resp.data.outputs || [];
          if (outputs.length) {
            finalUrl = outputs[0].url;
          }
          if (!finalUrl && resp.data.final) {
            finalUrl = resp.data.final;
          }

          // 1) Hidden input
          if (finalUrl && $hiddenField) {
            $hiddenField.val(finalUrl);
          }

            // Reflect in UI
            paintPreview($activeFileUpload, finalUrl);

          // Hide modal + show drop area again
          const modalId    = $activeFileUpload.data('modal-id');
          const dropAreaId = $activeFileUpload.data('drop-area-id');
          $('#' + modalId).hide();
          $('#' + dropAreaId).show();

          // Destroy the crop instance
          cropperInstance.destroy();
          cropperInstance = null;

        } else {
          const errMessage = (resp.data && resp.data.message) 
            ? resp.data.message 
            : 'Unknown crop error.';
          alert(errMessage);
          console.error('Crop Error:', errMessage);
        }
      },

      error: function(){
        alert('AJAX error: could not upload cropped image');
        console.error('AJAX Error: Could not upload cropped image');
      }
    });
  });

  /**
   * 4) "Cancel" => hide the modal, show drop area, destroy Cropper
   */
  $(document).on('click', '.wv-crop-modal button[id$="-crop-cancel"]', function(e){
    e.preventDefault();

    if ($activeFileUpload) {
      const modalId    = $activeFileUpload.data('modal-id');
      const dropAreaId = $activeFileUpload.data('drop-area-id');
      $('#'+modalId).hide();
      $('#'+dropAreaId).show();
    }

    if (cropperInstance) {
      cropperInstance.destroy();
      cropperInstance = null;
    }
  });

  /**
   * 5) "Remove file" => reset hidden field, revert placeholder
   */
  $(document).on('click', '.wv-remove-file', function(e){
    e.preventDefault();
    const $fileUpload = $(this).closest('.wv-file-upload');
    const hiddenSelector = $fileUpload.data('hidden-input');
    if (hiddenSelector) {
      $(hiddenSelector).val('');
    }

    // Reset placeholder + filename
    const placeholder = $fileUpload.data('placeholder') || 'https://placehold.co/300';
    $fileUpload.find('.wv-placeholder-img').attr('src', placeholder).addClass('d-none');
    $fileUpload.find('.svg-placeholder').removeClass('d-none');
    $fileUpload.find('.wv-file-name').text('No file selected');
    $(this).hide();
  });

  /**
   * 6) Optional: Drag & drop
   */
  // Over
  $(document).on('dragover', '.wv-file-drop-area', function(e){
    e.preventDefault();
    e.stopPropagation();
    $(this).addClass('wv-drag-over');
  });
  // Leave
  $(document).on('dragleave', '.wv-file-drop-area', function(e){
    e.preventDefault();
    e.stopPropagation();
    $(this).removeClass('wv-drag-over');
  });
  // Drop
  $(document).on('drop', '.wv-file-drop-area', function(e){
    e.preventDefault();
    e.stopPropagation();
    $(this).removeClass('wv-drag-over');

    const files = e.originalEvent.dataTransfer.files;
    if (!files || !files.length) return;

    const file = files[0];

     if (!ALLOWED_TYPES.includes(file.type)) {
      alert('Only JPG, PNG or WEBP files are allowed.');
      return;
    }
    if (file.size > MAX_SIZE) {
      alert('Max file size is 2 MB.');
      return;
    }
    
    $activeFileUpload = $(this).closest('.wv-file-upload');

    // Show filename & remove button
    const $preview = $activeFileUpload.find('.wv-file-preview');
    $preview.find('.wv-file-name').text(file.name).show();
    $preview.find('.wv-remove-file').show();

    // Read file & open modal
    const modalId     = $activeFileUpload.data('modal-id');
    const dropAreaId  = $activeFileUpload.data('drop-area-id');
    const cropImageId = $activeFileUpload.data('crop-image-id');

    const $modal     = $('#'+modalId);
    const $dropArea  = $('#'+dropAreaId);
    const $cropImage = $('#'+cropImageId);

    const reader = new FileReader();
    reader.onload = function(evt){
      $modal.show();
      $dropArea.hide();
      $cropImage.attr('src', evt.target.result);

      initCropper($cropImage);
    };
    reader.readAsDataURL(file);
  });

  $(document).ready(function () {
    $('.wv-file-upload').each(function () {
      const $fu   = $(this);
      const hidSel = $fu.data('hidden-input');
      if (!hidSel) return;
      const url = $(hidSel).val();
      if (url) paintPreview($fu, url);
    });
  });

})(jQuery);
