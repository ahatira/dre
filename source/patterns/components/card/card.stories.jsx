import cardTwig from './card.twig';
import data from './card.yml';

const settings = {
  title: 'Components/Card',
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component:
          'Generic flexible container providing visual structure (border, padding, shadow) and layout variants. ' +
          'Supports vertical and horizontal layouts with optional images, composable content sections (header, body, footer), ' +
          'and clickable link mode.\n\n' +
          '- **Variants**: default (border), outlined (thick border), flat (no border), elevated (shadow).\n' +
          '- **Layouts**: vertical (image top/bottom), horizontal (image left/right).\n' +
          '- **Sizes**: small (16px), medium (32px, default), large (32px extended).\n' +
          '- **Radius**: none (default), sm (4px), md (8px), lg (16px).\n' +
          '- **Image Position**: start (top/left), end (bottom/right).\n' +
          '- **Clickable**: Pass `url` prop to render as `<a>` with hover effects.\n' +
          '- **Composition**: Use `{% embed %}` blocks for complex layouts with child components.\n' +
          '- **Design tokens**: `--ps-card-*` component-scoped variables (3-layer system).\n' +
          '- **Real Estate context**: Perfect for property cards, office listings, news articles.',
      },
    },
  },
  argTypes: {
    // Appearance - Visual variants
    variant: {
      control: { type: 'select' },
      options: ['default', 'outlined', 'flat', 'elevated'],
      description: 'Visual appearance variant',
      table: {
        category: 'Appearance',
        type: { summary: 'default | outlined | flat | elevated' },
        defaultValue: { summary: 'default' },
      },
    },

    // Appearance - Layout
    layout: {
      control: { type: 'inline-radio' },
      options: ['vertical', 'horizontal'],
      description: 'Layout orientation (vertical: image top/bottom, horizontal: image left/right)',
      table: {
        category: 'Appearance',
        type: { summary: 'vertical | horizontal' },
        defaultValue: { summary: 'vertical' },
      },
    },

    // Appearance - Image position
    imagePosition: {
      control: { type: 'select' },
      options: ['top', 'bottom', 'left', 'right'],
      description:
        'Image position (top/bottom for vertical layout, left/right for horizontal layout)',
      table: {
        category: 'Appearance',
        type: { summary: 'top | bottom | left | right' },
        defaultValue: { summary: 'top' },
      },
    },

    // Appearance - Spacing
    size: {
      control: { type: 'inline-radio' },
      options: ['small', 'medium', 'large'],
      description: 'Content padding size',
      table: {
        category: 'Appearance',
        type: { summary: 'small | medium | large' },
        defaultValue: { summary: 'medium' },
      },
    },

    // Appearance - Radius
    radius: {
      control: { type: 'inline-radio' },
      options: ['none', 'sm', 'md', 'lg'],
      description: 'Border radius amount',
      table: {
        category: 'Appearance',
        type: { summary: 'none | sm | md | lg' },
        defaultValue: { summary: 'none' },
      },
    },

    // Link - Navigation
    url: {
      control: 'text',
      description: 'Optional URL - renders card as clickable <a> element with hover effects',
      table: {
        category: 'Link',
        type: { summary: 'string | undefined' },
      },
    },

    // Content - Image
    image: {
      control: 'text',
      description: 'Image/media HTML content (optional)',
      table: {
        category: 'Content',
        type: { summary: 'string | html' },
      },
    },

    // Content - Header
    header: {
      control: 'text',
      description: 'Header section HTML (optional)',
      table: {
        category: 'Content',
        type: { summary: 'string | html' },
      },
    },

    // Content - Body
    body: {
      control: 'text',
      description: 'Body/content section HTML (optional)',
      table: {
        category: 'Content',
        type: { summary: 'string | html' },
      },
    },

    // Content - Footer
    footer: {
      control: 'text',
      description: 'Footer section HTML (optional)',
      table: {
        category: 'Content',
        type: { summary: 'string | html' },
      },
    },
  },
};

// ==============================================
// STORY 1: Default Card (Interactive Playground)
// ==============================================

