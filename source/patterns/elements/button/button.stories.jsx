import buttonTwig from './button.twig';
import data from './button.yml';
import colorsList from '../../documentation/colors-list.json';
import sizesList from '../../documentation/sizes-list.json';
import iconsList from '../../documentation/icons-list.json';
import variantsList from '../../documentation/variants-list.json';

export default {
  title: 'Elements/Button',
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component:
          "Bouton d'action sémantique conforme au Design System.\n\n" +
          '- **Variants**: primary, secondary, success, info, warning, danger, dark, light — couleurs via tokens de marque.\n' +
          '- **Styles**: plein (par défaut) et `outline` (fond transparent, bordure tokenisée).\n' +
          '- **Tailles**: small, medium (défaut), large — hauteurs/espacements pilotés par tokens.\n' +
          "- **Icônes**: optionnelles à gauche/droite, via nom d'icône (police `bnpre-icons`).\n" +
          '- **États**: disabled et loading avec styles/accessibilité conformes.\n' +
          '- **Mise en page**: `fullWidth` étend à 100% du conteneur.\n' +
          '- **Accessibilité**: rôle/comportement bouton ou lien selon `url`; focus visible; libellé textuel requis.\n' +
          "- **Rendu minimal**: `.ps-button` porte les styles par défaut; les modificateurs n'apparaissent que si une option diffère du défaut.",
      },
    },
  },
  argTypes: {
    // Content
    label: {
      description: 'Texte affiché dans le bouton',
      control: { type: 'text' },
      table: {
        category: 'Content',
        type: { summary: 'string', required: true },
        defaultValue: { summary: 'Button' },
      },
    },
    icon: {
      description: "Nom de l'icône à afficher (optionnel)",
      control: { type: 'select' },
      options: iconsList.categories.generic,
      table: {
        category: 'Content',
        type: { summary: 'string' },
      },
    },
    iconPosition: {
      description: "Position de l'icône par rapport au texte",
      control: { type: 'select' },
      options: ['left', 'right'],
      table: {
        category: 'Content',
        type: { summary: 'left | right' },
        defaultValue: { summary: 'right' },
      },
    },
    // Appearance
    variant: {
      description: 'Variant sémantique du bouton',
      control: { type: 'select' },
      options: variantsList.color.components.button,
      table: {
        category: 'Appearance',
        type: { summary: variantsList.color.components.button.join(' | ') },
        defaultValue: { summary: 'primary' },
      },
    },
    outline: {
      description: 'Version outline (bordure uniquement, fond transparent)',
      control: { type: 'boolean' },
      table: {
        category: 'Appearance',
        type: { summary: 'boolean' },
        defaultValue: { summary: false },
      },
    },
    size: {
      description: 'Taille du bouton',
      control: { type: 'select' },
      options: variantsList.size.compact,
      table: {
        category: 'Appearance',
        type: { summary: variantsList.size.compact.join(' | ') },
        defaultValue: { summary: 'medium' },
      },
    },
    fullWidth: {
      description: 'Bouton en pleine largeur (width: 100%)',
      control: { type: 'boolean' },
      table: {
        category: 'Appearance',
        type: { summary: 'boolean' },
        defaultValue: { summary: false },
      },
    },
    // Behavior
    disabled: {
      description: "Désactive le bouton (réduit l'opacité à 50%)",
      control: { type: 'boolean' },
      table: {
        category: 'Behavior',
        type: { summary: 'boolean' },
        defaultValue: { summary: false },
      },
    },
    loading: {
      description: 'Affiche un état de chargement',
      control: { type: 'boolean' },
      table: {
        category: 'Behavior',
        type: { summary: 'boolean' },
        defaultValue: { summary: false },
      },
    },
    // Link
    url: {
      description: 'URL de destination (transforme le bouton en lien)',
      control: { type: 'text' },
      table: {
        category: 'Link',
        type: { summary: 'string' },
      },
    },
    target: {
      description: 'Attribut target du lien',
      control: { type: 'select' },
      options: ['_self', '_blank'],
      table: {
        category: 'Link',
        type: { summary: '_self | _blank' },
        defaultValue: { summary: '_self' },
      },
    },
  },
};

export const Default = {
  render: (args) => buttonTwig(args),
  args: { ...data },
};

export const AllVariants = {
  render: () => `
    <div style="display: flex; gap: var(--size-4); flex-wrap: wrap;">
      ${variantsList.color.components.button.map(variant => buttonTwig({ label: variant.charAt(0).toUpperCase() + variant.slice(1), variant })).join('')}
    </div>
  `,
};

export const AllOutlines = {
  render: () => `
    <div style="display: flex; gap: var(--size-4); flex-wrap: wrap;">
      ${variantsList.color.components.button.map(variant => buttonTwig({ label: variant.charAt(0).toUpperCase() + variant.slice(1), variant, outline: true })).join('')}
    </div>
  `,
};

export const AllSizes = {
  render: () => `
    <div style="display: flex; gap: var(--size-4); align-items: center;">
      ${variantsList.size.compact.map(size => buttonTwig({ label: size.charAt(0).toUpperCase() + size.slice(1), variant: 'primary', size })).join('')}
    </div>
  `,
};

export const WithIcons = {
  render: () => `
    <div style="display: flex; gap: var(--size-4); flex-wrap: wrap;">
      ${buttonTwig({ label: 'Rechercher', variant: 'primary', icon: 'search', iconPosition: 'left' })}
      ${buttonTwig({ label: 'Suivant', variant: 'primary', icon: 'arrow-right', iconPosition: 'right' })}
      ${buttonTwig({ icon: 'close', variant: 'primary', size: 'medium' })}
    </div>
  `,
};

export const FullWidth = {
  render: () => buttonTwig({ label: 'Full Width Button', variant: 'primary', fullWidth: true }),
};

export const Loading = {
  render: () => `
    <div style="display: flex; gap: var(--size-4); align-items: center;">
      ${buttonTwig({ label: 'Chargement...', variant: 'primary', loading: true })}
      ${buttonTwig({ label: 'Chargement...', variant: 'secondary', outline: true, loading: true })}
    </div>
  `,
};

export const Disabled = {
  render: () => `
    <div style="display: flex; gap: var(--size-4); align-items: center;">
      ${buttonTwig({ label: 'Désactivé', variant: 'primary', disabled: true })}
      ${buttonTwig({ label: 'Désactivé', variant: 'secondary', outline: true, disabled: true })}
    </div>
  `,
};
