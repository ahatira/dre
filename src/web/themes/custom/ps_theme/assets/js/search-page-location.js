(function (Drupal, drupalSettings, once) {
  'use strict';

  /**
   * Waits until the geofield map has rendered.
   *
   * @param {HTMLElement} root
   * @param {function} callback
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
      if (mapData?.google_map && typeof google !== 'undefined' && google.maps) {
        window.clearInterval(timer);
        callback(mapData);
        return;
      }

      if (attempts >= 300) {
        window.clearInterval(timer);
      }
    }, 200);
  }

  /**
   * Centers the search map on the selected location and draws a radius circle.
   */
  Drupal.behaviors.psSearchPageLocation = {
    attach(context) {
      const locationMap = drupalSettings.psSearch?.locationMap;
      if (!locationMap || locationMap.lat === undefined || locationMap.lng === undefined) {
        return;
      }

      once('ps-search-location-map', '.ps-search-view', context).forEach(function (root) {
        whenMapReady(root, function (mapData) {
          const map = mapData.google_map;
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
            Object.keys(mapData.markers).forEach(function (nid) {
              const marker = mapData.markers[nid];
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
        });
      });
    },
  };
}(Drupal, drupalSettings, once));
