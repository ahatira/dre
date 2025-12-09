# Icon (Element/Atom)

SVG sprite-based icon system with semantic sizing, color inheritance, and accessibility support.

## Overview

The Icon atom renders SVG icons from a compiled sprite, inheriting sizing and color from parent context. This approach provides:

- **Performance**: Single HTTP request (sprite.svg cacheable)
- **Responsive sizing**: Icons scale with parent `font-size` (1em = 100% of parent)
- **Color control**: Icons inherit parent `color` property (semantic)
- **Accessibility**: Full ARIA support (labels, hidden state)
- **Maintainability**: Auto-generated from `source/icons-source/` SVG files

## Props

| Prop | Type | Required | Default | Description |
|------|------|----------|---------|-------------|
| `icon` | string | ✓ | — | Icon name without prefix (e.g., "check", "home", "search") |
| `position` | string | — | `start` | Icon position: `start` (before text) or `end` (after text); affects margins |
| `ariaLabel` | string | — | — | ARIA label for icon-only or semantic meaning (accessibility) |
| `ariaHidden` | boolean | — | false | Hide from screen readers (for purely decorative icons) |
| `attributes` | object | — | — | Additional HTML attributes (classes, data-*, etc.) |

## Sizing

Icons automatically inherit parent `font-size`. No size prop—use parent context:

```twig
{# Small icon #}
<span style="font-size: var(--font-size-1);">
  {% include '@elements/icon/icon.twig' with { icon: 'check' } only %}
  Small text
</span>

{# Medium icon (default) #}
<span style="font-size: var(--font-size-2);">
  {% include '@elements/icon/icon.twig' with { icon: 'check' } only %}
  Medium text
</span>

{# Large icon #}
<span style="font-size: var(--font-size-4);">
  {% include '@elements/icon/icon.twig' with { icon: 'check' } only %}
  Large text
</span>
```

## Colors

Icons inherit parent `color` property. No color prop—use semantic color via parent:

```twig
{# Primary action #}
<div style="color: var(--primary);">
  {% include '@elements/icon/icon.twig' with { icon: 'star' } only %}
  Star this property
</div>

{# Success state #}
<div style="color: var(--success);">
  {% include '@elements/icon/icon.twig' with { icon: 'check' } only %}
  Booking confirmed
</div>

{# Danger state #}
<div style="color: var(--danger);">
  {% include '@elements/icon/icon.twig' with { icon: 'alert' } only %}
  Error message
</div>
```

## Positioning

Control icon position relative to text using `data-icon-position`:

```twig
{# Icon before text (default) #}
<button>
  {% include '@elements/icon/icon.twig' with {
    icon: 'check',
    position: 'start'
  } only %}
  Submit
</button>

{# Icon after text #}
<a href="/next">
  Next step
  {% include '@elements/icon/icon.twig' with {
    icon: 'arrow-right',
    position: 'end'
  } only %}
</a>
```

## Accessibility

### Icon with Label (icon-only buttons)

```twig
<button aria-label="Close dialog">
  {% include '@elements/icon/icon.twig' with {
    icon: 'close',
    ariaHidden: true
  } only %}
</button>
```

### Icon with Text (no label needed)

```twig
<button>
  {% include '@elements/icon/icon.twig' with {
    icon: 'check',
    ariaHidden: true
  } only %}
  Confirm
</button>
```

### Decorative Icons

Use `ariaHidden: true` for purely decorative icons:

```twig
<span style="color: var(--gray-400);">
  {% include '@elements/icon/icon.twig' with {
    icon: 'star',
    ariaHidden: true
  } only %}
  Rating
</span>
```

## BEM Structure

```
.ps-icon                          /* Block - icon element */
  [data-icon="name"]              /* Attribute selector for sprite reference */
  [data-icon-position="end"]      /* Modifier - position control */
```

## CSS Architecture

- **Base styles**: Display, sizing, color inheritance, vertical alignment
- **Position modifiers**: Margin adjustments via `data-icon-position`
- **Color inheritance**: `color: inherit;` from parent
- **Size inheritance**: `font-size: inherit;` for responsive 1em scaling

All icon mappings (`[data-icon="name"]`) are auto-generated in `source/props/icons-generated.css`.

## Integration

The Icon atom is designed for composition in buttons, badges, links, and other components:

```twig
{# Button with icon (atoms included) #}
<button class="ps-button">
  {% include '@elements/icon/icon.twig' with {
    icon: 'check',
    position: 'start'
  } only %}
  Submit form
</button>

{# Badge with icon (atoms included) #}
<span class="ps-badge" style="color: var(--success);">
  {% include '@elements/icon/icon.twig' with {
    icon: 'check',
    ariaHidden: true
  } only %}
  Complete
</span>

{# Link with trailing icon #}
<a href="/properties" class="ps-link">
  Browse properties
  {% include '@elements/icon/icon.twig' with {
    icon: 'arrow-right',
    position: 'end',
    ariaHidden: true
  } only %}
</a>
```

## Available Icons

All SVG files in `source/icons-source/` are automatically compiled into the sprite. 140+ icons available, including:

- **Navigation**: menu, close, arrow-right, arrow-left, chevron-right, etc.
- **Actions**: check, search, edit, delete, share, download, etc.
- **Status**: home, building, key, phone, email, calendar, etc.
- **Semantic**: alert, info, warning, success, error, etc.
- **Real Estate specific**: property, bed, bath, euro, area, location, etc.

Use icon names without the `icon-` prefix.

## Design Tokens

- **Sizing**: Inherits `font-size` (responsive 1em)
- **Colors**: Inherits `color` (semantic via parent)
- **Spacing**: Position margins via `var(--size-2)` (from `sizes.css`)
- **Vertical alignment**: `-0.125em` for text baseline compatibility

## Implementation Notes

- **Build**: Icons compiled from `source/icons-source/**/*.svg` via `build-icons.mjs`
- **Output**: Sprite at `source/assets/icons/icons-sprite.svg`, CSS at `source/props/icons-generated.css`
- **Twig constraint**: Uses native Twig only (Drupal 10/11 compatible)
- **Performance**: Single sprite HTTP request, cacheable, no JS required
- **Browser support**: All modern browsers (IE11+ with polyfills)

## Examples

### Real Estate: Property Listing CTA

```twig
<a href="/properties/downtown-loft" style="color: var(--primary);">
  {% include '@elements/icon/icon.twig' with {
    icon: 'home',
    position: 'start'
  } only %}
  View Property Details
</a>
```

### Real Estate: Contact Agent Button

```twig
<button style="color: var(--primary);">
  {% include '@elements/icon/icon.twig' with {
    icon: 'phone',
    position: 'start'
  } only %}
  Schedule a Visit
</button>
```

### Real Estate: Confirmation Message

```twig
<div style="color: var(--success);">
  {% include '@elements/icon/icon.twig' with {
    icon: 'check',
    position: 'start',
    ariaHidden: true
  } only %}
  <strong>Booking confirmed!</strong> You'll receive a confirmation email shortly.
</div>
```
