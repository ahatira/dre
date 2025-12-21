/**
 * Tabs (Organism/Collection)
 *
 * Tab navigation container composing individual Tab components.
 * Includes keyboard navigation, animated indicator, and WAI-ARIA compliance.
 */

import iconsRegistry from '../../documentation/icons-registry.json';
import tabsTemplate from './tabs.twig';
import data from './tabs.yml';
import './tabs.js';

const settings = {
  title: 'Collections/Tabs',
  tags: ['autodocs'],
  parameters: {
    layout: 'padded',
    docs: {
      description: {
        component:
          'Tab navigation for property details, dashboards, and content organization. Includes animated indicator, keyboard navigation (Arrow keys, Home, End), and auto/manual activation modes.',
      },
    },
  },
  render: (args) => tabsTemplate(args),
  argTypes: {
    label: {
      control: 'text',
      description: 'Accessible label for tablist (aria-label).',
      table: {
        category: 'Accessibility',
        type: { summary: 'string' },
        defaultValue: { summary: 'Tabs navigation' },
      },
    },
    variant: {
      control: 'select',
      options: [
        'neutral',
        'primary',
        'secondary',
        'success',
        'danger',
        'warning',
        'info',
        'gold',
        'light',
        'dark',
      ],
      description: 'Semantic color variant.',
      table: {
        category: 'Appearance',
        type: { summary: 'string' },
        defaultValue: { summary: 'neutral' },
      },
    },
    pill: {
      control: 'boolean',
      description: 'Toggle pill presentation style.',
      table: {
        category: 'Appearance',
        type: { summary: 'boolean' },
        defaultValue: { summary: 'false' },
      },
    },
    vertical: {
      control: 'boolean',
      description:
        'Vertical orientation (default: horizontal). Keyboard nav uses Up/Down instead of Left/Right.',
      table: {
        category: 'Behavior',
        type: { summary: 'boolean' },
        defaultValue: { summary: 'false' },
      },
    },
    auto: {
      control: 'boolean',
      description:
        'Auto activation mode (default: manual). When true, focus activates tab. When false, requires Enter/Space.',
      table: {
        category: 'Behavior',
        type: { summary: 'boolean' },
        defaultValue: { summary: 'false' },
      },
    },
    icon: {
      control: 'select',
      options: [null, ...iconsRegistry.names],
      description: 'Default icon for all tabs (overridden by tab-specific icon).',
      table: {
        category: 'Content',
        type: { summary: 'string|null' },
        defaultValue: { summary: 'null' },
      },
    },
    tabs: {
      control: 'object',
      description: 'Array of tab objects (id, label, content, active, disabled, icon).',
      table: {
        category: 'Content',
        type: { summary: 'array' },
        defaultValue: { summary: '[]' },
      },
    },
  },
};

export default settings;

// Store data with args for use in story args
data.args = data;

// ============================================================================
// Stories
// ============================================================================

// Default: Property details with icons
export const Default = {
  name: 'Default',
  args: data.args || data,
};

// -----------------------------------------------------------------------------
// All semantic variants in one story
// -----------------------------------------------------------------------------
export const AllVariants = {
  name: 'All Variants',
  render: () => {
    const variants = [
      'neutral',
      'primary',
      'secondary',
      'success',
      'danger',
      'warning',
      'info',
      'gold',
      'light',
      'dark',
    ];
    const simpleTabs = [
      { id: 'tab1', label: 'Détails', content: '<p>Informations générales</p>', active: true },
      { id: 'tab2', label: 'Photos', content: '<p>Galerie images</p>' },
      { id: 'tab3', label: 'Plans', content: '<p>Plans étage</p>' },
    ];

    return `<div class="just-for-gap">
      ${variants
        .map(
          (variant) => `
        <div>
          <div class="heading">${variant.charAt(0).toUpperCase() + variant.slice(1)}</div>
          ${tabsTemplate({
            label: `${variant} tabs`,
            variant,
            tabs: simpleTabs,
          })}
        </div>
      `
        )
        .join('')}
    </div>`;
  },
  parameters: {
    docs: {
      description: {
        story:
          'All 10 semantic color variants with animated indicator. Click tabs to see animation.',
      },
    },
  },
};

