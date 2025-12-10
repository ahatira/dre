import offerCardTwig from './offer-card.twig';
import data from './offer-card.yml';

const settings = {
  title: 'Components/Offer Card',
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
        category: 'Status',
        type: { summary: 'boolean' },
        defaultValue: { summary: false },
      },
    },
    'status.exclusivity': {
      control: 'boolean',
      description: 'Show "Exclusivity" badge',
      table: {
        category: 'Status',
        type: { summary: 'boolean' },
        defaultValue: { summary: false },
      },
    },
    'cta.text': {
      control: 'text',
      description: 'CTA link text',
      table: {
        category: 'CTA',
        type: { summary: 'string' },
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
  render: (args) => offerCardTwig(args),
  args: {
    ...data,
  },
};

// ==============================================
// STORY 2: Variants (Badge Combinations)
// ==============================================

export const Variants = {
  render: () => {
    const variants = [
      {
        key: 'all-badges',
        label: 'All Badges',
        status: { viewed: true, exclusivity: true },
      },
      {
        key: 'viewed-only',
        label: 'Viewed Only',
        status: { viewed: true, exclusivity: false },
      },
      {
        key: 'exclusivity-only',
        label: 'Exclusivity Only',
        status: { viewed: false, exclusivity: true },
      },
      {
        key: 'no-badges',
        label: 'No Badges',
        status: { viewed: false, exclusivity: false },
      },
    ];

    return `
      <div style="display: flex; gap: 1.5rem; flex-wrap: wrap;">
        ${variants
          .map(
            ({ label, status }) => `
          <div style="flex: 1; min-width: 280px;">
            <h4 style="margin: 0 0 0.5rem 0; font-size: 0.875rem; font-weight: 600;">${label}</h4>
            ${offerCardTwig({
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
          '**Badge combinations** demonstrate different status states:\n\n' +
          '- **All Badges**: Viewed + Exclusivity (most common)\n' +
          '- **Viewed Only**: Property previously viewed by user\n' +
          '- **Exclusivity Only**: Exclusive property listing\n' +
          '- **No Badges**: Clean state for new listings',
      },
    },
  },
};

// ==============================================
// STORY 3: Layouts (Vertical + Horizontal)
// ==============================================

export const Layouts = {
  render: () => {
    return `
      <div style="display: flex; flex-direction: column; gap: 3rem;">
        <div>
          <h3 style="margin: 0 0 1rem 0; font-size: 1rem; font-weight: 600;">Vertical Layout (Default)</h3>
          <p style="margin: 0 0 1rem 0; font-size: 0.875rem; color: var(--gray-600);">
            Mobile-friendly layout with image on top, content below
          </p>
          <div style="max-width: 400px;">
            ${offerCardTwig({
              ...data,
              layout: 'vertical',
            })}
          </div>
        </div>
        
        <div>
          <h3 style="margin: 0 0 1rem 0; font-size: 1rem; font-weight: 600;">Horizontal Layout</h3>
          <p style="margin: 0 0 1rem 0; font-size: 0.875rem; color: var(--gray-600);">
            Desktop grid layout with image on left, content on right
          </p>
          ${offerCardTwig({
            ...data,
            layout: 'horizontal',
          })}
        </div>
      </div>
    `;
  },
  parameters: {
    docs: {
      description: {
        story:
          '**Two responsive layouts** for different contexts:\n\n' +
          '- **Vertical**: Image top, content below - ideal for mobile and card grids\n' +
          '- **Horizontal**: Image left (40%), content right (60%) - desktop list view\n\n' +
          'Horizontal layout automatically stacks to vertical on screens < 768px.',
      },
    },
  },
};

// ==============================================
// STORY 4: As Link (Clickable Card)
// ==============================================

export const AsLink = {
  render: () => {
    return `
      <div style="max-width: 400px;">
        <p style="margin: 0 0 1rem 0; font-size: 0.875rem; color: var(--gray-600);">
          Card with <code>url</code> prop renders as clickable <code>&lt;a&gt;</code> element
        </p>
        ${offerCardTwig({
          ...data,
          url: '#property-detail-page',
          status: { viewed: false, exclusivity: true },
        })}
      </div>
    `;
  },
  parameters: {
    docs: {
      description: {
        story:
          '**Entire card is clickable** when `url` prop is provided.\n\n' +
          'Card renders as `<a>` element with proper accessibility. The CTA link inside uses pseudo-element technique to extend clickable area.',
      },
    },
  },
};
