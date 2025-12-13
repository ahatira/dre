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
    <div style="display: flex; flex-wrap: wrap; gap: var(--size-3); max-width: 800px;">
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
    <div style="display: flex; flex-direction: column; gap: var(--size-4); max-width: 800px;">
      <div>
        <small style="display: block; margin-bottom: var(--size-2); color: var(--gray-600); font-weight: 600;">Icon at start (default)</small>
        <div style="display: flex; flex-wrap: wrap; gap: var(--size-2);">
          ${eyebrowTwig({ label: 'Confirmé', variant: 'success', icon: 'check' })}
          ${eyebrowTwig({ label: 'Informations', variant: 'info', icon: 'info' })}
        </div>
      </div>
      <div>
        <small style="display: block; margin-bottom: var(--size-2); color: var(--gray-600); font-weight: 600;">Icon at end</small>
        <div style="display: flex; flex-wrap: wrap; gap: var(--size-2);">
          ${eyebrowTwig({ label: 'Avec icône fin', variant: 'primary', icon: 'arrow-right', iconPosition: 'end' })}
        </div>
      </div>
      <div>
        <small style="display: block; margin-bottom: var(--size-2); color: var(--gray-600); font-weight: 600;">Icon + Pill combination</small>
        <div style="display: flex; flex-wrap: wrap; gap: var(--size-2);">
          ${eyebrowTwig({ label: 'Premium', variant: 'gold', icon: 'award', pill: true })}
        </div>
      </div>
    </div>
  `,
};
