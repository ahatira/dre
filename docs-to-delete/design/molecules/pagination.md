# Pagination (Molecule)

**Niveau Atomic Design** : Molecule / Navigation  
**Catégorie** : Paginator  
**Statut** : 🚧 Draft  
**Version** : 1.0.0

---

## 📋 Description

Navigation paginée avec boutons précédent/suivant et numéros de page. Le lien courant est marqué `aria-current="page"`. Conçue pour être alimentée par Drupal (items pré-calculés) afin d’éviter la logique métier dans Twig.

---

## 🎨 Aperçu visuel

```
‹ Précédent   1   2   [3]   4   5   Suivant ›
```

---

## 🏗️ Structure BEM

```html
<nav class="ps-pagination" aria-label="Pagination">
  <ul class="ps-pagination__list">
    <li class="ps-pagination__item ps-pagination__item--prev ps-pagination__item--disabled"><span class="ps-pagination__link">‹ Précédent</span></li>
    <li class="ps-pagination__item"><a class="ps-pagination__link" href="?page=1">1</a></li>
    <li class="ps-pagination__item"><a class="ps-pagination__link" href="?page=2">2</a></li>
    <li class="ps-pagination__item ps-pagination__item--current" aria-current="page"><span class="ps-pagination__link">3</span></li>
    <li class="ps-pagination__item"><a class="ps-pagination__link" href="?page=4">4</a></li>
    <li class="ps-pagination__item"><a class="ps-pagination__link" href="?page=5">5</a></li>
    <li class="ps-pagination__item ps-pagination__item--next"><a class="ps-pagination__link" href="?page=4">Suivant ›</a></li>
  </ul>
</nav>
```

### Classes BEM

```
ps-pagination                       // Block
  ps-pagination__list              // Liste de liens
  ps-pagination__item              // Élément
  ps-pagination__link              // Lien cliquable ou span

Modificateurs :
  ps-pagination__item--current     // Page courante (non cliquable)
  ps-pagination__item--disabled    // État désactivé
  ps-pagination__item--prev        // Précédent
  ps-pagination__item--next        // Suivant
  ps-pagination--compact           // Variante compacte
```

---

## 📐 Props (Component API)

### Drupal Component YAML

```yaml
$schema: https://git.drupalcode.org/project/drupal/-/raw/10.1.x/core/modules/sdc/src/Component/schema.json
name: 'PS Pagination'
status: stable
group: molecules
description: 'Pagination accessible avec éléments pré-calculés.'

props:
  type: object
  properties:
    items:
      type: array
      title: Éléments
      description: Liste ordonnée d’éléments (Drupal la fournit)
      items:
        type: object
        properties:
          label:
            type: string
          url:
            type: string
          current:
            type: boolean
            default: false
          disabled:
            type: boolean
            default: false
          rel:
            type: string
            enum: ['prev','next','nofollow']
          ariaLabel:
            type: string
        required: ['label']
    compact:
      type: boolean
      title: Compact
      default: false
    attributes:
      type: Drupal\Core\Template\Attribute
  required:
    - items
```

---

## 🎭 Variants

- `compact: true` réduit les paddings et l’espacement.

---

## 🎨 Design Tokens

- Typo: `--ps-font-family-primary`, `--ps-font-size-base|sm`
- Spacing: `--ps-spacing-2|3`
- Couleurs: liens via palette `--ps-link*`, bordures `--ps-color-neutral-300`, fond actif doux (proposition `--ps-color-primary-green-soft` ou utiliser `rgba(...)`)
- Radius: `--ps-border-radius-sm`

Si un fond doux n’existe pas, proposer `colors.primary.soft`.

---

## 🔧 Template Twig

