// Bootstrap JS
import 'bootstrap';

// jQuery plugins
import $ from 'jquery';
import 'select2';

// intl-tel-input
import intlTelInput from 'intl-tel-input';
import 'intl-tel-input/build/css/intlTelInput.css';

// Swiper (bundle includes Autoplay)
import Swiper from 'swiper/bundle';

// Custom scripts
import './ds_cropper.js';
import './public.js';
// import './dashboard.js';
// import './products.js';


/* ───────────────────────────────────────────────
 * Helpers
 * ──────────────────────────────────────────── */
  /* ---------------------------------------------------------------
  * Phone widgets (intl‑tel‑input)
  * --------------------------------------------------------------*/
  const PHONE_OPTIONS = {
    initialCountry   : 'auto',
    // onlyCountries  : ['rs','ba','hr','me','mk','si','bg'], // <- if you need a white‑list
    excludeCountries : ['XK'],                                // hide Kosovo
    geoIpLookup      : cb => fetch('https://ipapi.co/json')
                                .then(r => r.json()).then(d => cb(d.country_code))
                                .catch(() => cb('us')),
    utilsScript      : 'https://cdn.jsdelivr.net/npm/intl-tel-input@23.0.10/build/js/utils.js',
    nationalMode     : false                                   // always E.164
  };

  /** Attach intl‑tel‑input to every un‑initialised phone field inside `context`. */
  function initializeIntlTelInputs (context = document) {
    context.querySelectorAll('.wv-phone-input-field').forEach(input => {
      if (input.dataset.intlInitialized) return;          // already done

      /* 1 — instantiate */
      const iti = intlTelInput(input, PHONE_OPTIONS);

      /* 2 — auto‑insert/replace the dial‑code on flag change */
      const syncDialPrefix = () => {
        const dial = '+' + iti.getSelectedCountryData().dialCode;
        if (!input.value.trim() || !input.value.startsWith('+')) {
          input.value = `${dial} `;
        } else if (!input.value.startsWith(dial)) {
          input.value = input.value.replace(/^\+\d+\s*/, `${dial} `);
        }
      };
      input.addEventListener('countrychange', syncDialPrefix);   // :contentReference[oaicite:0]{index=0}

      /* 3 — ensure a prefix straight away (blank fields only) */
      if (!input.value.trim()) syncDialPrefix();

      /* 4 — if the field was pre‑filled, show the correct flag */
      if (input.value.trim()) iti.setNumber(input.value.trim()); // works with or without '+'

      /* 5 — validation hook kept intact */
      input.form?.addEventListener('submit', e => {
        if (!iti.isValidNumber()) { e.preventDefault(); alert('Please enter a valid phone number.'); }
        // normalise to pure E.164 before submit
        input.value = iti.getNumber().replace(/\s+/g, '');
      });

      input.dataset.intlInitialized = 'true';
    });
  }
  export { initializeIntlTelInputs };



/* ───────────────────────────────────────────────
 * 8 ZONES / nested carousels
 * ──────────────────────────────────────────── */
