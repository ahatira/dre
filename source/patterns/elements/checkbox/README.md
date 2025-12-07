# Checkbox

**Category**: Element (Atom)  
**Status**: ✅ Stable  
**Version**: 1.0.0

Native checkbox input with custom visual styling. Fully accessible with keyboard navigation and screen reader support.

---

## Usage

```twig
{% include '@elements/checkbox/checkbox.twig' with {
  name: 'property-features',
  value: 'parking',
  label: 'Parking available',
  checked: false,
  disabled: false
} only %}
```

---

## Props

| Prop | Type | Required | Default | Description |
|------|------|----------|---------|-------------|
| `name` | `string` | ✅ Yes | - | Input name attribute |
| `value` | `string` | ✅ Yes | - | Input value attribute |
| `label` | `string` | No | `null` | Checkbox label text |
| `color` | `string` | No | `primary` | Color variant: primary, secondary, success, warning, danger, info, dark, light |
| `size` | `string` | No | `md` | Size variant: xs, sm, md, lg, xl, xxl |
| `checked` | `boolean` | No | `false` | Checked state |
| `indeterminate` | `boolean` | No | `false` | Indeterminate state (partial selection) |
| `disabled` | `boolean` | No | `false` | Disabled state |
| `id` | `string` | No | Auto-generated | Unique ID for input |
| `attributes` | `Attribute` | No | `null` | Additional HTML attributes |

---

## BEM Structure

```css
.ps-checkbox                    /* Block: Container */
├── .ps-checkbox__input         /* Element: Native input (visually hidden) */
├── .ps-checkbox__checkmark     /* Element: Custom visual icon using SVG mask */
└── .ps-checkbox__label         /* Element: Label text */

Color Modifiers (checked state color):
├── .ps-checkbox--primary       /* Primary color (default) */
├── .ps-checkbox--secondary     /* Secondary color */
├── .ps-checkbox--success       /* Success/positive color */
├── .ps-checkbox--warning       /* Warning/caution color */
├── .ps-checkbox--danger        /* Danger/error color */
├── .ps-checkbox--info          /* Info/informational color */
├── .ps-checkbox--dark          /* Dark text color */
└── .ps-checkbox--light         /* Light/neutral color */

Size Modifiers (box and label sizing):
├── .ps-checkbox--xs            /* Extra small (12px box) */
├── .ps-checkbox--sm            /* Small (16px box) */
├── .ps-checkbox--md            /* Medium (24px box, default) */
├── .ps-checkbox--lg            /* Large (28px box) */
├── .ps-checkbox--xl            /* Extra large (32px box) */
└── .ps-checkbox--xxl           /* Extra extra large (40px box) */

State Modifiers:
├── .ps-checkbox--indeterminate /* Indeterminate/partial selection state */
└── .ps-checkbox--disabled      /* Disabled (non-interactive) state */
```

---

## Design Tokens

### Size Variants

| Variant | Box Size | Label Font Size | Gap |
|---------|----------|-----------------|-----|
| `xs` | var(--size-3) | 12px | 6px |
| `sm` | var(--size-4) | 14px | 8px |
| `md` | var(--size-6) | 16px | 8px |
| `lg` | var(--size-7) | 18px | 10px |
| `xl` | var(--size-8) | 20px | 12px |
| `xxl` | var(--size-10) | 22px | 14px |

### Color Variants

All colors follow the project's semantic color system. Default is `primary`. Each color affects:
- Checked state icon color
- Hover state icon color (lighter variant via `--{color}-hover` token)
- Associated label text color when checked

| Variant | Checked Color | Hover Color |
|---------|---------------|-------------|
| `primary` | var(--primary) | var(--primary-hover) |
| `secondary` | var(--secondary) | var(--secondary-hover) |
| `success` | var(--success) | var(--success-hover) |
| `warning` | var(--warning) | var(--warning-hover) |
| `danger` | var(--danger) | var(--danger-hover) |
| `info` | var(--info) | var(--info-hover) |
| `dark` | var(--dark) | var(--dark-hover) |
| `light` | var(--light) | var(--light-hover) |

### Core Tokens

**Sizing & Spacing:**
- `--ps-checkbox-size` - Checkbox box size (default: var(--size-6), 24px)
- `--ps-checkbox-gap` - Gap between box and label (default: var(--size-2), 8px)

