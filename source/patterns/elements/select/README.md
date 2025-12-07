# Select (Atom)

Native `<select>` without label, Drupal-friendly attributes. Use external labels (via `for`/`id`) in higher-level components.

## Usage

```twig
{% include '@elements/select/select.twig' with {
  name: 'country',
  id: 'country',
  options: [
    { value: '', label: 'Select your country', disabled: true, selected: true },
    { value: 'fr', label: 'France' },
    { value: 'es', label: 'Spain' }
  ],
  attributes: attributes,
} only %}
```

## Props

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `options` | array | `[]` | List of `{ value, label, disabled?, selected? }` |
| `name` | string | `''` | Name attribute |
| `id` | string | `name + '-select'` | ID attribute |
| `disabled` | boolean | `false` | Disabled state |
| `required` | boolean | `false` | Required state |
| `color` | string | `default` | Color variant (default, primary, secondary, info, warning, danger, success) |
| `size` | string | `md` | Size variant (xs, sm, md, lg, xl, xxl) |
| `attributes` | Drupal Attribute | `create_attribute()` | Attributes for `<select>` |
| `wrapper_attributes` | Drupal Attribute | `create_attribute()` | Attributes for wrapper div |

## BEM

- Block: `ps-select` (on `<select>`)  
- Element: `ps-select__wrapper`  
- Modifiers:
  - `ps-select--disabled`
  - `ps-select--primary`, `ps-select--secondary`, `ps-select--info`, `ps-select--warning`, `ps-select--danger`, `ps-select--success` (color variants)
  - `ps-select--xs`, `ps-select--sm`, `ps-select--md`, `ps-select--lg`, `ps-select--xl`, `ps-select--xxl` (size variants)

## CSS Variables (Layer 2)

- Dimensions: `--ps-select-width`, `--ps-select-min-height`, `--ps-select-padding-y`, `--ps-select-padding-x`, `--ps-select-padding-right`
- Typography: `--ps-select-font-family`, `--ps-select-font-size`, `--ps-select-font-weight`, `--ps-select-line-height`
- Colors: `--ps-select-bg`, `--ps-select-color`, `--ps-select-border-color`, `--ps-select-placeholder-color`
- States: `--ps-select-hover-border-color`, `--ps-select-focus-border-color`, `--ps-select-focus-ring-color`, `--ps-select-focus-ring-width`
- Disabled: `--ps-select-disabled-bg`, `--ps-select-disabled-color`, `--ps-select-disabled-border-color`, `--ps-select-disabled-opacity`
- Borders: `--ps-select-border-width`, `--ps-select-border-radius`
- Transitions: `--ps-select-transition-duration`, `--ps-select-transition-timing`

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

- Native `<select>` semantics.
- `disabled` adds `aria-disabled="true"`.
- `required` adds `aria-required="true"`.
- Use external label + `for`/`id` association via parents.

## States

- Default / Hover / Focus-visible / Disabled

## Notes

- Arrow icon relies on native rendering; no custom sprite to stay token-only.
- For grouped selects or searchable dropdowns, use higher-level components.
