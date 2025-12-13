import iconsRegistry from '../../documentation/icons-registry.json';
import linkTwig from './link.twig';
import data from './link.yml';

export default {
  title: 'Elements/Link',
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component: `Semantic text link with optional icon and pill style. Use utilities for colors (.text-primary), sizes (.text-lg), and decoration (.no-underline).`,
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
 * Apply semantic colors via utility classes (.text-primary, .text-success, .text-danger, etc.)
 */
export const WithColor = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-3);">
      <a href="#" class="ps-link text-primary">Planifier une visite</a>
      <a href="#" class="ps-link text-secondary">Contacter un conseiller</a>
      <a href="#" class="ps-link text-success">Bien disponible</a>
      <a href="#" class="ps-link text-danger">Bien vendu</a>
      <a href="#" class="ps-link text-warning">Offre limitée</a>
      <a href="#" class="ps-link text-info">En savoir plus</a>
      <a href="#" class="ps-link text-gold">Biens premium</a>
    </div>
  `,
};

/**
 * Icon integration with data-icon attribute
 */
export const WithIcon = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-3);">
      ${linkTwig({ label: 'Voir tous les biens', url: '#', icon: 'arrow-right' })}
      ${linkTwig({ label: 'Télécharger la brochure', url: '#', icon: 'download' })}
      ${linkTwig({ label: 'Consulter les infos', url: '#', icon: 'info' })}
      ${linkTwig({ label: 'Bien vérifié', url: '#', icon: 'check' })}
    </div>
  `,
};

/**
 * Combine utilities for advanced styling (colors, typography, text-decoration)
 */
export const WithUtilities = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-3);">
      <a href="#" class="ps-link no-underline text-primary font-semibold">Sans soulignement</a>
      <a href="#" class="ps-link text-lg text-secondary">Grande taille</a>
      <a href="#" class="ps-link no-underline font-bold text-success">Gras + couleur</a>
      <a href="#" class="ps-link text-sm text-gray-600">Petit + gris</a>
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
