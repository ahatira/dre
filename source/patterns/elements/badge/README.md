# Badge

Compact semantic label / status indicator with bold text and saturated backgrounds. Uses relative sizing (em units) to scale proportionally with parent font-size for flexible typography integration.

## Overview
- **Purpose**: Highlight statuses, categories, counts or meta labels inline.
- **Variants (color)**: `primary` (default) `secondary` `success` `danger` `warning` `info` `light` `dark` `gold`.
- **Sizes**: `small` `medium` (default) `large` — scale proportionally with parent element.
- **Shape**: Base rounded radius, optional `pill` modifier for fully circular ends.
- **Icon**: Decorative glyph before text; never the sole accessible label (text always required).
- **Link**: Provide `url` to render `<a>` with focus outline.
- **Accessibility**: Bold text + saturated backgrounds ensure high contrast; icon is decorative (`aria-hidden` via icon system); focus visible only for interactive links.
- **Markup principle**: No modifier classes for default values; each modifier works independently.
- **Relative sizing**: Badge scales with parent font-size using em units (e.g., larger in headings, smaller in body text).

## Markup
```twig
{# Primary badge (default) #}
<span class="ps-badge">New</span>

{# Secondary badge with icon #}
<span class="ps-badge ps-badge--secondary">
  <span class="ps-badge__icon" data-icon="check" aria-hidden="true"></span>
  <span class="ps-badge__text">Verified</span>
</span>

{# In heading (scales proportionally) #}
<h2>Property Title <span class="ps-badge ps-badge--success">Available</span></h2>

{# Pill link badge #}
<a href="#" class="ps-badge ps-badge--info ps-badge--pill">Learn more</a>
```

## Props
| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `text` | string (required) | `"Badge"` | Visible label text. |
| `icon` | string | `''` | Decorative icon name (no `icon-` prefix). |
| `color` | enum | `primary` | Semantic color variant (saturated backgrounds). |
| `size` | enum | `medium` | Size scale in relative em units (scales with parent font-size). |
| `pill` | boolean | `false` | Fully rounded pill shape. |
| `url` | string | `''` | Link URL (renders `<a>`). |
| `attributes` | Attribute | — | Additional HTML attributes merged onto root element. |

## BEM Structure
- Block: `.ps-badge`
- Elements: `.ps-badge__icon`, `.ps-badge__text`
- Modifiers: `--small` `--large` `--pill` color variants listed above

## Color Variants (Semantic)
| Variant | Background Token | Text Token | Use Case |
|---------|------------------|-----------|----------|
| primary | `--primary` | `--white` | Brand actions, featured items |
| secondary | `--secondary` | `--white` | Secondary emphasis |
| success | `--success` | `--white` | Positive states, available |
| danger | `--danger` | `--white` | Errors, sold, unavailable |
| warning | `--warning` | `--gray-900` | Cautions, pending |
| info | `--info` | `--white` | Informational, new |
| light | `--light` | `--dark` | Light backgrounds, neutral |
| dark | `--dark` | `--white` | Dark backgrounds, emphasis |
| gold | `--gold` | `--white` | Premium, exclusive features |

## Design Tokens Used
- **Spacing (relative)**: `0.25em`, `0.35em`, `0.5em`, `0.65em`, `0.85em` (scale with parent font-size)
- **Typography (relative)**: `0.65em`, `0.75em`, `0.875em`, `1em` (scale with parent font-size)
- **Font weight**: `--font-weight-700` (bold for visibility)
- **Colors (semantic - saturated)**: `--primary` `--secondary` `--success` `--danger` `--warning` `--info` `--light` `--dark` `--gold`
- **Colors (text)**: `--white` (on dark backgrounds), `--gray-900` (on warning), `--dark` (on light)
- **Radius**: `--radius-2` `--radius-round`
- **Animation**: `--duration-fast` `--ease-3`

## Component-Scoped Variables
Badge uses component-scoped CSS variables with relative units:

```css
.ps-badge {
  --ps-badge-bg: var(--primary);
  --ps-badge-color: var(--white);
  --ps-badge-padding-y: 0.35em;     /* Relative to badge font-size */
  --ps-badge-padding-x: 0.65em;     /* Relative to badge font-size */
  --ps-badge-font-size: 0.75em;     /* Relative to parent font-size */
  --ps-badge-font-weight: var(--font-weight-700);
  --ps-badge-radius: var(--radius-2);
  /* ... */
}
```

