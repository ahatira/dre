# Carousel

Responsive carousel for images or cards with navigation controls, pagination, keyboard support (Arrow keys, Home/End), and touch swipe gestures.

**Built with [Swiper.js v12](https://swiperjs.com/)** - Industry-standard carousel library with 39k+ GitHub stars, native WCAG AA accessibility, and extensive mobile optimization.

## Why Swiper?

Following the PS Theme library selection methodology (`.github/COMPLETE_RULES.md` Section 19):

- **Complexity**: Carousel interactions (touch gestures, loop logic, RTL, lazy loading) are complex and error-prone to implement from scratch
- **Battle-tested**: 39,000+ GitHub stars, actively maintained since 2014, used by millions of websites
- **Accessibility**: Native WCAG AA compliance with ARIA live regions, keyboard navigation, and screen reader announcements
- **Performance**: Modular architecture (~15KB gzipped with Navigation + Pagination + Keyboard + A11y), GPU-accelerated transforms, optimized for mobile
- **Maintenance**: Regular updates, security patches, extensive documentation, large community support
- **Mobile-first**: Designed for touch interfaces with smooth swipe gestures, momentum scrolling, and edge resistance

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `slides` | array | `[]` | **Required.** Array of slide objects containing `id` and either `image` (with `src`, `alt`) or `card` (HTML content) |
| `variant` | string | `'images'` | Display variant: `images` or `cards` |
| `loop` | boolean | `false` | Enable infinite loop navigation |
| `autoHeight` | boolean | `false` | Automatically adjust carousel height based on slide content |
| `ariaLabel` | string | `'Carousel'` | Accessible label for the carousel region |
| `attributes` | object | `null` | Additional HTML attributes to apply to the root element |

## BEM Classes

```
ps-carousel                     // Root container
ps-carousel--images             // Images variant (default)
## BEM Classes

```
ps-carousel                     // Root container (also has .swiper class for Swiper compatibility)
ps-carousel--images             // Images variant (default)
ps-carousel--cards              // Cards variant
ps-carousel--loop               // Loop enabled
ps-carousel--auto-height        // Auto height enabled
ps-carousel__track              // Flex container for slides (also has .swiper-wrapper class)
ps-carousel__slide              // Individual slide item (also has .swiper-slide class)
ps-carousel__image              // Image element within slide
ps-carousel__card               // Card content wrapper
ps-carousel__controls           // Navigation controls container
ps-carousel__button             // Navigation button base
ps-carousel__prev               // Previous button
ps-carousel__next               // Next button
ps-carousel__icon               // Icon within button
ps-carousel__pagination         // Pagination bullets container (also has .swiper-pagination class)
```

**Note**: Swiper adds its own classes (`.swiper`, `.swiper-wrapper`, `.swiper-slide`, `.swiper-pagination-bullet`) for internal functionality. PS Theme classes follow BEM for styling and integration.

## Design Tokens Used

### Colors
- `--ps-color-primary-600` - Active bullet, icon color, focus outline
- `--ps-color-neutral-0` - White (button background, bullet background)

### Spacing
- `--size-2` (8px) - Gap between pagination bullets
- `--size-4` (16px) - Horizontal padding for controls, card content padding
- `--size-6` (24px) - Pagination bottom position

### Sizing
- `--size-3` (12px) - Pagination bullet size
- `--size-12` (48px) - Navigation button size (circular)

### Borders
- `--radius-round` (9999px) - Circular buttons and bullets
- `--border-size-2` (2px) - Focus outline width

### Shadows
- `--shadow-2` - Pagination bullet shadow
- `--shadow-3` - Navigation button shadow, active bullet shadow
- `--shadow-4` - Navigation button hover shadow

### Typography
- `--font-weight-400` - Icon font weight

## Usage Examples

### Basic Images Carousel

```twig
{% include '@components/carousel/carousel.twig' with {
  slides: [
    { 
      id: 'slide1', 
      image: { src: '/images/property-1.jpg', alt: 'Living room' } 
    },
    { 
      id: 'slide2', 
      image: { src: '/images/property-2.jpg', alt: 'Kitchen' } 
    },
    { 
      id: 'slide3', 
      image: { src: '/images/property-3.jpg', alt: 'Bedroom' } 
    },
  ],
  ariaLabel: 'Property gallery',
} %}
```

### Cards Carousel with Loop

```twig
{% include '@components/carousel/carousel.twig' with {
  variant: 'cards',
  loop: true,
  slides: [
    { 
      id: 'card1', 
      card: '<div class="property-card">...</div>' 
    },
    { 
      id: 'card2', 
      card: '<div class="property-card">...</div>' 
    },
    { 
      id: 'card3', 
      card: '<div class="property-card">...</div>' 
    },
  ],
  ariaLabel: 'Featured properties',
} %}
```

### Auto Height for Variable Content

```twig
{% include '@components/carousel/carousel.twig' with {
  autoHeight: true,
  slides: [
    { id: 's1', image: { src: '/img/short.jpg', alt: 'Short image' } },
    { id: 's2', image: { src: '/img/tall.jpg', alt: 'Tall image' } },
  ],
} %}
```

## Real-World Use Cases

1. **Property Gallery** - Display multiple photos of a real estate property with navigation
2. **Featured Properties** - Showcase property cards in a rotating carousel
3. **Testimonials** - Display client testimonials or reviews in card format
4. **Product Showcase** - Highlight multiple products or services
5. **Image Gallery** - Any image-based content that benefits from carousel navigation

## Accessibility

### Keyboard Navigation
- **Arrow Left/Right** - Navigate between slides
- **Home** - Jump to first slide
- **End** - Jump to last slide
- **Tab** - Focus controls and pagination bullets
- **Enter/Space** - Activate focused button

### ARIA Attributes
- `aria-label` - Describes the carousel purpose (provided via `ariaLabel` prop)
- `aria-roledescription="slide"` - Identifies slides to screen readers (managed by Swiper)
- `aria-current="true"` - Marks the active slide (updated on slide change)
- `role="group"` - Groups slide content semantically (Swiper default)
- `aria-hidden="true"` - Hides decorative icons

**Note**: Swiper.js handles most ARIA attributes automatically through its A11y module, including live region announcements, slide labels, and navigation messages.

### Focus Management
- Keyboard navigation fully supported via Swiper's Keyboard module
- Visible focus indicators on all interactive elements (buttons, pagination)
- Focus outline uses `--ps-color-primary-600` for consistency
- `onlyInViewport: true` prevents keyboard control when carousel is off-screen

### Best Practices
- Always provide meaningful `ariaLabel` for context (required prop)
- Include descriptive `alt` text for all images
- Ensure sufficient color contrast (WCAG AA minimum)
- Buttons are properly labeled for screen readers (via Swiper A11y module)
- Slides announce their position automatically (e.g., "Slide 1 of 3")

## JavaScript Behavior

**Powered by [Swiper.js v12](https://swiperjs.com/)** with custom Drupal behavior wrapper:

### Features
- **Navigation** - Prev/next buttons with disabled state on first/last slide (unless loop enabled)
- **Pagination** - Clickable bullets with active state synchronization
- **Keyboard** - Arrow keys, Home/End navigation (Swiper Keyboard module)
- **Touch** - Smooth swipe gestures with momentum scrolling (Swiper native)
- **Accessibility** - ARIA announcements, live regions, keyboard support (Swiper A11y module)
- **Loop** - Optional infinite scrolling without visual jumps
- **Auto Height** - Adapts to tallest slide in view

### Implementation
- Drupal behavior: `Drupal.behaviors.psCarousel`
- Uses `once()` for idempotent initialization (prevents duplicates on AJAX)
- Cleanup on `detach` trigger to destroy Swiper instances
- Standalone init for Storybook/non-Drupal contexts
- Configuration via CSS modifier classes (`.ps-carousel--loop`, `.ps-carousel--auto-height`)

### Library Details
- **Package**: `swiper` v12 (installed via npm)
- **Modules**: Navigation, Pagination, Keyboard, A11y
- **Bundle Size**: ~15KB gzipped (modular imports only)
- **Documentation**: [swiperjs.com/swiper-api](https://swiperjs.com/swiper-api)

See `carousel.js` for the complete implementation.

## Browser Support

- Modern browsers (Chrome, Firefox, Safari, Edge)
- CSS Grid and Flexbox required
- Touch events for mobile devices
- Keyboard event listeners
- CSS Custom Properties (CSS variables)

## Notes

- Images use `loading="lazy"` for performance optimization
- Carousel uses CSS `will-change: transform` for smooth animations
- Transition duration: 300ms with cubic-bezier easing
- Bullets auto-generate based on slides array length
- Component is fully responsive without media queries