// -----------------------------------------------------------------------------
// Pill style
// -----------------------------------------------------------------------------
export const PillStyle = {
  name: 'Pill Style',
  render: () => {
    const variants = ['neutral', 'primary', 'secondary', 'success', 'warning'];
    const simpleTabs = [
      { id: 'tab1', label: 'Explorer', content: '<p>Découvrez les biens</p>', active: true },
      { id: 'tab2', label: 'Comparer', content: '<p>Comparez les offres</p>' },
      { id: 'tab3', label: 'Décider', content: '<p>Prenez votre décision</p>' },
    ];

    return `<div class="just-for-gap">
      ${variants
        .map(
          (variant) => `
        <div>
          <div class="heading">${variant.charAt(0).toUpperCase() + variant.slice(1)} Pill</div>
          ${tabsTemplate({
            label: `${variant} pill tabs`,
            variant,
            pill: true,
            tabs: simpleTabs,
          })}
        </div>
      `
        )
        .join('')}
    </div>`;
  },
  parameters: {
    docs: {
      description: {
        story: 'Pill presentation with filled background. No animated indicator in pill mode.',
      },
    },
  },
};

// -----------------------------------------------------------------------------
// Tabs with icons
// -----------------------------------------------------------------------------
export const WithIcons = {
  name: 'With Icons',
  args: {
    label: 'Validation process',
    variant: 'success',
    icon: 'check',
    tabs: [
      {
        id: 'tab-docs',
        label: 'Documents',
        icon: 'document',
        content: '<p>Documentation légale validée</p>',
        active: true,
      },
      {
        id: 'tab-finance',
        label: 'Financement',
        icon: 'euro',
        content: '<p>Plan de financement approuvé</p>',
      },
      {
        id: 'tab-signature',
        label: 'Signature',
        icon: 'signature',
        content: '<p>Contrat prêt à être signé</p>',
      },
    ],
  },
  parameters: {
    docs: {
      description: {
        story:
          'Tabs with specific icons per tab. Global icon prop applies to tabs without explicit icon.',
      },
    },
  },
};

// -----------------------------------------------------------------------------
// Vertical orientation
// -----------------------------------------------------------------------------
export const VerticalOrientation = {
  name: 'Vertical Orientation',
  args: {
    label: 'Property sections',
    variant: 'neutral',
    vertical: true,
    tabs: [
      {
        id: 'tab-v1',
        label: 'Vue ensemble',
        content: '<h3>Synthèse</h3><p>1 250 m² de bureaux flexibles près de La Défense</p>',
        active: true,
      },
      {
        id: 'tab-v2',
        label: 'Caractéristiques',
        content:
          '<h3>Détails</h3><p>Surface: 1 250 m² • Livraison: Q2 2026 • Prix: 540 €/m²/an</p>',
      },
      {
        id: 'tab-v3',
        label: 'Équipements',
        content: '<h3>Services</h3><p>Accueil, salles de réunion, terrasse, parking</p>',
      },
      {
        id: 'tab-v4',
        label: 'Contact',
        content: '<h3>Équipe</h3><p>Contactez nos conseillers experts</p>',
      },
    ],
  },
  parameters: {
    docs: {
      description: {
        story:
          'Vertical tab layout with animated indicator on right border. Keyboard navigation uses Up/Down arrows instead of Left/Right.',
      },
    },
  },
};

// -----------------------------------------------------------------------------
// Auto activation mode
// -----------------------------------------------------------------------------
export const AutoActivation = {
  name: 'Auto Activation',
  args: {
    label: 'Auto activation demo',
    variant: 'primary',
    auto: true,
    tabs: [
      {
        id: 'tab-m1',
        label: 'Étape 1',
        content: '<p>Utilisez les flèches pour naviguer, le focus active automatiquement</p>',
        active: true,
      },
      {
        id: 'tab-m2',
        label: 'Étape 2',
        content: '<p>Le panel change automatiquement au focus</p>',
      },
      {
        id: 'tab-m3',
        label: 'Étape 3',
        content: '<p>Activation automatique au focus</p>',
      },
    ],
  },
  parameters: {
    docs: {
      description: {
        story:
          'Auto activation mode: Focus automatically activates tabs. Default is manual mode (requires Enter/Space). Useful for quick content preview.',
      },
    },
  },
};
