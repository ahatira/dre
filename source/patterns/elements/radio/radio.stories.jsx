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
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Unchecked (default icon)</p>
        ${radioTwig({ name: 'demo1', value: '1', label: 'Option 1', checked: false })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Checked (filled green icon)</p>
        ${radioTwig({ name: 'demo2', value: '2', label: 'Option 2', checked: true })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Disabled unchecked (50% opacity)</p>
        ${radioTwig({ name: 'demo3', value: '3', label: 'Option 3', checked: false, disabled: true })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Disabled checked (50% opacity)</p>
        ${radioTwig({ name: 'demo4', value: '4', label: 'Option 4', checked: true, disabled: true })}
      </div>
    </div>
  `,
};

export const UseCases = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-8);">
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-2);">Single Choice Selection</h3>
        <div style="display: flex; flex-direction: column; gap: var(--size-3);">
          ${radioTwig({ name: 'plan', value: 'basic', label: 'Basic Plan - Free', checked: false })}
          ${radioTwig({ name: 'plan', value: 'premium', label: 'Premium Plan - $9.99/month', checked: true })}
          ${radioTwig({ name: 'plan', value: 'enterprise', label: 'Enterprise Plan - Contact us', checked: false })}
        </div>
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-2);">Account Type</h3>
        <div style="display: flex; flex-direction: column; gap: var(--size-3);">
          ${radioTwig({ name: 'type', value: 'individual', label: 'Individual', checked: true })}
          ${radioTwig({ name: 'type', value: 'business', label: 'Business', checked: false })}
        </div>
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-2);">Payment Method</h3>
        <div style="display: flex; flex-direction: column; gap: var(--size-3);">
          ${radioTwig({ name: 'payment', value: 'card', label: 'Credit Card', checked: true })}
          ${radioTwig({ name: 'payment', value: 'paypal', label: 'PayPal', checked: false })}
          ${radioTwig({ name: 'payment', value: 'bank', label: 'Bank Transfer', checked: false, disabled: true })}
        </div>
      </div>
    </div>
  `,
};
