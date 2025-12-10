import radioTwig from './radio.twig';
import data from './radio.yml';

export default {
  title: 'Elements/Radio',
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component:
          'Individual radio button for single selection within a group. Uses SVG mask-image technique (aligned with Checkbox). Typically used within the `radios` molecule for complete form fields with legend and validation.',
      },
    },
  },
  argTypes: {
    // Content
    name: {
      description:
        'Input name attribute (group identifier - all radios in same group must share this name)',
      control: 'text',
      table: { category: 'Content' },
    },
    value: {
      description: 'Input value attribute (unique value for this specific option)',
      control: 'text',
      table: { category: 'Content' },
    },
    id: {
      description: 'Input ID for label association (auto-generated from name + value if omitted)',
      control: 'text',
      table: { category: 'Content' },
    },
    label: {
      description: 'Label text displayed next to radio circle',
      control: 'text',
      table: { category: 'Content' },
    },

    // Behavior
    checked: {
      description: 'Whether this radio is checked (only one per group can be checked)',
      control: 'boolean',
      table: { category: 'Behavior', defaultValue: { summary: false } },
    },
    disabled: {
      description: 'Whether this radio is disabled (50% opacity, not-allowed cursor)',
      control: 'boolean',
      table: { category: 'Behavior', defaultValue: { summary: false } },
    },

    // Attributes
    attributes: {
      description: 'Drupal attributes object for wrapper <label> element',
      control: false,
      table: { category: 'Attributes' },
    },
  },
  render: (args) => radioTwig(args),
};

/**
 * Default
 * Standard unchecked state
 */
export const Default = {
  args: data,
};

/**
 * States
 * All possible radio states: normal, checked, disabled, disabled+checked
 */
export const States = {
  render: () => {
    return `
      <div style="display: flex; flex-direction: column; gap: var(--size-4);">
        ${radioTwig({
          name: 'states_demo',
          value: 'unchecked',
          id: 'state-unchecked',
          label: 'Unchecked (normal)',
          checked: false,
        })}
        ${radioTwig({
          name: 'states_demo',
          value: 'checked',
          id: 'state-checked',
          label: 'Checked (selected)',
          checked: true,
        })}
        ${radioTwig({
          name: 'states_demo',
          value: 'disabled',
          id: 'state-disabled',
          label: 'Disabled (unchecked)',
          disabled: true,
        })}
        ${radioTwig({
          name: 'states_demo_disabled',
          value: 'disabled-checked',
          id: 'state-disabled-checked',
          label: 'Disabled + Checked',
          checked: true,
          disabled: true,
        })}
      </div>
    `;
  },
};

/**
 * In Context
 * Real Estate: Budget selection in a form fieldset
 */
export const InContext = {
  render: () => {
    const budgetRanges = [
      { value: 'low', label: 'Moins de 200 000 €', checked: false },
      { value: 'medium', label: '200 000 € - 400 000 €', checked: true },
      { value: 'high', label: 'Plus de 400 000 €', checked: false },
    ];

    return `
      <fieldset style="border: 1px solid var(--gray-300); padding: var(--size-4); border-radius: var(--radius-2);">
        <legend style="padding: 0 var(--size-2); font-size: var(--font-size-2); font-weight: var(--font-weight-600); color: var(--gray-900);">
          Votre budget
        </legend>
        <div style="display: flex; flex-direction: column; gap: var(--size-3); margin-top: var(--size-3);">
          ${budgetRanges
            .map((range) =>
              radioTwig({
                name: 'budget_range',
                value: range.value,
                id: `budget-${range.value}`,
                label: range.label,
                checked: range.checked,
              })
            )
            .join('')}
        </div>
      </fieldset>
    `;
  },
};
