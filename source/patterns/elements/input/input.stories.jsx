import inputTwig from './input.twig';
import inputData from './input.yml';

export default {
  title: 'Elements/Input',
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component:
          'Base input field (ATOM). Without label, icon, or helper. For complete input, use Form-element (Molecule).',
      },
    },
  },
  argTypes: {
    value: {
      control: 'text',
      description: 'Current field value',
      table: {
        category: 'Content',
        type: { summary: 'string' },
        defaultValue: { summary: '""' },
      },
    },
    placeholder: {
      control: 'text',
      description: 'Text displayed when field is empty',
      table: {
        category: 'Content',
        type: { summary: 'string' },
        defaultValue: { summary: '""' },
      },
    },
    type: {
      control: 'select',
      options: ['text', 'email', 'password', 'number', 'search', 'tel', 'url'],
      description: 'HTML5 input type',
      table: {
        category: 'Content',
        type: { summary: 'string' },
        defaultValue: { summary: '"text"' },
      },
    },
    state: {
      control: 'select',
      options: [null, 'error', 'success', 'warning'],
      description: 'Validation state of the field',
      table: {
        category: 'State',
        type: { summary: 'null | "error" | "success" | "warning"' },
        defaultValue: { summary: 'null' },
      },
    },
    disabled: {
      control: 'boolean',
      description: 'Disable the field (read-only, non-editable)',
      table: {
        category: 'State',
        type: { summary: 'boolean' },
        defaultValue: { summary: 'false' },
      },
    },
    required: {
      control: 'boolean',
      description: 'Mark field as required',
      table: {
        category: 'State',
        type: { summary: 'boolean' },
        defaultValue: { summary: 'false' },
      },
    },
    name: {
      control: 'text',
      description: 'Name attribute (for form submission)',
      table: {
        category: 'Accessibility',
        type: { summary: 'string' },
        defaultValue: { summary: '"email"' },
      },
    },
    id: {
      control: 'text',
      description: 'ID attribute (for label association)',
      table: {
        category: 'Accessibility',
        type: { summary: 'string' },
        defaultValue: { summary: 'null' },
      },
    },
    autocomplete: {
      control: 'text',
      description: 'HTML5 autocomplete (email, password, current-password, etc.)',
      table: {
        category: 'Accessibility',
        type: { summary: 'string' },
        defaultValue: { summary: 'null' },
      },
    },
  },
};

// ============ MOCKUP STATES ============

export const Default = {
  render: (args) => inputTwig(args),
  args: { ...inputData },
};

export const Placeholder = {
  render: (args) => inputTwig(args),
  args: { ...inputData, value: '', placeholder: 'Placeholder' },
};

export const Focus = {
  render: (args) => inputTwig(args),
  args: { ...inputData, value: 'Value' },
  parameters: {
    docs: {
      description: {
        story: 'Visible focus: 2px black border (WCAG 2.2 AA)',
      },
    },
  },
};

export const Success = {
  render: (args) => inputTwig(args),
  args: { ...inputData, value: 'Value', state: 'success' },
};

export const ErrorState = {
  render: (args) => inputTwig(args),
  args: { ...inputData, value: 'Value', state: 'error' },
};

export const Warning = {
  render: (args) => inputTwig(args),
  args: { ...inputData, value: 'Value', state: 'warning' },
};

export const DisabledPlaceholder = {
  render: (args) => inputTwig(args),
  args: {
    ...inputData,
    value: '',
    placeholder: 'Placeholder',
    disabled: true,
  },
  name: 'Disabled (placeholder)',
};

export const DisabledValue = {
  render: (args) => inputTwig(args),
  args: { ...inputData, value: 'Value', disabled: true },
  name: 'Disabled (value)',
};

// ============ TYPES ============

export const TypeEmail = {
  render: (args) => inputTwig(args),
  args: {
    ...inputData,
    type: 'email',
    placeholder: 'you@example.com',
    autocomplete: 'email',
    value: '',
  },
  name: 'Type: Email',
};

export const TypePassword = {
  render: (args) => inputTwig(args),
  args: {
    ...inputData,
    type: 'password',
    placeholder: 'Password',
    autocomplete: 'current-password',
    value: '',
  },
  name: 'Type: Password',
};

export const TypeNumber = {
  render: (args) => inputTwig(args),
  args: { ...inputData, type: 'number', placeholder: 'Ex: 250000', value: '' },
  name: 'Type: Number',
};

export const TypeSearch = {
  render: (args) => inputTwig(args),
  args: { ...inputData, type: 'search', placeholder: 'Search...', value: '' },
  name: 'Type: Search',
};

// ============ SHOWCASE ============

export const AllStates = {
  render: () => {
    const states = [
      { label: 'Default', args: { ...inputData, value: 'Value' } },
      { label: 'Placeholder', args: { ...inputData, value: '', placeholder: 'Placeholder' } },
      { label: 'Focus', args: { ...inputData, value: 'Value' } },
      { label: 'Success', args: { ...inputData, value: 'Value', state: 'success' } },
      { label: 'Error', args: { ...inputData, value: 'Value', state: 'error' } },
      { label: 'Warning', args: { ...inputData, value: 'Value', state: 'warning' } },
      {
        label: 'Disabled (placeholder)',
        args: { ...inputData, value: '', placeholder: 'Placeholder', disabled: true },
      },
      { label: 'Disabled (value)', args: { ...inputData, value: 'Value', disabled: true } },
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
            ${inputTwig(state.args)}
          </div>
        `
          )
          .join('')}
      </div>
    `;
  },
  name: 'Showcase: All States',
};
