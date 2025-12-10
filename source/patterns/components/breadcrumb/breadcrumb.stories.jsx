import breadcrumbTwig from './breadcrumb.twig';
import breadcrumbData from './breadcrumb.yml';

const settings = {
  title: 'Components/Breadcrumb',
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component:
          'Breadcrumb shows the page hierarchy with semantic, accessible markup. Improves navigation and SEO with clear current-page indication. Separator (›) is auto-generated via CSS ::after pseudo-element.',
      },
    },
  },
  argTypes: {
    items: {
      description: 'Array of breadcrumb items with label and optional url',
      control: { type: 'object' },
      table: {
        category: 'Content',
        type: { summary: 'array<{label: string, url?: string}>' },
      },
    },
    compact: {
      description: 'Reduced size and spacing (12px font, 2px separator margin)',
      control: { type: 'boolean' },
      table: {
        category: 'Modifiers',
        type: { summary: 'boolean' },
        defaultValue: { summary: 'false' },
      },
    },
    inverted: {
      description: 'Dark theme for light backgrounds (white text, light hover)',
      control: { type: 'boolean' },
      table: {
        category: 'Modifiers',
        type: { summary: 'boolean' },
        defaultValue: { summary: 'false' },
      },
    },
    noUnderline: {
      description: 'Remove underline from links (shows on hover only)',
      control: { type: 'boolean' },
      table: {
        category: 'Modifiers',
        type: { summary: 'boolean' },
        defaultValue: { summary: 'false' },
      },
    },
  },
};

export const Default = {
  render: (args) => breadcrumbTwig(args),
  args: { ...breadcrumbData },
};

export const ShortPath = {
  name: 'Short Path (2 levels)',
  render: () =>
    breadcrumbTwig({
      items: [{ label: 'Home', url: '/' }, { label: 'Properties for Sale' }],
    }),
  parameters: {
    docs: {
      description: {
        story:
          'Minimal breadcrumb with only 2 levels - useful for shallow navigation hierarchies or landing pages.',
      },
    },
  },
};

export const DeepHierarchy = {
  name: 'Deep Hierarchy (7 levels)',
  render: () =>
    breadcrumbTwig({
      items: [
        { label: 'Home', url: '/' },
        { label: 'Real Estate', url: '/real-estate' },
        { label: 'Commercial', url: '/real-estate/commercial' },
        { label: 'Office Buildings', url: '/real-estate/commercial/offices' },
        { label: 'Paris', url: '/real-estate/commercial/offices/paris' },
        { label: '8th District', url: '/real-estate/commercial/offices/paris/8th' },
        { label: 'Champs-Élysées Premium Office Space' },
      ],
    }),
  parameters: {
    docs: {
      description: {
        story:
          'Complex navigation path with 7 levels - demonstrates flex-wrap behavior on narrow viewports and handles long hierarchies gracefully.',
      },
    },
  },
};

export const CommercialProperty = {
  name: 'Commercial Property Path',
  render: () =>
    breadcrumbTwig({
      items: [
        { label: 'Home', url: '/' },
        { label: 'Commercial Real Estate', url: '/commercial' },
        { label: 'Retail Spaces', url: '/commercial/retail' },
        { label: 'Paris 16th - Luxury Boutique Space' },
      ],
    }),
  parameters: {
    docs: {
      description: {
        story:
          'Real Estate métier example - Commercial property navigation path showing category hierarchy (Home → Category → Subcategory → Property).',
      },
    },
  },
};

export const ResidentialComplex = {
  name: 'Residential Complex Path',
  render: () =>
    breadcrumbTwig({
      items: [
        { label: 'Home', url: '/' },
        { label: 'Residential Properties', url: '/residential' },
        { label: 'Apartment Complexes', url: '/residential/complexes' },
        { label: 'Paris 15th District', url: '/residential/complexes/paris-15' },
        { label: 'Les Jardins de Vaugirard - Modern 3BR Apartment' },
      ],
    }),
  parameters: {
    docs: {
      description: {
        story:
          'Real Estate métier example - Residential complex navigation showing Location → Type → District → Specific Property with descriptive final label.',
      },
    },
  },
};

export const Compact = {
  name: 'Compact Variant',
  render: () =>
    breadcrumbTwig({
      items: [
        { label: 'Home', url: '/' },
        { label: 'Real Estate', url: '/real-estate' },
        { label: 'Commercial', url: '/real-estate/commercial' },
        { label: 'Office Buildings Paris 8th' },
      ],
      compact: true,
    }),
  parameters: {
    docs: {
      description: {
        story:
          'Compact variant with reduced font size (12px) and tighter spacing (2px separator margin). Useful for sidebars, footers, or space-constrained layouts.',
      },
    },
  },
};

export const Inverted = {
  name: 'Inverted Theme (Dark Backgrounds)',
  render: () =>
    breadcrumbTwig({
      items: [
        { label: 'Home', url: '/' },
        { label: 'Luxury Properties', url: '/luxury' },
        { label: 'Penthouse Apartments', url: '/luxury/penthouses' },
        { label: 'Paris 16th - Exclusive Penthouse' },
      ],
      inverted: true,
    }),
  parameters: {
    docs: {
      description: {
        story:
          'Inverted theme with white text for dark backgrounds (hero sections, dark headers). Links use lighter hover colors for better contrast.',
      },
    },
    backgrounds: {
      default: 'dark',
      values: [
        { name: 'dark', value: '#1F2A33' },
        { name: 'primary', value: '#00915A' },
      ],
    },
  },
};

export const NoUnderline = {
  name: 'No Underline Variant',
  render: () =>
    breadcrumbTwig({
      items: [
        { label: 'Home', url: '/' },
        { label: 'Properties', url: '/properties' },
        { label: 'Office Spaces', url: '/properties/offices' },
        { label: 'Premium Office Space' },
      ],
      noUnderline: true,
    }),
  parameters: {
    docs: {
      description: {
        story:
          'Clean design without underline on links by default. Underline appears on hover for better visual feedback. Useful for modern, minimalist designs.',
      },
    },
  },
};

export default settings;
