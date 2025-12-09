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

State Modifiers:
├── .ps-checkbox--checked       /* Checked state */
├── .ps-checkbox--indeterminate /* Indeterminate/partial selection state */
└── .ps-checkbox--disabled      /* Disabled (non-interactive) state */
```

---

## Design Tokens

### Core Tokens

**Sizing & Spacing:**
- `--ps-checkbox-size` - Checkbox box size (default: var(--size-6), 24px)
- `--ps-checkbox-gap` - Gap between box and label (default: var(--size-2), 8px)

**Visual & Styling:**
- `--ps-checkbox-icon-unchecked-mask` - SVG mask for unchecked state
- `--ps-checkbox-icon-checked-mask` - SVG mask for checked state
- `--ps-checkbox-icon-indeterminate-mask` - SVG mask for indeterminate state
- `--ps-checkbox-border-radius` - Border radius (default: 0, square corners)

**Colors:**
- `--ps-checkbox-checkmark-color-default` - Unchecked icon color (default: var(--gray-700))
- `--ps-checkbox-checkmark-color-checked` - Checked icon color (default: var(--primary))
- `--ps-checkbox-hover-checkmark-color` - Hover icon color (default: var(--primary-hover))
- `--ps-checkbox-disabled-opacity` - Disabled state opacity (default: 0.5)

**Typography:**
- `--ps-checkbox-label-font-size` - Label font size (default: var(--font-size-2))
- `--ps-checkbox-label-color` - Unchecked label text color (default: var(--gray-700))
- `--ps-checkbox-label-color-checked` - Checked label text color (default: var(--primary))
- `--ps-checkbox-label-color-hover` - Hover label text color

**Animation:**
- `--ps-checkbox-transition-duration` - Transition duration (default: var(--duration-fast))
- `--ps-checkbox-transition-timing` - Transition easing (default: var(--ease-3))

---

## Accessibility

### Keyboard Support

- **Tab**: Focus checkbox
- **Space**: Toggle checked state
- **Shift + Tab**: Focus previous element

### Screen Readers

- Native checkbox semantics preserved
- Label properly associated with input via `<label>` element
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

---

## CSS Variables (Customization)

Override these CSS variables for custom styling:

```css
.ps-checkbox {
  /* Override default sizing */
  --ps-checkbox-size: 28px; /* Larger than default (24px) */
  --ps-checkbox-gap: 12px; /* More spacing between box and label */
  
  /* Override default colors */
  --ps-checkbox-checkmark-color-default: var(--gray-600); /* Darker unchecked icon */
  --ps-checkbox-checkmark-color-checked: var(--secondary); /* Use secondary instead of primary */
  
  /* Override animation timing */
  --ps-checkbox-transition-duration: 300ms;
}
```

All component-scoped variables can be overridden at the `.ps-checkbox` level.

---

## JavaScript Behavior

### Indeterminate State

The indeterminate state **cannot be set via HTML attribute** - it requires JavaScript. The component includes a Drupal behavior (`psCheckbox`) that automatically applies the indeterminate state to checkboxes with `data-indeterminate="true"`.

**How it works:**

1. When you set `indeterminate: true` in Twig, it adds `data-indeterminate="true"` attribute
2. The JavaScript behavior (`checkbox.js`) detects this attribute on page load
3. It sets `element.indeterminate = true` via JavaScript API
4. The CSS detects the `:indeterminate` pseudo-class and applies the indeterminate icon

**User interaction:**
- When a user clicks an indeterminate checkbox, it becomes checked (or unchecked)
- The indeterminate state is automatically removed on user interaction
- This follows the standard UX pattern for tri-state checkboxes

**Manual control (JavaScript):**
```javascript
// Set indeterminate state manually
const checkbox = document.querySelector('#my-checkbox');
checkbox.indeterminate = true;

// Check if indeterminate
if (checkbox.indeterminate) {
  console.log('Checkbox is indeterminate');
}

// Clear indeterminate state
checkbox.indeterminate = false;
```

**AJAX/Dynamic content:**
The behavior uses Drupal's `once()` API, so it works automatically with AJAX-loaded content.

---

## Component Dependencies

**JavaScript**: Requires `checkbox.js` (automatically included via Drupal libraries)  
**Drupal**: Uses `Drupal.behaviors` and `once()` API  
**Icons**: Uses SVG masks from icon system (`checkbox-unchecked.svg`, `checkbox-checked.svg`, `checkbox-indeterminate.svg`)

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

## Design Rationale

### Why No Color Variants?

A checkbox is a **binary input control** (checked/unchecked), not a status indicator. Color variants (success, danger, warning) imply semantic meaning that should belong to the containing **form element** or **status message**, not the checkbox itself.

**Correct pattern**: Use a status message or form-field wrapper to communicate semantic state:

```twig
{# ✅ CORRECT - Status via form-field wrapper, not checkbox color #}
{% include '@components/form-field/form-field.twig' with {
  status: 'success',
  label: 'Property verified',
  input: checkboxTwig(...)
} only %}

{# ❌ WRONG - Semantic color on checkbox itself #}
{% include '@elements/checkbox/checkbox.twig' with {
  label: 'Property verified',
  color: 'success'  # Not supported
} only %}
```

### Why Single Size?

A checkbox requires a **minimum touch target of 24px** for accessibility (WCAG 2.1 guideline). Smaller sizes (12-16px) become difficult for users with tremors or motor impairments. Larger sizes (28-40px) create disproportionate visual weight in typical forms.

The single **24px default size** balances accessibility (sufficient touch target) with visual harmony (standard form layouts).

---

## Version History

- **1.0.0** (2025-12-09): Initial stable release with checked/unchecked/indeterminate/disabled states, SVG mask-based icon styling
