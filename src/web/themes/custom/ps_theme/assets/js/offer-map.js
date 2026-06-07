(function (Drupal, once) {
  'use strict';

  const pendingCallbacks = [];
  let mapsLoading = false;

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
   * Maps offer POI filter values to Google Places types.
   */
  function poiPlaceType(type) {
    return {
      transport: 'transit_station',
      parkings: 'parking',
      restaurants: 'restaurant',
      hotels: 'lodging',
    }[type] || 'point_of_interest';
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
    const map = new window.google.maps.Map(mapContainer, {
      center,
      zoom: 15,
      mapTypeControl: false,
      streetViewControl: false,
      fullscreenControl: true,
    });

    const offerMarker = new window.google.maps.Marker({
      map,
      position: center,
      title: root.dataset.address || '',
    });

    if (root.dataset.address) {
      const infoWindow = new window.google.maps.InfoWindow({
        content: root.dataset.address,
      });
      offerMarker.addListener('click', () => {
        infoWindow.open({ anchor: offerMarker, map });
      });
    }

    const placesService = new window.google.maps.places.PlacesService(map);
    const poiMarkers = {};

    const clearPoi = (type) => {
      (poiMarkers[type] || []).forEach((marker) => marker.setMap(null));
      poiMarkers[type] = [];
    };

    const renderPoi = (type, enabled) => {
      clearPoi(type);
      if (!enabled) {
        return;
      }

      placesService.nearbySearch({
        location: center,
        radius: 800,
        type: poiPlaceType(type),
      }, (results, status) => {
        if (status !== window.google.maps.places.PlacesServiceStatus.OK || !results) {
          return;
        }

        poiMarkers[type] = results.slice(0, 20).map((place) => {
          if (!place.geometry || !place.geometry.location) {
            return null;
          }
          return new window.google.maps.Marker({
            map,
            position: place.geometry.location,
            title: place.name || '',
          });
        }).filter(Boolean);
      });
    };

    root.querySelectorAll('[data-ps-poi-filter]').forEach((checkbox) => {
      checkbox.addEventListener('change', () => {
        renderPoi(checkbox.value, checkbox.checked);
      });
      if (checkbox.checked) {
        renderPoi(checkbox.value, true);
      }
    });

    const travelInput = root.querySelector('[data-ps-travel-from]');
    const travelResult = root.querySelector('[data-ps-travel-result]');
    if (travelInput && travelResult) {
      const geocoder = new window.google.maps.Geocoder();
      const directionsService = new window.google.maps.DirectionsService();

      const calculateTravel = () => {
        const from = travelInput.value.trim();
        if (!from) {
          travelResult.hidden = true;
          return;
        }

        geocoder.geocode({ address: from }, (geocodeResults, geocodeStatus) => {
          if (geocodeStatus !== 'OK' || !geocodeResults || !geocodeResults[0]) {
            travelResult.textContent = Drupal.t('Unable to calculate travel time.');
            travelResult.hidden = false;
            return;
          }

          directionsService.route({
            origin: geocodeResults[0].geometry.location,
            destination: center,
            travelMode: window.google.maps.TravelMode.DRIVING,
          }, (routeResult, routeStatus) => {
            if (routeStatus !== 'OK' || !routeResult?.routes?.[0]?.legs?.[0]?.duration?.text) {
              travelResult.textContent = Drupal.t('Unable to calculate travel time.');
              travelResult.hidden = false;
              return;
            }

            travelResult.textContent = routeResult.routes[0].legs[0].duration.text;
            travelResult.hidden = false;
          });
        });
      };

      travelInput.addEventListener('change', calculateTravel);
    }
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

        loadGoogleMaps(settings, () => {
          initOfferMap(root, settings);
        });
      });
    },
  };
})(Drupal, once);
