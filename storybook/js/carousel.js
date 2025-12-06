((d, f) => {
  function p(e) {
    return e
      ? {
          el: e,
          clickable: !0,
          bulletClass: 'swiper-pagination-bullet',
          bulletActiveClass: 'swiper-pagination-bullet-active',
        }
      : !1;
  }
  function h(e, t) {
    return e
      ? {
          320: { slidesPerView: 1, spaceBetween: 28 },
          768: { slidesPerView: 2, spaceBetween: 28 },
          1024: { slidesPerView: 3, spaceBetween: 28 },
          1200: { slidesPerView: 4, spaceBetween: 28 },
          1600: { slidesPerView: 5, spaceBetween: 28 },
        }
      : t
        ? {
            320: { slidesPerView: 4, spaceBetween: 8 },
            640: { slidesPerView: 5, spaceBetween: 8 },
            1024: { slidesPerView: 6, spaceBetween: 8 },
          }
        : {};
  }
  function b(e) {
    const {
      hasLoop: t,
      hasAutoHeight: s,
      isCards: n,
      isThumbs: i,
      prevButton: a,
      nextButton: o,
      pagination: u,
    } = e;
    return {
      loop: i ? !1 : t,
      autoHeight: s,
      speed: 300,
      spaceBetween: n ? 28 : i ? 8 : 0,
      slidesPerView: n ? 1 : i ? 5 : 1,
      centeredSlides: !n && !i,
      watchOverflow: !0,
      watchSlidesProgress: !!i,
      freeMode: !!i,
      slideToClickedSlide: !!i,
      navigation: { prevEl: a, nextEl: o, disabledClass: 'is-disabled' },
      pagination: p(u),
      keyboard: { enabled: !0, onlyInViewport: !0 },
      a11y: {
        enabled: !0,
        prevSlideMessage: 'Previous slide',
        nextSlideMessage: 'Next slide',
        firstSlideMessage: 'This is the first slide',
        lastSlideMessage: 'This is the last slide',
        paginationBulletMessage: 'Go to slide {{index}}',
      },
      breakpoints: h(n, i),
    };
  }
  function g(e, t, s) {
    if (!s) return;
    const n = document.getElementById(s);
    n != null && n.swiperInstance
      ? (t.thumbs = { swiper: n.swiperInstance })
      : ((t.thumbs = { swiper: null }), (e.__pendingThumbsTargetId = s));
  }
  function I(e) {
    e.thumbs &&
      (typeof e.thumbs.init == 'function' && e.thumbs.init(),
      typeof e.thumbs.update == 'function' && e.thumbs.update());
  }
  function v(e, t, s) {
    !e.swiperInstance ||
      e.__pendingThumbsTargetId !== s ||
      ((e.swiperInstance.params.thumbs = e.swiperInstance.params.thumbs || {}),
      (e.swiperInstance.params.thumbs.swiper = t),
      I(e.swiperInstance),
      l(e.swiperInstance, t),
      delete e.__pendingThumbsTargetId);
  }
  function w(e, t) {
    const s = e.id;
    s &&
      document.querySelectorAll(`[data-carousel-thumbs="${s}"]`).forEach((n) => {
        v(n, t, s);
      });
  }
  function x(e, t) {
    const s = t.realIndex;
    e.forEach((n, i) => {
      const a = Number.parseInt(n.dataset.slideIndex, 10),
        o = e[i + 1],
        u = o ? Number.parseInt(o.dataset.slideIndex, 10) - 1 : t.slides.length - 1,
        r = s >= a && s <= u;
      (n.dataset.active = r ? 'true' : 'false'),
        n.setAttribute('aria-selected', r ? 'true' : 'false'),
        (n.tabIndex = r ? 0 : -1);
    });
  }
  function m(e, t, s) {
    const n = s - 1;
    return e === 'ArrowRight' || e === 'Right'
      ? t === n
        ? 0
        : t + 1
      : e === 'ArrowLeft' || e === 'Left'
        ? t === 0
          ? n
          : t - 1
        : -1;
  }
  function T(e, t, s) {
    const n = m(e, t, s);
    if (n >= 0) return n;
    const i = s - 1;
    return e === 'Home' ? 0 : e === 'End' ? i : -1;
  }
  function y(e, t, s, n, i, a) {
    const o = e.key,
      u = T(o, s, n);
    if (u >= 0) {
      const r = i[u];
      r && r.focus(), e.preventDefault(), e.stopPropagation();
      return;
    }
    if (o === 'Enter' || o === ' ') {
      const r = Number.parseInt(t.dataset.slideIndex, 10);
      Number.isNaN(r) || a.slideTo(r), e.preventDefault(), e.stopPropagation();
    }
  }
  function A(e, t) {
    if (t.length === 0) return;
    t.forEach((n) => {
      n.addEventListener('click', () => {
        const i = Number.parseInt(n.dataset.slideIndex, 10);
        Number.isNaN(i) || e.slideTo(i);
      });
    });
    const s = () => {
      x(t, e);
    };
    e.on('slideChange', s),
      s(),
      t.forEach((n, i) => {
        n.addEventListener('keydown', (a) => {
          y(a, n, i, t.length, t, e);
        });
      });
  }
  function C(e) {
    const t = () => {
      if (!e || !e.slides || e.slides.length === 0) return;
      const s = e.activeIndex ?? e.realIndex ?? 0;
      e.slides.forEach((n, i) => {
        i === s ? n.setAttribute('aria-current', 'true') : n.removeAttribute('aria-current');
      });
    };
    e.on('init', t), e.on('slideChange', t), t();
  }
  function B(e, t) {
    var n;
    const s = e.realIndex ?? e.activeIndex ?? 0;
    t.slideTo(s),
      (n = t.slides) != null &&
        n.length &&
        t.slides.forEach((i, a) => {
          a === s
            ? i.classList.add('swiper-slide-thumb-active')
            : i.classList.remove('swiper-slide-thumb-active');
        });
  }
  function l(e, t) {
    if (!e || !t || e.__syncedWith === t) return;
    (e.__syncedWith = t),
      t.on('tap', () => {
        const n = t.clickedIndex;
        typeof n == 'number' && n >= 0 && e.slideTo(n);
      });
    const s = () => {
      B(e, t);
    };
    e.on('slideChange', s), s();
  }
  function E(e) {
    return {
      hasLoop: e.classList.contains('ps-carousel--loop'),
      hasAutoHeight: e.classList.contains('ps-carousel--auto-height'),
      isCards: e.classList.contains('ps-carousel--cards'),
      isThumbs: e.getAttribute('data-carousel-role') === 'thumbs',
      thumbsTargetId: e.getAttribute('data-carousel-thumbs'),
      attrSlidesPerView: e.getAttribute('data-slides-per-view'),
      attrSpaceBetween: e.getAttribute('data-space-between'),
    };
  }
  function N(e) {
    return {
      prevButton: e.querySelector('[data-carousel-prev]'),
      nextButton: e.querySelector('[data-carousel-next]'),
      pagination: e.querySelector('.ps-carousel__pagination'),
      toolbarItems: Array.from(e.querySelectorAll('[data-toolbar-item]')),
    };
  }
  function P(e, t) {
    const { isThumbs: s, attrSlidesPerView: n, attrSpaceBetween: i } = t;
    if (
      s &&
      (n &&
        ((e.slidesPerView = n === 'auto' ? 'auto' : Number(n)),
        e.breakpoints && (e.breakpoints = {})),
      i)
    ) {
      const a = Number(i);
      Number.isNaN(a) || (e.spaceBetween = a);
    }
  }
  function c(e) {
    if (typeof Swiper > 'u')
      return (
        console.error('Carousel: Swiper.js is not loaded. Install via npm: npm install swiper'),
        null
      );
    const t = E(e),
      s = N(e),
      n = b({
        hasLoop: t.hasLoop,
        hasAutoHeight: t.hasAutoHeight,
        isCards: t.isCards,
        isThumbs: t.isThumbs,
        prevButton: s.prevButton,
        nextButton: s.nextButton,
        pagination: s.pagination,
      });
    P(n, t), g(e, n, t.thumbsTargetId);
    const i = new Swiper(e, n);
    if ((t.isThumbs && w(e, i), A(i, s.toolbarItems), C(i), t.thumbsTargetId)) {
      const a = document.getElementById(t.thumbsTargetId);
      a != null && a.swiperInstance && l(i, a.swiperInstance);
    }
    return i;
  }
  (d.behaviors.psCarousel = {
    attach(e) {
      f('ps-carousel', '[data-carousel]', e).forEach((s) => {
        const n = c(s);
        n && (s.swiperInstance = n);
      });
    },
    detach(e, t, s) {
      s === 'unload' &&
        e.querySelectorAll('[data-carousel]').forEach((i) => {
          i.swiperInstance && (i.swiperInstance.destroy(!0, !0), delete i.swiperInstance);
        });
    },
  }),
    (typeof d > 'u' || !d.behaviors) &&
      document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('[data-carousel]').forEach((e) => {
          if (!e.swiperInstance) {
            const t = c(e);
            t && (e.swiperInstance = t);
          }
        });
      });
})(typeof Drupal < 'u' ? Drupal : {}, typeof once < 'u' ? once : () => []);
