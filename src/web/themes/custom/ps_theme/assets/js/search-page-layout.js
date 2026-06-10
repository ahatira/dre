(function (Drupal, once) {
  'use strict';

  /**
   * Triggers map resize when search chrome (filter bar) height changes.
   *
   * @param {HTMLElement} root
   *   Search view root element.
   */
  function syncSearchLayout(root) {
    const filter = root.querySelector('.ps-search-view__filter-bar');
    const mobile = root.querySelector('.ps-search-view__mobile-toolbar-wrap');
    const chromeHeight = (filter?.offsetHeight || 0) + (mobile?.offsetHeight || 0);

    root.style.setProperty('--ps-search-chrome-height', `${chromeHeight}px`);
    Drupal.psSearchMap.resizeMaps(root);
  }

  Drupal.behaviors.psSearchPageLayout = {
    attach(context) {
      once('ps-search-layout', '.ps-search-view', context).forEach(function (root) {
        const hideBtn = root.querySelector('.js-ps-hide-list');
        const showBtn = root.querySelector('.js-ps-show-list');

        syncSearchLayout(root);

        if (typeof ResizeObserver !== 'undefined') {
          const observer = new ResizeObserver(function () {
            syncSearchLayout(root);
          });
          observer.observe(root);
          const filter = root.querySelector('.ps-search-view__filter-bar');
          if (filter) {
            observer.observe(filter);
          }
        }
        else {
          window.addEventListener('resize', function () {
            syncSearchLayout(root);
          });
        }

        if (hideBtn) {
          hideBtn.addEventListener('click', function () {
            Drupal.psSearchMap.setListVisible(root, false);
          });
        }

        if (showBtn) {
          showBtn.addEventListener('click', function () {
            Drupal.psSearchMap.setListVisible(root, true);
          });
        }

        root.addEventListener('change', function (event) {
          const sortSelect = event.target.closest('.js-ps-sort-select');
          if (!sortSelect || !root.contains(sortSelect)) {
            return;
          }

          const url = new URL(window.location.href);
          const parts = sortSelect.value.split('|');
          url.searchParams.set('sort_by', parts[0] || 'search_api_relevance');
          url.searchParams.set('sort_order', parts[1] || 'DESC');
          url.searchParams.delete('page');

          // Full navigation unless search-page-zone-reload is attached (phase 2+).
          if (typeof Drupal.psSearchPage?.reloadSearch === 'function') {
            Drupal.psSearchPage.reloadSearch(root, {
              browserUrl: url.pathname + url.search,
              params: url.searchParams,
              preserveViewport: true,
              eventName: 'ps-search-filters-applied',
            }).catch(function () {
              window.location.assign(url.toString());
            });
            return;
          }

          window.location.assign(url.toString());
        });

        requestAnimationFrame(function () {
          setTimeout(function () {
            syncSearchLayout(root);
          }, 500);
        });
      });

      document.addEventListener('ps-search-map-resize', function () {
        const root = document.querySelector('.ps-search-view');
        if (root) {
          syncSearchLayout(root);
        }
      });
    },
  };
}(Drupal, once));
