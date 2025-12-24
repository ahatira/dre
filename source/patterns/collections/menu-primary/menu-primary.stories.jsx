/**
 * Primary Navigation Menu Stories
 *
 * Concise set of stories focusing on key variants
 * and realistic Drupal-like data.
 */

import menuPrimaryTwig from './menu-primary.twig';
import menuData from './menu-primary.yml';
import './menu-primary.js';

export default {
  title: 'Collections/Menu Primary',
  tags: ['autodocs'],
  render(args) {
    return menuPrimaryTwig(args);
  },
  parameters: {
    layout: 'fullscreen',
    docs: {
      description: {
        component:
          'Responsive primary navigation menu with multi-level dropdowns on desktop and accordion behavior on mobile.',
      },
    },
  },
  argTypes: {
    items: {
      description: 'Nested menu items array (Drupal menu structure).',
      control: 'object',
      table: {
        category: 'Data',
        type: { summary: 'array' },
      },
    },
    menu_name: {
      description: 'Machine name of the menu.',
      control: 'text',
      table: {
        category: 'Data',
        type: { summary: 'string' },
        defaultValue: { summary: 'primary' },
      },
    },
    modifier: {
      description: 'BEM modifier for visual variants.',
      control: 'select',
      options: [null, 'compact'],
      table: {
        category: 'Modifiers',
        type: { summary: 'string' },
      },
    },
    attributes: {
      description: 'Additional HTML attributes for the nav element.',
      control: 'object',
      table: {
        category: 'Attributes',
        type: { summary: 'Attribute' },
      },
    },
  },
};

// Default: full multi-level navigation using YAML data
export const Default = {
  args: {
    ...menuData,
  },
  parameters: {
    docs: {
      description: {
        story: 'Standard primary navigation with all mega-menu sections populated from YAML data.',
      },
    },
  },
};

// Compact variant: reduced padding for dense layouts
export const Compact = {
  args: {
    ...menuData,
    modifier: 'compact',
  },
  parameters: {
    docs: {
      description: {
        story: 'Compact variant with reduced vertical padding for space-constrained contexts.',
      },
    },
  },
};

// (Dark and single-level stories intentionally removed to keep stories focused on main variants.)
