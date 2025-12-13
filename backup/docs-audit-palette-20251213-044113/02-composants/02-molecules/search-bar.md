# Search Bar (Molecule)

**Niveau Atomic Design** : Molecule / Form  
**Catégorie** : Search  
**Statut** : 🚧 Draft  
**Version** : 1.0.0

---

## 📋 Description

Champ de recherche avec bouton de soumission. Conçu pour l’en-tête et les pages de résultats. Utilise les tokens du champ (`Field`) et du bouton (`Button`).

---

## 🎨 Aperçu visuel

```
[ 🔍 Rechercher un bien…            ]  [ Rechercher ]
```

---

## 🏗️ Structure BEM

```html
<form class="ps-search-bar ps-search-bar--inline" role="search" action="/search" method="get">
  <div class="ps-search-bar__field">
    <input class="ps-search-bar__input" type="search" name="q" placeholder="Rechercher un bien…" aria-label="Rechercher" autocomplete="on" />
    <span class="ps-search-bar__icon" data-icon="search" aria-hidden="true"></span>
  </div>
  <button class="ps-search-bar__submit ps-button ps-button--primary ps-button--green" type="submit">
    <span class="ps-button__label">Rechercher</span>
  </button>
</form>
```

### Classes BEM

```
ps-search-bar                          // Block
  ps-search-bar__field                 // Wrapper du champ
  ps-search-bar__input                 // Input type=search
  ps-search-bar__icon                  // Icône loupe (optionnel)
  ps-search-bar__submit                // Bouton de soumission

Modificateurs :
  ps-search-bar--inline                // Forme compacte (alignée)
  ps-search-bar--block                 // Forme en pile (mobile/formulaire)
  ps-search-bar--small|medium|large    // Tailles
```

---

## 📐 Props (Component API)

### Drupal Component YAML

```yaml
$schema: https://git.drupalcode.org/project/drupal/-/raw/10.1.x/core/modules/sdc/src/Component/schema.json
name: 'PS Search Bar'
status: stable
group: molecules
description: 'Barre de recherche accessible avec champ et bouton.'

props:
  type: object
  properties:
    action:
      type: string
      title: Action URL
      format: uri-reference
      default: '/search'
    method:
      type: string
      title: Méthode
      enum: ['get','post']
      default: 'get'
    name:
      type: string
      title: Paramètre de recherche
      default: 'q'
    placeholder:
      type: string
      title: Placeholder
      default: 'Rechercher'
    value:
      type: string
      title: Valeur initiale
    buttonLabel:
      type: string
      title: Libellé du bouton
      default: 'Rechercher'
    size:
      type: string
      title: Taille
      enum: ['small','medium','large']
      default: 'medium'
    layout:
      type: string
      title: Disposition
      enum: ['inline','block']
      default: 'inline'
    showIcon:
      type: boolean
      title: Icône de champ
      default: true
    ariaLabel:
      type: string
      title: Libellé accessible du champ
      default: 'Rechercher'
    autocomplete:
      type: string
      title: Autocomplete
      enum: ['on','off']
      default: 'on'
    attributes:
      type: Drupal\Core\Template\Attribute
      title: Attributs HTML du <form>
  required: []
```

---

## 🎭 Variants

- Layout: `inline` (desktop) ou `block` (mobile/form).
- Tailles: `small|medium|large` appliquées au champ et au bouton.
- Icône: `showIcon: true|false`.

---

## 🎨 Design Tokens

- Typo: `--ps-font-family-primary`, `--ps-font-size-{sm,base,lg}`, `--ps-line-height-normal`
- Spacing: `--ps-spacing-2|3|4` (gaps, paddings)
- Couleurs: champs et bouton via tokens existants (`Field` et `Button`), `--ps-color-neutral-300|100`, `--ps-color-white`
- Focus: `--ps-color-interactive-focus-outline`, `--ps-border-width-focus`

