# Card

Generic flexible container providing visual structure and layout variants for composable content.

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| variant | string | 'default' | Visual variant (default, outlined, flat, elevated) |
| layout | string | 'vertical' | Layout orientation (vertical, horizontal) |
| size | string | 'medium' | Padding size (small, medium, large) |
| radius | string | 'none' | Border radius (none, sm, md, lg) |
| imagePosition | string | 'top' | Image position (top/bottom for vertical, left/right for horizontal) |
| url | string | - | Optional URL (renders card as clickable `<a>` element) |
| image | string/html | - | Image/media HTML content (optional) |
| header | string/html | - | Header HTML content (optional) |
| body | string/html | - | Body HTML content (optional) |
| footer | string/html | - | Footer HTML content (optional) |
| content | string/html | - | Main content HTML (used if no header/body/footer) |
| attributes | object | - | Additional HTML attributes |

## Two Usage Patterns

### Pattern 1: Direct Props (Storybook, Simple Usage)

Pass HTML content directly as props. Best for Storybook stories and simple use cases:

```twig
{% include '@components/card/card.twig' with {
  radius: 'md',
  image: '<img src="/path/to/image.jpg" alt="Property" />',
  header: '<h3>Property Title</h3>',
  body: '<p>Description here...</p>',
  footer: '<a href="#">View details →</a>'
} only %}
```

### Pattern 2: Twig Blocks (Drupal, Complex Composition)

Use `{% embed %}` with Twig blocks for complex composition with child components:

```twig
{% embed '@components/card/card.twig' with {
  layout: 'horizontal',
  radius: 'md'
} only %}
  
  {% block image %}
    {% include '@elements/image/image.twig' with {
      src: '/images/property.jpg',
      alt: 'Property photo',
      ratio: '16x9'
    } only %}
  {% endblock %}
  
  {% block content %}
    <h3>Property Title</h3>
    <p>Description here...</p>
  {% endblock %}
  
{% endembed %}
```

**Note**: Twig blocks are only available with `{% embed %}` pattern, not with `{% include %}`.

## BEM Structure

```
ps-card                          (base container - article or a)
├── ps-card__image               (optional image/media zone)
└── ps-card__content             (main content wrapper)
    ├── ps-card__header          (optional header zone)
    ├── ps-card__body            (optional body zone)
    └── ps-card__footer          (optional footer zone)

Modifiers:
├── ps-card--outlined            (thick border)
├── ps-card--flat                (no border)
├── ps-card--elevated            (shadow)
├── ps-card--horizontal          (horizontal layout)
├── ps-card--small               (small padding: 16px)
├── ps-card--large               (large padding: 32px)
├── ps-card--radius-sm           (border radius: 4px)
├── ps-card--radius-md           (border radius: 8px)
├── ps-card--radius-lg           (border radius: 16px)
├── ps-card--image-right         (horizontal: image on right)
└── ps-card--image-bottom        (vertical: image on bottom)
```

## Design Tokens

### Visual
- `--white` - Background color
- `--gray-200` - Default border color
- `--gray-300` - Outlined variant border color
- `--shadow-2` - Elevated variant shadow (default)
- `--shadow-3` - Hover shadow (clickable cards)
- `--shadow-4` - Elevated hover shadow

### Border Radius
- `--radius-2` (4px) - Small radius
- `--radius-4` (8px) - Medium radius
- `--radius-6` (16px) - Large radius

### Spacing
- Small padding: `--size-4` (16px)
- Medium padding: `1.875rem 1.5rem` (30px 24px - Figma exact)
- Large padding: `--size-8` (32px)
- Content gap: `--size-4` (16px)

### Layout
- Horizontal image width: 40%
- Horizontal content width: 60%

### Transitions
- `--duration-fast` - Animation duration
- `--ease-3` - Easing function

## Usage

### Simple Card with Direct Props

```twig
{% include '@components/card/card.twig' with {
  radius: 'md',
  image: '<img src="/property.jpg" alt="Property" style="width: 100%; height: 240px; object-fit: cover;" />',
  header: '<h3>Modern Office Space</h3>',
  body: '<p>Premium office building in Madrid...</p>',
  footer: '<span>View details →</span>'
} only %}
```

