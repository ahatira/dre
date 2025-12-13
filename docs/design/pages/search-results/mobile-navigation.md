# Navigation mobile & Actions (Responsive)

Barre de navigation supérieure mobile et actions contextuelles pour la page de recherche.

---

## Modèle de contenu

```yaml
mobileTopBar:
  # Barre supérieure fixe (mobile uniquement)
  backButton:
    href: '/'                      # ou history.back()
    label: 'Back'
    icon: 'arrow-left'
  
  title: 'Search results'          # ou dynamique selon filtre
  
  actionsMenu:                     # Menu 3 points (optionnel)
    items:
      - label: 'Save search'
        icon: 'bookmark'
        action: 'saveSearch'
      - label: 'Share results'
        icon: 'share'
        action: 'shareResults'
      - label: 'Alert me'
        icon: 'bell'
        action: 'createAlert'

actionsBar:
  # Barre actions contextuelles (mobile, au-dessus résultats)
  sortButton:
    label: 'Sort'                  # ouvre modale tri
    icon: 'sort'
    badge: '3'                     # nb critères actifs (optionnel)
  
  filtersButton:
    label: 'Filters'               # ouvre modale filtres
    icon: 'filter'
    badge: '5'                     # nb filtres actifs
  
  mapToggle:
    label: 'Show map'              # toggle carte/liste
    icon: 'map'
    state: 'list'                  # list | map

resultsCount:
  total: 127
  label: '{count} properties found'
```

---

## UX par breakpoint

### Desktop (≥768px)
**Pas de top bar mobile** :
- Navigation breadcrumb classique
- Actions intégrées dans header page (Save search, Share, Alert)
- Filtres et tri dans sidebar gauche

### Mobile (<768px)

#### Top Bar (fixe, sticky)
**Structure** :
```
┌─────────────────────────────┐
│ [←] Search results    [⋮]   │
└─────────────────────────────┘
```

**Éléments** :
- **Bouton Back** (gauche) : icône flèche, retour page précédente
- **Titre** (centre) : "Search results" ou contexte (ex: "Paris 15e")
- **Menu actions** (droite) : icône 3 points verticaux, ouvre menu déroulant

**Comportement** :
- Fixe en haut viewport (z-index élevé)
- Visible au scroll up, masqué au scroll down (optionnel)
- Ombre légère quand contenu scroll sous la barre

#### Actions Bar (sous top bar)
**Structure** :
```
┌──────────────────────────────────────────┐
│ 127 properties found                     │
│ [Sort (3)] [Filters (5)] [Show map]      │
└──────────────────────────────────────────┘
```

**Éléments** :
- **Compteur résultats** : ligne 1, texte left
- **Boutons actions** : ligne 2, 3 boutons horizontaux
  - Sort : badge compteur si tri appliqué
  - Filters : badge compteur nb filtres actifs (toujours visible)
  - Show map : toggle icône map/list

**Comportement** :
- Fixe sous top bar (sticky position)
- Visible au scroll (reste accessible)
- Boutons déclenchent modales plein écran (Sort, Filters) ou toggle map

---

## Top Bar détails

### Bouton Back
**Visuel** :
- Icône : `arrow-left`, taille `--size-6`
- Touch target : 48x48px minimum
- Couleur : `--text-primary`, hover `--primary`

**Action** :
- Click → `history.back()` ou navigation vers `/` (fallback)

### Titre
**Visuel** :
- Font : `--font-heading-sm`
- Couleur : `--text-primary`
- Centré ou aligné gauche (après icône back)
- Truncate si long : ellipsis (max 200px)

**Variantes** :
- Statique : "Search results"
- Dynamique : "Paris 15e · 2 rooms" (contexte filtres principaux)

### Menu Actions (3 points)
**Visuel** :
- Icône : `more-vertical`, taille `--size-6`
- Touch target : 48x48px
- Couleur : `--text-primary`

**Dropdown** :
- Ouvre menu déroulant (slide down depuis icône)
- Fond blanc, ombre `--shadow-lg`
- Items : icône + label (3 actions)
- Close : tap outside ou item sélectionné

