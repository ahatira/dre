# Architecture Technique - PS Theme

**Vue d'ensemble complète du stack et de la structure**

---

## 📦 Stack Technologique

### Production (Drupal)

```
Drupal 10/11
├── Twig Templates (.twig)
│   ├── Component templates (source/patterns/)
│   └── Drupal overrides (templates/)
├── YAML Configuration
│   ├── Component schemas (.yml)
│   ├── Libraries (ps.libraries.yml)
│   └── Theme info (ps.info.yml)
└── JavaScript Behaviors
    ├── Drupal.behaviors pattern
    └── once() for idempotency
```

**Justification** : Drupal est le CMS de BNP Paribas Real Estate. Le thème doit être 100% compatible avec l'écosystème Drupal.

### Développement (Storybook)

```
Storybook HTML Edition
├── Vite (Build tool)
│   ├── Dev server with HMR
│   ├── CSS processing
│   └── Asset bundling
├── PostCSS
│   ├── postcss-import (imports CSS)
│   ├── postcss-nested (nesting &)
│   └── autoprefixer (vendor prefixes)
├── Biome (Linting + Formatting)
│   ├── JavaScript/JSX
│   ├── JSON
│   └── Configuration files
└── @faker-js/faker
    └── Realistic mock data generation
```

**Justification** :
- **Storybook HTML** : Documentation interactive, test isolation, pas de framework lock-in
- **Vite** : Build ultra-rapide, HMR instantané, bundle optimisé
- **PostCSS** : Nesting natif (meilleure lisibilité), optimisations CSS
- **Biome** : Linter/formatter unifié (remplacement ESLint + Prettier, 10-100× plus rapide)

---

## 📁 Structure des Fichiers

### Vue d'ensemble

```
ps_theme/
├── .github/
│   ├── instructions/           # Documentation technique v4.0.0 (6 fichiers)
│   └── prompts/               # 13 prompts AI prêts à l'emploi
├── docs/                      # Documentation utilisateur (française)
│   ├── 01-presentation/       # Architecture et méthodologie
│   ├── 02-composants/         # Spécifications des 87 composants
│   ├── 03-tokens/             # Design tokens documentation
│   ├── 04-guide-developpement/ # Guides pratiques
│   ├── 05-changelog/          # Historique des implémentations
│   ├── 06-ressources/         # Maquettes Figma, assets
│   └── 07-integration-drupal/ # Guides intégration Drupal
├── scripts/                   # Scripts de build et génération
│   ├── build-icons.mjs        # Génération sprite + CSS icons
│   ├── check-tokens.mjs       # Recherche tokens avec statistiques
│   ├── generate-pattern.mjs   # Scaffolding composants
│   └── sync-libraries.mjs     # Synchronisation ps.libraries.yml
├── source/
│   ├── assets/                # Assets statiques
│   │   ├── flags/             # Drapeaux pays (SVG)
│   │   ├── fonts/             # Web fonts BNP Paribas
│   │   ├── icons/             # Sprite SVG généré
│   │   └── images/            # Images de contenu
│   ├── icons-source/          # Sources SVG icons (13 catégories)
│   ├── patterns/              # Composants Atomic Design
│   │   ├── base/              # Stories base (colors, fonts, etc.)
│   │   ├── elements/          # Atomes (19 composants)
│   │   ├── components/        # Molécules (20 composants)
│   │   ├── collections/       # Organismes (12 composants)
│   │   ├── layouts/           # Templates (8 composants)
│   │   ├── pages/             # Pages (8 composants)
│   │   └── documentation/     # Données générées (icons-registry.json)
│   └── props/                 # Design tokens (11 fichiers CSS)
│       ├── colors.css         # Palette complète + semantic
│       ├── sizes.css          # Échelle 0-32 (0.25rem)
│       ├── fonts.css          # Tailles + poids typographie
│       ├── brand.css          # Tokens sémantiques BNP
│       ├── borders.css        # Bordures + radius
│       ├── shadows.css        # Ombres + elevation
│       ├── animations.css     # Durées animation
│       ├── easing.css         # Courbes d'accélération
│       ├── media.css          # Breakpoints responsive
│       ├── zindex.css         # Empilements contextuels
│       └── icons.css          # Mappings icons (généré)
├── storybook/                 # Build Storybook statique
├── templates/                 # Overrides Drupal templates
│   ├── block/
│   ├── navigation/
│   ├── field/
│   └── ...
├── biome.json                 # Configuration Biome (lint + format)
├── package.json               # Dependencies + scripts npm
├── postcss.config.js          # Configuration PostCSS
├── ps.info.yml                # Configuration thème Drupal
├── ps.libraries.yml           # Déclaration assets Drupal
├── ps.layouts.yml             # Déclaration layouts Drupal
├── svgo.config.mjs            # Optimisation SVG
└── vite.config.js             # Configuration Vite + Storybook
```

