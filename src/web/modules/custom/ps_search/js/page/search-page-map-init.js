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
   * Positions zoom controls bottom-left above Google attribution (BNPPRE maquette).
   *
   * @param {HTMLElement} mapEl
   *   Map container element.
   */
  function positionZoomControls(mapEl) {
    const controls = mapEl.querySelectorAll(
      '.gm-bundled-control-on-bottom, .gm-bundled-control'
    );
    controls.forEach(function (control) {
      if (!control.querySelector('[title="Zoom in"], [aria-label="Zoom in"]')) {
        return;
      }
      control.style.removeProperty('top');
      control.style.setProperty('bottom', '2.125rem', 'important');
      control.style.setProperty('left', '1rem', 'important');
    });
  }

  /**
   * Schedules zoom control positioning (Google may re-apply inline top after idle).
   *
   * @param {HTMLElement} mapEl
   *   Map container element.
   */
  function scheduleZoomControlPosition(mapEl) {
    [0, 300, 1000].forEach(function (delay) {
      window.setTimeout(function () {
        positionZoomControls(mapEl);
      }, delay);
    });
  }

  /**
   * Removes non-maquette map chrome (fullscreen, rotate) and keeps zoom bottom-left.
   *
   * @param {HTMLElement} mapEl
   *   Map container element.
   * @param {google.maps.Map} map
   *   Map instance.
   */
  function sanitizeMapControls(mapEl, map) {
    const removeSelectors = [
      '.gm-fullscreen-control',
      'button[aria-label*="fullscreen" i]',
      'button[title*="fullscreen" i]',
      'button[aria-label*="plein écran" i]',
      'button[aria-label*="Rotate" i]',
      'button[title*="Rotate" i]',
    ];

    const purge = function () {
      removeSelectors.forEach(function (selector) {
        mapEl.querySelectorAll(selector).forEach(function (node) {
          node.remove();
        });
      });
    };

    purge();
    scheduleZoomControlPosition(mapEl);
    if (map && google.maps.event) {
      google.maps.event.addListenerOnce(map, 'idle', function () {
        purge();
        scheduleZoomControlPosition(mapEl);
      });
    }
  }

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
        gestureHandling: config.gestureHandling || 'cooperative',
        mapTypeId: 'roadmap',
        disableDefaultUI: true,
        zoomControl: true,
        zoomControlOptions: {
          position: google.maps.ControlPosition.LEFT_BOTTOM,
        },
        mapTypeControl: false,
        streetViewControl: false,
        fullscreenControl: false,
        rotateControl: false,
        scaleControl: false,
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

      sanitizeMapControls(el, bucket.map);

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
