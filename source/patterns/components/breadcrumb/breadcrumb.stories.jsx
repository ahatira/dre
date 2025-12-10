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

export const AllModifiers = {
  name: 'All Modifiers',
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-8);">
      <div>
        <h4 style="margin-bottom: var(--size-2); font-size: var(--font-size-0); color: var(--gray-700);">Default</h4>
        ${markup({
          items: [
            { label: 'Accueil', url: '/' },
            { label: 'Locations', url: '/locations' },
            { label: 'Paris 15ème', url: '/locations/paris-15' },
            { label: 'Appartement T4' },
          ],
        })}
      </div>
      
      <div>
        <h4 style="margin-bottom: var(--size-2); font-size: var(--font-size-0); color: var(--gray-700);">Compact</h4>
        ${markup({
          items: [
            { label: 'Accueil', url: '/' },
            { label: 'Bureaux', url: '/bureaux' },
            { label: 'La Défense', url: '/bureaux/la-defense' },
            { label: 'Tour Granite' },
          ],
          compact: true,
        })}
      </div>
      
      <div style="background: var(--gray-900); padding: var(--size-4); border-radius: var(--radius-2);">
        <h4 style="margin-bottom: var(--size-2); font-size: var(--font-size-0); color: var(--white);">Inverted</h4>
        ${markup({
          items: [
            { label: 'Accueil', url: '/' },
            { label: 'Investissement', url: '/investissement' },
            { label: 'Résidences Seniors', url: '/investissement/seniors' },
            { label: 'Villa Medicis' },
          ],
          inverted: true,
        })}
      </div>
      
      <div>
        <h4 style="margin-bottom: var(--size-2); font-size: var(--font-size-0); color: var(--gray-700);">No Underline</h4>
        ${markup({
          items: [
            { label: 'Accueil', url: '/' },
            { label: 'Terrains', url: '/terrains' },
            { label: 'Île-de-France', url: '/terrains/idf' },
            { label: 'Versailles' },
          ],
          noUnderline: true,
        })}
      </div>
    </div>
  `,
};

export const RealEstateUseCases = {
  name: 'Real Estate Use Cases',
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-8);">
      <div>
        <h3 style="margin-bottom: var(--size-3); color: var(--gray-900);">Property Listing Page</h3>
        ${markup({
          items: [
            { label: 'Accueil', url: '/' },
            { label: 'Locations', url: '/locations' },
            { label: 'Paris 15ème Arrondissement', url: '/locations/paris-15' },
            { label: 'Appartement familial T4 - Vue sur Tour Eiffel' },
          ],
        })}
      </div>
      
      <div>
        <h3 style="margin-bottom: var(--size-3); color: var(--gray-900);">Office Space Listing</h3>
        ${markup({
          items: [
            { label: 'Accueil', url: '/' },
            { label: 'Bureaux', url: '/bureaux' },
            { label: 'La Défense', url: '/bureaux/la-defense' },
            { label: 'Tour Granite - Plateau 1200m² - Vue panoramique Seine' },
          ],
        })}
      </div>
      
      <div>
        <h3 style="margin-bottom: var(--size-3); color: var(--gray-900);">Investment Program</h3>
        ${markup({
          items: [
            { label: 'Accueil', url: '/' },
            { label: 'Investissement', url: '/investissement' },
            { label: 'Résidences Services Seniors', url: '/investissement/seniors' },
            { label: 'Programme Villa Medicis - Neuilly-sur-Seine' },
          ],
        })}
      </div>
      
      <div>
        <h3 style="margin-bottom: var(--size-3); color: var(--gray-900);">Land Listing</h3>
        ${markup({
          items: [
            { label: 'Accueil', url: '/' },
            { label: 'Terrains', url: '/terrains' },
            { label: 'Île-de-France', url: '/terrains/ile-de-france' },
            { label: 'Terrain constructible 2500m² - Versailles' },
          ],
        })}
      </div>
      
      <div style="background: var(--gray-100); padding: var(--size-4); border-radius: var(--radius-2);">
        <h3 style="margin-bottom: var(--size-3); color: var(--gray-900);">Sidebar Navigation (Compact)</h3>
        ${markup({
          items: [
            { label: 'Accueil', url: '/' },
            { label: 'Commerces', url: '/commerces' },
            { label: 'Paris', url: '/commerces/paris' },
            { label: 'Boutique Champs-Élysées' },
          ],
          compact: true,
        })}
      </div>
    </div>
  `,
};