function initZoneCarousels() {
  const zoneSwiperEl = document.getElementById('zonesSwiper');
  if (!zoneSwiperEl) return;              // page without 8-zones → skip

  const zoneSwiper = new Swiper(zoneSwiperEl, {
    slidesPerView: 'auto',
    centeredSlides: true,
    spaceBetween: -128,
    loop: true,
    speed: 600,
    navigation: { nextEl: '.ds-next', prevEl: '.ds-prev' },
    allowTouchMove: true,
    breakpoints: {
      1200: { spaceBetween: -128 },
      992:  { spaceBetween: -96  },
      768:  { spaceBetween: -64  },
      0:    { slidesPerView: 1, centeredSlides: false, spaceBetween: 0 },
    },
  });

  /* pairs that share a main slide */
  const alias = { '2C': '2A', '1G': '1' };

  /* hall label (for merged captions) */
  const hallLabel = document.getElementById('wv-selected-hall-zone');
  const labelMap  = { '2A':'2A / 2C','2C':'2A / 2C','1':'1 / 1G','1G':'1 / 1G' };

  /* slug → slide-index map */
  const slugToIndex = {};
  zoneSwiper.slides.forEach(slide => {
    if (slide.classList.contains('swiper-slide-duplicate')) return;
    const slug = slide.dataset.hall;
    if (slug && !(slug in slugToIndex)) {
      slugToIndex[slug] = Number(slide.getAttribute('data-swiper-slide-index'));
    }
  });
  Object.entries(alias).forEach(([dup, canon]) => {
    if (canon in slugToIndex) slugToIndex[dup] = slugToIndex[canon];
  });

  /* create & cache every mini-swiper */
  const innerSwipers = {};
  document.querySelectorAll('.zone-gallery').forEach(el => {
    innerSwipers[ el.closest('.swiper-slide').dataset.hall ] =
      new Swiper(el, {
        slidesPerView: 1,
        speed: 400,
        pagination: { el: el.querySelector('.zone-pagination'), clickable: true },
      });
  });

  /* bullets jump to the right main slide */
  document.querySelectorAll('#wv_hall_nav .wv-nav-hall-zones').forEach(b => {
    const raw = b.id.replace('wv-nav-hall_', '');
    b.addEventListener('click', () => {
      const target = slugToIndex[ alias[raw] ?? raw ];
      if (target !== undefined) zoneSwiper.slideToLoop(target);
    });
  });

  /* sync SVG, label, and reset ALL mini-swipers */
  const syncUI = () => {
    const activeSlug  = zoneSwiper.slides[zoneSwiper.activeIndex].dataset.hall;
    const canonActive = alias[activeSlug] ?? activeSlug;

    /* highlight correct bullet */
    document.querySelectorAll('#wv_hall_nav .wv-nav-hall-zones').forEach(b => {
      const slug  = b.id.replace('wv-nav-hall_', '');
      const canon = alias[slug] ?? slug;
      b.classList.toggle('active', canon === canonActive);
    });

    /* update “HALL …” label */
    if (hallLabel) hallLabel.textContent = labelMap[activeSlug] ?? activeSlug;

    /* reset EVERY mini-gallery to its first slide */
    Object.values(innerSwipers).forEach(sw => sw.slideTo(0, 0));
  };

  syncUI();
  zoneSwiper.on('slideChange', syncUI);
}

/* ───────────────────────────────────────────────
 * Entry
 * ──────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', () => {
  initializeIntlTelInputs();
  $('.my-select2').select2();

  new Swiper('.overlap-carousel', {
    slidesPerView: 5,
    spaceBetween: -48,
    loop: true,
    autoplay: { delay: 4000, disableOnInteraction: false },
    allowTouchMove: false,
    breakpoints: {
      1200: { slidesPerView: 5, spaceBetween: -48 },
      992:  { slidesPerView: 4, spaceBetween: -48  },
      768:  { slidesPerView: 1, spaceBetween: -24  },
      0:    { slidesPerView: 1, centeredSlides: false, spaceBetween: -24 },
    },
  });

  new Swiper('.wv-h-news-carousel', {
    slidesPerView: 'auto',
    centeredSlides: true,
    spaceBetween: 12,
    loop: true,
    autoplay: { delay: 5000, disableOnInteraction: false },
  });

  new Swiper('.wv-h-podcast-carousel', {
    slidesPerView: 'auto',
    centeredSlides: true,
    spaceBetween: 12,
    loop: true,
    autoplay: { delay: 5000, disableOnInteraction: false },
  });

  new Swiper('.wv-card-carousel', {
    slidesPerView: 5,
    spaceBetween: 12,
    loop: true,
    autoplay: { delay: 4000, disableOnInteraction: false },
    allowTouchMove: false,
    pagination: { el: '.wv-card-carousel .swiper-pagination', clickable: true },
    breakpoints: {
      1200: { slidesPerView: 4, spaceBetween: 12 },
      992:  { slidesPerView: 3, spaceBetween: 12 },
      768:  { slidesPerView: 1, spaceBetween: 12 },
      0:    { slidesPerView: 1, centeredSlides: false, spaceBetween: 12 },
    },
  });

  /* continuous, slow, auto-width carousel */
  new Swiper('.wv-img-carousel', {
    slidesPerView: 'auto',
    spaceBetween: 12,
    loop: true,
    loopAdditionalSlides: 6,
    speed: 15000,
    autoplay: { delay: 0, disableOnInteraction: false, pauseOnMouseEnter: true },
    allowTouchMove: false,
    grabCursor: false,
  });

  initZoneCarousels();   // guarded initializer
});

$(document).ajaxComplete(() => {
  initializeIntlTelInputs();
});
