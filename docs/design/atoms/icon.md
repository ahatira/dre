# Icon (Atom)

Niveau: Atom / Element
Rôle: Pictogrammes utilisés dans toute l'UI (navigation, actions, statuts).
Statut: ✅ Stable
Version: 1.0.0

---

## Description
Système d'icônes basé sur un **sprite SVG généré automatiquement** depuis `source/icons-source/*.svg` (139 icônes actuelles). Les catégories Figma sont documentaires uniquement ; la référence reste le `name` unique (ex: `search`, `pin`, `facebook`).

- **Source**: fichiers SVG dans `source/icons-source/` (hors du publicDir, non copiés en production).
- **Sprite**: généré par `npm run icons:build` → `source/assets/icons/icons-sprite.svg` (copié vers `dist/icons/`).
- **Inventaire**: `icons-list.json` généré automatiquement pour Storybook.

## BEM
```
ps-icon
  ps-icon__svg
  ps-icon__fallback (optionnel, icon-font)
  
Modificateurs taille:
  ps-icon--xs | --sm | --md | --lg | --xl | --xxl
  
Modificateurs style (couleur):
  ps-icon--default | --primary | --secondary | --success | --warning | --danger | --info
  
Modificateurs état:
  ps-icon--disabled
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
      description: Nom unique (ex: search, pin, facebook)
    size:
      type: string
      enum: ['xs','sm','md','lg','xl','xxl']
      default: 'md'
    color:
      type: string
      enum: ['default','primary','secondary','success','warning','danger','info']
      default: 'default'
      description: Couleur sémantique (currentColor)
    disabled:
      type: boolean
      default: false
      description: État visuel désactivé (opacity)
    ariaLabel:
      type: string
      title: Label accessibilité (requis pour icônes informatives)
  required: ['name']
```

## Twig
```twig
{# @ps_theme/ps-icon/ps-icon.twig #}
{% set classes = [
  'ps-icon',
  size != 'md' ? 'ps-icon--' ~ size : null,
  color != 'default' ? 'ps-icon--' ~ color : null,
  disabled ? 'ps-icon--disabled' : null
] %}
<span class="{{ classes|join(' ')|trim }}"{% if ariaLabel %} role="img" aria-label="{{ ariaLabel }}"{% else %} aria-hidden="true"{% endif %}>
  <svg class="ps-icon__svg" focusable="false" aria-hidden="true">
    <use href="/icons/icons-sprite.svg#icon-{{ name }}"></use>
  </svg>
</span>
```

## Variants
- **Taille**: xs (10px), sm (16px), md (20px), lg (24px), xl (32px), xxl (48px)
- **Couleur**: default, primary, secondary, success, warning, danger, info
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
