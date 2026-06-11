(function (Drupal, once) {
  'use strict';

  const CLEAR_DELAY_MS = 80;

  Drupal.behaviors.psSearchPageMapSync = {
    attach(context) {
      once('ps-search-map-sync', '.ps-search-view', context).forEach(function (root) {
        let activeNid = null;
        let pinnedNid = null;
        let activeCluster = null;
        let clearTimer = null;
        let mapDataRef = null;

        /**
         * Clears MarkerClusterer highlight from the active group icon.
         */
        function clearClusterHighlight() {
          if (activeCluster) {
            Drupal.psSearchMap.setClusterHighlight(activeCluster, false);
            activeCluster = null;
          }
        }

        /**
         * Applies highlight styles to a card and its map marker.
         *
         * @param {string} nid
         *   Offer node id.
         * @param {boolean} force
         *   Re-apply even when already active.
         */
        function setActive(nid, force) {
          if (!nid || !mapDataRef?.markersByNid) {
            return;
          }
          if (!force && nid === activeNid) {
            return;
          }

          clearActive();

          activeNid = nid;
          const marker = mapDataRef.markersByNid[nid];
          const card = root.querySelector(`.ps-offer-search-card[data-offer-id="${nid}"]`);
          const cluster = Drupal.psSearchMap.findClusterForMarker(mapDataRef.markerCluster, marker);

          if (cluster) {
            activeCluster = cluster;
            Drupal.psSearchMap.setClusterHighlight(cluster, true);
          }
          else if (marker) {
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
          clearClusterHighlight();

          if (!activeNid || !mapDataRef?.markersByNid) {
            activeNid = null;
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
         * Stores pinned selection on the view root.
         *
         * @param {string|null} nid
         *   Offer node id.
         */
        function setPinned(nid) {
          pinnedNid = nid ? String(nid) : null;
          Drupal.psSearchMap.setSelectedOfferId(root, pinnedNid);
        }

        /**
         * Clears click-pinned selection (list mode).
         */
        function clearPinned() {
          setPinned(null);
          clearActive();
        }

        /**
         * Defers clearing after list hover ends.
         */
        function scheduleClear() {
          window.clearTimeout(clearTimer);
          clearTimer = window.setTimeout(function () {
            if (pinnedNid) {
              setActive(pinnedNid, true);
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
         * Handles marker click in split view — scroll list and pin highlight.
         *
         * @param {string} nid
         *   Offer node id.
         */
        function selectFromMapInListView(nid) {
          setPinned(String(nid));
          setActive(String(nid), true);
          const card = root.querySelector(`.ps-offer-search-card[data-offer-id="${nid}"]`);
          Drupal.psSearchMap.scrollToCard(root, card);
        }

        // List → map sync on hover (delegated — supports load-more appended rows).
        const listPanel = root.querySelector('.js-ps-search-list-panel');
        if (listPanel) {
          listPanel.addEventListener('mouseenter', function (event) {
            const card = event.target.closest('.ps-offer-search-card[data-offer-id]');
            if (!card || !listPanel.contains(card)) {
              return;
            }
            if (!Drupal.psSearchMap.isListVisible(root)) {
              return;
            }
            const nid = card.getAttribute('data-offer-id');
            if (!nid) {
              return;
            }
            cancelClear();
            setActive(nid);
          }, true);

          listPanel.addEventListener('mouseleave', function (event) {
            const card = event.target.closest('.ps-offer-search-card[data-offer-id]');
            if (!card || !listPanel.contains(card)) {
              return;
            }
            if (!Drupal.psSearchMap.isListVisible(root)) {
              return;
            }
            scheduleClear();
          }, true);
        }

        // Re-bind is not needed for markers; highlight new rows after load more.
        root.addEventListener('ps-search-list-new-content', function () {
          if (pinnedNid) {
            setActive(pinnedNid, true);
          }
        });

        // Map → list sync on marker click (dispatched from map-popup.js).
        root.addEventListener('ps-search-map-marker-select', function (event) {
          const nid = event.detail?.nid;
          if (!nid) {
            return;
          }

          if (Drupal.psSearchMap.isListVisible(root)) {
            const card = root.querySelector(`.ps-offer-search-card[data-offer-id="${nid}"]`);
            if (card) {
              selectFromMapInListView(String(nid));
              return;
            }

            if (drupalSettings.psSearch?.listLoadAll) {
              Drupal.psSearchPage.fetchOfferCard(String(nid)).then(function (loadedCard) {
                if (loadedCard) {
                  const listPanel = Drupal.psSearchPage.getListScrollEl(root);
                  if (listPanel) {
                    listPanel.appendChild(loadedCard);
                  }
                  selectFromMapInListView(String(nid));
                }
              });
              return;
            }

            Drupal.psSearchPage.loadUntilOfferCard(root, String(nid)).then(function (loadedCard) {
              if (loadedCard) {
                selectFromMapInListView(String(nid));
              }
            });
            return;
          }

          setPinned(String(nid));
          setActive(String(nid), true);
        });

        root.addEventListener('ps-search-map-marker-clear', function () {
          clearPinned();
        });

        /**
         * Binds map marker clicks for list-visible map ↔ list sync.
         *
         * @param {object} mapData
         *   PS map data bucket.
         */
        function bindMapMarkerClicks(mapData) {
          Object.keys(mapData.markersByNid || {}).forEach(function (nid) {
            const marker = mapData.markersByNid[nid];
            if (!marker || marker.__psSearchSyncBound) {
              return;
            }

            marker.__psSearchSyncBound = true;
            google.maps.event.addListener(marker, 'click', function () {
              Drupal.psSearchMap.closeMapInfoWindow(mapData);
              if (Drupal.psSearchMap.isListVisible(root)) {
                root.dispatchEvent(new CustomEvent('ps-search-map-marker-select', {
                  detail: { nid: String(nid) },
                }));
              }
            });
          });
        }

        /**
         * Indexes map markers once the PS shell finished loading markers.
         *
         * @param {object} mapData
         *   PS map data bucket.
         */
        function onMapMarkersReady(mapData) {
          Drupal.psSearchMap.indexMarkersByNid(mapData);
          mapDataRef = mapData;
          bindMapMarkerClicks(mapData);
          if (pinnedNid) {
            setActive(pinnedNid, true);
          }
        }

        // Re-bind marker clicks after each API marker reload (filters, load more, zone).
        root.addEventListener('ps-search-map-markers-loaded', function (event) {
          const mapData = event.detail?.mapData;
          if (!mapData) {
            return;
          }
          clearClusterHighlight();
          onMapMarkersReady(mapData);
        });

        Drupal.psSearchMap.whenMapReady(root, function (mapData) {
          const map = mapData.map || mapData.google_map;
          if (map) {
            google.maps.event.addListener(map, 'click', function () {
              if (Drupal.psSearchMap.isListVisible(root)) {
                clearPinned();
              }
            });
          }
        });
      });
    },
  };
}(Drupal, once));
