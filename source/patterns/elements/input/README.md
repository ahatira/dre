# Input (Atom)

Base text-like input field without label, Drupal-friendly attributes. Apply a label via parent components (e.g., form-element) using the `id`/`for` association.

## Usage

```twig
{% include '@elements/input/input.twig' with {
  name: 'email',
  type: 'email',
  placeholder: 'you@example.com',
  id: 'email',
  required: true,
  attributes: attributes,
} only %}
```

## Props

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `type` | string | `text` | Input type (text, email, password, number, search, tel, url) |
| `name` | string | `''` | Input name attribute |
| `value` | string | `''` | Current value |
| `placeholder` | string | `''` | Placeholder text |
| `id` | string | `name` + `-input` | Input ID (used by external label) |
| `autocomplete` | string | `null` | Autocomplete attribute |
| `disabled` | boolean | `false` | Disabled state |
| `required` | boolean | `false` | Required state |
| `color` | string | `default` | Color variant (default, primary, secondary, info, warning, danger, success) |
| `size` | string | `md` | Size variant (xs, sm, md, lg, xl, xxl) |
| `attributes` | Drupal Attribute | `create_attribute()` | Attributes applied to `<input>` |
| `wrapper_attributes` | Drupal Attribute | `create_attribute()` | Attributes applied to wrapper div |

## BEM

- Block: `ps-input` (applied on `<input>`)  
- Element: `ps-input__wrapper`
- Modifiers: 
  - `ps-input--disabled`
  - `ps-input--primary`, `ps-input--secondary`, `ps-input--info`, `ps-input--warning`, `ps-input--danger`, `ps-input--success` (color variants)
  - `ps-input--xs`, `ps-input--sm`, `ps-input--md`, `ps-input--lg`, `ps-input--xl`, `ps-input--xxl` (size variants)

## CSS Variables (Layer 2)

- `--ps-input-width`
- `--ps-input-min-height`
- `--ps-input-padding-y`, `--ps-input-padding-x`
- `--ps-input-font-family`, `--ps-input-font-size`, `--ps-input-font-weight`, `--ps-input-line-height`
- `--ps-input-bg`, `--ps-input-color`, `--ps-input-border-color`, `--ps-input-placeholder-color`
- `--ps-input-hover-border-color`, `--ps-input-focus-border-color`, `--ps-input-focus-ring-color`, `--ps-input-focus-ring-width`
- `--ps-input-disabled-bg`, `--ps-input-disabled-color`, `--ps-input-disabled-border-color`, `--ps-input-disabled-opacity`
- `--ps-input-border-width`, `--ps-input-border-radius`
- `--ps-input-transition-duration`, `--ps-input-transition-timing`

## Variants

### Color Variants

- **default** (default): Standard gray border (`--border-default`)
- **primary**: Brand green border (`--primary`)
- **secondary**: Secondary blue border (`--secondary`)
- **info**: Informational blue border (`--info`)
- **warning**: Warning orange border (`--warning`)
- **danger**: Error red border (`--danger`)
- **success**: Success green border (`--success`)

Color variants modify border, focus ring, and related state colors via CSS custom properties.

### Size Variants

- **xs**: Extra small (height: 28px, font: 12px)
- **sm**: Small (height: 32px, font: 13px)
- **md** (default): Medium (height: 40px, font: 14px)
- **lg**: Large (height: 48px, font: 16px)
- **xl**: Extra large (height: 56px, font: 18px)
- **xxl**: Extra extra large (height: 64px, font: 20px)

Size variants adjust height, padding, and font-size via Layer 2 CSS variables.

## Accessibility

- Native `<input>` semantics; pass `id` and use an external `<label for="...">`.
- `disabled` adds `aria-disabled="true"`.
- `required` adds `aria-required="true"`.
- `autocomplete` supported when provided.
- Focus-visible styles ensure keyboard visibility.

## States

- Default / Hover / Focus-visible
- Disabled

## Notes

- Use with higher-level wrappers (e.g., form-element) for labels, helper text, errors.
- For textarea/select, use dedicated atoms instead of this input.
