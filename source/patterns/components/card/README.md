# Card (Generic Container)

Card is a flexible container component that provides visual structure (border, padding, shadow) and layout options. Content is composed freely using Twig blocks, allowing maximum reusability across different use cases.

## Architecture Philosophy

Card is **NOT** a specialized component. It's a **generic container** that:
- ✅ Defines visual structure (border, radius, shadow, padding)
- ✅ Provides layout variants (vertical, horizontal)
- ✅ Offers size options (small, medium, large)
- ❌ Does NOT impose content structure (no title, price, badges, etc.)
- ❌ Does NOT include business logic (no favorites, status, etc.)

**Use composition** to create specialized cards (OfferCard, NewsCard, etc.) that embed Card.

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `variant` | `string` | `default` | Visual variant: `default`, `outlined`, `flat`, `elevated` |
| `layout` | `string` | `vertical` | Layout orientation: `vertical`, `horizontal` |
| `size` | `string` | `medium` | Padding size: `small`, `medium`, `large` |
| `radius` | `string` | `none` | Border radius: `none`, `sm`, `md`, `lg` |
| `imagePosition` | `string` | `top`/`left` | Image position: `top`/`bottom` (vertical), `left`/`right` (horizontal) |
| `url` | `string` | — | Optional link URL (wraps entire card as `<a>`) |
| `attributes` | `object` | — | Additional HTML attributes |

## BEM Structure

```
.ps-card (base container)
├── .ps-card__image (optional image/media zone)
└── .ps-card__content (main content wrapper)
    ├── .ps-card__header (optional header zone)
    ├── .ps-card__body (optional body zone)
    └── .ps-card__footer (optional footer zone)

Modifiers:
├── .ps-card--outlined
├── .ps-card--flat
├── .ps-card--elevated
├── .ps-card--horizontal
├── .ps-card--small
├── .ps-card--large
├── .ps-card--radius-none
├── .ps-card--radius-sm
├── .ps-card--radius-md
├── .ps-card--radius-lg
├── .ps-card--image-right (horizontal only)
└── .ps-card--image-bottom (vertical only)
```

## Twig Blocks

Card uses **Twig blocks** for content composition:

| Block | Description |
|-------|-------------|
| `image` | Image/media area (optional) |
| `content` | Main content (default block if no header/body/footer) |
| `header` | Header section (optional) |
| `body` | Body section (optional) |
| `footer` | Footer section (optional) |

## Design Tokens

