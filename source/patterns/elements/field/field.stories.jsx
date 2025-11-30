import fieldTwig from './field.twig';
import data from './field.yml';
import iconsList from '../../documentation/icons-list.json';

const settings = {
  title: 'Elements/Field',
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component:
          'Base field component (input/select/textarea) with token-based styling and accessible states. Supports icons, error/disabled/done states, and multiple input types.'
      },
    },
  },
  argTypes: {
    // Content
    value: {
      description: 'Current value of the field',
      control: { type: 'text' },
      table: {
        category: 'Content',
        type: { summary: 'string' },
        defaultValue: { summary: '' },
      },
    },
    placeholder: {
      description: 'Placeholder text shown when field is empty',
      control: { type: 'text' },
      table: {
        category: 'Content',
        type: { summary: 'string' },
        defaultValue: { summary: '' },
      },
    },
    // Appearance
    type: {
      description: 'Type of field input',
      control: { type: 'select' },
      options: ['text', 'number', 'email', 'search', 'select', 'textarea'],
      table: {
        category: 'Appearance',
        type: { summary: 'text | number | email | search | select | textarea' },
        defaultValue: { summary: 'text' },
      },
    },
    icon: {
      description: 'Icon to display (optional)',
      control: { type: 'select' },
      options: ['', ...iconsList.all],
      table: {
        category: 'Appearance',
        type: { summary: 'string' },
        defaultValue: { summary: '' },
      },
    },
    iconPosition: {
      description: 'Position of the icon',
      control: { type: 'select' },
      options: ['left', 'right'],
      table: {
        category: 'Appearance',
        type: { summary: 'left | right' },
        defaultValue: { summary: 'right' },
      },
    },
    // Behavior
    disabled: {
      description: 'Disabled state of the field',
      control: { type: 'boolean' },
      table: {
        category: 'Behavior',
        type: { summary: 'boolean' },
        defaultValue: { summary: 'false' },
      },
    },
    done: {
      description: 'Success/validated state of the field',
      control: { type: 'boolean' },
      table: {
        category: 'Behavior',
        type: { summary: 'boolean' },
        defaultValue: { summary: 'false' },
      },
    },
    // Accessibility
    error: {
      description: 'Error message to display below the field (sets aria-invalid and aria-describedby)',
      control: { type: 'text' },
      table: {
        category: 'Accessibility',
        type: { summary: 'string' },
        defaultValue: { summary: '' },
      },
    },
  },
};

export const Default = {
  render: (args) => fieldTwig(args),
  args: { ...data },
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
