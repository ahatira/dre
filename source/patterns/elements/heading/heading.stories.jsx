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
          'Liste de biens immobiliers. H2 avec `.text-primary` pour catégorie, H3 avec `.font-light` (300) pour biens individuels - crée hiérarchie visuelle légère et aérée.',
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

export const RealEstateHero = {
  render: () => `
    <section style="padding: var(--size-10); background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%); color: var(--white);">
      <h6 class="text-light uppercase" style="margin-bottom: var(--size-3);">Nouvelle opportunité</h6>
      <h1 class="text-light font-black" style="margin-bottom: var(--size-4); font-size: var(--font-size-11); line-height: var(--leading-tight);">Tour CB21 - La Défense</h1>
      <h2 class="text-light font-light" style="margin-bottom: 0; font-size: var(--font-size-6);">8 500 m² de bureaux divisibles</h2>
    </section>
  `,
  parameters: {
    docs: {
      description: {
        story:
          'Hero section avec **combinaisons avancées** : `.text-light` sur fond brand, `.font-black` pour impact, `.font-light` pour subtitle. Démontre override de font-size/line-height/margin-bottom via inline styles pour cas exceptionnels.',
      },
    },
  },
};

export const ResponsiveHeadings = {
  render: () => `
    <style>
      @media (max-width: 768px) {
        .hero-title { font-size: var(--font-size-8) !important; }
        .hero-subtitle { font-size: var(--font-size-4) !important; }
      }
    </style>
    <div style="padding: var(--size-8); background: var(--gray-50);">
      <h1 class="hero-title text-primary font-extrabold" style="font-size: var(--font-size-11); margin-bottom: var(--size-4);">Immobilier d'entreprise</h1>
      <h2 class="hero-subtitle font-light" style="font-size: var(--font-size-6); margin-bottom: 0;">Paris • Lyon • Marseille • Bordeaux</h2>
      <p style="margin-top: var(--size-6); color: var(--gray-600);">Resize browser to see responsive behavior (desktop: 60px/28px → mobile: 36px/24px)</p>
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story:
          '**Responsive Typography**: Combinaison utilities + inline styles + media queries. Font-size réduit sur mobile via classes custom. Pattern réaliste pour hero sections adaptatives.',
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

export const StatusBadges = {
  render: () => `
    <style>
      .status-heading { display: flex; align-items: center; gap: var(--size-2); }
      .status-heading::before { content: ''; display: inline-block; width: 8px; height: 8px; border-radius: 50%; }
      .status-heading.success::before { background: var(--success); }
      .status-heading.info::before { background: var(--info); }
      .status-heading.warning::before { background: var(--warning); }
      .status-heading.danger::before { background: var(--danger); }
    </style>
    <div style="display: flex; flex-direction: column; gap: var(--size-5); padding: var(--size-6); background: var(--white); border-radius: var(--radius-3); box-shadow: var(--shadow-2);">
      <h4 class="status-heading success text-success font-semibold" style="margin: 0;">Offre acceptée</h4>
      <h4 class="status-heading info text-info font-semibold" style="margin: 0;">En cours d'instruction</h4>
      <h4 class="status-heading warning text-warning font-semibold" style="margin: 0;">Documents manquants</h4>
      <h4 class="status-heading danger text-danger font-semibold" style="margin: 0;">Offre refusée</h4>
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story:
          'Statuts avec indicateurs visuels. Combinaison **utilities + custom CSS** : `.text-{semantic}` pour couleur, `.font-semibold` pour poids, `::before` pseudo-element pour dot. Pattern réaliste pour dashboards.',
      },
    },
  },
};

export const SkipLinks = {
  render: () => `
    <style>
      .skip-link { position: absolute; top: -40px; left: 0; background: var(--primary); color: var(--white); padding: var(--size-2) var(--size-4); z-index: 100; transition: top 0.2s; }
      .skip-link:focus { top: 0; outline: 3px solid var(--warning); outline-offset: 2px; }
    </style>
    <div>
      <a href="#main-content" class="skip-link">Aller au contenu principal</a>
      <a href="#search" class="skip-link" style="left: 200px;">Aller à la recherche</a>
      
      <h1 class="visually-hidden">BNP Paribas Real Estate</h1>
      
      <nav style="padding: var(--size-4); background: var(--gray-100); margin-bottom: var(--size-6);">
        <h2 class="visually-hidden">Navigation principale</h2>
        <ul style="list-style: none; padding: 0; display: flex; gap: var(--size-5); margin: 0;">
          <li><a href="#">Acheter</a></li>
          <li><a href="#">Louer</a></li>
          <li><a href="#">Vendre</a></li>
        </ul>
      </nav>
      
      <main id="main-content" style="padding: var(--size-6); background: var(--gray-50);">
        <h2>Nos dernières offres</h2>
        <p>Contenu principal ici...</p>
      </main>
      
      <aside id="search" style="padding: var(--size-6); background: var(--white); margin-top: var(--size-6);">
        <h2 class="visually-hidden">Recherche de biens</h2>
        <p>Formulaire de recherche ici...</p>
      </aside>
      
      <p style="margin-top: var(--size-6); padding: var(--size-4); background: var(--info-bg-subtle); color: var(--info); border-radius: var(--radius-2);">♿ Press TAB to reveal skip links. H1 and section headings use <code>.visually-hidden</code> for screen readers.</p>
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story:
          "**Pattern d'accessibilité complet** : Skip links (`:focus` reveals), `.visually-hidden` pour structure sémantique (H1, H2 sections). Conforme WCAG 2.2 AA - OPQTEAM bypass blocks (2.4.1).",
      },
    },
  },
};

export const CombinedUtilities = {
  render: () => `
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: var(--size-6);">
      <!-- Card 1: Primary bold centered -->
      <div style="padding: var(--size-6); background: var(--gray-50); border-radius: var(--radius-3);">
        <h3 class="text-center text-primary font-black" style="margin-bottom: var(--size-3);">Bureaux neufs</h3>
        <p class="text-center" style="color: var(--gray-600); margin: 0;">La Défense</p>
      </div>
      
      <!-- Card 2: Secondary light right-aligned -->
      <div style="padding: var(--size-6); background: var(--gray-50); border-radius: var(--radius-3);">
        <h3 class="text-right text-secondary font-light" style="margin-bottom: var(--size-3);">Locaux commerciaux</h3>
        <p class="text-right" style="color: var(--gray-600); margin: 0;">Champs-Élysées</p>
      </div>
      
      <!-- Card 3: Gold extrabold with custom spacing -->
      <div style="padding: var(--size-6); background: linear-gradient(135deg, var(--gold-subtle) 0%, var(--gold-bg-subtle) 100%); border-radius: var(--radius-3);">
        <h3 class="text-gold font-extrabold" style="margin-bottom: var(--size-2); letter-spacing: var(--tracking-wide); text-transform: uppercase;">Prestige</h3>
        <p style="color: var(--gray-700); margin: 0; font-size: var(--font-size-2);">Hôtels particuliers</p>
      </div>
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story:
          '**Combinaisons avancées** : Utilities (`.text-center`, `.text-primary`, `.font-black`) + inline styles (margin-bottom, letter-spacing, text-transform). Démontre flexibilité pour cas complexes sans créer modifiers CSS.',
      },
    },
  },
};
