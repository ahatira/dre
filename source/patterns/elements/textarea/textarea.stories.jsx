import textareaTwig from './textarea.twig';
import data from './textarea.yml';

export default {
  title: 'Elements/Textarea',
  tags: ['autodocs'],
  render: (args) => textareaTwig(args),
  args: data,
  parameters: {
    docs: {
      description: {
        component:
          'Textarea atom sans label. À utiliser avec un label externe. Styles alignés sur Input : radius=0, border, pas de box-shadow, focus visible, tokens Surface.',
      },
    },
  },
  argTypes: {
    value: {
      control: 'text',
      description: 'Contenu du textarea',
      table: { category: 'Content', type: { summary: 'string' } },
    },
    placeholder: {
      control: 'text',
      description: 'Texte d’exemple',
      table: { category: 'Content', type: { summary: 'string' } },
    },
    state: {
      control: 'select',
      options: [null, 'error', 'success', 'warning'],
      description: 'État de validation',
      table: { category: 'State', type: { summary: 'null | error | success | warning' }, defaultValue: { summary: 'null' } },
    },
    disabled: {
      control: 'boolean',
      description: 'Champ désactivé',
      table: { category: 'State', type: { summary: 'boolean' }, defaultValue: { summary: 'false' } },
    },
    required: {
      control: 'boolean',
      description: 'Champ requis',
      table: { category: 'State', type: { summary: 'boolean' }, defaultValue: { summary: 'false' } },
    },
    rows: {
      control: 'number',
      description: 'Nombre de lignes',
      table: { category: 'Content', type: { summary: 'number' }, defaultValue: { summary: '4' } },
    },
  },
};

export const Default = {
  args: {
    ...data,
    value: '',
    state: null,
    disabled: false,
    required: false,
    placeholder: 'Décrivez votre besoin immobilier...'
  },
};

export const Error = {
  args: {
    ...data,
    value: '',
    state: 'error',
    placeholder: 'Erreur de saisie',
  },
};

export const Success = {
  args: {
    ...data,
    value: 'Demande envoyée',
    state: 'success',
    placeholder: 'Succès',
  },
};

export const Warning = {
  args: {
    ...data,
    value: '',
    state: 'warning',
    placeholder: 'Attention',
  },
};

export const Disabled = {
  args: {
    ...data,
    value: 'Champ désactivé',
    disabled: true,
    placeholder: 'Non éditable',
  },
};

export const Required = {
  args: {
    ...data,
    required: true,
    placeholder: 'Champ obligatoire',
  },
};

export const FocusVisible = {
  args: {
    ...data,
    value: '',
    state: null,
    placeholder: 'Tabulez pour voir le focus',
  },
  parameters: {
    docs: {
      description: {
        story: 'Utilisez la touche Tab pour vérifier le focus visible (conforme WCAG 2.2 AA).',
      },
    },
  },
};

export const WithExternalLabel = {
  render: (args) => {
    const id = args.id || 'textarea-label-demo';
    return `
      <div style="display: flex; flex-direction: column; gap: var(--size-2); max-width: 420px;">
        <label for="${id}" style="font-weight: var(--font-weight-600);">Votre message</label>
        ${textareaTwig({ ...args, id })}
      </div>
    `;
  },
  args: {
    ...data,
    id: 'textarea-label-demo',
    value: 'Je souhaite visiter le loft.',
  },
};