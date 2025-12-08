# Icon (Element)

SVG sprite-based icon component with semantic sizes and colors.

## Props
- `name` (string, required): icon name without `icon-` prefix.
- `size` (xs|sm|md|lg|xl|xxl, default `md`): semantic sizes via tokens.
- `color` (`default|primary|secondary|success|warning|danger|info`, default `default`): semantic colors.
- `disabled` (boolean, default `false`): disabled visual state (reduced opacity + pointer lock).
- `ariaLabel` (string, optional): accessibility label for informative icons; omit for decorative.

## BEM
- Block: `ps-icon`
- Elements: `ps-icon__svg`
- Modifiers: `--xs|--sm|--md|--lg|--xl|--xxl`, `--primary|--secondary|--success|--warning|--danger|--info`, `--disabled`

## Tokens
- **Sizes**: `--ps-icon-size-xs` (10px), `--ps-icon-size-sm` (16px), `--ps-icon-size-md` (20px), `--ps-icon-size-lg` (24px), `--ps-icon-size-xl` (32px), `--ps-icon-size-xxl` (48px)
- **Colors**: `--ps-icon-color` (default: `var(--gray-600)`)
- **State**: `--ps-icon-disabled-opacity` (default: `var(--opacity-disabled, 0.5)`)
- **Base tokens**: `var(--size-205|4|5|6|8|12)`, `var(--primary)`, `var(--secondary)`, `var(--success)`, `var(--warning)`, `var(--danger)`, `var(--info)`, `var(--gray-600)`

## Usage
```twig
{# Default sprite usage #}
{% include '@elements/icon/icon.twig' with { name: 'search', size: 'md', color: 'default' } %}

{# Composition: additional class for parent context #}
{% include '@elements/icon/icon.twig' with { name: 'check', size: 'sm', color: 'success', attributes: create_attribute().addClass('ps-alert__icon') } %}
```

## Accessibility
- Decorative icons: `aria-hidden="true"` automatically.
- Informative icons: set `ariaLabel` (adds `role="img"`).
- SVG is `focusable="false"`; focus is managed by parent interactive element.

## Notes
- SVG sprite is generated from `source/icons-source/*.svg` into `source/assets/icons/icons-sprite.svg` via `npm run icons:build` (run automatically in `npm run build`).
- Only the compiled sprite is copied to `dist/` (not the 139 source SVG files), optimizing production bundle size.
