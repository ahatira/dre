# Offer Full Layout

Complete page layout for displaying real estate property details in Drupal 11.

## Overview

**Category**: Layouts  
**Domain**: Real Estate  
**Bundle**: `offer`  
**View Mode**: `full`

## Features

- ✅ **Hero gallery** with carousel, favorite button, media toolbar
- ✅ **Two-column responsive layout** (main content + sticky sidebar on desktop)
- ✅ **Mobile-optimized** with bottom bar consultant CTA
- ✅ **Full-width interactive map** with POI filters
- ✅ **Modular composition** via 9 sub-templates
- ✅ **Placeholder mode** for loading states
- ✅ **Semantic BEM architecture** with mobile-first CSS

## Structure

```
offer-full/
├── offer-full.twig           # Main layout template
├── offer-full.css            # Complete styles (mobile-first)
├── offer-full.yml            # Mock data (Madrid office)
├── offer-full.stories.jsx    # Storybook stories
├── README.md                 # This file
└── template/                 # Sub-templates
    ├── offer-hero.twig       # Hero gallery with carousel
    ├── offer-reference.twig  # Reference number
    ├── offer-meta.twig       # Title, price, key metadata
    ├── offer-description.twig # Main description
    ├── offer-features.twig   # Equipments & services
    ├── offer-energy.twig     # DPE, GES, labels
    ├── offer-surface.twig    # Surface breakdown table
    ├── offer-location.twig   # Address & transport
    ├── offer-map.twig        # Interactive map (full-width)
    └── offer-sidebar.twig    # Consultant profile & CTA
```

## Content Sections (Top to Bottom)

1. **Hero** - Image carousel with navigation, favorite button, media toolbar
2. **Reference** - Property reference number
3. **Metadata** - Building name, title, price, surface, availability
4. **Description** - Main property description (expandable)
5. **Features** - Equipments and services lists
6. **Energy** - DPE/GES ratings and environmental labels
7. **Surface Table** - Detailed surface breakdown
8. **Location** - Address and transport information
9. **Map** - Full-width interactive map with POI filters

## Responsive Behavior

### Mobile (< 1024px)
- Single column layout
- Hero carousel optimized for touch
- Sidebar becomes **fixed bottom bar** with compact consultant CTA
- Map filters and travel calculator stacked vertically

### Desktop (≥ 1024px - `@media(--laptop)`)
- Two-column layout (main content + sidebar)
- Sidebar becomes **sticky** (position: sticky, top: 2rem)
- Meta section: Building/Title on left, Price on right
- Features section: Two-column grid
- Map with absolute-positioned filters and travel calculator

## CSS Architecture

**Prefix**: `offer-`  
**Methodology**: BEM  
**Approach**: Mobile-first

### Key Classes

```css
/* Layout */
.offer-layout                 /* Main container */
.offer-layout__main           /* Content column */
.offer-layout__sidebar        /* Sidebar (sticky on desktop, fixed bottom on mobile) */

/* Hero */
.offer-hero                   /* Hero container */
.offer-hero__favorite         /* Favorite button (absolute positioned) */
.offer-hero__carousel         /* Carousel wrapper */
.offer-hero__nav              /* Navigation arrows */
.offer-hero__toolbar          /* Media toolbar (photos, 3D, plan) */

/* Meta */
.offer-meta                   /* Metadata container */
.offer-meta__header           /* Header with title + price */
.offer-meta__title            /* H1 title */
.offer-meta__price            /* Price display */
.offer-meta__details          /* Key details row */

/* Map */
.offer-map                    /* Full-width section */
.offer-map__wrapper           /* Map wrapper */
.offer-map__container         /* Map canvas (for Leaflet/Google Maps) */
.offer-map__filters           /* POI filters (absolute positioned) */
.offer-map__travel-time       /* Travel time calculator */

/* Sidebar */
.offer-sidebar__consultant    /* Consultant profile */
.offer-sidebar__actions       /* CTA buttons */
```

## Drupal Integration

### Preprocess Function

