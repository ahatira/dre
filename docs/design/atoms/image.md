# Image (Atom)

**Niveau Atomic Design** : Atom / Media  
**Catégorie** : Media  
**Statut** : 🚧 Draft  
**Version** : 1.0.0

---

## 📋 Description

Image responsive avec `loading=lazy` par défaut, support de `srcset/sizes`, ratio optionnel et coins arrondis.

---

## 🎨 Aperçu visuel

```
[ Image responsive ]  max-width: 100%, height: auto
[ Image ratio 16:9 ]  object-fit: cover
[ Avatar (rounded-full) ]
```

---

## 🏗️ Structure BEM

```html
<figure class="ps-image ps-image--rounded-md ps-image--fit-cover">
  <img class="ps-image__img" src="/path/img.jpg" alt="Description" loading="lazy" width="800" height="450" />
</figure>
```

### Classes BEM

```
ps-image                         // Block wrapper (figure)
  ps-image__img                  // Élément img

Modificateurs :
  ps-image--fit-cover|contain    // Object-fit
  ps-image--rounded-none|sm|md|lg|full
  ps-image--ratio-16x9|1x1|4x3   // Ratio via padding (optionnel)
```

---

## 📐 Props (Component API)

### Drupal Component YAML

```yaml
$schema: https://git.drupalcode.org/project/drupal/-/raw/10.1.x/core/modules/sdc/src/Component/schema.json
name: 'PS Image'
status: stable
group: atoms
description: 'Image responsive avec lazy loading, srcset/sizes et options de ratio.'

props:
  type: object
  properties:
    src:
      type: string
      title: Source
      description: URL de l'image
      format: uri-reference
    alt:
      type: string
      title: Texte alternatif
      description: Description pour l’accessibilité
    width:
      type: integer
      title: Largeur intrinsèque
    height:
      type: integer
      title: Hauteur intrinsèque
    srcset:
      type: array
      title: Srcset
      items:
        type: string
      description: Liste de sources (ex: ['/img-400.jpg 400w','/img-800.jpg 800w'])
    sizes:
      type: string
      title: Sizes
      description: Attribut sizes (ex: '(min-width: 768px) 50vw, 100vw')
    loading:
      type: string
      title: Loading
      enum: ['lazy','eager']
      default: 'lazy'
    decoding:
      type: string
      title: Decoding
      enum: ['async','auto','sync']
      default: 'auto'
    fit:
      type: string
      title: Object fit
      enum: ['cover','contain']
      default: 'cover'
    rounded:
      type: string
      title: Rayon
      enum: ['none','sm','md','lg','full']
      default: 'none'
    ratio:
      type: string
      title: Ratio visuel
      enum: ['none','16x9','1x1','4x3']
      default: 'none'
    attributes:
      type: Drupal\Core\Template\Attribute
      title: Attributs HTML additionnels
  required:
    - src
    - alt
```

---

## 🎭 Variants

- Fit: `cover` (défaut) ou `contain`.
- Rayons: `none|sm|md|lg|full` (avatar).
- Ratio: `none|16x9|1x1|4x3` via wrapper.

---

## 🎨 Design Tokens

- Couleur de fond placeholder: `--ps-color-neutral-100` (si utilisée)
- Rayon: `--ps-border-radius-none|sm|md|lg|full`
- Ombres (optionnel): `--ps-shadow-sm|md`

Si `--ps-color-neutral-100` manque, proposer `colors.neutral.100`.

---

## 🔧 Template Twig

```twig
{#
 * Template for Image atom.
 * Variables:
 * - src (string), alt (string), width (int), height (int)
 * - srcset (array<string>), sizes (string)
 * - loading ('lazy'|'eager'), decoding ('async'|'auto'|'sync')
 * - fit ('cover'|'contain'), rounded ('none'|'sm'|'md'|'lg'|'full'), ratio
 * - attributes: Attribute
 #}

{% set fit = fit|default('cover') %}
{% set rounded = rounded|default('none') %}
{% set ratio = ratio|default('none') %}

{% set classes = [
  'ps-image',
  'ps-image--fit-' ~ fit,
  'ps-image--rounded-' ~ rounded,
  ratio != 'none' ? 'ps-image--ratio-' ~ ratio
] %}

<figure {{ attributes.addClass(classes) }}>
  {% if ratio != 'none' %}
    <span class="ps-image__ratio" aria-hidden="true"></span>
  {% endif %}
  <img
    class="ps-image__img"
    src="{{ src }}"
    alt="{{ alt }}"
    {% if width %}width="{{ width }}"{% endif %}
    {% if height %}height="{{ height }}"{% endif %}
    {% if srcset %}srcset="{{ srcset|join(', ') }}"{% endif %}
    {% if sizes %}sizes="{{ sizes }}"{% endif %}
    loading="{{ loading|default('lazy') }}"
    decoding="{{ decoding|default('auto') }}"
  />
</figure>
```

---

## 🎨 Styles SCSS

```scss
.ps-image {
  display: block;
  max-width: 100%;

  &__img {
    display: block;
    width: 100%;
    height: auto;
    object-fit: var(--ps-image-object-fit, cover);
    border-radius: var(--ps-image-border-radius, var(--ps-border-radius-none));
  }

  &--fit-cover .ps-image__img { object-fit: cover; }
  &--fit-contain .ps-image__img { object-fit: contain; }

  &--rounded-none .ps-image__img { border-radius: var(--ps-border-radius-none); }
  &--rounded-sm .ps-image__img { border-radius: var(--ps-border-radius-sm); }
  &--rounded-md .ps-image__img { border-radius: var(--ps-border-radius-md); }
  &--rounded-lg .ps-image__img { border-radius: var(--ps-border-radius-lg); }
  &--rounded-full .ps-image__img { border-radius: var(--ps-border-radius-full, 9999px); }

  // Ratio helper (padding technique)
  &__ratio { display: block; width: 100%; }
  &--ratio-16x9 &__ratio { padding-top: 56.25%; }
  &--ratio-1x1 &__ratio { padding-top: 100%; }
  &--ratio-4x3 &__ratio { padding-top: 75%; }

  // Placeholders/Background (optional)
  background-color: var(--ps-color-neutral-100, #F2F4F5);
  overflow: hidden;
}
```

---

## ♿ Accessibilité

- `alt` descriptif requis (éviter images décoratives non-alt ici; utilisez `role="presentation"` au besoin).
- Fournir dimensions `width/height` pour éviter les CLS.
- Ne pas supprimer `loading="lazy"` sauf nécessité de LCP.

---

## 📱 Comportement responsive

- Utiliser `srcset` et `sizes` pour livrer la bonne résolution.
- Le wrapper gère des ratios stables pour layouts.

---

## 🧪 Exemples d'usage

```twig
{# Image responsive simple #}
{% include '@ps_theme/ps-image/ps-image.twig' with {
  src: '/images/hero-800.jpg',
  alt: 'Façade d\'immeuble haussmannien',
  width: 800,
  height: 450,
  srcset: ['/images/hero-400.jpg 400w','/images/hero-800.jpg 800w','/images/hero-1200.jpg 1200w'],
  sizes: '(min-width: 1024px) 960px, 100vw',
  rounded: 'md',
  fit: 'cover',
} %}
```

---

## 📚 Ressources

- Design tokens: `/design/tokens/borders.yml`, `/design/tokens/colors.yml`
- Bonnes pratiques: Core Web Vitals (CLS/LCP)

---

Dernière mise à jour : 28 novembre 2025  
Contributeurs : Design System Team
