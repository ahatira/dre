import iconsRegistry from '../../../documentation/icons-registry.json';
import locationTemplate from './location.twig';
import data from './location.yml';

const settings = {
  title: 'Collections/Offer/Location',
  tags: ['autodocs'],
  parameters: {
    layout: 'padded',
    docs: {
      description: {
        component:
          'Organism showing offer location with icon title, address lines, and transport list. Composes Heading atom and semantic blocks.',
      },
    },
  },
  render: (args) => locationTemplate(args),
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
      description: 'Section title',
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
    address: {
      description: 'Address object with lines and country info',
      table: {
        category: 'Content',
        type: { summary: 'object' },
      },
    },
    transport: {
      description: 'Transport info: { title, items }',
      table: {
        category: 'Content',
        type: { summary: 'object' },
      },
    },
  },
};

export const Default = {
  args: data.args || data,
};

export default settings;