```php
/**
 * Implements hook_preprocess_node().
 */
function ps_preprocess_node__offer__full(&$variables) {
  $node = $variables['node'];
  
  // Building name
  $variables['building_name'] = [
    'label' => t('Building'),
    'value' => $node->get('field_building_name')->value,
  ];
  
  // Reference
  $variables['reference'] = [
    'label' => t('Reference'),
    'value' => $node->get('field_reference')->value,
  ];
  
  // Price
  $variables['price'] = [
    'label' => t('Rent'),
    'value' => $node->get('field_price')->value,
    'currency' => '€',
    'unit' => 'HT/HC/m²/an',
  ];
  
  // Surface
  $variables['surface_total'] = [
    'label' => t('Total surface'),
    'value' => $node->get('field_surface_total')->value,
    'unit' => 'm²',
  ];
  
  // Hero gallery
  $hero_images = [];
  foreach ($node->get('field_images')->referencedEntities() as $media) {
    $hero_images[] = [
      'url' => file_create_url($media->field_media_image->entity->getFileUri()),
      'alt' => $media->field_media_image->alt,
    ];
  }
  
  $variables['hero'] = [
    'images' => $hero_images,
    'photos_count' => count($hero_images),
    'visit_3d' => $node->get('field_3d_visit_url')->uri ? [
      'url' => $node->get('field_3d_visit_url')->uri,
    ] : null,
    'plan' => $node->get('field_plan_file')->entity ? [
      'url' => file_create_url($node->get('field_plan_file')->entity->getFileUri()),
    ] : null,
  ];
  
  // Consultant
  if ($consultant_ref = $node->get('field_consultant')->entity) {
    $variables['consultant'] = [
      'name' => $consultant_ref->get('field_full_name')->value,
      'phone' => $consultant_ref->get('field_phone')->value,
      'photo' => $consultant_ref->get('field_photo')->entity ? [
        'url' => file_create_url($consultant_ref->get('field_photo')->entity->getFileUri()),
        'alt' => $consultant_ref->get('field_full_name')->value,
      ] : null,
    ];
  }
  
  // Location
  $variables['location'] = [
    'address' => [
      'street' => $node->get('field_address_street')->value,
      'postal_code' => $node->get('field_address_postal')->value,
      'city' => $node->get('field_address_city')->value,
    ],
    'coordinates' => [
      'lat' => $node->get('field_geolocation')->lat,
      'lng' => $node->get('field_geolocation')->lng,
    ],
  ];
  
  // Enable map
  $variables['map'] = [
    'enabled' => true,
    'zoom' => 15,
  ];
}
```

### Theme Hook

```php
/**
 * Implements hook_theme().
 */
function ps_theme() {
  return [
    'node__offer__full' => [
      'template' => 'node--offer--full',
      'base hook' => 'node',
    ],
  ];
}
```

### Libraries

```yaml
# ps.libraries.yml
offer-full:
  css:
    component:
      source/patterns/layouts/offer-full/offer-full.css: {}
  js:
    source/js/carousel.js: {}
    source/js/map.js: {}
  dependencies:
    - ps/button
    - ps/skeleton
```

## Storybook Usage

```bash
npm run storybook
```

Navigate to: **Layouts → Offer Full**

### Available Stories

1. **Default** - Complete Madrid office data
2. **Placeholder** - Skeleton loading state
3. **Minimal** - Required fields only
4. **Without Energy** - No DPE/GES data
5. **Without Surface Table** - No surface breakdown
6. **Consultant No Photo** - Placeholder avatar
7. **Mobile** - Mobile viewport
8. **Tablet** - Tablet viewport

## Accessibility

- ✅ Semantic HTML5 landmarks (`<article>`, `<section>`, `<aside>`)
- ✅ Proper heading hierarchy (H1 → H2 → H3)
- ✅ `aria-label` on sidebar and map
- ✅ `aria-pressed` on favorite button
- ✅ Keyboard navigation support for carousel
- ✅ Focus-visible states on all interactive elements
- ✅ Skip links support via main content wrapper

## JavaScript Enhancements

### Carousel (Progressive Enhancement)

```javascript
// source/js/carousel.js
document.querySelectorAll('[data-carousel="offer-hero"]').forEach(carousel => {
  // Initialize carousel functionality
  // Handle navigation arrows, slide counter, touch gestures
});
```

### Map (Progressive Enhancement)

```javascript
// source/js/map.js
if (document.getElementById('offer-map')) {
  // Initialize Leaflet or Google Maps
  // Handle POI filters, travel time calculator, layer toggle
}
```

## Dependencies

- **Atoms**: Button, Skeleton, Icon
- **Molecules**: None (self-contained)
- **External**: Leaflet or Google Maps API (for interactive map)

## Testing Checklist

- [ ] Desktop layout (≥ 1024px): Two columns, sticky sidebar
- [ ] Mobile layout (< 1024px): Single column, bottom bar
- [ ] Hero carousel navigation works
- [ ] Favorite button toggles aria-pressed
- [ ] Map initializes with correct coordinates
- [ ] POI filters toggle map markers
- [ ] Travel time calculator accepts input
- [ ] Sidebar CTA buttons trigger modals/actions
- [ ] Placeholder mode renders skeletons correctly
- [ ] All sections gracefully handle missing data

## Browser Support

- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+

## Notes

- Map requires external library (Leaflet or Google Maps API)
- Carousel supports touch gestures on mobile
- Energy badges use semantic color coding (A=green, G=red)
- Surface table is horizontally scrollable on mobile
- Consultant photo falls back to placeholder avatar icon

## Author

Design System Team - BNP Paribas Real Estate

## License

Proprietary - Internal use only
