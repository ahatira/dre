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
  render: (args) => `
    <div style="padding: var(--size-6); background: var(--gray-100);">
      <button class="ps-search-trigger" style="padding: var(--size-3) var(--size-6); background: var(--primary); color: var(--white); border: none; border-radius: var(--radius-2); cursor: pointer; font-weight: var(--font-weight-600);">
        <span style="margin-right: var(--size-2);">🔍</span>
        Open Search Form
      </button>
    </div>
    ${searchForm(args)}
  `,
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
