# Comparateur de biens (Responsive)

Panel latéral permettant de comparer jusqu'à 3 biens côte à côte avec leurs caractéristiques principales.

---

## Modèle de contenu

```yaml
comparator:
  items:                           # 0 à 3 biens
    - id: 'prop-123'
      title: 'T2 avec vue Tour Eiffel'
      location: '75015 Paris'
      price: '€2,500'
      priceLabel: '/ mois'
      image:
        src: '/images/property-123.jpg'
        alt: 'T2 avec vue Tour Eiffel'
      badge: 'Exclusive'           # optionnel
      features:                    # caractéristiques principales
        - label: 'Surface'
          value: '45 m²'
          icon: 'area'
        - label: 'Pièces'
          value: '2'
          icon: 'rooms'
        - label: 'Étage'
          value: '5e'
          icon: 'stairs'
        - label: 'Disponibilité'
          value: 'Immédiat'
          icon: 'calendar'
        - label: 'Type'
          value: 'Appartement'
          icon: 'building'
        - label: 'Meublé'
          value: 'Non'
          icon: 'furniture'
      href: '/property/123'
      ctaLabel: 'View details'
  
  state:
    count: 2                       # nb biens sélectionnés
    max: 3                         # limite fixe
    isEmpty: false
```

---

## UX par breakpoint

### Desktop (≥768px)
**Panel latéral droit** (drawer) :
- Largeur : 800px (3 colonnes de ~260px + gaps)
- Slide in depuis la droite
- Overlay semi-transparent sur contenu
- Header sticky : "Compare" + compteur + bouton Close
- Colonnes : 3 cartes verticales côte à côte
- Footer sticky : "Reset all" + "Compare" (CTA primaire)

**États** :
- Fermé : icône compteur badge dans header ("Compare (2)")
- Ouvert : panel visible avec biens, scroll vertical si besoin
- Vide : message "No properties to compare yet"

### Mobile (<768px)
**Drawer plein écran** :
- Slide in depuis le bas (ou overlay plein écran)
- Header : titre + compteur + Close
- Layout vertical : cartes empilées (1 colonne)
- Scroll vertical pour voir tous les biens
- Footer sticky : actions "Reset all" + "Compare"

---

## Structure visuelle

### Header (sticky)
**Desktop** :
- Titre : "Compare properties" + badge compteur "(2/3)"
- Bouton Close (X) à droite
- Bordure bas `--border-light`

**Mobile** :
- Même structure mais titre tronqué si besoin
- Icône Close plus grande (48px touch target)

### Zone vide
**Message (si aucun bien)** :
- Icône illustrative (comparer)
- Titre : "No properties selected"
- Description : "Select up to 3 properties to compare"
- Illustration optionnelle

### Cartes comparaison

**Layout desktop (3 colonnes)** :
```
┌────────┬────────┬────────┐
│ Bien 1 │ Bien 2 │ Bien 3 │
│  img   │  img   │  img   │
│  info  │  info  │  info  │
│ détails│ détails│ détails│
│  CTA   │  CTA   │  CTA   │
└────────┴────────┴────────┘
```

**Chaque colonne** :
- Image : ratio 4:3, badge "Exclusive" si applicable
- Bouton Remove (X) en haut droite image
- Titre bien (2 lignes max, ellipsis)
- Localisation (icône pin + ville)
- Prix : format large, label "/mois"
- **Caractéristiques** (tableau) :
  - Lignes alternées fond gris clair
  - Icône + label + valeur
  - Surface, pièces, étage, dispo, type, meublé
- CTA : "View details" (bouton secondaire pleine largeur)

**Layout mobile (1 colonne empilée)** :
- Mêmes éléments mais cartes full width
- Gap vertical entre cartes
- Swipe horizontal possible (optionnel)

### Footer (sticky)

**Actions** :
- **"Reset all"** (secondaire, gauche) : supprime tous les biens, ferme panel
- **"Compare"** (primaire, droite) : validation → action personnalisée (ex: envoi email, export PDF, ou redirection page comparative)

**Désactivation** :
- Si 0 ou 1 bien : "Compare" désactivé (besoin 2 min)
- Reset toujours actif si ≥ 1 bien

---

## Accessibilité

**Panel** :
- `role="dialog"`, `aria-modal="true"`
- `aria-labelledby="comparator-title"`
- Focus trap : Tab cycle dans panel
- ESC : ferme panel

**Header** :
- Titre : `<h2 id="comparator-title">`
- Compteur : `aria-live="polite"` (annonce changements)
- Close : `aria-label="Close comparison"`

**Cartes** :
- Image : `alt` descriptif
- Remove button : `aria-label="Remove [titre bien] from comparison"`
- Caractéristiques : tableau `<table>` avec `<th scope="row">` pour labels
- CTA : label explicite "View details for [titre]"

**Footer** :
- Reset : `aria-label="Remove all properties from comparison"`
- Compare : désactivé si < 2 biens, `aria-disabled="true"` + tooltip

**Clavier** :
- Tab : navigation logique (header → cartes → footer)
- Shift+Tab : retour arrière
- Espace/Entrée : active boutons
- ESC : ferme panel

---

## Tokens (Design)

**Panel** :
- Overlay : `--overlay-dark-medium` (0.5 opacity)
- Fond panel : `--white`
- Largeur desktop : 800px (3 × 260px + 2 × 10px gaps)
- Padding : `--size-6` (header/footer), `--size-4` (contenu)
- Ombre : `--shadow-xl`

