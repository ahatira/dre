# FormField Component

Complete form field molecule that wraps the ps-field atom with label, helper text, and error message support.

## Overview

FormField combines all necessary elements for accessible form inputs: label with required indicator, input field with pass-through props, optional helper text, and error message with ARIA announcements. Provides consistent form field patterns across the entire design system.

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `label` | string | (required) | Label text for the field |
| `id` | string | auto-generated | Unique ID for label/field association |
| `field` | object | `{}` | Props passed through to ps-field atom (see below) |
| `helperText` | string | `''` | Optional helper text below field |
| `error` | string | `''` | Error message (replaces helper, sets error state) |
| `required` | boolean | `false` | Mark field as required (shows asterisk) |
| `disabled` | boolean | `false` | Disable entire field group |
| `attributes` | Attribute | — | Additional HTML attributes for wrapper |

### Field Object Props (Pass-through to ps-field)

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `field.type` | string | `'text'` | Input type: text, email, number, search, textarea, select |
| `field.value` | string | `''` | Current field value |
| `field.placeholder` | string | `''` | Placeholder text |
| `field.disabled` | boolean | `false` | Disabled state (overridden by form-field disabled) |
| `field.icon` | string | `''` | Icon name without "icon-" prefix |
| `field.iconPosition` | string | `'right'` | Icon position: left or right |

## BEM Structure

```
ps-form-field                           # Base wrapper
├── ps-label.ps-form-field__label       # Label atom with context class
│   ├── ps-label__text                  # Label text
│   ├── ps-label__required              # Asterisk for required fields
│   └── ps-visually-hidden              # Screen reader text "(required field)"
├── ps-form-field__input-wrapper        # Input wrapper (contains ps-field)
├── ps-form-field__helper               # Helper text (hidden when error present)
└── ps-form-field__error                # Error message (role="alert")

Modifiers:
├── ps-form-field--required             # Required field (only adds asterisk, no styles)
├── ps-form-field--error                # Error state (red label, shows error message)
└── ps-form-field--disabled             # Disabled state (reduced opacity, no pointer)
```

## Design Tokens and Layer 2 Variables

### Layer 1 Tokens
- Spacing: `--size-1`, `--size-2`, `--size-3`, `--size-4`
- Typography: `--font-sans`, `--font-size-0`, `--font-size--1`, `--font-weight-400`, `--font-weight-500`, `--font-weight-600`, `--leading-tight`, `--leading-snug`
- Colors: `--text-primary`, `--text-secondary`, `--gray-600`, `--gray-500`, `--danger`
- Borders: `--border-size-2`

### Layer 2 (Component-Scoped) Defaults
- `--ps-form-field-gap` (vertical spacing between label/field/helper)
- `--ps-form-field-label-*` (font family/size/weight/color)
- `--ps-form-field-helper-*` (font family/size/weight/line-height/color)
- `--ps-form-field-error-*` (font, padding, gap, accent width/color, background via `color-mix`)
- `--ps-form-field-disabled-*` (opacity, text color)
- `--ps-form-field-input-error-border` (passes error border to `ps-field` atom)

These variables can be overridden by modifiers (`--error`, `--disabled`) or external contexts while keeping Layer 1 tokens intact.

## Usage Examples

### Basic Field with Helper Text

```twig
{% include '@components/form-field/form-field.twig' with {
  label: 'Email Address',
  field: {
    type: 'email',
    placeholder: 'example@domain.com',
  },
  helperText: 'We will never share your email with anyone.',
} %}
```

### Required Field

```twig
{% include '@components/form-field/form-field.twig' with {
  label: 'Full Name',
  field: {
    type: 'text',
    placeholder: 'Jean Dupont',
  },
  required: true,
} %}
```

### Field with Error

```twig
{% include '@components/form-field/form-field.twig' with {
  label: 'Email Address',
  field: {
    type: 'email',
    value: 'invalid-email',
  },
  error: 'Please enter a valid email address.',
  required: true,
} %}
```

### Field with Icon

```twig
{% include '@components/form-field/form-field.twig' with {
  label: 'Search',
  field: {
    type: 'search',
    placeholder: 'Search properties...',
    icon: 'search',
    iconPosition: 'right',
  },
  helperText: 'Enter keywords to search our database.',
} %}
```

### Textarea Field

```twig
{% include '@components/form-field/form-field.twig' with {
  label: 'Description',
  field: {
    type: 'textarea',
    placeholder: 'Enter details...',
  },
  helperText: 'Provide additional information (optional).',
} %}
```

### Disabled Field

```twig
{% include '@components/form-field/form-field.twig' with {
  label: 'Account Type',
  field: {
    type: 'text',
    value: 'Premium Member',
  },
  disabled: true,
  helperText: 'This field cannot be modified.',
} %}
```

