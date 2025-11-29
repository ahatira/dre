# ps-heading

Atom: Semantic typographic headings (h1–h6) with semantic color + weight variants. Default output is minimal: `<h1 class="ps-heading">`.

## API

- `text` (string) REQUIRED — Heading text content
- `level` (string) — `h1|h2|h3|h4|h5|h6` (default: `h1`)
- `align` (string) — `left|center|right` (default: `left`)
- `color` (string) — `default|primary|secondary|success|warning|danger|info` (default: `default`)
- `weight` (string) — `light|regular|bold|extra` (default: `bold`)
- `visuallyHidden` (boolean) — Screen reader only (default: `false`)
- `icon` (string) — Optional icon class from `icons.css` (e.g., `icon-pin-map`)
- `iconPosition` (string) — `left|right` (default: `left`)

## Heading Hierarchy (Scale)

- **h1**: 48px / 1.25 / bold
- **h2**: 36px / 1.25 / bold
- **h3**: 28px / 1.375 / bold
- **h4**: 24px / 1.375 / bold
- **h5**: 20px / 1.5 / semi-bold (600)
- **h6**: 16px / 1.5 / semi-bold (600) / uppercase + wide tracking

## BEM

- Block: `ps-heading`
- Elements: `ps-heading__text`, `ps-heading__icon`
- Modifiers (independent):
  - Levels: `--h2`, `--h3`, `--h4`, `--h5`, `--h6` (base = h1)
  - Alignment: `--align-center`, `--align-right` (base = left)
  - Color: `--primary`, `--secondary`, `--success`, `--warning`, `--danger`, `--info`
  - Weight: `--light`, `--regular`, `--bold`, `--extra` (base = bold)
  - Visibility: `--visually-hidden`
  - Icon presence: `--with-icon`

## Minimal Markup Principle

Default output (only base class):
```html
<h1 class="ps-heading"><span class="ps-heading__text">Title</span></h1>
```
Any modifier class appears ONLY when value differs from its default.

## Usage Examples

```twig
{# Default h1 #}
{% include '@ps_theme/heading/heading.twig' with { text: 'Page Title' } %}

{# h2 section title (adds --h2) #}
{% include '@ps_theme/heading/heading.twig' with { text: 'Section', level: 'h2' } %}

{# Warning colored h3 #}
{% include '@ps_theme/heading/heading.twig' with { text: 'Caution', level: 'h3', color: 'warning' } %}

{# Secondary light weight h4 centered #}
{% include '@ps_theme/heading/heading.twig' with { text: 'Sub Block', level: 'h4', color: 'secondary', weight: 'light', align: 'center' } %}

{# With right icon #}
{% include '@ps_theme/heading/heading.twig' with { text: 'Voir les détails', level: 'h3', icon: 'icon-arrow-right', iconPosition: 'right' } %}

{# Screen reader only structural heading #}
{% include '@ps_theme/heading/heading.twig' with { text: 'Navigation', level: 'h2', visuallyHidden: true } %}
```

## Design Tokens Used

Typography & Weight:
- `--ps-heading-h1-size..h6` fallbacks `--font-size-10|8|6|5|3|1`
- `--ps-heading-h1-line-height..h6` fallbacks `--leading-tight|tight|snug|snug|normal|normal`
- `--ps-font-weight-bold` fallback `--font-weight-700`
- `--font-weight-600` (h5/h6 defaults)
- Weight modifiers: `--font-weight-300|400|700|800`
- Tracking for h6: `--tracking-wide`

Color:
- Base text: `--ps-color-text` fallback `--gray-900`
- Semantic variants: `--brand-primary`, `--brand-secondary`, `--btn-success`, `--btn-warning`, `--btn-danger`, `--btn-info`

Spacing:
- Bottom margin: `--ps-spacing-6` fallback `--size-6`
- Icon gap: `--size-2`

Icon:
- Font family: `bnpre-icons` via `source/props/icons.css`

## Accessibility

- Maintain logical hierarchy (do not skip levels arbitrarily).
- One `h1` per page; additional visual needs use lower levels styled as needed.
- `visuallyHidden` preserves semantics for assistive tech.
- Icons marked `aria-hidden="true"` when decorative.

## States & Modifiers Independence

Each modifier adjusts only its concern (color, weight, alignment, level) and works standalone.

## Responsive Considerations

Typography scales via tokens; adjust at token layer if responsive sizes introduced.

## Testing Checklist

- Verify color variants reflect correct brand tokens.
- Ensure weight modifiers override level defaults cleanly.
- Confirm minimal markup: no default modifier classes on base render.
- Screen reader only heading removed visually but present in accessibility tree.

## Typography Reference

```
h1: 48px / 1.25 / 700
h2: 36px / 1.25 / 700
h3: 28px / 1.375 / 700
h4: 24px / 1.375 / 700
h5: 20px / 1.5 / 600
h6: 16px / 1.5 / 600 / uppercase / wide tracking
```
