# Link (Element/Atom)

Text link component with interactive states (hover, active, visited) and optional icon support.

## Description

The Link component provides a semantic and accessible way to create hyperlinks with consistent styling across the PS Theme. It supports multiple color variants, underline options, icons, external link handling, and disabled states. All interactive states are handled with smooth transitions and proper ARIA attributes.

## Props

| Property | Type | Default | Required | Description |
|----------|------|---------|----------|-------------|
| `text` | string | `'Link text'` | Yes | The link text content |
| `url` | string | `'#'` | Yes | The link URL or path |
| `color` | string | `'primary'` | No | Color variant: `default`, `primary`, `secondary`, `info`, `warning`, `success`, `danger`, `dark`, `light`. |
| `underline` | boolean | `true` | No | Show underline decoration |
| `icon` | string | `''` | No | Icon name to display (from ps-icons font) |
| `iconPosition` | string | `'right'` | No | Icon position: `left`, `right` |
| `size` | string | `'md'` | No | Size variant: `xs`, `sm`, `md`, `lg`, `xl`, `xxl` |
| `target` | string | `'_self'` | No | Link target: `_self`, `_blank` |
| `rel` | string | `''` | No | Link rel attribute (auto-set for `_blank`) |
| `disabled` | boolean | `false` | No | Disabled state |
| `attributes` | Attribute | - | No | Additional Drupal attributes |

## BEM Structure

```
ps-link                    # Base component (underline by default)
├── ps-link__text         # Text content wrapper
└── ps-link__icon         # Icon element (optional, icon via CSS ::before)

Modifiers:
├── ps-link--primary      # Primary color
├── ps-link--secondary    # Secondary color
├── ps-link--info         # Info color
├── ps-link--warning      # Warning color
├── ps-link--success      # Success color
├── ps-link--danger       # Danger color
├── ps-link--dark         # Dark color
├── ps-link--light        # Light color
├── ps-link--default      # Default text color
├── ps-link--no-underline # Remove underline decoration
├── ps-link--with-icon    # Contains icon
├── ps-link--icon-left    # Icon positioned on left side
├── ps-link--external     # External link (_blank)
├── ps-link--disabled     # Disabled state
├── ps-link--xs           # Extra small size
├── ps-link--sm           # Small size
├── ps-link--md           # Medium size
├── ps-link--lg           # Large size
├── ps-link--xl           # Extra large size
└── ps-link--xxl          # Double extra large size
```

## Design Tokens Used
- Système à 3 couches :
  - Palette de base : `colors.css` (ex : `--green-600`, `--blue-600`, ...)
  - Sémantique : `semantic.css` (ex : `--color-link-primary`, ...)
  - Composant : `link.css` (ex : `--ps-link-primary`, ...)

### Colors
- `--ps-link-primary`, `--ps-link-secondary`, `--ps-link-info`, `--ps-link-warning`, `--ps-link-success`, `--ps-link-danger`, `--ps-link-dark`, `--ps-link-light`, `--ps-link-default`
- Chaque couleur a ses états : hover, active, visited, disabled

### Typography & Sizes
- `--ps-link-size-xs`, `--ps-link-size-sm`, `--ps-link-size-md`, `--ps-link-size-lg`, `--ps-link-size-xl`, `--ps-link-size-xxl`
- `--font-sans`, `--font-weight-400`, `--leading-normal`

### Spacing
- `--size-2` (gap icône/texte)
- `--size-5` (icône)

### Borders & Radii
- `--border-size-1`, `--border-size-2`, `--radius-1`

## Usage Examples

### Basic Link
```twig
{% include '@ps_theme/link/link.twig' with {
  text: 'Learn more',
  url: '/about',
} %}
```

### Link with Icon (no underline)
```twig
{% include '@ps_theme/link/link.twig' with {
  text: 'Next page',
  url: '/properties',
  icon: 'arrow-right',
    iconPosition: 'right',
    underline: false,
  } %}
  ```

  ### Link with Icon on Left
  ```twig
  {% include '@ps_theme/link/link.twig' with {
    text: 'Previous page',
    url: '/previous',
    icon: 'arrow-left',
    iconPosition: 'left',
  underline: false,
} %}
```

**Note:** Icons are rendered via CSS pseudo-elements using the `bnpre-icons` font. Icon position can be controlled with `iconPosition` prop (`left` or `right`, default is `right`). Supported icons include: `arrow-right`, `arrow-left`, `arrow-up`, `arrow-down`, `external-link`, `download`.

