import iconsRegistry from '../../../documentation/icons-registry.json';
import surfaceTemplate from './surface.twig';
import data from './surface.yml';

const settings = {
  title: 'Collections/Offer/Surface',
  tags: ['autodocs'],
  parameters: {
    layout: 'padded',
    docs: {
      description: {
        component:
          'Organism displaying property surface data table with heading icon. Composes existing Heading and Table atoms. Real Estate context: Shows lot, floor, nature, surface area, and availability.',
      },
    },
  },
  render: (args) => surfaceTemplate(args),
  args: data.args || data,
  argTypes: {
    html_tag: {
      description: 'Root HTML tag',
      options: ['div', 'section', 'article'],
      control: { type: 'select' },
      table: {
        category: 'Layout',
        type: { summary: 'string' },
        defaultValue: { summary: "'section'" },
      },
    },
    title: {
      description: 'Table heading text',
      control: { type: 'text' },
      table: {
        category: 'Content',
        type: { summary: 'string' },
      },
    },
    icon: {
      control: 'select',
      options: iconsRegistry.names,
      description: 'Icon name for data-icon attribute (without icon- prefix)',
      table: {
        category: 'Content',
        type: { summary: 'string' },
      },
    },
    title_level: {
      description: 'HTML heading level (h1-h6)',
      options: ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'],
      control: { type: 'select' },
      table: {
        category: 'Content',
        type: { summary: 'string' },
        defaultValue: { summary: "'h3'" },
      },
    },
    headers: {
      description: 'Array of column header definitions: [{key, label, sortable, numeric, sticky}]',
      table: {
        category: 'Table',
        type: { summary: 'array' },
      },
    },
    rows: {
      description: 'Array of table rows: [{id, cells, selected, disabled}]',
      table: {
        category: 'Table',
        type: { summary: 'array' },
      },
    },
    variant: {
      description: 'Table color variant',
      options: [
        'default',
        'primary',
        'secondary',
        'success',
        'danger',
        'warning',
        'info',
        'light',
        'dark',
        'gold',
      ],
      control: { type: 'select' },
      table: {
        category: 'Table',
        type: { summary: 'string' },
        defaultValue: { summary: "'default'" },
      },
    },
    striped: {
      description: 'Enable zebra striping on rows',
      control: { type: 'boolean' },
      table: {
        category: 'Table',
        type: { summary: 'boolean' },
        defaultValue: { summary: 'false' },
      },
    },
    hover: {
      description: 'Enable hover effect on rows',
      control: { type: 'boolean' },
      table: {
        category: 'Table',
        type: { summary: 'boolean' },
        defaultValue: { summary: 'true' },
      },
    },
    bordered: {
      description: 'Display borders around cells',
      control: { type: 'boolean' },
      table: {
        category: 'Table',
        type: { summary: 'boolean' },
        defaultValue: { summary: 'false' },
      },
    },
  },
};

export const Default = {
  args: data.args || data,
};

export const WithStriping = {
  args: {
    ...(data.args || data),
    striped: true,
  },
};

export const WithBorders = {
  args: {
    ...(data.args || data),
    bordered: true,
    striped: true,
  },
};

export const PrimaryVariant = {
  args: {
    ...(data.args || data),
    variant: 'primary',
  },
};

export default settings;
