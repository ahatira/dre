# PS Design System - Index des Composants

**Version** : 1.0.0  
**Dernière mise à jour** : 30 novembre 2025  
**Statut** : 🟡 En développement

---

## 📊 Vue d'ensemble

**Composants implémentés** : 6 / 87 (7%)  
**Composants à implémenter** : 81  
**Design tokens** : ✅ Disponibles dans `source/props/*.css` (colors, fonts, brand, sizes, etc.)  
**Documentation de référence** : `docs/design/` (spécifications complètes)

---

## 🗂️ Structure du Projet

```
source/patterns/
├── elements/          # Atoms (19 composants)
│   ├── button/       ✅ IMPLÉMENTÉ
│   ├── badge/        ✅ IMPLÉMENTÉ  
│   ├── avatar/       ⏳ À implémenter
│   ├── checkbox/     ⏳ À implémenter
│   ├── divider/      ⏳ À implémenter
│   ├── eyebrow/      ⏳ À implémenter
│   ├── field/        ⏳ À implémenter
│   ├── flag/         ⏳ À implémenter
│   ├── heading/      ⏳ À implémenter
│   ├── icon/         ⏳ À implémenter
│   ├── image/        ✅ IMPLÉMENTÉ
│   ├── label/        ✅ IMPLÉMENTÉ (minimal)
│   ├── link/         ⏳ À implémenter
│   ├── progress-bar/ ⏳ À implémenter
│   ├── radio/        ⏳ À implémenter
│   ├── skip-link/    ⏳ À implémenter
│   ├── spinner/      ⏳ À implémenter
│   ├── text/         ⏳ À implémenter
│   └── toggle/       ⏳ À implémenter
│
├── components/        # Molecules (20 composants)
│   ├── alert/        ✅ IMPLÉMENTÉ
│   ├── breadcrumb/   ✅ IMPLÉMENTÉ
│   ├── callout/      ⏳ À implémenter
│   ├── card/         ✅ IMPLÉMENTÉ (générique; compose `offer-card`)
│   ├── date-badge/   ⏳ À implémenter
│   ├── dropdown/     ⏳ À implémenter
│   ├── featured-card/⏳ À implémenter
│   ├── quote/        ⏳ À implémenter
│   ├── accordion/    ⏳ À implémenter
│   ├── form-field/   ⏳ À implémenter
│   ├── language-selector/ ⏳ À implémenter
│   ├── menu-item/    ⏳ À implémenter
│   ├── modal/        ⏳ À implémenter
│   ├── pagination/   ⏳ À implémenter
│   ├── search-bar/   ⏳ À implémenter
│   ├── stepper/      ⏳ À implémenter
│   ├── table/        ⏳ À implémenter
│   ├── tabs/         ⏳ À implémenter
│   ├── tag-list/     ⏳ À implémenter
│   ├── toast/        ⏳ À implémenter
│   ├── tooltip/      ⏳ À implémenter
│   ├── video/        ⏳ À implémenter
│   ├── carousel/     ⏳ À implémenter
│   └── skeleton/     ⏳ À implémenter
│
├── collections/       # Organisms (12 composants)
│   ├── tag-list/     ⏳ À implémenter (existe mais incomplet)
│   ├── article-list/ ⏳ À implémenter
│   ├── calculator/   ⏳ À implémenter
│   ├── card-grid/    ⏳ À implémenter
│   ├── feature-section/ ⏳ À implémenter
│   ├── filter-panel/ ⏳ À implémenter
│   ├── footer/       ⏳ À implémenter
│   ├── header/       ⏳ À implémenter
│   ├── hero/         ⏳ À implémenter
│   ├── main-menu/    ⏳ À implémenter
│   ├── map-view/     ⏳ À implémenter
│   ├── pre-footer/   ⏳ À implémenter
│   └── search-form/  ⏳ À implémenter
│
├── layouts/           # Templates (8 composants)
│   ├── block/        ⏳ À implémenter (existe mais incomplet)
│   ├── article-layout/ ⏳ À implémenter
│   ├── content-sidebar/ ⏳ À implémenter
│   ├── full-width/   ⏳ À implémenter
│   ├── grid-layout/  ⏳ À implémenter
│   ├── hero-layout/  ⏳ À implémenter
│   ├── page-container/ ⏳ À implémenter
│   └── two-column/   ⏳ À implémenter
│
└── pages/             # Pages (8 composants)
    ├── about/        ⏳ À implémenter
    ├── blog-article/ ⏳ À implémenter
    ├── blog-listing/ ⏳ À implémenter
    ├── contact/      ⏳ À implémenter
    ├── home-page/    ⏳ À implémenter
    ├── property-detail/ ⏳ À implémenter
    ├── property-search/ ⏳ À implémenter
    └── user-account/ ⏳ À implémenter
```

---

## 📈 Progression par Catégorie

