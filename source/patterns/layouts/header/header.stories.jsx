import header from './header.twig';
import headerData from './header.yml';

export default {
  title: 'Layouts/Header',
  tags: ['autodocs'],
  argTypes: {
    // Configuration
    show_tagline: {
      control: 'boolean',
      description: 'Display company tagline below logo (for branding context)',
      table: {
        category: 'Configuration',
        defaultValue: { summary: 'false' },
      },
    },
    tagline: {
      control: 'text',
      description: 'Tagline text (fallback: site_slogan or "Real Estate for a Changing World")',
      table: {
        category: 'Configuration',
        defaultValue: { summary: 'Real Estate for a Changing World' },
      },
    },
    sticky: {
      control: 'boolean',
      description: 'Enable sticky header behavior on scroll (header stays at top)',
      table: {
        category: 'Configuration',
        defaultValue: { summary: 'true' },
      },
    },

    // Logo Configuration
    'logo_config.variant': {
      control: 'select',
      options: ['default', 'square'],
      description: 'Logo variant (default: horizontal logo, square: 3D square logo)',
      table: {
        category: 'Logo',
        defaultValue: { summary: 'default' },
      },
    },
    'logo_config.size': {
      control: 'select',
      options: ['small', 'medium', 'large'],
      description: 'Logo size (affects header height responsively)',
      table: {
        category: 'Logo',
        defaultValue: { summary: 'medium' },
      },
    },
    'logo_config.href': {
      control: 'text',
      description: 'Logo link URL (default: homepage)',
      table: {
        category: 'Logo',
        defaultValue: { summary: '/' },
      },
    },

    // Language Selector
    'language_selector_config.size': {
      control: 'select',
      options: ['sm', 'md', 'lg'],
      description: 'Language selector size',
      table: {
        category: 'Language Selector',
        defaultValue: { summary: 'sm' },
      },
    },

    // Menus
    primary_menu_items: {
      control: 'object',
      description: 'Primary navigation menu items array (supports mega-menus and submenus)',
      table: {
        category: 'Menus',
        type: { summary: 'array' },
      },
    },
    secondary_menu_items: {
      control: 'object',
      description: 'Secondary navigation menu items array (right-side actions)',
      table: {
        category: 'Menus',
        type: { summary: 'array' },
      },
    },

    // Drupal Regions
    'page.header_top': {
      control: 'text',
      description: 'Drupal region: Header top (language selector block)',
      table: {
        category: 'Drupal Regions',
        type: { summary: 'string (HTML)' },
      },
    },
    'page.header_navigation': {
      control: 'text',
      description: 'Drupal region: Primary navigation (menu block)',
      table: {
        category: 'Drupal Regions',
        type: { summary: 'string (HTML)' },
      },
    },
    'page.header_actions': {
      control: 'text',
      description: 'Drupal region: Actions (user menu, search, custom blocks)',
      table: {
        category: 'Drupal Regions',
        type: { summary: 'string (HTML)' },
      },
    },

    // Advanced
    modifier_class: {
      control: 'text',
      description: 'Optional modifier class for custom variants or theming',
      table: {
        category: 'Advanced',
        type: { summary: 'string' },
      },
    },
  },
};

/**
 * Default: Simple header (logged out state)
 * - Logo without tagline
 * - Language selector
 * - Primary navigation menu
 * - Secondary menu with login button
 */
export const Default = {
  render: (args) => header(args),
  args: {
    ...headerData,
    show_tagline: false,
    sticky: true,
  },
};

/**
 * With Tagline: Header with company slogan
 * - Logo with tagline below
 * - All other elements same as default
 */
export const WithTagline = {
  render: (args) => header(args),
  args: {
    ...headerData,
    show_tagline: true,
    tagline: 'Real Estate for a Changing World',
    sticky: true,
  },
};

/**
 * Logged In: Header with user menu
 * - User account dropdown replaces login button
 * - Badge notification indicator
 */
export const LoggedIn = {
  render: (args) => header(args),
  args: {
    ...headerData,
    show_tagline: false,
    sticky: true,
    secondary_menu_items: [
      {
        title: 'What are you looking for ?',
        url: '/search',
        link_class: ['ps-button', 'ps-button--primary', 'ps-button--outline'],
      },
      {
        title: 'Find a property',
        url: '/search',
        link_class: ['menu-link'],
      },
      {
        title: 'Enzo',
        url: '#',
        link_class: ['ps-button', 'ps-button--primary'],
        icon: 'account',
        aria_haspopup: true,
        below: [
          {
            title: 'My profile',
            url: '/user/profile',
            link_class: ['menu-link'],
          },
          {
            title: 'My properties',
            url: '/user/properties',
            link_class: ['menu-link'],
          },
          {
            title: 'Messages (3)',
            url: '/user/messages',
            link_class: ['menu-link'],
          },
          {
            title: 'separator',
            separator: true,
          },
          {
            title: 'Settings',
            url: '/user/settings',
            link_class: ['menu-link'],
          },
          {
            title: 'Log out',
            url: '/user/logout',
            link_class: ['menu-link'],
          },
        ],
      },
      {
        title: 'Contact us',
        url: '/contact',
        link_class: ['ps-button', 'ps-button--secondary'],
        icon: 'email-outline',
      },
    ],
  },
};

/**
 * Mobile Preview: View header in mobile size
 * - Hamburger menu toggle visible
 * - Offcanvas navigation
 */
export const MobilePreview = {
  render: (args) => header(args),
  args: {
    ...headerData,
    show_tagline: false,
    sticky: true,
  },
  parameters: {
    viewport: {
      defaultViewport: 'mobile1',
    },
  },
};

/**
 * Without Sticky: Header without sticky behavior
 * - Header scrolls normally
 */
export const WithoutSticky = {
  render: (args) => header(args),
  args: {
    ...headerData,
    show_tagline: false,
    sticky: false,
  },
};
