import iconsRegistry from '../../documentation/icons-registry.json';
import eyebrowTwig from './eyebrow.twig';
import data from './eyebrow.yml';

export default {
  title: 'Elements/Eyebrow',
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component: `Contextual label placed above headings with semantic color variants. Supports pill shape and optional icon integration.`,
      },
    },
  },
  argTypes: {
    label: {
      description: 'Text content displayed in eyebrow',
      control: { type: 'text' },
      table: {
        category: 'Content',
        type: { summary: 'string' },
        defaultValue: { summary: 'Label' },
      },
    },
    variant: {
      description: 'Color variant with semantic meaning',
      control: { type: 'select' },
      options: [
        '',
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
      table: {
        category: 'Appearance',
        type: {
          summary: 'primary | secondary | success | danger | warning | info | gold | light | dark',
        },
        defaultValue: { summary: '' },
      },
    },
    pill: {
      description: 'Rounded pill shape with extended padding',
      control: { type: 'boolean' },
      table: {
        category: 'Appearance',
        type: { summary: 'boolean' },
        defaultValue: { summary: false },
      },
    },
    icon: {
      description: 'Icon name without "icon-" prefix',
      control: { type: 'select' },
      options: ['', ...iconsRegistry.names],
      table: {
        category: 'Content',
        type: { summary: 'string' },
        defaultValue: { summary: '' },
      },
    },
    iconPosition: {
      description: 'Icon position relative to label',
      control: { type: 'select' },
      options: ['start', 'end'],
      table: {
        category: 'Appearance',
        type: { summary: 'start | end' },
        defaultValue: { summary: 'start' },
      },
    },
  },
};

// Stories
export const Default = {
  render: (args) => eyebrowTwig(args),
  args: { ...data },
};

/**
 * 9 semantic color variants
 */
export const WithColor = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-3);">
      ${eyebrowTwig({ label: 'Actualité marché', variant: 'primary' })}
      ${eyebrowTwig({ label: 'Article blog', variant: 'secondary' })}
      ${eyebrowTwig({ label: 'Bien disponible', variant: 'success' })}
      ${eyebrowTwig({ label: 'Bien vendu', variant: 'danger' })}
      ${eyebrowTwig({ label: 'Offre limitée', variant: 'warning' })}
      ${eyebrowTwig({ label: 'En savoir plus', variant: 'info' })}
      ${eyebrowTwig({ label: 'Bien premium', variant: 'gold' })}
      ${eyebrowTwig({ label: 'Sur fond sombre', variant: 'light' })}
      ${eyebrowTwig({ label: 'Sur fond clair', variant: 'dark' })}
    </div>
  `,
};

/**
 * Pill modifier for rounded shape
 */
export const WithPill = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-3);">
      ${eyebrowTwig({ label: 'Standard', variant: 'primary', pill: false })}
      ${eyebrowTwig({ label: 'Pill shape', variant: 'primary', pill: true })}
      ${eyebrowTwig({ label: 'Premium offer', variant: 'gold', pill: true })}
      ${eyebrowTwig({ label: 'New listing', variant: 'success', pill: true })}
    </div>
  `,
};

/**
 * Icon integration with start/end positions
 */
export const WithIcon = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-3);">
      ${eyebrowTwig({ label: 'Confirmé', variant: 'success', icon: 'check' })}
      ${eyebrowTwig({ label: 'Informations', variant: 'info', icon: 'info' })}
      ${eyebrowTwig({ label: 'Avec icône fin', variant: 'primary', icon: 'arrow-right', iconPosition: 'end' })}
      ${eyebrowTwig({ label: 'Premium', variant: 'gold', icon: 'award', pill: true })}
    </div>
  `,
};

/**
 * Real estate use cases
 */
export const RealEstateContext = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-6); padding: var(--size-6);">
      
      <!-- Hero Section -->
      <div style="background: var(--gray-50); padding: var(--size-8); border-radius: var(--radius-3);">
        ${eyebrowTwig({ label: 'Investisseurs', variant: 'primary' })}
        <h1 style="margin-top: var(--size-3); margin-bottom: 0; font-size: var(--font-size-8);">Portefeuille immobilier premium</h1>
      </div>

      <!-- Featured Property Card -->
      <div style="border: 1px solid var(--gray-200); border-radius: var(--radius-3); padding: var(--size-6); max-width: 420px;">
        ${eyebrowTwig({ label: 'Bien phare', variant: 'gold', icon: 'award', pill: true })}
        <h3 style="margin: var(--size-3) 0 var(--size-2) 0; font-size: var(--font-size-5);">Tour Premium - La Défense</h3>
        <p style="margin: 0; color: var(--text-secondary); font-size: var(--font-size-2);">3 500 m² - Bureau de prestige avec vue panoramique</p>
      </div>

      <!-- Market News -->
      <div style="border: 1px solid var(--gray-200); border-radius: var(--radius-3); padding: var(--size-6); max-width: 420px;">
        ${eyebrowTwig({ label: 'Actualité marché', variant: 'primary' })}
        <h3 style="margin: var(--size-3) 0 var(--size-2) 0; font-size: var(--font-size-4);">Tendances Q4 2025</h3>
        <p style="margin: 0; color: var(--text-secondary);">Analyse du secteur tertiaire en Île-de-France</p>
      </div>

      <!-- Category Tags -->
      <div>
        <h4 style="margin-bottom: var(--size-3); font-size: var(--font-size-2); color: var(--text-secondary);">Catégories de biens</h4>
        <div style="display: flex; gap: var(--size-2); flex-wrap: wrap;">
          ${eyebrowTwig({ label: 'Bureau', variant: '' })}
          ${eyebrowTwig({ label: 'Retail', variant: '' })}
          ${eyebrowTwig({ label: 'Logistique', variant: '' })}
          ${eyebrowTwig({ label: 'Résidentiel', variant: '' })}
        </div>
      </div>

    </div>
  `,
};
