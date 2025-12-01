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

// Import component behaviors (with JS)
import '../source/patterns/components/accordion/accordion.js';
import '../source/patterns/components/alert/alert.js';
import '../source/patterns/components/carousel/carousel.js';
import '../source/patterns/components/dropdown/dropdown.js';

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

export const decorators = [
  withThemeByDataAttribute({
    themes: {
      PS: 'ps',
      Other: 'other',
    },
    defaultTheme: 'PS',
    attributeName: 'data-theme',
  }),
  (storyFn) => {
    useEffect(() => Drupal.attachBehaviors(), []); // eslint-disable-line
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
    viewport: { viewports: customViewports },
  },
  controls: {
    matchers: {
      color: /(background|color)$/i,
      date: /Date$/,
    },
  },
};

export default preview;
