import checkboxTwig from './checkbox.twig';
import data from './checkbox.yml';

export default {
  title: 'Elements/Checkbox',
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component:
          'Accessible checkbox compliant with the Design System.\n\n' +
          '- **Content**: native input + optional label linked by `id`/`for`.\n' +
          '- **States**: checked, disabled — adapted styles and cursor.\n' +
          '- **Icon**: rendered via pseudo-elements (icon font), without additional markup.\n' +
          '- **Accessibility**: keyboard target, focus visible; native ARIA announcement; label recommended for understanding.\n' +
          '- **Tokens**: colors, spacing, borders and typography only via tokens.\n' +
          '- **Minimal markup**: base class applies default styles; modifiers added only if necessary.',
      },
    },
  },
  argTypes: {
    // Content
    name: {
      control: 'text',
      description: 'Input name attribute',
      table: {
        category: 'Content',
        type: { summary: 'string', required: true },
      },
    },
    value: {
      control: 'text',
      description: 'Input value',
      table: {
        category: 'Content',
        type: { summary: 'string', required: true },
      },
    },
    label: {
      control: 'text',
      description: 'Label text (optional)',
      table: {
        category: 'Content',
        type: { summary: 'string' },
      },
    },
    // Appearance
    id: {
      control: 'text',
      description: 'Input ID (auto-generated if empty)',
      table: {
        category: 'Appearance',
        type: { summary: 'string' },
      },
    },
    // Behavior
    checked: {
      control: 'boolean',
      description: 'Checked state',
      table: {
        category: 'Behavior',
        type: { summary: 'boolean' },
        defaultValue: { summary: false },
      },
    },
    disabled: {
      control: 'boolean',
      description: 'Disabled state',
      table: {
        category: 'Behavior',
        type: { summary: 'boolean' },
        defaultValue: { summary: false },
      },
    },
  },
};

// Story: Default
export const Default = {
  render: (args) => checkboxTwig(args),
  args: { ...data },
};

// Story: NoLabel (accessibilité)
export const NoLabel = {
  render: () => checkboxTwig({ ...data, label: '' }),
};

// Story: WithLongLabel (multi-lignes)
export const WithLongLabel = {
  render: () => checkboxTwig({ ...data, label: 'Lorem ipsum dolor sit amet consectetur. Cursus posuere et egestas id metus sit.' }),
};

// Story: AllStates (checked/unchecked, enabled/disabled)
export const AllStates = {
  render: () => `
    <div style="display: flex; gap: var(--size-4); flex-wrap: wrap;">
      ${checkboxTwig({ ...data, checked: false, disabled: false, label: 'Unchecked' })}
      ${checkboxTwig({ ...data, checked: true, disabled: false, label: 'Checked' })}
      ${checkboxTwig({ ...data, checked: false, disabled: true, label: 'Disabled' })}
      ${checkboxTwig({ ...data, checked: true, disabled: true, label: 'Checked Disabled' })}
    </div>
  `,
};

// Story: AllLabels (label, no label, long label)
export const AllLabels = {
  render: () => `
    <div style="display: flex; gap: var(--size-4); flex-wrap: wrap;">
      ${checkboxTwig({ ...data, label: 'Option label' })}
      ${checkboxTwig({ ...data, label: '' })}
      ${checkboxTwig({ ...data, label: 'Lorem ipsum dolor sit amet consectetur. Cursus posuere et egestas id metus sit.' })}
    </div>
  `,
};

// Story: AllCombinations (checked/unchecked × enabled/disabled × label/no label)
export const AllCombinations = {
  render: () => `
    <div style="display: grid; gap: var(--size-4); grid-template-columns: repeat(4, minmax(0, 1fr));">
      <div><strong>Checked + Label</strong><br/>${checkboxTwig({ ...data, checked: true, disabled: false, label: 'Checked' })}</div>
      <div><strong>Unchecked + Label</strong><br/>${checkboxTwig({ ...data, checked: false, disabled: false, label: 'Unchecked' })}</div>
      <div><strong>Checked + NoLabel</strong><br/>${checkboxTwig({ ...data, checked: true, disabled: false, label: '' })}</div>
      <div><strong>Unchecked + NoLabel</strong><br/>${checkboxTwig({ ...data, checked: false, disabled: false, label: '' })}</div>
      <div><strong>Checked + Disabled + Label</strong><br/>${checkboxTwig({ ...data, checked: true, disabled: true, label: 'Checked Disabled' })}</div>
      <div><strong>Unchecked + Disabled + Label</strong><br/>${checkboxTwig({ ...data, checked: false, disabled: true, label: 'Disabled' })}</div>
      <div><strong>Checked + Disabled + NoLabel</strong><br/>${checkboxTwig({ ...data, checked: true, disabled: true, label: '' })}</div>
      <div><strong>Unchecked + Disabled + NoLabel</strong><br/>${checkboxTwig({ ...data, checked: false, disabled: true, label: '' })}</div>
    </div>
  `,
};

// Story: Group (usage réel)
export const Group = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-6);">
      <div>
        <strong>Options:</strong><br/><br/>
        ${checkboxTwig({ name: 'group1', value: '1', label: 'Option label', checked: false })}
        ${checkboxTwig({ name: 'group1', value: '2', label: 'Option label', checked: true })}
      </div>
      <div>
        <strong>Long label:</strong><br/><br/>
        ${checkboxTwig({ name: 'group2', value: '1', label: 'Lorem ipsum dolor sit amet consectetur. Cursus posuere et egestas id metus sit.', checked: false })}
        ${checkboxTwig({ name: 'group2', value: '2', label: 'Lorem ipsum dolor sit amet consectetur. Cursus posuere et egestas id metus sit.', checked: true })}
      </div>
    </div>
  `,
};
