# Breadcrumb

Navigation trail showing page hierarchy within the site structure, improving SEO and user experience with accessible semantic markup.

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `items` | array | `[]` | **Required**. Array of breadcrumb items. Each item: `{ label: string, url?: string, icon?: string }`. Last item is current page (no url). |
| `compact` | boolean | `false` | Enable compact spacing (uses smaller typography tokens). |
| `truncate` | boolean | `false` | Enable CSS text truncation for long labels (limit width, ellipsis). |
| `color` | enum | `default` | Semantic color variant. One of: `default`, `primary`, `secondary`, `info`, `warning`, `success`, `danger`, `dark`, `light`. |
| `size` | enum | `md` | Size variant. One of: `xs`, `sm`, `md`, `lg`, `xl`, `xxl`. |
| `attributes` | Attribute | `null` | Additional HTML attributes for the `<nav>` element. |

## BEM Structure

```css
ps-breadcrumb                    // Block (nav element)
  ps-breadcrumb__list            // Ordered list
  ps-breadcrumb__item            // List item (separator via ::after)
  ps-breadcrumb__link            // Link element
  ps-breadcrumb__item--current   // Modifier for current item

Modifiers:
  ps-breadcrumb--compact         // Reduced spacing and font size
  ps-breadcrumb--truncate        // Text truncation enabled
  ps-breadcrumb--{color}         // Semantic color variant (see Props)
  ps-breadcrumb--{size}          // Size variant (see Props)

Note: Separator (›) is generated via CSS ::after pseudo-element
```

## Design Tokens

This component consumes the design tokens via a 3-layer mapping (contextual → semantic → base). No hardcoded values are used.

### Component-level variables

- `--ps-breadcrumb-color` → `var(--color-text-default, var(--text-default))`
- `--ps-breadcrumb-link-hover` → `var(--color-primary, var(--primary))`
- `--ps-breadcrumb-focus-color` → `var(--color-info, var(--info))`

### Core tokens referenced

- Typography: `--font-sans`, `--font-size-0`, `--font-size-1`, `--font-size-2`, `--font-size-3`, `--font-size-4`, `--leading-5`, `--leading-6`, `--leading-7`, `--leading-8`, `--leading-9`, `--font-weight-400`
- Colors: `--text-default`, `--primary`, `--info`, contextual `--color-*` (semantic layer)
- Spacing: `--size-1`, `--size-2`, `--border-size-2`, `--radius-1`
- Animations/Easing: `--duration-fast`, `--ease-3`

Note: The separator icon uses the `bnpre-icons` font; color is `currentColor` to inherit from `--ps-breadcrumb-color`.

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

### Compact Variant

```twig
{% include '@components/breadcrumb/breadcrumb.twig' with {
  items: [
    { label: 'Home', url: '/' },
    { label: 'Products', url: '/products' },
    { label: 'Electronics' }
  ],
  compact: true,
  color: 'default',
  size: 'sm'
} only %}
```

### With Icons

```twig
{% include '@components/breadcrumb/breadcrumb.twig' with {
  items: [
    { label: 'Home', url: '/', icon: 'home' },
    { label: 'Real Estate', url: '/real-estate', icon: 'building' },
    { label: 'Commercial Offices' }
  ],
  color: 'primary',
  size: 'md'
} only %}
```

### Truncated (for narrow containers)

```twig
{% include '@components/breadcrumb/breadcrumb.twig' with {
  items: [
    { label: 'Home', url: '/' },
    { label: 'Very Long Category Name', url: '/category' },
    { label: 'Current Page with Long Name' }
  ],
  truncate: true,
  color: 'default',
  size: 'xs'
} only %}
```

## Real-World Use Cases

- **Property page navigation**: Show hierarchy from homepage → location → property type → specific property
- **Blog post breadcrumb**: Display category path and current article
- **E-commerce product**: Navigate from home → category → subcategory → product
- **Documentation structure**: Show section → subsection → current page
- **Multi-level navigation**: Any hierarchical content structure requiring user orientation

## Accessibility

- **Semantic markup**: Uses `<nav aria-label="Breadcrumb">` for screen reader identification
- **Current page indicator**: Last item has `aria-current="page"` attribute
- **Separator handling**: Visual separator generated via CSS `::after` (invisible to screen readers)
- **Focus states**: All links have visible `:focus-visible` outline using semantic token (`--ps-breadcrumb-focus-color`) with sufficient contrast
- **Color contrast**: Link colors meet WCAG AA requirements (4.5:1 minimum)
- **Keyboard navigation**: Standard tab navigation through all links
- **Visited links**: Distinct color for visited states to aid navigation

## Variants

### Standard (default)

Regular font size (16px) with line-height 24px per Figma specs. Links are underlined, current page has no underline.

### Compact

Smaller font size (14px) with line-height 20px. Useful for:

- Mobile layouts
- Sidebars
- Dense interfaces
- Secondary navigation areas

### Truncate

Limits label width to 16 characters with ellipsis. Best for:

- Narrow containers (mobile, sidebars)
- Long category/page names
- Fixed-width layouts
- Preventing text wrapping in tight spaces

## Browser Support

- Modern browsers (Chrome, Firefox, Safari, Edge)
- CSS flexbox required
- CSS custom properties required
- Graceful degradation for older browsers (basic list display)

## SEO Benefits

- **Structured data compatible**: Can be enhanced with JSON-LD BreadcrumbList schema
- **Internal linking**: Improves site crawlability and link equity distribution
- **User context**: Reduces bounce rate by showing navigation path
- **Rich snippets**: Breadcrumb markup enables Google Search breadcrumb display

## Notes

- Last item should **not** have a `url` property (represents current page)
- Icon names should be passed **without** the `icon-` prefix
- Component uses `@elements/icon/icon.twig` for icon rendering
- Responsive flex-wrap allows natural line breaking on narrow screens
- `aria-current="page"` is automatically applied to the last item
 - Use `color` and `size` props to adapt appearance contextually; modifiers are independent and can be applied singly.
