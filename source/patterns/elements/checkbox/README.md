# Checkbox Component

A native, accessible checkbox input with integrated label support.

## Component Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `name` | string | `'checkbox'` | The `name` attribute of the checkbox input |
| `value` | string | `'1'` | The `value` attribute of the checkbox input |
| `label` | string | `'Accept'` | Label text displayed next to the checkbox |
| `checked` | boolean | `false` | Checked state of the checkbox |
| `disabled` | boolean | `false` | Disabled state of the checkbox |
| `id` | string | auto-generated | Unique identifier (auto-generated if not provided) |

## BEM Structure

```
ps-checkbox                    # Root element
├── ps-checkbox__input         # Native <input type="checkbox">
└── ps-checkbox__label         # Associated <label> element
```

### Modifiers

- `ps-checkbox--disabled` - Applied when checkbox is disabled

## Design Tokens Used

### Sizing & Spacing
- `--size-3` (12px) - Checkbox input dimensions (20px total with border)
- `--size-2` (8px) - Label spacing (gap between checkbox and text)

### Colors
- `--brand-primary` - Checked state background & border color
- `--brand-primary-dark` - Hover/active state background
- `--neutral-50` - Checkmark icon color
- `--neutral-200` - Unchecked border color
- `--neutral-100` - Unchecked background color
- `--neutral-300` - Disabled border color
- `--neutral-150` - Disabled background color
- `--neutral-500` - Label text color
- `--neutral-400` - Disabled label text color

### Typography
- `--font-size-1` (14px) - Label text size
- `--font-weight-regular` (400) - Label text weight
- `--line-height-1` (20px) - Label line height

### Borders & Radius
- `--border-size-1` (1px) - Checkbox border width
- `--radius-2` (4px) - Checkbox corner radius

### Transitions
- `--ps-transition-duration-fast` (150ms) - State change duration

## Usage Examples

### Basic Checkbox
```twig
{% include '@elements/checkbox/checkbox.twig' with {
  name: 'terms',
  value: '1',
  label: 'I accept the terms and conditions'
} %}
```

### Checked Checkbox
```twig
{% include '@elements/checkbox/checkbox.twig' with {
  name: 'newsletter',
  value: '1',
  label: 'Subscribe to newsletter',
  checked: true
} %}
```

### Disabled Checkbox
```twig
{% include '@elements/checkbox/checkbox.twig' with {
  name: 'readonly',
  value: '1',
  label: 'This option cannot be changed',
  disabled: true
} %}
```

### Checkbox Group (Vertical)
```twig
<fieldset>
  <legend>Select your preferences</legend>
  {% for option in options %}
    {% include '@elements/checkbox/checkbox.twig' with {
      name: 'preferences[]',
      value: option.value,
      label: option.label
    } %}
  {% endfor %}
</fieldset>
```

## Accessibility

- **Native Input**: Uses native `<input type="checkbox">` for full keyboard support
- **Label Association**: Label automatically associated via `for` attribute
- **Keyboard Navigation**: Standard Tab/Space navigation
- **Focus Indicator**: Visible focus outline (2px solid brand-primary with 2px offset)
- **Disabled State**: Uses `aria-disabled="true"` and prevents interaction
- **Screen Readers**: Native checkbox semantics announced correctly

### Focus Management
- Tab: Move focus between checkboxes
- Space: Toggle checkbox state
- Focus visible only on keyboard interaction (`:focus-visible`)

## Best Practices

### DO ✅
- Always provide a visible label
- Group related checkboxes in a `<fieldset>` with `<legend>`
- Use vertical stacking for better scannability
- Maintain 20×20px checkbox size for optimal touch target
- Use tokens for all styling (colors, spacing, transitions)

### DON'T ❌
- Don't use checkboxes without labels (accessibility violation)
- Don't nest other clickable elements inside label
- Don't hardcode colors or sizes
- Don't create horizontal checkbox groups (harder to scan)
- Don't override native checkbox behavior with custom JavaScript

## Component Audit Checklist

- [x] Uses native `<input type="checkbox">`
- [x] All colors use design tokens
- [x] All sizes use design tokens (--size-*)
- [x] Typography uses design tokens (--font-*)
- [x] Transitions use design tokens
- [x] BEM methodology with `ps-` prefix
- [x] CSS uses PostCSS nesting syntax
- [x] Auto-generated ID when not provided
- [x] Label properly associated with input
- [x] Keyboard navigation supported
- [x] Focus indicators visible
- [x] Disabled state properly handled
- [x] No hardcoded values in CSS
- [x] Storybook documentation complete
- [x] All content in English
