/** @type { import('@storybook/html-vite').Preview } */

import { withThemeByDataAttribute } from '@storybook/addon-themes';
import { useEffect } from 'storybook/preview-api';
import Twig from 'twig';
import twigDrupal from 'twig-drupal-filters';

// Imports custom device viewports.
import customViewports from './viewports';

// Imports custom JS to allow Storybook to understand Drupal behaviors.
import './drupal/drupal';
import './drupal/once';
import './drupal/twigExtensions'; // Drupal Twig functions (link, t, create_attribute, etc.)

// Import Swiper.js for carousel component
import Swiper from 'swiper';
import { A11y, Keyboard, Navigation, Pagination } from 'swiper/modules';

// Configure Swiper modules globally
Swiper.use([Navigation, Pagination, Keyboard, A11y]);

// Make Swiper available globally for carousel behavior
window.Swiper = Swiper;

// Import Video.js for video component
import videojs from 'video.js';
import 'video.js/dist/video-js.css';
import 'videojs-youtube';

window.videojs = videojs;

// Import component behaviors (with JS)
import '../source/patterns/components/alert/alert.js';
import '../source/patterns/components/card-offer-slide/card-offer-slide.js';
import '../source/patterns/components/carousel/carousel.js';
import '../source/patterns/components/dropdown/dropdown.js';
import '../source/patterns/components/language-selector/language-selector.js';
import '../source/patterns/components/read-more/read-more.js';
import '../source/patterns/components/search-form/search-form.js';
import '../source/patterns/components/table/table.js';
import '../source/patterns/components/toast/toast.js';
import '../source/patterns/components/video/video.js';
import '../source/patterns/components/map-widget/map-widget.js';

// Import element behaviors
import '../source/patterns/elements/button/button.js';
import '../source/patterns/elements/checkbox/checkbox.js';
import '../source/patterns/elements/collapse/collapse.js';

// Import collection behaviors
import '../source/patterns/collections/accordion/accordion.js';
import '../source/patterns/collections/menu-primary/menu-primary.js';

// Import layout behaviors
import '../source/patterns/layouts/header/header.js';

// Import base pattern behaviors
import '../source/patterns/base/animations/animations.js';

// Imports the CSS for all components combined into a single stylesheet.
import '../source/patterns/styles.css';

// Imports all Storybook CSS for display.
import '../source/patterns/storybook.css';

function setupTwig(twig) {
  twig.cache();
  twigDrupal(twig);
  return twig;
}

setupTwig(Twig);

/**
 * Storybook-only helper to inject external map providers (Leaflet CDN + Google Maps API) to mimic Drupal environment.
 * - Leaflet: CSS + JS from unpkg (matches versions used in component)
 * - Google Maps: development load without API key (watermark expected); production should rely on Drupal/library.
 */
const memoizedLoad = () => {
  let leafletPromise;
  let googlePromise;

  const loadStyle = (id, href) => {
    if (document.getElementById(id)) {
      return Promise.resolve();
    }
    return new Promise((resolve, reject) => {
      const link = document.createElement('link');
      link.id = id;
      link.rel = 'stylesheet';
      link.href = href;
      link.onload = resolve;
      link.onerror = reject;
      document.head.appendChild(link);
    });
  };

  const loadScript = (id, src) => {
    if (document.getElementById(id)) {
      return Promise.resolve();
    }
    return new Promise((resolve, reject) => {
      const script = document.createElement('script');
      script.id = id;
      script.src = src;
      script.async = true;
      script.onload = resolve;
      script.onerror = reject;
      document.head.appendChild(script);
    });
  };

  const loadLeafletCdn = () => {
    if (leafletPromise) {
      return leafletPromise;
    }
    const css = loadStyle('leaflet-css-cdn', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css');
    const cssFs = loadStyle(
      'leaflet-fullscreen-css-cdn',
      'https://unpkg.com/leaflet.fullscreen@2.4.0/Control.FullScreen.css'
    );
    const js = loadScript('leaflet-js-cdn', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js');
    const jsFs = loadScript(
      'leaflet-fullscreen-js-cdn',
      'https://unpkg.com/leaflet.fullscreen@2.4.0/Control.FullScreen.min.js'
    );
    leafletPromise = Promise.all([css, cssFs, js, jsFs]).catch((error) => {
      console.warn('[storybook] Leaflet CDN load failed', error);
    });
    return leafletPromise;
  };

  const loadGoogleMapsDev = () => {
    if (typeof window !== 'undefined' && window.google && window.google.maps) {
      return Promise.resolve();
    }
    if (googlePromise) {
      return googlePromise;
    }

    googlePromise = loadScript(
      'google-maps-api-dev',
      'https://maps.googleapis.com/maps/api/js?v=3'
    ).catch((error) => {
      console.warn('[storybook] Google Maps API load failed (dev mode)', error);
    });

    return googlePromise;
  };

  return {
    loadProviders: () => Promise.all([loadLeafletCdn(), loadGoogleMapsDev()]),
  };
};

const mapProviderLoader = memoizedLoad();

export const decorators = [
  withThemeByDataAttribute({
    themes: {
      PS: 'ps',
      Other: 'other',
    },
    defaultTheme: 'PS',
    attributeName: 'data-theme',
  }),
  (storyFn, context) => {
    useEffect(() => {
      // Ensure map providers are available in Storybook (Leaflet + Google Maps dev)
      mapProviderLoader.loadProviders();

      // Re-attach Drupal behaviors after each render (including args changes)
      Drupal.attachBehaviors(document, context);
    });
    return storyFn();
  },
];

const preview = {
  parameters: {
    options: {
      storySort: {
        order: [
          'Getting started',
          ['Intro'],
          'Base',
          'Elements',
          'Components',
          'Collections',
          'Layouts',
          'Pages',
          'Theme',
          '*',
        ],
        includeName: true,
      },
    },
    viewport: { options: customViewports },
  },
  controls: {
    matchers: {
      color: /(background|color)$/i,
      date: /Date$/,
    },
  },
};

export default preview;
