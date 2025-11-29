import component from './badge.twig';
import data from './badge.yml';
import './badge.css';

export default {
  title: 'Elements/Badge',
  render: (args) => component(args),
  args: data,
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component:
          'Badge compact indiquant un état ou une étiquette.\n\n' +
          '- Variants: default (gris), primary (vert), secondary (violet), gold, info, success, warning, danger — couleurs via tokens de marque.\n' +
          '- Tailles: small, medium (défaut), large — espacements/typos pilotés par tokens.\n' +
          '- Forme: base arrondie (rayon par défaut), option `pill` pour pilule.\n' +
          "- Icônes: via nom d'icône (font `bnpre-icons`) sans balise supplémentaire.\n" +
          '- Liens: `url` rend une balise <a> accessible.\n' +
          "- Accessibilité: texte toujours lisible (contraste défini par tokens); focus visible sur les liens; rôle implicite d'étiquette.\n" +
          "- Marquage minimal: `.ps-badge` fournit les styles par défaut; les modificateurs n'apparaissent que si une option diffère du défaut.",
      },
    },
  },
  argTypes: {
    text: {
      control: 'text',
      description: 'Badge text',
      table: {
        category: 'content',
        type: { summary: 'string', required: true },
      },
    },
    icon: {
      control: 'text',
      description: 'Icon name (e.g., icon-pwd-show, icon-calendar)',
      table: {
        category: 'content',
        type: { summary: 'string' },
      },
    },
    variant: {
      control: { type: 'select' },
      options: ['default', 'primary', 'secondary', 'gold', 'info', 'success', 'warning', 'danger'],
      description: 'Color variant',
      table: {
        category: 'appearance',
        defaultValue: { summary: 'default' },
      },
    },
    pill: {
      control: 'boolean',
      description: 'Rounded pill shape',
      table: {
        category: 'appearance',
        defaultValue: { summary: false },
      },
    },
    url: {
      control: 'text',
      description: 'Link URL (renders <a>)',
      table: {
        category: 'behavior',
        type: { summary: 'string' },
      },
    },
    size: {
      control: { type: 'inline-radio' },
      options: ['small', 'medium', 'large'],
      description: 'Badge size',
      table: {
        category: 'layout',
        defaultValue: { summary: 'medium' },
      },
    },
  },
};

// Variants
export const Default = {
  args: { ...data, variant: 'default', text: 'Default' },
};

export const Primary = {
  args: { ...data, variant: 'primary', text: 'Primary' },
};

export const Secondary = {
  args: { ...data, variant: 'secondary', text: 'Secondary' },
};

export const Gold = {
  args: { ...data, variant: 'gold', text: 'Gold' },
};

export const Info = {
  args: { ...data, variant: 'info', text: 'Info' },
};

export const Success = {
  args: { ...data, variant: 'success', text: 'Success' },
};

export const Warning = {
  args: { ...data, variant: 'warning', text: 'Warning' },
};

export const Danger = {
  args: { ...data, variant: 'danger', text: 'Danger' },
};

// Sizes
export const Sizes = {
  render: () => `
    <div style="display:flex; gap: var(--size-4); align-items:center; flex-wrap:wrap">
      ${component({ ...data, size: 'small', text: 'Small' })}
      ${component({ ...data, size: 'medium', text: 'Medium' })}
      ${component({ ...data, size: 'large', text: 'Large' })}
    </div>
  `,
};

// Pill shape
export const Pills = {
  render: () => `
    <div style="display:flex; gap: var(--size-4); align-items:center; flex-wrap:wrap">
      ${component({ ...data, variant: 'default', text: 'Pill', pill: true })}
      ${component({ ...data, variant: 'primary', text: 'Primary Pill', pill: true })}
      ${component({ ...data, variant: 'gold', text: 'Gold Pill', pill: true })}
    </div>
  `,
};

// With icons
export const WithIcons = {
  render: () => `
    <div style="display:flex; gap: var(--size-4); align-items:center; flex-wrap:wrap">
      ${component({ variant: 'info', text: 'With icon', icon: 'icon-pin-map' })}
      ${component({ variant: 'success', text: 'Calendar', icon: 'icon-calendar', pill: true })}
      ${component({ variant: 'gold', text: 'Exclusivity', icon: 'icon-pwd-show', pill: true })}
    </div>
  `,
};

// As links
export const AsLinks = {
  render: () => `
    <div style="display:flex; gap: var(--size-4); align-items:center; flex-wrap:wrap">
      ${component({ variant: 'primary', text: 'Link badge', url: '#' })}
      ${component({ variant: 'info', text: 'View more', icon: 'icon-eye', url: '#', pill: true })}
    </div>
  `,
};

// Kitchen sink
export const AllVariants = {
  render: () => `
    <div style="display:grid; gap: var(--size-3); grid-template-columns: repeat(auto-fit, minmax(140px, 1fr))">
      ${component({ variant: 'default', text: 'Default' })}
      ${component({ variant: 'primary', text: 'Primary' })}
      ${component({ variant: 'secondary', text: 'Secondary' })}
      ${component({ variant: 'gold', text: 'Gold' })}
      ${component({ variant: 'info', text: 'Info' })}
      ${component({ variant: 'success', text: 'Success' })}
      ${component({ variant: 'warning', text: 'Warning' })}
      ${component({ variant: 'danger', text: 'Danger' })}
    </div>
  `,
};
