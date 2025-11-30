import checkboxTwig from './checkbox.twig';
import data from './checkbox.yml';

export default {
  title: 'Elements/Checkbox',
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component: `Native checkbox input with accessible label and custom styling.

**Key Features:**
- Native HTML checkbox input (keyboard + screen reader compatible)
- Optional label linked via for/id attributes
- Custom visual box with checkmark icon (pseudo-element)
- 2 states: checked, disabled (unchecked default)
- 20×20px touch target with 2px border
- Pure token implementation (colors, spacing, typography, borders)

**Usage Guidelines:**
- Always provide label text when possible (accessibility best practice)
- Use for independent options (vs radio for mutually exclusive)
- Group related checkboxes under fieldset with legend
- Auto-generate ID from name+value if not provided
- Prefer vertical stacking for checkbox groups
- Label should describe what checking means ("Enable notifications" not "Notifications")

**Accessibility:**
- Native input ensures keyboard navigation (Space to toggle, Tab to focus)
- Label wraps input: clicking label toggles checkbox
- aria-disabled="true" on disabled checkboxes
- Focus visible outline (2px blue) for keyboard users
- Screen readers announce checked/unchecked state automatically
- Icon checkmark aria-hidden (decorative only)
- Label text required for understanding (visually hidden if omitted but present for AT)

**Design Tokens:**
- Sizing: 20px box (--size-5), 8px label gap (--size-2)
- Colors: --ps-color-border-default (unchecked), --brand-primary (checked), --blue-500 (focus)
- Typography: --font-size-0 (14px), --font-weight-400, line-height 1.5
- Border: --border-size-2 (2px), --radius-1 (2px border-radius)
- State: 50% opacity disabled, 2px outline focus

**Do Not:**
- Use without label text (accessibility violation)
- Nest checkboxes inside clickable containers (interaction conflict)
- Apply disabled without visual + ARIA indication
- Hardcode colors, sizes or spacing`,
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
