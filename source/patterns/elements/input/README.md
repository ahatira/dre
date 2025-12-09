# Input (Atom)

Base input field (without label, icon, or helper). This is an **ATOM** in the Atomic Design hierarchy.

For a complete input with label, helper text, error messages, and/or icons, use the **Form-element** (Molecule) component instead.

## Atomic Design Note

- **Input (Atom)** = Single element (`<input>`)
- **Form-element (Molecule)** = Input + Label + Helper + Icon wrapper
- **Form (Organism)** = Complete form with multiple Form-elements

## Usage

```twig
{% include '@elements/input/input.twig' with {
  name: 'email',
  type: 'email',
  placeholder: 'you@example.com',
  id: 'email',
  required: true,
  state: 'error',
  attributes: attributes,
} only %}
```

## Props

| Prop | Type | Default | Description |
| --- | --- | --- | --- |
| `type` | string | `text` | HTML input type (text, email, password, number, search, tel, url) |
| `name` | string | `''` | Input name attribute (recommended) |
| `value` | string | `''` | Current value |
| `placeholder` | string | `''` | Placeholder text |
| `id` | string | `name` + `-input` | Input ID (for label association) |
| `autocomplete` | string | `null` | HTML5 autocomplete attribute |
| `disabled` | boolean | `false` | Disabled state |
| `required` | boolean | `false` | Required field |
| `state` | string | `null` | Validation state: `null` \| `'error'` \| `'success'` \| `'warning'` |
| `attributes` | Drupal Attribute | `create_attribute()` | Attributes applied to `<input>` |

## BEM

- **Block**: `ps-input` (applied to `<input>`)  
- **Modifiers**: 
  - `ps-input--disabled` (disabled state)
  - `ps-input--error` (red border, validation failed)
  - `ps-input--success` (green border, validation passed / "Done" in mockup)
  - `ps-input--warning` (orange border, warning)

## Design Tokens (3 Layers)

### Layer 1: Global Tokens
- Colors: `--border-default`, `--text-primary`, `--danger`, `--success`, `--warning`, `--white`, `--gray-100`, `--gray-300`, `--gray-400`, `--gray-500`
- Spacing: `--size-2`, `--size-4`, `--size-10`
- Typography: `--font-sans`, `--font-size-1`, `--font-weight-400`, `--leading-6`
- Borders: `--border-size-2`
- Transitions: `--duration-fast`, `--ease-3`

### Layer 2: Component Tokens
- `--ps-input-width`: Width (default: 100%)
- `--ps-input-min-height`: Minimum height (default: 40px / `--size-10`)
- `--ps-input-padding-y`, `--ps-input-padding-x`: Internal spacing
- `--ps-input-font-family`, `--ps-input-font-size`, `--ps-input-font-weight`, `--ps-input-line-height`
- `--ps-input-bg`, `--ps-input-color`: Background and text colors
- `--ps-input-border-color`, `--ps-input-hover-border-color`, `--ps-input-focus-border-color`
- `--ps-input-placeholder-color`: Placeholder color
- `--ps-input-disabled-bg` (gray 100), `--ps-input-disabled-color` (gray 500), `--ps-input-disabled-border-color`
- `--ps-input-border-width`, `--ps-input-border-radius` (0 = square angles)
- `--ps-input-transition-duration`, `--ps-input-transition-timing`

### Layer 3: Modifier Overrides
Modifiers (`--error`, `--success`, `--warning`) override border tokens for validation states.

## States

### Interactive States (CSS automatic)
- **Default**: Default state, gray border (`--border-default`), square angles
- **Placeholder**: Placeholder text visible, gray border
- **Hover**: Darker gray border (`:hover`)
- **Focus**: Thick black border (2px) (`:focus-visible`) - **WCAG 2.2 AA visible focus**
- **Disabled**: Gray 100 background, gray 500 text, gray 300 border