---

## 🔨 Système de Build

### Commandes principales

```bash
# Développement
npm run watch               # Vite dev server + Storybook (http://localhost:6006)
npm run storybook           # Storybook seul (sans Vite)

# Build production
npm run build               # Compile assets + lint/format checks
npm run storybook:build     # Build Storybook statique (storybook/)

# Génération
npm run generate:pattern    # Scaffolding composant (interactif ou flags)
npm run build:icons         # Génération sprite SVG + icons.css
npm run tokens:check        # Recherche token (definition + usages)

# Qualité
npm run lint                # Biome lint (JS, JSON)
npm run format              # Biome format (écriture)
npm run check               # Biome check (lint + format sans écriture)
```

### Pipeline de build

```
npm run build
├── 1. Biome check (lint + format)
│   ├── Vérifie JavaScript/JSON
│   ├── Vérifie formatage
│   └── ❌ Bloque si erreurs
├── 2. Icon build (scripts/build-icons.mjs)
│   ├── Optimise SVG (SVGO)
│   ├── Génère sprite (icons-sprite.svg)
│   ├── Génère CSS (icons.css)
│   └── Génère registry (icons-registry.json)
├── 3. Vite build
│   ├── PostCSS processing
│   │   ├── postcss-import (résolution @import)
│   │   ├── postcss-nested (nesting &)
│   │   └── autoprefixer (vendor prefixes)
│   ├── Bundle CSS (styles.css)
│   ├── Bundle JS (patterns.js)
│   └── Copy assets (fonts, images, icons)
└── ✅ Output: storybook/ (déployable)
```

### Validation

Le build échoue si :
- ❌ Erreurs de lint (Biome)
- ❌ Formatage incorrect (Biome)
- ❌ Erreurs CSS (PostCSS)
- ❌ Erreurs JavaScript (Vite)
- ❌ Erreurs Storybook (build Storybook)

---

## 🧩 Structure des Composants

### Nomenclature des fichiers

Tous les composants suivent la **structure à 4 fichiers** (v2.1.0+) :

```
{component}/
├── {component}.twig        # Template Drupal (obligatoire)
├── {component}.css         # Styles avec tokens (obligatoire)
├── {component}.yml         # Mock data Drupal SDC (obligatoire)
└── {component}.stories.jsx # Storybook stories (obligatoire)
```

**Note** : Avant v2.1.0, les composants avaient un 5ème fichier `README.md`. Supprimé pour réduire la maintenance (docs centralisées dans `docs/02-composants/`).

### Convention de nommage

| Type | Convention | Exemples |
|------|-----------|----------|
| **Fichiers** | kebab-case | `card-offer-search.twig` |
| **Classes CSS** | BEM avec préfixe `ps-` | `.ps-button`, `.ps-button__icon`, `.ps-button--primary` |
| **Tokens CSS** | kebab-case avec `--` | `--primary`, `--size-4`, `--font-sans` |
| **Tokens composant** | `--ps-{component}-{property}` | `--ps-button-padding-x`, `--ps-card-border-radius` |
| **Namespaces Twig** | Atomic level | `@elements/`, `@components/`, `@collections/` |

### Exemple complet : Button

```twig
{# button.twig #}
{##
 # Button component - Interactive element for user actions
 #
 # @param string variant - Color variant (primary, secondary, success, etc.)
 # @param string size - Size variant (small, medium, large)
 # @param bool disabled - Disabled state
 # @param object attributes - Drupal attributes object
 #}
{% set classes = [
  'ps-button',
  variant ? 'ps-button--' ~ variant,
  size ? 'ps-button--' ~ size,
  disabled ? 'ps-button--disabled',
] %}

<button{{ attributes.addClass(classes) }} {{ disabled ? 'disabled' }}>
  {{ text }}
</button>
```

