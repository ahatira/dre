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
      url: 'https://images.unsplash.com/photo-1568605114967-8130f3a36994?w=400&h=400&fit=crop&q=80',
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
      url: 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=242&h=212&fit=crop&q=80',
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
      url: 'https://images.unsplash.com/photo-1560184897-ae75f418493e?w=400&h=400&fit=crop&q=80',
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
      url: 'https://images.unsplash.com/photo-1582407947304-fd86f028f716?w=400&h=400&fit=crop&q=80',
      alt: 'Office in Lyon',
    },
    meta: [{ icon: 'pin-map', text: '69003 LYON' }],
    cta: {
      text: 'View the property',
      url: '#property-cta',
    },
  },
};
