/**
 * Map Widget - Provider-agnostic integration (Leaflet | Google Maps)
 *
 * - Works in Drupal: uses global providers loaded via libraries.
 * - Works in Storybook: lazy-imports Leaflet; Google is provided by preview decorator; CSS injected via preview CDN.
 * - No top-level dependency on global L/Google.
 */
import {
  getGoogleIconOptions,
  getLeafletIconOptions,
  readMapDataset,
} from '../../base/js/map-utils.js';

let leafletLoader;
let leafletIconFixApplied = false;

const applyLeafletIconFix = (L) => {
  if (leafletIconFixApplied || !L || !L.Icon || !L.Icon.Default) {
    return;
  }

  delete L.Icon.Default.prototype._getIconUrl;
  L.Icon.Default.mergeOptions({
    iconRetinaUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon-2x.png',
    iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
    shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
  });

  leafletIconFixApplied = true;
};

const loadLeaflet = () => {
  if (leafletLoader) {
    return leafletLoader;
  }

  leafletLoader = new Promise((resolve, reject) => {
    const hasGlobalLeaflet = typeof window !== 'undefined' && window.L;

    if (hasGlobalLeaflet) {
      applyLeafletIconFix(window.L);
      resolve(window.L);
      return;
    }

    Promise.all([import('leaflet'), import('leaflet.fullscreen')])
      .then(([leafletModule]) => {
        const L = leafletModule.default || leafletModule;
        applyLeafletIconFix(L);
        resolve(L);
      })
      .catch(reject);
  });

  return leafletLoader;
};

