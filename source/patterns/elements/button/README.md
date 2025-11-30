# Button

Interactive action trigger with semantic variants and accessible states.

## Overview
- **Purpose**: Primary call-to-action element for user interactions.
- **Variants**: 8 semantic colors (primary, secondary, success, info, warning, danger, dark, light).
- **Styles**: filled (default), outline (transparent bg + border).
- **Sizes**: small (34px), medium (36px, default), large (40px).
- **Icons**: optional left/right positioning; icon-only mode supported.
- **States**: disabled (50% opacity), loading (spinner overlay).
- **Layout**: fullWidth for block-level display.
- **Accessibility**: renders `<button>` or `<a>` based on url; focus visible; aria attributes for states.

## Markup
```html
<!-- Default button -->
<button class="ps-button">
  <span class="ps-button__label">Button</span>
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
| `variant` | enum | `primary` | Semantic color (primary \| secondary \| success \| info \| warning \| danger \| dark \| light). |
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
- Modifiers (variant): `--secondary` `--success` `--info` `--warning` `--danger` `--dark` `--light` (primary default)
- Modifiers (style): `--outline`
- Modifiers (size): `--small` `--large` (medium default)
- Modifiers (state): `--disabled` `--loading` `--full-width`
- Modifiers (icon): `--icon-left` `--icon-right` `--icon-only`

## Variant Colors
| Variant | Base Token | Hover Token | Active Token |
|---------|-----------|-------------|--------------|
| primary | `--btn-primary` | `--btn-primary-hover` | `--btn-primary-active` |
| secondary | `--btn-secondary` | `--btn-secondary-hover` | `--btn-secondary-active` |
| success | `--btn-success` | `--btn-success-hover` | `--btn-success-active` |
| info | `--btn-info` | `--btn-info-hover` | `--btn-info-active` |
| warning | `--btn-warning` | `--btn-warning-hover` | `--btn-warning-active` |
| danger | `--btn-danger` | `--btn-danger-hover` | `--btn-danger-active` |
| dark | `--btn-dark` | `--btn-dark-hover` | `--btn-dark-active` |
| light | `--btn-light` | `--btn-light-hover` | `--btn-light-active` |

## Design Tokens Used
- Colors: `--btn-*` variant tokens (base/hover/active), `--white` (text on colored)
- Sizing: `--size-2` (gap/padding-v), `--size-4` (padding-h), `--size-9` (height md), `--size-10` (height lg)
- Typography: `--font-sans`, `--font-weight-400`, `--size-305` (14px small), `--size-4` (16px md), `1.125rem` (18px lg)
- Border: `--border-size-2` (outline + focus)
- Transition: `cubic-bezier(0.4, 0.0, 0.2, 1)` 150ms

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
{# Primary action #}
{{ include('@elements/button/button.twig', { label: 'Submit', variant: 'primary' }) }}

{# Secondary outline #}
{{ include('@elements/button/button.twig', { label: 'Cancel', variant: 'secondary', outline: true }) }}

{# Link button #}
{{ include('@elements/button/button.twig', { label: 'Learn more', variant: 'primary', url: '/about', icon: 'arrow-right' }) }}

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
