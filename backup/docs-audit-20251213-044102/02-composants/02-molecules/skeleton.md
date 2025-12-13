# Skeleton (Molecule)

**Niveau Atomic Design** : Molecule / Feedback  
**Catégorie** : Loading states  
**Statut** : 🚧 Draft  
**Version** : 1.0.0

---

## 📋 Description

Skeleton loaders pour indiquer un chargement: blocs gris animés imitant la structure finale (texte, avatar, carte, table rows). Réduit la perception de latence et stabilise la mise en page. Propose variantes et tailles, avec animation "shimmer" (opt-in) respectant préférences système (prefers-reduced-motion).

---

## 🏗️ Structure BEM

```html
<div class="ps-skeleton ps-skeleton--text">
  <span class="ps-skeleton__block" style="width: 60%"></span>
  <span class="ps-skeleton__block" style="width: 80%"></span>
  <span class="ps-skeleton__block" style="width: 40%"></span>
</div>

<div class="ps-skeleton ps-skeleton--card ps-skeleton--shimmer">
  <div class="ps-skeleton__media"></div>
  <div class="ps-skeleton__title"></div>
  <div class="ps-skeleton__line" style="width: 90%"></div>
  <div class="ps-skeleton__line" style="width: 70%"></div>
</div>
```

### Classes BEM

```
ps-skeleton                               // Block
  ps-skeleton__block                      // Ligne générique (texte)
  ps-skeleton__media                      // Placeholder image/video
  ps-skeleton__title                      // Placeholder titre
  ps-skeleton__line                       // Ligne de texte

Modificateurs :
  ps-skeleton--text                       // Paragraphes
  ps-skeleton--avatar                     // Avatar (cercle)
  ps-skeleton--card                       // Carte (image + lignes)
  ps-skeleton--table                      // Lignes de table
  ps-skeleton--shimmer                    // Animation shimmer
  ps-skeleton--sm|--md|--lg               // Tailles (deprecated - use small/medium/large)
  ps-skeleton--small|--medium|--large     // Tailles (standardized)
```

---

## 📐 Props (Component API)

### Drupal Component YAML

```yaml
$schema: https://git.drupalcode.org/project/drupal/-/raw/10.1.x/core/modules/sdc/src/Component/schema.json
name: 'PS Skeleton'
status: stable
group: molecules
description: 'Skeleton loaders (text, avatar, card, table rows).'

props:
  type: object
  properties:
    variant:
      type: string
      enum: ['text','avatar','card','table']
      default: 'text'
    lines:
      type: number
      default: 3
      description: 'Nombre de lignes pour text/table'
    shimmer:
      type: boolean
      default: false
    size:
      type: string
      enum: ['small','medium','large']
      default: 'medium'
      description: 'Taille des éléments skeleton. Système standardisé'
    attributes:
      type: Drupal\\Core\\Template\\Attribute
```

---

## 🎭 Variants

- **Types** : `text`, `avatar`, `card`, `table`.
- **Animation** : `shimmer` (respect `prefers-reduced-motion`).
- **Tailles** : `small`|`medium`|`large`.

---

## 🎨 Design Tokens

- Couleurs: `--ps-color-neutral-200`, `--ps-color-neutral-300`
- Rayons: `--ps-border-radius-sm|md`
- Espacements: `--ps-spacing-2|3`
- Animation: `--ps-skeleton-shimmer` (duration, gradient)

---

## 🔧 Template Twig

```twig
{% set variant = variant|default('text') %}
{% set lines = lines|default(3) %}
{% set shimmer = shimmer|default(false) %}
{% set size = size|default('md') %}

{% set root_classes = ['ps-skeleton', 'ps-skeleton--' ~ variant, 'ps-skeleton--' ~ size] %}
{% if shimmer %}{% set root_classes = root_classes|merge(['ps-skeleton--shimmer']) %}{% endif %}

<div {{ attributes.addClass(root_classes) }} aria-hidden="true">
  {% if variant == 'text' %}
    {% for i in range(1, lines) %}
      <span class="ps-skeleton__block" style="width: {{ (80 - i*10) ~ '%' }}"></span>
    {% endfor %}
  {% elseif variant == 'avatar' %}
    <span class="ps-skeleton__block" style="width: 48px; height: 48px; border-radius: 50%"></span>
  {% elseif variant == 'card' %}
    <div class="ps-skeleton__media"></div>
    <div class="ps-skeleton__title"></div>
    {% for i in range(1, 3) %}
      <div class="ps-skeleton__line" style="width: {{ (90 - i*20) ~ '%' }}"></div>
    {% endfor %}
  {% elseif variant == 'table' %}
    {% for i in range(1, lines) %}
      <div class="ps-skeleton__line" style="width: 100%"></div>
    {% endfor %}
  {% endif %}
</div>
```

---

## 🎨 Styles SCSS

```scss
.ps-skeleton {
  --bg: var(--ps-color-neutral-200, #E8EBEF);
  --bg2: var(--ps-color-neutral-300, #D2D7DB);
  --ps-skeleton-shimmer-duration: 1.2s;
  --ps-skeleton-shimmer-gradient: linear-gradient(90deg, rgba(255,255,255,0) 0%, rgba(255,255,255,0.5) 50%, rgba(255,255,255,0) 100%);

  &__block, &__media, &__title, &__line {
    display: block; height: 12px; margin: var(--ps-spacing-2, 8px) 0;
    background: var(--bg);
    border-radius: var(--ps-border-radius-sm, 4px);
  }

  &__media { height: 160px; }
  &__title { height: 16px; margin-top: var(--ps-spacing-3, 12px); }
  &__line { height: 12px; }

  &--sm { .ps-skeleton__media { height: 120px; } .ps-skeleton__title { height: 14px; } .ps-skeleton__line { height: 10px; } }
  &--lg { .ps-skeleton__media { height: 200px; } .ps-skeleton__title { height: 18px; } .ps-skeleton__line { height: 14px; } }

  &--shimmer {
    @media (prefers-reduced-motion: no-preference) {
      .ps-skeleton__block, .ps-skeleton__media, .ps-skeleton__title, .ps-skeleton__line {
        position: relative; overflow: hidden;
      }
      .ps-skeleton__block::after, .ps-skeleton__media::after, .ps-skeleton__title::after, .ps-skeleton__line::after {
        content: ""; position: absolute; inset: 0;
        background: var(--ps-skeleton-shimmer-gradient);
        animation: ps-skeleton-shimmer var(--ps-skeleton-shimmer-duration) linear infinite;
      }
      @keyframes ps-skeleton-shimmer {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
      }
    }
  }
}
```

---

## ♿ Accessibilité

- `aria-hidden="true"` sur le skeleton; ne pas annoncer aux lecteurs d'écran.
- Préserver la structure globale pour éviter layout shift.
- Respecter `prefers-reduced-motion`.

---

## 📱 Responsive

- Blocs width en % pour s'adapter; hauteurs adaptatives via tailles.

---

## 🧪 Exemples d'usage

```twig
{% include '@ps_theme/ps-skeleton/ps-skeleton.twig' with {
  variant: 'card', shimmer: true, size: 'md'
} %}
```

---

## 📚 Ressources

- Skeleton UI best practices
- Motion accessibility guidelines
