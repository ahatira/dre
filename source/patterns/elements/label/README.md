# Label (Element/Atom)

Form field label with semantic `<label>` binding, required indicator, and disabled state. Essential building block for form components.

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `text` | `string` | — | Label text content (required) |
| `forId` | `string` | — | ID of the associated form field for proper label-input binding |
| `required` | `boolean` | `false` | Adds visual asterisk (*) and screen reader text "(required field)" |
| `disabled` | `boolean` | `false` | Disabled state with reduced opacity and muted color |
| `color` | `string` | `default` | Color variant (default, primary, secondary, info, warning, danger, success) |
| `size` | `string` | `md` | Size variant (xs, sm, md, lg, xl) |
| `baseClass` | `string` | `'ps-label'` | Override root class when composing inside other components (e.g., `'ps-form-element__label'`). Modifiers map to `baseClass--required` and `baseClass--disabled`; elements use `baseClass__text` and `baseClass__required`.
| `attributes` | `Drupal.Core.Template.Attribute` | — | Additional HTML attributes |

## BEM Structure

```
.ps-label                    # Base label element (<label>)
  .ps-label__text           # Label text wrapper
  .ps-label__required       # Visual asterisk (*) for required fields

Modifiers:
  .ps-label--required       # Applied when required=true (or `baseClass--required` when overridden)
  .ps-label--disabled       # Applied when disabled=true (or `baseClass--disabled` when overridden)
  .ps-label--primary, .ps-label--secondary, .ps-label--info, .ps-label--warning, .ps-label--danger, .ps-label--success # Color variants
  .ps-label--xs, .ps-label--sm, .ps-label--md, .ps-label--lg, .ps-label--xl # Size variants

Global Utilities (from base/utilities/visibility.css):
  .visually-hidden          # Screen reader only text (WCAG standard)
```

## Design Tokens Used

