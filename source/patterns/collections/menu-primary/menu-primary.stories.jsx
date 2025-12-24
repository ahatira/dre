/**
 * Primary Navigation Menu Stories
 *
 * Multi-level responsive navigation with dropdown support.
 * CSS-first approach with hover/focus-within interactions.
 */

import menuPrimaryTwig from './menu-primary.twig';
import menuData from './menu-primary.yml';
import './menu-primary.js';

export default {
  title: 'Collections/Menu Primary',
  tags: ['autodocs'],
  render: (args) => menuPrimaryTwig(args),
  parameters: {
    layout: 'fullscreen',
    docs: {
      description: {
        component: `
## Primary Navigation Menu

Multi-level responsive navigation component for main site navigation.

### Features
- **Responsive**: Horizontal desktop layout, vertical mobile layout
- **Multi-level**: Supports unlimited nesting levels
- **CSS-first**: Hover/focus-within interactions without JavaScript
- **Accessible**: Full keyboard navigation support
- **Drupal-compatible**: Based on core menu.html.twig structure

### Behavior

**Desktop (≥768px)**:
- Horizontal menu bar
- Dropdown submenus on hover/focus
- Flyout for nested levels (level 2+)
- Active state with bottom border indicator

**Mobile (<768px)**:
- Vertical stacked layout
- Expandable accordion-style submenus
- Chevron indicators on right
- Full-width touch targets

### Usage

\`\`\`twig
{% include '@collections/menu-primary/menu-primary.twig' with {
  menu_name: 'primary',
  items: menu_items
} only %}
\`\`\`

### Accessibility
- \`aria-label\` on navigation landmark
- Keyboard navigation with Tab/Shift+Tab
- Focus indicators on all interactive elements
- Proper semantic HTML (nav, ul, li, a)
        `,
      },
    },
  },
  argTypes: {
    // Data
    items: {
      description: 'Nested menu items array (Drupal menu structure)',
      control: 'object',
      table: {
        category: 'Data',
        type: { summary: 'array' },
      },
    },
    menu_name: {
      description: 'Machine name of the menu',
      control: 'text',
      table: {
        category: 'Data',
        type: { summary: 'string' },
        defaultValue: { summary: 'primary' },
      },
    },

    // Modifiers
    modifier: {
      description: 'BEM modifier for variant styling',
      control: 'select',
      options: [null, 'compact', 'dark'],
      table: {
        category: 'Modifiers',
        type: { summary: 'string' },
      },
    },

    // Attributes
    attributes: {
      description: 'Additional HTML attributes for the nav element',
      control: 'object',
      table: {
        category: 'Attributes',
        type: { summary: 'Attribute' },
      },
    },
  },
};

/**
 * Default Story
 * Standard navigation with different mega-menu styles:
 * - Find a property: Multilevel mega-menu (nested flyouts)
 * - About us: Flat mega-menu
 * - Solutions: Flat mega-menu (with active trail)
 * - Latest News: Flat mega-menu
 */
export const Default = {
  args: {
    ...menuData,
  },
  parameters: {
    docs: {
      description: {
        story: `Demonstrates all mega-menu patterns in one navigation:
        
**Multilevel mega-menu** (Find a property):
- Nested dropdowns with flyouts
- Residential → Apartments/Houses/Villas
- Commercial → Office/Retail/Industrial

**Flat mega-menus** (About us, Solutions, Latest News):
- Single-level dropdowns
- Vertical list layout
- No nested flyouts`,
      },
    },
  },
};

/**
 * Compact Variant
 * Reduced padding for space-constrained layouts.
 */
export const Compact = {
  args: {
    ...menuData,
    modifier: 'compact',
  },
  parameters: {
    docs: {
      description: {
        story: 'Compact variant with reduced spacing for dense layouts.',
      },
    },
  },
};

/**
 * Dark Variant
 * Inverted color scheme for dark backgrounds.
 */
export const Dark = {
  args: {
    ...menuData,
    modifier: 'dark',
  },
  parameters: {
    backgrounds: {
      default: 'dark',
    },
    docs: {
      description: {
        story: 'Dark variant with inverted colors for use on dark backgrounds.',
      },
    },
  },
};

/**
 * Single Level Menu
 * Simple navigation without submenus.
 */
export const SingleLevel = {
  args: {
    menu_name: 'primary',
    items: [
      {
        title: 'Home',
        url: '/',
        in_active_trail: false,
        attributes: { class: [] },
      },
      {
        title: 'Properties',
        url: '/properties',
        in_active_trail: true,
        attributes: { class: [] },
      },
      {
        title: 'About',
        url: '/about',
        in_active_trail: false,
        attributes: { class: [] },
      },
      {
        title: 'Contact',
        url: '/contact',
        in_active_trail: false,
        attributes: { class: [] },
      },
    ],
  },
  parameters: {
    docs: {
      description: {
        story: 'Simplified menu with no dropdowns or submenus.',
      },
    },
  },
};

/**
 * Deep Nesting (3 Levels)
 * Demonstrates flyout behavior for deeply nested menus.
 */
export const DeepNesting = {
  args: {
    menu_name: 'primary',
    items: [
      {
        title: 'Properties',
        url: '/properties',
        in_active_trail: false,
        is_expanded: true,
        is_collapsed: false,
        attributes: { class: [] },
        below: [
          {
            title: 'Residential',
            url: '/properties/residential',
            in_active_trail: false,
            is_expanded: true,
            is_collapsed: false,
            attributes: { class: [] },
            below: [
              {
                title: 'Luxury apartments',
                url: '/properties/residential/luxury-apartments',
                in_active_trail: false,
                is_expanded: true,
                is_collapsed: false,
                attributes: { class: [] },
                below: [
                  {
                    title: 'Paris',
                    url: '/properties/residential/luxury-apartments/paris',
                    in_active_trail: false,
                    attributes: { class: [] },
                  },
                  {
                    title: 'London',
                    url: '/properties/residential/luxury-apartments/london',
                    in_active_trail: false,
                    attributes: { class: [] },
                  },
                ],
              },
              {
                title: 'Family homes',
                url: '/properties/residential/family-homes',
                in_active_trail: false,
                attributes: { class: [] },
              },
            ],
          },
          {
            title: 'Commercial',
            url: '/properties/commercial',
            in_active_trail: false,
            attributes: { class: [] },
          },
        ],
      },
      {
        title: 'Services',
        url: '/services',
        in_active_trail: false,
        attributes: { class: [] },
      },
    ],
  },
  parameters: {
    docs: {
      description: {
        story: 'Three levels of nesting with flyout submenus on desktop.',
      },
    },
  },
};

/**
 * Mobile Preview
 * View optimized for mobile viewport.
 */
export const MobileView = {
  args: {
    ...menuData,
  },
  parameters: {
    viewport: {
      defaultViewport: 'mobile1',
    },
    docs: {
      description: {
        story: 'Mobile viewport showing vertical stacked layout with expandable submenus.',
      },
    },
  },
};

/**
 * Desktop Preview
 * View optimized for desktop viewport with hover states.
 */
export const DesktopView = {
  args: {
    ...menuData,
  },
  parameters: {
    viewport: {
      defaultViewport: 'desktop',
    },
    docs: {
      description: {
        story:
          'Desktop viewport showing horizontal layout with dropdown submenus. Hover over items to see dropdowns.',
      },
    },
  },
};
