# Breadcrumb

Navigation trail showing page hierarchy within the site structure, improving SEO and user experience with accessible semantic markup.

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `items` | array | `[]` | **Required**. Array of breadcrumb items. Each item: `{ label: string, url?: string, icon?: string }`. Last item is current page (no url). |
| `compact` | boolean | `false` | Reduced size variant (12px font, 2px separator margin). Useful for sidebars, footers. |
| `truncate` | boolean | `false` | Truncate intermediate items with ellipsis (max 20ch). First and last items remain fully visible. |
| `inverted` | boolean | `false` | Dark theme variant with white text for light backgrounds (hero sections, dark headers). |
| `attributes` | Attribute | `null` | Additional HTML attributes for the `<nav>` element. |

## BEM Structure

```css
ps-breadcrumb                    // Block (nav element)
  ps-breadcrumb__list            // Ordered list
  ps-breadcrumb__item            // List item
  ps-breadcrumb__link            // Link element
  ps-breadcrumb__icon            // Icon container (optional, via data-icon)
  ps-breadcrumb__item--current   // Modifier for current item

Modifiers:
  ps-breadcrumb--compact         // Reduced size (12px font, 2px separator)
  ps-breadcrumb--truncate        // Ellipsis on intermediate items
  ps-breadcrumb--inverted        // Dark theme (white text)

Note: Separator (›) is auto-generated via ::after pseudo-element (not a BEM element)
```

## CSS Variables System (3-Layer Architecture)

### Layer 2: Component-Scoped Variables

The component defines customizable variables that can be overridden in context:

```css
/* Typography */
--ps-breadcrumb-font-family: var(--font-sans);
--ps-breadcrumb-font-size: var(--font-size-1);        /* 14px */
--ps-breadcrumb-font-weight: var(--font-weight-400);
--ps-breadcrumb-line-height: var(--leading-6);        /* 24px */

/* Colors */
--ps-breadcrumb-color: var(--text-primary);
--ps-breadcrumb-link-color: var(--text-primary);
--ps-breadcrumb-link-hover-color: var(--primary);
--ps-breadcrumb-separator-color: var(--gray-500);

/* Spacing */
--ps-breadcrumb-separator-margin: var(--size-1);      /* 4px */
--ps-breadcrumb-list-gap: 0;

/* Focus */
--ps-breadcrumb-focus-outline-width: var(--border-size-2);
--ps-breadcrumb-focus-outline-color: var(--secondary);
--ps-breadcrumb-focus-outline-offset: var(--border-size-2);
--ps-breadcrumb-focus-border-radius: var(--radius-1);

/* Transitions */
--ps-breadcrumb-transition-duration: var(--duration-fast);
--ps-breadcrumb-transition-timing: var(--ease-3);

/* Separator (U+203A) */
--ps-breadcrumb-separator-content: '›';
```

### Customization Example

```css
/* Override separator margin for compact layout */
.sidebar .ps-breadcrumb {
  --ps-breadcrumb-separator-margin: var(--size-05);  /* 2px instead of 4px */
  --ps-breadcrumb-font-size: var(--font-size-0);     /* 12px instead of 14px */
}

/* Custom separator character */
.ps-breadcrumb--slash {
  --ps-breadcrumb-separator-content: '/';
}

/* Dark theme override */
[data-theme="dark"] .ps-breadcrumb {
  --ps-breadcrumb-link-color: var(--gray-200);
  --ps-breadcrumb-link-hover-color: var(--primary-light);
}
```

## Design Tokens (Layer 1 References)

- Typography: `--font-sans`, `--font-size-1` (14px), `--leading-6` (24px), `--font-weight-400`
- Colors: `--text-primary`, `--gray-500` (separator), `--primary` (hover), `--secondary` (focus)
- Spacing: `--size-1` (4px separator margin)
- Borders: `--border-size-2`, `--radius-1`
- Animations: `--duration-fast`, `--ease-3`
- Separator: › (U+203A - Single Right-Pointing Angle Quotation Mark)

## Usage

### Basic Example

