/* ------------------------------------------------------------------
 * Admin Users screen
 * 2025‑07‑10 – groups, exclude / read‑only lists, 3‑button verify
 * -----------------------------------------------------------------*/
import $       from 'jquery';
import * as bt from 'bootstrap';

/* global wvAdminUsers */
(($) => {
	'use strict';

	/* ─────────────────── FIELD LISTS ─────────────────── */

	/* ❌  never rendered, never posted (also blocked server‑side) */
	const EXCLUDE_KEYS = [
		'nickname','first_name','last_name','description','rich_editing','syntax_highlighting',
		'comment_shortcuts','admin_color','use_ssl','show_admin_bar_front','locale',
		'dismissed_wp_pointers','session_tokens','terms_conditions',
		// verify‑only flags:
		'wv_admin_verified','wv_status','wv_ex_stage1_verified','wv_ex_stage2_verified',
		'has_reserved_stand','wv_wvhb_support',
	];

	/* 👁️  rendered but disabled (read‑only) */
	const READ_ONLY_KEYS = [
		// system / role
		'wc_last_active','wv_linked_exhib','zqvz_capabilities','zqvz_user_level','last_update',
		'user_email','role','wv_profile_selection','wv_email',
		// media
		'wv_user-logo','wv_user-avatar',
	];

	/* ✅ editable fields grouped for UI  */
	const GROUPS = {
		'Company details': [
			'wv_category','wv_participationModel','wv_fieldOfWork','wv_exhibitingProducts',
			'wv_company_name','wv_company_pobRegion','wv_company_email','wv_company_city',
			'wv_company_website','wv_company_address','wv_companyDescription','wv_annual_production',
			'wv_current_stock','wv_company_idRegistryNumber','wv_company_vatRegistryNumber',
			'wv_company_iban','wv_company_domesticBank','wv_company_foreignBank',
			'wv_company_domesticAccountNumber','wv_company_foreignAccountNumber',
			'wv_company_domesticSwift','wv_company_foreignSwift','wv_socInstagram',
			'wv_socLinkedin','wv_socFacebook','wv_socX',
		],
		'Profile details': [
			'wv_firstName','wv_lastName','wv_professionalOccupation','wv_yearsOfExperience',
			'wv_nationality','wv_positionInCompany','wv_contactTelephone',
			'wv_exhibitor_rep_whatsapp','wv_exhibitor_rep_viber',
		],
		'WooCommerce (billing / shipping)': 'dynamic',   // everything starting with billing_ / shipping_
	};

	/* ─────────────────── helpers ─────────────────── */

	const FLAGS_COL   = 7;
	const SPINNER_SEL = '#globalSpinner';
	let   currentUser = 0;

	const Spinner = { show(){ $(SPINNER_SEL).removeClass('d-none'); },
	                   hide(){ $(SPINNER_SEL).addClass('d-none'); } };

	const ajax = (action, data = {}) => {
		console.log('%c[AJAX ►]', 'color:#06a', action, data);
		Spinner.show();
		return $.post(
			wvAdminUsers.ajax,
			{ action, nonce: wvAdminUsers.nonce, ...data },
			null,'json'
		).always(r => { console.log('%c[AJAX ◄]', 'color:#06a', action, r); Spinner.hide(); });
	};

	const nice = (v) => {
		if (Array.isArray(v))        { return v.join(', '); }
		if (/^https?:\/\//.test(v))  { return `<a href="${v}" target="_blank">${v}</a>`; }
		if (/^[^@]+@[^@]+$/.test(v)) { return `<a href="mailto:${v}">${v}</a>`; }
		return $('<div>').text(v).html();
	};

	const inputFor = (key, val) => {
		const ro   = READ_ONLY_KEYS.includes(key);
		const name = ro ? '' : ` name="${key}"`;
		const dis  = ro ? ' disabled' : '';

		/* boolean → checkbox */
		if (val === '1' || val === '0') {
			return `<input type="checkbox"${name} class="form-check-input"${dis} ${val === '1' ? 'checked' : ''}>`;
		}
		/* long text → textarea */
		if (String(val).length > 100) {
			return `<textarea${name} rows="3" class="form-control"${dis}>${val}</textarea>`;
		}
		return `<input type="text"${name} value="${val}" class="form-control"${dis}>`;
	};

	/* ---------- local replica of flags_for_user() ------------------- */
	const flagsHTML = m => {
		const b = [];
		if (m.wv_admin_verified === '1')     b.push('<span class="badge bg-success">Verified</span>');
		if (m.wv_ex_stage1_verified === '1') b.push('<span class="badge bg-info">Stage&nbsp;1</span>');
		if (m.wv_ex_stage2_verified === '1') b.push('<span class="badge bg-info">Stage&nbsp;2</span>');
		if (m.has_reserved_stand  === '1')   b.push('<span class="badge bg-primary">Stand</span>');
		const hb = m.wv_wvhb_support || 'NONE';
		if (hb && hb !== 'NONE' && hb !== 'Not Applyed')
			b.push(`<span class="badge bg-warning text-dark">${hb}</span>`);
		return b.join(' ');
	};


	/* ─────────────────── DataTable & filters ─────────────────── */

	const table = $('#wv-admin-users-table').DataTable({
		pageLength: 20,
		order: [[0,'desc']],
		initComplete() {
			const api = this.api();
			$('#wv-admin-filters select').each(function () {
				const $s = $(this);
				const c  = +$s.data('col');
				const vs = new Set();
				api.column(c).data().each((cell)=>{
					if (c === FLAGS_COL)
						$('<div>').html(cell).find('.badge').each((_,b)=>vs.add($(b).text().trim()));
					else
						vs.add($('<div>').html(cell).text().trim());
				});
				[...vs].sort().forEach(v=>v && $s.append(`<option>${v}</option>`));
			});
		}
	});

	$('#wv-admin-filters select').on('change',function(){
		const col=$(this).data('col'), val=$(this).val();
		const rex= col===FLAGS_COL?val:`^${$.fn.dataTable.util.escapeRegex(val)}$`;
		table.column(col).search(val?rex:'',true,false).draw();
	});

	/* ─────────────────── View/Edit modal ─────────────────── */

	const $modal = $('#wvAdminUserModal'); const modal = new bt.Modal($modal[0]);

	$('#wvAdminUserModal').on('hidden.bs.modal', () => {
		$('#wvAdminUserModal .modal-body').html('');   // discard the edited clone
	});


	$(document).on('click','.wv-view',async function(){
		currentUser=$(this).closest('tr').data('user');
		const res = await ajax('wv_admin_get_user',{user_id:currentUser});
		if(!res.success){alert(res.data);return;}

		const {profile_html,user}=res.data;
		$modal.find('.modal-title').text(`#${user.ID} — ${user.display_name}`);
		$modal.find('.modal-body').html(profile_html);
		// If you still need the quick “raw edit” form, you can append it:
		// $modal.find('.modal-body').append('<hr>'+html);
		$('#wv-admin-user-save').data('user',currentUser).show();
		modal.show();
	});

	$modal.on('submit','#wv-edit-form',e=>e.preventDefault());

	$('#wv-admin-user-save').on('click',async function(){
		const meta={};
		$('#wv-edit-form').find('[name]').each(function(){
			meta[this.name]=this.type==='checkbox'?(this.checked?'1':'0'):$(this).val();
		});
		const r=await ajax('wv_admin_save_user',{user_id:currentUser,meta});
		alert(r.success?r.data:'Error'); if(r.success){modal.hide();table.row(`[data-user="${currentUser}"]`).invalidate().draw(false);}
	});

	/* ────────────────────────────────────────────────────────────────
	*  VERIFY MODAL (updated 2025‑07‑10)
	* ────────────────────────────────────────────────────────────── */

	const VERIFY_KEYS = [
		'wv_admin_verified',
		'wv_ex_stage1_verified',
		'wv_ex_stage2_verified',
		'has_reserved_stand',
		'wv_wvhb_support',
		'wv_status'                        // ← status last
	];

	$(document).on('click', '.wv-verify', async function () {
		currentUser = $(this).closest('tr').data('user');

		const res = await ajax('wv_admin_get_user', { user_id: currentUser });
		if (!res.success) { alert(res.data); return; }

		const { verify_meta, labels, user, meta } = res.data;   // meta = full user meta
		const HINTS = {
			wv_admin_verified  : 'Final QA check by Wine Vision staff',
			wv_ex_stage1_verified : 'Stand purchased – stage 1',
			wv_ex_stage2_verified : 'Portfolio complete – stage 2',
			has_reserved_stand : 'Stands have been reserved in Step 1',
			wv_wvhb_support   : 'Hosted‑Buyer support category',
			wv_status         : 'Overall account state shown to the user'
		};
		const role = user.role;                                 // needs PHP change above

		/* keep for later look‑ups in the change handler */
		$modal.data('role', role).data('meta', meta);

		/* ---------- build modal body ---------- */
		let body = '<div class="row g-16">';
		VERIFY_KEYS.forEach(k => {
			if (!(k in verify_meta)) return;

			const lbl = labels[k] || k;
			const val = verify_meta[k];
			const typ = wvAdminUsers.editable[k];
			const hint = HINTS[k] ? `<div class="form-text">${HINTS[k]}</div>` : '';

			if (typ === 'checkbox') {
				body += `<div class="col-12 form-check form-switch form-switch-lg">
							<div class="d-block position-relative ps-12">
								<input class="form-check-input" type="checkbox"
									data-key="${k}" ${val === '1' ? 'checked' : ''} style="transform:scale(1.5);">
								<label class="form-check-label ps-12">${lbl}</label>
							</div>
						</div>${hint}`;
			} else {
				const opts = (k === 'wv_status'
						? ['Pending', 'Active', 'Disabled']
						: ['NONE', 'Category IV', 'Category III', 'Category II', 'Category I']
					)
					.map(o => `<option${o === val ? ' selected' : ''}>${o}</option>`)
					.join('');
				body += `<div class="col-12"><label class="form-label">${lbl}</label>
							<select class="form-select" data-key="${k}">${opts}</select>
						</div>`;
			}
		});
		body += '</div>';

		$modal.find('.modal-title').text(`Verify #${user.ID}`);
		$modal.find('.modal-body').html(body);
		$('#wv-admin-user-save').hide();
		modal.show();
	});

	/* ---------- utility dialog ---------- */
	const askChoice = (canMail, cb) => {
		$('#wvChoiceModal').remove();                            // reset

		const mailBtn = canMail
			? `<button type="button" class="btn btn-success w-100" data-choice="update-mail">
				✔ Update &amp; notify user
			</button>`
			: '';

		$('body').append(`
		<div class="modal fade" id="wvChoiceModal" tabindex="-1">
			<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content p-24">
				<h5>What would you like to do?</h5>
				<p class="small text-muted mb-84">
				Changes are saved instantly; e‑mails go out only if you ask for them.
				</p>

				<div class="d-grid gap-12">            <!-- buttons live in body now -->
				<button type="button" class="btn btn-primary w-100" data-choice="update">
					💾 Just update
				</button>
				${mailBtn}
				<button type="button" class="btn btn-light w-100" data-bs-dismiss="modal">
					✖ Cancel
				</button>
				</div>
			</div>
			</div>
		</div>`);

		const m = new bt.Modal('#wvChoiceModal');
		$('#wvChoiceModal').one('click', '[data-choice]', e => {
			cb($(e.currentTarget).data('choice'));
			m.hide();
		});
		m.show();
	};



	/* ---------- template resolver ---------- */
	const guessTpl = (key, val, meta, role) => {

		/* Helper: decide if current visitor is professional */
		const isProVisitor =
			role === 'visitor' &&
			String(meta.wv_participationModel || '').toLowerCase().includes('company');

		/* wv_admin_verified -------------------------------------- */
		if (key === 'wv_admin_verified' && val === '1' && role === 'exhibitor') {
			return 'exhibitor_approved';
		}

		/* wv_status ---------------------------------------------------- */
		if (key === 'wv_status') {

			if (val === 'Active') {
				if (role === 'exhibitor')            return 'exhibitor_approved';
				if (role === 'buyer')                return (meta.wv_wvhb_support || 'NONE') === 'NONE'
														? 'buyer_approved'
														: 'buyer_approved_hosted';
				if (isProVisitor)                    return 'provisitor_approved';
				if (role === 'visitor')              return 'visitor_approved';

			} else if (val === 'Pending') {
				if (role === 'exhibitor')            return 'exhibitor_validating';
				if (role === 'buyer')                return 'buyer_validating';
				if (isProVisitor)                    return 'provisitor_validating';
				if (role === 'visitor')              return 'visitor_evaluating';

			} else if (val === 'Disabled') {
				if (role === 'exhibitor')            return 'exhibitor_rejected';
				if (role === 'buyer')                return 'buyer_rejected';
				if (isProVisitor)                    return 'provisitor_rejected';
				if (role === 'visitor')              return 'visitor_rejected';
			}
		}

		/* has_reserved_stand ------------------------------------------ */
		if (key === 'has_reserved_stand' && val === '1' && role === 'exhibitor') {
			const model = String(meta.wv_participationModel || '').toLowerCase();
			return model.includes('head')
				? 'exhibitor_stands_reserved'
				: 'exhibitor_stand_reserved';
		}

		/* every other key → no mail */
		return '';
	};


	/* ---------- save / (optionally) mail ---------- */
	$modal.on('change', '[data-key]', function () {

		const key = $(this).data('key');
		const val = $(this).is(':checkbox') ? (this.checked ? '1' : '0') : $(this).val();

		const role = $modal.data('role');
		const meta = { ...$modal.data('meta'), [key]: val };          // local copy after change

		const tpl = guessTpl(key, val, meta, role);                    // slug or ''

		const ctl = this;
		const oldVal = ctl.type === 'checkbox' ? (ctl.defaultChecked ? '1' : '0') : $(ctl).data('old') || $(ctl).prop('defaultValue') || '';
		askChoice(!!tpl, async (choice) => {
			if (choice === 'cancel') {
				/* --- revert UI --------------------------------------- */
				if (ctl.type === 'checkbox') ctl.checked = oldVal === '1';
				else                         $(ctl).val(oldVal);
				return;
			}
			if (choice === 'update-mail' && !tpl) choice = 'update';   // safety

			/* 1) save ------------------------------------------------------- */
			await ajax('wv_admin_save_user', { user_id: currentUser, meta: { [key]: val } });

			/* 1b) fetch fresh meta for row repaint --------------------- */
			const ref = await ajax('wv_admin_get_user', { user_id: currentUser });
			if (ref.success) {
				const fresh  = ref.data.meta;
				const $row   = $(`[data-user="${currentUser}"]`);
				const dtRow  = table.row($row);

				// Flags  = column 6 ; Status = column 7
				const rowData = dtRow.data();
				rowData[7] = flagsHTML(fresh);
				rowData[8] = fresh.wv_status || 'Pending';
				dtRow.data(rowData).invalidate().draw(false);
			}

			/* 2) mail ------------------------------------------------------- */
			if (choice === 'update-mail') {
				await ajax('wv_admin_send_notice', { user_id: currentUser, template: tpl });
			}

			/* 3) refresh row & cache --------------------------------------- */
			$modal.data('meta', meta);
			table.row(`[data-user="${currentUser}"]`).invalidate().draw(false);
		});
	});


	/* ─────────────────── delete & mail ─────────────────── */

	$(document).on('click','.wv-disable',function(){
		if(!confirm('Delete this user permanently?'))return;
		const id=$(this).closest('tr').data('user');
		ajax('wv_admin_delete_user',{user_id:id}).then(()=>table.row(`[data-user="${id}"]`).remove().draw(false));
	});

	/* ─────────────────── Notify modal ▲ ─────────────────── */
		const notifyModal = new bt.Modal('#wvAdminNotifyModal');
		let   notifyUser  = 0;

		/* ▲ 5 new controls for the “custom” branch */
		const customFieldsHtml = `
			<div id="wv-custom-fields">
				<div class="mb-8">
					<label class="form-label fw-semibold mb-0">Subject</label>
					<input type="text" id="wv-mail-subject" class="form-control">
				</div>
				<div class="mb-8">
					<label class="form-label fw-semibold mb-0">Title (large heading)</label>
					<input type="text" id="wv-mail-title" class="form-control">
				</div>
				<div class="mb-8">
					<label class="form-label fw-semibold mb-0">Main message (HTML allowed)</label>
					<textarea id="wv-mail-html" rows="6" class="form-control"></textarea>
				</div>
				<div class="mb-8">
					<label class="form-label fw-semibold mb-0">Note (small print)</label>
					<textarea id="wv-mail-note" rows="3" class="form-control"></textarea>
				</div>
				<div class="mb-8">
					<label class="form-label fw-semibold mb-0">Button text</label>
					<input type="text" id="wv-mail-btn-text" class="form-control" value="Go to website">
				</div>
				<div class="mb-8">
					<label class="form-label fw-semibold mb-0">Button link</label>
					<input type="url"  id="wv-mail-btn-link" class="form-control" value="${location.origin}">
				</div>
				<hr>
			</div>`;
		$('#wvAdminNotifyModal .modal-body').append(customFieldsHtml);

	

		$(document).on('click', '.wv-notify', function () {
			notifyUser = $(this).closest('tr').data('user');
			const email = $(this).closest('tr').data('email') || '';
			$('#wv-notify-email').text(email);

			$('#wv-custom-fields').removeClass('d-none');          // always show fields
			$('#wv-mail-subject,#wv-mail-html,#wv-mail-note').val('');
			notifyModal.show();
		});

		$('#wv-send-notify').on('click', async () => {

			let payload;
			payload = {
				user_id   : notifyUser,
				template  : 'custom',
				subject   : $('#wv-mail-subject').val().trim(),
				title     : $('#wv-mail-title').val().trim(),
				html      : $('#wv-mail-html').val().trim(),
				custom_body : $('#wv-mail-html').val().trim(),
				note      : $('#wv-mail-note').val().trim(),
				btn_text  : $('#wv-mail-btn-text').val().trim(),
				btn_link  : $('#wv-mail-btn-link').val().trim(),
			};

			const r = await ajax('wv_admin_send_notice', payload);
			alert(r.success ? (r.data || 'Sent.') : (r.data || 'Mail error'));
			if (r.success) notifyModal.hide();
		});

		const $standModal = $('#wvStandOverviewModal');
		const standModal  = new bt.Modal($standModal[0]);

		/* ───────── Stand overview button ───────── */
		$(document).on('click', '.wv-stands', async function () {
			const uid = $(this).closest('tr').data('user');
			const company = $(this).closest('tr').data('company');
			const res = await ajax('wv_admin_get_stands', { user_id: uid });
			if (res.success) {
				$standModal.find('.modal-title').text(`Stand Overview | ${uid} | ${company}`);
				$standModal.find('.modal-body').html(res.data);
				standModal.show();
			} else {
				alert(res.data || 'No stand information available.');
			}
		});

	$('#wv-export-users').on('click', () => {
		const url = wvAdminUsers.ajax +
			'?action=wv_admin_export_users_csv' +
			'&nonce=' + encodeURIComponent( wvAdminUsers.nonce );
		window.location.href = url;          // triggers the download
	});




})(jQuery);
