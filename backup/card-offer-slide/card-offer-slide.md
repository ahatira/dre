# Card Offer Slide

**Compact card component for real estate property listings** in vertical layout, optimized for listing grids and galleries.

## Overview

Card Offer Slide displays properties with essential information in a compact, mobile-friendly format:
- Property image with favorite button overlay
- Compact "Price • Surface" header
- Product title
- Location with icon
- Call-to-action link

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `title` | `string` | `'Product title'` | Property title (required) |
| `price` | `string` | — | Price value (e.g., "20 000 €") |
| `surface` | `string` | — | Surface area (e.g., "611 m²") |
| `image` | `object` | — | Image data: `{ url: string, alt: string }` (required) |
| `location` | `string` | — | Location text (e.g., "28010 MADRID") |
| `locationIcon` | `string` | `'pin-map'` | Icon name for location (without icon- prefix) |
| `cta` | `object` | `{ text: 'Consulter l\'annonce', url: '#' }` | CTA: `{ text: string, url: string }` |
| `url` | `string` | — | Optional card link URL (makes entire card clickable) |
| `isFavorite` | `boolean` | `false` | Favorite state (filled heart icon when true) |
| `attributes` | `Attribute` | — | Additional HTML attributes |

## BEM Structure

```
ps-card (inherited from Card component)
  ps-card__media
    ps-card-offer-slide__media (position relative wrapper)
      [img tag]
      ps-card-offer-slide__favorite-wrapper (absolute positioned)
        ps-card-offer-slide__favorite (button)
  
  ps-card__content
    ps-card__body
      ps-card-offer-slide__header
        ps-card-offer-slide__price
        ps-card-offer-slide__separator (• bullet)
        ps-card-offer-slide__surface
      ps-card-offer-slide__title
      ps-card-offer-slide__location
        ps-card-offer-slide__location-icon
        ps-card-offer-slide__location-text
    
    ps-card__footer
      [Link component]
```

## Component Variables (Layer 2)

### Favorite Button
- `--ps-offer-card-list-favorite-size: var(--size-10)` - Button size (40px)
- `--ps-offer-card-list-favorite-bg: var(--white)` - Button background
- `--ps-offer-card-list-favorite-color: var(--gray-600)` - Default icon color
- `--ps-offer-card-list-favorite-hover-color: var(--danger)` - Hover state
- `--ps-offer-card-list-favorite-active-color: var(--danger)` - Active/pressed

### Header (Price • Surface)
- `--ps-offer-card-list-header-font-size: var(--font-size-1)` - Header size (16px)
- `--ps-offer-card-list-header-font-weight: var(--font-weight-700)` - Bold text

### Title
- `--ps-offer-card-list-title-font-size: var(--font-size-2)` - Title size (18px)
- `--ps-offer-card-list-title-font-weight: var(--font-weight-600)` - Semi-bold

### Location
- `--ps-offer-card-list-location-font-size: var(--font-size-0)` - Location size (14px)
- `--ps-offer-card-list-location-icon-size: var(--size-4)` - Icon size (16px)

## Usage

### Basic Example

```twig
{% include '@components/offer-card-list/offer-card-list.twig' with {
  title: 'Office Space PARIS',
  price: '650 €',
  surface: '2 450 m²',
  image: {
    url: '/images/office-paris.jpg',
    alt: 'Modern office in Paris'
  },
  location: 'Paris - La Défense',
  cta: {
    text: 'Consulter l\'annonce',
    url: '/properties/paris-123'
  }
} only %}
```

### With Favorite State

```twig
{% include '@components/offer-card-list/offer-card-list.twig' with {
  title: 'Retail Space LYON',
  price: '4 500 €',
  surface: '180 m²',
  image: {
    url: '/images/retail-lyon.jpg',
    alt: 'Retail space in Lyon'
  },
  location: 'Lyon - Part-Dieu',
  isFavorite: true,
  cta: {
    text: 'Voir les détails',
    url: '/properties/lyon-456'
  }
} only %}
```

### Property Grid (Drupal Loop)

```twig
<div class="property-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.5rem;">
  {% for property in properties %}
    {% include '@components/offer-card-list/offer-card-list.twig' with {
      title: property.title,
      price: property.price,
      surface: property.surface,
      image: {
        url: property.image.url,
        alt: property.image.alt
      },
      location: property.location,
      isFavorite: property.user_has_favorited,
      cta: {
        text: 'Consulter l\'annonce',
        url: property.url
      },
      url: property.url
    } only %}
  {% endfor %}
</div>
```

## Design Tokens Used

### Spacing (sizes.css)
- `--size-2` (8px) - Header gap, location gap
- `--size-3` (12px) - Favorite button position
- `--size-4` (16px) - Location icon size
- `--size-6` (24px) - Favorite icon size
- `--size-10` (40px) - Favorite button size

### Colors (colors.css, brand.css)
- `--white` - Favorite button background
- `--gray-400` - Separator bullet
- `--gray-600` - Favorite default color
- `--danger` - Favorite hover/active (red)
- `--text-primary` - Title, price, surface
- `--text-secondary` - Location text

### Typography (fonts.css)
- `--font-size-0` (14px) - Location
- `--font-size-1` (16px) - Price/surface header
- `--font-size-2` (18px) - Title
- `--font-weight-400` - Location (normal)
- `--font-weight-600` - Title (semi-bold)
- `--font-weight-700` - Price/surface (bold)

## Accessibility

**WCAG 2.2 Level AA Compliance** ✅

### ARIA & Semantics
- ✅ Favorite button: `aria-label` ("Add to favorites" / "Remove from favorites")
- ✅ Favorite state: `aria-pressed` attribute (true/false)
- ✅ Icons: `aria-hidden="true"` (text alternatives in labels)
- ✅ Title: Semantic `<h3>` heading

### Keyboard Navigation
- ✅ Tab order: Favorite button → CTA link
- ✅ Focus visible: 2px outline on interactive elements
- ✅ Button: `type="button"` for proper semantics

### Touch Targets
- ✅ Favorite button: 40×40px (exceeds 24×24px minimum)
- ✅ CTA link: Full footer width

## Dependencies

- `@components/card/card.twig` - Base container
- `@elements/link/link.twig` - CTA link
- Icons system (`source/props/icons.css`) - Location and favorite icons

## Browser Support

- ✅ Modern browsers (Chrome, Firefox, Safari, Edge)
- ✅ CSS Grid layout
- ✅ CSS Custom Properties
- ✅ Aspect-ratio for images

---

**Version**: 1.0.0  
**Date**: 2025-12-10  
**Status**: ✅ Stable
