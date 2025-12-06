import { c as r } from './card-bgc-lC9n.js';
import './iframe-D21U4yYN.js';
import './twig-BPJOkNgt.js';
const V = {
    variant: 'default',
    layout: 'vertical',
    size: 'medium',
    radius: 'none',
    imagePosition: 'top',
  },
  B = {
    title: 'Components/Card',
    tags: ['autodocs'],
    parameters: {
      docs: {
        description: {
          component: `Generic flexible container providing visual structure (border, padding, shadow) and layout variants. Content is composed freely via Twig blocks for maximum reusability.

Use composition to create specialized cards (OfferCard, NewsCard, etc.) that embed Card.`,
        },
      },
    },
    argTypes: {
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
      url: {
        control: 'text',
        description: 'Optional URL (renders card as clickable <a> element)',
        table: { category: 'Link', type: { summary: 'string' } },
      },
    },
  },
  t = {
    render: (a) =>
      r({
        ...a,
        image:
          '<img src="https://loremflickr.com/640/480/office,building" alt="Modern office building" style="display: block; width: 100%; height: 100%; object-fit: cover;" />',
        header:
          '<h3 style="margin: 0; font-size: 1.25rem; font-weight: 600; color: var(--gray-900);">Modern Office Space</h3>',
        body: '<p style="margin: 0; font-size: 1rem; color: var(--gray-600);">Premium office building located in the heart of Madrid business district. Features modern amenities, underground parking, and excellent public transport connections.</p>',
        footer: `<div style="display: flex; gap: 1rem; align-items: center;">
        <span style="font-size: 0.875rem; color: var(--gray-500);">📍 Madrid</span>
        <span style="font-size: 0.875rem; color: var(--gray-500);">📏 611 m²</span>
      </div>`,
      }),
    args: { ...V },
  },
  i = {
    render: () => `
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: var(--size-6);">
        ${['default', 'outlined', 'flat', 'elevated'].map((e) => r({ variant: e, radius: 'md', image: `<img src="https://loremflickr.com/640/480/apartment,building?random=${e}" alt="Property" style="display: block; width: 100%; height: 200px; object-fit: cover;" />`, header: `<h3 style="margin: 0; font-size: 1.125rem; font-weight: 600; color: var(--gray-900);">${e.charAt(0).toUpperCase() + e.slice(1)} Variant</h3>`, body: `<p style="margin: 0; font-size: 0.875rem; color: var(--gray-600);">Card with ${e} visual style.</p>` })).join('')}
      </div>
    `,
  },
  o = {
    render: () => `
      <div style="display: grid; gap: var(--size-8);">
        <div>
          <h4 style="margin: 0 0 var(--size-4) 0; font-size: 1rem; font-weight: 600;">Vertical Layout (Default)</h4>
          ${r({
            layout: 'vertical',
            radius: 'md',
            image:
              '<img src="https://loremflickr.com/640/480/house,property?random=1" alt="Property" style="display: block; width: 100%; height: 240px; object-fit: cover;" />',
            header:
              '<h3 style="margin: 0; font-size: 1.25rem; font-weight: 600; color: var(--gray-900);">Luxury Apartment Paris</h3>',
            body: '<p style="margin: 0; font-size: 1rem; color: var(--gray-600);">Spacious 3-bedroom apartment in the 8th arrondissement. High ceilings, Haussmann architecture, recent renovation.</p>',
            footer: `<div style="display: flex; gap: 1rem;">
              <span style="font-size: 0.875rem; color: var(--gray-500);">📍 Paris 8e</span>
              <span style="font-size: 0.875rem; color: var(--gray-500);">📏 120 m²</span>
            </div>`,
          })}
        </div>
        
        <div>
          <h4 style="margin: 0 0 var(--size-4) 0; font-size: 1rem; font-weight: 600;">Horizontal Layout</h4>
          ${r({
            layout: 'horizontal',
            radius: 'md',
            image:
              '<img src="https://loremflickr.com/640/480/villa,property?random=2" alt="Property" style="display: block; width: 100%; height: 100%; object-fit: cover;" />',
            header: `<h3 style="margin: 0; font-size: 1.25rem; font-weight: 600; color: var(--gray-900);">Villa Côte d'Azur</h3>`,
            body: '<p style="margin: 0; font-size: 1rem; color: var(--gray-600);">Exceptional waterfront villa with panoramic Mediterranean sea views. Private pool, 5 bedrooms, landscaped garden.</p>',
            footer: `<div style="display: flex; gap: 1rem;">
              <span style="font-size: 0.875rem; color: var(--gray-500);">📍 Nice</span>
              <span style="font-size: 0.875rem; color: var(--gray-500);">📏 350 m²</span>
            </div>`,
          })}
        </div>
        
        <div>
          <h4 style="margin: 0 0 var(--size-4) 0; font-size: 1rem; font-weight: 600;">Horizontal Layout (Image Right)</h4>
          ${r({
            layout: 'horizontal',
            imagePosition: 'right',
            radius: 'md',
            image:
              '<img src="https://loremflickr.com/640/480/commercial,real-estate?random=3" alt="Property" style="display: block; width: 100%; height: 100%; object-fit: cover;" />',
            header:
              '<h3 style="margin: 0; font-size: 1.25rem; font-weight: 600; color: var(--gray-900);">Commercial Space Barcelona</h3>',
            body: '<p style="margin: 0; font-size: 1rem; color: var(--gray-600);">Prime retail location on Las Ramblas. High foot traffic, flexible layout, excellent visibility.</p>',
            footer: `<div style="display: flex; gap: 1rem;">
              <span style="font-size: 0.875rem; color: var(--gray-500);">📍 Barcelona</span>
              <span style="font-size: 0.875rem; color: var(--gray-500);">📏 85 m²</span>
            </div>`,
          })}
        </div>
      </div>
    `,
  },
  n = {
    render: () => `
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: var(--size-6);">
        ${['small', 'medium', 'large'].map((e) => r({ size: e, radius: 'md', image: `<img src="https://loremflickr.com/640/480/property,building?random=${e}" alt="Property" style="display: block; width: 100%; height: 200px; object-fit: cover;" />`, header: `<h3 style="margin: 0; font-size: 1.125rem; font-weight: 600; color: var(--gray-900);">${e.charAt(0).toUpperCase() + e.slice(1)} Size</h3>`, body: `<p style="margin: 0; font-size: 0.875rem; color: var(--gray-600);">Card with ${e} padding.</p>` })).join('')}
      </div>
    `,
  },
  s = {
    render: () => `
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: var(--size-6);">
        ${['none', 'sm', 'md', 'lg'].map((e) => r({ radius: e, image: `<img src="https://loremflickr.com/640/480/architecture,building?random=${e}" alt="Property" style="display: block; width: 100%; height: 200px; object-fit: cover;" />`, header: `<h3 style="margin: 0; font-size: 1.125rem; font-weight: 600; color: var(--gray-900);">Radius: ${e}</h3>`, body: '<p style="margin: 0; font-size: 0.875rem; color: var(--gray-600);">Border radius variant.</p>' })).join('')}
      </div>
    `,
  },
  l = {
    render: () => `
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: var(--size-6);">
        ${r({ url: '#property-123', variant: 'elevated', radius: 'md', image: '<img src="https://loremflickr.com/640/480/office,property?random=click1" alt="Property" style="display: block; width: 100%; height: 200px; object-fit: cover;" />', header: '<h3 style="margin: 0; font-size: 1.125rem; font-weight: 600; color: var(--gray-900);">Office Space Madrid</h3>', body: '<p style="margin: 0; font-size: 0.875rem; color: var(--gray-600);">Click to view details (hover for effect).</p>', footer: '<span style="font-size: 0.875rem; color: var(--primary); font-weight: 600;">View property →</span>' })}
        
        ${r({ url: '#property-456', variant: 'elevated', radius: 'md', image: '<img src="https://loremflickr.com/640/480/apartment,property?random=click2" alt="Property" style="display: block; width: 100%; height: 200px; object-fit: cover;" />', header: '<h3 style="margin: 0; font-size: 1.125rem; font-weight: 600; color: var(--gray-900);">Apartment Barcelona</h3>', body: '<p style="margin: 0; font-size: 0.875rem; color: var(--gray-600);">Click to view details (hover for effect).</p>', footer: '<span style="font-size: 0.875rem; color: var(--primary); font-weight: 600;">View property →</span>' })}
      </div>
    `,
  },
  d = {
    render: () => `
      <div style="display: grid; gap: var(--size-8);">
        <div>
          <h4 style="margin: 0 0 var(--size-4) 0; font-size: 1rem; font-weight: 600;">Property Listing Card</h4>
          ${r({
            url: '#property-789',
            variant: 'elevated',
            radius: 'lg',
            image:
              '<img src="https://loremflickr.com/640/480/real-estate,listing?random=use1" alt="Property listing" style="display: block; width: 100%; height: 240px; object-fit: cover;" />',
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
          ${r({
            url: '#article-123',
            layout: 'horizontal',
            radius: 'md',
            image:
              '<img src="https://loremflickr.com/640/480/meeting,business?random=use2" alt="Article" style="display: block; width: 100%; height: 100%; object-fit: cover;" />',
            header: `<div>
              <span style="display: inline-block; padding: 0.25rem 0.5rem; background: var(--gray-100); color: var(--gray-700); border-radius: var(--radius-1); font-size: 0.75rem; font-weight: 600; margin-bottom: 0.5rem;">MARKET INSIGHTS</span>
              <h3 style="margin: 0; font-size: 1.125rem; font-weight: 600; color: var(--gray-900);">European Real Estate Market Trends 2025</h3>
            </div>`,
            body: '<p style="margin: 0; font-size: 0.875rem; color: var(--gray-600); line-height: 1.5;">Analysis of key trends shaping the commercial real estate sector across major European markets...</p>',
            footer: `<div style="display: flex; gap: var(--size-2); align-items: center; font-size: 0.75rem; color: var(--gray-500);">
              <span>Dec 3, 2025</span>
              <span>•</span>
              <span>5 min read</span>
            </div>`,
          })}
        </div>
        
        <div>
          <h4 style="margin: 0 0 var(--size-4) 0; font-size: 1rem; font-weight: 600;">Simple Info Card (No Image)</h4>
          ${r({
            variant: 'outlined',
            radius: 'md',
            size: 'small',
            header:
              '<h3 style="margin: 0; font-size: 1rem; font-weight: 600; color: var(--gray-900);">Contact Information</h3>',
            body: `<div style="display: flex; flex-direction: column; gap: 0.5rem; font-size: 0.875rem; color: var(--gray-600);">
              <p style="margin: 0;">📧 contact@bnpparibas-realestate.com</p>
              <p style="margin: 0;">📞 +33 1 55 65 20 00</p>
              <p style="margin: 0;">📍 167 Quai de la Bataille de Stalingrad, Paris</p>
            </div>`,
          })}
        </div>
      </div>
    `,
  };
var m, p, c;
t.parameters = {
  ...t.parameters,
  docs: {
    ...((m = t.parameters) == null ? void 0 : m.docs),
    source: {
      originalSource: `{
  render: args => {
    return cardTwig({
      ...args,
      // Embed blocks as strings for Storybook
      image: \`<img src="https://loremflickr.com/640/480/office,building" alt="Modern office building" style="display: block; width: 100%; height: 100%; object-fit: cover;" />\`,
      header: \`<h3 style="margin: 0; font-size: 1.25rem; font-weight: 600; color: var(--gray-900);">Modern Office Space</h3>\`,
      body: \`<p style="margin: 0; font-size: 1rem; color: var(--gray-600);">Premium office building located in the heart of Madrid business district. Features modern amenities, underground parking, and excellent public transport connections.</p>\`,
      footer: \`<div style="display: flex; gap: 1rem; align-items: center;">
        <span style="font-size: 0.875rem; color: var(--gray-500);">📍 Madrid</span>
        <span style="font-size: 0.875rem; color: var(--gray-500);">📏 611 m²</span>
      </div>\`
    });
  },
  args: {
    ...data
  }
}`,
      ...((c = (p = t.parameters) == null ? void 0 : p.docs) == null ? void 0 : c.source),
    },
  },
};
var g, y, f;
i.parameters = {
  ...i.parameters,
  docs: {
    ...((g = i.parameters) == null ? void 0 : g.docs),
    source: {
      originalSource: `{
  render: () => {
    const variants = ['default', 'outlined', 'flat', 'elevated'];
    return \`
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: var(--size-6);">
        \${variants.map(variant => cardTwig({
      variant,
      radius: 'md',
      image: \`<img src="https://loremflickr.com/640/480/apartment,building?random=\${variant}" alt="Property" style="display: block; width: 100%; height: 200px; object-fit: cover;" />\`,
      header: \`<h3 style="margin: 0; font-size: 1.125rem; font-weight: 600; color: var(--gray-900);">\${variant.charAt(0).toUpperCase() + variant.slice(1)} Variant</h3>\`,
      body: \`<p style="margin: 0; font-size: 0.875rem; color: var(--gray-600);">Card with \${variant} visual style.</p>\`
    })).join('')}
      </div>
    \`;
  }
}`,
      ...((f = (y = i.parameters) == null ? void 0 : y.docs) == null ? void 0 : f.source),
    },
  },
};
var h, v, u;
o.parameters = {
  ...o.parameters,
  docs: {
    ...((h = o.parameters) == null ? void 0 : h.docs),
    source: {
      originalSource: `{
  render: () => {
    return \`
      <div style="display: grid; gap: var(--size-8);">
        <div>
          <h4 style="margin: 0 0 var(--size-4) 0; font-size: 1rem; font-weight: 600;">Vertical Layout (Default)</h4>
          \${cardTwig({
      layout: 'vertical',
      radius: 'md',
      image: \`<img src="https://loremflickr.com/640/480/house,property?random=1" alt="Property" style="display: block; width: 100%; height: 240px; object-fit: cover;" />\`,
      header: \`<h3 style="margin: 0; font-size: 1.25rem; font-weight: 600; color: var(--gray-900);">Luxury Apartment Paris</h3>\`,
      body: \`<p style="margin: 0; font-size: 1rem; color: var(--gray-600);">Spacious 3-bedroom apartment in the 8th arrondissement. High ceilings, Haussmann architecture, recent renovation.</p>\`,
      footer: \`<div style="display: flex; gap: 1rem;">
              <span style="font-size: 0.875rem; color: var(--gray-500);">📍 Paris 8e</span>
              <span style="font-size: 0.875rem; color: var(--gray-500);">📏 120 m²</span>
            </div>\`
    })}
        </div>
        
        <div>
          <h4 style="margin: 0 0 var(--size-4) 0; font-size: 1rem; font-weight: 600;">Horizontal Layout</h4>
          \${cardTwig({
      layout: 'horizontal',
      radius: 'md',
      image: \`<img src="https://loremflickr.com/640/480/villa,property?random=2" alt="Property" style="display: block; width: 100%; height: 100%; object-fit: cover;" />\`,
      header: \`<h3 style="margin: 0; font-size: 1.25rem; font-weight: 600; color: var(--gray-900);">Villa Côte d'Azur</h3>\`,
      body: \`<p style="margin: 0; font-size: 1rem; color: var(--gray-600);">Exceptional waterfront villa with panoramic Mediterranean sea views. Private pool, 5 bedrooms, landscaped garden.</p>\`,
      footer: \`<div style="display: flex; gap: 1rem;">
              <span style="font-size: 0.875rem; color: var(--gray-500);">📍 Nice</span>
              <span style="font-size: 0.875rem; color: var(--gray-500);">📏 350 m²</span>
            </div>\`
    })}
        </div>
        
        <div>
          <h4 style="margin: 0 0 var(--size-4) 0; font-size: 1rem; font-weight: 600;">Horizontal Layout (Image Right)</h4>
          \${cardTwig({
      layout: 'horizontal',
      imagePosition: 'right',
      radius: 'md',
      image: \`<img src="https://loremflickr.com/640/480/commercial,real-estate?random=3" alt="Property" style="display: block; width: 100%; height: 100%; object-fit: cover;" />\`,
      header: \`<h3 style="margin: 0; font-size: 1.25rem; font-weight: 600; color: var(--gray-900);">Commercial Space Barcelona</h3>\`,
      body: \`<p style="margin: 0; font-size: 1rem; color: var(--gray-600);">Prime retail location on Las Ramblas. High foot traffic, flexible layout, excellent visibility.</p>\`,
      footer: \`<div style="display: flex; gap: 1rem;">
              <span style="font-size: 0.875rem; color: var(--gray-500);">📍 Barcelona</span>
              <span style="font-size: 0.875rem; color: var(--gray-500);">📏 85 m²</span>
            </div>\`
    })}
        </div>
      </div>
    \`;
  }
}`,
      ...((u = (v = o.parameters) == null ? void 0 : v.docs) == null ? void 0 : u.source),
    },
  },
};
var z, b, w;
n.parameters = {
  ...n.parameters,
  docs: {
    ...((z = n.parameters) == null ? void 0 : z.docs),
    source: {
      originalSource: `{
  render: () => {
    const sizes = ['small', 'medium', 'large'];
    return \`
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: var(--size-6);">
        \${sizes.map(size => cardTwig({
      size,
      radius: 'md',
      image: \`<img src="https://loremflickr.com/640/480/property,building?random=\${size}" alt="Property" style="display: block; width: 100%; height: 200px; object-fit: cover;" />\`,
      header: \`<h3 style="margin: 0; font-size: 1.125rem; font-weight: 600; color: var(--gray-900);">\${size.charAt(0).toUpperCase() + size.slice(1)} Size</h3>\`,
      body: \`<p style="margin: 0; font-size: 0.875rem; color: var(--gray-600);">Card with \${size} padding.</p>\`
    })).join('')}
      </div>
    \`;
  }
}`,
      ...((w = (b = n.parameters) == null ? void 0 : b.docs) == null ? void 0 : w.source),
    },
  },
};
var x, k, P;
s.parameters = {
  ...s.parameters,
  docs: {
    ...((x = s.parameters) == null ? void 0 : x.docs),
    source: {
      originalSource: `{
  render: () => {
    const radiusOptions = ['none', 'sm', 'md', 'lg'];
    return \`
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: var(--size-6);">
        \${radiusOptions.map(radius => cardTwig({
      radius,
      image: \`<img src="https://loremflickr.com/640/480/architecture,building?random=\${radius}" alt="Property" style="display: block; width: 100%; height: 200px; object-fit: cover;" />\`,
      header: \`<h3 style="margin: 0; font-size: 1.125rem; font-weight: 600; color: var(--gray-900);">Radius: \${radius}</h3>\`,
      body: \`<p style="margin: 0; font-size: 0.875rem; color: var(--gray-600);">Border radius variant.</p>\`
    })).join('')}
      </div>
    \`;
  }
}`,
      ...((P = (k = s.parameters) == null ? void 0 : k.docs) == null ? void 0 : P.source),
    },
  },
};
var $, C, j;
l.parameters = {
  ...l.parameters,
  docs: {
    ...(($ = l.parameters) == null ? void 0 : $.docs),
    source: {
      originalSource: `{
  render: () => {
    return \`
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: var(--size-6);">
        \${cardTwig({
      url: '#property-123',
      variant: 'elevated',
      radius: 'md',
      image: \`<img src="https://loremflickr.com/640/480/office,property?random=click1" alt="Property" style="display: block; width: 100%; height: 200px; object-fit: cover;" />\`,
      header: \`<h3 style="margin: 0; font-size: 1.125rem; font-weight: 600; color: var(--gray-900);">Office Space Madrid</h3>\`,
      body: \`<p style="margin: 0; font-size: 0.875rem; color: var(--gray-600);">Click to view details (hover for effect).</p>\`,
      footer: \`<span style="font-size: 0.875rem; color: var(--primary); font-weight: 600;">View property →</span>\`
    })}
        
        \${cardTwig({
      url: '#property-456',
      variant: 'elevated',
      radius: 'md',
      image: \`<img src="https://loremflickr.com/640/480/apartment,property?random=click2" alt="Property" style="display: block; width: 100%; height: 200px; object-fit: cover;" />\`,
      header: \`<h3 style="margin: 0; font-size: 1.125rem; font-weight: 600; color: var(--gray-900);">Apartment Barcelona</h3>\`,
      body: \`<p style="margin: 0; font-size: 0.875rem; color: var(--gray-600);">Click to view details (hover for effect).</p>\`,
      footer: \`<span style="font-size: 0.875rem; color: var(--primary); font-weight: 600;">View property →</span>\`
    })}
      </div>
    \`;
  }
}`,
      ...((j = (C = l.parameters) == null ? void 0 : C.docs) == null ? void 0 : j.source),
    },
  },
};
var A, S, T;
d.parameters = {
  ...d.parameters,
  docs: {
    ...((A = d.parameters) == null ? void 0 : A.docs),
    source: {
      originalSource: `{
  render: () => {
    return \`
      <div style="display: grid; gap: var(--size-8);">
        <div>
          <h4 style="margin: 0 0 var(--size-4) 0; font-size: 1rem; font-weight: 600;">Property Listing Card</h4>
          \${cardTwig({
      url: '#property-789',
      variant: 'elevated',
      radius: 'lg',
      image: \`<img src="https://loremflickr.com/640/480/real-estate,listing?random=use1" alt="Property listing" style="display: block; width: 100%; height: 240px; object-fit: cover;" />\`,
      header: \`<div style="display: flex; justify-content: space-between; align-items: start;">
              <h3 style="margin: 0; font-size: 1.25rem; font-weight: 600; color: var(--gray-900);">Penthouse Paris 16e</h3>
              <span style="padding: 0.25rem 0.75rem; background: var(--primary); color: white; border-radius: var(--radius-round); font-size: 0.75rem; font-weight: 600;">NEW</span>
            </div>\`,
      body: \`<p style="margin: 0 0 var(--size-3) 0; font-size: 1rem; color: var(--gray-600);">Exceptional duplex penthouse with 360° views of Paris. Terraces, private elevator, luxury finishes.</p>
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
            </div>\`,
      footer: \`<div style="display: flex; justify-content: space-between; align-items: center;">
              <span style="font-size: 1.5rem; font-weight: 700; color: var(--gray-900);">€2,500,000</span>
              <span style="font-size: 0.875rem; color: var(--primary); font-weight: 600;">View details →</span>
            </div>\`
    })}
        </div>
        
        <div>
          <h4 style="margin: 0 0 var(--size-4) 0; font-size: 1rem; font-weight: 600;">News/Blog Card</h4>
          \${cardTwig({
      url: '#article-123',
      layout: 'horizontal',
      radius: 'md',
      image: \`<img src="https://loremflickr.com/640/480/meeting,business?random=use2" alt="Article" style="display: block; width: 100%; height: 100%; object-fit: cover;" />\`,
      header: \`<div>
              <span style="display: inline-block; padding: 0.25rem 0.5rem; background: var(--gray-100); color: var(--gray-700); border-radius: var(--radius-1); font-size: 0.75rem; font-weight: 600; margin-bottom: 0.5rem;">MARKET INSIGHTS</span>
              <h3 style="margin: 0; font-size: 1.125rem; font-weight: 600; color: var(--gray-900);">European Real Estate Market Trends 2025</h3>
            </div>\`,
      body: \`<p style="margin: 0; font-size: 0.875rem; color: var(--gray-600); line-height: 1.5;">Analysis of key trends shaping the commercial real estate sector across major European markets...</p>\`,
      footer: \`<div style="display: flex; gap: var(--size-2); align-items: center; font-size: 0.75rem; color: var(--gray-500);">
              <span>Dec 3, 2025</span>
              <span>•</span>
              <span>5 min read</span>
            </div>\`
    })}
        </div>
        
        <div>
          <h4 style="margin: 0 0 var(--size-4) 0; font-size: 1rem; font-weight: 600;">Simple Info Card (No Image)</h4>
          \${cardTwig({
      variant: 'outlined',
      radius: 'md',
      size: 'small',
      header: \`<h3 style="margin: 0; font-size: 1rem; font-weight: 600; color: var(--gray-900);">Contact Information</h3>\`,
      body: \`<div style="display: flex; flex-direction: column; gap: 0.5rem; font-size: 0.875rem; color: var(--gray-600);">
              <p style="margin: 0;">📧 contact@bnpparibas-realestate.com</p>
              <p style="margin: 0;">📞 +33 1 55 65 20 00</p>
              <p style="margin: 0;">📍 167 Quai de la Bataille de Stalingrad, Paris</p>
            </div>\`
    })}
        </div>
      </div>
    \`;
  }
}`,
      ...((T = (S = d.parameters) == null ? void 0 : S.docs) == null ? void 0 : T.source),
    },
  },
};
const I = [
  'Default',
  'AllVariants',
  'AllLayouts',
  'AllSizes',
  'AllRadius',
  'ClickableCards',
  'UseCases',
];
export {
  o as AllLayouts,
  s as AllRadius,
  n as AllSizes,
  i as AllVariants,
  l as ClickableCards,
  t as Default,
  d as UseCases,
  I as __namedExportsOrder,
  B as default,
};
