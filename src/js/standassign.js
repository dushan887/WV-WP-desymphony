/* eslint-env jquery */
;(function ($, window, document) {
  'use strict';

  /* ────────────────────────────────────────────────────────────────
   * Quick guard – run only on the profile/stands page
   * ───────────────────────────────────────────────────────────── */
  if (typeof wvStandUsers   === 'undefined' ||
      typeof wvUserStands   === 'undefined' ||
      typeof wvDashboardData=== 'undefined') {
    return;
  }

  /* ────────────────────────────────────────────────────────────────
   * 0.  GLOBAL SPINNER + AJAX WRAPPER
   *     (same pattern as dashboard.js)
   * ───────────────────────────────────────────────────────────── */
  const Spinner = {
    el  : null,
    init()  { this.el = document.getElementById('globalSpinner'); },
    show() { this.el && this.el.classList.add('active'); },
    hide() { this.el && this.el.classList.remove('active'); }
  };
  Spinner.init();

  /** Promise‑based helper that toggles the spinner automatically */
  const ajax = (payload) => {
    Spinner.show();
    return $.post(
      wvDashboardData.ajaxUrl,
      { security: wvDashboardData.nonce, ...payload },
      null,
      'json'
    ).always(() => Spinner.hide());
  };

  /** Tiny toast */
  const toast = (msg, ok = false) =>
    $('<div/>', { class: 'wv-toast ' + (ok ? 'wv-toast-ok' : 'wv-toast-err'),
                  text : msg })
      .appendTo('body').hide().fadeIn(140)
      .delay(3400).fadeOut(140, function () { $(this).remove(); });

  /* ────────────────────────────────────────────────────────────────
   * 1.  STATE
   * ───────────────────────────────────────────────────────────── */
  let selectedStandId = 0;                // Woo product‑ID
  let selectedUserId  = 0;                // WP user‑ID

  const standCapCache    = Object.create(null); // pid → capacity
  const standAssignCache = Object.create(null); // pid → [userIDs]

  /* ────────────────────────────────────────────────────────────────
   * 1a.  Mirror helper – keep wvUserStands.byHall in sync
   * ───────────────────────────────────────────────────────────── */
  function syncStandRow (pid, users = [], capacity = 1) {
    Object.values(wvUserStands.byHall).forEach(arr => {
      arr.forEach(st => {
        if (parseInt(st.pid, 10) === parseInt(pid, 10)) {
          st.assigned_users = users;
          st.capacity       = capacity;
        }
      });
    });
  }

  /* ────────────────────────────────────────────────────────────────
   * 2.  DOM
   * ───────────────────────────────────────────────────────────── */
  const $assignBtn = $('#wv-assign-stand');
  const $shareBtn  = $('#wv-share-stand');
  const $removeBtn = $('#wv-remove-stand');

  const $selectBox = $('#ds-assign-select');
  const $selectVal = $selectBox.find('.selectBox__value');
  const $menu      = $selectBox.find('.dropdown-menu');

  /* ────────────────────────────────────────────────────────────────
   * 3.  BUTTON VISIBILITY
   * ───────────────────────────────────────────────────────────── */
  function refreshButtons () {

    if (!selectedStandId) {
      $assignBtn.addClass('d-none');
      $shareBtn .addClass('d-none');
      $removeBtn.addClass('d-none');
      return;
    }

    const users   = standAssignCache[selectedStandId] || [];
    const cap     = standCapCache   [selectedStandId] || 1;

    const onStand = users.includes(selectedUserId);
    const free    = cap - users.length;

    $assignBtn.toggleClass('d-none',
      !(selectedUserId && !onStand && free > 0 && users.length === 0));

    $shareBtn .toggleClass('d-none',
      !(selectedUserId && !onStand && free > 0 && users.length  > 0));

    $removeBtn.toggleClass('d-none', !onStand);
  }

  /* ────────────────────────────────────────────────────────────────
   * 4.  BUILD DROPDOWN
   * ───────────────────────────────────────────────────────────── */
  function buildDropdown () {
    $menu.empty();
    wvStandUsers.forEach(u => {
      if (!u.is_head && !u.accepted) return; // only accepted Co‑Ex

      // Pull assigned stand numbers from wvUserStands.byHall by matching user ID
      let assignedToArr = [];
      Object.values(wvUserStands.byHall).forEach(hallArr => {
        hallArr.forEach(stand => {
          if (Array.isArray(stand.assigned_users) && stand.assigned_users.includes(u.id)) {
            assignedToArr.push($('<span/>').text(`${stand.hall}/${stand.no}`)[0].outerHTML);
          }
        });
      });

      let assignedToStr = '';
      if (assignedToArr.length) {
        assignedToStr = assignedToArr.join(', ');
      }

      const label = (u.is_head ? '(Me) ' : '') + u.name + (assignedToStr ? ' ' + assignedToStr : '');

      $('<div/>', {
        class    : 'dropdown-item',
        'data-id': u.id,
        html     : label
      }).appendTo($menu);
    });
  }

  /* ────────────────────────────────────────────────────────────────
   * 5.  CUSTOM‑SELECT OPEN / CLOSE
   * ───────────────────────────────────────────────────────────── */
  $selectBox.on('click', function (e) {
    if ($(e.target).hasClass('dropdown-item')) return;
    $(this).toggleClass('show');
    $menu.toggleClass('show');
  });

  $(document).on('click', e => {
    if (!$(e.target).closest('#ds-assign-select').length) {
      $selectBox.removeClass('show');
      $menu.removeClass('show');
    }
  });

  /* ────────────────────────────────────────────────────────────────
   * 6.  SVG STAND CLICK
   * ───────────────────────────────────────────────────────────── */
  $(document).on('click', '.ds-stand', function () {

    /* Lazy‑fill pid/size/no attributes once */
    if (!$(this).data('pid')) {
      const m = (this.id || '').match(/^wv_hall_(.+)_(\d+)$/);
      if (!m) return;

      const hall = m[1];
      const no   = parseInt(m[2], 10);

      const row = (wvUserStands.byHall[hall] || [])
                    .find(r => parseInt(r.no,10) === no);
      if (!row) return;

      $(this).data({ pid: row.pid, size: row.size, no: row.no });
    }

    selectedStandId = parseInt($(this).data('pid'), 10);

    $('.ds-stand').removeClass('active');
    $(this).addClass('active');

    // Get the stand number, remove any non-digit characters, then set the text
    const standNo = String($(this).data('no')).replace(/\D/g, '');
    $('#selected-stand-number').text(standNo);
    $('#selected-stand-size').text($(this).data('size'));
    $('#selected-stand-box').removeClass('d-none');
    $('#ds-assign-select').removeClass('d-none');

    fetchStandState(selectedStandId).then(refreshButtons);
  });

  /* ────────────────────────────────────────────────────────────────
   * 7.  DROPDOWN PICK
   * ───────────────────────────────────────────────────────────── */
  $menu.on('click', '.dropdown-item', function () {
    selectedUserId = parseInt($(this).data('id'), 10);
    $selectVal.text($(this).text());
    $selectBox.removeClass('open');
    $menu.removeClass('show');
    refreshButtons();
  });

  /* ────────────────────────────────────────────────────────────────
   * 8.  ASSIGN / SHARE   (same handler)
   * ───────────────────────────────────────────────────────────── */
  function assignCurrentUser () {
    ajax({
      action     : 'wv_assign_stand',
      product_id : selectedStandId,
      user_id    : selectedUserId
    }).done(res => {
      if (res.success && (res.data?.ok ?? true)) {  
        toast('Stand updated', true);
        selectedUserId = 0;
        $selectVal.text('Select stand user');
        fetchStandState(selectedStandId).then(refreshButtons);
      } else {
        toast(res.data?.msg || 'Cannot assign stand.');
      }
    }).fail(() => toast('Server error – please try again.'));
  }

  $assignBtn.on('click', assignCurrentUser);
  $shareBtn .on('click', assignCurrentUser);

  /* ────────────────────────────────────────────────────────────────
   * 9.  REMOVE
   * ───────────────────────────────────────────────────────────── */
  $removeBtn.on('click', () => {
    ajax({
      action     : 'wv_unassign_stand',
      product_id : selectedStandId,
      user_id    : selectedUserId
    }).done(res => {
      if (res.success && (res.data?.ok ?? true)) { 
        toast('Stand freed', true);
        selectedUserId = 0;
        $selectVal.text('Select stand user');
        fetchStandState(selectedStandId).then(refreshButtons);
      } else {
        toast(res.data?.msg || 'Cannot free stand.');
      }
    }).fail(() => toast('Server error – please try again.'));
  });

  /* ────────────────────────────────────────────────────────────────
   * 10.  FETCH + CACHE ONE STAND
   * ───────────────────────────────────────────────────────────── */
  function fetchStandState (pid) {
    if (!pid) return $.Deferred().resolve().promise();

    return ajax({ action: 'wv_get_stand_state', product_id: pid })
      .then(res => {
        if (!res.success) { toast('Error fetching stand data'); return; }

        const users    = res.data?.users    || [];
        const capacity = res.data?.capacity || 1;

        standAssignCache[pid] = users;
        standCapCache   [pid] = capacity;

        /* keep local model + UI in sync */
        syncStandRow(pid, users, capacity);
        buildDropdown();
      });
  }

  /* ────────────────────────────────────────────────────────────────
   * 11.  BOOT
   * ───────────────────────────────────────────────────────────── */
  buildDropdown();

})(jQuery, window, document);
