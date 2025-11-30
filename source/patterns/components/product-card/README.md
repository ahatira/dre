# Product Card

Product Card is a specialized component for real estate listings that extends the generic Card container with product-specific content and styling.

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
    ├── .ps-product-card__header
    │   ├── .ps-product-card__badges
    │   │   └── .ps-product-card__badge (--viewed, --gold)
    │   │       └── .ps-product-card__badge-icon
    │   └── .ps-product-card__actions
    │       └── .ps-product-card__action
    ├── .ps-product-card__body
    │   ├── .ps-product-card__title
    │   ├── .ps-product-card__surface
    │   └── .ps-product-card__meta
    │       └── .ps-product-card__meta-item
    │           ├── .ps-product-card__meta-icon
    │           └── .ps-product-card__meta-text
    └── .ps-product-card__footer
        ├── .ps-product-card__price
        └── CTA link
```

## Design Tokens

### Colors (Figma exact)
- Border: `#EBEDEF` (Grey #6)
- Viewed badge: `#EBEDEF` background, `#434F57` text (Grey #2)
- Gold badge: `#D1AE6E` background, `#FFFFFF` text
- Title/Surface/Price: `#333333` (Grey #1)
- Meta text: `#777E83` (Grey #3)
- Action icons: `#777E83` default, `#A22B66` active (Pink #4)

### Typography
- Title: `16px` regular (--font-size-1, --font-weight-400)
- Surface: `16px` bold (--font-size-1, --font-weight-700)
- Price: `20px` bold (--font-size-3, --font-weight-700)
- Meta: `14px` regular (--font-size-0, --font-weight-400)
- Badges: `14px` regular (--font-size-0, --font-weight-400)

### Spacing
- Content padding: `30px 24px` (inherited from Card medium size)
- Badges gap: `8px` (--size-2)
- Actions gap: `12px`
- Footer gap: `9px`
- Icon sizes: `12px` (badge), `16px` (meta), `24px` (actions)

## Usage

### Basic Product Card

```twig
{% include '@components/product-card/product-card.twig' with {
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
{% include '@components/product-card/product-card.twig' with {
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
{% include '@components/product-card/product-card.twig' with {
  layout: 'horizontal',
  ...
} %}
```

## Composition Architecture

Product Card **composes** the generic Card component using Twig `{% embed %}`:
- Inherits all layout/container styles from `ps-card`
- Adds product-specific content structure via blocks
- Defines product-specific styling with `ps-product-card` prefix

This separation allows:
- ✅ Card reusability for other content types (news, events, testimonials)
- ✅ Product-specific features without bloating the base component
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
