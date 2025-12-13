# Home Page (Page)

**Niveau Atomic Design** : Page  
**Catégorie** : Landing  
**Statut** : 🚧 Draft  
**Version** : 1.0.0

---

## 📋 Description

Page d'accueil composée de hero, formulaire de recherche, sections de mise en avant, listing de cartes, et footer.

---

## 🧩 Composition

- organisms/header
- organisms/hero
- organisms/search-form
- organisms/feature-section
- organisms/card-grid
- organisms/footer

---

## 📐 Props (Page API)

```yaml
name: 'PS Home Page'
status: stable
group: pages
props:
  type: object
  properties:
    hero: { type: object }
    searchForm: { type: object }
    features: { type: object }
    cards: { type: object }
    attributes: { type: Drupal\\Core\\Template\\Attribute }
```

---

## 🔧 Template Twig

```twig
<section class="ps-page ps-home-page" aria-label="Accueil">
  {{ include('@ps_theme/ps-header/ps-header.twig', {}, with_context=false) }}
  {{ include('@ps_theme/ps-hero/ps-hero.twig', hero, with_context=false) }}
  {{ include('@ps_theme/ps-search-form/ps-search-form.twig', searchForm, with_context=false) }}
  {{ include('@ps_theme/ps-feature-section/ps-feature-section.twig', features, with_context=false) }}
  {{ include('@ps_theme/ps-card-grid/ps-card-grid.twig', cards, with_context=false) }}
  {{ include('@ps_theme/ps-footer/ps-footer.twig', {}, with_context=false) }}
</section>
```

---

## 🎨 Styles SCSS

```scss
.ps-home-page { display: block; }
```

---

## ♿ Accessibilité

- Sections labellisées; ordre de lecture logique.

---

## 🧪 Exemples d'usage

```twig
{% include '@ps_theme/ps-home-page/ps-home-page.twig' with {
  hero: { title: 'Trouver votre bien', text: 'Commencez votre recherche', media: { type:'image', image:{ src:'/media/hero.jpg', alt:'Ville' } } },
  searchForm: { /* props */ },
  features: { /* props */ },
  cards: { /* props */ }
} %}
```
