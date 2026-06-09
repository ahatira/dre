(function (Drupal, once) {
  'use strict';

  const BOUNDS_THRESHOLD = 0.001;

  /**
   * Formats a coordinate for map_bounds query values.
   *
   * @param {number} value
   *   Latitude or longitude.
   *
   * @return {string}
   *   Normalised coordinate string.
   */
  function formatCoord(value) {
    return parseFloat(Number(value).toFixed(6)).toString();
  }

  /**
   * Serialises Google map bounds for the map_bounds URL parameter.
   *
   * @param {google.maps.LatLngBounds} bounds
   *   Map viewport bounds.
   *
   * @return {string}
   *   map_bounds query value.
   */
  function boundsToQueryValue(bounds) {
    const sw = bounds.getSouthWest();
    const ne = bounds.getNorthEast();
    return [
      formatCoord(sw.lat()),
      formatCoord(sw.lng()),
      formatCoord(ne.lat()),
      formatCoord(ne.lng()),
    ].join(',');
  }

  /**
   * Normalises bounds corners from map or settings object.
   *
   * @param {google.maps.LatLngBounds|object} bounds
   *   Bounds source.
   *
   * @return {{swLat: number, swLng: number, neLat: number, neLng: number}|null}
   *   Parsed corners.
   */
  function normalizeBounds(bounds) {
    if (!bounds) {
      return null;
    }

    if (typeof bounds.getSouthWest === 'function') {
      const sw = bounds.getSouthWest();
      const ne = bounds.getNorthEast();
      return {
        swLat: sw.lat(),
        swLng: sw.lng(),
        neLat: ne.lat(),
        neLng: ne.lng(),
      };
    }

    if (typeof bounds.swLat === 'number') {
      return {
        swLat: bounds.swLat,
        swLng: bounds.swLng,
        neLat: bounds.neLat,
        neLng: bounds.neLng,
      };
    }

    return null;
  }

  /**
   * Whether two bounding boxes differ beyond the comparison threshold.
   *
   * @param {object|null} active
   *   Active zone bounds.
   * @param {google.maps.LatLngBounds} viewport
   *   Current map viewport.
   *
   * @return {boolean}
   *   TRUE when the viewport no longer matches the active zone.
   */
  function boundsDiffer(active, viewport) {
    const current = normalizeBounds(viewport);
    if (!active || !current) {
      return false;
    }

    return Math.abs(current.swLat - active.swLat) > BOUNDS_THRESHOLD
      || Math.abs(current.swLng - active.swLng) > BOUNDS_THRESHOLD
      || Math.abs(current.neLat - active.neLat) > BOUNDS_THRESHOLD
      || Math.abs(current.neLng - active.neLng) > BOUNDS_THRESHOLD;
  }

  /**
   * Builds a search URL preserving filters and updating map_bounds.
   *
   * @param {string} mapBoundsValue
   *   New map_bounds query value.
   *
   * @return {string}
   *   Relative navigation URL.
   */
  function buildSearchAreaUrl(mapBoundsValue) {
    const params = new URLSearchParams(window.location.search);
    params.set('map_bounds', mapBoundsValue);
    params.delete('page');
    const query = params.toString();
    return query ? `${window.location.pathname}?${query}` : window.location.pathname;
  }

  Drupal.behaviors.psSearchPageMapBounds = {
    attach(context) {
      once('ps-search-map-bounds', '.ps-search-view', context).forEach(function (root) {
        const button = root.querySelector('.js-ps-search-this-area');
        if (!button) {
          return;
        }

        let activeBounds = normalizeBounds(window.drupalSettings?.psSearch?.mapBounds);
        let baselineBounds = activeBounds ? { ...activeBounds } : null;
        let userInteracted = false;
        let suppressInteraction = true;
        let idleTimer = null;
        let mapListenersBound = false;

        /**
         * Shows or hides the "Search this area" button.
         *
         * @param {google.maps.Map} map
         *   Google map instance.
         */
        function updateButton(map) {
          if (!map || typeof map.getBounds !== 'function') {
            button.hidden = true;
            return;
          }

          const viewport = map.getBounds();
          if (!viewport || !userInteracted || suppressInteraction) {
            button.hidden = true;
            return;
          }

          const reference = baselineBounds || activeBounds;
          button.hidden = !boundsDiffer(reference, viewport);
        }

        /**
         * Marks the map as user-adjusted (pan/zoom).
         */
        function markUserInteracted() {
          if (suppressInteraction) {
            return;
          }
          userInteracted = true;
        }

        /**
         * Schedules a debounced bounds check after map movement stops.
         *
         * @param {google.maps.Map} map
         *   Google map instance.
         */
        function scheduleBoundsCheck(map) {
          window.clearTimeout(idleTimer);
          idleTimer = window.setTimeout(function () {
            updateButton(map);
          }, 200);
        }

        /**
         * Captures the active zone after markers fit the map.
         *
         * @param {google.maps.Map} map
         *   Google map instance.
         */
        function captureActiveZone(map) {
          const viewport = map.getBounds();
          const normalized = normalizeBounds(viewport);
          if (normalized) {
            baselineBounds = normalized;
            activeBounds = normalized;
          }
          userInteracted = false;
          suppressInteraction = false;
          button.hidden = true;
        }

        /**
         * Binds map listeners once the shell is ready.
         *
         * @param {object} mapData
         *   Geofield map bucket.
         */
        function bindMapListeners(mapData) {
          if (mapListenersBound) {
            return;
          }

          const map = mapData.map || mapData.google_map;
          if (!map || typeof google === 'undefined' || !google.maps) {
            return;
          }

          mapListenersBound = true;
          const mapDiv = map.getDiv ? map.getDiv() : null;

          map.addListener('dragstart', markUserInteracted);
          map.addListener('dragend', function () {
            scheduleBoundsCheck(map);
          });
          map.addListener('idle', function () {
            if (suppressInteraction) {
              return;
            }
            scheduleBoundsCheck(map);
          });

          if (mapDiv) {
            mapDiv.addEventListener('wheel', markUserInteracted, { passive: true });
            mapDiv.addEventListener('touchstart', markUserInteracted, { passive: true });
          }

          button.addEventListener('click', function () {
            const viewport = map.getBounds();
            if (!viewport) {
              return;
            }

            const mapBoundsValue = boundsToQueryValue(viewport);
            button.disabled = true;
            button.classList.add('is-loading');

            if (typeof Drupal.psSearchPage.reloadZoneSearch === 'function') {
              Drupal.psSearchPage.reloadZoneSearch(root, mapBoundsValue)
                .then(function () {
                  button.disabled = false;
                  button.classList.remove('is-loading');
                  button.hidden = true;
                  userInteracted = false;
                  captureActiveZone(map);
                })
                .catch(function () {
                  window.location.assign(buildSearchAreaUrl(mapBoundsValue));
                });
              return;
            }

            window.location.assign(buildSearchAreaUrl(mapBoundsValue));
          });
        }

        Drupal.psSearchMap.whenMapShellReady(root, function (mapData) {
          bindMapListeners(mapData);
        });

        root.addEventListener('ps-search-zone-reloaded', function () {
          suppressInteraction = true;
          userInteracted = false;
          button.hidden = true;

          Drupal.psSearchMap.whenMapShellReady(root, function (mapData) {
            const map = mapData.map || mapData.google_map;
            if (!map) {
              return;
            }
            window.setTimeout(function () {
              captureActiveZone(map);
            }, 350);
          });
        });

        root.addEventListener('ps-search-map-markers-loaded', function () {
          suppressInteraction = true;
          userInteracted = false;
          button.hidden = true;

          Drupal.psSearchMap.whenMapShellReady(root, function (mapData) {
            const map = mapData.map || mapData.google_map;
            if (!map) {
              return;
            }

            window.setTimeout(function () {
              captureActiveZone(map);
            }, 350);
          });
        });
      });
    },
  };
}(Drupal, once));
