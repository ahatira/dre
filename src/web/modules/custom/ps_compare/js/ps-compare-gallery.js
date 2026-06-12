(function (Drupal, once) {
  'use strict';

  const SLIDE_DURATION_MS = 400;

  /**
   * @return {boolean}
   */
  function prefersReducedMotion() {
    return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  }

  /**
   * @param {HTMLElement} media
   *   Gallery media wrapper.
   */
  function initGallery(media) {
    const track = media.querySelector('[data-ps-compare-gallery-track]');
    const slides = track ? track.querySelectorAll('.ps-compare-gallery__slide') : [];
    if (!track || slides.length < 2) {
      return;
    }

    const prev = media.querySelector('.ps-compare-gallery__nav--prev');
    const next = media.querySelector('.ps-compare-gallery__nav--next');
    if (!prev || !next) {
      return;
    }

    let index = 0;
    let animating = false;

    const update = () => {
      track.style.transform = `translateX(-${index * 100}%)`;
      prev.disabled = index === 0;
      next.disabled = index === slides.length - 1;
    };

    const goTo = (direction) => {
      if (animating) {
        return;
      }

      const nextIndex = index + direction;
      if (nextIndex < 0 || nextIndex >= slides.length) {
        return;
      }

      index = nextIndex;
      update();

      if (prefersReducedMotion()) {
        return;
      }

      animating = true;
      window.setTimeout(() => {
        animating = false;
      }, SLIDE_DURATION_MS);
    };

    prev.addEventListener('click', (event) => {
      event.preventDefault();
      event.stopPropagation();
      goTo(-1);
    });

    next.addEventListener('click', (event) => {
      event.preventDefault();
      event.stopPropagation();
      goTo(1);
    });

    update();
  }

  Drupal.behaviors.psCompareGallery = {
    attach(context) {
      once('ps-compare-gallery', '[data-ps-compare-gallery]', context).forEach((gallery) => {
        const media = gallery.querySelector('.ps-compare-gallery__media');
        if (media) {
          initGallery(media);
        }
      });
    },
  };
})(Drupal, once);
