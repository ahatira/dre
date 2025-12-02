import radioTwig from './radio.twig';
import data from './radio.yml';

export default {
  title: 'Elements/Radio',
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component: `Semantic radio control for single selection within a group.
Supports checked/disabled states, focus-visible, and accessible labeling.`,
      },
    },
  },
  argTypes: {
    // Content
    label: {
      description: 'Visible label text displayed next to radio button',
      control: { type: 'text' },
      table: {
        category: 'Content',
        type: { summary: 'string' },
        defaultValue: { summary: 'Option label' },
      },
    },
    value: {
      description: 'Unique value for this radio button within the group',
      control: { type: 'text' },
      table: {
        category: 'Content',
        type: { summary: 'string', required: true },
        defaultValue: { summary: '1' },
      },
    },
    // Behavior
    name: {
      description: 'Radio group name (all radios with same name allow single selection)',
      control: { type: 'text' },
      table: {
        category: 'Behavior',
        type: { summary: 'string', required: true },
        defaultValue: { summary: 'option' },
      },
    },
    checked: {
      description: 'Checked state (only one radio per group should be checked)',
      control: { type: 'boolean' },
      table: {
        category: 'Behavior',
        type: { summary: 'boolean' },
        defaultValue: { summary: 'false' },
      },
    },
    disabled: {
      description: 'Disabled state (50% opacity, not-allowed cursor, prevents interaction)',
      control: { type: 'boolean' },
      table: {
        category: 'Behavior',
        type: { summary: 'boolean' },
        defaultValue: { summary: 'false' },
      },
    },
  },
  args: { ...data },
};

export const Default = {
  render: (args) => radioTwig(args),
  args: { ...data },
};

// === Grouped Showcase Stories ===

export const AllStates = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-4);">
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Unchecked (gray border circle)</p>
        ${radioTwig({ name: 'demo1', value: '1', label: 'Option label', checked: false })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Checked (green filled circle with white dot)</p>
        ${radioTwig({ name: 'demo2', value: '2', label: 'Option label', checked: true })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Disabled unchecked (50% opacity)</p>
        ${radioTwig({ name: 'demo3', value: '3', label: 'Option label', checked: false, disabled: true })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Disabled checked (50% opacity)</p>
        ${radioTwig({ name: 'demo4', value: '4', label: 'Option label', checked: true, disabled: true })}
      </div>
    </div>
  `,
};

export const UseCases = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-8);">
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-2);">Property Type Selection</h3>
        <div style="display: flex; flex-direction: column; gap: var(--size-3);">
          ${radioTwig({ name: 'property-type', value: 'apartment', label: 'Apartment', checked: false })}
          ${radioTwig({ name: 'property-type', value: 'house', label: 'House', checked: true })}
          ${radioTwig({ name: 'property-type', value: 'commercial', label: 'Commercial Property', checked: false })}
          ${radioTwig({ name: 'property-type', value: 'land', label: 'Land / Plot', checked: false })}
        </div>
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-2);">Listing Status</h3>
        <div style="display: flex; flex-direction: column; gap: var(--size-3);">
          ${radioTwig({ name: 'status', value: 'sale', label: 'For Sale', checked: true })}
          ${radioTwig({ name: 'status', value: 'rent', label: 'For Rent', checked: false })}
          ${radioTwig({ name: 'status', value: 'sold', label: 'Sold', checked: false, disabled: true })}
        </div>
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-2);">Mortgage Type</h3>
        <div style="display: flex; flex-direction: column; gap: var(--size-3);">
          ${radioTwig({ name: 'mortgage', value: 'fixed', label: 'Fixed Rate Mortgage', checked: true })}
          ${radioTwig({ name: 'mortgage', value: 'variable', label: 'Variable Rate Mortgage', checked: false })}
          ${radioTwig({ name: 'mortgage', value: 'interest', label: 'Interest Only', checked: false })}
        </div>
      </div>
    </div>
  `,
};
