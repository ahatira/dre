import formFieldTwig from './form-field.twig';
import data from './form-field.yml';

export default {
  title: 'Components/Form Field',
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component:
          'Complete form field with label, input/select/textarea, helper, and error. Supports icons and optional/required indicators.',
      },
    },
  },
  argTypes: {
    label: {
      description: 'Field label text',
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
      description: 'Field name attribute',
      control: 'text',
      table: { category: 'Content' },
    },
    id: {
      description: 'Field ID attribute',
      control: 'text',
      table: { category: 'Content' },
    },
    value: {
      description: 'Field initial value',
      control: 'text',
      table: { category: 'Content' },
    },
    placeholder: {
      description: 'Placeholder text',
      control: 'text',
      table: { category: 'Content' },
    },
    required: {
      description: 'Mark field as required with asterisk',
      control: 'boolean',
      table: { category: 'Behavior' },
    },
    optional: {
      description: 'Show "Optional" badge',
      control: 'boolean',
      table: { category: 'Behavior' },
    },
    disabled: {
      description: 'Disabled state',
      control: 'boolean',
      table: { category: 'Behavior' },
    },
    helper: {
      description: 'Helper/explanation text below field',
      control: 'text',
      table: { category: 'Content' },
    },
    error: {
      description: 'Error message',
      control: 'text',
      table: { category: 'Validation' },
    },
    icon_left: {
      description: 'Icon name for left position (e.g., "search")',
      control: 'text',
      table: { category: 'Icons' },
    },
    icon_right: {
      description: 'Icon name for right position (e.g., "chevron-down")',
      control: 'text',
      table: { category: 'Icons' },
    },
  },
  render: (args) => formFieldTwig(args),
};

/* ============================================
   VARIATIONS (From Maquette)
   ============================================ */

export const FieldWithLabel = {
  name: 'Field with label',
  args: {
    ...data,
    label: 'Label',
    placeholder: 'Value',
    optional: true,
  },
};

export const ErrorState = {
  name: 'Error',
  args: {
    ...data,
    label: 'Label',
    placeholder: 'Value',
    error: 'Lorem ipsum dolor sit amet.',
    optional: true,
  },
};

export const TextAreaField = {
  name: 'Text area',
  args: {
    ...data,
    type: 'textarea',
    label: 'Label',
    placeholder: 'Placeholder',
    rows: 4,
    optional: true,
  },
};

export const NumericField = {
  name: 'Numérique',
  args: {
    ...data,
    type: 'select',
    label: 'Label',
    options: [{ label: 'Value', value: 'value' }],
    optional: true,
  },
};

export const FieldWithExplanation = {
  name: 'Field with explanation',
  args: {
    ...data,
    label: 'Label',
    placeholder: 'Value',
    helper: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
    optional: true,
  },
};

/* ============================================
   FIELD STATES (No Icon)
   ============================================ */

export const StateDefault = {
  name: '[States] Default - No Icon',
  args: {
    ...data,
    type: 'text',
    placeholder: 'Value',
  },
};

export const StatePlaceholder = {
  name: '[States] Placeholder - No Icon',
  args: {
    ...data,
    type: 'text',
    placeholder: 'Placeholder',
  },
};

export const StateHover = {
  name: '[States] Hover - No Icon',
  args: {
    ...data,
    type: 'text',
    placeholder: 'Label',
  },
};

export const StateFocus = {
  name: '[States] Focus - No Icon',
  args: {
    ...data,
    type: 'text',
    value: 'Value',
  },
};

export const StateDone = {
  name: '[States] Done (Success) - No Icon',
  args: {
    ...data,
    type: 'text',
    value: 'Value',
    state: 'success',
  },
};

export const StateError = {
  name: '[States] Error - No Icon',
  args: {
    ...data,
    type: 'text',
    value: 'Value',
    error: 'Error message',
  },
};

export const StateDisabledPlaceholder = {
  name: '[States] Disabled (placeholder) - No Icon',
  args: {
    ...data,
    type: 'text',
    placeholder: 'Placeholder',
    disabled: true,
  },
};

export const StateDisabledValue = {
  name: '[States] Disabled (value) - No Icon',
  args: {
    ...data,
    type: 'text',
    value: 'Value',
    disabled: true,
  },
};

