# Radio Item (Atom-like)

Individual radio input with label wrapper.

## Usage

```twig
{% include '@elements/radio-item/radio-item.twig' with {
  name: 'property_type',
  value: 'apartment',
  label: 'Apartment',
  id: 'property-apartment',
} only %}
```

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `name` | string | 'option' | Input name attribute (group identifier) |
| `value` | string | '1' | Input value attribute |
| `id` | string | auto | Input ID for label association |
| `label` | string | '' | Label text |
| `checked` | boolean | false | Whether radio is checked |
| `disabled` | boolean | false | Whether radio is disabled |
| `attributes` | Drupal Attribute | {} | HTML attributes for input |

## BEM

- Wrapper: `form-item`, `form-type-radio`
- Input: `form-radio`

## Notes

- Direct Drupal template element
- Use within Radios group or standalone
- All radios in group must share same `name`
