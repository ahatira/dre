# Select (Atom)

Native select element wrapped with custom chevron icon. Implements BEM methodology with `ps-` prefix and CSS 3-layer variable system. Typically composed into form-field molecule for complete form controls with label, help text, and error messages.

## Usage

```twig
{% include '@elements/select/select.twig' with {
  name: 'property_type',
  id: 'property-type',
  options: [
    { value: '', label: 'Sélectionner un type...', disabled: true, selected: true },
    { value: 'apartment', label: 'Appartement' },
    { value: 'house', label: 'Maison' }
  ],
} only %}
```

### With Validation States

```twig
{# Success state #}
{% include '@elements/select/select.twig' with {
  name: 'property_type',
  id: 'property-type',
  success: true,
  options: [...],
} only %}

{# Error state #}
{% include '@elements/select/select.twig' with {
  name: 'property_type',
  id: 'property-type',
  error: true,
  options: [...],
} only %}
```

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `options` | array | `[]` | Array of option objects: `{ value, label, disabled?, selected? }` |
| `name` | string | `''` | Form control name attribute |
| `id` | string | auto | HTML ID for label association (auto-generated from name if omitted) |
| `disabled` | boolean | `false` | Disable the select (applies ps-select--disabled modifier) |
| `required` | boolean | `false` | Mark as required (adds aria-required="true") |
| `error` | boolean | `null` | Error state with red border (applies ps-select--error) |
| `success` | boolean | `null` | Success state with green border (applies ps-select--success) |
| `attributes` | Drupal Attribute | `{}` | HTML attributes for `<select>` element |
| `wrapper_attributes` | Drupal Attribute | `{}` | HTML attributes for wrapper `<div>` element |

## BEM Structure

```
.ps-select                    (Block - wrapper div with relative positioning)
├── .ps-select__input         (Element - native <select>)
└── ::after pseudo-element    (Icon - chevron-down, CSS-only, no DOM element)

Modifiers:
├── .ps-select--disabled      (State - applied to wrapper when disabled)
├── .ps-select--error         (State - red border/icon for validation error)
└── .ps-select--success       (State - green border/icon for validation success)
```

### Icon Implementation

The chevron icon is rendered as a CSS `::after` pseudo-element on `.ps-select`, using the icon system's SVG sprite for rendering. This keeps the DOM clean while maintaining styling consistency.

```css
.ps-select::after {
  content: '';
  mask-image: url('/icons/icons-sprite.svg#icon-chevron-down'); /* From icon registry */
  background-color: var(--ps-select-icon-color);
  /* ... positioning and sizing ... */
}
```

## CSS Tokens (3-Layer System)

### Layer 1: Global Tokens

```css
/* Sizes */
--size-2: 8px;
--size-3: 12px;
--size-4: 16px;
--size-7: 28px;
--size-10: 40px;

/* Colors */
--white: #ffffff;
--gray-100: #f5f5f5;
--gray-300: #e0e0e0;
--gray-400: #b4babe;
--gray-500: #8b8f95;
--gray-600: #717579;
--gray-700: #434f57;
--gray-900: #1a1f25;

/* Semantic */
--danger: #EB3636;
--success: #198754;

/* Borders */
--border-size-2: 2px;
--radius-4: 8px;
--border-focus: var(--gray-900);

/* Typography */
--font-sans: 'BNP Sans', system-ui, sans-serif;
--font-size-1: 1rem;        /* 16px */
--font-weight-400: 400;
--leading-normal: 1.5;

/* Animations */
--duration-fast: 150ms;
--ease-3: cubic-bezier(0.4, 0, 0.2, 1);
```

### Layer 2: Component Variables

```css
.ps-select {
  /* Sizing */
  --ps-select-width: 100%;
  --ps-select-min-height: var(--size-10);        /* 40px */
  --ps-select-padding-y: var(--size-2);          /* 8px */
  --ps-select-padding-x: var(--size-4);          /* 16px */
  --ps-select-padding-right: var(--size-7);      /* 28px for icon */
  
  /* Typography */
  --ps-select-font-family: var(--font-sans);
  --ps-select-font-size: var(--font-size-1);     /* 16px */
  --ps-select-font-weight: var(--font-weight-400);
  --ps-select-line-height: var(--leading-normal);

  /* Colors - Base */
  --ps-select-bg: var(--white);
  --ps-select-color: var(--gray-900);
  --ps-select-border-color: var(--gray-300);

  /* Colors - Hover */
  --ps-select-hover-border-color: var(--gray-400);
  --ps-select-hover-bg: var(--white);

  /* Colors - Focus */
  --ps-select-focus-border-color: var(--border-focus);  /* gray-900 */
  --ps-select-focus-bg: var(--white);

  /* Colors - Disabled */
  --ps-select-disabled-bg: var(--gray-100);
  --ps-select-disabled-color: var(--gray-500);
  --ps-select-disabled-border-color: var(--gray-300);
  --ps-select-disabled-opacity: 0.6;

  /* Icon (chevron) */
  --ps-select-icon-color: var(--gray-700);
  --ps-select-icon-size: var(--size-3);          /* 12px */

  /* Borders */
  --ps-select-border-width: var(--border-size-2);
  --ps-select-border-radius: var(--radius-4);          /* 8px rounded corners */

  /* Animations */
  --ps-select-transition-duration: var(--duration-fast);
  --ps-select-transition-timing: var(--ease-3);
}
```

