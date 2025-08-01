/* global wvAddonAjax, WVRegisterData, wvCartData */
(function ($, window, document) {
	'use strict';

	/*****************************************************************
	 * Spinner + ajaxRequest
	 *****************************************************************/
	const Spinner = {
		el: null,
		init() { this.el = document.getElementById('globalSpinner'); },
		show() { this.el && this.el.classList.add('active'); },
		hide() { this.el && this.el.classList.remove('active'); }
	};
	Spinner.init();

	const ajaxRequest = (payload, ok, fail = () => {}) => {
		Spinner.show();
		$.ajax({
			url: wvAddonAjax.ajaxUrl,
			type: 'POST',
			dataType: 'json',
			data: payload
		})
			.done(res => { Spinner.hide(); (res && res.success ? ok : fail)(res.data || res); })
			.fail(err => { Spinner.hide(); fail(err); });
	};

	/*****************************************************************
	 * 1) LOGIN
	 *****************************************************************/
	$(document).on('submit', '#wv-login-form', function (e) {
		e.preventDefault();
		const $f = $(this);
		ajaxRequest({
			action: 'wv_addon_login',
			email: $f.find('#wv_login_email').val() || '',
			password: $f.find('#wv_login_password').val() || '',
			nonce: $f.find('[name="wv_addon_login_nonce_field"]').val() || ''
		}, d => {
			$('#wv-login-messages').html(`<div class="wv-success">${d.message}</div>`);
			if (d.redirect) window.location.href = d.redirect;
		}, d => {
			$('#wv-login-messages').html(`<div class="wv-error">${d.message}</div>`);
		});
	});

	/*****************************************************************
	 * 2) PASSWORD RESET
	 *****************************************************************/
	$(document).on('submit', '#wv-password-reset-form', function (e) {
		e.preventDefault();
		const $f = $(this);
		ajaxRequest({
			action: 'wv_addon_password_reset',
			email: $f.find('#wv_reset_email').val() || '',
			nonce: $f.find('#wv_addon_password_reset_nonce_field').val() || ''
		}, d => {
			$('#wv-password-reset-messages').html(`<div class="wv-success">${d.message}</div>`);
		}, d => {
			$('#wv-password-reset-messages').html(`<div class="wv-error">${d.message}</div>`);
		});
	});

	/*****************************************************************
	 * 3) 2FA
	 *****************************************************************/
	$(document).on('submit', '#wv-2fa-form', function (e) {
		e.preventDefault();
		const $f = $(this);
		ajaxRequest({
			action: 'wv_addon_2fa_verify',
			code: $f.find('#wv_2fa_code').val() || '',
			nonce: $f.find('[name="nonce"]').val() || ''
		}, d => {
			$('#wv-2fa-messages').html(`<div class="wv-success">${d.message}</div>`);
		}, d => {
			$('#wv-2fa-messages').html(`<div class="wv-error">${d.message}</div>`);
		});
	});

	/*****************************************************************
	 * 4) PROFILE EDIT
	 *****************************************************************/
	$(document).on('submit', '#wv-profile-form', function (e) {
		e.preventDefault();
		const $f = $(this);
		const payload = {
			action: 'wv_addon_update_profile',
			nonce: $f.find('#wv_addon_profile_nonce_field').val() || ''
		};
		$f.serializeArray().forEach(({ name, value }) => { payload[name] = value; });
		ajaxRequest(payload, d => {
			$('#wv-profile-messages').html(`<div class="wv-success">${d.message}</div>`);
		}, d => {
			$('#wv-profile-messages').html(`<div class="wv-error">${d.message}</div>`);
		});
	});

	/*****************************************************************
	 * 5) MULTI-STEP REGISTRATION
	 *****************************************************************/
	const setProfileClass = () => {

		/* NEW – invited flow: force Exhibitor skin  ---------------- */
		if (window.WVRegisterData && WVRegisterData.coex_token) {
			$('body').removeClass('wv-exhibitor wv-buyer wv-visitor')
					.addClass('wv-exhibitor');
			return;                       // nothing else to check
		}
		/* ---------------------------------------------------------- */

		const p = $('input[name="wv_profileSelection"]:checked').val()
				|| $('select[name="wv_profileSelection"]').val() || '';

		$('body').removeClass('wv-exhibitor wv-buyer wv-visitor')
				.addClass(p ? `wv-${p.toLowerCase()}` : '');
	};

	$(document).ready(setProfileClass)
		.on('change',
			'input[name="wv_profileSelection"], select[name="wv_profileSelection"]',
			setProfileClass);

	const syncProgress = step => {
		// $('#wv-progress-indicator').text('REGISTER & CREATE ACCOUNT');
		$('#ds-step-debug .alert').text(`Current Step: ${step}`);
		$('#wv-reg-steps-container').attr('data-current-step', step);
	};

	/* ---------------------------------------------------------------
	*  Simple tabbed tag-filter  (no other dependencies)
	* --------------------------------------------------------------*/
	const initPlainFilters = ctx => {
		const $scope = ctx || $(document);

		const $btns = $scope.find('.wv-filter-btn');   // the top tabs
		const $tags = $scope.find('.wv-checkbox-tag'); // individual tag chips

		/* switch to a given group */
		const showGroup = grp => {
			$btns.removeClass('active')
				.filter(`[data-filter="${grp}"]`).addClass('active');

			$tags.hide()
				.filter(`[data-group="${grp}"]`).show();
		};

		/* 1 — clicking on a tab */
		$btns.off('click.tabs').on('click.tabs', function () {
			showGroup($(this).data('filter'));
		});

		/* 2 — initial state: always open on the first tab */
		if ($btns.length) {
			showGroup($btns.first().data('filter'));
		}
	};

	/* run once on first page load,
	re-run after every Ajax step injection */
	$(document).ready(() => initPlainFilters());


	/**
	 * Prefill all form fields that belong to the current step.
	 * @param {Object} data – key → value  (value may be array for multi-select)
	 * @param {jQuery} ctx  – scope to search in (defaults to whole document)
	 */
	const applyPrefill = (data = {}, ctx = $(document)) => {
		Object.entries(data).forEach(([name, val]) => {

			/* ---------- multi-value: checkboxes | multi-select ---------- */
			if (Array.isArray(val)) {
				val.forEach(v => {
					ctx.find(`[name="${name}[]"][value="${v}"]`).prop('checked', true);
					ctx.find(`[name="${name}"][value="${v}"]`).prop('checked', true);
					ctx.find(`[name="${name}"] option[value="${v}"]`).prop('selected', true);
				});

				/* fire change so Select2 / custom widgets refresh */
				ctx.find(`[name="${name}"],[name="${name}[]"]`).trigger('input').trigger('change');
				return;
			}

			/* ---------- single-value ---------- */
			const $el = ctx.find(`[name="${name}"],[name="${name}[]"]`);
			if (!$el.length) return;

			if ($el.hasClass('wv-phone-input-field')) {
				const iti = getItiInstance($el[0]);
				iti && iti.setNumber(val);
			}

			if ($el.is(':checkbox,:radio')) {
				$el.filter(`[value="${val}"]`)
				.prop('checked', true)
				.trigger('input')
				.trigger('change');
			} else {                       // input, textarea, single select
				$el.val(val)
				.trigger('input')
				.trigger('change');
			}
		});
	};



	/* ===========================================================
   STEP NAVIGATION   —   Next | Prev | Submit
   ========================================================== */

	/**
	 * Helper – returns the intl-tel-input instance for a node
	 * (v23 “globals” build). ­Null if not initialised.
	 */
	function getItiInstance(el) {
	return window.intlTelInputGlobals &&
			window.intlTelInputGlobals.getInstance
			? window.intlTelInputGlobals.getInstance(el)
			: null;
	}

	$(document).on('click', 'button[name="navigation"]', function (e) {
	e.preventDefault();

	const $btn  = $(this);
	const dir   = $btn.val();                 // 'next' | 'prev' | 'submit'
	const $form = $btn.closest('form');

	/* ---------- reset previous UI state -------------------- */
	if (dir !== 'prev') {
		$form.find('.is-invalid').removeClass('is-invalid');
	}
	$('#wv-reg-messages').empty();

	/* ========================================================
		1) PHONE MERGE + VALIDATION (intl-tel-input)
		======================================================= */
		let badPhone = false;

		$form.find('.wv-phone-input-field').each(function () {
			const iti = getItiInstance(this);
			if (!iti) return;                       // not initialised – skip

			/* 1a – validate */
			if (!iti.isValidNumber()) {
			badPhone = true;
			return;
			}

			/* 1b – overwrite THIS field with the full E.164 number */
			const full = iti.getNumber().replace(/\s+/g, '');
			$(this).val(full);              // keeps name="wv_contactTelephone"
			iti.setNumber(full);            // ensure flag stays correct
		});

		if (badPhone) {
			alert('Please enter a valid phone number.');
			return;                                   // stop here – don’t AJAX
		}

	/* ========================================================
		2) COLLECT STEP DATA
		======================================================= */
	const stepKey  = $form.find('[name="current_step"]').val() || '1';
	const stepData = {};

	$form.serializeArray().forEach(({ name, value }) => {
		if (name.endsWith('[]')) {
		const base = name.slice(0, -2);
		(stepData[base] = stepData[base] || []).push(value);
		} else {
		stepData[name] = value;
		}
	});

	/* ========================================================
		3) AJAX CALL
		======================================================= */
	$btn.prop('disabled', true);

	ajaxRequest(
		{
		action      : 'wv_register_step',
		security    : (window.WVRegisterData ? WVRegisterData.nonce : ''),
		current_step: stepKey,
		navigation  : dir,
		data        : stepData,
		coex_token  : (window.WVRegisterData ? WVRegisterData.coex_token : '')
		},

		/* ---------- SUCCESS ---------- */
		resp => {
		$btn.prop('disabled', false);

		if (dir === 'next' || dir === 'prev') {
			$form.find('[name="current_step"]').val(resp.next_step_key);
			$('#wv-reg-steps-container').html(resp.step_html);
			applyPrefill(resp.prefill, $('#wv-reg-steps-container'));
			syncProgress(resp.next_step_key);
			initPlainFilters($('#wv-reg-steps-container'));
			if (typeof loadCountryDropdown === 'function') {
			loadCountryDropdown($('#wv-reg-steps-container'));
			}
			if (typeof initializeIntlTelInputs === 'function') {
			initializeIntlTelInputs($('#wv-reg-steps-container')[0]);
			}
			$(document).trigger('wv_step_loaded', $('#wv-reg-steps-container'));
		} else {                               // submit
			if (resp.redirect) {
			window.location.href = resp.redirect;
			} else {
			$('#wv-reg-messages').html(`<p class="wv-success">${resp.message}</p>`);
			}
		}
		},

		/* ---------- ERROR ---------- */
		err => {
		$btn.prop('disabled', false);
		$('#wv-reg-messages').html(`<p class="wv-error">${err.message || 'Error'}</p>`);

		if (dir !== 'prev' && Array.isArray(err.fields)) {
			err.fields.forEach(slug => {
			$form.find(`[name="${slug}"],[name="${slug}[]"]`).addClass('is-invalid');
			});
		}
		}
	);
	});




	/* Email duplication check */
	$(document).on('blur',
		'input[name="wv_exhibitor_rep_email"], input[name="wv_probuyer_email"], input[name="wv_visitor_email"]',
		function () {
			const email = $(this).val().trim();
			if (!email) return;
			ajaxRequest({
				action: 'wv_check_email',
				security: (window.WVRegisterData ? WVRegisterData.nonce : ''),
				email
			}, () => { }, d => {
				$('#wv-reg-messages').html(`<p class="wv-error">${d.message}</p>`);
			});
		});

	/*****************************************************************
	 * 6) CONFIRM STAND (Sequential add-on updates)
	 *****************************************************************/
	$(document).on('click.ds-confirm-stand', '.ds-confirm-stand', function (e) {
		e.preventDefault();
		const cartKey = $(this).data('cart-key');
		const $card = $(this).closest('.card');
		const addons = [];
		$card.find('.ds-addon-qty').each(function () {
			const $q = $(this);
			const slug = $q.data('addon-slug');
			const price = parseFloat($q.data('addon-price'));
			const qty = $card.find(`.ds-addon-check[data-addon-slug="${slug}"]`).prop('checked')
				? (parseInt($q.val(), 10) || 1) : 0;
			addons.push({ slug, price, qty });
		});

		const run = (i, html = '') => {
			if (i >= addons.length) {
				$('#stand-cart-container').html(html);
				$card.find('.collapse').collapse('hide');
				return;
			}
			const ad = addons[i];
			ajaxRequest({
				action: 'ds_update_stand_addon',
				nonce: wvCartData.nonce,
				cart_key: cartKey,
				addon_slug: ad.slug,
				addon_price: ad.price,
				addon_qty: ad.qty
			}, d => run(i + 1, d.html), () => alert('Add-on error'));
		};
		run(0);
	});

	/**
	 * Core toggler.
	 * @param {HTMLInputElement} input
	 * @param {HTMLElement} icon
	 */
	const flip = (input, icon) => {
		const isPwd = input.type === 'password';
		input.type  = isPwd ? 'text' : 'password';

		/*  show ⇄ hide  --------------------------------------------------------- */
		if (icon) {
			// support both old (.shown/.hidden) and new (.wv_show/.wv_hide) names
			icon.classList.toggle('shown',   isPwd);
			icon.classList.toggle('hidden',  !isPwd);
			icon.classList.toggle('wv_show', isPwd);
			icon.classList.toggle('wv_hide', !isPwd);

			// update aria‑label for a11y
			icon.setAttribute('aria-label', isPwd ? 'Hide password' : 'Show password');
		}
	};

	/* 1) support legacy inline onclick="togglePassword('id')" */
	window.togglePassword = id => {
		const input = document.getElementById(id);
		if (input) flip(input, input.parentElement.querySelector('.wv-toggle-password'));
	};

	/* 2) preferred: add .wv-toggle-password and data-target="field_id" */
	document.addEventListener('click', e => {
		const btn = e.target.closest('.wv-toggle-password');
		if (!btn) return;

		const id    = btn.getAttribute('data-target') || btn.getAttribute('onclick')?.match(/['"]([^'"]+)['"]/)?.[1];
		const input = id ? document.getElementById(id) : null;
		if (input) flip(input, btn);
	});

	const lockEmail = ctx => {
		if (!window.WVRegisterData || !WVRegisterData.coex_email) return;
		(ctx || $(document))
			.find('input[name="wv_email"]')
			.val(WVRegisterData.coex_email)
			.attr('readonly', true)
			.addClass('is-readonly');
	};

	/* run once and after every step injection */
	$(document).ready(() => lockEmail());
	$(document).on('wv_step_loaded', (e, ctx) => lockEmail(ctx));

})(jQuery, window, document);