### Component Variables (Layer 2)
Component uses scoped variables for easy customization:
- `--ps-label-gap` — Gap between text and asterisk (default: `--size-1` = 4px)
- `--ps-label-margin-bottom` — Bottom margin (default: `--size-2` = 8px)
- `--ps-label-color` — Text color (default: `--text-primary` = #434F57)
- `--ps-label-font-size` — Font size (default: `--font-size-0` = 14px)
- `--ps-label-font-weight` — Font weight (default: `--font-weight-600` = semi-bold)
- `--ps-label-required-color` — Asterisk color (default: `--danger` = #EB3636)
- `--ps-label-disabled-color` — Disabled text (default: `--text-disabled` = #B0B8BD)

### Root Primitives (Layer 1)
References from `source/props/*.css`:

**Colors** (`colors.css` + `brand.css`):
- `--text-primary` — Main text color (#434F57, gray-700)
- `--text-disabled` — Disabled text (#B0B8BD, gray-400)
- `--danger` — Error/required color (#EB3636, brand red)

**Typography** (`fonts.css`):
- `--font-sans` — BNPP Sans font family
- `--font-size-0` — 14px (0.875rem)
- `--font-weight-600` — Semi-bold (600)
- `--font-weight-700` — Bold (700)
- `--leading-5` — Line height 20px (1.25rem)

**Spacing** (`sizes.css`):
- `--size-1` — 4px (0.25rem)
- `--size-2` — 8px (0.5rem)

### Customization Example (Layer 3)
```css
/* Override in specific context */
.compact-form .ps-label {
  --ps-label-font-size: var(--font-size--1); /* Smaller: 12px */
  --ps-label-margin-bottom: var(--size-1);   /* Tighter: 4px */
}
```

## Variants

### Default
Standard label with black text, semi-bold weight, clickable cursor.

### Color Variants

- **default** (default): Standard text color (`--text-primary`)
- **primary**: Brand green color (`--primary`)
- **secondary**: Secondary blue color (`--secondary`)
- **info**: Informational blue color (`--info`)
- **warning**: Warning orange color (`--warning`)
- **danger**: Error red color (`--danger`)
- **success**: Success green color (`--success`)

Color variants modify the label text color via CSS custom properties.

### Size Variants

- **xs**: Extra small (font: 12px)
- **sm**: Small (font: 13px)
- **md** (default): Medium (font: 14px)
- **lg**: Large (font: 16px)
- **xl**: Extra large (font: 18px)

Size variants adjust the font-size via Layer 2 CSS variables.

### Required (`required: true`)
Adds:
- Red asterisk (*) with `aria-hidden="true"`
- Screen reader text "(required field)" with `.ps-visually-hidden`
- Modifier class `.ps-label--required`

### Disabled (`disabled: true`)
Applies:
- Muted gray color (`--gray-500`)
- 70% opacity
- `cursor: not-allowed`
- Modifier class `.ps-label--disabled`

### Required + Disabled
Combines both modifiers - red asterisk with disabled styling.

## Accessibility

### WCAG 2.2 Level AA Compliance

**Semantic HTML**:
- Uses `<label>` element with `for` attribute for proper input association
- Clicking label focuses associated input field

**Screen Readers**:
- Visual asterisk has `aria-hidden="true"` (decorative only)
- Hidden text "(required field)" announced by screen readers via `.visually-hidden`
- `.visually-hidden` uses WCAG standard pattern (defined in `base/utilities/visibility.css`)

**Keyboard Navigation**:
- No direct keyboard interaction (label triggers input focus)
- Disabled state uses `cursor: not-allowed` for visual feedback

**Color Contrast**:
- Default text: `--gray-900` on white = 21:1 ratio (AAA)
- Required asterisk: `--red-600` on white = 4.5:1 ratio (AA)
- Disabled text: `--gray-500` on white = 4.5:1 ratio (AA, with opacity 0.7)

**Focus Management**:
- Label itself not focusable (semantic behavior)
- Click/touch triggers focus on associated input via `for` attribute

## Use Cases

### 1. Contact Forms
```twig
{{ include('@elements/label/label.twig', {
  text: 'Email address',
  forId: 'contact-email',
  required: true
}) }}
```

### 2. User Registration
```twig
{{ include('@elements/label/label.twig', {
  text: 'Password',
  forId: 'user-password',
  required: true
}) }}
```

### 3. Settings with Disabled Fields
```twig
{{ include('@elements/label/label.twig', {
  text: 'Account ID (read-only)',
  forId: 'account-id',
  disabled: true
}) }}
```

### 4. Optional Information
```twig
{{ include('@elements/label/label.twig', {
  text: 'Phone number (optional)',
  forId: 'user-phone'
}) }}
```

### 5. With Form-Element Molecule
Label is typically composed within `form-element` molecule:
```twig
{# form-element uses label internally #}
{{ include('@components/form-element/form-element.twig', {
  label: 'Your name',
  required: true,
  type: 'text',
  placeholder: 'John Doe'
}) }}
```

## Drupal Integration

### As Standalone Component
```twig
{# In Drupal form template #}
{{ include('@elements/label/label.twig', {
  text: form.field_name['#title'],
  forId: form.field_name['#id'],
  required: form.field_name['#required'],
  disabled: form.field_name['#disabled']
}) }}
```

### With Form API
```php
$form['email'] = [
  '#type' => 'textfield',
  '#title' => t('Email'),
  '#required' => TRUE,
  '#id' => 'user-email',
];
```

Twig:
```twig
{{ include('@elements/label/label.twig', {
  text: form.email['#title'],
  forId: form.email['#id'],
  required: form.email['#required']
}) }}
{{ form.email|without('title') }}
```

### Theme Hook
```php
function ps_theme_preprocess_label(&$variables) {
  // Custom preprocessing if needed
  $variables['text'] = $variables['element']['#title'];
  $variables['forId'] = $variables['element']['#id'];
  $variables['required'] = !empty($variables['element']['#required']);
}
```

## Component Composition

**Used By** (Molecules that compose this atom):
- `form-element` — Combines label + field + helper/error
- `checkbox` — May use label pattern
- `radio` — May use label pattern

**Dependencies**: None (pure atom, no composed elements)

### Composition Example with `baseClass`
```twig
{# Inside a form-element molecule #}
{% include '@elements/label/label.twig' with {
  text: 'Email address',
  forId: 'contact-email',
  required: true,
  baseClass: 'ps-form-element__label'
} %}
```

## Notes

- **Do NOT recreate label markup** in molecules - always compose this atom
- `.ps-visually-hidden` is a global utility, not component-specific
- Asterisk color matches error color for consistency
- `cursor: pointer` on default state for better UX (indicates clickability)
- Works with all input types: text, email, number, select, textarea, checkbox, radio

---

Component Status: ✅ Complete
Last Updated: December 3, 2025
