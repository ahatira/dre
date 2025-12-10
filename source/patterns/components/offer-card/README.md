# Offer Card

**Specialized card component for real estate property listings** that embeds the generic Card component with property-specific content and styling.

## Overview

Offer Card displays real estate properties with:
- Property image with overlay badges (viewed, exclusivity) and action buttons (compare, favorite)
- Property information: title, surface area, location metadata
- Pricing and call-to-action
- Vertical (mobile-friendly) and horizontal (desktop grid) layouts

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `layout` | `string` | `'vertical'` | Layout orientation: `vertical`, `horizontal` |
| `title` | `string` | — | Property title (required) |
| `surface` | `string` | — | Surface area (e.g., "611.3 m²") |
| `price` | `string` | — | Price with unit (e.g., "20 000 € HT/HC/m²/an") |
| `image` | `object` | — | Image data: `{ url: string, alt: string }` (required) |
| `meta` | `array` | `[]` | Metadata items: `[{ icon: string, text: string }]` |
| `status` | `object` | `{ viewed: false, exclusivity: false }` | Status badges: `{ viewed: boolean, exclusivity: boolean }` |
| `cta` | `object` | `{ text: 'View the property', url: '#' }` | Call-to-action: `{ text: string, url: string }` |
| `url` | `string` | — | Optional card link URL (makes entire card clickable) |
| `attributes` | `Attribute` | — | Additional HTML attributes |

## BEM Structure

```
ps-card (inherited from Card component)
  ps-card__media
    ps-offer-card__media (position relative wrapper)
      [Image component]
      ps-offer-card__overlay (absolute positioned)
        ps-offer-card__badges
          ps-offer-card__badge
            ps-offer-card__badge--viewed
            ps-offer-card__badge--gold
          ps-offer-card__badge-icon
          ps-offer-card__badge-text
        ps-offer-card__actions
          ps-offer-card__action
            ps-offer-card__action--compare
            ps-offer-card__action--favorite
  
  ps-card__content
    ps-card__body
      ps-offer-card__title
      ps-offer-card__surface
      ps-offer-card__meta
        ps-offer-card__meta-item
          ps-offer-card__meta-icon
          ps-offer-card__meta-text
    
    ps-card__footer
      ps-offer-card__price
      [Link component]
```

## Component Variables (Layer 2)

All component variables use design tokens from `source/props/`:

### Overlay
- `--ps-offer-card-overlay-padding: var(--size-4)` - Overlay padding
- `--ps-offer-card-overlay-gap: var(--size-3)` - Gap between badges and actions

### Badges
- `--ps-offer-card-badge-height: var(--size-6)` - Badge height
- `--ps-offer-card-badge-padding-inline: var(--size-3)` - Horizontal padding
- `--ps-offer-card-badge-gap: var(--size-2)` - Gap between icon and text
- `--ps-offer-card-badge-radius: var(--radius-pill)` - Border radius
- `--ps-offer-card-badge-viewed-bg: var(--gray-100)` - Viewed badge background
- `--ps-offer-card-badge-gold-bg: var(--gold)` - Exclusivity badge background

### Actions
- `--ps-offer-card-action-size: var(--size-8)` - Action button size
- `--ps-offer-card-action-bg: var(--white)` - Button background
- `--ps-offer-card-action-color: var(--gray-600)` - Default icon color
- `--ps-offer-card-action-hover-color: var(--primary)` - Hover state color
- `--ps-offer-card-action-active-color: var(--secondary)` - Active/pressed state

### Typography
- `--ps-offer-card-title-font-size: var(--font-size-1)` - Title size (16px)
- `--ps-offer-card-surface-font-weight: var(--font-weight-700)` - Surface bold
- `--ps-offer-card-price-font-size: var(--font-size-3)` - Price size (20px)
- `--ps-offer-card-meta-font-size: var(--font-size-0)` - Meta size (14px)

## Usage

### Basic Example

```twig
{% include '@components/offer-card/offer-card.twig' with {
  title: 'Rent Offices MADRID Barrio de Chamberí',
  surface: '611.3 m²',
  price: '20 000 € HT/HC/m²/an',
  image: {
    url: '/images/property-madrid.jpg',
    alt: 'Modern office space in Madrid'
  },
  meta: [
    { icon: 'pin-map', text: '28010 MADRID' }
  ],
  cta: {
    text: 'View the property',
    url: '/properties/madrid-123'
  }
} only %}
```

### With Status Badges

```twig
{% include '@components/offer-card/offer-card.twig' with {
  title: 'Rent Warehouse LYON',
  surface: '2,800 m²',
  price: '8,500 € / month',
  image: {
    url: '/images/warehouse-lyon.jpg',
    alt: 'Warehouse in Lyon'
  },
  status: {
    viewed: true,
    exclusivity: true
  },
  meta: [
    { icon: 'pin-map', text: 'Lyon Industrial Zone' }
  ],
  cta: {
    text: 'View details',
    url: '/properties/lyon-456'
  }
} only %}
```

### Horizontal Layout (Desktop Grid)

