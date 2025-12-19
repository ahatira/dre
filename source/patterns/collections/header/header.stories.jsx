import header from './header.twig';
import data from './header.yml';

export default {
  title: 'Collections/Header',
  tags: ['autodocs'],
  parameters: {
    layout: 'fullscreen',
  },
  argTypes: {
    // Zone Top-Left: Logo + Tagline
    logo_config: {
      description: 'Logo component configuration',
      control: 'object',
      table: { category: 'Zone Top-Left' },
    },
    tagline: {
      description: 'Brand tagline displayed next to logo',
      control: 'text',
      table: { category: 'Zone Top-Left' },
    },

    // Zone Top-Right: Language Selector
    language_config: {
      description: 'Language selector configuration with current language and options',
      control: 'object',
      table: { category: 'Zone Top-Right' },
    },

    // Zone Bottom-Left: Navigation
    nav_items: {
      description: 'Navigation menu items array with submenu support',
      control: 'object',
      table: { category: 'Zone Bottom-Left' },
    },

    // Zone Bottom-Right: Actions
    actions: {
      description: 'Header action buttons, icons, and user menu array (button | icon | user types)',
      control: 'object',
      table: { category: 'Zone Bottom-Right' },
    },

    // Behavior
    sticky: {
      description: 'Enable sticky positioning on scroll',
      control: 'boolean',
      table: { category: 'Behavior', defaultValue: { summary: false } },
    },
  },
};

/**
 * Default header with 4 zones (logged-in state)
 * Top: Logo + Tagline | Language Selector
 * Bottom: Navigation | Actions (with user "Enzo" + badge)
 */
export const Default = {
  render: (args) => header(args),
  args: {
    ...data,
  },
};

/**
 * Sticky header that follows scroll
 */
export const Sticky = {
  render: (args) => header(args),
  args: {
    ...data,
    sticky: true,
  },
};

/**
 * Logged-out state with "Log in / Sign up" button
 */
export const LoggedOut = {
  render: (args) => header(args),
  args: {
    ...data,
    actions: [
      {
        type: 'button',
        variant: 'outline-primary',
        label: 'What are you looking for?',
        icon: 'search',
        href: '#',
        size: 'sm',
        searchTrigger: true,
      },
      {
        type: 'button',
        variant: 'primary',
        label: 'Find a property',
        href: '/properties',
        size: 'sm',
      },
      {
        type: 'button',
        variant: 'success',
        label: 'Log in / Sign up',
        icon: 'user',
        href: '/login',
        size: 'sm',
      },
      {
        type: 'button',
        variant: 'secondary',
        label: 'Contact us',
        href: '/contact',
        size: 'sm',
      },
      {
        type: 'icon',
        icon: 'heart',
        ariaLabel: 'Favorites',
        href: '/favorites',
      },
      {
        type: 'icon',
        icon: 'search',
        ariaLabel: 'Search',
        href: '/search',
      },
    ],
  },
};

/**
 * Logged-in state with user dropdown and notification badge
 */
export const LoggedIn = {
  render: (args) => header(args),
  args: {
    ...data,
  },
};

/**
 * Mobile view simulation (simplified actions)
 */
export const Mobile = {
  render: (args) => header(args),
  args: {
    ...data,
    actions: [
      {
        type: 'icon',
        icon: 'search',
        ariaLabel: 'Search',
        href: '/search',
      },
      {
        type: 'icon',
        icon: 'user',
        ariaLabel: 'Account',
        href: '/account',
      },
      {
        type: 'icon',
        icon: 'heart',
        ariaLabel: 'Favorites',
        href: '/favorites',
      },
    ],
  },
  parameters: {
    viewport: {
      defaultViewport: 'mobile1',
    },
  },
};

/**
 * French version with French language selector
 */
export const French = {
  render: (args) => header(args),
  args: {
    ...data,
    tagline: 'Immobilier pour un monde en mutation',
    language_config: {
      current: {
        code: 'FR',
        label: 'Fr',
        locale: 'fr-FR',
      },
      options: [
        { code: 'GB', label: 'En', value: 'en', locale: 'en-GB', selected: false },
        { code: 'FR', label: 'Fr', value: 'fr', locale: 'fr-FR', selected: true },
      ],
    },
    nav_items: [
      {
        label: 'Trouver un bien',
        href: '/properties',
        submenu: [
          { label: 'Acheter', href: '/properties/buy' },
          { label: 'Louer', href: '/properties/rent' },
          { label: 'Commercial', href: '/properties/commercial' },
        ],
      },
      { label: 'À propos', href: '/about' },
      { label: 'Solutions', href: '/solutions' },
      { label: 'Actualités', href: '/news' },
    ],
    actions: [
      {
        type: 'button',
        variant: 'outline-primary',
        label: 'Que recherchez-vous ?',
        icon: 'search',
        href: '#',
        size: 'sm',
        searchTrigger: true,
      },
      {
        type: 'button',
        variant: 'primary',
        label: 'Trouver un bien',
        href: '/properties',
        size: 'sm',
      },
      {
        type: 'button',
        variant: 'primary',
        label: 'Se connecter',
        icon: 'user',
        href: '/login',
        size: 'sm',
      },
      {
        type: 'button',
        variant: 'secondary',
        label: 'Nous contacter',
        href: '/contact',
        size: 'sm',
      },
      {
        type: 'icon',
        icon: 'heart',
        ariaLabel: 'Favoris',
        href: '/favorites',
      },
    ],
  },
};
