# Blog Listing (Page)

**Niveau Atomic Design** : Page  
**Catégorie** : Blog  
**Statut** : 🚧 Draft  
**Version** : 1.0.0

---

## 📋 Description

Listing des articles de blog avec pagination et sidebar optionnelle.

---

## 🧩 Composition

- organisms/header
- organisms/article-list
- molecules/pagination
- templates/content-sidebar (optional)
- organisms/footer

---

## 📐 Props (Page API)

```yaml
name: 'PS Blog Listing'
status: stable
group: pages
props:
  type: object
  properties:
    articles: { type: object }
    pagination: { type: object }
    sidebar: { type: object }
    attributes: { type: Drupal\\Core\\Template\\Attribute }
```

---

## 🔧 Template Twig

```twig
<section class="ps-page ps-blog-listing" aria-label="Blog">
  {{ include('@ps_theme/ps-header/ps-header.twig', {}, with_context=false) }}
  <div class="ps-blog-layout">
    <main>
      {{ include('@ps_theme/ps-article-list/ps-article-list.twig', articles, with_context=false) }}
      {{ include('@ps_theme/ps-pagination/ps-pagination.twig', pagination, with_context=false) }}
    </main>
    {% if sidebar %}
      <aside>
        {{ include('@ps_theme/ps-content-sidebar/ps-content-sidebar.twig', sidebar, with_context=false) }}
      </aside>
    {% endif %}
  </div>
  {{ include('@ps_theme/ps-footer/ps-footer.twig', {}, with_context=false) }}
</section>
```

---

## 🎨 Styles SCSS

```scss
.ps-blog-layout { display: grid; grid-template-columns: 1fr 320px; gap: var(--ps-spacing-4, 16px); }
@media (max-width: 992px) { .ps-blog-layout { grid-template-columns: 1fr; } }
```

---

## ♿ Accessibilité

- Landmarks main/aside; `aria-label` context.

---

## 🧪 Exemples d'usage

```twig
{% include '@ps_theme/ps-blog-listing/ps-blog-listing.twig' with {
  articles: { /* props */ }, pagination: { /* props */ }
} %}
```
