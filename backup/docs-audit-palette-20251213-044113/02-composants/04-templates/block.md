# Block (Template)

**Niveau Atomic Design** : Template / Section  
**Catégorie** : Container  
**Statut** : 🚧 Draft  
**Version** : 1.0.0

---

## 📋 Description

Section générique avec titre, texte, contenu et actions. Thèmes clair/foncé, largeur contenue ou pleine, fond alternatif.

---

## 🏗️ Structure BEM

```html
<section class="ps-block ps-block--light ps-block--contained" aria-labelledby="block-title">
  <header class="ps-block__header">
    <h2 id="block-title" class="ps-block__title">Titre de section</h2>
    <p class="ps-block__text">Texte introductif.</p>
  </header>
  <div class="ps-block__content">
    <!-- contenu -->
  </div>
  <div class="ps-block__actions">
    <a class="ps-button ps-button--primary" href="#">Action</a>
  </div>
</section>
```

### Classes BEM

```
ps-block                                    // Block
  ps-block__header                          // Header
  ps-block__title                           // Title
  ps-block__text                            // Subtitle
  ps-block__content                         // Content
  ps-block__actions                         // Actions

Modificateurs :
  ps-block--light | --dark                  // Theme
  ps-block--contained | --full              // Width
  ps-block--alt                             // Alternate background
```

---

## 📐 Props (Component API)

```yaml
$schema: https://git.drupalcode.org/project/drupal/-/raw/10.1.x/core/modules/sdc/src/Component/schema.json
name: 'PS Block'
status: stable
group: templates
description: 'Generic section block with header, content, and actions.'

props:
  type: object
  properties:
    title: { type: string }
    text: { type: string }
    content: { type: string }
    actions:
      type: array
      items:
        type: object
        properties:
          label: { type: string }
          href: { type: string }
          variant: { type: string, enum: ['primary','secondary'], default: 'primary' }
        required: ['label','href']
    theme: { type: string, enum: ['light','dark'], default: 'light' }
    width: { type: string, enum: ['contained','full'], default: 'contained' }
    alt: { type: boolean, default: false }
    attributes:
      type: Drupal\\Core\\Template\\Attribute
```

---

## 🔧 Template Twig

```twig
{% set theme = theme|default('light') %}
{% set width = width|default('contained') %}
{% set classes = ['ps-block', 'ps-block--' ~ theme, 'ps-block--' ~ width, alt ? 'ps-block--alt'] %}
<section {{ attributes.addClass(classes) }} aria-labelledby="block-title">
  {% if title or text %}
    <header class="ps-block__header">
      {% if title %}<h2 id="block-title" class="ps-block__title">{{ title }}</h2>{% endif %}
      {% if text %}<p class="ps-block__text">{{ text }}</p>{% endif %}
    </header>
  {% endif %}
  <div class="ps-block__content">{{ content|raw }}</div>
  {% if actions %}
    <div class="ps-block__actions">
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
.ps-block {
  padding: var(--size-6) 0;
  &__header { margin-bottom: var(--size-4); }
  &__title { font-size: var(--font-size-6); }
  &__text { color: var(--text-secondary); }
  &__actions { margin-top: var(--size-4); display: flex; gap: var(--size-3); }

  &--dark { background: var(--gray-900); color: var(--white); }
  &--full { width: 100%; }
  &--contained { max-width: 1200px; margin: 0 auto; padding-left: var(--size-4); padding-right: var(--size-4); }
  &--alt { background: var(--gray-100); }
}
```

---

## ♿ Accessibilité

- `aria-labelledby` pour relier le titre.
- Contraste respecté selon thème.

---

## 🧪 Exemples d'usage

```twig
{% include '@ps_theme/ps-block/ps-block.twig' with {
  title: 'Titre de section',
  text: 'Texte introductif',
  content: include('@ps_theme/ps-card-grid/ps-card-grid.twig', { /* props */ }, with_context=false),
  actions: [ { label: 'Action', href: '#' } ],
  theme: 'light', width: 'contained', alt: true
} %}
```

---

## 📚 Ressources

- Tokens: spacing, colors, typography
