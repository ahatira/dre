(function (Drupal, once) {
  'use strict';

  Drupal.behaviors.psSearchPageMapPopup = {
    attach(context) {
      once('ps-search-map-popup', '.ps-search-view', context).forEach(function (root) {
        const popup = root.querySelector('.js-ps-search-map-popup');
        const popupContent = root.querySelector('.js-ps-search-map-popup-content');
        const closeBtn = root.querySelector('.js-ps-search-map-popup-close');
        if (!popup || !popupContent) {
          return;
        }

        let openNid = null;
        let mapDataRef = null;

        /**
         * Hides the floating map offer panel.
         */
        function closePopup() {
          popup.hidden = true;
          popupContent.textContent = '';
          openNid = null;
          root.dispatchEvent(new CustomEvent('ps-search-map-marker-clear'));
        }

        /**
         * Shows the offer panel cloned from the results list (full-map mode only).
         *
         * @param {string} nid
         *   Offer node id.
         */
        function openPopup(nid) {
          const card = root.querySelector(`.ps-offer-search-card[data-offer-id="${nid}"]`);
          if (!card) {
            closePopup();
            return;
          }

          if (openNid === nid && !popup.hidden) {
            closePopup();
            return;
          }

          popupContent.textContent = '';
          const clone = card.cloneNode(true);
          clone.classList.add('ps-offer-search-card--map-panel');
          clone.classList.remove('is-map-sync-active');
          clone.removeAttribute('id');
          popupContent.appendChild(clone);
          popup.hidden = false;
          openNid = nid;

          root.dispatchEvent(new CustomEvent('ps-search-map-marker-select', {
            detail: { nid },
          }));
        }

        /**
         * Binds marker click handlers for the offer panel.
         *
         * @param {object} mapData
         *   Geofield map data bucket.
         */
        function bindMarkers(mapData) {
          mapDataRef = mapData;

          Object.keys(mapData.markersByNid || {}).forEach(function (nid) {
            const marker = mapData.markersByNid[nid];
            if (marker.__psSearchPopupBound) {
              return;
            }

            marker.__psSearchPopupBound = true;

            google.maps.event.addListener(marker, 'click', function (event) {
              if (event && typeof event.stop === 'function') {
                event.stop();
              }
              Drupal.psSearchMap.closeGeofieldInfoWindow(mapData);

              if (Drupal.psSearchMap.isListVisible(root)) {
                closePopup();
                root.dispatchEvent(new CustomEvent('ps-search-map-marker-select', {
                  detail: { nid },
                }));
                return;
              }

              openPopup(nid);
            });
          });
        }

        if (closeBtn) {
          closeBtn.addEventListener('click', closePopup);
        }

        Drupal.psSearchMap.whenMapReady(root, function (mapData) {
          mapDataRef = mapData;
          const map = mapData.map || mapData.google_map;
          if (map) {
            google.maps.event.addListener(map, 'click', closePopup);
          }
        });

        root.addEventListener('ps-search-map-markers-loaded', function (event) {
          bindMarkers(event.detail.mapData);
        });

        document.addEventListener('keydown', function (event) {
          if (event.key === 'Escape' && !popup.hidden) {
            closePopup();
          }
        });

        root.addEventListener('ps-search-list-shown', closePopup);
      });
    },
  };
}(Drupal, once));