### Card with Structured Sections

```twig
{% embed '@components/card/card.twig' with {
  layout: 'vertical',
  radius: 'md',
  size: 'medium'
} only %}
  
  {% block image %}
    {% include '@elements/image/image.twig' with {
      src: '/images/property.jpg',
      alt: 'Property photo',
      ratio: '16x9'
    } only %}
  {% endblock %}
  
  {% block header %}
    <h3>Property Title</h3>
  {% endblock %}
  
  {% block body %}
    <p>Description here...</p>
  {% endblock %}
  
  {% block footer %}
    <a href="#">View details →</a>
  {% endblock %}
  
{% endembed %}
```

### Horizontal Card with Image on Right

```twig
{% embed '@components/card/card.twig' with {
  layout: 'horizontal',
  imagePosition: 'right',
  radius: 'lg',
  variant: 'elevated'
} only %}
  
  {% block image %}
    <img src="/path/to/image.jpg" alt="Description" />
  {% endblock %}
  
  {% block content %}
    <h3>Content Title</h3>
    <p>Description...</p>
  {% endblock %}
  
{% endembed %}
```

### Clickable Card (as Link)

```twig
{% embed '@components/card/card.twig' with {
  url: '/property/123',
  variant: 'elevated',
  radius: 'md'
} only %}
  
  {% block image %}
    <img src="/property.jpg" alt="Property" />
  {% endblock %}
  
  {% block content %}
    <h3>Click to view details</h3>
    <p>Entire card is clickable.</p>
  {% endblock %}
  
{% endembed %}
```

## Real-World Use Cases

### Property Listing Card
```twig
{% embed '@components/card/card.twig' with {
  url: '/property/456',
  variant: 'elevated',
  radius: 'lg'
} only %}
  {% block image %}
    {% include '@elements/image/image.twig' with {
      src: '/images/penthouse-paris.jpg',
      alt: 'Penthouse Paris 16e',
      ratio: '4x3'
    } only %}
  {% endblock %}
  
  {% block header %}
    <div style="display: flex; justify-content: space-between;">
      <h3>Penthouse Paris 16e</h3>
      {% include '@elements/badge/badge.twig' with {
        text: 'NEW',
        variant: 'primary',
        size: 'small'
      } only %}
    </div>
  {% endblock %}
  
  {% block body %}
    <p>Exceptional duplex penthouse with 360° views...</p>
    <div class="property-features">
      <span>🛏️ 4 bedrooms</span>
      <span>🚿 3 bathrooms</span>
      <span>📏 280 m²</span>
    </div>
  {% endblock %}
  
  {% block footer %}
    <div style="display: flex; justify-content: space-between;">
      <span class="price">€2,500,000</span>
      <span>View details →</span>
    </div>
  {% endblock %}
{% endembed %}
```

### News/Blog Card (Horizontal)
```twig
{% embed '@components/card/card.twig' with {
  url: '/news/market-trends-2025',
  layout: 'horizontal',
  radius: 'md'
} only %}
  {% block image %}
    <img src="/news/market-trends.jpg" alt="Market trends" />
  {% endblock %}
  
  {% block header %}
    <span class="category-badge">MARKET INSIGHTS</span>
    <h3>European Real Estate Market Trends 2025</h3>
  {% endblock %}
  
  {% block body %}
    <p>Analysis of key trends shaping the commercial real estate sector...</p>
  {% endblock %}
  
  {% block footer %}
    <span class="meta">Dec 3, 2025 • 5 min read</span>
  {% endblock %}
{% endembed %}
```

### Info Card (No Image)
```twig
{% embed '@components/card/card.twig' with {
  variant: 'outlined',
  radius: 'md',
  size: 'small'
} only %}
  {% block header %}
    <h3>Contact Information</h3>
  {% endblock %}
  
  {% block body %}
    <p>📧 contact@bnpparibas-realestate.com</p>
    <p>📞 +33 1 55 65 20 00</p>
    <p>📍 167 Quai de la Bataille de Stalingrad, Paris</p>
  {% endblock %}
{% endembed %}
```

### Creating Specialized Cards

For domain-specific cards (e.g., OfferCard, NewsCard), create a component that embeds Card:

