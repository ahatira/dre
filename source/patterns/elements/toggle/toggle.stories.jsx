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
          'Size variants: small (dense settings), medium (default), large (prominent toggles).',
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

export const UseCases = {
  render: () => `
    <div style="display:grid; gap:1.5rem; max-width:640px;">
      <div style="padding:1rem; border:1px solid var(--gray-200); border-radius:var(--radius-2); background:white;">
        <h4 style="margin:0 0 .5rem; font-size:14px; font-weight:600;">Account Preferences</h4>
        ${toggleTwig({ name: 'email_alerts', label: 'Email alerts', description: 'Receive summary notifications weekly', checked: true })}
        ${toggleTwig({ name: 'sms_alerts', label: 'SMS alerts', description: 'Send important updates by SMS', checked: false })}
        ${toggleTwig({ name: 'beta_features', label: 'Beta features', description: 'Access experimental interface changes', checked: true, showLabels: true })}
      </div>
      <div style="padding:1rem; border:1px solid var(--gray-200); border-radius:var(--radius-2); background:white;">
        <h4 style="margin:0 0 .5rem; font-size:14px; font-weight:600;">Display Settings</h4>
        ${toggleTwig({ name: 'dark_mode', label: 'Dark mode', description: 'Reduce eye strain in low-light', checked: false, size: 'large' })}
        ${toggleTwig({ name: 'dense_layout', label: 'Compact layout', description: 'Show more data on screen', checked: true, size: 'small' })}
      </div>
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story:
          'Real usage: grouped preference panels combining states, sizes, and internal labels.',
      },
    },
  },
};
