import inputTwig from './input.twig';
import data from './input.yml';

export default {
  title: 'Elements/Input',
  tags: ['autodocs'],
  render: (args) => inputTwig(args),
  args: data,
  parameters: {
    docs: {
      description: {
        component:
          'Base text-like input field (no label), Drupal-friendly attributes. Supports common types, disabled/required states, and token-based styling.',
      },
    },
  },
  argTypes: {
    // Content
    value: {
      description: 'Current value',
      control: 'text',
      table: { category: 'Content' },
    },
    placeholder: {
      description: 'Placeholder text',
      control: 'text',
      table: { category: 'Content' },
    },

    // Appearance
    type: {
      description: 'Input type',
      control: 'select',
      options: ['text', 'email', 'password', 'number', 'search', 'tel', 'url'],
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
      description: 'Disable the input',
      control: 'boolean',
      table: { category: 'Behavior' },
    },
    required: {
      description: 'Mark input as required',
      control: 'boolean',
      table: { category: 'Behavior' },
    },

    // Accessibility
    name: {
      description: 'Name attribute (recommended)',
      control: 'text',
      table: { category: 'Accessibility' },
    },
    id: {
      description: 'ID attribute (for external label association)',
      control: 'text',
      table: { category: 'Accessibility' },
    },
    autocomplete: {
      description: 'Autocomplete attribute',
      control: 'text',
      table: { category: 'Accessibility' },
    },
  },
};

export const Default = {
  args: { ...data },
};

export const Password = {
  args: {
    ...data,
    type: 'password',
    placeholder: 'Enter password',
  },
};

export const Disabled = {
  args: {
    ...data,
    disabled: true,
    value: 'Disabled value',
  },
};

export const Numeric = {
  args: {
    ...data,
    type: 'number',
    placeholder: '123',
  },
};

export const WithExternalLabel = {
  render: (args) => {
    const id = args.id || 'input-with-label';
    const name = args.name || 'input';

    return `
      <div style="display: flex; flex-direction: column; gap: var(--size-2); max-width: 320px;">
        <label for="${id}" style="font-weight: var(--font-weight-600);">Label</label>
        ${inputTwig({ ...args, id, name })}
      </div>
    `;
  },
  args: {
    ...data,
    id: 'input-with-label',
    name: 'input',
    placeholder: 'Enter text...',
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
            ${inputTwig({ ...data, color, placeholder: `${color} input` })}
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
            ${inputTwig({ ...data, size, placeholder: `Size ${size.toUpperCase()}` })}
          </div>
        `
          )
          .join('')}
      </div>
    `;
  },
};
