# Property Search (Page)

**Niveau Atomic Design** : Page  
**Catégorie** : Search Results  
**Statut** : 🚧 Draft  
**Version** : 1.0.0

---

## 📋 Description

Page de résultats de recherche avec panneau de filtres, grille de cartes, carte, et pagination.

---

## 🧩 Composition

- organisms/header
- organisms/filter-panel
- organisms/card-grid
- organisms/map-view
- molecules/pagination
- organisms/footer

---

## 📐 Props (Page API)

```yaml
name: 'PS Property Search'
status: stable
group: pages
props:
  type: object
  properties:
    filters: { type: object }
    grid: { type: object }
    map: { type: object }
    pagination: { type: object }
    attributes: { type: Drupal\\Core\\Template\\Attribute }
```

---

## 🔧 Template Twig

```twig
<section class="ps-page ps-property-search" aria-label="Résultats de recherche">
  {{ include('@ps_theme/ps-header/ps-header.twig', {}, with_context=false) }}
  <div class="ps-layout">
    <aside class="ps-layout__sidebar">
      {{ include('@ps_theme/ps-filter-panel/ps-filter-panel.twig', filters, with_context=false) }}
    </aside>
    <main class="ps-layout__main">
      {{ include('@ps_theme/ps-card-grid/ps-card-grid.twig', grid, with_context=false) }}
      {{ include('@ps_theme/ps-pagination/ps-pagination.twig', pagination, with_context=false) }}
    </main>
  </div>
  {{ include('@ps_theme/ps-map-view/ps-map-view.twig', map, with_context=false) }}
  {{ include('@ps_theme/ps-footer/ps-footer.twig', {}, with_context=false) }}
</section>
```

---

## 🎨 Styles SCSS

```scss
.ps-property-search { }
.ps-layout { display: grid; grid-template-columns: 280px 1fr; gap: var(--ps-spacing-4, 16px); }
@media (max-width: 992px) { .ps-layout { grid-template-columns: 1fr; } }
```

---

## ♿ Accessibilité

- Landmarks `aside` et `main`.
- `aria-label` sur la page et composants.

---

## 🧪 Exemples d'usage

```twig
{% include '@ps_theme/ps-property-search/ps-property-search.twig' with {
  filters: { /* props */ }, grid: { /* props */ }, map: { /* props */ }, pagination: { /* props */ }
} %}
```