| Catégorie | Implémentés | Total | Pourcentage |
|-----------|-------------|-------|-------------|
| **Elements** (Atoms) | 3 | 19 | 🟡 16% |
| **Components** (Molecules) | 3 | 20 | 🟠 15% |
| **Collections** (Organisms) | 0 | 12 | 🔴 0% |
| **Layouts** (Templates) | 0 | 8 | 🔴 0% |
| **Pages** | 0 | 8 | 🔴 0% |
| **Design Tokens** | ✅ | ✅ | ✅ 100% |
| **TOTAL** | **6** | **87** | **🟡 7%** |

---

## 🎯 Plan d'Implémentation

### Phase 1 : FONDATIONS (Priorité Critique)

**Objectif** : Composants de base essentiels pour tout le système.

#### Elements (Atoms)
1. ⏳ `icon` - Bibliothèque d'icônes SVG (2000+ occurrences Figma)
2. ⏳ `heading` - Titres h1-h6 avec presets
3. ⏳ `text` - Paragraphes et textes
4. ⏳ `link` - Liens texte
5. ⏳ `field` - Champs de formulaire (147 occurrences)
6. ⏳ `checkbox` - Cases à cocher
7. ⏳ `radio` - Boutons radio
8. ⏳ `image` - Images responsive

**Estimation** : 8 composants × 3h = **24h**

#### Components (Molecules)
1. ⏳ `card` - Carte de contenu (47 occurrences) - **PRIORITÉ #1**
2. ⏳ `dropdown` - Menu déroulant (262 occurrences)
3. ⏳ `form-field` - Champ avec label/error (147 occurrences)
4. ⏳ `pagination` - Navigation listings
5. ⏳ `search-bar` - Barre de recherche (60 occurrences)

**Estimation** : 5 composants × 4h = **20h**

**Total Phase 1** : **44 heures** (13 composants)

---

### Phase 2 : NAVIGATION & STRUCTURE (Priorité Haute)

#### Collections (Organisms)
1. ⏳ `header` - En-tête site (43 occurrences) - **CRITIQUE**
2. ⏳ `footer` - Pied de page (23 occurrences) - **CRITIQUE**
3. ⏳ `main-menu` - Menu principal
4. ⏳ `hero` - Section hero

**Estimation** : 4 composants × 6h = **24h**

#### Layouts (Templates)
1. ⏳ `page-container` - Container principal - **CRITIQUE**
2. ⏳ `block` - Bloc générique
3. ⏳ `two-column` - Layout 2 colonnes
4. ⏳ `grid-layout` - Layout grille

**Estimation** : 4 composants × 5h = **20h**

**Total Phase 2** : **44 heures** (8 composants)

---

### Phase 3 : FEATURES MÉTIER (Priorité Haute)

#### Collections (Organisms)
1. ⏳ `search-form` - Formulaire de recherche
2. ⏳ `card-grid` - Grille de cartes
3. ⏳ `filter-panel` - Panneau de filtres (6 occurrences)
4. ⏳ `map-view` - Vue carte (198 occurrences)

**Estimation** : 4 composants × 8h = **32h**

#### Components (Molecules)
1. ⏳ `menu-item` - Item de menu (139 occurrences)
2. ⏳ `modal` - Fenêtre modale
3. ⏳ `tooltip` - Infobulles
4. ⏳ `tabs` - Onglets

**Estimation** : 4 composants × 4h = **16h**

**Total Phase 3** : **48 heures** (8 composants)

---

### Phase 4 : PAGES COMPLÈTES (Priorité Haute)

#### Pages
1. ⏳ `home-page` - Page d'accueil (8 occurrences) - **CRITIQUE**
2. ⏳ `property-search` - Recherche propriétés - **CRITIQUE**
3. ⏳ `property-detail` - Détail propriété - **CRITIQUE**
4. ⏳ `user-account` - Compte utilisateur

**Estimation** : 4 pages × 10h = **40h**

**Total Phase 4** : **40 heures** (4 pages)

---

### Phase 5 : ENRICHISSEMENT UX (Priorité Moyenne)

#### Elements (Atoms)
- ⏳ `toggle`, `spinner`, `eyebrow`, `flag`, `avatar`, `divider`, `progress-bar`, `skip-link`

**Estimation** : 8 composants × 2h = **16h**

#### Components (Molecules)
- ⏳ `accordion`, `stepper`, `table`, `toast`, `tag-list`, `language-selector`

**Estimation** : 6 composants × 4h = **24h**

#### Collections (Organisms)
- ⏳ `feature-section`, `calculator`, `article-list`, `pre-footer`

**Estimation** : 4 composants × 5h = **20h**

**Total Phase 5** : **60 heures** (18 composants)

---

### Phase 6 : CONTENU & MEDIA (Priorité Moyenne)

#### Components (Molecules)
- ⏳ `callout`, `date-badge`, `featured-card`, `quote`, `video`, `carousel`, `skeleton`

**Estimation** : 7 composants × 3h = **21h**

#### Layouts (Templates)
- ⏳ `content-sidebar`, `full-width`, `hero-layout`, `article-layout`

**Estimation** : 4 composants × 4h = **16h**

