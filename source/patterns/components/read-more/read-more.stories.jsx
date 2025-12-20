import readMoreTwig from './read-more.twig';
import data from './read-more.yml';
import './read-more.css';
import './read-more.js';

export default {
  title: 'Components/Read More',
  tags: ['autodocs'],
  render: (args) => readMoreTwig(args),
  args: data,
  parameters: {
    docs: {
      description: {
        component:
          'JavaScript-powered content expander for WYSIWYG content with smooth height transitions. Measures content height dynamically and reveals/collapses on click. Ideal for property descriptions, articles, and rich HTML content. Auto-hides toggle if content shorter than maxHeight.',
      },
    },
  },
  argTypes: {
    /* Content */
    content: {
      control: { type: 'text' },
      description:
        'Full WYSIWYG content (HTML allowed - paragraphs, lists, images, etc.). Use `|raw` filter in Twig.',
      table: {
        category: 'Content',
        type: { summary: 'string (HTML)', required: true },
      },
    },
    maxHeight: {
      control: { type: 'number', min: 50, max: 500, step: 10 },
      description: 'Maximum height in pixels before truncation (default: 150px ≈ 6 lines of text).',
      table: {
        category: 'Content',
        type: { summary: 'number' },
        defaultValue: { summary: '150' },
      },
    },
    expandLabel: {
      control: { type: 'text' },
      description: 'Button label when content is collapsed (French: "Voir plus").',
      table: {
        category: 'Content',
        type: { summary: 'string' },
        defaultValue: { summary: 'Voir plus' },
      },
    },
    collapseLabel: {
      control: { type: 'text' },
      description: 'Button label when content is expanded (French: "Voir moins").',
      table: {
        category: 'Content',
        type: { summary: 'string' },
        defaultValue: { summary: 'Voir moins' },
      },
    },
    showIcon: {
      control: { type: 'boolean' },
      description: 'Show chevron icon next to label.',
      table: {
        category: 'Appearance',
        type: { summary: 'boolean' },
        defaultValue: { summary: 'true' },
      },
    },

    /* Behavior */
    expanded: {
      control: { type: 'boolean' },
      description: 'Initial state: true = fully visible, false = truncated (default).',
      table: {
        category: 'Behavior',
        type: { summary: 'boolean' },
        defaultValue: { summary: 'false' },
      },
    },
  },
};

/**
 * Default
 * Real estate property description with WYSIWYG content (paragraphs + list)
 */
export const Default = {
  render: (args) => readMoreTwig(args),
  args: { ...data },
};

/**
 * Expanded State
 * Component initialized in expanded state (all content visible)
 */
export const ExpandedState = {
  render: (args) => readMoreTwig(args),
  args: {
    ...data,
    expanded: true,
  },
  parameters: {
    docs: {
      description: {
        story:
          'Component can be initialized in expanded state. Useful when deep-linking to specific content or for accessibility preferences.',
      },
    },
  },
};

/**
 * Different Max Heights
 * Showcase of 100px, 150px (default), 200px, and 300px truncation heights
 */
export const DifferentMaxHeights = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-8);">
      <div>
        <h3 style="margin: 0 0 var(--size-3); font-size: var(--font-size-2); color: var(--gray-700);">100px (compact)</h3>
        ${readMoreTwig({ ...data, maxHeight: 100 })}
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-3); font-size: var(--font-size-2); color: var(--gray-700);">150px (default)</h3>
        ${readMoreTwig({ ...data, maxHeight: 150 })}
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-3); font-size: var(--font-size-2); color: var(--gray-700);">200px (generous)</h3>
        ${readMoreTwig({ ...data, maxHeight: 200 })}
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-3); font-size: var(--font-size-2); color: var(--gray-700);">300px (very generous)</h3>
        ${readMoreTwig({ ...data, maxHeight: 300 })}
      </div>
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story:
          'Max height is configurable in pixels. Common values: 100px (compact), 150px (balanced default), 200-300px (generous preview).',
      },
    },
  },
};

/**
 * Short Content
 * When content is shorter than maxHeight, toggle auto-hides
 */
export const ShortContent = {
  render: (args) => readMoreTwig(args),
  args: {
    ...data,
    content: '<p>Studio lumineux de 25 m² au cœur du Marais. Idéal premier investissement.</p>',
    maxHeight: 150,
  },
  parameters: {
    docs: {
      description: {
        story:
          'When content height is less than maxHeight, JavaScript automatically hides the toggle button. Content displays at full height without truncation.',
      },
    },
  },
};

/**
 * Rich WYSIWYG Content
 * Complex HTML with headings, lists, bold, links
 */
