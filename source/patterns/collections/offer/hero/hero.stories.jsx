/**
 * Hero (Collection/Organism)
 *
 * Organism composes Carousel component with overlay favorite button.
 * Used for property showcase with media gallery and quick-access favorite toggle.
 */

import heroTemplate from './hero.twig';
import data from './hero.yml';

const settings = {
  title: 'Collections/Offer/Hero',
  tags: ['autodocs'],
  parameters: {
    layout: 'fullscreen',
    docs: {
      description: {
        component:
          'Organism composing Carousel with overlay favorite button. Used for property showcase with image gallery, 3D visit, floor plans, and quick-access favorite toggle.',
      },
    },
  },
  render: (args) => heroTemplate(args),
  args: data.args || data,
  argTypes: {
    media: {
      description: 'Carousel configuration (variant, fit, slides, toolbar)',
      table: {
        category: 'Content',
        type: { summary: 'object' },
      },
    },
    favorite: {
      description: 'Favorite button configuration (icon, toggle, active, ariaLabel)',
      table: {
        category: 'Content',
        type: { summary: 'object' },
      },
    },
    attributes: {
      description: 'Drupal attributes object for root element',
      table: {
        category: 'Layout',
        type: { summary: 'Drupal.Attribute' },
      },
    },
  },
};

export const Default = {
  args: data.args || data,
};

export const WithFavoritedState = {
  args: {
    media: data.media,
    favorite: {
      icon: 'heart',
      toggle: true,
      active: true,
      ariaLabel: 'Remove from favorites',
    },
  },
};

export const WithoutFavorite = {
  args: {
    media: data.media,
    favorite: null,
  },
};

export default settings;
