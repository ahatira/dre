# Card Offer Slide

**Compact card component for real estate property listings** in sliders and galleries, optimized for vertical layout.

## Overview

Card Offer Slide extends the base Card component to display property offers with:
- Property image (3:2 aspect ratio)
- Favorite toggle button (heart icon overlay)
- Compact "Price • Surface" header
- Property title
- Location with pin icon
- Call-to-action link

Perfect for property listings, search results, and featured properties in sliders.

---

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `title` | `string` | `'Product title'` | Property title (required) |
| `price` | `string` | — | Price value (e.g., "650 €", "20 000 €") |
| `surface` | `string` | — | Surface area (e.g., "611 m²", "2 450 m²") |
| `image` | `object` | — | Image data: `{ url: string, alt: string }` (required) |
| `location` | `string` | — | Location text (e.g., "Madrid", "Paris - La Défense") |
| `locationIcon` | `string` | `'pin-map'` | Icon name for location (without icon- prefix) |
| `cta` | `object` | `{ text: 'Consulter l\'annonce', url: '#' }` | CTA: `{ text: string, url: string }` |
| `isFavorite` | `boolean` | `false` | Favorite state (filled heart when true) |
| `bundle` | `string` | `'offer'` | Drupal content type (generates `.ps-card--type-{bundle}`) |
| `view_mode` | `string` | `'slide'` | Drupal view mode (generates `.ps-card--view-mode-{view_mode}`) |
| `attributes` | `Attribute` | — | Additional HTML attributes |

---

## BEM Structure

```
ps-card (base from Card component)
  ps-card__media
    ps-card-offer-slide__image
    ps-card-offer-slide__overlay
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
      ps-card-offer-slide__cta
        ps-card-offer-slide__cta-text
        ps-card-offer-slide__cta-icon
```

---

## Design Tokens

### Overlay Tokens (defined on `:where(.ps-card.ps-card-offer-slide)`)

Used by elements in `media_overlay` block:

```css
--favorite-size: var(--size-8);              /* 32px button */
--favorite-bg: var(--white);                 /* White background */
--favorite-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); /* Subtle shadow */
--favorite-icon-size: var(--size-5);         /* 20px icon */
--favorite-hover-bg: var(--danger-bg-subtle); /* Rose pâle on hover */
--favorite-active-bg: var(--danger);         /* Rose plein when active */
```

### Component Tokens (defined on `.ps-card-offer-slide`)

```css
/* Container */
--ps-card-offer-slide-max-width: 320px;

/* Image */
--ps-card-offer-slide-image-aspect-ratio: 3/2;

/* Overlay */
--ps-card-offer-slide-overlay-inset: var(--size-3); /* 12px from edges */

/* Header (Price • m²) */
--ps-card-offer-slide-header-font-size: var(--font-size-0);      /* 14px */
--ps-card-offer-slide-header-font-weight: var(--font-weight-400); /* Regular */
--ps-card-offer-slide-header-color: var(--text-secondary);        /* Gray */

/* Title */
--ps-card-offer-slide-title-font-size: var(--font-size-2);       /* 18px */
--ps-card-offer-slide-title-font-weight: var(--font-weight-600);  /* Semi-bold */
--ps-card-offer-slide-title-color: var(--text-primary);           /* Dark */

/* Location */
--ps-card-offer-slide-location-font-size: var(--font-size-0);    /* 14px */
--ps-card-offer-slide-location-color: var(--text-secondary);      /* Gray */
--ps-card-offer-slide-location-icon-size: var(--size-4);          /* 16px */

/* CTA */
--ps-card-offer-slide-cta-font-size: var(--font-size-1);         /* 16px */
--ps-card-offer-slide-cta-font-weight: var(--font-weight-600);    /* Semi-bold */
--ps-card-offer-slide-cta-color: var(--primary);                  /* Green */
```

**Why two token blocks?**  
Elements in `media_overlay` are children of `.ps-card__media` (from base Card), not `.ps-card-offer-slide`. Using `:where()` makes overlay tokens accessible via cascade with zero specificity impact.

---

## Accessibility

### WCAG 2.2 AA Compliance

