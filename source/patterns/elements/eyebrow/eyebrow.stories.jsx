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
    <div style="display: flex; flex-direction: column; gap: var(--size-4); max-width: 600px;">
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
    <div style="display: flex; flex-direction: column; gap: var(--size-4); max-width: 600px;">
      <div>
        <small style="display: block; margin-bottom: var(--size-2); color: var(--gray-600); font-weight: 600;">Standard shape</small>
        ${eyebrowTwig({ label: 'Standard', variant: 'primary', pill: false })}
      </div>
      <div>
        <small style="display: block; margin-bottom: var(--size-2); color: var(--gray-600); font-weight: 600;">Pill shape (rounded)</small>
        ${eyebrowTwig({ label: 'Pill shape', variant: 'primary', pill: true })}
      </div>
      <div>
        <small style="display: block; margin-bottom: var(--size-2); color: var(--gray-600); font-weight: 600;">More examples</small>
        <div style="display: flex; gap: var(--size-2); flex-wrap: wrap;">
          ${eyebrowTwig({ label: 'Premium offer', variant: 'gold', pill: true })}
          ${eyebrowTwig({ label: 'New listing', variant: 'success', pill: true })}
        </div>
      </div>
    </div>
  `,
};

/**
 * Icon integration with start/end positions
 */
export const WithIcon = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-4); max-width: 600px;">
      <div>
        <small style="display: block; margin-bottom: var(--size-2); color: var(--gray-600); font-weight: 600;">Icon at start (default)</small>
        <div style="display: flex; flex-direction: column; gap: var(--size-2);">
          ${eyebrowTwig({ label: 'Confirmé', variant: 'success', icon: 'check' })}
          ${eyebrowTwig({ label: 'Informations', variant: 'info', icon: 'info' })}
        </div>
      </div>
      <div>
        <small style="display: block; margin-bottom: var(--size-2); color: var(--gray-600); font-weight: 600;">Icon at end</small>
        ${eyebrowTwig({ label: 'Avec icône fin', variant: 'primary', icon: 'arrow-right', iconPosition: 'end' })}
      </div>
      <div>
        <small style="display: block; margin-bottom: var(--size-2); color: var(--gray-600); font-weight: 600;">Icon + Pill combination</small>
        ${eyebrowTwig({ label: 'Premium', variant: 'gold', icon: 'award', pill: true })}
      </div>
    </div>
  `,
};

/**
 * Real estate use cases
 */
export const RealEstateContext = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-8); padding: var(--size-4);">
      
      <!-- News Card Example -->
      <div>
        <h4 style="margin-bottom: var(--size-4); font-size: var(--font-size-2); color: var(--gray-700); font-weight: 600;">News Card Example</h4>
        <div style="border: 1px solid var(--gray-200); border-radius: var(--radius-3); padding: var(--size-6); max-width: 420px; background: white;">
          <div style="display: flex; gap: var(--size-2); margin-bottom: var(--size-4);">
            ${eyebrowTwig({ label: 'Investisseurs', variant: 'primary' })}
            ${eyebrowTwig({ label: 'Date', variant: '' })}
          </div>
          <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-5); font-weight: 600;">Portefeuille immobilier premium</h3>
          <p style="margin: 0; color: var(--gray-600); font-size: var(--font-size-2); line-height: 1.5;">Découvrez notre sélection de biens d'exception pour les investisseurs professionnels.</p>
        </div>
      </div>

      <!-- Property Card Example -->
      <div>
        <h4 style="margin-bottom: var(--size-4); font-size: var(--font-size-2); color: var(--gray-700); font-weight: 600;">Property Card Example</h4>
        <div style="border: 1px solid var(--gray-200); border-radius: var(--radius-3); padding: var(--size-6); max-width: 420px; background: white;">
          ${eyebrowTwig({ label: 'Bien phare', variant: 'gold', pill: true })}
          <h3 style="margin: var(--size-3) 0 var(--size-2) 0; font-size: var(--font-size-5); font-weight: 600;">Tour Premium - La Défense</h3>
          <p style="margin: 0; color: var(--gray-600); font-size: var(--font-size-2);">3 500 m² - Bureau de prestige avec vue panoramique</p>
        </div>
      </div>

      <!-- Market Analysis Example -->
      <div>
        <h4 style="margin-bottom: var(--size-4); font-size: var(--font-size-2); color: var(--gray-700); font-weight: 600;">Market Analysis Example</h4>
        <div style="border: 1px solid var(--gray-200); border-radius: var(--radius-3); padding: var(--size-6); max-width: 420px; background: white;">
          ${eyebrowTwig({ label: 'Actualité marché', variant: 'primary' })}
          <h3 style="margin: var(--size-3) 0 var(--size-2) 0; font-size: var(--font-size-4); font-weight: 600;">Tendances Q4 2025</h3>
          <p style="margin: 0; color: var(--gray-600); font-size: var(--font-size-2);">Analyse du secteur tertiaire en Île-de-France</p>
        </div>
      </div>

      <!-- Category Tags Example -->
      <div>
        <h4 style="margin-bottom: var(--size-3); font-size: var(--font-size-2); color: var(--gray-700); font-weight: 600;">Catégories de biens</h4>
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
