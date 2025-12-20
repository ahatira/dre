import selectTwig from './select.twig';
import data from './select.yml';

export default {
  title: 'Elements/Select',
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component:
          'Native select element wrapped with custom chevron icon. Supports validation states (error, success) and disabled state. Typically used within form-field molecule for complete form control with label, help text, and error messages.',
      },
    },
  },
  argTypes: {
    // Content
    options: {
      description:
        'Array of option objects: [{ value, label, disabled?, selected? }]. First option often used as placeholder with disabled+selected flags.',
      control: false,
      table: { category: 'Content' },
    },

    // Behavior
    name: {
      description: 'Form control name attribute (group identifier)',
      control: 'text',
      table: { category: 'Behavior' },
    },
    id: {
      description: 'HTML ID for label association (auto-generated from name if omitted)',
      control: 'text',
      table: { category: 'Behavior' },
    },
    disabled: {
      description: 'Disable the select (applies ps-select--disabled modifier)',
      control: 'boolean',
      table: { category: 'Behavior', defaultValue: { summary: false } },
    },
    required: {
      description: 'Mark as required form control (adds aria-required="true")',
      control: 'boolean',
      table: { category: 'Behavior', defaultValue: { summary: false } },
    },

    // States - Validation
    error: {
      description: 'Error state with red border and icon (applies ps-select--error modifier)',
      control: 'boolean',
      table: { category: 'States', defaultValue: { summary: false } },
    },
    success: {
      description:
        'Success/valid state with green border and icon (applies ps-select--success modifier)',
      control: 'boolean',
      table: { category: 'States', defaultValue: { summary: false } },
    },

    // Attributes
    attributes: {
      description: 'Drupal attributes object for native <select> element',
      control: false,
      table: { category: 'Attributes' },
    },
    wrapper_attributes: {
      description: 'Drupal attributes object for wrapper <div> element',
      control: false,
      table: { category: 'Attributes' },
    },
  },
  render: (args) => selectTwig(args),
};

/**
 * Default
 * Standard select with placeholder option and real estate property types
 */
export const Default = {
  args: data,
};

/**
 * With Option Groups
 * Select with optgroups for categorizing related options (Real Estate property types by category)
 */
export const WithOptionGroups = {
  args: {
    name: 'property_category',
    id: 'property-category-select',
    options: [
      { value: '', label: 'Sélectionner un bien...', disabled: true, selected: true },
      {
        label: 'Résidentiel',
        options: [
          { value: 'apartment', label: 'Appartement' },
          { value: 'house', label: 'Maison' },
          { value: 'penthouse', label: 'Penthouse' },
          { value: 'villa', label: 'Villa' },
        ],
      },
      {
        label: 'Commercial',
        options: [
          { value: 'office', label: 'Bureau' },
          { value: 'retail', label: 'Local commercial' },
          { value: 'warehouse', label: 'Entrepôt' },
          { value: 'coworking', label: 'Espace de coworking' },
        ],
      },
      {
        label: 'Terrain',
        options: [
          { value: 'building_land', label: 'Terrain constructible' },
          { value: 'agricultural_land', label: 'Terrain agricole' },
        ],
      },
    ],
  },
};

/**
 * States
 * All possible select states: default, hover, focus, success, error, disabled, disabled+placeholder
 */
export const States = {
  render: () => {
    return `
      <div style="display: flex; flex-direction: column; gap: var(--size-6);">
        <!-- Default / Placeholder -->
        <div>
          <div style="font-weight: var(--font-weight-600); margin-bottom: var(--size-2); font-size: var(--font-size-0); color: var(--gray-700);">Default (Placeholder)</div>
          ${selectTwig({
            name: 'states_default',
            id: 'states-default',
            options: [
              { value: '', label: 'Select an option', disabled: true, selected: true },
              { value: 'opt1', label: 'Option 1' },
              { value: 'opt2', label: 'Option 2' },
            ],
          })}
        </div>

        <!-- Hover -->
        <div>
          <div style="font-weight: var(--font-weight-600); margin-bottom: var(--size-2); font-size: var(--font-size-0); color: var(--gray-700);">Hover</div>
          ${selectTwig({
            name: 'states_hover',
            id: 'states-hover',
            options: [
              { value: 'apartment', label: 'Appartement', selected: true },
              { value: 'house', label: 'Maison' },
            ],
          })}
        </div>

        <!-- Focus -->
        <div>
          <div style="font-weight: var(--font-weight-600); margin-bottom: var(--size-2); font-size: var(--font-size-0); color: var(--gray-700);">Focus (outline visible on interaction)</div>
          ${selectTwig({
            name: 'states_focus',
            id: 'states-focus',
            options: [
              { value: 'apartment', label: 'Appartement', selected: true },
              { value: 'house', label: 'Maison' },
            ],
          })}
        </div>

        <!-- Success -->
        <div>
          <div style="font-weight: var(--font-weight-600); margin-bottom: var(--size-2); font-size: var(--font-size-0); color: var(--success);">Success (validated)</div>
          ${selectTwig({
            name: 'states_success',
            id: 'states-success',
            success: true,
            options: [
              { value: 'apartment', label: 'Appartement', selected: true },
              { value: 'house', label: 'Maison' },
            ],
          })}
        </div>

        <!-- Error -->
        <div>
          <div style="font-weight: var(--font-weight-600); margin-bottom: var(--size-2); font-size: var(--font-size-0); color: var(--danger);">Error (validation failed)</div>
          ${selectTwig({
            name: 'states_error',
            id: 'states-error',
            error: true,
            options: [
              { value: '', label: 'Select an option', disabled: true, selected: true },
              { value: 'apartment', label: 'Appartement' },
            ],
          })}
        </div>

        <!-- Disabled (with placeholder) -->
        <div>
          <div style="font-weight: var(--font-weight-600); margin-bottom: var(--size-2); font-size: var(--font-size-0); color: var(--gray-700);">Disabled (placeholder)</div>
          ${selectTwig({
            name: 'states_disabled_placeholder',
            id: 'states-disabled-placeholder',
            disabled: true,
            options: [
              { value: '', label: 'Not available', disabled: true, selected: true },
              { value: 'apartment', label: 'Appartement' },
            ],
          })}
        </div>

        <!-- Disabled (with value) -->
        <div>
          <div style="font-weight: var(--font-weight-600); margin-bottom: var(--size-2); font-size: var(--font-size-0); color: var(--gray-700);">Disabled (value selected)</div>
          ${selectTwig({
            name: 'states_disabled_value',
            id: 'states-disabled-value',
            disabled: true,
            options: [
              { value: 'apartment', label: 'Appartement', selected: true },
              { value: 'house', label: 'Maison' },
            ],
          })}
        </div>
      </div>
    `;
  },
};
