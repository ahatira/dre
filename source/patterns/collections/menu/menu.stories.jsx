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
          'Multi-level navigation based on Drupal core menu template. Renders nested lists via a recursive macro. Accessible, responsive, and token-driven CSS.',
      },
    },
  },
  argTypes: {
    menu_name: {
      description: 'Machine name of the menu (Drupal). Used for theme hook identification.',
      table: {
        category: 'Content',
        type: { summary: 'string' },
      },
    },
    items: {
      description:
        'Nested array of menu items. Each item: { title, url, attributes?, below?, is_expanded?, is_collapsed?, in_active_trail? }',
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

export const Default = {
  render: renderMenu,
  args: data.args || data,
};
