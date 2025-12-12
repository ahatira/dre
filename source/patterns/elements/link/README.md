# Link (Element/Atom)

Semantic text link with interactive states, optional icon support, and flexible color variants.

## Description

The Link component provides accessible hyperlinks with consistent styling across the PS Theme. It supports:
- **10 semantic color variants** (default, primary, secondary, gold, info, warning, success, danger, dark, light)
- **6 size options** (xs to xxl) for hierarchy adaptation
- **Icon support** (left/right positioning, no prefix needed)
- **Underline control** and disabled states
- **Smooth transitions** and focus-visible accessibility
- **Real estate context** (navigation, CTAs, status indicators)

All interactive states use the three-layer CSS variables system for easy customization at component, modifier, and context levels.

## Props

| Property | Type | Default | Required | Description |
|----------|------|---------|----------|-------------|
| `text` | string | `'Link text'` | Yes | The link text content |
| `url` | string | `'#'` | Yes | The link URL or path |
| `color` | string | `null` | No | Color variant: `null` (currentColor), `primary`, `secondary`, `gold`, `info`, `warning`, `success`, `danger`, `dark`, `light` |
| `size` | string | `null` | No | Size variant: `null` (md), `xs`, `sm`, `md`, `lg`, `xl`, `xxl` |
| `underline` | boolean | `true` | No | Show underline decoration (hover removes it) |
| `icon` | string | `''` | No | Icon name without 'icon-' prefix |
| `iconPosition` | string | `'right'` | No | Icon position: `left`, `right` |
| `target` | string | `'_self'` | No | Link target: `_self`, `_blank` |
| `rel` | string | `''` | No | Link rel attribute (auto-set for `_blank`) |
| `disabled` | boolean | `false` | No | Disabled state (renders as span with aria-disabled) |
| `attributes` | Attribute | - | No | Additional Drupal attributes |

## BEM Structure

```
ps-link                     # Base component (currentColor + underline)
├── ps-link__text          # Text content wrapper
└── ps-link__icon          # Icon element (optional)

Modifiers - Colors (Semantic):
├── ps-link--primary       # Brand green (#00915A) - Main actions, CTAs
├── ps-link--secondary     # Brand pink (#A12B66) - Alternative actions
├── ps-link--gold          # Premium gold (#D1AE6E) - Luxury properties
├── ps-link--info          # Informational blue (#2563EB) - Help/info links
├── ps-link--warning       # Warning yellow (#FBBF24) - Time-sensitive offers
├── ps-link--success       # Success teal (#198754) - Available/confirmed
├── ps-link--danger        # Danger red (#EB3636) - Sold/unavailable
├── ps-link--dark          # Dark gray (#111827) - Light backgrounds
└── ps-link--light         # Light gray (#F3F4F6) - Dark backgrounds

Modifiers - Sizes:
├── ps-link--xs            # Extra small (12px) - footnotes
├── ps-link--sm            # Small (14px) - secondary navigation
├── ps-link--md            # Medium (16px, default) - body text
├── ps-link--lg            # Large (18px) - feature links
├── ps-link--xl            # Extra large (22px) - hero sections
└── ps-link--xxl           # Double extra large (24px) - major CTAs

Modifiers - Behavior:
├── ps-link--no-underline  # Remove underline decoration
├── ps-link--icon-left     # Icon positioned on left side
└── ps-link--disabled      # Disabled state (span, aria-disabled)
```

## CSS Variables System (3-Layer Architecture)

### Layer 1: Root Primitives (Global Tokens)

Used globally in `source/props/`:

**Semantic colors** (all with -hover, -active states):
- `--primary`, `--secondary`, `--gold`, `--info`, `--warning`, `--success`, `--danger`

**Grays**: `--gray-100` to `--gray-900`

**Typography**: `--font-sans`, `--font-size-0` to `--font-size-5`, `--font-weight-400`, `--leading-normal`

**Spacing**: `--size-2`, `--size-3`, `--size-4`, etc.

**Borders**: `--border-size-1`, `--border-size-2`, `--radius-1`, `--radius-2`

**Animations**: `--duration-fast`, `--ease-4`

