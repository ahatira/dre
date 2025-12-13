# Page Résultats de recherche (Search Results) - Vue d'ensemble

Page principale affichant les résultats de recherche immobilière avec filtres, carte interactive, et outils de comparaison.

---

## Vue d'ensemble

**Objectif** : Présenter les biens correspondant aux critères de recherche avec des outils avancés de tri, filtrage, et comparaison.

**URL type** : `/search?location=paris&type=apartment&rooms=2`

**Breakpoints** :
- **Desktop (≥768px)** : Layout 3 colonnes (filtres 25% + résultats 40% + carte 35%)
- **Mobile (<768px)** : Layout vertical avec actions bar sticky et modales plein écran

---

## Composants principaux

### 1. Navigation & Actions mobile
**Fichier** : `mobile-navigation.md`

**Desktop** : Breadcrumb + header actions (save, share, alert)  
**Mobile** : 
- Top bar fixe : Back + Titre + Menu actions
- Actions bar sticky : Sort + Filters + Map toggle
- Compteur résultats

### 2. Filtres & Tri
**Fichier** : `filters-sort.md`

**Desktop** : Sidebar gauche avec filtres permanents + dropdown tri  
**Mobile** : 
- Bouton "Filters" ouvre panel plein écran
- Bouton "Sort" ouvre modale tri
- Badges compteurs filtres actifs

**Filtres disponibles** :
- Prix (slider range)
- Surface (min/max avec presets)
- Pièces (checkboxes)
- Type bien (apartment, house, studio, loft)
- Caractéristiques (meublé, balcon, parking, etc.)
- Disponibilité (dates)

**Options tri** :
- Prix croissant/décroissant
- Date publication (plus récent)
- Surface croissant/décroissant
- Distance (si localisation activée)

### 3. Liste & Cartes résultats
**Fichier** : `results-cards.md`

**Desktop** : Cards horizontales avec image 40% + contenu 60%  
**Mobile** : Cards verticales (image top + contenu)

**Contenu carte** :
- Image carrousel (multiple photos)
- Badges (Viewed, Exclusive, New)
- Prix + label "/mois"
- Titre + localisation
- Caractéristiques clés (surface, pièces, étage)
- Actions : favoris (heart), compare (checkbox), CTA "View details"

**Pagination** : Infinite scroll (lazy load) ou pagination numérique

### 4. Carte interactive
**Fichier** : `map-distance.md`

**Desktop** : Carte fixe sticky à droite (50% largeur)  
**Mobile** : Carte plein écran (overlay) ouverte via "Show map"

**Fonctionnalités** :
- Pins cliquables avec label prix
- Zoom/Pan controls
- Widget zone distance : personnalise rayon recherche (walking, transports, bike, car)
- Modale configuration : modes transport + minutes + adresse origine

### 5. Comparateur de biens
**Fichier** : `comparator.md`

**Desktop** : Drawer latéral droit (800px, 3 colonnes)  
**Mobile** : Drawer plein écran (layout vertical)

**Fonctionnalités** :
- Ajouter jusqu'à 3 biens via checkbox "Add to compare"
- Tableau comparatif caractéristiques (surface, pièces, étage, dispo, type, meublé)
- Actions : Remove bien, Reset all, Compare (validation)
- État vide : message "No properties to compare yet"

### 6. Bannière calculateur loyer
**Fichier** : `calculator-banner.md`

**Desktop** : Bannière horizontale (titre + 3 champs inline + CTA)  
**Mobile** : Bannière verticale (champs empilés)

**Fonctionnalités** :
- Champs : localisation (autocomplete), surface (number), type bien (select)
- CTA "Calculate" : estime loyer selon critères
- Résultat : modal avec fourchette prix + disclaimer + CTA expert

---

## Architecture responsive

### Desktop (≥768px)
```
┌───────────────────────────────────────────────────────────┐
│ Header (Breadcrumb + Actions)                             │
├──────────┬────────────────────────────┬───────────────────┤
│ Filtres  │ Résultats (scroll)         │ Carte (sticky)    │
│ (sidebar)│ - Compteur + Tri           │ - Pins            │
│ - Prix   │ - Cards horizontales       │ - Zone distance   │
│ - Surface│ - Pagination/infinite      │ - Controls        │
│ - Pièces │                            │                   │
│ - Type   │                            │                   │
│ - Caract │                            │                   │
│ - Reset  │                            │                   │
├──────────┴────────────────────────────┴───────────────────┤
│ Bannière calculateur (optionnel)                          │
└───────────────────────────────────────────────────────────┘
│ Comparateur (drawer slide in si actif)                    │
```

### Mobile (<768px)
```
┌─────────────────────────────┐
│ Top Bar (sticky)            │
│ [←] Search results    [⋮]   │
├─────────────────────────────┤
│ Actions Bar (sticky)        │
│ 127 properties found        │
│ [Sort] [Filters] [Show map] │
├─────────────────────────────┤
│ Résultats (scroll)          │
│ - Cards verticales          │
│ - Infinite scroll           │
│                             │
├─────────────────────────────┤
│ Bannière calculateur        │
└─────────────────────────────┘
│ Modales :                   │
│ - Filters (plein écran)     │
│ - Sort (plein écran)        │
│ - Map (overlay plein écran) │
│ - Comparator (drawer)       │
```

---

## Flux utilisateur

### Recherche initiale
1. Utilisateur arrive depuis homepage ou navigation
2. Page charge avec filtres par défaut (query params)
3. Affiche résultats + carte avec pins
4. Desktop : filtres sidebar + carte sticky
5. Mobile : liste seule, actions bar pour ouvrir filtres/carte

### Affiner recherche
**Desktop** :
1. Ajuste filtres sidebar → résultats update en temps réel (Ajax)
2. Change tri dropdown → résultats réorganisés
3. Carte update pins selon filtres

