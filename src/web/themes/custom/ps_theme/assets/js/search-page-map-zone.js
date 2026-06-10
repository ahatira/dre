(function (Drupal, once) {
  'use strict';

  const DEFAULT_TRANSPORT = 'walking';
  const DEFAULT_TIME = '5';

  /**
   * Parses travel time minutes from select value.
   *
   * @param {string} value
   *   Select option value.
   *
   * @return {number}
   */
  function parseMinutes(value) {
    const minutes = parseInt(value, 10);
    return Number.isFinite(minutes) && minutes > 0 ? minutes : 5;
  }

  /**
   * Builds the isochrone API URL for the active center and travel settings.
   *
   * @param {google.maps.LatLng} center
   * @param {string} transport
   * @param {string} minutesValue
   *
   * @return {string}
   */
  function buildIsochroneUrl(center, transport, minutesValue) {
    const base = drupalSettings.psSearch?.isochroneUrl || '/ps-search/isochrone';
    const params = new URLSearchParams({
      lat: String(center.lat()),
      lng: String(center.lng()),
      transport: transport,
      minutes: String(parseMinutes(minutesValue)),
    });
    return `${base}?${params.toString()}`;
  }

  Drupal.behaviors.psSearchPageMapZone = {
    attach(context) {
      once('ps-search-map-zone', '.ps-search-view', context).forEach(function (root) {
        const toggle = root.querySelector('.js-ps-distance-zone-toggle');
        const summary = root.querySelector('.js-ps-customize-area-summary');
        const summaryIconWrap = root.querySelector('.js-ps-customize-area-icon');
        const transportButtons = root.querySelectorAll('.js-ps-customize-transport');
        const travelTime = root.querySelector('.js-ps-customize-travel-time');
        const resetBtn = root.querySelector('.js-ps-customize-reset');
        const validateBtn = root.querySelector('.js-ps-customize-validate');
        const modal = root.querySelector('#ps-search-customize-area');

        let selectedTransport = DEFAULT_TRANSPORT;
        let selectedMinutes = DEFAULT_TIME;
        let distanceOverlay = null;
        let activeIsochrone = null;

        /**
         * Removes the current distance zone overlay from the map.
         */
        function clearDistanceOverlay() {
          if (distanceOverlay && typeof distanceOverlay.setMap === 'function') {
            distanceOverlay.setMap(null);
          }
          distanceOverlay = null;
        }

        /**
         * Updates the customize area button label and icon.
         */
        function updateSummary() {
          if (!summary || !travelTime) {
            return;
          }
          const option = travelTime.options[travelTime.selectedIndex];
          summary.textContent = option ? option.textContent.trim() : '';

          const activeTransport = root.querySelector('.js-ps-customize-transport.is-active');
          const sourceIcon = activeTransport?.querySelector('.js-ps-customize-transport-icon');
          if (summaryIconWrap && sourceIcon) {
            summaryIconWrap.innerHTML = sourceIcon.innerHTML;
          }
        }

        /**
         * Resolves isochrone center from selected marker, or sensible fallbacks.
         *
         * Priority: explicit nid → pinned selection → float panel → first marker
         * → map viewport center.
         *
         * @param {object} mapData
         *   Geofield map data.
         * @param {string|null} [preferredNid]
         *   Offer id from the triggering event, when available.
         *
         * @return {google.maps.LatLng|null}
         */
        function resolveZoneCenter(mapData, preferredNid) {
          const map = mapData?.map || mapData?.google_map;
          const candidates = [];

          if (preferredNid) {
            candidates.push(String(preferredNid));
          }

          const selectedNid = Drupal.psSearchMap.getSelectedOfferId(root);
          if (selectedNid) {
            candidates.push(String(selectedNid));
          }

          const floatNid = root.querySelector('.ps-offer-search-card--float-panel[data-offer-id]')?.getAttribute('data-offer-id');
          if (floatNid) {
            candidates.push(String(floatNid));
          }

          const markersByNid = mapData?.markersByNid || {};
          for (let i = 0; i < candidates.length; i++) {
            const marker = markersByNid[candidates[i]];
            if (marker?.getPosition) {
              return marker.getPosition();
            }
          }

          const markerIds = Object.keys(markersByNid);
          if (markerIds.length > 0) {
            const firstMarker = markersByNid[markerIds[0]];
            if (firstMarker?.getPosition) {
              return firstMarker.getPosition();
            }
          }

          if (map?.getCenter) {
            const center = map.getCenter();
            if (center) {
              return center;
            }
          }

          return null;
        }

        /**
         * Draws the isochrone polygon returned by the backend.
         *
         * @param {google.maps.Map} map
         *   Google map instance.
         * @param {object} payload
         *   Isochrone API payload.
         */
        function drawIsochronePolygon(map, payload) {
          clearDistanceOverlay();
          const ring = payload?.polygon?.[0];
          if (!Array.isArray(ring) || ring.length < 3) {
            return;
          }

          const paths = ring.map(function (pair) {
            return { lat: pair[1], lng: pair[0] };
          });

          distanceOverlay = new google.maps.Polygon({
            paths: paths,
            fillColor: '#00915A',
            fillOpacity: 0.12,
            map: map,
            strokeColor: '#00915A',
            strokeOpacity: 0.45,
            strokeWeight: 2,
          });
        }

        /**
         * Loads isochrone geometry and optionally applies map_bounds to search.
         *
         * @param {object} [options]
         *   Refresh options.
         * @param {boolean} [options.fitBounds]
         *   Whether to reframe the map on the zone.
         * @param {boolean} [options.applyZone]
         *   Whether to reload list/markers with map_bounds.
         * @param {string|null} [options.nid]
         *   Offer id for circle center resolution.
         */
        function refreshDistanceZone(options) {
          const fitBounds = options?.fitBounds === true;
          const applyZone = options?.applyZone === true;
          const preferredNid = options?.nid || null;

          clearDistanceOverlay();
          activeIsochrone = null;

          if (!toggle || !toggle.checked || typeof google === 'undefined' || !google.maps) {
            return Promise.resolve(null);
          }

          const mapData = Drupal.psSearchMap.getMapData(root);
          const map = mapData?.map || mapData?.google_map;
          const center = mapData ? resolveZoneCenter(mapData, preferredNid) : null;
          if (!map || !center) {
            return Promise.resolve(null);
          }

          return fetch(buildIsochroneUrl(center, selectedTransport, selectedMinutes), {
            headers: { Accept: 'application/json' },
          })
            .then(function (response) {
              if (!response.ok) {
                throw new Error('isochrone_request_failed');
              }
              return response.json();
            })
            .then(function (payload) {
              activeIsochrone = payload;
              drawIsochronePolygon(map, payload);

              if (fitBounds && payload?.bounds) {
                const bounds = new google.maps.LatLngBounds(
                  { lat: payload.bounds.swLat, lng: payload.bounds.swLng },
                  { lat: payload.bounds.neLat, lng: payload.bounds.neLng },
                );
                map.fitBounds(bounds, 48);
              }

              root.dispatchEvent(new CustomEvent('ps-search-distance-zone-updated', {
                detail: {
                  transport: selectedTransport,
                  minutes: selectedMinutes,
                  radius: payload?.radius_m || 0,
                  map_bounds: payload?.map_bounds || '',
                },
              }));

              if (applyZone && payload?.map_bounds && typeof Drupal.psSearchPage.reloadZoneSearch === 'function') {
                return Drupal.psSearchPage.reloadZoneSearch(root, payload.map_bounds);
              }

              return payload;
            })
            .catch(function () {
              return null;
            });
        }

        /**
         * Applies transport + travel time from modal controls.
         */
        function applyModalSettings() {
          if (travelTime) {
            selectedMinutes = travelTime.value;
          }
          updateSummary();
          refreshDistanceZone({ fitBounds: true, applyZone: true });
        }

        transportButtons.forEach(function (button) {
          button.addEventListener('click', function () {
            selectedTransport = button.getAttribute('data-transport') || DEFAULT_TRANSPORT;
            transportButtons.forEach(function (item) {
              const isActive = item === button;
              item.classList.toggle('is-active', isActive);
              item.setAttribute('aria-pressed', isActive ? 'true' : 'false');
            });
          });
        });

        if (resetBtn) {
          resetBtn.addEventListener('click', function () {
            selectedTransport = DEFAULT_TRANSPORT;
            selectedMinutes = DEFAULT_TIME;
            if (travelTime) {
              travelTime.value = DEFAULT_TIME;
            }
            transportButtons.forEach(function (item) {
              const isWalking = item.getAttribute('data-transport') === DEFAULT_TRANSPORT;
              item.classList.toggle('is-active', isWalking);
              item.setAttribute('aria-pressed', isWalking ? 'true' : 'false');
            });
            updateSummary();
          });
        }

        if (validateBtn) {
          validateBtn.addEventListener('click', function () {
            applyModalSettings();
          });
        }

        if (modal) {
          modal.addEventListener('hidden.bs.modal', function () {
            applyModalSettings();
          });
        }

        if (toggle) {
          toggle.addEventListener('change', function () {
            refreshDistanceZone({ fitBounds: toggle.checked, applyZone: false });
          });
        }

        root.addEventListener('ps-search-map-marker-select', function (event) {
          if (!toggle || !toggle.checked) {
            return;
          }
          refreshDistanceZone({
            fitBounds: false,
            applyZone: false,
            nid: event.detail?.nid ? String(event.detail.nid) : null,
          });
        });

        root.addEventListener('ps-search-map-marker-clear', function () {
          clearDistanceOverlay();
          activeIsochrone = null;
        });

        root.addEventListener('ps-search-list-shown', function () {
          clearDistanceOverlay();
          activeIsochrone = null;
        });

        root.addEventListener('ps-search-map-mode', function () {
          if (!toggle || !toggle.checked) {
            return;
          }
          window.setTimeout(function () {
            refreshDistanceZone({ fitBounds: false, applyZone: false });
          }, 100);
        });

        root.addEventListener('ps-search-map-markers-loaded', function () {
          if (toggle && toggle.checked && !Drupal.psSearchMap.isListVisible(root)) {
            refreshDistanceZone({
              fitBounds: false,
              applyZone: false,
              nid: Drupal.psSearchMap.getSelectedOfferId(root),
            });
          }
        });

        updateSummary();
      });
    },
  };
}(Drupal, once));
