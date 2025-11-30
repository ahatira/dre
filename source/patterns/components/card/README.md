# Card

Versatile content presentation with image, title, description, metadata, and actions. Supports multiple variants (product, news, publication) and flexible layouts.

## Properties

| Property     | Type     | Default   | Required | Description                                                       |
| ------------ | -------- | --------- | -------- | ----------------------------------------------------------------- |
| `variant`    | `string` | `product` | No       | Card variant: `product`, `news`, `publication`, `solution`, `study`, `push`, `featured`, `compact` |
| `layout`     | `string` | `vertical`| No       | Card layout: `vertical`, `horizontal`                             |
| `title`      | `string` | —         | Yes      | Card title (required)                                             |
| `description`| `string` | —         | No       | Card description text                                             |
| `eyebrow`    | `string` | —         | No       | Eyebrow text above title                                          |
| `badge`      | `string` | —         | No       | Badge text to display on card                                     |
| `image`      | `object` | —         | No       | Image data: `{ url: string, alt: string }`                        |
| `meta`       | `array`  | —         | No       | Metadata items: `[{ icon: string, text: string }]`                |
| `cta`        | `object` | —         | No       | Call-to-action: `{ text: string, url: string, variant?: string }`|
| `url`        | `string` | —         | No       | Card link URL (makes entire card clickable)                       |
| `attributes` | `object` | —         | No       | Additional HTML attributes (Drupal Attribute object)              |

## BEM Structure

```
ps-card                        # Base card container
  ps-card__image               # Image wrapper
  ps-card__content             # Content wrapper
  ps-card__eyebrow             # Eyebrow text
  ps-card__title               # Title heading
  ps-card__description         # Description text
  ps-card__meta                # Metadata list
    ps-card__meta-item         # Individual metadata item
    ps-card__meta-icon         # Metadata icon
    ps-card__meta-text         # Metadata text
  ps-card__actions             # Actions wrapper
  
Modifiers:
  ps-card--product             # Product variant (default)
  ps-card--news                # News variant
  ps-card--publication         # Publication variant
  ps-card--solution            # Solution variant
  ps-card--study               # Study variant
  ps-card--push                # Push/featured highlight variant
  ps-card--featured            # Featured variant (larger padding, shadow)
  ps-card--compact             # Compact variant (smaller spacing)
  ps-card--vertical            # Vertical layout (default)
  ps-card--horizontal          # Horizontal layout
```

## Design Tokens

### Colors
- **Text**: `--gray-900` (title), `--gray-700` (description), `--gray-600` (eyebrow, meta)
- **Background**: `--white`
- **Borders**: `--gray-200`
- **Variant colors**: `--blue-600` (news), `--sky-600` (publication), `--green-600` (solution, push hover)

### Spacing
- **Padding**: `--size-5` (default content), `--size-6` (featured), `--size-4` (compact)
- **Gaps**: `--size-3` (content items), `--size-4` (meta items), `--size-2` (meta icon-text)

### Typography
- **Title**: `--font-size-3` (default), `--font-size-4` (featured), `--font-size-2` (compact), `--font-weight-700`
- **Description**: `--font-size-1` (default), `--font-size-0` (compact), `--leading-normal`
- **Eyebrow**: `--font-size-sm`, `--font-weight-600`, `--tracking-wide`
- **Meta**: `--font-size-0`

### Borders & Radius
- **Border**: `--border-size-1` (default), `--border-size-2` (push variant)
- **Radius**: `--radius-4`

### Shadows
- **Default**: `--shadow-4` (hover)
- **Featured**: `--shadow-3` (base)

### Transitions
- **Duration**: `--duration-2`
- **Easing**: `--ease-out`

## Variants

### Product (default)
Standard product card with 16:9 image aspect ratio. Used for property listings, product showcases.

### News
News article card with 4:3 image and blue eyebrow color.

### Publication
Publication/report card with 3:4 image (portrait) and sky-blue eyebrow.

### Solution
Service/solution card with green eyebrow, 16:9 image.

### Study
Case study card with 1:1 square image and gray eyebrow.

### Push
Highlighted card with green border (2px), draws attention to featured content.

### Featured
Premium variant with larger padding (`--size-6`), shadow, and bigger title font.

### Compact
Space-saving variant with reduced padding and smaller typography.

## Layouts

### Vertical (default)
Image on top, content below. Standard card layout.

### Horizontal
Image on left (40% width, 1:1 aspect), content on right. Suitable for list views.

## Usage Examples

### Basic Product Card