**Mobile** :
1. Tap "Filters" → modale plein écran
2. Ajuste critères → voir nb résultats en temps réel
3. "Validate" → applique filtres, ferme modale, update liste
4. Tap "Sort" → modale tri → sélection → update ordre

### Visualiser carte
**Desktop** : Carte toujours visible à droite, scroll liste indépendant  
**Mobile** :
1. Tap "Show map" → carte plein écran overlay
2. Pins cliquables → bottom sheet résumé bien
3. Bouton "Close" → retour liste

### Zone distance
**Desktop/Mobile** :
1. Click "Customize area" (bouton sur carte)
2. Modale configuration : mode transport + minutes + adresse
3. "Validate" → dessine zone sur carte, filtre résultats dans zone
4. Badge compteur update

### Comparer biens
1. Coche "Add to compare" sur cartes (max 3)
2. Badge compteur header update "(2)"
3. Click compteur → ouvre comparateur drawer
4. Drawer : 3 colonnes desktop / empilé mobile
5. "Compare" → validation (redirect page comparative ou export)

### Calculer loyer
1. Rempli champs bannière calculateur (location, surface, type)
2. "Calculate" → modal résultat avec fourchette prix
3. "Contact expert" → formulaire contact

---

## Données & API

### Endpoints principaux
```
GET  /api/search/results?location={city}&type={type}&rooms={rooms}&page={n}
POST /api/search/filter (body: filtres complets)
GET  /api/map/markers?bounds={lat1,lng1,lat2,lng2}
POST /api/map/isochrone (body: mode, minutes, origin)
GET  /api/compare?ids={id1,id2,id3}
POST /api/calculator/estimate (body: location, surface, type)
POST /api/searches/save (body: critères recherche)
POST /api/alerts/create (body: critères alerte)
```

### State management
- **Filtres actifs** : objet filters (synchro sidebar ↔ URL params ↔ API)
- **Tri** : string sortBy ('price_asc', 'date_desc', etc.)
- **Vue** : 'list' | 'map' (mobile toggle)
- **Comparaison** : array propertyIds (max 3)
- **Pagination** : page courante, hasMore, total

---

## Accessibilité

### Navigation clavier
- Filtres : Tab entre champs, Espace/Entrée sélection
- Résultats : Tab entre cartes, Entrée ouvre fiche
- Carte : Pins tabulables, Entrée ouvre popup
- Comparateur : Focus trap dans drawer, ESC ferme

### Screen readers
- Compteur résultats : `aria-live="polite"` (annonce changements)
- Filtres actifs : badges avec `aria-label` explicite
- Carte : fallback textuel si charge échouée
- Modales : `role="dialog"`, `aria-modal="true"`

### Focus management
- Modale ouverte : focus piégé, ESC ferme, focus retour trigger
- Infinite scroll : focus maintenu sur dernier item après load
- Drawer comparateur : focus premier élément à ouverture

---

## Performance

### Optimisations
- **Lazy load images** : cards résultats (intersection observer)
- **Infinite scroll** : charge 20 résultats par batch
- **Debounce filtres** : 500ms avant requête API
- **Markers clustering** : > 50 pins sur carte
- **Cache** : isochrones calculés (session storage)
- **Preload** : tiles carte proches viewport

### Métriques cibles
- First Load : < 2s
- Filter update : < 500ms
- Map render : < 1s
- Infinite scroll : < 300ms par batch

---

## Tests & validation

### Scenarios clés
1. **Recherche vide** : message "No properties found" + suggestions
2. **1 résultat** : layout adapté (pas de pagination)
3. **> 100 résultats** : infinite scroll performant
4. **Filtres combinés** : logique AND/OR correcte
5. **Zone distance** : calcul isochrone précis
6. **Comparateur max** : toast "Maximum 3 properties"
7. **Mobile landscape** : layout adapté (orientation)

### Breakpoints testés
- Desktop : 1920px, 1440px, 1024px, 768px
- Mobile : 375px (iPhone SE), 414px (iPhone Pro), 360px (Android)
- Tablet : 768px portrait/landscape

---

## Notes d'implémentation

### Composants réutilisés
- `Badge` (atom) : Viewed, Exclusive, New, compteurs
- `Button` (atom) : CTAs, actions, filtres
- `Checkbox` (atom) : filtres multi-sélection, compare
- `Input` (atom) : champs filtres (prix, surface)
- `Select` (atom) : dropdowns (tri, type bien)
- `Card` (molecule) : cartes résultats
- `Modal` (organism) : filtres, tri, distance, calculateur

### Modules Drupal custom
- **Search module** : logique recherche + filtres
- **Map Integration** : API carte + isochrones
- **Comparator** : gestion état comparaison
- **Calculator** : estimation loyers
- **Alerts** : création alertes email

### State & interactions
- React Context ou Zustand pour état global
- Axios ou Fetch pour API calls
- LocalStorage : préférences utilisateur (vue map/list)
- SessionStorage : cache temporaire (isochrones)

---

## Références

**Fichiers specs détaillés** :
- `mobile-navigation.md` - Top bar + actions bar mobile
- `filters-sort.md` - Filtres sidebar + modale tri
- `results-cards.md` - Cards résultats + pagination
- `map-distance.md` - Carte interactive + zone distance
- `comparator.md` - Drawer comparaison 3 biens
- `calculator-banner.md` - Bannière calculateur loyer

**Composants atoms/molecules** :
- Voir `docs/atomic/elements.md` et `docs/atomic/components.md`

**Maquettes** :
- Desktop : `docs/maquettes/search-results-desktop.png`
- Mobile : `docs/maquettes/search-results-mobile.png`
