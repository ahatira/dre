 - Ajout tokens avatar : --size-20 (80px), --ps-color-primary-600, --ps-color-neutral-0, --ps-color-neutral-100, --ps-color-neutral-200, --ps-color-neutral-400, --ps-color-neutral-600, --ps-color-success-600, --ps-color-error-600, --ps-border-radius-full, --ps-border-radius-sm, --ps-border-width-default, --ps-transition-duration-fast (pixel perfect avatar)
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
- [ ] `search-form` - Formulaire de recherche propriétés
- [ ] `card-grid` - Grille de cartes responsive
- [ ] `filter-panel` - Panneau de filtres avancés (6 occurrences)
- [ ] `map-view` - Vue carte interactive (198 occurrences)

#### Components (4 composants)
- [ ] `menu-item` - Item de menu avec submenu (139 occurrences)
- [ ] `modal` - Fenêtre modale accessible
- [ ] `tooltip` - Infobulles contextuel
- [ ] `tabs` - Onglets avec panels

**Estimation Phase 3** : 48 heures (8 composants)

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

#### Elements (8 composants)
- [ ] `toggle` - Switch on/off
- [ ] `spinner` - Indicateurs de chargement
- [ ] `eyebrow` - Surtitre/kicker
- [ ] `flag` - Drapeaux de langues
- [ ] `avatar` - Avatars utilisateurs
- [ ] `divider` - Séparateurs visuels
- [ ] `progress-bar` - Barres de progression
- [ ] `skip-link` - Lien d'évitement (a11y)

#### Components (6 composants)
- [ ] `accordion` - Accordéon pliable
- [ ] `stepper` - Indicateur d'étapes
- [ ] `table` - Tableaux de données
- [ ] `toast` - Notifications temporaires
- [ ] `tag-list` - Liste de tags/badges
- [ ] `language-selector` - Sélecteur de langue

#### Collections (4 composants)
- [ ] `feature-section` - Section de features
- [ ] `calculator` - Calculateur (prêt, mensualités)
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
