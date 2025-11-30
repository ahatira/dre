import productCardTwig from './product-card.twig';

export default {
  title: 'Components/Product Card',
  tags: ['autodocs'],
  render: (args) => productCardTwig(args),
  argTypes: {
    // Layout
    layout: {
      control: 'select',
      options: ['vertical', 'horizontal'],
      description: 'Card layout orientation',
      table: { category: 'Layout' },
    },

    // Content
    title: {
      control: 'text',
      description: 'Property title',
      table: { category: 'Content' },
    },
    surface: {
      control: 'text',
      description: 'Surface area (e.g., "611.3 m²")',
      table: { category: 'Content' },
    },
    price: {
      control: 'text',
      description: 'Price text',
      table: { category: 'Content' },
    },

    // Image
    image: {
      control: 'object',
      description: 'Image data with url and alt',
      table: { category: 'Content' },
    },

    // Meta
    meta: {
      control: 'object',
      description: 'Array of metadata items (icon, text)',
      table: { category: 'Content' },
    },

    // Status
    status: {
      control: 'object',
      description: 'Status badges: { viewed: boolean, exclusivity: boolean }',
      table: { category: 'Appearance' },
    },

    // CTA
    cta: {
      control: 'object',
      description: 'Call-to-action: { text: string, url: string }',
      table: { category: 'Behavior' },
    },

    // Link
    url: {
      control: 'text',
      description: 'Optional card link URL',
      table: { category: 'Behavior' },
    },
  },
};

// Default Story
export const Default = {
  args: {
    layout: 'vertical',
    title: 'Rent Offices MADRID Barrio de Chamberí',
    surface: '611.3 m²',
    price: '20 000 € HT/HC/m²/an',
    status: {
      viewed: true,
      exclusivity: true,
    },
    image: {
      url: 'https://picsum.photos/400/400?random=1',
      alt: 'Office space in Madrid',
    },
    meta: [{ icon: 'pin-map', text: '28010 MADRID' }],
    cta: {
      text: 'View the property',
      url: '#property-123',
    },
  },
};

// Horizontal Layout
export const HorizontalLayout = {
  args: {
    layout: 'horizontal',
    title: 'Rent Offices MADRID Barrio de Chamberí',
    surface: '611.3 m²',
    price: '20 000 € HT/HC/m²/an',
    status: {
      viewed: true,
      exclusivity: true,
    },
    image: {
      url: 'https://picsum.photos/242/212?random=2',
      alt: 'Office space',
    },
    meta: [{ icon: 'pin-map', text: '28010 MADRID' }],
    cta: {
      text: 'View the property',
      url: '#property-456',
    },
  },
};

// Without Status Badges
export const WithoutStatus = {
  args: {
    layout: 'vertical',
    title: 'Sale Apartment PARIS 16ème',
    surface: '120 m²',
    price: '1 500 000 €',
    image: {
      url: 'https://picsum.photos/400/400?random=3',
      alt: 'Apartment in Paris',
    },
    meta: [{ icon: 'pin-map', text: '75016 PARIS' }],
    cta: {
      text: 'View the property',
      url: '#property-789',
    },
  },
};

// As Link (entire card clickable)
export const AsLink = {
  args: {
    layout: 'vertical',
    title: 'Rent Office LYON Part-Dieu',
    surface: '450 m²',
    price: '15 000 € HT/HC/m²/an',
    url: '#property-full-link',
    image: {
      url: 'https://picsum.photos/400/400?random=4',
      alt: 'Office in Lyon',
    },
    meta: [{ icon: 'pin-map', text: '69003 LYON' }],
    cta: {
      text: 'View the property',
      url: '#property-cta',
    },
  },
};
