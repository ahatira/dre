(function (Drupal, once, $) {
  'use strict';

  const MAX_AUTO_LOAD_PAGES = 20;

  /**
   * @namespace
   */
  Drupal.psSearchPage = Drupal.psSearchPage || {};

  /**
   * Returns the list scroll container.
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
   * Returns offer node ids currently rendered in the list panel.
   *
   * @param {HTMLElement} root
   *   Search view root.
   *
   * @return {Set<string>}
   *   Loaded list offer ids.
   */
  Drupal.psSearchPage.getListOfferNids = function (root) {
    const nids = new Set();
    const listPanel = Drupal.psSearchPage.getListScrollEl(root);
    if (!listPanel) {
      return nids;
    }

    listPanel.querySelectorAll('.ps-offer-search-card[data-offer-id]').forEach(function (card) {
      const nid = card.getAttribute('data-offer-id');
      if (nid) {
        nids.add(String(nid));
      }
    });

    return nids;
  };

  /**
   * Clicks the Views Load More pager link when present.
   *
   * @param {HTMLElement} root
   *   Search view root.
   *
   * @return {boolean}
   *   TRUE when a load-more action was triggered.
   */
  Drupal.psSearchPage.triggerLoadMore = function (root) {
    const loadMoreLink = root.querySelector('.pager--load-more a[href]');
    if (!loadMoreLink) {
      return false;
    }
    loadMoreLink.click();
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
          root.removeEventListener('ps-search-list-new-content', onNewContent);
          resolve(card);
          return;
        }

        loads += 1;
        if (loads >= MAX_AUTO_LOAD_PAGES || !Drupal.psSearchPage.triggerLoadMore(root)) {
          root.removeEventListener('ps-search-list-new-content', onNewContent);
          resolve(null);
        }
      };

      root.addEventListener('ps-search-list-new-content', onNewContent);

      if (!Drupal.psSearchPage.triggerLoadMore(root)) {
        root.removeEventListener('ps-search-list-new-content', onNewContent);
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

  Drupal.behaviors.psSearchPageLoadMore = {
    attach(context) {
      once('ps-search-load-more', '.ps-search-view', context).forEach(function (root) {
        // Bridge views_load_more jQuery event to native custom event for map/list sync.
        $(root).on('views_load_more.new_content', function () {
          root.dispatchEvent(new CustomEvent('ps-search-list-new-content'));
        });
      });
    },
  };
}(Drupal, once, jQuery));
