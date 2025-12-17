/**
 * Feature Section (Molecule)
 *
 * Thematic section composing Icon + Heading + List atoms.
 * Used for property features, services, building info, etc.
 */

import iconsRegistry from '../../documentation/icons-registry.json';
import featureSectionTemplate from './feature-section.twig';
import data from './feature-section.yml';

export default {
  title: 'Components/Feature Section',
  tags: ['autodocs'],
  parameters: {
    layout: 'padded',
    docs: {
      description: {
        component:
          'Molecule combining icon, title, and bulleted list. Composes existing Icon, Heading, and List atoms. Two-columns variant available for responsive layout on larger screens.',
      },
    },
  },
  render: (args) => featureSectionTemplate(args),
  argTypes: {
    title: {
      control: 'text',
      description: 'Section title.',
      table: {
        category: 'Content',
        type: { summary: 'string' },
      },
    },
    icon: {
      control: 'select',
      options: iconsRegistry.names,
      description: 'Icon name for data-icon attribute (without "icon-" prefix).',
      table: {
        category: 'Content',
        type: { summary: 'string' },
      },
    },
    items: {
      control: 'object',
      description: 'Array of list items (strings or HTML).',
      table: {
        category: 'Content',
        type: { summary: 'array' },
      },
    },
    columns_min_items: {
      control: 'number',
      description:
        'Minimum number of items to enable two-columns layout. Set to 0 to disable (default). Set to threshold (e.g., 5) to enable when items >= threshold.',
      table: {
        category: 'Appearance',
        type: { summary: 'number' },
        defaultValue: { summary: '0' },
      },
    },
  },
};

/**
 * Default: Equipments section (single column)
 */
export const Default = {
  args: data,
};

/**
 * Services section (two columns on desktop)
 */
export const Services = {
  args: {
    title: 'Services',
    icon: 'office',
    columns_min_items: 5,
    items: [
      'Home : Hostesses',
      'Office layout: 3 open-plan offices',
      'Partitioned offices: Yes',
      'Meeting room: Yes',
      'Cafeteria: Yes',
      'Security: Intercom access control',
      'Terraces - gardens : Balcony overlooking the park',
    ],
  },
};

/**
 * Building condition section
 */
export const BuildingCondition = {
  args: {
    title: 'Building condition',
    icon: 'commercial-space',
    columns_min_items: 0,
    items: [
      'Condition of building: Restructured with common area',
      'Condition of premises : state of use',
    ],
  },
};

/**
 * More information section
 */
export const MoreInformation = {
  args: {
    title: 'More information',
    icon: 'info',
    columns_min_items: 0,
    items: ['Several courtyards', 'Garden level in perfect condition', 'Highly flexible'],
  },
};
