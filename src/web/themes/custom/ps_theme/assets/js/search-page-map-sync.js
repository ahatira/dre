(function (Drupal, once) {
  'use strict';

  const CLEAR_DELAY_MS = 80;

  Drupal.behaviors.psSearchPageMapSync = {
    attach(context) {
      once('ps-search-map-sync', '.ps-search-view', context).forEach(function (root) {
        let activeNid = null;
        let pinnedNid = null;
        let clearTimer = null;
        let mapDataRef = null;

        /**
         * Applies highlight styles to a card and its map marker.
         *
         * @param {string} nid
         *   Offer node id.
         */
        function setActive(nid) {
          if (!nid || nid === activeNid || !mapDataRef?.markersByNid) {
            return;
          }

          clearActive();

          activeNid = nid;
          const marker = mapDataRef.markersByNid[nid];
          const card = root.querySelector(`.ps-offer-search-card[data-offer-id="${nid}"]`);

          if (marker) {
            const label = Drupal.psSearchMap.getMarkerMeta(marker).label;
            if (label) {
              marker.setIcon(Drupal.psSearchMap.buildPriceMarkerIcon(label, true));
              marker.setZIndex(google.maps.Marker.MAX_ZINDEX + 1);
            }
          }

          if (card) {
            card.classList.add('is-map-sync-active');
          }
        }

        /**
         * Clears card and marker highlight state.
         */
        function clearActive() {
          if (!activeNid || !mapDataRef?.markersByNid) {
            return;
          }

          const marker = mapDataRef.markersByNid[activeNid];
          const card = root.querySelector(`.ps-offer-search-card[data-offer-id="${activeNid}"]`);

          if (marker) {
            const label = Drupal.psSearchMap.getMarkerMeta(marker).label;
            if (label) {
              marker.setIcon(Drupal.psSearchMap.buildPriceMarkerIcon(label, false));
            }
            marker.setZIndex(null);
          }

          if (card) {
            card.classList.remove('is-map-sync-active');
          }

          activeNid = null;
        }

        /**
         * Clears click-pinned selection (list mode).
         */
        function clearPinned() {
          pinnedNid = null;
          clearActive();
        }

        /**
         * Defers clearing so pointer can move between card and marker.
         */
        function scheduleClear() {
          window.clearTimeout(clearTimer);
          clearTimer = window.setTimeout(function () {
            if (pinnedNid && activeNid === pinnedNid) {
              return;
            }
            clearActive();
          }, CLEAR_DELAY_MS);
        }

        /**
         * Cancels a pending clear when entering a synced element.
         */
        function cancelClear() {
          window.clearTimeout(clearTimer);
        }

        /**
         * Handles marker click in split view — scroll list, no overlay panel.
         *
         * @param {string} nid
         *   Offer node id.
         */
        function selectFromMapInListView(nid) {
          pinnedNid = nid;
          setActive(nid);
          const card = root.querySelector(`.ps-offer-search-card[data-offer-id="${nid}"]`);
          Drupal.psSearchMap.scrollToCard(root, card);
        }

        /**
         * Binds hover sync to geofield map markers.
         *
         * @param {object} mapData
         *   Geofield map data bucket.
         */
        function bindMarkers(mapData) {
          mapDataRef = mapData;

          Object.keys(mapData.markersByNid || {}).forEach(function (nid) {
            const marker = mapData.markersByNid[nid];
            if (marker.__psSearchSyncBound) {
              return;
            }

            marker.__psSearchSyncBound = true;

            google.maps.event.addListener(marker, 'mouseover', function () {
              cancelClear();
              setActive(nid);

              if (Drupal.psSearchMap.isListVisible(root)) {
                const card = root.querySelector(`.ps-offer-search-card[data-offer-id="${nid}"]`);
                Drupal.psSearchMap.scrollToCard(root, card);
              }
            });

            google.maps.event.addListener(marker, 'mouseout', function () {
              scheduleClear();
            });
          });
        }

        root.querySelectorAll('.ps-offer-search-card[data-offer-id]').forEach(function (card) {
          const nid = card.getAttribute('data-offer-id');
          if (!nid) {
            return;
          }

          card.addEventListener('mouseenter', function () {
            cancelClear();
            setActive(nid);
          });

          card.addEventListener('mouseleave', function () {
            scheduleClear();
          });
        });

        root.addEventListener('ps-search-map-marker-select', function (event) {
          const nid = event.detail?.nid;
          if (!nid) {
            return;
          }

          if (Drupal.psSearchMap.isListVisible(root)) {
            selectFromMapInListView(String(nid));
            return;
          }

          pinnedNid = String(nid);
          setActive(String(nid));
        });

        root.addEventListener('ps-search-map-marker-clear', function () {
          clearPinned();
        });

        root.addEventListener('ps-search-map-markers-loaded', function (event) {
          bindMarkers(event.detail.mapData);
        });

        Drupal.psSearchMap.whenMapReady(root, function (mapData) {
          mapDataRef = mapData;
          const map = mapData.map || mapData.google_map;
          if (map) {
            google.maps.event.addListener(map, 'click', function () {
              clearPinned();
            });
          }
        });
      });
    },
  };
}(Drupal, once));