```css
/* button.css */
/**
 * Button Component
 * 
 * Layer 1: Global tokens (from source/props/)
 * Layer 2: Component-scoped variables
 * Layer 3: Modifiers override Layer 2
 */

.ps-button {
  /* Layer 2: Component defaults */
  --ps-button-padding-x: var(--size-4);
  --ps-button-padding-y: var(--size-2);
  --ps-button-font-size: var(--font-size-2);
  --ps-button-bg: var(--primary);
  --ps-button-color: var(--white);
  
  /* Styles using Layer 2 variables */
  padding: var(--ps-button-padding-y) var(--ps-button-padding-x);
  font-size: var(--ps-button-font-size);
  background: var(--ps-button-bg);
  color: var(--ps-button-color);
  border-radius: var(--radius-2);
  cursor: pointer;
  
  &:hover {
    --ps-button-bg: var(--primary-hover);
  }
  
  /* Layer 3: Modifiers */
  &--secondary {
    --ps-button-bg: var(--secondary);
    
    &:hover {
      --ps-button-bg: var(--secondary-hover);
    }
  }
  
  &--large {
    --ps-button-padding-x: var(--size-6);
    --ps-button-padding-y: var(--size-3);
    --ps-button-font-size: var(--font-size-3);
  }
}
```

```yaml
# button.yml
$schema: https://git.drupalcode.org/project/sdc/-/raw/1.x/src/metadata.schema.json
name: Button
status: stable
props:
  type: object
  properties:
    text:
      type: string
      title: Button text
    variant:
      type: string
      enum: ['primary', 'secondary', 'success', 'info', 'warning', 'danger']
      default: primary
    size:
      type: string
      enum: ['small', 'medium', 'large']
```

```jsx
// button.stories.jsx
import button from './button.twig';
import buttonData from './button.yml';

export default {
  title: 'Elements/Button',
  tags: ['autodocs'],
  render: (args) => button(args),
  argTypes: {
    text: { control: 'text', table: { category: 'Content' } },
    variant: { 
      control: 'select', 
      options: ['primary', 'secondary', 'success', 'info', 'warning', 'danger'],
      table: { category: 'Appearance' }
    },
    size: {
      control: 'select',
      options: ['small', 'medium', 'large'],
      table: { category: 'Appearance' }
    },
  }
};

export const Default = {
  args: { ...buttonData }
};

export const AllVariants = {
  render: () => `
    ${button({ text: 'Primary', variant: 'primary' })}
    ${button({ text: 'Secondary', variant: 'secondary' })}
    ${button({ text: 'Success', variant: 'success' })}
  `
};
```

---

## 🔗 Intégration Drupal

### Namespaces Twig

Configurés dans `ps.info.yml` :

```yaml
component-libraries:
  elements:
    paths:
      - source/patterns/elements
  components:
    paths:
      - source/patterns/components
  collections:
    paths:
      - source/patterns/collections
  layouts:
    paths:
      - source/patterns/layouts
  pages:
    paths:
      - source/patterns/pages
```

**Usage dans templates Drupal** :
```twig
{# templates/node/node--article.html.twig #}
{% include '@components/card/card.twig' with {
  title: node.label,
  body: content.body|render,
  image: { src: file_url(node.field_image.entity.uri.value) }
} only %}
```

### Libraries (Assets)

Déclaration dans `ps.libraries.yml` :

```yaml
global:
  css:
    theme:
      css/styles.css: {}
  js:
    js/patterns.js: {}
  dependencies:
    - core/drupal
    - core/once

button:
  css:
    component:
      patterns/elements/button/button.css: {}
```

**Attachement** :
```php
// ps.theme
function ps_preprocess_node(&$variables) {
  $variables['#attached']['library'][] = 'ps/button';
}
```

### Drupal Behaviors

Pattern JavaScript pour Drupal :

```javascript
/**
 * Dropdown behavior
 */
(function (Drupal, once) {
  Drupal.behaviors.psDropdown = {
    attach(context) {
      once('ps-dropdown', '.ps-dropdown', context).forEach((element) => {
        // Initialization logic here
        const trigger = element.querySelector('.ps-dropdown__trigger');
        const menu = element.querySelector('.ps-dropdown__menu');
        
        trigger?.addEventListener('click', () => {
          menu?.classList.toggle('ps-dropdown__menu--open');
        });
      });
    }
  };
})(Drupal, once);
```