const baseImage =
  '<img src="/source/assets/images/3-2.jpg" alt="Modern office building" style="display: block; width: 100%; height: 100%; object-fit: cover;" />';

export const Default = {
  render: (args) => {
    return cardTwig({
      ...args,
      image: baseImage,
      header: `<h3 style="margin: 0; font-size: 1.25rem; font-weight: 600; color: var(--gray-900);">Bureau Premium - Madrid</h3>`,
      body: `<p style="margin: 0; font-size: 1rem; color: var(--gray-600); line-height: 1.5;">Immeuble de bureaux moderne avec équipements haut de gamme, parking souterrain et excellentes connexions transports en commun.</p>`,
      footer: `<div style="display: flex; gap: 1rem; align-items: center; font-size: 0.875rem; color: var(--gray-500);">
        <span>📍 Madrid</span>
        <span>📏 611 m²</span>
        <span style="margin-left: auto; font-weight: 600; color: var(--primary);">€850,000</span>
      </div>`,
    });
  },
  args: { ...data },
};

// ==============================================
// SHOWCASE 1: Visual Variants
// ==============================================

export const VisualVariants = {
  render: () => {
    const variants = [
      { variant: 'default', desc: 'Standard border (1px)' },
      { variant: 'outlined', desc: 'Thick border (2px)' },
      { variant: 'flat', desc: 'No border' },
      { variant: 'elevated', desc: 'Shadow elevation' },
    ];

    return `
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 2rem;">
        ${variants
          .map(
            ({ variant, desc }) => `
          <div>
            <h4 style="margin: 0 0 0.5rem 0; font-size: 0.875rem; font-weight: 600; color: var(--gray-700);">${variant.charAt(0).toUpperCase() + variant.slice(1)}</h4>
            <p style="margin: 0 0 1rem 0; font-size: 0.75rem; color: var(--gray-500);">${desc}</p>
            ${cardTwig({
              variant,
              radius: 'md',
              size: 'small',
              image: baseImage,
              header: `<h3 style="margin: 0; font-size: 1rem; font-weight: 600; color: var(--gray-900);">Office Space</h3>`,
              body: `<p style="margin: 0; font-size: 0.875rem; color: var(--gray-600);">Modern workspace with premium amenities.</p>`,
            })}
          </div>
        `
          )
          .join('')}
      </div>
    `;
  },
};

// ==============================================
// SHOWCASE 2: Layout Orientations & Image Position
// ==============================================

