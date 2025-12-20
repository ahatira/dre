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

export const Primary = {
  args: { ...data, variant: 'primary' },
};

export const Secondary = {
  args: { ...data, variant: 'secondary' },
};

export const Success = {
  args: { ...data, variant: 'success' },
};

export const Danger = {
  args: { ...data, variant: 'danger' },
};

export const Warning = {
  args: { ...data, variant: 'warning' },
};

export const Info = {
  args: { ...data, variant: 'info' },
};

export const Gold = {
  args: { ...data, variant: 'gold' },
};

export const Light = {
  args: { ...data, variant: 'light' },
};

export const Dark = {
  args: { ...data, variant: 'dark' },
};

export const PrimaryPill = {
  args: { ...data, variant: 'primary', pill: true },
};

export const WithSuggestionsAndVariant = {
  args: { ...data, search_text: 'Berlin', variant: 'success', has_suggestions: true },
};
