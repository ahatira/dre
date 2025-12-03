import cardTwig from './card.twig';
import data from './card.yml';

const settings = {
  title: 'Components/Card',
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component:
          'Generic flexible container providing visual structure (border, padding, shadow) and layout variants. Content is composed freely via Twig blocks for maximum reusability.\n\n' +
          'Use composition to create specialized cards (OfferCard, NewsCard, etc.) that embed Card.',
      },
    },
  },
  argTypes: {
    // Appearance
    variant: {
      control: { type: 'select' },
      options: ['default', 'outlined', 'flat', 'elevated'],
      description: 'Visual variant',
      table: {
        category: 'Appearance',
        type: { summary: 'default | outlined | flat | elevated' },
        defaultValue: { summary: 'default' },
      },
    },
    layout: {
      control: { type: 'inline-radio' },
      options: ['vertical', 'horizontal'],
      description: 'Layout orientation',
      table: {
        category: 'Appearance',
        type: { summary: 'vertical | horizontal' },
        defaultValue: { summary: 'vertical' },
      },
    },
    size: {
      control: { type: 'inline-radio' },
      options: ['small', 'medium', 'large'],
      description: 'Padding size',
      table: {
        category: 'Appearance',
        type: { summary: 'small | medium | large' },
        defaultValue: { summary: 'medium' },
      },
    },
    radius: {
      control: { type: 'inline-radio' },
      options: ['none', 'sm', 'md', 'lg'],
      description: 'Border radius',
      table: {
        category: 'Appearance',
        type: { summary: 'none | sm | md | lg' },
        defaultValue: { summary: 'none' },
      },
    },
    imagePosition: {
      control: { type: 'select' },
      options: ['top', 'bottom', 'left', 'right'],
      description: 'Image position (top/bottom for vertical, left/right for horizontal)',
      table: {
        category: 'Appearance',
        type: { summary: 'top | bottom | left | right' },
        defaultValue: { summary: 'top' },
      },
    },

    // Link
    url: {
      control: 'text',
      description: 'Optional URL (renders card as clickable <a> element)',
      table: {
        category: 'Link',
        type: { summary: 'string' },
      },
    },
  },
};

// Default story with controls
export const Default = {
  render: (args) => {
    return cardTwig({
      ...args,
      // Embed blocks as strings for Storybook
      image: `<img src="https://loremflickr.com/640/480/office,building" alt="Modern office building" style="display: block; width: 100%; height: 100%; object-fit: cover;" />`,
      header: `<h3 style="margin: 0; font-size: 1.25rem; font-weight: 600; color: var(--gray-900);">Modern Office Space</h3>`,
      body: `<p style="margin: 0; font-size: 1rem; color: var(--gray-600);">Premium office building located in the heart of Madrid business district. Features modern amenities, underground parking, and excellent public transport connections.</p>`,
      footer: `<div style="display: flex; gap: 1rem; align-items: center;">
        <span style="font-size: 0.875rem; color: var(--gray-500);">📍 Madrid</span>
        <span style="font-size: 0.875rem; color: var(--gray-500);">📏 611 m²</span>
      </div>`,
    });
  },
  args: { ...data },
};

// Showcase: All visual variants
export const AllVariants = {
  render: () => {
    const variants = ['default', 'outlined', 'flat', 'elevated'];
    return `
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: var(--size-6);">
        ${variants
          .map((variant) =>
            cardTwig({
              variant,
              radius: 'md',
              image: `<img src="https://loremflickr.com/640/480/apartment,building?random=${variant}" alt="Property" style="display: block; width: 100%; height: 200px; object-fit: cover;" />`,
              header: `<h3 style="margin: 0; font-size: 1.125rem; font-weight: 600; color: var(--gray-900);">${variant.charAt(0).toUpperCase() + variant.slice(1)} Variant</h3>`,
              body: `<p style="margin: 0; font-size: 0.875rem; color: var(--gray-600);">Card with ${variant} visual style.</p>`,
            })
          )
          .join('')}
      </div>
    `;
  },
};