- ✅ **Semantic HTML**: `<h3>` for title, `<a>` for CTA
- ✅ **ARIA labels**: Favorite button has `aria-label` for state ("Ajouter aux favoris" / "Retirer des favoris")
- ✅ **Focus visible**: All interactive elements (button, link) have visible focus indicator (2px outline)
- ✅ **Color contrast**: All text meets minimum 4.5:1 ratio
- ✅ **Icon accessibility**: Icons use `aria-hidden="true"`, text labels provided
- ✅ **Keyboard navigation**: All actions accessible via keyboard (Tab, Enter, Space)
- ✅ **Touch targets**: Minimum 44x44px (favorite button is 32x32px but has 12px padding around it)

### Keyboard Shortcuts

| Key | Action |
|-----|--------|
| `Tab` | Navigate to favorite button / CTA link |
| `Enter` / `Space` | Toggle favorite / Follow CTA link |

---

## Usage Examples

### Basic

```twig
{% include '@components/card-offer-slide/card-offer-slide.twig' with {
  title: 'Office Space PARIS',
  price: '650 €',
  surface: '2 450 m²',
  image: { url: '/images/building.jpg', alt: 'Office in Paris' },
  location: 'Paris - La Défense',
  cta: { text: 'Consulter l\'annonce', url: '/property/123' }
} only %}
```

### With Favorite State

```twig
{% include '@components/card-offer-slide/card-offer-slide.twig' with {
  title: 'Retail Space LYON',
  price: '4 500 €',
  surface: '180 m²',
  image: { url: '/images/retail.jpg', alt: 'Retail space in Lyon' },
  location: 'Lyon - Part-Dieu',
  isFavorite: true
} only %}
```

### Minimal (Title + Image)

```twig
{% include '@components/card-offer-slide/card-offer-slide.twig' with {
  title: 'Warehouse MARSEILLE',
  image: { url: '/images/warehouse.jpg', alt: 'Warehouse' }
} only %}
```

### Property Grid

```twig
<div class="property-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.5rem;">
  {% for property in properties %}
    {% include '@components/card-offer-slide/card-offer-slide.twig' with {
      title: property.title,
      price: property.price,
      surface: property.surface,
      image: { url: property.imageUrl, alt: property.imageAlt },
      location: property.location,
      cta: { text: 'Consulter l\'annonce', url: property.url },
      isFavorite: property.isFavorite
    } only %}
  {% endfor %}
</div>
```

---

## Technical Notes

### Card Component Inheritance

This component uses `{% embed '@components/card/card.twig' %}` to inherit:
- Border, radius, shadow styling
- Responsive padding/gap
- Media + Content structure
- Drupal class generation (`ps-card--type-{bundle}`, `ps-card--view-mode-{view_mode}`)

### Twig Blocks Overridden

- `media`: Property image with 3:2 aspect ratio
- `media_overlay`: Favorite button in top-right corner
- `body`: Header (price • surface), title, location
- `footer`: CTA link with arrow icon

### CSS Pattern: `:where()` for Overlay Tokens

Favorite button is inside `.ps-card__media` (from Card component), not `.ps-card-offer-slide`. To make tokens accessible:

```css
/* ✅ Tokens accessible in .ps-card__media descendants */
:where(.ps-card.ps-card-offer-slide) {
  --favorite-size: var(--size-8);
}

/* ❌ Would NOT work - wrong cascade path */
.ps-card-offer-slide {
  --favorite-size: var(--size-8);
}
```

See `.github/instructions/card-inheritance.instructions.md` for details.

---

## Browser Support

- ✅ Chrome 88+
- ✅ Firefox 87+
- ✅ Safari 14.1+
- ✅ Edge 88+

**CSS Features Used**:
- `aspect-ratio` (3:2 image)
- `:where()` pseudo-class (overlay tokens)
- CSS Nesting (`&__element`)
- Custom properties (design tokens)

---

## Related Components

- **Card** (`@components/card`) - Base container component
- **Image** (`@elements/image`) - Property image atom
- **Button** (`@elements/button`) - Favorite toggle button

---

## Changelog

### v1.0.0 (2025-12-10)

- ✅ Initial implementation
- ✅ Card component inheritance with Drupal integration
- ✅ Favorite toggle button with `:where()` pattern
- ✅ 3:2 aspect ratio image
- ✅ Compact "Price • Surface" header
- ✅ Location with pin icon
- ✅ CTA link with arrow
- ✅ WCAG 2.2 AA compliant
- ✅ Full Storybook documentation
