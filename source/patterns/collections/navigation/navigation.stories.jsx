import navigation from './navigation.twig';
import data from './navigation.yml';

export default {
  title: 'Collections/Navigation',
  tags: ['autodocs'],
  parameters: {
    layout: 'fullscreen',
  },
  argTypes: {
    // Structure
    items: {
      description: 'Navigation menu items array with label, href, and optional submenu',
      control: 'object',
      table: {
        category: 'Structure',
        type: { summary: 'array' },
      },
    },

    // Layout
    variant: {
      description: 'Navigation layout orientation',
      control: 'select',
      options: ['horizontal', 'vertical'],
      table: {
        category: 'Layout',
        defaultValue: { summary: 'horizontal' },
      },
    },

    // Drupal
    attributes: {
      description: 'Drupal attributes object for additional classes/attributes',
      control: 'object',
      table: {
        category: 'Drupal',
        type: { summary: 'Attribute' },
      },
    },
  },
};

/**
 * Default mega-menu navigation (desktop horizontal layout)
 */
export const Default = {
  render: (args) => navigation(args),
  args: {
    ...data,
  },
};

/**
 * Vertical navigation layout (sidebar style)
 */
export const Vertical = {
  render: (args) => navigation(args),
  args: {
    ...data,
    variant: 'vertical',
  },
};

/**
 * Mobile view simulation (auto-responsive)
 */
export const MobileView = {
  render: (args) => navigation(args),
  args: {
    ...data,
  },
  parameters: {
    viewport: {
      defaultViewport: 'mobile1',
    },
  },
};

/**
 * Navigation with active trail
 */
export const WithActiveItem = {
  render: (args) => navigation(args),
  args: {
    items: [
      {
        label: 'Buy',
        href: '/properties/buy',
        active: true,
        submenu: [
          { label: 'Apartments', href: '/properties/buy/apartments' },
          { label: 'Houses & Villas', href: '/properties/buy/houses' },
          { label: 'Commercial Properties', href: '/properties/buy/commercial' },
        ],
      },
      {
        label: 'Rent',
        href: '/properties/rent',
        submenu: [
          { label: 'Apartments for Rent', href: '/properties/rent/apartments' },
          { label: 'Houses for Rent', href: '/properties/rent/houses' },
        ],
      },
      {
        label: 'Services',
        href: '/services',
      },
      {
        label: 'Insights',
        href: '/insights',
      },
    ],
    variant: 'horizontal',
  },
};
