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
          'Semantic heading component (h1–h6) with component-scoped CSS variables for typography, colors, weights, and alignment. Supports visually hidden mode for accessibility.',
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
    // Appearance
    color: {
      control: { type: 'select' },
      options: [
        'default',
        'primary',
        'secondary',
        'success',
        'warning',
        'danger',
        'info',
        'gold',
        'light',
        'dark',
      ],
      description: 'Semantic color variant',
      table: {
        category: 'Appearance',
        type: {
          summary:
            'default | primary | secondary | success | warning | danger | info | gold | light | dark',
        },
        defaultValue: { summary: 'default' },
      },
    },
    weight: {
      control: { type: 'select' },
      options: ['light', 'regular', 'bold', 'extra'],
      description: 'Font weight variant (light: 300, regular: 400, bold: 700, extra: 800)',
      table: {
        category: 'Appearance',
        type: { summary: 'light | regular | bold | extra' },
        defaultValue: { summary: 'bold' },
      },
    },
    align: {
      control: { type: 'inline-radio' },
      options: ['left', 'center', 'right'],
      description: 'Text alignment',
      table: {
        category: 'Appearance',
        type: { summary: 'left | center | right' },
        defaultValue: { summary: 'left' },
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
  },
};

export const Default = {
  render: (args) => component(args),
  args: { ...data },
};

export const SemanticHierarchy = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-6);">
      ${component({ level: 'h1', text: 'Bureaux Haussmanniens Paris 8ème' })}
      ${component({ level: 'h2', text: 'Caractéristiques du bien' })}
      ${component({ level: 'h3', text: 'Surface et aménagement' })}
      ${component({ level: 'h4', text: 'Espaces de travail' })}
      ${component({ level: 'h5', text: 'Open space principal' })}
      ${component({ level: 'h6', text: 'Bureau individuel' })}
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
      ${component({
        level: 'h2',
        text: 'Immobilier de bureaux à louer',
        color: 'primary',
      })}
      ${component({
        level: 'h3',
        text: 'Tour Horizon - La Défense',
        weight: 'regular',
      })}
      ${component({
        level: 'h3',
        text: 'Bureaux de standing - Neuilly-sur-Seine',
        weight: 'regular',
      })}
      ${component({
        level: 'h3',
        text: 'Immeuble mixte - Lyon Part-Dieu',
        weight: 'regular',
      })}
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
      ${component({ level: 'h3', text: 'Offre acceptée', color: 'success' })}
      ${component({ level: 'h3', text: "Dossier en cours d'instruction", color: 'info' })}
      ${component({ level: 'h3', text: 'Documents manquants', color: 'warning' })}
      ${component({ level: 'h3', text: 'Offre refusée', color: 'danger' })}
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story:
          "Statuts de transactions immobilières. Les couleurs sémantiques renforcent visuellement l'état du dossier (success/info/warning/danger).",
      },
    },
  },
};

export const ColorVariants = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-5);">
      ${component({ level: 'h3', text: 'Default gray heading', color: 'default' })}
      ${component({ level: 'h3', text: 'Primary brand heading', color: 'primary' })}
      ${component({ level: 'h3', text: 'Secondary accent heading', color: 'secondary' })}
      ${component({ level: 'h3', text: 'Success status heading', color: 'success' })}
      ${component({ level: 'h3', text: 'Warning status heading', color: 'warning' })}
      ${component({ level: 'h3', text: 'Danger status heading', color: 'danger' })}
      ${component({ level: 'h3', text: 'Info status heading', color: 'info' })}
      ${component({ level: 'h3', text: 'Gold premium heading', color: 'gold' })}
      ${component({ level: 'h3', text: 'Light heading (for dark bg)', color: 'light' })}
      ${component({ level: 'h3', text: 'Dark heading (for light bg)', color: 'dark' })}
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story:
          'Toutes les couleurs sémantiques disponibles pour les titres. Utilisez default pour texte standard, primary/secondary pour branding, success/warning/danger/info pour statuts, gold pour contenu premium, light/dark pour contraste.',
      },
    },
  },
};

export const WeightVariants = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-5);">
      ${component({ level: 'h2', text: 'Light weight (300)', weight: 'light' })}
      ${component({ level: 'h2', text: 'Regular weight (400)', weight: 'regular' })}
      ${component({ level: 'h2', text: 'Bold weight (700) - Default', weight: 'bold' })}
      ${component({ level: 'h2', text: 'Extra bold weight (800)', weight: 'extra' })}
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story:
          'Variations de poids typographiques. Light pour titres aérés, Regular pour sous-titres, Bold (défaut) pour emphase standard, Extra pour impact maximum.',
      },
    },
  },
};

export const PropertyCategories = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-6);">
      ${component({
        level: 'h2',
        text: 'Immobilier tertiaire',
        align: 'center',
        color: 'primary',
        weight: 'extra',
      })}
      ${component({
        level: 'h2',
        text: 'Résidentiel de prestige',
        align: 'center',
        color: 'secondary',
        weight: 'bold',
      })}
      ${component({
        level: 'h2',
        text: 'Entrepôts logistiques',
        align: 'center',
        weight: 'regular',
      })}
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story:
          "Catégories de biens en page d'accueil. Titres centrés avec variations de poids et couleur pour différencier les secteurs d'activité.",
      },
    },
  },
};

export const MarketReports = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-7);">
      ${component({
        level: 'h1',
        text: 'Observatoire du marché immobilier Q4 2024',
        color: 'primary',
      })}
      ${component({
        level: 'h2',
        text: 'Analyse par typologie',
      })}
      ${component({
        level: 'h3',
        text: 'Bureaux premium - Paris intra-muros',
        weight: 'light',
      })}
      ${component({
        level: 'h4',
        text: 'Évolution des prix au m²',
      })}
      ${component({
        level: 'h4',
        text: 'Taux de vacance par arrondissement',
      })}
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story:
          'Structure documentaire pour rapports de marché. Hiérarchie claire avec h1 coloré, h3 en light pour sous-catégories, h4 pour métriques spécifiques.',
      },
    },
  },
};

export const AlignmentVariants = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-6); padding: var(--size-6); background: var(--gray-50);">
      ${component({
        level: 'h2',
        text: 'Portfolio immobilier',
        align: 'left',
      })}
      ${component({
        level: 'h2',
        text: 'Découvrez nos services',
        align: 'center',
        color: 'primary',
      })}
      ${component({
        level: 'h2',
        text: 'Contact expert',
        align: 'right',
        weight: 'light',
      })}
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story:
          "Variations d'alignement pour différents contextes. Gauche par défaut, centré pour mise en avant, droite pour éléments de navigation.",
      },
    },
  },
};

export const AccessibilityExample = {
  render: () => `
    <div>
      ${component({
        level: 'h2',
        text: 'Navigation principale',
        visuallyHidden: true,
      })}
      <nav>
        <ul style="list-style: none; padding: 0; display: flex; gap: var(--size-5);">
          <li><a href="#">Acheter</a></li>
          <li><a href="#">Louer</a></li>
          <li><a href="#">Vendre</a></li>
          <li><a href="#">Expertise</a></li>
        </ul>
      </nav>
      
      ${component({
        level: 'h2',
        text: 'Recherche de biens',
        visuallyHidden: true,
      })}
      <form style="padding: var(--size-6); background: var(--gray-50); margin-top: var(--size-6);">
        <p>Formulaire de recherche ici...</p>
      </form>
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story:
          "Titres structurants masqués visuellement (visuallyHidden) pour améliorer la navigation au clavier et l'accessibilité lecteur d'écran sans impact visuel.",
      },
    },
  },
};
