# Radio (Atom)

Individual radio button for single selection within a group. Implements BEM methodology with `ps-` prefix and CSS 3-layer variable system.

## Usage

```twig
{% include '@elements/radio/radio.twig' with {
  name: 'property_type',
  value: 'apartment',
  label: 'Appartement',
  checked: true,
} only %}
```

### Grouped Usage (Real Estate Context)

```twig
<div class="form-radios">
  {% include '@elements/radio/radio.twig' with {
    name: 'service_type',
    value: 'sale',
    id: 'service-sale',
    label: 'Vente',
    checked: true,
  } only %}
  
  {% include '@elements/radio/radio.twig' with {
    name: 'service_type',
    value: 'rental',
    id: 'service-rental',
    label: 'Location',
  } only %}
  
  {% include '@elements/radio/radio.twig' with {
    name: 'service_type',
    value: 'management',
    id: 'service-management',
    label: 'Gestion',
  } only %}
</div>
```

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `name` | string | `'option'` | Input name attribute (group identifier - all radios in group must share same name) |
| `value` | string | `'1'` | Input value attribute (unique identifier for this option) |
| `id` | string | auto | Input ID for label association (auto-generated from `name` + `value` if omitted) |
| `label` | string | `''` | Label text displayed next to radio circle |
| `checked` | boolean | `false` | Whether radio is checked (selected) |
| `disabled` | boolean | `false` | Whether radio is disabled (includes `aria-disabled="true"`) |
| `attributes` | Drupal Attribute | `{}` | HTML attributes for wrapper `<label>` element |

## BEM Structure

```
.ps-radio                    (Block - wrapper label)
├── .ps-radio__input         (Element - hidden native input)
├── .ps-radio__circle        (Element - visual circle indicator with SVG mask)
└── .ps-radio__label         (Element - label text)

Modifiers:
├── .ps-radio--checked       (State - checked appearance)
└── .ps-radio--disabled      (State - disabled appearance, 50% opacity)
```

**Note**: The circle indicator uses CSS `mask-image` with SVG backgrounds (`radio-off.svg` and `radio-on.svg`), aligned with the Checkbox pattern.

## CSS Tokens (3-Layer System)

### Layer 1: Global Tokens (from `source/props/`)
```css
/* Sizes */
--size-3: 12px;
--size-5: 20px;

/* Colors */
--gray-400: hsl(218, 17%, 62%);
--gray-600: hsl(215, 19%, 35%);
--gray-900: hsl(222, 47%, 11%);
--primary: hsl(162, 72%, 38%);           /* #00915A */
--primary-hover: hsl(162, 72%, 33%);     /* Darker green */
--secondary: hsl(326, 63%, 41%);         /* #A12B66 - Focus color */
--white: hsl(0, 0%, 100%);

/* Borders */
--border-size-2: 2px;

/* Animations */
--duration-fast: 150ms;
--ease-3: cubic-bezier(0.4, 0, 0.2, 1);

/* Typography */
--font-size-1: 1rem;                     /* 16px */
--leading-normal: 1.5;
```

### Layer 2: Component Variables (defaults)
```css
.ps-radio {
  /* Sizing */
  --ps-radio-size: var(--size-6);              /* 24px circle (aligned with checkbox) */
  --ps-radio-gap: var(--size-2);               /* 8px gap */
  
  /* Radio Icon Masks (SVG backgrounds) */
  --ps-radio-icon-unchecked-mask: svg-load('generic/radio-off.svg');
  --ps-radio-icon-checked-mask: svg-load('generic/radio-on.svg');
  
  /* Colors - Unchecked State */
  --ps-radio-icon-color-default: var(--gray-700);  /* #333333 - default gray */
  --ps-radio-icon-color-hover: var(--primary-hover);
  
  /* Colors - Checked State */
  --ps-radio-icon-color-checked: var(--primary);   /* #00915A - BNP green */
  --ps-radio-icon-color-checked-hover: var(--primary-hover);
  
  /* Label */
  --ps-radio-label-color: var(--gray-700);         /* #333333 */
  --ps-radio-label-color-checked: var(--primary);  /* #00915A when checked */
  --ps-radio-label-color-hover: var(--primary-hover);
  --ps-radio-label-size: var(--font-size-2);       /* 18px */
  
  /* States */
  --ps-radio-disabled-opacity: 0.5;
  --ps-radio-focus-outline-color: var(--border-focus);
  --ps-radio-focus-outline-width: var(--border-size-2);
  --ps-radio-focus-outline-offset: var(--border-size-2);
}
```

### Layer 3: Context Overrides (example)
```css
/* Custom theme override (if needed) */
.dark-theme .ps-radio {
  --ps-radio-icon-color-default: var(--gray-300);
  --ps-radio-label-color: var(--white);
}

/* Compact variant (via modifier or context) */
.ps-radio--compact {
  --ps-radio-size: var(--size-5);      /* 20px */
  --ps-radio-gap: var(--size-1);       /* 4px */
}
```

## Accessibility (WCAG 2.2 AA)

### Keyboard Navigation
- **Tab**: Focus radio (visible outline via `:focus-visible`)
- **Space**: Select focused radio
- **Arrow keys**: Navigate between radios in same group (native browser behavior)

### ARIA Attributes
- `aria-disabled="true"` added automatically when `disabled={true}`
- Native `<input type="radio">` provides implicit ARIA role

### Focus Management
- **Outline**: `2px solid var(--secondary)` (#A12B66 - BNP Secondary Pink)
- **Offset**: `2px` for clear separation
- **Contrast**: 4.1:1 ratio (WCAG AA compliant)

### Screen Readers
- Label association via `for`/`id` attributes
- Input value announced on selection
- Disabled state announced automatically

## Real Estate Examples

### Property Type Selection
```twig
{% set property_types = [
  { value: 'apartment', label: 'Appartement' },
  { value: 'house', label: 'Maison' },
  { value: 'commercial', label: 'Local commercial' },
  { value: 'office', label: 'Bureau' },
] %}

<fieldset>
  <legend>Type de bien recherché</legend>
  {% for type in property_types %}
    {% include '@elements/radio/radio.twig' with {
      name: 'property_type',
      value: type.value,
      id: 'property-' ~ type.value,
      label: type.label,
      checked: loop.first,
    } only %}
  {% endfor %}
</fieldset>
```

### Service Selection
```twig
{% set services = [
  { value: 'sale', label: 'Vente' },
  { value: 'rental', label: 'Location' },
  { value: 'management', label: 'Gestion de patrimoine' },
  { value: 'investment', label: 'Investissement' },
] %}

<div class="form-radios">
  {% for service in services %}
    {% include '@elements/radio/radio.twig' with {
      name: 'service_type',
      value: service.value,
      label: service.label,
    } only %}
  {% endfor %}
</div>
```

## Notes

- **Single selection**: Only one radio can be checked per group (same `name` attribute)
- **Group wrapper**: Use `<div class="form-radios">` or `<fieldset>` for semantic grouping
- **Always provide label**: Essential for accessibility and usability
- **Unique IDs**: Auto-generated from `name + value`, but can be overridden for custom logic
- **SVG mask technique**: Uses `svg-load()` with `mask-image` for radio-off.svg and radio-on.svg (aligned with Checkbox pattern)
- **No border/dot HTML**: The filled circle is rendered by the SVG, not separate HTML elements
- **Composition**: Atom-level component, typically composed into `radios` molecule for full form field

## Related Components

- **Radios** (Molecule): Group wrapper with legend/description
- **Checkbox** (Atom): Similar visual pattern for multiple selection
- **Form Field** (Molecule): Complete form field with label, help text, error