export const RichWysiwygContent = {
  render: () =>
    readMoreTwig({
      content: `
        <h3 style="margin-top: 0; color: var(--gray-900);">Villa d'exception avec piscine</h3>
        <p>Découvrez cette <strong>propriété unique</strong> située dans un quartier prisé de <em>Neuilly-sur-Seine</em>.</p>
        <h4 style="color: var(--gray-700);">Caractéristiques principales</h4>
        <ul>
          <li><strong>Surface</strong> : 280 m² habitables</li>
          <li><strong>Terrain</strong> : 500 m² paysager</li>
          <li><strong>Chambres</strong> : 6 dont 1 suite parentale</li>
          <li><strong>Équipements</strong> : Piscine chauffée 12x5m, pool house, garage double</li>
        </ul>
        <h4 style="color: var(--gray-700);">Prestations haut de gamme</h4>
        <ul>
          <li>Parquet massif chêne dans toutes les pièces</li>
          <li>Cuisine équipée <a href="#" style="color: var(--primary);">Gaggenau</a></li>
          <li>Domotique complète (éclairage, chauffage, sécurité)</li>
          <li>Cave voutée aménagée</li>
        </ul>
        <p style="margin-bottom: 0;"><strong>Prix</strong> : 2 850 000 € (honoraires inclus)</p>
      `,
      maxHeight: 150,
      expandLabel: 'Voir le détail complet',
      collapseLabel: 'Réduire',
    }),
  parameters: {
    docs: {
      description: {
        story:
          'Component supports rich WYSIWYG content: headings, paragraphs, lists, bold, italic, links. JavaScript measures actual rendered height including all formatting.',
      },
    },
  },
};

/**
 * Without Icon
 * Toggle button without chevron icon
 */
export const WithoutIcon = {
  render: (args) => readMoreTwig(args),
  args: {
    ...data,
    showIcon: false,
  },
  parameters: {
    docs: {
      description: {
        story:
          'Icon can be hidden via `showIcon: false` parameter. Useful for minimal UI or specific design contexts.',
      },
    },
  },
};

/**
 * Real Estate Contexts
 * Different property types with varied content structures
 */
export const RealEstateContexts = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-8);">
      <div>
        <h3 style="margin: 0 0 var(--size-3); font-size: var(--font-size-2); color: var(--gray-700);">Bureaux (Office)</h3>
        ${readMoreTwig({
          content: `
            <p><strong>Bureaux de standing</strong> dans immeuble récent certifié HQE Excellent.</p>
            <ul>
              <li>Surface modulable de 450 m² au 5e étage</li>
              <li>Terrasse de 80 m²</li>
              <li>Triple exposition (nord/sud/ouest)</li>
              <li>Climatisation réversible, faux-plafonds acoustiques</li>
              <li>2 salles de réunion vitrées</li>
            </ul>
            <p>Parking sécurisé 15 places. Fibre optique déployée. Accès direct RER A et métro ligne 1.</p>
          `,
          maxHeight: 120,
        })}
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-3); font-size: var(--font-size-2); color: var(--gray-700);">Maison (House)</h3>
        ${readMoreTwig({
          content: `
            <p>Maison de maître du XIXe siècle entièrement rénovée avec matériaux nobles.</p>
            <ul>
              <li>280 m² habitables sur 3 niveaux</li>
              <li>6 chambres dont 1 suite parentale 40 m²</li>
              <li>Parquet massif chêne, poutres apparentes</li>
              <li>Cuisine ouverte équipée Gaggenau</li>
              <li>Jardin paysager 500 m² avec piscine chauffée 12x5m</li>
            </ul>
            <p>Garage double, cave voutée. Secteur résidentiel calme, proche écoles internationales.</p>
          `,
          maxHeight: 120,
        })}
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-3); font-size: var(--font-size-2); color: var(--gray-700);">Terrain (Land)</h3>
        ${readMoreTwig({
          content: `
            <p><strong>Terrain constructible viabilisé</strong> 1200 m² en zone résidentielle.</p>
            <p>Certificat d'urbanisme positif pour construction 200 m² sur 2 niveaux. Exposition plein sud, vue dégagée sur vallée.</p>
            <p>Accès direct voie communale, réseaux eau/électricité/assainissement en limite de propriété. Proche commodités (2 km centre-ville).</p>
          `,
          maxHeight: 100,
        })}
      </div>
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story:
          'Real estate contexts: offices (bureaux), houses (maisons), and land (terrains). Component adapts to different content structures and lengths.',
      },
    },
  },
};
