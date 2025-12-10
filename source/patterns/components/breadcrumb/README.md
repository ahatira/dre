# Breadcrumb

**Type**: Component / Molecule  
**Level**: Atomic Design  
**Version**: 2.0.0

---

## 📋 Description

Accessible navigation breadcrumb component for hierarchical website structure indication. Uses `::after` pseudo-element with `mask-image` technique for chevron separator (SVG inline via `postcss-inline-svg`).

**Key features**:
- **3-Layer CSS Variables** (17 component-scoped variables)
- **::after separator** with `svg-load()` chevron (no extra `<li>` elements)
- **Webkit prefixes** for Safari/Chrome mask-image compatibility
- **Color inheritance** for separator (follows text color context)
- **3 modifiers** (compact, inverted, no-underline)

---

## 🔧 Usage

```twig
{% include '@components/breadcrumb/breadcrumb.twig' with {
  items: [
    { label: 'Accueil', url: '/' },
    { label: 'Locations', url: '/locations' },
    { label: 'Paris 15ème Arrondissement', url: '/locations/paris-15' },
    { label: 'Appartement familial T4 - Vue sur Tour Eiffel' }
  ]
} only %}
```

### With modifiers

```twig
{# Compact variant (sidebar/footer) #}
{% include '@components/breadcrumb/breadcrumb.twig' with {
  items: breadcrumb_items,
  compact: true
} only %}

{# Inverted theme (dark background) #}
{% include '@components/breadcrumb/breadcrumb.twig' with {
  items: breadcrumb_items,
  inverted: true
} only %}

{# No underline (modern design) #}
{% include '@components/breadcrumb/breadcrumb.twig' with {
  items: breadcrumb_items,
  noUnderline: true
} only %}
```

---

## 📐 Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `items` | `array<{label: string, url?: string}>` | `[]` | **Required**. List of breadcrumb items. Last item is current page (no link). |
| `compact` | `boolean` | `false` | Reduced size variant (12px font, 2px separator margin). |
| `inverted` | `boolean` | `false` | Dark theme with white text (for light backgrounds). |
| `noUnderline` | `boolean` | `false` | Remove underline from links (shows on hover). |
| `attributes` | `Attribute` | `null` | Additional HTML attributes for `<nav>` element. |

---

## 🏗️ BEM Structure

```html
<nav class="ps-breadcrumb" aria-label="Breadcrumb">
  <ol class="ps-breadcrumb__list">
    <li class="ps-breadcrumb__item">
      <a class="ps-breadcrumb__link" href="/">Accueil</a>
      <!-- ::after pseudo-element generates chevron separator here -->
    </li>
    <li class="ps-breadcrumb__item">
      <a class="ps-breadcrumb__link" href="/locations">Locations</a>
    </li>
    <li class="ps-breadcrumb__item" aria-current="page">
      <span class="ps-breadcrumb__link">Current Page</span>
    </li>
  </ol>
</nav>
```

### Classes

| Class | Element | Description |
|-------|---------|-------------|
| `.ps-breadcrumb` | `<nav>` | Block element (defines 17 CSS variables). |
| `.ps-breadcrumb__list` | `<ol>` | Ordered list container (flex layout). |
| `.ps-breadcrumb__item` | `<li>` | Breadcrumb item (generates `::after` chevron if not last). |
| `.ps-breadcrumb__link` | `<a>` / `<span>` | Link or current page text. |

### Modifiers

| Modifier | Description |
|----------|-------------|
| `.ps-breadcrumb--compact` | Reduced font size (12px) and separator margin (2px). |
| `.ps-breadcrumb--inverted` | White text and light hover colors (dark backgrounds). |
| `.ps-breadcrumb--no-underline` | No default underline on links (shows on hover). |

---

## 🎨 Design Tokens

### Layer 2: Component Variables (breadcrumb.css)

