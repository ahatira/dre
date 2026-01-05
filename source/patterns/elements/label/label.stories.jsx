import labelTwig from './label.twig';
import data from './label.yml';

export default {
  title: 'Elements/Label',
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component:
          'Form field label with semantic <label> binding, required indicator (asterisk + SR text), and disabled state. Essential building block for form components.',
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
  },
};

export const Default = {
  render: (args) => labelTwig(args),
  args: { ...data.props },
};

export const States = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-4);">
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-2); color: var(--gray-600);">Default</p>
        ${labelTwig({ text: 'Adresse du bien', forId: 'field-address' })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-2); color: var(--gray-600);">Required (with asterisk + screen reader text)</p>
        ${labelTwig({ text: 'Email de contact', forId: 'field-email', required: true })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-2); color: var(--gray-600);">Disabled (70% opacity, muted color)</p>
        ${labelTwig({ text: 'Référence cadastrale', forId: 'field-ref', disabled: true })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-2); color: var(--gray-600);">Required + Disabled</p>
        ${labelTwig({ text: 'Identifiant propriétaire', forId: 'field-id', required: true, disabled: true })}
      </div>
    </div>
  `,
};

export const WithFormFields = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-6);">
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-4);">Text input (required)</h3>
        ${labelTwig({ text: 'Nom complet', forId: 'name-input', required: true })}
        <input type="text" id="name-input" placeholder="Jean Dupont" style="width: 100%; max-width: 400px; padding: var(--size-2); border: 1px solid var(--border-default); border-radius: var(--radius-1); font-family: var(--font-sans); font-size: var(--font-size-2);" />
      </div>

      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-4);">Textarea (optional)</h3>
        ${labelTwig({ text: 'Description du bien', forId: 'description-input' })}
        <textarea id="description-input" rows="4" placeholder="Décrivez les caractéristiques du bien immobilier..." style="width: 100%; max-width: 400px; padding: var(--size-2); border: 1px solid var(--border-default); border-radius: var(--radius-1); font-family: var(--font-sans); font-size: var(--font-size-2); resize: vertical;"></textarea>
      </div>

      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-4);">Select (required)</h3>
        ${labelTwig({ text: 'Type de bien', forId: 'type-select', required: true })}
        <select id="type-select" style="width: 100%; max-width: 400px; padding: var(--size-2); border: 1px solid var(--border-default); border-radius: var(--radius-1); font-family: var(--font-sans); font-size: var(--font-size-2); cursor: pointer;">
          <option value="">Sélectionnez un type</option>
          <option value="appartement">Appartement</option>
          <option value="maison">Maison</option>
          <option value="bureau">Bureau</option>
          <option value="terrain">Terrain</option>
        </select>
      </div>

      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-4);">Disabled field</h3>
        ${labelTwig({ text: 'Identifiant unique (lecture seule)', forId: 'locked-input', disabled: true })}
        <input type="text" id="locked-input" value="BNP-2025-00123" disabled style="width: 100%; max-width: 400px; padding: var(--size-2); border: 1px solid var(--border-light); border-radius: var(--radius-1); font-family: var(--font-sans); font-size: var(--font-size-2); background: var(--gray-50); color: var(--text-disabled); cursor: not-allowed;" />
      </div>
    </div>
  `,
};
