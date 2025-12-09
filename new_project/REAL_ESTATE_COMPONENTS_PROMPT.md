# Prompt spécifique : Composants Real Estate BNP Paribas

**Contexte** : Après avoir généré les 47 composants génériques (atoms → pages), créer les composants spécifiques au projet immobilier BNP Paribas Real Estate basés sur les maquettes fournies.

**PRÉREQUIS** : 
- 47 composants génériques créés et validés ✅
- Instructions `.github/instructions/*.md` consultées ✅
- Assets sources préparés dans `source/assets/` ✅

---

## 📋 Instructions et assets requis

### Consulter instructions AVANT création

```bash
# Instructions génériques (déjà créées pour les 47 composants)
cat .github/instructions/components.instructions.md
cat .github/instructions/css.instructions.md
cat .github/instructions/accessibility.instructions.md
cat .github/instructions/atomic-design.instructions.md
cat .github/instructions/workflows.instructions.md
```

**Toutes les règles s'appliquent** : tokens, nesting, BEM, autodocs, focus-visible, WCAG 2.2 AA.

### Assets sources Real Estate spécifiques

**Ajouter dans `source/assets/`** :

```
source/assets/
  icons-source/
    real-estate/          # Icons métier (NEW)
      building.svg
      office.svg
      warehouse.svg
      retail.svg
      land.svg
      map-marker.svg
      map-cluster.svg
      floor-plan.svg
      surface-area.svg
      price-tag.svg
  images/                 # Images projet (NEW)
    property-placeholder.jpg
    consultant-avatar-placeholder.svg
    map-tile-placeholder.png
    hero-real-estate-1.jpg
    hero-real-estate-2.jpg
    hero-real-estate-3.jpg
```

**Build icons Real Estate** :
```bash
npm run build:icons
# Génère mappings pour data-icon="building", data-icon="office", etc.
```

---

## 🏢 Composants spécifiques Real Estate à créer

### ATOMS supplémentaires (3)

**1. `price-tag`** (molecule plutôt)
- Props: `amount` (number), `unit` (€/m²/an, HT/HC/m²/an), `period` (optional)
- Variants: `default`, `map-bubble` (pour affichage sur carte)
- Formatting: espaces milliers, devise, unité
- Example: "20 000 € HT/HC/m²/an"

**2. `surface-display`**
- Props: `value` (number), `unit` (m²)
- Formatting: "611.3 m²"
- Inline ou block display

**3. `location-tag`**
- Props: `address`, `city`, `postalCode`
- Icon: location pin
- Truncate si trop long
- Example: "28010 MADRID"

---

### MOLECULES supplémentaires (8)

**4. `property-badge`**
- Props: `type` (exclusivity|already-viewed|new|featured), `text`
- Couleurs: exclusivity (gold), already-viewed (gray), new (primary), featured (secondary)
- Icon optionnel
- Small size, pill variant

**5. `consultant-mini-card`**
- Props: `avatar`, `name`, `phone`, `onContact`, `onSchedule`
- Layout: avatar (circle) + name + phone + 2 CTAs (Contact / Schedule visit)
- Mobile: stack vertical
- Desktop: horizontal avec photo left

**6. `property-meta`**
- Props: `surface`, `location`, `price`, `type` (rent|sale)
- Layout: inline avec séparateurs (dots)
- Icons optionnels
- Responsive: wrap sur mobile

**7. `filter-dropdown`**
- Props: `label`, `options`, `selected`, `onChange`, `placeholder`
- Types: `select` (simple), `multiselect`, `autocomplete` (location), `range` (surface/price)
- Dropdown custom avec search (pour location)
- Range slider intégré (pour surface/price)

**8. `map-marker`**
- Props: `price`, `type` (default|cluster|selected)
- Variants: bubble avec prix, cluster avec count, marker selected (highlight)
- Colors: primary pour sélectionné, neutral pour défaut
- Cluster: affiche nombre de propriétés

**9. `share-menu`**
- Props: `url`, `title`, `onShare`
- Options: Facebook, Twitter, LinkedIn, Email, Copy link
- Dropdown ou modal selon contexte
- Toast confirmation après copy

**10. `favorite-toggle`**
- Props: `isFavorite`, `onToggle`, `propertyId`
- Icon: heart (outline / filled)
- Animation: scale + color change
- State management: local + API sync

**11. `photo-thumbnail`**
- Props: `src`, `alt`, `isActive`, `onClick`
- Size: 80x60px (lightbox thumbnails)
- Border highlight si active
- Hover effect

---

### ORGANISMS spécifiques (7)