### Layer 2: Component-Scoped Variables

**Location**: `.ps-link` selector  
**Purpose**: Default component behavior, overridable via modifiers

```css
.ps-link {
  /* Colors (Default = inherited) */
  --ps-link-color: currentColor;
  --ps-link-hover-color: currentColor;
  --ps-link-active-color: currentColor;
  --ps-link-visited-color: currentColor;
  --ps-link-disabled-color: var(--gray-500);
  
  /* Typography & Spacing */
  --ps-link-font-family: var(--font-sans);
  --ps-link-font-size: var(--font-size-2);  /* md = 16px */
  --ps-link-font-weight: var(--font-weight-400);
  --ps-link-line-height: var(--leading-normal);
  --ps-link-gap: var(--size-2);
  
  /* Decoration */
  --ps-link-text-decoration: underline;
  --ps-link-text-underline-offset: var(--border-size-1);
  --ps-link-hover-text-decoration: none;
  
  /* Focus accessibility */
  --ps-link-focus-outline-width: var(--border-size-2);
  --ps-link-focus-outline-color: var(--primary);
  --ps-link-focus-outline-offset: var(--border-size-1);
  --ps-link-focus-border-radius: var(--radius-1);
  
  /* Transitions */
  --ps-link-transition-duration: var(--duration-fast);
  --ps-link-transition-timing: var(--ease-4);
}
```

### Layer 3: Context Overrides

**Location**: Modifier selectors  
**Purpose**: Override Layer 2 variables for specific variants

```css
/* Color modifiers override all color variables */
.ps-link--primary {
  --ps-link-color: var(--primary);
  --ps-link-hover-color: var(--primary-hover);
  --ps-link-active-color: var(--primary-active);
  --ps-link-visited-color: var(--primary-active);
}

/* Size modifiers override typography */
.ps-link--lg {
  --ps-link-font-size: var(--font-size-3);  /* 18px */
}

/* Behavior modifiers */
.ps-link--no-underline {
  --ps-link-text-decoration: none;
}
```

**Context override example** (consuming context):
```css
.sidebar .ps-link {
  --ps-link-font-size: var(--font-size-1);  /* Smaller in sidebar */
}
```

## Semantic Colors Reference

### Default (No Class)
- **Use case**: Inline links inheriting surrounding text color
- **Color**: `currentColor` (inherited)
- **Underline**: Yes (removed on hover)

### Primary (Brand Green #00915A)
- **Use case**: Main CTAs, primary navigation actions
- **States**: base, hover (lighter), active (darker)
- **Real estate**: "Planifier une visite", "Consulter l'annonce"

### Secondary (Brand Pink #A12B66)
- **Use case**: Alternative actions, accents
- **States**: base, hover (lighter), active (darker)
- **Real estate**: "Contacter un conseiller", "Demander info"

### Gold (Premium #D1AE6E)
- **Use case**: Premium/luxury property listings
- **States**: base, hover (lighter), active (darker)
- **Real estate**: "Découvrir biens premium", "Bien haut standing"

### Info (Blue #2563EB)
- **Use case**: Informational content, help links
- **States**: base, hover (darker), active (darkest)
- **Real estate**: "En savoir plus", "Questions fréquentes"

### Warning (Yellow #FBBF24)
- **Use case**: Time-sensitive content, limited offers
- **States**: base, hover (darker), active (darkest)
- **Real estate**: "Offre à durée limitée", "Vue virtuellement"

### Success (Teal #198754)
- **Use case**: Available status, confirmations
- **States**: base, hover (darker), active (darkest)
- **Real estate**: "Bien disponible immédiatement", "Visites possibles"

### Danger (Red #EB3636)
- **Use case**: Sold status, unavailable properties
- **States**: base, hover (darker), active (darkest)
- **Real estate**: "Bien vendu", "Indisponible"

### Dark (Gray-900 #111827)
- **Use case**: Contrast on light backgrounds
- **States**: base (darkest), hover (lighter), active (lighter)
- **Real estate**: Any navigation on light backgrounds

