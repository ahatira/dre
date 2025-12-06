# Feature Section (Organism)

**Niveau Atomic Design** : Organism / Marketing  
**Catégorie** : Highlights  
**Statut** : 🚧 Draft  
**Version** : 1.0.0

---

## 📋 Description

Section de mise en avant avec icônes, titres, textes, et CTA optionnel. Variantes de grille (2/3/4 colonnes), alignement (left/center), fond alterné (light/dark).

---

## 🏗️ Structure BEM

```html
<section class="ps-feature-section ps-feature-section--3col ps-feature-section--center" aria-labelledby="fs-title">
  <header class="ps-feature-section__header">
    <h2 id="fs-title" class="ps-feature-section__title">Pourquoi nous choisir</h2>
    <p class="ps-feature-section__text">Transparence, rapidité, expertise locale.</p>
  </header>
  <ul class="ps-feature-section__list">
    <li class="ps-feature-section__item">
      <svg class="ps-icon" aria-hidden="true"><use href="#icon-shield"></use></svg>
      <h3 class="ps-feature-section__item-title">Sécurisé</h3>
      <p class="ps-feature-section__item-text">Vos données protégées.</p>
    </li>
    <li class="ps-feature-section__item">
      <svg class="ps-icon" aria-hidden="true"><use href="#icon-bolt"></use></svg>
      <h3 class="ps-feature-section__item-title">Rapide</h3>
      <p class="ps-feature-section__item-text">Résultats instantanés.</p>
    </li>
    <li class="ps-feature-section__item">
      <svg class="ps-icon" aria-hidden="true"><use href="#icon-map"></use></svg>
      <h3 class="ps-feature-section__item-title">Local</h3>
      <p class="ps-feature-section__item-text">Experts de votre quartier.</p>
    </li>
  </ul>
  <div class="ps-feature-section__actions">
    <a class="ps-button ps-button--primary" href="#">En savoir plus</a>
  </div>
</section>
```

### Classes BEM

```
ps-feature-section                          // Block
  ps-feature-section__header                // Section header
  ps-feature-section__title                 // Title
  ps-feature-section__text                  // Subtitle
  ps-feature-section__list                  // Items grid
  ps-feature-section__item                  // Item
  ps-feature-section__item-title            // Item title
  ps-feature-section__item-text             // Item text
  ps-feature-section__actions               // CTA

Modificateurs :
  ps-feature-section--2col|--3col|--4col    // Grid columns
  ps-feature-section--left|--center         // Alignment
  ps-feature-section--light|--dark          // Background theme
```

---

## 📐 Props (Component API)

```yaml
$schema: https://git.drupalcode.org/project/drupal/-/raw/10.1.x/core/modules/sdc/src/Component/schema.json
name: 'PS Feature Section'
status: stable
group: organisms
description: 'Section de mise en avant avec icônes et textes.'

props:
  type: object
  properties:
    title: { type: string }
    text: { type: string }
    columns: { type: number, enum: [2,3,4], default: 3 }
    align: { type: string, enum: ['left','center'], default: 'center' }
    theme: { type: string, enum: ['light','dark'], default: 'light' }
    items:
      type: array
      items:
        type: object
        properties:
          icon: { type: string }
          title: { type: string }
          text: { type: string }
        required: ['title','text']
    actions:
      type: array
      items:
        type: object
        properties:
          label: { type: string }
          href: { type: string }
          variant: { type: string, enum: ['primary','secondary'], default: 'primary' }
        required: ['label','href']
    attributes:
      type: Drupal\\Core\\Template\\Attribute
```

---

## 🔧 Template Twig

```twig
{% set columns = columns|default(3) %}
{% set align = align|default('center') %}
{% set theme = theme|default('light') %}
{% set classes = ['ps-feature-section', 'ps-feature-section--' ~ columns ~ 'col', 'ps-feature-section--' ~ align, 'ps-feature-section--' ~ theme] %}

<section {{ attributes.addClass(classes) }} aria-labelledby="fs-title">
  {% if title or text %}
    <header class="ps-feature-section__header">
      {% if title %}<h2 id="fs-title" class="ps-feature-section__title">{{ title }}</h2>{% endif %}
      {% if text %}<p class="ps-feature-section__text">{{ text }}</p>{% endif %}
    </header>
  {% endif %}

  {% if items %}
    <ul class="ps-feature-section__list">
      {% for it in items %}
        <li class="ps-feature-section__item">
          {% if it.icon %}<svg class="ps-icon" aria-hidden="true"><use href="#icon-{{ it.icon }}"></use></svg>{% endif %}
          <h3 class="ps-feature-section__item-title">{{ it.title }}</h3>
          <p class="ps-feature-section__item-text">{{ it.text }}</p>
        </li>
      {% endfor %}
    </ul>
  {% endif %}

  {% if actions %}
    <div class="ps-feature-section__actions">
      {% for a in actions %}
        <a class="ps-button ps-button--{{ a.variant|default('primary') }}" href="{{ a.href }}">{{ a.label }}</a>
      {% endfor %}
    </div>
  {% endif %}
</section>
```

---

## 🎨 Styles SCSS

```scss
.ps-feature-section {
  padding: var(--size-6) 0;
  &__header { text-align: center; margin-bottom: var(--size-5); }
  &__title { font-size: var(--font-size-5); }
  &__text { color: var(--text-secondary); }

  &__list { display: grid; gap: var(--size-5); list-style: none; padding: 0; margin: 0; }
  &--2col &__list { grid-template-columns: repeat(2, 1fr); }
  &--3col &__list { grid-template-columns: repeat(3, 1fr); }
  &--4col &__list { grid-template-columns: repeat(4, 1fr); }

  &--left { &__header { text-align: left; } }
  &--dark { background: var(--gray-900); color: var(--white); }

  @media (max-width: 992px) { &--4col &__list { grid-template-columns: repeat(2, 1fr); } }
  @media (max-width: 600px) { &__list { grid-template-columns: 1fr; } }
}
```

---

## ♿ Accessibilité

- Section avec `aria-labelledby`.
- Titres hiérarchiques cohérents (`h2` > `h3`).
- Icônes décoratives marquées `aria-hidden`.

---

## 🧪 Exemples d'usage

```twig
{% include '@ps_theme/ps-feature-section/ps-feature-section.twig' with {
  title: 'Pourquoi nous choisir',
  text: 'Transparence, rapidité, expertise locale.',
  columns: 3,
  items: [
    { icon: 'shield', title: 'Sécurisé', text: 'Vos données protégées.' },
    { icon: 'bolt', title: 'Rapide', text: 'Résultats instantanés.' },
    { icon: 'map', title: 'Local', text: 'Experts de votre quartier.' }
  ],
  actions: [ { label: 'En savoir plus', href: '#' } ]
} %}
```

---

## 📚 Ressources

- Composition: atoms (icon, button, heading, text)
- Tokens: spacing, typography, colors
