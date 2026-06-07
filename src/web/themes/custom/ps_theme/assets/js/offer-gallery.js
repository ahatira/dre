(function (Drupal, once) {
  'use strict';

  const IMAGE_SLIDE_TYPES = ['image', 'plan_image'];
  const SLIDE_TRANSITION_MS = 360;

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

  function createIframe(src, title) {
    const iframe = document.createElement('iframe');
    iframe.src = src;
    iframe.setAttribute('loading', 'lazy');
    iframe.setAttribute('allowfullscreen', '');
    iframe.setAttribute('title', title || '');
    iframe.className = 'ps-gallery-lightbox__iframe';
    return iframe;
  }

  function renderSlideContent(container, slide) {
    if (!container || !slide) {
      return;
    }

    container.innerHTML = '';

    switch (slide.type) {
      case 'image':
      case 'plan_image': {
        const image = document.createElement('img');
        image.src = slide.url;
        image.alt = slide.alt || '';
        image.className = 'ps-gallery-lightbox__image';
        container.appendChild(image);
        break;
      }

      case 'video_oembed': {
        container.appendChild(createIframe(slide.embed_url, slide.alt));
        break;
      }

      case 'video_file':
      case 'video_url': {
        const video = document.createElement('video');
        video.controls = true;
        video.preload = 'metadata';
        video.className = 'ps-gallery-lightbox__video';
        const source = document.createElement('source');
        source.src = slide.video_url;
        video.appendChild(source);
        container.appendChild(video);
        break;
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
        container.appendChild(wrapper);
        break;
      }

      case 'plan_pdf': {
        container.appendChild(createIframe(slide.iframe_url, slide.alt));
        break;
      }

      default:
        break;
    }
  }

  function updateLightboxThumbs(modal, index) {
    modal.querySelectorAll('[data-ps-lightbox-thumb]').forEach((thumb) => {
      thumb.classList.toggle('is-active', Number(thumb.dataset.index) === index);
    });
  }

  function renderLightboxSlide(modal, index) {
    const settings = getGallerySettings();
    const slides = settings.slides || [];
    const content = modal.querySelector('[data-ps-lightbox-content]');
    if (!slides.length || !content) {
      return null;
    }

    const safeIndex = normalizeIndex(index, slides.length);
    renderSlideContent(content, slides[safeIndex]);
    modal.dataset.currentIndex = String(safeIndex);
    updateLightboxThumbs(modal, safeIndex);
    return safeIndex;
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
    renderLightboxSlide(modal, index);
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

        modal.addEventListener('hide.bs.modal', clearContent);

        modal.querySelector('[data-ps-lightbox-prev]')?.addEventListener('click', () => {
          const currentIndex = Number(modal.dataset.currentIndex || 0);
          renderLightboxSlide(modal, currentIndex - 1);
        });
        modal.querySelector('[data-ps-lightbox-next]')?.addEventListener('click', () => {
          const currentIndex = Number(modal.dataset.currentIndex || 0);
          renderLightboxSlide(modal, currentIndex + 1);
        });
        modal.querySelectorAll('[data-ps-lightbox-thumb]').forEach((thumb) => {
          thumb.addEventListener('click', () => {
            renderLightboxSlide(modal, Number(thumb.dataset.index || 0));
          });
        });
      });
    },
  };
})(Drupal, once);
