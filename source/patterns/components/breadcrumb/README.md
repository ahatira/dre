# Breadcrumb

Navigation trail showing page hierarchy within the site structure, improving SEO and user experience with accessible semantic markup.

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `items` | array | `[]` | **Required**. Array of breadcrumb items. Each item: `{ label: string, url?: string }`. Last item is current page (no url). |
| `attributes` | Attribute | `null` | Additional HTML attributes for the `<nav>` element. |

## BEM Structure

```css
ps-breadcrumb                    // Block (nav element)
  ps-breadcrumb__list            // Ordered list
  ps-breadcrumb__item            // List item
  ps-breadcrumb__link            // Link element
  ps-breadcrumb__item--current   // Modifier for current item

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

### Short Path (2 levels)

```twig
{% include '@components/breadcrumb/breadcrumb.twig' with {
  items: [
    { label: 'Home', url: '/' },
    { label: 'Properties for Sale' }
  ]
} only %}
```

### Deep Hierarchy (7 levels)

```twig
{% include '@components/breadcrumb/breadcrumb.twig' with {
  items: [
    { label: 'Home', url: '/' },
    { label: 'Real Estate', url: '/real-estate' },
    { label: 'Commercial', url: '/real-estate/commercial' },
    { label: 'Office Buildings', url: '/real-estate/commercial/offices' },
    { label: 'Paris 8th District', url: '/real-estate/commercial/offices/paris-8' },
    { label: 'Champs-Élysées Premium Office Space' }
  ]
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
- **Rich snippets**: Breadcrumb markup enables Google Search breadcrumb display

## Notes

- Last item should **not** have a `url` property (represents current page)
- Separator is › character (U+203A - Single Right-Pointing Angle Quotation Mark)
- Separator is auto-generated via CSS `::after` pseudo-element (not a separate HTML element)
- All separator styling controlled via `--ps-breadcrumb-separator-*` CSS variables
- `aria-current="page"` is automatically applied to the last item
- Component uses 3-layer CSS variables system for maximum customization flexibility
