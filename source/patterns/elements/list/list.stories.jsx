/**
 * List (Atom)
 *
 * Basic HTML list component for content (ul/ol).
 * ul = bulleted by default, ol = numbered by default.
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
          'HTML list element with native styles by default: ul displays disc markers, ol displays decimal numbers. Use "unstyled" variant to remove markers. Supports nested lists with automatic style cascade.',
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
      options: [null, 'bulleted', 'disc', 'circle', 'square', 'numbered', 'unstyled'],
      description:
        'Optional modifier to override default styles. null=native (ul=disc, ol=decimal), bulleted=force disc cascade, disc/circle/square=specific marker, numbered=force decimal cascade, unstyled=no markers.',
      table: {
        category: 'Appearance',
        type: { summary: 'string' },
        defaultValue: { summary: 'null' },
      },
    },
    type: {
      control: 'select',
      options: ['ul', 'ol'],
      description: 'HTML element type: ul (bulleted by default) or ol (numbered by default).',
      table: {
        category: 'Structure',
        type: { summary: 'string' },
        defaultValue: { summary: 'ul' },
      },
    },
  },
};

/**
 * Default bulleted list (ul with native disc markers)
 */
export const Default = {
  args: data,
};

/**
 * Numbered list (ol with native decimal markers)
 */
export const Numbered = {
  args: {
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
      <ul class="ps-list">
        <li class="ps-list__item">Commercial Real Estate Services
          <ul class="ps-list">
            <li class="ps-list__item">Office space leasing</li>
            <li class="ps-list__item">Retail property management</li>
            <li class="ps-list__item">Industrial facilities
              <ul class="ps-list">
                <li class="ps-list__item">Logistics hubs</li>
                <li class="ps-list__item">Manufacturing sites</li>
                <li class="ps-list__item">Warehouses</li>
              </ul>
            </li>
          </ul>
        </li>
        <li class="ps-list__item">Residential Real Estate
          <ul class="ps-list">
            <li class="ps-list__item">Luxury apartments</li>
            <li class="ps-list__item">Family homes</li>
            <li class="ps-list__item">Student housing</li>
          </ul>
        </li>
      </ul>
    `;
  },
};

/**
 * Real estate property features (typical use case)
 */
export const PropertyFeatures = {
  args: {
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
 * Steps process (numbered variant using ol)
 */
export const StepsProcess = {
  args: {
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

/**
 * Explicit marker variants (override defaults)
 */
export const MarkerVariants = {
  render: () => {
    return `
      <div style="display: grid; gap: 24px;">
        <div>
          <h4 style="margin-bottom: 8px; font-weight: 600;">Disc (explicit)</h4>
          ${listTemplate({
            variant: 'disc',
            items: ['Item with disc marker', 'Another disc item', 'Third disc item'],
          })}
        </div>
        <div>
          <h4 style="margin-bottom: 8px; font-weight: 600;">Circle</h4>
          ${listTemplate({
            variant: 'circle',
            items: ['Item with circle marker', 'Another circle item', 'Third circle item'],
          })}
        </div>
        <div>
          <h4 style="margin-bottom: 8px; font-weight: 600;">Square</h4>
          ${listTemplate({
            variant: 'square',
            items: ['Item with square marker', 'Another square item', 'Third square item'],
          })}
        </div>
      </div>
    `;
  },
};
