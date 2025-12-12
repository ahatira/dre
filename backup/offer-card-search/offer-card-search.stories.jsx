import offerCardSearchTwig from './offer-card-search.twig';
import data from './offer-card-search.yml';

const settings = {
  title: 'Components/Offer Card Search',
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component:
          '**Specialized card for real estate property listings** that embeds the generic Card component.\n\n' +
          '### Key Features\n' +
          '- **Image Overlay**: Status badges (viewed, exclusivity) and action buttons (compare, favorite) positioned over property image\n' +
          '- **Property Information**: Title, surface area, location metadata, and price\n' +
          '- **Responsive Layouts**: Vertical (default, mobile-friendly) or horizontal (desktop grid)\n' +
          '- **Clickable Cards**: Add `url` prop to make entire card interactive\n' +
          '- **Pixel Perfect**: Matches Figma design specifications exactly\n\n' +
          '### Composition\n' +
          'Embeds `@components/card/card.twig` using Twig blocks pattern for maximum flexibility.',
      },
    },
  },
  argTypes: {
    layout: {
      control: { type: 'inline-radio' },
      options: ['vertical', 'horizontal'],
      description: 'Card layout orientation',
      table: {
        category: 'Layout',
        type: { summary: 'vertical | horizontal' },
        defaultValue: { summary: 'vertical' },
      },
    },
    title: {
      control: 'text',
      description: 'Property title',
      table: {
        category: 'Content',
        type: { summary: 'string' },
      },
    },
    surface: {
      control: 'text',
      description: 'Surface area (e.g., "611.3 m²")',
      table: {
        category: 'Content',
        type: { summary: 'string' },
      },
    },
    price: {
      control: 'text',
      description: 'Price with unit (e.g., "20 000 € HT/HC/m²/an")',
      table: {
        category: 'Content',
        type: { summary: 'string' },
      },
    },
    'image.url': {
      control: 'text',
      description: 'Image URL',
      table: {
        category: 'Content',
        type: { summary: 'string' },
      },
    },
    'image.alt': {
      control: 'text',
      description: 'Image alt text',
      table: {
        category: 'Content',
        type: { summary: 'string' },
      },
    },
    meta: {
      control: 'object',
      description: 'Metadata items: [{ icon: string, text: string }]',
      table: {
        category: 'Content',
        type: { summary: 'array' },
      },
    },
    'status.viewed': {
      control: 'boolean',
      description: 'Show "Already viewed" badge',
      table: {
        category: 'Appearance',
        type: { summary: 'boolean' },
        defaultValue: { summary: false },
      },
    },
    'status.exclusivity': {
      control: 'boolean',
      description: 'Show "Exclusivity" badge',
      table: {
        category: 'Appearance',
        type: { summary: 'boolean' },
        defaultValue: { summary: false },
      },
    },
    'cta.text': {
      control: 'text',
      description: 'CTA link text',
      table: {
        category: 'Link',
        type: { summary: 'string' },
      },
    },
    'cta.url': {
      control: 'text',
      description: 'CTA link URL',
      table: {
        category: 'Link',
        type: { summary: 'string' },
      },
    },
    url: {
      control: 'text',
      description: 'Optional card link URL (makes entire card clickable)',
      table: {
        category: 'Behavior',
        type: { summary: 'string | undefined' },
      },
    },
  },
};

export default settings;

// ==============================================
// STORY 1: Default (Interactive Playground)
// ==============================================

export const Default = {
  render: (args) => offerCardSearchTwig(args),
  args: {
    ...data,
  },
};

// ==============================================
// STORY 2: Status Badges Variations
// ==============================================

export const StatusBadges = {
  render: () => {
    const statusVariations = [
      {
        label: 'Both Badges',
        status: { viewed: true, exclusivity: true },
        description: 'Property viewed by user + exclusive listing',
      },
      {
        label: 'Already Viewed',
        status: { viewed: true, exclusivity: false },
        description: 'User has previously viewed this property',
      },
      {
        label: 'Exclusivity',
        status: { viewed: false, exclusivity: true },
        description: 'Exclusive property available only through BNP Paribas',
      },
      {
        label: 'No Badges',
        status: { viewed: false, exclusivity: false },
        description: 'Fresh listing without status indicators',
      },
    ];

    return `
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">
        ${statusVariations
          .map(
            ({ label, status, description }) => `
          <div>
            <h4 style="margin: 0 0 0.25rem 0; font-size: 0.875rem; font-weight: 600; color: var(--text-primary);">${label}</h4>
            <p style="margin: 0 0 0.75rem 0; font-size: 0.75rem; color: var(--text-secondary);">${description}</p>
            ${offerCardSearchTwig({
              ...data,
              status,
              layout: 'vertical',
            })}
          </div>
        `
          )
          .join('')}
      </div>
    `;
  },
  parameters: {
    docs: {
      description: {
        story:
          '**Status badges** provide visual feedback about property availability and user interaction:\n\n' +
          '- **Both Badges**: Most informative state - exclusive + already seen\n' +
          '- **Already Viewed**: Helps users track which properties they explored\n' +
          '- **Exclusivity**: Premium indicator for exclusive BNP Paribas listings\n' +
          '- **No Badges**: Clean state for standard new listings',
      },
    },
  },
};

// ==============================================
// STORY 3: Real Estate Property Types
// ==============================================

