# Link (Element/Atom)

Semantic text link with interactive states and optional icon support.

## Description

The Link component provides accessible hyperlinks with consistent styling across the PS Theme. It supports multiple semantic color variants, flexible sizing, underline control, icons, external link handling, and disabled states. All interactive states are handled with smooth transitions and proper ARIA attributes. The component uses Bootstrap 5-inspired component-scoped CSS variables for easy customization.

## Props

| Property | Type | Default | Required | Description |
|----------|------|---------|----------|-------------|
| `text` | string | `'Link text'` | Yes | The link text content |
| `url` | string | `'#'` | Yes | The link URL or path |
| `color` | string | `null` | No | Color variant: `null` (currentColor), `primary`, `secondary`, `gold`, `info`, `warning`, `success`, `danger`, `dark`, `light` |
| `size` | string | `null` | No | Size variant: `null` (md), `xs`, `sm`, `md`, `lg`, `xl`, `xxl` |
| `underline` | boolean | `true` | No | Show underline decoration (hover removes it) |
| `icon` | string | `''` | No | Icon name without 'icon-' prefix (from ps-icons font) |
| `iconPosition` | string | `'right'` | No | Icon position: `left`, `right` |
| `target` | string | `'_self'` | No | Link target: `_self`, `_blank` |
| `rel` | string | `''` | No | Link rel attribute (auto-set for `_blank`) |
| `disabled` | boolean | `false` | No | Disabled state |
| `attributes` | Attribute | - | No | Additional Drupal attributes |

## BEM Structure

```
ps-link                     # Base component (currentColor + underline)
├── ps-link__text          # Text content wrapper
└── ps-link__icon          # Icon element (optional)

Modifiers - Colors:
├── ps-link--primary       # Primary color (green)
├── ps-link--secondary     # Secondary color (pink)
├── ps-link--gold          # Gold color (premium)
├── ps-link--info          # Info color (blue)
├── ps-link--warning       # Warning color (yellow)
├── ps-link--success       # Success color (green light)
├── ps-link--danger        # Danger color (red)
├── ps-link--dark          # Dark color (gray-900)
└── ps-link--light         # Light color (gray-100)

Modifiers - Sizes:
├── ps-link--xs            # Extra small (12px)
├── ps-link--sm            # Small (14px)
├── ps-link--md            # Medium (16px, default)
├── ps-link--lg            # Large (18px)
├── ps-link--xl            # Extra large (22px)
└── ps-link--xxl           # Double extra large (24px)

Modifiers - Behavior:
├── ps-link--no-underline  # Remove underline decoration
├── ps-link--icon-left     # Icon positioned on left side
└── ps-link--disabled      # Disabled state
```

## Design Tokens Used

### Three-Layer CSS Variables System

**Layer 1: Root Primitives** (`source/props/*.css`)
- Semantic colors: `--primary`, `--secondary`, `--gold`, `--info`, `--warning`, `--success`, `--danger`
- Semantic states: `--{color}-hover`, `--{color}-active`, `--{color}-text`, etc.
- Grays: `--gray-100` to `--gray-900`
- Typography: `--font-sans`, `--font-size-0` to `--font-size-5`, `--font-weight-400`, `--leading-normal`
- Spacing: `--size-2`
- Borders: `--border-size-1`, `--border-size-2`, `--radius-1`
- Animations: `--duration-fast`, `--ease-4`

**Layer 2: Component-Scoped Variables** (Override for customization)
```css
.ps-link {
  --ps-link-color: currentColor;
  --ps-link-hover-color: currentColor;
  --ps-link-active-color: currentColor;
  --ps-link-visited-color: currentColor;
  --ps-link-disabled-color: var(--gray-500);
  --ps-link-font-family: var(--font-sans);
  --ps-link-font-size: var(--font-size-2);
  --ps-link-font-weight: var(--font-weight-400);
  --ps-link-line-height: var(--leading-normal);
  --ps-link-gap: var(--size-2);
  --ps-link-text-decoration: underline;
  --ps-link-text-underline-offset: var(--border-size-1);
  --ps-link-hover-text-decoration: none;
  --ps-link-focus-outline-width: var(--border-size-2);
  --ps-link-focus-outline-color: var(--secondary);
  --ps-link-focus-outline-offset: var(--border-size-1);
  --ps-link-focus-border-radius: var(--radius-1);
  --ps-link-transition-duration: var(--duration-fast);
  --ps-link-transition-timing: var(--ease-4);
}
```

**Layer 3: Context Overrides** (Example)
```css
.sidebar .ps-link {
  --ps-link-font-size: var(--font-size-1); /* Smaller in sidebar */
}
```

## Usage Examples

### Basic Link (Default)
```twig
{% include '@elements/link/link.twig' with {
  text: 'View property details',
  url: '/property/modern-office-building',
} %}
```

### Primary Link
```twig
{% include '@elements/link/link.twig' with {
  text: 'Schedule property tour',
  url: '/contact',
  color: 'primary',
} %}
```

### Link with Icon (No Underline)
```twig
{% include '@elements/link/link.twig' with {
  text: 'Next property listing',
  url: '/properties?page=2',
  icon: 'arrow-right',
  iconPosition: 'right',
  underline: false,
  color: 'primary',
} %}
```

### Link with Icon on Left
```twig
{% include '@elements/link/link.twig' with {
  text: 'Previous property listing',
  url: '/properties?page=1',
  icon: 'arrow-left',
  iconPosition: 'left',
  underline: false,
  color: 'primary',
} %}
```

