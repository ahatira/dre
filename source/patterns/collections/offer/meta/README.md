# Offer Meta

Main metadata display component for real estate offers.

## Overview

The Offer Meta organism displays essential information about a property listing, including reference number, title, price, surface area, location, availability, mandate type, and action buttons.

## Usage

```twig
{% include '@collections/offer/meta/offer-meta.twig' with {
  title: 'Rent Offices MADRID Barrio de Chamberí',
  reference: {
    label: 'Reference',
    value: 'OLBUR2200801'
  },
  building: {
    label: 'Building',
    value: 'Edificio ARA'
  },
  price: {
    label: 'Rent',
    value: '20 000 €',
    unit: 'HT/HC/m²/an',
    tooltip: 'Price excluding charges and tax'
  },
  surface_total: {
    label: 'Surface total',
    value: 611.3,
    unit: 'm²'
  },
  location: {
    address: {
      postal_code: '28010',
      locality: 'MADRID'
    }
  },
  availability: {
    label: 'Available',
    value: 'Immediately'
  },
  mandate_type: {
    label: 'Type of mandate',
    value: 'Exclusive'
  },
  actions: {
    items: [
      {
        label: 'Access to the surface area table',
        url: '#surface-table',
        variant: 'primary',
        outline: true,
        icon: 'arrow-down',
        icon_position: 'right'
      },
      {
        label: 'Download the brochure',
        url: '#download',
        variant: 'primary'
      }
    ]
  }
} only %}
```

## Layout Behavior

### Mobile (< 768px)
- **Stacked layout**: All sections stack vertically
- **Full-width actions**: Buttons take full width
- **Price below title**: Price appears under the title/building name

### Desktop (≥ 768px)
- **Grid layout**: Title and price aligned horizontally (price to the right)
- **Horizontal metadata**: Surface/location and availability/mandate on same line
- **Inline actions**: Buttons displayed side by side

### Large Desktop (≥ 1024px)
- **Larger typography**: Building name and title increase to `--font-size-8`
- **Prominent price**: Price value increases to `--font-size-9`

## Component Structure

```
.ps-offer-meta
├── .ps-offer-meta__header
│   ├── .ps-offer-meta__reference
│   └── .ps-offer-meta__title-container
│       ├── .ps-offer-meta__title-group
│       │   ├── .ps-offer-meta__building
│       │   └── .ps-offer-meta__title
│       └── .ps-offer-meta__price
│           ├── .ps-offer-meta__price-value
│           ├── .ps-offer-meta__price-unit
│           └── .ps-offer-meta__price-tooltip
├── .ps-offer-meta__metadata
│   ├── .ps-offer-meta__primary-info
│   │   ├── .ps-offer-meta__surface
│   │   └── .ps-offer-meta__location
│   └── .ps-offer-meta__secondary-info
│       ├── .ps-offer-meta__availability
│       └── .ps-offer-meta__mandate
└── .ps-offer-meta__actions
    └── .ps-button (composed via include)
```

## Data Structure

### Required Fields
- `title` - Main offer title
- `actions.items` - Array of action buttons

### Optional Fields
- `reference.label` / `reference.value` - Reference number
- `building.label` / `building.value` - Building name
- `price.label` / `price.value` / `price.unit` / `price.tooltip` - Pricing information
- `surface_total.label` / `surface_total.value` / `surface_total.unit` - Surface area
- `location.address.postal_code` / `location.address.locality` - Location data
- `availability.label` / `availability.value` - Availability status
- `mandate_type.label` / `mandate_type.value` - Mandate type

## Design Tokens Used

### Spacing
- `--size-2`, `--size-3`, `--size-4`, `--size-6`, `--size-8` - Gaps and padding

### Typography
- `--font-size-2` to `--font-size-9` - Font sizes (responsive)
- `--font-weight-400`, `--font-weight-600`, `--font-weight-700` - Font weights
- `--line-height-1`, `--line-height-2`, `--line-height-3` - Line heights

### Colors
- `--gray-900` - Primary text color
- `--text-secondary` - Secondary text color
- `--border-default`, `--border-focus` - Border colors
- `--gray-100`, `--gray-400` - Background/border hover states

### Transitions
- `--duration-150` - Transition duration
- `--ease-out` - Easing function

## Accessibility

- Semantic HTML structure with `<header>`, `<h1>`, `<h2>` tags
- Price tooltip button includes `aria-label` for screen readers
- Focus-visible states on interactive elements (tooltip button)
- Proper color contrast ratios

## Related Components

- [Button](../../elements/button/) - Used for action buttons
- Offer Hero - Often used together as page header
- Offer Features - Complementary offer details section

## References

- Design spec: Based on BNPPRE offer detail page mockups (Madrid office example)
- Storybook: Collections/Offer/Meta
