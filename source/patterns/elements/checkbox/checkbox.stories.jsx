import checkboxTwig from './checkbox.twig';
import data from './checkbox.yml';

export default {
  title: 'Elements/Checkbox',
  render: (args) => checkboxTwig(args),
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component:
          'Native checkbox input with custom styling. Supports checked/unchecked states, disabled state, and optional labels. Fully accessible with keyboard navigation.',
      },
    },
  },
  argTypes: {
    // Content
    name: {
      description: 'Input name attribute (required)',
      control: { type: 'text' },
      table: {
        category: 'Content',
        type: { summary: 'string', required: true },
        defaultValue: { summary: 'checkbox-name' },
      },
    },
    value: {
      description: 'Input value attribute (required)',
      control: { type: 'text' },
      table: {
        category: 'Content',
        type: { summary: 'string', required: true },
        defaultValue: { summary: 'checkbox-value' },
      },
    },
    label: {
      description: 'Checkbox label text (optional)',
      control: { type: 'text' },
      table: {
        category: 'Content',
        type: { summary: 'string' },
      },
    },
    // State
    checked: {
      description: 'Checked state',
      control: { type: 'boolean' },
      table: {
        category: 'State',
        type: { summary: 'boolean' },
        defaultValue: { summary: 'false' },
      },
    },
    disabled: {
      description: 'Disabled state',
      control: { type: 'boolean' },
      table: {
        category: 'State',
        type: { summary: 'boolean' },
        defaultValue: { summary: 'false' },
      },
    },
    // Advanced
    id: {
      description: 'Unique ID for input (auto-generated if not provided)',
      control: { type: 'text' },
      table: {
        category: 'Advanced',
        type: { summary: 'string' },
      },
    },
  },
  args: {
    ...data,
  },
};

/**
 * Default: Standard checkbox with label
 */
export const Default = {
  args: {
    name: 'property-type',
    value: 'apartment',
    label: 'Apartment',
    checked: false,
    disabled: false,
  },
};

/**
 * Checked: Pre-selected checkbox
 */
export const Checked = {
  args: {
    name: 'property-features',
    value: 'parking',
    label: 'Parking available',
    checked: true,
    disabled: false,
  },
};

/**
 * Disabled: Non-interactive state
 */
export const Disabled = {
  args: {
    name: 'property-features',
    value: 'garden',
    label: 'Garden (not available)',
    checked: false,
    disabled: true,
  },
};

/**
 * Disabled Checked: Pre-selected and non-interactive
 */
export const DisabledChecked = {
  args: {
    name: 'property-features',
    value: 'balcony',
    label: 'Balcony (included)',
    checked: true,
    disabled: true,
  },
};

/**
 * No Label: Checkbox without label text
 */
export const NoLabel = {
  args: {
    name: 'agreement',
    value: 'accepted',
    label: null,
    checked: false,
    disabled: false,
  },
};

/**
 * Long Label: Checkbox with multiline text
 */
export const LongLabel = {
  args: {
    name: 'terms',
    value: 'accepted',
    label:
      'I agree to the terms and conditions of the real estate transaction, including the payment schedule and property inspection requirements.',
    checked: false,
    disabled: false,
  },
};

/**
 * Real Estate Form: Multiple checkboxes for property search
 */
export const RealEstateForm = {
  render: () => {
    const properties = [
      { name: 'property-type', value: 'apartment', label: 'Apartment', checked: true },
      { name: 'property-type', value: 'house', label: 'House', checked: false },
      { name: 'property-type', value: 'commercial', label: 'Commercial Space', checked: false },
      { name: 'property-type', value: 'land', label: 'Land', checked: false },
    ];

    const features = [
      { name: 'features', value: 'parking', label: 'Parking', checked: true },
      { name: 'features', value: 'elevator', label: 'Elevator', checked: true },
      { name: 'features', value: 'balcony', label: 'Balcony', checked: false },
      { name: 'features', value: 'garden', label: 'Garden', checked: false },
      { name: 'features', value: 'pool', label: 'Swimming Pool', checked: false },
    ];

    return `
      <div style="max-width: 600px;">
        <h3 style="margin-bottom: 1rem; font-size: 1.125rem; font-weight: 600;">Property Type</h3>
        <div style="display: flex; flex-direction: column; gap: 0.75rem; margin-bottom: 2rem;">
          ${properties.map((prop) => checkboxTwig(prop)).join('')}
        </div>

        <h3 style="margin-bottom: 1rem; font-size: 1.125rem; font-weight: 600;">Required Features</h3>
        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
          ${features.map((feat) => checkboxTwig(feat)).join('')}
        </div>
      </div>
    `;
  },
};

/**
 * Grid Layout: Checkboxes in two columns
 */
export const GridLayout = {
  render: () => {
    const amenities = [
      { name: 'amenities', value: 'gym', label: 'Fitness Center' },
      { name: 'amenities', value: 'concierge', label: '24/7 Concierge' },
      { name: 'amenities', value: 'security', label: 'Security System' },
      { name: 'amenities', value: 'storage', label: 'Storage Space' },
      { name: 'amenities', value: 'bike', label: 'Bike Storage' },
      { name: 'amenities', value: 'terrace', label: 'Rooftop Terrace' },
    ];

    return `
      <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.75rem; max-width: 600px;">
        ${amenities.map((item) => checkboxTwig(item)).join('')}
      </div>
    `;
  },
};
