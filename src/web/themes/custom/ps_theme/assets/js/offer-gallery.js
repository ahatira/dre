(function (Drupal, once) {
  'use strict';

  function getImages(root) {
    const template = root.querySelector('[data-ps-gallery-images]');
    if (!template) {
      return [];
    }
    return Array.from(template.querySelectorAll('[data-url]')).map((node) => ({
      url: node.getAttribute('data-url'),
      alt: node.getAttribute('data-alt') || '',
    }));
  }

  function openLightbox(index) {
    const modal = document.querySelector('[data-ps-gallery-lightbox]');
    if (!modal) {
      return;
    }
    const image = modal.querySelector('[data-ps-lightbox-image]');
    const settings = drupalSettings.psOfferGallery || {};
    const images = settings.images || [];
    if (!images.length || !image) {
      return;
    }
    const safeIndex = ((index % images.length) + images.length) % images.length;
    image.src = images[safeIndex].url;
    image.alt = images[safeIndex].alt || '';
    modal.querySelectorAll('[data-ps-lightbox-thumb]').forEach((thumb) => {
      thumb.classList.toggle('is-active', Number(thumb.dataset.index) === safeIndex);
    });
    modal.dataset.currentIndex = String(safeIndex);
    const bootstrapModal = window.bootstrap?.Modal?.getOrCreateInstance(modal);
    bootstrapModal?.show();
  }

  Drupal.behaviors.psOfferGallery = {
    attach(context) {
      once('ps-offer-gallery-hero', '[data-ps-gallery-hero]', context).forEach((hero) => {
        const images = getImages(hero);
        let currentIndex = 0;
        const mainImage = hero.querySelector('.ps-media-gallery-hero__image');

        const renderHero = () => {
          if (!mainImage || !images.length) {
            return;
          }
          currentIndex = ((currentIndex % images.length) + images.length) % images.length;
          mainImage.src = images[currentIndex].url;
          mainImage.alt = images[currentIndex].alt || '';
        };

        hero.querySelector('[data-ps-gallery-prev]')?.addEventListener('click', () => {
          currentIndex -= 1;
          renderHero();
        });
        hero.querySelector('[data-ps-gallery-next]')?.addEventListener('click', () => {
          currentIndex += 1;
          renderHero();
        });
        hero.querySelectorAll('[data-ps-gallery-open]').forEach((trigger) => {
          trigger.addEventListener('click', () => {
            const index = Number(trigger.getAttribute('data-ps-gallery-index') || currentIndex);
            openLightbox(index);
          });
        });
      });

      once('ps-offer-gallery-lightbox', '[data-ps-gallery-lightbox]', context).forEach((modal) => {
        const image = modal.querySelector('[data-ps-lightbox-image]');
        const settings = drupalSettings.psOfferGallery || {};
        const images = settings.images || [];
        let currentIndex = Number(modal.dataset.currentIndex || 0);

        const renderModal = () => {
          if (!image || !images.length) {
            return;
          }
          currentIndex = ((currentIndex % images.length) + images.length) % images.length;
          image.src = images[currentIndex].url;
          image.alt = images[currentIndex].alt || '';
          modal.dataset.currentIndex = String(currentIndex);
          modal.querySelectorAll('[data-ps-lightbox-thumb]').forEach((thumb) => {
            thumb.classList.toggle('is-active', Number(thumb.dataset.index) === currentIndex);
          });
        };

        modal.querySelector('[data-ps-lightbox-prev]')?.addEventListener('click', () => {
          currentIndex -= 1;
          renderModal();
        });
        modal.querySelector('[data-ps-lightbox-next]')?.addEventListener('click', () => {
          currentIndex += 1;
          renderModal();
        });
        modal.querySelectorAll('[data-ps-lightbox-thumb]').forEach((thumb) => {
          thumb.addEventListener('click', () => {
            currentIndex = Number(thumb.dataset.index || 0);
            renderModal();
          });
        });
      });
    },
  };
})(Drupal, once);
