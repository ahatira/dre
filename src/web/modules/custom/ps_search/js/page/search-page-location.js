(function (Drupal, drupalSettings, once) {
  'use strict';

  Drupal.behaviors.psSearchPageLocation = {
    attach(context) {
      const locationMap = drupalSettings.psSearch?.locationMap;
      if (!locationMap || locationMap.lat === undefined || locationMap.lng === undefined) {
        return;
      }

      once('ps-search-location-map', '.ps-search-view', context).forEach(function (root) {
        /**
         * Approximates a LatLngBounds from a center point and radius in metres.
         *
         * @param {{lat: number, lng: number}} center
         * @param {number} radiusM
         *
         * @return {google.maps.LatLngBounds|null}
         */
        function boundsFromCenterRadius(center, radiusM) {
          const latDelta = radiusM / 111000;
          const lngScale = Math.cos((center.lat * Math.PI) / 180) || 1;
          const lngDelta = radiusM / (111000 * lngScale);
          if (!window.google?.maps?.LatLngBounds) {
            return null;
          }
          return new google.maps.LatLngBounds(
            { lat: center.lat - latDelta, lng: center.lng - lngDelta },
            { lat: center.lat + latDelta, lng: center.lng + lngDelta }
          );
        }

        /**
         * Centers the map on the selected location (no visible radius overlay).
         *
         * @param {object} mapData
         *   PS map data bucket.
         * @param {object} [options]
         *   Viewport options.
         * @param {boolean} [options.reframe]
         *   Whether to center/fit the map on the location filter.
         */
        function applyLocationViewport(mapData, options) {
          const reframe = options?.reframe === true;
          const map = mapData.map || mapData.google_map;
          if (!map) {
            return;
          }

          const center = {
            lat: Number(locationMap.lat),
            lng: Number(locationMap.lng),
          };
          const radiusM = Number(locationMap.radiusM) || 2500;

          if (root.__psSearchLocationCircle) {
            root.__psSearchLocationCircle.setMap(null);
            root.__psSearchLocationCircle = null;
          }

          if (!reframe) {
            return;
          }

          map.setCenter(center);
          if (locationMap.zoom) {
            map.setZoom(Number(locationMap.zoom));
          }

          const bounds = boundsFromCenterRadius(center, radiusM);
          if (bounds) {
            map.fitBounds(bounds);
          }

          if (mapData.markers && Object.keys(mapData.markers).length > 0) {
            const markerBounds = new google.maps.LatLngBounds();
            Object.keys(mapData.markers).forEach(function (key) {
              const marker = mapData.markers[key];
              const pos = marker.getPosition();
              if (pos) {
                markerBounds.extend(pos);
              }
            });
            if (bounds) {
              markerBounds.union(bounds);
            }
            map.fitBounds(markerBounds);
          }
        }

        root.addEventListener('ps-search-map-markers-loaded', function (event) {
          applyLocationViewport(event.detail.mapData, {
            reframe: event.detail?.preserveViewport !== true,
          });
        });
      });
    },
  };
}(Drupal, drupalSettings, once));
