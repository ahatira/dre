# Icon (Atom)

Niveau: Atom / Element
Rôle: Pictogrammes utilisés dans toute l'UI (navigation, actions, statuts).
Statut: ✅ Stable
Version: 1.0.0

---

## Description
Bibliothèque complète d'icônes structurée en 13 catégories **documentaires** (organisation uniquement) :
- **Generic** : Flèches, checkbox, search, edit, close, calendrier, validation, etc.
- **Ad/Annonce** : Accessibilité, équipement, structure, favoris, hôte, parking, transport, etc.
- **Blog** : Events, derniers articles, marché, tendances, témoignage
- **Categories** : Compte, acheter/louer, marché capital, confier un bien, conseil
- **Metropole** : Quartier, médaille
- **Mobile-only** : Menu hamburger, touch
- **Recherche** : Comparateur, créer alerte, drag & drop, liste, sélectionner zone carte
- **Social Media** : Facebook, LinkedIn, Mail, Twitter, Youtube
- **Tools** : Espace ouvert, parties communes
- **Tutoffice** : Next, pause, play, previous
- **Univers** : Bureaux, commerces, coworking, entrepôts logistiques, locaux d'activité, terrain
- **Styles** : 4 couleurs (dark grey, light grey, green, white) × 4 états (default, disabled, hover, selected)
- **Group 1** : Autres icônes génériques

**Note** : Les catégories sont documentaires uniquement. L'icône est référencée par son `name` unique.

D'après l'inventaire Figma, 2000+ occurrences au total.

## BEM
```
ps-icon
  ps-icon__svg
  
Modificateurs taille:
  ps-icon--small | --medium | --large | --xlarge
  
Modificateurs style:
  ps-icon--stroke | --fill
  
Modificateurs état:
  ps-icon--default | --disabled | --hover | --selected
  
Modificateurs couleur:
  ps-icon--dark-grey | --light-grey | --green | --white
```

## API (YAML)
```yaml
$schema: https://git.drupalcode.org/project/drupal/-/raw/10.1.x/core/modules/sdc/src/Component/schema.json
name: 'PS Icon'
status: stable
group: atoms
props:
  type: object
  properties:
    name:
      type: string
      title: Icône
      description: Nom unique de l'icône (ex: arrow-right, pin-map, facebook, fav-filled)
    size:
      type: number
      enum: [16,20,24,32]
      default: 24
    state:
      type: string
      enum: ['default','disabled','hover','selected']
      default: 'default'
      description: État visuel de l'icône
    colorVariant:
      type: string
      enum: ['dark-grey','light-grey','green','white']
      default: 'dark-grey'
      description: Variante de couleur (UI spec)
    color:
      type: string
      title: Couleur CSS custom
      description: Surcharge la colorVariant si fourni
    ariaLabel:
      type: string
      title: Label accessibilité
  required: ['name']
```

## Twig
```twig
{# @ps_theme/ps-icon/ps-icon.twig #}
{% set classes = [
  'ps-icon',
  size == 16 ? 'ps-icon--small' : (size == 20 ? 'ps-icon--medium' : (size == 32 ? 'ps-icon--xlarge' : 'ps-icon--large')),
  state ? 'ps-icon--' ~ state,
  colorVariant ? 'ps-icon--' ~ colorVariant
] %}
<svg {{ attributes.addClass(classes) }} width="{{ size }}" height="{{ size }}" aria-label="{{ ariaLabel }}" role="img" fill="currentColor" style="{{ color ? 'color: ' ~ color }}">
  <use href="#icon-{{ name }}" />
</svg>
```

## Variants
- **Taille**: 16/20/24/32px
- **Style**: stroke/fill
- **États** (UI spec):
  - `default`: État normal
  - `disabled`: Icône désactivée (opacité réduite ou gris clair)
  - `hover`: Survol interactif
  - `selected`: État sélectionné/actif