```twig
{% include '@components/card/card.twig' with {
  title: 'Luxury Apartment Paris 15th',
  description: 'Modern 3-room apartment, 65m², close to metro.',
  eyebrow: 'Apartment',
  image: {
    url: '/images/property.jpg',
    alt: 'Modern apartment'
  },
  meta: [
    { icon: 'pin-map', text: 'Paris 15th' },
    { icon: 'surface', text: '65 m²' },
    { icon: 'bedroom', text: '3 rooms' }
  ],
  cta: {
    text: 'View Property',
    url: '/property/123',
    variant: 'primary'
  }
} only %}
```

### News Card (Horizontal)

```twig
{% include '@components/card/card.twig' with {
  variant: 'news',
  layout: 'horizontal',
  title: 'Q4 Market Results Released',
  description: 'Strong performance across all business units.',
  eyebrow: 'Company News',
  image: {
    url: '/images/news.jpg',
    alt: 'Office building'
  },
  meta: [
    { icon: 'calendar', text: 'March 15, 2025' },
    { icon: 'user', text: 'John Doe' }
  ],
  cta: {
    text: 'Read More',
    url: '/news/q4-results'
  }
} only %}
```

### Clickable Card (No Button)

```twig
{% include '@components/card/card.twig' with {
  url: '/property/456',
  title: 'Featured Villa Cannes',
  description: '5 bedrooms, sea view, pool.',
  eyebrow: 'Villa',
  badge: 'Exclusive',
  image: {
    url: '/images/villa.jpg',
    alt: 'Villa with pool'
  },
  meta: [
    { icon: 'pin-map', text: 'Cannes' },
    { icon: 'surface', text: '250 m²' }
  ]
} only %}
```

### Featured Push Card

```twig
{% include '@components/card/card.twig' with {
  variant: 'push',
  title: 'Exclusive Investment Opportunity',
  description: 'Limited time offer for premium properties.',
  eyebrow: 'Featured',
  badge: 'Hot Deal',
  image: {
    url: '/images/featured.jpg',
    alt: 'Featured property'
  },
  meta: [
    { icon: 'pin-map', text: 'Paris CBD' },
    { icon: 'calendar', text: 'Expires Soon' }
  ],
  cta: {
    text: 'Contact Us',
    url: '/contact'
  }
} only %}
```

### Compact Card (No Image)

```twig
{% include '@components/card/card.twig' with {
  variant: 'compact',
  title: 'Quick Update',
  description: 'New listings available this week.',
  eyebrow: 'Update',
  meta: [
    { icon: 'calendar', text: 'Today' }
  ]
} only %}
```

## Real-World Use Cases

### Property Listings Grid
Display multiple property cards in a grid layout for search results or category pages.

### News/Blog Feed
Present articles with news variant, horizontal layout for easy scanning.

### Publications Library
Showcase reports, whitepapers with publication variant (portrait images).

### Featured Content
Highlight special offers or premium listings with push or featured variants.

### Related Content
Use compact variant for sidebar or footer suggestions.

### Service Pages
Display solutions/services with solution variant and descriptive icons.

## Accessibility

- **Semantic HTML**: Uses `<article>` for standalone cards, `<a>` when entire card is linked
- **Heading hierarchy**: Title uses `<h3>` (adjust level based on page context)
- **Alt text**: Always provide meaningful `alt` text for images
- **Focus states**: Linked cards have visible focus outline (`:focus-visible`)
- **Keyboard navigation**: Clickable cards are keyboard accessible
- **Loading**: Images use `loading="lazy"` for performance
- **Icon decoration**: Meta icons use `aria-hidden="true"` (text conveys meaning)
- **Color contrast**: All text meets WCAG AA standards (4.5:1 minimum)

### Recommendations

- Keep titles concise (max 60 characters)
- Limit metadata to 2-4 items for readability
- Provide descriptive alt text for images (not just "image")
- Use appropriate heading level based on page hierarchy
- For card grids, consider wrapping in `<section>` with heading
- Test keyboard navigation: Tab → Enter should activate card link

## Notes

- **Image aspect ratios** vary by variant (16:9 default, 4:3 news, 3:4 publication, 1:1 study/horizontal)
- **Horizontal layout** changes image to 40% width with 1:1 aspect ratio
- **Badge** displays via Badge atom component (primary color, small size)
- **CTA button** displays via Button atom (customizable variant)
- **Meta icons** use centralized icon system (no "icon-" prefix in code)
- **Independent modifiers**: All variants and layout modifiers work independently
- **Minimal markup**: Only applies modifier classes when different from defaults

## Browser Support

Modern browsers supporting CSS nesting, custom properties, aspect-ratio, and object-fit. Autoprefixed via PostCSS.