**Justification** :
- `once()` : Empêche double initialisation (idempotence)
- `Drupal.behaviors` : Auto-exécution au chargement + AJAX
- `context` : Scope limité (performance + AJAX compatibility)

---

## 🎨 Design Tokens Architecture

### 3 Couches (Three-Tier System)

```
┌─────────────────────────────────────────┐
│ Layer 1: Global Tokens (source/props/)  │
│ Palette + primitives                     │
│ --gray-700, --size-4, --font-sans       │
└─────────────┬───────────────────────────┘
              │
┌─────────────▼───────────────────────────┐
│ Layer 2: Component Tokens               │
│ Component-scoped defaults                │
│ --ps-button-bg: var(--primary)          │
└─────────────┬───────────────────────────┘
              │
┌─────────────▼───────────────────────────┐
│ Layer 3: Context Overrides               │
│ Modifiers, consumers, utilities          │
│ .ps-button--secondary { --ps-button-bg  │
└──────────────────────────────────────────┘
```

**Exemple concret** :

```css
/* Layer 1: Global tokens (colors.css) */
:root {
  --green-600: #00915A;
  --green-700: #007A4C;
  --white: #FFFFFF;
  --size-4: 1rem;
  --size-2: 0.5rem;
}

/* Layer 1: Semantic tokens (brand.css) */
:root {
  --primary: var(--green-600);
  --primary-hover: var(--green-700);
}

/* Layer 2: Component tokens (button.css) */
.ps-button {
  --ps-button-bg: var(--primary);
  --ps-button-color: var(--white);
  --ps-button-padding-x: var(--size-4);
  --ps-button-padding-y: var(--size-2);
  
  background: var(--ps-button-bg);
  color: var(--ps-button-color);
  padding: var(--ps-button-padding-y) var(--ps-button-padding-x);
}

/* Layer 3: Context override (consumer CSS) */
.login-form .ps-button {
  --ps-button-padding-x: var(--size-6); /* Override pour cette page */
}
```

**Bénéfices** :
- ✅ **Maintenabilité** : Changement couleur brand → 1 seul fichier (brand.css)
- ✅ **Flexibilité** : Override tokens sans toucher au CSS de base
- ✅ **Cohérence** : Tous les composants utilisent les mêmes primitives
- ✅ **Performance** : Cascade CSS native (pas de recompilation)

### Fichiers tokens (source/props/)

| Fichier | Responsabilité | Exemples |
|---------|----------------|----------|
| `colors.css` | Palette complète | `--gray-700`, `--green-600`, `--white` |
| `brand.css` | Tokens sémantiques BNP | `--primary`, `--secondary`, `--success`, `--danger` |
| `sizes.css` | Échelle espacements | `--size-0` (0) → `--size-32` (8rem) |
| `fonts.css` | Typographie | `--font-sans`, `--font-size-2`, `--font-weight-600` |
| `borders.css` | Bordures + radius | `--border-size-1`, `--radius-2` |
| `shadows.css` | Ombres + elevation | `--shadow-sm`, `--shadow-lg` |
| `animations.css` | Durées | `--duration-fast`, `--duration-normal` |
| `easing.css` | Courbes accélération | `--ease-in-out`, `--ease-spring` |
| `media.css` | Breakpoints | `--breakpoint-tablet`, `--breakpoint-desktop` |
| `zindex.css` | Empilements | `--z-dropdown`, `--z-modal`, `--z-tooltip` |
| `icons.css` | Mappings icons (généré) | `[data-icon="check"]::before` |

---

## 🔄 Workflows de Développement

### Créer un nouveau composant

```bash
# 1. Générer la structure
npm run generate:pattern -- --type=element --name="Badge"

# 2. Développer avec hot reload
npm run watch  # http://localhost:6006

# 3. Implémenter les 4 fichiers
# - badge.twig (template)
# - badge.css (styles avec tokens)
# - badge.yml (mock data)
# - badge.stories.jsx (Storybook + argTypes)

# 4. Valider
npm run build  # Lint + format + build

# 5. Commiter
git add source/patterns/elements/badge/
git commit -m "feat(elements): Add Badge component with semantic colors"
```

### Composer un composant complexe