### External Link
```twig
{% include '@ps_theme/link/link.twig' with {
  text: 'Documentation',
  url: 'https://example.com',
  target: '_blank',
} %}
```

### Secondary Link
```twig
{% include '@ps_theme/link/link.twig' with {
  text: 'Contact us',
  url: '/contact',
  color: 'secondary',
} %}
```

### Inverse Link (on dark background)
```twig
{% include '@ps_theme/link/link.twig' with {
  text: 'Privacy policy',
  url: '/privacy',
  color: 'inverse',
} %}
```

### Disabled Link
```twig
{% include '@ps_theme/link/link.twig' with {
  text: 'Unavailable',
  url: '#',
  disabled: true,
} %}
```

## Real-World Use Cases

### 1. Inline Text Link
Use in paragraphs or text content to provide additional information:
```twig
<p>
  Learn more about our services. 
  {% include '@ps_theme/link/link.twig' with {
    text: 'View our offerings',
    url: '/services',
    color: 'primary',
  } %}
  and discover how we can help.
</p>
```

### 2. Navigation Link
Use in navigation menus or lists without underline:
```twig
{% include '@ps_theme/link/link.twig' with {
  text: 'Properties',
  url: '/properties',
  underline: false,
} %}
```

### 3. Pagination Links
Use with icons on left or right for pagination:
```twig
<div style="display: flex; gap: var(--size-6); justify-content: space-between;">
  {% include '@ps_theme/link/link.twig' with {
    text: 'Previous',
    url: '/page/1',
    icon: 'arrow-left',
    iconPosition: 'left',
    underline: false,
  } %}
  {% include '@ps_theme/link/link.twig' with {
    text: 'Next',
    url: '/page/3',
    icon: 'arrow-right',
    iconPosition: 'right',
    underline: false,
  } %}
</div>
```

### 4. Call-to-Action Link
Use with icon for prominent CTAs:
```twig
{% include '@ps_theme/link/link.twig' with {
  text: 'View all properties',
  url: '/properties',
  icon: 'arrow-right',
  iconPosition: 'right',
  underline: false,
} %}
```
  text: 'Discover our projects',
  url: '/projects',
  icon: 'arrow-right',
  underline: false,
  color: 'green',
} %}
```

### 4. Footer Link
Use white variant in dark footer:
```twig
{% include '@ps_theme/link/link.twig' with {
  text: 'Terms & Conditions',
  url: '/terms',
  color: 'white',
  underline: false,
} %}
```

### 5. External Resource
Use for links to external websites:
```twig
{% include '@ps_theme/link/link.twig' with {
  text: 'Visit BNP Paribas',
  url: 'https://www.bnpparibas.com',
  target: '_blank',
} %}
```

## Accessibility

### ARIA Attributes
- `aria-disabled="true"` - Applied to disabled links (rendered as `<span>`)
- `aria-hidden="true"` - Applied to decorative icons

### Focus States
- Visible focus outline with `--blue-500` color
- 2px outline with 1px offset for clear visibility
- Border radius for smooth outline appearance

### External Links
- Automatically adds `rel="noopener noreferrer"` for security
- Consider adding visually hidden text: "(opens in new tab)"

### Disabled State
- Rendered as `<span>` instead of `<a>` to prevent keyboard navigation
- `pointer-events: none` prevents interaction
- Appropriate disabled color for low contrast indication

### Color Contrast
- All link colors meet WCAG AA contrast requirements (≥ 4.5:1)
- Hover/active states maintain sufficient contrast
- Visited state uses distinct color for user orientation

### Keyboard Navigation
- Fully keyboard accessible via Tab key
- Focus visible indicator for keyboard users
- Disabled links are not focusable

## Browser Support

- Chrome/Edge 90+
- Firefox 88+
- Safari 14+
- All modern browsers with CSS nesting support

## Related Components

- **Button** - For actions instead of navigation
- **Icon** - For standalone icon usage
## Notes

- Links use semantic `<a>` elements by default
- Disabled links render as `<span>` to prevent navigation
- External links automatically get security attributes
- **Icons are rendered via CSS pseudo-elements** using `bnpre-icons` font family
- Icon name is passed via `data-icon` attribute and mapped in CSS
- All color variants support full state changes (hover, active, visited)
- **Underline is shown by default** (base style), use `underline: false` to remove it
- **Minimal HTML output:** Base class alone for defaults (green + underline)nts
- All color variants support full state changes (hover, active, visited)
- Underline is shown by default but can be toggled off
