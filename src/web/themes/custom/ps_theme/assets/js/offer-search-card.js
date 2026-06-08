(function (Drupal, once) {
  'use strict';

  const SLIDE_DURATION_MS = 400;

  /**
   * Whether the user prefers reduced motion.
   *
   * @return {boolean}
   */
  function prefersReducedMotion() {
    return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  }

  /**
   * Shows the "Already viewed" badge when applicable.
   *
   * @param {HTMLElement} card
   *   Offer search card root element.
   */
  function applyViewedBadge(card) {
    const nodeId = card.getAttribute('data-offer-id');
    const badge = card.querySelector('[data-offer-viewed-badge]');
    if (!nodeId || !badge || !Drupal.psOfferViewed?.has(nodeId)) {
      return;
    }
    badge.hidden = false;
    badge.classList.remove('is-hidden');
  }

  /**
   * Initializes horizontal slide navigation for a card gallery.
   *
   * @param {HTMLElement} media
   *   Media wrapper element.
   */
  function initGallery(media) {
    const track = media.querySelector('[data-offer-search-card-track]');
    const slides = track ? track.querySelectorAll('.ps-offer-search-card__slide') : [];
    if (!track || slides.length < 2) {
      return;
    }

    const prev = media.querySelector('.ps-offer-search-card__nav--prev');
    const next = media.querySelector('.ps-offer-search-card__nav--next');
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

  Drupal.behaviors.psOfferSearchCard = {
    attach(context) {
      once('ps-offer-search-card', '.ps-offer-search-card[data-offer-id]', context).forEach((card) => {
        applyViewedBadge(card);

        const media = card.querySelector('.ps-offer-search-card__media');
        if (media) {
          initGallery(media);
        }
      });

      once('ps-offer-search-card-favorite', '.ps-offer-search-card__action--favorite', context).forEach((wrapper) => {
        wrapper.addEventListener('click', (event) => {
          event.stopPropagation();
        });
      });
    },
  };
})(Drupal, once);