```twig
{#
 * Template for Pagination molecule.
 * Variables:
 * - items: array<{ label, url?, current?, disabled?, rel?, ariaLabel? }>
 * - compact: bool
 * - attributes: Attribute
 #}

{% set classes = [ 'ps-pagination', compact ? 'ps-pagination--compact' ] %}

<nav {{ attributes.addClass(classes) }} aria-label="Pagination">
  <ul class="ps-pagination__list">
    {% for item in items %}
      {% set is_current = item.current %}
      {% set is_disabled = item.disabled %}
      {% set item_classes = [ 'ps-pagination__item', is_current ? 'ps-pagination__item--current', is_disabled ? 'ps-pagination__item--disabled', item.rel == 'prev' ? 'ps-pagination__item--prev', item.rel == 'next' ? 'ps-pagination__item--next' ] %}
      <li class="{{ item_classes|join(' ') }}" {% if is_current %}aria-current="page"{% endif %}>
        {% if item.url and not is_current and not is_disabled %}
          <a class="ps-pagination__link" href="{{ item.url }}" {% if item.rel %}rel="{{ item.rel }}"{% endif %} {% if item.ariaLabel %}aria-label="{{ item.ariaLabel }}"{% endif %}>{{ item.label }}</a>
        {% else %}
          <span class="ps-pagination__link" {% if item.ariaLabel %}aria-label="{{ item.ariaLabel }}"{% endif %}>{{ item.label }}</span>
        {% endif %}
      </li>
    {% endfor %}
  </ul>
</nav>
```

---

## 🎨 Styles SCSS

```scss
.ps-pagination {
  font-family: var(--ps-font-family-primary);
  font-size: var(--ps-font-size-base, 16px);

  &__list { display: flex; gap: var(--ps-spacing-2, 8px); align-items: center; list-style: none; margin: 0; padding: 0; }

  &__link {
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 36px; height: 36px; padding: 0 10px;
    text-decoration: none;
    color: var(--ps-link, var(--ps-color-primary-green, #00915A));
    border: 1px solid var(--ps-color-neutral-300, #D2D7DB);
    border-radius: var(--ps-border-radius-sm, 4px);
    background: var(--ps-color-white, #FFFFFF);

    &:hover { color: var(--ps-link-hover, #006B43); background: rgba(0,145,90,0.06); }
    &:visited { color: var(--ps-link-visited, #8E2A68); }
  }

  &__item--current .ps-pagination__link {
    color: var(--ps-color-white, #FFFFFF);
    background: var(--ps-color-primary-green, #00915A);
    border-color: var(--ps-color-primary-green, #00915A);
    pointer-events: none;
  }

  &__item--disabled .ps-pagination__link { color: var(--ps-color-text-muted, #6B7780); background: var(--ps-color-neutral-100, #F2F4F5); border-color: var(--ps-color-neutral-300, #D2D7DB); pointer-events: none; }

  &--compact { font-size: var(--ps-font-size-sm, 14px); }
}
```

---

## ♿ Accessibilité

- `nav[aria-label="Pagination"]`, `aria-current="page"` pour l’élément courant.
- Liens `rel="prev|next"` pour SEO/assistive tech.
- Zones désactivées rendues en `<span>` non focusables.

---

## 📱 Comportement responsive

- Espacement fluide; `min-width` des numéros assure une cible tactile suffisante.
- Possibilité d’ajouter une variante mobile (précédent/suivant uniquement) en extension.

---

## 🧪 Exemples d'usage

```twig
{% include '@ps_theme/ps-pagination/ps-pagination.twig' with {
  items: [
    { label: '‹ Précédent', rel: 'prev', disabled: true, ariaLabel: 'Page précédente' },
    { label: '1', url: '?page=1' },
    { label: '2', url: '?page=2' },
    { label: '3', current: true },
    { label: '4', url: '?page=4' },
    { label: '5', url: '?page=5' },
    { label: 'Suivant ›', rel: 'next', url: '?page=4', ariaLabel: 'Page suivante' },
  ],
} %}
```

---

## 📚 Ressources

- Dépendances: Button, Icon
- Design tokens: `/design/tokens/colors.yml`, `/design/tokens/spacing.yml`, `/design/tokens/typography.yml`
