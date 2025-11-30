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
| primary | `--brand-primary` | `--white` |
| secondary | `--brand-secondary` | `--white` |
| gold | `--accent-gold` | `--white` |
| info | `--blue-100` | `--blue-700` |
| success | `--green-100` | `--green-700` |
| warning | `--yellow-100` | `--yellow-700` |
| danger | `--red-100` | `--red-700` |

## Design Tokens Used
- Spacing: `--size-05` `--size-1` `--size-2` `--size-3`
- Typography: `--font-size-xs` `--font-size-sm` `--font-size-0` `--font-weight-500`
- Colors: `--gray-*` `--brand-primary` `--brand-secondary` `--accent-gold` semantic scales (blue/green/yellow/red)
- Radius: `--radius-2` `--radius-round`
- Transition: `--ps-transition-duration-fast`

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
- `--accent-gold` token introduced replacing previous hardcoded HSL gold value.
- `secondary` now uses `--brand-secondary` token instead of accent magenta direct value.

## Audit Checklist
- No hardcoded sizes/colors/durations.
- Modifier classes appear only when non-default option chosen.
- Each modifier works independently (no chained requirements).
- Semantic color variants all present and documented.
- README written in English.
