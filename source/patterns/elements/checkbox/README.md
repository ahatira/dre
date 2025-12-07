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
| `disabled` | `boolean` | No | `false` | Disabled state |
| `id` | `string` | No | Auto-generated | Unique ID for input |
| `attributes` | `Attribute` | No | `null` | Additional HTML attributes |

---

## BEM Structure

```css
.ps-checkbox                    /* Block: Container */
├── .ps-checkbox__input         /* Element: Native input (visually hidden) */
├── .ps-checkbox__box           /* Element: Custom visual box */
│   └── .ps-checkbox__checkmark /* Element: SVG checkmark icon */
└── .ps-checkbox__label         /* Element: Label text */

Modifiers:
├── .ps-checkbox--checked       /* State: Checked */
└── .ps-checkbox--disabled      /* State: Disabled */
```

---

## Design Tokens

### Sizing & Spacing

- `--size-6` (24px) - Checkbox box size (spec requirement)
- `--size-2` (8px) - Gap between box and label (spec requirement)

### Border & Visual

- `--border-size-1` (1px) - Border width
- `--gray-400` - Border color (unselected)
- `--radius-3` (6px) - Border radius (visible rounding on 24px box)

### Colors

**Unselected state:**
- Background: `--white`
- Border: `--gray-400` (gray)
- Label: `--gray-700` (#333333)

**Selected state:**
- Background: `--white`
- Border: `--primary` (#00915A green)
- Checkmark: `--primary` (#00915A green)
- Label: `--primary` (#00915A green)

**Hover state (both unchecked/checked):**
- Border: `hsl(157, 95%, 35%)` (#04AF6E lighter green)
- Checkmark: `hsl(157, 95%, 35%)` (#04AF6E lighter green)
- Label: `hsl(157, 95%, 35%)` (#04AF6E lighter green)

### Focus

- `--border-focus` - Focus outline color

### Typography

- `--font-size-2` - Label font size
- `--font-weight-400` - Label font weight
- `--leading-normal` - Label line height

### Animation

- `--duration-fast` - Transition duration
- `--ease-3` - Transition easing

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

---

## CSS Variables (Customization)

Override these CSS variables for custom styling:

```css
.ps-checkbox {
  /* Sizing */
  --ps-checkbox-size: var(--size-6); /* Larger checkbox */
  --ps-checkbox-gap: var(--size-3); /* More spacing */

  /* Colors */
  --ps-checkbox-checked-bg: var(--secondary); /* Magenta instead of green */
  --ps-checkbox-hover-border-color: var(--secondary);

  /* Border */
  --ps-checkbox-border-radius: var(--radius-3); /* More rounded */
}
```

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
- Visual styling applied through custom `.ps-checkbox__box` element
- Checkmark appears with smooth scale animation
- No JavaScript required for basic functionality
- Fully compatible with Drupal Forms API
- Works with or without label text
- Label automatically wraps for long text content

---

## Version History

- **1.0.0** (2025-12-07): Initial stable release

