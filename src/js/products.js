/* src/js/products.js – unminified, ES5‑safe */

;(function ($, window, document) {
	'use strict';

	/*****************************************************************
	 * 0)  Global spinner + tiny ajax wrapper
	 *****************************************************************/
	const Spinner = {
		el: null,
		init() { this.el = document.getElementById('globalSpinner'); },
		show() { this.el && this.el.classList.add('active'); },
		hide() { this.el && this.el.classList.remove('active'); }
	};
	Spinner.init();

	function ajaxRequest(payload, ok, fail = () => {}) {
		Spinner.show();
		$.ajax({
			url       : payload.ajaxUrl || wvDashboardData.ajaxUrl,
			type      : 'POST',
			dataType  : 'json',
			data      : payload
		}).done(res => {
			Spinner.hide();
			(res && res.success ? ok : fail)(res);
		}).fail(err => {
			Spinner.hide();
			fail(err);
		});
	}

	/*****************************************************************
	 * 1)  Helpers
	 *****************************************************************/
	const MAX_PRODUCTS = 20;
	/* slugify similar to WP’s sanitize_title() */
	function wpSlug(str) {
		return String(str).toLowerCase()
			.normalize('NFD').replace(/[\u0300-\u036f]/g, '')
			.replace(/[^a-z0-9]+/g, '-')
			.replace(/^-+|-+$/g, '');
	}

	/* escape for HTML injection in list */
	function escHtml(str) {
		return String(str).replace(/[&<>"']/g, s => ({
			'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'
		})[s]);
	}

	if (typeof window.wvDashboardData === 'undefined') return;

	const ajaxUrl = wvDashboardData.ajaxUrl;
	const nonce   = wvDashboardData.nonce;

	const ajaxPayload = extra => ({ security: nonce, ...extra });

	/*****************************************************************
	 * 2)  DOM ready
	 *****************************************************************/
	$(function () {

		/* ──────────────────────────────────────────────────────────
		   2.1  Add‑product controls
		   ─────────────────────────────────────────────────────── */
		const $dropdownBtn  = $('#wv-add-product-dropdown-btn');
		const $dropdownMenu = $('#wv-add-product-dropdown-menu');

		$dropdownBtn.on('click', e => {
			e.preventDefault();
			$dropdownMenu.toggle();
		});

		/* wine / spirits selectable dropdown */
		$dropdownMenu.on('click', '.wv-dropdown-item', function () {
			const selectedType = $(this).data('type');   // wine | spirits
			const rawCat       = $(this).data('subcat') || '';
			const catSlug      = wpSlug(rawCat);

			$dropdownMenu.hide();

			ajaxRequest(
				ajaxPayload({ action: 'wv_create_empty_product', product_type: selectedType }),
				res => openProductModal('edit', {
					id      : res.data.product_id,
					type    : selectedType,
					category: catSlug,
					is_new  : true
				}),
				err => alert(err.data?.message || 'Error creating product')
			);
		});

		/* quick‑add buttons for Food / Other (appear conditionally in PHP) */
		$('#wv-add-food-btn').on('click', function () {
			ajaxRequest(
				ajaxPayload({ action: 'wv_create_empty_product', product_type: 'food' }),
				res => openProductModal('edit', { id: res.data.product_id, type: 'food', is_new: true }),
				err => alert(err.data?.message || 'Error')
			);
		});

		$('#wv-add-other-btn').on('click', function () {
			ajaxRequest(
				ajaxPayload({ action: 'wv_create_empty_product', product_type: 'other' }),
				res => openProductModal('edit', { id: res.data.product_id, type: 'other', is_new: true }),
				err => alert(err.data?.message || 'Error')
			);
		});

		/* ──────────────────────────────────────────────────────────
		   2.2  Product list (load / render)
		   ─────────────────────────────────────────────────────── */
		const $plist  = $('#wv-products-list');
		const $pcount = $('#wv-products-count');
		const $nav 	  = $('#wv-product-nav');
		let productsCache = [];

		function renderProducts(products) {
			const placeholder = wvDashboardData.placeholderImg || 'https://placehold.co/1024.jpg?text=W';
			const LIMIT       = 4;               // number of dummy tiles to show
			let html          = '<div class="row g-12">';

			/* ------------------------------------------------------------------
			1) REAL products (if any)
			------------------------------------------------------------------ */
			$.each(products, function (idx, p) {
				html +=
					'<div class="col-6 col-lg-3">' +
						'<div class="wv-product-item br-12 wv-bg-w p-12" data-id="' + p.id + '">' +

							/* title */
							'<div class="d-flex align-items-start">' +
								'<div class="fs-18 fw-600 mb-8 white-space-nowrap">' +
									(p.title ? escHtml(p.title) : 'Untitled') +
								'</div>' +
							'</div>' +

							/* image */
							'<div class="d-flex">' +
								'<img src="' + (p.image_url || placeholder) + '" alt="" class="img-fluid br-8">' +
							'</div>' +

							/* buttons */
							'<div class="d-flex align-items-start mt-12 gap-8 justify-content-between">' +
								'<button type="button" class="wv-button wv-button-pill wv-button-sm wv-button-info px-16 wv-btn-view">View</button>' +
								'<button type="button" class="wv-button wv-button-pill wv-button-sm wv-button-edit px-16 wv-btn-edit me-auto">Edit</button>' +
								'<button type="button" class="wv-button wv-button-pill wv-icon-button wv-button-sm wv-button-light-danger wv-btn-delete wv-px-8">' +
									'<span class="wv wv_x-40-f fs-20"></span>' +
								'</button>' +
							'</div>' +

						'</div>' +
					'</div>';
			});

			/* ------------------------------------------------------------------
			2) PLACEHOLDER tiles when user has 0 products
			------------------------------------------------------------------ */
			if (products.length === 0) {
				for (let i = 0; i < LIMIT; i++) {
					html +=
						'<div class="col-6 col-lg-3">' +
							/* ‑50% opacity & pointer‑events‑none for full tile */
							'<div class="wv-product-item br-12 wv-bg-w p-12 opacity-50 pe-none">' +

								'<div class="d-flex align-items-start">' +
									'<div class="fs-18 fw-600 mb-8 white-space-nowrap">Product title</div>' +
								'</div>' +

								'<div class="d-flex">' +
									'<div style="aspect-ratio:9/12;width:100%;">' +
										'<img src="' + placeholder + '" alt="" class="img-fluid br-8" style="object-fit:cover;width:100%;height:100%;">' +
									'</div>' +
								'</div>' +

								'<div class="d-flex align-items-start mt-12 gap-8 justify-content-between">' +
									'<button type="button" class="wv-button wv-button-pill wv-button-sm wv-button-info px-16" disabled>View</button>' +
									'<button type="button" class="wv-button wv-button-pill wv-button-sm wv-button-edit px-16 me-auto" disabled>Edit</button>' +
									'<button type="button" class="wv-button wv-button-pill wv-icon-button wv-button-sm wv-button-light-danger wv-px-8" disabled>' +
										'<span class="wv wv_x-40-f fs-20"></span>' +
									'</button>' +
								'</div>' +

							'</div>' +
						'</div>';
				}
			}

			html += '</div>';
			$plist.html(html);

			/* ------------------------------------------------------------------
			3) Counter + add‑button lock
			------------------------------------------------------------------ */
			$pcount.text(`${products.length} / ${MAX_PRODUCTS}`);

			const $addControls = $('#wv-add-product-dropdown-btn, #wv-add-food-btn, #wv-add-other-btn');
			if (products.length >= MAX_PRODUCTS) {
				$addControls.prop('disabled', true)
							.attr('title', 'Limit reached (20)').addClass('disabled');
			} else {
				$addControls.prop('disabled', false)
							.attr('title', '').removeClass('disabled');
			}

			$nav.toggleClass('wv-has-products', products.length > 0)
    			.toggleClass('wv-no-products',  products.length === 0);
		}


		function loadProducts() {
			ajaxRequest(
				ajaxPayload({ action: 'wv_get_products' }),
				res => {
					productsCache = res.data.products || [];
					renderProducts(productsCache);
				},
				err => {
					$plist.html('<p class="wv-error">' + (err.data?.message || 'Error') + '</p>');
					$pcount.text('0');
				}
			);
		}

		/* ──────────────────────────────────────────────────────────
		   2.3  Modal helpers
		   ─────────────────────────────────────────────────────── */
		const $modal      = $('#wv-product-modal');
		const $modalForm  = $('#wv-product-form');
		const $modalTitle = $('#wv-modal-title');
		const $modalClose = $('#wv-modal-close');
		const $footerBtn  = $modalForm.find('button[type="submit"]');

		/** Show only one field‑group.
		 *  @param {string}  type      wine|spirits|food|other
		 *  @param {boolean} editable  true for Add / Edit mode
		 */
		function showFieldsByType(type, editable) {
			['wine','spirits','food','other'].forEach(t => {
				const $grp = $('.wv-' + t + '-fields');

				if (t === type) {
					/* ── make visible & restore attributes ── */
					$grp.show().find('input,select,textarea').each(function () {
						const $el = $(this);
						if ($el.data('wvReq')) {                 // restore “required”
							$el.attr('required', true);
						}
						$el.prop('disabled', !editable);         // disable only in View mode
					});

				} else {
					/* ── hide & neutralise validation ── */
					$grp.hide().find('input,select,textarea').each(function () {
						const $el = $(this);
						if ($el.is('[required]')) {
							$el.data('wvReq', 1).removeAttr('required');
						}
						$el.prop('disabled', true);
					});
				}
			});
		}


		function openProductModal(mode, p = {}) {

			if (mode === 'edit') {
				const isNew = !!p.is_new;
				$('#ds-new-prod-btn').toggleClass('d-none', !isNew);
				$modalTitle.text(isNew ? 'Add product' : 'Edit product');
				$footerBtn.text(isNew ? 'Add product' : 'Save');
				$modalForm.removeClass('wv-view').addClass('wv-editable')
					.find('input,select,textarea,button[type="submit"]').prop('disabled', false);
			} else {
				$modalTitle.text('View product');
				$footerBtn.text('Save');
				$modalForm.removeClass('wv-editable').addClass('wv-view')
					.find('input,select,textarea,button[type="submit"]').prop('disabled', true);
				$modalClose.prop('disabled', false);
			}

			showFieldsByType(p.type || 'wine', mode === 'edit');

			if (typeof p.is_new !== 'undefined') {
				$modalForm.data('isNewProduct', p.is_new);
			} else {
				$modalForm.removeData('isNewProduct');
			}

			$('#wv-product-id').val(p.id || '');
			$('#wv-product-type').val(p.type || 'wine');

			/* simple inputs & checkboxes */
			$modalForm.find('input:not([type=file]):not([type=button]):not([type=submit])[name], textarea[name], select:not([multiple])[name]')
				.each(function () {
					const name = this.name;
					const val  = (p[name] !== undefined && p[name] !== null) ? p[name] : '';
					if (this.type === 'checkbox') {
						$(this).prop('checked', val == 1 || val === '1' || val === true);
					} else {
						$(this).val(val);
					}
				});

			/* multi‑selects */
			$modalForm.find('select[multiple]').each(function () {
				const $sel = $(this);
				const key  = $sel.attr('name').replace(/\[\]$/, '');
				const raw  = p[key] || [];
				const vals = Array.isArray(raw) ? raw : (typeof raw === 'string' && raw.length ? raw.split(',') : []);

				if ($sel.hasClass('select2-hidden-accessible')) $sel.select2('destroy');
				$sel.select2({ width: '100%', dropdownParent: $modal })
				    .val(vals).trigger('change');
			});

			/* image preview */
			/* ---------- image preview ---------- */
			const placeholder = wvDashboardData.placeholderImg || '';   // global fallback
			const imgUrl      = p.image_url || '';

			const $prev  = $modalForm.find('.wv-placeholder-img');
			const $svg   = $modalForm.find('.svg-placeholder');
			const $fname = $modalForm.find('.wv-file-name');
			const $rm    = $modalForm.find('.wv-remove-file');
			const $bg    = $modalForm.find('.wv-file-preview-container');

			if (imgUrl) {
				/* product already has an image */
				$prev.attr('src', imgUrl).removeClass('d-none');
				$svg.addClass('d-none');
				$bg.css('background-image', `url("${imgUrl}")`);
				$fname.text(imgUrl.split('/').pop());
				$rm.show();
			} else {
				/* brand‑new product → revert to placeholder */
				if (placeholder) $prev.attr('src', placeholder);
				$prev.addClass('d-none');
				$svg.removeClass('d-none');
				$bg.css('background-image', '');
				$fname.text('No file selected');
				$rm.hide();
			}

			$modal.show();

			/* now that the element exists in the DOM, attach ID for cropper */
			$modalForm.find('.wv-file-upload')
					  .attr('data-product-id', p.id || '');
		}

		function closeProductModal() {
			const isNew = $modalForm.data('isNewProduct') || false;
			const id    = parseInt($('#wv-product-id').val(), 10) || 0;
			const title = $('#product-title').val().trim();

			if (isNew && id && !title) {
				ajaxRequest(ajaxPayload({ action: 'wv_delete_product', id }), () => {});
			}

			$modal.hide();
			$modalForm[0].reset();
			$modalForm.removeData('isNewProduct');
			$modalForm.find('.wv-file-name').text('No file selected');
			$modalForm.find('.wv-remove-file').hide();
		}

		$modalClose.on('click', closeProductModal);
		$modal.on('click', '.wv-modal-backdrop', closeProductModal);

		/* ──────────────────────────────────────────────────────────
		   2.4  List button delegation
		   ─────────────────────────────────────────────────────── */
		$plist.on('click', '.wv-btn-view', function () {
			const id = $(this).closest('.wv-product-item').data('id');
			const p  = productsCache.find(r => r.id == id);
			if (!p) return alert('Product not found');
			openProductModal('view', p);
		});

		$plist.on('click', '.wv-btn-edit', function () {
			const id = $(this).closest('.wv-product-item').data('id');
			const p  = productsCache.find(r => r.id == id);
			if (!p) return alert('Product not found');
			openProductModal('edit', p);
		});

		$plist.on('click', '.wv-btn-delete', function () {
			if (!confirm('Delete this product?')) return;
			const id = $(this).closest('.wv-product-item').data('id');
			ajaxRequest(
				ajaxPayload({ action: 'wv_delete_product', id }),
				() => loadProducts(),
				err => alert(err.data?.message || 'Delete error')
			);
		});

		/* ──────────────────────────────────────────────────────────
		   2.5  Save / submit
		   ─────────────────────────────────────────────────────── */
		$modalForm.on('submit', function (e) {
			e.preventDefault();
			const data = new FormData(this);
			data.append('action', 'wv_save_product');
			data.append('security', nonce);

			Spinner.show();
			$.ajax({
				url        : ajaxUrl,
				type       : 'POST',
				data       : data,
				processData: false,
				contentType: false,
				dataType   : 'json'
			}).done(resp => {
				Spinner.hide();
				if (!resp.success) return alert(resp.data.message || 'Error');

				const prod = resp.data.product;

				/* If the user uploaded/cropped an image in this session,
				   copy the hidden‑field value because it already has the
				   ?ver=timestamp URL. */
				const localImg = $('#wv-product-image-hidden').val();
				if (localImg) prod.image_url = localImg;

				const idx  = productsCache.findIndex(p => p.id == prod.id); // loose match
				if (idx > -1) productsCache[idx] = prod; else productsCache.push(prod);
				renderProducts(productsCache);
				closeProductModal();
			}).fail(err => {
				Spinner.hide();
				console.error('[Products] AJAX error:', err);
				alert('AJAX error – check console');
			});
		});

		/* ------------------------------------------------------------------
			2.6  PAGE REFRESH / TAB CLOSE:  delete “ghost” new product
			------------------------------------------------------------------
			If the modal is left open (isNewProduct flag present) and the user
			reloads or navigates away BEFORE clicking “Save”, we fire a
			navigator.sendBeacon() request to remove the empty row so no
			untitled products remain in the DB.
		------------------------------------------------------------------ */
		$(window).on('beforeunload', function () {
			const isNew = $modalForm.data('isNewProduct') || false;
			const id    = parseInt($('#wv-product-id').val(), 10) || 0;
			const title = $('#product-title').val().trim();

			if (isNew && id && !title) {
				/* Build the payload just like ajaxPayload() does, but as FormData
					so sendBeacon can transmit it synchronously. */
				const fd = new FormData();
				fd.append('action',   'wv_delete_product');
				fd.append('security', nonce);
				fd.append('id',        id);

				navigator.sendBeacon(ajaxUrl, fd);
			}
		});

		/* initial list */
		loadProducts();
	});
})(jQuery, window, document);
