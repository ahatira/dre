(function (Drupal, once) {
  'use strict';

  const IMAGE_SLIDE_TYPES = ['image', 'plan_image'];
  const HERO_SPEED_MS = 360;

  let offerGalleryLightbox = null;
  let thumbBarRoot = null;
  let counterElement = null;
  let statusElement = null;

  function getGallerySettings() {
    return drupalSettings.psOfferGallery || {};
  }

  function getSlides() {
    return getGallerySettings().slides || [];
  }

  function normalizeIndex(index, length) {
    if (!length) {
      return 0;
    }
    return ((index % length) + length) % length;
  }

  function resolveEntryIndex(entry) {
    const slides = getSlides();
    const entryIndexes = getGallerySettings().entry_indexes || {};

    if (!slides.length) {
      return 0;
    }

    if (entry && entryIndexes[entry] !== undefined && entryIndexes[entry] !== null) {
      return entryIndexes[entry];
    }

    return 0;
  }

  function isImageSlide(type) {
    return IMAGE_SLIDE_TYPES.includes(type);
  }

  function isPhotoSwipeAvailable() {
    return typeof PhotoSwipeLightbox !== 'undefined' && typeof PhotoSwipe !== 'undefined';
  }

  function isSwiperAvailable() {
    return typeof Swiper !== 'undefined';
  }

  function createIframe(src, title) {
    const iframe = document.createElement('iframe');
    iframe.src = src;
    iframe.setAttribute('loading', 'lazy');
    iframe.setAttribute('allowfullscreen', '');
    iframe.setAttribute('allow', 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share');
    iframe.setAttribute('referrerpolicy', 'strict-origin-when-cross-origin');
    iframe.setAttribute('title', title || '');
    iframe.className = 'ps-gallery-lightbox__iframe';
    return iframe;
  }

  function buildNonImageSlideElement(slide) {
    if (!slide) {
      return null;
    }

    switch (slide.type) {
      case 'video_oembed':
        return createIframe(slide.embed_url, slide.alt);

      case 'video_file':
      case 'video_url': {
        const video = document.createElement('video');
        video.controls = true;
        video.preload = 'metadata';
        video.className = 'ps-gallery-lightbox__video';
        const source = document.createElement('source');
        source.src = slide.video_url;
        video.appendChild(source);
        return video;
      }

      case 'visit_3d': {
        const wrapper = document.createElement('div');
        wrapper.className = 'ps-gallery-lightbox__visit';
        wrapper.appendChild(createIframe(slide.iframe_url, slide.alt));
        if (slide.external_url) {
          const fallback = document.createElement('a');
          fallback.href = slide.external_url;
          fallback.target = '_blank';
          fallback.rel = 'noopener noreferrer';
          fallback.className = 'ps-gallery-lightbox__fallback';
          fallback.textContent = Drupal.t('Open 3D visit in a new tab');
          wrapper.appendChild(fallback);
        }
        return wrapper;
      }

      case 'plan_pdf':
        return createIframe(slide.iframe_url, slide.alt);

      default:
        return null;
    }
  }

  function buildPhotoSwipeDataSource(slides) {
    return slides.map((slide, galleryIndex) => {
      if (isImageSlide(slide.type)) {
        return {
          src: slide.url,
          msrc: slide.thumb_url || slide.url,
          width: Number(slide.width) || 1920,
          height: Number(slide.height) || 1080,
          alt: slide.alt || '',
          galleryIndex,
          slideType: slide.type,
        };
      }

      return {
        galleryIndex,
        slideType: slide.type,
        width: 1600,
        height: 900,
      };
    });
  }

  function updateLightboxCounter(index, total) {
    if (!counterElement) {
      return;
    }
    counterElement.textContent = Drupal.t('@current / @total', {
      '@current': index + 1,
      '@total': total,
    });
  }

  function updateLightboxStatus(index, slides) {
    if (!statusElement) {
      return;
    }
    const slide = slides[index];
    const label = slide?.label || slide?.alt || '';
    const position = Drupal.t('@current of @total', {
      '@current': index + 1,
      '@total': slides.length,
    });
    statusElement.textContent = label ? `${position} — ${label}` : position;
  }

  function updateLightboxThumbs(index) {
    const thumbButtons = thumbBarRoot
      ? thumbBarRoot.querySelectorAll('[data-ps-lightbox-thumb]')
      : document.querySelectorAll('[data-ps-gallery-lightbox] [data-ps-lightbox-thumb]');

    thumbButtons.forEach((thumb) => {
      const isActive = Number(thumb.dataset.index) === index;
      thumb.classList.toggle('is-active', isActive);
      if (isActive) {
        thumb.setAttribute('aria-current', 'true');
      }
      else {
        thumb.removeAttribute('aria-current');
      }
    });
  }

  function scrollActiveThumbIntoView(index) {
    const track = thumbBarRoot?.querySelector('[data-ps-lightbox-thumbs-track]')
      || thumbBarRoot?.closest('[data-ps-lightbox-thumbs-track]');
    const thumb = thumbBarRoot?.querySelector(`[data-ps-lightbox-thumb][data-index="${index}"]`);
    if (!track || !thumb) {
      return;
    }

    const trackRect = track.getBoundingClientRect();
    const thumbRect = thumb.getBoundingClientRect();
    const thumbCenter = thumbRect.left + (thumbRect.width / 2);
    const trackCenter = trackRect.left + (trackRect.width / 2);
    track.scrollTo({
      left: track.scrollLeft + (thumbCenter - trackCenter),
      behavior: 'smooth',
    });
  }

  function bindThumbBarEvents(root, pswp) {
    root.querySelectorAll('[data-ps-lightbox-thumb]').forEach((thumb) => {
      thumb.addEventListener('click', (event) => {
        event.preventDefault();
        event.stopPropagation();
        const nextIndex = Number(thumb.dataset.index || 0);
        if (Number.isNaN(nextIndex) || nextIndex === pswp.currIndex) {
          return;
        }
        pswp.goTo(nextIndex);
      });
    });
  }

  function mountThumbBar(pswp) {
    const source = document.querySelector('[data-ps-gallery-lightbox]');
    const track = source?.querySelector('[data-ps-lightbox-thumbs-track]');
    if (!track) {
      return null;
    }

    const mount = document.createElement('div');
    mount.className = 'ps-gallery-lightbox__thumbs-ui';
    mount.innerHTML = track.outerHTML;
    pswp.element.appendChild(mount);
    bindThumbBarEvents(mount, pswp);
    return mount;
  }

  function initOfferGalleryPhotoSwipe() {
    if (offerGalleryLightbox || !isPhotoSwipeAvailable()) {
      return offerGalleryLightbox;
    }

    const source = document.querySelector('[data-ps-gallery-lightbox]');
    statusElement = source?.querySelector('[data-ps-lightbox-status]') || null;

    offerGalleryLightbox = new PhotoSwipeLightbox({
      pswpModule: PhotoSwipe,
      dataSource: [],
      bgOpacity: 1,
      wheelToZoom: true,
      showHideAnimationType: 'zoom',
      initialZoomLevel: 'fit',
      secondaryZoomLevel: 2,
      maxZoomLevel: 4,
      loop: true,
      padding: { top: 48, bottom: 120, left: 12, right: 12 },
      indexIndicatorSep: ' / ',
      preload: [1, 2],
    });

    offerGalleryLightbox.on('contentLoad', (event) => {
      const { content } = event;
      const slideType = content.data.slideType;
      if (!slideType || isImageSlide(slideType)) {
        return;
      }

      event.preventDefault();
      const slide = getSlides()[content.data.galleryIndex];
      const inner = buildNonImageSlideElement(slide);
      if (!inner) {
        return;
      }

      const wrapper = document.createElement('div');
      wrapper.className = `ps-gallery-lightbox__html-slide ps-gallery-lightbox__slide--${slide.type}`;
      wrapper.appendChild(inner);
      content.element = wrapper;

      const chromeHeight = parseFloat(getComputedStyle(document.body).getPropertyValue('--ps-gallery-lightbox-chrome-height')) || 136;
      const counterOffset = parseFloat(getComputedStyle(document.body).getPropertyValue('--ps-gallery-lightbox-counter-offset')) || 144;
      const maxHeight = Math.max(200, window.innerHeight - chromeHeight - counterOffset - 48);
      const maxWidth = Math.max(280, window.innerWidth - 24);
      const aspect = 16 / 9;
      let height = Math.min(maxHeight, maxWidth / aspect);
      let width = height * aspect;
      if (width > maxWidth) {
        width = maxWidth;
        height = width / aspect;
      }
      content.width = Math.round(width);
      content.height = Math.round(height);
      content.onLoaded();
    });

    offerGalleryLightbox.on('uiRegister', () => {
      const { pswp } = offerGalleryLightbox;

      pswp.ui.registerElement({
        name: 'psCounter',
        className: 'ps-gallery-lightbox__counter',
        order: 5,
        appendTo: 'wrapper',
        onInit: (element) => {
          element.setAttribute('data-ps-lightbox-counter', '');
          element.setAttribute('aria-hidden', 'true');
          counterElement = element;
        },
      });

    });

    offerGalleryLightbox.on('afterInit', () => {
      const { pswp } = offerGalleryLightbox;
      thumbBarRoot = mountThumbBar(pswp);
      if (thumbBarRoot && counterElement) {
        thumbBarRoot.prepend(counterElement);
        counterElement.classList.add('ps-gallery-lightbox__counter--anchored');
      }
      document.body.classList.add('ps-gallery-lightbox-open');
      const index = pswp.currIndex;
      const slides = getSlides();
      updateLightboxCounter(index, slides.length);
      updateLightboxStatus(index, slides);
      updateLightboxThumbs(index);
      scrollActiveThumbIntoView(index);
    });

    offerGalleryLightbox.on('change', () => {
      const { pswp } = offerGalleryLightbox;
      if (!pswp) {
        return;
      }
      const index = pswp.currIndex;
      const slides = getSlides();
      updateLightboxCounter(index, slides.length);
      updateLightboxStatus(index, slides);
      updateLightboxThumbs(index);
      scrollActiveThumbIntoView(index);
      syncHeroIndex(index);
    });

    offerGalleryLightbox.on('close', () => {
      const index = offerGalleryLightbox.pswp?.currIndex;
      if (index !== undefined) {
        syncHeroIndex(index);
      }
      counterElement?.classList.remove('ps-gallery-lightbox__counter--anchored');
      thumbBarRoot?.remove();
      thumbBarRoot = null;
      counterElement = null;
      document.body.classList.remove('ps-gallery-lightbox-open');
    });

    offerGalleryLightbox.init();
    return offerGalleryLightbox;
  }

  function openOfferGallery(index) {
    const slides = getSlides();
    if (!slides.length) {
      return;
    }

    const lightbox = initOfferGalleryPhotoSwipe();
    if (!lightbox) {
      return;
    }

    const safeIndex = normalizeIndex(index, slides.length);
    lightbox.loadAndOpen(safeIndex, buildPhotoSwipeDataSource(slides));
  }

  function syncHeroIndex(index) {
    const hero = document.querySelector('[data-ps-gallery-hero]');
    hero?.psOfferGallery?.setIndex(index);
  }

  function initHeroSwiper(hero) {
    const swiperElement = hero.querySelector('[data-ps-gallery-swiper]');
    const slides = getSlides();

    if (!swiperElement || !slides.length || !isSwiperAvailable()) {
      return null;
    }

    const swiper = new Swiper(swiperElement, {
      speed: HERO_SPEED_MS,
      loop: slides.length > 1,
      slidesPerView: 1,
      allowTouchMove: true,
      watchOverflow: true,
      a11y: {
        prevSlideMessage: Drupal.t('Previous media'),
        nextSlideMessage: Drupal.t('Next media'),
      },
      navigation: {
        prevEl: hero.querySelector('[data-ps-gallery-prev]'),
        nextEl: hero.querySelector('[data-ps-gallery-next]'),
      },
      on: {
        slideChange(instance) {
          hero.dataset.psGalleryIndex = String(instance.realIndex);
        },
      },
    });

    hero.psOfferGallery = {
      setIndex(nextIndex) {
        const safeIndex = normalizeIndex(nextIndex, slides.length);
        if (swiper.params.loop) {
          swiper.slideToLoop(safeIndex, 0);
        }
        else {
          swiper.slideTo(safeIndex, 0);
        }
        hero.dataset.psGalleryIndex = String(safeIndex);
      },
      getIndex() {
        return swiper.realIndex;
      },
      swiper,
    };

    hero.dataset.psGalleryIndex = String(swiper.realIndex);
    return swiper;
  }

  Drupal.behaviors.psOfferGallery = {
    attach(context) {
      once('ps-offer-gallery-hero', '[data-ps-gallery-hero]', context).forEach((hero) => {
        initHeroSwiper(hero);

        hero.querySelectorAll('[data-ps-gallery-open]').forEach((trigger) => {
          trigger.addEventListener('click', () => {
            const entry = trigger.getAttribute('data-ps-gallery-entry');
            if (entry) {
              openOfferGallery(resolveEntryIndex(entry));
              return;
            }
            const heroIndex = Number(hero.dataset.psGalleryIndex || hero.psOfferGallery?.getIndex?.() || 0);
            openOfferGallery(heroIndex);
          });
        });
      });
    },
  };
})(Drupal, once);