export const PropertyTypes = {
  render: () => {
    const properties = [
      {
        title: 'Rent Offices PARIS La Défense',
        surface: '2 450 m²',
        price: '650 € HT/HC/m²/an',
        image: {
          url: '/images/building.jpg',
          alt: 'Modern office building in La Défense',
        },
        meta: [
          { icon: 'pin-map', text: 'Paris - La Défense' },
          { icon: 'building', text: 'Tour First' },
        ],
        status: { viewed: false, exclusivity: true },
      },
      {
        title: 'Sale Retail Space LYON Part-Dieu',
        surface: '180 m²',
        price: '4 500 € HT/m²',
        image: {
          url: '/images/3-2.jpg',
          alt: 'Retail storefront in Lyon',
        },
        meta: [
          { icon: 'pin-map', text: 'Lyon - Part-Dieu' },
          { icon: 'shop', text: 'Commercial Center' },
        ],
        status: { viewed: true, exclusivity: false },
      },
      {
        title: 'Rent Warehouse MARSEILLE Fos-sur-Mer',
        surface: '8 000 m²',
        price: '55 € HT/HC/m²/an',
        image: {
          url: '/images/16-9.jpg',
          alt: 'Industrial warehouse exterior',
        },
        meta: [
          { icon: 'pin-map', text: 'Marseille - Fos-sur-Mer' },
          { icon: 'warehouse', text: 'Logistics Hub' },
        ],
        status: { viewed: false, exclusivity: false },
      },
    ];

    return `
      <div style="display: flex; flex-direction: column; gap: 2rem;">
        ${properties
          .map(
            (property) => `
          <div>
            ${offerCardSearchTwig({
              ...property,
              layout: 'horizontal',
              cta: { text: 'View property details', url: '#' },
            })}
          </div>
        `
          )
          .join('')}
      </div>
    `;
  },
  parameters: {
    docs: {
      description: {
        story:
          '**Real estate property types** showcase the component with authentic business contexts:\n\n' +
          '- **Office Spaces**: High-end business districts (La Défense, Paris)\n' +
          '- **Retail Spaces**: Commercial centers and storefronts\n' +
          '- **Industrial/Logistics**: Warehouses and distribution centers\n\n' +
          'Each property type uses appropriate metadata (location, building type) and realistic pricing models.',
      },
    },
  },
};

// ==============================================
// STORY 4: Layout Comparison
// ==============================================

export const LayoutComparison = {
  render: () => {
    return `
      <div style="display: flex; flex-direction: column; gap: 3rem;">
        <!-- Vertical Layout -->
        <section>
          <h3 style="margin: 0 0 0.5rem 0; font-size: 1rem; font-weight: 600; color: var(--text-primary);">Vertical Layout (Default)</h3>
          <p style="margin: 0 0 1rem 0; font-size: 0.875rem; color: var(--text-secondary);">
            Image on top, content below. <strong>Ideal for:</strong> mobile devices, card grids (3-4 columns), property galleries
          </p>
          <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">
            ${offerCardSearchTwig({
              ...data,
              layout: 'vertical',
              status: { viewed: false, exclusivity: true },
            })}
            ${offerCardSearchTwig({
              ...data,
              layout: 'vertical',
              title: 'Rent Offices BARCELONA Passeig de Gràcia',
              surface: '890 m²',
              price: '380 € HT/HC/m²/an',
              image: {
                url: '/images/building.jpg',
                alt: 'Modern office in Barcelona',
              },
              status: { viewed: true, exclusivity: false },
            })}
          </div>
        </section>
        
        <!-- Horizontal Layout -->
        <section>
          <h3 style="margin: 0 0 0.5rem 0; font-size: 1rem; font-weight: 600; color: var(--text-primary);">Horizontal Layout</h3>
          <p style="margin: 0 0 1rem 0; font-size: 0.875rem; color: var(--text-secondary);">
            Image left (40%), content right (60%). <strong>Ideal for:</strong> desktop list views, search results, property comparison tables
          </p>
          <div style="display: flex; flex-direction: column; gap: 1rem;">
            ${offerCardSearchTwig({
              ...data,
              layout: 'horizontal',
              status: { viewed: false, exclusivity: true },
            })}
            ${offerCardSearchTwig({
              ...data,
              layout: 'horizontal',
              title: 'Sale Offices BERLIN Alexanderplatz',
              surface: '1 200 m²',
              price: '8 500 € HT/m²',
              image: {
                url: '/images/3-2.jpg',
                alt: 'Office building in Berlin',
              },
              meta: [
                { icon: 'pin-map', text: 'Berlin - Alexanderplatz' },
                { icon: 'building', text: 'Tech District' },
              ],
              status: { viewed: true, exclusivity: false },
            })}
          </div>
        </section>
      </div>
    `;
  },
  parameters: {
    docs: {
      description: {
        story:
          '**Layout variants** optimize card presentation for different UI contexts:\n\n' +
          '- **Vertical**: Maximizes image impact, works in grids, mobile-first\n' +
          '- **Horizontal**: Scan-friendly for lists, better text legibility, desktop-optimized\n\n' +
          '⚠️ **Responsive behavior**: Horizontal layout automatically stacks to vertical on screens < 768px (handled by Card component).',
      },
    },
  },
};

// ==============================================
// STORY 5: Minimal Configuration
// ==============================================

export const Minimal = {
  render: () => {
    return `
      <div style="max-width: 400px;">
        ${offerCardSearchTwig({
          title: 'Rent Offices AMSTERDAM Zuidas',
          image: {
            url: '/images/building.jpg',
            alt: 'Office building in Amsterdam',
          },
        })}
      </div>
    `;
  },
  parameters: {
    docs: {
      description: {
        story:
          '**Minimal configuration** with only required props (`title`, `image`).\n\n' +
          'All optional props (surface, price, meta, status badges, CTA) are omitted. The component gracefully handles missing data with sensible defaults:\n\n' +
          '- No status badges displayed\n' +
          '- Default CTA text: "View the property"\n' +
          '- Vertical layout\n' +
          '- No metadata items',
      },
    },
  },
};
