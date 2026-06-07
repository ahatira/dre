(function (Drupal, once) {
  'use strict';

  const IMAGE_SLIDE_TYPES = ['image', 'plan_image'];
  const SLIDE_TRANSITION_MS = 360;
  const SWIPE_THRESHOLD_PX = 48;
  const DOUBLE_TAP_MS = 300;

  function getGallerySettings() {
    return drupalSettings.psOfferGallery || {};
  }

  function prefersReducedMotion() {
    return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  }

  function getSlides(hero) {
    const settings = getGallerySettings();
    const fromSettings = settings.slides || [];
    if (fromSettings.length) {
      return fromSettings;
    }

    const root = hero || document;
    const template = root.querySelector('[data-ps-gallery-slides]');
    if (!template) {
      return [];
    }
    const scope = template.content || template;
    return Array.from(scope.querySelectorAll('[data-type]')).map((node) => ({
      type: node.getAttribute('data-type') || 'image',
      url: node.getAttribute('data-url') || '',
      thumb_url: node.getAttribute('data-thumb-url') || '',
      alt: node.getAttribute('data-alt') || '',
      label: node.getAttribute('data-label') || '',
    }));
  }

  function normalizeIndex(index, length) {
    if (!length) {
      return 0;
    }
    return ((index % length) + length) % length;
  }

  function resolveEntryIndex(entry) {
    const settings = getGallerySettings();
    const slides = settings.slides || [];
    const entryIndexes = settings.entry_indexes || {};

    if (!slides.length) {
      return 0;
    }

    if (entry && entryIndexes[entry] !== undefined && entryIndexes[entry] !== null) {
      return entryIndexes[entry];
    }

    return 0;
  }

  function resolveMediaIcon(type) {
    if (!type) {
      return '';
    }
    if (type.startsWith('video')) {
      return 'play';
    }
    if (type === 'visit_3d') {
      return '3d';
    }
    if (type.startsWith('plan')) {
      return 'file';
    }
    return '';
  }

  function resolveSlideGroup(type) {
    if (!type) {
      return 'photos';
    }
    if (type.startsWith('video')) {
      return 'video';
    }
    if (type === 'visit_3d') {
      return 'visit';
    }
    if (type.startsWith('plan')) {
      return 'plan';
    }
    return 'photos';
  }

  function isImageSlide(type) {
    return IMAGE_SLIDE_TYPES.includes(type);
  }

  function createHeroIcon(type) {
    const iconType = resolveMediaIcon(type);
    if (!iconType) {
      return null;
    }
    const icon = document.createElement('span');
    icon.className = `ps-media-gallery-hero__icon ps-media-gallery-hero__icon--${iconType}`;
    icon.setAttribute('aria-hidden', 'true');
    return icon;
  }

  function buildHeroSlideElement(slide) {
    const wrapper = document.createElement('div');
    wrapper.className = `ps-media-gallery-hero__slide ps-media-gallery-hero__slide--${slide.type}`;

    if (isImageSlide(slide.type)) {
      const image = document.createElement('img');
      image.src = slide.url;
      image.alt = slide.alt || '';
      image.className = 'ps-media-gallery-hero__image';
      image.loading = 'eager';
      wrapper.appendChild(image);
    }
    else {
      const thumbWrap = document.createElement('div');
      thumbWrap.className = 'ps-media-gallery-hero__thumb-wrap';

      if (slide.thumb_url) {
        const thumb = document.createElement('img');
        thumb.src = slide.thumb_url;
        thumb.alt = '';
        thumb.className = 'ps-media-gallery-hero__thumb-bg';
        thumb.loading = 'lazy';
        thumbWrap.appendChild(thumb);
      }
      else {
        const placeholder = document.createElement('span');
        placeholder.className = 'ps-media-gallery-hero__thumb-placeholder';
        placeholder.setAttribute('aria-hidden', 'true');
        thumbWrap.appendChild(placeholder);
      }

      const icon = createHeroIcon(slide.type);
      if (icon) {
        thumbWrap.appendChild(icon);
      }

      wrapper.appendChild(thumbWrap);

      const label = slide.label || slide.alt || '';
      if (label) {
        const sr = document.createElement('span');
        sr.className = 'visually-hidden';
        sr.textContent = label;
        wrapper.appendChild(sr);
      }
    }

    return wrapper;
  }

  function renderHeroSlide(stage, slide, direction) {
    if (!stage || !slide) {
      return;
    }

    const incoming = buildHeroSlideElement(slide);
    const animate = direction && !prefersReducedMotion();
    const outgoing = stage.querySelector('.ps-media-gallery-hero__slide.is-active')
      || stage.querySelector('.ps-media-gallery-hero__slide');

    if (!animate || !outgoing) {
      stage.innerHTML = '';
      incoming.classList.add('is-active');
      stage.appendChild(incoming);
      return;
    }

    incoming.classList.add(direction === 'prev' ? 'is-entering-prev' : 'is-entering-next');
    stage.appendChild(incoming);

    outgoing.classList.remove('is-active');
    outgoing.classList.add('is-leaving', direction === 'prev' ? 'is-leaving-prev' : 'is-leaving-next');

    const finalize = () => {
      outgoing.remove();
      incoming.classList.remove('is-entering-prev', 'is-entering-next');
      incoming.classList.add('is-active');
    };

    let completed = false;
    const onEnd = (event) => {
      if (completed || event.propertyName !== 'transform') {
        return;
      }
      completed = true;
      finalize();
    };

    outgoing.addEventListener('transitionend', onEnd);
    window.setTimeout(() => {
      if (!completed) {
        completed = true;
        finalize();
      }
    }, SLIDE_TRANSITION_MS + 40);

    window.requestAnimationFrame(() => {
      window.requestAnimationFrame(() => {
        incoming.classList.add('is-active');
      });
    });
  }

  function syncHeroIndex(index) {
    const hero = document.querySelector('[data-ps-gallery-hero]');
    if (!hero?.psOfferGallery) {
      return;
    }
    hero.psOfferGallery.setIndex(index);
    hero.psOfferGallery.render();
  }

  function createIframe(src, title) {
    const iframe = document.createElement('iframe');
    iframe.src = src;
    iframe.setAttribute('loading', 'lazy');
    iframe.setAttribute('allowfullscreen', '');
    iframe.setAttribute('title', title || '');
    iframe.className = 'ps-gallery-lightbox__iframe';
    return iframe;
  }

  let photoSwipeLightbox = null;

  function isPhotoSwipeAvailable() {
    return typeof PhotoSwipeLightbox !== 'undefined' && typeof PhotoSwipe !== 'undefined';
  }

  function buildPhotoSwipeDataSource(activeImage, activeGalleryIndex) {
    const slides = getGallerySettings().slides || [];
    return slides
      .map((slide, galleryIndex) => ({ slide, galleryIndex }))
      .filter(({ slide }) => isImageSlide(slide.type))
      .map(({ slide, galleryIndex }) => {
        const useDomDimensions = galleryIndex === activeGalleryIndex && activeImage?.naturalWidth;
        return {
          src: slide.url,
          msrc: slide.thumb_url || slide.url,
          width: useDomDimensions ? activeImage.naturalWidth : (Number(slide.width) || 1920),
          height: useDomDimensions ? activeImage.naturalHeight : (Number(slide.height) || 1080),
          alt: slide.alt || '',
          galleryIndex,
        };
      });
  }

  function galleryIndexToPhotoSwipeIndex(galleryIndex) {
    const slides = getGallerySettings().slides || [];
    let photoSwipeIndex = 0;
    for (let i = 0; i < slides.length; i++) {
      if (!isImageSlide(slides[i].type)) {
        continue;
      }
      if (i === galleryIndex) {
        return photoSwipeIndex;
      }
      photoSwipeIndex++;
    }
    return 0;
  }

  function initPhotoSwipeLightbox(onIndexChange) {
    if (photoSwipeLightbox || !isPhotoSwipeAvailable()) {
      return photoSwipeLightbox;
    }

    photoSwipeLightbox = new PhotoSwipeLightbox({
      pswpModule: PhotoSwipe,
      bgOpacity: 1,
      wheelToZoom: true,
      showHideAnimationType: 'zoom',
      initialZoomLevel: 'fit',
      secondaryZoomLevel: 2,
      maxZoomLevel: 4,
      padding: { top: 24, bottom: 24, left: 16, right: 16 },
      indexIndicatorSep: ' / ',
    });

    photoSwipeLightbox.on('change', () => {
      const pswp = photoSwipeLightbox.pswp;
      if (!pswp || typeof onIndexChange !== 'function') {
        return;
      }
      const item = buildPhotoSwipeDataSource(null, -1)[pswp.currIndex];
      if (item?.galleryIndex !== undefined) {
        onIndexChange(item.galleryIndex);
      }
    });

    photoSwipeLightbox.init();
    return photoSwipeLightbox;
  }

  function openPhotoSwipeZoom(image, galleryIndex, event) {
    const lightbox = initPhotoSwipeLightbox();
    if (!lightbox) {
      return;
    }

    const dataSource = buildPhotoSwipeDataSource(image, galleryIndex);
    if (!dataSource.length) {
      return;
    }

    const photoSwipeIndex = galleryIndexToPhotoSwipeIndex(galleryIndex);
    const point = event && Number.isFinite(event.clientX)
      ? { x: event.clientX, y: event.clientY }
      : undefined;

    lightbox.loadAndOpen(photoSwipeIndex, dataSource, point);
  }

  function bindPhotoSwipeZoom(content, onIndexChange) {
    if (!content) {
      return;
    }

    initPhotoSwipeLightbox(onIndexChange);

    let lastTap = 0;

    content.addEventListener('click', (event) => {
      const image = event.target.closest('.ps-gallery-lightbox__image');
      if (!image) {
        return;
      }
      const modal = content.closest('[data-ps-gallery-lightbox]');
      const galleryIndex = Number(modal?.dataset.currentIndex || 0);
      const now = Date.now();
      if (now - lastTap < DOUBLE_TAP_MS) {
        event.preventDefault();
        openPhotoSwipeZoom(image, galleryIndex, event);
        lastTap = 0;
        return;
      }
      lastTap = now;
    });

    content.addEventListener('dblclick', (event) => {
      const image = event.target.closest('.ps-gallery-lightbox__image');
      if (!image) {
        return;
      }
      event.preventDefault();
      const modal = content.closest('[data-ps-gallery-lightbox]');
      const galleryIndex = Number(modal?.dataset.currentIndex || 0);
      openPhotoSwipeZoom(image, galleryIndex, event);
    });
  }

  function wrapZoomableImage(image) {
    const zoom = document.createElement('div');
    zoom.className = 'ps-gallery-lightbox__zoom';
    image.setAttribute('title', Drupal.t('Double-tap to zoom'));
    zoom.appendChild(image);
    return zoom;
  }

  function buildLightboxSlideInner(slide) {
    if (!slide) {
      return null;
    }

    switch (slide.type) {
      case 'image':
      case 'plan_image': {
        const image = document.createElement('img');
        image.src = slide.url;
        image.alt = slide.alt || '';
        image.className = 'ps-gallery-lightbox__image';
        image.loading = 'eager';
        image.decoding = 'async';
        return wrapZoomableImage(image);
      }

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

  function buildLightboxSlideElement(slide) {
    const wrapper = document.createElement('div');
    wrapper.className = `ps-gallery-lightbox__slide ps-gallery-lightbox__slide--${slide.type}`;
    const inner = buildLightboxSlideInner(slide);
    if (inner) {
      wrapper.appendChild(inner);
    }
    return wrapper;
  }

  function renderLightboxSlideAnimated(container, slide, direction) {
    if (!container || !slide) {
      return;
    }

    const incoming = buildLightboxSlideElement(slide);
    const animate = direction && !prefersReducedMotion();
    const outgoing = container.querySelector('.ps-gallery-lightbox__slide.is-active')
      || container.querySelector('.ps-gallery-lightbox__slide');

    if (!animate || !outgoing) {
      container.innerHTML = '';
      incoming.classList.add('is-active');
      container.appendChild(incoming);
      return;
    }

    incoming.classList.add(direction === 'prev' ? 'is-entering-prev' : 'is-entering-next');
    container.appendChild(incoming);

    outgoing.classList.remove('is-active');
    outgoing.classList.add('is-leaving', direction === 'prev' ? 'is-leaving-prev' : 'is-leaving-next');

    const finalize = () => {
      outgoing.remove();
      incoming.classList.remove('is-entering-prev', 'is-entering-next');
      incoming.classList.add('is-active');
    };

    let completed = false;
    const onEnd = (event) => {
      if (completed || event.propertyName !== 'transform') {
        return;
      }
      completed = true;
      finalize();
    };

    outgoing.addEventListener('transitionend', onEnd);
    window.setTimeout(() => {
      if (!completed) {
        completed = true;
        finalize();
      }
    }, SLIDE_TRANSITION_MS + 40);

    window.requestAnimationFrame(() => {
      window.requestAnimationFrame(() => {
        incoming.classList.add('is-active');
      });
    });
  }

  function updateLightboxThumbs(modal, index) {
    modal.querySelectorAll('[data-ps-lightbox-thumb]').forEach((thumb) => {
      const isActive = Number(thumb.dataset.index) === index;
      thumb.classList.toggle('is-active', isActive);
      thumb.setAttribute('aria-current', isActive ? 'true' : 'false');
    });
  }

  function updateLightboxCounter(modal, index, total) {
    const counter = modal.querySelector('[data-ps-lightbox-counter]');
    if (!counter) {
      return;
    }
    counter.textContent = Drupal.t('@current / @total', {
      '@current': index + 1,
      '@total': total,
    });
  }

  function scrollActiveThumbIntoView(modal, index) {
    const track = modal.querySelector('.ps-gallery-lightbox__thumbs-track');
    const thumb = modal.querySelector(`[data-ps-lightbox-thumb][data-index="${index}"]`);
    if (!track || !thumb) {
      return;
    }

    const trackRect = track.getBoundingClientRect();
    const thumbRect = thumb.getBoundingClientRect();
    const thumbCenter = thumbRect.left + (thumbRect.width / 2);
    const trackCenter = trackRect.left + (trackRect.width / 2);
    const delta = thumbCenter - trackCenter;

    track.scrollTo({
      left: track.scrollLeft + delta,
      behavior: prefersReducedMotion() ? 'auto' : 'smooth',
    });
  }

  function focusActiveThumb(modal, index, navigationSource) {
    if (navigationSource === 'keyboard-arrow' || navigationSource === 'swipe') {
      return;
    }
    const thumb = modal.querySelector(`[data-ps-lightbox-thumb][data-index="${index}"]`);
    if (!thumb) {
      return;
    }
    window.setTimeout(() => {
      thumb.focus({ preventScroll: true });
    }, prefersReducedMotion() ? 0 : SLIDE_TRANSITION_MS);
  }

  function preloadAdjacentSlides(slides, index) {
    const length = slides.length;
    [-1, 1].forEach((offset) => {
      const safeIndex = normalizeIndex(index + offset, length);
      const slide = slides[safeIndex];
      if (!slide || !isImageSlide(slide.type) || !slide.url) {
        return;
      }
      const preloader = new Image();
      preloader.decoding = 'async';
      preloader.src = slide.url;
    });
  }

  function resolveLightboxDirection(currentIndex, nextIndex, length) {
    if (currentIndex === nextIndex) {
      return null;
    }

    const forwardSteps = (nextIndex - currentIndex + length) % length;
    const backwardSteps = (currentIndex - nextIndex + length) % length;
    return forwardSteps <= backwardSteps ? 'next' : 'prev';
  }

  function renderLightboxSlide(modal, index, direction, navigationSource) {
    const settings = getGallerySettings();
    const slides = settings.slides || [];
    const content = modal.querySelector('[data-ps-lightbox-content]');
    if (!slides.length || !content) {
      return null;
    }

    const currentIndex = Number(modal.dataset.currentIndex || 0);
    const safeIndex = normalizeIndex(index, slides.length);
    const resolvedDirection = direction || resolveLightboxDirection(currentIndex, safeIndex, slides.length);

    renderLightboxSlideAnimated(content, slides[safeIndex], resolvedDirection);
    modal.dataset.currentIndex = String(safeIndex);
    updateLightboxThumbs(modal, safeIndex);
    updateLightboxCounter(modal, safeIndex, slides.length);
    scrollActiveThumbIntoView(modal, safeIndex);
    preloadAdjacentSlides(slides, safeIndex);
    focusActiveThumb(modal, safeIndex, navigationSource);

    const status = modal.querySelector('[data-ps-lightbox-status]');
    const slide = slides[safeIndex];
    if (status) {
      const label = slide.label || slide.alt || '';
      const position = Drupal.t('@current of @total', {
        '@current': safeIndex + 1,
        '@total': slides.length,
      });
      status.textContent = label ? `${position} — ${label}` : position;
    }

    return safeIndex;
  }

  function bindLightboxSwipe(content, onSwipe) {
    if (!content) {
      return;
    }

    let startX = 0;
    let startY = 0;
    let tracking = false;

    content.addEventListener('pointerdown', (event) => {
      if (event.pointerType === 'mouse' && event.button !== 0) {
        return;
      }
      startX = event.clientX;
      startY = event.clientY;
      tracking = true;
    });

    const endSwipe = (event) => {
      if (!tracking) {
        return;
      }
      tracking = false;
      const deltaX = event.clientX - startX;
      const deltaY = event.clientY - startY;
      if (Math.abs(deltaX) < SWIPE_THRESHOLD_PX || Math.abs(deltaX) < Math.abs(deltaY)) {
        return;
      }
      onSwipe(deltaX < 0 ? 'next' : 'prev');
    };

    content.addEventListener('pointerup', endSwipe);
    content.addEventListener('pointercancel', () => {
      tracking = false;
    });
  }

  function ensureLightboxInBody(modal) {
    if (modal.parentElement !== document.body) {
      document.body.appendChild(modal);
    }
  }

  function openLightbox(index) {
    const modal = document.querySelector('[data-ps-gallery-lightbox]');
    if (!modal) {
      return;
    }

    const settings = getGallerySettings();
    if (!settings.slides?.length) {
      return;
    }

    ensureLightboxInBody(modal);
    renderLightboxSlide(modal, index, null, 'open');
    const bootstrapModal = window.bootstrap?.Modal?.getOrCreateInstance(modal);
    bootstrapModal?.show();
  }

  Drupal.behaviors.psOfferGallery = {
    attach(context) {
      once('ps-offer-gallery-hero', '[data-ps-gallery-hero]', context).forEach((hero) => {
        const stage = hero.querySelector('[data-ps-gallery-stage]');
        let heroInitialized = false;

        const getHeroIndex = () => Number(hero.dataset.psGalleryIndex || 0);

        const setHeroIndex = (index) => {
          hero.dataset.psGalleryIndex = String(index);
        };

        const renderHero = (direction) => {
          const slides = getSlides(hero);
          if (!stage || !slides.length) {
            return;
          }
          const safeIndex = normalizeIndex(getHeroIndex(), slides.length);
          setHeroIndex(safeIndex);

          if (!heroInitialized) {
            heroInitialized = true;
            const existing = stage.querySelector('.ps-media-gallery-hero__slide');
            if (existing) {
              existing.classList.add('is-active');
              return;
            }
          }

          renderHeroSlide(stage, slides[safeIndex], direction);
        };

        hero.psOfferGallery = {
          setIndex: setHeroIndex,
          render: () => renderHero(),
        };

        renderHero();

        hero.querySelector('[data-ps-gallery-prev]')?.addEventListener('click', (event) => {
          event.preventDefault();
          event.stopPropagation();
          setHeroIndex(getHeroIndex() - 1);
          renderHero('prev');
        });
        hero.querySelector('[data-ps-gallery-next]')?.addEventListener('click', (event) => {
          event.preventDefault();
          event.stopPropagation();
          setHeroIndex(getHeroIndex() + 1);
          renderHero('next');
        });
        hero.querySelectorAll('[data-ps-gallery-open]').forEach((trigger) => {
          trigger.addEventListener('click', () => {
            const entry = trigger.getAttribute('data-ps-gallery-entry');
            if (entry) {
              openLightbox(resolveEntryIndex(entry));
              return;
            }
            openLightbox(getHeroIndex());
          });
        });
      });

      once('ps-offer-gallery-lightbox', '[data-ps-gallery-lightbox]', context).forEach((modal) => {
        const content = modal.querySelector('[data-ps-lightbox-content]');

        const clearContent = () => {
          if (content) {
            content.innerHTML = '';
          }
        };

        const navigateLightbox = (index, direction, navigationSource) => {
          renderLightboxSlide(modal, index, direction, navigationSource);
        };

        bindLightboxSwipe(content, (direction) => {
          const currentIndex = Number(modal.dataset.currentIndex || 0);
          navigateLightbox(
            direction === 'next' ? currentIndex + 1 : currentIndex - 1,
            direction,
            'swipe'
          );
        });

        bindPhotoSwipeZoom(content, (galleryIndex) => {
          const currentIndex = Number(modal.dataset.currentIndex || 0);
          if (galleryIndex !== currentIndex) {
            navigateLightbox(galleryIndex, null, 'photoswipe');
          }
        });

        modal.addEventListener('hidden.bs.modal', () => {
          const index = Number(modal.dataset.currentIndex || 0);
          syncHeroIndex(index);
          clearContent();
          document.body.classList.remove('ps-gallery-lightbox-open');
        });

        modal.querySelector('[data-ps-lightbox-prev]')?.addEventListener('click', (event) => {
          event.preventDefault();
          const currentIndex = Number(modal.dataset.currentIndex || 0);
          navigateLightbox(currentIndex - 1, 'prev', 'nav');
        });
        modal.querySelector('[data-ps-lightbox-next]')?.addEventListener('click', (event) => {
          event.preventDefault();
          const currentIndex = Number(modal.dataset.currentIndex || 0);
          navigateLightbox(currentIndex + 1, 'next', 'nav');
        });
        modal.querySelectorAll('[data-ps-lightbox-thumb]').forEach((thumb) => {
          thumb.addEventListener('click', (event) => {
            event.preventDefault();
            const currentIndex = Number(modal.dataset.currentIndex || 0);
            const nextIndex = Number(thumb.dataset.index || 0);
            if (nextIndex === currentIndex) {
              return;
            }
            navigateLightbox(nextIndex, null, 'thumb');
          });
        });

        modal.addEventListener('keydown', (event) => {
          if (!modal.classList.contains('show')) {
            return;
          }

          const currentIndex = Number(modal.dataset.currentIndex || 0);
          if (event.key === 'ArrowLeft') {
            event.preventDefault();
            navigateLightbox(currentIndex - 1, 'prev', 'keyboard-arrow');
          }
          else if (event.key === 'ArrowRight') {
            event.preventDefault();
            navigateLightbox(currentIndex + 1, 'next', 'keyboard-arrow');
          }
        });

        modal.addEventListener('shown.bs.modal', () => {
          const currentIndex = Number(modal.dataset.currentIndex || 0);
          scrollActiveThumbIntoView(modal, currentIndex);
          document.body.classList.add('ps-gallery-lightbox-open');
        });
      });
    },
  };
})(Drupal, once);
