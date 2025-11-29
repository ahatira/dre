import fieldTwig from './field.twig';
import data from './field.yml';

const settings = {
  title: 'Elements/Field',
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component:
          'Base input/select/textarea field component without label or helper text. Supports multiple input types (text, number, email, search, select, textarea), error states, disabled states, and optional icons (left or right positioned). Part of the atomic design system as an element/atom.\n\n## Key Features\n\n- **Multiple Input Types**: text, number, email, search, select, textarea\n- **Visual States**: default, hover, focus, filled, error, done (success), disabled\n- **Icon Support**: left or right positioned icons using icon font classes (icon-search, icon-arrow-down, icon-check, etc.)\n- **Border Variations**: 1px default, 2px for focus/error/done states\n- **Accessibility**: Full ARIA support (aria-invalid, aria-describedby, aria-disabled, role attributes)\n- **No Box Shadow**: Clean design with border-only focus indicators\n\n## Usage Guidelines\n\n### When to Use\n- As a standalone input field\n- Inside form-field molecules (with labels and helper text)\n- For property search forms, contact forms, user account forms\n\n### Border States\n- **Default**: 1px gray border (#D6DBDE)\n- **Hover**: 1px darker gray border\n- **Focus**: 2px black border (no box-shadow)\n- **Error**: 2px red border (#EB3636) + error message below\n- **Done/Success**: 2px green border (turquoise)\n- **Disabled**: 1px gray border + light gray background\n\n### Icon Usage\nIcons use the standard icon font classes:\n```jsx\n// Search with icon-search\n<Field type="search" icon="search" iconPosition="right" />\n\n// Select with icon-arrow-down\n<Field type="select" icon="arrow-down" iconPosition="right" />\n\n// Success with icon-check\n<Field type="text" done={true} icon="check" iconPosition="left" />\n```\n\n### Accessibility\n- Error fields have `aria-invalid="true"` and `aria-describedby` linking to error message\n- Error messages use `role="alert"` for screen reader announcements\n- Disabled fields use `aria-disabled="true"`\n- Select fields use `role="combobox"` with proper ARIA attributes\n- Icons are decorative and use `aria-hidden="true"`',
      },
    },
  },
  argTypes: {
    type: {
      description: 'Type of field input',
      control: { type: 'select' },
      options: ['text', 'number', 'email', 'search', 'select', 'textarea'],
      table: {
        type: { summary: 'text | number | email | search | select | textarea' },
        defaultValue: { summary: 'text' },
      },
    },
    value: {
      description: 'Current value of the field',
      control: { type: 'text' },
      table: {
        type: { summary: 'string' },
        defaultValue: { summary: '' },
      },
    },
    placeholder: {
      description: 'Placeholder text shown when field is empty',
      control: { type: 'text' },
      table: {
        type: { summary: 'string' },
        defaultValue: { summary: '' },
      },
    },
    disabled: {
      description: 'Disabled state of the field',
      control: { type: 'boolean' },
      table: {
        type: { summary: 'boolean' },
        defaultValue: { summary: 'false' },
      },
    },
    error: {
      description: 'Error message to display below the field',
      control: { type: 'text' },
      table: {
        type: { summary: 'string' },
        defaultValue: { summary: '' },
      },
    },
    done: {
      description: 'Success/validated state of the field',
      control: { type: 'boolean' },
      table: {
        type: { summary: 'boolean' },
        defaultValue: { summary: 'false' },
      },
    },
    icon: {
      description: 'Icon to display (requires icon name from icon font)',
      control: { type: 'text' },
      table: {
        type: { summary: 'string' },
        defaultValue: { summary: '' },
      },
    },
    iconPosition: {
      description: 'Position of the icon',
      control: { type: 'select' },
      options: ['left', 'right'],
      table: {
        type: { summary: 'left | right' },
        defaultValue: { summary: 'right' },
      },
    },
  },
};

export const Default = {
  render: (args) => fieldTwig(args),
  args: { ...data },
};

export const Text = {
  render: () =>
    fieldTwig({
      type: 'text',
      placeholder: 'Enter your name...',
    }),
};

export const NumberField = {
  render: () =>
    fieldTwig({
      type: 'number',
      placeholder: 'Enter a number...',
    }),
};

export const Email = {
  render: () =>
    fieldTwig({
      type: 'email',
      placeholder: 'your.email@example.com',
    }),
};

export const Search = {
  render: () =>
    fieldTwig({
      type: 'search',
      placeholder: 'Search...',
      icon: 'search',
      iconPosition: 'right',
    }),
};

export const Select = {
  render: () =>
    fieldTwig({
      type: 'select',
      value: 'Select an option',
      icon: 'arrow-down',
      iconPosition: 'right',
    }),
};

export const Textarea = {
  render: () =>
    fieldTwig({
      type: 'textarea',
      placeholder: 'Enter your message...',
    }),
};

export const WithIconLeft = {
  render: () =>
    fieldTwig({
      type: 'search',
      placeholder: 'Search...',
      icon: 'search',
      iconPosition: 'left',
    }),
};

export const WithIconRight = {
  render: () =>
    fieldTwig({
      type: 'email',
      placeholder: 'your.email@example.com',
      icon: 'check',
      iconPosition: 'right',
    }),
};

export const Filled = {
  render: () =>
    fieldTwig({
      type: 'text',
      value: 'John Doe',
      placeholder: 'Enter your name...',
    }),
};

export const ErrorState = {
  render: () =>
    fieldTwig({
      type: 'email',
      value: 'invalid-email',
      placeholder: 'your.email@example.com',
      error: 'Please enter a valid email address',
    }),
};

