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

## Design Tokens

Note: This component currently mirrors Figma exact values for badges, text, and actions. A follow-up task will align all hardcoded values with PS tokens per `.github/COMPLETE_RULES.md`.

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
