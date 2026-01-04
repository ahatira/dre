# Offer Full - Architecture Modulaire

## Vue d'ensemble

Le layout **Offer Full** utilise une architecture modulaire avec des sections sémantiques indépendantes, facilitant la maintenance et l'évolution du composant.

## Structure HTML

```
<article>
  ├── <div class="container">
  │   └── <div class="offer-layout">
  │       ├── <div class="offer-layout__main">
  │       │   ├── <section class="offer-hero">           // Galerie photos
  │       │   ├── <section class="offer-meta">           // Header + Actions
  │       │   │   ├── <div class="offer-meta__header">   // Titre, badges, prix
  │       │   │   └── <div class="offer-meta__actions">  // Boutons CTA
  │       │   ├── <section class="offer-description">    // Texte descriptif
  │       │   ├── <section class="offer-features">       // 4 sections caractéristiques
  │       │   ├── <section class="offer-energy">         // DPE/GES (placeholder)
  │       │   └── <section class="offer-surface-table">  // Tableau surfaces
  │       └── <aside class="offer-layout__sidebar">      // Carte consultant (sticky)
  └── <section class="offer-map">                        // Map full-width (hors container)
      └── <div class="offer-map__container">
          ├── Address
          ├── Map widget (placeholder)
          ├── POI filters (placeholder)
          └── Travel time calculator (placeholder)
```

## Sections détaillées

### 1. Offer Hero (`.offer-hero`)
- **Contenu** : Carrousel de photos de la propriété
- **Composant** : Carousel (source/patterns/components/carousel/)
- **Position** : Première section dans main

### 2. Offer Meta (`.offer-meta`)
- **Contenu** : Informations clés + actions
- **Sous-sections** :
  - `offer-meta__header` : Titre, badges, référence, surface, localisation, prix, disponibilité, mandat
  - `offer-meta__actions` : Liste de boutons CTA (tableau surfaces, brochure, visite)
- **Responsive** : Actions en colonne mobile → ligne desktop

### 3. Offer Description (`.offer-description`)
- **Contenu** : Texte descriptif avec toggle "Voir plus"
- **Composant** : Read More (source/patterns/components/read-more/)
- **Comportement** : Texte tronqué avec expansion

### 4. Offer Features (`.offer-features`)
- **Contenu** : 4 sections de caractéristiques
  - Équipements (offer-features__section)
  - Services (offer-features__section)
  - État du bâtiment (offer-features__section)
  - Informations complémentaires (offer-features__section)
- **Composant** : Feature Section (source/patterns/components/feature-section/)
- **Structure** : Icône + titre + liste de texte

### 5. Offer Energy (`.offer-energy`)
- **Contenu** : Widgets DPE et GES (placeholder)
- **État** : À implémenter (actuellement placeholder bordure pointillée)
- **Données** : DPE et GES scores

### 6. Offer Surface Table (`.offer-surface-table`)
- **Contenu** : Tableau des surfaces (lots, étages)
- **Composant** : Table (source/patterns/elements/table/)
- **Données** : Headers + rows avec cellules string

### 7. Offer Map (`.offer-map`)
- **Position** : Full-width HORS du .container
- **Background** : Gris clair (`--gray-50`)
- **Sous-sections** :
  - `offer-map__address` : Adresse de la propriété
  - `offer-map__placeholder` : Carte interactive (placeholder)
  - `offer-map__poi` : Filtres points d'intérêt (placeholder)
  - `offer-map__travel-time` : Calculateur temps de trajet (placeholder)
- **Responsive** : Padding adaptatif selon breakpoint

### 8. Sidebar (`.offer-layout__sidebar`)
- **Contenu** : Carte consultant
- **Composant** : Card Agent (source/patterns/components/card-agent/)
- **Comportement** : Sticky sur desktop (`position: sticky; top: var(--size-6)`)
- **Responsive** : Pleine largeur mobile → 1/3 desktop

## Layout Responsive

### Mobile (< 768px)
- **Layout** : 1 colonne
- **Order** : Hero → Meta → Description → Features → Energy → Surface Table → Consultant → Map
- **Actions** : Boutons empilés verticalement
- **Padding** : `--size-5` → `--size-6` (≥640px)

