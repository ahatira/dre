# Flag Component

**Category**: Element/Atom  
**Status**: ✅ Complete  
**Version**: 1.0.0

## Description

Visual indicator for country/language representation using flag images. The Flag component accepts ISO 3166-1 alpha-2 country codes or BCP 47 locale tags and renders the corresponding flag image with proper accessibility attributes.

**Key Features:**

- Supports both country codes (FR, GB, DE) and locale tags (fr-FR, en-GB)
- Three sizes: small (16px), medium (20px - default), large (24px)
- Three shapes: square (default), rounded (4px radius), circle (full round)
- Disabled state with grayscale effect
- Decorative mode for screen reader exclusion
- Automatic locale-to-country-code mapping

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `code` | `string` | - | Country code ISO 3166-1 alpha-2 (ex: FR, GB, DE, ES, IT, NL) |
| `locale` | `string` | - | BCP 47 locale tag (ex: fr-FR, en-GB). If provided, derives country code |
| `label` | `string` | - | Accessible label (ex: "France", "United Kingdom") for alt/title |
| `src` | `string` | `/assets/flags/{code}.svg` | Explicit flag image path (optional) |
| `size` | `'sm' \| 'md' \| 'lg'` | `'md'` | Flag size: sm (16px), md (20px), lg (24px) |
| `shape` | `'square' \| 'rounded' \| 'circle'` | `'square'` | Flag shape: square, rounded (4px), circle (full round) |
| `disabled` | `boolean` | `false` | Disabled state (grayed out with reduced opacity) |
| `decorative` | `boolean` | `false` | Decorative only (hides from screen readers with empty alt and aria-hidden) |
| `attributes` | `Attribute` | - | Additional HTML attributes |

## BEM Structure

```bem
ps-flag                    // Block wrapper (span)
  ps-flag__img             // Image element

Modifiers:
  ps-flag--sm              // Small size (16px)
  ps-flag--lg              // Large size (24px)
  ps-flag--rounded         // Rounded corners (4px)
  ps-flag--circle          // Circular shape
  ps-flag--disabled        // Disabled state
```

**Note**: Default size (`md` - 20px) and shape (`square`) do NOT add modifier classes to keep HTML minimal.

## Design Tokens Used

### Sizes

- `--size-4`: 16px (small)
- `--size-5`: 20px (medium - default)
- `--size-6`: 24px (large)

### Borders

- `--radius-2`: 4px (rounded shape)
- `--radius-round`: 9999px (circle shape)

### States

- Opacity: `0.5` (disabled state)
- Filter: `grayscale(0.2)` (disabled state)

## Usage Examples

### Basic Usage

```twig
{# France flag with default settings (medium, square) #}
{% include '@elements/flag/flag.twig' with {
  code: 'FR',
  label: 'France'
} %}
```

### With Locale Tag

```twig
{# Derive country code from BCP 47 locale tag #}
{% include '@elements/flag/flag.twig' with {
  locale: 'en-GB',
  label: 'English (UK)'
} %}
```

### Different Sizes

```twig
{# Small flag (16px) #}
{% include '@elements/flag/flag.twig' with {
  code: 'DE',
  label: 'Germany',
  size: 'sm'
} %}

{# Large flag (24px) #}
{% include '@elements/flag/flag.twig' with {
  code: 'ES',
  label: 'Spain',
  size: 'lg'
} %}
```

### Different Shapes

```twig
{# Rounded corners (4px) #}
{% include '@elements/flag/flag.twig' with {
  code: 'IT',
  label: 'Italy',
  shape: 'rounded'
} %}

{# Circular shape #}
{% include '@elements/flag/flag.twig' with {
  code: 'NL',
  label: 'Netherlands',
  shape: 'circle'
} %}
```

### Disabled State

```twig
{# Grayed out flag (50% opacity + 20% grayscale) #}
{% include '@elements/flag/flag.twig' with {
  code: 'IE',
  label: 'Ireland',
  disabled: true
} %}
```

### Decorative Mode

```twig
{# Hide from screen readers (empty alt, aria-hidden) #}
{% include '@elements/flag/flag.twig' with {
  code: 'PL',
  decorative: true
} %}
```

## Real-World Use Cases

### Language Selector

