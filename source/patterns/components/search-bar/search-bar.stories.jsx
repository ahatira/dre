import markup from './search-bar.twig';
import data from './search-bar.yml';

export default {
  title: 'Components/Search Bar',
  tags: ['autodocs'],
  render: (args) => markup(args),
  args: data,
  argTypes: {
    label: {
      control: 'text',
      table: {
        category: 'Content',
        type: { summary: 'string' },
      },
    },
    placeholder: {
      control: 'text',
      table: {
        category: 'Content',
        type: { summary: 'string' },
      },
    },
    search_text: {
      control: 'text',
      table: {
        category: 'State',
        type: { summary: 'string' },
      },
    },
    variant: {
      control: 'select',
      options: [
        'neutral',
        'primary',
        'secondary',
        'success',
        'danger',
        'warning',
        'info',
        'gold',
        'light',
        'dark',
      ],
      table: {
        category: 'Appearance',
        type: { summary: 'string' },
      },
    },
    pill: {
      control: 'boolean',
      table: {
        category: 'Appearance',
        type: { summary: 'boolean' },
      },
    },
    icon: {
      control: 'text',
      table: {
        category: 'Content',
        type: { summary: 'string' },
      },
    },
    has_suggestions: {
      control: 'boolean',
      table: {
        category: 'Configuration',
        type: { summary: 'boolean' },
      },
    },
    suggestions: {
      control: 'object',
      table: {
        category: 'Content',
        type: { summary: 'array' },
      },
    },
  },
};

export const Default = { args: data };

export const WithSuggestions = {
  args: { ...data, search_text: 'Paris', has_suggestions: true },
};

export const Pill = {
  args: { ...data, pill: true },
};
