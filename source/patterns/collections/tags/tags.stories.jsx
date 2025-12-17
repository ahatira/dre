import markup from './tags.twig';
import data from './tags.yml';

const settings = {
  title: 'Collections/Tags',
  tags: ['autodocs'],
  argTypes: {
    tags: {
      description:
        'Array of Tag atom properties. Each tag can have: label, variant (filled/outline), color, size, selected, removable, iconStart, url',
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
        color: 'primary',
        size: 'md',
        selected: true,
        removable: true,
      },
      {
        label: 'Bureaux',
        variant: 'filled',
        color: 'primary',
        size: 'md',
        selected: true,
        removable: true,
      },
      {
        label: '200-500 m²',
        variant: 'filled',
        color: 'primary',
        size: 'md',
        selected: true,
        removable: true,
      },
      {
        label: 'Disponible',
        variant: 'filled',
        color: 'primary',
        size: 'md',
        selected: true,
        removable: true,
      },
      {
        label: 'Climatisation',
        variant: 'filled',
        color: 'primary',
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
          'Active search filters with filled primary tags. Typical use case for property search results page.',
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
      { label: 'Filled Primary', variant: 'filled', color: 'primary', removable: true },
      { label: 'Outline Neutral', variant: 'outline', color: 'neutral', removable: true },
      { label: 'Filled Success', variant: 'filled', color: 'success', removable: true },
      { label: 'Outline Danger', variant: 'outline', color: 'danger', removable: true },
      { label: 'Filled Info', variant: 'filled', color: 'info', removable: false },
    ],
  },
  parameters: {
    docs: {
      description: {
        story:
          'Mix of filled and outline variants with different colors. Each tag is an autonomous atom.',
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
      { label: 'Madrid', variant: 'outline', color: 'neutral', removable: true, iconStart: true },
      {
        label: 'Coworking to let',
        variant: 'outline',
        color: 'neutral',
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
      { label: 'Bureaux', variant: 'filled', color: 'neutral', removable: false, url: '/bureaux' },
      {
        label: 'Commerces',
        variant: 'filled',
        color: 'neutral',
        removable: false,
        url: '/commerces',
      },
      {
        label: 'Entrepôts',
        variant: 'filled',
        color: 'neutral',
        removable: false,
        url: '/entrepots',
      },
      {
        label: 'Coworking',
        variant: 'filled',
        color: 'neutral',
        removable: false,
        url: '/coworking',
      },
      { label: 'Terrain', variant: 'filled', color: 'neutral', removable: false, url: '/terrain' },
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
        color: 'primary',
        size: 'sm',
        selected: true,
        removable: true,
      },
      {
        label: 'Paris 9e',
        variant: 'filled',
        color: 'primary',
        size: 'sm',
        selected: false,
        removable: true,
      },
      {
        label: 'Paris 16e',
        variant: 'filled',
        color: 'primary',
        size: 'sm',
        selected: true,
        removable: true,
      },
      {
        label: 'Neuilly-sur-Seine',
        variant: 'filled',
        color: 'primary',
        size: 'sm',
        selected: false,
        removable: true,
      },
      {
        label: 'Levallois-Perret',
        variant: 'filled',
        color: 'primary',
        size: 'sm',
        selected: true,
        removable: true,
      },
      {
        label: 'Boulogne-Billancourt',
        variant: 'filled',
        color: 'primary',
        size: 'sm',
        selected: false,
        removable: true,
      },
      {
        label: 'Bureaux',
        variant: 'filled',
        color: 'success',
        size: 'sm',
        selected: true,
        removable: true,
      },
      {
        label: 'Commerces',
        variant: 'filled',
        color: 'success',
        size: 'sm',
        selected: false,
        removable: true,
      },
      {
        label: '100-200 m²',
        variant: 'filled',
        color: 'info',
        size: 'sm',
        selected: true,
        removable: true,
      },
      {
        label: '200-500 m²',
        variant: 'filled',
        color: 'info',
        size: 'sm',
        selected: false,
        removable: true,
      },
      {
        label: 'Disponible immédiatement',
        variant: 'filled',
        color: 'warning',
        size: 'sm',
        selected: true,
        removable: true,
      },
      {
        label: 'Climatisation',
        variant: 'filled',
        color: 'secondary',
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
          'Many tags automatically wrap with flex-wrap. Small size variant used for compact display. Mix of colors demonstrates individual tag autonomy.',
      },
    },
  },
};
