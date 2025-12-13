# Content + Sidebar (Template)

**Niveau Atomic Design** : Template / Layout  
**Catégorie** : Grid  
**Statut** : 🚧 Draft  
**Version** : 1.0.0

---

## 📋 Description

Disposition contenu principal avec barre latérale à gauche/droite. Empilement responsive, sidebar sous le contenu sur mobile.

---

## 🏗️ Structure BEM

```html
<section class="ps-content-sidebar ps-content-sidebar--right" aria-label="Contenu avec sidebar">
  <div class="ps-content-sidebar__content">
    <!-- contenu principal -->
  </div>
  <aside class="ps-content-sidebar__sidebar">
    <!-- sidebar -->
  </aside>
</section>
```

### Classes BEM

```
ps-content-sidebar                          // Block
  ps-content-sidebar__content               // Main content
  ps-content-sidebar__sidebar               // Sidebar

Modificateurs :
  ps-content-sidebar--left | --right        // Sidebar position
```

---

## 📐 Props (Component API)

```yaml
$schema: https://git.drupalcode.org/project/drupal/-/raw/10.1.x/core/modules/sdc/src/Component/schema.json
name: 'PS Content Sidebar'
status: stable
group: templates
description: 'Content + sidebar layout.'

props:
  type: object
  properties:
    position: { type: string, enum: ['left','right'], default: 'right' }
    content: { type: string }
    sidebar: { type: string }
    attributes:
      type: Drupal\\Core\\Template\\Attribute
```

---

## 🔧 Template Twig

```twig
{% set position = position|default('right') %}
{% set classes = ['ps-content-sidebar', 'ps-content-sidebar--' ~ position] %}
<section {{ attributes.addClass(classes) }} aria-label="Contenu avec sidebar">
  <div class="ps-content-sidebar__content">{{ content|raw }}</div>
  <aside class="ps-content-sidebar__sidebar">{{ sidebar|raw }}</aside>
</section>
```

---

## 🎨 Styles SCSS

```scss
.ps-content-sidebar {
  display: grid; gap: var(--size-4);
  grid-template-columns: 3fr 1fr;
  &--left { direction: rtl; }
  @media (max-width: 768px) { grid-template-columns: 1fr; }
}
```

---

## ♿ Accessibilité

- Section labellisée; `aside` pour sidebar.

---

## 🧪 Exemples d'usage

```twig
{% include '@ps_theme/ps-content-sidebar/ps-content-sidebar.twig' with {
  position: 'right',
  content: include('@ps_theme/ps-article/ps-article.twig', { /* props */ }, with_context=false),
  sidebar: include('@ps_theme/ps-filter-panel/ps-filter-panel.twig', { /* props */ }, with_context=false)
} %}
```

---

## 📚 Ressources

- Tokens: spacing, grid
