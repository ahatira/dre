# Radios (Molecule)

Drupal radios group wrapper.

## Usage

```twig
{% include '@components/radios/radios.twig' with {
  attributes: create_attribute(),
  children: rendered_radios,
} only %}
```

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `attributes` | Drupal Attribute | {} | HTML attributes for wrapper `<div>` |
| `children` | slot | — | Rendered radio items |

## BEM

- Block: `form-radios` (on wrapper `<div>`)

## Notes

- Direct Drupal template override
- Children should be individual radio inputs with labels