## Accessibility

### Label Association
- Label uses `for` attribute connected to field `id`
- Auto-generates unique ID if not provided
- Screen readers announce label when field receives focus
- Field input receives the same `id` plus `aria-describedby` pointing to helper or error and `aria-errormessage` when an error is present

### Required Fields
- Visual asterisk indicator with `aria-label="required"`
- Semantic `required` attribute passed to field
- Clear visual distinction (red asterisk)

### Error Announcements
- Error messages use `role="alert"` and `aria-live="polite"`
- Screen readers announce errors immediately when they appear
- Error message ID linked to field via `aria-describedby`
- Label changes color to red for visual indication

### Helper Text
- Helper text provides additional context
- Linked to field via `aria-describedby`
- Hidden when error is present (error replaces helper)

### Disabled State
- Sets `disabled` and `aria-disabled="true"` on field
- Reduced opacity (0.6) for visual indication
- `pointer-events: none` prevents interaction
- Cursor changes to `not-allowed`

### Keyboard Navigation
- Full keyboard support inherited from ps-field
- Tab navigation between fields
- Focus-within effect highlights active field

### Color Contrast
- All text meets WCAG 2.2 AA standards (4.5:1 minimum)
- Error red (#EB3636) has sufficient contrast on white
- Disabled state maintains readable contrast

## Real-World Use Cases

1. **Contact Forms** - Name, email, phone, message fields
2. **Login/Registration** - Email, password, confirmation fields
3. **Search Filters** - Search inputs with helper instructions
4. **Profile Settings** - User information updates
5. **Property Listings** - Size, price, location inputs
6. **Feedback Forms** - Textarea fields with character guidance
7. **Newsletter Signup** - Email subscription with privacy notice

## Integration with Drupal

### Rendering in Twig Templates

```twig
{# Render a Drupal form field #}
{% include '@components/form-field/form-field.twig' with {
  label: field.label,
  id: field.id,
  field: {
    type: field.type,
    value: field.value,
    placeholder: field.placeholder,
    disabled: field.disabled,
  },
  error: field.errors|first,
  required: field.required,
  helperText: field.description,
} %}
```

### Form API Integration

```php
$form['email'] = [
  '#type' => 'textfield',
  '#title' => t('Email Address'),
  '#required' => TRUE,
  '#attributes' => [
    'placeholder' => t('example@domain.com'),
  ],
  '#description' => t('We will send a confirmation email.'),
];
```

### Validation Messages

Error messages automatically display via FormField's error prop:

```php
$form_state->setErrorByName('email', t('Please enter a valid email address.'));
```

## Browser Support

- Chrome/Edge 90+ (CSS nesting via PostCSS)
- Firefox 88+
- Safari 14+
- Mobile browsers (iOS Safari 14+, Chrome Android 90+)

## Related Components

- **Field** (atom) - Base input/textarea/select component
- **Label** (atom) - Standalone label element
- **Checkbox** (atom) - Alternative input type
- **Radio** (atom) - Alternative input type
- **Toggle** (atom) - Alternative input type

## Component Status

- ✅ Design complete
- ✅ Development complete
- ✅ Accessibility reviewed
- ✅ Documentation complete
- ⏳ Pending visual validation

## Future Enhancements

1. **Inline Layout Variant** - Label and field side-by-side (ps-form-field--inline)
2. **Character Counter** - Show remaining characters for textarea
3. **Field Groups** - Multiple related fields in one group
4. **Floating Labels** - Label animates to top when focused
5. **Autocomplete Support** - Integration with autocomplete/dropdown
6. **Multi-step Validation** - Progressive validation indicators

## Changelog

### Version 1.1.0 (2025-12-01)
- ✅ Refactored to use Label atom (composition instead of direct markup)
- ✅ Better Atomic Design hierarchy (molecule → atom)
- ✅ Label class override via attributes for form-field context styles

### Version 1.0.0 (2025-12-01)
- ✅ Initial implementation with complete feature set
- ✅ 5 Storybook stories (Default, AllStates, WithIcon, AllFieldTypes, InFormContext)
- ✅ Full accessibility support (ARIA, label association, error announcements)
- ✅ Pass-through props to ps-field atom
- ✅ Required field indicator with asterisk
- ✅ Error state replaces helper text
- ✅ Disabled state with opacity and pointer-events
- ✅ Focus-within label color change
- ✅ 100% design tokens, 0 hardcoded values
- ✅ BEM strict with ps-form-field prefix
- ✅ CSS nesting with & syntax
- ✅ Minimal markup (defaults don't add classes)
