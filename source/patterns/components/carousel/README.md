# Carousel

Responsive carousel for images or cards with Swiper.js v12 integration. Supports navigation, pagination, keyboard navigation, touch gestures, and optional multi-media toolbar with accessibility features.

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| **slides** | array | [] | **Required.** Array of slide objects with `id` and either `image` ({ src, alt }) or `card` (HTML string) |
| variant | string | 'images' | Display variant: `images` (default) or `cards` |
| fit | string | 'cover' | Image object-fit mode: `cover` (default) or `contain` (images variant only) |
| loop | boolean | false | Enable infinite loop navigation |
| autoHeight | boolean | false | Auto-adjust carousel height based on active slide content |
| withPagination | boolean | false | Show pagination bullets below carousel |
| toolbar | object | {} | Multi-media toolbar config with `items` array: [{ type, label, icon, slideIndex }] |
| ariaLabel | string | 'Carousel' | Accessible label for carousel region |
| attributes | string/object | null | Additional HTML attributes for root element |

## BEM Structure

```
.ps-carousel (root, also .swiper)
├── .ps-carousel__track (.swiper-wrapper) - Slide container
├── .ps-carousel__slide (.swiper-slide) - Individual slide
│   ├── .ps-carousel__image - Image element
│   └── .ps-carousel__card - Card wrapper
├── .ps-carousel__controls - Navigation controls container
│   ├── .ps-carousel__button - Navigation button
│   │   └── .ps-carousel__icon - Button icon (decorative)
├── .ps-carousel__pagination (.swiper-pagination) - Pagination bullets
└── .ps-carousel__toolbar [data-carousel-role="tablist"] - Multi-media toolbar
    ├── .ps-carousel__toolbar-item [role="tab"] - Toolbar button
    │   ├── .ps-carousel__toolbar-icon - Toolbar icon (decorative)
    │   └── .ps-carousel__toolbar-label - Toolbar label text
    └── .ps-carousel__toolbar-divider - Visual divider
```

**Modifiers:**
- `.ps-carousel--cards` - Cards variant with external buttons and gradients
- `.ps-carousel--loop` - Infinite loop navigation enabled
- `.ps-carousel--auto-height` - Dynamic height adjustment enabled
- `.ps-carousel--with-toolbar` - Toolbar visible
- `.ps-carousel--fit-contain` - Image fit contain mode
- `.ps-carousel--lightbox` - Fullscreen overlay mode

## Design Tokens (3-Layer System)

Carousel uses a **component-scoped CSS Variables system** (Bootstrap 5 inspired) with 3 layers:

### Layer 1: Root Primitives
Located in `source/props/*.css`, these are foundational design tokens used by components:
- Colors: `--primary`, `--secondary`, `--white`, `--gray-50`, `--gray-400`, `--gray-600`
- Spacing: `--size-1` through `--size-16`
- Typography: `--font-size-0`, `--font-weight-400`
- Shadows: `--shadow-2`, `--shadow-3`, `--shadow-4`
- Borders: `--border-size-1`, `--border-size-2`
- Radius: `--radius-2`, `--radius-3`, `--radius-round`

### Layer 2: Component-Scoped Variables
Defined in `carousel.css` with defaults referencing Layer 1 primitives:

**Navigation & Pagination:**
- `--ps-carousel-button-*` - Button sizing, colors, shadows
- `--ps-carousel-pagination-*` - Bullet styling, positioning
- `--ps-carousel-focus-*` - Focus outline styling

**Toolbar:**
- `--ps-carousel-toolbar-*` - Toolbar layout, colors, spacing
- `--ps-carousel-toolbar-item-*` - Toolbar button styling
- `--ps-carousel-toolbar-icon-*` / `--ps-carousel-toolbar-label-*` - Icon/label styling

**Cards Variant:**
- `--ps-carousel-cards-*` - Cards-specific buttons, gradients, spacing

### Layer 3: Context Overrides
Consumers can override component variables in their own CSS:
```css
.my-custom-carousel {
  --ps-carousel-button-bg: var(--purple-600);
  --ps-carousel-toolbar-item-color-active: var(--orange-500);
}
```

**See `.github/CSS_VARIABLES_SYSTEM.md` for complete migration strategy.**

## Keyboard Navigation & Accessibility

### Toolbar Keyboard Shortcuts
When toolbar is visible, toolbar items support roving tabindex pattern:
- **Arrow Right** / **Right Arrow** - Focus next toolbar item
- **Arrow Left** / **Left Arrow** - Focus previous toolbar item
- **Home** - Focus first toolbar item
- **End** - Focus last toolbar item
- **Enter** / **Space** - Activate focused toolbar item (jump to media group)

### ARIA Attributes
- Toolbar: `role="tablist"`, `aria-label="Media groups"` (customizable)
- Toolbar Items: `role="tab"`, `aria-selected="true|false"`, `tabindex="0|-1"` (roving tabindex)
- Active Slide: `aria-current="true"` (updated on slide change)
- Navigation Buttons: `aria-label="Previous/Next slide"`
- Pagination Bullets: Keyboard navigable, focus-visible outlined

