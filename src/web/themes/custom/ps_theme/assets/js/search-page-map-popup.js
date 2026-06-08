(function (Drupal, once) {
  'use strict';

  /**
   * Closes any geofield map InfoWindow on the search map.
   *
   * @param {object} mapData
   *   Geofield map data bucket.
   */
  function closeGeofieldInfoWindow(mapData) {
    if (mapData?.infowindow && typeof mapData.infowindow.close === 'function') {
      mapData.infowindow.close();
    }
  }

  /**
   * Waits until the geofield map has rendered markers.
   *
   * @param {HTMLElement} root
   *   Search view root.
   * @param {function} callback
   *   Called with mapData when ready.
   */
  function whenMapReady(root, callback) {
    const mapEl = root.querySelector('.geofield-google-map');
    if (!mapEl) {
      return;
    }

    const mapId = mapEl.id;
    let attempts = 0;
    const timer = window.setInterval(function () {
      attempts += 1;
      const mapData = Drupal.geoFieldMapFormatter?.map_data?.[mapId];
      const markerCount = mapData?.markers ? Object.keys(mapData.markers).length : 0;

      if (markerCount > 0 && typeof google !== 'undefined' && google.maps) {
        window.clearInterval(timer);
        callback(mapData);
        return;
      }

      if (attempts >= 300) {
        window.clearInterval(timer);
      }
    }, 200);
  }

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

        /**
         * Hides the floating map card popup.
         */
        function closePopup() {
          popup.hidden = true;
          popupContent.textContent = '';
          openNid = null;
        }

        /**
         * Shows a compact offer card cloned from the results list.
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
          clone.classList.add('ps-offer-search-card--map-popup');
          clone.classList.remove('is-map-sync-active');
          clone.removeAttribute('id');
          popupContent.appendChild(clone);
          popup.hidden = false;
          openNid = nid;

          const listCard = root.querySelector(`.ps-offer-search-card[data-offer-id="${nid}"]`);
          if (listCard) {
            listCard.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
          }
        }

        if (closeBtn) {
          closeBtn.addEventListener('click', closePopup);
        }

        whenMapReady(root, function (mapData) {
          if (mapData.map) {
            google.maps.event.addListener(mapData.map, 'click', closePopup);
          }

          Object.keys(mapData.markers).forEach(function (nid) {
            const marker = mapData.markers[nid];
            if (marker.__psSearchPopupBound) {
              return;
            }

            marker.__psSearchPopupBound = true;

            google.maps.event.addListener(marker, 'click', function (event) {
              if (event && typeof event.stop === 'function') {
                event.stop();
              }
              closeGeofieldInfoWindow(mapData);
              openPopup(nid);
            });
          });
        });

        document.addEventListener('keydown', function (event) {
          if (event.key === 'Escape' && !popup.hidden) {
            closePopup();
          }
        });
      });
    },
  };
}(Drupal, once));
