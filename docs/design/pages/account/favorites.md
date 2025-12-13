# Favoris (Responsive)

Liste des biens immobiliers sauvegardés par l'utilisateur avec actions rapides.

---

## Modèle de contenu

```yaml
favorites:
  items:
    - id: 'prop-123'
      title: 'Product title'
      location: 'Location'
      price: 'Price'
      surface: 'm²'
      image: '/path/to/image.jpg'
      href: '/property/123'
      isFavorite: true
  totalCount: 6
  hasMore: false
  
pagination:
  currentPage: 1
  itemsPerPage: 6      # Desktop: 6 (grille 3x2), Mobile: 10
  totalPages: 1
```

---

## UX par breakpoint

### Desktop (≥768px)
**Grille 3 colonnes** :
- Cartes bien alignées sur 3 colonnes (gap `--size-6`)
- Ratio image 16:9 ou 4:3
- Icône cœur rose (plein) dans coin supérieur droit image
- Prix + surface en ligne sous image
- Titre bien en gras + localisation avec pin
- Lien "View the property" vert avec flèche
- Pagination en bas si plus de 6 items

**Actions** :
- Hover carte : ombre accentuée
- Click cœur : confirmation modale → retrait → toast
- Click "View the property" : navigation vers fiche bien

### Mobile (<768px)
**Liste pile (1 colonne)** :
- App bar : Back "Menu" + titre "My favorites (6)"
- Cartes empilées pleine largeur avec marges latérales
- Espacements généreux entre cartes (`--size-6` ou `--size-8`)
- Structure carte identique desktop mais layout vertical
- Bouton "See more favorites" outline en bas (si pagination)

**Actions** :
- Tap cœur : confirmation modale → retrait → animation fade out + collapse
- Tap carte (hors cœur) : navigation vers fiche
- Tap "See more" : lazy load cartes suivantes ou navigation page suivante

---

## Structure carte favori

**Image** :
- Ratio aspect maintenu (16:9 recommandé)
- Lazy loading (IntersectionObserver)
- Icône cœur : position absolute, top right, fond blanc semi-transparent, ombre légère

**Contenu** :
- **Prix + surface** : ligne, prix en gras, separator "•", surface normale
- **Titre** : 2 lignes max avec ellipsis, bold
- **Localisation** : icône pin + texte gris, 1 ligne avec ellipsis
- **CTA** : lien texte vert "View the property" avec icône flèche droite

---

## Accessibilité

**Grille/Liste** :
- Titre page `<h1>` : "My favorites" + compteur `(6)` en `<span>`
- Structure sémantique : `<ul>` + `<li>` pour la liste
- Cartes : `<article>` avec heading `<h2>` (titre bien)

**Icône cœur** :
- Bouton `<button>` avec `aria-label="Remove from favorites"`
- État : `aria-pressed="true"` (indique actif)
- Icône décorative `aria-hidden="true"`

**Images** :
- `alt` descriptif : "Photo of [property title]"
- Lazy loading avec placeholder visible

**CTAs** :
- "View the property" : texte explicite, icône flèche `aria-hidden`
- Focus visible sur tous interactifs (`:focus-visible`)

---

## Tokens (Design)

**Cartes** :
- Fond : `--white`
- Ombre : `--shadow-sm` (repos), `--shadow-md` (hover)
- Bordure : `--border-light` ou sans bordure
- Rayon : `--radius-md`
- Padding intérieur : `--size-4`

**Image** :
- Rayon supérieur conforme rayon carte
- Icône cœur : couleur `--secondary` (rose), taille `--size-6`

**Contenu** :
- Prix : bold, couleur `--text-primary`, taille `--font-size-4`
- Titre : bold, couleur `--text-primary`, taille `--font-size-5`
- Localisation : couleur `--text-secondary`, taille `--font-size-3`
- Icône pin : couleur `--text-secondary`

**CTA** :
- Couleur : `--primary` (vert)
- Hover : `--primary-hover`
- Icône flèche : `data-icon="arrow-right"`

**Espacements** :
- Desktop : gap grille `--size-6`, padding carte `--size-4`
- Mobile : gap vertical `--size-6` ou `--size-8`, padding carte `--size-4`

---

## États & interactions

**Desktop** :
- Hover carte : ombre `--shadow-md` + légère translation Y
- Hover cœur : scale 1.1
- Click cœur : modale confirmation → suppression → toast "Property removed from favorites"
- Click carte/CTA : navigation `/property/{id}`

**Mobile** :
- Tap cœur : modale confirmation → suppression → animation fade out (300ms) + collapse height
- Tap carte : navigation
- Tap "See more" : 
  - Si pagination : charge page suivante
  - Si lazy load : fetch + append 10 cartes supplémentaires

---

## Modale confirmation retrait

**Contenu** :
- Titre : "Remove from favorites?"
- Message : "Are you sure you want to remove [property title] from your favorites?"
- Boutons : "Cancel" (secondaire) + "Remove" (danger)

**Accessibilité** :
- `role="dialog"`, `aria-modal="true"`
- Focus trap dans la modale
- Close sur Escape
- Focus retour sur bouton cœur après fermeture

---

## Performance

**Images** :
- Lazy loading : `loading="lazy"` ou IntersectionObserver
- Formats optimisés : WebP avec fallback
- Sizes attribut : `(min-width: 768px) 33vw, 100vw`

**Lazy loading liste** :
- Desktop : pagination classique (SEO friendly)
- Mobile : infinite scroll avec IntersectionObserver sur sentinel
- Limit : 6 desktop, 10 mobile par batch

**API** :
- GET `/api/user/favorites?page=1&limit=6`
- DELETE `/api/user/favorites/{propertyId}` (retrait)
- Cache : invalidation après retrait

---

## États vides

**Aucun favori** :
- Message centré : "You haven't saved any properties yet"
- CTA : "Start searching" → navigation vers recherche
- Illustration optionnelle (empty state)

---

## Variantes

**Desktop** :
- Grille 3 colonnes (défaut) vs 4 colonnes (écrans larges ≥1440px)
- Avec/sans pagination (si peu d'items)

**Mobile** :
- Lazy load (recommandé UX) vs pagination classique
- Bouton "See more" vs infinite scroll

---

## Données d'entrée (exemple Storybook)

```twig
{% set favorites = {
  items: [
    {
      id: 'prop-123',
      title: 'Modern office space in La Défense',
      location: 'Paris, La Défense',
      price: '€2,500/m²',
      surface: '250 m²',
      image: '/images/property-1.jpg',
      href: '/property/123',
      isFavorite: true
    }
  ],
  totalCount: 6,
  hasMore: false
} %}
```

---

## Notes d'implémentation

**Composants** :
- `FavoritesGrid` (organism, desktop)
- `FavoritesList` (organism, mobile)
- Réutiliser `Card` molecule existante avec variante `favorite`

**Drupal** :
- Flag module pour système favoris
- View custom "User favorites" avec filtres
- Ajax pour retrait (pas de rechargement)
- Permissions : `flag/unflag favorites`