**12. `property-card-advanced`**
- Composition: 
  - Photo carousel (atoms/icon pour navigation)
  - Badges top-left (property-badge)
  - Favorite top-right (favorite-toggle)
  - Share icon top-right
  - Price (price-tag)
  - Title (heading)
  - Location (location-tag)
  - Surface (surface-display)
  - CTA "View the property" (button)
- Layout: vertical card, image 16:9, content padding
- Hover: shadow elevation
- Responsive: full-width mobile, grid desktop

**13. `consultant-card-full`**
- Composition:
  - Avatar large (avatar atom)
  - Name (heading h3)
  - Phone avec icon (link + icon)
  - Contact button (button primary)
  - Schedule visit button (button secondary outline)
- Layout: card avec border, padding, shadow subtle
- Desktop: horizontal layout
- Mobile: vertical stack

**14. `search-hero-overlay`**
- Composition:
  - Background image avec overlay dark (30-50% opacity)
  - Centered search form:
    - Tabs (Property type: Offices / Buy / Rent)
    - Location input (autocomplete)
    - Surface range (filter-dropdown)
    - Price range (filter-dropdown)
    - Submit button "Search"
- Layout: fullscreen hero, form centered avec backdrop white/semi-transparent
- Responsive: form full-width mobile, max-width desktop

**15. `filter-bar-advanced`**
- Composition:
  - Property type dropdown
  - Location autocomplete (avec suggestions)
  - Surface range slider
  - Price range slider
  - "More filters" button (ouvre modal avec filtres additionnels)
  - "Create an alert" button
  - "Hide list" / "Show list" toggle (pour map view)
- Layout: sticky top sur scroll
- Responsive: collapse sur mobile avec drawer

**16. `property-lightbox`**
- Composition:
  - Fullscreen overlay (dark backdrop)
  - Main image (large, centered)
  - Prev/Next arrows (icon buttons)
  - Thumbnails strip (bottom, scrollable)
  - Close button (top-right, icon X)
  - Counter "1/15" (top-left)
- Keyboard: Escape (close), Arrow keys (navigate)
- Touch: swipe left/right
- Lazy load images

**17. `map-view-interactive`**
- Composition:
  - Map container (Leaflet/Google Maps)
  - Custom markers (map-marker avec prix)
  - Clusters (grouping proche markers)
  - Selected marker highlight
  - Popup on click: mini property card
  - Toggle list/map button
  - Zoom controls
- Features:
  - Cluster on zoom out
  - Unclustered markers on zoom in
  - Price bubbles update on viewport change
  - Click marker → show property details
- Responsive: full viewport mobile, sidebar layout desktop

**18. `property-detail-hero`**
- Composition:
  - Photo carousel large (avec thumbnails)
  - Badges overlay (exclusivity, etc.)
  - Favorite + Share buttons (top-right)
  - Price prominent (large, bottom-left overlay)
  - Surface + location (bottom-left overlay)
- Layout: hero section, image 21:9 desktop, 4:3 mobile
- Carousel: autoplay optional, touch swipe

---

### TEMPLATES spécifiques (3)

**19. `listing-with-map-template`**
- Layout:
  - Header (sticky)
  - Filter bar (sticky below header)
  - Split view:
    - Left: property cards grid (scrollable)
    - Right: map view (sticky)
  - Toggle button pour switch list/map sur mobile
- Responsive: 
  - Desktop: 60/40 split (list/map)
  - Tablet: 50/50 ou full-width toggle
  - Mobile: full-width avec bottom sheet map

**20. `property-detail-template`**
- Layout:
  - Header
  - Property detail hero (carousel + info)
  - Breadcrumb
  - Main content (2-col):
    - Left: Description, tabs (equipments, services, etc.), tables
    - Right: consultant card (sticky), schedule visit CTA
  - Map section (location + nearby properties)
  - Related properties carousel
  - Footer
- Responsive: single column mobile, 70/30 desktop

**21. `homepage-real-estate-template`**
- Layout:
  - Header
  - Search hero overlay (fullscreen)
  - Value propositions (4 cards grid)
  - "Best way to approach" section (accordion)
  - Featured properties carousel
  - Expert card (image + text side-by-side)
  - News section (3 cards grid)
  - Footer
- Responsive: stacking mobile, grid desktop

---

### PAGES spécifiques (4)

**22. `listing-page-properties`**
- Uses: `listing-with-map-template`
- Data: 13+ properties from API
- Filters: active state, URL params
- Map: real coordinates, clustering
- Sort: by surface, price, date

**23. `property-detail-page`**
- Uses: `property-detail-template`
- Data: single property full details
- Consultant: real contact info
- Related: 4 similar properties
- Map: exact location + nearby

