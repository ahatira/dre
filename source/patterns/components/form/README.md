# Form (Molecule)

Drupal form wrapper element.

## Usage

```twig
{% include '@components/form/form.twig' with {
  attributes: create_attribute(),
  children: rendered_form,
} only %}
```

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `attributes` | Drupal Attribute | {} | HTML attributes for `<form>` |
| `children` | slot | — | Form content (fields, buttons, etc.) |

## BEM

- Block: `form` (on `<form>`)

## Notes

- Direct Drupal template override
- Use with Drupal form API render arrays
