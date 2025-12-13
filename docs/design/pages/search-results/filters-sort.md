# Filtres & Tri (Responsive)

Système de filtrage et tri des résultats de recherche immobilière.

---

## Modèle de contenu

```yaml
filters:
  propertyType:
    - { value: 'office', label: 'Office', checked: true }
    - { value: 'retail', label: 'Retail', checked: false }
    - { value: 'warehouse', label: 'Warehouse', checked: false }
  
  locations:
    - { value: 'paris-8', label: 'Paris 8th', checked: true }
    - { value: 'paris-17', label: 'Paris 17th', checked: false }
  
  surface:
    min: 100
    max: 500
    unit: 'm²'
  
  price:
    min: null
    max: 1000000
    unit: '€'
  
  advanced:
    parking: true
    airConditioning: false
    accessibility: true

sorting:
  options:
    - { value: 'surface-asc', label: 'Increasing surface' }
    - { value: 'surface-desc', label: 'Decreasing surface' }
    - { value: 'price-asc', label: 'Increasing price' }
    - { value: 'price-desc', label: 'Decreasing price' }
    - { value: 'distance-desc', label: 'Decreasing distance' }
  current: 'surface-asc'

resultsCount: 24
```

---

## UX par breakpoint

### Desktop (≥768px)
**Barre de filtres** (sticky en haut) :
- Layout horizontal avec sections clés visibles
- Dropdowns pour types, localisations
- Range sliders pour surface/prix
- Bouton "Advanced filters" → ouvre panneau/modale
- Reset button si filtres actifs
- Compteur résultats à droite

**Menu tri** :
- Dropdown aligné à droite du compteur résultats
- Label "Sort by : [critère actuel]"
- Menu ouvert : liste options avec radio buttons
- Annonce ARIA du nouveau critère après sélection

### Mobile (<768px)
**Top bar** :
- Bouton "Back" (retour page précédente)
- Logo BNP centré (optionnel)

**Actions bar** :
- 2 boutons primaires pleine largeur :
  - "Show map" → ouvre carte plein écran
  - "See all filters (X)" → ouvre panneau filtres (X = nb filtres actifs)

**Tri** :
- Sous le titre page et compteur résultats
- Format : "Sort by : [critère]" avec caret down
- Tap → menu déroulant modal avec options
- Sélection → ferme menu + annonce ARIA

**Filtres** (panneau/page dédiée) :
- Ouvert via "See all filters"
- Sections collapsibles (accordéons)
- CTAs en bas : "Reset" + "Apply (X results)"
- Close via bouton X ou Apply

---

## Accessibilité

**Filtres desktop** :
- Dropdowns : `aria-expanded`, navigation clavier
- Range sliders : labels, min/max annoncés, `aria-valuetext`
- Checkboxes : groupes avec `fieldset` + `legend`
- Reset button : `aria-label="Reset all filters"`

**Filtres mobile** :
- Panneau : `role="dialog"` ou `<aside>`, focus trap si modal
- Accordéons : `aria-expanded`, focus management
- Apply button : annonce nb résultats via `aria-live`

**Tri** :
- Dropdown : `aria-expanded`, `aria-haspopup="listbox"`
- Options : `role="option"`, `aria-selected` sur actif
- Changement annoncé : `aria-live="polite"` "Sorted by [critère]"

---

## Tokens (Design)

**Filtres** :
- Barre sticky : fond `--white`, ombre `--shadow-sm`
- Dropdowns : bordure `--border-default`, focus `--border-focus`
- Range sliders : track `--gray-200`, thumb `--primary`
- Checkboxes : couleur `--primary` si checked
- Reset button : couleur `--danger` ou `--neutral`

**Tri** :
- Dropdown button : bordure `--border-default`, caret `data-icon="chevron-down"`
- Menu options : fond `--white`, hover `--light-bg-subtle`
- Option active : fond `--primary-bg-subtle`, radio `--primary`

**Mobile** :
- Boutons primaires : fond `--primary`, texte blanc
- Badge compteur filtres : fond `--primary`, texte blanc, rayon pill
- Panneau filtres : fond `--white`, overlay `--overlay-dark-medium`

**Espacements** :
- Desktop : padding barre `--size-4`, gap sections `--size-6`
- Mobile : padding panneau `--size-6`, gap boutons `--size-4`

---

## États & interactions

**Desktop** :
- Hover dropdown : bordure accentuée
- Click dropdown → ouvre menu, autres se ferment
- Change filter → debounce 500ms → fetch résultats → update liste + carte
- Click Reset → vide filtres → fetch résultats
- Tri : click → menu → select → ferme menu → fetch résultats

**Mobile** :
- Tap "See all filters" → ouvre panneau pleine page avec overlay
- Tap section accordéon → expand/collapse
- Tap "Reset" → vide filtres, reste dans panneau
- Tap "Apply (X results)" → ferme panneau → fetch résultats → update liste
- Tri : tap → modal menu → select → ferme → fetch

---

## Validation

**Surface** :
- Min ≤ Max (message si invalide)
- Valeurs positives uniquement

**Prix** :
- Min ≤ Max
- Format monétaire valide

---

## Performance

**Debounce** :
- Range sliders : 500ms après dernière modif
- Text inputs localisation : 300ms + autocomplete

**API** :
- GET `/api/search?filters={...}&sort={...}&page=1`
- Cache résultats côté client (session storage)
- Invalidation cache si filtres changent

**Lazy loading** :
- Panneau filtres mobile : chargé au premier open (optionnel)

---

## Données d'entrée (exemple)

```twig
{% set filters = {
  propertyType: [...],
  locations: [...],
  surface: { min: 100, max: 500 },
  price: { max: 1000000 }
} %}

{% set sorting = {
  options: [...],
  current: 'surface-asc'
} %}
```

---

## Variantes

**Desktop** :
- Barre filtres sticky vs relative
- Filtres avancés en panneau latéral vs modale centrée

**Mobile** :
- Panneau filtres pleine page vs bottom drawer
- Apply button sticky bas vs scroll avec contenu

---

## Notes d'implémentation

**Composants** :
- Desktop : `FiltersBar` (organism) + `SortDropdown` (molecule)
- Mobile : `MobileActionsBar` (molecule) + `FiltersPanel` (organism) + `SortDropdownMobile` (molecule)

**Drupal** :
- Views exposed filters
- Ajax pour update résultats sans rechargement
- Query params pour URLs partageables : `/search?type=office&surface_min=100`
- Behaviors : `once('filters')` pour idempotence
