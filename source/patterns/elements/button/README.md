# Button

Interactive action trigger with semantic variants (neutral default, primary, secondary, success, info, warning, danger), multiple sizes (small/medium/large), icon support (left/right/only), disabled/loading states, outline style, and full-width layout.

## Markup
```html
<!-- Default neutral button (gray) -->
<button class="ps-button">
  <span class="ps-button__label">Button</span>
</button>

<!-- Primary (brand green) -->
<button class="ps-button ps-button--primary">
  <span class="ps-button__label">Cancel</span>
</button>

<!-- Primary with icon -->
<button class="ps-button ps-button--icon-right">
  <span class="ps-button__label">Next</span>
  <span class="ps-button__icon ps-button__icon--right" data-icon="arrow-right" aria-hidden="true"></span>
</button>

<!-- Outline secondary -->
<button class="ps-button ps-button--secondary ps-button--outline">
  <span class="ps-button__label">Cancel</span>
</button>

<!-- Loading state -->
<button class="ps-button ps-button--loading" aria-busy="true">
  <span class="ps-button__spinner" aria-hidden="true"></span>
  <span class="ps-button__label">Loading...</span>
</button>
```

## Props
| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `label` | string (required) | `'Button'` | Button text content. |
| `variant` | enum | `neutral` | Semantic color (neutral \| primary \| secondary \| success \| info \| warning \| danger). |
| `outline` | boolean | `false` | Outline style (border only). |
| `size` | enum | `medium` | Size scale (small \| medium \| large). |
| `url` | string | `''` | Link URL (renders `<a>`). |
| `target` | enum | `_self` | Link target (_self \| _blank). |
| `icon` | string | `''` | Icon name (no "icon-" prefix). |
| `iconPosition` | enum | `right` | Icon placement (left \| right). |
| `disabled` | boolean | `false` | Disabled state. |
| `loading` | boolean | `false` | Loading state with spinner. |
| `fullWidth` | boolean | `false` | Block-level width (100%). |
| `attributes` | Attribute | — | Additional HTML attributes. |

## BEM Structure
- Block: `.ps-button`
- Elements: `.ps-button__label`, `.ps-button__icon`, `.ps-button__spinner`
- Modifiers (variant): `--neutral` (default), `--primary`, `--secondary`, `--success`, `--info`, `--warning`, `--danger`
- Modifiers (style): `--outline`
- Modifiers (size): `--small` `--large` (medium default)
- Modifiers (state): `--disabled` `--loading` `--full-width`
- Modifiers (icon): `--icon-left` `--icon-right` `--icon-only`

## Variant Colors
| Variant | Base Token | Hover Token | Active Token |
|---------|-----------|-------------|--------------|
| primary | `--primary` | `--primary-hover` | `--primary-active` |
| secondary | `--secondary` | `--secondary-hover` | `--secondary-active` |
| neutral | `--neutral` | `--neutral-hover` | `--neutral-active` |
| success | `--success` | `--success-hover` | `--success-active` |
| info | `--info` | `--info-hover` | `--info-active` |
| warning | `--warning` | `--warning-hover` | `--warning-active` |
| danger | `--danger` | `--danger-hover` | `--danger-active` |

## CSS Variables System (3 Layers - Bootstrap 5 Inspired)

### Layer 2: Component-Scoped Variables (Customizable)

The button component uses **component-scoped CSS variables** that can be overridden in context:

```css
.ps-button {
  /* Spacing */
  --ps-button-gap: var(--size-2);
  --ps-button-padding-y: var(--size-2);
  --ps-button-padding-x: var(--size-4);
  --ps-button-height: var(--size-9);
  
  /* Colors */
  --ps-button-bg: var(--primary);
  --ps-button-color: var(--primary-text);
  --ps-button-hover-bg: var(--primary-hover);
  --ps-button-active-bg: var(--primary-active);
  --ps-button-spinner-color: var(--white);
  
  /* States */
  --ps-button-disabled-opacity: 0.5;
  --ps-button-focus-outline-width: var(--border-size-2);
  --ps-button-focus-outline-color: var(--border-focus);
  
  /* Transitions */
  --ps-button-transition-duration: var(--duration-fast);
  --ps-button-transition-timing: var(--ease-4);
}
```

