# Article Layout (Template)

**Niveau Atomic Design** : Template / Article  
**Catégorie** : Content  
**Statut** : 🚧 Draft  
**Version** : 1.0.0

---

## 📋 Description

Mise en page d'article avec en-tête (titre/méta), corps, et sidebar optionnelle.

---

## 🏗️ Structure BEM

```html
<article class="ps-article-layout" aria-labelledby="article-title">
  <header class="ps-article-layout__header">
    <h1 id="article-title" class="ps-article-layout__title">Titre</h1>
    <div class="ps-article-layout__meta">
      <span class="ps-article-layout__author">Par Alice</span>
      <time class="ps-article-layout__date" datetime="2025-11-28">28 Nov 2025</time>
    </div>
  </header>
  <div class="ps-article-layout__body">
    <!-- contenu HTML -->
  </div>
  <aside class="ps-article-layout__sidebar">
    <!-- sidebar optional -->
  </aside>
</article>
```

### Classes BEM

```
ps-article-layout                           // Block
  ps-article-layout__header                 // Header
  ps-article-layout__title                  // Title
  ps-article-layout__meta                   // Meta
  ps-article-layout__body                   // Body content
  ps-article-layout__sidebar                // Sidebar
```

---

## 📐 Props (Component API)

```yaml
$schema: https://git.drupalcode.org/project/drupal/-/raw/10.1.x/core/modules/sdc/src/Component/schema.json
name: 'PS Article Layout'
status: stable
group: templates
description: 'Article layout with header, body, and optional sidebar.'

props:
  type: object
  properties:
    title: { type: string }
    author: { type: string }
    date: { type: string }
    body: { type: string }
    sidebar: { type: string }
    attributes:
      type: Drupal\\Core\\Template\\Attribute
```

---

## 🔧 Template Twig

```twig
<article {{ attributes.addClass('ps-article-layout') }} aria-labelledby="article-title">
  <header class="ps-article-layout__header">
    {% if title %}<h1 id="article-title" class="ps-article-layout__title">{{ title }}</h1>{% endif %}
    <div class="ps-article-layout__meta">
      {% if author %}<span class="ps-article-layout__author">Par {{ author }}</span>{% endif %}
      {% if date %}<time class="ps-article-layout__date" datetime="{{ date }}">{{ date }}</time>{% endif %}
    </div>
  </header>
  <div class="ps-article-layout__body">{{ body|raw }}</div>
  {% if sidebar %}<aside class="ps-article-layout__sidebar">{{ sidebar|raw }}</aside>{% endif %}
</article>
```

---

## 🎨 Styles SCSS

```scss
.ps-article-layout {
  &__header { margin-bottom: var(--size-4); }
  &__title { font-size: var(--font-size-7); }
  &__meta { color: var(--text-secondary); display: flex; gap: var(--size-2); }
  display: grid; gap: var(--size-4); grid-template-columns: 3fr 1fr;
  &__body { grid-column: 1; }
  &__sidebar { grid-column: 2; }
  @media (max-width: 768px) { grid-template-columns: 1fr; &__sidebar { grid-column: 1; } }
}
```

---

## ♿ Accessibilité

- `aria-labelledby` pour titre.
- `time` avec `datetime`.

---

## 🧪 Exemples d'usage

```twig
{% include '@ps_theme/ps-article-layout/ps-article-layout.twig' with {
  title: 'Titre', author: 'Alice', date: '2025-11-28',
  body: '<p>Contenu HTML...</p>',
  sidebar: include('@ps_theme/ps-feature-section/ps-feature-section.twig', { /* props */ }, with_context=false)
} %}
```

---

## 📚 Ressources

- Composition: organisms/article-list, templates/content-sidebar
