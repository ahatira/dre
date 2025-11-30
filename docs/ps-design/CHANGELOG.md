- 2025-11-30: **breadcrumb** - Composant complet conforme template standard + **PIXEL PERFECT Figma**
  - Props : items (array required - label, url?, icon?), compact (bool), truncate (bool), attributes
  - Variants : standard (défaut), compact (font réduite + gaps réduits), truncate (max-width 16ch)
  - BEM strict : `.ps-breadcrumb`, `.ps-breadcrumb__list`, `.ps-breadcrumb__item`, `.ps-breadcrumb__link`, `.ps-breadcrumb__current`, `.ps-breadcrumb__separator`, `.ps-breadcrumb__item--current`
  - Modifiers indépendants : `--compact`, `--truncate`
  - HTML minimal : classe base seule par défaut, modifiers ajoutés seulement si compact/truncate activés
  - **Icons via @elements/icon** : utilise composant icon.twig (prop name sans préfixe "icon-")
  - **PIXEL PERFECT Figma** : font-size 16px (--font-size-1), line-height 24px (--leading-6), gap 4px (--size-1), couleur #333333 (--text-default), underline sur liens uniquement, gap icon-text 8px (--size-2)
  - Tokens utilisés : --font-sans, --font-size-1 (16px), --font-size-0 (14px compact), --leading-6 (24px), --leading-5 (20px compact), --text-default (#333333), --brand-primary (hover), --gray-400, --blue-500, --font-weight-400, --size-1 (4px gap items), --size-2 (8px gap icon), --border-size-2, --radius-1
  - Aucun nouveau token créé : tous les tokens existants étaient suffisants
  - Stories Storybook : 7 stories (Default, WithIcons, Compact, Truncated, Simple, Deep, ShowcaseVariants)
  - Fichiers : `.twig`, `.css`, `.yml`, `.stories.jsx`, `README.md`
  - Accessibilité : `<nav aria-label="Breadcrumb">`, `aria-current="page"` sur dernier item, séparateur `aria-hidden="true"`, focus-visible outline, couleurs WCAG AA
  - Navigation sémantique : `<ol>` ordered list, dernier item non-cliquable (span), liens avec underline standard
  - CSS nesting moderne : structure &__element, &--modifier, transitions fluides
  - SEO : compatible structured data (JSON-LD BreadcrumbList), améliore crawlabilité
  - Use cases : navigation immobilier (home → location → property), blog (category path), e-commerce (home → category → product), documentation
  - Build : validé (npm run build) - aucune erreur
  - **Audit conformité** : 100% - Tokens uniquement, BEM strict, HTML minimal, modifiers indépendants, documentation anglaise complète, description concise ≤ 2 lignes, **PIXEL PERFECT selon maquette Figma analysée**
- 2025-11-29: Progress Bar tokens added
  - Added `--ps-color-info-600`, `--ps-color-warning-600`, `--ps-color-neutral-500`, `--ps-color-neutral-700` in `source/props/colors.css` to support semantic colors for Progress Bar variants.
  - Added `--ps-transition-duration-normal` and normalized `--ps-transition-duration-fast` under `:where(html)` in `source/props/animations.css` for consistent transitions.
  - Added `--progress-striped-gradient` in `source/props/theme.css` to provide a reusable striped background for indeterminate/striped states.
  - Justification: Ensure Progress Bar uses project tokens exclusively (no hardcoded values) and supports all specified semantic variants and states.
 - Ajout tokens avatar : --size-20 (80px), --ps-color-primary-600, --ps-color-neutral-0, --ps-color-neutral-100, --ps-color-neutral-200, --ps-color-neutral-400, --ps-color-neutral-600, --ps-color-success-600, --ps-color-error-600, --ps-border-radius-full, --ps-border-radius-sm, --ps-border-width-default, --ps-transition-duration-fast (pixel perfect avatar)
 - Ajout tokens shadow pour focus des champs : --shadow-focus-primary (blue focus ring), --shadow-focus-error (red error ring), --shadow-focus-success (green success ring)
 - Ajout tokens link pour tous les variants et états interactifs : --ps-link-green, --ps-link-green-hover, --ps-link-green-active, --ps-link-green-visited, --ps-link-green-disabled, --ps-link-purple (+ hover/active/visited/disabled), --ps-link-white (+ hover/active/visited/disabled), --ps-link-default (+ hover/active/visited/disabled)
 - ✅ **link** - Composant complet conforme template standard - 2025-11-29
   - Props : text (required), url (required), color (green/purple/white/default), underline (bool défaut true), icon, target (_self/_blank), rel, disabled
   - Variants : green (défaut), purple, white, default (blue)
   - Modifiers : no-underline, with-icon, external, disabled
   - États interactifs : hover, active, visited, focus-visible, disabled (tous gérés par variant)
   - BEM strict : `.ps-link`, `.ps-link__text`, `.ps-link__icon`
   - Modifiers indépendants : `--purple`, `--white`, `--default`, `--no-underline`, `--with-icon`, `--external`, `--disabled`
   - HTML minimal : classe base seule par défaut (green, underline=true), modifiers ajoutés seulement si différents
   - **Icons via CSS** : gestion complète via pseudo-élément `::before`, font `bnpre-icons`, mapping via `data-icon` attribute
   - **Underline par défaut** : style dans base class, modifier inverse `--no-underline` pour le retirer
   - Tokens créés : 20 tokens link (4 variants × 5 états chacun) dans colors.css
   - Tokens utilisés : --ps-link-*, --size-2, --size-4, --size-5, --font-sans, --font-weight-400, --leading-normal, --border-size-1, --border-size-2, --radius-1, --blue-500
   - Stories Storybook : 11 stories (Default, Green, Purple, White, DefaultBlue, WithIcon, External, WithoutUnderline, Disabled, AllColorVariants, UseCases)
   - Fichiers : `.twig`, `.css`, `.yml`, `.stories.jsx`, `README.md`
   - Accessibilité : <a> sémantique par défaut, <span> pour disabled, aria-disabled="true", aria-hidden sur icônes, rel="noopener noreferrer" auto pour _blank, focus outline visible (WCAG AA)
   - Support external links : target="_blank" + rel sécurisé automatique, modifier --external optionnel
   - Transitions fluides : color + text-decoration (150ms cubic-bezier)
   - Build : validé (npm run build)
   - **Audit conformité** : 100% - Icons en CSS pur, HTML minimal optimisé, YAML documenté
 - ✅ **image** - Composant complet conforme template standard - 2025-11-29
   - Props : src (required), alt (required), width, height, srcset (array), sizes, loading, decoding, fit, rounded, ratio
   - Object-fit : cover (défaut), contain
   - Border radius : none (défaut), sm (4px), md (6px), lg (12px), full (circle)
   - Aspect ratios : none (défaut), 16x9, 1x1, 4x3 (via padding technique)
   - BEM strict : `.ps-image`, `.ps-image__img`, `.ps-image__ratio`
   - Modifiers indépendants : `--fit-contain`, `--rounded-sm`, `--rounded-md`, `--rounded-lg`, `--rounded-full`, `--ratio-16x9`, `--ratio-1x1`, `--ratio-4x3`
   - HTML minimal : classe base seule par défaut (fit=cover, rounded=none, ratio=none), modifiers ajoutés seulement si différents
   - Tokens utilisés : --ps-color-neutral-100 (fallback --gray-50), --radius-2, --radius-3, --radius-5, --radius-round
   - Stories Storybook : 11 stories (Default, WithRatio16x9, WithRatio1x1, WithRatio4x3, RoundedSmall, RoundedMedium, RoundedLarge, RoundedFull, FitContain, AllRatios, AllRounded, ObjectFit, WithSrcset, UseCases)
   - Fichiers : `.twig`, `.css`, `.yml`, `.stories.jsx`, `README.md`
   - Accessibilité : alt obligatoire, width/height pour éviter CLS, loading="lazy" par défaut, decoding="auto", aria-hidden sur ratio helper
   - Performance : lazy loading natif, srcset/sizes pour responsive, dimensions explicites (CLS prevention), ratio fixe pour layouts stables
   - Semantic HTML : utilise `<figure>` pour structure sémantique
   - Use cases : hero banners (16:9), card thumbnails (4:3), avatars (1:1 + rounded-full), gallery thumbnails (1:1), logos (contain fit)
   - Build : validé (npm run build) - aucune erreur
 - ✅ **flag** - Composant complet conforme template standard - 2025-11-29
   - Props : code (ISO 3166-1 alpha-2), locale (BCP 47), label, src, size, shape, disabled, decorative
   - Tailles : sm (16px), md (20px défaut), lg (24px)
   - Formes : square (défaut), rounded (4px), circle (full round)
   - État : disabled (opacity 0.5 + grayscale 0.2)
   - BEM strict : `.ps-flag`, `.ps-flag__img`
   - Modifiers indépendants : `--sm`, `--lg`, `--rounded`, `--circle`, `--disabled`
   - HTML minimal : classe base seule par défaut (md + square), modifiers ajoutés seulement si différents
   - Tokens utilisés : --size-4 (16px), --size-5 (20px), --size-6 (24px), --radius-2 (4px), --radius-round (full circle)
   - Stories Storybook : 10 stories (Default, France, UnitedKingdom, Germany, Spain, Italy, Netherlands, AllCountries, Sizes, Shapes, DisabledState, LocaleMapping, AllVariantsCombined, UseCases)
   - Fichiers : `.twig`, `.css`, `.yml`, `.stories.jsx`, `README.md`
   - Normalisation locale : supporte code direct (FR, GB) ET locale BCP 47 (fr-FR, en-GB) avec extraction automatique du code pays
   - Accessibilité : label obligatoire (sauf mode decorative), alt/title sur images, aria-hidden si decorative, dimensions explicites (width/height)
   - Build : validé (npm run build, npm run storybook:build) - aucune erreur
 - ✅ **field** - Composant complet conforme template standard - 2025-11-29
   - Types : text (défaut), number, email, search, select/dropdown, textarea
   - États : default, hover, focus, filled, error, disabled, done/success
   - Icône : support via CSS pseudo-élément (bnpre-icons), position left/right
   - BEM strict : `.ps-field`, `.ps-field__input`, `.ps-field__icon`, `.ps-field__error`
   - Modifiers indépendants : `--text`, `--number`, `--email`, `--search`, `--select`, `--textarea`, `--error`, `--disabled`, `--filled`, `--done`, `--icon-left`, `--icon-right`
   - HTML minimal : classe base seule par défaut, modifiers ajoutés seulement si différents
   - Tokens créés : --ps-color-border-default (#D6DBDE), --ps-color-border-hover, --ps-color-border-focus (#0288D1), --ps-color-border-error (#EB3636), --ps-color-border-success, --ps-color-field-bg, --ps-color-field-text, --ps-color-field-placeholder, --ps-color-field-disabled-bg, --ps-color-field-disabled-text
   - Tokens utilisés : --size-2, --size-3, --size-4, --size-5, --size-10, --size-20, --size-305, --border-size-2, --radius-2, --font-sans, --font-weight-400, --leading-normal
   - Stories Storybook : 13 stories (Default, Text, Number, Email, Search, Select, Textarea, WithIconLeft, WithIconRight, Filled, Error, Disabled, AllTypes, AllStates, IconVariations, UseCases)
   - Fichiers : `.twig`, `.css`, `.yml`, `.stories.jsx`, `README.md`
   - Accessibilité : aria-invalid, aria-describedby, aria-disabled, role="combobox" pour select, role="alert" pour erreurs, aria-hidden sur icônes décoratives
   - Support : input types (text, number, email, search), textarea (resize vertical), select (styled combobox), placeholder natifs
 - ✅ **eyebrow** - Composant complet conforme template standard - 2025-11-29
   - Variants : primary, secondary, accent, neutral (couleurs sémantiques tokens)
   - Tailles : small (12px), medium (14px défaut)
   - Styles : uppercase (défaut), bold
   - Décorations : withLine (ligne horizontale), withDot (point décoratif)
   - Icône : support via CSS pseudo-élément (font bnpre-icons)
   - BEM strict : `.ps-eyebrow`, `.ps-eyebrow__icon`, `.ps-eyebrow__text`, `.ps-eyebrow__line`, `.ps-eyebrow__dot`
   - Modifiers indépendants : `--primary`, `--secondary`, `--accent`, `--small`, `--uppercase`, `--bold`, `--with-line`, `--with-dot`
   - HTML minimal : classe base seule par défaut, modifiers ajoutés seulement si différents
   - Tokens utilisés : --ps-color-primary-600, --ps-color-neutral-600, --ps-color-neutral-500, --blue-600, --font-sans, --font-size-xs, --font-size-sm, --font-weight-500, --font-weight-600, --tracking-wide, --tracking-wider, --size-2, --size-3, --size-05, --size-8, --size-10
   - Stories Storybook : 10 stories (Default, Primary, Secondary, Accent, Neutral, WithLine, WithDot, SmallSize, AllVariants, UseCases)
   - Fichiers : `.twig`, `.css`, `.yml`, `.stories.jsx`, `README.md`
   - Accessibilité : décorations aria-hidden, contraste WCAG AA, ordre DOM correct
   - Build : validé (npm run build, npm run storybook:build)
 - ♻️ **heading** - Refactor conformité + ajout couleurs/poids - 2025-11-29
   - HTML minimal: base `.ps-heading` = h1 align left (sans modifiers)
   - Niveaux indépendants: `--h2 --h3 --h4 --h5 --h6` (h1 implicite)
   - Couleurs sémantiques: `--primary --secondary --success --warning --danger --info` (tokens brand / btn)
   - Poids indépendants: `--light --regular --bold --extra` (fallback tokens font-weight-300..800)
   - Icônes via CSS: `.ps-heading__icon` (bnpre-icons) aria-hidden décoratif
   - Tokens fallbacks: `--ps-heading-h*-size|line-height` → `--font-size-*`, `--leading-*`; base couleur `--ps-color-text` → `--gray-900`
   - Twig: classes conditionnelles (niveau, align, couleur, weight, icon, visuallyHidden)
   - YAML: nouveaux props `color`, `weight` documentés
   - Stories: ajout ColorVariants, WeightVariants, AllVariants
   - README: mis à jour (defaults h1, nouvelles modifiers, minimal markup)
# PS Design System - CHANGELOG

Toutes les modifications notables du système de design seront documentées dans ce fichier.

Format basé sur [Keep a Changelog](https://keepachangelog.com/fr/1.0.0/).

---

## [1.0.0] - 2025-11-28

### ✅ Implémenté

#### Elements (Atoms)
- **button** - Composant complet avec 10+ stories
  - Variants : primary/secondary × green/purple/white
  - Tailles : small (34px), medium (36px), large (40px)
  - États : default, hover, focus, active, disabled, loading
  - Icônes : left/right/only avec SVG inline
  - Support `<a>` et `<button>` selon présence de `url`
  - Fichiers : `.twig`, `.css`, `.yml`, `.stories.jsx`, `.mdx`
  
- **icon** - ✨ **NOUVEAU** - Système d'icônes fonts complet avec 89 icônes
  - **Fonts** : bnpre-icons (75 icônes) + bnpre-icons-poi (14 icônes POI)
  - **Classes** : `.icon-*` et `.icon-poi-*` (depuis `source/props/icons.css`)
  - **Modifiers** : `--small` (16px), `--medium` (20px), `--large` (24px), `--xlarge` (32px)
  - **États** : normal, disabled (opacity 50%)
  - **Couleurs** : Hérite de `color` ou custom via prop `color`
  - **Stories** : Gallery complète des 89 icônes avec filtres (regular/POI)
  - **Fichiers** : `.twig`, `.css`, `.yml`, `.stories.jsx`, `README.md`
  - **Fonts téléchargées** : `source/assets/fonts/icons/` + `source/assets/fonts/icons-poi/`
  - **Build** : Fonts copiées automatiquement dans `dist/fonts/` et `storybook/assets/`
  
- **badge** - Composant avec BEM `ps-badge`
  - Variants : small/medium/large
  - Formes : rounded/square/pill
  - Tokens CSS utilisés correctement
  - Fichiers : `.css`, `.twig`, `.yml`, `.stories.jsx`
  
- **label** - Implémentation minimale
  - À enrichir avec variants et states

#### Components (Molecules)
- **alert** - Implémentation partielle
  - Structure de base présente
  - À compléter : variants (info/success/warning/error), dismissible

- **breadcrumb** - Implémentation partielle
  - Structure de base présente
  - À compléter : truncation, responsive, ARIA

### 🔧 Infrastructure & Workflow

- ✅ **Icon font system refactorée** (28 nov 2025)
  - ❌ Supprimé : Script `icons:build` + dépendance `icon-font-generator`
  - ❌ Supprimé : Génération automatique de fonts depuis SVG
  - ✅ Ajouté : Fonts téléchargées depuis bnppre.fr et versionnées
  - ✅ Ajouté : Script `extract-icons.mjs` pour parser `icons.css`
  - ✅ Ajouté : `icons-list.json` avec liste complète des 89 icônes
  - ✅ Mis à jour : `source/props/icons.css` avec URLs locales
  - ✅ Nettoyé : Dossier `source/assets/fonts/PsIcon/` supprimé
  - ⚠️ **IMPORTANT** : Les classes `.icon-*` dans `icons.css` ne doivent **JAMAIS** être modifiées

### 📋 Documentation Créée

- ✅ `docs/ps-design/README.md` - Documentation principale du système
- ✅ `docs/ps-design/INDEX.md` - Inventaire complet avec progression
- ✅ `docs/ps-design/COMPONENT_TEMPLATE.md` - Template standard à suivre
- ✅ `docs/ps-design/CHANGELOG.md` - Ce fichier

### 🎨 Design Tokens

- ✅ `source/props/*.css` - Tokens CSS organisés par catégorie
  - `colors.css` - Couleurs système (gray, red, green, blue, etc.)
  - `brand.css` - Couleurs de marque BNP Paribas
  - `fonts.css` - Typographie (BNPP Sans, Open Sans, sizes, weights, line heights)
  - `sizes.css` - Système de tailles et spacing
  - `borders.css`, `shadows.css`, `animations.css`, `easing.css`, `zindex.css`

### 📚 Référence

- ✅ `docs/design/` - Spécifications complètes des 87 composants à implémenter
  - 19 atoms, 20 molecules, 12 organisms, 8 templates, 8 pages
  - Documentation détaillée avec BEM, props, variants, tokens, a11y
  - 7 fichiers YAML de tokens de référence

### 🔧 Workflow

- ✅ Storybook configuré et fonctionnel
- ✅ Vite build + watch configurés
- ✅ npm scripts : `build`, `watch`, `storybook:dev`, `storybook:build`

---

## ⏳ À Venir (Roadmap)

### Phase 1 : FONDATIONS (Priorité Critique) - Q1 2026

#### Elements (8 composants)
- [ ] `icon` - Bibliothèque SVG complète (2000+ icônes)
- [ ] `heading` - Titres h1-h6 avec presets typographiques
- [ ] `text` - Paragraphes et textes avec variants
- [ ] `link` - Liens avec états et couleurs
- [ ] `field` - Champs input/textarea avec validation
- [ ] `checkbox` - Cases à cocher accessibles
- [ ] `radio` - Boutons radio accessibles
- [ ] `image` - Images responsive avec lazy loading

#### Components (5 composants)
- [ ] `card` - **PRIORITÉ #1** - Carte de contenu (47 occurrences Figma)
- [ ] `dropdown` - Select/menu déroulant (262 occurrences)
- [ ] `form-field` - Champ avec label/helper/error
- [ ] `pagination` - Navigation listings
- [ ] `search-bar` - Barre de recherche avec suggestions

**Estimation Phase 1** : 44 heures (13 composants)

---

### Phase 2 : NAVIGATION & STRUCTURE (Priorité Haute) - Q1 2026

#### Collections (4 composants)
- [ ] `header` - **CRITIQUE** - En-tête site (43 occurrences)
- [ ] `footer` - **CRITIQUE** - Pied de page (23 occurrences)
- [ ] `main-menu` - Menu principal avec sous-menus
- [ ] `hero` - Section hero avec media/content

#### Layouts (4 composants)
- [ ] `page-container` - **CRITIQUE** - Container principal
- [ ] `block` - Bloc générique de section
- [ ] `two-column` - Layout 2 colonnes responsive
- [ ] `grid-layout` - Layout grille adaptative
**Estimation Phase 2** : 44 heures (8 composants)

---

### Phase 3 : FEATURES MÉTIER (Priorité Haute) - Q2 2026

#### Collections (4 composants)
- [ ] `card-grid` - Grille de cartes responsive
- [ ] `filter-panel` - Panneau de filtres avancés (6 occurrences)
- [ ] `map-view` - Vue carte interactive (198 occurrences)

#### Components (4 composants)
- [ ] `menu-item` - Item de menu avec submenu (139 occurrences)
- [ ] `modal` - Fenêtre modale accessible
- [ ] `tooltip` - Infobulles contextuel
- [ ] `tabs` - Onglets avec panels


29/11/2025 - Ajout des tokens pour le composant Label :
  - --ps-color-text, --ps-color-text-muted (colors.css)
  - --ps-font-family-primary, --ps-font-size-sm, --ps-font-weight-medium, --ps-font-weight-bold (fonts.css)
  - --ps-spacing-1, --ps-spacing-2 (sizes.css)
29/11/2025 - ✅ **accordion** - Composant conforme template standard
 - Fichiers : `.twig`, `.css`, `.yml`, `.stories.jsx`, `README.md`
 - Props : items[] (id,title,content,open,icon), singleOpen (bool, défaut true), bordered (bool, défaut false), flush (bool, défaut false), headingLevel (h2-h5, défaut h3)
 - Modifiers : `--bordered`, `--flush`, `__item--open` (état)
 - HTML minimal : base `.ps-accordion` sans modifiers pour l'état par défaut (non-borderé, non-flush)
 - Icône : `<span class="ps-accordion__icon">` + pseudo-élément (font bnpre-icons) avec swap glyph plus/minus sur état ouvert
 - Tokens ajoutés : `--ps-spacing-3`, `--ps-spacing-4`, `--ps-spacing-5`, `--ps-icon-size-16` (sizes.css) ; `--ps-border-width-default`, `--ps-border-width-focus`, `--ps-border-radius-sm` (borders.css)
 - Tokens utilisés : typographie (`--ps-font-family-primary`, `--font-size-1`), espace (`--ps-spacing-2..5`), bordures (`--ps-border-width-default`, `--ps-color-border-focus`, `--gray-300`, `--ps-border-width-focus`, `--ps-border-radius-sm`), icône (`--ps-icon-size-16`)
 - Accessibilité : aria-expanded + hidden, panels role="region" aria-labelledby, navigation clavier Enter/Espace, outline focus tokenisé
 - Stories : Default, Bordered, Flush, MultipleOpen, HeadingLevelH4, AllVariants
 - Conformité : Aucun hardcode (remplacement de var(--size-*) par var(--ps-spacing-*)), défaut bordered inversé pour respecter règle HTML minimal
 - Justification tokens : Spacing 3/4/5 et alias border width/radius nécessaires pour harmoniser API design et éviter fallback valeurs; icon size normalisée
---

### Phase 4 : PAGES COMPLÈTES (Priorité Haute) - Q2 2026
#### Pages (4 composants)
- [ ] `home-page` - **CRITIQUE** - Page d'accueil (8 occurrences)
- [ ] `property-search` - **CRITIQUE** - Recherche propriétés
- [ ] `property-detail` - **CRITIQUE** - Détail propriété
- [ ] `user-account` - Compte utilisateur

**Estimation Phase 4** : 40 heures (4 pages)

---

### Phase 5 : ENRICHISSEMENT UX (Priorité Moyenne) - Q2-Q3 2026
- [ ] `eyebrow` - Surtitre/kicker
- [ ] `flag` - Drapeaux de langues
- [ ] `avatar` - Avatars utilisateurs
- [ ] `progress-bar` - Barres de progression
- [ ] `skip-link` - Lien d'évitement (a11y)

#### Components (6 composants)
- [ ] `accordion` - Accordéon pliable
- [ ] `stepper` - Indicateur d'étapes
- [ ] `table` - Tableaux de données
- [ ] `toast` - Notifications temporaires
- [ ] `language-selector` - Sélecteur de langue

#### Collections (4 composants)
- [ ] `feature-section` - Section de features
- [ ] `article-list` - Liste d'articles
- [ ] `pre-footer` - Section avant footer

**Estimation Phase 5** : 60 heures (18 composants)

---

### Phase 6 : CONTENU & MEDIA (Priorité Moyenne) - Q3 2026

#### Components (7 composants)
- [ ] `callout` - Bloc d'appel à l'action
- [ ] `date-badge` - Badge de date
- [ ] `featured-card` - Carte mise en avant
- [ ] `quote` - Citations
- [ ] `video` - Lecteur vidéo
- [ ] `carousel` - Carrousel d'images
- [ ] `skeleton` - Placeholders de chargement

#### Layouts (4 composants)
- [ ] `content-sidebar` - Layout contenu + sidebar
- [ ] `full-width` - Layout pleine largeur
- [ ] `hero-layout` - Template de hero
- [ ] `article-layout` - Template d'article

#### Pages (4 composants)
- [ ] `contact` - Page de contact
- [ ] `about` - Page à propos
- [ ] `blog-listing` - Liste d'articles de blog
- [ ] `blog-article` - Article de blog

**Estimation Phase 6** : 61 heures (15 composants)

---

## 📊 Statistiques Globales

| Statut | Composants | Pourcentage |
|--------|------------|-------------|
| ✅ Implémentés | 5 | 6% |
| ⏳ À implémenter | 82 | 94% |
| **Total** | **87** | **100%** |

**Temps estimé total** : 297 heures  
**Temps déjà investi** : ~23 heures (5 composants)  
**Temps restant** : ~274 heures

---

## 🎯 Objectifs par Trimestre

### Q1 2026 (Janv-Mars)
- ✅ Phase 1 complète (13 composants fondamentaux)
- ✅ Phase 2 complète (8 composants navigation)
- **Total Q1** : 21 composants (24% du design system)

### Q2 2026 (Avril-Juin)
- ✅ Phase 3 complète (8 composants features métier)
- ✅ Phase 4 complète (4 pages critiques)
- **Total Q2** : +12 composants (33 total = 38%)

### Q3 2026 (Juil-Sept)
- ✅ Phase 5 complète (18 composants enrichissement)
- ✅ Phase 6 complète (15 composants contenu)
- **Total Q3** : +33 composants (66 total = 76%)

### Q4 2026 (Oct-Déc)
- ✅ Composants restants (21 composants)
- ✅ Tests, optimisations, documentation
- **Total Q4** : 87 composants = **100%**

---

## 📝 Format des Entrées

### Exemple d'entrée pour nouveau composant :

```markdown
### [Date] - Ajout de {Component Name}

- **Fichiers** : `.twig`, `.css`, `.yml`, `.stories.jsx`, `.mdx`
- **Variants** : Liste des variants implémentés
- **Props** : Liste des propriétés disponibles
- **États** : default, hover, focus, disabled, etc.
- **Accessibilité** : Conformité WCAG 2.2 AA
- **Tokens utilisés** : Liste des tokens CSS
- **Stories Storybook** : Nombre de stories créées
- **Tests** : Navigateurs/devices testés
```

---

## 🔗 Références

- **Documentation design** : `docs/design/`
- **Template composant** : `docs/ps-design/COMPONENT_TEMPLATE.md`
- **Index progression** : `docs/ps-design/INDEX.md`
- **Exemple référence** : `source/patterns/elements/button/`
- **Design tokens** : `source/props/*.css` (colors, fonts, brand, sizes, etc.)

---

**Version** : 1.0.0  
**Dernière mise à jour** : 28 novembre 2025  
**Prochain sprint** : Phase 1 (icon, heading, text, link, field, checkbox, radio, image, card)
