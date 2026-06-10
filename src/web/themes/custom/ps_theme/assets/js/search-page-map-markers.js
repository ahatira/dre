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
   * Clears existing markers and cluster state on the geofield map bucket.
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
   * Places a client-side marker without geofield infowindow click handlers.
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
   * Bridges geofield MarkerClusterer — does not replace Views rendering.
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
   * Rebuilds MarkerClusterer for API-injected markers (dense zones only).
   */
  function rebuildApiMarkerCluster(mapData) {
    if (typeof MarkerClusterer === 'undefined' || !mapData.map) {
      return;
    }

    const clusterSettings = mapData.map_markercluster || {};
    if (!clusterSettings.markercluster_control) {
      return;
    }

    const options = {
      imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m',
      minimumClusterSize: 2,
      maxZoom: 18,
    };

    if (clusterSettings.markercluster_additional_options) {
      try {
        Object.assign(options, JSON.parse(clusterSettings.markercluster_additional_options));
      }
      catch (e) {
        // Keep default cluster options when JSON is invalid.
      }
    }

    const split = splitClusterableMarkers(mapData);

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

    mapData.__psSearchColocatedClusterBound = false;
    mapData.markerCluster = new MarkerClusterer(mapData.map, split.clusterable, options);
    bindColocatedClusterHandler(mapData);
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
    const mapEl = root.querySelector('.geofield-google-map');
    const formatter = Drupal.geoFieldMapFormatter;
    if (!mapEl || !formatter?.map_data?.[mapEl.id]) {
      return;
    }

    const mapData = formatter.map_data[mapEl.id];
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
   * Injects API markers into the geofield map bucket (dense zones).
   */
  function applyMarkers(root, markers, options) {
    const preserveViewport = options?.preserveViewport === true;
    const mapEl = root.querySelector('.geofield-google-map');
    const formatter = Drupal.geoFieldMapFormatter;
    if (!mapEl || !formatter?.map_data?.[mapEl.id]) {
      return;
    }

    const mapData = formatter.map_data[mapEl.id];
    clearMarkers(mapData);
    ensureOms(mapData);

    markers.forEach(function (item) {
      const nid = String(item.nid || '');
      const lat = parseFloat(item.lat);
      const lng = parseFloat(item.lng);
      const label = String(item.label || '');
      if (!nid || !Number.isFinite(lat) || !Number.isFinite(lng)) {
        return;
      }

      const marker = new google.maps.Marker({
        position: new google.maps.LatLng(lat, lng),
        icon: Drupal.psSearchMap.buildPriceMarkerIcon(label, false),
        map: mapData.oms ? null : mapData.map,
      });

      marker.geojsonProperties = {
        entity_id: nid,
        ps_search_nid: nid,
        ps_search_price: label,
      };

      placeClientMarker(marker, mapData);
    });

    Drupal.psSearchMap.indexMarkersByNid(mapData);
    rebuildApiMarkerCluster(mapData);
    if (!preserveViewport) {
      fitMapToZone(root, mapData);
    }
    Drupal.psSearchMap.resizeMaps(root);
    dispatchMapMarkersLoaded(root, mapData, options, 'markers');
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

    applyMarkers(root, markers, options);
  }

  /**
   * Whether dense zones use the markers API instead of geofield Views features.
   */
  function shouldUseMarkersApi() {
    const settings = drupalSettings.psSearch || {};
    const zoneCount = Number(settings.zoneCount || 0);
    const markersMax = Number(settings.markersMax || 500);
    return settings.markersClusterEnabled !== false && zoneCount > markersMax;
  }

  /**
   * Bridges geofield Views output for list hover/click sync after map reload.
   */
  function initGeofieldMapBridge(root, options) {
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
    dispatchMapMarkersLoaded(root, mapData, options, 'geofield');
  }

  /**
   * Initializes map markers on first paint.
   */
  function initMapMarkers(root) {
    if (shouldUseMarkersApi()) {
      const params = typeof Drupal.psSearchPage?.buildMapReloadParams === 'function'
        ? Drupal.psSearchPage.buildMapReloadParams(root)
        : null;
      loadMarkers(root, params ? params.toString() : undefined);
      return;
    }

    const zoneCount = Number(drupalSettings.psSearch?.zoneCount || 0);
    const onReady = function () {
      initGeofieldMapBridge(root, { preserveViewport: false });
    };

    if (zoneCount === 0) {
      Drupal.psSearchMap.whenMapShellReady(root, onReady);
      return;
    }

    Drupal.psSearchMap.whenMapReady(root, onReady);
  }

  /**
   * Fetches zone markers and renders them on the map (dense zones only).
   */
  function loadMarkers(root, queryString, options) {
    const baseUrl = window.drupalSettings?.psSearch?.markersUrl || '/ps-search/markers';
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
        root.psSearchMarkersCache = payload;
        applyMarkersPayload(root, payload, options);
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
  Drupal.psSearchPage.initGeofieldMapBridge = initGeofieldMapBridge;

  Drupal.behaviors.psSearchPageMapMarkers = {
    attach(context) {
      once('ps-search-map-markers', '.ps-search-view', context).forEach(function (root) {
        initMapMarkers(root);
      });
    },
  };
}(Drupal, once));
