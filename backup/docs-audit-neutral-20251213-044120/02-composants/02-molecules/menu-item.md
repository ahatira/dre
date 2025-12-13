# Menu Item (Molecule)

**Niveau Atomic Design** : Molecule / Navigation  
**Catégorie** : Menu link  
**Statut** : 🚧 Draft  
**Version** : 1.0.0

---

## 📋 Description

Lien de menu avec états (par défaut, actif, désactivé), icône optionnelle et indicateur de sous-menu. Utilisé par le `Main Menu` pour composer la navigation principale.

---

## 🎨 Aperçu visuel

```
Accueil        | Biens ▼        | Contact
```

---

## 🏗️ Structure BEM

```html
<li class="ps-menu-item ps-menu-item--level-1 ps-menu-item--active">
  <a class="ps-menu-item__link" href="/">
    <span class="ps-menu-item__icon" aria-hidden="true"></span>
    <span class="ps-menu-item__label">Accueil</span>
  </a>
</li>
```

Sous-menu (indication visuelle seulement – ouverture gérée par `Main Menu`):
```html
<li class="ps-menu-item ps-menu-item--has-children">
  <a class="ps-menu-item__link" href="/biens" aria-haspopup="true">
    <span class="ps-menu-item__label">Biens</span>
    <span class="ps-menu-item__caret" data-icon="chevron-down" aria-hidden="true"></span>
  </a>
</li>
```

### Classes BEM

```
ps-menu-item                          // Bloc LI
  ps-menu-item__link                  // Lien principal
  ps-menu-item__label                 // Texte du lien
  ps-menu-item__icon                  // Icône optionnelle
  ps-menu-item__caret                 // Indicateur sous-menu

Modificateurs :
  ps-menu-item--active               // Actif (aria-current)
  ps-menu-item--disabled             // Désactivé (span)
  ps-menu-item--has-children         // A un sous-menu
  ps-menu-item--level-1..n           // Profondeur
```

---

## 📐 Props (Component API)

### Drupal Component YAML

```yaml
$schema: https://git.drupalcode.org/project/drupal/-/raw/10.1.x/core/modules/sdc/src/Component/schema.json
name: 'PS Menu Item'
status: stable
group: molecules
description: 'Lien de menu avec icône optionnelle et indicateur de sous-menu.'

props:
  type: object
  properties:
    label:
      type: string
      title: Label
    url:
      type: string
      title: URL
      format: uri-reference
    icon:
      type: string
      title: Icône (optionnelle)
    target:
      type: string
      enum: ['_self','_blank']
      default: '_self'
    active:
      type: boolean
      title: Actif
      default: false
    disabled:
      type: boolean
      title: Désactivé
      default: false
    hasChildren:
      type: boolean
      title: A un sous-menu
      default: false
    level:
      type: integer
      title: Niveau (1..n)
      default: 1
    attributes:
      type: Drupal\Core\Template\Attribute
      title: Attributs HTML du <li>
  required:
    - label
```

---

## 🎭 Variants

- Icône à gauche via prop `icon`.
- Indicateur sous-menu: `hasChildren: true` ajoute un caret.
- Actif: `active: true` ajoute `aria-current`.
- Désactivé: rend un `<span>` non focusable.

---

## 🎨 Design Tokens

- Typo: `--ps-font-family-primary`, `--ps-font-size-base`, `--ps-font-weight-medium`
- Couleurs: `--ps-link`, `--ps-link-hover`, `--ps-link-visited`, `--ps-color-text`
- Spacing: `--ps-spacing-3` (padding), `--ps-spacing-2` (gap)
- Icon size: `20px` (proposer `--ps-icon-size-20` si absent)

---

## 🔧 Template Twig

```twig
{#
 * Template for Menu Item molecule.
 * Variables: voir API YAML
 #}

{% set level = level|default(1) %}
{% set classes = [
  'ps-menu-item',
  'ps-menu-item--level-' ~ level,
  active ? 'ps-menu-item--active',
  disabled ? 'ps-menu-item--disabled',
  hasChildren ? 'ps-menu-item--has-children'
] %}

<li {{ attributes.addClass(classes) }}>
  {% set is_link = url and not disabled %}
  {% set tag = is_link ? 'a' : 'span' %}

  <{{ tag }} class="ps-menu-item__link" {% if is_link %}href="{{ url }}"{% endif %} {% if target == '_blank' %}target="_blank" rel="noopener noreferrer"{% endif %} {% if active %}aria-current="page"{% endif %} {% if hasChildren %}aria-haspopup="true"{% endif %}>
    {% if icon %}
      <span class="ps-menu-item__icon" data-icon="{{ icon }}" aria-hidden="true"></span>
    {% endif %}
    <span class="ps-menu-item__label">{{ label }}</span>
    {% if hasChildren %}
      <span class="ps-menu-item__caret" data-icon="chevron-down" aria-hidden="true"></span>
    {% endif %}
  </{{ tag }}>
</li>
```

---

## 🎨 Styles SCSS

```scss
.ps-menu-item {
  &__link {
    display: inline-flex;
    align-items: center;
    gap: var(--ps-spacing-2, 8px);
    padding: var(--ps-spacing-3, 12px) var(--ps-spacing-3, 12px);
    color: var(--ps-link, var(--ps-color-primary-green, #00915A));
    text-decoration: none;
    &:hover { color: var(--ps-link-hover, #006B43); text-decoration: underline; }
    &:visited { color: var(--ps-link-visited, #8E2A68); }
    &:focus-visible { outline: var(--ps-border-width-focus, 2px) solid var(--ps-color-interactive-focus-outline, #0B5FFF); outline-offset: 2px; }
  }

  &--active .ps-menu-item__link {
    color: var(--ps-color-text, #1F2A33);
    font-weight: var(--ps-font-weight-medium, 500);
    text-decoration: none;
    border-bottom: 2px solid var(--ps-color-primary-green, #00915A);
  }

  &--disabled .ps-menu-item__link { color: var(--ps-color-text-muted, #6B7780); pointer-events: none; }

  &__icon, &__caret { width: 20px; height: 20px; flex-shrink: 0; }
}
```

---

## ♿ Accessibilité

- `aria-current="page"` pour l’élément actif.
- Indicateur sous-menu via `aria-haspopup="true"` (ou via bouton dans `Main Menu` pour le contrôle d’ouverture).
- Focus visible sur les liens; contrastes via tokens.

---

## 📱 Comportement responsive

- Spacing fluide; le layout horizontal/vertical est géré par `Main Menu`.

---

## 🧪 Exemples d'usage

```twig
{# Lien simple #}
{% include '@ps_theme/ps-menu-item/ps-menu-item.twig' with { label: 'Accueil', url: '/' } %}

{# Actif #}
{% include '@ps_theme/ps-menu-item/ps-menu-item.twig' with { label: 'Biens', url: '/biens', active: true } %}

{# Avec sous-menu (indicateur) #}
{% include '@ps_theme/ps-menu-item/ps-menu-item.twig' with { label: 'Services', url: '/services', hasChildren: true } %}
```

---

## 📚 Ressources

- Dépendances: Link, Icon
- Pattern parent: Main Menu
- Tokens: `/design/tokens/colors.yml`, `/design/tokens/typography.yml`, `/design/tokens/spacing.yml`
