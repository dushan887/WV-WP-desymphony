/* global wvDashboardData */
;(function ($, window, document) {
	'use strict';

	/* Abort when not on dashboard --------------------------------------- */
	if (typeof wvDashboardData === 'undefined') return;

	/**********************************************************************
	 * 1)  Global spinner + ajax helper
	 *********************************************************************/
	const Spinner = {
		el  : null,
		init()  { this.el = document.getElementById('globalSpinner'); },
		show() { this.el && this.el.classList.add('active'); },
		hide() { this.el && this.el.classList.remove('active'); }
	};
	Spinner.init();

	/**
	 * Ajax wrapper that automatically toggles the global spinner.
	 * Accepts both `FormData` and plain objects.
	 *
	 * @param {FormData|Object} payload
	 * @param {Function} success
	 * @param {Function} fail
	 */
	const ajaxRequest = (payload, success = () => {}, fail = () => {}) => {
		Spinner.show();
		$.ajax({
			url        : wvDashboardData.ajaxUrl,
			type       : 'POST',
			data       : payload,
			processData: !(payload instanceof FormData),
			contentType: (payload instanceof FormData) ? false :
			             'application/x-www-form-urlencoded; charset=UTF-8',
			dataType   : 'json'
		})
			.done(res  => { Spinner.hide(); (res && res.success ? success : fail)(res.data || res); })
			.fail(xhr  => { Spinner.hide(); fail({ message: xhr.statusText, ...xhr }); });
	};

	/**********************************************************************
	 * 2)  Editable profile sections
	 *********************************************************************/
	$(function () {

		/* --------------------------------------------------------------
		 * Toggle edit mode
		 * ------------------------------------------------------------*/
		$(document).on('click', '.wv-form-section .edit-toggle', function (e) {
			e.preventDefault();

			const $btn  = $(this);
			const $form = $btn.closest('.wv-form-section');

			if ($form.hasClass('wv-editable')) {
				$form.trigger('submit');                                       // save
			} else {
				$form.find('input,textarea,select').not('[type="hidden"]')
				     .prop('disabled', false);
				$form.addClass('wv-editable');
				$btn.text('Done');
			}
		});

		/* --------------------------------------------------------------
		 * Submit section (AJAX)
		 * ------------------------------------------------------------*/
		$(document).on('submit', '.wv-form-section', function (e) {
			e.preventDefault();

			const $form = $(this);
			const $btn  = $form.find('.edit-toggle');
			const fd    = new FormData(this);

			/* Section key — prefer explicit data‑section="" override ------ */
			let section = $form.data('section');
			if (!section) {
				section = ($form.attr('id') || '')
					.replace(/^wv\-/, '')        // wv-company-info-form → company-info-form
					.replace(/\-form$/, '')      // company-info-form   → company-info
					.replace(/-/g, '_');         // company-info        → company_info
			}

			fd.append('action',   'wv_addon_update_profile_section');
			fd.append('security', wvDashboardData.nonce);
			fd.append('section',  section);

			$btn.prop('disabled', true);

			ajaxRequest(
				fd,

				/* ---------- SUCCESS ---------- */
				data => {
					let $msg = $form.find('.wv-form-message');
					if (!$msg.length) {
						$msg = $('<div class="wv-form-message"></div>').prependTo($form);
					}

					$msg.html(`<div class="wv-success">${data.message}</div>`);
					$form.find('input,textarea,select').not('[type="hidden"]').prop('disabled', true);
					$form.removeClass('wv-editable').find('.edit-toggle').text('Edit');
					$btn.prop('disabled', false);
				},

				/* ---------- ERROR ---------- */
				err => {
					let $msg = $form.find('.wv-form-message');
					if (!$msg.length) {
						$msg = $('<div class="wv-form-message"></div>').prependTo($form);
					}

					$msg.html(`<div class="wv-error">${err.message || 'Error'}</div>`);
					$btn.prop('disabled', false);
				}
			);
		});
	});

})(jQuery, window, document);