### Focus Management
- Toolbar items use **roving tabindex** pattern (one item has `tabindex="0"`, others `tabindex="-1"`)
- Focus automatically updates when active slide changes
- All interactive elements support visible focus outlines

## Accessibility Features

1. **Keyboard Navigation**: Full keyboard support for carousel, pagination, toolbar
2. **Screen Reader**: ARIA labels, roles, live regions for slide changes
3. **Focus Management**: Visible focus indicators on all interactive elements
4. **Color Contrast**: All text meets WCAG AA standards
5. **Touch/Swipe**: Full swipe gesture support for mobile and touch devices
6. **Responsive**: Works at all screen sizes without loss of functionality

## Usage Examples

### Basic Image Carousel
```twig
{% include '@components/carousel/carousel.twig' with {
  slides: [
    { id: 'slide1', image: { src: '/images/property-1.jpg', alt: 'Modern office building' } },
    { id: 'slide2', image: { src: '/images/property-2.jpg', alt: 'Luxury apartment' } }
  ],
  withPagination: true,
  ariaLabel: 'Property gallery'
} only %}
```

### Cards Carousel with Toolbar
```twig
{% include '@components/carousel/carousel.twig' with {
  slides: [
    { id: 'card1', card: '<div>Property card HTML...</div>' },
    { id: 'card2', card: '<div>Property card HTML...</div>' }
  ],
  variant: 'cards',
  loop: true,
  toolbar: {
    items: [
      { type: 'photos', label: '13 photos', icon: 'picture', slideIndex: 0 },
      { type: 'plans', label: '6 plans', icon: 'last-articles', slideIndex: 13 }
    ]
  }
} only %}
```

### Lightbox Modal
```twig
{% include '@components/carousel/carousel.twig' with {
  slides: images,
  ariaLabel: 'Fullscreen property gallery',
  lightbox: true,
  attributes: 'data-carousel-thumbs="carousel-thumbs-modal"'
} only %}
```

## Variants

### Images (Default)
Single image per slide, navigation buttons overlay. Supports object-fit modes (cover/contain).

### Cards
Multiple cards per row with external buttons and gradient overlays. Responsive: 1 card (mobile) → 4 cards (desktop).

## Dependencies

