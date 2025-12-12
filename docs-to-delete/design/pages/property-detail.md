# Property Detail (Page)

**Niveau Atomic Design** : Page  
**Catégorie** : Detail  
**Statut** : 🚧 Draft  
**Version** : 1.0.0

---

## 📋 Description

Page détail d'une propriété avec hero, galerie/tabs, caractéristiques, carte, et propriétés liées.

---

## 🧩 Composition

- organisms/header
- organisms/hero
- molecules/tabs (gallery/details)
- molecules/table (features/specs)
- organisms/map-view
- organisms/card-grid (related properties)
- organisms/footer

---

## 📐 Props (Page API)

```yaml
name: 'PS Property Detail'
status: stable
group: pages
props:
  type: object
  properties:
    hero: { type: object }
    tabs: { type: object }
    features: { type: object }
    map: { type: object }
    related: { type: object }
    attributes: { type: Drupal\\Core\\Template\\Attribute }
```

---

## 🔧 Template Twig

```twig
<section class="ps-page ps-property-detail" aria-label="Détail propriété">
  {{ include('@ps_theme/ps-header/ps-header.twig', {}, with_context=false) }}
  {{ include('@ps_theme/ps-hero/ps-hero.twig', hero, with_context=false) }}
  {{ include('@ps_theme/ps-tabs/ps-tabs.twig', tabs, with_context=false) }}
  {{ include('@ps_theme/ps-table/ps-table.twig', features, with_context=false) }}
  {{ include('@ps_theme/ps-map-view/ps-map-view.twig', map, with_context=false) }}
  {{ include('@ps_theme/ps-card-grid/ps-card-grid.twig', related, with_context=false) }}
  {{ include('@ps_theme/ps-footer/ps-footer.twig', {}, with_context=false) }}
</section>
```

---

## 🎨 Styles SCSS

```scss
.ps-property-detail { display: block; }
```

---

## ♿ Accessibilité

- Sections labellisées; images avec `alt`; tables avec `scope`/`caption`.

---

## 🧪 Exemples d'usage

```twig
{% include '@ps_theme/ps-property-detail/ps-property-detail.twig' with {
  hero: { /* props */ }, tabs: { /* props */ }, features: { /* props */ }, map: { /* props */ }, related: { /* props */ }
} %}
```
