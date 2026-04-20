(function (Drupal, once, drupalSettings) {
  'use strict';

  const STORAGE_KEY = 'ps_offer_viewed_ids';

  function toNumber(value) {
    const parsed = Number.parseInt(String(value || ''), 10);
    return Number.isNaN(parsed) ? 0 : parsed;
  }

  function readViewedIds() {
    try {
      const raw = window.localStorage.getItem(STORAGE_KEY);
      const parsed = raw ? JSON.parse(raw) : [];
      if (!Array.isArray(parsed)) {
        return [];
      }
      return parsed.map(toNumber).filter((id) => id > 0);
    }
    catch (error) {
      return [];
    }
  }

  function writeViewedIds(ids) {
    const unique = Array.from(new Set(ids.map(toNumber).filter((id) => id > 0)));
    try {
      window.localStorage.setItem(STORAGE_KEY, JSON.stringify(unique));
    }
    catch (error) {
      // Ignore storage errors and keep progressive behavior.
    }
    return unique;
  }

  function mergeViewedIdsFromSettings() {
    const settings = drupalSettings.psOfferCardSearch || {};
    const incoming = Array.isArray(settings.viewedOfferIds) ? settings.viewedOfferIds : [];
    if (incoming.length === 0) {
      return readViewedIds();
    }

    const current = readViewedIds();
    return writeViewedIds(current.concat(incoming));
  }

  function revealViewedBadges(viewedIds, context) {
    const viewedSet = new Set(viewedIds);

    once('ps-offer-viewed-card', '.ps-card-offer-search[data-offer-id]', context).forEach((card) => {
      const offerId = toNumber(card.getAttribute('data-offer-id'));
      if (!viewedSet.has(offerId)) {
        return;
      }

      card.classList.add('is-viewed');
      const badge = card.querySelector('.js-ps-offer-viewed-badge');
      if (badge) {
        badge.classList.remove('is-hidden');
      }
    });
  }

  function setComparatorState(link, isActive) {
    link.classList.toggle('is-active', isActive);
    link.setAttribute('aria-pressed', isActive ? 'true' : 'false');
  }

  function bindComparatorLinks(context) {
    once('ps-offer-compare-link', '.js-ps-offer-compare', context).forEach((link) => {
      link.addEventListener('click', (event) => {
        event.preventDefault();

        const endpoint = (link.getAttribute('data-ps-compare-url') || '').trim();
        const nextState = link.getAttribute('aria-pressed') !== 'true';

        if (!endpoint) {
          setComparatorState(link, nextState);
          return;
        }

        fetch(endpoint, {
          method: 'POST',
          credentials: 'same-origin',
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            Accept: 'application/json',
          },
        })
          .then((response) => {
            if (!response.ok) {
              throw new Error('Comparator endpoint error');
            }
            return response.json();
          })
          .then(() => {
            setComparatorState(link, nextState);
          })
          .catch(() => {
            // Keep UI responsive even while compare backend is pending.
            setComparatorState(link, nextState);
          });
      });
    });
  }

  Drupal.behaviors.psOfferCardSearchTracking = {
    attach(context) {
      const viewedIds = mergeViewedIdsFromSettings();
      revealViewedBadges(viewedIds, context);
      bindComparatorLinks(context);
    },
  };
})(Drupal, once, drupalSettings);