### Visual
- Border: `1.5px solid #EBEDEF` (Figma exact: Grey #6)
- Border radius (default): `0` (customizable via `radius` prop)
  - None: `0` (default)
  - Small: `var(--radius-2)`
  - Medium: `var(--radius-4)`
  - Large: `var(--radius-6)`
- Background: `var(--white)`
- Shadow (hover/elevated): `var(--shadow-4)`

### Spacing
- Small padding: `var(--size-4)` (16px)
- Medium padding: `30px 24px` (Figma exact)
- Large padding: `var(--size-6)` (32px)
- Content gap: `var(--size-4)` (16px)

### Horizontal Layout
- Image width: `242px` (Figma exact)
- Image min-height: `212px` (Figma exact)

### Transitions
- Shadow: `var(--ps-transition-duration-normal)` + `var(--ease-out-2)`
- Transform: `var(--ps-transition-duration-fast)` + `var(--ease-out-1)`

## Usage

### Basic Card with Simple Content

```twig
{% embed '@components/card/card.twig' %}
  {% block content %}
    <h3>Card Title</h3>
    <p>Simple description text...</p>
  {% endblock %}
{% endembed %}
```

### Card with Image

```twig
{% embed '@components/card/card.twig' with { variant: 'elevated' } %}
  {% block image %}
    <img src="image.jpg" alt="Description" />
  {% endblock %}

  {% block content %}
    <h3>Title</h3>
    <p>Content goes here...</p>
  {% endblock %}
{% endembed %}
```

### Card with Header, Body, Footer

```twig
{% embed '@components/card/card.twig' %}
  {% block image %}
    <img src="image.jpg" alt="Image" />
  {% endblock %}

  {% block header %}
    <span class="badge">News</span>
    <span class="date">Nov 30, 2025</span>
  {% endblock %}

  {% block body %}
    <h3>Article Title</h3>
    <p>Article excerpt...</p>
  {% endblock %}

  {% block footer %}
    <a href="#">Read more →</a>
  {% endblock %}
{% endembed %}
```

### Horizontal Layout

```twig
{% embed '@components/card/card.twig' with { layout: 'horizontal' } %}
  {% block image %}
    <img src="image.jpg" alt="Image" />
  {% endblock %}

  {% block content %}
    <h3>Horizontal Card</h3>
    <p>Image on left (242px width)</p>
  {% endblock %}
{% endembed %}
```

### As Link (clickable card)

```twig
{% embed '@components/card/card.twig' with { url: '/article/123' } %}
  {% block content %}
    <h3>Clickable Card</h3>
    <p>Entire card is a link</p>
  {% endblock %}
{% endembed %}
```

### Visual Variants

```twig
{# Default: Standard border + hover shadow #}
{% embed '@components/card/card.twig' with { variant: 'default' } %}...{% endembed %}

{# Outlined: Thicker border, no shadow #}
{% embed '@components/card/card.twig' with { variant: 'outlined' } %}...{% endembed %}

{# Flat: No border, no shadow #}
{% embed '@components/card/card.twig' with { variant: 'flat' } %}...{% endembed %}

{# Elevated: Shadow always visible #}
{% embed '@components/card/card.twig' with { variant: 'elevated' } %}...{% endembed %}
```

### Size Options

```twig
{# Small: 16px padding #}
{% embed '@components/card/card.twig' with { size: 'small' } %}...{% endembed %}

{# Medium: 30px 24px padding (default) #}
{% embed '@components/card/card.twig' with { size: 'medium' } %}...{% endembed %}

{# Large: 32px padding #}
{% embed '@components/card/card.twig' with { size: 'large' } %}...{% endembed %}
```

### Border Radius Options

```twig
{# No radius: Sharp corners #}
{% embed '@components/card/card.twig' with { radius: 'none' } %}...{% endembed %}

{# Small radius: var(--radius-2) #}
{% embed '@components/card/card.twig' with { radius: 'sm' } %}...{% endembed %}

{# Medium radius: var(--radius-4) (default) #}
{% embed '@components/card/card.twig' with { radius: 'md' } %}...{% endembed %}

{# Large radius: var(--radius-6) #}
{% embed '@components/card/card.twig' with { radius: 'lg' } %}...{% endembed %}
```

### Image Position Options

```twig
{# Vertical layout with image on top (default) #}
{% embed '@components/card/card.twig' with { layout: 'vertical', imagePosition: 'top' } %}
  {% block image %}<img src="..." alt="Image" />{% endblock %}
  {% block content %}<h3>Image Top</h3>{% endblock %}
{% endembed %}

{# Vertical layout with image on bottom #}
{% embed '@components/card/card.twig' with { layout: 'vertical', imagePosition: 'bottom' } %}
  {% block image %}<img src="..." alt="Image" />{% endblock %}
  {% block content %}<h3>Image Bottom</h3>{% endblock %}
{% endembed %}

{# Horizontal layout with image on left (default) #}
{% embed '@components/card/card.twig' with { layout: 'horizontal', imagePosition: 'left' } %}
  {% block image %}<img src="..." alt="Image" />{% endblock %}
  {% block content %}<h3>Image Left</h3>{% endblock %}
{% endembed %}

{# Horizontal layout with image on right #}
{% embed '@components/card/card.twig' with { layout: 'horizontal', imagePosition: 'right' } %}
  {% block image %}<img src="..." alt="Image" />{% endblock %}
  {% block content %}<h3>Image Right</h3>{% endblock %}
{% endembed %}
```

## Composition Pattern

For specialized cards (products, news, events, etc.), **create dedicated components** that embed Card:

```twig
{# source/patterns/components/offer-card/offer-card.twig #}
{% embed '@components/card/card.twig' with { layout: layout } %}
  {% block image %}
    <img src="{{ image.url }}" alt="{{ image.alt }}" />
  {% endblock %}

  {% block content %}
    {# Offer-specific structure #}
    <div class="ps-offer-card__header">
      {# Status badges, actions, etc. #}
    </div>
    <h3 class="ps-offer-card__title">{{ title }}</h3>
    <p class="ps-offer-card__price">{{ price }}</p>
    {# ... #}
  {% endblock %}
{% endembed %}
```

This approach:
- ✅ Keeps Card generic and reusable
- ✅ Separates container (Card) from content (ProductCard)
- ✅ Allows different card types without modifying Card
- ✅ Easier maintenance and testing

## Specialized Components

These components **use** Card via composition:
- **OfferCard** - Real estate offer listings (status, price, location, CTA)
- **NewsCard** - News articles (tag, date, excerpt, read more)
- **EventCard** - Events (date, location, registration)
- **TestimonialCard** - Customer testimonials (quote, author, avatar)

See individual component documentation for details.

## Accessibility

- **Focus states**: Visible outline on linked cards (`:focus-visible`)
- **Keyboard navigation**: All interactive cards accessible via Tab/Enter
- **Semantic HTML**: Uses `<div>` by default, `<a>` when `url` provided
- **Image alt**: Required for images in blocks
- **Screen readers**: No ARIA needed for container (semantic zones)

## Browser Support

- Modern browsers (Chrome, Firefox, Safari, Edge)
- CSS nesting via PostCSS (postcss-nested)
- No JavaScript dependencies

## Related Components

- **ProductCard** - Specialized card for real estate products
- **Link** (element) - Used for card links
- **Badge** (element) - Can be used in composed content
- **Button** (element) - Can be used in composed content

## Migration Notes

If migrating from old Card component with specific props:
1. Create specialized component (e.g., `ProductCard`)
2. Move business logic to specialized component
3. Use `{% embed %}` with blocks to compose content
4. Update templates to use new component
5. Test all variants and layouts

See `.backup/card-refactor/` for old implementation reference.
