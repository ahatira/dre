# Breadcrumb (Molecule)

**Niveau Atomic Design** : Molecule / Navigation  
**Cat√©gorie** : Navigational trail  
**Statut** : ‚úÖ Stable  
**Version** : 2.0.0

---

## Ì≥ã Description

Fil d'Ariane accessible pour indiquer la position de la page dans la hi√©rarchie du site, am√©liorer le SEO et l'UX.

**Caract√©ristiques techniques :**
- **3-Layer CSS Variables** : Component-scoped CSS variables avec defaults globaux
- **::after pseudo-element separator** : Chevron g√©n√©r√© via `::after` sur chaque `__item`, pas de `<li>` s√©parateur
- **svg-load() technique** : Chevron SVG inline via `mask-image` avec `postcss-inline-svg`
- **Webkit prefixes** : `-webkit-mask-image` + variants pour compatibilit√© Safari/Chrome
- **Color inheritance** : Le chevron h√©rite de `currentColor` pour suivre la couleur du texte

**Modifiers impl√©ment√©s :**
- `--compact` : Espacement et font r√©duits (12px, margin 2px)
- `--inverted` : Th√®me sombre avec texte blanc pour fonds fonc√©s
- `--no-underline` : Design √©pur√© sans underline par d√©faut (appara√Æt au hover)

---

## Ìæ® Aper√ßu visuel

```
Accueil  ‚Ä∫  Locations  ‚Ä∫  Paris 15e  ‚Ä∫  Appartement familial
         ‚îî‚îÄ ::after (chevron SVG via mask-image)
```

---

## ÌøóÔ∏è Structure BEM (v2 - Simplifi√©e)

```html
<nav class="ps-breadcrumb" aria-label="Breadcrumb">
  <ol class="ps-breadcrumb__list">
    <li class="ps-breadcrumb__item">
      <a class="ps-breadcrumb__link" href="/">Accueil</a>
    </li>
    <li class="ps-breadcrumb__item">
      <a class="ps-breadcrumb__link" href="/locations">Locations</a>
    </li>
    <li class="ps-breadcrumb__item">
      <a class="ps-breadcrumb__link" href="/locations/paris-15">Paris 15e</a>
    </li>
    <li class="ps-breadcrumb__item" aria-current="page">
      <span class="ps-breadcrumb__link">Appartement familial</span>
    </li>
  </ol>
</nav>
```

**‚ö†Ô∏è CHANGEMENT MAJEUR** : Plus de `__separator` `<li>` distinct. Le chevron est g√©n√©r√© par `.ps-breadcrumb__item:not(:last-child)::after`.

### Classes BEM

```
ps-breadcrumb                         // Block principal (d√©finit 17 CSS variables)
  ps-breadcrumb__list                 // <ol> Flex container avec gap
  ps-breadcrumb__item                 // <li> √âl√©ment (g√©n√®re ::after chevron si not :last-child)
  ps-breadcrumb__link                 // <a> ou <span> pour page courante

Modificateurs :
  ps-breadcrumb--compact              // Font 12px, separator-margin 2px
  ps-breadcrumb--inverted             // White text, light hover colors (dark backgrounds)
  ps-breadcrumb--no-underline         // No default underline, shows on hover
```

---

## Ì≥ê Props (Component API)

### Drupal Component YAML Schema

```yaml
$schema: https://git.drupalcode.org/project/drupal/-/raw/10.1.x/core/modules/sdc/src/Component/schema.json
name: 'PS Breadcrumb'
status: stable
group: components
description: 'Fil d'Ariane accessible avec navigation hi√©rarchique et page courante non-cliquable.'

props:
  type: object
  properties:
    items:
      type: array
      title: √âl√©ments du breadcrumb
      description: 'Liste des niveaux de navigation (le dernier est la page courante)'
      items:
        type: object
        properties:
          label:
            type: string
            title: Libell√©
            description: 'Texte affich√© pour cet √©l√©ment'
          url:
            type: string
            title: URL
            description: 'Lien vers la page (optionnel pour le dernier √©l√©ment)'
        required: ['label']
    compact:
      type: boolean
      title: Compact
      description: 'R√©duit la taille (12px font, 2px separator margin)'
      default: false
    inverted:
      type: boolean
      title: Inverted
      description: 'Th√®me sombre avec texte blanc (pour fonds fonc√©s)'
      default: false
    noUnderline:
      type: boolean
      title: No Underline
      description: 'Supprime le underline par d√©faut (appara√Æt au hover)'
      default: false
    attributes:
      type: Drupal\Core\Template\Attribute
      title: HTML Attributes
      description: 'Attributs HTML suppl√©mentaires pour le <nav>'
  required:
    - items
```

