import component from './heading.twig';
import data from './heading.yml';
import './heading.css';

export default {
  title: 'Elements/Heading',
  render: (args) => component(args),
  args: data,
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component:
          'Semantic heading component (h1–h6) with **utility-first approach**. Use `.text-*` for colors, `.font-*` for weights, `.text-center` for alignment. Component provides base typography only.',
      },
    },
  },
  argTypes: {
    // Content
    text: {
      control: 'text',
      description: 'Heading text content',
      table: {
        category: 'Content',
        type: { summary: 'string', required: true },
        defaultValue: { summary: 'Heading text' },
      },
    },
    // Structure
    level: {
      control: { type: 'select' },
      options: ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'],
      description:
        'Semantic heading level (h1: 48px, h2: 36px, h3: 28px, h4: 24px, h5: 20px, h6: 16px)',
      table: {
        category: 'Structure',
        type: { summary: 'h1 | h2 | h3 | h4 | h5 | h6' },
        defaultValue: { summary: 'h1' },
      },
    },
    // Accessibility
    visuallyHidden: {
      control: 'boolean',
      description: 'Hide visually but keep for screen readers (accessibility)',
      table: {
        category: 'Accessibility',
        type: { summary: 'boolean' },
        defaultValue: { summary: 'false' },
      },
    },
    // Attributes (for utility classes)
    attributes: {
      control: false,
      description:
        '**Utility-first styling**: Use `attributes.addClass()` for variations. Examples: `attributes.addClass("text-primary")` for color, `attributes.addClass("font-bold")` for weight, `attributes.addClass("text-center")` for alignment. See utilities/colors.css and utilities/typography.css.',
      table: {
        category: 'Attributes',
        type: { summary: 'Attribute' },
      },
    },
  },
};

export const Default = {
  render: (args) => component(args),
  args: { ...data },
  parameters: {
    docs: {
      description: {
        story:
          'Default H1 heading with base typography. Use utility classes for styling variations.',
      },
    },
  },
};

export const SemanticHierarchy = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-6);">
      <h1>Bureaux Haussmanniens Paris 8ème</h1>
      <h2>Caractéristiques du bien</h2>
      <h3>Surface et aménagement</h3>
      <h4>Espaces de travail</h4>
      <h5>Open space principal</h5>
      <h6>Bureau individuel</h6>
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story:
          "Hiérarchie sémantique complète (h1-h6) avec contexte immobilier. Démontre l'utilisation correcte de la structure de titre pour une fiche de bien.",
      },
    },
  },
};

export const PropertyListings = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-8);">
      <h2 class="text-primary">Immobilier de bureaux à louer</h2>
      <h3 class="font-light">Tour Horizon - La Défense</h3>
      <h3 class="font-light">Bureaux de standing - Neuilly-sur-Seine</h3>
      <h3 class="font-light">Immeuble mixte - Lyon Part-Dieu</h3>
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story:
          "Titres de liste de biens immobiliers. Le h2 avec couleur primaire attire l'attention sur la catégorie, les h3 en regular weight représentent les biens individuels.",
      },
    },
  },
};

export const TransactionStatuses = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-5);">
      <h3 class="text-success">Offre acceptée</h3>
      <h3 class="text-info">Dossier en cours d'instruction</h3>
      <h3 class="text-warning">Documents manquants</h3>
      <h3 class="text-danger">Offre refusée</h3>
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story:
          "Statuts de transactions immobilières. Les couleurs sémantiques (.text-success, .text-info, .text-warning, .text-danger) renforcent visuellement l'état du dossier.",
      },
    },
  },
};