### External Link
```twig
{% include '@elements/link/link.twig' with {
  text: 'View on external portal',
  url: 'https://example.com',
  target: '_blank',
  color: 'primary',
} %}
```

### Secondary Link
```twig
{% include '@elements/link/link.twig' with {
  text: 'Contact real estate agent',
  url: '/contact',
  color: 'secondary',
} %}
```

### Link on Dark Background
```twig
{% include '@elements/link/link.twig' with {
  text: 'Privacy policy',
  url: '/privacy',
  color: 'light',
} %}
```

### Disabled Link
```twig
{% include '@elements/link/link.twig' with {
  text: 'Property unavailable',
  url: '#',
  disabled: true,
} %}
```

### Large Link
```twig
{% include '@elements/link/link.twig' with {
  text: 'Featured property',
  url: '/featured',
  size: 'lg',
  color: 'primary',
} %}
```

## Real-World Use Cases

### 1. Inline Text Link
Use in paragraphs or text content to provide additional information:
```twig
<p>
  Discover our portfolio of modern office buildings across Paris. 
  {% include '@elements/link/link.twig' with {
    text: 'Learn more about commercial properties',
    url: '/properties/commercial',
    color: 'primary',
  } %}
  and find the perfect space for your business needs.
</p>
```

### 2. Navigation Link
Use in navigation menus or lists without underline:
```twig
{% include '@elements/link/link.twig' with {
  text: 'Properties',
  url: '/properties',
  underline: false,
} %}
```

### 3. Pagination Links
Use with icons on left or right for pagination:
```twig
<nav aria-label="Pagination">
  <div style="display: flex; gap: var(--size-6); justify-content: space-between;">
    {% include '@elements/link/link.twig' with {
      text: 'Previous',
      url: '/properties?page=1',
      icon: 'arrow-left',
      iconPosition: 'left',
      underline: false,
      color: 'primary',
    } %}
    {% include '@elements/link/link.twig' with {
      text: 'Next',
      url: '/properties?page=3',
      icon: 'arrow-right',
      iconPosition: 'right',
      underline: false,
      color: 'primary',
    } %}
  </div>
</nav>
```

### 4. Call-to-Action Link
Use with icon for prominent CTAs:
```twig
{% include '@elements/link/link.twig' with {
  text: 'View all properties',
  url: '/properties',
  icon: 'arrow-right',
  iconPosition: 'right',
  underline: false,
  color: 'primary',
  size: 'lg',
} %}
```

### 5. Footer Link
Use light variant in dark footer:
```twig
{% include '@elements/link/link.twig' with {
  text: 'Terms & Conditions',
  url: '/terms',
  color: 'light',
  underline: false,
} %}
```

### 6. External Resource
Use for links to external websites:
```twig
{% include '@elements/link/link.twig' with {
  text: 'Visit BNP Paribas Real Estate',
  url: 'https://www.realestate.bnpparibas.com',
  target: '_blank',
  color: 'primary',
} %}
```

## Accessibility

### ARIA Attributes
- `aria-disabled="true"` - Applied to disabled links (rendered as `<span>`)
- `aria-hidden="true"` - Applied to decorative icons

### Focus States
- Visible focus outline with `--secondary` color
- 2px outline with 1px offset for clear visibility
- Border radius for smooth outline appearance
- Meets WCAG 2.1 AA requirements for focus indicators

### External Links
- Automatically adds `rel="noopener noreferrer"` for security when `target="_blank"`
- Consider adding visually hidden text: "(opens in new tab)" for screen readers

### Disabled State
- Rendered as `<span>` instead of `<a>` to prevent keyboard navigation
- `pointer-events: none` prevents interaction
- `aria-disabled="true"` announces disabled state to assistive technologies
- Appropriate disabled color (`--gray-500`) for low contrast indication

### Color Contrast
- All link colors meet WCAG AA contrast requirements (≥ 4.5:1)
- Hover/active states maintain sufficient contrast
- Visited state uses distinct color for user orientation
- Default (currentColor) inherits parent text color for flexibility

### Keyboard Navigation
- Fully keyboard accessible via Tab key
- Focus visible indicator for keyboard users (`outline` + `outline-offset`)
- Disabled links are not focusable (semantic `<span>`)
- Enter key activates link (native browser behavior)

## Browser Support

- Chrome/Edge 90+
- Firefox 88+
- Safari 14+
- All modern browsers with CSS nesting support (postcss-nested for legacy)

## Related Components

- **Button** - For actions instead of navigation
- **Icon** - For standalone icon usage
- **Breadcrumb** - For hierarchical navigation links
- **Menu Item** - For structured navigation menus

## Notes

- Links use semantic `<a>` elements by default for navigation
- Disabled links render as `<span>` to prevent navigation and keyboard focus
- External links (`target="_blank"`) automatically get security attributes (`rel="noopener noreferrer"`)
- **Icons are rendered via CSS pseudo-elements** using `data-icon` attribute and `icons.css` mappings
- Icon name is passed without `icon-` prefix (e.g., `arrow-right`, not `icon-arrow-right`)
- All color variants support full state changes (default, hover, active, visited, disabled)
- **Underline is shown by default** (base style), use `underline: false` to remove it
- **Minimal HTML output:** Base class alone for defaults (currentColor + underline), modifiers only when needed
- **Bootstrap 5-inspired CSS variables:** Override component-scoped variables for easy customization
- Supports dark mode via context overrides (e.g., `[data-theme="dark"] .ps-link { --ps-link-color: ... }`)
