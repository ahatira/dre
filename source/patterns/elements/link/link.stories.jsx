import iconsRegistry from '../../documentation/icons-registry.json';
import linkTwig from './link.twig';
import data from './link.yml';

export default {
  title: 'Elements/Link',
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component: `Semantic text link with color variants and proper interactive states (hover, active, visited). Supports optional icon and external target handling.`,
      },
    },
  },
  argTypes: {
    label: {
      description: 'Link text content',
      control: { type: 'text' },
      table: {
        category: 'Content',
        type: { summary: 'string' },
        defaultValue: { summary: 'Link text' },
      },
    },
    url: {
      description: 'Link destination URL',
      control: { type: 'text' },
      table: {
        category: 'Content',
        type: { summary: 'string' },
        defaultValue: { summary: '#' },
      },
    },
    variant: {
      description: 'Color variant with hover/active/visited states',
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
        defaultValue: { summary: 'end' },
      },
    },
    target: {
      description: 'Link target (_self or _blank)',
      control: { type: 'select' },
      options: ['_self', '_blank'],
      table: {
        category: 'Behavior',
        type: { summary: '_self | _blank' },
        defaultValue: { summary: '_self' },
      },
    },
  },
};

// Stories
export const Default = {
  render: (args) => linkTwig(args),
  args: { ...data },
};

/**
 * 9 color variants with proper hover/active/visited states
 */
export const WithColor = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-3);">
      ${linkTwig({ label: 'Planifier une visite', url: '#', variant: 'primary' })}
      ${linkTwig({ label: 'Contacter un conseiller', url: '#', variant: 'secondary' })}
      ${linkTwig({ label: 'Bien disponible', url: '#', variant: 'success' })}
      ${linkTwig({ label: 'Bien vendu', url: '#', variant: 'danger' })}
      ${linkTwig({ label: 'Offre limitée', url: '#', variant: 'warning' })}
      ${linkTwig({ label: 'En savoir plus', url: '#', variant: 'info' })}
      ${linkTwig({ label: 'Biens premium', url: '#', variant: 'gold' })}
      ${linkTwig({ label: 'Navigation dark', url: '#', variant: 'dark' })}
    </div>
    <div style="margin-top: var(--size-4); background: var(--gray-800); padding: var(--size-4); border-radius: var(--radius-2);">
      ${linkTwig({ label: 'Pied de page', url: '#', variant: 'light' })}
    </div>
  `,
};

/**
 * Icon integration with data-icon attribute (start or end position)
 */
export const WithIcon = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-3);">
      ${linkTwig({ label: 'Voir tous les biens', url: '#', variant: 'primary', icon: 'arrow-right' })}
      ${linkTwig({ label: 'Retour aux résultats', url: '#', variant: 'primary', icon: 'arrow-left', iconPosition: 'start' })}
      ${linkTwig({ label: 'Télécharger la brochure', url: '#', variant: 'secondary', icon: 'download' })}
      ${linkTwig({ label: 'Consulter les infos', url: '#', variant: 'info', icon: 'info', iconPosition: 'start' })}
      ${linkTwig({ label: 'Bien vérifié', url: '#', variant: 'success', icon: 'check' })}
    </div>
  `,
};

/**
 * Combine with utilities for additional styling (decoration, weight, alignment)
 */
export const WithUtilities = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-3);">
      <a href="#" class="ps-link ps-link--primary no-underline font-semibold">Sans soulignement</a>
      <a href="#" class="ps-link ps-link--secondary font-bold">Gras</a>
      <a href="#" class="ps-link ps-link--success text-center">Aligné au centre</a>
      <a href="#" class="ps-link ps-link--info">Lien info (hover pour voir effet)</a>
    </div>
  `,
};

/**
 * External link with target="_blank" and security attributes
 */
export const ExternalLink = {
  render: () =>
    linkTwig({
      label: 'Site externe',
      url: 'https://example.com',
      target: '_blank',
      icon: 'external-link',
    }),
};
