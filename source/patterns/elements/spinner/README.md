# Spinner

**Version**: 1.0.0  
**Status**: ✅ Stable  
**Type**: Atom / Element  
**Category**: Feedback / Loading

Animated loading indicator for asynchronous states (data loading, form submission, etc.). Three visual variants available: circular (default), dots, and bars. Full semantic color support (default, primary, secondary, success, info, warning, danger, dark, light, white).

---

## Props

| Prop | Type | Default | Required | Description |
|------|------|---------|----------|-------------|
| `variant` | `string` | `'circular'` | ❌ | Spinner type: `circular` \| `dots` \| `bars` |
| `size` | `string` | `'md'` | ❌ | Size: `xs` (16px) \| `sm` (24px) \| `md` (32px) \| `lg` (48px) \| `xl` (64px) \| `xxl` (80px) |
| `color` | `string` | `'default'` | ❌ | Color: `default` \| `primary` \| `secondary` \| `success` \| `info` \| `warning` \| `danger` \| `dark` \| `light` \| `white` |
| `text` | `string` | `'Loading...'` | ❌ | Screen reader text (announced but visually hidden) |
| `centered` | `boolean` | `false` | ❌ | Center in parent container (position absolute) |
| `attributes` | `Attribute` | — | ❌ | Additional Drupal HTML attributes |

---

## BEM Structure

```
.ps-spinner                      ← Container with role="status"
  .ps-spinner__svg               ← SVG container (circular only)
  .ps-spinner__circle            ← Animated circle (circular)
  .ps-spinner__dot               ← Animated dot (dots, 3x)
  .ps-spinner__bar               ← Animated bar (bars, 3x)
  .ps-spinner__text              ← Visually hidden text (a11y)

Modifiers:
  .ps-spinner--circular          ← Circular rotating variant (default, no class needed)
  .ps-spinner--dots              ← 3 bouncing dots variant
  .ps-spinner--bars              ← 3 stretching bars variant
  .ps-spinner--xs|sm|md|lg|xl|xxl ← Sizes
  .ps-spinner--default|primary|secondary|success|info|warning|danger|dark|light|white ← Colors
  .ps-spinner--centered          ← Absolute centering
```

---

## Component Variables (Layer 2)

Defined in `spinner.css`; override any of these in context or custom CSS:

| Variable | Default (references) | Purpose |
|----------|----------------------|---------|
| `--ps-spinner-size` | `var(--size-8)` | Spinner dimension (width & height) - 32px default |
| `--ps-spinner-color` | `var(--gray-500)` | Text/stroke color |
| `--ps-spinner-display` | `inline-flex` | Display type |
| `--ps-spinner-align-items` | `center` | Vertical alignment |
| `--ps-spinner-justify-content` | `center` | Horizontal alignment |

### Size Modifiers (Override --ps-spinner-size)

| Size | Modifier Class | Dimension | Use Case |
|------|----------------|-----------|----------|
| `xs` | `.ps-spinner--xs` | 16px (`--size-4`) | Button loading states |
| `sm` | `.ps-spinner--sm` | 24px (`--size-6`) | Inline/compact spaces |
| `md` | `.ps-spinner--md` | 32px (`--size-8`) | **Default** - standard usage |
| `lg` | `.ps-spinner--lg` | 48px (`--size-12`) | Centered loaders |
| `xl` | `.ps-spinner--xl` | 64px (`--size-16`) | Full page loading |
| `xxl` | `.ps-spinner--xxl` | 80px (`--size-20`) | Hero/splash screens |

### Color Modifiers (Override --ps-spinner-color)

| Color | Modifier Class | Token | Use Case |
|-------|----------------|-------|----------|
| `default` | — (no class) | `var(--gray-500)` | Neutral/default state |
| `primary` | `.ps-spinner--primary` | `var(--primary)` | BNP brand green |
| `secondary` | `.ps-spinner--secondary` | `var(--secondary)` | Pink accent |
| `success` | `.ps-spinner--success` | `var(--success)` | Success/confirmation |
| `info` | `.ps-spinner--info` | `var(--info)` | Informational |
| `warning` | `.ps-spinner--warning` | `var(--warning)` | Warning states |
| `danger` | `.ps-spinner--danger` | `var(--danger)` | Error/destructive |
| `dark` | `.ps-spinner--dark` | `var(--gray-800)` | Dark backgrounds (light mode) |
| `light` | `.ps-spinner--light` | `var(--gray-200)` | Light backgrounds |
| `white` | `.ps-spinner--white` | `var(--white)` | Dark background overlays |

### Variant Modifiers