### Layer 3: Context Overrides

```css
/* Custom theme override */
.dark-theme .ps-select {
  --ps-select-bg: var(--gray-800);
  --ps-select-color: var(--white);
  --ps-select-border-color: var(--gray-600);
}

/* Compact variant (via modifier or context) */
.ps-select--compact {
  --ps-select-min-height: var(--size-8);
  --ps-select-padding-y: var(--size-1);
}
```

## Accessibility

### Keyboard Navigation

- **Tab**: Focus select element (visible outline)
- **Space/Enter**: Open options menu (native behavior)
- **Arrow keys**: Navigate options (native behavior)

### ARIA Attributes

- `aria-disabled="true"` - Added automatically when `disabled={true}`
- `aria-required="true"` - Added automatically when `required={true}`
- Native `<select>` provides implicit ARIA role

### Focus Management

- **Outline**: `2px solid var(--border-focus)` (gray-900)
- **Offset**: `2px` for clear separation
- **Contrast**: 4.5:1+ ratio (WCAG AA compliant)

### Screen Readers

- Native `<select>` is semantically correct for all screen readers
- Label association via `for`/`id` attributes in parent form-field
- Disabled state announced automatically

## States

All states controlled by modifiers or native HTML attributes:

| State | Modifier | Native | Visual Change |
|-------|----------|--------|--------------|
| **Default** | - | - | Gray border, chevron |
| **Hover** | - | `:hover` | Slightly darker border |
| **Focus** | - | `:focus-visible` | Black outline, darker border |
| **Success** | `--success` | - | Green border & chevron |
| **Error** | `--error` | - | Red border & chevron |
| **Disabled** | `--disabled` | `:disabled` | Light gray background, 60% opacity |

## Real Estate Examples

### Property Type Selection

```twig
{% set property_types = [
  { value: '', label: 'Sélectionner un type...', disabled: true, selected: true },
  { value: 'apartment', label: 'Appartement' },
  { value: 'house', label: 'Maison' },
  { value: 'commercial', label: 'Local commercial' },
  { value: 'office', label: 'Bureau' },
] %}

<div style="display: flex; flex-direction: column; gap: var(--size-2);">
  <label for="property-type" style="font-weight: var(--font-weight-600);">Type de bien</label>
  {% include '@elements/select/select.twig' with {
    name: 'property_type',
    id: 'property-type',
    options: property_types,
  } only %}
</div>
```

### Service Selection

```twig
{% set services = [
  { value: '', label: 'Choisir un service...', disabled: true, selected: true },
  { value: 'sale', label: 'Vente' },
  { value: 'rental', label: 'Location' },
  { value: 'management', label: 'Gestion de patrimoine' },
] %}

<div style="display: flex; flex-direction: column; gap: var(--size-2);">
  <label for="service-type" style="font-weight: var(--font-weight-600);">Service</label>
  {% include '@elements/select/select.twig' with {
    name: 'service_type',
    id: 'service-type',
    options: services,
  } only %}
</div>
```

## Notes

- **Wrapper**: Select is always wrapped in `<div class="ps-select">` for icon positioning
- **Icon**: Custom chevron-down icon positioned absolutely over select (pointer-events: none)
- **Native select**: Appearance: none removes browser styling; custom chevron provides visual consistency
- **No color/size variants**: Select uses semantic validation states (error/success) instead; size is always default
- **Composition**: Typically composed into `form-field` molecule for complete form control
- **Label**: External label required (not part of atom); use via parent component with `for`/`id`

## Related Components

- **Form Field** (Molecule): Complete form field with label, help text, error message
- **Radios** (Molecule): For mutually exclusive options (alternative to select)
- **Checkbox** (Atom): For multiple selection (different interaction pattern)
