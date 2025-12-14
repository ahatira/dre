import FormField from './form-field.twig';
import data from './form-field.yml';

export default {
  title: 'Components/Form Field',
  component: FormField,
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component:
          'Form field molecule composing label, control (input/textarea/select), helper and error. Supports icons and optional badge.',
      },
    },
  },
  argTypes: {
    type: {
      control: 'select',
      options: ['text', 'textarea', 'select'],
      table: { category: 'Appearance' },
    },
    label: { control: 'text', table: { category: 'Content' } },
    placeholder: { control: 'text', table: { category: 'Content' } },
    value: { control: 'text', table: { category: 'Content' } },
    helper: { control: 'text', table: { category: 'Content' } },
    error: { control: 'text', table: { category: 'Validation' } },
    optional: { control: 'boolean', table: { category: 'Behavior' } },
    disabled: { control: 'boolean', table: { category: 'Behavior' } },
    icon: { control: 'text', table: { category: 'Icons' } },
  },
  render: (args) => FormField(args),
};

// -----------------------------------------------------------------------------
// Core stories (concise, state-focused)
// -----------------------------------------------------------------------------

export const DefaultText = {
  name: 'Default text',
  args: {
    ...data,
    label: 'Label',
    placeholder: 'Value',
    optional: true,
  },
};

export const WithIcon = {
  name: 'Text with icon',
  args: {
    ...data,
    label: 'Search',
    placeholder: 'Rechercher un bien',
    icon: 'search',
  },
};

export const SuccessState = {
  name: 'Success state',
  args: {
    ...data,
    label: 'Email',
    value: 'agent@immo.fr',
    state: 'success',
  },
};

export const ErrorState = {
  name: 'Error state',
  args: {
    ...data,
    label: 'Email',
    value: 'invalid',
    error: 'Adresse e-mail invalide',
  },
};

export const DisabledState = {
  name: 'Disabled placeholder',
  args: {
    ...data,
    label: 'Ville',
    placeholder: 'Paris ou Lyon',
    disabled: true,
  },
};

export const TextareaField = {
  name: 'Textarea with helper',
  args: {
    ...data,
    type: 'textarea',
    label: 'Description',
    placeholder: 'Décrivez le bien...',
    helper: '250 caractères maximum.',
    rows: 4,
  },
};

export const SelectField = {
  name: 'Select (native chevron)',
  args: {
    ...data,
    type: 'select',
    label: 'Type de bien',
    options: [
      { label: 'Appartement', value: 'apt' },
      { label: 'Maison', value: 'house' },
      { label: 'Bureau', value: 'office' },
    ],
  },
};
