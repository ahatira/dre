/**
 * @file
 * Storybook stories for Navigation Menu component
 *
 * Navigation Menu: Responsive multi-level navigation for header
 * - Desktop: horizontal menu with dropdown submenus (hover or click)
 * - Mobile: vertical accordion menu
 * - Full keyboard and screen reader support
 */

import navigationMenuData from './navigation-menu.yml';

export default {
  title: 'Collections/Navigation Menu',
  tags: ['autodocs'],
  argTypes: {
    menu_name: {
      control: 'text',
      description: 'Machine name of the menu (Drupal)',
      table: {
        category: 'Configuration',
        type: { summary: 'string' },
        defaultValue: { summary: 'main' },
      },
    },
    variant: {
      control: 'select',
      options: ['default', 'dark', 'mobile'],
      description: 'Visual variant',
      table: {
        category: 'Style',
        type: { summary: 'string' },
        defaultValue: { summary: 'default' },
      },
    },
    behavior: {
      control: 'select',
      options: ['hover', 'click'],
      description: 'Desktop submenu behavior',
      table: {
        category: 'Interaction',
        type: { summary: 'string' },
        defaultValue: { summary: 'hover' },
      },
    },
    accordion: {
      control: 'boolean',
      description: 'Mobile accordion mode (only one item open at a time)',
      table: {
        category: 'Interaction',
        type: { summary: 'boolean' },
        defaultValue: { summary: false },
      },
    },
    items: {
      control: 'object',
      description: 'Nested array of menu items',
      table: {
        category: 'Data',
        type: { summary: 'array' },
      },
    },
  },
};

/**
 * Default navigation menu
 * Desktop: horizontal with hover dropdowns
 * Mobile: vertical with toggle buttons
 */
export const Default = {
  args: navigationMenuData,
};

/**
 * Click behavior
 * Desktop: requires click on toggle button to open submenus
 * Useful for touch devices or when hover is not desired
 */
export const ClickBehavior = {
  args: {
    ...navigationMenuData,
    behavior: 'click',
  },
};

/**
 * Dark variant
 * For use on dark backgrounds or in dark mode interfaces
 */
export const DarkVariant = {
  args: {
    ...navigationMenuData,
    variant: 'dark',
  },
  parameters: {
    backgrounds: { default: 'dark' },
  },
};

/**
 * Mobile variant
 * Fullscreen drawer mode with vertical layout
 */
export const MobileVariant = {
  args: {
    ...navigationMenuData,
    variant: 'mobile',
  },
  parameters: {
    viewport: {
      defaultViewport: 'mobile1',
    },
  },
};

/**
 * Accordion mode
 * Only one submenu can be open at a time (mobile)
 * Requires JavaScript for behavior
 */
export const AccordionMode = {
  args: {
    ...navigationMenuData,
    accordion: true,
  },
  parameters: {
    viewport: {
      defaultViewport: 'mobile1',
    },
  },
};

/**
 * Simple menu (no submenus)
 * Demonstrates menu without dropdown complexity
 */
export const SimpleMenu = {
  args: {
    menu_name: 'footer',
    items: [
      {
        title: 'Home',
        url: '/',
        in_active_trail: true,
      },
      {
        title: 'About',
        url: '/about',
      },
      {
        title: 'Contact',
        url: '/contact',
      },
      {
        title: 'Privacy',
        url: '/privacy',
      },
    ],
  },
};

/**
 * Accessibility showcase
 * Demonstrates keyboard navigation and ARIA attributes
 */
export const AccessibilityShowcase = {
  args: navigationMenuData,
  parameters: {
    docs: {
      description: {
        story: `
**Keyboard Navigation:**
- \`Tab\`: Navigate through menu items
- \`Enter/Space\`: Open submenu (click mode) or follow link
- \`Escape\`: Close submenu
- \`Arrow keys\`: Navigate within submenu

**Screen Reader Support:**
- Navigation landmark with \`role="navigation"\`
- \`aria-label\` for main navigation
- \`aria-expanded\` on toggle buttons
- \`aria-hidden\` on decorative icons

**Focus Management:**
- Visible focus indicators on all interactive elements
- Focus trap in mobile drawer mode
- Focus restoration when closing submenus
        `,
      },
    },
  },
};

/**
 * Real Estate Context
 * Menu for real estate website with property search focus
 */
export const RealEstateContext = {
  args: {
    menu_name: 'main',
    items: [
      {
        title: 'Rechercher un bien',
        url: '/recherche',
        in_active_trail: true,
        below: [
          {
            title: 'Location',
            url: '/recherche/location',
          },
          {
            title: 'Vente',
            url: '/recherche/vente',
          },
          {
            title: 'Programmes neufs',
            url: '/recherche/neuf',
          },
          {
            title: 'Investissement',
            url: '/recherche/investissement',
          },
        ],
      },
      {
        title: 'Nos services',
        url: '/services',
        below: [
          {
            title: 'Estimation immobilière',
            url: '/services/estimation',
          },
          {
            title: 'Gestion locative',
            url: '/services/gestion',
          },
          {
            title: "Immobilier d'entreprise",
            url: '/services/entreprise',
          },
          {
            title: 'Conseil patrimonial',
            url: '/services/conseil',
          },
        ],
      },
      {
        title: 'Actualités',
        url: '/actualites',
        below: [
          {
            title: 'Analyses de marché',
            url: '/actualites/marche',
          },
          {
            title: 'Communiqués de presse',
            url: '/actualites/presse',
          },
          {
            title: 'Blog immobilier',
            url: '/actualites/blog',
          },
        ],
      },
      {
        title: 'Qui sommes-nous',
        url: '/a-propos',
        below: [
          {
            title: 'Notre entreprise',
            url: '/a-propos/entreprise',
          },
          {
            title: 'Notre équipe',
            url: '/a-propos/equipe',
          },
          {
            title: 'Carrières',
            url: '/a-propos/carrieres',
          },
          {
            title: 'Nous contacter',
            url: '/a-propos/contact',
          },
        ],
      },
    ],
  },
};
