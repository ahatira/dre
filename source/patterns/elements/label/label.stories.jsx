import labelTwig from './label.twig';
import data from './label.yml';

export default {
  title: 'Elements/Label',
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component:
          'Accessible form field label with required indicator, disabled state, and flexible attributes. Uses semantic <label> binding and supports screen reader text via tokens and structured markup.',
      },
    },
  },
  args: { ...data.props },
  argTypes: {
    // Content
    text: {
      description: 'Label text content',
      control: { type: 'text' },
      table: {
        category: 'Content',
        type: { summary: 'string', required: true },
        defaultValue: { summary: 'Label' },
      },
    },
    // Behavior
    forId: {
      description: 'ID of the associated form field for proper label-input binding',
      control: { type: 'text' },
      table: {
        category: 'Behavior',
        type: { summary: 'string' },
        defaultValue: { summary: '' },
      },
    },
    required: {
      description: 'Adds visual asterisk (*) and accessible "required" text for screen readers',
      control: { type: 'boolean' },
      table: {
        category: 'Behavior',
        type: { summary: 'boolean' },
        defaultValue: { summary: 'false' },
      },
    },
    disabled: {
      description: 'Disabled state with reduced opacity (70%) and muted text color',
      control: { type: 'boolean' },
      table: {
        category: 'Behavior',
        type: { summary: 'boolean' },
        defaultValue: { summary: 'false' },
      },
    },
    // Structure
    attributes: {
      description: 'Additional HTML attributes object for custom styling or data attributes',
      control: { type: 'object' },
      table: {
        category: 'Structure',
        type: { summary: 'object' },
        defaultValue: { summary: '{}' },
      },
    },
  },
};

export const Default = {
  render: (args) => labelTwig(args),
  args: { ...data.props },
};

export const AllStates = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-4);">
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Default</p>
        ${labelTwig({ text: 'Your name', forId: 'field-1' })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Required (with asterisk + screen reader text)</p>
        ${labelTwig({ text: 'Your email', forId: 'field-2', required: true })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Disabled (70% opacity, muted color)</p>
        ${labelTwig({ text: 'Disabled field', forId: 'field-3', disabled: true })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Required + Disabled</p>
        ${labelTwig({ text: 'Required disabled', forId: 'field-4', required: true, disabled: true })}
      </div>
    </div>
  `,
};

export const UseCases = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-6);">
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-2);">With text input</h3>
        ${labelTwig({ text: 'Full name', forId: 'name-input', required: true })}
        <input type="text" id="name-input" style="width: 100%; max-width: 300px; padding: var(--size-2); border: 1px solid var(--gray-300); border-radius: var(--radius-1);" />
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-2);">With textarea</h3>
        ${labelTwig({ text: 'Description', forId: 'description-input' })}
        <textarea id="description-input" rows="3" style="width: 100%; max-width: 300px; padding: var(--size-2); border: 1px solid var(--gray-300); border-radius: var(--radius-1);"></textarea>
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-2);">Disabled field</h3>
        ${labelTwig({ text: 'Locked field', forId: 'locked-input', disabled: true })}
        <input type="text" id="locked-input" disabled style="width: 100%; max-width: 300px; padding: var(--size-2); border: 1px solid var(--gray-300); border-radius: var(--radius-1); opacity: 0.7;" />
      </div>
    </div>
  `,
};