### Tablet/Desktop (≥ 768px)
- **Layout** : Grid 2 colonnes
  - Main : `2fr` (66%)
  - Sidebar : `1fr` (33%)
  - Gap : `var(--size-10)`
- **Actions** : Boutons en ligne horizontale
- **Consultant** : Sticky en sidebar
- **Padding** : `--size-8` → `--size-12` (≥1280px)

### Desktop Large (≥ 1440px)
- **Container** : Max-width 1440px centré
- **Padding** : `--size-14`

## Classes CSS principales

### Container (réutilisable Drupal)
```css
.container {
  max-width: 1440px;
  margin: 0 auto;
  padding: 0 var(--container-padding-x);
}
```

### Offer Layout (Grid)
```css
.offer-layout {
  display: flex;              /* Mobile : column */
  flex-direction: column;
  gap: var(--offer-layout-gap);
  
  /* Desktop (≥768px) */
  display: grid;
  grid-template-columns: 2fr 1fr;
}
```

### Sections
- Toutes utilisent `display: flex; flex-direction: column; gap: var(--size-*)`
- Titres de section : `font-size: var(--font-size-4)`, `font-weight: 700`, `color: var(--gray-900)`
- Placeholders : Bordure pointillée grise (`border: 2px dashed var(--gray-300)`)

## Tokens Design System

### Spacing
- `--size-2` à `--size-14` : Tous les paddings, gaps, margins
- Container padding : `--size-5` (mobile) → `--size-14` (desktop large)
- Section gap : `--size-6` (mobile) → `--size-8` (laptop)

### Colors
- Titres : `--gray-900`
- Texte secondaire : `--gray-600`, `--gray-700`, `--gray-800`
- Prix : `--primary` (vert BNP)
- Backgrounds : `--gray-50`, `--white`
- Bordures : `--gray-200`, `--gray-300`

### Typography
- Titre principal : `--font-size-7` (mobile) → `--font-size-8` (desktop)
- Titres de section : `--font-size-4`
- Texte body : `--font-size-1`
- Weights : `--font-weight-700`, `--font-weight-600`

### Autres
- Border-radius : `--radius-2`
- Line-height : `--line-height-1`, `--line-height-3`

## Avantages de l'architecture

1. **Modularité** : Chaque section est autonome et peut être déplacée/supprimée facilement
2. **Sémantique** : Noms de classes explicites (`offer-hero` vs `ps-offer-full__gallery`)
3. **Maintenance** : Modification d'une section n'impacte pas les autres
4. **Drupal compatible** : Structure `<article>` + `.container` standard
5. **Extensibilité** : Ajout facile de nouvelles sections
6. **Performance** : CSS minimal grâce aux tokens et nesting
7. **Accessibilité** : Sections avec `aria-label` appropriés

## Skeleton Mode

Toutes les sections supportent le mode skeleton (`skeleton: true`) :
- Remplace le contenu par des placeholders animés
- Utilise le composant Skeleton (source/patterns/elements/skeleton/)
- Variantes : `button`, `rectangle`, `text`, `card`
- Permet le chargement Ajax fluide

## Prochaines évolutions

1. **Energy Section** : Implémenter widgets DPE/GES interactifs
2. **Map Widget** : Carte interactive Leaflet/Google Maps
3. **POI Filters** : Checkboxes filtrables pour points d'intérêt
4. **Travel Time** : Calculateur avec autocomplete origine/destination
5. **Gallery Lightbox** : Ajout lightbox pour agrandir les photos
6. **Share Buttons** : Boutons partage réseaux sociaux
7. **Print Stylesheet** : Optimisation pour impression PDF

## Fichiers

- **Twig** : `offer-full.twig` (371 lignes)
- **CSS** : `offer-full.css` (380 lignes)
- **YAML** : `offer-full.yml` (mock data Madrid office)
- **Stories** : `offer-full.stories.jsx` (3 stories : Default, Skeleton, Minimal)
- **Doc** : `ARCHITECTURE.md` (ce fichier)
