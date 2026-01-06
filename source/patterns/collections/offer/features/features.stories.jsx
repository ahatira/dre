/**
 * Features (Collection/Organism)
 *
 * Thematic section composing Icon, Heading, and List atoms.
 * Used for property features, services, building amenities, and information.
 * Supports responsive two-column layout when item count reaches threshold.
 */

import iconsRegistry from '../../documentation/icons-registry.json';
import featuresTemplate from './features.twig';
import data from './features.yml';

const settings = {
  title: 'Collections/Offer/Features',
  tags: ['autodocs'],
  parameters: {
    layout: 'padded',
    docs: {
      description: {
        component:
          'Organism combining icon, title, and bulleted list. Composes existing Icon, Heading, and List atoms. Two-column layout available on desktop when items reach threshold.',
      },
    },
  },
  render: (args) => featuresTemplate(args),
  args: data.args || data,
  argTypes: {
    title: {
      description: 'Section title',
      table: {
        category: 'Content',
        type: { summary: 'string' },
      },
    },
    icon: {
      control: 'select',
      options: iconsRegistry.names,
      description: 'Icon name for data-icon attribute (without icon- prefix)',
      table: {
        category: 'Content',
        type: { summary: 'string' },
      },
    },
    items: {
      description: 'Array of list items (strings or HTML)',
      table: {
        category: 'Content',
        type: { summary: 'array' },
      },
    },
    columns_min_items: {
      control: 'number',
      description:
        'Minimum number of items to enable two-column layout. Set to 0 (default) to disable.',
      table: {
        category: 'Appearance',
        type: { summary: 'number' },
        defaultValue: { summary: '0' },
      },
    },
    attributes: {
      description: 'Drupal attributes object for root element',
      table: {
        category: 'Layout',
        type: { summary: 'Drupal.Attribute' },
      },
    },
  },
};

export const Default = {
  args: data.args || data,
};

export const Services = {
  name: 'Services (Two-Column)',
  args: {
    title: 'Services disponibles',
    icon: 'reception',
    columns_min_items: 5,
    items: [
      "Accueil : Hôtesses d'accueil",
      'Aménagement : 3 salles open-space',
      'Bureaux fermés : Oui',
      'Salle de réunion : Oui',
      'Cafétéria : Oui',
      "Sécurité : Contrôle d'accès",
      'Terrasse : Balcon vue parc',
    ],
  },
};

export const BuildingCondition = {
  args: {
    title: 'État du bâtiment',
    icon: 'office',
    columns_min_items: 0,
    items: [
      'État général : Restructuré avec parties communes',
      "État des locaux : Bon état d'usage",
    ],
  },
};

export const MoreInformation = {
  args: {
    title: 'Informations supplémentaires',
    icon: 'info',
    columns_min_items: 0,
    items: ['Plusieurs cours intérieures', 'Rez-de-chaussée jardin en bon état', 'Très flexible'],
  },
};

export default settings;
