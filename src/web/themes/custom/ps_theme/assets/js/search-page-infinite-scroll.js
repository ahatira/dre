(function (Drupal, once, $) {
  'use strict';

  const SCROLL_THRESHOLD = 200;
  const MAX_AUTO_LOAD_PAGES = 20;

  /**
   * @namespace
   */
  Drupal.psSearchPage = Drupal.psSearchPage || {};

  /**
   * Returns the list scroll container for infinite scroll.
   *
   * @param {HTMLElement} root
   *   Search view root.
   *
   * @return {HTMLElement|null}
   *   Scrollable list panel.
   */
  Drupal.psSearchPage.getListScrollEl = function (root) {
    return root?.querySelector('.js-ps-search-list-panel') || null;
  };

  /**
   * Clicks the Views Infinite Scroll "next" link when present.
   *
   * @param {HTMLElement} root
   *   Search view root.
   *
   * @return {boolean}
   *   TRUE when a load-more action was triggered.
   */
  Drupal.psSearchPage.triggerLoadMore = function (root) {
    const nextLink = root.querySelector('[data-drupal-views-infinite-scroll-pager] [rel="next"]');
    if (!nextLink) {
      return false;
    }
    nextLink.click();
    return true;
  };

  /**
   * Loads additional pages until an offer card appears or limits are hit.
   *
   * @param {HTMLElement} root
   *   Search view root.
   * @param {string} nid
   *   Offer node id.
   *
   * @return {Promise<HTMLElement|null>}
   *   Resolves with the card element when found.
   */
  Drupal.psSearchPage.loadUntilOfferCard = function (root, nid) {
    const existing = root.querySelector(`.ps-offer-search-card[data-offer-id="${nid}"]`);
    if (existing) {
      return Promise.resolve(existing);
    }

    let loads = 0;

    return new Promise(function (resolve) {
      const onNewContent = function () {
        const card = root.querySelector(`.ps-offer-search-card[data-offer-id="${nid}"]`);
        if (card) {
          root.removeEventListener('ps-search-infinite-scroll-new-content', onNewContent);
          resolve(card);
          return;
        }

        loads += 1;
        if (loads >= MAX_AUTO_LOAD_PAGES || !Drupal.psSearchPage.triggerLoadMore(root)) {
          root.removeEventListener('ps-search-infinite-scroll-new-content', onNewContent);
          resolve(null);
        }
      };

      root.addEventListener('ps-search-infinite-scroll-new-content', onNewContent);

      if (!Drupal.psSearchPage.triggerLoadMore(root)) {
        root.removeEventListener('ps-search-infinite-scroll-new-content', onNewContent);
        resolve(null);
      }
    });
  };

  /**
   * Fetches a single offer card via Views AJAX (block_offer_card display).
   *
   * @param {string} nid
   *   Offer node id.
   *
   * @return {Promise<HTMLElement|null>}
   *   Parsed offer card element.
   */
  Drupal.psSearchPage.fetchOfferCard = function (nid) {
    const params = new URLSearchParams({
      _drupal_ajax: '1',
      view_name: 'ps_search_offers',
      view_display_id: 'block_offer_card',
      view_args: String(nid),
    });

    return fetch(`${Drupal.url('views/ajax')}?${params.toString()}`, {
      headers: {
        Accept: 'application/vnd.drupal-ajax',
      },
      credentials: 'same-origin',
    })
      .then(function (response) {
        if (!response.ok) {
          return null;
        }
        return response.json();
      })
      .then(function (commands) {
        if (!Array.isArray(commands)) {
          return null;
        }

        const wrapper = document.createElement('div');
        commands.forEach(function (command) {
          if (command.command === 'insert' && command.data) {
            wrapper.insertAdjacentHTML('beforeend', command.data);
          }
        });

        return wrapper.querySelector(`.ps-offer-search-card[data-offer-id="${nid}"]`);
      })
      .catch(function () {
        return null;
      });
  };

  Drupal.behaviors.psSearchPageInfiniteScroll = {
    attach(context) {
      once('ps-search-infinite-scroll', '.ps-search-view', context).forEach(function (root) {
        const scrollEl = Drupal.psSearchPage.getListScrollEl(root);
        if (!scrollEl) {
          return;
        }

        // Views Infinite Scroll listens on window; our list scrolls in a panel.
        scrollEl.addEventListener('scroll', Drupal.debounce(function () {
          if (drupalSettings.psSearch?.listLoadAll) {
            return;
          }

          const pager = root.querySelector('[data-drupal-views-infinite-scroll-pager="automatic"]');
          if (!pager || !scrollEl.contains(pager)) {
            return;
          }

          const remaining = scrollEl.scrollHeight - scrollEl.scrollTop - scrollEl.clientHeight;
          if (remaining < SCROLL_THRESHOLD) {
            const nextLink = pager.querySelector('[rel="next"]');
            if (nextLink) {
              nextLink.click();
            }
          }
        }, 200));

        // Bridge VIS jQuery event to native custom event for map/list sync.
        $(root).on('views_infinite_scroll.new_content', function () {
          root.dispatchEvent(new CustomEvent('ps-search-infinite-scroll-new-content'));
        });
      });
    },
  };
}(Drupal, once, jQuery));
