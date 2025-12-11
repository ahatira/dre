import cardOfferSearchTwig from './card-offer-search.twig';
import data from './card-offer-search.yml';

export default {
  title: 'Components/Card Offer Search',
  tags: ['autodocs'],
  render: (args) => cardOfferSearchTwig(args),
  args: data,

  parameters: {
    docs: {
      description: {
        component:
          '**Search result card for real estate properties** with horizontal layout on desktop.\n\n' +
          '### Key Features\n' +
          '- **Responsive layout**: Horizontal (desktop 768px+) → Vertical (mobile)\n' +
          '- **Image carousel**: Multiple photos with prev/next navigation\n' +
          '- **Status badges**: Already viewed (light), Exclusivity (gold)\n' +
          '- **Action buttons**: Comparator and Favorite toggles (top-right)\n' +
          '- **Complete info**: Title, surface, location with icon, price + unit\n' +
          '- **Primary CTA**: Prominent button in footer\n\n' +
          '### Use Cases\n' +
          'Search result pages, property listings, comparison grids, detailed previews.',
      },
    },
  },

  argTypes: {
    // ==========================================
    // Content - Primary data
    // ==========================================
    title: {
      control: 'text',
      description: 'Property title',
      table: {
        category: 'Content',
        type: { summary: 'string' },
      },
    },
    price: {
      control: 'text',
      description: 'Price value (e.g., "20 000 €")',
      table: {
        category: 'Content',
        type: { summary: 'string' },
      },
    },
    priceUnit: {
      control: 'text',
      description: 'Price unit (e.g., "HT/HC/m²/an")',
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
    images: {
      control: 'object',
      description: 'Array of image objects: [{ url: string, alt: string }]',
      table: {
        category: 'Content',
        type: { summary: 'array' },
      },
    },
    location: {
      control: 'text',
      description: 'Location text (e.g., "28010 MADRID")',
      table: {
        category: 'Content',
        type: { summary: 'string' },
      },
    },

    // ==========================================
    // Appearance - Visual options
    // ==========================================
    locationIcon: {
      control: 'text',
      description: 'Location icon name (without icon- prefix)',
      table: {
        category: 'Appearance',
        type: { summary: 'string' },
        defaultValue: { summary: 'pin' },
      },
    },

    // ==========================================
    // Behavior - Interactions & states
    // ==========================================
    isViewed: {
      control: 'boolean',
      description: 'Already viewed state (light badge with eye icon)',
      table: {
        category: 'Behavior',
        type: { summary: 'boolean' },
        defaultValue: { summary: false },
      },
    },
    isExclusive: {
      control: 'boolean',
      description: 'Exclusivity state (gold badge)',
      table: {
        category: 'Behavior',
        type: { summary: 'boolean' },
        defaultValue: { summary: false },
      },
    },
    isComparator: {
      control: 'boolean',
      description: 'Comparator active state (toggle button)',
      table: {
        category: 'Behavior',
        type: { summary: 'boolean' },
        defaultValue: { summary: false },
      },
    },
    isFavorite: {
      control: 'boolean',
      description: 'Favorite state (filled/outlined heart icon)',
      table: {
        category: 'Behavior',
        type: { summary: 'boolean' },
        defaultValue: { summary: false },
      },
    },

    // ==========================================
    // CTA - Call-to-action
    // ==========================================
    'cta.text': {
      control: 'text',
      description: 'CTA button text',
      table: {
        category: 'CTA',
        type: { summary: 'string' },
      },
    },
    'cta.url': {
      control: 'text',
      description: 'CTA link destination URL',
      table: {
        category: 'CTA',
        type: { summary: 'string' },
      },
    },

    // ==========================================
    // Drupal - Integration parameters
    // ==========================================
    bundle: {
      control: 'text',
      description: 'Drupal entity bundle (generates CSS classes)',
      table: {
        category: 'Drupal',
        type: { summary: 'string' },
        defaultValue: { summary: 'offer' },
      },
    },
    view_mode: {
      control: 'text',
      description: 'Drupal view mode (generates CSS classes)',
      table: {
        category: 'Drupal',
        type: { summary: 'string' },
        defaultValue: { summary: 'search' },
      },
    },
  },
};

/**
 * Default story - Interactive playground with all controls
 */
export const Default = {
  parameters: {
    docs: {
      description: {
        story:
          '**Interactive example** with all available controls.\n\n' +
          'Use the Controls panel to:\n' +
          '- Edit property details (title, price, surface, location)\n' +
          '- Toggle badges (viewed, exclusivity)\n' +
          '- Toggle actions (comparator, favorite)\n' +
          '- Change CTA text/URL\n' +
          '- Test responsive behavior (resize viewport)',
      },
    },
  },
};

/**
 * Search Results Grid - Multiple cards with varied states
 */
export const SearchResults = {
  render: () => {
    const properties = [
      {
        title: 'Rent Offices MADRID Barrio de Chamberí',
        price: '20 000 €',
        priceUnit: 'HT/HC/m²/an',
        surface: '611.3 m²',
        images: [
          { url: '/images/3-2.jpg', alt: 'Office space Madrid' },
          { url: '/images/building.jpg', alt: 'Building exterior' },
        ],
        location: '28010 MADRID',
        cta: { text: 'View the property', url: '#' },
        isViewed: true,
        isExclusive: true,
        isFavorite: false,
      },
      {
        title: 'Office PARIS La Défense',
        price: '650 €',
        priceUnit: 'per m²/year',
        surface: '2 450 m²',
        images: [{ url: '/images/1-1.jpg', alt: 'Office Paris' }],
        location: 'Paris - La Défense',
        cta: { text: "Consulter l'annonce", url: '#' },
        isViewed: false,
        isExclusive: false,
        isFavorite: true,
        isComparator: true,
      },
      {
        title: 'Retail Space BARCELONA Passeig de Gràcia',
        price: '4 500 €',
        priceUnit: 'per month',
        surface: '180 m²',
        images: [
          { url: '/images/16-9.jpg', alt: 'Retail Barcelona' },
          { url: '/images/4-3.jpg', alt: 'Interior view' },
        ],
        location: '08008 BARCELONA',
        cta: { text: 'Ver propiedad', url: '#' },
        isViewed: true,
        isExclusive: false,
        isFavorite: false,
      },
      {
        title: 'Warehouse LYON Fos-sur-Mer',
        price: '1 200 €',
        priceUnit: 'per month',
        surface: '8 000 m²',
        images: [{ url: '/images/2-3.jpg', alt: 'Warehouse Lyon' }],
        location: 'Lyon - Part-Dieu',
        cta: { text: 'Voir le bien', url: '#' },
        isViewed: false,
        isExclusive: true,
        isFavorite: true,
      },
      {
        title: 'Office LISBON Avenida da Liberdade',
        price: '15 000 €',
        priceUnit: 'HT/HC/m²/an',
        surface: '1 200 m²',
        images: [
          { url: '/images/3-4.jpg', alt: 'Office Lisbon' },
          { url: '/images/building.jpg', alt: 'Building view' },
        ],
        location: '1250 LISBON',
        cta: { text: 'View property', url: '#' },
        isViewed: false,
        isExclusive: false,
        isFavorite: false,
        isComparator: true,
      },
      {
        title: 'Commercial Space MARSEILLE Vieux-Port',
        price: '3 200 €',
        priceUnit: 'per month',
        surface: '350 m²',
        images: [{ url: '/images/4-3.jpg', alt: 'Commercial Marseille' }],
        location: '13001 MARSEILLE',
        cta: { text: 'Consulter', url: '#' },
        isViewed: true,
        isExclusive: true,
        isFavorite: true,
        isComparator: true,
      },
    ];

    return `
      <div style="display: flex; flex-direction: column; gap: var(--size-4); padding: var(--size-4); max-width: 1200px; margin: 0 auto;">
        ${properties.map((property) => cardOfferSearchTwig(property)).join('')}
      </div>
    `;
  },

  parameters: {
    docs: {
      description: {
        story:
          '**Responsive vertical stack** demonstrating varied states.\n\n' +
          '**Grid Features**:\n' +
          '- Vertical stacking (search results layout)\n' +
          '- Mixed states (viewed, exclusive, favorite, comparator)\n' +
          '- Varied content (different lengths, prices, units)\n' +
          '- Different image counts (1-2 photos per property)\n' +
          '- Real Estate data (offices, retail, warehouses)\n' +
          '- Multilingual CTA buttons (French, English, Spanish)\n\n' +
          '**Use Cases**: Search results, comparison pages, saved properties lists.',
      },
    },
  },
};