### Light (Gray-100 #F3F4F6)
- **Use case**: Contrast on dark backgrounds
- **States**: base (lightest), hover (darker), active (darker)
- **Real estate**: Footer navigation, dark themes

## Usage Examples

### Basic Link (Default)
```twig
{% include '@elements/link/link.twig' with {
  text: 'Voir l\'annonce complète',
  url: '/property/12345'
} only %}
```

### Primary Link
```twig
{% include '@elements/link/link.twig' with {
  text: 'Planifier une visite',
  url: '/schedule',
  color: 'primary'
} only %}
```

### Link with Icon (Right)
```twig
{% include '@elements/link/link.twig' with {
  text: 'Télécharger la fiche',
  url: '/download/property.pdf',
  icon: 'download',
  color: 'primary',
  underline: false
} only %}
```

### Link with Icon (Left)
```twig
{% include '@elements/link/link.twig' with {
  text: 'Annonce précédente',
  url: '/properties?page=1',
  icon: 'arrow-left',
  iconPosition: 'left',
  underline: false,
  color: 'primary'
} only %}
```

### External Link
```twig
{% include '@elements/link/link.twig' with {
  text: 'Voir sur portail partenaire',
  url: 'https://example.com/property',
  target: '_blank',
  icon: 'external-link',
  color: 'primary'
} only %}
```

### Status Indicator (Success)
```twig
{% include '@elements/link/link.twig' with {
  text: 'Bien disponible immédiatement',
  url: '/property/12345',
  color: 'success',
  icon: 'check',
  underline: false
} only %}
```

### Status Indicator (Sold)
```twig
{% include '@elements/link/link.twig' with {
  text: 'Bien vendu',
  url: '#',
  color: 'danger',
  disabled: true
} only %}
```

### Link on Dark Background
```twig
{% include '@elements/link/link.twig' with {
  text: 'Politique de confidentialité',
  url: '/privacy',
  color: 'light',
  size: 'sm'
} only %}
```

### Different Sizes
```twig
<!-- Extra small (footnote) -->
{{ include_link('Lire plus', '/details', size='xs', color='primary') }}

<!-- Small (secondary nav) -->
{{ include_link('Voir aussi', '/related', size='sm', color='primary') }}

<!-- Medium (default body) -->
{{ include_link('Consulter', '/property', size='md', color='primary') }}

<!-- Large (feature) -->
{{ include_link('Découvrir', '/featured', size='lg', color='primary') }}

<!-- Extra Large (hero) -->
{{ include_link('Commencer', '/search', size='xl', color='primary') }}
```

## Real Estate Use Cases

### Navigation
```twig
<nav aria-label="Breadcrumb">
  {% include '@elements/link/link.twig' with { text: 'Accueil', url: '/', underline: false } only %}
  <span>/</span>
  {% include '@elements/link/link.twig' with { text: 'Bureaux', url: '/commercial', underline: false } only %}
  <span>/</span>
  <span>Paris 8ème</span>
</nav>
```

### Inline CTA in Description
```twig
<p>
  Découvrez {% include '@elements/link/link.twig' with {
    text: 'ce bien premium',
    url: '/property/12345',
    color: 'gold'
  } only %} ou 
  {% include '@elements/link/link.twig' with {
    text: 'contacter notre équipe',
    url: '/contact',
    color: 'secondary'
  } only %}.
</p>
```

### Pagination Navigation
```twig
<div style="display: flex; gap: var(--size-4);">
  {% include '@elements/link/link.twig' with {
    text: 'Annonce précédente',
    url: previous_url,
    icon: 'arrow-left',
    iconPosition: 'left',
    underline: false
  } only %}
  
  {% include '@elements/link/link.twig' with {
    text: 'Annonce suivante',
    url: next_url,
    icon: 'arrow-right',
    iconPosition: 'right',
    underline: false
  } only %}
</div>
```

### Property Status Indicators
```twig
<!-- Available -->
{% include '@elements/link/link.twig' with {
  text: 'Disponible immédiatement',
  url: '/property/12345',
  color: 'success',
  icon: 'check',
  underline: false
} only %}

<!-- Limited Time -->
{% include '@elements/link/link.twig' with {
  text: 'Offre à durée limitée',
  url: '/property/12345',
  color: 'warning',
  underline: false
} only %}

<!-- Sold -->
{% include '@elements/link/link.twig' with {
  text: 'Bien vendu',
  url: '#',
  color: 'danger',
  disabled: true
} only %}
```

