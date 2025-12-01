# Flag Component

**Category**: Element/Atom  
**Status**: ✅ Complete  
**Version**: 2.0.0

## Description

Visual indicator for country/language representation using flag images. Supports ISO 3166-1 alpha-2 country codes (FR, GB, DE) or BCP 47 locale tags (fr-FR, en-GB) with automatic locale-to-country-code mapping.

**Key Features:**

- Five sizes: xs (12px), sm (16px), md (20px - default), lg (24px), xl (48px)
- Three shapes: square (default, 4:3 ratio), rounded (4:3 with 4px corners), circle (1:1 ratio)
- Interactive states: hover, focus-visible, disabled
- Component-scoped CSS variables for runtime customization
- Decorative mode for accessibility (aria-hidden)

## Architecture

**3-Layer CSS Variables System** (Bootstrap 5 inspired):

- **Layer 1**: Root tokens (`--size-*`, `--radius-*`, `--duration-*`, `--ease-*`) from `source/props/`
- **Layer 2**: Component variables (`--ps-flag-*`) with defaults
- **Layer 3**: Context overrides (`.sidebar .ps-flag { --ps-flag-size: ... }`)

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `code` | `string` | - | Country code ISO 3166-1 alpha-2 (ex: FR, GB, DE, ES, IT, NL) |
| `locale` | `string` | - | BCP 47 locale tag (ex: fr-FR, en-GB). If provided, derives country code |
| `label` | `string` | - | Accessible label (ex: "France", "United Kingdom") for alt/title |
| `src` | `string` | `/flags/{code}.svg` | Explicit flag image path (overrides default) |
| `size` | `'xs' \| 'sm' \| 'md' \| 'lg' \| 'xl'` | `'md'` | Flag size: xs (12px), sm (16px), md (20px), lg (24px), xl (48px) |
| `shape` | `'square' \| 'rounded' \| 'circle'` | `'square'` | Flag shape: square (4:3), rounded (4:3 + 4px corners), circle (1:1) |
| `disabled` | `boolean` | `false` | Disabled state (reduced opacity + grayscale) |
| `decorative` | `boolean` | `false` | Decorative only (hides from screen readers with aria-hidden) |
| `attributes` | `Attribute` | - | Additional HTML attributes |

## BEM Structure

```bem
ps-flag                    // Block wrapper (span)
  ps-flag__img             // Image element

Modifiers (Sizes):
  ps-flag--xs              // Extra small (12px)
  ps-flag--sm              // Small (16px)
  ps-flag--lg              // Large (24px)
  ps-flag--xl              // Extra large (48px)

Modifiers (Shapes):
  ps-flag--rounded         // Rounded corners (4px)
  ps-flag--circle          // Circular shape (1:1 ratio)

Modifiers (States):
  ps-flag--disabled        // Disabled state
```

**Note**: Default size (`md` - 20px) and shape (`square`) do NOT add modifier classes (minimal markup principle).

## Design Tokens Used

### Layer 1: Root Tokens (from `source/props/`)

**Sizes** (`sizes.css`):
- `--size-3`: 12px (xs)
- `--size-4`: 16px (sm)
- `--size-5`: 20px (md - default)
- `--size-6`: 24px (lg)
- `--size-12`: 48px (xl)
- `--size-1`: 4px (focus outline offset)

**Borders** (`borders.css`):
- `--radius-2`: 4px (rounded shape corners)
- `--radius-round`: 9999px (circle shape)
- `--border-size-2`: 2px (focus outline width)

**Brand Colors** (`brand.css`):
- `--border-focus`: Focus outline color

**Animations** (`animations.css`):
- `--duration-fast`: 150ms (transitions)

**Easing** (`easing.css`):
- `--ease-4`: cubic-bezier(0.25, 0, 0.2, 1) (smooth transitions)

### Layer 2: Component Variables

The Flag component exposes these CSS variables for customization:

**Layout:**
```css
--ps-flag-size: var(--size-5);           /* Base height/width (default: 20px) */
--ps-flag-aspect-ratio: calc(4 / 3);     /* Width multiplier (default: 4:3) */
```

**Visual:**
```css
--ps-flag-border-radius: 0;              /* Corner radius (default: 0 for square) */
```

