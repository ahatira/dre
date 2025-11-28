# Two-Column (Template)

**Niveau Atomic Design** : Template / Layout  
**Catégorie** : Grid  
**Statut** : 🚧 Draft  
**Version** : 1.0.0

---

## 📋 Description

Disposition deux colonnes avec ratios: 50-50, 60-40, 40-60. Empilement responsive sur petits écrans. Slots pour contenu gauche/droite.

---

## 🏗️ Structure BEM

```html
<section class="ps-two-col ps-two-col--60-40" aria-label="Deux colonnes">
  <div class="ps-two-col__left">
    <!-- contenu -->
  </div>
  <div class="ps-two-col__right">
    <!-- contenu -->
  </div>
</section>
```

### Classes BEM

```
ps-two-col                                  // Block
  ps-two-col__left                          // Left column
  ps-two-col__right                         // Right column

Modificateurs :
  ps-two-col--50-50 | --60-40 | --40-60     // Ratios
```

---

## 📐 Props (Component API)

```yaml
$schema: https://git.drupalcode.org/project/drupal/-/raw/10.1.x/core/modules/sdc/src/Component/schema.json
name: 'PS Two Column'
status: stable
group: templates
description: 'Two-column layout with ratios.'

props:
  type: object
  properties:
    ratio: { type: string, enum: ['50-50','60-40','40-60'], default: '60-40' }
    left: { type: string, description: 'Rendered HTML for left content' }
    right: { type: string, description: 'Rendered HTML for right content' }
    attributes:
      type: Drupal\\Core\\Template\\Attribute
```

---

## 🔧 Template Twig

```twig
{% set ratio = ratio|default('60-40') %}
{% set classes = ['ps-two-col', 'ps-two-col--' ~ ratio] %}
<section {{ attributes.addClass(classes) }} aria-label="Deux colonnes">
  <div class="ps-two-col__left">{{ left|raw }}</div>
  <div class="ps-two-col__right">{{ right|raw }}</div>
</section>
```

---

## 🎨 Styles SCSS

```scss
.ps-two-col {
  display: grid; gap: var(--ps-spacing-4, 16px);
  grid-template-columns: 3fr 2fr; // default 60-40

  &--50-50 { grid-template-columns: 1fr 1fr; }
  &--60-40 { grid-template-columns: 3fr 2fr; }
  &--40-60 { grid-template-columns: 2fr 3fr; }

  @media (max-width: 768px) { grid-template-columns: 1fr; }
}
```

---

## ♿ Accessibilité

- Section labellisée.
- Ordre du DOM respecte la lecture.

---

## 🧪 Exemples d'usage

```twig
{% include '@ps_theme/ps-two-col/ps-two-col.twig' with {
  ratio: '60-40',
  left: include('@ps_theme/ps-card/ps-card.twig', { /* props */ }, with_context=false),
  right: include('@ps_theme/ps-filter-panel/ps-filter-panel.twig', { /* props */ }, with_context=false)
} %}
```

---

## 📚 Ressources

- Tokens: spacing, grid