### Customization Examples

**Contextual Theming (Layer 3):**
```css
/* Sidebar buttons with different colors */
.sidebar .ps-button {
  --ps-button-bg: var(--secondary);
  --ps-button-hover-bg: var(--secondary-hover);
}
```

**Runtime Customization (JavaScript):**
```javascript
button.style.setProperty('--ps-button-bg', 'var(--success)');
```

**Size Overrides:**
```css
.ps-button--small {
  --ps-button-height: 2.12375rem;
  --ps-button-padding-y: var(--size-105);
  --ps-button-font-size: var(--size-305);
}
```

**Benefits:**
- **Cascade control**: Variants override variables, not properties directly.
- **Runtime customization**: JavaScript can modify variables without specificity wars.
- **Contextual theming**: Parent selectors can adjust button colors/sizes for specific contexts.

## Design Tokens Used (Layer 1)
- Colors: `--primary`, `--secondary`, `--neutral`, `--success`, `--info`, `--warning`, `--danger` (base/hover/active/text variants)
- Sizing: `--size-2` (gap/padding-v), `--size-4` (padding-h), `--size-9` (height md), `--size-10` (height lg)
- Typography: `--font-sans`, `--font-weight-400`, `--size-305` (14px small), `--size-4` (16px md), `1.125rem` (18px lg)
- Border: `--border-size-2` (outline + focus)
- Transition: `--duration-fast` (0.15s), `--ease-4` (cubic-bezier)

## Accessibility
- **Button vs Link**: `<button>` by default; `<a>` when url provided (semantic correctness).
- **Disabled**: `disabled` + `aria-disabled="true"` on button; `pointer-events: none` on link.
- **Loading**: `aria-busy="true"` announces state to screen readers.
- **Icon-only**: Label visually hidden but present for screen readers (never omit label prop).
- **Focus**: `:focus-visible` outline 2px for keyboard navigation.
- **Touch target**: Minimum 36px height (WCAG 2.2 Level A compliant).
- **Contrast**: All variants meet WCAG AA minimum (verified).

## Usage Examples
```twig
{# Primary action (green) #}
{{ include('@elements/button/button.twig', { label: 'Submit', variant: 'primary' }) }}

{# Neutral/default (gray) #}
{{ include('@elements/button/button.twig', { label: 'Cancel', variant: 'neutral' }) }}

{# Outline without variant -> neutral by default #}
{{ include('@elements/button/button.twig', { label: 'Outline default', outline: true }) }}

{# Secondary outline (pink) #}
{{ include('@elements/button/button.twig', { label: 'Learn more', variant: 'secondary', outline: true }) }}

{# Icon left #}
{{ include('@elements/button/button.twig', { label: 'Download', variant: 'success', icon: 'download', iconPosition: 'left' }) }}

{# Loading state #}
{{ include('@elements/button/button.twig', { label: 'Saving...', variant: 'primary', loading: true, disabled: true }) }}

{# Full width (forms) #}
{{ include('@elements/button/button.twig', { label: 'Submit form', variant: 'primary', fullWidth: true }) }}
```

## Do & Don't
| Do | Don't |
|----|-------|
| Use primary for main action (one per section) | Stack multiple primaries together |
| Use semantic colors for context (success/danger) | Use generic colors for critical actions |
| Provide label text always (even if icon-only) | Omit label or use only icon without accessible text |
| Use outline for secondary actions | Mix outline and filled of same variant next to each other |

## Migration Notes
- Small size height corrected from 33.98px to 34px (simplified token).
- Transition uses cubic-bezier for smooth animation.
- Loading state uses spinner overlay with preserved layout (no content shift).

## Audit Checklist
- No hardcoded colors/sizes (all via tokens).
- Label prop always present (required).
- Each variant has hover/active states defined.
- Focus outline visible for keyboard users.
- Icon marked aria-hidden.
- README in English.
