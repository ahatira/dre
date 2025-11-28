# Card Grid (Organism)

**Niveau Atomic Design** : Organism / Listing  
**Catégorie** : Grid layout  
**Statut** : 🚧 Draft  
**Version** : 1.0.0

---

## 📋 Description

Grille responsive de cartes (produits/propriétés). Supporte contrôles en-tête (trier, filtrer), pagination en pied, et différentes densités (compact/comfortable). Utilise le composant `molecules/card.md` pour chaque item.

---

## 🏗️ Structure BEM

```html
<section class="ps-card-grid" aria-label="Résultats">
  <header class="ps-card-grid__header">
    <div class="ps-card-grid__controls">
      <label class="ps-label" for="sort">Trier</label>
      <select id="sort" class="ps-field">
        <option value="price_asc">Prix croissant</option>
        <option value="price_desc">Prix décroissant</option>
      </select>
    </div>
  </header>

  <ul class="ps-card-grid__list">
    <li class="ps-card-grid__item">
      <!-- include card molecule here -->
    </li>
    <li class="ps-card-grid__item"></li>
    <li class="ps-card-grid__item"></li>
  </ul>

  <footer class="ps-card-grid__footer">
    <!-- pagination molecule -->
  </footer>
</section>
```

### Classes BEM

```
ps-card-grid                               // Block
  ps-card-grid__header                     // Header controls
  ps-card-grid__controls                   // Sorting/filter controls
  ps-card-grid__list                       // Grid list
  ps-card-grid__item                       // Grid item wrapper
  ps-card-grid__footer                     // Footer (pagination)

Modificateurs :
  ps-card-grid--compact                    // Dense layout
  ps-card-grid--comfortable                // Standard spacing
```

---

## 📐 Props (Component API)

```yaml
$schema: https://git.drupalcode.org/project/drupal/-/raw/10.1.x/core/modules/sdc/src/Component/schema.json
name: 'PS Card Grid'
status: stable
group: organisms
description: 'Responsive grid of card molecules with controls and pagination.'

props:
  type: object
  properties:
    items:
      type: array
      items:
        type: object
        properties:
          card:
            type: object
            description: 'Rendered card HTML/Twig macro output'
    density:
      type: string
      enum: ['compact','comfortable']
      default: 'comfortable'
    controls:
      type: object
      description: 'Sorting/filter controls config'
    pagination:
      type: object
      description: 'Pagination config/rendered'
    attributes:
      type: Drupal\\Core\\Template\\Attribute
  required:
    - items
```

---

## 🔧 Template Twig

```twig
{% set density = density|default('comfortable') %}
{% set classes = ['ps-card-grid', 'ps-card-grid--' ~ density] %}

<section {{ attributes.addClass(classes) }} aria-label="Résultats">
  {% if controls %}
    <header class="ps-card-grid__header">
      <div class="ps-card-grid__controls">
        {{ controls|raw }}
      </div>
    </header>
  {% endif %}

  <ul class="ps-card-grid__list">
    {% for item in items %}
      <li class="ps-card-grid__item">
        {{ item.card|raw }}
      </li>
    {% endfor %}
  </ul>

  {% if pagination %}
    <footer class="ps-card-grid__footer">
      {{ pagination|raw }}
    </footer>
  {% endif %}
</section>
```

---

## 🎨 Styles SCSS

```scss
.ps-card-grid {
  &__header { display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--ps-spacing-4, 16px); }
  &__list { display: grid; grid-template-columns: repeat(3, 1fr); gap: var(--ps-spacing-4, 16px); list-style: none; padding: 0; margin: 0; }
  &__item { display: block; }
  &__footer { margin-top: var(--ps-spacing-4, 16px); }

  &--compact { &__list { gap: var(--ps-spacing-3, 12px); } }

  @media (max-width: 992px) { &__list { grid-template-columns: repeat(2, 1fr); } }
  @media (max-width: 600px) { &__list { grid-template-columns: 1fr; } }
}
```

---

## ♿ Accessibilité

- `aria-label` contextualise la section.
- Grille sous forme de liste avec items lisibles.
- Contrôles avec labels.

---

## 🧪 Exemples d'usage

```twig
{% include '@ps_theme/ps-card-grid/ps-card-grid.twig' with {
  items: [
    { card: include('@ps_theme/ps-card/ps-card.twig', { /* props */ }, with_context=false) },
    { card: include('@ps_theme/ps-card/ps-card.twig', { /* props */ }, with_context=false) },
    { card: include('@ps_theme/ps-card/ps-card.twig', { /* props */ }, with_context=false) }
  ],
  controls: include('@ps_theme/ps-dropdown/ps-dropdown.twig', { /* props */ }, with_context=false),
  pagination: include('@ps_theme/ps-pagination/ps-pagination.twig', { /* props */ }, with_context=false)
} %}
```

---

## 📚 Ressources

- Composition: molecules/card.md, molecules/pagination.md
- Tokens: spacing, grid