((Drupal, once) => {
  Drupal.behaviors.psMapWidget = {
    /**
     * Attach map behavior
     * @param {Document|HTMLElement} context - DOM context
     */
    attach(context) {
      once('psMapWidget', '.ps-map-widget__canvas', context).forEach((canvas) => {
        this.initializeMap(canvas);
      });
    },

    /**
     * Initialize Leaflet map
     * @private
     */
    initializeMap(canvas) {
      // Read data via shared utils
      const { lat, lng, zoom, provider, markers } = readMapDataset(canvas);

      // Initialize based on provider
      if (provider === 'google') {
        this.initializeGoogleMap(canvas, lat, lng, zoom, markers);
      } else {
        loadLeaflet()
          .then((L) => this.initializeLeafletMap(L, canvas, lat, lng, zoom, markers))
          .catch(() => {
            canvas.innerHTML = `
              <div class="ps-map-widget__fallback">
                <p><strong>Carte non disponible</strong></p>
                <p>Le chargement du module Leaflet a échoué.</p>
              </div>
            `;
          });
      }
    },

    /**
     * Initialize Leaflet map
     * @private
     */
    initializeLeafletMap(L, canvas, lat, lng, zoom, markers) {
      // Create map instance
      const map = L.map(canvas, {
        center: [lat, lng],
        zoom: zoom,
        zoomControl: true,
        fullscreenControl: true,
        fullscreenControlOptions: {
          position: 'topright',
        },
      });

      // Setup base layers
      this.setupBaseLayers(L, map);

      // Add markers
      if (markers.length > 0) {
        this.addMarkers(L, map, markers);
      }

      // Store map instance on element for external access
      canvas.leafletMap = map;

      // Dispatch custom event
      canvas.dispatchEvent(
        new CustomEvent('map:initialized', {
          detail: { map, markers, provider: 'leaflet' },
          bubbles: true,
        })
      );
    },

    /**
     * Initialize Google Maps (requires API key in production)
     * @private
     */
    initializeGoogleMap(canvas, lat, lng, zoom, markers) {
      // Check if Google Maps API is loaded
      if (typeof google === 'undefined' || !google.maps) {
        canvas.innerHTML = `
          <div class="ps-map-widget__fallback">
            <p><strong>Google Maps non disponible</strong></p>
            <p>Incluez l'API Google Maps dans votre page :</p>
            <code>&lt;script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY"&gt;&lt;/script&gt;</code>
          </div>
        `;
        return;
      }

      const map = new google.maps.Map(canvas, {
        center: { lat, lng },
        zoom: zoom,
        mapTypeControl: true,
        mapTypeControlOptions: {
          style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR,
          position: google.maps.ControlPosition.TOP_RIGHT,
        },
        fullscreenControl: true,
        zoomControl: true,
      });

      const googleMarkers = [];

      // Add markers
      const bounds = new google.maps.LatLngBounds();
      const icon = getGoogleIconOptions(google);
      markers.forEach((marker) => {
        const markerLat = parseFloat(marker.lat);
        const markerLng = parseFloat(marker.lng);
        const label = marker.label || 'Point';

        if (!Number.isNaN(markerLat) && !Number.isNaN(markerLng)) {
          const position = { lat: markerLat, lng: markerLng };
          const gMarker = new google.maps.Marker({
            position: position,
            map: map,
            title: label,
            icon,
          });

          googleMarkers.push(gMarker);

          // Info window
          const infoWindow = new google.maps.InfoWindow({
            content: `
              <div class="ps-map-widget__popup" role="dialog" aria-label="${label}">
                <strong>${label}</strong>
                ${marker.description ? `<p>${marker.description}</p>` : ''}
                ${marker.distance ? `<p class="ps-map-widget__popup-distance">${marker.distance}</p>` : ''}
              </div>
            `,
          });

          gMarker.addListener('click', () => {
            infoWindow.open(map, gMarker);
          });

          bounds.extend(position);
        }
      });

      // Fit bounds if multiple markers
      if (markers.length > 1) {
        map.fitBounds(bounds);
      }

      // Store map instance
      canvas.googleMap = map;
      canvas.googleMarkers = googleMarkers;

      // Dispatch event
      canvas.dispatchEvent(
        new CustomEvent('map:initialized', {
          detail: { map, markers, provider: 'google' },
          bubbles: true,
        })
      );
    },

    /**
     * Setup base layers (OSM + Satellite) with control
     * @private
     */
    setupBaseLayers(L, map) {
      const osmLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution:
          '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19,
      });

      const satelliteLayer = L.tileLayer(
        'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',
        {
          attribution:
            '&copy; <a href="https://www.esri.com/">Esri</a>, DigitalGlobe, GeoEye, Earthstar Geographics',
          maxZoom: 18,
        }
      );

      // Add default layer (OSM)
      osmLayer.addTo(map);

      // Layer control
      const baseLayers = {
        Map: osmLayer,
        Satellite: satelliteLayer,
      };

      L.control
        .layers(baseLayers, null, {
          position: 'topright',
        })
        .addTo(map);
    },

    /**
     * Add markers to map and fit bounds
     * @private
     */
    addMarkers(L, map, markers) {
      markers.forEach((marker) => {
        const markerLat = parseFloat(marker.lat);
        const markerLng = parseFloat(marker.lng);
        const label = marker.label || 'Point';
        const iconOptions = getLeafletIconOptions();

        if (!Number.isNaN(markerLat) && !Number.isNaN(markerLng)) {
          const leafletMarker = L.marker([markerLat, markerLng], {
            icon: L.icon(iconOptions),
            title: label,
            alt: label,
          }).addTo(map);

          // Popup with accessible content
          leafletMarker.bindPopup(`
            <div class="ps-map-widget__popup" role="dialog" aria-label="${label}">
              <strong>${label}</strong>
              ${marker.description ? `<p>${marker.description}</p>` : ''}
              ${marker.distance ? `<p class="ps-map-widget__popup-distance">${marker.distance}</p>` : ''}
            </div>
          `);
        }
      });

      // Fit bounds to show all markers if multiple
      if (markers.length > 1) {
        const bounds = markers
          .filter((m) => !Number.isNaN(parseFloat(m.lat)) && !Number.isNaN(parseFloat(m.lng)))
          .map((m) => [parseFloat(m.lat), parseFloat(m.lng)]);

        if (bounds.length > 1) {
          map.fitBounds(bounds, { padding: [50, 50] });
        }
      }
    },

    detach(context, _settings, trigger) {
      if (trigger !== 'unload') {
        return;
      }

      once.remove('psMapWidget', '.ps-map-widget__canvas', context).forEach((canvas) => {
        if (canvas.leafletMap && typeof canvas.leafletMap.remove === 'function') {
          canvas.leafletMap.remove();
        }

        if (canvas.googleMarkers && Array.isArray(canvas.googleMarkers)) {
          canvas.googleMarkers.forEach((marker) => {
            if (marker && typeof marker.setMap === 'function') {
              marker.setMap(null);
            }
          });
        }

        if (
          canvas.googleMap &&
          typeof google !== 'undefined' &&
          google.maps &&
          google.maps.event &&
          typeof google.maps.event.clearInstanceListeners === 'function'
        ) {
          google.maps.event.clearInstanceListeners(canvas.googleMap);
        }

        delete canvas.leafletMap;
        delete canvas.googleMap;
        delete canvas.googleMarkers;

        canvas.innerHTML = '';
      });
    },

    // Icon options now provided by shared utils
  };
})(Drupal, once);
