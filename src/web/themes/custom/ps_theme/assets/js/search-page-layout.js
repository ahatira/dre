(function (Drupal, once) {
  'use strict';

  /**
   * Triggers Google Maps resize after layout changes.
   *
   * @param {HTMLElement} root
   *   Search view root element.
   */
  function resizeMaps(root) {
    root.querySelectorAll('.geofield-google-map').forEach(function (mapEl) {
      const mapId = mapEl.id;
      const mapData = Drupal.geoFieldMapFormatter?.map_data?.[mapId];
      if (typeof google !== 'undefined' && google.maps && mapData?.map) {
        google.maps.event.trigger(mapData.map, 'resize');
      }
    });
  }

  Drupal.behaviors.psSearchPageLayout = {
    attach(context) {
      once('ps-search-layout', '.ps-search-view', context).forEach(function (root) {
        const hideBtn = root.querySelector('.js-ps-hide-list');
        const showBtn = root.querySelector('.js-ps-show-list');
        const sortSelect = root.querySelector('.js-ps-sort-select');

        /**
         * Toggles list pane visibility and resizes the map.
         *
         * @param {boolean} visible
         *   Whether the list pane should be visible.
         */
        function setListVisible(visible) {
          root.classList.toggle('ps-search-view--list-hidden', !visible);
          if (hideBtn) {
            hideBtn.setAttribute('aria-expanded', visible ? 'true' : 'false');
          }
          if (showBtn) {
            showBtn.hidden = visible;
          }
          requestAnimationFrame(function () {
            setTimeout(function () {
              resizeMaps(root);
            }, 320);
          });
        }

        if (hideBtn) {
          hideBtn.addEventListener('click', function () {
            setListVisible(false);
          });
        }

        if (showBtn) {
          showBtn.addEventListener('click', function () {
            setListVisible(true);
          });
        }

        if (sortSelect) {
          sortSelect.addEventListener('change', function () {
            const url = new URL(window.location.href);
            const parts = sortSelect.value.split('|');
            url.searchParams.set('sort_by', parts[0] || 'search_api_relevance');
            url.searchParams.set('sort_order', parts[1] || 'DESC');
            window.location.assign(url.toString());
          });
        }

        // Geofield map initializes before the split layout settles — resize once ready.
        requestAnimationFrame(function () {
          setTimeout(function () {
            resizeMaps(root);
          }, 500);
        });
      });
    },
  };
}(Drupal, once));
