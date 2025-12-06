# Contact (Page)

**Niveau Atomic Design** : Page  
**Catégorie** : Contact  
**Statut** : 🚧 Draft  
**Version** : 1.0.0

---

## 📋 Description

Page de contact avec formulaire et carte.

---

## 🧩 Composition

- organisms/header
- molecules/form-field (fields)
- organisms/map-view
- organisms/footer

---

## 📐 Props (Page API)

```yaml
name: 'PS Contact'
status: stable
group: pages
props:
  type: object
  properties:
    form: { type: object }
    map: { type: object }
    attributes: { type: Drupal\\Core\\Template\\Attribute }
```

---

## 🔧 Template Twig

```twig
<section class="ps-page ps-contact" aria-label="Contact">
  {{ include('@ps_theme/ps-header/ps-header.twig', {}, with_context=false) }}
  <div class="ps-contact__content">
    {{ include('@ps_theme/ps-form-field/ps-form-field.twig', form, with_context=false) }}
    {{ include('@ps_theme/ps-map-view/ps-map-view.twig', map, with_context=false) }}
  </div>
  {{ include('@ps_theme/ps-footer/ps-footer.twig', {}, with_context=false) }}
</section>
```

---

## 🎨 Styles SCSS

```scss
.ps-contact__content { display: grid; gap: var(--size-4); grid-template-columns: 1fr 1fr; }
@media (max-width: 768px) { .ps-contact__content { grid-template-columns: 1fr; } }
```

---

## ♿ Accessibilité

- Labels et champs accessibles.

---

## 🧪 Exemples d'usage

```twig
{% include '@ps_theme/ps-contact/ps-contact.twig' with {
  form: { /* props */ },
  map: { /* props */ }
} %}
```