Propositions si manquants: `--ps-icon-size-20` pour la loupe.

---

## 🔧 Template Twig

```twig
{#
 * Template for Search Bar molecule.
 * Variables: voir API YAML ci-dessus
 #}

{% set size = size|default('medium') %}
{% set layout = layout|default('inline') %}
{% set classes = [
  'ps-search-bar',
  'ps-search-bar--' ~ layout,
  'ps-search-bar--' ~ size,
] %}

<form {{ attributes.addClass(classes) }} role="search" action="{{ action|default('/search') }}" method="{{ method|default('get') }}">
  <div class="ps-search-bar__field">
    <input
      class="ps-search-bar__input"
      type="search"
      name="{{ name|default('q') }}"
      {% if value %}value="{{ value }}"{% endif %}
      placeholder="{{ placeholder|default('Rechercher') }}"
      aria-label="{{ ariaLabel|default('Rechercher') }}"
      autocomplete="{{ autocomplete|default('on') }}"
    />
    {% if showIcon %}
      <span class="ps-search-bar__icon" data-icon="search" aria-hidden="true"></span>
    {% endif %}
  </div>

  <button class="ps-search-bar__submit ps-button ps-button--primary ps-button--green" type="submit">
    <span class="ps-button__label">{{ buttonLabel|default('Rechercher') }}</span>
  </button>
</form>
```

---

## 🎨 Styles SCSS

```scss
.ps-search-bar {
  display: grid;
  grid-auto-flow: column;
  align-items: stretch;
  gap: var(--ps-spacing-3, 12px);

  &--block { grid-auto-flow: row; }

  &__field {
    position: relative;
    display: grid;
  }

  &__input {
    padding: var(--ps-field-padding-vertical, 8px) var(--ps-field-padding-horizontal, 12px);
    padding-left: calc(var(--ps-field-padding-horizontal, 12px) + 24px);
    border: var(--ps-border-width-default, 1px) solid var(--ps-color-neutral-300, #D2D7DB);
    border-radius: var(--ps-border-radius-sm, 4px);
    background: var(--ps-color-white, #FFFFFF);
    font-family: var(--ps-font-family-primary);
    font-size: var(--ps-font-size-base, 16px);
    line-height: var(--ps-line-height-normal, 1.5);

    &:focus-visible { outline: var(--ps-border-width-focus, 2px) solid var(--ps-color-interactive-focus-outline, #0B5FFF); outline-offset: 2px; }
  }

  &__icon {
    position: absolute;
    left: 8px; top: 50%; transform: translateY(-50%);
    width: 20px; height: 20px;
    color: var(--ps-color-neutral-500, #8A949C);
    pointer-events: none;
  }

  &--small &__input { font-size: var(--ps-font-size-sm, 14px); padding: 6px 10px; padding-left: 30px; }
  &--large &__input { font-size: var(--ps-font-size-lg, 18px); padding: 10px 14px; padding-left: 34px; }

  &__submit { white-space: nowrap; }
}
```

---

## ♿ Accessibilité

- Utilise `role="search"` sur le formulaire.
- Label accessible via `ariaLabel` sur l’input, et placeholder comme aide seulement.
- Contraste et focus conformes via tokens.

---

## 📱 Comportement responsive

- Layout `inline` passe en `block` sur petits écrans (ou via prop `layout`).
- Le bouton se place sous le champ en `block`.

---

## 🧪 Exemples d'usage

```twig
{% include '@ps_theme/ps-search-bar/ps-search-bar.twig' with {
  action: '/recherche',
  method: 'get',
  name: 'q',
  placeholder: 'Rechercher un bien…',
  buttonLabel: 'Rechercher',
  size: 'medium',
  layout: 'inline',
  showIcon: true,
} %}
```

---

## 📚 Ressources

- Dépendances: Field, Button, Icon
- Design tokens: `/design/tokens/colors.yml`, `/design/tokens/spacing.yml`, `/design/tokens/typography.yml`