export const UtilityColors = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-5);">
      <h3>Default gray heading (no utility)</h3>
      <h3 class="text-primary">Primary brand heading (.text-primary)</h3>
      <h3 class="text-secondary">Secondary accent heading (.text-secondary)</h3>
      <h3 class="text-success">Success status heading (.text-success)</h3>
      <h3 class="text-warning">Warning status heading (.text-warning)</h3>
      <h3 class="text-danger">Danger status heading (.text-danger)</h3>
      <h3 class="text-info">Info status heading (.text-info)</h3>
      <h3 class="text-gold">Gold premium heading (.text-gold)</h3>
      <h3 class="text-light" style="background: var(--gray-900); padding: var(--size-3);">Light heading (.text-light - for dark bg)</h3>
      <h3 class="text-dark">Dark heading (.text-dark)</h3>
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story:
          '**Utility-First Colors**: All semantic colors via `.text-*` utilities from `utilities/colors.css`. Use `.text-primary/.text-secondary` for branding, `.text-success/.text-danger/.text-warning/.text-info` for statuses, `.text-gold` for premium, `.text-light/.text-dark` for contrast.',
      },
    },
  },
};

export const UtilityWeights = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-5);">
      <h2 class="font-light">Light weight 300 (.font-light)</h2>
      <h2 class="font-regular">Regular weight 400 (.font-regular)</h2>
      <h2>Bold weight 700 (default - no utility)</h2>
      <h2 class="font-extrabold">Extrabold weight 800 (.font-extrabold)</h2>
      <h2 class="font-black">Black weight 900 (.font-black)</h2>
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story:
          '**Utility-First Weights**: Use `.font-*` utilities from `utilities/typography.css`. Available: `.font-thin` (100) to `.font-black` (900). Default heading weight is 700 (bold), no utility needed.',
      },
    },
  },
};

export const PropertyCategories = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-6);">
      <h2 class="text-center text-primary font-extrabold">Immobilier tertiaire</h2>
      <h2 class="text-center text-secondary">Résidentiel de prestige</h2>
      <h2 class="text-center font-light">Entrepôts logistiques</h2>
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story:
          "Catégories de biens en page d'accueil. Démontre **combinaison d'utilities** : `.text-center` pour alignement, `.text-primary/.text-secondary` pour couleur, `.font-extrabold/.font-light` pour poids.",
      },
    },
  },
};

export const MarketReports = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-7);">
      <h1 class="text-primary">Observatoire du marché immobilier Q4 2024</h1>
      <h2>Analyse par typologie</h2>
      <h3 class="font-light">Bureaux premium - Paris intra-muros</h3>
      <h4>Évolution des prix au m²</h4>
      <h4>Taux de vacance par arrondissement</h4>
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story:
          'Structure documentaire pour rapports de marché. Hiérarchie claire avec h1 coloré (`.text-primary`), h3 en light (`.font-light`) pour sous-catégories, h4 pour métriques spécifiques.',
      },
    },
  },
};

export const UtilityAlignment = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-6); padding: var(--size-6); background: var(--gray-50);">
      <h2>Portfolio immobilier (left by default)</h2>
      <h2 class="text-center text-primary">Découvrez nos services (.text-center)</h2>
      <h2 class="text-right font-light">Contact expert (.text-right)</h2>
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story:
          '**Utility-First Alignment**: Use `.text-left`, `.text-center`, `.text-right` from `utilities/typography.css`. Default is left-aligned (no utility needed).',
      },
    },
  },
};

export const AccessibilityExample = {
  render: () => `
    <div>
      <h2 class="visually-hidden">Navigation principale</h2>
      <nav>
        <ul style="list-style: none; padding: 0; display: flex; gap: var(--size-5);">
          <li><a href="#">Acheter</a></li>
          <li><a href="#">Louer</a></li>
          <li><a href="#">Vendre</a></li>
          <li><a href="#">Expertise</a></li>
        </ul>
      </nav>
      
      <h2 class="visually-hidden">Recherche de biens</h2>
      <form style="padding: var(--size-6); background: var(--gray-50); margin-top: var(--size-6);">
        <p>Formulaire de recherche ici...</p>
      </form>
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story:
          "Titres structurants masqués visuellement via `.visually-hidden` utility (from `utilities/accessibility.css`) pour améliorer la navigation au clavier et l'accessibilité lecteur d'écran sans impact visuel.",
      },
    },
  },
};
