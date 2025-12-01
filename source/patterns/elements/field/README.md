# Field (Element/Atom)

Base input/select/textarea field component without label or helper text. Part of the atomic design system as an element/atom.

## Description

The Field component is a foundational form element that provides a consistent, accessible interface for text input, number input, email input, search, select dropdowns, and textarea fields. It supports multiple states (default, hover, focus, filled, error, disabled), optional icons (left or right positioned), and follows BNP Paribas RealEstate design system tokens.

**Note:** This component does NOT include labels or helper text. For complete form fields with labels, use the `form-field` molecule component.

## BEM Structure

```
.ps-field                    # Base container
  .ps-field__input          # Input/textarea/select element
  .ps-field__icon           # Optional icon (left or right)
    .ps-field__icon--left   # Icon positioned on left
    .ps-field__icon--right  # Icon positioned on right
  .ps-field__error          # Error message display

Modifiers:
  .ps-field--text           # Text input (default)
  .ps-field--number         # Number input
  .ps-field--email          # Email input
  .ps-field--search         # Search input
  .ps-field--select         # Select dropdown
  .ps-field--textarea       # Textarea
  .ps-field--error          # Error state
  .ps-field--disabled       # Disabled state
  .ps-field--filled         # Has value
  .ps-field--icon-left      # Icon on left side
  .ps-field--icon-right     # Icon on right side
  .ps-field--done           # Success/validated state
```

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `type` | `string` | `'text'` | Field type: `text`, `number`, `email`, `search`, `select`, `textarea` |
| `value` | `string` | `''` | Current value of the field |
| `placeholder` | `string` | `''` | Placeholder text shown when field is empty |
| `disabled` | `boolean` | `false` | Disabled state of the field |
| `error` | `string` | `''` | Error message to display below the field |
| `icon` | `string` | `''` | Icon name to display (requires icon font) |
| `iconPosition` | `string` | `'right'` | Icon position: `left` or `right` |
| `attributes` | `Drupal.Core.Template.Attribute` | - | Additional HTML attributes |

## Design Tokens Used

### Layer 1: Root Tokens (Global)

