(function (Drupal, once) {
  'use strict';

  const pendingCallbacks = [];
  let mapsLoading = false;

  const MAP_STYLES = [
    { featureType: 'poi', stylers: [{ visibility: 'off' }] },
    { featureType: 'poi.business', stylers: [{ visibility: 'off' }] },
    { featureType: 'road', elementType: 'labels.icon', stylers: [{ visibility: 'off' }] },
    { featureType: 'transit', stylers: [{ visibility: 'off' }] },
  ];

  const POI_PLACE_TYPES = {
    transport: ['transit_station', 'bus_station', 'train_station', 'subway_station', 'light_rail_station'],
    parkings: ['parking'],
    restaurants: ['restaurant'],
    hotels: ['lodging'],
  };

  const POI_MARKER_COLORS = {
    transport: '#0072CE',
    parkings: '#6C757D',
    restaurants: '#E87722',
    hotels: '#6B2C91',
  };

  /**
   * Loads the Google Maps JavaScript API once per page.
   */
  function loadGoogleMaps(settings, callback) {
    if (window.google && window.google.maps) {
      callback();
      return;
    }

    pendingCallbacks.push(callback);
    if (mapsLoading) {
      return;
    }

    const apiKey = settings.apiKey || '';
    if (!apiKey) {
      return;
    }

    mapsLoading = true;
    window.psOfferMapGoogleCallback = function () {
      mapsLoading = false;
      pendingCallbacks.splice(0).forEach((pending) => pending());
    };

    const params = new URLSearchParams({
      key: apiKey,
      libraries: 'places',
      language: settings.language || 'en',
      callback: 'psOfferMapGoogleCallback',
      loading: 'async',
      v: 'weekly',
    });

    const script = document.createElement('script');
    script.src = `${settings.scriptUrl}?${params.toString()}`;
    script.async = true;
    script.defer = true;
    document.head.appendChild(script);
  }

  /**
   * Returns Google Places types for one POI filter value.
   */
  function poiPlaceTypes(type) {
    return POI_PLACE_TYPES[type] || ['point_of_interest'];
  }

  const POI_MARKER_WIDTH = 32;
  const POI_MARKER_HEIGHT = 46;
  const coloredPoiMarkerCache = {};

  /**
   * Returns the configured color for one POI category.
   */
  function getPoiMarkerColor(type, settings) {
    const colors = settings.map?.poiMarkerColors || POI_MARKER_COLORS;
    return colors[type] || POI_MARKER_COLORS.restaurants;
  }

  /**
   * Returns a colored circle marker fallback for one POI category.
   */
  function buildPoiCircleMarkerIcon(fillColor) {
    return {
      path: window.google.maps.SymbolPath.CIRCLE,
      fillColor,
      fillOpacity: 1,
      strokeColor: '#FFFFFF',
      strokeWeight: 2,
      scale: 7,
    };
  }

  /**
   * Returns a pin marker icon descriptor for one URL.
   */
  function buildPoiPinMarkerIcon(url) {
    return {
      url,
      scaledSize: new window.google.maps.Size(POI_MARKER_WIDTH, POI_MARKER_HEIGHT),
      anchor: new window.google.maps.Point(POI_MARKER_WIDTH / 2, POI_MARKER_HEIGHT),
    };
  }

  /**
   * Returns a colored pin or circle marker for one POI category.
   */
  async function poiMarkerIcon(type, settings) {
    const fillColor = getPoiMarkerColor(type, settings);
    const markerUrl = settings.map?.poiMarkerUrls?.[type];

    if (!markerUrl) {
      return buildPoiCircleMarkerIcon(fillColor);
    }

    const cacheKey = `${markerUrl}|${fillColor}`;
    if (coloredPoiMarkerCache[cacheKey]) {
      return coloredPoiMarkerCache[cacheKey];
    }

    try {
      const response = await fetch(markerUrl);
      if (!response.ok) {
        return buildPoiCircleMarkerIcon(fillColor);
      }

      const coloredSvg = (await response.text()).replace(/currentColor/g, fillColor);
      const blobUrl = URL.createObjectURL(new Blob([coloredSvg], { type: 'image/svg+xml' }));
      const icon = buildPoiPinMarkerIcon(blobUrl);
      coloredPoiMarkerCache[cacheKey] = icon;
      return icon;
    }
    catch (error) {
      return buildPoiCircleMarkerIcon(fillColor);
    }
  }

  /**
   * Returns the configured map options for one offer root element.
   */
  function getMapOptions(root, settings) {
    const mapSettings = settings.map || {};
    const exactLocation = root.dataset.exactLocation === '1';
    const isLargeCity = Boolean(mapSettings.isLargeCity);

    if (exactLocation) {
      return {
        exactLocation: true,
        zoom: Number(mapSettings.zoomExact) || 15,
        circleRadius: 0,
      };
    }

    return {
      exactLocation: false,
      zoom: Number(isLargeCity ? mapSettings.zoomApproxLargeCity : mapSettings.zoomApprox) || 13,
      circleRadius: Number(isLargeCity ? mapSettings.circleRadiusLargeCity : mapSettings.circleRadius) || 2500,
      circleColor: mapSettings.circleColor || '#00915A',
    };
  }

  /**
   * Renders either the exact offer marker or an approximate location circle.
   */
  function renderOfferLocation(map, center, root, settings, mapOptions) {
    const label = root.dataset.address || root.dataset.locality || '';

    if (mapOptions.exactLocation) {
      const marker = new window.google.maps.Marker({
        map,
        position: center,
        title: label,
        animation: window.google.maps.Animation.DROP,
        icon: settings.map?.markerUrl
          ? buildPoiPinMarkerIcon(settings.map.markerUrl)
          : undefined,
      });

      if (root.dataset.address) {
        const infoWindow = new window.google.maps.InfoWindow({
          content: root.dataset.address,
        });
        marker.addListener('click', () => {
          infoWindow.open({ anchor: marker, map });
        });
      }

      return { marker, circle: null };
    }

    const circle = new window.google.maps.Circle({
      map,
      center,
      radius: mapOptions.circleRadius,
      strokeColor: mapOptions.circleColor,
      strokeOpacity: 0.8,
      strokeWeight: 2,
      fillColor: mapOptions.circleColor,
      fillOpacity: 0.35,
    });

    return { marker: null, circle };
  }

  /**
   * Runs a Google Places nearby search as a promise.
   */
  function nearbySearch(placesService, request) {
    return new Promise((resolve) => {
      placesService.nearbySearch(request, (results, status) => {
        if (status !== window.google.maps.places.PlacesServiceStatus.OK || !results) {
          resolve([]);
          return;
        }
        resolve(results);
      });
    });
  }

  /**
   * Initializes POI layers when enabled in map settings.
   */
  function initPoiFilters(root, map, center, settings) {
    if (!settings.map?.poiEnabled) {
      return;
    }

    const placesService = new window.google.maps.places.PlacesService(map);
    const poiMarkers = {};
    const poiRadius = Number(settings.map.poiRadius) || 800;
    const infoWindow = new window.google.maps.InfoWindow();

    const clearPoi = (type) => {
      (poiMarkers[type] || []).forEach((marker) => marker.setMap(null));
      poiMarkers[type] = [];
    };

    const renderPoi = async (type, enabled) => {
      clearPoi(type);
      if (!enabled) {
        return;
      }

      const placeTypes = poiPlaceTypes(type);
      const merged = new Map();

      for (const placeType of placeTypes) {
        const results = await nearbySearch(placesService, {
          location: center,
          radius: poiRadius,
          type: placeType,
        });

        results.forEach((place) => {
          if (!place.place_id || !place.geometry?.location) {
            return;
          }
          if (!merged.has(place.place_id)) {
            merged.set(place.place_id, place);
          }
        });
      }

      const icon = await poiMarkerIcon(type, settings);
      poiMarkers[type] = Array.from(merged.values()).slice(0, 20).map((place) => {
        const marker = new window.google.maps.Marker({
          map,
          position: place.geometry.location,
          title: place.name || '',
          icon,
        });

        marker.addListener('click', () => {
          const address = place.vicinity ? `<br><small>${place.vicinity}</small>` : '';
          infoWindow.setContent(`<strong>${place.name || ''}</strong>${address}`);
          infoWindow.open({ anchor: marker, map });
        });

        return marker;
      });
    };

    root.querySelectorAll('[data-ps-poi-filter]').forEach((checkbox) => {
      checkbox.addEventListener('change', () => {
        renderPoi(checkbox.value, checkbox.checked);
      });
    });
  }

  /**
   * Returns polyline options for one travel mode.
   */
  function getRoutePolylineOptions(mode) {
    if (mode === 'DRIVING' || mode === 'BICYCLING') {
      return {
        strokeColor: '#000000',
        strokeWeight: 5,
        strokeOpacity: 0.8,
      };
    }

    const dottedLine = {
      path: window.google.maps.SymbolPath.CIRCLE,
      fillOpacity: 1,
      scale: 2,
    };

    return {
      strokeOpacity: 0,
      icons: [{
        icon: dottedLine,
        offset: '0',
        repeat: '10px',
      }],
    };
  }

  /**
   * Returns a translated error message for a directions status.
   */
  function getTravelErrorMessage(status, mode) {
    if (status === 'ZERO_RESULTS') {
      if (mode === 'TRANSIT') {
        return Drupal.t('No public transport route found for this journey.');
      }
      if (mode === 'WALKING') {
        return Drupal.t('No walking route found for this journey.');
      }
      if (mode === 'BICYCLING') {
        return Drupal.t('No cycling route found for this journey.');
      }
      return Drupal.t('No route found for this journey.');
    }

    if (status === 'NOT_FOUND') {
      return Drupal.t('Departure or destination address could not be found.');
    }

    return Drupal.t('Unable to calculate travel time.');
  }

  /**
   * Initializes travel time tools for exact-address offers.
   */
  function initTravelTools(root, map, center, settings) {
    const travelInput = root.querySelector('[data-ps-travel-from]');
    const travelResult = root.querySelector('[data-ps-travel-result]');
    const durationEl = root.querySelector('[data-ps-travel-duration]');
    const distanceEl = root.querySelector('[data-ps-travel-distance]');
    const routePanel = root.querySelector('[data-ps-travel-route]');
    const routeSummaryEl = root.querySelector('[data-ps-travel-route-summary]');
    const messageEl = root.querySelector('[data-ps-travel-message]');
    const errorEl = root.querySelector('[data-ps-travel-error]');
    const modesPanel = root.querySelector('[data-ps-travel-modes]');
    const clearButton = root.querySelector('[data-ps-travel-clear]');
    if (!travelInput || !travelResult || !durationEl || !distanceEl) {
      return;
    }

    const mapAssets = settings.map || {};
    const travelMarkerUrls = {
      DRIVING: mapAssets.markerDrivingUrl,
      TRANSIT: mapAssets.markerTransitUrl,
      WALKING: mapAssets.markerWalkingUrl,
      BICYCLING: mapAssets.markerBicyclingUrl,
    };
    const destinationMarkerUrl = mapAssets.markerUrl;

    const directionsService = new window.google.maps.DirectionsService();
    let directionsRenderer = null;
    let routeMarkers = [];
    let selectedMode = 'DRIVING';
    let selectedPlace = null;

    const hideFeedback = () => {
      travelResult.hidden = true;
      if (routePanel) {
        routePanel.hidden = true;
      }
      if (routeSummaryEl) {
        routeSummaryEl.textContent = '';
      }
      if (messageEl) {
        messageEl.hidden = true;
        messageEl.textContent = '';
      }
      if (errorEl) {
        errorEl.hidden = true;
        errorEl.textContent = '';
      }
    };

    const clearRoute = () => {
      if (directionsRenderer) {
        directionsRenderer.setMap(null);
        directionsRenderer = null;
      }
      routeMarkers.forEach((marker) => marker.setMap(null));
      routeMarkers = [];
      selectedPlace = null;
      hideFeedback();
      durationEl.textContent = '';
      distanceEl.textContent = '';
      if (clearButton) {
        clearButton.hidden = true;
      }
      if (modesPanel) {
        modesPanel.hidden = true;
      }
    };

    const setModeSelection = (mode) => {
      selectedMode = mode;
      root.querySelectorAll('[data-ps-travel-mode]').forEach((button) => {
        const isSelected = button.dataset.psTravelMode === mode;
        button.classList.toggle('is-selected', isSelected);
        button.setAttribute('aria-pressed', isSelected ? 'true' : 'false');
      });
    };

    const renderRouteMarkers = (route, mode) => {
      routeMarkers.forEach((marker) => marker.setMap(null));
      routeMarkers = [];

      const leg = route?.legs?.[0];
      if (!leg) {
        return;
      }

      const startMarker = new window.google.maps.Marker({
        map,
        position: leg.start_location,
        icon: travelMarkerUrls[mode] || undefined,
        title: leg.start_address || '',
      });
      const endMarker = new window.google.maps.Marker({
        map,
        position: leg.end_location,
        icon: destinationMarkerUrl
          ? buildPoiPinMarkerIcon(destinationMarkerUrl)
          : undefined,
        title: leg.end_address || '',
      });

      routeMarkers = [startMarker, endMarker];
    };

    const calculateTravel = () => {
      if (!selectedPlace?.geometry?.location) {
        hideFeedback();
        return;
      }

      hideFeedback();

      const request = {
        origin: selectedPlace.geometry.location,
        destination: center,
        travelMode: window.google.maps.TravelMode[selectedMode],
      };

      if (selectedMode === 'TRANSIT') {
        request.transitOptions = {
          departureTime: new Date(),
        };
      }

      directionsService.route(request, (routeResult, routeStatus) => {
        if (routeStatus !== 'OK' || !routeResult?.routes?.[0]) {
          if (errorEl) {
            errorEl.textContent = getTravelErrorMessage(routeStatus, selectedMode);
            errorEl.hidden = false;
          }
          return;
        }

        const route = routeResult.routes[0];
        const leg = route.legs?.[0];
        if (!leg?.duration?.text) {
          if (errorEl) {
            errorEl.textContent = getTravelErrorMessage('ZERO_RESULTS', selectedMode);
            errorEl.hidden = false;
          }
          return;
        }

        if (directionsRenderer) {
          directionsRenderer.setMap(null);
        }

        directionsRenderer = new window.google.maps.DirectionsRenderer({
          map,
          directions: routeResult,
          suppressMarkers: true,
          preserveViewport: false,
          polylineOptions: getRoutePolylineOptions(selectedMode),
        });

        renderRouteMarkers(route, selectedMode);

        durationEl.textContent = leg.duration.text;
        distanceEl.textContent = leg.distance?.text ? ` (${leg.distance.text})` : '';
        if (routePanel && routeSummaryEl && route.summary) {
          routeSummaryEl.textContent = route.summary;
          routePanel.hidden = false;
        }
        else if (routePanel) {
          routePanel.hidden = true;
        }
        travelResult.hidden = false;

        if (messageEl && selectedMode === 'TRANSIT' && leg.duration.value > 7200) {
          messageEl.textContent = Drupal.t('This route may include several connections.');
          messageEl.hidden = false;
        }
      });
    };

    const placesService = new window.google.maps.places.PlacesService(map);

    const applySelectedPlace = (place) => {
      if (!place?.geometry?.location) {
        if (errorEl) {
          errorEl.textContent = Drupal.t('Please select an address from the suggestions.');
          errorEl.hidden = false;
        }
        return;
      }

      selectedPlace = place;
      if (clearButton) {
        clearButton.hidden = false;
      }
      if (modesPanel) {
        modesPanel.hidden = false;
      }
      calculateTravel();
    };

    const autocomplete = new window.google.maps.places.Autocomplete(travelInput, {
      types: [],
      componentRestrictions: { country: 'fr' },
      fields: ['place_id', 'geometry', 'formatted_address', 'name'],
    });

    autocomplete.addListener('place_changed', () => {
      const place = autocomplete.getPlace();
      if (!place?.place_id) {
        if (errorEl) {
          errorEl.textContent = Drupal.t('Please select an address from the suggestions.');
          errorEl.hidden = false;
        }
        return;
      }

      if (place.geometry?.location) {
        applySelectedPlace(place);
        return;
      }

      placesService.getDetails({
        placeId: place.place_id,
        fields: ['geometry', 'formatted_address', 'name'],
      }, (detail, status) => {
        if (status !== window.google.maps.places.PlacesServiceStatus.OK) {
          if (errorEl) {
            errorEl.textContent = Drupal.t('Please select an address from the suggestions.');
            errorEl.hidden = false;
          }
          return;
        }

        applySelectedPlace(detail);
      });
    });

    root.querySelectorAll('[data-ps-travel-mode]').forEach((button) => {
      button.addEventListener('click', () => {
        setModeSelection(button.dataset.psTravelMode || 'DRIVING');
        if (selectedPlace?.geometry?.location) {
          calculateTravel();
        }
      });
    });

    travelInput.addEventListener('input', () => {
      if (!travelInput.value.trim()) {
        clearRoute();
      }
    });

    if (clearButton) {
      clearButton.addEventListener('click', () => {
        travelInput.value = '';
        clearRoute();
        travelInput.focus();
      });
    }
  }

  /**
   * Initializes the offer map, POI layers and travel time tools.
   */
  function initOfferMap(root, settings) {
    const lat = Number(root.dataset.lat);
    const lng = Number(root.dataset.lng);
    const mapContainer = root.querySelector('[data-ps-map-container]');
    if (!mapContainer || Number.isNaN(lat) || Number.isNaN(lng)) {
      return;
    }

    const center = { lat, lng };
    const mapOptions = getMapOptions(root, settings);
    const map = new window.google.maps.Map(mapContainer, {
      center,
      zoom: mapOptions.zoom,
      styles: MAP_STYLES,
      mapTypeControl: false,
      streetViewControl: false,
      fullscreenControl: true,
    });

    renderOfferLocation(map, center, root, settings, mapOptions);
    initPoiFilters(root, map, center, settings);
    if (root.dataset.exactLocation === '1') {
      initTravelTools(root, map, center, settings);
    }
  }

  /**
   * Loads Google Maps only when the map container enters the viewport.
   */
  function initWhenVisible(root, settings) {
    const mapContainer = root.querySelector('[data-ps-map-container]');
    if (!mapContainer) {
      return;
    }

    if (!('IntersectionObserver' in window)) {
      loadGoogleMaps(settings, () => {
        initOfferMap(root, settings);
      });
      return;
    }

    const observer = new IntersectionObserver((entries) => {
      if (!entries[0]?.isIntersecting) {
        return;
      }

      observer.disconnect();
      loadGoogleMaps(settings, () => {
        initOfferMap(root, settings);
      });
    }, { threshold: 0 });

    observer.observe(mapContainer);
  }

  Drupal.behaviors.psOfferMap = {
    attach(context) {
      once('ps-offer-map', '[data-ps-offer-map]', context).forEach((root) => {
        const settings = drupalSettings.psOfferMap || {};
        const mapContainer = root.querySelector('[data-ps-map-container]');
        if (!mapContainer) {
          return;
        }

        if (!settings.apiKey) {
          mapContainer.textContent = Drupal.t('Google Maps API key is not configured.');
          return;
        }

        initWhenVisible(root, settings);
      });
    },
  };
})(Drupal, once);
