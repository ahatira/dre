# Carte interactive & Zone distance (Responsive)

Carte géographique affichant les biens avec pins et fonctionnalité de zone de distance personnalisable.

---

## Modèle de contenu

```yaml
map:
  center:
    lat: 48.8566
    lng: 2.3522
  zoom: 12
  
  markers:
    - id: 'prop-123'
      lat: 48.8606
      lng: 2.3376
      priceLabel: '€2,500'       # ou 'NC' (not communicated)
      status: 'available'
      href: '/property/123'
  
  distanceZone:
    enabled: false
    mode: 'walking'              # walking | transports | bike | car
    minutes: 15
    origin:
      lat: 48.8566
      lng: 2.3522
      address: '123 Rue de Rivoli, Paris'
  
  controls:
    zoomIn: true
    zoomOut: true
    fullscreen: true             # Mobile uniquement
    hideList: true               # Desktop uniquement
```

---

## UX par breakpoint

### Desktop (≥768px)
**Carte fixe à droite** (50% largeur viewport) :
- Hauteur 100% viewport, sticky position
- Pins cliquables avec tooltip hover
- Contrôles zoom en bas droite
- Bouton "Hide list" en haut gauche (toggle visibilité liste)
- Zone distance overlay si activée (polygon bleu transparent)

**Widget zone distance** (sur la carte) :
- Bouton "Customize area" flottant (coin carte)
- Click → ouvre modale configuration
- Si zone active : indicateur visible (icône + temps)

### Mobile (<768px)
**Carte en mode plein écran** :
- Ouverte via bouton "Show map" (actions bar)
- Overlay plein écran avec header
- Header : bouton "Close" (X) + titre "Map view"
- Pins plus gros pour touch (48px min)
- Contrôles zoom + fullscreen native
- Liste biens masquée (carte seule)

**Zone distance mobile** :
- Même logique mais modale adaptée plein écran
- Grille modes + selector minutes plus grand

---

## Pins (Markers)

**Visuel** :
- Icône pin standard ou custom SVG
- Label prix dans bulle : "€2,500" ou "NC"
- Couleur selon statut :
  - Disponible : vert `--primary`
  - Non communiqué : gris `--neutral`
  - Vu récemment : bleu `--info` (optionnel)

**Interactions** :
- **Desktop** :
  - Hover → agrandit légèrement + affiche tooltip (titre bien)
  - Click → ouvre popup avec résumé bien + CTA "View"
- **Mobile** :
  - Tap → ouvre bottom sheet avec résumé + CTA
  - Tap CTA → navigation vers fiche bien

---

## Zone de distance

### Widget bouton (sur carte)
- Position : flottant, coin haut gauche carte (desktop) ou bas (mobile)
- Label : "Customize area" ou icône horloge + temps si actif
- Badge compteur : nb biens dans zone (optionnel)

### Modale configuration

**Desktop** (modale centrée) :
- Titre : "Customize your search area"
- **Modes de transport** (4 boutons) :
  - Walking (icône piéton)
  - Public transports (icône métro)
  - Bike (icône vélo)
  - Car (icône voiture)
- **Sélecteur minutes** :
  - Options : 5, 10, 15, 20, 25, 30, 45, 60 min
  - Radio buttons ou slider
- **Adresse origine** :
  - Input texte avec autocomplete
  - Icône pin + label "From: [adresse]"
- **Actions** :
  - "Reset" (secondaire) → désactive zone
  - "Validate" (primaire) → applique + ferme modale + update carte

**Mobile** (plein écran) :
- Même contenu mais layout adapté
- Grille 2x2 pour modes transport (boutons plus grands)
- Sélecteur minutes en grille 4x2
- Actions sticky en bas

---

## Accessibilité

**Carte** :
- Fallback textuel : liste pins si carte ne charge pas
- Contrôles zoom : boutons avec `aria-label` ("Zoom in", "Zoom out")
- Pins : `role="button"`, `aria-label="Property at [price], [location]"`
- Popup pin : `role="dialog"`, focus trap, ESC ferme

**Zone distance modale** :
- `role="dialog"`, `aria-modal="true"`
- Titre : `<h2>` ou `aria-labelledby`
- Modes transport : groupe `fieldset` avec `legend`
- Sélecteur minutes : labels visibles, `aria-pressed` si boutons
- Input adresse : label associé, autocomplete accessible
- Actions : focus visible, ordre tab logique

