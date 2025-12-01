
# Icon (Element/Atom)

Semantic icon component supporting 6 color variants and 4 sizes. Accessible for both decorative and informative use.

## Props

| Prop      | Type    | Default   | Description |
|-----------|---------|-----------|-------------|
| name      | string  | 'search'  | Icon name without "icon-" prefix |
| size      | string  | 'medium'  | Size: small (16px), medium (20px), large (24px), xlarge (32px) |
| color     | string  | 'primary' | Semantic color: primary, secondary, success, warning, danger, info |
| disabled  | boolean | false     | Disabled state (50% opacity) |
| ariaLabel | string  | —         | Accessibility label (informative icons only) |

## BEM Structure

- `.icon-{name}` - Base icon class (from icons.css, do not modify)
- `.ps-icon--small` - Size modifier
- `.ps-icon--medium` - Size modifier (default)
- `.ps-icon--large` - Size modifier
- `.ps-icon--xlarge` - Size modifier
- `.ps-icon--primary` - Color modifier (default)
- `.ps-icon--secondary` - Color modifier
- `.ps-icon--success` - Color modifier
- `.ps-icon--warning` - Color modifier
- `.ps-icon--danger` - Color modifier
- `.ps-icon--info` - Color modifier
- `.ps-icon--disabled` - State modifier

## Design Tokens

- `--ps-icon-size-small` (16px)
- `--ps-icon-size-medium` (20px)
- `--ps-icon-size-large` (24px)
- `--ps-icon-size-xlarge` (32px)
- `--ps-icon-color` (semantic, default: var(--text-primary))
- Semantic colors: `--brand-primary`, `--brand-secondary`, `--success`, `--warning`, `--danger`, `--info`

## Usage

```twig
{{ include('@elements/icon/icon.twig', {
  name: 'search',
  size: 'medium',
  color: 'primary'
}) }}
```

## Real-World Use Cases

- Status badge icons (success, warning, danger)
- Navigation icons (arrow-left, arrow-right)
- Action icons (edit, bin, share)
- Form controls (checkbox, radio)

## Accessibility

- Decorative icons: `aria-hidden="true"` (default)
- Informative icons: `aria-label` and `role="img"`
- Contrast meets WCAG AA for all semantic colors

## Variants

- **Colors**: primary, secondary, success, warning, danger, info
- **Sizes**: small, medium, large, xlarge