```css
.ps-breadcrumb {
  /* Typography */
  --ps-breadcrumb-font-family: var(--font-sans);
  --ps-breadcrumb-font-size: var(--font-size-0);           /* 14px */
  --ps-breadcrumb-font-weight: var(--font-weight-400);
  --ps-breadcrumb-line-height: var(--leading-6);           /* 1.5 */
  
  /* Colors */
  --ps-breadcrumb-color: var(--text-primary);
  --ps-breadcrumb-link-color: var(--text-primary);
  --ps-breadcrumb-link-hover-color: var(--primary);
  
  /* Separator (::after with mask-image) */
  --ps-breadcrumb-separator-color: var(--text-primary);
  --ps-breadcrumb-separator-margin: var(--size-1);         /* 4px */
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

### Global Tokens Used

| Token | Value | Usage |
|-------|-------|-------|
| `--font-sans` | `"BNPP Sans", sans-serif` | Font family |
| `--font-size-0` | `0.875rem` (14px) | Default font size |
| `--font-size--1` | `0.75rem` (12px) | Compact font size |
| `--text-primary` | `var(--gray-900)` | Text color |
| `--primary` | `var(--green-600)` | Hover color |
| `--white` | `hsl(0, 0%, 100%)` | Inverted theme color |
| `--size-1` | `0.25rem` (4px) | Separator margin default |
| `--size-05` | `0.125rem` (2px) | Compact separator margin |
| `--border-size-2` | `2px` | Focus outline width |
| `--duration-fast` | `150ms` | Transition duration |
| `--ease-3` | `cubic-bezier(...)` | Transition timing |

---

## ♿ Accessibility

✅ **WCAG 2.2 AA compliant**

- **Navigation landmark**: `<nav aria-label="Breadcrumb">` identifies region.
- **Current page indicator**: `aria-current="page"` on last `<li>`.
- **Semantic HTML**: `<ol>` (ordered list) indicates hierarchy.
- **Keyboard navigation**: Links accessible via Tab key with visible `focus-visible` outline.
- **Color contrast**: Text colors meet 4.5:1 minimum ratio.
- **No ARIA on separator**: `::after` pseudo-element is purely visual, not in accessibility tree.

**Screen reader experience**:
```
Navigation landmark, Breadcrumb
List, 4 items
Link, Accueil
Link, Locations
Link, Paris 15ème Arrondissement
Current page, Appartement familial T4 - Vue sur Tour Eiffel
```

---

## 🎯 Examples

### Basic Breadcrumb

```twig
{% include '@components/breadcrumb/breadcrumb.twig' with {
  items: [
    { label: 'Accueil', url: '/' },
    { label: 'Locations', url: '/locations' },
    { label: 'Paris 15ème', url: '/locations/paris-15' },
    { label: 'Appartement familial' }
  ]
} only %}
```

### Compact for Sidebar

```twig
{% include '@components/breadcrumb/breadcrumb.twig' with {
  items: [
    { label: 'Accueil', url: '/' },
    { label: 'Bureaux', url: '/bureaux' },
    { label: 'La Défense' }
  ],
  compact: true
} only %}
```

### Inverted on Dark Background

```twig
<div style="background-color: var(--gray-900); padding: var(--size-4);">
  {% include '@components/breadcrumb/breadcrumb.twig' with {
    items: [
      { label: 'Home', url: '/' },
      { label: 'Investment', url: '/investment' },
      { label: 'Senior Living' }
    ],
    inverted: true
  } only %}
</div>
```

### Modern Clean Design

```twig
{% include '@components/breadcrumb/breadcrumb.twig' with {
  items: [
    { label: 'Accueil', url: '/' },
    { label: 'Terrains', url: '/terrains' },
    { label: 'Versailles' }
  ],
  noUnderline: true
} only %}
```

---

## 🔧 Technical Notes

### ::after Separator Implementation

The chevron separator is generated using `::after` pseudo-element with **mask-image** technique:

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

**MUST use correct plugin order** in `postcss.config.js`:

```javascript
export default {
  plugins: [
    postcssImport(),
    postcssNested(),         // ← MUST come BEFORE postcssInlineSvg
    postcssInlineSvg({       // ← Processes svg-load() in nested rules
      paths: ['source/icons-source']
    }),
    // ...
  ]
}
```

**Why**: `postcssNested` must flatten CSS nesting BEFORE `postcssInlineSvg` processes `svg-load()` calls. Wrong order = separator won't render.

### Dependencies

**Icons**:
- `source/icons-source/generic/chevron-right.svg` (required)

**Build tools**:
- `postcss-nested` (CSS nesting support)
- `postcss-inline-svg` (svg-load() transformation)

---

## 📱 Responsive Behavior

- **Flex wrap**: `.ps-breadcrumb__list` uses `display: flex` with automatic wrap on narrow viewports.
- **Compact modifier**: Recommended for mobile (`< 640px`) to save space.
- **Touch targets**: Links have minimum 44x44px touch target (padding adjusted).

---

## 📚 Related Components

- **Button** (`source/patterns/elements/button/`) - Similar modifier pattern (compact, inverted)
- **Badge** (`source/patterns/elements/badge/`) - Semantic color system reference
- **Icon** (`source/patterns/elements/icon/`) - Icon rendering system

---

## 📄 References

- **Design Spec**: `docs/design/molecules/breadcrumb.md`
- **Icon System**: `.github/instructions/icon-system.instructions.md`
- **SEO**: [Breadcrumb structured data (JSON-LD)](https://developers.google.com/search/docs/appearance/structured-data/breadcrumb)
- **ARIA**: [aria-current specification](https://www.w3.org/TR/wai-aria-1.2/#aria-current)