#### Colors (Semantic)
- `--white` - Field background (white)
- `--black` - Focus border color
- `--text-primary` - Field text color (#333333)
- `--text-secondary` - Placeholder text color (gray-500)
- `--text-disabled` - Disabled text color (gray-400)
- `--border-default` - Default border color (#D6DBDE)
- `--border-light` - Hover border color (lighter gray)
- `--danger` - Error border/text color (#EB3636 - BNP official red)
- `--success` - Success border/icon color (green - BNP official)
- `--bg-disabled` - Disabled background (light gray #F9F9FB)
- `--gray-500` - Icon color default

#### Spacing
- `--size-2` (0.5rem / 8px) - Input padding vertical, gap between elements
- `--size-3` (0.75rem / 12px) - Icon margins
- `--size-4` (1rem / 16px) - Input padding horizontal, icon size
- `--size-7` (1.75rem / 28px) - Error spacing
- `--size-10` (2.5rem / 40px) - Minimum field height (Figma spec)
- `--size-20` (5rem / 80px) - Minimum textarea height
- `--size-305` (0.875rem / 14px) - Base font size, error message font size (Figma spec)

#### Borders & Radii
- `--border-size-2` (2px) - Border width (Figma spec)
- `--radius-2` (4px) - Border radius

#### Typography
- `--font-sans` - Font family (BNPP Sans)
- `--font-weight-400` - Regular font weight
- `--leading-normal` - Line height

#### Transitions
- `--duration-fast` (150ms) - Transition duration
- `--ease-4` - Cubic bezier timing function

### Layer 2: Component-Scoped Variables

The field component uses component-scoped variables for easy customization (Bootstrap 5 pattern):

```css
/* Sizing */
--ps-field-min-height: var(--size-10);
--ps-field-width: 100%;

/* Spacing */
--ps-field-padding-y: var(--size-2);
--ps-field-padding-x: var(--size-4);
--ps-field-gap: var(--size-2);

/* Typography */
--ps-field-font-family: var(--font-sans);
--ps-field-font-size: var(--size-305);
--ps-field-font-weight: var(--font-weight-400);
--ps-field-line-height: 1.71;

/* Colors - Default state */
--ps-field-bg: var(--white);
--ps-field-color: var(--text-primary);
--ps-field-border-color: var(--border-default);
--ps-field-placeholder-color: var(--gray-400);

/* Colors - Hover state */
--ps-field-hover-border-color: var(--border-light);

/* Colors - Focus state */
--ps-field-focus-border-color: var(--black);
--ps-field-focus-shadow-color: var(--black);

/* Colors - Error state */
--ps-field-error-border-color: var(--danger);
--ps-field-error-color: var(--danger);

/* Colors - Success state */
--ps-field-success-border-color: var(--success);
--ps-field-success-color: var(--success);

/* Colors - Disabled state */
--ps-field-disabled-bg: var(--bg-disabled);
--ps-field-disabled-color: var(--text-disabled);
--ps-field-disabled-border-color: var(--border-default);

/* Borders */
--ps-field-border-width: var(--border-size-2);
--ps-field-border-radius: var(--radius-2);

/* Icon */
--ps-field-icon-size: var(--size-4);
--ps-field-icon-margin: var(--size-3);
--ps-field-icon-color: var(--gray-500);
--ps-field-icon-spacing-reduce: var(--size-2);

/* Error message */
--ps-field-error-font-size: var(--size-305);
--ps-field-error-spacing: var(--size-7);
--ps-field-error-margin-top: var(--size-1);

/* Transitions */
--ps-field-transition-duration: var(--duration-fast);
--ps-field-transition-timing: var(--ease-4);

/* Textarea specific */
--ps-field-textarea-min-height: var(--size-20);
```

#### Customization Examples

**Context Override:**
```css
/* Compact fields in sidebar */
.sidebar .ps-field {
  --ps-field-min-height: var(--size-8);
  --ps-field-padding-y: var(--size-1);
  --ps-field-font-size: var(--size-3);
}
```

**Theme Override:**
```css
/* Dark theme fields */
[data-theme="dark"] .ps-field {
  --ps-field-bg: var(--gray-800);
  --ps-field-color: var(--white);
  --ps-field-border-color: var(--gray-700);
}
```

**Inline Override:**
```html
<div class="ps-field" style="--ps-field-border-color: var(--primary);">
  <input class="ps-field__input" type="text" placeholder="Custom border" />
</div>
```

**JavaScript Override:**
```javascript
document.querySelector('.ps-field').style.setProperty(
  '--ps-field-focus-border-color', 
  'var(--primary)'
);
```

## Usage Examples

### Basic Text Input
```twig
{% include '@ps_theme/field/field.twig' with {
  type: 'text',
  placeholder: 'Enter your name...',
} %}
```

### Email Input with Icon
```twig
{% include '@ps_theme/field/field.twig' with {
  type: 'email',
  placeholder: 'your.email@example.com',
  icon: 'check',
  iconPosition: 'right',
} %}
```

### Search Field
```twig
{% include '@ps_theme/field/field.twig' with {
  type: 'search',
  placeholder: 'Search properties...',
  icon: 'search',
  iconPosition: 'left',
} %}
```

### Field with Error
```twig
{% include '@ps_theme/field/field.twig' with {
  type: 'email',
  value: 'invalid-email',
  error: 'Please enter a valid email address',
} %}
```

### Disabled Field
```twig
{% include '@ps_theme/field/field.twig' with {
  type: 'text',
  value: 'Read-only value',
  disabled: true,
} %}
```

### Textarea
```twig
{% include '@ps_theme/field/field.twig' with {
  type: 'textarea',
  placeholder: 'Enter your message...',
} %}
```

### Select Dropdown
```twig
{% include '@ps_theme/field/field.twig' with {
  type: 'select',
  value: 'Select an option',
  icon: 'arrow-down',
  iconPosition: 'right',
} %}
```

## Real-World Use Cases

### Contact Form
```twig
<form>
  {% include '@ps_theme/field/field.twig' with {
    type: 'text',
    placeholder: 'Full Name',
  } %}
  
  {% include '@ps_theme/field/field.twig' with {
    type: 'email',
    placeholder: 'Email Address',
  } %}
  
  {% include '@ps_theme/field/field.twig' with {
    type: 'textarea',
    placeholder: 'Your Message',
  } %}
</form>
```

### Property Search Form
```twig
<div class="search-form">
  {% include '@ps_theme/field/field.twig' with {
    type: 'search',
    placeholder: 'Search location...',
    icon: 'search',
    iconPosition: 'right',
  } %}
  
  {% include '@ps_theme/field/field.twig' with {
    type: 'select',
    value: 'Property Type',
    icon: 'arrow-down',
    iconPosition: 'right',
  } %}
  
  {% include '@ps_theme/field/field.twig' with {
    type: 'number',
    placeholder: 'Max Price',
  } %}
</div>
```

### Form Validation
```twig
{# Valid email - success state #}
{% include '@ps_theme/field/field.twig' with {
  type: 'email',
  value: 'user@example.com',
  icon: 'check',
  iconPosition: 'right',
} %}

{# Invalid email - error state #}
{% include '@ps_theme/field/field.twig' with {
  type: 'email',
  value: 'invalid-email',
  error: 'Please enter a valid email address',
} %}
```

## States

### Default
Empty field with placeholder text, default border color.

### Hover
Darker border on mouse hover (non-disabled, non-error fields).

### Focus
Blue border (`--ps-color-border-focus`) with subtle box-shadow on focus.

### Filled
Field has a value, maintains default styling with value displayed.

### Error
Red border (`--ps-color-border-error`) with error message below, red icon color.

### Disabled
Light gray background, reduced opacity, cursor not-allowed, non-interactive.

### Done/Success
Green border (`--ps-color-border-success`) indicating validated/successful input.

## Accessibility

- **ARIA Labels**: Input elements use `aria-invalid="true"` when in error state
- **ARIA Describedby**: Error messages linked via `aria-describedby="field-error"`
- **ARIA Disabled**: Disabled fields use `aria-disabled="true"`
- **ARIA Roles**: Select fields use `role="combobox"` with `aria-expanded` and `aria-haspopup`
- **Focus Management**: Clear focus indicators with `:focus-visible` styles
- **Error Alerts**: Error messages use `role="alert"` for screen reader announcements
- **Icon Hiding**: Decorative icons use `aria-hidden="true"`
- **Keyboard Navigation**: All interactive elements are keyboard accessible

## Browser Support

- Modern browsers (Chrome, Firefox, Safari, Edge)
- Follows progressive enhancement principles
- CSS custom properties with fallbacks
- Removes default browser input styling for consistency

## Notes

- Icons use the centralized `data-icon` attribute system (managed by `icons.css`)
- Icon name passed WITHOUT "icon-" prefix (e.g., `icon: 'search'` not `icon: 'icon-search'`)
- Icon positioning adjusts input padding automatically via component variables
- Textarea resizes vertically by default (`resize: vertical`)
- Number input hides native browser spinners
- Select field is styled as combobox, actual dropdown functionality requires JavaScript
- This component does NOT include labels - use the `form-field` molecule for complete form fields
- All spacing uses design tokens for consistency
- Border widths and colors follow brand guidelines
- **Component-scoped variables** enable easy runtime customization
- **3-layer cascade system**: Root tokens → Component variables → Context overrides

## Related Components

- **Form Field (Molecule)**: Complete form field with label, helper text, and field
- **Label (Element)**: Standalone label component
- **Icon (Element)**: Icon component used for decorative icons
- **Checkbox (Element)**: Checkbox input component
- **Radio (Element)**: Radio button input component

## Validation

✅ BEM naming convention with `ps-` prefix  
✅ All CSS values use design tokens (no hardcoded values)  
✅ Complete Twig template with all props documented  
✅ Comprehensive Storybook stories (Default, variants, states, showcases)  
✅ README with full documentation  
✅ ARIA attributes for accessibility  
✅ Interactive states (hover, focus, active, disabled)  
✅ Pixel-perfect implementation matching design specs  
✅ Error handling and validation states  
✅ Icon support with centralized `data-icon` system  
✅ **Component-scoped variables implemented (Bootstrap 5 pattern)**  
✅ **3-layer variable cascade (Root → Component → Context)**  
✅ **Runtime customization support via CSS variables**  
✅ **All transitions use design tokens**  

## Version

**1.0.0** - Initial implementation following PS Theme component template standard
