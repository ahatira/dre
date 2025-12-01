# PS Icon (Element)

Icon component using the project icon font and global `icons.css` mappings via a CSS pseudo-element. Minimal markup and strict BEM with `ps-` prefix, token-only sizes and colors.

## Description
- Renders an icon glyph using `<span class="ps-icon"><span class="ps-icon__icon" data-icon="name"></span></span>`.
- Sizes map to tokens: `--ps-icon-size-16|20|24|32`.
- Colors use semantic/base tokens: `var(--gray-600|400)`, `var(--primary)`, `var(--white)`.
- States are independent modifiers: `--default|--disabled|--hover|--selected`.

## Props
- `name` (string, required): unique icon name (e.g. `arrow-right`).
- `size` (number, enum: 16|20|24|32, default 24): icon size.
- `state` (string, enum: default|disabled|hover|selected, default `default`).
- `colorVariant` (string, enum: dark-grey|light-grey|green|white, default `dark-grey`).
- `color` (string, optional): custom CSS color (overrides colorVariant).
- `ariaLabel` (string, optional): accessibility label (informative icons). If omitted, the icon is decorative.

## BEM
- Block: `ps-icon`
- Element: `ps-icon__icon`
- Modifiers (independent):
  - Size: `ps-icon--small|--medium|--large|--xlarge`
  - State: `ps-icon--default|--disabled|--hover|--selected`
  - Color: `ps-icon--dark-grey|--light-grey|--green|--white`

## Tokens Used
- `--ps-icon-size-16`, `--ps-icon-size-20`, `--ps-icon-size-24`, `--ps-icon-size-32`
- `--gray-600`, `--gray-400`, `--primary`, `--white`

## Usage Examples
```twig
{% include '@ps_theme/ps-icon/icon.twig' with { name: 'arrow-right', size: 24, ariaLabel: 'Go to next' } %}
{% include '@ps_theme/ps-icon/icon.twig' with { name: 'facebook', size: 20, colorVariant: 'green', state: 'hover' } %}
{% include '@ps_theme/ps-icon/icon.twig' with { name: 'fav-filled', size: 24, colorVariant: 'green', state: 'selected' } %}
```

## Real Use Cases
- Inside buttons, links, and navigation controls.
- Status indicators in alerts, badges, and cards.
- Toggles, dropdowns, and accordions (chevrons).

## Accessibility
- Provide `ariaLabel` for informative icons.
- For decorative icons, omit `ariaLabel` and the component sets `aria-hidden="true"`.
- Glyph inherits `currentColor`; ensure sufficient contrast relative to background.

## Notes
- Icon glyphs are defined globally in `source/props/icons.css` using `[data-icon]::before`.
- No hardcoded values; only tokens are used for size and color.
- `stroke|fill` modifiers from the design spec are not applicable to font glyphs; if required for SVG icons, introduce component-specific tokens and an alternative rendering pattern.
