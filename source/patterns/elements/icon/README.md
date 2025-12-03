# Icon (Element)

Semantic icon component supporting 6 color variants and 4 sizes. Accessible for both decorative and informative use.

## Props
- `name` (string, required): icon name without "icon-" prefix.
- `size` (xs|sm|md|lg|xl|xxl, default `md`): icon size.
- `color` (default|primary|secondary|success|warning|danger|info, default `default`): semantic color.
- `disabled` (boolean, default `false`): disabled visual state.
- `ariaLabel` (string, optional): accessibility label for informative icons.
 - `baseClass` (string, optional): Override root BEM class for composition. When provided, Icon emits only this class and mapped modifiers; otherwise emits `ps-icon` classes.

## BEM
- Block: `ps-icon`
- Element: `ps-icon__icon`
- Modifiers: `--xs|--sm|--md|--lg|--xl|--xxl`, `--primary|--secondary|--success|--warning|--danger|--info`, `--disabled`

## Tokens
- **Sizes**: `--ps-icon-size-xs` (10px), `--ps-icon-size-sm` (16px), `--ps-icon-size-md` (20px), `--ps-icon-size-lg` (24px), `--ps-icon-size-xl` (32px), `--ps-icon-size-xxl` (48px)
- **Colors**: `--ps-icon-color` (default: `var(--gray-600)`)
- **Base tokens**: `var(--size-205|4|5|6|8|12)`, `var(--primary)`, `var(--secondary)`, `var(--success)`, `var(--warning)`, `var(--danger)`, `var(--info)`, `var(--gray-600)`

## CSS Variable System
Uses 3-layer architecture:
- **Layer 1**: Component tokens (`--ps-icon-*`) for internal flexibility
- **Layer 2**: Modifiers override component tokens via cascade
- **Layer 3**: Base tokens from `source/props/*.css`

## Usage
```twig
{% include '@elements/icon/icon.twig' with { name: 'search', size: 'md', color: 'default' } %}

{# Composition: override root class (baseClass) #}
{% include '@elements/icon/icon.twig' with { name: 'check', size: 'sm', color: 'success', baseClass: 'ps-alert__icon' } %}
```

## Accessibility
- Decorative icons: `aria-hidden="true"`.
- Informative icons: provide `ariaLabel` and `role="img"`.

## Notes
- Glyphs rendered via global `source/props/icons.css` `[data-icon]::before`.
- No hardcoded values; tokens only.