#### Pages
- ⏳ `contact`, `about`, `blog-listing`, `blog-article`

**Estimation** : 4 pages × 6h = **24h**

**Total Phase 6** : **61 heures** (15 composants)

---

## ⏱️ Estimation Totale

| Phase | Composants | Temps estimé | Priorité |
|-------|------------|--------------|----------|
| Phase 1 (Fondations) | 13 | 44h | 🔴 Critique |
| Phase 2 (Navigation) | 8 | 44h | 🔴 Critique |
| Phase 3 (Features) | 8 | 48h | 🟠 Haute |
| Phase 4 (Pages) | 4 | 40h | 🟠 Haute |
| Phase 5 (Enrichissement) | 18 | 60h | 🟡 Moyenne |
| Phase 6 (Contenu) | 15 | 61h | 🟡 Moyenne |
| **TOTAL** | **66** | **297h** | |

**Temps moyen par composant** : 4.5h  
**Composants déjà faits** : 5 (22.5h économisées)  
**Temps restant estimé** : **274.5 heures**

---

## 📚 Références

### Documentation
- **Template de composant** : `docs/ps-design/COMPONENT_TEMPLATE.md`
- **Spécifications design** : `docs/design/` (87 fichiers `.md` complets)
- **Exemple de référence** : `source/patterns/elements/button/`
- **Design tokens** : `source/props/ps-tokens.css`

### Workflow
1. Lire spec dans `docs/design/{level}/{component}.md`
2. Créer structure dans `source/patterns/{level}/{component}/`
3. Suivre template `COMPONENT_TEMPLATE.md`
4. Implémenter les 5 fichiers (`.twig`, `.css`, `.yml`, `.stories.jsx`, `.mdx`)
5. Tester dans Storybook (`npm run watch`)
6. Valider accessibilité (WCAG 2.2 AA)
7. Mettre à jour `CHANGELOG.md`

### Commandes
```bash
# Développement avec watch + Storybook
npm run watch

# Build production
npm run build

# Storybook static
npm run storybook:build
```

---

## 🎨 Design Tokens Disponibles

Tous les tokens sont disponibles dans `source/props/ps-tokens.css` :

### Couleurs
- `--ps-color-primary` (#00915A - Vert BNP)
- `--ps-color-purple` (#BA3075)
- `--ps-color-gray-*` (50 à 900)
- `--ps-color-success`, `--ps-color-error`, `--ps-color-warning`, `--ps-color-info`

### Typographie
- `--ps-font-family-primary` (BNPP Sans)
- `--ps-font-family-secondary` (Open Sans)
- `--ps-font-size-xs` à `--ps-font-size-4xl`
- `--ps-font-weight-light` (300) à `--ps-font-weight-extrabold` (800)

### Spacing, Layout, Shadows, Transitions
- Voir `source/props/ps-tokens.css` pour la liste complète

---

## ✅ Composants Implémentés

### Elements
1. ✅ **button** - `source/patterns/elements/button/`
   - 10+ stories, 5 fichiers complets
   - Variants : primary/secondary × green/purple/white
   - Tailles : small/medium/large
   - États : disabled, loading, full-width
   - Icônes : left/right/only

2. ✅ **badge** - `source/patterns/elements/badge/`
   - BEM avec préfixe `ps-badge`
   - Variants : small/medium/large, rounded/square/pill
   - Tokens CSS utilisés

3. ✅ **label** - `source/patterns/elements/label/`
   - Implémentation minimale (à enrichir)

### Components
1. ✅ **alert** - `source/patterns/components/alert/`
   - Implémentation partielle (à compléter)

2. ✅ **breadcrumb** - `source/patterns/components/breadcrumb/`
   - Implémentation partielle (à compléter)

3. ✅ **card** - `source/patterns/components/card/`
   - Conteneur générique composable (blocs Twig image/content/header/body/footer)
   - Modifiers indépendants (layout horizontal, imagePosition, radius none|sm|md|lg)
   - Compose des cartes spécialisées comme `offer-card`

---

## 🔄 Prochaines Étapes Immédiates

### Sprint 1 (Phase 1 - Semaine 1-2)
1. ⏳ Implémenter `icon` (critique pour tous les autres composants)
2. ⏳ Implémenter `heading`, `text`, `link`
3. ⏳ Implémenter `field`, `checkbox`, `radio`
4. ⏳ Implémenter `card` (composant le plus utilisé)

### Sprint 2 (Phase 2 - Semaine 3-4)
1. ⏳ Implémenter `header` et `footer`
2. ⏳ Implémenter `page-container`
3. ⏳ Implémenter `main-menu`
4. ⏳ Implémenter première page complète (`home-page`)

---

**Statut actuel** : 🟡 7% complété (6/87 composants)  
**Objectif Q1 2026** : 🎯 Phase 1-2 complètes (25% - 21 composants)  
**Objectif Q2 2026** : 🎯 Phase 1-4 complètes (70% - 33 composants)

---

**Version** : 1.0.0  
**Dernière mise à jour** : 30 novembre 2025
