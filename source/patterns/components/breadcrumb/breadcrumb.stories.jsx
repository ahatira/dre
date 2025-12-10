import markup from './breadcrumb.twig';
import data from './breadcrumb.yml';

const settings = {
  title: 'Components/Breadcrumb',
  tags: ['autodocs'],
  render: (args) => markup(args),
  args: { ...data },
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
      description: 'Reduced size variant (12px font, 2px separator margin)',
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
      description: 'Remove underline from links (shows on hover)',
      table: {
        category: 'Modifiers',
        defaultValue: { summary: 'false' },
      },
    },
  },
};

export default settings;

// ========================================
// STORIES
// ========================================

export const Default = {
  name: 'Default',
  args: {
    items: [
      { label: 'Accueil', url: '/' },
      { label: 'Locations', url: '/locations' },
      { label: 'Paris 15ème Arrondissement', url: '/locations/paris-15' },
      { label: 'Appartement familial T4 - Vue sur Tour Eiffel' },
    ],
  },
};

export const Compact = {
  name: 'Compact',
  args: {
    items: [
      { label: 'Accueil', url: '/' },
      { label: 'Bureaux', url: '/bureaux' },
      { label: 'La Défense', url: '/bureaux/la-defense' },
      { label: 'Tour Granite - Plateau 1200m²' },
    ],
    compact: true,
  },
};

export const Inverted = {
  name: 'Inverted (Dark Background)',
  args: {
    items: [
      { label: 'Accueil', url: '/' },
      { label: 'Investissement', url: '/investissement' },
      { label: 'Résidences Services Seniors', url: '/investissement/seniors' },
      { label: 'Programme Villa Medicis - Neuilly-sur-Seine' },
    ],
    inverted: true,
  },
  parameters: {
    backgrounds: { default: 'dark' },
  },
};

export const NoUnderline = {
  name: 'No Underline',
  args: {
    items: [
      { label: 'Accueil', url: '/' },
      { label: 'Terrains', url: '/terrains' },
      { label: 'Île-de-France', url: '/terrains/ile-de-france' },
      { label: 'Terrain constructible 2500m² - Versailles' },
    ],
    noUnderline: true,
  },
};

export const LongPath = {
  name: 'Long Path (8 levels)',
  args: {
    items: [
      { label: 'Accueil', url: '/' },
      { label: "Immobilier d'Entreprise", url: '/immobilier-entreprise' },
      { label: 'Bureaux', url: '/immobilier-entreprise/bureaux' },
      { label: 'Île-de-France', url: '/immobilier-entreprise/bureaux/idf' },
      { label: 'Paris Ouest', url: '/immobilier-entreprise/bureaux/idf/ouest' },
      { label: '8ème Arrondissement', url: '/immobilier-entreprise/bureaux/idf/ouest/75008' },
      {
        label: 'Quartier Champs-Élysées',
        url: '/immobilier-entreprise/bureaux/idf/ouest/75008/champs-elysees',
      },
      { label: 'Immeuble Haussmannien - 3500m² divisibles' },
    ],
  },
};

export const ShortPath = {
  name: 'Short Path (2 levels)',
  args: {
    items: [{ label: 'Accueil', url: '/' }, { label: 'Contact' }],
  },
};
