# Icon (Element)

SVG sprite-based icon component with semantic sizes and colors.

## Props
- `name` (string, required): icon name without `icon-` prefix.
- `ariaLabel` (string, optional): accessibility label for informative icons; omit for decorative.
- `attributes` (Drupal\Attribute, optional): extend markup (e.g., add classes/data-*).

## BEM
- Block: `ps-icon`
- Elements: `ps-icon__svg`
- Modifiers: `--xs|--sm|--md|--lg|--xl|--xxl`, `--primary|--secondary|--success|--warning|--danger|--info`, `--disabled` (apply manually via `attributes.addClass()` if needed)

## Tokens
- **Sizes**: `--ps-icon-size-xs` (10px), `--ps-icon-size-sm` (16px), `--ps-icon-size-md` (20px), `--ps-icon-size-lg` (24px), `--ps-icon-size-xl` (32px), `--ps-icon-size-xxl` (48px)
- **Dynamic sizing**: `--ps-icon-size` (optional override applied by parent components)
- **Colors**: `--ps-icon-color` (defaults to `currentColor`)
- **State**: `--ps-icon-disabled-opacity` (default: `var(--opacity-disabled, 0.5)`)
- **Base tokens**: `var(--size-205|4|5|6|8|12)`, semantic color tokens as needed.

## Usage
```twig
{# Default sprite usage #}
{% include '@elements/icon/icon.twig' with { name: 'search' } %}

{# Composition: additional class for parent context #}
{% include '@elements/icon/icon.twig' with {
	name: 'check',
	attributes: create_attribute()
		.addClass('ps-alert__icon')
		.setAttribute('aria-hidden', 'true')
} %}
```

## Accessibility
- Decorative icons: `aria-hidden="true"` automatically.
- Informative icons: set `ariaLabel` (adds `role="img"`).
- SVG is `focusable="false"`; focus is managed by parent interactive element.
- Disabled visuals handled by parent (use `attributes.addClass('ps-icon--disabled')` only if opacity token is required).

## Notes
- SVG sprite is generated from `source/icons-source/*.svg` into `source/assets/icons/icons-sprite.svg` via `npm run icons:build` (run automatically in `npm run build`).
- Only the compiled sprite is copied to `dist/` (not the 139 source SVG files), optimizing production bundle size.