```twig
{% include '@components/offer-card/offer-card.twig' with {
  layout: 'horizontal',
  title: 'Sale Apartment PARIS 16ème',
  surface: '120 m²',
  price: '1 500 000 €',
  image: {
    url: '/images/apartment-paris.jpg',
    alt: 'Luxury apartment in Paris'
  },
  meta: [
    { icon: 'pin-map', text: '75016 PARIS' }
  ],
  cta: {
    text: 'Schedule visit',
    url: '/properties/paris-789'
  }
} only %}
```

### Clickable Card (As Link)

```twig
{% include '@components/offer-card/offer-card.twig' with {
  url: '/properties/madrid-office-123',
  title: 'Premium Office Space',
  surface: '450 m²',
  price: '18 000 € HT/HC/m²/an',
  image: {
    url: '/images/office.jpg',
    alt: 'Premium office space'
  },
  status: {
    exclusivity: true
  },
  meta: [
    { icon: 'pin-map', text: 'Madrid Business District' }
  ]
} only %}
```

### Property Listing Loop (Drupal)

```twig
<div class="property-grid">
  {% for property in properties %}
    {% include '@components/offer-card/offer-card.twig' with {
      title: property.title,
      surface: property.surface,
      price: property.price,
      image: {
        url: property.image.url,
        alt: property.image.alt
      },
      meta: property.metadata,
      status: {
        viewed: property.user_has_viewed,
        exclusivity: property.is_exclusive
      },
      cta: {
        text: 'View details',
        url: property.url
      },
      url: property.url
    } only %}
  {% endfor %}
</div>
```

## Composition Architecture

Offer Card **embeds** the generic Card component using Twig's `{% embed %}` pattern:

```twig
{% embed '@components/card/card.twig' with {
  layout: layout,
  variant: 'elevated',
  radius: 'md'
} only %}

  {% block media %}
    {# Image with overlay badges/actions #}
  {% endblock %}

  {% block body %}
    {# Title, surface, metadata #}
  {% endblock %}

  {% block footer %}
    {# Price and CTA link #}
  {% endblock %}

{% endembed %}
```

### Dependencies

- `@components/card/card.twig` - Base container component
- `@elements/image/image.twig` - Property image rendering
- `@elements/link/link.twig` - CTA link component
- Icons system (`source/props/icons.css`) - Badge and meta icons

## Accessibility

### ARIA & Semantics
- ✅ Action buttons use `type="button"` and descriptive `aria-label`
- ✅ Icons use `aria-hidden="true"` (text alternatives provided)
- ✅ Status badges provide visible text labels ("Already viewed", "Exclusivity")
- ✅ Link card uses semantic `<a>` element when `url` is provided

### Keyboard Navigation
- ✅ All interactive elements (buttons, links) keyboard accessible
- ✅ Focus indicators via `focus-visible` (2px outline)
- ✅ Logical tab order: compare → favorite → CTA link

### Screen Readers
- ✅ Property title uses `<h3>` heading for document structure
- ✅ Metadata list uses semantic `<ul>` with `list-style: none`
- ✅ Price and surface use semantic text elements

### Color Contrast
- ✅ Viewed badge: Gray text on light gray (WCAG AA compliant)
- ✅ Gold badge: White text on gold background (WCAG AAA)
- ✅ Action buttons: Sufficient contrast in all states
- ✅ Price text: Dark text on white background

## Responsive Behavior

### Vertical Layout (Default)
- **All screens**: Image top, content below
- **Image ratio**: 3:2 (aspect-ratio)
- **Content padding**: Inherited from Card `size="medium"` (24px)

### Horizontal Layout
- **Desktop (≥768px)**: Image left (40%), content right (60%)
- **Mobile (<768px)**: Automatically stacks to vertical layout
- **Image height**: Matches content height (flexbox stretch)

### Overlay
- **All layouts**: Badges and actions positioned absolute over image
- **Padding**: 16px (var(--size-4)) from image edges
- **Responsive gap**: 12px (var(--size-3)) between badges and actions

## Browser Support

- ✅ Modern browsers (Chrome, Firefox, Safari, Edge)
- ✅ CSS Grid and Flexbox layout
- ✅ CSS Custom Properties (design tokens)
- ✅ CSS Nesting (PostCSS transformed)
- ✅ Aspect-ratio for image sizing

## Related Components

- **Card** - Base container component (embedded by Offer Card)
- **Image** - Property image rendering
- **Link** - CTA link styling
- **Badge** - Status indicator styling reference

## Design Tokens Reference

All tokens defined in `source/props/`:
- **Spacing**: `--size-*` (sizes.css)
- **Colors**: `--gray-*`, `--gold`, `--primary`, `--secondary` (colors.css)
- **Typography**: `--font-size-*`, `--font-weight-*` (fonts.css)
- **Radii**: `--radius-pill`, `--radius-full` (borders.css)
- **Shadows**: `--shadow-2` (shadows.css)
- **Durations**: `--duration-fast` (animations.css)
- **Easing**: `--ease-out-1` (easing.css)

## Notes

- **Pixel Perfect**: Implementation matches Figma designs exactly
- **No Hardcoded Values**: All styles use design tokens
- **Minimal CSS**: Only 250 lines (vs 392 in old version)
- **Proper Composition**: Uses embed pattern, not include
- **Accessibility First**: WCAG 2.2 AA compliant
- **Real Estate Context**: Faker.js examples for property data

---

**Version**: 2.0.0 (Complete redesign)  
**Last Updated**: 2025-12-10  
**Status**: ✅ Stable
