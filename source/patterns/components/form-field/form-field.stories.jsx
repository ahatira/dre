import formFieldTwig from './form-field.twig';
import formFieldData from './form-field.yml';

export default {
  title: 'Components/FormField',
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component:
          'Complete form field with label, input, helper text, and error message. Wraps ps-field atom with form semantics.\n\n' +
          'See Props, Showcases, and README for details on states, accessibility, and integration.',
      },
    },
  },
  argTypes: {
    // Content
    label: {
      name: 'label',
      description: 'Label text for the field',
      control: 'text',
      table: {
        category: 'Content',
        type: { summary: 'string' },
      },
    },
    helperText: {
      name: 'helperText',
      description: 'Optional helper text below field (hidden when error is present)',
      control: 'text',
      table: {
        category: 'Content',
        type: { summary: 'string' },
      },
    },
    error: {
      name: 'error',
      description: 'Error message to display (replaces helper text, sets error state)',
      control: 'text',
      table: {
        category: 'Content',
        type: { summary: 'string' },
      },
    },
    'field.placeholder': {
      name: 'field.placeholder',
      description: 'Placeholder text for the input field',
      control: 'text',
      table: {
        category: 'Content',
        type: { summary: 'string' },
      },
    },
    'field.value': {
      name: 'field.value',
      description: 'Current value of the field',
      control: 'text',
      table: {
        category: 'Content',
        type: { summary: 'string' },
      },
    },

    // Appearance
    'field.type': {
      name: 'field.type',
      description: 'Input field type',
      control: 'select',
      options: ['text', 'email', 'number', 'search', 'textarea', 'select'],
      table: {
        category: 'Appearance',
        type: { summary: 'string' },
        defaultValue: { summary: 'text' },
      },
    },
    'field.icon': {
      name: 'field.icon',
      description: 'Icon name (without "icon-" prefix)',
      control: 'text',
      table: {
        category: 'Appearance',
        type: { summary: 'string' },
      },
    },
    'field.iconPosition': {
      name: 'field.iconPosition',
      description: 'Icon position',
      control: 'select',
      options: ['left', 'right'],
      table: {
        category: 'Appearance',
        type: { summary: 'string' },
        defaultValue: { summary: 'right' },
      },
    },

    // Behavior
    required: {
      name: 'required',
      description: 'Mark field as required (shows asterisk)',
      control: 'boolean',
      table: {
        category: 'Behavior',
        type: { summary: 'boolean' },
        defaultValue: { summary: false },
      },
    },
    disabled: {
      name: 'disabled',
      description: 'Disable entire field group',
      control: 'boolean',
      table: {
        category: 'Behavior',
        type: { summary: 'boolean' },
        defaultValue: { summary: false },
      },
    },

    // Accessibility
    id: {
      name: 'id',
      description: 'Unique ID for label/field association (auto-generated if omitted)',
      control: 'text',
      table: {
        category: 'Accessibility',
        type: { summary: 'string' },
      },
    },
  },
};

/**
 * Default FormField - Email input with helper text
 */
export const Default = {
  render: (args) => formFieldTwig(args),
  args: {
    ...formFieldData,
  },
};

/**
 * All Field States Showcase
 * Demonstrates default, filled, error, and disabled states
 */
