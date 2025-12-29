import menuPrimaryTwig from './menu-primary.twig';
import data from './menu-primary.yml';
import './menu-primary.js';

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
