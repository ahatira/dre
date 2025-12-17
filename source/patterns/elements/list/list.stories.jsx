/**
 * List (Atom)
 *
 * Basic HTML list component for content (ul/ol).
 * Provides styled bullets, numbers, or unstyled variants.
 */

import listTemplate from './list.twig';
import data from './list.yml';

export default {
  title: 'Elements/List',
  tags: ['autodocs'],
  parameters: {
    layout: 'padded',
    docs: {
      description: {
        component:
          'HTML list element with three variants: bulleted (disc markers), numbered (decimal), or unstyled (no markers). Supports nested lists with automatic style cascade.',
      },
    },
  },
  render: (args) => listTemplate(args),
  argTypes: {
    items: {
      control: 'object',
      description: 'Array of list items (strings or HTML).',
      table: {
        category: 'Content',
        type: { summary: 'array' },
      },
    },
    variant: {
      control: 'select',
      options: ['bulleted', 'numbered', 'unstyled'],
      description: 'List style variant.',
      table: {
        category: 'Appearance',
        type: { summary: 'string' },
        defaultValue: { summary: 'bulleted' },
      },
    },
    type: {
      control: 'select',
      options: ['ul', 'ol'],
      description: 'HTML element type (auto-determined by variant if not specified).',
      table: {
        category: 'Structure',
        type: { summary: 'string' },
        defaultValue: { summary: 'ul' },
      },
    },
  },
};

/**
 * Default bulleted list (ul with disc markers)
 */
export const Default = {
  args: data,
};

/**
 * Numbered list (ol with decimal markers)
 */
export const Numbered = {
  args: {
    variant: 'numbered',
    type: 'ol',
    items: [
      'Schedule property viewing with our consultant',
      'Review financial documentation and lease terms',
      'Complete due diligence and site inspection',
      'Sign preliminary agreement and deposit',
      'Finalize lease contract and move-in logistics',
    ],
  },
};

/**
 * Unstyled list (no markers, useful for custom content)
 */
export const Unstyled = {
  args: {
    variant: 'unstyled',
    items: ['Item without marker', 'Another item', 'Custom styled content'],
  },
};

/**
 * Nested lists (multi-level structure)
 */
export const Nested = {
  render: () => {
    return `
      ${listTemplate({
        variant: 'bulleted',
        items: [
          'Commercial Real Estate Services',
          listTemplate({
            variant: 'bulleted',
            items: [
              'Office space leasing',
              'Retail property management',
              'Industrial facilities',
              listTemplate({
                variant: 'bulleted',
                items: ['Logistics hubs', 'Manufacturing sites', 'Warehouses'],
              }),
            ],
          }),
          'Residential Real Estate',
          listTemplate({
            variant: 'bulleted',
            items: ['Luxury apartments', 'Family homes', 'Student housing'],
          }),
        ],
      })}
    `;
  },
};

/**
 * Real estate property features (typical use case)
 */
export const PropertyFeatures = {
  args: {
    variant: 'bulleted',
    items: [
      'Total surface area: 1,250 m² divisible',
      'Floor-to-ceiling windows with natural light',
      'Raised access flooring for cable management',
      'VRV air conditioning system',
      'LED lighting with motion sensors',
      'Accessibility: ramps, elevators, disabled facilities',
      'Security: badge access, CCTV, 24/7 surveillance',
      'Parking: 50 spaces + 20 visitor spots',
    ],
  },
};

/**
 * Steps process (numbered variant)
 */
export const StepsProcess = {
  args: {
    variant: 'numbered',
    type: 'ol',
    items: [
      '<strong>Discovery</strong> - Define your space requirements and budget',
      '<strong>Search</strong> - Access exclusive property listings',
      '<strong>Visit</strong> - Tour selected properties with expert guidance',
      '<strong>Negotiate</strong> - Secure best terms with our support',
      '<strong>Move In</strong> - Coordinate logistics and settle in',
    ],
  },
};