export const AllStates = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-6);">
      <!-- Default (empty) -->
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-1); color: var(--gray-700);">Default (empty)</h3>
        ${formFieldTwig({
          label: 'Email Address',
          field: {
            type: 'email',
            placeholder: 'Enter your email',
          },
          helperText: 'We will never share your email with anyone.',
        })}
      </div>

      <!-- Filled -->
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-1); color: var(--gray-700);">Filled</h3>
        ${formFieldTwig({
          label: 'Full Name',
          field: {
            type: 'text',
            value: 'Jean Dupont',
            placeholder: 'Enter your name',
          },
          helperText: 'Please enter your full legal name.',
        })}
      </div>

      <!-- Required -->
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-1); color: var(--gray-700);">Required</h3>
        ${formFieldTwig({
          label: 'Phone Number',
          field: {
            type: 'text',
            placeholder: '+33 1 23 45 67 89',
          },
          required: true,
          helperText: 'Required for account verification.',
        })}
      </div>

      <!-- Error -->
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-1); color: var(--gray-700);">Error</h3>
        ${formFieldTwig({
          label: 'Email Address',
          field: {
            type: 'email',
            value: 'invalid-email',
            placeholder: 'Enter your email',
          },
          error: 'Please enter a valid email address.',
          required: true,
        })}
      </div>

      <!-- Disabled -->
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-1); color: var(--gray-700);">Disabled</h3>
        ${formFieldTwig({
          label: 'Account Type',
          field: {
            type: 'text',
            value: 'Premium Member',
            placeholder: 'Account type',
          },
          disabled: true,
          helperText: 'This field cannot be modified.',
        })}
      </div>
    </div>
  `,
};

/**
 * With Icon - Shows fields with left and right positioned icons
 */
export const WithIcon = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-6);">
      <!-- Icon Right (default) -->
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-1); color: var(--gray-700);">Icon Right</h3>
        ${formFieldTwig({
          label: 'Search',
          field: {
            type: 'search',
            placeholder: 'Search properties...',
            icon: 'search',
            iconPosition: 'right',
          },
          helperText: 'Enter keywords to search our database.',
        })}
      </div>

      <!-- Icon Left -->
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-1); color: var(--gray-700);">Icon Left</h3>
        ${formFieldTwig({
          label: 'Email',
          field: {
            type: 'email',
            placeholder: 'example@domain.com',
            icon: 'mail',
            iconPosition: 'left',
          },
          helperText: 'We will send a confirmation email.',
        })}
      </div>
    </div>
  `,
};

/**
 * Different Field Types - Text, Email, Number, Textarea
 */
export const AllFieldTypes = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-6);">
      <!-- Text -->
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-1); color: var(--gray-700);">Text Input</h3>
        ${formFieldTwig({
          label: 'Full Name',
          field: {
            type: 'text',
            placeholder: 'John Doe',
          },
          helperText: 'Enter your first and last name.',
        })}
      </div>

      <!-- Email -->
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-1); color: var(--gray-700);">Email Input</h3>
        ${formFieldTwig({
          label: 'Email Address',
          field: {
            type: 'email',
            placeholder: 'example@domain.com',
          },
          required: true,
          helperText: 'Valid email format required.',
        })}
      </div>

      <!-- Number -->
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-1); color: var(--gray-700);">Number Input</h3>
        ${formFieldTwig({
          label: 'Property Size (m²)',
          field: {
            type: 'number',
            placeholder: '75',
          },
          helperText: 'Enter the total area in square meters.',
        })}
      </div>

      <!-- Textarea -->
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-1); color: var(--gray-700);">Textarea</h3>
        ${formFieldTwig({
          label: 'Description',
          field: {
            type: 'textarea',
            placeholder: 'Enter a detailed description...',
          },
          helperText: 'Provide additional details (optional).',
        })}
      </div>
    </div>
  `,
};

/**
 * In Form Context - Multiple fields in a realistic form layout
 */
export const InFormContext = {
  render: () => `
    <form style="display: flex; flex-direction: column; gap: var(--size-5); max-width: 480px; padding: var(--size-6); background: var(--gray-50); border-radius: var(--radius-2);">
      <h2 style="margin: 0 0 var(--size-4) 0; font-size: var(--font-size-3); font-weight: var(--font-weight-700); color: var(--gray-900);">Contact Information</h2>
      
      ${formFieldTwig({
        label: 'Full Name',
        id: 'contact-name',
        field: {
          type: 'text',
          placeholder: 'Jean Dupont',
        },
        required: true,
      })}

      ${formFieldTwig({
        label: 'Email Address',
        id: 'contact-email',
        field: {
          type: 'email',
          placeholder: 'jean.dupont@example.com',
        },
        required: true,
        helperText: 'We will send a confirmation to this address.',
      })}

      ${formFieldTwig({
        label: 'Phone Number',
        id: 'contact-phone',
        field: {
          type: 'text',
          placeholder: '+33 1 23 45 67 89',
        },
        helperText: 'Optional - for SMS notifications.',
      })}

      ${formFieldTwig({
        label: 'Message',
        id: 'contact-message',
        field: {
          type: 'textarea',
          placeholder: 'How can we help you?',
        },
        required: true,
        helperText: 'Please provide details about your inquiry.',
      })}

      <button 
        type="submit" 
        style="padding: var(--size-3) var(--size-6); background: var(--brand-primary); color: var(--white); border: none; border-radius: var(--radius-1); font-size: var(--font-size-1); font-weight: var(--font-weight-600); cursor: pointer;"
        onmouseover="this.style.background='var(--green-700)'"
        onmouseout="this.style.background='var(--brand-primary)'"
      >
        Submit Form
      </button>
    </form>
  `,
};
