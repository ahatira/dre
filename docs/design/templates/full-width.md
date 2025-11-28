# Full-Width (Template)

**Niveau Atomic Design** : Template / Band  
**Catégorie** : Layout  
**Statut** : 🚧 Draft  
**Version** : 1.0.0

---

## 📋 Description

Bandeau pleine largeur pour contenu media/texte. Option conteneur interne pour limiter la largeur du contenu.

---

## 🏗️ Structure BEM

```html
<section class="ps-full-width ps-full-width--light" aria-label="Bandeau">
  <div class="ps-full-width__inner">
    <!-- contenu -->
  </div>
</section>
```

### Classes BEM

```
ps-full-width                               // Block
  ps-full-width__inner                      // Inner container

Modificateurs :
  ps-full-width--light | --dark             // Theme
  ps-full-width--contained                  // Limit inner width
```

---

## 📐 Props (Component API)

```yaml
$schema: https://git.drupalcode.org/project/drupal/-/raw/10.1.x/core/modules/sdc/src/Component/schema.json
name: 'PS Full Width'
status: stable
group: templates
description: 'Full-width band with optional inner container.'

props:
  type: object
  properties:
    theme: { type: string, enum: ['light','dark'], default: 'light' }
    contained: { type: boolean, default: false }
    content: { type: string }
    attributes:
      type: Drupal\\Core\\Template\\Attribute
```

---

## 🔧 Template Twig

```twig
{% set theme = theme|default('light') %}
{% set contained = contained|default(false) %}
{% set classes = ['ps-full-width', 'ps-full-width--' ~ theme, contained ? 'ps-full-width--contained'] %}
<section {{ attributes.addClass(classes) }} aria-label="Bandeau">
  <div class="ps-full-width__inner">{{ content|raw }}</div>
</section>
```

---

## 🎨 Styles SCSS

```scss
.ps-full-width {
  width: 100%;
  &__inner { padding: var(--ps-spacing-6, 24px) var(--ps-spacing-4, 16px); }
  &--contained &__inner { max-width: 1200px; margin: 0 auto; }
  &--dark { background: var(--ps-color-neutral-900, #0E1A23); color: var(--ps-color-neutral-0, #FFF); }
}
```

---

## ♿ Accessibilité

- Section labellisée.

---

## 🧪 Exemples d'usage

```twig
{% include '@ps_theme/ps-full-width/ps-full-width.twig' with {
  theme: 'dark', contained: true,
  content: include('@ps_theme/ps-feature-section/ps-feature-section.twig', { /* props */ }, with_context=false)
} %}
```

---

## 📚 Ressources

- Tokens: spacing, colors
