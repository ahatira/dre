# About (Page)

**Niveau Atomic Design** : Page  
**Catégorie** : Company  
**Statut** : 🚧 Draft  
**Version** : 1.0.0

---

## 📋 Description

Page À propos avec hero, contenu, et section équipe.

---

## 🧩 Composition

- organisms/header
- organisms/hero
- templates/block (content)
- organisms/feature-section (team or values)
- organisms/footer

---

## 📐 Props (Page API)

```yaml
name: 'PS About'
status: stable
group: pages
props:
  type: object
  properties:
    hero: { type: object }
    block: { type: object }
    features: { type: object }
    attributes: { type: Drupal\\Core\\Template\\Attribute }
```

---

## 🔧 Template Twig

```twig
<section class="ps-page ps-about" aria-label="À propos">
  {{ include('@ps_theme/ps-header/ps-header.twig', {}, with_context=false) }}
  {{ include('@ps_theme/ps-hero/ps-hero.twig', hero, with_context=false) }}
  {{ include('@ps_theme/ps-block/ps-block.twig', block, with_context=false) }}
  {{ include('@ps_theme/ps-feature-section/ps-feature-section.twig', features, with_context=false) }}
  {{ include('@ps_theme/ps-footer/ps-footer.twig', {}, with_context=false) }}
</section>
```

---

## 🎨 Styles SCSS

```scss
.ps-about { }
```

---

## ♿ Accessibilité

- Sections labellisées.

---

## 🧪 Exemples d'usage

```twig
{% include '@ps_theme/ps-about/ps-about.twig' with {
  hero: { /* props */ },
  block: { /* props */ },
  features: { /* props */ }
} %}
```
