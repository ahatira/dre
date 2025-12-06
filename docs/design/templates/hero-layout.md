# Hero Layout (Template)

**Niveau Atomic Design** : Template / Hero  
**Catégorie** : Layout  
**Statut** : 🚧 Draft  
**Version** : 1.0.0

---

## 📋 Description

Mise en page de page avec hero en tête, suivi d'une section de contenu configurable.

---

## 🏗️ Structure BEM

```html
<section class="ps-hero-layout" aria-label="Hero layout">
  <div class="ps-hero-layout__hero">
    <!-- hero organism render -->
  </div>
  <div class="ps-hero-layout__content">
    <!-- content render -->
  </div>
</section>
```

### Classes BEM

```
ps-hero-layout                              // Block
  ps-hero-layout__hero                      // Hero slot
  ps-hero-layout__content                   // Content slot
```

---

## 📐 Props (Component API)

```yaml
$schema: https://git.drupalcode.org/project/drupal/-/raw/10.1.x/core/modules/sdc/src/Component/schema.json
name: 'PS Hero Layout'
status: stable
group: templates
description: 'Page layout with hero and content slots.'

props:
  type: object
  properties:
    hero: { type: string, description: 'Rendered hero HTML' }
    content: { type: string, description: 'Rendered content HTML' }
    attributes:
      type: Drupal\\Core\\Template\\Attribute
```

---

## 🔧 Template Twig

```twig
<section {{ attributes.addClass('ps-hero-layout') }} aria-label="Hero layout">
  <div class="ps-hero-layout__hero">{{ hero|raw }}</div>
  <div class="ps-hero-layout__content">{{ content|raw }}</div>
</section>
```

---

## 🎨 Styles SCSS

```scss
.ps-hero-layout {
  &__hero { margin-bottom: var(--size-6); }
}
```

---

## ♿ Accessibilité

- Section labellisée.

---

## 🧪 Exemples d'usage

```twig
{% include '@ps_theme/ps-hero-layout/ps-hero-layout.twig' with {
  hero: include('@ps_theme/ps-hero/ps-hero.twig', { /* props */ }, with_context=false),
  content: include('@ps_theme/ps-card-grid/ps-card-grid.twig', { /* props */ }, with_context=false)
} %}
```

---

## 📚 Ressources

- Composition: organisms/hero, organisms/card-grid
