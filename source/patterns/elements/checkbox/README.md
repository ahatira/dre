# Checkbox Component

A native, accessible checkbox input with icon-based visual styling and optional label support. Built with component-scoped CSS variables for easy customization.

## Component Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `name` | string | (required) | The `name` attribute of the checkbox input |
| `value` | string | (required) | The `value` attribute of the checkbox input |
| `label` | string | `''` | Label text displayed next to the checkbox (optional) |
| `checked` | boolean | `false` | Checked state of the checkbox |
| `disabled` | boolean | `false` | Disabled state of the checkbox |
| `id` | string | `name ~ '-' ~ value` | Unique identifier (auto-generated from name+value if not provided) |

## BEM Structure

```
ps-checkbox                    # Root <label> element
├── ps-checkbox__input         # Native <input type="checkbox"> (visually hidden)
├── ps-checkbox__box           # Visual box with icon (via ::before pseudo-element)
└── ps-checkbox__label         # Label text (optional)
```

### Modifiers

- `ps-checkbox--disabled` - Applied when checkbox is disabled (only when `disabled=true`)

## CSS Variables System (3-Layer Architecture)

### Layer 1: Root Primitives (Referenced by Component)

**Sizing & Spacing**
- `--size-2` (8px) - Gap between checkbox and label
- `--size-5` (20px) - Checkbox box size

**Typography**
- `--font-sans` - Label font family
- `--font-size-1` (16px) - Label text size
- `--font-size-2` (18px) - Icon size
- `--font-weight-400` - Label font weight
- `--leading-6` (24px) - Label line height

**Colors**
- `--primary` - Brand green (#00915A) for checked state
- `--text-primary` - Default label and icon color (#434F57)
- `--border-focus` - Focus outline color (#333333)

**Transitions**
- `--duration-fast` (0.15s) - Transition duration
- `--ease-3` - Transition timing function
- `--border-size-2` (2px) - Focus outline width

### Layer 2: Component-Scoped Variables (Customizable)

```css
.ps-checkbox {
  /* Layout */
  --ps-checkbox-gap: var(--size-2);
  --ps-checkbox-align: flex-start;
  --ps-checkbox-box-size: var(--size-5);
  
  /* Typography */
  --ps-checkbox-label-font-family: var(--font-sans);
  --ps-checkbox-label-font-size: var(--font-size-1);
  --ps-checkbox-label-font-weight: var(--font-weight-400);
  --ps-checkbox-label-line-height: var(--leading-6);
  --ps-checkbox-label-color: var(--text-primary);
  --ps-checkbox-label-color-checked: var(--primary);
  
  /* Icon colors */
  --ps-checkbox-icon-color-unchecked: var(--text-primary);
  --ps-checkbox-icon-color-checked: var(--primary);
  --ps-checkbox-icon-size: var(--font-size-2);
  
  /* Focus state */
  --ps-checkbox-focus-outline-width: var(--border-size-2);
  --ps-checkbox-focus-outline-color: var(--border-focus);
  --ps-checkbox-focus-outline-offset: var(--border-size-2);
  
  /* Disabled state */
  --ps-checkbox-disabled-opacity: 0.5;
  
  /* Transitions */
  --ps-checkbox-transition-duration: var(--duration-fast);
  --ps-checkbox-transition-timing: var(--ease-3);
}
```

### Layer 3: Context Overrides (Example)

Override component variables for specific contexts:

```css
/* Compact form variant */
.form--compact .ps-checkbox {
  --ps-checkbox-gap: var(--size-1);
  --ps-checkbox-box-size: var(--size-4);
  --ps-checkbox-label-font-size: var(--font-size-0);
}

/* Custom brand color */
.sidebar .ps-checkbox {
  --ps-checkbox-icon-color-checked: var(--secondary);
  --ps-checkbox-label-color-checked: var(--secondary);
}
```

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
- **Label Association**: Label wraps input and text, ensuring proper click target
- **Visual Hidden Input**: Input visually hidden but fully accessible to assistive technologies
- **Keyboard Navigation**: Standard Tab/Space navigation works out of the box
- **Focus Indicator**: Visible focus outline (2px solid, 2px offset) on keyboard focus only (`:focus-visible`)
- **Disabled State**: Uses native `disabled` attribute, no redundant ARIA needed
- **Icon Decorative**: Icon box marked `aria-hidden="true"` as visual only
- **Screen Readers**: Native checkbox semantics announced correctly

### Focus Management
- **Tab**: Move focus between checkboxes
- **Space**: Toggle checkbox state
- **Focus visible only on keyboard interaction** (`:focus-visible`)

## Best Practices

### DO ✅
- Always provide a visible label for better UX and accessibility
- Group related checkboxes in a `<fieldset>` with `<legend>`
- Use vertical stacking for better scannability
- Maintain 20×20px checkbox size for optimal touch target
- Use component-scoped variables for customization
- Let label wrap naturally for long text

### DON'T ❌
- Don't use checkboxes without labels (accessibility violation)
- Don't nest other clickable elements inside label
- Don't hardcode colors, sizes, or transitions in component CSS
- Don't create horizontal checkbox groups (harder to scan)
- Don't override native checkbox behavior with custom JavaScript
## Component Audit Checklist

- [x] Uses native `<input type="checkbox">`
- [x] **CSS Variables System**: 3-layer architecture (primitives → component → context)
- [x] **All colors use semantic tokens** (`--primary`, `--text-primary`, `--border-focus`)
- [x] **All sizes use design tokens** (`--size-*`, `--font-size-*`, `--border-size-*`)
- [x] **Typography uses design tokens** (`--font-sans`, `--font-weight-400`, `--leading-6`)
- [x] **Transitions use design tokens** (`--duration-fast`, `--ease-3`)
- [x] **BEM methodology with `ps-` prefix** (`ps-checkbox`, `ps-checkbox__input`, `ps-checkbox__box`, `ps-checkbox__label`)
- [x] **CSS uses PostCSS nesting syntax** (modern `&` syntax throughout)
- [x] **Minimal markup**: Modifier `--disabled` only added when `disabled=true`
- [x] **Cascade order correct**: Base → Elements → States → Modifiers
- [x] **Auto-generated ID** when not provided (`name ~ '-' ~ value`)
- [x] **Label wraps input** for proper click target and accessibility
- [x] **Keyboard navigation** supported (Tab, Space)
- [x] **Focus indicators visible** (`:focus-visible` only on keyboard interaction)
- [x] **Disabled state properly handled** (native `disabled`, no redundant ARIA)
- [x] **No hardcoded values** in CSS (all values use tokens or component variables)
- [x] **Storybook documentation complete** (`tags: ['autodocs']`, argTypes categorized, showcases)
- [x] **Icons centralized** (icon glyphs defined in CSS, no inline variables)
- [x] **All content in English** (comments, docs, props) CSS
- [x] Storybook documentation complete
- [x] All content in English
