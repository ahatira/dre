# Glossaire - PS Theme

**Terminologie française normalisée du projet**

---

## 📖 Table des Matières

1. [Termes Techniques](#termes-techniques)
2. [Atomic Design](#atomic-design)
3. [CSS et Styling](#css-et-styling)
4. [Accessibilité](#accessibilité)
5. [Vocabulaire Métier Immobilier](#vocabulaire-métier-immobilier)
6. [Outils et Technologies](#outils-et-technologies)

---

## 🔧 Termes Techniques

### A

**Atom (Atome)**  
Composant élémentaire indivisible de l'interface (ex: button, input, icon).  
→ `source/patterns/elements/`

**Attributes**  
Objet Drupal permettant d'ajouter des attributs HTML aux éléments Twig.  
→ Usage : `{{ attributes.addClass(classes) }}`

**ArgTypes**  
Configuration Storybook définissant les contrôles interactifs pour chaque prop.  
→ Catégories : Content, Appearance, Behavior, Accessibility, Drupal

**Autodocs**  
Documentation automatique générée par Storybook à partir des argTypes.  
→ Tag obligatoire : `tags: ['autodocs']`

### B

**BEM (Block Element Modifier)**  
Méthodologie de nommage CSS avec structure `.block__element--modifier`.  
→ Préfixe PS Theme : `ps-`

**Breakpoint**  
Point de rupture responsive (ex: 768px = tablet).  
→ 6 breakpoints : mobile-sm, mobile, tablet, laptop, desktop, desktop-large

**Brand Tokens**  
Tokens sémantiques spécifiques à la marque BNP Paribas Real Estate.  
→ Fichier : `source/props/brand.css`

### C

**Composant**  
Élément réutilisable de l'interface avec 4 fichiers (.twig, .css, .yml, .stories.jsx).  
→ 5 niveaux Atomic Design

**Composition**  
Assemblage de composants plus petits pour créer des composants complexes.  
→ Workflow Token-First obligatoire

**Consumer**  
Composant qui utilise/compose un autre composant (ex: Card Offer Search consomme Card).

### D

**Design Token**  
Variable CSS globale définissant une valeur de design réutilisable.  
→ Format : `--token-name` (ex: `--primary`, `--size-4`)

**Drupal Behavior**  
Pattern JavaScript pour Drupal permettant l'initialisation de composants.  
→ Utilise `once()` pour idempotence

### F

**Faker.js**  
Bibliothèque JavaScript générant des données mock réalistes.  
→ Usage : Stories Storybook uniquement

**Focus-visible**  
Pseudo-classe CSS affichant le focus uniquement sur navigation clavier.  
→ Obligatoire WCAG 2.2 Focus Appearance

### H

**Hardcoded Value (Valeur codée en dur)**  
Valeur CSS directe au lieu d'utiliser un token.  
→ ❌ Interdit : `padding: 16px;` ✅ Correct : `padding: var(--size-4);`

### L

**Layer (Couche)**  
Niveau dans l'architecture CSS 3-layer (Global → Component → Context).  
→ Token-First utilise les 3 layers

### M

**Mock Data**  
Données de test réalistes pour développement et Storybook.  
→ Fichiers : `{component}.yml`

**Modifier (Modificateur)**  
Variante d'un composant BEM changeant l'apparence ou comportement.  
→ Format : `ps-{block}--{modifier}` (ex: `ps-button--primary`)

**Molecule (Molécule)**  
Composant composé de plusieurs atomes (ex: card, form-field).  
→ `source/patterns/components/`

### N

**Namespace**  
Préfixe Twig permettant d'importer des composants.  
→ Exemples : `@elements/`, `@components/`, `@collections/`

**Nesting (Imbrication)**  
Syntaxe CSS permettant d'imbriquer des sélecteurs avec `&`.  
→ Obligatoire dans PS Theme (PostCSS)

### O

**Organism (Organisme)**  
Composant complexe composé de molécules et atomes (ex: header, footer).  
→ `source/patterns/collections/`

**Override (Surcharge)**  
Remplacement d'une valeur par défaut par une valeur contextuelle.  
→ Token-First : Override tokens plutôt que CSS direct

### P

**Page**  
Instance complète d'un template avec contenu réel.  
→ `source/patterns/pages/`

**Palette**  
Ensemble de couleurs avec nuances (ex: gray-50 à gray-900).  
→ Fichier : `source/props/colors.css`

**Props**  
Paramètres d'un composant Twig définis dans le YAML schema.  
→ Format : `type`, `enum`, `default`

### R

**Real Estate Context**  
Contexte métier immobilier BNP Paribas dans les données mock.  
→ Exemples : property, agent, office, location

**Responsive**  
Adaptation de l'interface aux différentes tailles d'écran.  
→ Approche Mobile-First obligatoire

### S

**Semantic Token**  
Token avec signification contextuelle plutôt que descriptive.  
→ ✅ `--primary`, `--success` ❌ `--green-600`, `--blue-500`

**Sprite SVG**  
Fichier SVG contenant plusieurs icônes sous forme de symboles.  
→ Fichier : `source/assets/icons/icons-sprite.svg`

**Storybook**  
Outil de documentation interactive des composants.  
→ Dev : http://localhost:6006

### T

**Template**  
Structure de page avec zones de contenu (placeholders).  
→ `source/patterns/layouts/`

**Three-Tier System (Système 3 couches)**  
Architecture CSS avec 3 niveaux de tokens (Global → Component → Context).  
→ Voir Token-First

**Token-First**  
Workflow de composition privilégiant l'override de tokens plutôt que CSS direct.  
→ 4 Steps : Params → Utils → Tokens ⭐ → CSS

**Touch Target**  
Zone cliquable/touchable d'un élément interactif.  
→ Minimum WCAG 2.2 : 44×44px (36×36px acceptable mobile)

### V

**Variant**  
Type de modifier changeant l'apparence visuelle d'un composant.  
→ Exemples : color variants (primary, secondary), size variants (small, large)

### W

**WCAG (Web Content Accessibility Guidelines)**  
Standards d'accessibilité web du W3C.  
→ PS Theme : WCAG 2.2 niveau AA minimum

---

## 🧬 Atomic Design

**Atomic Design**  
Méthodologie de design hiérarchique en 5 niveaux (Atoms → Molecules → Organisms → Templates → Pages).  
→ Créée par Brad Frost (2013)

**Atome (Atom)**  
Niveau 1 : Composants élémentaires indivisibles (button, input, icon, badge).  
→ 19 composants prévus

**Molécule (Molecule)**  
Niveau 2 : Groupes d'atomes fonctionnant ensemble (card, form-field, breadcrumb).  
→ 20 composants prévus

**Organisme (Organism)**  
Niveau 3 : Sections complexes de l'interface (header, footer, property-grid).  
→ 12 composants prévus

**Template**  
Niveau 4 : Structures de page avec placeholders (page-container, two-column).  
→ 8 composants prévus

**Page**  
Niveau 5 : Instances complètes avec contenu réel (home-page, property-search).  
→ 8 composants prévus

---

## 🎨 CSS et Styling

**BEM (Block Element Modifier)**  
Méthodologie de nommage CSS : `.block__element--modifier`.  
→ Préfixe : `ps-` (ex: `ps-button`, `ps-button__icon`, `ps-button--primary`)

**Block (Bloc)**  
Composant racine dans BEM.  
→ Classe : `.ps-{component}` (ex: `.ps-card`)

**Element (Élément)**  
Partie d'un block dans BEM.  
→ Classe : `.ps-{block}__{element}` (ex: `.ps-card__media`)

**Modifier (Modificateur)**  
Variante d'un block ou element dans BEM.  
→ Classe : `.ps-{block}--{modifier}` (ex: `.ps-card--outlined`)

**CSS Nesting (Imbrication CSS)**  
Syntaxe permettant d'imbriquer des sélecteurs avec `&`.  
→ PostCSS (postcss-nested) compile en CSS flat

**Design Token**  
Variable CSS custom property définissant une valeur de design.  
→ Format : `--token-name`, Usage : `var(--token-name)`

**Layer 1 (Couche 1)**  
Tokens globaux primitifs (palette, spacing, typography).  
→ Fichiers : `source/props/*.css`

**Layer 2 (Couche 2)**  
Tokens component-scoped avec préfixe `--ps-{component}-`.  
→ Defaults pour le composant

**Layer 3 (Couche 3)**  
Overrides contextuels (modifiers, consumers, utilities).  
→ Surcharges de Layer 2

**Semantic Token (Token sémantique)**  
Token avec signification contextuelle.  
→ Exemples : `--primary` (vert), `--danger` (rouge), `--success` (teal)

**Palette Token (Token palette)**  
Token de couleur brute avec nuance.  
→ Exemples : `--gray-700`, `--green-600`, `--blue-500`

**PostCSS**  
Outil de transformation CSS avec plugins.  
→ PS Theme : import, nesting, autoprefixer

**Hardcoded Value (Valeur en dur)**  
Valeur CSS directe au lieu de token.  
→ ❌ Interdit : `#00915A`, `16px` ✅ Correct : `var(--primary)`, `var(--size-4)`

---

## ♿ Accessibilité

**A11y (Accessibility)**  
Abréviation numérique de "accessibility" (11 lettres entre A et Y).  
→ Standard : WCAG 2.2 AA

**WCAG (Web Content Accessibility Guidelines)**  
Directives d'accessibilité du W3C.  
→ 4 principes POUR : Perceivable, Operable, Understandable, Robust

**ARIA (Accessible Rich Internet Applications)**  
Attributs HTML améliorant l'accessibilité (aria-label, aria-hidden, aria-expanded, etc.).  
→ Utiliser pour améliorer sémantique native, pas la remplacer

**Focus-visible**  
Indicateur visuel du focus clavier.  
→ Obligatoire WCAG 2.2 Focus Appearance (2px solid outline + offset)

**Touch Target**  
Zone cliquable/touchable d'un élément interactif.  
→ Minimum : 44×44px (WCAG 2.2 Target Size), 36×36px acceptable mobile

**Contrast Ratio (Ratio de contraste)**  
Rapport entre luminosité du texte et du fond.  
→ Minimum WCAG AA : 4.5:1 (texte normal), 3:1 (texte large ou UI)

**Screen Reader (Lecteur d'écran)**  
Logiciel lisant le contenu d'une page pour utilisateurs aveugles.  
→ Exemples : NVDA, JAWS, VoiceOver

**Keyboard Navigation (Navigation clavier)**  
Navigation sans souris (Tab, Enter, Space, Arrows).  
→ Tous les interactifs doivent être accessibles au clavier

**Alternative Text (Texte alternatif)**  
Description textuelle d'une image.  
→ Attribut : `alt="..."` ou `aria-label="..."`

---

## 🏢 Vocabulaire Métier Immobilier

**BNP Paribas Real Estate**  
Société immobilière du groupe BNP Paribas (client du projet).  
→ Marque : Vert #00915A, Rose #A12B66

**Property (Bien immobilier)**  
Actif immobilier à vendre ou à louer.  
→ Types : Office, Retail, Warehouse, Commercial, Residential

**Office (Bureau)**  
Espace de travail commercial.  
→ Exemples : Open space, Private office, Coworking

**Retail (Commerce)**  
Espace commercial de vente.  
→ Exemples : Boutique, Shopping center, Storefront

**Warehouse (Entrepôt)**  
Espace logistique de stockage.  
→ Exemples : Distribution center, Storage facility

**Surface Area (Surface habitable)**  
Superficie d'un bien en m² ou ft².  
→ Unités : m² (Europe), ft² (USA)

**Price (Prix)**  
Valeur du bien immobilier.  
→ Formats : €/month, $/sqft, €/m²/year

**Location (Localisation)**  
Adresse géographique du bien.  
→ Composants : City, Postal code, District, Country

**Agent**  
Conseiller immobilier BNP Paribas.  
→ Informations : Name, Photo, Phone, Email, Specialization

**Listing (Annonce)**  
Publication d'un bien disponible.  
→ Statuts : Available, Reserved, Sold, Under offer

**Exclusivity (Exclusivité)**  
Bien en exclusivité chez BNP Paribas.  
→ Badge : Or (gold), mention "Exclusive"

**Viewed (Déjà vu)**  
Bien déjà consulté par l'utilisateur.  
→ Badge : Gris, icône eye

**Comparator (Comparateur)**  
Outil de comparaison de biens.  
→ Action : Add to compare, icon compare

**Favorite (Favori)**  
Bien sauvegardé par l'utilisateur.  
→ Action : Add to favorites, icon heart

---

## 🛠️ Outils et Technologies

**Drupal**  
CMS (Content Management System) open-source en PHP.  
→ Version : 10/11

**Twig**  
Moteur de templates pour Drupal et Symfony.  
→ Syntaxe : `{{ variable }}`, `{% if %}`, `{% include %}`

**Storybook**  
Outil de documentation interactive de composants UI.  
→ Édition : HTML (pas React/Vue/Angular)

**Vite**  
Build tool moderne ultra-rapide.  
→ Features : HMR (Hot Module Replacement), dev server, optimisation bundle

**PostCSS**  
Outil de transformation CSS avec plugins.  
→ Plugins : import, nested, autoprefixer

**Biome**  
Linter/formatter unifié pour JavaScript, JSON, TypeScript.  
→ Remplace ESLint + Prettier (10-100× plus rapide)

**Faker.js**  
Bibliothèque de génération de données mock réalistes.  
→ Catégories : person, company, location, lorem, image

**NPM (Node Package Manager)**  
Gestionnaire de dépendances JavaScript.  
→ Fichier : `package.json`

**Git**  
Système de contrôle de version.  
→ Conventions : Commits structurés (type/scope), branches feature

**SVGO (SVG Optimizer)**  
Outil d'optimisation SVG.  
→ Optimisations : Remove attributes, minify paths, remove comments

**once()**  
Utilitaire Drupal garantissant l'initialisation unique d'éléments.  
→ Usage : `once('id', '.selector', context).forEach(...)`

---

## 📋 Abréviations Communes

| Abréviation | Signification | Description |
|-------------|---------------|-------------|
| **A11y** | Accessibility | Accessibilité (11 lettres entre A et Y) |
| **BEM** | Block Element Modifier | Méthodologie de nommage CSS |
| **CTA** | Call To Action | Bouton d'action principal |
| **CSS** | Cascading Style Sheets | Langage de styles web |
| **HMR** | Hot Module Replacement | Rechargement automatique en dev |
| **HTML** | HyperText Markup Language | Langage de balisage web |
| **JS** | JavaScript | Langage de programmation web |
| **JSON** | JavaScript Object Notation | Format de données structurées |
| **SDC** | Single Directory Components | Architecture composants Drupal |
| **SVG** | Scalable Vector Graphics | Format graphique vectoriel |
| **UI** | User Interface | Interface utilisateur |
| **UX** | User Experience | Expérience utilisateur |
| **WCAG** | Web Content Accessibility Guidelines | Standards accessibilité W3C |
| **YAML** | YAML Ain't Markup Language | Format de configuration |

---

## 🔗 Conventions de Nommage

### Fichiers et Dossiers

```
kebab-case                  # Fichiers, dossiers, composants
  → card-offer-search.twig
  → form-field.css
  → property-grid/
```

### Classes CSS (BEM)

```
.ps-block                   # Block (préfixe ps-)
.ps-block__element          # Element (double underscore)
.ps-block--modifier         # Modifier (double dash)
.ps-block__element--modifier # Element + modifier
```

### Tokens CSS

```
--token-name                # Global tokens (kebab-case)
  → --primary, --size-4
  
--ps-component-property     # Component tokens (préfixe ps-)
  → --ps-button-padding-x
  → --ps-card-border-radius
```

### JavaScript

```
camelCase                   # Variables, functions
  → handleClick()
  → isDisabled

PascalCase                  # Classes, components
  → Dropdown
  → PropertyCard
  
UPPER_SNAKE_CASE           # Constants
  → API_URL
  → MAX_ITEMS
```

### Namespaces Twig

```
@elements/                  # Atomes
@components/                # Molécules
@collections/               # Organismes
@layouts/                   # Templates
@pages/                     # Pages
```

---

## 📖 Termes à Éviter

| ❌ Éviter | ✅ Utiliser | Raison |
|----------|------------|--------|
| `class` (prop) | `attributes.addClass()` | Drupal convention |
| `baseClass` | ❌ FORBIDDEN | Règle PS Theme stricte |
| `style` (inline) | CSS tokens | Maintenabilité |
| Hardcoded colors | Semantic tokens | Token-First |
| `.js-*` classes | `data-*` attributes | Modern standard |
| `#id` selectors | `.class` selectors | Spécificité + réutilisabilité |
| Nested BEM | Flat BEM (2 levels max) | Lisibilité |
| Arrow functions (Twig) | Ternary operator | Drupal compatibility |

---

## 📚 Ressources Complémentaires

### Documentation Interne
- `.github/instructions/` – 6 fichiers v4.0.0
- `docs/` – Documentation complète française
- `.github/prompts/` – 13 prompts AI

### Références Externes
- [Atomic Design](https://atomicdesign.bradfrost.com/) – Brad Frost
- [BEM Methodology](http://getbem.com/) – Block Element Modifier
- [WCAG 2.2](https://www.w3.org/WAI/WCAG22/quickref/) – Standards accessibilité
- [Drupal Twig](https://www.drupal.org/docs/theming-drupal/twig-in-drupal) – Documentation officielle

---

**Navigation** : [← Méthodologie](./methodologie.md) | [Retour présentation](./README.md)
