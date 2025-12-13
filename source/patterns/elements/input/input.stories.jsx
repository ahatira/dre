import inputTwig from './input.twig';
import inputData from './input.yml';

export default {
  title: 'Elements/Input',
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component:
          'Base input field (ATOM). Without label, icon, or helper. For complete input, use Form-element (Molecule).',
      },
    },
  },
  argTypes: {
    value: {
      control: 'text',
      description: 'Current field value',
      table: {
        category: 'Content',
        type: { summary: 'string' },
        defaultValue: { summary: '""' },
      },
    },
    placeholder: {
      control: 'text',
      description: 'Text displayed when field is empty',
      table: {
        category: 'Content',
        type: { summary: 'string' },
        defaultValue: { summary: '""' },
      },
    },
    type: {
      control: 'select',
      options: ['text', 'email', 'password', 'number', 'search', 'tel', 'url'],
      description: 'HTML5 input type',
      table: {
        category: 'Content',
        type: { summary: 'string' },
        defaultValue: { summary: '"text"' },
      },
    },
    state: {
      control: 'select',
      options: [null, 'error', 'success', 'warning'],
      description: 'Validation state of the field',
      table: {
        category: 'State',
        type: { summary: 'null | "error" | "success" | "warning"' },
        defaultValue: { summary: 'null' },
      },
    },
    disabled: {
      control: 'boolean',
      description: 'Disable the field (read-only, non-editable)',
      table: {
        category: 'State',
        type: { summary: 'boolean' },
        defaultValue: { summary: 'false' },
      },
    },
    required: {
      control: 'boolean',
      description: 'Mark field as required',
      table: {
        category: 'State',
        type: { summary: 'boolean' },
        defaultValue: { summary: 'false' },
      },
    },
    name: {
      control: 'text',
      description: 'Name attribute (for form submission)',
      table: {
        category: 'Accessibility',
        type: { summary: 'string' },
        defaultValue: { summary: '"email"' },
      },
    },
    id: {
      control: 'text',
      description: 'ID attribute (for label association)',
      table: {
        category: 'Accessibility',
        type: { summary: 'string' },
        defaultValue: { summary: 'null' },
      },
    },
    autocomplete: {
      control: 'text',
      description: 'HTML5 autocomplete (email, password, current-password, etc.)',
      table: {
        category: 'Accessibility',
        type: { summary: 'string' },
        defaultValue: { summary: 'null' },
      },
    },
  },
};

const Template = (args) => inputTwig(args);

// ============ MOCKUP STATES ============

export const Default = Template.bind({});
Default.args = { ...inputData };

export const Placeholder = Template.bind({});
Placeholder.args = { ...inputData, value: '', placeholder: 'Placeholder' };

export const Focus = Template.bind({});
Focus.args = { ...inputData, value: 'Value' };
Focus.parameters = {
  docs: {
    description: {
      story: 'Visible focus: 2px black border (WCAG 2.2 AA)',
    },
  },
};

export const Success = Template.bind({});
Success.args = { ...inputData, value: 'Value', state: 'success' };

export const ErrorState = Template.bind({});
ErrorState.args = { ...inputData, value: 'Value', state: 'error' };

export const Warning = Template.bind({});
Warning.args = { ...inputData, value: 'Value', state: 'warning' };

export const DisabledPlaceholder = {
  render: Template,
  args: {
    ...inputData,
    value: '',
    placeholder: 'Placeholder',
    disabled: true,
  },
  name: 'Disabled (placeholder)',
};

export const DisabledValue = {
  render: Template,
  args: { ...inputData, value: 'Value', disabled: true },
  name: 'Disabled (value)',
};

// ============ TYPES ============

export const TypeEmail = {
  render: Template,
  args: {
    ...inputData,
    type: 'email',
    placeholder: 'you@example.com',
    autocomplete: 'email',
    value: '',
  },
  name: 'Type: Email',
};

export const TypePassword = {
  render: Template,
  args: {
    ...inputData,
    type: 'password',
    placeholder: 'Password',
    autocomplete: 'current-password',
    value: '',
  },
  name: 'Type: Password',
};

export const TypeNumber = {
  render: Template,
  args: { ...inputData, type: 'number', placeholder: 'Ex: 250000', value: '' },
  name: 'Type: Number',
};

export const TypeSearch = {
  render: Template,
  args: { ...inputData, type: 'search', placeholder: 'Search...', value: '' },
  name: 'Type: Search',
};

// ============ SHOWCASE ============

export const AllStates = {
  render: () => {
    const states = [
      { label: 'Default', args: { ...inputData, value: 'Value' } },
      { label: 'Placeholder', args: { ...inputData, value: '', placeholder: 'Placeholder' } },
      { label: 'Focus', args: { ...inputData, value: 'Value' } },
      { label: 'Success', args: { ...inputData, value: 'Value', state: 'success' } },
      { label: 'Error', args: { ...inputData, value: 'Value', state: 'error' } },
      { label: 'Warning', args: { ...inputData, value: 'Value', state: 'warning' } },
      {
        label: 'Disabled (placeholder)',
        args: { ...inputData, value: '', placeholder: 'Placeholder', disabled: true },
      },
      { label: 'Disabled (value)', args: { ...inputData, value: 'Value', disabled: true } },
    ];

    return `
      <div style="display: flex; flex-direction: column; gap: var(--size-4); max-width: 500px;">
        ${states
          .map(
            (state) => `
          <div>
            <p style="margin-bottom: var(--size-2); font-weight: 600; font-size: 12px; color: var(--text-secondary);">
              ${state.label}
            </p>
            ${inputTwig(state.args)}
          </div>
        `
          )
          .join('')}
      </div>
    `;
  },
  name: 'Showcase: All States',
};

