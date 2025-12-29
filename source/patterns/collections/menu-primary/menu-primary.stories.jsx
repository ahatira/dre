import menuPrimaryTwig from './menu-primary.twig';
import data from './menu-primary.yml';

function renderMenuPrimary(args) {
  return menuPrimaryTwig(args);
}

const settings = {
  title: 'Collections/Menu Primary',
  tags: ['autodocs'],
  render: renderMenuPrimary,
  args: data.args || data,
  parameters: {
    docs: {
      description: {
        component:
          'Primary navigation menu with multi-level support. Desktop: horizontal layout with hover dropdowns. Mobile: vertical accordion. Chevrons managed via CSS pseudo-elements.',
      },
    },
  },
  argTypes: {
    menu_name: {
      description: 'Machine name of the menu (Drupal).',
      control: 'text',
      table: {
        category: 'Content',
        type: { summary: 'string' },
        defaultValue: { summary: 'primary' },
      },
    },
    items: {
      description:
        'Nested array of menu items. Each item: { title, url, below?, in_active_trail? }. Supports up to 3 levels.',
      table: {
        category: 'Content',
        type: {
          summary:
            'Array<{ title: string, url: string, below?: Array, in_active_trail?: boolean }>',
        },
      },
    },
  },
};

export default settings;

// Default story - All menu items from Figma
export const Default = {
  render: renderMenuPrimary,
  args: data.args || data,
};

// Desktop viewport
export const Desktop = {
  render: renderMenuPrimary,
  args: data.args || data,
  parameters: {
    viewport: {
      defaultViewport: 'desktop',
    },
    docs: {
      description: {
        story:
          'Desktop view: horizontal layout (64px height), hover dropdowns, green underline (2px) on active root items.',
      },
    },
  },
};

// Mobile viewport
export const Mobile = {
  render: renderMenuPrimary,
  args: {
    menu_name: 'primary',
    items: [
      {
        title: 'Find a property',
        url: '/find-property',
        below: [
          { title: 'Buy', url: '/find-property/buy' },
          { title: 'Rent', url: '/find-property/rent' },
          { title: 'Invest', url: '/find-property/invest' },
        ],
      },
      {
        title: 'About us',
        url: '/about-us',
        in_active_trail: true,
      },
      {
        title: 'Solutions',
        url: '/solutions',
      },
      {
        title: 'Latest News',
        url: '/news',
      },
    ],
  },
  parameters: {
    viewport: {
      defaultViewport: 'mobile1',
    },
    docs: {
      description: {
        story:
          'Mobile view: vertical accordion, chevron rotates 180deg when open, indented submenus.',
      },
    },
  },
};

// Three-level nesting
export const ThreeLevels = {
  render: renderMenuPrimary,
  args: data.args || data,
  parameters: {
    docs: {
      description: {
        story:
          'Demonstrates three levels of nesting. Desktop: nested dropdowns. Mobile: progressive indentation (24px, 36px, 48px).',
      },
    },
  },
};

// Flat menu (no submenus)
export const FlatMenu = {
  render: renderMenuPrimary,
  args: {
    menu_name: 'primary',
    items: [
      { title: 'Home', url: '/' },
      { title: 'About', url: '/about', in_active_trail: true },
      { title: 'Services', url: '/services' },
      { title: 'Contact', url: '/contact' },
    ],
  },
  parameters: {
    docs: {
      description: {
        story: 'Flat menu without submenus - no chevrons displayed.',
      },
    },
  },
};
