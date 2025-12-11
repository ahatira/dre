# Offer Card

Offer Card is a specialized component for real estate listings that extends the generic Card container with offer-specific content and styling.

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `layout` | `string` | `vertical` | Layout orientation: `vertical`, `horizontal` |
| `title` | `string` | — | Property title (required) |
| `surface` | `string` | — | Surface area (e.g., "611.3 m²") |
| `price` | `string` | — | Price text (e.g., "20 000 € HT/HC/m²/an") |
| `image` | `object` | — | Image data: `{ url: string, alt: string }` |
| `meta` | `array` | — | Metadata items: `[{ icon: string, text: string }]` |
| `status` | `object` | — | Status badges: `{ viewed: boolean, exclusivity: boolean }` |
| `cta` | `object` | — | Call-to-action: `{ text: string, url: string }` |
| `url` | `string` | — | Optional card link URL (wraps entire card) |
| `attributes` | `object` | — | Additional HTML attributes |

## BEM Structure

```
.ps-card (from generic Card)
└── .ps-card__image
└── .ps-card__content
    ├── .ps-offer-card__header
    │   ├── .ps-offer-card__badges
    │   │   └── .ps-offer-card__badge (--viewed, --gold)
    │   │       └── .ps-offer-card__badge-icon
    │   └── .ps-offer-card__actions
    │       └── .ps-offer-card__action
    ├── .ps-offer-card__body
    │   ├── .ps-offer-card__title
    │   ├── .ps-offer-card__surface
    │   └── .ps-offer-card__meta
    │       └── .ps-offer-card__meta-item
    │           ├── .ps-offer-card__meta-icon
    │           └── .ps-offer-card__meta-text
    └── .ps-offer-card__footer
        ├── .ps-offer-card__price
        └── CTA link
```

## Component Variables (Layer 2)

Offer Card uses a 3-layer token system. Override these component-scoped variables for customization:

### Header
- `--ps-offer-card-header-height`: `var(--size-6)` - Header container height
- `--ps-offer-card-badges-gap`: `var(--size-2)` - Gap between badges
- `--ps-offer-card-actions-gap`: `var(--size-3)` - Gap between action buttons

### Badge
- `--ps-offer-card-badge-height`: `var(--size-6)` - Badge height
- `--ps-offer-card-badge-padding-y`: `var(--size-2)` - Vertical padding
- `--ps-offer-card-badge-padding-x`: `var(--size-3)` - Horizontal padding
- `--ps-offer-card-badge-radius`: `0` - Border radius (no rounding by default)
- `--ps-offer-card-badge-viewed-bg`: `var(--gray-100)` - "Viewed" badge background
- `--ps-offer-card-badge-viewed-color`: `var(--gray-700)` - "Viewed" badge text color
- `--ps-offer-card-badge-gold-bg`: `var(--yellow-500)` - "Exclusivity" badge background
- `--ps-offer-card-badge-gold-color`: `var(--white)` - "Exclusivity" badge text color

### Action Buttons
- `--ps-offer-card-action-size`: `var(--size-6)` - Button size (width/height)
- `--ps-offer-card-action-color`: `var(--gray-500)` - Default icon color
- `--ps-offer-card-action-hover-color`: `var(--primary)` - Hover color
- `--ps-offer-card-action-active-color`: `var(--red-600)` - Active state color
- `--ps-offer-card-action-focus-radius`: `0` - Focus outline radius

### Typography
- `--ps-offer-card-title-font-size`: `var(--font-size-1)` - Title (16px)
- `--ps-offer-card-title-font-weight`: `var(--font-weight-400)` - Title weight
- `--ps-offer-card-surface-font-size`: `var(--font-size-1)` - Surface (16px)
- `--ps-offer-card-surface-font-weight`: `var(--font-weight-700)` - Surface weight (bold)
- `--ps-offer-card-price-font-size`: `var(--font-size-3)` - Price (20px)
- `--ps-offer-card-price-font-weight`: `var(--font-weight-700)` - Price weight (bold)
- `--ps-offer-card-meta-font-size`: `var(--font-size-0)` - Meta text (14px)

### Colors
- `--ps-offer-card-title-color`: `var(--text-primary)` - Title text color
- `--ps-offer-card-meta-color`: `var(--gray-600)` - Meta text color
- `--ps-offer-card-price-color`: `var(--text-primary)` - Price text color

## Usage

### Basic Offer Card

```twig
{% include '@components/offer-card/offer-card.twig' with {
  title: 'Rent Offices MADRID',
  surface: '611.3 m²',
  price: '20 000 €',
  image: { url: 'image.jpg', alt: 'Office' },
  meta: [
    { icon: 'pin-map', text: 'Madrid' }
  ],
  cta: {
    text: 'View property',
    url: '#property'
  }
} %}
```

### With Status Badges

```twig
{% include '@components/offer-card/offer-card.twig' with {
  title: 'Office Space',
  status: {
    viewed: true,
    exclusivity: true
  },
  ...
} %}
```

### Horizontal Layout

```twig
{% include '@components/offer-card/offer-card.twig' with {
  layout: 'horizontal',
  ...
} %}
```

## Composition Architecture

Offer Card **composes** the generic Card component using Twig `{% embed %}`:
- Inherits all layout/container styles from `ps-card`
- Adds offer-specific content structure via blocks
- Defines offer-specific styling with `ps-offer-card` prefix

This separation allows:
- ✅ Card reusability for other content types (news, events, testimonials)
- ✅ Offer-specific features without bloating the base component
- ✅ Easy maintenance and testing of both components independently

## Accessibility

- Status badges: Descriptive text for screen readers
- Action buttons: `aria-label` for icon-only buttons
- Meta icons: `aria-hidden="true"` (decorative)
- Image: `alt` text required
- Focus states: Visible outline on all interactive elements
- Keyboard: All actions accessible via Tab/Enter

## Browser Support

- Modern browsers (Chrome, Firefox, Safari, Edge)
- CSS nesting via PostCSS (postcss-nested)
- No JavaScript dependencies

## Related Components

- **Card** (generic container) - Base component
- **Link** (element) - Used for CTA
- **Icon** (element) - Used for badges, meta, actions
