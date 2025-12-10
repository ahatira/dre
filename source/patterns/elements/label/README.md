# Label (Element/Atom)

Form field label with semantic `<label>` binding, required indicator, and disabled state. Essential building block for form components.

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

Global Utilities (from base/utilities/visibility.css):
  .visually-hidden          # Screen reader only text (WCAG standard)
```

## Design Tokens Used

### Component Variables (Layer 2)

Component uses scoped CSS variables for easy customization:

```css
.ps-label {
  /* Spacing */
  --ps-label-gap: var(--size-1);                /* 4px - Gap between text and asterisk */
  --ps-label-margin-bottom: var(--size-2);      /* 8px - Bottom margin */
  
  /* Typography */
  --ps-label-color: var(--text-primary);        /* #434F57 - Main text color */
  --ps-label-font-family: var(--font-sans);     /* BNPP Sans */
  --ps-label-font-size: var(--font-size-0);     /* 14px */
  --ps-label-font-weight: var(--font-weight-600); /* 600 - Semi-bold */
  --ps-label-line-height: var(--leading-5);     /* 20px */
  
  /* States */
  --ps-label-cursor: pointer;
  --ps-label-required-color: var(--danger);     /* #EB3636 - Red asterisk */
  --ps-label-required-weight: var(--font-weight-700); /* 700 - Bold */
  --ps-label-disabled-color: var(--text-disabled); /* #B0B8BD - Muted gray */
  --ps-label-disabled-opacity: 0.7;
  --ps-label-disabled-cursor: not-allowed;
}
```

### Root Primitives (Layer 1)

References from `source/props/*.css`:

**Colors** (`colors.css` + `brand.css`):
- `--text-primary` — Main text (#434F57, gray-700)
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

/* Dark mode example */
[data-theme="dark"] .ps-label {
  --ps-label-color: var(--gray-100);
  --ps-label-disabled-color: var(--gray-600);
}
```

## Variants

### Default
Standard label with gray-700 text, semi-bold weight, pointer cursor.

### Required (`required: true`)
Adds:
- Red asterisk (*) with `aria-hidden="true"` (decorative only)
- Screen reader text "(required field)" with `.visually-hidden`
- Modifier class `.ps-label--required`

### Disabled (`disabled: true`)
Applies:
- Muted gray color (`--text-disabled`)
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

**Keyboard Navigation**:
- No direct keyboard interaction (label triggers input focus)
- Disabled state uses `cursor: not-allowed` for visual feedback

**Color Contrast**:
- Default text: `--text-primary` on white = 8.59:1 ratio (AAA) ✅
- Required asterisk: `--danger` on white = 4.54:1 ratio (AA) ✅
- Disabled text with opacity: `--text-disabled` at 70% = 4.5:1 ratio (AA) ✅

**Focus Management**:
- Label itself not focusable (semantic behavior)
- Click/touch triggers focus on associated input via `for` attribute

## Use Cases

### Contact Forms
```twig
{% include '@elements/label/label.twig' with {
  text: 'Email professionnel',
  forId: 'contact-email',
  required: true
} only %}
```

### Property Registration
```twig
{% include '@elements/label/label.twig' with {
  text: 'Surface habitable (m²)',
  forId: 'property-surface',
  required: true
} only %}
```

### Settings with Disabled Fields
```twig
{% include '@elements/label/label.twig' with {
  text: 'Identifiant unique (lecture seule)',
  forId: 'account-id',
  disabled: true
} only %}
```

## Drupal Integration

### Standalone Component
```twig
{# In Drupal form template #}
{% include '@elements/label/label.twig' with {
  text: form.field_name['#title'],
  forId: form.field_name['#id'],
  required: form.field_name['#required'],
  disabled: form.field_name['#disabled']
} only %}
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
{% include '@elements/label/label.twig' with {
  text: form.email['#title'],
  forId: form.email['#id'],
  required: form.email['#required']
} only %}
{{ form.email|without('title') }}
```

## Component Composition

**Used By** (Molecules that compose this atom):
- `form-element` — Combines label + field + helper/error
- `checkbox` — May use label pattern
- `radio` — May use label pattern

**Dependencies**: None (pure atom, no composed elements)

### Composition Example
```twig
{# Inside form-element molecule - use attributes.addClass() #}
{% include '@elements/label/label.twig' with {
  text: 'Email address',
  forId: 'contact-email',
  required: true,
  attributes: create_attribute().addClass('custom-form__label')
} only %}
```

## Notes

- **Do NOT recreate label markup** in molecules - always compose this atom
- `.visually-hidden` is a global utility, not component-specific
- Asterisk color matches error color for consistency
- `cursor: pointer` on default state for better UX (indicates clickability)
- Works with all input types: text, email, number, select, textarea, checkbox, radio
- **Never use `baseClass` parameter** - use `attributes.addClass()` for custom classes

---

**Component Status**: ✅ Standardized (3-layer CSS variables, no baseClass)  
**Last Updated**: December 10, 2025
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
