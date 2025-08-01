/**
 *
 * Combined front-end scripts for:
 *  1) Login Form
 *  2) Password Reset
 *  3) Two-Factor Auth (2FA)
 *  4) Profile Edit
 *  5) Multi-Step Registration
 *
 * Requires:
 *  - A localized JS object (via wp_localize_script) with:
 *      wvAddonAjax.ajaxUrl = admin_url('admin-ajax.php')
 *  - Nonces for each form, e.g., 'wv_login_nonce', 'wv_password_reset_nonce', etc.
 */

(function($){
	'use strict';
  
	/*****************************************************************
	 * 1) LOGIN FORM
	 *****************************************************************/
	$(document).on('submit', '#wv-login-form', function(e){
	  e.preventDefault();
	  const $form = $(this);
	  const email = $form.find('#wv_login_email').val() || '';
	  const pass  = $form.find('#wv_login_password').val() || '';
	  const nonce = $form.find('input[name="wv_addon_login_nonce_field"]').val() || '';
  
	  $.post(wvAddonAjax.ajaxUrl, {
		action: 'wv_addon_login',        // must match add_action('wp_ajax_nopriv_wv_login'...)
		email: email,
		password: pass,
		nonce: nonce
	  }, function(response){
		const $msg = $('#wv-login-messages');
		if(response.success){
		  $msg.html('<div class="wv-success">'+ response.data.message +'</div>');
		  // Optionally redirect
		  // window.location = '/dashboard';
		} else {
		  $msg.html('<div class="wv-error">'+ response.data.message +'</div>');
		}
	  });
	});
  
  
	/*****************************************************************
	 * 2) PASSWORD RESET FORM
	 *****************************************************************/
	$(document).on('submit', '#wv-password-reset-form', function(e){
	  e.preventDefault();
	  const $form = $(this);
	  const email = $form.find('#wv_reset_email').val() || '';
	  const nonce = $form.find('#wv_addon_password_reset_nonce_field').val() || '';
  
	  $.post(wvAddonAjax.ajaxUrl, {
		action: 'wv_addon_password_reset',
		email: email,
		nonce: nonce
	  }, function(response){
		const $msg = $('#wv-password-reset-messages');
		if(response.success){
		  $msg.html('<div class="wv-success">'+ response.data.message +'</div>');
		} else {
		  $msg.html('<div class="wv-error">'+ response.data.message +'</div>');
		}
	  });
	});
  
  
	/*****************************************************************
	 * 3) TWO-FACTOR AUTH (2FA) FORM
	 *****************************************************************/
	$(document).on('submit', '#wv-2fa-form', function(e){
	  e.preventDefault();
	  const $form = $(this);
	  const code  = $form.find('#wv_2fa_code').val() || '';
	  const nonce = $form.find('input[name="nonce"]').val() || '';
  
	  $.post(wvAddonAjax.ajaxUrl, {
		action: 'wv_addon_2fa_verify',
		code: code,
		nonce: nonce
	  }, function(response){
		const $msg = $('#wv-2fa-messages');
		if(response.success){
		  $msg.html('<div class="wv-success">'+ response.data.message +'</div>');
		} else {
		  $msg.html('<div class="wv-error">'+ response.data.message +'</div>');
		}
	  });
	});
  
  
	/*****************************************************************
	 * 4) PROFILE EDIT FORM
	 *****************************************************************/
	$(document).on('submit', '#wv-profile-form', function(e){
	  e.preventDefault();
	  const $form = $(this);
	  const formData = $form.serializeArray();
	  const nonce = $form.find('#wv_addon_profile_nonce_field').val() || '';
	  const dataObj = { action: 'wv_addon_update_profile', nonce: nonce };
  
	  formData.forEach((item) => {
		dataObj[item.name] = item.value;
	  });
  
	  $.post(wvAddonAjax.ajaxUrl, dataObj, function(response){
		const $msg = $('#wv-profile-messages');
		if(response.success){
		  $msg.html('<div class="wv-success">'+ response.data.message +'</div>');
		} else {
		  $msg.html('<div class="wv-error">'+ response.data.message +'</div>');
		}
	  });
	});
  
  
	/*****************************************************************
	 * 5) MULTI-STEP REGISTRATION
	 *    Steps: next, prev, submit; partials loaded via AJAX
	 *****************************************************************/
	$(document).ready(function() {
		// Now check after the DOM is ready
		if ($('#wv-wrap').length) {
			updateRegistrationClass();
			$(document).on('change', 'input[name="wv_profileSelection"], select[name="wv_profileSelection"]', function() {
				updateRegistrationClass();
			});
		}
	});

	function updateRegistrationClass() {
		var radioVal = $('input[name="wv_profileSelection"]:checked').val();
		var selectVal = $('select[name="wv_profileSelection"]').val();
		
		var profile = radioVal || selectVal;
		var newClass = '';
		if (profile === 'Exhibitor') {
			newClass = 'wv-exhibitor';
		} else if (profile === 'Buyer') {
			newClass = 'wv-buyer';
		} else if (profile === 'Visitor') {
			newClass = 'wv-visitor';
		}
		$('body').removeClass('wv-exhibitor wv-buyer wv-visitor').addClass(newClass);
	}

	// ===== plain‐jQuery filters =====
	function initPlainFilters(context){
		var $ctx = context || $(document);
	
		$ctx.find('.wv-filter-btn').off('click.plain').on('click.plain', function(){
		var filter = $(this).data('filter');
	
		// toggle active state
		$ctx.find('.wv-filter-btn').removeClass('active');
		$(this).addClass('active');
	
		// show/hide tags
		if(filter === 'all'){
			$ctx.find('.wv-checkbox-tag').slideDown(100);
		} else {
			$ctx.find('.wv-checkbox-tag').each(function(){
			var grp = $(this).data('group');
			if(grp === filter) {
				$(this).slideDown(100);
			} else {
				$(this).slideUp(100);
			}
			});
		}
		});
	}
	
	// initial call on page load
	$(document).ready(function(){
		initPlainFilters();
	});

  
	/**
	 * Helper: Update the progress indicator text if needed
	 * e.g. "REGISTER & CREATE ACCOUNT > EXHIBITOR" or "VISITOR"
	 */
	function updateProgressIndicator() {
	  const currentStep = $('input[name="current_step"]').val() || '1';
	  let label = 'REGISTER & CREATE ACCOUNT';
	  // Example: If user selected exhibitor, append " > EXHIBITOR"
	  // This is arbitrary - depends on how you track that selection
	  // Could parse from step data or store a global variable
	  $('#wv-progress-indicator').html(label);
	  $('#ds-step-debug .alert').text('Current Step: ' + currentStep);
	  $('#wv-reg-steps-container').attr('data-current-step', currentStep);
	}
  
	// Step navigation button (Prev, Next, Submit)
	$(document).on('click', 'button[name="navigation"]', function(e){
	  e.preventDefault();
	  const $btn     = $(this);
	  const $form    = $btn.closest('form');
	  const navValue = $btn.val(); // 'next', 'prev', 'submit'
	  const currentStep = $form.find('input[name="current_step"]').val() || '1';
  
	  // Collect step data
	  const formDataArr = $form.find(':input').serializeArray();
	  const stepData = {};
	  formDataArr.forEach(item => {
		let key = item.name;
		// handle array fields '[]'
		if(key.slice(-2) === '[]'){
		  key = key.slice(0, -2);
		  if(!Array.isArray(stepData[key])) stepData[key] = [];
		  stepData[key].push(item.value);
		} else {
		  stepData[key] = item.value;
		}
	  });
  
	  $btn.prop('disabled', true);
  
	  $.ajax({
		url: wvAddonAjax.ajaxUrl,
		type: 'POST',
		dataType: 'json',
		data: {
		  action: 'wv_register_step',
		  security:   (window.WVRegisterData ? WVRegisterData.nonce : ''),
		  current_step: currentStep,
		  navigation:  navValue,
		  data: stepData
		},
		success: function(resp){
		  $btn.prop('disabled', false);
		  if(resp.success){
			if(navValue === 'next' || navValue === 'prev'){
			  $form.find('input[name="current_step"]').val(resp.data.next_step_key);
			  $('#wv-reg-steps-container').html(resp.data.step_html);
			  updateProgressIndicator();
			  initPlainFilters();

			} else if(navValue === 'submit'){
			  // Done
			  if(resp.data.redirect){
				window.location.href = resp.data.redirect;
			  } else {
				$('#wv-reg-messages').html('<p class="wv-success">'+resp.data.message+'</p>');
			  }
			}
		  } else {
			$('#wv-reg-messages').html('<p class="wv-error">'+resp.data.message+'</p>');
		  }
		},
		error: function(){
		  $btn.prop('disabled', false);
		  $('#wv-reg-messages').html('<p class="wv-error">An unexpected error occurred.</p>');
		}
	  });
	});
  
	// (Optional) Email duplication check
	$(document).on('blur', 'input[name="wv_exhibitor_rep_email"], input[name="wv_probuyer_email"], input[name="wv_visitor_email"]', function(){
	  const email = $(this).val().trim();
	  if(!email){ return; }
	  $.post(wvAddonAjax.ajaxUrl, {
		action: 'wv_check_email',
		security: (window.WVRegisterData ? WVRegisterData.nonce : ''),
		email: email
	  }, function(resp){
		if(!resp.success){
		  $('#wv-reg-messages').html('<p class="wv-error">'+resp.data.message+'</p>');
		} else {
		  // Clear error if previously shown
		  // ...
		}
	  });
	});
  
  })(jQuery);
  