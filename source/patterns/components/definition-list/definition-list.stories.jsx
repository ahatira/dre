/**
 * Definition List (Molecule)
 *
 * Key-value pairs list using HTML <dl>, <dt>, <dd> structure.
 * Designed for specifications, features, and structured data.
 */

import definitionListTemplate from './definition-list.twig';
import data from './definition-list.yml';

export default {
  title: 'Components/Definition List',
  tags: ['autodocs'],
  parameters: {
    layout: 'padded',
    docs: {
      description: {
        component:
          'Semantic definition list for key-value data. Supports optional icons, three layout variants (default stacked, inline horizontal, grid 2-column). Ideal for property specifications, features, and structured information.',
      },
    },
  },
  render: (args) => definitionListTemplate(args),
  argTypes: {
    items: {
      control: 'object',
      description: 'Array of definition objects with term, definition, and optional icon.',
      table: {
        category: 'Content',
        type: { summary: 'array' },
      },
    },
    variant: {
      control: 'select',
      options: ['default', 'inline', 'grid'],
      description: 'Layout variant: default (stacked), inline (horizontal), grid (2-column).',
      table: {
        category: 'Appearance',
        type: { summary: 'string' },
        defaultValue: { summary: 'default' },
      },
    },
  },
};

/**
 * Default stacked layout (vertical term/definition)
 */
export const Default = {
  args: data,
};

/**
 * Inline variant (term and definition on same line)
 */
export const Inline = {
  args: {
    variant: 'inline',
    items: [
      { term: 'Surface', definition: '1,250 m²', icon: 'area' },
      { term: 'Floor', definition: '3rd floor', icon: 'building' },
      { term: 'Availability', definition: 'Q2 2026', icon: 'calendar' },
      { term: 'Price', definition: '540 €/m²/year', icon: 'euro' },
    ],
  },
};

/**
 * Grid variant (2-column layout on larger screens)
 */
export const Grid = {
  args: {
    variant: 'grid',
    items: [
      { term: 'Total Surface', definition: '1,250 m² divisible', icon: 'area' },
      { term: 'Floor', definition: '3rd floor with elevator', icon: 'building' },
      { term: 'Availability', definition: 'Q2 2026 (April-June)', icon: 'calendar' },
      { term: 'Rental Price', definition: '540 €/m²/year', icon: 'euro' },
      { term: 'Energy Class', definition: 'Class B (85 kWh/m²/year)', icon: 'energy' },
      { term: 'Parking', definition: '50 spaces + 20 visitor spots', icon: 'car' },
    ],
  },
};

/**
 * Without icons (simple key-value pairs)
 */
export const WithoutIcons = {
  args: {
    variant: 'default',
    items: [
      { term: 'Property Type', definition: 'Office Space' },
      { term: 'Location', definition: 'La Défense, Puteaux (92)' },
      { term: 'Construction Year', definition: '2022' },
      { term: 'Certification', definition: 'BREEAM Excellent' },
    ],
  },
};

/**
 * Building amenities (typical use case)
 */
export const BuildingAmenities = {
  args: {
    variant: 'default',
    items: [
      {
        term: 'Air Conditioning',
        definition: 'VRV system with individual zone control',
        icon: 'air-conditioning',
      },
      {
        term: 'Lighting',
        definition: 'LED lighting with motion sensors and dimming',
        icon: 'lightbulb',
      },
      {
        term: 'Internet',
        definition: 'Fiber optic 10 Gbps symmetrical',
        icon: 'wifi',
      },
      {
        term: 'Security',
        definition: 'Badge access, CCTV, 24/7 surveillance',
        icon: 'security',
      },
      {
        term: 'Accessibility',
        definition: 'Wheelchair ramps, elevators, adapted facilities',
        icon: 'accessibility',
      },
      {
        term: 'Services',
        definition: 'Cafeteria, fitness center, bike storage, showers',
        icon: 'services',
      },
    ],
  },
};

/**
 * Property financials (inline variant)
 */
export const PropertyFinancials = {
  args: {
    variant: 'inline',
    items: [
      { term: 'Base Rent', definition: '540 €/m²/year', icon: 'euro' },
      { term: 'Service Charges', definition: '85 €/m²/year', icon: 'receipt' },
      { term: 'Property Tax', definition: '42 €/m²/year', icon: 'document' },
      { term: 'Total Cost', definition: '667 €/m²/year (all-in)', icon: 'calculator' },
      { term: 'Deposit', definition: '3 months rent', icon: 'bank' },
      { term: 'Lease Term', definition: '6 years (3/6/9)', icon: 'contract' },
    ],
  },
};
