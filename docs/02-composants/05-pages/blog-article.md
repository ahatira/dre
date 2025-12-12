# Blog Article (Page)

**Niveau Atomic Design** : Page  
**Catégorie** : Blog  
**Statut** : 🚧 Draft  
**Version** : 1.0.0

---

## 📋 Description

Page d'article de blog avec mise en page article et sections liées.

---

## 🧩 Composition

- organisms/header
- templates/article-layout
- organisms/feature-section (related or author bio)
- organisms/footer

---

## 📐 Props (Page API)

```yaml
name: 'PS Blog Article'
status: stable
group: pages
props:
  type: object
  properties:
    article: { type: object }
    related: { type: object }
    attributes: { type: Drupal\\Core\\Template\\Attribute }
```

---

## 🔧 Template Twig

```twig
<section class="ps-page ps-blog-article" aria-label="Article de blog">
  {{ include('@ps_theme/ps-header/ps-header.twig', {}, with_context=false) }}
  {{ include('@ps_theme/ps-article-layout/ps-article-layout.twig', article, with_context=false) }}
  {{ include('@ps_theme/ps-feature-section/ps-feature-section.twig', related, with_context=false) }}
  {{ include('@ps_theme/ps-footer/ps-footer.twig', {}, with_context=false) }}
</section>
```

---

## 🎨 Styles SCSS

```scss
.ps-blog-article { }
```

---

## ♿ Accessibilité

- `aria-label` contextuel; `time` et headings hiérarchiques.

---

## 🧪 Exemples d'usage

```twig
{% include '@ps_theme/ps-blog-article/ps-blog-article.twig' with {
  article: { /* props */ }, related: { /* props */ }
} %}
```