// Showcase: Layouts (vertical vs horizontal)
export const AllLayouts = {
  render: () => {
    return `
      <div style="display: grid; gap: var(--size-8);">
        <div>
          <h4 style="margin: 0 0 var(--size-4) 0; font-size: 1rem; font-weight: 600;">Vertical Layout (Default)</h4>
          ${cardTwig({
            layout: 'vertical',
            radius: 'md',
            image: `<img src="https://loremflickr.com/640/480/house,property?random=1" alt="Property" style="display: block; width: 100%; height: 240px; object-fit: cover;" />`,
            header: `<h3 style="margin: 0; font-size: 1.25rem; font-weight: 600; color: var(--gray-900);">Luxury Apartment Paris</h3>`,
            body: `<p style="margin: 0; font-size: 1rem; color: var(--gray-600);">Spacious 3-bedroom apartment in the 8th arrondissement. High ceilings, Haussmann architecture, recent renovation.</p>`,
            footer: `<div style="display: flex; gap: 1rem;">
              <span style="font-size: 0.875rem; color: var(--gray-500);">📍 Paris 8e</span>
              <span style="font-size: 0.875rem; color: var(--gray-500);">📏 120 m²</span>
            </div>`,
          })}
        </div>
        
        <div>
          <h4 style="margin: 0 0 var(--size-4) 0; font-size: 1rem; font-weight: 600;">Horizontal Layout</h4>
          ${cardTwig({
            layout: 'horizontal',
            radius: 'md',
            image: `<img src="https://loremflickr.com/640/480/villa,property?random=2" alt="Property" style="display: block; width: 100%; height: 100%; object-fit: cover;" />`,
            header: `<h3 style="margin: 0; font-size: 1.25rem; font-weight: 600; color: var(--gray-900);">Villa Côte d'Azur</h3>`,
            body: `<p style="margin: 0; font-size: 1rem; color: var(--gray-600);">Exceptional waterfront villa with panoramic Mediterranean sea views. Private pool, 5 bedrooms, landscaped garden.</p>`,
            footer: `<div style="display: flex; gap: 1rem;">
              <span style="font-size: 0.875rem; color: var(--gray-500);">📍 Nice</span>
              <span style="font-size: 0.875rem; color: var(--gray-500);">📏 350 m²</span>
            </div>`,
          })}
        </div>
        
        <div>
          <h4 style="margin: 0 0 var(--size-4) 0; font-size: 1rem; font-weight: 600;">Horizontal Layout (Image Right)</h4>
          ${cardTwig({
            layout: 'horizontal',
            imagePosition: 'right',
            radius: 'md',
            image: `<img src="https://loremflickr.com/640/480/commercial,real-estate?random=3" alt="Property" style="display: block; width: 100%; height: 100%; object-fit: cover;" />`,
            header: `<h3 style="margin: 0; font-size: 1.25rem; font-weight: 600; color: var(--gray-900);">Commercial Space Barcelona</h3>`,
            body: `<p style="margin: 0; font-size: 1rem; color: var(--gray-600);">Prime retail location on Las Ramblas. High foot traffic, flexible layout, excellent visibility.</p>`,
            footer: `<div style="display: flex; gap: 1rem;">
              <span style="font-size: 0.875rem; color: var(--gray-500);">📍 Barcelona</span>
              <span style="font-size: 0.875rem; color: var(--gray-500);">📏 85 m²</span>
            </div>`,
          })}
        </div>
      </div>
    `;
  },
};

// Showcase: All sizes
export const AllSizes = {
  render: () => {
    const sizes = ['small', 'medium', 'large'];
    return `
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: var(--size-6);">
        ${sizes
          .map((size) =>
            cardTwig({
              size,
              radius: 'md',
              image: `<img src="https://loremflickr.com/640/480/property,building?random=${size}" alt="Property" style="display: block; width: 100%; height: 200px; object-fit: cover;" />`,
              header: `<h3 style="margin: 0; font-size: 1.125rem; font-weight: 600; color: var(--gray-900);">${size.charAt(0).toUpperCase() + size.slice(1)} Size</h3>`,
              body: `<p style="margin: 0; font-size: 0.875rem; color: var(--gray-600);">Card with ${size} padding.</p>`,
            })
          )
          .join('')}
      </div>
    `;
  },
};

// Showcase: Border radius
export const AllRadius = {
  render: () => {
    const radiusOptions = ['none', 'sm', 'md', 'lg'];
    return `
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: var(--size-6);">
        ${radiusOptions
          .map((radius) =>
            cardTwig({
              radius,
              image: `<img src="https://loremflickr.com/640/480/architecture,building?random=${radius}" alt="Property" style="display: block; width: 100%; height: 200px; object-fit: cover;" />`,
              header: `<h3 style="margin: 0; font-size: 1.125rem; font-weight: 600; color: var(--gray-900);">Radius: ${radius}</h3>`,
              body: `<p style="margin: 0; font-size: 0.875rem; color: var(--gray-600);">Border radius variant.</p>`,
            })
          )
          .join('')}
      </div>
    `;
  },
};

// Showcase: Clickable cards (with URL)
export const ClickableCards = {
  render: () => {
    return `
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: var(--size-6);">
        ${cardTwig({
          url: '#property-123',
          variant: 'elevated',
          radius: 'md',
          image: `<img src="https://loremflickr.com/640/480/office,property?random=click1" alt="Property" style="display: block; width: 100%; height: 200px; object-fit: cover;" />`,
          header: `<h3 style="margin: 0; font-size: 1.125rem; font-weight: 600; color: var(--gray-900);">Office Space Madrid</h3>`,
          body: `<p style="margin: 0; font-size: 0.875rem; color: var(--gray-600);">Click to view details (hover for effect).</p>`,
          footer: `<span style="font-size: 0.875rem; color: var(--primary); font-weight: 600;">View property →</span>`,
        })}
        
        ${cardTwig({
          url: '#property-456',
          variant: 'elevated',
          radius: 'md',
          image: `<img src="https://loremflickr.com/640/480/apartment,property?random=click2" alt="Property" style="display: block; width: 100%; height: 200px; object-fit: cover;" />`,
          header: `<h3 style="margin: 0; font-size: 1.125rem; font-weight: 600; color: var(--gray-900);">Apartment Barcelona</h3>`,
          body: `<p style="margin: 0; font-size: 0.875rem; color: var(--gray-600);">Click to view details (hover for effect).</p>`,
          footer: `<span style="font-size: 0.875rem; color: var(--primary); font-weight: 600;">View property →</span>`,
        })}
      </div>
    `;
  },
};

// Real-world use cases
export const UseCases = {
  render: () => {
    return `
      <div style="display: grid; gap: var(--size-8);">
        <div>
          <h4 style="margin: 0 0 var(--size-4) 0; font-size: 1rem; font-weight: 600;">Property Listing Card</h4>
          ${cardTwig({
            url: '#property-789',
            variant: 'elevated',
            radius: 'lg',
            image: `<img src="https://loremflickr.com/640/480/real-estate,listing?random=use1" alt="Property listing" style="display: block; width: 100%; height: 240px; object-fit: cover;" />`,
            header: `<div style="display: flex; justify-content: space-between; align-items: start;">
              <h3 style="margin: 0; font-size: 1.25rem; font-weight: 600; color: var(--gray-900);">Penthouse Paris 16e</h3>
              <span style="padding: 0.25rem 0.75rem; background: var(--primary); color: white; border-radius: var(--radius-round); font-size: 0.75rem; font-weight: 600;">NEW</span>
            </div>`,
            body: `<p style="margin: 0 0 var(--size-3) 0; font-size: 1rem; color: var(--gray-600);">Exceptional duplex penthouse with 360° views of Paris. Terraces, private elevator, luxury finishes.</p>
            <div style="display: flex; gap: var(--size-4); flex-wrap: wrap;">
              <span style="display: inline-flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; color: var(--gray-600);">
                <span>🛏️</span> 4 bedrooms
              </span>
              <span style="display: inline-flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; color: var(--gray-600);">
                <span>🚿</span> 3 bathrooms
              </span>
              <span style="display: inline-flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; color: var(--gray-600);">
                <span>📏</span> 280 m²
              </span>
            </div>`,
            footer: `<div style="display: flex; justify-content: space-between; align-items: center;">
              <span style="font-size: 1.5rem; font-weight: 700; color: var(--gray-900);">€2,500,000</span>
              <span style="font-size: 0.875rem; color: var(--primary); font-weight: 600;">View details →</span>
            </div>`,
          })}
        </div>
        
        <div>
          <h4 style="margin: 0 0 var(--size-4) 0; font-size: 1rem; font-weight: 600;">News/Blog Card</h4>
          ${cardTwig({
            url: '#article-123',
            layout: 'horizontal',
            radius: 'md',
            image: `<img src="https://loremflickr.com/640/480/meeting,business?random=use2" alt="Article" style="display: block; width: 100%; height: 100%; object-fit: cover;" />`,
            header: `<div>
              <span style="display: inline-block; padding: 0.25rem 0.5rem; background: var(--gray-100); color: var(--gray-700); border-radius: var(--radius-1); font-size: 0.75rem; font-weight: 600; margin-bottom: 0.5rem;">MARKET INSIGHTS</span>
              <h3 style="margin: 0; font-size: 1.125rem; font-weight: 600; color: var(--gray-900);">European Real Estate Market Trends 2025</h3>
            </div>`,
            body: `<p style="margin: 0; font-size: 0.875rem; color: var(--gray-600); line-height: 1.5;">Analysis of key trends shaping the commercial real estate sector across major European markets...</p>`,
            footer: `<div style="display: flex; gap: var(--size-2); align-items: center; font-size: 0.75rem; color: var(--gray-500);">
              <span>Dec 3, 2025</span>
              <span>•</span>
              <span>5 min read</span>
            </div>`,
          })}
        </div>
        
        <div>
          <h4 style="margin: 0 0 var(--size-4) 0; font-size: 1rem; font-weight: 600;">Simple Info Card (No Image)</h4>
          ${cardTwig({
            variant: 'outlined',
            radius: 'md',
            size: 'small',
            header: `<h3 style="margin: 0; font-size: 1rem; font-weight: 600; color: var(--gray-900);">Contact Information</h3>`,
            body: `<div style="display: flex; flex-direction: column; gap: 0.5rem; font-size: 0.875rem; color: var(--gray-600);">
              <p style="margin: 0;">📧 contact@bnpparibas-realestate.com</p>
              <p style="margin: 0;">📞 +33 1 55 65 20 00</p>
              <p style="margin: 0;">📍 167 Quai de la Bataille de Stalingrad, Paris</p>
            </div>`,
          })}
        </div>
      </div>
    `;
  },
};

export default settings;
