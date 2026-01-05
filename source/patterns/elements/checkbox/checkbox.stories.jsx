import checkboxTwig from './checkbox.twig';
import data from './checkbox.yml';

export default {
  title: 'Elements/Checkbox',
  render: (args) => checkboxTwig(args),
  args: data,
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component:
          'Native checkbox input with custom SVG icon styling. Supports checked/unchecked/indeterminate/disabled states with smooth animations. Fully accessible with keyboard navigation and WCAG 2.2 AA compliance.',
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
    attributes: {
      description:
        'Additional HTML attributes for Drupal integration (ARIA, data-*, extra classes).',
      control: { type: 'object' },
      table: {
        category: 'Accessibility',
        type: { summary: 'object' },
      },
    },
  },
};

/**
 * Par Défaut: Interactive playground with all controls
 */
export const ParDéfaut = {
  name: 'Default',
  args: {
    name: 'property-features',
    value: 'parking',
    label: 'Parking available',
    checked: false,
    indeterminate: false,
    disabled: false,
  },
};

/**
 * États: All checkbox states in a compact showcase
 */
export const États = {
  name: 'States',
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-6); max-width: 600px;">
      <div>
        <h4 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-3); font-weight: 600; text-transform: uppercase; color: var(--gray-700); letter-spacing: 0.05em;">Interactive States</h4>
        <div style="display: flex; flex-direction: column; gap: var(--size-3); padding: var(--size-4); background-color: var(--gray-50); border-radius: var(--radius-2);">
          ${checkboxTwig({ name: 'state-1', value: 'unchecked', label: 'Unchecked (default)', checked: false })}
          ${checkboxTwig({ name: 'state-2', value: 'checked', label: 'Checked (selected)', checked: true })}
          ${checkboxTwig({ name: 'state-3', value: 'indeterminate', label: 'Indeterminate (partial selection)', indeterminate: true })}
        </div>
      </div>

      <div>
        <h4 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-3); font-weight: 600; text-transform: uppercase; color: var(--gray-700); letter-spacing: 0.05em;">Disabled States</h4>
        <div style="display: flex; flex-direction: column; gap: var(--size-3); padding: var(--size-4); background-color: var(--gray-50); border-radius: var(--radius-2);">
          ${checkboxTwig({ name: 'state-4', value: 'disabled-unchecked', label: 'Disabled (unchecked)', checked: false, disabled: true })}
          ${checkboxTwig({ name: 'state-5', value: 'disabled-checked', label: 'Disabled (checked)', checked: true, disabled: true })}
          ${checkboxTwig({ name: 'state-6', value: 'disabled-indeterminate', label: 'Disabled (indeterminate)', indeterminate: true, disabled: true })}
        </div>
      </div>
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story:
          'All possible checkbox states: unchecked, checked, indeterminate (partial selection), and their disabled variants. Interactive states show smooth hover transitions.',
      },
    },
  },
};

/**
 * Indeterminé: Hierarchical selection with indeterminate parent
 */
export const Indeterminé = {
  name: 'Indeterminate',
  render: () => `
    <div style="max-width: 600px;">
      <div style="margin-bottom: var(--size-5); padding: var(--size-4); background-color: var(--gray-100); border-left: 4px solid var(--primary); border-radius: var(--radius-2);">
        <p style="font-size: var(--font-size-3); color: var(--gray-700); margin: 0;">
          <strong>Indeterminate state</strong> indicates a parent checkbox where <strong>some (but not all) children are selected</strong>. Common in hierarchical filters and permission systems.
        </p>
      </div>

      <div style="padding: var(--size-5); background-color: var(--gray-50); border-radius: var(--radius-3); border: 1px solid var(--border-light);">
        <div style="margin-bottom: var(--size-5);">
          ${checkboxTwig({
            name: 'amenities-all',
            value: 'all',
            label: 'Select all amenities (2/4 selected)',
            indeterminate: true,
          })}
        </div>

        <div style="margin-left: var(--size-5); padding-left: var(--size-4); border-left: 2px solid var(--border-light);">
          <div style="display: flex; flex-direction: column; gap: var(--size-3);">
            ${checkboxTwig({ name: 'amenities', value: 'gym', label: 'Fitness Center', checked: true })}
            ${checkboxTwig({ name: 'amenities', value: 'pool', label: 'Swimming Pool', checked: true })}
            ${checkboxTwig({ name: 'amenities', value: 'spa', label: 'Spa & Wellness', checked: false })}
            ${checkboxTwig({ name: 'amenities', value: 'concierge', label: '24/7 Concierge', checked: false })}
          </div>
        </div>
      </div>
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story:
          'Demonstrates indeterminate state in a parent-child relationship. The parent checkbox shows a horizontal line icon when some (but not all) children are selected. Clicking the indeterminate checkbox clears the state.',
      },
    },
  },
};
