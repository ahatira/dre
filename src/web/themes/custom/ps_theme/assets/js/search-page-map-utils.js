(function (Drupal) {
  'use strict';

  const MARKER_GREEN = '#00915A';
  const MARKER_WHITE = '#FFFFFF';
  const BODY_HEIGHT = 26;
  const BOX_MIN_WIDTH = 44;
  const CHAR_WIDTH = 7;
  const H_PADDING = 16;
  const BORDER = 2;
  const POINTER_HEIGHT = 7;
  const POINTER_HALF_WIDTH = 5;
  const DOT_SIZE = 8;
  const DOT_GAP = 4;

  /**
   * Builds a closed SVG path for the price bubble body + pointer.
   *
   * @return {string}
   *   SVG path d attribute.
   */
  function buildBubblePath(left, right, centerX, bodyBottom, pointerHalfWidth, pointerTip) {
    return `M ${left},0 H ${right} V ${bodyBottom} L ${centerX + pointerHalfWidth},${bodyBottom} L ${centerX},${pointerTip} L ${centerX - pointerHalfWidth},${bodyBottom} H ${left} Z`;
  }

  /**
   * @namespace
   */
  Drupal.psSearchMap = Drupal.psSearchMap || {};

  /**
   * Builds a BNPPRE price marker icon (mirrors server-side SVG).
   *
   * @param {string} label
   *   Price label.
   * @param {boolean} active
   *   Whether the marker is highlighted.
   *
   * @return {google.maps.Icon}
   *   Google Maps icon descriptor.
   */
  Drupal.psSearchMap.buildPriceMarkerIcon = function (label, active) {
    const boxWidth = Math.max(BOX_MIN_WIDTH, label.length * CHAR_WIDTH + H_PADDING);
    const totalWidth = Math.max(boxWidth, DOT_SIZE + 4);
    const boxX = Math.floor((totalWidth - boxWidth) / 2);
    const centerX = Math.floor(totalWidth / 2);
    const pointerTip = BODY_HEIGHT + POINTER_HEIGHT;
    const dotCy = pointerTip + DOT_GAP + Math.floor(DOT_SIZE / 2);
    const totalHeight = pointerTip + DOT_GAP + DOT_SIZE;
    const dotRadius = Math.floor(DOT_SIZE / 2);
    const fill = active ? MARKER_GREEN : MARKER_WHITE;
    const textFill = active ? MARKER_WHITE : MARKER_GREEN;
    const stroke = MARKER_GREEN;
    const bubblePath = buildBubblePath(
      boxX,
      boxX + boxWidth,
      centerX,
      BODY_HEIGHT,
      POINTER_HALF_WIDTH,
      pointerTip,
    );
    const safeLabel = label
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;');
    const svg = '<?xml version="1.0" encoding="UTF-8"?>'
      + `<svg xmlns="http://www.w3.org/2000/svg" width="${totalWidth}" height="${totalHeight}" viewBox="0 0 ${totalWidth} ${totalHeight}">`
      + `<path d="${bubblePath}" fill="${fill}" stroke="${stroke}" stroke-width="${BORDER}" stroke-linejoin="round"/>`
      + `<text x="${centerX}" y="13" dominant-baseline="central" text-anchor="middle" fill="${textFill}" font-family="Arial, Helvetica, sans-serif" font-size="12" font-weight="700">${safeLabel}</text>`
      + `<circle cx="${centerX}" cy="${dotCy}" r="${dotRadius}" fill="${stroke}"/>`
      + '</svg>';

    return {
      url: `data:image/svg+xml;charset=UTF-8,${encodeURIComponent(svg)}`,
      scaledSize: new google.maps.Size(totalWidth, totalHeight),
      anchor: new google.maps.Point(centerX, totalHeight),
    };
  };

  /**
   * Returns marker metadata from geofield geojson properties.
   *
   * @param {google.maps.Marker} marker
   *   Map marker.
   *
   * @return {{nid: string, label: string}}
   *   Node id and price label.
   */
  Drupal.psSearchMap.getMarkerMeta = function (marker) {
    const props = marker.get('geojsonProperties') || {};
    return {
      nid: String(props.ps_search_nid || props.entity_id || ''),
      label: String(props.ps_search_price || props.tooltip || ''),
    };
  };

  /**
   * Indexes geofield markers by offer node id for list/map sync.
   *
   * @param {object} mapData
   *   Geofield map data bucket.
   */
  Drupal.psSearchMap.indexMarkersByNid = function (mapData) {
    mapData.markersByNid = {};
    Object.keys(mapData.markers || {}).forEach(function (key) {
      const marker = mapData.markers[key];
      const meta = Drupal.psSearchMap.getMarkerMeta(marker);
      if (meta.nid) {
        mapData.markersByNid[meta.nid] = marker;
      }
    });
  };

  /**
   * Waits until the geofield map and its markers are ready.
   *
   * @param {HTMLElement} root
   *   Search view root.
   * @param {function} callback
   *   Called with mapData when ready.
   */
  Drupal.psSearchMap.whenMapReady = function (root, callback) {
    const mapEl = root.querySelector('.geofield-google-map');
    if (!mapEl) {
      return;
    }

    const mapId = mapEl.id;
    let attempts = 0;

    const timer = window.setInterval(function () {
      attempts += 1;
      const mapData = Drupal.geoFieldMapFormatter?.map_data?.[mapId];
      const map = mapData?.map || mapData?.google_map;
      const markerCount = Object.keys(mapData?.markers || {}).length;

      if (map && typeof google !== 'undefined' && google.maps && markerCount > 0) {
        window.clearInterval(timer);
        Drupal.psSearchMap.indexMarkersByNid(mapData);
        callback(mapData);
        root.dispatchEvent(new CustomEvent('ps-search-map-markers-loaded', {
          detail: { mapData: mapData },
        }));
        return;
      }

      if (attempts >= 300) {
        window.clearInterval(timer);
      }
    }, 200);
  };

  /**
   * Whether the results list pane is visible (split view).
   *
   * @param {HTMLElement} root
   *   Search view root.
   *
   * @return {boolean}
   *   TRUE when the list column is shown.
   */
  Drupal.psSearchMap.isListVisible = function (root) {
    return !root.classList.contains('ps-search-view--list-hidden');
  };

  /**
   * Scrolls the results list to a card.
   *
   * @param {HTMLElement} root
   *   Search view root.
   * @param {HTMLElement} card
   *   Offer card element.
   */
  Drupal.psSearchMap.scrollToCard = function (root, card) {
    if (!card) {
      return;
    }

    const scrollEl = root.querySelector('.js-ps-search-results-scroll');
    if (scrollEl && scrollEl.contains(card)) {
      const scrollRect = scrollEl.getBoundingClientRect();
      const cardRect = card.getBoundingClientRect();
      scrollEl.scrollTop += cardRect.top - scrollRect.top - 12;
      return;
    }

    card.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
  };

  /**
   * Closes any geofield map InfoWindow on the search map.
   *
   * @param {object} mapData
   *   Geofield map data bucket.
   */
  Drupal.psSearchMap.closeGeofieldInfoWindow = function (mapData) {
    if (mapData?.infowindow && typeof mapData.infowindow.close === 'function') {
      mapData.infowindow.close();
    }
  };

}(Drupal));
