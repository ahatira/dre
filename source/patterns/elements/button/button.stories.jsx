import button from './button.twig';
import data from './button.yml';

const settings = {
  title: 'Elements/Button',
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component:
          "Bouton d'action avec variants sémantiques (primary, secondary, success, info, warning, danger, dark, light). Supporte les versions outline, icônes, différentes tailles, et états disabled/loading.",
      },
    },
  },
  argTypes: {
    label: {
      description: 'Texte affiché dans le bouton',
      control: { type: 'text' },
      table: {
        type: { summary: 'string' },
        defaultValue: { summary: 'Button' },
      },
    },
    variant: {
      description: 'Variant sémantique du bouton',
      control: { type: 'select' },
      options: ['primary', 'secondary', 'success', 'info', 'warning', 'danger', 'dark', 'light'],
      table: {
        type: { summary: 'primary | secondary | success | info | warning | danger | dark | light' },
        defaultValue: { summary: 'primary' },
      },
    },
    outline: {
      description: 'Version outline (bordure uniquement, fond transparent)',
      control: { type: 'boolean' },
      table: {
        type: { summary: 'boolean' },
        defaultValue: { summary: 'false' },
      },
    },
    size: {
      description: 'Taille du bouton : small (33.98px), medium (36px), large (40px)',
      control: { type: 'select' },
      options: ['small', 'medium', 'large'],
      table: {
        type: { summary: 'small | medium | large' },
        defaultValue: { summary: 'medium' },
      },
    },
    icon: {
      description: "Nom de l'icône à afficher (optionnel)",
      control: { type: 'text' },
      table: {
        type: { summary: 'string' },
      },
    },
    iconPosition: {
      description: "Position de l'icône par rapport au texte",
      control: { type: 'select' },
      options: ['left', 'right'],
      table: {
        type: { summary: 'left | right' },
        defaultValue: { summary: 'right' },
      },
    },
    disabled: {
      description: "Désactive le bouton (réduit l'opacité à 50%)",
      control: { type: 'boolean' },
      table: {
        type: { summary: 'boolean' },
        defaultValue: { summary: 'false' },
      },
    },
    loading: {
      description: 'Affiche un état de chargement',
      control: { type: 'boolean' },
      table: {
        type: { summary: 'boolean' },
        defaultValue: { summary: 'false' },
      },
    },
    fullWidth: {
      description: 'Bouton en pleine largeur (width: 100%)',
      control: { type: 'boolean' },
      table: {
        type: { summary: 'boolean' },
        defaultValue: { summary: 'false' },
      },
    },
    url: {
      description: 'URL de destination (transforme le bouton en lien)',
      control: { type: 'text' },
      table: {
        type: { summary: 'string' },
      },
    },
  },
};

export const Default = {
  render: (args) => button(args),
  args: { ...data },
};

export const Primary = {
  render: () => button({ label: 'Primary', variant: 'primary', size: 'medium' }),
};

export const Secondary = {
  render: () => button({ label: 'Secondary', variant: 'secondary', size: 'medium' }),
};

export const Success = {
  render: () => button({ label: 'Success', variant: 'success', size: 'medium' }),
};

export const Info = {
  render: () => button({ label: 'Info', variant: 'info', size: 'medium' }),
};

export const Warning = {
  render: () => button({ label: 'Warning', variant: 'warning', size: 'medium' }),
};

export const Danger = {
  render: () => button({ label: 'Danger', variant: 'danger', size: 'medium' }),
};

export const Dark = {
  render: () => button({ label: 'Dark', variant: 'dark', size: 'medium' }),
};

export const Light = {
  render: () => button({ label: 'Light', variant: 'light', size: 'medium' }),
};

export const AllVariants = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: 16px;">
      <div style="display: flex; gap: 12px; flex-wrap: wrap;">
        ${button({ label: 'Primary', variant: 'primary' })}
        ${button({ label: 'Secondary', variant: 'secondary' })}
        ${button({ label: 'Success', variant: 'success' })}
        ${button({ label: 'Info', variant: 'info' })}
        ${button({ label: 'Warning', variant: 'warning' })}
        ${button({ label: 'Danger', variant: 'danger' })}
        ${button({ label: 'Dark', variant: 'dark' })}
        ${button({ label: 'Light', variant: 'light' })}
      </div>
    </div>
  `,
};

export const AllOutlines = {
  render: () => `
    <div style="display: flex; gap: 12px; flex-wrap: wrap;">
      ${button({ label: 'Primary', variant: 'primary', outline: true })}
      ${button({ label: 'Secondary', variant: 'secondary', outline: true })}
      ${button({ label: 'Success', variant: 'success', outline: true })}
      ${button({ label: 'Info', variant: 'info', outline: true })}
      ${button({ label: 'Warning', variant: 'warning', outline: true })}
      ${button({ label: 'Danger', variant: 'danger', outline: true })}
      ${button({ label: 'Dark', variant: 'dark', outline: true })}
      ${button({ label: 'Light', variant: 'light', outline: true })}
    </div>
  `,
};

export const Sizes = {
  render: () => `
    <div style="display: flex; gap: 12px; align-items: center;">
      ${button({ label: 'Small', variant: 'primary', size: 'small' })}
      ${button({ label: 'Medium', variant: 'primary', size: 'medium' })}
      ${button({ label: 'Large', variant: 'primary', size: 'large' })}
    </div>
  `,
};

export const WithIcons = {
  render: () => `
    <div style="display: flex; gap: 12px; flex-wrap: wrap;">
      ${button({ label: 'Rechercher', variant: 'primary', icon: 'search', iconPosition: 'left' })}
      ${button({ label: 'Suivant', variant: 'primary', icon: 'arrow-right', iconPosition: 'right' })}
      ${button({ icon: 'close', variant: 'primary', size: 'medium' })}
    </div>
  `,
};

export const FullWidth = {
  render: () => button({ label: 'Full Width Button', variant: 'primary', fullWidth: true }),
};

export const Loading = {
  render: () => `
    <div style="display: flex; gap: 12px; align-items: center;">
      ${button({ label: 'Chargement...', variant: 'primary', loading: true })}
      ${button({ label: 'Chargement...', variant: 'secondary', outline: true, loading: true })}
    </div>
  `,
};

export const Disabled = {
  render: () => `
    <div style="display: flex; gap: 12px; align-items: center;">
      ${button({ label: 'Désactivé', variant: 'primary', disabled: true })}
      ${button({ label: 'Désactivé', variant: 'secondary', outline: true, disabled: true })}
    </div>
  `,
};

export default settings;
