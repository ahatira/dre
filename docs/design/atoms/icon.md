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
- **Inventaire**: `icons-registry.json` généré automatiquement par `npm run build:icons` (141 icônes).

## BEM
```
ps-icon
  ps-icon__svg
  
Modificateurs taille:
  ps-icon--xs | --sm | --md | --lg | --xl | --xxl
  
Modificateurs style (couleur):
  ps-icon--default | --primary | --secondary | --success | --warning | --danger | --info
  
Modificateurs état:
  ps-icon--disabled

> Les modificateurs ne sont plus appliqués automatiquement par l'API. Utiliser `attributes.addClass()` pour les attacher selon le contexte (ex: badges, alertes).
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
    ariaLabel:
      type: string
      title: Label accessibilité (requis pour icônes informatives)
    attributes:
      type: object
      description: Objet Drupal Attributes pour composition (classes, data-*)
  required: ['name']
```

## Twig
```twig
{# @ps_theme/ps-icon/ps-icon.twig #}
{% set attributes = attributes|default(create_attribute()).addClass('ps-icon') %}
<span{{ attributes }}{% if ariaLabel %} role="img" aria-label="{{ ariaLabel }}"{% else %} aria-hidden="true"{% endif %}>
  <svg class="ps-icon__svg" focusable="false" aria-hidden="true">
    <use href="/icons/icons-sprite.svg#icon-{{ name }}"></use>
  </svg>
</span>
```

## Variants
- **Taille**: xs (10px), sm (16px), md (20px), lg (24px), xl (32px), xxl (48px) via classes `ps-icon--{size}`
- **Couleur**: default, primary, secondary, success, warning, danger, info via classes `ps-icon--{color}`
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
  name: 'arrow-right',
  ariaLabel: 'Aller à droite'
} %}

{% include '@ps_theme/ps-icon/ps-icon.twig' with {
  name: 'facebook',
  attributes: create_attribute().addClass('ps-social-link__icon')
} %}

{% include '@ps_theme/ps-icon/ps-icon.twig' with {
  name: 'fav-filled',
  attributes: create_attribute().addClass(['ps-icon--success', 'ps-favorite__icon'])
} %}
```

## Inventaire des icônes par catégorie

### Ad
- `accessibility`, `air-conditioning`, `camera`, `changing-room`, `details`, `elevator`, `energy`, `equipment`, `floors`, `guide`, `gym`, `heart`, `heart-outline`, `host`, `hotel`, `kitchen`, `lounge`, `meeting-room`, `office-partitions`, `parking`, `people`, `phone`, `picture`, `price`, `reception`, `restaurant`, `sanitary`, `structure`, `unavailable`, `virtual-tour`, `waiting-room`

### Website
- `account`, `buy-rent`, `euro`, `finance`, `logout`, `mandate`, `notifications`, `send`, `settings`

### Generic
- `alert`, `arrow-corner`, `arrow-down`, `arrow-left`, `arrow-right`, `arrow-up`, `bin`, `calendar`, `check`, `checkbox-check`, `checkbox-checked`, `checkbox-stroke`, `checkbox-unchecked`, `chevron-down`, `chevron-left`, `chevron-right`, `chevron-up`, `close`, `cloud`, `download`, `edit`, `exit`, `eye`, `eye-closed`, `help`, `hidden`, `info`, `minus`, `minus-small`, `plus`, `plus-small`, `pwd-hide`, `pwd-show`, `radio`, `radio-off`, `radio-on`, `show`, `square`, `start-outline`, `tooltip`, `trash`, `unfold`

### Search
- `alert-add`, `area-select`, `around-me`, `cards`, `comparateur-empty`, `compare`, `drag`, `filter`, `list`, `map`, `marker`, `nearby`, `pin`, `search`

### Metropole
- `area`, `award`, `district`

### Social media
- `email`, `email-outline`, `facebook`, `linkedin`, `share`, `twitter`, `youtube`

### Mobile only
- `burger-menu`, `menu`, `touch`

### TutOffice
- `next`, `pause`, `play`, `previous`

### Univers
- `commercial-space`, `coworking`, `land`, `office`, `shop`, `warehouse`

### Tools
- `cube-focus`, `open-space`, `shared-areas`

### Blog
- `events`, `market`, `recent-posts`, `testimonial`, `trends`

### Other
- `chart`, `fullscreen`, `video`

### Country
- `bike`, `boat`, `bus`, `car`, `metro`, `plane`, `rer`, `train`, `tram`, `transport`, `walking`

## Cohérence Atomic
Utilisé par Button, Card, Header, Language selector, etc.