Pour les **Molecules, Organisms, Templates, Pages**, suivre le **Token-First Workflow** :

**4 Steps (STEP 3 préféré)** :

```
1. CHECK NATIVE PARAMS → Utiliser props du parent (variant, size, layout)
2. CHECK UTILITY CLASSES → Classes helper (u-padding-large, u-gap-4)
3. OVERRIDE TOKENS ⭐ → Surcharger tokens parent/enfant (PRÉFÉRÉ)
4. TARGETED CSS → CSS overrides ciblés (dernier recours)
```

**Exemple** : Card Offer Search compose Card

```css
/* card-offer-search.css - STEP 3: Override tokens */
.ps-card-offer-search {
  /* Override Card tokens pour customiser */
  --ps-card-padding-x: var(--size-6);        /* Plus d'espace horizontal */
  --ps-card-padding-y: var(--size-7);        /* Plus d'espace vertical */
  --ps-card-border-color: var(--gray-200);   /* Bordure plus subtile */
  --ps-card-border-radius: var(--radius-3);  /* Coins plus arrondis */
}
```

**Avantages** :
- ✅ Pas de modification du CSS parent (card.css intact)
- ✅ Tokens documentés et maintenables
- ✅ Cascade prévisible et scopée
- ✅ Performance optimale (cascade native CSS)

---

## 🧪 Tests et Qualité

### Audit composant (100 points)

Système d'audit structuré en 8+1 catégories :

```
1. Structure Fichiers (10 pts)
   - 4 fichiers obligatoires présents
   - Nommage cohérent
   
2. BEM (10 pts)
   - Préfixe ps-
   - Modifiers fonctionnent seuls
   
3. CSS Tokens (15 pts)
   - Zero hardcoded values
   - 3-layer architecture
   
4. CSS Nesting (10 pts)
   - Utilisation & syntax
   - Cascade correcte
   
5. Twig (15 pts)
   - JSDoc header
   - Pas de JS methods
   - attributes|without('class')
   
6. YAML (10 pts)
   - $schema déclaré
   - Props documentées
   
7. Storybook (15 pts)
   - tags: ['autodocs']
   - argTypes catégorisées
   
8. Accessibility (15 pts)
   - WCAG 2.2 AA
   - Focus-visible
   - ARIA labels
   
9. Responsive (Bonus 10 pts)
   - 6 breakpoints documentés
```

**Score minimum** : 80/90 (89%)  
**Score recommandé** : 100/100 (100%)

### Validation automatique

```bash
# Lint + format
npm run check

# Build complet
npm run build

# Recherche tokens
npm run tokens:check -- --primary

# Validation icons
npm run icons:validate
```

---

## 🌐 Déploiement

### Build production

```bash
# 1. Build assets
npm run build

# 2. Build Storybook statique
npm run storybook:build

# 3. Outputs
# - storybook/ (documentation statique)
# - css/ (styles compilés)
# - js/ (scripts bundlés)
# - icons/ (sprite SVG)
# - fonts/ (web fonts)
```

### Intégration continue (CI/CD)

```yaml
# .gitlab-ci.yml / .github/workflows/ci.yml
build:
  script:
    - npm ci
    - npm run build          # Validation complète
    - npm run storybook:build # Documentation
  artifacts:
    paths:
      - storybook/
      - css/
      - js/
```

### Déploiement Drupal

```bash
# 1. Sur serveur Drupal
cd web/themes/custom/ps_theme/

# 2. Copier assets compilés
rsync -av css/ web/themes/custom/ps_theme/css/
rsync -av js/ web/themes/custom/ps_theme/js/
rsync -av icons/ web/themes/custom/ps_theme/icons/

# 3. Clear cache Drupal
drush cr
```

---

## 📚 Références

### Documentation interne
- `.github/instructions/` – 6 fichiers consolidés v4.0.0
- `docs/` – Documentation complète (française)
- `.github/prompts/` – 13 prompts AI

### Outils externes
- [Storybook](https://storybook.js.org/) – Documentation interactive
- [Vite](https://vitejs.dev/) – Build tool moderne
- [Drupal](https://www.drupal.org/) – CMS open-source
- [Atomic Design](https://atomicdesign.bradfrost.com/) – Méthodologie Brad Frost

---

**Navigation** : [← Présentation](./README.md) | [Méthodologie →](./methodologie.md)
