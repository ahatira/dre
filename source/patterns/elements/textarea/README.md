# Textarea (Atom)

Native `<textarea>` without label. External labels are handled upstream via `for`/`id` and this atom exposes validation, sizing, and semantic color variants.

## Usage


```twig
{% include '@elements/textarea/textarea.twig' with {
  name: 'message',
  id: 'message-textarea',
  placeholder: 'Décrivez votre besoin immobilier...',
  rows: 6,
  state: null
} only %}
```

Example with external label:

```twig
<label for="message-textarea">Votre message</label>
{% include '@elements/textarea/textarea.twig' with {
  name: 'message',
  id: 'message-textarea',
  value: 'Je souhaite visiter le loft proche de La Defense',
  required: true
} only %}
```

## Props

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `name` | string | `message` | Name attribute |
| `id` | string | `name + '-textarea'` | ID attribute |
| `value` | string | `''` | Initial content |
| `placeholder` | string | `Decrivez votre besoin immobilier (budget, localisation, surface)...` | Placeholder text |
| `rows` | number | `4` | Native `rows` attribute |
| `disabled` | boolean | `false` | Disabled state (adds `aria-disabled`) |
| `required` | boolean | `false` | Required state (adds `aria-required`) |
| `size` | string | `md` | Size variant: xs, sm, md, lg, xl, xxl |
| `size` | string | `md` | Size variant: xs, sm, md, lg, xl, xxl |
| `state` | string \| null | `null` | Validation state: error, success, warning (sets `aria-invalid` on error) |
| `attributes` | Drupal Attribute | `create_attribute()` | Attributes applied to `<textarea>` |

## BEM

- Block: `ps-textarea`
- Modifiers:
  - `ps-textarea--disabled`
  - `ps-textarea--{size}` (xs, sm, md, lg, xl, xxl)
  - `ps-textarea--error`, `ps-textarea--success`, `ps-textarea--warning`

## CSS Variables (Layer 2)

- Dimensions: `--ps-textarea-width`, `--ps-textarea-min-height`, `--ps-textarea-padding-block`, `--ps-textarea-padding-inline`
- Typography: `--ps-textarea-font-family`, `--ps-textarea-font-size`, `--ps-textarea-font-weight`, `--ps-textarea-line-height`
- Colors: `--ps-textarea-bg`, `--ps-textarea-color`, `--ps-textarea-border-color`, `--ps-textarea-placeholder-color`
- States: `--ps-textarea-hover-border-color`, `--ps-textarea-focus-border-color`, `--ps-textarea-focus-ring-color`, `--ps-textarea-focus-ring-width`
- Disabled: `--ps-textarea-disabled-bg`, `--ps-textarea-disabled-color`, `--ps-textarea-disabled-border-color`, `--ps-textarea-disabled-opacity`
- Borders: `--ps-textarea-border-width`, `--ps-textarea-border-radius`
- Transitions: `--ps-textarea-transition-duration`, `--ps-textarea-transition-timing`

## Variants


- **Sizes**: xs, sm, md, lg, xl, xxl adjust min-height, padding, and font size through Layer 2 variables (md matches la maquette).
- **Validation**: `state` peut être error, success ou warning ; error ajoute `aria-invalid="true"` dans le template.

## Accessibility

- Focus visible conforme WCAG 2.2 AA (border-color et ring visible, pas de shadow, radius 0).
- `disabled` ajoute `aria-disabled="true"` ; `required` ajoute `aria-required="true"` ; `state: 'error'` ajoute `aria-invalid="true"`.
- Toujours associer un `<label>` externe avec l’attribut `for`/`id` pour l’accessibilité.

## Notes

- Redimensionnement vertical uniquement.
- À combiner avec les composants de formulaire pour afficher l’aide ou les messages d’erreur.
