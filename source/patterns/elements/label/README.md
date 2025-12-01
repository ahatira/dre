# Label (Element/Atom)

Accessible form field label with required indicator and semantic HTML association. Essential building block for form components.

## Overview

The Label component provides a semantic `<label>` element that associates text with form inputs using the `for` attribute. It supports required field indication with both visual (asterisk) and accessible (screen reader text) cues, plus disabled state styling.

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `text` | `string` | — | Label text content (required) |
| `forId` | `string` | — | ID of the associated form field for proper label-input binding |
| `required` | `boolean` | `false` | Adds visual asterisk (*) and screen reader text "(required field)" |
| `disabled` | `boolean` | `false` | Disabled state with reduced opacity and muted color |
| `attributes` | `Drupal.Core.Template.Attribute` | — | Additional HTML attributes |

## BEM Structure

```
.ps-label                    # Base label element (<label>)
  .ps-label__text           # Label text wrapper
  .ps-label__required       # Visual asterisk (*) for required fields

Modifiers:
  .ps-label--required       # Applied when required=true
  .ps-label--disabled       # Applied when disabled=true

Global Utilities:
  .ps-visually-hidden       # Screen reader only text (WCAG standard)
```

## Design Tokens Used

### Colors
- `--ps-color-text` / `--gray-900` — Default label text color
- `--ps-color-text-muted` / `--gray-500` — Disabled label color
- `--ps-color-error-600` / `--red-600` — Required indicator color (red asterisk)

### Typography
- `--ps-font-family-primary` / `--font-sans` — Font family
- `--ps-font-size-sm` / `--font-size-sm` — Label font size (12px)
- `--ps-font-weight-medium` / `--font-weight-600` — Default weight (semi-bold)
- `--ps-font-weight-bold` / `--font-weight-700` — Required asterisk weight
- `--leading-5` — Line height (20px)

### Spacing
- `--ps-spacing-1` / `--size-1` — Gap between text and asterisk (4px)
- `--ps-spacing-2` / `--size-2` — Bottom margin (8px)

## Variants

### Default
Standard label with black text, semi-bold weight, clickable cursor.

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
- Hidden text "(required field)" announced by screen readers via `.ps-visually-hidden`
- `.ps-visually-hidden` uses WCAG standard pattern (absolute positioning, 1px dimensions)

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

### 5. With Form-Field Molecule
Label is typically composed within `form-field` molecule:
```twig
{# form-field uses label internally #}
{{ include('@components/form-field/form-field.twig', {
  label: 'Your name',
  required: true,
  field: { type: 'text', placeholder: 'John Doe' }
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
- `form-field` — Combines label + field + helper/error
- `checkbox` — May use label pattern
- `radio` — May use label pattern

**Dependencies**: None (pure atom, no composed elements)

## Notes

- **Do NOT recreate label markup** in molecules - always compose this atom
- `.ps-visually-hidden` is a global utility, not component-specific
- Asterisk color matches error color for consistency
- `cursor: pointer` on default state for better UX (indicates clickability)
- Works with all input types: text, email, number, select, textarea, checkbox, radio
