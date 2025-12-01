import checkboxTwig from './checkbox.twig';
import data from './checkbox.yml';

export default {
  title: 'Elements/Checkbox',
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component:
          'Native checkbox with accessible label and component-scoped CSS variables. Supports checked/disabled states with semantic color tokens.',
      },
    },
  },
  argTypes: {
    // Content
    name: {
      control: 'text',
      description: 'Input `name` attribute (required)',
      table: {
        category: 'Content',
        type: { summary: 'string' },
      },
    },
    value: {
      control: 'text',
      description: 'Input `value` attribute (required)',
      table: {
        category: 'Content',
        type: { summary: 'string' },
      },
    },
    label: {
      control: 'text',
      description: 'Label text displayed next to checkbox',
      table: {
        category: 'Content',
        type: { summary: 'string' },
      },
    },
    // Behavior
    checked: {
      control: 'boolean',
      description: 'Whether checkbox is checked',
      table: {
        category: 'Behavior',
        type: { summary: 'boolean' },
        defaultValue: { summary: 'false' },
      },
    },
    disabled: {
      control: 'boolean',
      description: 'Whether checkbox is disabled',
      table: {
        category: 'Behavior',
        type: { summary: 'boolean' },
        defaultValue: { summary: 'false' },
      },
    },
    // Accessibility
    id: {
      control: 'text',
      description: 'Unique input ID (auto-generated from name+value if omitted)',
      table: {
        category: 'Accessibility',
        type: { summary: 'string' },
      },
    },
  },
};

// Story: Default (interactive)
export const Default = {
  render: (args) => checkboxTwig(args),
  args: { ...data },
};

// Story: AllStates (showcase all checked/disabled combinations)
export const AllStates = {
  render: () => `
    <div style="display: flex; gap: var(--size-6); flex-wrap: wrap; align-items: flex-start;">
      <div style="display: flex; flex-direction: column; gap: var(--size-3);">
        <strong>Enabled</strong>
        ${checkboxTwig({ name: 'enabled', value: '1', label: 'Unchecked', checked: false, disabled: false })}
        ${checkboxTwig({ name: 'enabled', value: '2', label: 'Checked', checked: true, disabled: false })}
      </div>
      <div style="display: flex; flex-direction: column; gap: var(--size-3);">
        <strong>Disabled</strong>
        ${checkboxTwig({ name: 'disabled', value: '1', label: 'Unchecked', checked: false, disabled: true })}
        ${checkboxTwig({ name: 'disabled', value: '2', label: 'Checked', checked: true, disabled: true })}
      </div>
      <div style="display: flex; flex-direction: column; gap: var(--size-3);">
        <strong>No Label</strong>
        ${checkboxTwig({ name: 'nolabel', value: '1', label: '', checked: false, disabled: false })}
        ${checkboxTwig({ name: 'nolabel', value: '2', label: '', checked: true, disabled: false })}
      </div>
      <div style="display: flex; flex-direction: column; gap: var(--size-3); max-width: 300px;">
        <strong>Long Label</strong>
        ${checkboxTwig({ name: 'long', value: '1', label: 'Lorem ipsum dolor sit amet consectetur adipiscing elit. Cursus posuere et egestas id metus sit amet magna.', checked: false })}
      </div>
    </div>
  `,
};

// Story: Group (real-world usage in forms)
export const Group = {
  render: () => `
    <fieldset style="border: 0; padding: 0; margin: 0;">
      <legend style="font-weight: var(--font-weight-600); margin-bottom: var(--size-3);">Select your preferences</legend>
      <div style="display: flex; flex-direction: column; gap: var(--size-3);">
        ${checkboxTwig({ name: 'preferences[]', value: 'newsletter', label: 'Subscribe to newsletter', checked: true })}
        ${checkboxTwig({ name: 'preferences[]', value: 'updates', label: 'Receive product updates', checked: false })}
        ${checkboxTwig({ name: 'preferences[]', value: 'offers', label: 'Get special offers and promotions', checked: false })}
        ${checkboxTwig({ name: 'preferences[]', value: 'terms', label: 'I accept the terms and conditions', checked: false })}
      </div>
    </fieldset>
  `,
};
