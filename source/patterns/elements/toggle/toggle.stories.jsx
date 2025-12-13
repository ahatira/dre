import toggleTwig from './toggle.twig';
import data from './toggle.yml';

export default {
  title: 'Elements/Toggle',
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component: `Semantic on/off switch for immediate boolean preferences.
Supports sizes, checked/disabled states, optional ON/OFF labels, and accessibility.`,
      },
    },
  },
  argTypes: {
    // Content
    label: {
      control: { type: 'text' },
      description: 'External visible label describing the preference.',
      table: {
        category: 'Content',
        type: { summary: 'string' },
        defaultValue: { summary: 'Enable notifications' },
      },
    },
    description: {
      control: { type: 'text' },
      description: 'Optional helper text below the control providing context.',
      table: { category: 'Content', type: { summary: 'string' }, defaultValue: { summary: '' } },
    },
    onLabel: {
      control: { type: 'text' },
      description: 'Internal ON state label shown when showLabels = true.',
      table: { category: 'Content', type: { summary: 'string' }, defaultValue: { summary: 'On' } },
    },
    offLabel: {
      control: { type: 'text' },
      description: 'Internal OFF state label shown when showLabels = true.',
      table: { category: 'Content', type: { summary: 'string' }, defaultValue: { summary: 'Off' } },
    },
    // Appearance
    size: {
      control: { type: 'select' },
      options: ['small', 'medium', 'large'],
      description: 'Visual size variant (medium is default).',
      table: {
        category: 'Appearance',
        type: { summary: 'small | medium | large' },
        defaultValue: { summary: 'medium' },
      },
    },
    color: {
      control: { type: 'select' },
      options: [
        'default',
        'primary',
        'secondary',
        'success',
        'info',
        'warning',
        'danger',
        'dark',
        'light',
      ],
      description:
        'Semantic checked track color. Default uses primary; choose semantic variants for contextual toggles.',
      table: {
        category: 'Appearance',
        type: {
          summary:
            'default | primary | secondary | success | info | warning | danger | dark | light',
        },
        defaultValue: { summary: 'default' },
      },
    },
    showLabels: {
      control: { type: 'boolean' },
      description: 'Show internal ON/OFF indicators inside the track.',
      table: {
        category: 'Appearance',
        type: { summary: 'boolean' },
        defaultValue: { summary: false },
      },
    },
    // Behavior
    checked: {
      control: { type: 'boolean' },
      description: 'Switch state (true = on).',
      table: {
        category: 'Behavior',
        type: { summary: 'boolean' },
        defaultValue: { summary: false },
      },
    },
    disabled: {
      control: { type: 'boolean' },
      description: 'Disable interaction and reduce opacity.',
      table: {
        category: 'Behavior',
        type: { summary: 'boolean' },
        defaultValue: { summary: false },
      },
    },
    // Form / Structure
    name: {
      control: { type: 'text' },
      description: 'Form field name attribute.',
      table: {
        category: 'Structure',
        type: { summary: 'string' },
        defaultValue: { summary: 'notifications' },
      },
    },
  },
  args: { ...data },
};

export const Default = {
  render: (args) => toggleTwig(args),
};

// === Showcase Stories ===

export const AllStates = {
  render: () => `
    <div style="display:flex; gap:2rem; flex-wrap:wrap; align-items:flex-start;">
      <div style="display:flex; flex-direction:column; gap:0.5rem;">
        <strong style="font-size:12px; text-transform:uppercase; letter-spacing:.5px; color:var(--gray-600);">Unchecked</strong>
        ${toggleTwig({ ...data, checked: false })}
      </div>
      <div style="display:flex; flex-direction:column; gap:0.5rem;">
        <strong style="font-size:12px; text-transform:uppercase; letter-spacing:.5px; color:var(--gray-600);">Checked</strong>
        ${toggleTwig({ ...data, checked: true })}
      </div>
      <div style="display:flex; flex-direction:column; gap:0.5rem;">
        <strong style="font-size:12px; text-transform:uppercase; letter-spacing:.5px; color:var(--gray-600);">Disabled</strong>
        ${toggleTwig({ ...data, disabled: true })}
      </div>
    </div>
  `,
  parameters: {
    docs: { description: { story: 'Core interaction states: unchecked, checked, disabled.' } },
  },
};

export const AllSizes = {
  render: () => `
    <div style="display:flex; gap:2rem; flex-wrap:wrap; align-items:center;">
      ${toggleTwig({ ...data, size: 'small', label: 'Small size' })}
      ${toggleTwig({ ...data, size: 'medium', label: 'Medium size (default)' })}
      ${toggleTwig({ ...data, size: 'large', label: 'Large size' })}
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story:
          'Three standard sizes: small, medium (default), large. Use smaller sizes for dense settings and larger sizes for prominent toggles.',
      },
    },
  },
};

export const AllColors = {
  render: () => `
    <div style="display:grid; gap:1rem; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); align-items:center;">
      ${toggleTwig({ ...data, color: 'default', label: 'Default (primary)', checked: true })}
      ${toggleTwig({ ...data, color: 'primary', label: 'Primary', checked: true })}
      ${toggleTwig({ ...data, color: 'secondary', label: 'Secondary', checked: true })}
      ${toggleTwig({ ...data, color: 'success', label: 'Success', checked: true })}
      ${toggleTwig({ ...data, color: 'info', label: 'Info', checked: true })}
      ${toggleTwig({ ...data, color: 'warning', label: 'Warning', checked: true })}
      ${toggleTwig({ ...data, color: 'danger', label: 'Danger', checked: true })}
      ${toggleTwig({ ...data, color: 'dark', label: 'Dark', checked: true })}
      <div style="background: var(--gray-700); padding: var(--size-4); border-radius: var(--radius-2);
        --ps-toggle-label-color: var(--white);
        --ps-toggle-description-color: var(--gray-300);">
        ${toggleTwig({ ...data, color: 'light', label: 'Light (on dark tile)', checked: true })}
      </div>
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story:
          'Semantic color variants for the checked state. The `light` variant is shown on a dark background for contrast.',
      },
    },
  },
};

export const WithInternalLabels = {
  render: () => `
    <div style="display:flex; gap:2rem; flex-wrap:wrap; align-items:center;">
      ${toggleTwig({ ...data, showLabels: true })}
      ${toggleTwig({ ...data, showLabels: true, checked: true })}
      ${toggleTwig({ ...data, showLabels: true, size: 'large', onLabel: 'YES', offLabel: 'NO' })}
    </div>
  `,
  parameters: {
    docs: {
      description: { story: 'Internal labels clarify state when surrounding context is minimal.' },
    },
  },
};
