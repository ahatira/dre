import markup from './tags.twig';
import data from './tags.yml';

const settings = {
  title: 'Collections/Tags',
  tags: ['autodocs'],
  argTypes: {
    tags: {
      description:
        'Array of Tag atom properties. Each tag can have: label, variant (filled/outline), size, selected, removable, iconStart, url. NOTE: color parameter removed - only 2 variants exist per maquette.',
      control: 'object',
      table: { category: 'Content' },
    },
  },
  parameters: {
    docs: {
      description: {
        component:
          'Tags organism - Collection container for Tag atoms. Composes multiple Tag atoms with consistent spacing and wrapping. Individual tag styling is managed by the Tag atom component itself (Atomic Design composition pattern).',
      },
    },
  },
};

export default settings;

// ============================================
// DEFAULT STORY
// ============================================
export const Default = {
  render: (args) => markup(args),
  args: data,
};

// ============================================
// ACTIVE FILTERS
// ============================================
export const ActiveFilters = {
  render: (args) => markup(args),
  args: {
    tags: [
      {
        label: 'Paris 8e',
        variant: 'filled',
        size: 'md',
        selected: true,
        removable: true,
      },
      {
        label: 'Bureaux',
        variant: 'filled',
        size: 'md',
        selected: true,
        removable: true,
      },
      {
        label: '200-500 m²',
        variant: 'filled',
        size: 'md',
        selected: true,
        removable: true,
      },
      {
        label: 'Disponible',
        variant: 'filled',
        size: 'md',
        selected: true,
        removable: true,
      },
      {
        label: 'Climatisation',
        variant: 'filled',
        size: 'md',
        selected: true,
        removable: true,
      },
    ],
  },
  parameters: {
    docs: {
      description: {
        story:
          'Active search filters with filled blue tags. Typical use case for property search results page.',
      },
    },
  },
};

// ============================================
// MIXED VARIANTS
// ============================================
export const MixedVariants = {
  render: (args) => markup(args),
  args: {
    tags: [
      { label: 'Filled Blue', variant: 'filled', removable: true },
      { label: 'Outline White', variant: 'outline', removable: true },
      { label: 'Filled Selected', variant: 'filled', selected: true, removable: true },
      { label: 'Outline Not Removable', variant: 'outline', removable: false },
      { label: 'Filled Large', variant: 'filled', size: 'lg', removable: true },
    ],
  },
  parameters: {
    docs: {
      description: {
        story:
          'Mix of filled (blue) and outline (white) variants. Only 2 color styles exist per maquette. Each tag is an autonomous atom.',
      },
    },
  },
};

// ============================================
// SEARCH INPUT TAGS
// ============================================
export const SearchInputTags = {
  render: (args) => markup(args),
  args: {
    tags: [
      { label: 'Madrid', variant: 'outline', removable: true, iconStart: true },
      {
        label: 'Coworking to let',
        variant: 'outline',
        removable: true,
        iconStart: true,
      },
    ],
  },
  parameters: {
    docs: {
      description: {
        story:
          'Tags for search input (per maquette top image). Outline variant with icon at start for removal.',
      },
    },
  },
};

// ============================================
// CATEGORY LINKS
// ============================================
export const CategoryLinks = {
  render: (args) => markup(args),
  args: {
    tags: [
      { label: 'Bureaux', variant: 'filled', removable: false, url: '/bureaux' },
      {
        label: 'Commerces',
        variant: 'filled',
        removable: false,
        url: '/commerces',
      },
      {
        label: 'Entrepôts',
        variant: 'filled',
        removable: false,
        url: '/entrepots',
      },
      {
        label: 'Coworking',
        variant: 'filled',
        removable: false,
        url: '/coworking',
      },
      { label: 'Terrain', variant: 'filled', removable: false, url: '/terrain' },
    ],
  },
  parameters: {
    docs: {
      description: {
        story: 'Category navigation tags as clickable links without remove buttons.',
      },
    },
  },
};

// ============================================
// MANY TAGS (WRAPPING)
// ============================================
export const ManyTags = {
  render: (args) => markup(args),
  args: {
    tags: [
      {
        label: 'Paris 8e',
        variant: 'filled',
        size: 'sm',
        selected: true,
        removable: true,
      },
      {
        label: 'Paris 9e',
        variant: 'filled',
        size: 'sm',
        selected: false,
        removable: true,
      },
      {
        label: 'Paris 16e',
        variant: 'filled',
        size: 'sm',
        selected: true,
        removable: true,
      },
      {
        label: 'Neuilly-sur-Seine',
        variant: 'filled',
        size: 'sm',
        selected: false,
        removable: true,
      },
      {
        label: 'Levallois-Perret',
        variant: 'filled',
        size: 'sm',
        selected: true,
        removable: true,
      },
      {
        label: 'Boulogne-Billancourt',
        variant: 'filled',
        size: 'sm',
        selected: false,
        removable: true,
      },
      {
        label: 'Bureaux',
        variant: 'filled',
        size: 'sm',
        selected: true,
        removable: true,
      },
      {
        label: 'Commerces',
        variant: 'filled',
        size: 'sm',
        selected: false,
        removable: true,
      },
      {
        label: '100-200 m²',
        variant: 'filled',
        size: 'sm',
        selected: true,
        removable: true,
      },
      {
        label: '200-500 m²',
        variant: 'filled',
        size: 'sm',
        selected: false,
        removable: true,
      },
      {
        label: 'Disponible immédiatement',
        variant: 'filled',
        size: 'sm',
        selected: true,
        removable: true,
      },
      {
        label: 'Climatisation',
        variant: 'filled',
        size: 'sm',
        selected: true,
        removable: true,
      },
    ],
  },
  parameters: {
    docs: {
      description: {
        story:
          'Many tags automatically wrap with flex-wrap. Small size variant used for compact display. All tags use filled blue variant per maquette.',
      },
    },
  },
};
