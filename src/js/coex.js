/* global wvDashboardData */
;(function ($, window) {
	'use strict';

	/* ──────────────────────────────────────────────────────────────
	 * Abort if localisation isn’t present
	 * ─────────────────────────────────────────────────────────── */
	if (typeof wvDashboardData === 'undefined') return;

	$(function () {

/* ───── 1. CONSTANTS & STATE ─────────────────────────────────── */
const { ajaxUrl, nonce, slots: rawSlots = 10, role = '', checkoutUrl } = wvDashboardData;
const unlimited      = parseInt(rawSlots, 10) < 0;
const totalSlots     = unlimited ? Infinity : parseInt(rawSlots, 10);
const isCoExhibitor  = role.toLowerCase().includes('co-');

const $list      = $('#wv-coex-invites-list');
const $count     = $('#wv-coex-total-count');
const $remaining = $('#coex-slots-remaining');
const $form      = $('#wv-coex-invite-form');
if (isCoExhibitor) $form.parent().hide();          // Co‑Exhibitors cannot invite

const log   = (...a) => window.console && console.debug && console.debug(...a);
const toast = (msg, ok = false) => {
	const $t = $(`<div class="wv-toast ${ok ? 'wv-toast-ok' : 'wv-toast-err'}">${msg}</div>`)
		.appendTo('body').hide().fadeIn(180);
	setTimeout(() => $t.fadeOut(180, () => $t.remove()), 4000);
};

/* ───── 2. GLOBAL SPINNER ─────────────────────────────────────── */
const Spinner = window.Spinner || {
	el  : null,
	init() { this.el = document.getElementById('globalSpinner'); },
	show() { this.el && this.el.classList.add('active'); },
	hide() { this.el && this.el.classList.remove('active'); }
};
if (!window.Spinner) { Spinner.init(); window.Spinner = Spinner; }

const ajaxRequest = (payload, ok, fail = () => {}) => {
	Spinner.show();
	$.ajax({
		url : ajaxUrl,
		type: 'POST',
		dataType: 'json',
		data: payload
	})
		.done(res  => { Spinner.hide(); (res && res.success ? ok   : fail)(res.data || res); })
		.fail(err => { Spinner.hide(); fail(err); });
};

/* ───── 3. FETCH & RENDER INVITES ─────────────────────────────── */
let inviteCache = [];

const loadInvites = () => {
	ajaxRequest(
		{ action: 'wv_get_coex_invites', security: nonce },
		res => {
			log('[CoEx] get_invites →', res);
			inviteCache = res.invites || [];
			renderInvites(inviteCache);
		},
		err => {
			$list.html(`<p class="wv-error">${err.message || 'Error fetching invites'}</p>`);
		}
	);
};

const mapState = inv => {
	if (inv.status === 'pending')  return { css: 'wv-pending',  label: 'Pending'  };
	if (inv.status === 'declined') return { css: 'wv-declined', label: 'Declined' };
	return inv.stand
		? { css: 'wv-assigned', label: 'Registered' }
		: { css: 'wv-accepted', label: 'Registered' };
};

const renderInvites = invites => {

	if (!invites.length) {
		$list.html('<p>No invites yet.</p>');
		updateCounts([]);
		return;
	}

	const html = invites.map((inv, i) => {
		const { css, label } = mapState(inv);
		const idx    = i + 1;
		const name   = inv.registered ? inv.company : inv.email;
		const avatar = inv.registered ? inv.avatar : 'https://placehold.co/120?text=%20';

		const left = `
			<div class="wv-coex-left d-flex ps-32">
				<div class="wv-coex-avatar border rounded-circle d-inline-block position-relative z-3">
					<div class="wv-coex-index">${idx}</div>
					<img class="p-8 d-block rounded-circle" src="${avatar}" width="120" height="120" alt="Avatar">
				</div>
			</div>`;

		const timeOrMail = inv.registered ? `
			<span class="wv-badge-group me-8 d-flex align-items-stretch justify-content-start">
				<span class="wv-badge wv-badge-ico-30 wv-badge-v_10 br-4 br-r-0 p-0"><i class="wv wv_account-a fs-20"></i></span>
				<span class="wv-badge wv-badge-v_10">${label}</span>
				<span class="wv-badge wv-badge-v_10 br-4 br-l-0">${inv.date_registered}</span>
			</span>` : `
			<span class="wv-badge-group me-8 d-flex align-items-stretch justify-content-start">
				<span class="wv-badge wv-badge-c_10 br-4 br-r-0 p-0"><i class="wv wv_mail-f fs-30"></i></span>
				<span class="wv-badge wv-badge-c_10 br-4 br-l-0">${inv.invited_ago}</span>
			</span>`;

		const deleteBtn = (!isCoExhibitor && inv.status !== 'accepted') ? `
			<button class="wv-coex-delete wv-button wv-icon-button wv-button-light-danger br-4 me-auto"
					data-id="${inv.id}">
				<i class="wv wv_trash fs-30"><span class="path1 opacity-0"></span><span class="path2 wv-i-r"></span></i>
			</button>` : '';

		let standCtrl = '';
		if (inv.status === 'accepted') {
			if (inv.stand) {
				standCtrl = `
					<span class="wv-badge-group ms-8 d-flex align-items-stretch justify-content-start">
						<span class="wv-badge wv-badge-wv br-4 br-r-0 p-0"><i class="wv wv_stand-assign fs-30"></i></span>
						<span class="wv-badge wv-badge-wv br-4 br-l-0">${inv.stand}</span>
					</span>`;
			} else if (!isCoExhibitor) {
				standCtrl = `
					<span class="wv-badge-group ms-8 d-flex align-items-stretch justify-content-start wv-coex-assign-stand"
						  data-id="${inv.id}" style="cursor:pointer;">
						<span class="wv-badge wv-badge-v br-4 br-r-0 p-0"><i class="wv wv_stand-assign fs-30"></i></span>
						<span class="wv-badge wv-badge-v br-4 br-l-0">Assign stand</span>
					</span>`;
			}
		}

		const statusBadge = (inv.status !== 'accepted') ? `
			<span class="wv-badge-group ms-8 d-flex align-items-stretch justify-content-start">
				<span class="wv-badge wv-badge-c_10 br-4 br-r-0 border-end wv-bc-w p-0"><i class="wv wv_pending-f fs-30"></i></span>
				<span class="wv-badge wv-badge-c_10 br-4 br-l-0">${label}</span>
			</span>` : '';

		return `
		<div class="col-12 mb-12">
			<div class="wv-coex-item d-flex w-100 br-12 shadow-sm position-relative p-12 ${css}">
				${left}
				<div class="wv-coex-right px-16 w-100 d-flex flex-column justify-content-between">
					<div class="wv-coex-top d-flex align-items-start justify-content-between w-100 py-8">
						<div class="wv-coex-name fs-32 fw-500 lh-1-2">${name}</div>
					</div>
					<div class="wv-coex-bottom d-flex align-items-start justify-content-between w-100 py-8">
						${timeOrMail}
						${deleteBtn}
						${standCtrl}
						${statusBadge}
					</div>
				</div>
			</div>
		</div>`;
	}).join('');

	$list.html(html);
	updateCounts(invites);
};

const updateCounts = invites => {
	const used = invites.filter(i => i.status !== 'declined').length;
	$count.text(used);
	$remaining.text(unlimited ? '∞' : Math.max(0, totalSlots - used));
	$('#ds-invited-counter').text(unlimited ? `Invited ${used}` : `Invited ${used}/${totalSlots}`);

	const atLimit = !unlimited && used >= totalSlots;
	$form.find('button[type="submit"]').prop('disabled', atLimit);
	$form.find('input, textarea, select').prop('disabled', atLimit);
};

/* ───── 4. INVITE FORM ───────────────────────────────────────── */
$form.on('submit', function (e) {
	e.preventDefault();

	if (!unlimited && parseInt($remaining.text(), 10) <= 0) {
		return toast('You have reached the maximum number of invitations.');
	}

	const email = $('#wv-coex-email').val().trim();
	if (!email) return toast('Please enter an e‑mail address.');
	if (!$('#wv-coex-tos').is(':checked')) return toast('Please tick the responsibility checkbox.');

	if (inviteCache.some(i => i.email.toLowerCase() === email.toLowerCase() && i.status !== 'declined')) {
		return toast('You have already invited this address.');
	}

	$.post(ajaxUrl,
		{ action: 'wv_send_coex_invite', security: nonce, email },
		resp => {
			if (resp.success) {
				this.reset();
				toast('Invitation sent.', true);
				loadInvites();
			} else {
				toast(resp.data.message || 'Error sending invite.');
			}
		},
		'json'
	).fail(() => toast('Unexpected error.'));
});

/* ───── 5. ROW ACTIONS (delete / assign stand) ──────────────── */
$list
	.on('click', '.wv-coex-delete', function () {
		const id = $(this).data('id');
		if (!confirm('Delete this pending invite?')) return;

		ajaxRequest(
			{ action: 'wv_delete_coex_invite', security: nonce, id },
			() => { toast('Invite removed.', true); loadInvites(); },
			err => toast(err.message || 'Delete failed.')
		);
	})

	.on('click', '.wv-coex-assign-stand', function () {
		const id    = $(this).data('id');
		const stand = prompt('Enter stand code (e.g. 2C/22):');
		if (!stand) return;

		ajaxRequest(
			{ action: 'wv_assign_coex_stand', security: nonce, id, stand },
			() => { toast('Stand assigned.', true); loadInvites(); },
			err => toast(err.message || 'Stand assignment failed.')
		);
	});

/* ───── 6. SLOT‑PURCHASE MODULE (Solo Exhibitor) ────────────── */
if (role === 'Solo Exhibitor') {
	const $mod = $('#wv-buy-slot-module');
	if ($mod.length) {                           // only when purchase form is present
		const NET  = 70;
		const VATR = 0.20;
		const fmt  = v => v.toLocaleString('de-DE', { minimumFractionDigits:2, maximumFractionDigits:2 }) + ' €';

		const remaining = parseInt($mod.data('remaining'), 10) || 1;
		const pid       = parseInt($mod.data('pid'), 10);

		/* disable radios above remaining, pre‑select first */
		$('input[name="slot_option"]', $mod).each(function () {
			const v = parseInt(this.value, 10);
			if (v > remaining) $(this).prop('disabled', true).parent().addClass('opacity-50');
		}).filter(':enabled').first().prop('checked', true);

		const update = () => {
			const q   = parseInt($('input[name="slot_option"]:checked', $mod).val(), 10);
			$('#wv-slot-net')  .text(fmt(NET * q));
			$('#wv-slot-vat')  .text(fmt(NET * q * VATR));
			$('#wv-slot-gross').text(fmt(NET * q * (1 + VATR)));
		};
		update();
		$('input[name="slot_option"]', $mod).on('change', update);

		$('#wv-slot-pay').on('click', function (e) {
			e.preventDefault();
			if (!$('#terms_conditions').is(':checked')) {
				return toast('Please accept the commercial terms.');
			}
			const qty = parseInt($('input[name="slot_option"]:checked', $mod).val(), 10);

			// One hop → checkout
			window.location.href = `${checkoutUrl}?coex_slot_qty=${qty}`;
		});
	}
}

/* ───── 7. BOOTSTRAP ────────────────────────────────────────── */
loadInvites();
});

/* no global click handler needed now – handled inside module init above */

})(jQuery, window);