### Validation States (via `state` prop)
- **success** (mockup: "Done"): Green border (`--success`)
- **error**: Red border (`--danger`), `aria-invalid="true"`
- **warning**: Orange border (`--warning`)

## Icon Management (Atomic Design Focus)

### ⚠️ **Input Has NO Icons**

Input is an **ATOM** and must remain minimal. Icons (left/right) must be managed at the **MOLECULE** level (Form-element).

**Correct Architecture**:

```
Input (Atom) = <input class="ps-input">
                └─ No child elements
                └─ No wrapper
                └─ No icons

Form-element (Molecule) = <div class="ps-form-element">
  ├─ <label class="ps-form-element__label">
  ├─ <div class="ps-form-element__input-wrapper">
  │   ├─ <svg class="ps-form-element__icon--left"> (optional)
  │   ├─ <input class="ps-input">
  │   ├─ <svg class="ps-form-element__icon--right"> (optional)
  │   └─ Padding-left/right adjusted if icons present
  ├─ <span class="ps-form-element__helper">
  └─ <span class="ps-form-element__error">
```

**Mockup with icons** = Preview of Form-element component, not Input alone.

## Accessibility

- Native HTML semantics `<input>`
- Association with external label via `id` and `<label for="...">`
- `disabled` adds `aria-disabled="true"`
- `required` adds `aria-required="true"`
- `state="error"` adds `aria-invalid="true"`
- `autocomplete` attribute supported for improved UX
- **Visible focus** (`:focus-visible`) with 2px border for WCAG 2.2 AA compliance

## Use Cases

### With external label
```twig
<label for="email-input">Email address</label>
{% include '@elements/input/input.twig' with {
  id: 'email-input',
  name: 'email',
  type: 'email',
  placeholder: 'you@example.com',
  required: true
} only %}
```

### Validation error
```twig
{% include '@elements/input/input.twig' with {
  name: 'email',
  type: 'email',
  value: 'invalid-email',
  state: 'error'
} only %}
```

### With success (Done in mockup)
```twig
{% include '@elements/input/input.twig' with {
  name: 'email',
  type: 'email',
  value: 'you@example.com',
  state: 'success'
} only %}
```

### Disabled input
```twig
{% include '@elements/input/input.twig' with {
  name: 'readonly-field',
  value: 'Read-only',
  disabled: true
} only %}
```

### Real estate search (no icon at atom level)
```twig
{% include '@elements/input/input.twig' with {
  name: 'search',
  type: 'search',
  placeholder: 'Search for a property...',
  autocomplete: 'off'
} only %}
```

### With icon (Form-element molecule, not Input atom)
```twig
{# To be implemented at Form-element level #}
{% include '@molecules/form-element/form-element.twig' with {
  label: 'Search',
  icon_left: 'search',
  input_type: 'search',
  placeholder: 'Search for a property...'
} only %}
```

## Important Notes

- **STRICT Atomic Design**: Input = ATOM (field only, no child elements). Icons/Label = MOLECULE.
- **Mockup with icons** = Preview of Form-element component, not Input atom responsibility.
- **No thematic color variants**: Only validation states (`error`, `success`, `warning`).
- **No size variants**: Fixed height 40px. For variations, create a new component.
- **Visible focus ring**: 2px border for WCAG 2.2 AA compliance.
- For textarea/select, use dedicated atoms.

## Conformity Checklist

- ✅ **Atomic Design**: Pure ATOM (field only, no children)
- ✅ **Twig**: Defaults, ternaries, no arrow functions
- ✅ **CSS**: 3-layer system, nesting, semantic tokens, visible focus
- ✅ **Stories**: Autodocs enabled, varied states WITHOUT icons
- ✅ **Accessibility**: WCAG 2.2 AA, ARIA, 2px focus-visible
- ✅ **BEM**: Strict nomenclature, conditional modifiers

---

**Status**: ✅ COMPLIANT (Atom)  
**To implement**: Form-element (Molecule) for label/icon management  
**Last updated**: December 9, 2025  
**Maintainer**: Design System Team

