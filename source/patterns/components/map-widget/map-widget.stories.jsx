/**
 * Map Widget (Molecule)
 *
 * Generic container for interactive maps (Leaflet, Google Maps).
 * Exposes data attributes for JavaScript hydration with coordinates and markers.
 * Compose with POI Filter Group and Travel Time Calculator for full location UI.
 */

import template from './map-widget.twig';
import data from './map-widget.yml';
import './map-widget.css';
import './map-widget.js';

export default {
  title: 'Components/Map Widget',
  tags: ['autodocs'],
  parameters: {
    layout: 'padded',
    docs: {
      description: {
        component:
          'Minimal structural container for map providers (Leaflet, Google Maps). JavaScript reads data-lat, data-lng, data-zoom, data-markers to render interactive map. CSS for Leaflet/fullscreen comes from Drupal libraries in production and from the Storybook preview decorator (CDN) in docs. Provides noscript fallback for accessibility. Spec: docs/design/pages/property-detail/location.md.',
      },
    },
  },
  render: (args) => template(args),
  argTypes: {
    map: {
      control: 'object',
      description: 'Map center coordinates and zoom level: {lat, lng, zoom}.',
      table: {
        category: 'Map',
        type: { summary: 'object' },
        defaultValue: { summary: '{ lat: 48.8566, lng: 2.3522, zoom: 14 }' },
      },
    },
    markers: {
      control: 'object',
      description:
        'Array of points to display: [{label, lat, lng, category}]. Category used for filtering (transport/restaurant/hotel).',
      table: { category: 'Map', type: { summary: 'array' } },
    },
    provider: {
      control: { type: 'select' },
      options: ['leaflet', 'google'],
      description: 'Map provider to use (affects JavaScript initialization).',
      table: { category: 'Map', type: { summary: 'string' }, defaultValue: { summary: 'leaflet' } },
    },
    fallback: {
      control: 'text',
      description: 'Textual fallback displayed when JavaScript is unavailable.',
      table: { category: 'Accessibility', type: { summary: 'string' } },
    },
    ariaLabel: {
      control: 'text',
      description: 'Accessible label for the map region.',
      table: {
        category: 'Accessibility',
        type: { summary: 'string' },
        defaultValue: { summary: 'Carte interactive' },
      },
    },
  },
};

export const Default = {
  args: data,
};

export const Google = {
  name: 'Google Maps (requires API)',
  args: {
    ...data,
    provider: 'google',
  },
  parameters: {
    docs: {
      description: {
        story:
          'Google Maps demo WITHOUT API key. Displays "For development purposes only" watermark. To use in production, add Google Maps API script with your key: `<script src="https://maps.googleapis.com/maps/api/js?key=YOUR_KEY"></script>`',
      },
    },
  },
  decorators: [
    (Story) => {
      // Load Google Maps API (without key = development mode with watermark)
      if (typeof google === 'undefined') {
        const script = document.createElement('script');
        script.src = 'https://maps.googleapis.com/maps/api/js?v=3';
        script.async = true;
        document.head.appendChild(script);
      }
      return Story();
    },
  ],
};

export const NoMarkers = {
  name: 'Without markers',
  args: {
    map: { lat: 48.8566, lng: 2.3522, zoom: 12 },
    markers: [],
    provider: 'leaflet',
    fallback: 'Carte interactive indisponible.',
    ariaLabel: 'Carte de Paris',
  },
};
