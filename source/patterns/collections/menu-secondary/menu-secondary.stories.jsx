import menuSecondaryTwig from './menu-secondary.twig';
import data from './menu-secondary.yml';

export default {
  title: 'Collections/Menu Secondary',
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component:
          'Horizontal secondary navigation using standard Drupal menu markup (div + ul.menu + li + a/button). Reuses Button and Link styles via classes in YAML.',
      },
    },
    layout: 'fullscreen',
  },
  argTypes: {
    items: {
      description: 'Menu items array (title, url?, link_class[], icon?, below[])',
      control: { type: 'object' },
      table: { category: 'Content' },
    },
    modifier: {
      description: 'Optional modifier class (e.g., compact, dark)',
      control: { type: 'select', options: [null, 'compact', 'dark'] },
      table: { category: 'Appearance' },
    },
    attributes: {
      description: 'Additional HTML attributes (Attribute object)',
      control: false,
      table: { category: 'Attributes' },
    },
  },
};

/**
 * Disconnected state - Anonymous user
 * Shows search, navigation links, login button, and contact button
 */
export const Disconnected = {
  parameters: {
    docs: {
      description: {
        story:
          'Anonymous state: simple link + primary login button + secondary contact button. All styling via classes in items.link_class.',
      },
    },
  },
  render: (args) => menuSecondaryTwig(args),
  args: { ...data },
};

/**
 * Connected state - Authenticated user (like Enzo)
 * Shows search, navigation links, user dropdown menu, and contact button
 */
export const Connected = {
  parameters: {
    docs: {
      description: {
        story:
          'Authenticated state: user button with dropdown + contact button. Dropdown rendered via nested items.below.',
      },
    },
  },
  render: (args) => menuSecondaryTwig(args),
  args: {
    items: [
      {
        title: 'What are you looking for ?',
        url: '/search',
        link_class: ['ps-button', 'ps-button--primary', 'ps-button--outline'],
      },
      { title: 'Find a property', url: '/search', link_class: ['menu-link'] },
      {
        title: 'Enzo',
        link_class: ['menu-link', 'menu-dropdown'],
        icon: 'account',
        below: [
          { title: 'My Account', url: '/account', link_class: ['menu-link'] },
          { title: 'My Favorites', url: '/account/favorites', link_class: ['menu-link'] },
          { title: 'My Alerts', url: '/account/alerts', link_class: ['menu-link'] },
          { title: 'separator' },
          { title: 'Logout', url: '/logout', link_class: ['menu-link'] },
        ],
      },
      {
        title: 'Contact us',
        url: '/contact',
        link_class: ['ps-button', 'ps-button--secondary'],
        icon: 'email-outline',
      },
    ],
    modifier: null,
  },
};

/**
 * Extended navigation links
 * Shows multiple navigation items (desktop scenario)
 */
export const WithMultipleLinks = {
  parameters: {
    docs: {
      description: {
        story: 'Menu with multiple navigation links (desktop scenario).',
      },
    },
  },
  render: (args) => menuSecondaryTwig(args),
  args: {
    items: [
      {
        title: 'What are you looking for ?',
        url: '/search',
        link_class: ['ps-button', 'ps-button--primary', 'ps-button--outline'],
      },
      { title: 'Sell with us', url: '/sell', link_class: ['menu-link'] },
      { title: 'Services', url: '/services', link_class: ['menu-link'] },
      {
        title: 'Contact us',
        url: '/contact',
        link_class: ['ps-button', 'ps-button--secondary'],
        icon: 'envelope',
      },
    ],
  },
};

/**
 * Mobile responsive layout
 * Stack layout optimized for small screens
 */
export const Mobile = {
  parameters: {
    viewport: { defaultViewport: 'mobile1' },
    docs: {
      description: {
        story: 'Responsive mobile layout: items wrap based on CSS.',
      },
    },
  },
  render: (args) => menuSecondaryTwig(args),
  args: { ...data },
};
