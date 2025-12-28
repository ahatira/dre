import menuTwig from './menu.twig';
import data from './menu.yml';

function renderMenu(args) {
  return menuTwig(args);
}

const settings = {
  title: 'Collections/Menu',
  tags: ['autodocs'],
  render: renderMenu,
  args: data.args || data,
  parameters: {
    docs: {
      description: {
        component:
          'Multi-level navigation menu for primary navigation. Desktop: horizontal layout with dropdown on hover. Mobile: vertical accordion (one item open at a time). Fully accessible with ARIA attributes and keyboard navigation.',
      },
    },
  },
  argTypes: {
    menu_name: {
      description: 'Machine name of the menu (Drupal). Used for theme hook identification.',
      control: 'text',
      table: {
        category: 'Content',
        type: { summary: 'string' },
        defaultValue: { summary: 'primary' },
      },
    },
    modifier: {
      description:
        'Optional modifier class for styling variations (e.g., "primary" for main navigation).',
      control: 'text',
      table: {
        category: 'Presentation',
        type: { summary: 'string' },
      },
    },
    items: {
      description:
        'Nested array of menu items. Each item: { title, url, attributes?, below?, is_expanded?, is_collapsed?, in_active_trail? }. Supports up to 3 levels of nesting.',
      table: {
        category: 'Content',
        type: {
          summary:
            'Array<{ title: string, url: string|null, attributes?: Attribute, below?: Array, is_expanded?: boolean, is_collapsed?: boolean, in_active_trail?: boolean }>',
        },
      },
    },
  },
};

export default settings;

// Default story - Primary navigation with all items from Figma
export const Default = {
  render: renderMenu,
  args: data.args || data,
};

// Desktop view showcase
export const Desktop = {
  render: renderMenu,
  args: data.args || data,
  parameters: {
    viewport: {
      defaultViewport: 'desktop',
    },
    docs: {
      description: {
        story:
          'Desktop view: horizontal layout with 64px item height, hover states, and dropdown submenus. Active item shows green underline (2px solid primary).',
      },
    },
  },
};

// Mobile view showcase
export const Mobile = {
  render: renderMenu,
  args: {
    ...data.args,
    items: [
      {
        title: 'Find a property',
        url: '/find-property',
        is_expanded: true,
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
        is_expanded: false,
        below: [
          { title: 'Property Management', url: '/solutions/property-management' },
          { title: 'Consultancy', url: '/solutions/consultancy' },
        ],
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
          'Mobile view: vertical accordion layout. One item open at a time. Chevron rotates 180deg when expanded. Active item shows green color.',
      },
    },
  },
};

// Three-level nesting showcase
export const ThreeLevels = {
  render: renderMenu,
  args: data.args || data,
  parameters: {
    docs: {
      description: {
        story:
          'Demonstrates three levels of menu nesting. Desktop: nested dropdowns. Mobile: indented accordion items.',
      },
    },
  },
};

// No submenus (flat menu)
export const FlatMenu = {
  render: renderMenu,
  args: {
    menu_name: 'secondary',
    modifier: 'primary',
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