```twig
{# offer-card.twig - Specialized card composing generic Card #}
{% embed '@components/card/card.twig' with {
  url: url,
  variant: 'elevated',
  radius: 'lg'
} only %}
  
  {% block image %}
    {% include '@elements/image/image.twig' with {
      src: image.url,
      alt: image.alt,
      ratio: '4x3'
    } only %}
  {% endblock %}
  
  {% block header %}
    <h3>{{ title }}</h3>
    {% if badge %}
      {% include '@elements/badge/badge.twig' with badge only %}
    {% endif %}
  {% endblock %}
  
  {% block body %}
    <p>{{ description }}</p>
    {% if meta %}
      <div class="meta-info">
        {% for item in meta %}
          <span>{{ item.icon }} {{ item.text }}</span>
        {% endfor %}
      </div>
    {% endif %}
  {% endblock %}
  
  {% block footer %}
    <div class="price-cta">
      <span class="price">{{ price }}</span>
      <span class="cta">View details →</span>
    </div>
  {% endblock %}
  
{% endembed %}
```

## Composition with Atoms

Card is a **generic container** that commonly composes these atoms:

- **`@elements/image/image.twig`** - Images with aspect ratios
- **`@elements/heading/heading.twig`** - Semantic headings
- **`@elements/text/text.twig`** - Text/descriptions
- **`@elements/link/link.twig`** - Links/CTAs
- **`@elements/badge/badge.twig`** - Status badges/labels
- **`@elements/button/button.twig`** - Action buttons

## Accessibility

- **Semantic HTML**: Uses `<article>` by default, `<a>` when URL provided
- **Heading hierarchy**: Delegated to child components (use appropriate heading levels)
- **Image alt text**: Required on images (delegated to image block)
- **Keyboard navigation**: Clickable cards are fully keyboard accessible (Tab + Enter)
- **Focus visible**: Clickable cards have visible focus outline (`:focus-visible`)
- **Color contrast**: WCAG AA compliant (border color #E8EBEF vs white background)
- **ARIA**: `role="article"` implicit, no additional ARIA needed
- **Link semantics**: When URL provided, entire card is clickable (better UX than nested links)

## Variants

### Visual Variants
- **default**: Standard border (`1px solid #E8EBEF`)
- **outlined**: Thick border (`2px solid #D0D5DD`)
- **flat**: No border
- **elevated**: Shadow instead of border (hover effect enhanced)

### Layout Variants
- **vertical** (default): Image top/bottom, content below/above
- **horizontal**: Image left/right (40% width), content beside (60% width)

### Size Variants
- **small**: 16px padding, 12px gap
- **medium** (default): 30px/24px padding (Figma exact), 16px gap
- **large**: 32px padding, 20px gap

### Border Radius Variants
- **none** (default): No border radius (0)
- **sm**: 4px border radius
- **md**: 8px border radius
- **lg**: 16px border radius

### Image Position
- **Vertical layout**: top (default) | bottom
- **Horizontal layout**: left (default) | right

## Responsive Behavior

- **Mobile (< 768px)**: Horizontal cards automatically stack vertically
- **Padding**: Remains consistent across breakpoints (adjust via size prop if needed)
- **Image**: Maintains aspect ratio, fills container width on mobile

## Notes

- **Generic container**: Card does NOT enforce content structure. Use Twig blocks for flexibility.
- **Composition over specialization**: Create specialized cards (OfferCard, NewsCard) by embedding generic Card.
- **No typography**: Card delegates font sizes, weights, colors to child components.
- **No predefined slots**: Unlike specialized components, Card doesn't force title/price/meta structure.
- **Progressive enhancement**: Works without JavaScript. Hover effects are CSS-only.
- **Clickable pattern**: When `url` provided, entire card becomes clickable (better UX than small "Read more" links).

## Browser Support

- Modern browsers (Chrome, Firefox, Safari, Edge)
- CSS Grid and Flexbox required
- `:focus-visible` supported (graceful degradation to `:focus` in older browsers)

---

**Component Type**: Molecule (Generic Container)  
**Category**: Components  
**Status**: Stable  
**Version**: 1.0.0
