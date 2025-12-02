# Badge

Compact semantic label / status indicator supporting size, color and shape modifiers with optional decorative icon and link behavior.

## Overview
- **Purpose**: Highlight statuses, categories, counts or meta labels inline.
- **Variants (color)**: `default` `primary` `secondary` `gold` (legacy accent) `info` `success` `warning` `danger`.
- **Sizes**: `small` `medium` (default) `large`.
- **Shape**: Base rounded radius, optional `pill` modifier for fully circular ends.
- **Icon**: Decorative glyph before text; never the sole accessible label (text always required).
- **Link**: Provide `url` to render `<a>` with focus outline.
- **Accessibility**: Text ensures contrast; icon is decorative (`aria-hidden` via icon system); focus visible only for interactive links.
- **Markup principle**: No modifier classes for default values; each modifier works independently.

## Markup
```twig
{# Default badge #}
<span class="ps-badge">Default</span>

{# Primary badge with icon #}
<span class="ps-badge ps-badge--primary">
  <span class="ps-badge__icon" data-icon="check"></span>
  <span class="ps-badge__text">Verified</span>
</span>

{# Pill link badge #}
<a href="#" class="ps-badge ps-badge--info ps-badge--pill">Learn more</a>
```

## Props
| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `text` | string (required) | `"Badge"` | Visible label text. |
| `icon` | string | `''` | Decorative icon name (no `icon-` prefix). |
| `color` | enum | `default` | Semantic color variant. |
| `size` | enum | `medium` | Size scale (changes font & padding). |
| `pill` | boolean | `false` | Fully rounded pill shape. |
| `url` | string | `''` | Link URL (renders `<a>`). |
| `attributes` | Attribute | — | Additional HTML attributes merged onto root element. |

## BEM Structure
- Block: `.ps-badge`
- Elements: `.ps-badge__icon`, `.ps-badge__text`
- Modifiers: `--small` `--large` `--pill` color variants listed above

## Color Variants (Semantic)
| Variant | Background Token | Text Token |
|---------|------------------|-----------|
| default | `--gray-200` | `--gray-600` |
| primary | `--primary` | `--white` |
| secondary | `--secondary` | `--white` |
| gold | `--yellow-500` | `--white` |
| info | `--blue-100` | `--blue-700` |
| success | `--green-100` | `--green-700` |
| warning | `--yellow-100` | `--yellow-700` |
| danger | `--red-100` | `--red-700` |

## Design Tokens Used
- Spacing: `--size-05` `--size-1` `--size-2` `--size-3` `--size-105`
- Typography: `--font-size--2` (10px) `--font-size--1` (12px) `--font-size-0` (14px) `--font-weight-500`
- Colors: `--gray-*` `--primary` `--secondary` `--yellow-500` semantic scales (blue/green/yellow/red)
- Radius: `--radius-2` `--radius-round`
- Transition: `--duration-fast`

## Component-Scoped Variables
Badge uses Bootstrap 5-inspired component-scoped variables:

```css
.ps-badge {
  --ps-badge-bg: var(--gray-200);
  --ps-badge-color: var(--gray-600);
  --ps-badge-padding-y: var(--size-1);
  --ps-badge-padding-x: var(--size-2);
  --ps-badge-font-size: var(--font-size--1);
  --ps-badge-radius: var(--radius-2);
  /* ... */
}
```

Modifiers only change variables, enabling runtime customization and context overrides.

## Accessibility
- Text is always present ensuring descriptive label.
- Decorative icon rendered via `data-icon` is ignored by assistive tech.
- Focus outline applied only when component is an interactive link (`<a>`).
- Ensure color variants maintain contrast ratio (semantic palette chosen accordingly).

## Usage Examples
```twig
{# Success status #}
{{ include('@ps_theme/badge/badge.twig', { text: 'Active', color: 'success', icon: 'check' }) }}

{# Warning #}
{{ include('@ps_theme/badge/badge.twig', { text: 'Pending', color: 'warning', icon: 'help' }) }}

{# Linked info badge #}
{{ include('@ps_theme/badge/badge.twig', { text: 'More info', color: 'info', url: '#' }) }}

{# Pill with icon #}
{{ include('@ps_theme/badge/badge.twig', { text: 'Exclusive', color: 'gold', pill: true, icon: 'medal' }) }}
```

## Do & Don’t
| Do | Don’t |
|----|-------|
| Use semantic color names | Hardcode hex/HSL values |
| Keep text short (1–2 words) | Rely on icon only for meaning |
| Use `pill` only when rounded shape intended | Combine multiple shape modifiers |
| Provide `url` for interactive link style | Add link styles to non-link elements |

## Migration Notes
- `--yellow-500` token used for legacy gold variant (replaces non-existent `--accent-gold`).
- `--secondary` now uses direct semantic token.
- `--primary` is primary semantic color.
- Font sizes migrated: `--font-size--2` (10px), `--font-size--1` (12px), `--font-size-0` (14px).
- Transition token: `--duration-fast` replaces `--ps-transition-duration-fast`.
- **Component-scoped variables** implemented (Bootstrap 5 pattern) - all tokens centralized in `.ps-badge` base.

## Audit Checklist
- No hardcoded sizes/colors/durations.
- Modifier classes appear only when non-default option chosen.
- Each modifier works independently (no chained requirements).
- Semantic color variants all present and documented.
- README written in English.
