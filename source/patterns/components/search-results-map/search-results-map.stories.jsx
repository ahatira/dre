/**
 * Search Results Map (Molecule)
 *
 * Interactive map for property search results with price markers, clustering, and radius visualization.
 * Google Maps integration with custom HTML markers and optional marker clustering.
 * Emits events for result selection and map interaction (bounds, zoom).
 */

import template from './search-results-map.twig';
import data from './search-results-map.yml';
import 'leaflet/dist/leaflet.css';
import 'leaflet.markercluster/dist/MarkerCluster.css';
import 'leaflet.markercluster/dist/MarkerCluster.Default.css';
import './search-results-map.css';
import './search-results-map.js';

export default {
  title: 'Components/Search Results Map',
  tags: ['autodocs'],
  parameters: {
    layout: 'padded',
    docs: {
      description: {
        component:
          'Interactive map for displaying search results with price markers, clustering, and radius visualization. **Supports Leaflet (OpenStreetMap) and Google Maps**. Leaflet is used by default for Storybook compatibility. **Events:** `result:click` (marker clicked), `ui:toggle-list` (list visibility toggled), `bounds:changed` (map bounds/zoom changed).',
      },
    },
  },
  // Provide default args so Docs ArgsTable always renders with values
  args: data,
  render: (args) => template(args),
  argTypes: {
    map: {
      description: 'Map center coordinates and initial zoom level',
      table: {
        category: 'Map',
        defaultValue: { summary: '{ lat: 48.8723, lng: 2.3035, zoom: 13 }' },
      },
    },
    provider: {
      control: { type: 'select' },
      options: ['leaflet', 'google'],
      description: 'Map provider (Leaflet = OpenStreetMap, Google = Google Maps)',
      table: { category: 'Map', defaultValue: { summary: 'leaflet' } },
    },
    results: {
      description:
        'Array of search results with coordinates and prices. Each result: { id, lat, lng, price (number or null for NC), currency }',
      table: { category: 'Data' },
    },
    showRadius: {
      control: 'boolean',
      description: 'Display a distance circle around the map center',
      table: { category: 'Map', defaultValue: { summary: false } },
    },
    radiusMeters: {
      control: 'number',
      description: 'Radius of the distance circle in meters',
      table: { category: 'Map', defaultValue: { summary: 1200 } },
    },
    cluster: {
      control: 'boolean',
      description:
        'Enable marker clustering for dense result sets (requires @googlemaps/markerclusterer)',
      table: { category: 'Map', defaultValue: { summary: true } },
    },
    selectedId: {
      control: 'text',
      description: 'ID of the selected result (applies visual highlight to marker)',
      table: { category: 'Selection', defaultValue: { summary: 'null' } },
    },
  },
};

export const Default = {
  args: {},
};

export const WithRadius = {
  args: {
    ...data,
    showRadius: true,
  },
};

export const WithoutClustering = {
  args: {
    ...data,
    cluster: false,
  },
};

export const WithSelected = {
  args: {
    ...data,
    selectedId: 'r1',
  },
};

export const DenseResults = {
  args: {
    ...data,
    results: Array.from({ length: 80 }).map((_, i) => ({
      id: `d${i + 1}`,
      lat: 48.862 + Math.random() * 0.04,
      lng: 2.315 + Math.random() * 0.08,
      price: Math.random() > 0.2 ? Math.floor(400 + Math.random() * 1200) : null,
      currency: 'EUR',
    })),
    cluster: true,
    showRadius: true,
  },
};

// Story mirroring the provided default mockup expectations
export const MockupDefault = {
  name: 'Mockup: Default',
  args: {
    ...data,
    showRadius: true,
    radiusMeters: 1200,
    cluster: true,
    selectedId: 'r4',
  },
};
// Google Maps variant (requires API key)
export const GoogleMapsProvider = {
  name: 'Google Maps (requires API)',
  args: {
    ...data,
    provider: 'google',
    showRadius: true,
  },
  parameters: {
    docs: {
      description: {
        story:
          'Google Maps provider variant. **Requires Google Maps API with AdvancedMarkerElement support.** Load in your page:\n\n```html\n<script src="https://maps.googleapis.com/maps/api/js?key=YOUR_KEY&libraries=marker"></script>\n<script src="https://unpkg.com/@googlemaps/markerclusterer/dist/index.min.js"></script>\n```\n\nIf unavailable, automatically falls back to Leaflet.',
      },
    },
  },
  decorators: [
    (Story) => {
      // Load Google Maps API dynamically for Storybook demo
      if (!window.google) {
        const script = document.createElement('script');
        script.src =
          'https://maps.googleapis.com/maps/api/js?key=AIzaSyDIVGlKWf1A1YzN6Zqwz_VxR7m1X6pL_kY&libraries=marker';
        script.async = true;
        script.defer = true;
        document.head.appendChild(script);

        // Load MarkerClusterer too
        const clusterScript = document.createElement('script');
        clusterScript.src = 'https://unpkg.com/@googlemaps/markerclusterer/dist/index.min.js';
        clusterScript.async = true;
        document.head.appendChild(clusterScript);
      }
      return Story();
    },
  ],
};
