(function (Drupal, once) {
  'use strict';

  const MARKER_FILL = '#00915A';
  const MARKER_FILL_ACTIVE = '#007348';
  const MARKER_HEIGHT = 28;
  const MARKER_MIN_WIDTH = 44;
  const MARKER_CHAR_WIDTH = 7;
  const MARKER_H_PADDING = 16;
  const CLEAR_DELAY_MS = 80;

  /**
   * Builds a BNPPRE price pill marker icon (mirrors server-side SVG).
   *
   * @param {string} label
   *   Price label.
   * @param {boolean} active
   *   Whether the marker is highlighted.
   *
   * @return {google.maps.Icon}
   *   Google Maps icon descriptor.
   */
  function buildPriceMarkerIcon(label, active) {
    const width = Math.max(MARKER_MIN_WIDTH, label.length * MARKER_CHAR_WIDTH + MARKER_H_PADDING);
    const fill = active ? MARKER_FILL_ACTIVE : MARKER_FILL;
    const strokeAttrs = active ? ' stroke="#ffffff" stroke-width="2"' : '';
    const safeLabel = label
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;');
    const radius = MARKER_HEIGHT / 2;
    const svg = '<?xml version="1.0" encoding="UTF-8"?>'
      + `<svg xmlns="http://www.w3.org/2000/svg" width="${width}" height="${MARKER_HEIGHT}" viewBox="0 0 ${width} ${MARKER_HEIGHT}">`
      + `<rect x="1" y="1" width="${width - 2}" height="${MARKER_HEIGHT - 2}" rx="${radius}" ry="${radius}" fill="${fill}"${strokeAttrs}/>`
      + `<text x="50%" y="50%" dominant-baseline="central" text-anchor="middle" fill="#FFFFFF" font-family="Arial, Helvetica, sans-serif" font-size="12" font-weight="700">${safeLabel}</text>`
      + '</svg>';

    return {
      url: `data:image/svg+xml;charset=UTF-8,${encodeURIComponent(svg)}`,
      scaledSize: new google.maps.Size(width, MARKER_HEIGHT),
      anchor: new google.maps.Point(width / 2, MARKER_HEIGHT),
    };
  }

  /**
   * Returns marker metadata from geofield geojson properties.
   *
   * @param {google.maps.Marker} marker
   *   Map marker.
   *
   * @return {{nid: string, label: string}}
   *   Node id and price label.
   */
  function getMarkerMeta(marker) {
    const props = marker.get('geojsonProperties') || {};
    return {
      nid: String(props.ps_search_nid || props.entity_id || ''),
      label: String(props.ps_search_price || props.tooltip || ''),
    };
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

  Drupal.behaviors.psSearchPageMapSync = {
    attach(context) {
      once('ps-search-map-sync', '.ps-search-view', context).forEach(function (root) {
        whenMapReady(root, function (mapData) {
          const cards = root.querySelectorAll('.ps-offer-search-card[data-offer-id]');
          let activeNid = null;
          let clearTimer = null;

          /**
           * Applies highlight styles to a card and its map marker.
           *
           * @param {string} nid
           *   Offer node id.
           */
          function setActive(nid) {
            if (!nid || nid === activeNid) {
              return;
            }

            clearActive();

            activeNid = nid;
            const marker = mapData.markers[nid];
            const card = root.querySelector(`.ps-offer-search-card[data-offer-id="${nid}"]`);

            if (marker) {
              const label = getMarkerMeta(marker).label;
              if (label) {
                marker.setIcon(buildPriceMarkerIcon(label, true));
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
            if (!activeNid) {
              return;
            }

            const marker = mapData.markers[activeNid];
            const card = root.querySelector(`.ps-offer-search-card[data-offer-id="${activeNid}"]`);

            if (marker) {
              const label = getMarkerMeta(marker).label;
              if (label) {
                marker.setIcon(buildPriceMarkerIcon(label, false));
              }
              marker.setZIndex(null);
            }

            if (card) {
              card.classList.remove('is-map-sync-active');
            }

            activeNid = null;
          }

          /**
           * Defers clearing so pointer can move between card and marker.
           */
          function scheduleClear() {
            window.clearTimeout(clearTimer);
            clearTimer = window.setTimeout(clearActive, CLEAR_DELAY_MS);
          }

          /**
           * Cancels a pending clear when entering a synced element.
           */
          function cancelClear() {
            window.clearTimeout(clearTimer);
          }

          cards.forEach(function (card) {
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

          Object.keys(mapData.markers).forEach(function (nid) {
            const marker = mapData.markers[nid];
            if (marker.__psSearchSyncBound) {
              return;
            }

            marker.__psSearchSyncBound = true;

            google.maps.event.addListener(marker, 'mouseover', function () {
              cancelClear();
              setActive(nid);

              const card = root.querySelector(`.ps-offer-search-card[data-offer-id="${nid}"]`);
              if (card) {
                card.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
              }
            });

            google.maps.event.addListener(marker, 'mouseout', function () {
              scheduleClear();
            });
          });
        });
      });
    },
  };
}(Drupal, once));