- **Couleurs** (UI spec):
  - `dark-grey`: #434F57 (défaut, texte principal)
  - `light-grey`: #B4BABE (désactivé, secondaire)
  - `green`: #00915A (actions, liens, selected)
  - `white`: #FFFFFF (sur fonds sombres)

**Note** : Les 13 catégories (generic, ad, blog, etc.) sont documentaires uniquement pour l'inventaire.

## Tokens
- Couleurs: `tokens/colors.yml` (`color.text.*`, `color.interactive.*`)
- Tailles: `tokens/typography.yml` (`icon sizes`)

## Accessibilité
- Fournir `ariaLabel` pour icônes informatives.
- Icônes purement décoratives: `aria-hidden="true"` et pas d'ariaLabel.

## Exemples
```twig
{% include '@ps_theme/ps-icon/ps-icon.twig' with {
  name: 'arrow-right', size: 24, ariaLabel: 'Aller à droite'
} %}

{% include '@ps_theme/ps-icon/ps-icon.twig' with {
  name: 'facebook', size: 20, colorVariant: 'green', state: 'hover'
} %}

{% include '@ps_theme/ps-icon/ps-icon.twig' with {
  name: 'fav-filled', size: 24, colorVariant: 'green', state: 'selected'
} %}
```

## Inventaire des icônes par catégorie

### Generic
- Navigation: `arrow-down`, `arrow-left`, `arrow-right`, `arrow-top`, `big-arrow-down`, `big-arrow-left`, `big-arrow-right`, `big-arrow-top`, `big-arrow-corner`
- Actions: `close`, `edit`, `download`, `upload`, `send`, `plus-big`, `plus-small`, `minus-big`, `minus-small`
- Interface: `checkbox-checked`, `checkbox-unchecked`, `radio-selected`, `radio-unselected`, `search`, `settings`, `filter`, `unfold`
- Infos: `calendar`, `check`, `help`, `infos`, `information`, `pin-map`, `map`, `picture`
- Visibilité: `pwd-hide`, `pwd-show`, `eye`, `eye-closed`
- Menu: `menu` (hamburger)

### Ad/Annonce
- Équipements: `accessibility`, `elevator`, `partitioned-offices`, `bus`, `car`, `air-conditioning`, `equipement`, `structure`, `kitchen`, `subway`, `train`, `tram`, `transport`, `bike`, `changing-rooms`
- Favoris: `fav-filled`, `fav-stroke`
- Propriétés: `comparator-empty`, `hotel`, `walking`, `meeting-rooms`, `outdoor`, `people-number`, `phone`, `prix`, `rer`, `restaurant`, `sanitary`, `share`, `sport-room`, `floors`, `surface`, `virtual-tour`, `waiting-room`, `welcome-room`, `energy-consumption`, `gas-emission`, `the-mosts`, `airport`, `boat`, `details`, `guided-visit`, `burger-menu`, `notification`, `logout`, `delete`, `filter`

### Blog
- `events`, `last-articles`, `market`, `trends`, `testimony`

### Categories (Website)
- `account`, `acheter-louer` (buy-rent), `capital-market`, `entrusting-property`, `advise`

### Metropole
- `district`, `medal`

### Mobile-only
- `menu`, `touch`

### Recherche (Search)
- `comparator`, `create-alert`, `drag-and-drop`, `list`, `select-area-map`

### Social Media
- `facebook`, `linkedin`, `mail`, `mail-outlined`, `twitter`, `youtube`

### Tools
- `open-space`, `common-areas`

### Tutoffice (Video/Tuto controls)
- `next`, `pause`, `play`, `previous`

### Univers (Property types)
- `offices`, `shops`, `coworking`, `logistic-warehouses`, `business-premises`, `terrain` (land)

### Other (Generic/Group 1)
- `full-screen`, `chart`, `video`

## Cohérence Atomic
Utilisé par Button, Card, Header, Language selector, etc.