**Visual & Styling:**
- `--ps-checkbox-icon-unchecked-mask` - SVG mask for unchecked state (checkbox-unchecked.svg)
- `--ps-checkbox-icon-checked-mask` - SVG mask for checked state (checkbox-checked.svg)
- `--ps-checkbox-border-radius` - Border radius (default: 0, square corners per spec)

**Colors:**
- `--ps-checkbox-checkmark-color-unchecked` - Unchecked icon color (default: var(--gray-700))
- `--ps-checkbox-checkmark-color-checked` - Checked icon color (default: var(--primary))
- `--ps-checkbox-hover-checkmark-color` - Hover icon color (derived from checked color's hover variant)
- `--ps-checkbox-disabled-opacity` - Disabled state opacity (default: 0.5)

**Typography:**
- `--ps-checkbox-label-font-size` - Label font size (size-variant dependent)
- `--ps-checkbox-label-color-unchecked` - Unchecked label text color (default: var(--gray-700))
- `--ps-checkbox-label-color-checked` - Checked label text color (matches icon color)

**Animation:**
- `--ps-checkbox-transition-duration` - Transition duration for color/mask changes
- `--ps-checkbox-transition-timing` - Transition easing function
- `--ps-checkbox-transition-delay` - Optional transition delay

---

## Accessibility

### Keyboard Support

- **Tab**: Focus checkbox
- **Space**: Toggle checked state
- **Shift + Tab**: Focus previous element

### Screen Readers

- Native checkbox semantics preserved
- Label properly associated with input via `for` attribute
- Disabled state announced via `aria-disabled="true"`
- Custom visual box hidden from screen readers with `aria-hidden="true"`

### Focus Indicator

- Visible focus outline on keyboard navigation (`:focus-visible`)
- Outline color: `--border-focus` (dark gray)
- Outline width: 2px
- Outline offset: 2px

### Color Contrast

- ✅ WCAG 2.2 AA compliant
- Label text: 4.5:1 minimum contrast ratio
- Focus indicator: 3:1 minimum contrast ratio

---

## Examples

### Basic Checkbox

```twig
{% include '@elements/checkbox/checkbox.twig' with {
  name: 'property-type',
  value: 'apartment',
  label: 'Apartment'
} only %}
```

### Checked Checkbox

```twig
{% include '@elements/checkbox/checkbox.twig' with {
  name: 'features',
  value: 'parking',
  label: 'Parking available',
  checked: true
} only %}
```

### Disabled Checkbox

```twig
{% include '@elements/checkbox/checkbox.twig' with {
  name: 'features',
  value: 'garden',
  label: 'Garden (not available)',
  disabled: true
} only %}
```

### Checkbox Without Label

```twig
{% include '@elements/checkbox/checkbox.twig' with {
  name: 'agreement',
  value: 'accepted'
} only %}
```

### Multiple Checkboxes (Property Search Form)

```twig
<fieldset>
  <legend>Property Type</legend>
  {% include '@elements/checkbox/checkbox.twig' with {
    name: 'property-type',
    value: 'apartment',
    label: 'Apartment',
    checked: true
  } only %}
  {% include '@elements/checkbox/checkbox.twig' with {
    name: 'property-type',
    value: 'house',
    label: 'House'
  } only %}
  {% include '@elements/checkbox/checkbox.twig' with {
    name: 'property-type',
    value: 'commercial',
    label: 'Commercial Space'
  } only %}
</fieldset>
```

### Color Variants

```twig
{# Primary (default) #}
{% include '@elements/checkbox/checkbox.twig' with {
  name: 'status',
  value: 'active',
  label: 'Active',
  color: 'primary',
  checked: true
} only %}

{# Success variant #}
{% include '@elements/checkbox/checkbox.twig' with {
  name: 'features',
  value: 'parking',
  label: 'Parking available',
  color: 'success',
  checked: true
} only %}

{# Warning variant #}
{% include '@elements/checkbox/checkbox.twig' with {
  name: 'status',
  value: 'pending',
  label: 'Pending approval',
  color: 'warning'
} only %}

{# Danger variant #}
{% include '@elements/checkbox/checkbox.twig' with {
  name: 'features',
  value: 'restrictions',
  label: 'Property has restrictions',
  color: 'danger'
} only %}
```

### Size Variants

```twig
{# Extra small #}
{% include '@elements/checkbox/checkbox.twig' with {
  name: 'compact',
  value: 'yes',
  label: 'Compact view',
  size: 'xs'
} only %}

{# Small #}
{% include '@elements/checkbox/checkbox.twig' with {
  name: 'sidebar',
  value: 'filter',
  label: 'Filter options',
  size: 'sm'
} only %}

{# Medium (default) #}
{% include '@elements/checkbox/checkbox.twig' with {
  name: 'form',
  value: 'agreed',
  label: 'I agree',
  size: 'md'
} only %}

{# Large #}
{% include '@elements/checkbox/checkbox.twig' with {
  name: 'featured',
  value: 'highlight',
  label: 'Featured listing',
  size: 'lg'
} only %}

{# Extra large #}
{% include '@elements/checkbox/checkbox.twig' with {
  name: 'important',
  value: 'confirm',
  label: 'Important confirmation',
  size: 'xl'
} only %}
```

### Indeterminate State (Parent Selector)

```twig
{# Parent checkbox showing indeterminate state #}
{% include '@elements/checkbox/checkbox.twig' with {
  name: 'all-features',
  value: 'all',
  label: 'All property features',
  indeterminate: true
} only %}

{# Child options (some selected, some not) #}
{% include '@elements/checkbox/checkbox.twig' with {
  name: 'features',
  value: 'parking',
  label: 'Parking',
  checked: true
} only %}
{% include '@elements/checkbox/checkbox.twig' with {
  name: 'features',
  value: 'garden',
  label: 'Garden',
  checked: false
} only %}
```

### Combined Color + Size

```twig
{# Warning color with large size #}
{% include '@elements/checkbox/checkbox.twig' with {
  name: 'alert',
  value: 'pending-review',
  label: 'Requires attention',
  color: 'warning',
  size: 'lg'
} only %}

{# Success color with small size in sidebar #}
{% include '@elements/checkbox/checkbox.twig' with {
  name: 'filters',
  value: 'verified',
  label: 'Verified only',
  color: 'success',
  size: 'sm'
} only %}
```

---

## CSS Variables (Customization)

Override these CSS variables for custom styling:

```css
.ps-checkbox {
  /* Override default sizing */
  --ps-checkbox-size: 28px; /* Larger than md (24px) */
  --ps-checkbox-gap: 12px; /* More spacing between box and label */
  
  /* Override default colors */
  --ps-checkbox-checkmark-color-unchecked: var(--gray-600); /* Darker unchecked icon */
  --ps-checkbox-checkmark-color-checked: var(--secondary); /* Use secondary instead of primary */
  
  /* Override animation timing */
  --ps-checkbox-transition-duration: 300ms;
}
```

All component-scoped variables can be overridden at the `.ps-checkbox` level or via the BEM modifiers (`--primary`, `--success`, etc.).

---

## Component Dependencies

**None** - This is a standalone atom component.

---

## Browser Support

- ✅ Chrome/Edge (latest)
- ✅ Firefox (latest)
- ✅ Safari (latest)
- ✅ iOS Safari (latest)
- ✅ Chrome Android (latest)

---

## Real Estate Context

Checkboxes are commonly used in:

- **Property search filters** (apartment type, features, amenities)
- **Contact forms** (consent, newsletter subscription)
- **Property listing forms** (available features, included services)
- **Settings and preferences** (notification preferences, privacy settings)

Example use cases:

- "Parking available"
- "Elevator access"
- "Furnished property"
- "Pet-friendly"
- "I agree to receive property alerts"

---

## Notes

- Native `<input type="checkbox">` preserved for full accessibility
- Visual styling applied through `.ps-checkbox__checkmark` element using SVG mask-image technique
- SVG icons loaded via `postcss-inline-svg` plugin for dynamic color control
- Icon color follows checkbox state via CSS `currentColor` and pseudo-class selectors
- Icon and label color transitions are synchronized for smooth visual feedback
- No JavaScript required for basic functionality
- Fully compatible with Drupal Forms API
- Works with or without label text
- Label automatically wraps for long text content
- Support for indeterminate state useful in hierarchical selection patterns (parent/child checkboxes)
- All color and size variants can be combined (e.g., `color: 'warning'` + `size: 'lg'`)

---

## Version History

- **1.2.0** (2025-12-07): Add color and size variants, indeterminate state support, synchronized animations
- **1.1.0** (2025-12-07): Implement SVG mask-based icon styling with postcss-inline-svg
- **1.0.0** (2025-12-07): Initial stable release

