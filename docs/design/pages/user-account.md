# User Account (Page)

**Niveau Atomic Design** : Page  
**Catégorie** : Account  
**Statut** : 🚧 Draft  
**Version** : 1.0.0

---

## 📋 Description

Tableau de bord utilisateur avec navigation latérale et zone de contenu.

---

## 🧩 Composition

- organisms/header
- organisms/main-menu (sidebar)
- templates/content-sidebar
- organisms/footer

---

## 📐 Props (Page API)

```yaml
name: 'PS User Account'
status: stable
group: pages
props:
  type: object
  properties:
    sidebar: { type: object }
    content: { type: object }
    attributes: { type: Drupal\\Core\\Template\\Attribute }
```

---

## 🔧 Template Twig

```twig
<section class="ps-page ps-user-account" aria-label="Compte utilisateur">
  {{ include('@ps_theme/ps-header/ps-header.twig', {}, with_context=false) }}
  {{ include('@ps_theme/ps-content-sidebar/ps-content-sidebar.twig', {
    position: 'left',
    content: content,
    sidebar: include('@ps_theme/ps-main-menu/ps-main-menu.twig', sidebar, with_context=false)
  }, with_context=false) }}
  {{ include('@ps_theme/ps-footer/ps-footer.twig', {}, with_context=false) }}
</section>
```

---

## 🎨 Styles SCSS

```scss
.ps-user-account { }
```

---

## ♿ Accessibilité

- Landmarks et titres des sections.

---

## 🧪 Exemples d'usage

```twig
{% include '@ps_theme/ps-user-account/ps-user-account.twig' with {
  sidebar: { /* props main-menu */ },
  content: '<h2>Mes favoris</h2>'
} %}
```
