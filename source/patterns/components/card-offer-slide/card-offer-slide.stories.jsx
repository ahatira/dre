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
// STORY: Favorite States
// ==============================================

export const FavoriteStates = {
  render: () => {
    return `
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: var(--size-6);">
        <div>
          <p style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-0); font-weight: var(--font-weight-600); color: var(--text-secondary);">Not Favorite</p>
          ${cardOfferSlideTwig({
            ...data,
            isFavorite: false,
          })}
        </div>
        
        <div>
          <p style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-0); font-weight: var(--font-weight-600); color: var(--text-secondary);">Favorite (Active)</p>
          ${cardOfferSlideTwig({
            ...data,
            isFavorite: true,
          })}
        </div>
      </div>
    `;
  },
  parameters: {
    docs: {
      description: {
        story:
          '**Favorite button toggle states**:\n\n' +
          '- **Not Favorite**: Outline heart icon, white background\n' +
          '- **Favorite (Active)**: Filled heart icon, red background, white icon',
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
        title: 'Office Space PARIS',
        price: '650 €',
        surface: '2 450 m²',
        location: 'Paris - La Défense',
        image: { url: '/images/building.jpg', alt: 'Office in Paris' },
        isFavorite: true,
      },
      {
        title: 'Retail Space LYON',
        price: '4 500 €',
        surface: '180 m²',
        location: 'Lyon - Part-Dieu',
        image: { url: '/images/3-2.jpg', alt: 'Retail in Lyon' },
        isFavorite: false,
      },
      {
        title: 'Warehouse MARSEILLE',
        price: '55 €',
        surface: '8 000 m²',
        location: 'Marseille - Fos-sur-Mer',
        image: { url: '/images/16-9.jpg', alt: 'Warehouse in Marseille' },
        isFavorite: false,
      },
      {
        title: 'Office BARCELONA',
        price: '380 €',
        surface: '890 m²',
        location: 'Barcelona - Passeig de Gràcia',
        image: { url: '/images/building.jpg', alt: 'Office in Barcelona' },
        isFavorite: true,
      },
      {
        title: 'Commercial Space MADRID',
        price: '1 200 €',
        surface: '450 m²',
        location: 'Madrid - Salamanca',
        image: { url: '/images/3-2.jpg', alt: 'Commercial space in Madrid' },
        isFavorite: false,
      },
      {
        title: 'Office Building LISBON',
        price: '320 €',
        surface: '1 500 m²',
        location: 'Lisbon - Avenida da Liberdade',
        image: { url: '/images/building.jpg', alt: 'Office in Lisbon' },
        isFavorite: false,
      },
    ];

    return `
      <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: var(--size-6);">
        ${properties
          .map(
            (property) => `
          ${cardOfferSlideTwig({
            ...property,
            cta: { text: "Consulter l'annonce", url: '#' },
          })}
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
          '**Property listing grid** with 6 cards in responsive layout.\n\n' +
          'Grid automatically adjusts columns based on available width (minimum 280px per card). ' +
          'Ideal for listing pages, search results, and homepage featured properties.',
      },
    },
  },
};

// ==============================================
// STORY: Minimal Configuration
// ==============================================

export const Minimal = {
  render: () => {
    return `
      <div style="max-width: 320px;">
        ${cardOfferSlideTwig({
          title: 'Office Space AMSTERDAM',
          image: {
            url: '/images/building.jpg',
            alt: 'Office in Amsterdam',
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
          'All optional fields omitted: price, surface, location, favorite state. ' +
          'Default CTA text: "Consulter l\'annonce".',
      },
    },
  },
};

// ==============================================
// STORY: Without CTA
// ==============================================

export const WithoutCTA = {
  render: () => {
    return `
      <div style="max-width: 320px;">
        ${cardOfferSlideTwig({
          ...data,
          cta: null,
        })}
      </div>
    `;
  },
  parameters: {
    docs: {
      description: {
        story:
          '**Card without CTA link** for preview/display contexts where click action is handled externally.',
      },
    },
  },
};
