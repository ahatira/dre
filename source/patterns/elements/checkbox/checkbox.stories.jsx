import checkbox from './checkbox.twig';
import data from './checkbox.yml';

export default {
  title: 'Elements/Checkbox',
  tags: ['autodocs'],
  render: (args) => checkbox(args),
  args: data,
  parameters: {
    docs: {
      description: {
        component:
          'Case à cocher accessible conforme au Design System.\n\n' +
          '- Contenu: input natif + label optionnel lié par `id`/`for`.\n' +
          '- États: `checked`, `disabled` — styles et curseur adaptés.\n' +
          '- Icône: rendu via pseudo-éléments (police d\'icônes), sans balise supplémentaire.\n' +
          '- Accessibilité: cible clavier, focus visible; annonce ARIA native; label recommandé pour la compréhension.\n' +
          '- Tokens: couleurs, espacements, bordures et typos uniquement via tokens.\n' +
          '- Marquage minimal: classe de base applique les styles par défaut; modificateurs ajoutés uniquement si nécessaires.',
      },
    },
  },
  argTypes: {
    name: {
      control: 'text',
      description: 'Input name attribute',
    },
    value: {
      control: 'text',
      description: 'Input value',
    },
    label: {
      control: 'text',
      description: 'Label text (optional)',
    },
    checked: {
      control: 'boolean',
      description: 'Checked state',
    },
    disabled: {
      control: 'boolean',
      description: 'Disabled state',
    },
    id: {
      control: 'text',
      description: 'Input ID (auto-generated if empty)',
    },
  },
};

export const Default = {
  args: { ...data },
};

export const NoLabel = {
  args: {
    ...data,
    label: '',
  },
};

export const Checked = {
  args: {
    ...data,
    checked: true,
  },
};

export const CheckedNoLabel = {
  args: {
    ...data,
    label: '',
    checked: true,
  },
};

export const WithLongLabel = {
  args: {
    ...data,
    label: 'Lorem ipsum dolor sit amet consectetur. Cursus posuere et egestas id metus sit.',
  },
};

export const Disabled = {
  args: {
    ...data,
    disabled: true,
  },
};

export const DisabledChecked = {
  args: {
    ...data,
    checked: true,
    disabled: true,
  },
};

export const Group = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: 1rem;">
      <div>
        <strong>No label:</strong><br/><br/>
        ${checkbox({ name: 'group1', value: '1', label: 'Option label', checked: false })}
      </div>
      <div>
        <strong>Label:</strong><br/><br/>
        ${checkbox({ name: 'group2', value: '1', label: 'Option label', checked: false })}<br/>
        ${checkbox({ name: 'group2', value: '2', label: 'Lorem ipsum dolor sit amet consectetur. Cursus posuere et egestas id metus sit.', checked: false })}
      </div>
      <div>
        <strong>Selected:</strong><br/><br/>
        ${checkbox({ name: 'group3', value: '1', label: 'Option label', checked: true })}<br/>
        ${checkbox({ name: 'group3', value: '2', label: 'Lorem ipsum dolor sit amet consectetur. Cursus posuere et egestas id metus sit.', checked: true })}
      </div>
    </div>
  `,
};
