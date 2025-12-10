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

Note: Separator (›) is generated via ::after pseudo-element with SVG mask (chevron-right)
```

## Design Tokens

- Typography: `--font-sans`, `--font-size-1` (14px), `--leading-6` (24px), `--font-weight-400`
- Colors: `--text-primary`, `--gray-500` (separator), `--primary` (hover), `--info` (focus)
- Spacing: `--size-1` (4px separator margin)
- Borders: `--border-size-2`, `--radius-1`
- Animations: `--duration-fast`, `--ease-3`
- Separator: › (right-pointing angle quotation mark)

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

### Simple (2 levels)

```twig
{% include '@components/breadcrumb/breadcrumb.twig' with {
  items: [
    { label: 'Home', url: '/' },
    { label: 'Current Page' }
  ]
} only %}
```

### Deep Hierarchy

```twig
{% include '@components/breadcrumb/breadcrumb.twig' with {
  items: [
    { label: 'Home', url: '/' },
    { label: 'Real Estate', url: '/real-estate' },
    { label: 'Commercial', url: '/real-estate/commercial' },
    { label: 'Office Buildings', url: '/real-estate/commercial/offices' },
    { label: 'Paris 8th District' }
  ]
} only %}
```

## Real-World Use Cases

- **Property page navigation**: Show hierarchy from homepage → location → property type → specific property
- **Blog post breadcrumb**: Display category path and current article
- **E-commerce product**: Navigate from home → category → subcategory → product
- **Documentation structure**: Show section → subsection → current page
## Accessibility
## Accessibility

- **Semantic markup**: Uses `<nav aria-label="Breadcrumb">` for screen reader identification
- **Current page indicator**: Last item has `aria-current="page"` attribute
- **Separator handling**: Visual separator generated via CSS `::after` (invisible to screen readers)
- **Focus states**: All links have visible `:focus-visible` outline with sufficient contrast (WCAG AA)
- **Color contrast**: Link colors meet WCAG AA requirements (4.5:1 minimum)
- **Keyboard navigation**: Standard tab navigation through all links

## SEO Benefits

- **Structured data compatible**: Can be enhanced with JSON-LD BreadcrumbList schema
- **Internal linking**: Improves site crawlability and link equity distribution
- **User context**: Reduces bounce rate by showing navigation path
- **Rich snippets**: Breadcrumb markup enables Google Search breadcrumb display
## Notes
## Notes

- Last item should **not** have a `url` property (represents current page)
- Responsive flex-wrap allows natural line breaking on narrow screens
- `aria-current="page"` is automatically applied to the last item
- Separator is simple › character (right-pointing angle quotation mark) with `--gray-500` color
- Pixel perfect: 14px font, 24px line-height, 4px gap between items and separator` font (invisible to screen readers)
