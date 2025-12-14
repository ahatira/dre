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
          'Form field molecule composing label, control (input/textarea/select), helper and error. Below are composite stories per type showing all states, with and without icon when applicable.',
      },
    },
  },
  argTypes: {
    type: {
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
    label: { control: 'text', table: { category: 'Content' } },
    placeholder: { control: 'text', table: { category: 'Content' } },
    value: { control: 'text', table: { category: 'Content' } },
    helper: { control: 'text', table: { category: 'Content' } },
    error: { control: 'text', table: { category: 'Validation' } },
    state: {
      control: { type: 'select' },
      options: [undefined, 'success', 'warning'],
      table: { category: 'Validation' },
      description: 'Validation state (error takes priority if set).',
    },
    optional: { control: 'boolean', table: { category: 'Behavior' } },
    required: { control: 'boolean', table: { category: 'Behavior' } },
    disabled: { control: 'boolean', table: { category: 'Behavior' } },
    icon: {
      control: 'text',
      table: { category: 'Icons' },
      description: 'Left icon name (e.g., "search").',
    },
    rows: {
      control: 'number',
      table: { category: 'Appearance' },
      description: 'Rows for textarea type.',
    },
  },
};

// -----------------------------------------------------------------------------
// Default single example
// -----------------------------------------------------------------------------
export const Default = {
  name: 'Default',
  args: {
    ...data,
    label: 'Champ de formulaire',
    placeholder: 'Votre saisie',
    optional: true,
  },
};

// -----------------------------------------------------------------------------
// Composite stories by type: all states, with/without icon when applicable
// -----------------------------------------------------------------------------

export const TextVariants = {
  name: 'Text: all states + icons',
  render: () => {
    const base = { ...data, type: 'text', label: 'Texte', placeholder: 'Votre saisie' };
    const make = (overrides) => FormField({ ...base, ...overrides });
    const items = [
      { title: 'Default', args: {} },
      { title: 'Default + icon', args: { icon: 'search' } },
      { title: 'Success', args: { state: 'success', value: 'valide' } },
      { title: 'Success + icon', args: { state: 'success', value: 'valide', icon: 'search' } },
      { title: 'Warning', args: { state: 'warning', value: '100000' } },
      { title: 'Warning + icon', args: { state: 'warning', value: '100000', icon: 'search' } },
      { title: 'Error', args: { error: 'Message d’erreur' } },
      { title: 'Error + icon', args: { error: 'Message d’erreur', icon: 'search' } },
      { title: 'Disabled', args: { disabled: true, placeholder: 'Indisponible' } },
      {
        title: 'Disabled + icon',
        args: { disabled: true, placeholder: 'Indisponible', icon: 'search' },
      },
    ];
    return `<div class="just-for-gap">${items
      .map((it) => `<div><div class="heading">${it.title}</div>${make(it.args)}</div>`)
      .join('')}</div>`;
  },
};

export const TextareaVariants = {
  name: 'Textarea: all states + icons',
  render: () => {
    const base = {
      ...data,
      type: 'textarea',
      label: 'Description',
      placeholder: 'Décrivez le bien...',
      rows: 4,
    };
    const make = (overrides) => FormField({ ...base, ...overrides });
    const items = [
      { title: 'Default', args: {} },
      { title: 'Default + icon', args: { icon: 'search' } },
      { title: 'Success', args: { state: 'success', value: 'Texte valide' } },
      {
        title: 'Success + icon',
        args: { state: 'success', value: 'Texte valide', icon: 'search' },
      },
      { title: 'Warning', args: { state: 'warning', value: 'Longueur limite proche' } },
      {
        title: 'Warning + icon',
        args: { state: 'warning', value: 'Longueur limite proche', icon: 'search' },
      },
      { title: 'Error', args: { error: 'Champ requis' } },
      { title: 'Error + icon', args: { error: 'Champ requis', icon: 'search' } },
      { title: 'Disabled', args: { disabled: true, placeholder: 'Indisponible' } },
      {
        title: 'Disabled + icon',
        args: { disabled: true, placeholder: 'Indisponible', icon: 'search' },
      },
    ];
    return `<div class="just-for-gap">${items
      .map((it) => `<div><div class="heading">${it.title}</div>${make(it.args)}</div>`)
      .join('')}</div>`;
  },
};

export const SelectVariants = {
  name: 'Select: all states',
  render: () => {
    const options = [
      { label: 'Appartement', value: 'apt' },
      { label: 'Maison', value: 'house' },
      { label: 'Bureau', value: 'office' },
    ];
    const base = { ...data, type: 'select', label: 'Type de bien', options };
    const make = (overrides) => FormField({ ...base, ...overrides });
    const items = [
      { title: 'Default', args: {} },
      { title: 'Success', args: { state: 'success' } },
      { title: 'Warning', args: { state: 'warning' } },
      { title: 'Error', args: { error: 'Sélection invalide' } },
      { title: 'Disabled', args: { disabled: true } },
    ];
    return `<div class="just-for-gap">${items
      .map((it) => `<div><div class="heading">${it.title}</div>${make(it.args)}</div>`)
      .join('')}</div>`;
  },
};