**24. `homepage`**
- Uses: `homepage-real-estate-template`
- Hero: rotating backgrounds (3-5 images)
- Featured: 8 top properties
- News: latest 3 articles

**25. `search-results-page`**
- Uses: `listing-with-map-template`
- Query: from search hero
- Filters: pre-populated from search
- No results state: suggestions

---

## 🖼️ Assets Real Estate à préparer

### Icons métier (25+ SVG)

**Catégories** :
- **Property types** : `building`, `office`, `warehouse`, `retail`, `land`, `coworking`
- **Map** : `map-marker`, `map-cluster`, `map-pin`, `location`
- **Features** : `surface-area`, `floor-plan`, `parking`, `accessibility`, `security`
- **Actions** : `favorite`, `favorite-filled`, `share`, `phone`, `email`, `calendar`, `download`
- **Filters** : `filter`, `sort`, `search`, `close`

**Source** : `source/assets/icons-source/real-estate/*.svg`

**Optimisation SVGO** :
```bash
npx svgo source/assets/icons-source/real-estate/*.svg --config=svgo.config.mjs
```

### Images propriétés (photos projet)

**Formats requis** :
- **Placeholders** : 1200×800px (ratio 3:2), WebP + JPEG fallback
- **Thumbnails lightbox** : 120×80px (ratio 3:2)
- **Map tiles** : 256×256px, 512×512px (@2x)
- **Hero backgrounds** : 1920×1080px (ratio 16:9), WebP

**Exemple naming** :
```
source/assets/images/properties/
  office-madrid-01.webp
  office-madrid-01.jpg          # Fallback
  office-madrid-01-thumb.webp   # Thumbnail
  office-madrid-01-thumb.jpg
```

### Consultant avatars

**Format** : SVG placeholder générique (pas de photos réelles)
```svg
<!-- source/assets/images/consultant-placeholder.svg -->
<svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
  <circle cx="50" cy="50" r="50" fill="var(--light)"/>
  <circle cx="50" cy="40" r="15" fill="var(--primary)"/>
  <path d="M30 70 Q50 60 70 70" fill="var(--primary)"/>
</svg>
```

### Map assets

**Custom markers** (SVG) :
```svg
<!-- map-marker-primary.svg -->
<svg width="40" height="50" xmlns="http://www.w3.org/2000/svg">
  <path d="M20 0 C9 0 0 9 0 20 C0 35 20 50 20 50 S40 35 40 20 C40 9 31 0 20 0" 
        fill="var(--primary)" stroke="white" stroke-width="2"/>
  <circle cx="20" cy="20" r="8" fill="white"/>
</svg>
```

**Cluster icons** (SVG avec text) :
```svg
<!-- map-cluster.svg -->
<svg width="50" height="50" xmlns="http://www.w3.org/2000/svg">
  <circle cx="25" cy="25" r="24" fill="var(--primary)" stroke="white" stroke-width="2"/>
  <text x="25" y="30" text-anchor="middle" fill="white" font-size="16" font-weight="bold">
    {count}
  </text>
</svg>
```

---

## 🛠️ Technologies spécifiques à intégrer

### Map integration
```bash
npm install leaflet react-leaflet
npm install @types/leaflet --save-dev
```

**Config Vite** (ajouter):
```js
// vite.config.js
optimizeDeps: {
  include: ['leaflet']
}
```

**Leaflet custom marker**:
```js
// components/organisms/map-view-interactive/map-marker.js
const createPriceMarker = (price) => {
  return L.divIcon({
    html: `<div class="ps-map-marker">${price}</div>`,
    className: 'ps-map-marker-wrapper',
    iconSize: [60, 30]
  });
};
```

### Autocomplete location
```bash
npm install @algolia/autocomplete-js
```

### Range sliders
```bash
npm install noUiSlider
```

### Image optimization
```bash
npm install sharp (server-side)
# ou utiliser Drupal image styles
```

---

## 📋 Instructions spécifiques Real Estate

### Données Faker.js pour stories