**States:**
```css
--ps-flag-disabled-opacity: 0.5;         /* Disabled state opacity */
--ps-flag-disabled-grayscale: 0.2;       /* Disabled state grayscale filter */
--ps-flag-hover-opacity: 0.9;            /* Hover state opacity */
```

**Transitions:**
```css
--ps-flag-transition-duration: var(--duration-fast);  /* 150ms */
--ps-flag-transition-timing: var(--ease-4);           /* Smooth easing */
```

### Layer 3: Context Overrides (Examples)

**Runtime customization via CSS:**
```css
/* Larger flags in hero section */
.hero .ps-flag {
  --ps-flag-size: var(--size-8); /* 32px custom size */
}

/* Square aspect ratio for all flags in sidebar */
.sidebar .ps-flag {
  --ps-flag-aspect-ratio: 1; /* 1:1 ratio */
}

/* Custom hover effect in navigation */
.nav .ps-flag {
  --ps-flag-hover-opacity: 0.7;
}
```

**Runtime customization via JavaScript:**
```javascript
// Override single property
const flag = document.querySelector('.ps-flag');
flag.style.setProperty('--ps-flag-size', 'var(--size-10)');

// Override multiple properties
Object.assign(flag.style, {
  '--ps-flag-border-radius': 'var(--radius-3)',
  '--ps-flag-aspect-ratio': '1'
});
```

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
{# Extra small flag (12px) #}
{% include '@elements/flag/flag.twig' with {
  code: 'DE',
  label: 'Germany',
  size: 'xs'
} %}

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

{# Extra large flag (48px) #}
{% include '@elements/flag/flag.twig' with {
  code: 'IT',
  label: 'Italy',
  size: 'xl'
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
4. **Minimum size** is 12px (xs) for visibility (but prefer 16px+ for better accessibility)
5. **Disabled flags** maintain 50% opacity and 20% grayscale for visual feedback
6. **Interactive flags** support keyboard navigation with visible focus outline (2px, 4px offset)

### Interactive States

**Hover:**
- Opacity reduces to 90% (`--ps-flag-hover-opacity`)
- Visual feedback for clickable/interactive flags

**Focus:**
- 2px solid outline with 4px offset
- High contrast focus indicator for keyboard users

**Disabled:**
- 50% opacity + 20% grayscale filter
- `pointer-events: none` prevents interaction

### ARIA Attributes

- `alt` attribute on `<img>` contains the label or country code (empty if decorative)
- `title` attribute on `<span>` wrapper provides hover tooltip (empty if decorative)
- `aria-hidden="true"` added to `<img>` when `decorative: true`

### Screen Reader Experience

**With label:**

```html
<span class="ps-flag" title="France">
  <img alt="France" src="/flags/fr.svg" width="20" height="20" />
</span>
```

Screen reader announces: "France, image"

**Decorative mode:**

```html
<span class="ps-flag" title="">
  <img alt="" src="/flags/fr.svg" width="20" height="20" aria-hidden="true" />
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

- **v2.0.0** (2025-12-01): **Migration to 3-layer CSS variables system**
  - **BREAKING**: Refactored CSS with component-scoped variables (`--ps-flag-*`)
  - Added Layer 2 component variables for runtime customization
  - Replaced hardcoded values with design tokens (transitions, opacity, grayscale)
  - Added interactive states: hover (0.9 opacity), focus-visible (2px outline)
  - Optimized cascade: shapes override variables instead of repeating size logic
  - Fixed: Circle shape now uses single `--ps-flag-aspect-ratio: 1` variable
  - Updated documentation with 3-layer architecture + customization examples
  - Performance: Eliminated redundant size declarations in `--circle` modifier

- **v1.0.0** (2025-11-29): Initial implementation with full spec compliance
  - Added ISO 3166-1 alpha-2 and BCP 47 locale support
  - Implemented 5 sizes (xs, sm, md, lg, xl) and 3 shapes (square, rounded, circle)
  - Added disabled state with grayscale effect
  - Added decorative mode for accessibility
  - Pixel-perfect implementation with design tokens
  - Complete Storybook stories with all variants and use cases
