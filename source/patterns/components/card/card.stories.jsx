import component from './card.twig';
import data from './card.yml';
import './card.css';

export default {
  title: 'Components/Card',
  render: (args) => component(args),
  args: data,
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component:
          'Versatile card component for presenting content with image, title, description, metadata, and actions. Supports multiple variants and layouts.',
      },
    },
  },
  argTypes: {
    // Content
    title: {
      control: 'text',
      description: 'Card title (required).',
      table: {
        category: 'Content',
        type: { summary: 'string', required: true },
        defaultValue: { summary: data.title || '""' },
      },
    },
    description: {
      control: 'text',
      description: 'Card description text (optional).',
      table: {
        category: 'Content',
        type: { summary: 'string' },
        defaultValue: { summary: '""' },
      },
    },
    eyebrow: {
      control: 'text',
      description: 'Eyebrow text displayed above the title (optional).',
      table: {
        category: 'Content',
        type: { summary: 'string' },
        defaultValue: { summary: '""' },
      },
    },
    badge: {
      control: 'text',
      description: 'Badge text to display on the card (optional).',
      table: {
        category: 'Content',
        type: { summary: 'string' },
        defaultValue: { summary: '""' },
      },
    },
    image: {
      control: 'object',
      description: 'Image data with url and alt properties.',
      table: {
        category: 'Content',
        type: { summary: '{ url: string, alt: string }' },
        defaultValue: { summary: 'null' },
      },
    },
    meta: {
      control: 'object',
      description: 'Array of metadata items with icon and text properties.',
      table: {
        category: 'Content',
        type: { summary: 'Array<{ icon: string, text: string }>' },
        defaultValue: { summary: '[]' },
      },
    },
    cta: {
      control: 'object',
      description: 'Call-to-action button configuration.',
      table: {
        category: 'Content',
        type: { summary: '{ text: string, url: string, variant?: string }' },
        defaultValue: { summary: 'null' },
      },
    },

    // Appearance
    variant: {
      control: { type: 'select' },
      options: [
        'product',
        'news',
        'publication',
        'solution',
        'study',
        'push',
        'featured',
        'compact',
      ],
      description: 'Card variant defining visual style and behavior.',
      table: {
        category: 'Appearance',
        type: {
          summary: 'product | news | publication | solution | study | push | featured | compact',
        },
        defaultValue: { summary: 'product' },
      },
    },
    layout: {
      control: { type: 'inline-radio' },
      options: ['vertical', 'horizontal'],
      description: 'Card layout orientation.',
      table: {
        category: 'Appearance',
        type: { summary: 'vertical | horizontal' },
        defaultValue: { summary: 'vertical' },
      },
    },

    // Link
    url: {
      control: 'text',
      description: 'Card link URL (makes entire card clickable).',
      table: {
        category: 'Link',
        type: { summary: 'string' },
        defaultValue: { summary: '""' },
      },
    },

    // Accessibility
    attributes: {
      control: false,
      description: 'Additional HTML attributes (Drupal Attribute object).',
      table: {
        category: 'Accessibility',
        type: { summary: 'Drupal\\Core\\Template\\Attribute' },
        defaultValue: { summary: 'null' },
      },
    },
  },
};

// Default story
export const Default = {
  render: (args) => component(args),
  args: { ...data },
};

// === Showcase Stories ===