---

## Ìæ® Design Tokens (3-Layer System)

### Layer 1 : Global Tokens

```css
/* source/props/fonts.css */
--font-sans: "BNPP Sans", sans-serif;
--font-size-1: 0.875rem;       /* 14px */
--font-weight-400: 400;
--leading-6: 1.5;

/* source/props/colors.css */
--text-primary: var(--gray-900);
--text-secondary: var(--gray-600);

/* source/props/sizes.css */
--size-1: 0.25rem;             /* 4px */
--border-size-2: 2px;
```

### Layer 2 : Component Variables (breadcrumb.css)

```css
.ps-breadcrumb {
  /* Typography */
  --ps-breadcrumb-font-family: var(--font-sans);
  --ps-breadcrumb-font-size: var(--font-size-1);
  --ps-breadcrumb-font-weight: var(--font-weight-400);
  --ps-breadcrumb-line-height: var(--leading-6);
  
  /* Colors */
  --ps-breadcrumb-color: var(--text-primary);
  --ps-breadcrumb-link-color: var(--text-primary);
  --ps-breadcrumb-link-hover-color: var(--primary);
  
  /* Separator (::after pseudo-element) */
  --ps-breadcrumb-separator-color: var(--text-primary);
  --ps-breadcrumb-separator-margin: var(--size-1);
  --ps-breadcrumb-separator-icon-mask: svg-load('generic/chevron-right.svg');
  
  /* Layout */
  --ps-breadcrumb-list-gap: 0;
  
  /* Focus */
  --ps-breadcrumb-focus-outline-width: var(--border-size-2);
  --ps-breadcrumb-focus-outline-color: var(--primary);
  --ps-breadcrumb-focus-outline-offset: var(--border-size-2);
  
  /* Transitions */
  --ps-breadcrumb-transition-duration: var(--duration-fast);
  --ps-breadcrumb-transition-timing: var(--ease-3);
}
```

### Layer 3 : Modifier Overrides

```css
.ps-breadcrumb--compact {
  --ps-breadcrumb-font-size: 0.75rem;        /* 12px */
  --ps-breadcrumb-separator-margin: 0.125rem; /* 2px */
}

.ps-breadcrumb--inverted {
  --ps-breadcrumb-color: var(--white);
  --ps-breadcrumb-link-color: var(--white);
  --ps-breadcrumb-link-hover-color: var(--gray-300);
  --ps-breadcrumb-separator-color: var(--white);
}
```

---

## Ì¥ß Template Twig

```twig
{#
/**
 * @file
 * Breadcrumb navigation component.
 *
 * Available variables:
 * - items: array - List of breadcrumb items with 'label' and optional 'url'
 * - compact: boolean - Reduced size variant
 * - inverted: boolean - Dark theme for light backgrounds
 * - noUnderline: boolean - Remove underline from links
 * - attributes: Attribute - Additional HTML attributes
 */
#}
{% set classes = [
  'ps-breadcrumb',
  compact ? 'ps-breadcrumb--compact' : null,
  inverted ? 'ps-breadcrumb--inverted' : null,
  noUnderline ? 'ps-breadcrumb--no-underline' : null,
] %}

<nav {{ attributes.addClass(classes) }} aria-label="Breadcrumb">
  <ol class="ps-breadcrumb__list">
    {% for item in items %}
      <li class="ps-breadcrumb__item"{{ loop.last ? ' aria-current="page"' : '' }}>
        {% if item.url and not loop.last %}
          <a class="ps-breadcrumb__link" href="{{ item.url }}">{{ item.label }}</a>
        {% else %}
          <span class="ps-breadcrumb__link">{{ item.label }}</span>
        {% endif %}
      </li>
    {% endfor %}
  </ol>
</nav>
```

---

## Ìæ® CSS Implementation (Key Concepts)

### ::after Separator with svg-load()

```css
.ps-breadcrumb__item:not(:last-child)::after {
  content: '';
  display: inline-block;
  width: 1em;
  height: 1em;
  margin-inline: var(--ps-breadcrumb-separator-margin);
  
  /* svg-load() transforms to data URI during PostCSS compilation */
  mask-image: var(--ps-breadcrumb-separator-icon-mask);
  -webkit-mask-image: var(--ps-breadcrumb-separator-icon-mask);
  
  /* Webkit prefixes for Safari/Chrome compatibility */
  mask-repeat: no-repeat;
  -webkit-mask-repeat: no-repeat;
  mask-position: center;
  -webkit-mask-position: center;
  mask-size: contain;
  -webkit-mask-size: contain;
  
  /* Background color = separator color (inherits currentColor context) */
  background-color: var(--ps-breadcrumb-separator-color);
}
```