**Actions disponibles** :
1. **Save search** : enregistre critères actuels (compte utilisateur)
2. **Share results** : ouvre native share sheet (URL + params)
3. **Alert me** : crée alerte email nouveaux biens (formulaire modal)

---

## Actions Bar détails

### Compteur résultats
**Visuel** :
- Texte : `--font-body-sm`, bold
- Couleur : `--text-secondary`
- Format : "{count} properties found" ou "{count} bien(s) trouvé(s)"

**Comportement** :
- Update dynamique quand filtres changent (sans reload page)

### Bouton Sort
**Visuel** :
- Icône : `sort`, position gauche
- Label : "Sort"
- Badge compteur : si tri actif (ex: "3" = 3 critères)
- Style : bordure `--border-default`, fond blanc

**Action** :
- Click → ouvre modale tri plein écran (voir `filters-sort.md`)
- Modal avec options : Price, Date, Surface, Distance
- Validation → update résultats + ferme modal

### Bouton Filters
**Visuel** :
- Icône : `filter`, position gauche
- Label : "Filters"
- Badge compteur : nb filtres actifs (toujours visible si > 0)
- Style : si filtres actifs → fond `--primary-bg-subtle`, texte `--primary`

**Action** :
- Click → ouvre modale filtres plein écran (panel complet)
- Modal avec tous filtres : Price, Surface, Rooms, Type, Features
- Validation → applique filtres + update résultats

### Toggle Map
**Visuel** :
- Icône : `map` ou `list` (selon état)
- Label : "Show map" ou "Show list"
- Style : bordure `--border-default`, fond blanc

**Action** :
- Click → toggle affichage carte/liste
- **Mode List** (défaut) : affiche cards résultats, icône map
- **Mode Map** : affiche carte plein écran, icône list, masque cards

**Transition** :
- Animation slide : liste slide left, carte slide in right (300ms)

---

## Accessibilité

### Top Bar
**Structure** :
- `<header role="banner">` ou `<nav role="navigation">`
- Ordre tab : Back → Titre (si lien) → Menu actions

**Bouton Back** :
- `aria-label="Go back to previous page"`
- Keyboard : Espace/Entrée

**Titre** :
- `<h1>` si page principale ou `<span>` si contextuel
- `aria-live="polite"` si titre dynamique

**Menu actions** :
- Bouton : `aria-label="Open actions menu"`, `aria-expanded="false"`
- Dropdown : `role="menu"`, items `role="menuitem"`
- Close : ESC, focus retour bouton trigger

### Actions Bar
**Boutons** :
- Labels explicites : "Sort results", "Open filters", "Show map view"
- Badge compteur : `aria-label="3 sorting criteria active"` (screen reader)
- Focus visible : `--border-focus`

**Compteur** :
- `aria-live="polite"` : annonce changement nb résultats

**Toggle map** :
- `aria-pressed="false"` (liste) ou `"true"` (carte)
- Label change selon état : "Show map" ↔ "Show list"

---

## Tokens (Design)

### Top Bar
**Conteneur** :
- Fond : `--white`
- Bordure bas : `--border-light`
- Hauteur : 56px (touch target)
- Padding horizontal : `--size-4`
- Z-index : `--z-sticky` (au-dessus contenu)
- Ombre scroll : `--shadow-sm` (si contenu défile sous)

**Éléments** :
- Icônes : taille `--size-6`, couleur `--text-primary`
- Titre : `--font-heading-sm`, `--text-primary`

**Menu dropdown** :
- Fond : `--white`
- Ombre : `--shadow-lg`
- Rayon : `--radius-md`
- Padding items : `--size-4`
- Hover item : fond `--gray-100`

### Actions Bar
**Conteneur** :
- Fond : `--white`
- Bordure bas : `--border-light`
- Padding : `--size-4`
- Sticky position : sous top bar
- Z-index : `--z-sticky` - 1

**Compteur** :
- Font : `--font-body-sm`, bold
- Couleur : `--text-secondary`
- Margin bottom : `--size-3`

**Boutons** :
- Bordure : `--border-default`, rayon `--radius-md`
- Fond : `--white` (inactif), `--primary-bg-subtle` (actif)
- Padding : `--size-3` vertical, `--size-4` horizontal
- Gap icône-texte : `--size-2`
- Hover : bordure `--primary-border`

