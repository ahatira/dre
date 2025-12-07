# Checkboxes (Molecule)

Drupal checkboxes group wrapper.

## Usage

```twig
{% include '@components/checkboxes/checkboxes.twig' with {
  attributes: create_attribute(),
  children: rendered_checkboxes,
} only %}
```

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `attributes` | Drupal Attribute | {} | HTML attributes for wrapper `<div>` |
| `children` | slot | — | Rendered checkbox items |

## BEM

- Block: `form-checkboxes` (on wrapper `<div>`)

## Notes

- Direct Drupal template override
- Children should be individual checkbox inputs with labels
