# Liste de résultats & Cartes (Responsive)

Affichage des résultats de recherche sous forme de liste de cartes d'offres immobilières.

---

## Modèle de contenu

```yaml
results:
  items:
    - id: 'prop-123'
      title: 'Modern office space in La Défense'
      city: 'Paris, La Défense'
      price: '€2,500/m²'
      surface: '250 m²'
      media:
        images:
          - { src: '/images/prop-1.jpg', alt: 'Office interior' }
          - { src: '/images/prop-2.jpg', alt: 'Meeting room' }
        currentIndex: 0
      badges:
        viewed: true
        exclusive: true
      actions:
        isFavorite: false
        isCompared: false
        href: '/property/123'
  totalCount: 24
  currentPage: 1
  itemsPerPage: 12
```

---

## UX par breakpoint

### Desktop (≥768px)
**Liste à gauche, carte fixe à droite** :
- Grille 2 colonnes (liste + carte interactive)
- Liste scrollable avec cartes empilées verticalement
- Espacements généreux entre cartes (`--size-6`)
- Pagination en bas de liste (ou infinite scroll)

**Carte résultat (desktop)** :
- Layout horizontal : image gauche (40%) + contenu droite (60%)
- **Image** : carrousel avec prev/next, indicateur "X/Y", ratio 4:3
- **Badges** : "Already viewed" (neutre) + "Exclusivity" (or) en haut
- **Contenu** :
  - Titre en gras, 2 lignes max avec ellipsis
  - Localisation avec icône pin
  - Prix en gros + surface en ligne
- **Actions** :
  - Icône cœur (favoris) haut droite
  - Checkbox "Compare" avec label
  - CTA "View the property" vert avec flèche

### Mobile (<768px)
**Liste pile pleine largeur** :
- Cartes empilées 1 colonne avec marges latérales
- Espacements verticaux généreux (`--size-8`)
- Bouton "See more" en bas pour pagination

**Carte résultat (mobile)** :
- Layout vertical : image haut + contenu bas
- **Image** : carrousel pleine largeur, ratio 16:9, indicateur "X/Y"
- **Badges** : overlays haut image (même logique desktop)
- **Contenu** :
  - Titre, localisation, prix + surface identiques desktop
  - Layout plus compact (marges réduites)
- **Actions** :
  - Icône cœur absolute haut droite image
  - Checkbox "Compare" sous prix/surface
  - CTA bouton pleine largeur en bas carte

---

## Structure carte résultat

### Image carrousel
- **Images** : lazy loading, formats optimisés (WebP)
- **Contrôles** : boutons prev/next semi-transparents sur image
- **Indicateur** : "2/5" en bas droite overlay
- **Alt** : descriptif "Photo of [property title]"

### Badges
- **Already viewed** : fond neutre `--neutral-bg-subtle`, texte `--neutral`
- **Exclusivity** : fond or `--gold-bg-subtle`, texte `--gold`, icône étoile
- **Position** : absolute top left image (desktop) ou overlay haut (mobile)
- **Pile** : plusieurs badges empilés verticalement avec gap `--size-2`

### Contenu
- **Titre** : H3, bold, 2 lignes max, ellipsis
- **Localisation** : icône pin + texte gris, 1 ligne ellipsis
- **Prix** : gros (--font-size-7), bold, couleur `--text-primary`
- **Surface** : même ligne que prix, séparateur "•", taille normale

### Actions
- **Favoris** : 
  - Bouton icône cœur (`data-icon="heart"`)
  - État vide (outline) vs plein (filled, couleur `--secondary` rose)
  - `aria-label="Add to favorites"` / "Remove from favorites"
- **Comparer** :
  - Checkbox avec label "Compare"
  - Max 4 sélectionnés (message erreur si dépassement)
  - `aria-describedby` pointant vers message erreur
- **CTA** :
  - Desktop : lien texte vert "View the property" avec flèche
  - Mobile : bouton outline pleine largeur

---

## Accessibilité

**Liste** :
- Structure sémantique : `<ul>` + `<li>` ou grille CSS
- Titre page `<h1>` : "Search results (24 properties)"
- Annonce nb résultats via `aria-live="polite"` après filtre

**Carte** :
- Conteneur `<article>`
- Titre `<h3>` avec lien vers fiche
- Images : `alt` descriptif, `loading="lazy"`
- Carrousel : prev/next avec `aria-label`, focus visible
- Favoris : bouton avec état (`aria-pressed` optionnel)
- Compare : checkbox avec label associé
- Ordre tabulation : image/titre → favoris → compare → CTA

