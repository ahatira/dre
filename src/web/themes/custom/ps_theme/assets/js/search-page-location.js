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
         * Centers the map on the selected location and draws a radius circle.
         *
         * @param {object} mapData
         *   Geofield map data bucket.
         */
        function applyLocationOverlay(mapData) {
          const map = mapData.map || mapData.google_map;
          if (!map) {
            return;
          }

          const center = {
            lat: Number(locationMap.lat),
            lng: Number(locationMap.lng),
          };
          const radiusM = Number(locationMap.radiusM) || 2500;
          const circleColor = locationMap.circleColor || '#00915A';

          map.setCenter(center);
          if (locationMap.zoom) {
            map.setZoom(Number(locationMap.zoom));
          }

          if (root.__psSearchLocationCircle) {
            root.__psSearchLocationCircle.setMap(null);
          }

          const circle = new google.maps.Circle({
            map: map,
            center: center,
            radius: radiusM,
            strokeColor: circleColor,
            strokeOpacity: 0.9,
            strokeWeight: 2,
            fillColor: circleColor,
            fillOpacity: 0.12,
            clickable: false,
          });

          const bounds = circle.getBounds();
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

          root.__psSearchLocationCircle = circle;
        }

        Drupal.psSearchMap.whenMapReady(root, applyLocationOverlay);
        root.addEventListener('ps-search-map-markers-loaded', function (event) {
          applyLocationOverlay(event.detail.mapData);
        });
      });
    },
  };
}(Drupal, drupalSettings, once));
