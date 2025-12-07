import textareaTwig from './textarea.twig';
import data from './textarea.yml';

export default {
  title: 'Elements/Textarea',
  tags: ['autodocs'],
  render: (args) => textareaTwig(args),
  args: data,
  parameters: {
    docs: {
      description: {
        component:
          'Native textarea atom (no label), Drupal-friendly attributes. Supports rows, disabled/required states, token-based styling.',
      },
    },
  },
  argTypes: {
    // Content
    value: {
      description: 'Textarea content',
      control: 'text',
      table: { category: 'Content' },
    },
    placeholder: {
      description: 'Placeholder text',
      control: 'text',
      table: { category: 'Content' },
    },

    // Appearance
    rows: {
      description: 'Rows attribute',
      control: 'number',
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
      description: 'Disable the textarea',
      control: 'boolean',
      table: { category: 'Behavior' },
    },
    required: {
      description: 'Mark textarea as required',
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
    value: 'Disabled textarea',
  },
};

export const Required = {
  args: {
    ...data,
    required: true,
  },
};

export const WithRows = {
  args: {
    ...data,
    rows: 6,
    placeholder: '6 rows example',
  },
};

export const WithExternalLabel = {
  render: (args) => {
    const id = args.id || 'textarea-with-label';
    const name = args.name || 'textarea';

    return `
      <div style="display: flex; flex-direction: column; gap: var(--size-2); max-width: 400px;">
        <label for="${id}" style="font-weight: var(--font-weight-600);">Message</label>
        ${textareaTwig({ ...args, id, name })}
      </div>
    `;
  },
  args: {
    ...data,
    id: 'textarea-with-label',
    name: 'textarea',
    rows: 4,
    placeholder: 'Your message...',
  },
};

// Color Variants
export const Colors = {
  render: () => {
    const colors = ['default', 'primary', 'secondary', 'info', 'warning', 'danger', 'success'];
    return `
      <div style="display: flex; flex-direction: column; gap: var(--size-4); max-width: 500px;">
        ${colors
          .map(
            (color) => `
          <div style="display: flex; flex-direction: column; gap: var(--size-2);">
            <label style="font-weight: var(--font-weight-600); text-transform: capitalize;">${color}</label>
            ${textareaTwig({ ...data, color, placeholder: `${color} textarea` })}
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
      <div style="display: flex; flex-direction: column; gap: var(--size-4); max-width: 700px;">
        ${sizes
          .map(
            (size) => `
          <div style="display: flex; flex-direction: column; gap: var(--size-2);">
            <label style="font-weight: var(--font-weight-600); text-transform: uppercase;">${size}</label>
            ${textareaTwig({ ...data, size, placeholder: `Size ${size.toUpperCase()}` })}
          </div>
        `
          )
          .join('')}
      </div>
    `;
  },
};