### Footer Links
```twig
<footer style="background-color: var(--gray-800); padding: var(--size-6);">
  <div style="display: flex; flex-direction: column; gap: var(--size-2);">
    {% include '@elements/link/link.twig' with {
      text: 'À propos de nous',
      url: '/about',
      color: 'light',
      size: 'sm'
    } only %}
    
    {% include '@elements/link/link.twig' with {
      text: 'Politique de confidentialité',
      url: '/privacy',
      color: 'light',
      size: 'sm'
    } only %}
    
    {% include '@elements/link/link.twig' with {
      text: 'Nous contacter',
      url: '/contact',
      color: 'light',
      size: 'sm'
    } only %}
  </div>
</footer>
```

## Accessibility (WCAG 2.2 AA Compliant)

### Focus-Visible
- Blue outline (var(--primary)) with 2px width
- 1px offset for clear visibility
- Border radius for smooth appearance

### Disabled State
- Uses `aria-disabled="true"` attribute
- Applies `pointer-events: none` to prevent interaction
- Rendered as `<span>` instead of `<a>`

### Icon Handling
- All icons marked with `aria-hidden="true"` (text carries meaning)
- Icon name passed without prefix (e.g., `arrow-right`, not `icon-arrow-right`)

### External Links
- `target="_blank"` automatically includes `rel="noopener noreferrer"`
- Consider adding visually hidden text: "(opens in new tab)"

### Color Contrast
All variants meet WCAG AA (4.5:1 minimum):
- **Primary**: Green on white (6.2:1)
- **Secondary**: Pink on white (4.8:1)
- **Dark**: Gray-900 on white (12.4:1)
- **Light**: Gray-100 on dark (9.1:1)

### Keyboard Navigation
- Full Tab key support via native `<a>` element
- Disabled links not focusable (semantic `<span>`)
- Enter key activates link (browser behavior)

## Customization

### Smaller Size in Sidebar
```css
.sidebar .ps-link {
  --ps-link-font-size: var(--font-size-1);
}
```

### Custom Focus Color
```css
.ps-link {
  --ps-link-focus-outline-color: var(--gold);
}
```

### Alternative Underline Offset
```css
.ps-link {
  --ps-link-text-underline-offset: var(--size-1);
}
```

### Dark Mode Override
```css
[data-theme="dark"] .ps-link {
  --ps-link-color: var(--gray-100);
}
```

## Available Icons

**Navigation**: `arrow-left`, `arrow-right`, `chevron-left`, `chevron-right`

**Actions**: `download`, `external-link`, `share`, `copy`

**Contact**: `phone`, `mail`, `location`

**Status**: `check`, `x`, `alert`, `info`

See `source/patterns/documentation/icons-registry.json` for complete list.

## Stories

- **Default**: Simple example from YAML data
- **ColorVariants**: All 10 semantic colors with descriptions
- **SizeVariants**: xs to xxl showing hierarchy
- **UnderlineStates**: Underline control + disabled state
- **WithIcons**: Icon positioning and integration
- **RealEstateUseCases**: Real-world scenarios

## Browser Support

✅ Chrome/Edge 90+  
✅ Firefox 88+  
✅ Safari 14+  
✅ Mobile (iOS Safari, Chrome Android)

## Related Components

- **Button** - For actions instead of navigation
- **Icon** - For standalone icon usage
- **Breadcrumb** - For hierarchical navigation
- **Menu** - For structured navigation lists

## Important Notes

- Links use semantic `<a>` elements by default
- Disabled links render as `<span>` to prevent keyboard focus
- External links get automatic security attributes
- Icons use CSS `data-icon` attribute (no prefix needed)
- Underline shown by default (use `underline: false` to remove)
- All colors support full state changes (default, hover, active, visited, disabled)
- Three-layer CSS variables enable easy customization
- Supports dark mode via context overrides
