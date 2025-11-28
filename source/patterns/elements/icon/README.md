# Icon (Element/Atom)

Icon font system using **bnpre-icons** (75 icons) and **bnpre-icons-poi** (14 POI icons) fonts from `source/props/icons.css`.

## Usage

```twig
{{ include('@elements/icon/icon.twig', {
  name: 'icon-search',
  size: 'medium'
}) }}
```

Or directly in HTML:
```html
<i class="icon-search ps-icon--medium"></i>
<i class="icon-poi-hotel ps-icon--large"></i>
```

## Parameters

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `name` | string | `'icon-search'` | Full icon class name (e.g., `icon-account`, `icon-poi-hotel`) |
| `size` | string | `'medium'` | Size: `small` (16px), `medium` (20px), `large` (24px), `xlarge` (32px) |
| `color` | string | — | Custom CSS color (inherits by default) |
| `disabled` | boolean | `false` | Disabled state (50% opacity) |
| `ariaLabel` | string | — | Accessibility label (use for informative icons) |

## Available Icons

**Total: 89 icons** (75 regular + 14 POI)

### Regular Icons (75) - `icon-*`
Accessibility, account, arrows (8 directions), bike, bin, bus, business-premises, calendar, car, check, checkbox, close, comparateur, coworking, create-alert, district, edit, energy-cons, favorites, gas-emission, grue, help, hospitality, hotel, infos, last-articles, linkedin, logistic-warehouses, mail, map, menu, metro, moins/plus (big/small), next/previous, offices, parking, partitioned-offices, phone, picture, pin-map, play, pwd (hide/show), quote, rer, residentiel, restaurant, search, send, share, shops, surface, tram, transport, video, virtual-tour, walking, x-twitter, youtube

### POI Icons (14) - `icon-poi-*`
sport, transport, autre, commerce, education, hotel, loisir, parc, sante, service, bus-clear, metro-clear, rer-clear, tram-clear

**See Storybook Gallery stories for visual reference of all icons.**

## BEM Structure

```
.icon-{name}                 Base icon class (from source/props/icons.css) - DO NOT MODIFY
.icon-poi-{name}            POI icon class (from source/props/icons.css) - DO NOT MODIFY
.ps-icon--small             Size modifier
.ps-icon--medium            Size modifier (default)
.ps-icon--large             Size modifier
.ps-icon--xlarge            Size modifier
.ps-icon--disabled          State modifier
```

## Tokens Used

### Sizes
- `--size-4` (16px) — small
- `--size-5` (20px) — medium
- `--size-6` (24px) — large
- `--size-8` (32px) — xlarge

## Accessibility

- **Decorative icons**: Use `aria-hidden="true"` (default when no `ariaLabel`)
- **Informative icons**: Provide `ariaLabel` → adds `role="img"` and `aria-label`

```twig
{# Decorative (default) #}
{{ include('@elements/icon/icon.twig', {
  name: 'icon-arrow-right'
}) }}

{# Informative #}
{{ include('@elements/icon/icon.twig', {
  name: 'icon-search',
  ariaLabel: 'Search'
}) }}
```

## Examples

### Sizes
```twig
{{ include('@elements/icon/icon.twig', { name: 'icon-search', size: 'small' }) }}
{{ include('@elements/icon/icon.twig', { name: 'icon-search', size: 'medium' }) }}
{{ include('@elements/icon/icon.twig', { name: 'icon-search', size: 'large' }) }}
{{ include('@elements/icon/icon.twig', { name: 'icon-search', size: 'xlarge' }) }}
```

### Custom Colors
```twig
{{ include('@elements/icon/icon.twig', { name: 'icon-check', size: 'large', color: 'var(--bnp-green)' }) }}
{{ include('@elements/icon/icon.twig', { name: 'icon-close', size: 'large', color: 'var(--red-600)' }) }}
```

### POI Icons
```twig
{{ include('@elements/icon/icon.twig', { name: 'icon-poi-hotel', size: 'xlarge' }) }}
{{ include('@elements/icon/icon.twig', { name: 'icon-poi-transport', size: 'large' }) }}
```

### Disabled
```twig
{{ include('@elements/icon/icon.twig', { name: 'icon-search', disabled: true }) }}
```

## Technical Notes

- **Fonts**: Located in `source/assets/fonts/icons/` and `source/assets/fonts/icons-poi/`
- **Font Faces**: Defined in `source/props/icons.css`
- **Icon Classes**: `.icon-*` and `.icon-poi-*` defined in `source/props/icons.css` - **DO NOT MODIFY**
- **Size Modifiers**: `.ps-icon--*` defined in `source/patterns/elements/icon/icon.css`
- **Build**: Fonts are copied to `dist/` via Vite
- **List**: All icon names extracted in `source/patterns/documentation/icons-list.json`

## IMPORTANT

⚠️ **DO NOT MODIFY** the icon classes in `source/props/icons.css`. They are managed externally and should remain unchanged. Only use the size and state modifiers defined in this component's CSS.
