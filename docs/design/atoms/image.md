# Image (Atom)

**Niveau Atomic Design** : Atom / Media  
**Catégorie** : Media  
**Statut** : 🚧 Draft  
**Version** : 1.0.0

---

## 📋 Description

Image responsive avec `loading=lazy` par défaut, support de `srcset/sizes`, `object-fit` et coins arrondis. Ce composant gère uniquement l'élément `<img>` et ses variantes. Pour une structure avec légende, voir le composant "Figure".

---

## 🎨 Aperçu visuel

```
[ Image responsive ]  max-width: 100%, height: auto
[ Image avec object-fit: cover ]
[ Image avec coins arrondis (avatar) ]
```

---

## 🏗️ Structure BEM

```html
<img class="ps-image ps-image--rounded-md ps-image--fit-cover" 
     src="/path/img.jpg" 
     alt="Description" 
     loading="lazy" 
     width="800" 
     height="450" />
```

### Classes BEM

```
ps-image                         // Block (img element)

Modificateurs :
  ps-image--fit-cover|contain    // Object-fit
  ps-image--rounded-none|sm|md|lg|full // Border-radius
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
      enum: ['cover','contain','fill','none','scale-down']
      default: 'none'
    rounded:
      type: string
      title: Rayon
      enum: ['none','sm','md','lg','full']
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

- **Object-fit**: `none` (défaut), `cover`, `contain`, `fill`, `scale-down`
- **Border-radius**: `none` (défaut), `sm`, `md`, `lg`, `full` (cercle/avatar)

---

## 🎨 Design Tokens

- **Border-radius**: `--border-radius-none|sm|md|lg|full`
- **Spacing** (si nécessaire): `--size-*` pour marges/padding externes

**Note**: Les tokens d'`object-fit` sont gérés via modificateurs CSS natifs (pas de token custom nécessaire).

---

## 🔧 Template Twig

```twig
{#
 * Template for Image atom.
 * Variables:
 * - src (string), alt (string), width (int), height (int)
 * - srcset (array<string>), sizes (string)
 * - loading ('lazy'|'eager'), decoding ('async'|'auto'|'sync')
 * - fit ('none'|'cover'|'contain'|'fill'|'scale-down')
 * - rounded ('none'|'sm'|'md'|'lg'|'full')
 * - attributes: Attribute
 #}

{% set fit = fit|default('none') %}
{% set rounded = rounded|default('none') %}

{% set classes = [
  'ps-image',
  fit != 'none' ? 'ps-image--fit-' ~ fit,
  rounded != 'none' ? 'ps-image--rounded-' ~ rounded
] %}

<img
  {{ attributes.addClass(classes) }}
  src="{{ src }}"
  alt="{{ alt }}"
  {% if width %}width="{{ width }}"{% endif %}
  {% if height %}height="{{ height }}"{% endif %}
  {% if srcset %}srcset="{{ srcset|join(', ') }}"{% endif %}
  {% if sizes %}sizes="{{ sizes }}"{% endif %}
  loading="{{ loading|default('lazy') }}"
  decoding="{{ decoding|default('auto') }}"
/>
```

---

## 🎨 Styles SCSS

```scss
.ps-image {
  display: block;
  max-width: 100%;
## 🎨 Styles CSS

```css
.ps-image {
  max-width: 100%;
  height: auto;
  vertical-align: middle;

  /* Object-fit modifiers */
  &--fit-cover { object-fit: cover; }
  &--fit-contain { object-fit: contain; }
  &--fit-fill { object-fit: fill; }
  &--fit-none { object-fit: none; }
  &--fit-scale-down { object-fit: scale-down; }

  /* Border-radius modifiers */
  &--rounded-sm { border-radius: var(--border-radius-sm); }
  &--rounded-md { border-radius: var(--border-radius-md); }
  &--rounded-lg { border-radius: var(--border-radius-lg); }
  &--rounded-full { border-radius: var(--border-radius-full); }
}
```alt` descriptif requis (éviter images décoratives non-alt ici; utilisez `role="presentation"` au besoin).
- Fournir dimensions `width/height` pour éviter les CLS.
- Ne pas supprimer `loading="lazy"` sauf nécessité de LCP.

---

## 📱 Comportement responsive

- Utiliser `srcset` et `sizes` pour livrer la bonne résolution.
- Le wrapper gère des ratios stables pour layouts.

## ♿ Accessibilité

- **`alt` obligatoire** : Description textuelle pour les lecteurs d'écran.
- **Images décoratives** : Utiliser `alt=""` (pas `role="presentation"` sur `<img>`).
- **Dimensions** : Fournir `width` et `height` pour éviter les CLS (Cumulative Layout Shift).
- **Lazy loading** : `loading="lazy"` par défaut, sauf pour images above-the-fold (LCP).

---

## 📱 Comportement responsive

- **Responsive par défaut** : `max-width: 100%; height: auto;` appliqué sur `.ps-image`.
- **Srcset/Sizes** : Utiliser `srcset` et `sizes` pour servir la résolution optimale selon le viewport.
- **Aspect ratio** : Pour maintenir un ratio fixe, utiliser le composant "Figure" avec wrapper dédié.ro-800.jpg 800w','/images/hero-1200.jpg 1200w'],
  sizes: '(min-width: 1024px) 960px, 100vw',
## 🧪 Exemples d'usage

```twig
{# Image responsive simple #}
{% include '@elements/image/image.twig' with {
  src: '/images/hero-800.jpg',
  alt: 'Façade d\'immeuble haussmannien',
  width: 800,
  height: 450
} %}

{# Image avec srcset/sizes #}
{% include '@elements/image/image.twig' with {
  src: '/images/hero-800.jpg',
  alt: 'Façade d\'immeuble haussmannien',
  width: 800,
  height: 450,
  srcset: [
    '/images/hero-400.jpg 400w',
    '/images/hero-800.jpg 800w',
    '/images/hero-1200.jpg 1200w'
  ],
  sizes: '(min-width: 1024px) 960px, 100vw'
} %}

{# Image avec object-fit et border-radius #}
{% include '@elements/image/image.twig' with {
  src: '/images/avatar.jpg',
  alt: 'Portrait de Marie Dupont',
  width: 200,
  height: 200,
  fit: 'cover',
  rounded: 'full'
} %}

{# Image décorative #}
{% include '@elements/image/image.twig' with {
  src: '/images/pattern.svg',
  alt: '',
  width: 100,
  height: 100
} %}
```

---

## 📚 Ressources

- **Design tokens** : `source/props/borders.css`
- **Bonnes pratiques** : [Core Web Vitals](https://web.dev/vitals/) (CLS/LCP)
- **Bootstrap référence** : [Bootstrap Images](https://getbootstrap.com/docs/5.3/content/images/)
- **Composant Figure** : Voir `docs/design/atoms/figure.md` (à créer)

---

Dernière mise à jour : 2 décembre 2025  
Contributeurs : Design System Team