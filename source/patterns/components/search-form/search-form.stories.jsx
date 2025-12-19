import searchForm from './search-form.twig';
import data from './search-form.yml';

export default {
  title: 'Components/Search Form',
  tags: ['autodocs'],
  parameters: {
    layout: 'fullscreen',
  },
  argTypes: {
    placeholder: {
      description: 'Input placeholder text',
      control: 'text',
      table: { category: 'Input' },
    },
    action: {
      description: 'Form action URL',
      control: 'text',
      table: { category: 'Form' },
    },
    method: {
      description: 'Form submission method (GET | POST)',
      control: 'text',
      table: { category: 'Form' },
    },
    input_name: {
      description: 'Input field name attribute',
      control: 'text',
      table: { category: 'Input' },
    },
    show: {
      description: 'Display form initially (toggled by search button in header)',
      control: 'boolean',
      table: { category: 'Behavior', defaultValue: { summary: false } },
    },
  },
};

/**
 * Hidden state (default - toggled by search button in header)
 */
export const Hidden = {
  render: (args) => searchForm(args),
  args: {
    ...data,
    show: false,
  },
};

/**
 * Open/visible state
 */
export const Open = {
  render: (args) => searchForm(args),
  args: {
    ...data,
    show: true,
  },
};

/**
 * With custom placeholder
 */
export const CustomPlaceholder = {
  render: (args) => searchForm(args),
  args: {
    ...data,
    show: true,
    placeholder: 'Search properties, locations, agents...',
  },
};

/**
 * Mobile view
 */
export const Mobile = {
  render: (args) => searchForm(args),
  args: {
    ...data,
    show: true,
  },
  parameters: {
    viewport: {
      defaultViewport: 'mobile1',
    },
  },
};
