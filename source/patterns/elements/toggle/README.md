# ps-toggle

Accessible on/off switch for binary preferences (enable / disable). Uses a styled `<input type="checkbox">` with `role="switch"` for proper semantics.

## Props

| Prop        | Type    | Default  | Description |
|-------------|---------|----------|-------------|
| name        | string  | (none)   | Form field name attribute |
| label       | string  | (none)   | External visible label describing the preference |
| description | string  | (none)   | Optional helper text underneath |
| checked     | boolean | false    | Current switch state (true = on) |
| disabled    | boolean | false    | Disables interaction and reduces opacity |
| size        | string  | medium   | Visual size: small | medium | large |
| showLabels  | boolean | false    | Show internal ON/OFF labels inside track |
| onLabel     | string  | On       | Internal ON label (used when showLabels true) |
| offLabel    | string  | Off      | Internal OFF label (used when showLabels true) |

## BEM Structure

- `ps-toggle` (block)
- `ps-toggle__input` (hidden native input)
- `ps-toggle__track` (visual track)
- `ps-toggle__thumb` (movable knob)
- `ps-toggle__label` (inline label area wrapping input + track + text)
- `ps-toggle__description` (optional helper text)
- Modifiers: `ps-toggle--small` `ps-toggle--medium` `ps-toggle--large` `ps-toggle--disabled`
- Note: medium is default and does not add a modifier (keep markup minimal)

## Tokens

Toggle-specific size tokens added:
- `--ps-toggle-inset` (2px inner thumb inset)
- `--ps-toggle-width-small` / `--ps-toggle-width-medium` / `--ps-toggle-width-large`
- `--ps-toggle-height-small` / `--ps-toggle-height-medium` / `--ps-toggle-height-large`
- `--ps-toggle-thumb-small` / `--ps-toggle-thumb-medium` / `--ps-toggle-thumb-large`
- `--font-size-xxs` (9px internal ON/OFF label text)

Used along with existing spacing, color, radius, font-weight, and transition tokens. All geometry now derives from tokens (no raw px values in component CSS).

## Usage

```twig
{# Enabled preference #}
{% include '@ps_theme/toggle/toggle.twig' with {
  name: 'notifications',
  label: 'Enable notifications',
  checked: true
} %}

{# Disabled option #}
{% include '@ps_theme/toggle/toggle.twig' with {
  name: 'dark_mode',
  label: 'Dark mode',
  disabled: true,
  size: 'small'
} %}

{# With internal labels #}
{% include '@ps_theme/toggle/toggle.twig' with {
  name: 'auto_save',
  label: 'Auto save',
  showLabels: true,
  onLabel: 'On',
  offLabel: 'Off',
  size: 'large'
} %}
```

## Real Use Cases

- User preferences (notifications, newsletter)
- Display settings (dark mode, compact layout)
- Experimental feature flags (beta access)

## Accessibility

- Uses `role="switch"` + `aria-checked` for correct semantics
- Always provide a label (external or internal on/off text)
- Supports keyboard: space/enter toggles state
- Focus-visible outline must remain (do not remove for aesthetics)
- Disabled state still exposes label and description

## Guidelines

- Use switches for immediate single-value toggles (not multi-select groups)
- Avoid overuse of internal labels where external context is clear
- Group related preferences visually (cards, panels) instead of scattering individual switches
- Keep description concise; move long help text to contextual help patterns

