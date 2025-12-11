import cardOfferSlideTwig from './card-offer-slide.twig';
import data from './card-offer-slide.yml';

export default {
  title: 'Components/Card Offer Slide',
  tags: ['autodocs'],
  render: (args) => cardOfferSlideTwig(args),
  args: data,

  parameters: {
    docs: {
      description: {
        component:
          '**Compact card for real estate property listings** in sliders and galleries.\n\n' +
          '### Key Features\n' +
          '- **Vertical layout**: Image top, content below (mobile-optimized)\n' +
          '- **3:2 aspect ratio**: Standard property image format\n' +
          '- **Compact header**: "Price • Surface" single-line format\n' +
          '- **Favorite toggle**: Heart icon overlay (top-right, white background)\n' +
          '- **Essential info**: Title, location with pin icon, CTA link\n' +
          '- **Lightweight markup**: Minimal DOM for listing grids\n\n' +
          '### Use Cases\n' +
          'Property sliders, listing galleries, search result cards, homepage featured properties.',
      },
    },
  },

  argTypes: {
    // Content
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
      description: 'Price value (e.g., "650 €", "20 000 €")',
      table: {
        category: 'Content',
        type: { summary: 'string' },
      },
    },
    surface: {
      control: 'text',
      description: 'Surface area (e.g., "611 m²", "2 450 m²")',
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
      description: 'Image alt text for accessibility',
      table: {
        category: 'Content',
        type: { summary: 'string' },
      },
    },
    location: {
      control: 'text',
      description: 'Location text (e.g., "Madrid", "Paris - La Défense")',
      table: {
        category: 'Content',
        type: { summary: 'string' },
      },
    },

    // Appearance
    locationIcon: {
      control: 'text',
      description: 'Location icon name (without icon- prefix)',
      table: {
        category: 'Appearance',
        type: { summary: 'string' },
        defaultValue: { summary: 'pin-map' },
      },
    },

    // Behavior
    isFavorite: {
      control: 'boolean',
      description: 'Favorite state (filled/outlined heart icon)',
      table: {
        category: 'Behavior',
        type: { summary: 'boolean' },
        defaultValue: { summary: false },
      },
    },

    // CTA
    'cta.text': {
      control: 'text',
      description: 'CTA link text',
      table: {
        category: 'CTA',
        type: { summary: 'string' },
        defaultValue: { summary: "Consulter l'annonce" },
      },
    },
    'cta.url': {
      control: 'text',
      description: 'CTA link URL',
      table: {
        category: 'CTA',
        type: { summary: 'string' },
      },
    },

    // Drupal Integration
    bundle: {
      control: 'text',
      description: 'Drupal content type (generates .ps-card--type-{bundle} class)',
      table: {
        category: 'Drupal',
        type: { summary: 'string' },
        defaultValue: { summary: 'offer' },
      },
    },
    view_mode: {
      control: 'text',
      description: 'Drupal view mode (generates .ps-card--view-mode-{view_mode} class)',
      table: {
        category: 'Drupal',
        type: { summary: 'string' },
        defaultValue: { summary: 'slide' },
      },
    },
  },
};

// ==============================================
// STORY: Default
// ==============================================

export const Default = {
  args: data,
  parameters: {
    docs: {
      description: {
        story:
          '**Default card configuration** with all interactive controls.\n\n' +
          'Use Storybook controls panel to modify:\n' +
          '- Content: title, price, surface, location\n' +
          '- Image: URL and alt text\n' +
          '- Behavior: favorite state toggle\n' +
          '- CTA: link text and URL\n' +
          '- Drupal: bundle and view_mode classes',
      },
    },
  },
};

// ==============================================
// STORY: Property Grid
// ==============================================

export const PropertyGrid = {
  render: () => {
    const properties = [
      {
        title: 'Bureau PARIS La Défense',
        price: '650 €/m²/an',
        surface: '2 450 m²',
        location: 'Paris - La Défense',
        image: { url: '/images/1-1.jpg', alt: 'Bureau moderne à La Défense' },
        isFavorite: true,
        cta: { text: "Consulter l'annonce", url: '#property-paris' },
      },
      {
        title: 'Local Commercial LYON Part-Dieu',
        price: '4 500 €/mois',
        surface: '180 m²',
        location: 'Lyon - Part-Dieu',
        image: { url: '/images/3-2.jpg', alt: 'Local commercial à Lyon' },
        isFavorite: false,
        cta: { text: "Consulter l'annonce", url: '#property-lyon' },
      },
      {
        title: 'Entrepôt MARSEILLE Fos-sur-Mer',
        price: '20 000 € HT/HC/m²/an',
        surface: '8 000 m²',
        location: 'Marseille - Fos-sur-Mer',
        image: { url: '/images/16-9.jpg', alt: 'Entrepôt logistique à Marseille' },
        isFavorite: false,
        cta: { text: "Consulter l'annonce", url: '#property-marseille' },
      },
      {
        title: 'Bureau BARCELONA Passeig de Gràcia',
        price: '380 €',
        surface: '890 m²',
        location: 'Barcelona - Passeig de Gràcia',
        image: { url: '/images/4-3.jpg', alt: 'Bureau prestige à Barcelone' },
        isFavorite: true,
        cta: { text: "Consulter l'annonce", url: '#property-barcelona' },
      },
      {
        title: 'Surface Commerciale MADRID Salamanca',
        price: '1 200 €/mois',
        surface: '450 m²',
        location: 'Madrid - Salamanca',
        image: { url: '/images/2-3.jpg', alt: 'Surface commerciale à Madrid' },
        isFavorite: false,
        cta: { text: "Consulter l'annonce", url: '#property-madrid' },
      },
      {
        title: 'Immeuble de Bureaux LISBON Avenida',
        price: '12 500 € HT/HC',
        surface: '15 000 m²',
        location: 'Lisbon - Avenida da Liberdade',
        image: { url: '/images/3-4.jpg', alt: 'Immeuble de bureaux à Lisbonne' },
        isFavorite: false,
        cta: { text: "Consulter l'annonce", url: '#property-lisbon' },
      },
    ];

    return `
      <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: var(--size-6); padding: var(--size-4);">
        ${properties.map((property) => cardOfferSlideTwig(property)).join('')}
      </div>
    `;
  },
  parameters: {
    docs: {
      description: {
        story:
          '**Responsive property listing grid** with 6 real estate cards demonstrating **varied image aspect ratios**.\n\n' +
          '**Grid Features**:\n' +
          '- Auto-fill layout with minimum 280px per card\n' +
          '- Responsive columns (1-4 depending on viewport)\n' +
          '- Mixed favorite states (2 active, 4 inactive)\n' +
          '- Realistic Real Estate content (offices, retail, warehouses)\n\n' +
          '**Image Aspect Ratio Testing**:\n' +
          '- Card 1: 1:1 (square) → Cropped to 3:2\n' +
          '- Card 2: 3:2 (native ratio) → No cropping\n' +
          '- Card 3: 16:9 (wide) → Cropped to 3:2\n' +
          '- Card 4: 4:3 (traditional) → Cropped to 3:2\n' +
          '- Card 5: 2:3 (portrait) → Cropped to 3:2\n' +
          '- Card 6: 3:4 (portrait) → Cropped to 3:2\n\n' +
          'All images are normalized to 3:2 aspect ratio via CSS `aspect-ratio` property, demonstrating consistent visual presentation regardless of source image dimensions.\n\n' +
          '**Use Cases**: Property search results, listing pages, homepage featured properties, slider/carousel content.',
      },
    },
  },
};
