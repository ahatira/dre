(function (Drupal, once) {
  'use strict';

  const DEFAULT_TRANSPORT = 'walking';
  const DEFAULT_TIME = '5';
  /**
   * Radius in metres per transport mode (placeholder until isochrone API).
   *
   * @type {Object<string, number>}
   */
  const BASE_RADIUS = {
    walking: 400,
    transports: 1200,
    bike: 2000,
    car: 5000,
  };

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
   * Computes placeholder circle radius from transport + minutes.
   *
   * @param {string} transport
   * @param {string} minutesValue
   *
   * @return {number}
   */
  function computeRadius(transport, minutesValue) {
    const base = BASE_RADIUS[transport] || BASE_RADIUS.walking;
    const minutes = parseMinutes(minutesValue);
    return Math.round(base * (minutes / 5));
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
        let distanceCircle = null;

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
         * Resolves circle center from the selected offer marker.
         *
         * @param {object} mapData
         *   Geofield map data.
         * @param {string|null} [preferredNid]
         *   Offer id from the triggering event, when available.
         *
         * @return {google.maps.LatLng|null}
         */
        function resolveZoneCenter(mapData, preferredNid) {
          const nid = preferredNid
            || Drupal.psSearchMap.getSelectedOfferId(root)
            || root.querySelector('.ps-offer-search-card--float-panel[data-offer-id]')?.getAttribute('data-offer-id');
          const marker = nid && mapData.markersByNid ? mapData.markersByNid[nid] : null;
          if (marker?.getPosition) {
            return marker.getPosition();
          }
          return null;
        }

        /**
         * Draws or removes the distance zone circle on the map.
         *
         * @param {object} [options]
         *   Refresh options.
         * @param {boolean} [options.fitBounds]
         *   Whether to reframe the map on the circle (only when enabling the zone).
         * @param {string|null} [options.nid]
         *   Offer id for circle center resolution.
         */
        function refreshDistanceZone(options) {
          const fitBounds = options?.fitBounds === true;
          const preferredNid = options?.nid || null;

          if (distanceCircle) {
            distanceCircle.setMap(null);
            distanceCircle = null;
          }

          if (!toggle || !toggle.checked || typeof google === 'undefined' || !google.maps) {
            return;
          }

          const mapData = Drupal.psSearchMap.getMapData(root);
          const map = mapData?.map || mapData?.google_map;
          const center = mapData ? resolveZoneCenter(mapData, preferredNid) : null;
          if (!map || !center) {
            return;
          }

          const radius = computeRadius(selectedTransport, selectedMinutes);
          distanceCircle = new google.maps.Circle({
            center,
            fillColor: '#00915A',
            fillOpacity: 0.12,
            map,
            radius,
            strokeColor: '#00915A',
            strokeOpacity: 0.45,
            strokeWeight: 2,
          });

          if (fitBounds && typeof map.fitBounds === 'function' && typeof google.maps.LatLngBounds === 'function') {
            const bounds = distanceCircle.getBounds();
            if (bounds) {
              map.fitBounds(bounds, 48);
            }
          }

          root.dispatchEvent(new CustomEvent('ps-search-distance-zone-updated', {
            detail: {
              transport: selectedTransport,
              minutes: selectedMinutes,
              radius,
            },
          }));
        }

        /**
         * Applies transport + travel time from modal controls.
         */
        function applyModalSettings() {
          if (travelTime) {
            selectedMinutes = travelTime.value;
          }
          updateSummary();
          refreshDistanceZone({ fitBounds: true });
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
            refreshDistanceZone({ fitBounds: toggle.checked });
          });
        }

        root.addEventListener('ps-search-map-marker-select', function (event) {
          if (!toggle || !toggle.checked) {
            return;
          }
          refreshDistanceZone({
            fitBounds: false,
            nid: event.detail?.nid ? String(event.detail.nid) : null,
          });
        });

        root.addEventListener('ps-search-map-marker-clear', function () {
          if (distanceCircle) {
            distanceCircle.setMap(null);
            distanceCircle = null;
          }
        });

        root.addEventListener('ps-search-list-shown', function () {
          if (distanceCircle) {
            distanceCircle.setMap(null);
            distanceCircle = null;
          }
        });

        root.addEventListener('ps-search-map-mode', function () {
          if (!toggle || !toggle.checked) {
            return;
          }
          window.setTimeout(function () {
            refreshDistanceZone({ fitBounds: false });
          }, 100);
        });

        root.addEventListener('ps-search-map-markers-loaded', function (event) {
          if (toggle && toggle.checked && !Drupal.psSearchMap.isListVisible(root)) {
            refreshDistanceZone({
              fitBounds: false,
              nid: Drupal.psSearchMap.getSelectedOfferId(root),
            });
          }
        });

        updateSummary();
      });
    },
  };
}(Drupal, once));
