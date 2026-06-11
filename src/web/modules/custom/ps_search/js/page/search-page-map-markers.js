(function (Drupal, once) {
  'use strict';

  /**
   * @typedef {object} ApplyMarkersOptions
   * @property {boolean} [preserveViewport]
   *   When true, keeps the current map zoom/center.
   */

  /**
   * Builds the markers API URL preserving current search query parameters.
   */
  function buildMarkersUrl(baseUrl) {
    const url = new URL(baseUrl, window.location.origin);
    const params = typeof Drupal.psSearchPage?.buildSearchParams === 'function'
      ? Drupal.psSearchPage.buildSearchParams()
      : new URLSearchParams(window.location.search);
    params.forEach(function (value, key) {
      url.searchParams.set(key, value);
    });
    return url.pathname + url.search;
  }

  /**
   * Returns the PS map data bucket when the shell is ready.
   */
  function requireMapData(root) {
    const mapData = Drupal.psSearchMap.getMapData(root);
    return mapData?.map ? mapData : null;
  }

  /**
   * Clears existing markers and cluster state on the PS map bucket.
   */
  function clearMarkers(mapData) {
    Object.keys(mapData.markers || {}).forEach(function (key) {
      const marker = mapData.markers[key];
      if (!marker) {
        return;
      }
      if (mapData.oms && typeof mapData.oms.removeMarker === 'function') {
        mapData.oms.removeMarker(marker);
      }
      if (typeof marker.setMap === 'function') {
        marker.setMap(null);
      }
    });
    mapData.markers = {};

    if (mapData.markerCluster && typeof mapData.markerCluster.clearMarkers === 'function') {
      mapData.markerCluster.clearMarkers();
      mapData.markerCluster = null;
    }
  }

  /**
   * Initializes OverlappingMarkerSpiderfier when injecting API markers.
   */
  function ensureOms(mapData) {
    if (mapData.oms || typeof OverlappingMarkerSpiderfier === 'undefined' || !mapData.map) {
      return;
    }

    const omsSettings = mapData.map_oms || {};
    if (!omsSettings.map_oms_control) {
      return;
    }

    let omsOptions = {
      markersWontMove: true,
      markersWontHide: true,
      basicFormatEvents: true,
      keepSpiderfied: true,
    };

    if (omsSettings.map_oms_options && omsSettings.map_oms_options.length > 0) {
      try {
        omsOptions = JSON.parse(omsSettings.map_oms_options);
      }
      catch (e) {
        // Keep default OMS options when JSON is invalid.
      }
    }

    mapData.oms = new OverlappingMarkerSpiderfier(mapData.map, omsOptions);
  }

  /**
   * Places a client-side marker without native infowindow click handlers.
   */
  function placeClientMarker(marker, mapData) {
    ensureOms(mapData);

    if (mapData.oms) {
      mapData.oms.addMarker(marker);
    }
    else {
      marker.setMap(mapData.map);
    }

    const entityId = marker.geojsonProperties?.entity_id;
    if (entityId) {
      mapData.markers[entityId] = marker;
    }

    const position = marker.getPosition?.();
    if (position && mapData.map_bounds) {
      mapData.map_bounds.extend(position);
    }
  }

  /**
   * Whether all markers in a cluster share the exact same coordinates.
   */
  function markersSharePosition(markers) {
    if (!markers || markers.length < 2) {
      return false;
    }

    const first = markers[0].getPosition?.();
    if (!first) {
      return false;
    }

    const key = first.toUrlValue(7);
    return markers.every(function (marker) {
      const position = marker.getPosition?.();
      return position && position.toUrlValue(7) === key;
    });
  }

  /**
   * Spiderfies co-located markers via OMS when clustering cannot split them.
   */
  function spiderfyColocatedMarkers(mapData, markers) {
    if (!mapData.oms || !mapData.map || markers.length < 2) {
      return;
    }

    const center = markers[0].getPosition?.();
    if (center) {
      mapData.map.setCenter(center);
      if (mapData.map.getZoom() < 17) {
        mapData.map.setZoom(17);
      }
    }

    markers.forEach(function (marker) {
      if (mapData.markerCluster && typeof mapData.markerCluster.removeMarker === 'function') {
        mapData.markerCluster.removeMarker(marker);
      }
    });

    google.maps.event.trigger(markers[0], 'spider_click');
  }

  /**
   * Handles cluster clicks for markers that share the same coordinates.
   *
   * Uses MarkerClusterer Plus colocated spiderfy bridge.
   */
  function bindColocatedClusterHandler(mapData) {
    if (!mapData.markerCluster || mapData.__psSearchColocatedClusterBound) {
      return;
    }

    mapData.__psSearchColocatedClusterBound = true;

    google.maps.event.addListener(mapData.markerCluster, 'clusterclick', function (cluster) {
      const markers = Drupal.psSearchMap.getClusterMarkers(cluster);
      if (!markersSharePosition(markers)) {
        return;
      }

      spiderfyColocatedMarkers(mapData, markers);
    });
  }

  /**
   * Splits API markers between clusterer (distinct positions) and OMS-only groups.
   */
  function splitClusterableMarkers(mapData) {
    const groups = {};
    Object.keys(mapData.markers || {}).forEach(function (key) {
      const marker = mapData.markers[key];
      const position = marker?.getPosition?.();
      if (!position) {
        return;
      }
      const groupKey = position.toUrlValue(7);
      if (!groups[groupKey]) {
        groups[groupKey] = [];
      }
      groups[groupKey].push(marker);
    });

    const clusterable = [];
    const colocated = [];

    Object.keys(groups).forEach(function (groupKey) {
      const group = groups[groupKey];
      if (group.length > 1) {
        colocated.push.apply(colocated, group);
      }
      else {
        clusterable.push(group[0]);
      }
    });

    return { clusterable: clusterable, colocated: colocated };
  }

  /**
   * Whether OMS is active for this map bucket.
   */
  function isOmsActive(mapData) {
    if (mapData.oms) {
      return true;
    }
    const omsSettings = mapData.map_oms || {};
    return !!(omsSettings.map_oms_control && typeof OverlappingMarkerSpiderfier !== 'undefined');
  }

  /**
   * Shows individual price markers above maxZoom; clusters below.
   */
  function syncMarkerClusterVisibility(mapData) {
    if (!mapData.map || !mapData.__psSearchClusterOptions) {
      return;
    }

    const maxZoom = mapData.__psSearchClusterMaxZoom ?? 14;
    const zoom = mapData.map.getZoom();
    const shouldCluster = zoom <= maxZoom;
    const mode = shouldCluster ? 'cluster' : 'markers';
    const markers = Object.values(mapData.markers || {}).filter(function (marker) {
      return marker?.getPosition?.();
    });

    if (mapData.__psSearchClusterMode === mode) {
      if (mode !== 'cluster' || mapData.markerCluster || markers.length === 0) {
        return;
      }
    }

    if (zoom > maxZoom) {
      if (mapData.markerCluster) {
        mapData.markerCluster.clearMarkers();
        mapData.markerCluster = null;
      }
      markers.forEach(function (marker) {
        marker.setMap(mapData.map);
      });
      mapData.__psSearchClusterMode = mode;
      return;
    }

    if (markers.length === 0) {
      return;
    }

    markers.forEach(function (marker) {
      marker.setMap(null);
    });

    mapData.__psSearchColocatedClusterBound = false;
    mapData.markerCluster = new MarkerClusterer(
      mapData.map,
      markers,
      mapData.__psSearchClusterOptions,
    );
    bindColocatedClusterHandler(mapData);
    mapData.__psSearchClusterMode = mode;
  }

  /**
   * Keeps cluster vs marker display in sync with map zoom (MarkerClusterer maxZoom).
   */
  function bindClusterZoomSync(mapData, options) {
    if (!mapData.map) {
      return;
    }

    mapData.__psSearchClusterMaxZoom = options.maxZoom ?? 14;
    mapData.__psSearchClusterOptions = options;

    if (mapData.__psSearchClusterZoomBound) {
      syncMarkerClusterVisibility(mapData);
      return;
    }

    mapData.__psSearchClusterZoomBound = true;
    google.maps.event.addListener(mapData.map, 'idle', function () {
      syncMarkerClusterVisibility(mapData);
    });
    syncMarkerClusterVisibility(mapData);
  }

  /**
   * Rebuilds MarkerClusterer for API-injected markers (dense zones only).
   */
  function rebuildApiMarkerCluster(mapData) {
    if (typeof MarkerClusterer === 'undefined' || !mapData.map) {
      return;
    }

    const clusterSettings = mapData.map_markercluster || {};
    const clusterEnabled = clusterSettings.markercluster_control !== false
      && drupalSettings.psSearch?.markersClusterEnabled !== false;

    const allMarkers = Object.values(mapData.markers || {}).filter(function (marker) {
      return marker?.getPosition?.();
    });

    if (!clusterEnabled) {
      allMarkers.forEach(function (marker) {
        if (isOmsActive(mapData)) {
          placeClientMarker(marker, mapData);
        }
        else {
          marker.setMap(mapData.map);
        }
      });
      return;
    }

    const options = {
      imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m',
      minimumClusterSize: 2,
      maxZoom: 14,
      gridSize: 60,
    };

    if (clusterSettings.markercluster_additional_options) {
      try {
        Object.assign(options, JSON.parse(clusterSettings.markercluster_additional_options));
      }
      catch (e) {
        // Keep default cluster options when JSON is invalid.
      }
    }

    const split = isOmsActive(mapData)
      ? splitClusterableMarkers(mapData)
      : { clusterable: allMarkers, colocated: [] };

    if (mapData.markerCluster && typeof mapData.markerCluster.clearMarkers === 'function') {
      mapData.markerCluster.clearMarkers();
      mapData.markerCluster = null;
    }

    split.colocated.forEach(function (marker) {
      placeClientMarker(marker, mapData);
    });

    split.clusterable.forEach(function (marker) {
      if (mapData.oms && typeof mapData.oms.removeMarker === 'function') {
        mapData.oms.removeMarker(marker);
      }
      marker.setMap(null);
    });

    if (split.clusterable.length === 0) {
      mapData.markerCluster = null;
      return;
    }

    bindClusterZoomSync(mapData, options);
  }

  /**
   * Fits the map to configured bounds or marker positions.
   */
  function fitMapToZone(root, mapData) {
    const map = mapData.map;
    if (!map || typeof google === 'undefined' || !google.maps) {
      return;
    }

    const bounds = new google.maps.LatLngBounds();
    let count = 0;

    Object.values(mapData.markers || {}).forEach(function (marker) {
      const position = marker.getPosition?.();
      if (position) {
        bounds.extend(position);
        count += 1;
      }
    });

    if (count > 0 && !bounds.isEmpty()) {
      map.fitBounds(bounds, 48);
      return;
    }

    const boundsConfig = window.drupalSettings?.psSearch?.mapBounds;
    if (boundsConfig && typeof boundsConfig.swLat === 'number') {
      map.fitBounds(new google.maps.LatLngBounds(
        new google.maps.LatLng(boundsConfig.swLat, boundsConfig.swLng),
        new google.maps.LatLng(boundsConfig.neLat, boundsConfig.neLng),
      ), 48);
    }
  }

  /**
   * Notifies map sync listeners that markers are ready.
   */
  function dispatchMapMarkersLoaded(root, mapData, options, displayMode) {
    root.classList.add('is-map-ready');
    root.dispatchEvent(new CustomEvent('ps-search-map-markers-loaded', {
      detail: {
        mapData: mapData,
        preserveViewport: options?.preserveViewport === true,
        displayMode: displayMode,
      },
    }));
  }

  /**
   * Renders server-side grid clusters and zooms into a cell on click.
   */
  function applyClusters(root, clusters, options) {
    const preserveViewport = options?.preserveViewport === true;
    const mapData = requireMapData(root);
    if (!mapData) {
      return;
    }

    clearMarkers(mapData);

    clusters.forEach(function (cluster, index) {
      const lat = parseFloat(cluster.lat);
      const lng = parseFloat(cluster.lng);
      const count = Number(cluster.count || 0);
      const mapBounds = String(cluster.map_bounds || '');
      if (!Number.isFinite(lat) || !Number.isFinite(lng) || count <= 0 || mapBounds === '') {
        return;
      }

      const marker = new google.maps.Marker({
        position: new google.maps.LatLng(lat, lng),
        icon: Drupal.psSearchMap.buildClusterMarkerIcon(count),
        map: mapData.map,
        zIndex: google.maps.Marker.MAX_ZINDEX + count,
      });

      marker.geojsonProperties = {
        entity_id: `cluster:${index}`,
        ps_search_cluster: true,
        ps_search_cluster_count: count,
        ps_search_map_bounds: mapBounds,
      };

      mapData.markers[`cluster:${index}`] = marker;

      google.maps.event.addListener(marker, 'click', function () {
        if (typeof Drupal.psSearchPage.reloadZoneSearch === 'function') {
          Drupal.psSearchPage.reloadZoneSearch(root, mapBounds);
        }
      });
    });

    if (!preserveViewport) {
      fitMapToZone(root, mapData);
    }
    Drupal.psSearchMap.resizeMaps(root);
    dispatchMapMarkersLoaded(root, mapData, options, 'clusters');
  }

  /**
   * Injects API markers into the PS map bucket.
   */
  function applyMarkers(root, markers, options) {
    const preserveViewport = options?.preserveViewport === true;
    const mapData = requireMapData(root);
    if (!mapData) {
      return;
    }

    clearMarkers(mapData);

    createAndStoreMarkers(mapData, markers);
    finalizeMarkers(root, mapData, options, preserveViewport, 'markers');
  }

  /**
   * Appends new API markers without clearing existing map state (load-more).
   */
  function appendMarkers(root, markers, options) {
    const mapData = requireMapData(root);
    if (!mapData) {
      return;
    }

    const newMarkers = createAndStoreMarkers(mapData, markers, true);
    if (newMarkers.length === 0) {
      return;
    }

    if (mapData.markerCluster && typeof mapData.markerCluster.addMarkers === 'function') {
      newMarkers.forEach(function (marker) {
        marker.setMap(null);
      });
      mapData.markerCluster.addMarkers(newMarkers);
    }
    else {
      newMarkers.forEach(function (marker) {
        if (isOmsActive(mapData)) {
          placeClientMarker(marker, mapData);
        }
        else {
          marker.setMap(mapData.map);
        }
      });
    }

    finalizeMarkers(root, mapData, options, true, 'markers');
  }

  /**
   * Creates Google markers and stores them on the map bucket.
   *
   * @return {Array<google.maps.Marker>}
   *   Newly created markers.
   */
  function createAndStoreMarkers(mapData, markers, skipExisting) {
    const created = [];
    markers.forEach(function (item) {
      const nid = String(item.nid || '');
      const lat = parseFloat(item.lat);
      const lng = parseFloat(item.lng);
      const label = String(item.label || '');
      if (!nid || !Number.isFinite(lat) || !Number.isFinite(lng)) {
        return;
      }
      if (skipExisting && mapData.markers[nid]) {
        return;
      }

      const position = new google.maps.LatLng(lat, lng);
      const marker = new google.maps.Marker({
        position: position,
        icon: Drupal.psSearchMap.buildPriceMarkerIcon(label, false),
        map: null,
      });

      marker.geojsonProperties = {
        entity_id: nid,
        ps_search_nid: nid,
        ps_search_price: label,
      };

      mapData.markers[nid] = marker;
      created.push(marker);
      if (mapData.map_bounds) {
        mapData.map_bounds.extend(position);
      }
    });
    return created;
  }

  /**
   * Shared post-processing after marker injection or append.
   */
  function finalizeMarkers(root, mapData, options, preserveViewport, displayMode) {
    Drupal.psSearchMap.indexMarkersByNid(mapData);
    if (displayMode === 'markers' && options?.incremental !== true) {
      rebuildApiMarkerCluster(mapData);
    }
    if (!preserveViewport) {
      fitMapToZone(root, mapData);
    }
    Drupal.psSearchMap.resizeMaps(root);
    dispatchMapMarkersLoaded(root, mapData, options, displayMode);
  }

  /**
   * Applies markers API payload (individual markers or server-side clusters).
   */
  function applyMarkersPayload(root, payload, options) {
    const data = payload && typeof payload === 'object' ? payload : {};
    const displayMode = String(data.display_mode || 'markers');
    const clusters = Array.isArray(data.clusters) ? data.clusters : [];
    const markers = Array.isArray(data.markers) ? data.markers : [];

    if (displayMode === 'clusters' && clusters.length > 0) {
      applyClusters(root, clusters, options);
      return;
    }

    if (options?.incremental === true && markers.length > 0) {
      appendMarkers(root, markers, options);
      return;
    }

    applyMarkers(root, markers, options);
  }

  /**
   * Loads markers from the API into the PS map shell.
   */
  function refreshMapMarkers(root, options) {
    options = options || {};
    Drupal.psSearchMap.whenMapShellReady(root, function () {
      loadMarkers(root, null, options);
    });
  }

  /**
   * Initializes map markers on first paint (API markers in PS shell).
   */
  function initMapMarkers(root) {
    refreshMapMarkers(root, { preserveViewport: false });
  }

  /**
   * Re-indexes markers after reload for list hover/click sync.
   */
  function initMapBridge(root, options) {
    options = options || {};
    const mapData = Drupal.psSearchMap.getMapData(root);
    if (!mapData?.map) {
      return;
    }

    Drupal.psSearchMap.indexMarkersByNid(mapData);
    bindColocatedClusterHandler(mapData);

    if (options.preserveViewport !== true && drupalSettings.psSearch?.autoFitToResults) {
      fitMapToZone(root, mapData);
    }

    Drupal.psSearchMap.resizeMaps(root);
    dispatchMapMarkersLoaded(root, mapData, options, 'ps-map');
  }

  /**
   * Fetches zone markers via API (legacy — not used for map display).
   */
  function loadMarkers(root, queryString, options) {
    const baseUrl = window.drupalSettings?.psSearch?.markersUrl || '/api/ps/markers';
    const url = (typeof queryString === 'string' && queryString.length > 0)
      ? `${baseUrl}?${queryString}`
      : buildMarkersUrl(baseUrl);

    return fetch(url, {
      credentials: 'same-origin',
      headers: { Accept: 'application/json' },
    })
      .then(function (response) {
        if (!response.ok) {
          throw new Error('markers_fetch_failed');
        }
        return response.json();
      })
      .then(function (data) {
        const payload = data && typeof data === 'object' ? data : { markers: [], zone_count: 0 };
        if (Number.isFinite(Number(payload.zone_count))) {
          drupalSettings.psSearch = drupalSettings.psSearch || {};
          drupalSettings.psSearch.zoneCount = Number(payload.zone_count);
        }
        root.psSearchMarkersCache = payload;
        applyMarkersPayload(root, payload, options);
        if (options?.incremental === true) {
          root.psSearchMarkersIncrementalApplied = true;
        }
        return payload;
      })
      .catch(function () {
        root.psSearchMarkersCache = { display_mode: 'markers', markers: [], clusters: [] };
        applyMarkersPayload(root, root.psSearchMarkersCache, options);
        return { markers: [], clusters: [], zone_count: 0, display_mode: 'markers' };
      });
  }

  Drupal.psSearchPage = Drupal.psSearchPage || {};
  Drupal.psSearchPage.loadMarkers = loadMarkers;
  Drupal.psSearchPage.refreshMapMarkers = refreshMapMarkers;
  Drupal.psSearchPage.initMapBridge = initMapBridge;

  Drupal.behaviors.psSearchPageMapMarkers = {
    attach(context) {
      once('ps-search-map-markers', '.ps-search-view', context).forEach(function (root) {
        initMapMarkers(root);
      });
    },
  };
}(Drupal, once));
