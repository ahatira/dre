# Radio

**Version**: 2.0.0  
**Status**: ✅ Stable  
**Type**: Atom / Element

Native accessible radio button for single selection within a group. Uses BNP RE icon font for visual rendering (icon-radio-unselected / icon-radio-selected).

---

## Props

| Prop | Type | Default | Required | Description |
|------|------|---------|----------|-------------|
| `name` | `string` | `'option'` | ✅ | Radio group name (same name = single selection) |
| `value` | `string` | `'1'` | ✅ | Unique value for this radio button |
| `label` | `string` | `'Option label'` | ❌ | Visible label text next to radio |
| `checked` | `boolean` | `false` | ❌ | Initial checked state |
| `disabled` | `boolean` | `false` | ❌ | Disabled state |

---

## BEM Structure

```css
.ps-radio                    ← Container <label>
  .ps-radio__input           ← Input radio (visually hidden)
  .ps-radio__circle          ← Icon circle (renders via ::before)
  .ps-radio__label           ← Text label

Modifiers:
  .ps-radio--disabled        ← Disabled state
```

---

## Design Tokens Used

### Component Variables (Layer 2)

All customizable via CSS custom properties:

#### Layout

- `--ps-radio-gap` ← `--size-2` (8px) — Gap between circle and label
- `--ps-radio-align` ← `flex-start` — Vertical alignment
- `--ps-radio-circle-size` ← `--size-5` (20px) — Circle size

#### Icon Colors

- `--ps-radio-icon-color-unchecked` ← `--text-primary` — Icon color unselected
- `--ps-radio-icon-color-checked` ← `--primary` — Icon color selected (green)
- `--ps-radio-icon-size` ← `--font-size-2` (18px) — Icon font size

#### Label Colors

- `--ps-radio-label-color` ← `--text-primary` — Label text color
- `--ps-radio-label-color-checked` ← `--primary` — Label color when selected

#### Typography

- `--ps-radio-label-font-family` ← `--font-sans` — Label font family
- `--ps-radio-label-font-size` ← `--font-size-1` (16px) — Label font size
- `--ps-radio-label-font-weight` ← `--font-weight-400` — Label font weight
- `--ps-radio-label-line-height` ← `--leading-6` (24px) — Label line height

#### Focus State

- `--ps-radio-focus-outline-width` ← `--border-size-2` (2px) — Focus outline width
- `--ps-radio-focus-outline-color` ← `--border-focus` — Focus outline color
- `--ps-radio-focus-outline-offset` ← `--border-size-2` (2px) — Focus outline offset

#### Transitions

- `--ps-radio-transition-duration` ← `--duration-fast` (0.15s) — Transition duration
- `--ps-radio-transition-timing` ← `--ease-3` — Transition timing function

#### States

- `--ps-radio-disabled-opacity` ← `0.5` — Opacity when disabled

---

## Usage Examples

### Basic

```twig
{% include '@elements/radio/radio.twig' with {
  name: 'property-type',
  value: 'apartment',
  label: 'Apartment',
} %}
```

### Checked

```twig
{% include '@elements/radio/radio.twig' with {
  name: 'property-type',
  value: 'house',
  label: 'House',
  checked: true,
} %}
```

### Disabled

```twig
{% include '@elements/radio/radio.twig' with {
  name: 'property-type',
  value: 'commercial',
  label: 'Commercial (coming soon)',
  disabled: true,
} %}
```

### Radio Group

```twig
<fieldset>
  <legend>Select property type</legend>
  {% include '@elements/radio/radio.twig' with {
    name: 'property-type',
    value: 'apartment',
    label: 'Apartment',
  } %}
  {% include '@elements/radio/radio.twig' with {
    name: 'property-type',
    value: 'house',
    label: 'House',
    checked: true,
  } %}
  {% include '@elements/radio/radio.twig' with {
    name: 'property-type',
    value: 'commercial',
    label: 'Commercial Property',
  } %}
</fieldset>
```

---

## Real-World Use Cases

1. **Property Type Selection** — Apartment, House, Commercial, Land
2. **Listing Status** — For Sale, For Rent, Sold
3. **Mortgage Type** — Fixed Rate, Variable Rate, Interest Only
4. **Contact Preference** — Email, Phone, In-Person Visit
5. **Property Condition** — New, Renovated, Original Condition

---

## Accessibility

- ✅ Native `<input type="radio">` for keyboard support
- ✅ `aria-hidden="true"` on decorative circle element
- ✅ Clickable label via `<label>` wrapper
- ✅ Focus visible via `outline` on `:focus-visible`
- ✅ Disabled state via native `disabled` attribute
- ⚠️ **Group with `<fieldset>` + `<legend>`** for contextual clarity

---

## Browser Support

✅ All modern browsers (Chrome, Firefox, Safari, Edge)  
✅ Requires BNP RE icon font

---

## Notes

- **Icon Font Rendering**: Uses `\e86a` (radio-unselected) and `\e869` (radio-selected) from `bnpre-icons`
- **Native Accessibility**: Semantic `<input type="radio">` with visual icon overlay
- **Keyboard Support**: Full keyboard navigation and focus-visible states
- **Group behavior**: Radios with same `name` form single selection group
- **Minimal HTML**: No unnecessary conditional classes
- **Bootstrap 5 Inspired**: Component-scoped CSS variables for easy theming
- **Smooth Transitions**: Icon color transitions on selection change
