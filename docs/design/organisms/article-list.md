# Article List (Organism)

**Niveau Atomic Design** : Organism / Listing  
**Catégorie** : Content  
**Statut** : 🚧 Draft  
**Version** : 1.0.0

---

## 📋 Description

Liste/grille d'articles avec image, titre, extrait, métadonnées (auteur, date, tags) et pagination. Variantes compact/featured, layout list/grid.

---

## 🏗️ Structure BEM

```html
<section class="ps-article-list ps-article-list--grid" aria-label="Articles">
  <ul class="ps-article-list__list">
    <li class="ps-article-list__item">
      <article class="ps-article">
        <img class="ps-article__image" src="/media/a1.jpg" alt="" />
        <h3 class="ps-article__title"><a href="#">Titre d'article</a></h3>
        <p class="ps-article__excerpt">Extrait de contenu...</p>
        <div class="ps-article__meta">
          <span class="ps-article__author">Par Alice</span>
          <time class="ps-article__date" datetime="2025-11-28">28 Nov 2025</time>
        </div>
      </article>
    </li>
  </ul>
  <footer class="ps-article-list__footer">
    <!-- pagination molecule -->
  </footer>
</section>
```

### Classes BEM

```
ps-article-list                            // Block
  ps-article-list__list                    // Wrapper list/grid
  ps-article-list__item                    // Item
  ps-article-list__footer                  // Footer (pagination)

ps-article                                  // Child article block
  ps-article__image                         // Image
  ps-article__title                         // Title
  ps-article__excerpt                       // Excerpt
  ps-article__meta                          // Meta container
  ps-article__author                        // Author
  ps-article__date                          // Date

Modificateurs :
  ps-article-list--list|--grid              // Layout
  ps-article-list--compact                  // Dense spacing
  ps-article--featured                      // Larger image/title
```

---

## 📐 Props (Component API)

```yaml
$schema: https://git.drupalcode.org/project/drupal/-/raw/10.1.x/core/modules/sdc/src/Component/schema.json
name: 'PS Article List'
status: stable
group: organisms
description: 'List/grid of articles with metadata and pagination.'

props:
  type: object
  properties:
    layout: { type: string, enum: ['list','grid'], default: 'grid' }
    compact: { type: boolean, default: false }
    items:
      type: array
      items:
        type: object
        properties:
          image: { type: object, properties: { src: {type:string}, alt: {type:string} } }
          title: { type: string }
          href: { type: string }
          excerpt: { type: string }
          author: { type: string }
          date: { type: string }
        required: ['title','href']
    pagination: { type: object }
    attributes:
      type: Drupal\\Core\\Template\\Attribute
  required:
    - items
```

---

## 🔧 Template Twig

```twig
{% set layout = layout|default('grid') %}
{% set compact = compact|default(false) %}
{% set classes = ['ps-article-list', 'ps-article-list--' ~ layout, compact ? 'ps-article-list--compact'] %}
<section {{ attributes.addClass(classes) }} aria-label="Articles">
  <ul class="ps-article-list__list">
    {% for it in items %}
      <li class="ps-article-list__item">
        <article class="ps-article">
          {% if it.image %}<img class="ps-article__image" src="{{ it.image.src }}" alt="{{ it.image.alt }}" />{% endif %}
          <h3 class="ps-article__title"><a href="{{ it.href }}">{{ it.title }}</a></h3>
          {% if it.excerpt %}<p class="ps-article__excerpt">{{ it.excerpt }}</p>{% endif %}
          <div class="ps-article__meta">
            {% if it.author %}<span class="ps-article__author">Par {{ it.author }}</span>{% endif %}
            {% if it.date %}<time class="ps-article__date" datetime="{{ it.date }}">{{ it.date }}</time>{% endif %}
          </div>
        </article>
      </li>
    {% endfor %}
  </ul>
  {% if pagination %}
    <footer class="ps-article-list__footer">{{ pagination|raw }}</footer>
  {% endif %}
</section>
```

---

## 🎨 Styles SCSS

```scss
.ps-article-list {
  &__list { display: grid; gap: var(--ps-spacing-4, 16px); grid-template-columns: repeat(3, 1fr); list-style: none; padding: 0; margin: 0; }
  &__footer { margin-top: var(--ps-spacing-4, 16px); }

  &--list { &__list { grid-template-columns: 1fr; } }
  &--compact { &__list { gap: var(--ps-spacing-3, 12px); } }

  @media (max-width: 992px) { &__list { grid-template-columns: repeat(2, 1fr); } }
  @media (max-width: 600px) { &__list { grid-template-columns: 1fr; } }
}

.ps-article {
  &__image { width: 100%; height: auto; border-radius: var(--ps-border-radius-md, 8px); }
  &__title { font-size: var(--ps-font-size-lg, 18px); margin: var(--ps-spacing-2, 8px) 0; }
  &__excerpt { color: var(--ps-color-neutral-700, #3B4754); }
  &__meta { font-size: var(--ps-font-size-sm, 14px); color: var(--ps-color-neutral-600, #54636F); display: flex; gap: var(--ps-spacing-2, 8px); }

  &--featured &__title { font-size: var(--ps-font-size-xl, 24px); }
}
```

---

## ♿ Accessibilité

- Liens sur titres; images avec `alt`.
- Time avec `datetime` ISO.

---

## 🧪 Exemples d'usage

```twig
{% include '@ps_theme/ps-article-list/ps-article-list.twig' with {
  layout: 'grid',
  items: [
    { title: 'Marché immobilier', href: '#', excerpt: 'Tendances 2025...', author: 'Alice', date: '2025-11-28', image: { src:'/media/a1.jpg', alt:'Ville' } },
    { title: 'Conseils achat', href: '#', excerpt: 'Guide pratique...', author: 'Bob', date: '2025-10-12' },
    { title: 'Home staging', href: '#', excerpt: 'Valorisez votre bien', author: 'Carol', date: '2025-09-03' }
  ],
  pagination: include('@ps_theme/ps-pagination/ps-pagination.twig', { /* props */ }, with_context=false)
} %}
```

---

## 📚 Ressources

- Composition: molecules/card, molecules/pagination
- Tokens: spacing, typography
