import contactFormTemplate from './contact-form.twig';
import contactFormData from './contact-form.yml';

export default {
  title: 'Collections/Contact Form',
  tags: ['autodocs'],
  render: (args) => contactFormTemplate(args),
};

/**
 * Default contact form with pre-filled personal data,
 * project message, consent checkboxes, and legal notice.
 */
export const Default = {
  args: {
    ...contactFormData,
  },
  argTypes: {
    title: {
      control: 'text',
      description: 'Main form title',
      table: {
        category: 'Content',
        defaultValue: { summary: 'Contact' },
      },
    },
    section_1_title: {
      control: 'text',
      description: 'Section 1 heading (personal data)',
      table: {
        category: 'Content',
        defaultValue: { summary: 'What are your co-data?' },
      },
    },
    section_2_title: {
      control: 'text',
      description: 'Section 2 heading (project details)',
      table: {
        category: 'Content',
        defaultValue: { summary: 'Would you like to know more about your project?' },
      },
    },
    fields: {
      control: 'object',
      description: 'Form fields object (first_name, last_name, email, company, phone)',
      table: {
        category: 'Form Fields',
        type: {
          summary: 'object',
          detail: 'Contains first_name, last_name, email, company, phone field objects',
        },
      },
    },
    message_field: {
      control: 'object',
      description: 'Textarea field object for project message',
      table: {
        category: 'Form Fields',
        type: {
          summary: 'object',
          detail: 'Contains label, input_id, field_type (textarea), value, required, rows',
        },
      },
    },
    consents: {
      control: 'object',
      description: 'Array of checkbox consent objects',
      table: {
        category: 'Content',
        type: {
          summary: 'array',
          detail: 'Each consent: { label, id, name, value, checked }',
        },
      },
    },
    legal_text: {
      control: 'text',
      description: 'Legal/RGPD notice HTML text',
      table: {
        category: 'Content',
        type: { summary: 'HTML string' },
      },
    },
    submit_button: {
      control: 'object',
      description: 'Submit button configuration',
      table: {
        category: 'Actions',
        type: {
          summary: 'object',
          detail: 'Contains label, variant, type, fullWidth',
        },
        defaultValue: {
          summary: '{ label: "Send", variant: "primary", type: "submit", fullWidth: true }',
        },
      },
    },
    action: {
      control: 'text',
      description: 'Form action URL',
      table: {
        category: 'Behavior',
        defaultValue: { summary: '#contact-form' },
      },
    },
  },
};

/**
 * Empty contact form without pre-filled data.
 */
export const Empty = {
  args: {
    ...contactFormData,
    fields: {
      first_name: {
        label: 'First name',
        input_id: 'contact-first-name',
        field_type: 'text',
        required: true,
      },
      last_name: {
        label: 'Last name',
        input_id: 'contact-last-name',
        field_type: 'text',
        required: true,
      },
      email: {
        label: 'Professional e-mail',
        input_id: 'contact-email',
        field_type: 'email',
        required: true,
      },
      company: {
        label: 'Company',
        input_id: 'contact-company',
        field_type: 'text',
        placeholder: 'Enter your company',
        optional: true,
      },
      phone: {
        label: 'Professional phone',
        input_id: 'contact-phone',
        field_type: 'tel',
        placeholder: 'Enter your professional phone',
        optional: true,
      },
    },
    message_field: {
      label: 'Message',
      input_id: 'contact-message',
      field_type: 'textarea',
      placeholder: 'Enter your message...',
      required: true,
      rows: 6,
    },
  },
};

/**
 * Contact form with validation errors.
 */
export const WithErrors = {
  args: {
    ...contactFormData,
    fields: {
      first_name: {
        label: 'First name',
        input_id: 'contact-first-name',
        field_type: 'text',
        required: true,
        error: 'This field is required',
        value: '',
      },
      last_name: {
        label: 'Last name',
        input_id: 'contact-last-name',
        field_type: 'text',
        required: true,
        value: 'Plot',
      },
      email: {
        label: 'Professional e-mail',
        input_id: 'contact-email',
        field_type: 'email',
        required: true,
        error: 'Please enter a valid email address',
        value: 'invalid-email',
      },
      company: {
        label: 'Company',
        input_id: 'contact-company',
        field_type: 'text',
        placeholder: 'Enter your company',
        optional: true,
      },
      phone: {
        label: 'Professional phone',
        input_id: 'contact-phone',
        field_type: 'tel',
        placeholder: 'Enter your professional phone',
        optional: true,
      },
    },
    message_field: {
      label: 'Message',
      input_id: 'contact-message',
      field_type: 'textarea',
      required: true,
      error: 'This field is required',
      value: '',
      rows: 6,
    },
  },
};
