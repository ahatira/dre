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
          'Textarea atom without label. Always pair with external <label> element. Supports validation states (error, success, warning) and disabled state. Styled per maquette: radius=0, single border, no shadow, focus-visible border color change.',
      },
    },
  },
  argTypes: {
    /* Content */
    value: {
      control: 'text',
      description: 'Current textarea content',
      table: {
        category: 'Content',
        type: { summary: 'string' },
        defaultValue: { summary: '""' },
      },
    },
    placeholder: {
      control: 'text',
      description: 'Placeholder text when empty',
      table: {
        category: 'Content',
        type: { summary: 'string' },
        defaultValue: { summary: 'Decrivez votre besoin...' },
      },
    },
    rows: {
      control: 'number',
      description: 'Number of visible rows (HTML rows attribute)',
      table: {
        category: 'Content',
        type: { summary: 'number' },
        defaultValue: { summary: '4' },
      },
    },

    /* Appearance - Validation State */
    state: {
      control: 'select',
      options: [null, 'error', 'success', 'warning'],
      description: 'Validation state (changes border color)',
      table: {
        category: 'Appearance',
        type: { summary: 'null | "error" | "success" | "warning"' },
        defaultValue: { summary: 'null' },
      },
    },

    /* Behavior */
    disabled: {
      control: 'boolean',
      description: 'Disable textarea (read-only, non-editable)',
      table: {
        category: 'Behavior',
        type: { summary: 'boolean' },
        defaultValue: { summary: 'false' },
      },
    },
    required: {
      control: 'boolean',
      description: 'Mark field as required',
      table: {
        category: 'Behavior',
        type: { summary: 'boolean' },
        defaultValue: { summary: 'false' },
      },
    },

    /* Accessibility */
    name: {
      control: 'text',
      description: 'Name attribute (form submission)',
      table: {
        category: 'Accessibility',
        type: { summary: 'string' },
        defaultValue: { summary: 'message' },
      },
    },
    id: {
      control: 'text',
      description: 'ID attribute (link to external label via for)',
      table: {
        category: 'Accessibility',
        type: { summary: 'string' },
      },
    },
  },
};

/**
 * Default
 * Minimal textarea with placeholder
 */
export const Default = {
  render: (args) => textareaTwig(args),
  args: { ...data },
};

// ============ SHOWCASE ============

/**
 * All States
 * Grid showcase of all validation & disabled states
 */
export const AllStates = {
  render: () => {
    const states = [
      {
        label: 'Default',
        args: { ...data, value: 'Text content' },
      },
      {
        label: 'Placeholder',
        args: { ...data, value: '', placeholder: 'Placeholder' },
      },
      {
        label: 'Focus',
        args: { ...data, value: 'Text content' },
      },
      {
        label: 'Success',
        args: { ...data, value: 'Text content', state: 'success' },
      },
      {
        label: 'Error',
        args: { ...data, value: 'Text content', state: 'error' },
      },
      {
        label: 'Warning',
        args: { ...data, value: 'Text content', state: 'warning' },
      },
      {
        label: 'Disabled (placeholder)',
        args: {
          ...data,
          value: '',
          placeholder: 'Not available',
          disabled: true,
        },
      },
      {
        label: 'Disabled (value)',
        args: { ...data, value: 'Read-only content', disabled: true },
      },
    ];

    return `
      <div style="display: flex; flex-direction: column; gap: var(--size-4); max-width: 500px;">
        ${states
          .map(
            (state) => `
          <div>
            <p style="margin-bottom: var(--size-2); font-weight: 600; font-size: 12px; color: var(--text-secondary);">
              ${state.label}
            </p>
            ${textareaTwig(state.args)}
          </div>
        `
          )
          .join('')}
      </div>
    `;
  },
};