**Badge compteur** :
- Fond : `--primary`
- Texte : blanc, `--font-body-xs`
- Rayon : `--radius-full` (pill)
- Padding : `--size-1` horizontal
- Position : top-right du bouton (absolute)

---

## États & interactions

### Top Bar

**Scroll behavior** (optionnel) :
- Scroll down (>50px) : top bar slide up (masquée)
- Scroll up : top bar slide down (réapparaît)
- Au repos en haut : toujours visible

**Menu actions** :
- Click icône → dropdown slide down (200ms)
- Click item → action + ferme menu
- Click outside → ferme menu
- ESC → ferme menu

### Actions Bar

**Bouton Sort** :
- Click → modale tri plein écran
- Badge update si tri appliqué

**Bouton Filters** :
- Click → modale filtres plein écran
- Badge update en temps réel (nb filtres actifs)
- Style change si filtres actifs (fond subtle)

**Toggle Map** :
- Click → toggle state (list ↔ map)
- Icône change : map ↔ list
- Label change : "Show map" ↔ "Show list"
- Transition visuelle : cross-fade 300ms

---

## Performance

**Optimisations** :
- Top bar : fixed positioning (GPU compositing)
- Scroll listener : throttle 100ms (si hide/show au scroll)
- Badge update : debounce 200ms (évite trop d'updates)

**Lazy interactions** :
- Menu actions : rendered mais hidden (display none)
- Modales tri/filtres : chargées on demand (code splitting)

---

## API & Intégration

**Endpoints** :
- POST `/api/searches/save` : sauvegarde recherche utilisateur
- GET `/api/alerts/create` : crée alerte email
- GET `/api/results/count` : récupère compteur (si async)

**State management** :
- React Context ou Zustand : état global (filtres, tri, vue)
- LocalStorage : préférences utilisateur (dernière vue map/list)

---

## Données d'entrée (exemple)

```twig
{% set mobileNav = {
  topBar: {
    backButton: { href: '/', label: 'Back', icon: 'arrow-left' },
    title: 'Search results',
    actionsMenu: {
      items: [
        { label: 'Save search', icon: 'bookmark', action: 'saveSearch' },
        { label: 'Share results', icon: 'share', action: 'shareResults' },
        { label: 'Alert me', icon: 'bell', action: 'createAlert' }
      ]
    }
  },
  actionsBar: {
    resultsCount: { total: 127, label: '127 properties found' },
    sortButton: { label: 'Sort', icon: 'sort', badge: null },
    filtersButton: { label: 'Filters', icon: 'filter', badge: '5' },
    mapToggle: { label: 'Show map', icon: 'map', state: 'list' }
  }
} %}
```

---

## Variantes

**Top Bar** :
- Avec/sans scroll behavior (toujours visible vs auto-hide)
- Titre centré vs gauche (après back)
- Menu actions 3 points vs boutons icônes séparés

**Actions Bar** :
- 3 boutons (Sort + Filters + Map) vs 2 (Filters + Map, tri dans filters)
- Compteur au-dessus vs inline avec boutons

**Toggle Map** :
- Bouton séparé vs tab switch (List | Map)

---

## Messages utilisateur

**Confirmations** :
- Save search : Toast "Search saved successfully"
- Alert created : Toast "Alert created. You will receive emails."

**Erreurs** :
- Save search (non connecté) : "Please sign in to save searches"
- Share failed : "Unable to share. Please try again."

---

## Notes d'implémentation

**Composants** :
- `MobileTopBar` (organism, mobile uniquement)
- `MobileActionsBar` (organism, mobile uniquement)
- `ActionsMenu` (molecule, dropdown)
- Boutons : réutilisation atom `Button` avec variants

**Drupal** :
- Condition affichage : `{{ is_mobile }}` (via theme preprocess)
- Menu actions : permissions selon rôle (save search = user logged)
- Compteur : Ajax update ou inline JS

**JavaScript** :
- Scroll listener : throttle avec IntersectionObserver (performance)
- Toggle map : state management (Vue/React) ou vanilla JS
- Modal triggers : event delegation
- Share API : native `navigator.share()` si disponible