**Mobile fullscreen** :
- Bouton Close : `aria-label="Close map view"`
- Touch gestures : pinch zoom natif
- Bottom sheet pin : swipe down pour fermer (optionnel)

---

## Tokens (Design)

**Carte** :
- Fond : standard Google/Leaflet style ou custom
- Zone distance : `--primary` avec opacité 0.2, bordure `--primary`
- Overlay : `--overlay-dark-light` (pour modales sur carte)

**Pins** :
- Couleurs : `--primary` (disponible), `--neutral` (NC), `--info` (vu)
- Taille : 32px desktop, 48px mobile (touch target)
- Label bulle : fond blanc, ombre `--shadow-sm`, texte bold

**Modale distance** :
- Fond : `--white`, overlay `--overlay-dark-medium`
- Rayon : `--radius-lg`
- Padding : `--size-6`

**Boutons modes** :
- Inactif : bordure `--border-default`, fond transparent
- Actif : bordure `--primary-border`, fond `--primary-bg-subtle`
- Icônes : taille `--size-6` (24px)

**Actions** :
- Validate : fond `--primary`, texte blanc
- Reset : bordure `--neutral-border`, texte `--neutral`

---

## États & interactions

### Desktop
- **Zoom** : molette souris ou boutons +/-
- **Pan** : drag map
- **Pin hover** : scale 1.2 + tooltip
- **Pin click** : popup avec résumé + CTA
- **Customize area** : click → modale
- **Hide list** : toggle affichage liste (carte pleine largeur)

### Mobile
- **Show map** : ouvre carte plein écran, overlay liste
- **Pinch zoom** : geste natif
- **Pin tap** : bottom sheet résumé
- **Close** : retour liste résultats
- **Customize area** : tap → modale plein écran

### Zone distance
- **Validate** :
  - Calcul isochrone API (ex: Mapbox, Google Distance Matrix)
  - Dessine polygon sur carte
  - Filtre résultats (seuls biens dans zone affichés)
  - Update compteur résultats
  - Ferme modale
- **Reset** :
  - Supprime polygon
  - Affiche tous résultats
  - Désactive badge compteur
  - Ferme modale

---

## Performance

**Carte** :
- Lazy load : chargement au scroll ou au besoin
- Markers clustering si > 50 pins (groupe visuellement)
- Debounce pan/zoom : 300ms avant update markers

**API** :
- Isochrone : POST `/api/map/isochrone` avec `{ mode, minutes, origin }`
- Cache isochrones calculés (session storage)
- Timeout 10s avec fallback message erreur

**Optimisations** :
- Preload tiles proches du viewport
- Disable interactions pendant calcul isochrone (spinner)

---

## API & Intégration

**Maps provider** :
- Google Maps (recommandé, licensing BNP)
- Ou Leaflet + OpenStreetMap (open source)

**Distance Matrix** :
- Google Distance Matrix API
- Ou Mapbox Isochrone API
- Ou custom backend avec OSRM

---

## Données d'entrée (exemple)

```twig
{% set map = {
  center: { lat: 48.8566, lng: 2.3522 },
  zoom: 12,
  markers: [
    {
      id: 'prop-123',
      lat: 48.8606,
      lng: 2.3376,
      priceLabel: '€2,500',
      href: '/property/123'
    }
  ],
  distanceZone: {
    enabled: false,
    mode: 'walking',
    minutes: 15,
    origin: { lat: 48.8566, lng: 2.3522, address: '123 Rue de Rivoli' }
  }
} %}
```

---

## Variantes

**Desktop** :
- Carte 50% vs 40% largeur (ajustable)
- Sticky vs absolute positioning
- Avec/sans bouton "Hide list"

**Mobile** :
- Carte plein écran vs bottom half screen (split view)
- Bottom sheet pins vs popup centrée

---

## Notes d'implémentation

**Composants** :
- `MapWidget` (organism, responsive)
- `DistanceZoneButton` (molecule)
- `DistanceModal` (organism, variantes desktop/mobile)
- `MarkerPopup` (molecule)

**Drupal** :
- Custom module "Map Integration"
- Config : API keys (Google/Mapbox)
- Cache : isochrones calculés
- Ajax pour update markers après filtre