```js
// .storybook/preview.js helpers
import { faker } from '@faker-js/faker';

export const generateProperty = () => ({
  id: faker.string.uuid(),
  title: `${faker.lorem.words(3)} ${faker.location.city()}`,
  price: faker.number.int({ min: 10000, max: 50000 }),
  priceUnit: '€ HT/HC/m²/an',
  surface: faker.number.float({ min: 50, max: 1000, precision: 0.1 }),
  location: {
    address: faker.location.streetAddress(),
    city: faker.location.city(),
    postalCode: faker.location.zipCode()
  },
  images: Array.from({ length: 5 }, () => faker.image.urlLoremFlickr({ 
    category: 'building',
    width: 800,
    height: 600
  })),
  type: faker.helpers.arrayElement(['rent', 'sale']),
  availability: faker.helpers.arrayElement(['Immediately', '2025-01-15', '2025-03-01']),
  badges: faker.helpers.arrayElements(['Exclusivity', 'Already viewed', 'New'], { min: 0, max: 2 })
});

export const generateConsultant = () => ({
  id: faker.string.uuid(),
  name: faker.person.fullName(),
  phone: faker.phone.number('+33 # ## ## ## ##'),
  avatar: faker.image.avatar(),
  email: faker.internet.email()
});
```

### CSS spécifique (tokens additionnels)

```css
/* source/tokens/real-estate.css */
:root {
  /* Map */
  --map-marker-bg: var(--primary);
  --map-marker-text: white;
  --map-marker-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
  --map-cluster-bg: var(--primary);
  --map-cluster-text: white;
  
  /* Property card */
  --property-card-hover-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
  --property-badge-exclusivity: var(--gold);
  --property-badge-viewed: var(--light);
  
  /* Lightbox */
  --lightbox-overlay: rgba(0, 0, 0, 0.9);
  --lightbox-control-bg: rgba(255, 255, 255, 0.1);
  --lightbox-control-hover: rgba(255, 255, 255, 0.2);
}
```

---

## ⏱️ Plan de création (après les 47 composants génériques)

**H48-H54** (6h): Atoms + Molecules spécifiques
- price-tag, surface-display, location-tag
- property-badge, consultant-mini-card, property-meta
- filter-dropdown (avec range sliders), share-menu, favorite-toggle

**H54-H60** (6h): Organisms spécifiques (partie 1)
- property-card-advanced
- consultant-card-full
- search-hero-overlay
- filter-bar-advanced

**H60-H66** (6h): Organisms spécifiques (partie 2)
- property-lightbox (avec keyboard + touch)
- map-view-interactive (Leaflet integration)
- property-detail-hero

**H66-H72** (6h): Templates + Pages
- listing-with-map-template
- property-detail-template
- homepage-real-estate-template
- 4 pages avec données Faker.js

**H72-H78** (6h): Integration + Polish
- Map markers custom styling
- Autocomplete location (Algolia ou API Drupal)
- Range sliders surface/price
- Lightbox swipe gestures
- Responsive fine-tuning
- Performance (lazy loading images, map tiles)

**H78-H84** (6h): QA + Documentation
- Test tous les composants Real Estate
- A11y validation (lightbox keyboard, map screen readers)
- Documentation Storybook (Real Estate section)
- README avec composants spécifiques

---

## 🎯 Commandes de génération

```bash
# Après avoir généré les 47 composants génériques
npm run components:generate

# Prompts interactifs:
# Type: molecule
# Name: price-tag
# Description: Display formatted price with unit and period
# Category: Real Estate

# Répéter pour chaque composant spécifique (25 au total)
```

---

## ✅ Validation finale

Avant de livrer, vérifier:
- [ ] Tous les 25 composants Real Estate créés avec stories
- [ ] Map integration fonctionnelle (markers, clusters, price bubbles)
- [ ] Lightbox avec navigation keyboard + swipe
- [ ] Autocomplete location avec suggestions
- [ ] Range sliders surface/price (noUiSlider)
- [ ] Favorite + Share systems fonctionnels
- [ ] Responsive complet (mobile, tablet, desktop)
- [ ] A11y validé (contrast, keyboard, screen readers)
- [ ] Performance OK (lazy loading, map tiles caching)
- [ ] Storybook: section "Real Estate" avec tous les composants

---

## 📦 Livrable final

**72 composants totaux** :
- 47 génériques (base UI)
- 25 spécifiques Real Estate

**Structure Storybook** :
```
Atoms/
  Button, Badge, Input, ... (16)
  Price Tag, Surface Display, Location Tag (3)
Molecules/
  Form Field, Card, ... (15)
  Property Badge, Consultant Mini Card, ... (8)
Organisms/
  Header, Footer, ... (8)
  Property Card Advanced, Map View, ... (7)
Templates/
  One Column, ... (4)
  Listing With Map, Property Detail, ... (3)
Pages/
  Home, Listing, ... (4)
  Listing Properties, Property Detail, ... (4)
```

**Prêt pour intégration Drupal** : tous les composants SDC avec props validation, tokens CSS, behaviors Drupal, stories complètes.

Fin du prompt spécifique Real Estate.