```twig
{% include '@components/breadcrumb/breadcrumb.twig' with {
  items: [
    { label: 'Home', url: '/' },
    { label: 'Locations', url: '/locations' },
    { label: 'Paris 15e', url: '/locations/paris-15' },
    { label: 'Family Apartment' }
  ]
} only %}
```

### With Icons

```twig
{% include '@components/breadcrumb/breadcrumb.twig' with {
  items: [
    { label: 'Home', url: '/', icon: 'home' },
    { label: 'Commercial Properties', url: '/commercial', icon: 'office' },
    { label: 'Office Buildings', url: '/commercial/offices', icon: 'commercial-space' },
    { label: 'Premium Office Space' }
  ]
} only %}
```

### Compact Variant

```twig
{% include '@components/breadcrumb/breadcrumb.twig' with {
  items: [
    { label: 'Home', url: '/' },
    { label: 'Properties', url: '/properties' },
    { label: 'Current Property' }
  ],
  compact: true
} only %}
```

## Real-World Use Cases

- **Property page navigation**: Show hierarchy from homepage → location → property type → specific property
- **Compact layouts**: Use `compact: true` for sidebars, footers, or space-constrained areas
- **Deep hierarchies**: Use `truncate: true` for very deep navigation paths (>5 levels) with long labels
- **Dark hero sections**: Use `inverted: true` for breadcrumbs on dark backgrounds
- **Branded navigation**: Add icons (home, office, building) for better visual hierarchy
- **SEO optimization**: Structured navigation path improves crawlability and rich snippets
    { label: 'Commercial Real Estate Premium Listings', url: '/commercial' },
    { label: 'Office Buildings and Workspaces', url: '/offices' },
    { label: 'Current Property with Very Long Descriptive Name' }
  ],
  truncate: true
} only %}
```

### Inverted Theme (Dark Background)

```twig
{% include '@components/breadcrumb/breadcrumb.twig' with {
  items: [
    { label: 'Home', url: '/' },
    { label: 'Luxury Properties', url: '/luxury' },
    { label: 'Exclusive Penthouse' }
  ],
  inverted: true
} only %}
```

## Real-World Use Cases

- **Property page navigation**: Show hierarchy from homepage → location → property type → specific property
- **Blog post breadcrumb**: Display category path and current article
- **E-commerce product**: Navigate from home → category → subcategory → product
- **Documentation structure**: Show section → subsection → current page

## Accessibility

- **Semantic markup**: Uses `<nav aria-label="Breadcrumb">` for screen reader identification
- **Current page indicator**: Last item has `aria-current="page"` attribute
- **Separator handling**: Visual separator (›) generated via CSS `::after` pseudo-element (automatically invisible to screen readers - not announced)
- **Focus states**: All links have visible `:focus-visible` outline with sufficient contrast (WCAG AA)
- **Color contrast**: Link colors meet WCAG AA requirements (4.5:1 minimum)
- **Keyboard navigation**: Standard tab navigation through all links
- **Responsive**: Flex-wrap ensures natural line breaking on narrow screens

## SEO Benefits

- **Structured data compatible**: Can be enhanced with JSON-LD BreadcrumbList schema
- **Internal linking**: Improves site crawlability and link equity distribution
- **User context**: Reduces bounce rate by showing navigation path
## Notes

- Last item should **not** have a `url` property (represents current page)
- Separator is › character (U+203A - Single Right-Pointing Angle Quotation Mark)
- Separator is auto-generated via CSS `::after` pseudo-element (not a separate HTML element)
- All separator styling controlled via `--ps-breadcrumb-separator-*` CSS variables
- `aria-current="page"` is automatically applied to the last item
- Component uses 3-layer CSS variables system for maximum customization flexibility
- **Icons**: Use icon names from the icon system (e.g., 'home', 'office', 'commercial-space')
- **Compact**: Reduces font from 14px to 12px and separator margin from 4px to 2px
- **Truncate**: Applies `max-width: 20ch` with ellipsis to intermediate items only
- **Inverted**: Changes all colors to white/light variants for dark backgrounds
- `aria-current="page"` is automatically applied to the last item
- Component uses 3-layer CSS variables system for maximum customization flexibility