**Navigation clavier** :
- Tab : parcourt cartes dans ordre visuel
- Enter/Space sur contrôles carrousel : prev/next
- Focus visible sur tous interactifs (`:focus-visible`)

---

## Tokens (Design)

**Cartes** :
- Fond : `--white`
- Bordure : `--border-light`
- Ombre : `--shadow-sm` (repos), `--shadow-md` (hover)
- Rayon : `--radius-md`
- Padding : `--size-4` desktop, `--size-3` mobile

**Images** :
- Rayon : `--radius-md` (haut si layout vertical)
- Overlay carrousel : `--overlay-dark-light`

**Badges** :
- Viewed : fond `--neutral-bg-subtle`, texte `--neutral-text-emphasis`
- Exclusive : fond `--gold-bg-subtle`, texte `--gold`, bordure `--gold-border`
- Padding : `--size-2` horizontal, `--size-1` vertical
- Rayon : `--radius-sm`

**Actions** :
- Favoris : couleur `--secondary` (rose) si actif
- Compare : checkbox `--primary` si checked
- CTA : couleur `--primary`, hover `--primary-hover`

**Espacements** :
- Desktop : gap vertical cartes `--size-6`, padding interne `--size-4`
- Mobile : gap vertical `--size-8`, padding interne `--size-3`

---

## États & interactions

### Desktop
- **Hover carte** : ombre accentuée `--shadow-md`
- **Carrousel** : hover prev/next → opacité 100%, click → change image
- **Favoris** : click → toggle état → POST API → update UI + toast
- **Compare** : 
  - Check → ajoute au comparateur (max 4)
  - Si > 4 : message erreur "You can't compare more than 4 ads" + uncheck
  - Update compteur comparateur panel
- **CTA** : click → navigation `/property/{id}`

### Mobile
- **Tap image** : navigation vers fiche (pas de carrousel inline, galerie sur page bien)
- **Swipe image** : change photo carrousel (optionnel UX)
- **Favoris** : tap → toggle + API + toast
- **Compare** : tap checkbox → même logique desktop
- **CTA** : tap → navigation

---

## Pagination

**Desktop** :
- Numérotée en bas : "< 1 2 3 ... 10 >"
- Ou infinite scroll avec IntersectionObserver

**Mobile** :
- Bouton "See more" outline en bas liste
- Tap → fetch page suivante → append cartes → update compteur
- Ou infinite scroll automatique

---

## Performance

**Images** :
- Lazy loading : `loading="lazy"` ou IntersectionObserver
- Formats : WebP avec fallback JPEG
- Sizes : `(min-width: 768px) 50vw, 100vw`
- Carrousel : preload 1 image suivante

**API** :
- GET `/api/search?page=2&limit=12`
- Cache côté client (cartes déjà vues)
- Debounce scroll pour infinite scroll (200ms)

**Optimisations** :
- Virtualization si > 100 résultats (React Window / Intersection Observer)
- Skeleton loaders pendant fetch

---

## États vides

**Aucun résultat** :
- Message centré : "No properties match your criteria"
- Suggestions : "Try adjusting your filters" + bouton "Reset filters"
- Illustration optionnelle (empty state)

---

## Données d'entrée (exemple Storybook)

```twig
{% set results = {
  items: [
    {
      id: 'prop-123',
      title: 'Modern office space in La Défense',
      city: 'Paris, La Défense',
      price: '€2,500/m²',
      surface: '250 m²',
      media: {
        images: [
          { src: '/images/1.jpg', alt: 'Office' },
          { src: '/images/2.jpg', alt: 'Meeting room' }
        ],
        currentIndex: 0
      },
      badges: { viewed: true, exclusive: false },
      actions: { isFavorite: false, isCompared: false, href: '/property/123' }
    }
  ],
  totalCount: 24,
  currentPage: 1
} %}
```

---

## Notes d'implémentation

**Composants** :
- `ResultsList` (organism, responsive)
- `ResultCard` (molecule, variantes desktop/mobile)
- `ImageCarousel` (molecule, réutilisable)

**Drupal** :
- View "Property search results"
- Ajax pagination
- Flag module pour favoris
- Custom JS pour comparateur (max 4)
- Exposed filters pour tri/filtres
