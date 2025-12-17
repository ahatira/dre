/**
 * Definition List (Molecule)
 *
 * Key-value pairs list using HTML <dl>, <dt>, <dd> structure.
 * Simple and semantic component for specifications and structured data.
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
          'Semantic definition list for key-value data. Three layout variants: default (stacked), inline (horizontal), grid (2-column responsive). Ideal for property specifications, features, and structured information.',
      },
    },
  },
  render: (args) => definitionListTemplate(args),
  argTypes: {
    items: {
      control: 'object',
      description: 'Array of definition objects with term and definition.',
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
      { term: 'Surface', definition: '1,250 m²' },
      { term: 'Floor', definition: '3rd floor' },
      { term: 'Availability', definition: 'Q2 2026' },
      { term: 'Price', definition: '540 €/m²/year' },
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
      { term: 'Total Surface', definition: '1,250 m² divisible' },
      { term: 'Floor', definition: '3rd floor with elevator' },
      { term: 'Availability', definition: 'Q2 2026 (April-June)' },
      { term: 'Rental Price', definition: '540 €/m²/year' },
      { term: 'Energy Class', definition: 'Class B (85 kWh/m²/year)' },
      { term: 'Parking', definition: '50 spaces + 20 visitor spots' },
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
      { term: 'Air Conditioning', definition: 'VRV system with individual zone control' },
      { term: 'Lighting', definition: 'LED lighting with motion sensors and dimming' },
      { term: 'Internet', definition: 'Fiber optic 10 Gbps symmetrical' },
      { term: 'Security', definition: 'Badge access, CCTV, 24/7 surveillance' },
      { term: 'Accessibility', definition: 'Wheelchair ramps, elevators, adapted facilities' },
      { term: 'Services', definition: 'Cafeteria, fitness center, bike storage, showers' },
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
      { term: 'Base Rent', definition: '540 €/m²/year' },
      { term: 'Service Charges', definition: '85 €/m²/year' },
      { term: 'Property Tax', definition: '42 €/m²/year' },
      { term: 'Total Cost', definition: '667 €/m²/year (all-in)' },
      { term: 'Deposit', definition: '3 months rent' },
      { term: 'Lease Term', definition: '6 years (3/6/9)' },
    ],
  },
};