/**
 * Real Estate context examples
 */
export const RealEstateContext = {
  render: () => {
    return `
      <div style="display: flex; flex-direction: column; gap: var(--size-10);">
        <div>
          <h4 style="margin: 0 0 var(--size-4); color: var(--text-primary); font-size: var(--font-size-3); font-weight: 600;">Search Location (text)</h4>
          <div style="max-width: 500px;">
            ${inputTwig({
              type: 'text',
              name: 'location',
              placeholder: 'Paris, Lyon, Marseille...',
              value: '',
              id: 'search-location',
            })}
            <p style="margin-top: var(--size-2); font-size: var(--font-size-0); color: var(--text-secondary);">
              Usage: Widget de recherche de biens - champ "Où ?"
            </p>
          </div>
        </div>

        <div>
          <h4 style="margin: 0 0 var(--size-4); color: var(--text-primary); font-size: var(--font-size-3); font-weight: 600;">Budget Range (number)</h4>
          <div style="max-width: 500px; display: grid; grid-template-columns: 1fr 1fr; gap: var(--size-3);">
            <div>
              ${inputTwig({
                type: 'number',
                name: 'budget_min',
                placeholder: 'Min (€)',
                value: '',
                id: 'budget-min',
              })}
            </div>
            <div>
              ${inputTwig({
                type: 'number',
                name: 'budget_max',
                placeholder: 'Max (€)',
                value: '',
                id: 'budget-max',
              })}
            </div>
            <p style="grid-column: 1 / -1; margin: var(--size-2) 0 0; font-size: var(--font-size-0); color: var(--text-secondary);">
              Usage: Filtres de recherche - fourchette de prix
            </p>
          </div>
        </div>

        <div>
          <h4 style="margin: 0 0 var(--size-4); color: var(--text-primary); font-size: var(--font-size-3); font-weight: 600;">Surface Area (number)</h4>
          <div style="max-width: 300px;">
            ${inputTwig({
              type: 'number',
              name: 'surface',
              placeholder: 'Surface min (m²)',
              value: '75',
              id: 'surface',
            })}
            <p style="margin-top: var(--size-2); font-size: var(--font-size-0); color: var(--text-secondary);">
              Usage: Critère de recherche - surface minimale
            </p>
          </div>
        </div>

        <div>
          <h4 style="margin: 0 0 var(--size-4); color: var(--text-primary); font-size: var(--font-size-3); font-weight: 600;">Contact Form - Email (success state)</h4>
          <div style="max-width: 500px;">
            ${inputTwig({
              type: 'email',
              name: 'email',
              placeholder: 'votre.email@exemple.fr',
              value: 'client@exemple.fr',
              state: 'success',
              autocomplete: 'email',
              id: 'contact-email',
            })}
            <p style="margin-top: var(--size-2); font-size: var(--font-size-0); color: var(--success);">
              ✓ Email valide
            </p>
          </div>
        </div>

        <div>
          <h4 style="margin: 0 0 var(--size-4); color: var(--text-primary); font-size: var(--font-size-3); font-weight: 600;">Contact Form - Phone (error state)</h4>
          <div style="max-width: 500px;">
            ${inputTwig({
              type: 'tel',
              name: 'phone',
              placeholder: '+33 1 23 45 67 89',
              value: '123',
              state: 'error',
              autocomplete: 'tel',
              id: 'contact-phone',
            })}
            <p style="margin-top: var(--size-2); font-size: var(--font-size-0); color: var(--danger);">
              ✗ Format de téléphone invalide
            </p>
          </div>
        </div>

        <div>
          <h4 style="margin: 0 0 var(--size-4); color: var(--text-primary); font-size: var(--font-size-3); font-weight: 600;">Property Reference (search)</h4>
          <div style="max-width: 400px;">
            ${inputTwig({
              type: 'search',
              name: 'reference',
              placeholder: 'Ex: REF-2025-001234',
              value: '',
              id: 'property-reference',
            })}
            <p style="margin-top: var(--size-2); font-size: var(--font-size-0); color: var(--text-secondary);">
              Usage: Recherche par référence d'annonce
            </p>
          </div>
        </div>

        <div>
          <h4 style="margin: 0 0 var(--size-4); color: var(--text-primary); font-size: var(--font-size-3); font-weight: 600;">Agent Password (password, disabled)</h4>
          <div style="max-width: 400px;">
            ${inputTwig({
              type: 'password',
              name: 'password',
              placeholder: 'Mot de passe',
              value: 'secret123',
              disabled: true,
              autocomplete: 'current-password',
              id: 'agent-password',
            })}
            <p style="margin-top: var(--size-2); font-size: var(--font-size-0); color: var(--text-secondary);">
              Usage: Authentification agent (état désactivé)
            </p>
          </div>
        </div>
      </div>
    `;
  },
  parameters: {
    docs: {
      description: {
        story:
          'Common input usage patterns in real estate: search location (text), budget range (number pairs), surface area (number), contact forms (email/tel with validation states), property reference (search), and agent authentication (password).',
      },
    },
  },
};