Modifiers only change variables, enabling runtime customization and proportional scaling.
## Accessibility
- Bold text (font-weight 700) with saturated backgrounds ensure high visibility and contrast.
- Icon rendered via `data-icon` attribute with `aria-hidden="true"` (always decorative in badges).
- Focus outline applied only when component is an interactive link (`<a>`).
- All color variants maintain WCAG AA contrast ratios:
  - Primary/Secondary/Success/Danger/Info/Dark: White text on saturated background ≥ **4.5:1** ✅
  - Warning: Dark text (--gray-900) on yellow background ≥ **4.5:1** ✅
  - Light: Dark text (--dark) on light background ≥ **4.5:1** ✅ceeds 4.5:1)
  - Info: Blue 700 on Blue 100 = **5.2:1** ✅
  - Success: Green 700 on Green 100 = **5.4:1** ✅
## Usage Examples
```twig
{# Success status #}
{{ include('@ps_theme/badge/badge.twig', { text: 'Available', color: 'success', icon: 'check' }) }}

{# Warning #}
{{ include('@ps_theme/badge/badge.twig', { text: 'Pending', color: 'warning' }) }}

{# In heading (scales automatically) #}
<h1>Property Title {{ include('@ps_theme/badge/badge.twig', { text: 'New', color: 'info' }) }}</h1>

{# Pill with icon #}
{{ include('@ps_theme/badge/badge.twig', { text: 'Featured', color: 'primary', pill: true, icon: 'award' }) }}

{# Counter in button #}
<button>Notifications {{ include('@ps_theme/badge/badge.twig', { text: '4', color: 'danger', size: 'small' }) }}</button>
```Pill with icon #}
## Do & Don't
| Do | Don't |
|----|-------|
| Use semantic color names (primary, success, etc.) | Hardcode hex/HSL color values |
| Keep text short (1–2 words) | Rely on icon only for meaning |
| Use `pill` only when rounded shape intended | Combine multiple shape modifiers |
| Provide `url` for interactive link style | Add link styles to non-link elements |
| Leverage relative sizing in headings/buttons | Force fixed pixel sizes |
## Migration Notes
- **Icon System** (December 2025): Migrated from `data-icon` to Icon component (`@elements/icon`), enabling full SVG sprite support and consistent icon rendering across the theme.
- **Icon Composition** (v4.0.0+): Uses Icon component with `attributes.addClass()` instead of deprecated `baseClass` parameter (removed in v4.0.0).
- **Relative Sizing** (v4.1.0+): Badge now uses relative `em` units instead of fixed `rem` units, enabling proportional scaling with parent font-size (larger in headings, smaller in body text).
- **Color System** (v4.1.0+): Standardized to 9 semantic colors with saturated backgrounds:
  - Removed: `default` (use `light` or `primary` instead)
  - Added: `light`, `dark` (standard semantic palette)
  - Kept: `gold` (premium/exclusive accent)
  - Changed: All colors except `light` now use saturated backgrounds with white text for maximum visibility
- **Typography** (v4.1.0+): Font-weight increased from 500 (medium) to 700 (bold) for better readability at small sizes.
- **Default Color** (v4.1.0+): Default changed from `default` (gray) to `primary` (brand green) for consistency with modern badge patterns.
## Audit Checklist
- No hardcoded sizes/colors/durations (all use tokens or relative units).
- Relative sizing: em units enable proportional scaling with parent font-size.
- Modifier classes appear only when non-default option chosen.
- Each modifier works independently (no chained requirements).
- Semantic color variants (8) all present and documented with saturated backgrounds.
- Bold font-weight (700) for high visibility at small sizes.
- README written in English.es** implemented (Bootstrap 5 pattern) - all tokens centralized in `.ps-badge` base.

## Audit Checklist
- No hardcoded sizes/colors/durations.
- Modifier classes appear only when non-default option chosen.
- Each modifier works independently (no chained requirements).
- Semantic color variants all present and documented.
- README written in English.
