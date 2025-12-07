import selectTwig from './select.twig';
import data from './select.yml';

export default {
  title: 'Elements/Select',
  tags: ['autodocs'],
  render: (args) => selectTwig(args),
  args: data,
  parameters: {
    docs: {
      description: {
        component:
          'Native select atom (no label), Drupal-friendly attributes. Supports options list, disabled/required states, and token-based styling.',
      },
    },
  },
  argTypes: {
    // Content
    options: {
      description: 'Options array [{ value, label, disabled, selected }]',
      control: 'object',
      table: { category: 'Content' },
    },

    // Appearance
    placeholder: {
      description: 'Placeholder option label (use disabled selected item)',
      control: 'text',
      table: { category: 'Appearance' },
    },
    color: {
      description: 'Color variant',
      control: 'select',
      options: ['default', 'primary', 'secondary', 'info', 'warning', 'danger', 'success'],
      table: { category: 'Appearance' },
    },
    size: {
      description: 'Size variant',
      control: 'select',
      options: ['xs', 'sm', 'md', 'lg', 'xl', 'xxl'],
      table: { category: 'Appearance' },
    },

    // Behavior
    disabled: {
      description: 'Disable the select',
      control: 'boolean',
      table: { category: 'Behavior' },
    },
    required: {
      description: 'Mark select as required',
      control: 'boolean',
      table: { category: 'Behavior' },
    },

    // Accessibility
    name: {
      description: 'Name attribute',
      control: 'text',
      table: { category: 'Accessibility' },
    },
    id: {
      description: 'ID attribute',
      control: 'text',
      table: { category: 'Accessibility' },
    },
  },
};

export const Default = {
  args: { ...data },
};

export const Disabled = {
  args: {
    ...data,
    disabled: true,
  },
};

export const Required = {
  args: {
    ...data,
    required: true,
  },
};

export const CustomOptions = {
  args: {
    ...data,
    options: [
      { value: '', label: 'Choose...', disabled: true, selected: true },
      { value: 'fr', label: 'France' },
      { value: 'es', label: 'Spain' },
      { value: 'de', label: 'Germany' },
    ],
  },
};

export const WithExternalLabel = {
  render: (args) => {
    const id = args.id || 'select-with-label';
    const name = args.name || 'select';

    return `
      <div style="display: flex; flex-direction: column; gap: var(--size-2); max-width: 320px;">
        <label for="${id}" style="font-weight: var(--font-weight-600);">Select</label>
        ${selectTwig({ ...args, id, name })}
      </div>
    `;
  },
  args: {
    ...data,
    id: 'select-with-label',
    name: 'select',
  },
};

// Color Variants
export const Colors = {
  render: () => {
    const colors = ['default', 'primary', 'secondary', 'info', 'warning', 'danger', 'success'];
    return `
      <div style="display: flex; flex-direction: column; gap: var(--size-4); max-width: 400px;">
        ${colors
          .map(
            (color) => `
          <div style="display: flex; flex-direction: column; gap: var(--size-2);">
            <label style="font-weight: var(--font-weight-600); text-transform: capitalize;">${color}</label>
            ${selectTwig({ ...data, color })}
          </div>
        `
          )
          .join('')}
      </div>
    `;
  },
};

// Size Variants
export const Sizes = {
  render: () => {
    const sizes = ['xs', 'sm', 'md', 'lg', 'xl', 'xxl'];
    return `
      <div style="display: flex; flex-direction: column; gap: var(--size-4); max-width: 600px;">
        ${sizes
          .map(
            (size) => `
          <div style="display: flex; flex-direction: column; gap: var(--size-2);">
            <label style="font-weight: var(--font-weight-600); text-transform: uppercase;">${size}</label>
            ${selectTwig({ ...data, size })}
          </div>
        `
          )
          .join('')}
      </div>
    `;
  },
};