**Header** :
- Border bas : `--border-light`
- Titre : `--font-heading-lg`, `--text-primary`
- Badge compteur : fond `--primary-bg-subtle`, texte `--primary`
- Close : icône `--size-6`, couleur `--neutral`

**Cartes** :
- Fond : `--white`
- Bordure : `--border-default`, rayon `--radius-md`
- Gap colonnes : `--size-4` (desktop)
- Gap vertical : `--size-6` (mobile)

**Image** :
- Ratio : 4:3 (comme cards résultats)
- Badge : `--badge` (molecule Badge)
- Remove button : fond `--white`, ombre `--shadow-sm`, icône `--danger`

**Caractéristiques tableau** :
- Lignes alternées : fond `--gray-50` (1 ligne / 2)
- Padding cellules : `--size-3`
- Bordures : `--border-light`
- Icônes : taille `--size-5`, couleur `--neutral`
- Labels : `--font-body-sm`, `--text-secondary`
- Valeurs : `--font-body-sm`, `--text-primary`, bold

**Footer** :
- Border haut : `--border-light`
- Background : `--white` (sticky, ombre légère)
- Actions gap : `--size-4`

**Boutons** :
- Reset : variante secondaire, bordure `--neutral-border`
- Compare : variante primaire, fond `--primary`
- CTA cartes : variante secondaire, pleine largeur

---

## États & interactions

### Ouvrir panel
**Trigger** :
- Click compteur badge header résultats ("Compare (2)")
- Ou checkbox "Add to compare" sur carte → auto-open si 1er bien

**Animation** :
- Desktop : slide in depuis droite (300ms `ease-out`)
- Mobile : slide in depuis bas (300ms `ease-out`)
- Overlay fade in (200ms)

### Fermer panel
**Triggers** :
- Click bouton Close
- ESC keyboard
- Click overlay (optionnel, peut être désactivé)
- Action "Reset all" avec confirmation

**Animation** :
- Slide out inverse (200ms `ease-in`)
- Overlay fade out (150ms)

### Ajouter bien
**Depuis carte résultat** :
- Checkbox "Add to compare" cochée → bien ajouté
- Si panel fermé : badge compteur update
- Si panel ouvert : nouvelle colonne apparaît (fade in)
- Si 3 biens déjà : message toast "Maximum 3 properties"

### Supprimer bien
**Depuis panel** :
- Click Remove (X) sur carte → bien disparaît (fade out)
- Colonnes se réorganisent (animation shift)
- Compteur update
- Si 0 biens restants : affiche message vide

**Depuis liste résultats** :
- Décoche checkbox → bien retiré du panel
- Synchro bidirectionnelle

### Validation "Compare"
**Action possibles** :
- Redirection vers `/compare?ids=123,456,789` (page dédiée)
- Ou export PDF comparatif
- Ou envoi email avec tableau comparatif
- Loader pendant traitement

---

## Performance

**Lazy load** :
- Images cartes : `loading="lazy"`
- Panel rendu mais caché (display none) jusqu'à ouverture

**Optimisations** :
- Max 3 biens : limite mémoire
- Synchro state : LocalStorage ou SessionStorage pour persistance

**Données** :
- Features principales seulement (6-8 lignes max)
- Données complètes chargées si validation "Compare"

---

## API & Intégration

**Endpoints** :
- GET `/api/compare?ids=123,456,789` : récupère données complètes
- POST `/api/compare/export` : génère PDF ou email

**State management** :
- React Context ou Zustand pour état global
- Synchro checkboxes cartes ↔ panel

---

## Données d'entrée (exemple)

```twig
{% set comparator = {
  items: [
    {
      id: 'prop-123',
      title: 'T2 avec vue Tour Eiffel',
      location: '75015 Paris',
      price: '€2,500',
      priceLabel: '/ mois',
      image: { src: '/images/prop-123.jpg', alt: 'T2 vue Eiffel' },
      badge: 'Exclusive',
      features: [
        { label: 'Surface', value: '45 m²', icon: 'area' },
        { label: 'Pièces', value: '2', icon: 'rooms' }
      ],
      href: '/property/123',
      ctaLabel: 'View details'
    }
  ],
  state: { count: 2, max: 3, isEmpty: false }
} %}
```

---

## Variantes

**Desktop** :
- 2 colonnes (si largeur réduite)
- Panel pleine hauteur vs hauteur auto

**Mobile** :
- Drawer bas (half screen) vs fullscreen
- Swipe horizontal entre biens vs scroll vertical

**Actions** :
- "Compare" redirige vers page vs export direct
- "Reset all" avec/sans confirmation

---

## Messages utilisateur

**États** :
- Vide : "No properties to compare yet. Select up to 3 properties."
- Max atteint : Toast "Maximum 3 properties. Remove one to add another."
- Validation < 2 biens : Tooltip "Select at least 2 properties to compare"

**Confirmations** :
- Reset all : "Remove all properties from comparison?" (optionnel)

---

## Notes d'implémentation

**Composants** :
- `Comparator` (organism, panel drawer)
- `ComparatorCard` (molecule, carte interne)
- `ComparatorEmpty` (molecule, état vide)
- `FeatureRow` (atom, ligne caractéristique)

**Drupal** :
- Session storage : IDs biens comparés
- Ajax : update panel sans reload
- Permissions : export PDF selon rôle utilisateur
- Views : template custom pour page `/compare`

**JavaScript** :
- State global : biens sélectionnés (array IDs)
- Event listeners : checkboxes ↔ panel sync
- Animation : GSAP ou CSS transitions
- Focus management : trap focus dans panel ouvert