export const Layouts = {
  render: () => {
    return `
      <div style="display: grid; gap: 3rem;">
        <!-- Vertical Layout: Image Top -->
        <div>
          <h4 style="margin: 0 0 1rem 0; font-size: 1.125rem; font-weight: 600; color: var(--gray-900);">Vertical Layout - Image Top</h4>
          <div style="max-width: 400px;">
            ${cardTwig({
              layout: 'vertical',
              imagePosition: 'top',
              radius: 'md',
              variant: 'outlined',
              image: baseImage,
              header: `<h3 style="margin: 0; font-size: 1.25rem; font-weight: 600; color: var(--gray-900);">Appartement de Luxe</h3>`,
              body: `<p style="margin: 0; font-size: 1rem; color: var(--gray-600);">Spacieux appartement en centre-ville avec plafonds hauts et finitions modernes.</p>`,
              footer: `<div style="display: flex; gap: 1rem;"><span>📍 Paris</span><span>📏 125 m²</span></div>`,
            })}
          </div>
        </div>
        
        <!-- Vertical Layout: Image Bottom -->
        <div>
          <h4 style="margin: 0 0 1rem 0; font-size: 1.125rem; font-weight: 600; color: var(--gray-900);">Vertical Layout - Image Bottom</h4>
          <div style="max-width: 400px;">
            ${cardTwig({
              layout: 'vertical',
              imagePosition: 'bottom',
              radius: 'md',
              variant: 'elevated',
              image: baseImage,
              header: `<h3 style="margin: 0; font-size: 1.25rem; font-weight: 600; color: var(--gray-900);">Villa Côte d'Azur</h3>`,
              body: `<p style="margin: 0; font-size: 1rem; color: var(--gray-600);">Propriété exceptionnelle en bord de mer avec vue panoramique sur la Méditerranée.</p>`,
              footer: `<div style="display: flex; gap: 1rem;"><span>📍 Nice</span><span>📏 350 m²</span></div>`,
            })}
          </div>
        </div>
        
        <!-- Horizontal Layout: Image Left -->
        <div>
          <h4 style="margin: 0 0 1rem 0; font-size: 1.125rem; font-weight: 600; color: var(--gray-900);">Horizontal Layout - Image Left</h4>
          ${cardTwig({
            layout: 'horizontal',
            imagePosition: 'left',
            radius: 'md',
            image: baseImage,
            header: `<h3 style="margin: 0; font-size: 1.25rem; font-weight: 600; color: var(--gray-900);">Espace Commercial Barcelone</h3>`,
            body: `<p style="margin: 0; font-size: 1rem; color: var(--gray-600);">Emplacement commercial premium sur Las Ramblas avec fort trafic piéton et excellente visibilité.</p>`,
            footer: `<div style="display: flex; gap: 1rem; align-items: center;"><span>📍 Barcelone</span><span>📏 280 m²</span><span style="margin-left: auto; font-weight: 600; color: var(--primary);">€1,200,000</span></div>`,
          })}
        </div>
        
        <!-- Horizontal Layout: Image Right -->
        <div>
          <h4 style="margin: 0 0 1rem 0; font-size: 1.125rem; font-weight: 600; color: var(--gray-900);">Horizontal Layout - Image Right</h4>
          ${cardTwig({
            layout: 'horizontal',
            imagePosition: 'right',
            radius: 'md',
            variant: 'outlined',
            image: baseImage,
            header: `<h3 style="margin: 0; font-size: 1.25rem; font-weight: 600; color: var(--gray-900);">Terrain à Bâtir</h3>`,
            body: `<p style="margin: 0; font-size: 1rem; color: var(--gray-600);">Emplacement urbain stratégique avec permis de construire approuvé pour projet mixte résidentiel-commercial.</p>`,
            footer: `<div style="display: flex; gap: 1rem;"><span>📍 Lyon</span><span>📏 1500 m²</span></div>`,
          })}
        </div>
      </div>
    `;
  },
};

// ==============================================
// SHOWCASE 3: Padding Sizes
// ==============================================

export const Sizes = {
  render: () => {
    const sizes = [
      { size: 'small', padding: '16px', desc: 'Compact cards' },
      { size: 'medium', padding: '32px', desc: 'Default size' },
      { size: 'large', padding: '32px extended', desc: 'Spacious cards' },
    ];

    return `
      <div style="display: grid; gap: 2rem;">
        ${sizes
          .map(
            ({ size, padding, desc }) => `
          <div>
            <h4 style="margin: 0 0 0.5rem 0; font-size: 1rem; font-weight: 600; color: var(--gray-900);">${size.charAt(0).toUpperCase() + size.slice(1)} (${padding})</h4>
            <p style="margin: 0 0 1rem 0; font-size: 0.875rem; color: var(--gray-500);">${desc}</p>
            ${cardTwig({
              size,
              radius: 'md',
              variant: 'outlined',
              header: `<h3 style="margin: 0; font-size: 1.125rem; font-weight: 600; color: var(--gray-900);">Bureau ${size}</h3>`,
              body: `<p style="margin: 0; font-size: 0.875rem; color: var(--gray-600);">Exemple avec padding ${size}.</p>`,
            })}
          </div>
        `
          )
          .join('')}
      </div>
    `;
  },
};

// ==============================================
// SHOWCASE 4: Border Radius
// ==============================================

export const BorderRadius = {
  render: () => {
    const radii = [
      { radius: 'none', value: '0px', desc: 'Sharp corners' },
      { radius: 'sm', value: '4px', desc: 'Subtle rounding' },
      { radius: 'md', value: '8px', desc: 'Medium rounding' },
      { radius: 'lg', value: '16px', desc: 'Strong rounding' },
    ];

    return `
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 2rem;">
        ${radii
          .map(
            ({ radius, value, desc }) => `
          <div>
            <h4 style="margin: 0 0 0.5rem 0; font-size: 0.875rem; font-weight: 600; color: var(--gray-700);">Radius: ${radius} (${value})</h4>
            <p style="margin: 0 0 1rem 0; font-size: 0.75rem; color: var(--gray-500);">${desc}</p>
            ${cardTwig({
              radius,
              variant: 'elevated',
              size: 'small',
              image: baseImage,
              header: `<h3 style="margin: 0; font-size: 1rem; font-weight: 600; color: var(--gray-900);">Card ${radius}</h3>`,
              body: `<p style="margin: 0; font-size: 0.875rem; color: var(--gray-600);">Border radius: ${value}</p>`,
            })}
          </div>
        `
          )
          .join('')}
      </div>
    `;
  },
};

// ==============================================
// SHOWCASE 5: Clickable Cards (with URL)
// ==============================================

export const ClickableCards = {
  render: () => {
    return `
      <div style="display: grid; gap: 2rem;">
        <div>
          <h4 style="margin: 0 0 1rem 0; font-size: 1.125rem; font-weight: 600; color: var(--gray-900);">Standard Card (Static)</h4>
          <div style="max-width: 400px;">
            ${cardTwig({
              radius: 'md',
              variant: 'outlined',
              image: baseImage,
              header: `<h3 style="margin: 0; font-size: 1.125rem; font-weight: 600; color: var(--gray-900);">Non-clickable Card</h3>`,
              body: `<p style="margin: 0; font-size: 0.875rem; color: var(--gray-600);">Renders as &lt;article&gt; element.</p>`,
            })}
          </div>
        </div>
        
        <div>
          <h4 style="margin: 0 0 1rem 0; font-size: 1.125rem; font-weight: 600; color: var(--gray-900);">Clickable Card (with URL)</h4>
          <div style="max-width: 400px;">
            ${cardTwig({
              url: '#property-details',
              radius: 'md',
              variant: 'elevated',
              image: baseImage,
              header: `<h3 style="margin: 0; font-size: 1.125rem; font-weight: 600; color: var(--gray-900);">Clickable Card</h3>`,
              body: `<p style="margin: 0; font-size: 0.875rem; color: var(--gray-600);">Renders as &lt;a&gt; with hover effects. Try hovering!</p>`,
              footer: `<span style="color: var(--primary); font-weight: 600;">Voir les détails →</span>`,
            })}
          </div>
        </div>
      </div>
    `;
  },
};

// ==============================================
// SHOWCASE 6: Real Estate Use Cases
// ==============================================

export const RealEstateUseCases = {
  render: () => {
    return `
      <div style="display: grid; gap: 3rem;">
        <!-- Property Listing Card -->
        <div>
          <h3 style="margin: 0 0 1rem 0; font-size: 1.25rem; font-weight: 600; color: var(--gray-900);">📋 Fiche Propriété</h3>
          <div style="max-width: 400px;">
            ${cardTwig({
              url: '#property-123',
              layout: 'vertical',
              radius: 'md',
              variant: 'elevated',
              image: baseImage,
              header: `
                <div style="display: flex; justify-content: space-between; align-items: start;">
                  <div>
                    <span style="display: inline-block; padding: 0.25rem 0.5rem; background: var(--success-bg-subtle); color: var(--success); font-size: 0.75rem; font-weight: 600; border-radius: 4px; margin-bottom: 0.5rem;">DISPONIBLE</span>
                    <h3 style="margin: 0; font-size: 1.25rem; font-weight: 600; color: var(--gray-900);">Bureau Prestige La Défense</h3>
                  </div>
                </div>
              `,
              body: `
                <p style="margin: 0 0 1rem 0; font-size: 0.875rem; color: var(--gray-600); line-height: 1.6;">
                  Surface de bureaux premium dans immeuble classé, finitions haut de gamme, terrasse privative.
                </p>
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.75rem; font-size: 0.875rem;">
                  <div><span style="color: var(--gray-500);">📏 Surface:</span> <strong>611 m²</strong></div>
                  <div><span style="color: var(--gray-500);">🚇 Métro:</span> <strong>2 min</strong></div>
                  <div><span style="color: var(--gray-500);">🅿️ Parking:</span> <strong>15 places</strong></div>
                  <div><span style="color: var(--gray-500);">📅 Disponible:</span> <strong>Immédiat</strong></div>
                </div>
              `,
              footer: `
                <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 1rem; border-top: 1px solid var(--gray-200);">
                  <div>
                    <div style="font-size: 0.75rem; color: var(--gray-500);">Prix de vente</div>
                    <div style="font-size: 1.5rem; font-weight: 700; color: var(--primary);">2 850 000 €</div>
                  </div>
                  <span style="color: var(--primary); font-weight: 600;">Voir détails →</span>
                </div>
              `,
            })}
          </div>
        </div>
        
        <!-- News Article Card -->
        <div>
          <h3 style="margin: 0 0 1rem 0; font-size: 1.25rem; font-weight: 600; color: var(--gray-900);">📰 Article Actualités</h3>
          ${cardTwig({
            url: '#article-456',
            layout: 'horizontal',
            radius: 'md',
            variant: 'outlined',
            image: baseImage,
            header: `
              <div>
                <span style="display: inline-block; padding: 0.25rem 0.5rem; background: var(--info-bg-subtle); color: var(--info); font-size: 0.75rem; font-weight: 600; border-radius: 4px; margin-bottom: 0.5rem;">MARCHÉ</span>
                <h3 style="margin: 0; font-size: 1.125rem; font-weight: 600; color: var(--gray-900);">L'immobilier tertiaire en forte croissance</h3>
              </div>
            `,
            body: `
              <p style="margin: 0; font-size: 0.875rem; color: var(--gray-600); line-height: 1.6;">
                Le marché des bureaux affiche une progression de 12% ce trimestre, porté par la demande en espaces flexibles et la transformation digitale des entreprises.
              </p>
            `,
            footer: `
              <div style="display: flex; gap: 1rem; align-items: center; font-size: 0.75rem; color: var(--gray-500);">
                <span>👤 Marie Dubois</span>
                <span>📅 5 décembre 2025</span>
                <span>⏱️ 3 min</span>
              </div>
            `,
          })}
        </div>
        
        <!-- Agent Contact Card -->
        <div>
          <h3 style="margin: 0 0 1rem 0; font-size: 1.25rem; font-weight: 600; color: var(--gray-900);">👤 Fiche Agent</h3>
          <div style="max-width: 350px;">
            ${cardTwig({
              layout: 'vertical',
              radius: 'lg',
              variant: 'elevated',
              size: 'small',
              image:
                '<div style="width: 100%; height: 200px; background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%); display: flex; align-items: center; justify-content: center; font-size: 4rem;">👤</div>',
              header: `
                <div style="text-align: center;">
                  <h3 style="margin: 0 0 0.25rem 0; font-size: 1.25rem; font-weight: 600; color: var(--gray-900);">Sophie Martin</h3>
                  <p style="margin: 0; font-size: 0.875rem; color: var(--primary); font-weight: 500;">Conseillère Senior</p>
                </div>
              `,
              body: `
                <div style="text-align: center; font-size: 0.875rem; color: var(--gray-600);">
                  <p style="margin: 0 0 0.75rem 0;">Spécialiste immobilier tertiaire Paris & Île-de-France</p>
                  <div style="display: flex; flex-direction: column; gap: 0.5rem; align-items: center;">
                    <div>📧 sophie.martin@bnpparibas.com</div>
                    <div>📱 +33 6 12 34 56 78</div>
                  </div>
                </div>
              `,
              footer: `
                <div style="text-align: center;">
                  <button style="width: 100%; padding: 0.75rem; background: var(--primary); color: var(--white); border: none; border-radius: 6px; font-weight: 600; cursor: pointer;">
                    Prendre rendez-vous
                  </button>
                </div>
              `,
            })}
          </div>
        </div>
      </div>
    `;
  },
};

// ==============================================
// SHOWCASE 7: Composition Example (Documentation)
// ==============================================

export const CompositionWithEmbed = {
  render: () => {
    return `
      <div style="max-width: 800px;">
        <h3 style="margin: 0 0 1rem 0; font-size: 1.25rem; font-weight: 600; color: var(--gray-900);">📦 Composition Pattern (Drupal)</h3>
        <p style="margin: 0 0 2rem 0; font-size: 1rem; color: var(--gray-600); line-height: 1.6;">
          For complex layouts in Drupal, use <code>{% embed %}</code> blocks to compose Card with child components (buttons, badges, etc.).
          This example shows the recommended pattern for Real Estate property cards.
        </p>
        
        <div style="background: var(--gray-50); border: 2px dashed var(--gray-300); border-radius: 8px; padding: 2rem;">
          <pre style="margin: 0; font-family: 'Courier New', monospace; font-size: 0.875rem; color: var(--gray-800); overflow-x: auto; line-height: 1.6;"><code>{# Drupal: Property Card with composition #}
{% embed '@components/card/card.twig' with {
  layout: 'vertical',
  radius: 'md',
  variant: 'elevated',
  url: property.url
} only %}
  
  {% block image %}
    {% include '@elements/image/image.twig' with {
      src: property.image.url,
      alt: property.image.alt,
      ratio: '16x9'
    } only %}
  {% endblock %}
  
  {% block header %}
    {% include '@elements/badge/badge.twig' with {
      text: property.status,
      color: property.status_color
    } only %}
    &lt;h3&gt;{{ property.title }}&lt;/h3&gt;
  {% endblock %}
  
  {% block body %}
    &lt;p&gt;{{ property.description }}&lt;/p&gt;
    &lt;div class="property-specs"&gt;
      &lt;span&gt;📏 {{ property.surface }}&lt;/span&gt;
      &lt;span&gt;📍 {{ property.location }}&lt;/span&gt;
    &lt;/div&gt;
  {% endblock %}
  
  {% block footer %}
    &lt;div class="price-cta"&gt;
      &lt;strong&gt;{{ property.price }}&lt;/strong&gt;
      {% include '@elements/button/button.twig' with {
        text: 'Voir détails',
        color: 'primary',
        size: 'sm'
      } only %}
    &lt;/div&gt;
  {% endblock %}
  
{% endembed %}</code></pre>
        </div>
        
        <div style="margin-top: 2rem;">
          <h4 style="margin: 0 0 1rem 0; font-size: 1rem; font-weight: 600; color: var(--gray-900);">Result Preview:</h4>
          ${cardTwig({
            url: '#example',
            layout: 'vertical',
            radius: 'md',
            variant: 'elevated',
            image: baseImage,
            header: `
              <span style="display: inline-block; padding: 0.25rem 0.5rem; background: var(--success-bg-subtle); color: var(--success); font-size: 0.75rem; font-weight: 600; border-radius: 4px; margin-bottom: 0.5rem;">DISPONIBLE</span>
              <h3 style="margin: 0; font-size: 1.25rem; font-weight: 600; color: var(--gray-900);">Bureau Composé</h3>
            `,
            body: `
              <p style="margin: 0 0 1rem 0; font-size: 0.875rem; color: var(--gray-600);">Example de card composée avec blocks Twig.</p>
              <div style="display: flex; gap: 1rem; font-size: 0.875rem; color: var(--gray-500);">
                <span>📏 450 m²</span>
                <span>📍 Paris</span>
              </div>
            `,
            footer: `
              <div style="display: flex; justify-content: space-between; align-items: center;">
                <strong style="font-size: 1.25rem; color: var(--primary);">1 250 000 €</strong>
                <button style="padding: 0.5rem 1rem; background: var(--primary); color: var(--white); border: none; border-radius: 6px; font-weight: 600; cursor: pointer;">
                  Voir détails
                </button>
              </div>
            `,
          })}
        </div>
      </div>
    `;
  },
};

export default settings;