// All variants
export const AllVariants = {
  render: () => `
    <div style="display: grid; gap: var(--size-6); grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));">
      ${component({
        variant: 'product',
        title: 'Luxury Apartment Paris',
        description: 'Modern 3-room apartment, 65m², close to metro.',
        eyebrow: 'Apartment',
        image: { url: 'https://picsum.photos/800/450?random=1', alt: 'Apartment' },
        meta: [
          { icon: 'pin-map', text: 'Paris 15th' },
          { icon: 'surface', text: '65 m²' },
        ],
        cta: { text: 'View', url: '#', variant: 'primary' },
      })}
      ${component({
        variant: 'news',
        title: 'Q4 Market Results',
        description: 'Strong performance across all business units.',
        eyebrow: 'Company News',
        image: { url: 'https://picsum.photos/800/600?random=2', alt: 'News' },
        meta: [{ icon: 'calendar', text: 'March 15' }],
        cta: { text: 'Read', url: '#', variant: 'primary' },
      })}
      ${component({
        variant: 'publication',
        title: 'Market Trends 2025',
        description: 'Comprehensive analysis of global real estate.',
        eyebrow: 'Research Report',
        badge: 'New',
        image: { url: 'https://picsum.photos/600/800?random=3', alt: 'Report' },
        meta: [{ icon: 'download', text: 'PDF 2.5 MB' }],
        cta: { text: 'Download', url: '#', variant: 'primary' },
      })}
      ${component({
        variant: 'solution',
        title: 'Investment Services',
        description: 'Expert guidance for your real estate investments.',
        eyebrow: 'Solutions',
        image: { url: 'https://picsum.photos/800/450?random=4', alt: 'Solution' },
        cta: { text: 'Learn More', url: '#', variant: 'primary' },
      })}
      ${component({
        variant: 'study',
        title: 'Case Study: Office Renovation',
        description: 'Successful transformation of a historic building.',
        eyebrow: 'Case Study',
        image: { url: 'https://picsum.photos/600/600?random=5', alt: 'Study' },
        meta: [{ icon: 'calendar', text: '2024' }],
        cta: { text: 'View', url: '#', variant: 'primary' },
      })}
      ${component({
        variant: 'push',
        title: 'Featured Property',
        description: 'Exclusive listing - limited time offer.',
        eyebrow: 'Exclusive',
        badge: 'Featured',
        image: { url: 'https://picsum.photos/800/450?random=6', alt: 'Featured' },
        meta: [
          { icon: 'pin-map', text: 'Paris' },
          { icon: 'surface', text: '120 m²' },
        ],
        cta: { text: 'Contact', url: '#', variant: 'primary' },
      })}
    </div>
  `,
};

// Featured and Compact
export const FeaturedAndCompact = {
  render: () => `
    <div style="display: grid; gap: var(--size-6); grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));">
      ${component({
        variant: 'featured',
        title: 'Premium Office Space',
        description: 'State-of-the-art facilities in the heart of Paris business district.',
        eyebrow: 'Featured Listing',
        image: { url: 'https://picsum.photos/800/450?random=7', alt: 'Office' },
        meta: [
          { icon: 'pin-map', text: 'Paris CBD' },
          { icon: 'surface', text: '500 m²' },
        ],
        cta: { text: 'Schedule Tour', url: '#', variant: 'primary' },
      })}
      ${component({
        variant: 'compact',
        title: 'Quick Update',
        description: 'New listings available this week.',
        eyebrow: 'Update',
        image: { url: 'https://picsum.photos/800/450?random=8', alt: 'Update' },
        meta: [{ icon: 'calendar', text: 'Today' }],
      })}
    </div>
  `,
};

// All layouts
export const AllLayouts = {
  render: () => `
    <div style="display: grid; gap: var(--size-6);">
      <div>
        <h4 style="margin: 0 0 var(--size-4); font-size: var(--font-size-2); font-weight: 600;">Vertical Layout</h4>
        ${component({
          layout: 'vertical',
          title: 'Vertical Card Layout',
          description: 'Default vertical layout with image on top.',
          eyebrow: 'Vertical',
          image: { url: 'https://picsum.photos/800/450?random=9', alt: 'Vertical' },
          meta: [{ icon: 'pin-map', text: 'Location' }],
          cta: { text: 'View', url: '#', variant: 'primary' },
        })}
      </div>
      <div>
        <h4 style="margin: 0 0 var(--size-4); font-size: var(--font-size-2); font-weight: 600;">Horizontal Layout</h4>
        ${component({
          layout: 'horizontal',
          title: 'Horizontal Card Layout',
          description: 'Horizontal layout with image on the left side.',
          eyebrow: 'Horizontal',
          image: { url: 'https://picsum.photos/600/600?random=10', alt: 'Horizontal' },
          meta: [{ icon: 'pin-map', text: 'Location' }],
          cta: { text: 'View', url: '#', variant: 'primary' },
        })}
      </div>
    </div>
  `,
};

// With and without images
export const WithAndWithoutImages = {
  render: () => `
    <div style="display: grid; gap: var(--size-6); grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));">
      ${component({
        title: 'With Image',
        description: 'Card with image, meta, and CTA.',
        eyebrow: 'Property',
        image: { url: 'https://picsum.photos/800/450?random=11', alt: 'Property' },
        meta: [{ icon: 'pin-map', text: 'Location' }],
        cta: { text: 'View', url: '#', variant: 'primary' },
      })}
      ${component({
        title: 'Without Image',
        description: 'Card without image, showing content only.',
        eyebrow: 'Update',
        meta: [{ icon: 'calendar', text: 'Today' }],
        cta: { text: 'Read', url: '#', variant: 'primary' },
      })}
    </div>
  `,
};

