(function (Drupal, once) {
  'use strict';

  /**
   * Builds the markers API URL preserving current search query parameters.
   *
   * @param {string} baseUrl
   *   Markers endpoint path.
   *
   * @return {string}
   *   Fully qualified relative URL.
   */
  function buildMarkersUrl(baseUrl) {
    const url = new URL(baseUrl, window.location.origin);
    const current = new URLSearchParams(window.location.search);
    current.forEach(function (value, key) {
      url.searchParams.set(key, value);
    });
    return url.pathname + url.search;
  }

  /**
   * Clears existing markers and cluster state on the geofield map bucket.
   *
   * @param {object} mapData
   *   Geofield map data bucket.
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
   * Initializes OverlappingMarkerSpiderfier when the map shell has no features.
   *
   * Geofield only creates OMS when server features exist; API markers load later.
   *
   * @param {object} mapData
   *   Geofield map data bucket.
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
   *
   * @param {google.maps.Marker} marker
   *   Marker instance.
   * @param {object} mapData
   *   Geofield map data bucket.
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
   *
   * @param {Array<google.maps.Marker>} markers
   *   Cluster markers.
   *
   * @return {boolean}
   *   TRUE when every marker is co-located.
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
   * Returns markers attached to a MarkerClusterer cluster.
   *
   * @param {object} cluster
   *   MarkerClusterer cluster instance.
   *
   * @return {Array<google.maps.Marker>}
   *   Markers in the cluster.
   */
  function getClusterMarkers(cluster) {
    if (typeof cluster.getMarkers === 'function') {
      return cluster.getMarkers();
    }
    return Array.isArray(cluster.markers_) ? cluster.markers_ : [];
  }

  /**
   * Spiderfies co-located markers via OMS when clustering cannot split them.
   *
   * @param {object} mapData
   *   Geofield map data bucket.
   * @param {Array<google.maps.Marker>} markers
   *   Co-located cluster markers.
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
   * @param {object} mapData
   *   Geofield map data bucket.
   */
  function bindColocatedClusterHandler(mapData) {
    if (!mapData.markerCluster || mapData.__psSearchColocatedClusterBound) {
      return;
    }

    mapData.__psSearchColocatedClusterBound = true;

    google.maps.event.addListener(mapData.markerCluster, 'clusterclick', function (cluster) {
      const markers = getClusterMarkers(cluster);
      if (!markersSharePosition(markers)) {
        return;
      }

      spiderfyColocatedMarkers(mapData, markers);
    });
  }

  /**
   * Splits markers between clusterer (distinct positions) and OMS-only groups.
   *
   * @param {object} mapData
   *   Geofield map data bucket.
   *
   * @return {{clusterable: Array<google.maps.Marker>, colocated: Array<google.maps.Marker>}}
   *   Markers for MarkerClusterer vs OMS-only co-located groups.
   */
  function splitClusterableMarkers(mapData) {
    const groups = {};
    const allMarkers = Object.keys(mapData.markers || {}).map(function (key) {
      return mapData.markers[key];
    });

    allMarkers.forEach(function (marker) {
      const position = marker.getPosition?.();
      if (!position) {
        return;
      }
      const key = position.toUrlValue(7);
      if (!groups[key]) {
        groups[key] = [];
      }
      groups[key].push(marker);
    });

    const clusterable = [];
    const colocated = [];

    Object.keys(groups).forEach(function (key) {
      const group = groups[key];
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
   * Rebuilds MarkerClusterer from the current markers bucket.
   *
   * @param {string} mapId
   *   Geofield map element id.
   * @param {object} mapData
   *   Geofield map data bucket.
   */
  function rebuildMarkerCluster(mapId, mapData) {
    if (typeof MarkerClusterer === 'undefined' || !mapData.map) {
      return;
    }

    const clusterSettings = mapData.map_markercluster || {};
    if (!clusterSettings.markercluster_control) {
      return;
    }

    const options = {
      imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m',
      // Pairs at the same address are handled by OMS, not a permanent "2" cluster.
      minimumClusterSize: 3,
      maxZoom: 18,
    };

    if (clusterSettings.markercluster_additional_options) {
      try {
        const additional = JSON.parse(clusterSettings.markercluster_additional_options);
        Object.assign(options, additional);
      }
      catch (e) {
        // Keep default cluster options when JSON is invalid.
      }
    }

    const split = splitClusterableMarkers(mapData);

    // Co-located offers share one address: OMS spiderfies them on click.
    // MarkerClusterer cannot split identical coordinates, which caused a
    // permanent "2" cluster loop on zoom.
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
   *
   * @param {HTMLElement} root
   *   Search view root.
   * @param {object} mapData
   *   Geofield map data bucket.
   */
  function fitMapToZone(root, mapData) {
    const map = mapData.map;
    if (!map || typeof google === 'undefined' || !google.maps) {
      return;
    }

    const boundsConfig = window.drupalSettings?.psSearch?.mapBounds;

    if (boundsConfig && typeof boundsConfig.swLat === 'number') {
      const bounds = new google.maps.LatLngBounds(
        new google.maps.LatLng(boundsConfig.swLat, boundsConfig.swLng),
        new google.maps.LatLng(boundsConfig.neLat, boundsConfig.neLng),
      );
      map.fitBounds(bounds, 48);
      return;
    }

    const markerKeys = Object.keys(mapData.markers || {});
    if (markerKeys.length > 0) {
      const latLngBounds = new google.maps.LatLngBounds();
      markerKeys.forEach(function (key) {
        const position = mapData.markers[key].getPosition();
        if (position) {
          latLngBounds.extend(position);
        }
      });
      if (!latLngBounds.isEmpty()) {
        map.fitBounds(latLngBounds, 48);
      }
    }
  }

  /**
   * Injects API markers into the geofield map bucket.
   *
   * @param {HTMLElement} root
   *   Search view root.
   * @param {Array<object>} markers
   *   Marker payload from the markers API.
   */
  /**
   * @typedef {object} ApplyMarkersOptions
   * @property {boolean} [preserveViewport]
   *   When true, keeps the current map zoom/center (e.g. after Search this area).
   */

  /**
   * @param {ApplyMarkersOptions} [options]
   *   Marker render options.
   */
  function applyMarkers(root, markers, options) {
    const preserveViewport = options?.preserveViewport === true;
    const mapEl = root.querySelector('.geofield-google-map');
    const formatter = Drupal.geoFieldMapFormatter;
    if (!mapEl || !formatter?.map_data?.[mapEl.id]) {
      return;
    }

    const mapId = mapEl.id;
    const mapData = formatter.map_data[mapId];
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
    rebuildMarkerCluster(mapId, mapData);
    if (!preserveViewport) {
      fitMapToZone(root, mapData);
    }
    Drupal.psSearchMap.resizeMaps(root);

    root.classList.add('is-map-ready');
    root.dispatchEvent(new CustomEvent('ps-search-map-markers-loaded', {
      detail: {
        mapData: mapData,
        preserveViewport: preserveViewport,
      },
    }));
  }

  /**
   * Fetches zone markers and renders them on the map.
   *
   * @param {HTMLElement} root
   *   Search view root.
   * @param {string} [queryString]
   *   Optional query string (without leading "?").
   *
   * @return {Promise<object>}
   *   Markers API payload.
   */
  /**
   * @param {ApplyMarkersOptions} [options]
   *   Marker fetch/render options.
   */
  function loadMarkers(root, queryString, options) {
    const baseUrl = window.drupalSettings?.psSearch?.markersUrl || '/ps-search/markers';
    let url = baseUrl;
    if (typeof queryString === 'string' && queryString.length > 0) {
      url = `${baseUrl}?${queryString}`;
    }
    else {
      url = buildMarkersUrl(baseUrl);
    }

    return fetch(url, {
      credentials: 'same-origin',
      headers: {
        Accept: 'application/json',
      },
    })
      .then(function (response) {
        if (!response.ok) {
          throw new Error('markers_fetch_failed');
        }
        return response.json();
      })
      .then(function (data) {
        const payload = data && typeof data === 'object' ? data : { markers: [], zone_count: 0 };
        applyMarkers(root, Array.isArray(payload.markers) ? payload.markers : [], options);
        return payload;
      })
      .catch(function () {
        applyMarkers(root, [], options);
        return { markers: [], zone_count: 0 };
      });
  }

  Drupal.psSearchPage = Drupal.psSearchPage || {};
  Drupal.psSearchPage.loadMarkers = loadMarkers;

  Drupal.behaviors.psSearchPageMapMarkers = {
    attach(context) {
      once('ps-search-map-markers', '.ps-search-view', context).forEach(function (root) {
        Drupal.psSearchMap.whenMapShellReady(root, function () {
          loadMarkers(root);
        });
      });
    },
  };
}(Drupal, once));
