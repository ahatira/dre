# Textarea (Atom)

Native `<textarea>` without label, Drupal-friendly attributes. Use external labels via `for`/`id` at higher levels.

## Usage

```twig
{% include '@elements/textarea/textarea.twig' with {
  name: 'message',
  id: 'message',
  rows: 6,
  placeholder: 'Your message...',
  attributes: attributes,
} only %}
```

## Props

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `name` | string | `''` | Name attribute |
| `id` | string | `name + '-textarea'` | ID attribute |
| `value` | string | `''` | Initial content |
| `placeholder` | string | `''` | Placeholder text |
| `rows` | number | `4` | Rows attribute |
| `disabled` | boolean | `false` | Disabled state |
| `required` | boolean | `false` | Required state |
| `color` | string | `default` | Color variant (default, primary, secondary, info, warning, danger, success) |
| `size` | string | `md` | Size variant (xs, sm, md, lg, xl, xxl) |
| `attributes` | Drupal Attribute | `create_attribute()` | Attributes for `<textarea>` |
| `wrapper_attributes` | Drupal Attribute | `create_attribute()` | Attributes for wrapper div |

## BEM

- Block: `ps-textarea` (on `<textarea>`)  
- Element: `ps-textarea__wrapper`  
- Modifiers:
  - `ps-textarea--disabled`
  - `ps-textarea--primary`, `ps-textarea--secondary`, `ps-textarea--info`, `ps-textarea--warning`, `ps-textarea--danger`, `ps-textarea--success` (color variants)
  - `ps-textarea--xs`, `ps-textarea--sm`, `ps-textarea--md`, `ps-textarea--lg`, `ps-textarea--xl`, `ps-textarea--xxl` (size variants)

## CSS Variables (Layer 2)

- Dimensions: `--ps-textarea-width`, `--ps-textarea-min-height`, `--ps-textarea-padding-y`, `--ps-textarea-padding-x`
- Typography: `--ps-textarea-font-family`, `--ps-textarea-font-size`, `--ps-textarea-font-weight`, `--ps-textarea-line-height`
- Colors: `--ps-textarea-bg`, `--ps-textarea-color`, `--ps-textarea-border-color`, `--ps-textarea-placeholder-color`
- States: `--ps-textarea-hover-border-color`, `--ps-textarea-focus-border-color`, `--ps-textarea-focus-ring-color`, `--ps-textarea-focus-ring-width`
- Disabled: `--ps-textarea-disabled-bg`, `--ps-textarea-disabled-color`, `--ps-textarea-disabled-border-color`, `--ps-textarea-disabled-opacity`
- Borders: `--ps-textarea-border-width`, `--ps-textarea-border-radius`
- Transitions: `--ps-textarea-transition-duration`, `--ps-textarea-transition-timing`

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

- **xs**: Extra small (min-height: 28px, font: 12px)
- **sm**: Small (min-height: 32px, font: 13px)
- **md** (default): Medium (min-height: 40px, font: 14px)
- **lg**: Large (min-height: 48px, font: 16px)
- **xl**: Extra large (min-height: 56px, font: 18px)
- **xxl**: Extra extra large (min-height: 64px, font: 20px)

Size variants adjust minimum height, padding, and font-size via Layer 2 CSS variables.

## Accessibility

- Native `<textarea>` semantics.
- `disabled` adds `aria-disabled="true"`; `required` adds `aria-required="true"`.
- Use external label + `for`/`id` association via parent components.

## States

- Default / Hover / Focus-visible / Disabled

## Notes

- Resizable vertically only.
- For helper/error text, compose with higher-level wrappers (form-element).
