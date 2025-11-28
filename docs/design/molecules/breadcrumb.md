# Breadcrumb (Molecule)

**Niveau Atomic Design** : Molecule / Navigation  
**Catégorie** : Navigational trail  
**Statut** : 🚧 Draft  
**Version** : 1.0.0

---

## 📋 Description

Fil d’Ariane pour indiquer la position de la page dans la hiérarchie du site, améliorer le SEO et l’UX. Le dernier élément est non-cliquable et marqué `aria-current="page"`.

---

## 🎨 Aperçu visuel

```
Accueil  ›  Locations  ›  Paris 15e  ›  Appartement familial
```

---

## 🏗️ Structure BEM

```html
<nav class="ps-breadcrumb" aria-label="Breadcrumb">
  <ol class="ps-breadcrumb__list">
    <li class="ps-breadcrumb__item"><a class="ps-breadcrumb__link" href="/">Accueil</a></li>
    <li class="ps-breadcrumb__separator" aria-hidden="true">›</li>
    <li class="ps-breadcrumb__item"><a class="ps-breadcrumb__link" href="/locations">Locations</a></li>
    <li class="ps-breadcrumb__separator" aria-hidden="true">›</li>
    <li class="ps-breadcrumb__item"><a class="ps-breadcrumb__link" href="/locations/paris-15">Paris 15e</a></li>
    <li class="ps-breadcrumb__separator" aria-hidden="true">›</li>
    <li class="ps-breadcrumb__item ps-breadcrumb__item--current" aria-current="page"><span class="ps-breadcrumb__current">Appartement familial</span></li>
  </ol>
</nav>
```

### Classes BEM

```
ps-breadcrumb                         // Block principal
  ps-breadcrumb__list                 // Liste ordonnée
  ps-breadcrumb__item                 // Élément
  ps-breadcrumb__link                 // Lien
  ps-breadcrumb__current              // Élément courant (non cliquable)
  ps-breadcrumb__separator            // Séparateur visuel ›

Modificateurs :
  ps-breadcrumb--compact              // Espacement réduit
  ps-breadcrumb--truncate             // Troncature CSS des éléments intermédiaires
```

---

## 📐 Props (Component API)

### Drupal Component YAML

```yaml
$schema: https://git.drupalcode.org/project/drupal/-/raw/10.1.x/core/modules/sdc/src/Component/schema.json
name: 'PS Breadcrumb'
status: stable
group: molecules
description: 'Fil d’Ariane accessible avec éléments cliquables et page courante.'

props:
  type: object
  properties:
    items:
      type: array
      title: Éléments
      items:
        type: object
        properties:
          label:
            type: string
          url:
            type: string
          icon:
            type: string
        required: ['label']
    compact:
      type: boolean
      title: Compact
      default: false
    truncate:
      type: boolean
      title: Troncature CSS
      default: false
    attributes:
      type: Drupal\Core\Template\Attribute
  required:
    - items
```

---

## 🎭 Variants

- `compact: true` — réduit la taille et l’espacement.
- `truncate: true` — masque visuellement certains éléments intermédiaires (CSS-only).

---

## 🎨 Design Tokens

- Typo: `--ps-font-family-primary`, `--ps-font-size-sm`, `--ps-line-height-normal`
- Couleur: `--ps-color-text-muted` (liens), `--ps-link` palette pour hover/visited
- Spacing: `--ps-spacing-2`, `--ps-spacing-3`

Si `--ps-color-text-muted` manque, proposer `colors.text.muted`.

---

## 🔧 Template Twig

```twig
{#
 * Template for Breadcrumb molecule.
 * Variables:
 * - items: array<{label, url?, icon?}>
 * - compact: bool
 * - truncate: bool
 * - attributes: Attribute
 #}

{% set classes = [
  'ps-breadcrumb',
  compact ? 'ps-breadcrumb--compact',
  truncate ? 'ps-breadcrumb--truncate'
] %}

<nav {{ attributes.addClass(classes) }} aria-label="Breadcrumb">
  <ol class="ps-breadcrumb__list">
    {% for item in items %}
      {% set is_last = loop.last %}

      {% if not is_last %}
        <li class="ps-breadcrumb__item">
          {% if item.url %}
            <a class="ps-breadcrumb__link" href="{{ item.url }}">
              {% if item.icon %}
                <svg class="ps-breadcrumb__icon" aria-hidden="true"><use href="#icon-{{ item.icon }}"></use></svg>
              {% endif %}
              {{ item.label }}
            </a>
          {% else %}
            <span class="ps-breadcrumb__link">{{ item.label }}</span>
          {% endif %}
        </li>
        <li class="ps-breadcrumb__separator" aria-hidden="true">›</li>
      {% else %}
        <li class="ps-breadcrumb__item ps-breadcrumb__item--current" aria-current="page">
          <span class="ps-breadcrumb__current">{{ item.label }}</span>
        </li>
      {% endif %}
    {% endfor %}
  </ol>
</nav>
```

---

## 🎨 Styles SCSS

```scss
.ps-breadcrumb {
  font-family: var(--ps-font-family-primary);
  font-size: var(--ps-font-size-sm, 14px);
  line-height: var(--ps-line-height-normal, 1.5);
  color: var(--ps-color-text-muted, #6B7780);

  &__list { display: flex; flex-wrap: wrap; gap: var(--ps-spacing-2, 8px); margin: 0; padding: 0; list-style: none; }
  &__item { display: inline-flex; align-items: center; }
  &__separator { user-select: none; }

  &__link {
    color: var(--ps-link, var(--ps-color-primary-green, #00915A));
    text-decoration: none;
    &:hover { color: var(--ps-link-hover, #006B43); text-decoration: underline; }
    &:visited { color: var(--ps-link-visited, #8E2A68); }
  }

  &__current { color: var(--ps-color-text, #1F2A33); font-weight: var(--ps-font-weight-medium, 500); }

  &--compact { font-size: var(--ps-font-size-xs, 12px); }

  // Troncature simple (cache les éléments intermédiaires si long)
  &--truncate &__item { max-width: 16ch; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
}
```

---

## ♿ Accessibilité

- Utilise `<nav aria-label="Breadcrumb">` et `aria-current="page"` pour l’élément courant.
- Séparateur marqué `aria-hidden` pour ne pas polluer l’accessibilité.
- Contrastes conformes via palette lien/texte.

---

## 📱 Comportement responsive

- Flex wrap permet le retour à la ligne.
- Option `truncate` réduit l’encombrement sur mobile.

---

## 🧪 Exemples d'usage

```twig
{% include '@ps_theme/ps-breadcrumb/ps-breadcrumb.twig' with {
  items: [
    { label: 'Accueil', url: '/' },
    { label: 'Locations', url: '/locations' },
    { label: 'Paris 15e', url: '/locations/paris-15' },
    { label: 'Appartement familial' }
  ],
  compact: false,
  truncate: false,
} %}
```

---

## 📚 Ressources

- Design tokens: `/design/tokens/colors.yml`, `/design/tokens/typography.yml`, `/design/tokens/spacing.yml`
- SEO: Breadcrumb structured data (JSON-LD)
