import markup from './breadcrumb.twig';
import data from './breadcrumb.yml';

export default {
  title: 'Components/Breadcrumb',
  tags: ['autodocs'],
  argTypes: {
    items: {
      control: 'object',
      description: 'List of breadcrumb items with `label` and optional `url`',
      table: {
        category: 'Content',
        type: { summary: 'array<{label: string, url?: string}>' },
      },
    },
    compact: {
      control: 'boolean',
      description: 'Reduced size variant (12px font, 12px separator, 2px separator margin)',
      table: {
        category: 'Modifiers',
        defaultValue: { summary: 'false' },
      },
    },
    inverted: {
      control: 'boolean',
      description: 'Dark theme with white text (for light backgrounds)',
      table: {
        category: 'Modifiers',
        defaultValue: { summary: 'false' },
      },
    },
    noUnderline: {
      control: 'boolean',
      description: '**DEPRECATED** Removes underline from links. Default has underline.',
      table: {
        category: 'Modifiers',
        defaultValue: { summary: 'false' },
      },
    },
  },
};

// ========================================
// STORIES
// ========================================

export const Default = {
  render: (args) => markup(args),
  args: data,
};

export const Compact = {
  render: (args) => markup(args),
  args: Object.assign({}, data, {
    compact: true,
  }),
};

export const Inverted = {
  render: (args) => {
    return `
      <div style="background-color: var(--gray-900); padding: var(--size-8);">
        ${markup(args)}
      </div>
    `;
  },
  args: Object.assign({}, data, {
    inverted: true,
  }),
};

export const LongLabels = {
  name: 'Long labels / Overflow',
  render: (args) => markup(args),
  args: {
    items: [
      { label: 'Accueil', url: '/' },
      { label: 'Locations', url: '/locations' },
      { label: 'Paris 15ème Arrondissement', url: '/locations/paris-15' },
      {
        label:
          'Appartement familial T4 - Vue sur Tour Eiffel - Long title to test overflow and wrap behaviour',
      },
    ],
  },
};

export const NoUnderlineDeprecated = {
  name: 'No underline (deprecated)',
  render: (args) => markup(args),
  args: {
    ...data,
    noUnderline: true,
  },
};
