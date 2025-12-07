import formFieldTwig from './form-field.twig';
import data from './form-field.yml';

export default {
  title: 'Components/Form Field',
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component: 'Complete form field with label, input/select/textarea, helper, and error.',
      },
    },
  },
  argTypes: {
    label: {
      description: 'Field label',
      control: 'text',
      table: { category: 'Content' },
    },
    type: {
      description: 'Field type',
      control: 'select',
      options: [
        'text',
        'email',
        'password',
        'number',
        'search',
        'tel',
        'url',
        'textarea',
        'select',
      ],
      table: { category: 'Appearance' },
    },
    name: {
      description: 'Field name',
      control: 'text',
      table: { category: 'Content' },
    },
    id: {
      description: 'Field ID',
      control: 'text',
      table: { category: 'Content' },
    },
    value: {
      description: 'Field value',
      control: 'text',
      table: { category: 'Content' },
    },
    placeholder: {
      description: 'Placeholder text',
      control: 'text',
      table: { category: 'Content' },
    },
    required: {
      description: 'Whether field is required',
      control: 'boolean',
      table: { category: 'Behavior' },
    },
    disabled: {
      description: 'Whether field is disabled',
      control: 'boolean',
      table: { category: 'Behavior' },
    },
    helper: {
      description: 'Helper text',
      control: 'text',
      table: { category: 'Content' },
    },
    error: {
      description: 'Error message',
      control: 'text',
      table: { category: 'Validation' },
    },
  },
  render: (args) => formFieldTwig(args),
};

export const Default = {
  args: data,
};

export const Email = {
  args: {
    ...data,
    type: 'email',
    label: 'Email address',
  },
};

export const Password = {
  args: {
    ...data,
    type: 'password',
    label: 'Password',
  },
};

export const Textarea = {
  args: {
    ...data,
    type: 'textarea',
    label: 'Message',
    rows: 6,
  },
};

export const Select = {
  args: {
    ...data,
    type: 'select',
    label: 'Select option',
    options: [
      { label: 'Option 1', value: '1' },
      { label: 'Option 2', value: '2' },
      { label: 'Option 3', value: '3', selected: true },
    ],
  },
};

export const Required = {
  args: {
    ...data,
    required: true,
  },
};

export const Disabled = {
  args: {
    ...data,
    disabled: true,
    value: 'Disabled value',
  },
};

export const WithError = {
  args: {
    ...data,
    error: 'This field is required',
    helper: '',
  },
};