- **Swiper.js v12**: Core carousel functionality (https://swiperjs.com/)
  - Modules: Navigation, Pagination, Keyboard, A11y, Thumbs
- **CSS Variables System**: Uses root-level and component-scoped variables
- **PostCSS Nesting**: CSS files use native `&` nesting syntax

## Browser Support

- Chrome/Edge: Latest 2 versions
- Firefox: Latest 2 versions
- Safari: Latest 2 versions
- Touch devices: All modern iOS/Android
- `--font-weight-400` - Icon font weight

## Usage

### Basic Images Carousel

```twig
{% include '@components/carousel/carousel.twig' with {
  slides: [
    { id: 'slide1', image: { src: '/images/office-building.jpg', alt: 'Modern office building' } },
    { id: 'slide2', image: { src: '/images/luxury-apartment.jpg', alt: 'Luxury apartment' } },
    { id: 'slide3', image: { src: '/images/commercial-space.jpg', alt: 'Commercial space' } }
  ],
  ariaLabel: 'Property gallery'
} only %}
```

### Cards Carousel with Loop

```twig
{% include '@components/carousel/carousel.twig' with {
  variant: 'cards',
  loop: true,
  slides: [
    { id: 'card1', card: '<div class="property-card">Property 1</div>' },
    { id: 'card2', card: '<div class="property-card">Property 2</div>' },
    { id: 'card3', card: '<div class="property-card">Property 3</div>' }
  ],
  ariaLabel: 'Featured property listings'
} only %}
```

### Multi-Media Gallery with Toolbar

```twig
{% include '@components/carousel/carousel.twig' with {
  slides: [...photos, ...3d_visits, ...plans],
  withPagination: false,
  toolbar: {
    items: [
      { type: 'photos', label: '13 photos', icon: 'picture', slideIndex: 0 },
      { type: '3d-visit', label: '3 visites 3D', icon: 'select-area-map', slideIndex: 13 },
      { type: 'plan', label: '6 plans', icon: 'last-articles', slideIndex: 19 }
    ]
  },
  ariaLabel: 'Property media gallery'
} only %}
```

## Pixel-Perfect Specifications

Based on 4 design mockups:

### Maquette 1: Cards Carousel (4-card horizontal)
- **Images**: 250×188px (aspect-ratio 4:3)
- **Buttons**: 40×40px transparent with shadow-3, 20×20px icons
- **Favorite**: 48×48px white circle, 24px heart icon (#A22B66)
- **Gradients**: 168px width on edges (--size-42)
- **Card Spacing**: 16px between cards (--size-4)
- **Price**: 18px semi-bold, gray-400 (#9AA6B2)
- **Location**: 14px with 📍 icon, gray-600 (#6A7078)
- **CTA Link**: 16px primary green underline

### Maquette 2: Teaser Carousel (single preview)
- **Container**: max-width 400px
- **Image**: 240×240px square
- **Buttons**: 40×40px white circle with shadow-3, center overlay
- **No Pagination**: clean minimal look

### Maquette 3: Offer Carousel (fullscreen with toolbar)
- **Buttons**: 48×48px white circles with shadow-3, 24×24px icons
- **Toolbar**: 44px height, 24px border-radius, gray-50 background (#F9F9FB)
- **Toolbar Items**: 20×20px icons, 16px text, 8px padding
- **Dividers**: 22px height, 1px width, gray-400 (#9AA6B2)
- **Active State**: primary green background, white text, NO underline

### Maquette 4: Offer with Thumbnails (main + miniatures)
- **Thumbs Height**: 120px fixed
- **Thumbs Per View**: 5 visible
- **Thumbs Opacity**: 0.5 default, 0.75 hover, 1.0 active
- **Active Border**: 2px solid primary (#00915A)
- **Thumbs Buttons**: 32×32px, 16×16px icons

## Real-World Use Cases

1. **Property Listing Cards** - Showcase 4 featured properties with favorite buttons, prices, locations (Maquette 1)
2. **Property Preview (Teaser)** - Compact 400px preview carousel for property thumbnails (Maquette 2)
3. **Multi-Media Gallery** - Fullscreen carousel with toolbar navigation for photos/3D/plans (Maquette 3)
4. **Detailed Property View** - Main carousel synchronized with 5 thumbnail previews below (Maquette 4)
5. **Modal/Lightbox Gallery** - Full-screen image viewer with thumbnails navigation

## Accessibility

### Keyboard Navigation
- **Arrow Left/Right** - Navigate between slides
- **Home** - Jump to first slide
- **End** - Jump to last slide
- **Tab** - Focus navigation controls and pagination bullets
- **Enter/Space** - Activate focused button

### ARIA Attributes
- `aria-label` - Describes carousel purpose (via `ariaLabel` prop)
- `aria-roledescription="slide"` - Identifies slides to screen readers
- `aria-label="Slide X of Y"` - Announces slide position
- `role="group"` - Groups slide content semantically
- `aria-hidden="true"` - Hides decorative icons from screen readers

### Focus Management
- Visible focus indicators on all interactive elements (`:focus-visible`)
- Focus outline uses `--primary` color (2px solid)
- Focus offset for better visibility
- Keyboard navigation via Swiper.js Keyboard module

### Best Practices
- Always provide meaningful `ariaLabel` describing carousel content
- Include descriptive `alt` text for all images
- Ensure WCAG AA color contrast (4.5:1 for text, 3:1 for UI components)
- Buttons properly labeled for screen readers
- Slides announce position automatically (e.g., "Slide 1 of 3")

## JavaScript Behavior

**Powered by Swiper.js v12** with Drupal behavior wrapper:

### Features
- **Navigation** - Prev/next buttons with disabled state on boundaries (unless loop enabled)
- **Pagination** - Clickable bullets synchronized with active slide
- **Keyboard** - Arrow keys, Home/End navigation (Swiper Keyboard module)
- **Touch** - Smooth swipe gestures with momentum scrolling
- **Accessibility** - ARIA announcements and live regions (Swiper A11y module)
- **Loop** - Optional infinite scrolling without visual jumps
- **Auto Height** - Adapts height to active slide content

### Implementation
- Drupal behavior: `Drupal.behaviors.psCarousel`
- Uses `once()` for idempotent initialization
- Cleanup on `detach` to destroy instances
- Configuration via CSS modifier classes
- Standalone initialization for Storybook/non-Drupal contexts

### Library Integration
- **Package**: `swiper` v12 (npm)
- **Modules**: Navigation, Pagination, Keyboard, A11y
- **Bundle Size**: ~15KB gzipped (modular imports)
- **Documentation**: [swiperjs.com](https://swiperjs.com/)

**Note**: Swiper.js is imported globally in `.storybook/preview.js` and initialized via Drupal behaviors to ensure proper timing with `Drupal.attachBehaviors()`.

## Variants

### Images (default)
- Single full-width slide display
- Square white buttons (48×48px) with shadow
- Pagination bullets at bottom
- Image `object-fit: cover` by default
- Optional `fit: contain` for letterboxing

### Cards
- Multiple cards visible with partial next/prev preview
- External black buttons (40×40px) no shadow
- White gradient overlays on edges
- No pagination by default
- Loop enabled for infinite scrolling

### Toolbar
- Multi-media navigation (photos, 3D visits, plans, brochures)
- Rounded pill shape (24px border-radius)
- Light gray background (#F9F9FB)
- Jump to specific slide groups
- Active state synchronized with current slide

## Browser Support

- Modern browsers (Chrome, Firefox, Safari, Edge)
- CSS Nesting via PostCSS
- CSS Custom Properties (CSS variables)
- Touch events for mobile devices
- Keyboard event listeners

## Notes

- Images use `loading="lazy"` for performance
- Swiper adds `will-change: transform` for smooth animations
- Transition duration: 300ms with cubic-bezier easing
- Component fully responsive without media queries
- Minimal markup principle: default variant has no modifier classes
