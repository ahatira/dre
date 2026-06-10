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
   * Clears Drupal once markers so behaviors can re-attach on cloned cards.
   *
   * @param {HTMLElement} root
   *   Root element whose subtree should be reset.
   */
  function resetOnceAttributes(root) {
    root.removeAttribute('data-once');
    root.removeAttribute('data-drupal-once');
    root.querySelectorAll('[data-once], [data-drupal-once]').forEach(function (element) {
      element.removeAttribute('data-once');
      element.removeAttribute('data-drupal-once');
    });
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

  /**
   * Prepares comparator action (placeholder until comparator module ships).
   *
   * @param {HTMLElement} card
   *   Offer search card root element.
   */
  function initComparator(card) {
    const compareBtn = card.querySelector('.ps-offer-search-card__action--compare');
    if (!compareBtn || compareBtn.dataset.psCompareBound) {
      return;
    }

    compareBtn.dataset.psCompareBound = '1';
    compareBtn.addEventListener('click', function (event) {
      event.preventDefault();
      event.stopPropagation();
      if (typeof Drupal.announce === 'function') {
        Drupal.announce(Drupal.t('Comparator coming soon.'));
      }
    });
  }

  /**
   * Initializes favorite wrapper click isolation.
   *
   * @param {HTMLElement} card
   *   Offer search card root element.
   */
  function initFavoriteWrapper(card) {
    const wrapper = card.querySelector('.ps-offer-search-card__action--favorite');
    if (!wrapper || wrapper.dataset.psFavoriteWrapperBound) {
      return;
    }

    wrapper.dataset.psFavoriteWrapperBound = '1';
    wrapper.addEventListener('click', function (event) {
      event.stopPropagation();
    });
  }

  /**
   * Navigates to the offer page when the card is clicked (BNPPRE parity).
   *
   * Compare, favorite, carousel controls and explicit links are excluded.
   *
   * @param {HTMLElement} card
   *   Offer search card root element.
   */
  function initCardNavigation(card) {
    const cta = card.querySelector('.ps-offer-search-card__cta');
    if (!cta || card.dataset.psCardNavBound) {
      return;
    }

    card.dataset.psCardNavBound = '1';
    card.classList.add('ps-offer-search-card--clickable');

    card.addEventListener('click', function (event) {
      if (event.target.closest('.ps-offer-search-card__action, .ps-offer-search-card__nav, a, button, input, label')) {
        return;
      }

      const url = cta.getAttribute('href');
      if (!url) {
        return;
      }

      const nodeId = card.getAttribute('data-offer-id');
      if (nodeId && Drupal.psOfferViewed?.mark) {
        Drupal.psOfferViewed.mark(nodeId);
        applyViewedBadge(card);
      }

      window.location.assign(url);
    });
  }

  /**
   * Initializes interactions on a single offer search card.
   *
   * @param {HTMLElement} card
   *   Offer search card root element.
   */
  function initCard(card) {
    if (!card || !card.matches('.ps-offer-search-card[data-offer-id]')) {
      return;
    }

    applyViewedBadge(card);
    initCardNavigation(card);
    initComparator(card);
    initFavoriteWrapper(card);

    const media = card.querySelector('.ps-offer-search-card__media');
    if (media) {
      initGallery(media);
    }
  }

  Drupal.psOfferSearchCard = Drupal.psOfferSearchCard || {};
  Drupal.psOfferSearchCard.initCard = initCard;
  Drupal.psOfferSearchCard.resetOnceAttributes = resetOnceAttributes;

  Drupal.behaviors.psOfferSearchCard = {
    attach(context) {
      once('ps-offer-search-card', '.ps-offer-search-card[data-offer-id]', context).forEach(function (card) {
        initCard(card);
      });
    },
  };
})(Drupal, once);
