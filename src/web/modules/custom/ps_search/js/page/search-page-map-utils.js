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
   * Builds a count bubble icon for server-side grid clusters.
   *
   * @param {number|string} count
   *   Number of offers in the cluster cell.
   *
   * @return {google.maps.Icon}
   *   Google Maps icon descriptor.
   */
  Drupal.psSearchMap.buildClusterMarkerIcon = function (count) {
    const label = String(count);
    const diameter = Math.max(36, label.length * 10 + 20);
    const radius = Math.floor(diameter / 2);
    const center = radius;
    const safeLabel = label
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;');
    const svg = '<?xml version="1.0" encoding="UTF-8"?>'
      + `<svg xmlns="http://www.w3.org/2000/svg" width="${diameter}" height="${diameter}" viewBox="0 0 ${diameter} ${diameter}">`
      + `<circle cx="${center}" cy="${center}" r="${radius - 2}" fill="${MARKER_WHITE}" stroke="${MARKER_GREEN}" stroke-width="${BORDER}"/>`
      + `<text x="${center}" y="${center + 1}" dominant-baseline="central" text-anchor="middle" fill="${MARKER_GREEN}" font-family="Arial, Helvetica, sans-serif" font-size="13" font-weight="700">${safeLabel}</text>`
      + '</svg>';

    return {
      url: `data:image/svg+xml;charset=UTF-8,${encodeURIComponent(svg)}`,
      scaledSize: new google.maps.Size(diameter, diameter),
      anchor: new google.maps.Point(center, center),
    };
  };

  /**
   * Returns marker metadata from marker geojson-style properties.
   *
   * @param {google.maps.Marker} marker
   *   Map marker.
   *
   * @return {{nid: string, label: string}}
   *   Node id and price label.
   */
  Drupal.psSearchMap.getMarkerMeta = function (marker) {
    const props = marker.geojsonProperties
      || (typeof marker.get === 'function' ? marker.get('geojsonProperties') : null)
      || {};
    return {
      nid: String(props.ps_search_nid || props.entity_id || ''),
      label: String(props.ps_search_price || props.tooltip || ''),
    };
  };

  /**
   * Indexes map markers by offer node id for list/map sync.
   *
   * @param {object} mapData
   *   PS map data bucket.
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
   * Returns markers attached to a MarkerClusterer cluster instance.
   *
   * @param {object} cluster
   *   MarkerClusterer cluster.
   *
   * @return {Array<google.maps.Marker>}
   *   Markers in the cluster.
   */
  Drupal.psSearchMap.getClusterMarkers = function (cluster) {
    if (!cluster) {
      return [];
    }
    if (typeof cluster.getMarkers === 'function') {
      return cluster.getMarkers();
    }
    return Array.isArray(cluster.markers_) ? cluster.markers_ : [];
  };

  /**
   * Finds the MarkerClusterer group containing a marker (multi-marker only).
   *
   * @param {object|null} markerCluster
   *   MarkerClusterer instance.
   * @param {google.maps.Marker|null} marker
   *   Offer marker.
   *
   * @return {object|null}
   *   Cluster instance or NULL when the marker is shown individually.
   */
  Drupal.psSearchMap.findClusterForMarker = function (markerCluster, marker) {
    if (!markerCluster || !marker || typeof markerCluster.getClusters !== 'function') {
      return null;
    }

    const clusters = markerCluster.getClusters();
    for (let i = 0; i < clusters.length; i++) {
      const cluster = clusters[i];
      const markers = Drupal.psSearchMap.getClusterMarkers(cluster);
      if (markers.length > 1 && markers.indexOf(marker) !== -1) {
        return cluster;
      }
    }

    return null;
  };

  /**
   * Returns the DOM node for a MarkerClusterer cluster icon.
   *
   * @param {object|null} cluster
   *   MarkerClusterer cluster.
   *
   * @return {HTMLElement|null}
   *   Cluster icon element (class "cluster").
   */
  Drupal.psSearchMap.getClusterElement = function (cluster) {
    if (!cluster) {
      return null;
    }

    const icon = cluster.clusterIcon_ || cluster.clusterIcon;
    if (!icon) {
      return null;
    }

    return icon.div_ || icon.div || null;
  };

  /**
   * Applies list-sync highlight styling to a MarkerClusterer cluster icon.
   *
   * @param {object|null} cluster
   *   MarkerClusterer cluster.
   * @param {boolean} active
   *   Whether the cluster should look hovered/active.
   */
  Drupal.psSearchMap.setClusterHighlight = function (cluster, active) {
    const element = Drupal.psSearchMap.getClusterElement(cluster);
    if (!element) {
      return;
    }
    element.classList.toggle('is-map-sync-active', !!active);
  };

  /**
   * Returns the PS map container within a search view root.
   *
   * @param {HTMLElement} root
   *   Search view root.
   *
   * @return {HTMLElement|null}
   *   Map shell element.
   */
  function resolveMapElement(root) {
    if (typeof Drupal.psSearchMap.getMapRoot === 'function') {
      return Drupal.psSearchMap.getMapRoot(root);
    }
    const elementId = (typeof drupalSettings !== 'undefined' && drupalSettings.psSearch?.map?.elementId)
      || 'ps-search-map';
    return root?.querySelector(`#${elementId}`) || null;
  }

  /**
   * Returns PS map data for the search view when the shell is initialized.
   *
   * @param {HTMLElement} root
   *   Search view root.
   *
   * @return {object|null}
   *   PS map data bucket.
   */
  Drupal.psSearchMap.getMapData = function (root) {
    const mapEl = resolveMapElement(root);
    if (!mapEl || mapEl.dataset.psMapShellInit !== '1') {
      return null;
    }
    const mapData = Drupal.psSearchMap.instances?.[mapEl.id];
    if (!mapData) {
      return null;
    }
    if (!mapData.markersByNid) {
      Drupal.psSearchMap.indexMarkersByNid(mapData);
    }
    return mapData;
  };

  /**
   * Waits until the PS map shell is initialized (markers optional).
   *
   * @param {HTMLElement} root
   *   Search view root.
   * @param {function} callback
   *   Called with mapData when the map shell is ready.
   */
  Drupal.psSearchMap.whenMapShellReady = function (root, callback) {
    const mapEl = resolveMapElement(root);
    if (!mapEl) {
      return;
    }

    const finish = function (mapData) {
      const map = mapData?.map;
      if (map && typeof map.setOptions === 'function') {
        map.setOptions({ mapTypeControl: false });
      }
      Drupal.psSearchMap.resizeMaps(root);
      callback(mapData);
    };

    const tryReady = function () {
      const mapData = Drupal.psSearchMap.getMapData(root);
      const map = mapData?.map;
      if (map && typeof google !== 'undefined' && google.maps) {
        finish(mapData);
        return true;
      }
      return false;
    };

    if (tryReady()) {
      return;
    }

    const onShellReady = function (event) {
      if (event.target !== root) {
        return;
      }
      root.removeEventListener('ps-search-map-shell-ready', onShellReady);
      const mapData = Drupal.psSearchMap.getMapData(root);
      if (mapData?.map) {
        finish(mapData);
      }
    };
    root.addEventListener('ps-search-map-shell-ready', onShellReady);

    if (typeof Drupal.psSearchMap.initShell === 'function' && drupalSettings.psSearch?.map?.enabled) {
      Drupal.psSearchMap.initShell(root);
    }

    let attempts = 0;
    const timer = window.setInterval(function () {
      attempts += 1;
      if (tryReady()) {
        window.clearInterval(timer);
        root.removeEventListener('ps-search-map-shell-ready', onShellReady);
        return;
      }
      if (attempts >= 300) {
        window.clearInterval(timer);
        root.removeEventListener('ps-search-map-shell-ready', onShellReady);
      }
    }, 200);
  };

  /**
   * Waits until the PS map and its markers are ready.
   *
   * @param {HTMLElement} root
   *   Search view root.
   * @param {function} callback
   *   Called with mapData when ready.
   */
  Drupal.psSearchMap.whenMapReady = function (root, callback) {
    const mapEl = resolveMapElement(root);
    if (!mapEl) {
      return;
    }

    let completed = false;

    const finish = function (mapData) {
      if (completed) {
        return;
      }
      completed = true;
      Drupal.psSearchMap.indexMarkersByNid(mapData);
      const map = mapData?.map;
      if (map && typeof map.setOptions === 'function') {
        map.setOptions({ mapTypeControl: false });
      }
      Drupal.psSearchMap.resizeMaps(root);
      root.classList.add('is-map-ready');
      callback(mapData);
    };

    const getReadyMapData = function () {
      const mapData = Drupal.psSearchMap.getMapData(root);
      const map = mapData?.map;
      const markerCount = Object.keys(mapData?.markers || {}).length;

      if (map && typeof google !== 'undefined' && google.maps && markerCount > 0) {
        return mapData;
      }
      return null;
    };

    const invokeWhenReady = function () {
      const mapData = getReadyMapData();
      if (!mapData) {
        return false;
      }
      root.removeEventListener('ps-search-map-markers-loaded', onMarkersLoaded);
      window.clearInterval(timer);
      finish(mapData);
      return true;
    };

    const readyMapData = getReadyMapData();
    if (readyMapData) {
      finish(readyMapData);
      return;
    }

    const onMarkersLoaded = function (event) {
      if (event.target !== root || completed) {
        return;
      }
      invokeWhenReady();
    };
    root.addEventListener('ps-search-map-markers-loaded', onMarkersLoaded);

    let attempts = 0;
    const timer = window.setInterval(function () {
      attempts += 1;
      if (invokeWhenReady()) {
        return;
      }
      if (attempts >= 300) {
        window.clearInterval(timer);
        root.removeEventListener('ps-search-map-markers-loaded', onMarkersLoaded);
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
   * Returns the click-pinned offer id for list/map sync.
   *
   * @param {HTMLElement} root
   *   Search view root.
   *
   * @return {string|null}
   *   Selected offer node id, if any.
   */
  Drupal.psSearchMap.getSelectedOfferId = function (root) {
    const nid = root?.dataset?.psSelectedOfferId;
    return nid ? String(nid) : null;
  };

  /**
   * Stores the click-pinned offer id on the search view root.
   *
   * @param {HTMLElement} root
   *   Search view root.
   * @param {string|null} nid
   *   Offer node id or NULL to clear.
   */
  Drupal.psSearchMap.setSelectedOfferId = function (root, nid) {
    if (!root) {
      return;
    }
    if (nid) {
      root.dataset.psSelectedOfferId = String(nid);
      return;
    }
    delete root.dataset.psSelectedOfferId;
  };

  /**
   * Estimated sticky chrome height for document scroll offset (header + toolbar).
   *
   * @return {number}
   *   Pixel offset from viewport top.
   */
  Drupal.psSearchMap.getDocumentScrollOffset = function () {
    const rootStyles = getComputedStyle(document.documentElement);
    const headerSlot = Number.parseFloat(rootStyles.getPropertyValue('--ps-header-slot-height')) || 76;
    const toolbar = document.querySelector('.ps-search-view__mobile-toolbar-wrap');
    const toolbarHeight = toolbar ? toolbar.getBoundingClientRect().height : 56;
    return headerSlot + toolbarHeight + 12;
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

    const scrollEl = typeof Drupal.psSearchPage?.getListScrollEl === 'function'
      ? Drupal.psSearchPage.getListScrollEl(root)
      : root.querySelector('.js-ps-search-left-scroll, .js-ps-search-list-panel');
    if (scrollEl && scrollEl.contains(card)) {
      const scrollRect = scrollEl.getBoundingClientRect();
      const cardRect = card.getBoundingClientRect();
      scrollEl.scrollTop += cardRect.top - scrollRect.top - 12;
      return;
    }

    if (typeof Drupal.psSearchPage?.usesDocumentListScroll === 'function'
      && Drupal.psSearchPage.usesDocumentListScroll(root)) {
      const offset = Drupal.psSearchMap.getDocumentScrollOffset();
      const targetTop = card.getBoundingClientRect().top + window.scrollY - offset;
      window.scrollTo({ top: Math.max(0, targetTop), behavior: 'smooth' });
      return;
    }

    card.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
  };

  /**
   * Closes any map InfoWindow on the search map.
   *
   * @param {object} mapData
   *   PS map data bucket.
   */
  Drupal.psSearchMap.closeMapInfoWindow = function (mapData) {
    if (mapData?.infowindow && typeof mapData.infowindow.close === 'function') {
      mapData.infowindow.close();
    }
  };

  Drupal.psSearchMap.closeGeofieldInfoWindow = Drupal.psSearchMap.closeMapInfoWindow;

  /**
   * Bottom offset for zoom controls (above Google attribution).
   *
   * @param {HTMLElement} root
   *   Search view root.
   *
   * @return {string}
   *   CSS length for the bottom property.
   */
  Drupal.psSearchMap.getZoomControlBottom = function (root) {
    if (root?.classList.contains('ps-search-view--list-hidden')) {
      return 'calc(1rem + env(safe-area-inset-bottom, 0px))';
    }
    return '2.125rem';
  };

  /**
   * Pins Google Maps zoom controls bottom-left (BNPPRE maquette).
   *
   * Google may re-apply inline top after resize/idle; clear top/right explicitly.
   *
   * @param {HTMLElement} root
   *   Search view root.
   */
  Drupal.psSearchMap.positionZoomControls = function (root) {
    const mapEl = Drupal.psSearchMap.getMapRoot(root);
    if (!mapEl) {
      return;
    }

    const bottom = Drupal.psSearchMap.getZoomControlBottom(root);
    mapEl.querySelectorAll('.gm-bundled-control-on-bottom, .gm-bundled-control').forEach(function (control) {
      if (!control.querySelector('[title="Zoom in"], [aria-label="Zoom in"]')) {
        return;
      }
      control.style.removeProperty('top');
      control.style.removeProperty('right');
      control.style.setProperty('height', 'auto', 'important');
      control.style.setProperty('top', 'auto', 'important');
      control.style.setProperty('bottom', bottom, 'important');
      control.style.setProperty('left', '1rem', 'important');
      control.querySelectorAll(':scope > .gmnoprint').forEach(function (inner) {
        inner.style.removeProperty('top');
        inner.style.setProperty('position', 'static', 'important');
      });
    });
  };

  /**
   * Re-applies zoom control position after Google Maps layout passes.
   *
   * @param {HTMLElement} root
   *   Search view root.
   */
  Drupal.psSearchMap.scheduleZoomControlPosition = function (root) {
    [0, 300, 1000].forEach(function (delay) {
      window.setTimeout(function () {
        Drupal.psSearchMap.positionZoomControls(root);
      }, delay);
    });
  };

  /**
   * Triggers Google Maps resize after layout changes.
   *
   * @param {HTMLElement} root
   *   Search view root element.
   */
  Drupal.psSearchMap.resizeMaps = function (root) {
    const mapData = Drupal.psSearchMap.getMapData(root);
    const map = mapData?.map;
    if (typeof google !== 'undefined' && google.maps && map) {
      const center = map.getCenter();
      const zoom = map.getZoom();
      google.maps.event.trigger(map, 'resize');
      if (center) {
        map.setCenter(center);
      }
      if (typeof zoom === 'number') {
        map.setZoom(zoom);
      }
      Drupal.psSearchMap.scheduleZoomControlPosition(root);
    }
  };

  /**
   * Toggles list pane vs offer detail sidebar and resizes the map.
   *
   * @param {HTMLElement} root
   *   Search view root.
   * @param {boolean} visible
   *   Whether the list pane should be visible.
   */
  Drupal.psSearchMap.setListVisible = function (root, visible) {
    root.classList.toggle('ps-search-view--list-hidden', !visible);

    const hideBtn = root.querySelector('.js-ps-hide-list');
    const showBtn = root.querySelector('.js-ps-show-list');

    if (hideBtn) {
      hideBtn.setAttribute('aria-expanded', visible ? 'true' : 'false');
      hideBtn.hidden = !visible;
    }
    if (showBtn) {
      showBtn.hidden = visible;
    }

    if (visible) {
      root.dispatchEvent(new CustomEvent('ps-search-list-shown'));
    }
    else {
      root.dispatchEvent(new CustomEvent('ps-search-map-mode'));
    }

    document.dispatchEvent(new CustomEvent('ps-search-map-resize'));
    requestAnimationFrame(function () {
      setTimeout(function () {
        Drupal.psSearchMap.resizeMaps(root);
      }, 50);
      setTimeout(function () {
        Drupal.psSearchMap.resizeMaps(root);
      }, 350);
    });
  };

  Drupal.psSearchPage = Drupal.psSearchPage || {};

  const FACET_PARAM_KEYS = ['operation_type', 'asset_type'];

  /**
   * Reads a facet code from scalar or Views bracket query params.
   *
   * @param {URLSearchParams} params
   *   Query parameters.
   * @param {string} key
   *   Facet parameter name.
   *
   * @return {string|null}
   *   Facet code or NULL.
   */
  function readFacetValue(params, key) {
    const bracketPrefix = key + '[';
    let scalarValue = null;

    params.forEach(function (value, name) {
      if (name === key) {
        scalarValue = value;
        return;
      }
      if (name.indexOf(bracketPrefix) === 0 && name.charAt(name.length - 1) === ']') {
        scalarValue = value || name.slice(bracketPrefix.length, -1);
      }
    });

    return scalarValue ? String(scalarValue) : null;
  }

  /**
   * Removes scalar and bracket variants of a facet param.
   *
   * @param {URLSearchParams} params
   *   Query parameters.
   * @param {string} key
   *   Facet parameter name.
   */
  function clearFacetParams(params, key) {
    Array.from(params.keys()).forEach(function (name) {
      if (name === key || name.indexOf(key + '[') === 0) {
        params.delete(name);
      }
    });
  }

  /**
   * Sets a facet param using Views/Facets bracket format.
   *
   * @param {URLSearchParams} params
   *   Query parameters.
   * @param {string} key
   *   Facet parameter name.
   * @param {string} value
   *   Facet code.
   */
  function setFacetQueryParam(params, key, value) {
    clearFacetParams(params, key);
    if (value) {
      params.append(key + '[' + value + ']', value);
    }
  }

  /**
   * Converts SEO/scalar facet params to Views AJAX bracket format.
   *
   * @param {URLSearchParams} params
   *   Query parameters.
   *
   * @return {URLSearchParams}
   *   Params safe for /views/ajax requests.
   */
  Drupal.psSearchPage.normalizeFacetParamsForViewsAjax = function (params) {
    const normalized = new URLSearchParams(params);
    FACET_PARAM_KEYS.forEach(function (key) {
      const value = readFacetValue(normalized, key);
      if (value) {
        setFacetQueryParam(normalized, key, value);
      }
    });
    return normalized;
  };

  /**
   * Builds Views AJAX params: SEO/server filters + request overrides, facet-safe.
   *
   * @param {URLSearchParams} [requestParams]
   *   Params from a Views AJAX URL (page, sort, exposed filters).
   *
   * @return {URLSearchParams}
   *   Normalized params for /views/ajax.
   */
  Drupal.psSearchPage.buildViewsAjaxParams = function (requestParams) {
    const base = Drupal.psSearchPage.buildSearchParams();
    if (requestParams instanceof URLSearchParams) {
      requestParams.forEach(function (value, key) {
        base.set(key, value);
      });
    }
    return Drupal.psSearchPage.normalizeFacetParamsForViewsAjax(base);
  };

  /**
   * Converts facet params to scalar format for the markers API.
   *
   * @param {URLSearchParams} params
   *   Query parameters.
   *
   * @return {URLSearchParams}
   *   Params safe for /api/ps/markers requests.
   */
  Drupal.psSearchPage.normalizeFacetParamsForMarkersApi = function (params) {
    const normalized = new URLSearchParams(params);
    FACET_PARAM_KEYS.forEach(function (key) {
      const value = readFacetValue(normalized, key);
      clearFacetParams(normalized, key);
      if (value) {
        normalized.set(key, value);
      }
    });
    return normalized;
  };

  /**
   * Reads operation_type / asset_type codes from SEO path segments.
   *
   * @return {{operation_type?: string, asset_type?: string}}
   *   Facet codes inferred from /for-rent/office/ style paths.
   */
  function resolveSeoFacetsFromPath() {
    const settings = drupalSettings.psSearch || {};
    const opSlugs = settings.opSlugs || {};
    const assetSlugs = settings.assetSlugs || {};
    const langPrefix = settings.langPrefix || '';
    let path = window.location.pathname || '';
    if (langPrefix && path.indexOf(langPrefix + '/') === 0) {
      path = path.slice(langPrefix.length);
    }

    const segments = path.split('/').filter(Boolean);
    const searchPath = String(settings.searchPath || '/find-property').replace(/^\/+/, '').replace(/\/+$/, '');
    if (segments.length === 0 || segments[0] === searchPath) {
      return {};
    }

    let operationType = null;
    let assetType = null;
    Object.keys(opSlugs).forEach(function (code) {
      if (opSlugs[code] === segments[0]) {
        operationType = code;
      }
    });
    if (operationType && segments[1]) {
      Object.keys(assetSlugs).forEach(function (code) {
        if (assetSlugs[code] === segments[1]) {
          assetType = code;
        }
      });
    }
    else if (!operationType) {
      Object.keys(assetSlugs).forEach(function (code) {
        if (assetSlugs[code] === segments[0]) {
          assetType = code;
        }
      });
    }

    const facets = {};
    if (operationType) {
      facets.operation_type = operationType;
    }
    if (assetType) {
      facets.asset_type = assetType;
    }
    return facets;
  }

  function isFacetBracketParamKey(key) {
    return FACET_PARAM_KEYS.some(function (facetKey) {
      return key.indexOf(facetKey + '[') === 0 && key.charAt(key.length - 1) === ']';
    });
  }

  /**
   * Persists active facet filters into drupalSettings.path.currentQuery.
   *
   * Required after history.pushState to a SEO URL without a full page reload.
   *
   * @param {URLSearchParams} params
   *   Active filter params (Views bracket or scalar).
   */
  Drupal.psSearchPage.syncPathCurrentQueryFromParams = function (params) {
    if (!(params instanceof URLSearchParams)) {
      return;
    }
    drupalSettings.path = drupalSettings.path || {};
    const currentQuery = Object.assign({}, drupalSettings.path.currentQuery || {});
    const pathFacets = resolveSeoFacetsFromPath();

    FACET_PARAM_KEYS.forEach(function (key) {
      const pathValue = pathFacets[key];
      if (pathValue) {
        currentQuery[key] = pathValue;
        return;
      }
      const value = readFacetValue(params, key);
      if (value) {
        currentQuery[key] = value;
      }
      else {
        delete currentQuery[key];
      }
    });

    Object.keys(currentQuery).forEach(function (key) {
      if (isFacetBracketParamKey(key)) {
        delete currentQuery[key];
      }
    });

    const skipKeys = ['page', 'lang', '_drupal_ajax'];
    params.forEach(function (value, key) {
      if (FACET_PARAM_KEYS.indexOf(key) !== -1 || isFacetBracketParamKey(key)) {
        return;
      }
      if (skipKeys.indexOf(key) !== -1 || key.indexOf('view_') === 0) {
        return;
      }
      if (key.indexOf('[') !== -1) {
        if (value) {
          currentQuery[key] = value;
        }
        else {
          delete currentQuery[key];
        }
        return;
      }
      if (value) {
        currentQuery[key] = value;
      }
      else {
        delete currentQuery[key];
      }
    });

    drupalSettings.path.currentQuery = currentQuery;
  };

  /**
   * Ensures ?lang= matches the active content language for /api/ps/* calls.
   *
   * @param {URLSearchParams} params
   *   Query params to augment in place.
   *
   * @return {URLSearchParams}
   *   The same params instance.
   */
  Drupal.psSearchPage.appendContentLangParam = function (params) {
    if (!(params instanceof URLSearchParams) || params.has('lang')) {
      return params;
    }
    const lang = drupalSettings.path?.currentLanguage
      || document.documentElement.lang?.split('-')[0]
      || '';
    if (lang) {
      params.set('lang', lang);
    }
    return params;
  };

  /**
   * Builds search query params from the browser URL + server-injected SEO filters.
   *
   * SEO paths (/for-rent/office/) inject operation_type/asset_type server-side
   * into drupalSettings.path.currentQuery; window.location.search stays empty.
   *
   * @param {URLSearchParams} [overrides]
   *   Optional params merged last (e.g. map_bounds after pan).
   *
   * @return {URLSearchParams}
   *   Merged query parameters for markers API and Views AJAX.
   */
  Drupal.psSearchPage.buildSearchParams = function (overrides) {
    if (typeof Drupal.psSearchContext !== 'undefined' && Drupal.psSearchContext.isEnabled()) {
      const params = Drupal.psSearchContext.buildApiParams();
      if (overrides instanceof URLSearchParams) {
        overrides.forEach(function (value, key) {
          params.set(key, value);
        });
      }
      return params;
    }

    const params = new URLSearchParams(window.location.search);
    const serverQuery = (typeof drupalSettings !== 'undefined' && drupalSettings.path?.currentQuery) || {};

    Object.keys(serverQuery).forEach(function (key) {
      if (params.has(key)) {
        return;
      }
      const value = serverQuery[key];
      if (value === null || value === undefined || value === '') {
        return;
      }
      if (Array.isArray(value)) {
        value.forEach(function (item) {
          if (item !== null && item !== undefined && item !== '') {
            params.append(key, String(item));
          }
        });
        return;
      }
      if (typeof value === 'object') {
        return;
      }
      params.set(key, String(value));
    });

    const pathFacets = resolveSeoFacetsFromPath();
    Object.keys(pathFacets).forEach(function (key) {
      if (!readFacetValue(params, key)) {
        params.set(key, pathFacets[key]);
      }
    });

    if (overrides instanceof URLSearchParams) {
      overrides.forEach(function (value, key) {
        params.set(key, value);
      });
    }

    Drupal.psSearchPage.appendContentLangParam(params);

    return params;
  };

}(Drupal));