### PostCSS Plugin Order (CRITICAL)

```javascript
// postcss.config.js
export default {
  plugins: [
    postcssImport(),
    postcssNested(),         // ‚Üê MUST come BEFORE postcssInlineSvg
    postcssInlineSvg({       // ‚Üê Processes svg-load() in nested rules
      paths: ['source/icons-source']
    }),
    // ...
  ]
}
```

---

## ‚ôø Accessibilit√© (WCAG 2.2 AA)

‚úÖ **Navigation landmark** : `<nav aria-label="Breadcrumb">` identifie la r√©gion  
‚úÖ **Current page indicator** : `aria-current="page"` sur le dernier `<li>`  
‚úÖ **Semantic HTML** : `<ol>` (ordered list) indique la hi√©rarchie  
‚úÖ **Keyboard navigation** : Links accessibles au clavier, `focus-visible` outline  
‚úÖ **Color contrast** : Text colors meet 4.5:1 minimum ratio  
‚úÖ **No ARIA on separator** : Le `::after` est purement visuel, pas dans l'arbre d'accessibilit√©

**Screen reader experience** :
```
Navigation landmark, Breadcrumb
List, 4 items
Link, Accueil
Link, Locations
Link, Paris 15e
Current page, Appartement familial
```

---

## Ì≥± Comportement responsive

- **Flex wrap** : `.ps-breadcrumb__list` utilise `display: flex` avec wrap automatique
- **Compact modifier** : Recommand√© pour mobile (`< 640px`) pour √©conomiser l'espace
- **Touch targets** : Links ont minimum 44x44px touch target (padding ajust√©)

---

## Ì∑™ Exemples d'usage

### Basic Usage

```twig
{% include '@components/breadcrumb/breadcrumb.twig' with {
  items: [
    { label: 'Accueil', url: '/' },
    { label: 'Locations', url: '/locations' },
    { label: 'Paris 15e', url: '/locations/paris-15' },
    { label: 'Appartement familial' }
  ]
} only %}
```

### Compact Variant (Sidebar/Footer)

```twig
{% include '@components/breadcrumb/breadcrumb.twig' with {
  items: breadcrumb_items,
  compact: true
} only %}
```

### Inverted Theme (Dark Background)

```twig
<div style="background-color: var(--gray-900); padding: var(--size-4);">
  {% include '@components/breadcrumb/breadcrumb.twig' with {
    items: breadcrumb_items,
    inverted: true
  } only %}
</div>
```

### No Underline (Modern Design)

```twig
{% include '@components/breadcrumb/breadcrumb.twig' with {
  items: breadcrumb_items,
  noUnderline: true
} only %}
```

---

## ÌøóÔ∏è Dependencies

**Atoms requis** : Aucun (standalone molecule)

**Icons** :
- `source/icons-source/generic/chevron-right.svg` (used in mask-image)

**Build tools** :
- `postcss-nested` (CSS nesting support)
- `postcss-inline-svg` (svg-load() transformation)

---

## Ì≥ö Ressources

- **Design tokens** : `/source/props/colors.css`, `/source/props/fonts.css`, `/source/props/sizes.css`
- **SEO** : [Breadcrumb structured data (JSON-LD)](https://developers.google.com/search/docs/appearance/structured-data/breadcrumb)
- **ARIA** : [aria-current specification](https://www.w3.org/TR/wai-aria-1.2/#aria-current)
- **Icon system** : `.github/instructions/icon-system.instructions.md`

---

## Ì¥Ñ Changelog

### v2.0.0 (2025-12-10)
- **BREAKING** : Removed `__separator` `<li>` element, now uses `::after` pseudo-element
- **NEW** : 3-layer CSS variables system (17 component-scoped variables)
- **NEW** : svg-load() technique with mask-image for chevron separator
- **NEW** : Webkit prefixes for Safari/Chrome compatibility
- **NEW** : 3 modifiers (compact, inverted, no-underline)
- **IMPROVED** : Simplified BEM structure (4 classes instead of 6)
- **IMPROVED** : Color inheritance for separator (follows link color context)

### v1.0.0 (Initial)
- Basic breadcrumb with `<li>` separator elements
- Manual ` ‚Ä∫` character as separator
