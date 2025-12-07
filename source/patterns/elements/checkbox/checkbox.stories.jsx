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
          'Native checkbox input with custom SVG icon styling. Supports 8 semantic color variants (primary, secondary, success, warning, danger, info, dark, light) and 6 sizes (xs, sm, md, lg, xl, xxl). Includes checked/unchecked/indeterminate/disabled states with smooth animations. Fully accessible with keyboard navigation and WCAG 2.2 AA compliance.',
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
    // Appearance
    color: {
      description: 'Color variant',
      control: { type: 'select' },
      options: ['primary', 'secondary', 'success', 'warning', 'danger', 'info', 'dark', 'light'],
      table: {
        category: 'Appearance',
        type: { summary: 'primary | secondary | success | warning | danger | info | dark | light' },
        defaultValue: { summary: 'primary' },
      },
    },
    size: {
      description: 'Size variant',
      control: { type: 'inline-radio' },
      options: ['xs', 'sm', 'md', 'lg', 'xl', 'xxl'],
      table: {
        category: 'Appearance',
        type: { summary: 'xs | sm | md | lg | xl | xxl' },
        defaultValue: { summary: 'md' },
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
    indeterminate: {
      description: 'Indeterminate state (neither checked nor unchecked)',
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

/**
 * All Colors: Complete color variant showcase
 */
export const AllColors = {
  render: () => {
    const colors = [
      'primary',
      'secondary',
      'success',
      'warning',
      'danger',
      'info',
      'dark',
      'light',
    ];

    return `
      <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        ${colors
          .map(
            (color) => `
          <div>
            <h4 style="margin-bottom: 0.75rem; font-size: 0.875rem; font-weight: 600; text-transform: uppercase; color: var(--gray-700);">${color}</h4>
            <div style="display: flex; flex-direction: column; gap: 0.75rem; padding: 1rem; background-color: var(--gray-50); border-radius: 4px;">
              ${checkboxTwig({ name: `color-${color}`, value: 'unchecked', label: 'Unchecked', color, checked: false })}
              ${checkboxTwig({ name: `color-${color}`, value: 'checked', label: 'Checked', color, checked: true })}
              ${checkboxTwig({ name: `color-${color}`, value: 'disabled', label: 'Disabled', color, disabled: true })}
            </div>
          </div>
        `
          )
          .join('')}
      </div>
    `;
  },
  parameters: {
    docs: {
      description: {
        story:
          'Complete showcase of all 8 color variants: primary, secondary, success, warning, danger, info, dark, and light.',
      },
    },
  },
};

/**
 * All Sizes: Complete size variant showcase
 */
export const AllSizes = {
  render: () => {
    const sizes = ['xs', 'sm', 'md', 'lg', 'xl', 'xxl'];

    return `
      <div style="display: flex; flex-direction: column; gap: 1.5rem; align-items: flex-start;">
        ${sizes
          .map(
            (size) => `
          <div>
            <h4 style="margin-bottom: 0.5rem; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; color: var(--gray-700);">${size} size</h4>
            <div style="display: flex; gap: 1rem; padding: 1rem; background-color: var(--gray-50); border-radius: 4px; align-items: center;">
              ${checkboxTwig({ name: `size-${size}`, value: 'unchecked', label: 'Unchecked', size, checked: false })}
              ${checkboxTwig({ name: `size-${size}`, value: 'checked', label: 'Checked', size, checked: true })}
            </div>
          </div>
        `
          )
          .join('')}
      </div>
    `;
  },
  parameters: {
    docs: {
      description: {
        story:
          'Complete showcase of all 6 size variants: xs (extra small), sm (small), md (medium, default), lg (large), xl (extra large), and xxl (extra extra large).',
      },
    },
  },
};

/**
 * All States: Interactive state combinations
 */
export const AllStates = {
  render: () => {
    const states = [
      { label: 'Default (unchecked)', checked: false, disabled: false },
      { label: 'Checked', checked: true, disabled: false },
      { label: 'Indeterminate', checked: false, disabled: false, indeterminate: true },
      { label: 'Disabled (unchecked)', checked: false, disabled: true },
      { label: 'Disabled (checked)', checked: true, disabled: true },
      { label: 'Disabled (indeterminate)', checked: false, disabled: true, indeterminate: true },
    ];

    return `
      <div style="display: flex; flex-direction: column; gap: 1rem; max-width: 400px;">
        <p style="font-size: 0.875rem; color: var(--gray-600); margin-bottom: 1rem;">
          Interact with the checkboxes to see state changes. Hover and focus states are animated.
        </p>
        ${states
          .map(
            (state) => `
          <div style="display: flex; align-items: center; padding: 0.75rem; background-color: var(--gray-50); border-radius: 4px;">
            ${checkboxTwig({
              name: 'state-demo',
              value: state.label.toLowerCase().replace(/\s+/g, '-'),
              label: state.label,
              ...state,
            })}
          </div>
        `
          )
          .join('')}
      </div>
    `;
  },
  parameters: {
    docs: {
      description: {
        story:
          'Demonstration of all checkbox states: default, checked, indeterminate, disabled (in all variations). Test hover and focus interactions directly in Storybook.',
      },
    },
  },
};

/**
 * Indeterminate State: Partial selection indicator
 */
export const IndeterminateState = {
  render: () => {
    return `
      <div style="max-width: 600px;">
        <p style="margin-bottom: 1.5rem; color: var(--gray-600);">
          Indeterminate state is useful for parent checkboxes that control multiple child options.
        </p>

        <div style="padding: 1.5rem; background-color: var(--gray-50); border-radius: 4px;">
          <div style="margin-bottom: 1.5rem;">
            <h4 style="margin-bottom: 1rem; font-weight: 600;">Select all amenities</h4>
            ${checkboxTwig({
              name: 'amenities-all',
              value: 'all',
              label: 'All amenities (indeterminate state)',
              indeterminate: true,
            })}
          </div>

          <div style="margin-left: 2rem; display: flex; flex-direction: column; gap: 0.75rem;">
            <h5 style="margin-bottom: 0.5rem; font-size: 0.875rem; font-weight: 600; color: var(--gray-700);">Sub-options:</h5>
            ${checkboxTwig({ name: 'amenities', value: 'gym', label: 'Fitness Center', checked: true })}
            ${checkboxTwig({ name: 'amenities', value: 'pool', label: 'Swimming Pool', checked: true })}
            ${checkboxTwig({ name: 'amenities', value: 'spa', label: 'Spa & Wellness', checked: false })}
            ${checkboxTwig({ name: 'amenities', value: 'concierge', label: 'Concierge Service', checked: false })}
          </div>
        </div>
      </div>
    `;
  },
  parameters: {
    docs: {
      description: {
        story:
          'The indeterminate state indicates a parent checkbox with mixed selection status (some but not all child options selected). Useful for hierarchical selection interfaces.',
      },
    },
  },
};

/**
 * Color + Size Combined: Mixed variations
 */
export const ColorAndSizeCombined = {
  render: () => {
    const combinations = [
      { color: 'primary', size: 'sm' },
      { color: 'success', size: 'md' },
      { color: 'warning', size: 'lg' },
      { color: 'danger', size: 'xl' },
    ];

    return `
      <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        ${combinations
          .map(
            (combo) => `
          <div>
            <h4 style="margin-bottom: 0.75rem; font-size: 0.875rem; font-weight: 600; color: var(--gray-700);">${combo.color} + ${combo.size}</h4>
            <div style="display: flex; gap: 1.5rem; padding: 1rem; background-color: var(--gray-50); border-radius: 4px;">
              ${checkboxTwig({
                name: `combo-${combo.color}${combo.size}`,
                value: 'unchecked',
                label: 'Unchecked',
                color: combo.color,
                size: combo.size,
                checked: false,
              })}
              ${checkboxTwig({
                name: `combo-${combo.color}${combo.size}`,
                value: 'checked',
                label: 'Checked',
                color: combo.color,
                size: combo.size,
                checked: true,
              })}
            </div>
          </div>
        `
          )
          .join('')}
      </div>
    `;
  },
  parameters: {
    docs: {
      description: {
        story:
          'Examples combining both color and size variants to demonstrate flexible styling options.',
      },
    },
  },
};
