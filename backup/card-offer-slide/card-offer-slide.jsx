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
          '**Compact card for real estate property listings** in vertical layout.\n\n' +
          '### Key Features\n' +
          '- **Simplified Layout**: Vertical-only, mobile-optimized\n' +
          '- **Compact Header**: "Price • Surface" single-line format\n' +
          '- **Favorite Button**: Heart icon overlay (top right)\n' +
          '- **Essential Info**: Title, location with icon, CTA link\n' +
          '- **Lightweight**: Minimal markup for listing grids\n\n' +
          '### Use Cases\n' +
          'Property galleries, search result cards, listing pages with multiple items per row.',
      },
    },
  },
  argTypes: {
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
    surface: {
      control: 'text',
      description: 'Surface area (e.g., "611 m²")',
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
    location: {
      control: 'text',
      description: 'Location text (e.g., "28010 MADRID")',
      table: {
        category: 'Content',
        type: { summary: 'string' },
      },
    },
    locationIcon: {
      control: 'text',
      description: 'Location icon name (default: "pin-map")',
      table: {
        category: 'Appearance',
        type: { summary: 'string' },
        defaultValue: { summary: 'pin-map' },
      },
    },
    isFavorite: {
      control: 'boolean',
      description: 'Favorite state (filled heart icon)',
      table: {
        category: 'Behavior',
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

// ==============================================
// STORY 1: Favorite States
// ==============================================

export const FavoriteStates = {
  render: () => {
    return `
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">
        <div>
          <h4 style="margin: 0 0 0.5rem 0; font-size: 0.875rem; font-weight: 600;">Not Favorite</h4>
          ${cardOfferSlideTwig({
            ...data,
            isFavorite: false,
          })}
        </div>
        
        <div>
          <h4 style="margin: 0 0 0.5rem 0; font-size: 0.875rem; font-weight: 600;">Favorite (Active)</h4>
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
          '**Favorite button states** show heart icon toggle:\n\n' +
          '- **Not Favorite**: Outline heart icon (gray)\n' +
          '- **Favorite**: Filled heart icon (red)',
      },
    },
  },
};

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
    ];

    return `
      <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.5rem;">
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
          '**Property listing grid** demonstrates multiple cards in a responsive layout.\n\n' +
          'Grid automatically adjusts columns based on available width (minimum 280px per card).',
      },
    },
  },
};

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
          'All optional fields (price, surface, location, favorite state) are omitted. Default CTA text: "Consulter l\'annonce".',
      },
    },
  },
};