/* ============================================
   FIELD STATES (Icon Left - Search)
   ============================================ */

export const StateDefaultIconLeft = {
  name: '[States] Default - Icon Left',
  args: {
    ...data,
    type: 'text',
    placeholder: 'Value',
    icon_left: 'search',
  },
};

export const StatePlaceholderIconLeft = {
  name: '[States] Placeholder - Icon Left',
  args: {
    ...data,
    type: 'text',
    placeholder: 'Placeholder',
    icon_left: 'search',
  },
};

export const StateHoverIconLeft = {
  name: '[States] Hover - Icon Left',
  args: {
    ...data,
    type: 'text',
    placeholder: 'Label',
    icon_left: 'search',
  },
};

export const StateFocusIconLeft = {
  name: '[States] Focus - Icon Left',
  args: {
    ...data,
    type: 'text',
    value: 'Value',
    icon_left: 'search',
  },
};

export const StateDoneIconLeft = {
  name: '[States] Done (Success) - Icon Left',
  args: {
    ...data,
    type: 'text',
    value: 'Value',
    state: 'success',
    icon_left: 'search',
  },
};

export const StateErrorIconLeft = {
  name: '[States] Error - Icon Left',
  args: {
    ...data,
    type: 'text',
    value: 'Value',
    error: 'Error message',
    icon_left: 'search',
  },
};

export const StateDisabledPlaceholderIconLeft = {
  name: '[States] Disabled (placeholder) - Icon Left',
  args: {
    ...data,
    type: 'text',
    placeholder: 'Placeholder',
    disabled: true,
    icon_left: 'search',
  },
};

export const StateDisabledValueIconLeft = {
  name: '[States] Disabled (value) - Icon Left',
  args: {
    ...data,
    type: 'text',
    value: 'Value',
    disabled: true,
    icon_left: 'search',
  },
};

/* ============================================
   SELECT STATES (Icon Right - Chevron)
   ============================================ */

export const StateDefaultIconRight = {
  name: '[States] Default - Icon Right (Select)',
  args: {
    ...data,
    type: 'select',
    options: [{ label: 'Value', value: 'value' }],
    icon_right: 'chevron-down',
  },
};

export const StatePlaceholderIconRight = {
  name: '[States] Placeholder - Icon Right (Select)',
  args: {
    ...data,
    type: 'select',
    options: [{ label: 'Placeholder', value: 'placeholder' }],
    icon_right: 'chevron-down',
  },
};

export const StateHoverIconRight = {
  name: '[States] Hover - Icon Right (Select)',
  args: {
    ...data,
    type: 'select',
    options: [{ label: 'Label', value: 'label' }],
    icon_right: 'chevron-down',
  },
};

export const StateFocusIconRight = {
  name: '[States] Focus - Icon Right (Select)',
  args: {
    ...data,
    type: 'select',
    options: [{ label: 'Value', value: 'value', selected: true }],
    icon_right: 'chevron-down',
  },
};

export const StateDoneIconRight = {
  name: '[States] Done (Success) - Icon Right (Select)',
  args: {
    ...data,
    type: 'select',
    options: [{ label: 'Value', value: 'value', selected: true }],
    state: 'success',
    icon_right: 'chevron-down',
  },
};

export const StateErrorIconRight = {
  name: '[States] Error - Icon Right (Select)',
  args: {
    ...data,
    type: 'select',
    options: [{ label: 'Value', value: 'value', selected: true }],
    error: 'Error message',
    icon_right: 'chevron-down',
  },
};

export const StateDisabledPlaceholderIconRight = {
  name: '[States] Disabled (placeholder) - Icon Right (Select)',
  args: {
    ...data,
    type: 'select',
    options: [{ label: 'Placeholder', value: 'placeholder' }],
    disabled: true,
    icon_right: 'chevron-down',
  },
};

export const StateDisabledValueIconRight = {
  name: '[States] Disabled (value) - Icon Right (Select)',
  args: {
    ...data,
    type: 'select',
    options: [{ label: 'Value', value: 'value' }],
    disabled: true,
    icon_right: 'chevron-down',
  },
};
