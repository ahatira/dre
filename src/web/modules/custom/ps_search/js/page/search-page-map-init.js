(function (Drupal, once, drupalSettings) {
  'use strict';

  const DEFAULT_ELEMENT_ID = 'ps-search-map';

  Drupal.psSearchMap = Drupal.psSearchMap || {};
  Drupal.psSearchMap.instances = Drupal.psSearchMap.instances || {};

  let mapsApiLoading = false;
  const mapsApiQueue = [];

  /**
   * Returns map shell config from drupalSettings.
   *
   * @return {object}
   *   Map configuration bucket.
   */
  function getMapConfig() {
    return drupalSettings.psSearch?.map || {};
  }

  /**
   * Returns a short language code for the Maps JS API.
   *
   * @return {string}
   */
  function getMapsLanguage() {
    const configLang = getMapConfig().language;
    if (typeof configLang === 'string' && configLang.length > 0) {
      return configLang.split('-')[0];
    }
    const htmlLang = document.documentElement.lang || 'en';
    return htmlLang.split('-')[0];
  }

  /**
   * Runs queued callbacks once google.maps is available.
   */
  function flushMapsApiQueue() {
    while (mapsApiQueue.length > 0) {
      const callback = mapsApiQueue.shift();
      if (typeof callback === 'function') {
        callback();
      }
    }
  }

  /**
   * Loads the Google Maps JavaScript API when not already present.
   */
  function loadGoogleMapsApi() {
    if (typeof google !== 'undefined' && google.maps) {
      flushMapsApiQueue();
      return;
    }
    if (mapsApiLoading) {
      return;
    }
    mapsApiLoading = true;

    window.__psSearchMapGoogleCallback = function () {
      mapsApiLoading = false;
      flushMapsApiQueue();
    };

    const config = getMapConfig();
    const params = new URLSearchParams({
      v: 'weekly',
      loading: 'async',
      language: getMapsLanguage(),
      callback: '__psSearchMapGoogleCallback',
    });
    if (config.apiKey) {
      params.set('key', config.apiKey);
    }

    const script = document.createElement('script');
    script.src = `https://maps.googleapis.com/maps/api/js?${params.toString()}`;
    script.async = true;
    script.defer = true;
    document.head.appendChild(script);
  }

  /**
   * Executes callback when the Maps API is ready (loads script if needed).
   *
   * @param {function} callback
   *   Function to run when google.maps is available.
   */
  Drupal.psSearchMap.googleApiReady = function (callback) {
    if (typeof google !== 'undefined' && google.maps) {
      callback();
      return;
    }
    mapsApiQueue.push(callback);
    loadGoogleMapsApi();
  };

  /**
   * Returns the PS map container element within a search view root.
   *
   * @param {HTMLElement} root
   *   Search view root.
   *
   * @return {HTMLElement|null}
   */
  Drupal.psSearchMap.getMapRoot = function (root) {
    const elementId = getMapConfig().elementId || DEFAULT_ELEMENT_ID;
    return root?.querySelector(`#${elementId}`) || null;
  };

  /**
   * Returns PS map data bucket when the shell is initialized.
   *
   * @param {HTMLElement} root
   *   Search view root.
   *
   * @return {object|null}
   */
  Drupal.psSearchMap.getPsMapData = function (root) {
    const elementId = getMapConfig().elementId || DEFAULT_ELEMENT_ID;
    const el = Drupal.psSearchMap.getMapRoot(root);
    if (!el || el.dataset.psMapShellInit !== '1') {
      return null;
    }
    return Drupal.psSearchMap.instances[elementId] || null;
  };

  /**
   * Creates the google.maps.Map instance inside the PS shell container.
   *
   * @param {HTMLElement} root
   *   Search view root.
   */
  Drupal.psSearchMap.initShell = function (root) {
    const config = getMapConfig();
    if (!config.enabled) {
      return;
    }

    const elementId = config.elementId || DEFAULT_ELEMENT_ID;
    const el = Drupal.psSearchMap.getMapRoot(root);
    if (!el || el.dataset.psMapShellInit === '1') {
      return;
    }

    Drupal.psSearchMap.googleApiReady(function () {
      if (el.dataset.psMapShellInit === '1') {
        return;
      }

      const center = config.center || { lat: 46.603354, lng: 1.888334 };
      const mapOptions = {
        center: new google.maps.LatLng(center.lat, center.lng),
        zoom: Number.isFinite(config.zoom) ? config.zoom : 6,
        minZoom: Number.isFinite(config.zoomMin) ? config.zoomMin : 1,
        maxZoom: Number.isFinite(config.zoomMax) ? config.zoomMax : 22,
        gestureHandling: config.gestureHandling || 'auto',
        mapTypeId: 'roadmap',
        zoomControl: true,
        mapTypeControl: false,
        streetViewControl: false,
        fullscreenControl: true,
        scrollwheel: true,
      };

      if (config.mapId) {
        mapOptions.mapId = config.mapId;
      }

      const bucket = {
        map: new google.maps.Map(el, mapOptions),
        markers: {},
        markersByNid: {},
        markerCluster: null,
        map_bounds: new google.maps.LatLngBounds(),
        map_markercluster: {
          markercluster_control: drupalSettings.psSearch?.markersClusterEnabled !== false,
          markercluster_additional_options: JSON.stringify(config.clusterOptions || {}),
        },
      };

      Drupal.psSearchMap.instances[elementId] = bucket;
      el.dataset.psMapShellInit = '1';
      el.classList.add('is-ps-map-shell-ready');

      root.dispatchEvent(new CustomEvent('ps-search-map-shell-ready', {
        detail: {
          mapData: bucket,
          elementId: elementId,
        },
      }));
    });
  };

  Drupal.behaviors.psSearchPageMapInit = {
    attach(context) {
      once('ps-search-map-init', '.ps-search-view', context).forEach(function (root) {
        if (!drupalSettings.psSearch?.map?.enabled) {
          return;
        }
        Drupal.psSearchMap.initShell(root);
      });
    },
  };
}(Drupal, once, drupalSettings));
