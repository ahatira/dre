# Icon (Element/Atom)

Icon font system using the `ps-icons` font family generated from SVG sources.

## Usage

```twig
{{ include('@elements/icon/icon.twig', {
  name: 'arrow-right',
  size: 'large',
  colorVariant: 'dark-grey'
}) }}
```

## Parameters

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `name` | string | `'arrow-right'` | Icon name (required) |
| `size` | string | `'large'` | Size: `small` (16px), `medium` (20px), `large` (24px), `xlarge` (32px) |
| `colorVariant` | string | `'dark-grey'` | Color variant: `dark-grey`, `light-grey`, `green`, `white` |
| `disabled` | boolean | `false` | Disabled state (50% opacity) |
| `ariaLabel` | string | — | Accessibility label (use for informative icons) |

## Available Icons

- **Arrows**: `arrow-down`, `arrow-left`, `arrow-right`, `arrow-up`
- **Actions**: `close`, `search`, `menu`, `edit`, `delete`, `calendar`
- **Status**: `check`, `info`, `warning`
- **Math**: `plus`, `minus`
- **Test**: `test`

## BEM Structure

```
.ps-icon                     Block
.ps-icon-{name}             Icon glyph class
.ps-icon--small             Size modifier
.ps-icon--medium            Size modifier
.ps-icon--large             Size modifier (default)
.ps-icon--xlarge            Size modifier
.ps-icon--dark-grey         Color modifier (default)
.ps-icon--light-grey        Color modifier
.ps-icon--green             Color modifier
.ps-icon--white             Color modifier
.ps-icon--disabled          State modifier
```

## Tokens Used

### Sizes
- `--size-4` (16px) — small
- `--size-5` (20px) — medium
- `--size-6` (24px) — large
- `--size-8` (32px) — xlarge

### Colors
- `--gray-700` — dark-grey
- `--gray-400` — light-grey
- `--bnp-green` — green
- `--white` — white

## Accessibility

- **Decorative icons**: Use `aria-hidden="true"` (default when no `ariaLabel`)
- **Informative icons**: Provide `ariaLabel` → adds `role="img"` and `aria-label`

```twig
{# Decorative (default) #}
{{ include('@elements/icon/icon.twig', {
  name: 'arrow-right'
}) }}

{# Informative #}
{{ include('@elements/icon/icon.twig', {
  name: 'search',
  ariaLabel: 'Search'
}) }}
```

## Examples

### Sizes
```twig
{{ include('@elements/icon/icon.twig', { name: 'search', size: 'small' }) }}
{{ include('@elements/icon/icon.twig', { name: 'search', size: 'medium' }) }}
{{ include('@elements/icon/icon.twig', { name: 'search', size: 'large' }) }}
{{ include('@elements/icon/icon.twig', { name: 'search', size: 'xlarge' }) }}
```

### Colors
```twig
{{ include('@elements/icon/icon.twig', { name: 'check', colorVariant: 'green' }) }}
{{ include('@elements/icon/icon.twig', { name: 'warning', colorVariant: 'dark-grey' }) }}
{{ include('@elements/icon/icon.twig', { name: 'info', colorVariant: 'light-grey' }) }}
```

### Disabled
```twig
{{ include('@elements/icon/icon.twig', { name: 'search', disabled: true }) }}
```

## Technical Notes

- Icons are generated via `npm run icons:build` using `icon-font-generator`
- Source SVGs: `source/assets/icons/*.svg`
- Output font: `source/assets/fonts/PsIcon/ps-icons.*`
- Glyph mappings: `source/props/icons.css`
- Build process copies fonts to `dist/fonts/PsIcon/` via Vite

## Adding New Icons

1. Add SVG to `source/assets/icons/{name}.svg`
2. Run `npm run icons:build`
3. Verify in Storybook Gallery story
4. Update this README's "Available Icons" list
