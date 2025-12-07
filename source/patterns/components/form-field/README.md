# Form Field (Molecule)

Complete form field with label, input/select/textarea, helper text, and error message.

## Usage

```twig
{% include '@components/form-field/form-field.twig' with {
  label: 'Email',
  type: 'email',
  name: 'email',
  id: 'email',
  placeholder: 'your@email.com',
  required: true,
} only %}
```

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `label` | string | '' | Label text |
| `type` | string | 'text' | Field type (text, email, password, number, search, tel, url, textarea, select) |
| `name` | string | 'field' | Field name attribute |
| `id` | string | name | Field ID for label association |
| `value` | string | '' | Field value |
| `placeholder` | string | '' | Placeholder text |
| `required` | boolean | false | Required state |
| `disabled` | boolean | false | Disabled state |
| `helper` | string | '' | Helper/description text |
| `error` | string | '' | Error message (hides helper) |
| `rows` | number | 4 | Rows for textarea |
| `options` | array | [] | Options for select: `[{label, value, selected?}]` |
| `attributes` | Drupal Attribute | {} | HTML attributes for input/select/textarea |

## BEM

- Wrapper: `form-item`, `form-group`
- Label: `form-label`, `form-required`
- Control: `form-control`, `form-input`/`form-textarea`/`form-select`
- Helper: `form-helper`
- Error: `form-error`

## Notes

- Drupal-compatible form field
- Automatically switches between input types
- Error message takes precedence over helper text