```twig
<div class="language-selector">
  {% include '@elements/flag/flag.twig' with {
    code: 'FR',
    label: 'Français',
    size: 'sm',
    shape: 'rounded'
  } %}
  <span>Français</span>
  
  {% include '@elements/flag/flag.twig' with {
    code: 'GB',
    label: 'English',
    size: 'sm',
    shape: 'rounded'
  } %}
  <span>English</span>
</div>
```

### Country List with Flags

```twig
<ul class="country-list">
  {% for country in countries %}
    <li class="country-item">
      {% include '@elements/flag/flag.twig' with {
        code: country.code,
        label: country.name,
        size: 'md',
        shape: 'circle'
      } %}
      <span class="country-name">{{ country.name }}</span>
    </li>
  {% endfor %}
</ul>
```

### Office Locations

```twig
<div class="office-location">
  {% include '@elements/flag/flag.twig' with {
    code: 'FR',
    label: 'France',
    size: 'lg',
    shape: 'rounded'
  } %}
  <div class="office-details">
    <h3>Paris Office</h3>
    <p>123 Avenue des Champs-Élysées</p>
  </div>
</div>
```

## Accessibility

### Best Practices

1. **Always provide a label** when the flag represents meaningful information (not decorative)
2. **Use `decorative: true`** only when the flag is purely visual and redundant information exists nearby
3. **Never use flags alone** to convey language/country - always accompany with text labels
4. **Minimum size** is 16px (small) to ensure visibility
5. **Disabled flags** maintain 50% opacity and grayscale filter for visual feedback

### ARIA Attributes

- `alt` attribute on `<img>` contains the label or country code (empty if decorative)
- `title` attribute on `<span>` wrapper provides hover tooltip (empty if decorative)
- `aria-hidden="true"` added to `<img>` when `decorative: true`

### Screen Reader Experience

**With label:**

```html
<span class="ps-flag" title="France">
  <img alt="France" src="/assets/flags/fr.svg" width="20" height="20" />
</span>
```

Screen reader announces: "France, image"

**Decorative mode:**

```html
<span class="ps-flag" title="">
  <img alt="" src="/assets/flags/fr.svg" width="20" height="20" aria-hidden="true" />
</span>
```

Screen reader skips the image entirely.

## Locale Mapping Logic

The component implements **Option C** from the specification: it accepts both `code` (ISO 3166-1 alpha-2) and `locale` (BCP 47) props.

### Normalization Process

1. **Normalize locale**: Replace underscore with dash (`fr_FR` → `fr-FR`)
2. **Extract region**: Split on dash and take second part if 2 chars (`en-GB` → `GB`)
3. **Determine code**: Use explicit `code` prop, or fallback to extracted region
4. **Lowercase for asset**: Convert to lowercase for file path (`FR` → `fr.svg`)

### Examples

| Input | Extracted Code | Asset Path |
|-------|----------------|------------|
| `code: 'FR'` | `FR` | `/assets/flags/fr.svg` |
| `locale: 'fr-FR'` | `FR` | `/assets/flags/fr.svg` |
| `locale: 'en-GB'` | `GB` | `/assets/flags/gb.svg` |
| `locale: 'de_DE'` | `DE` | `/assets/flags/de.svg` |
| No code/locale | `null` | `/assets/flags/xx.svg` |

## Browser Support

- Modern browsers (Chrome, Firefox, Safari, Edge)
- Logical properties (`inline-size`, `block-size`) with fallback support
- SVG flag images (4:3 aspect ratio) for crisp HiDPI display
- CSS transitions for smooth state changes
- Supports 250+ country flags (ISO 3166-1 alpha-2)

## Related Components

- **Badge**: For status indicators with text
- **Avatar**: For user profile images
- **Icon**: For iconography system

## Changelog

- **v1.0.0** (2025-11-29): Initial implementation with full spec compliance
  - Added ISO 3166-1 alpha-2 and BCP 47 locale support
  - Implemented 3 sizes (sm, md, lg) and 3 shapes (square, rounded, circle)
  - Added disabled state with grayscale effect
  - Added decorative mode for accessibility
  - Pixel-perfect implementation with design tokens only
  - Complete Storybook stories with all variants and use cases