// As links
export const AsLinks = {
  render: () => `
    <div style="display: grid; gap: var(--size-6); grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));">
      ${component({
        url: '#property-link',
        title: 'Clickable Card',
        description: 'Entire card is clickable (hover to see effect).',
        eyebrow: 'Interactive',
        image: { url: 'https://picsum.photos/800/450?random=12', alt: 'Link card' },
        meta: [{ icon: 'pin-map', text: 'Location' }],
      })}
      ${component({
        title: 'With Button CTA',
        description: 'Card with explicit button instead of card link.',
        eyebrow: 'Standard',
        image: { url: 'https://picsum.photos/800/450?random=13', alt: 'Button card' },
        meta: [{ icon: 'pin-map', text: 'Location' }],
        cta: { text: 'View Details', url: '#', variant: 'primary' },
      })}
    </div>
  `,
};

// Use cases
export const UseCases = {
  render: () => `
    <div style="display: grid; gap: var(--size-8);">
      <div>
        <h4 style="margin: 0 0 var(--size-4); font-size: var(--font-size-3); font-weight: 600;">Property Listings</h4>
        <div style="display: grid; gap: var(--size-5); grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));">
          ${component({
            variant: 'product',
            url: '#property-1',
            title: 'Luxury Villa Cannes',
            description: '5 bedrooms, sea view, pool, 250m².',
            eyebrow: 'Villa',
            badge: 'Exclusive',
            image: { url: 'https://picsum.photos/800/450?random=14', alt: 'Villa' },
            meta: [
              { icon: 'pin-map', text: 'Cannes' },
              { icon: 'surface', text: '250 m²' },
              { icon: 'bedroom', text: '5 rooms' },
            ],
          })}
          ${component({
            variant: 'product',
            url: '#property-2',
            title: 'Modern Apartment Lyon',
            description: '2 bedrooms, downtown, 75m².',
            eyebrow: 'Apartment',
            image: { url: 'https://picsum.photos/800/450?random=15', alt: 'Apartment' },
            meta: [
              { icon: 'pin-map', text: 'Lyon' },
              { icon: 'surface', text: '75 m²' },
            ],
          })}
        </div>
      </div>
      <div>
        <h4 style="margin: 0 0 var(--size-4); font-size: var(--font-size-3); font-weight: 600;">News Grid</h4>
        <div style="display: grid; gap: var(--size-5); grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));">
          ${component({
            variant: 'news',
            layout: 'horizontal',
            title: 'Market Update Q1 2025',
            description: 'Analysis of market trends.',
            eyebrow: 'Market News',
            image: { url: 'https://picsum.photos/600/600?random=16', alt: 'News' },
            meta: [{ icon: 'calendar', text: 'March 30' }],
            cta: { text: 'Read', url: '#', variant: 'primary' },
          })}
          ${component({
            variant: 'news',
            layout: 'horizontal',
            title: 'New Office Opening',
            description: 'Expansion to new markets.',
            eyebrow: 'Company News',
            image: { url: 'https://picsum.photos/600/600?random=17', alt: 'News' },
            meta: [{ icon: 'calendar', text: 'March 28' }],
            cta: { text: 'Read', url: '#', variant: 'primary' },
          })}
        </div>
      </div>
      <div>
        <h4 style="margin: 0 0 var(--size-4); font-size: var(--font-size-3); font-weight: 600;">Featured Push</h4>
        ${component({
          variant: 'push',
          layout: 'horizontal',
          title: 'Exclusive Investment Opportunity',
          description: 'Limited time offer for premium properties in Paris.',
          eyebrow: 'Featured',
          badge: 'Hot Deal',
          image: { url: 'https://picsum.photos/800/600?random=18', alt: 'Featured' },
          meta: [
            { icon: 'pin-map', text: 'Paris' },
            { icon: 'calendar', text: 'Expires Soon' },
          ],
          cta: { text: 'Contact Us', url: '#', variant: 'primary' },
        })}
      </div>
    </div>
  `,
};