| Variant | Modifier Class | Animation | Use Case |
|---------|----------------|-----------|----------|
| `circular` | — (no class) | Rotating SVG circle | **Default** - most common |
| `dots` | `.ps-spinner--dots` | 3 bouncing dots | Subtle alternative |
| `bars` | `.ps-spinner--bars` | 3 stretching bars | Visual variety |

### Animations (Defined in CSS)

| Animation | Duration | Easing | Purpose |
|-----------|----------|--------|---------|
| `ps-spinner-rotate` | 1s | linear | SVG rotation (circular) |
| `ps-spinner-dash` | 1.5s | ease-in-out | Dash animation (circular stroke) |
| `ps-spinner-bounce` | 1.4s | ease-in-out | Bounce effect (dots) |
| `ps-spinner-stretch` | 1.2s | ease-in-out | Stretch effect (bars) |

---

## Design Tokens Used

### Layer 1: Global Primitives (from `source/props/*.css`)

- **Colors**: `var(--gray-500)`, `var(--gray-200)`, `var(--gray-800)`, `var(--white)`
- **Semantic Colors**: `var(--primary)`, `var(--secondary)`, `var(--success)`, `var(--info)`, `var(--warning)`, `var(--danger)`
- **Sizes**: `var(--size-4)` through `var(--size-20)`

### Layer 2: Component Scoped (from `spinner.css`)

- `--ps-spinner-size`: Dimension variable
- `--ps-spinner-color`: Text/stroke color
- `--ps-spinner-display`: Display type
- `--ps-spinner-align-items`: Alignment
- `--ps-spinner-justify-content`: Alignment

### Layer 3: Context Overrides (in custom CSS)

Override `--ps-spinner-size`, `--ps-spinner-color`, or other variables in parent CSS for context-specific styling.

---

## 🎨 Semantic Colors Reference

The spinner uses **semantic tokens** for all colors to ensure consistency with brand guidelines:

**Primary Colors**:
- `default` → `var(--gray-500)` (neutral gray)
- `primary` → `var(--primary)` (BNP brand green #00915A)
- `secondary` → `var(--secondary)` (pink accent #A12B66)

**Contextual Colors**:
- `success` → `var(--success)` (validation states)
- `info` → `var(--info)` (informational content)
- `warning` → `var(--warning)` (caution states)
- `danger` → `var(--danger)` (error/destructive actions)

**Light/Dark Variants**:
- `dark` → `var(--gray-800)` (dark backgrounds in light mode)
- `light` → `var(--gray-200)` (light backgrounds)
- `white` → `var(--white)` (overlays on dark backgrounds)

## Usage Examples

### Basic (Default)

```twig
{% include '@elements/spinner/spinner.twig' with {
  variant: 'circular',
  size: 'md',
  color: 'default',
} %}
```

### Variants

```twig
{# Circular (default) #}
{% include '@elements/spinner/spinner.twig' with {
  variant: 'circular',
} %}

{# Dots #}
{% include '@elements/spinner/spinner.twig' with {
  variant: 'dots',
} %}

{# Bars #}
{% include '@elements/spinner/spinner.twig' with {
  variant: 'bars',
} %}
```

### Sizes

```twig
{# Extra small (16px) #}
{% include '@elements/spinner/spinner.twig' with {
  size: 'xs',
} %}

{# Large (48px) #}
{% include '@elements/spinner/spinner.twig' with {
  size: 'lg',
} %}

{# Extra extra large (80px) #}
{% include '@elements/spinner/spinner.twig' with {
  size: 'xxl',
} %}
```

### Colors

```twig
{# Default (gray) #}
{% include '@elements/spinner/spinner.twig' with {
  color: 'default',
} %}

{# Primary (BNP green) #}
{% include '@elements/spinner/spinner.twig' with {
  color: 'primary',
} %}

{# Secondary (pink) #}
{% include '@elements/spinner/spinner.twig' with {
  color: 'secondary',
} %}

{# Success (green) #}
{% include '@elements/spinner/spinner.twig' with {
  color: 'success',
} %}

{# Info (blue) #}
{% include '@elements/spinner/spinner.twig' with {
  color: 'info',
} %}

{# Warning (yellow) #}
{% include '@elements/spinner/spinner.twig' with {
  color: 'warning',
} %}

{# Danger (red) #}
{% include '@elements/spinner/spinner.twig' with {
  color: 'danger',
} %}

{# Dark (neutral dark) #}
{% include '@elements/spinner/spinner.twig' with {
  color: 'dark',
} %}

{# Light (neutral light) #}
{% include '@elements/spinner/spinner.twig' with {
  color: 'light',
} %}

{# White (for dark backgrounds) #}
{% include '@elements/spinner/spinner.twig' with {
  color: 'white',
} %}
```

### Centered in Container

```twig
<div style="position: relative; height: 200px;">
  {% include '@elements/spinner/spinner.twig' with {
    centered: true,
    size: 'lg',
    text: 'Loading page content...',
  } %}
</div>
```

### Inline with Button

```twig
<button class="ps-button ps-button--primary" disabled>
  {% include '@elements/spinner/spinner.twig' with {
    size: 'xs',
    color: 'white',
  } %}
  Submitting...
</button>
```

---

## Real-World Use Cases

1. **Page loading** — Centered spinner during initial page load
2. **Form submission** — Inline in submit button
3. **Data loading** — In table or list during fetch
4. **File upload** — Indicates upload progress
5. **Asynchronous search** — Next to search field
6. **Navigation** — During page/route transition

---

## Accessibility

### ✅ WCAG 2.2 Compliance

- **role="status"** — Announces state changes to screen readers
- **aria-live="polite"** — Non-intrusive announcement (waits for user to finish current task)
- **Hidden text** — Visually hidden but announced (sr-only pattern)
- **No focus** — Non-interactive spinner, no tabindex

### Best Practices

1. **Always include text** — The `text` prop is announced to screen readers
2. **Clear context** — Text should describe what's loading ("Loading search results...")
3. **Sufficient contrast** — All colors meet WCAG AA (4.5:1)
4. **Reduced motion** — Respect `prefers-reduced-motion` (to implement if needed)

### Implementation Checklist

- [x] `role="status"` present
- [x] `aria-live="polite"` present
- [x] Descriptive text provided
- [x] Text visually hidden (sr-only)
- [x] No tabindex (non-interactive)
- [x] Sufficient contrast for all colors

---

## Animations

### Circular
- **Rotation**: 1s linear infinite (SVG circle rotates)
- **Dash**: 1.5s ease-in-out infinite (animated stroke-dasharray)

### Dots
- **Bounce**: 1.4s ease-in-out infinite both
- Delays per dot: -0.32s, -0.16s, 0s (wave effect)

### Bars
- **Stretch**: 1.2s ease-in-out infinite
- Delays per bar: -0.24s, -0.12s, 0s (wave effect)

---

## Behavior

### Display
- `display: inline-flex` — Integrates naturally inline or block depending on context
- Centered via `align-items: center` and `justify-content: center`

### Centered Variant
- `position: absolute` + `top: 50%` + `left: 50%`
- `transform: translate(-50%, -50%)` for perfect centering
- Requires parent with `position: relative`

### Color Inheritance
- Uses `currentColor` for animated elements
- Allows color control via color modifier or parent CSS

---

## Browser Support

✅ All modern browsers (Chrome, Firefox, Safari, Edge)  
✅ CSS animations (keyframes)  
✅ SVG support (for circular)  
✅ Screen readers (NVDA, JAWS, VoiceOver)

---

## Performance

- **GPU animations** — Uses `transform` and `opacity` (no layout reflow)
- **Lightweight SVG** — Circular uses a single `<circle>` SVG element
- **No JavaScript** — 100% CSS animations

---

## Related Components

- **Button** — Inline spinner in loading button
- **Progress Bar** — Alternative for determinate progress
- **Skeleton** — Alternative for content loading

---

## Testing

### Manual Testing

1. Verify smooth animation in all browsers
2. Test with screen reader (text announced correctly)
3. Verify color contrast with tools (Wave, axe DevTools)
4. Test centered variant in different containers

### Automated Testing

```javascript
// Playwright example
await expect(page.locator('.ps-spinner')).toHaveAttribute('role', 'status');
await expect(page.locator('.ps-spinner')).toHaveAttribute('aria-live', 'polite');
await expect(page.locator('.ps-spinner__text')).toHaveText('Loading...');
```

---

## Technical Notes

- **Minimal HTML**: Modifier classes added only if different from default
- **currentColor**: Allows color inheritance from parent or modifier
- **sr-only pattern**: Text hidden but accessible (position absolute + clip)
- **Animation delays**: Create wave/cascade effect for dots and bars
- **SVG viewBox**: Allows perfect scaling of circular variant

---

## Resources

- [WAI-ARIA - role="status"](https://www.w3.org/TR/wai-aria-1.2/#status)
- [WebAIM - Screen Reader Testing](https://webaim.org/articles/screenreader_testing/)
- [CSS Animations Performance](https://web.dev/animations-guide/)
