/**
 * Map Widget - Leaflet Integration
 *
 * Responsive interactive map with markers, layers (Map/Satellite), zoom controls,
 * and fullscreen capability using Leaflet.js.
 *
 * Features:
 * - Reads data-lat, data-lng, data-zoom from canvas
 * - Parses data-markers JSON for points display
 * - Layer switcher: OpenStreetMap / Satellite
 * - Fullscreen control
 * - Keyboard accessible
 * - Accessible marker popups
 */

import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import 'leaflet.fullscreen';
import 'leaflet.fullscreen/dist/Control.FullScreen.css';
import {
  getGoogleIconOptions,
  getLeafletIconOptions,
  readMapDataset,
} from '../../base/js/map-utils.js';

((Drupal, once) => {
  // Fix Leaflet default icon path issue (webpack/vite bundling)
  delete L.Icon.Default.prototype._getIconUrl;
  L.Icon.Default.mergeOptions({
    iconRetinaUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon-2x.png',
    iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
    shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
  });

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
        this.initializeLeafletMap(canvas, lat, lng, zoom, markers);
      }
    },

    /**
     * Initialize Leaflet map
     * @private
     */
    initializeLeafletMap(canvas, lat, lng, zoom, markers) {
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
      this.setupBaseLayers(map);

      // Add markers
      if (markers.length > 0) {
        this.addMarkers(map, markers);
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
          <div style="padding: var(--size-6); text-align: center; background: var(--gray-100); color: var(--text-secondary);">
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

          // Info window
          const infoWindow = new google.maps.InfoWindow({
            content: `
              <div style="padding: 8px;">
                <strong>${label}</strong>
                ${marker.description ? `<p style="margin: 4px 0 0 0;">${marker.description}</p>` : ''}
                ${marker.distance ? `<p style="margin: 4px 0 0 0; font-size: 12px; color: #666;">${marker.distance}</p>` : ''}
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
    setupBaseLayers(map) {
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
    addMarkers(map, markers) {
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

    // Icon options now provided by shared utils
  };
})(Drupal, once);