export const Done = {
  render: () =>
    fieldTwig({
      type: 'text',
      value: 'Valid input',
      placeholder: 'Enter text...',
      done: true,
      icon: 'check',
      iconPosition: 'left',
    }),
};

export const Disabled = {
  render: () =>
    fieldTwig({
      type: 'text',
      value: 'Disabled field',
      disabled: true,
    }),
};

export const AllTypes = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-6); max-width: 400px;">
      <div>
        <label style="display: block; margin-bottom: var(--size-2); font-weight: var(--font-weight-600);">Text</label>
        ${fieldTwig({ type: 'text', placeholder: 'Enter text...' })}
      </div>
      <div>
        <label style="display: block; margin-bottom: var(--size-2); font-weight: var(--font-weight-600);">Number</label>
        ${fieldTwig({ type: 'number', placeholder: 'Enter number...' })}
      </div>
      <div>
        <label style="display: block; margin-bottom: var(--size-2); font-weight: var(--font-weight-600);">Email</label>
        ${fieldTwig({ type: 'email', placeholder: 'your.email@example.com' })}
      </div>
      <div>
        <label style="display: block; margin-bottom: var(--size-2); font-weight: var(--font-weight-600);">Search</label>
        ${fieldTwig({ type: 'search', placeholder: 'Search...', icon: 'search', iconPosition: 'right' })}
      </div>
      <div>
        <label style="display: block; margin-bottom: var(--size-2); font-weight: var(--font-weight-600);">Select</label>
        ${fieldTwig({ type: 'select', value: 'Select an option', icon: 'arrow-down', iconPosition: 'right' })}
      </div>
      <div>
        <label style="display: block; margin-bottom: var(--size-2); font-weight: var(--font-weight-600);">Textarea</label>
        ${fieldTwig({ type: 'textarea', placeholder: 'Enter your message...' })}
      </div>
    </div>
  `,
};

export const AllStates = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-8); max-width: 400px;">
      <div>
        <label style="display: block; margin-bottom: var(--size-2); font-weight: var(--font-weight-600);">Default (empty)</label>
        ${fieldTwig({ type: 'text', placeholder: 'Enter text...' })}
      </div>
      <div>
        <label style="display: block; margin-bottom: var(--size-2); font-weight: var(--font-weight-600);">Filled</label>
        ${fieldTwig({ type: 'text', value: 'John Doe', placeholder: 'Enter text...' })}
      </div>
      <div>
        <label style="display: block; margin-bottom: var(--size-2); font-weight: var(--font-weight-600);">Done (Success)</label>
        ${fieldTwig({ type: 'text', value: 'Valid input', done: true, icon: 'check', iconPosition: 'left' })}
      </div>
      <div style="margin-bottom: var(--size-4);">
        <label style="display: block; margin-bottom: var(--size-2); font-weight: var(--font-weight-600);">Error</label>
        ${fieldTwig({ type: 'email', value: 'invalid-email', error: 'Please enter a valid email address' })}
      </div>
      <div>
        <label style="display: block; margin-bottom: var(--size-2); font-weight: var(--font-weight-600);">Disabled</label>
        ${fieldTwig({ type: 'text', value: 'Disabled field', disabled: true })}
      </div>
    </div>
  `,
};

export const IconVariations = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-6); max-width: 400px;">
      <div>
        <label style="display: block; margin-bottom: var(--size-2); font-weight: var(--font-weight-600);">Icon Left</label>
        ${fieldTwig({ type: 'search', placeholder: 'Search...', icon: 'search', iconPosition: 'left' })}
      </div>
      <div>
        <label style="display: block; margin-bottom: var(--size-2); font-weight: var(--font-weight-600);">Icon Right</label>
        ${fieldTwig({ type: 'email', placeholder: 'your.email@example.com', icon: 'check', iconPosition: 'right' })}
      </div>
      <div>
        <label style="display: block; margin-bottom: var(--size-2); font-weight: var(--font-weight-600);">No Icon</label>
        ${fieldTwig({ type: 'text', placeholder: 'Enter text...' })}
      </div>
    </div>
  `,
};

export const UseCases = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-8); max-width: 500px;">
      <div>
        <h3 style="margin: 0 0 var(--size-4) 0; font-size: var(--size-5);">Contact Form</h3>
        <div style="display: flex; flex-direction: column; gap: var(--size-4);">
          ${fieldTwig({ type: 'text', placeholder: 'Full Name' })}
          ${fieldTwig({ type: 'email', placeholder: 'Email Address' })}
          ${fieldTwig({ type: 'textarea', placeholder: 'Your Message' })}
        </div>
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-4) 0; font-size: var(--size-5);">Property Search</h3>
        <div style="display: flex; flex-direction: column; gap: var(--size-4);">
          ${fieldTwig({ type: 'search', placeholder: 'Search location...', icon: 'search', iconPosition: 'right' })}
          ${fieldTwig({ type: 'select', value: 'Property Type', icon: 'arrow-down', iconPosition: 'right' })}
          ${fieldTwig({ type: 'number', placeholder: 'Max Price' })}
        </div>
      </div>
      <div style="margin-bottom: var(--size-6);">
        <h3 style="margin: 0 0 var(--size-4) 0; font-size: var(--size-5);">Validation States</h3>
        <div style="display: flex; flex-direction: column; gap: var(--size-6);">
          ${fieldTwig({ type: 'email', value: 'user@example.com', icon: 'check', iconPosition: 'right' })}
          ${fieldTwig({ type: 'email', value: 'invalid-email', error: 'Please enter a valid email address' })}
        </div>
      </div>
    </div>
  `,
};

export default settings;
