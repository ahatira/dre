# Carousel (Molecule)


**Atomic Design Level**: Molecule / Media Gallery  
**Category**: Content slider  
**Status**: Stable  
**Version**: 1.0.0

---

## 📋 Description

Responsive carousel for images or cards, built with Swiper.js v12. Includes previous/next buttons, pagination (bullets), touch swipe, keyboard navigation (ArrowLeft/Right, Home/End), and full accessibility (ARIA, focus management). Supports 4 main use cases:

1. **Cards Carousel**: 4 property cards with favorite button, external navigation, gradients
2. **Teaser Carousel**: Single image preview with overlay navigation (40×40px)
3. **Offer Carousel**: Fullscreen gallery with toolbar (photos, 3D, plans) instead of pagination
4. **Offer with Thumbs**: Main carousel + synchronized thumbnails below (5 visible, 120px height)

---

## 🏗️ Structure BEM


```html
<section class="swiper ps-carousel ps-carousel--images" aria-label="Property gallery" data-carousel>
  <div class="swiper-wrapper ps-carousel__track">
    <div class="swiper-slide ps-carousel__slide" role="group" aria-roledescription="slide" aria-label="Slide 1 of 3">
      <img class="ps-carousel__image" src="/media/1.jpg" alt="Living room" loading="lazy" />
    </div>
    <div class="swiper-slide ps-carousel__slide" role="group" aria-roledescription="slide" aria-label="Slide 2 of 3">
      <img class="ps-carousel__image" src="/media/2.jpg" alt="Kitchen" loading="lazy" />
    </div>
    <div class="swiper-slide ps-carousel__slide" role="group" aria-roledescription="slide" aria-label="Slide 3 of 3">
      <img class="ps-carousel__image" src="/media/3.jpg" alt="Bedroom" loading="lazy" />
    </div>
  </div>
  <div class="ps-carousel__controls">
    <button class="ps-carousel__button ps-carousel__prev" type="button" aria-label="Previous slide" data-carousel-prev>
      <span class="ps-carousel__icon" data-icon="chevron-left" aria-hidden="true"></span>
    </button>
    <button class="ps-carousel__button ps-carousel__next" type="button" aria-label="Next slide" data-carousel-next>
      <span class="ps-carousel__icon" data-icon="chevron-right" aria-hidden="true"></span>
    </button>
  </div>
  <div class="swiper-pagination ps-carousel__pagination"></div>
</section>
```

### Classes BEM


```
ps-carousel                     // Root container (also has .swiper class)
ps-carousel--images             // Images variant (default)
ps-carousel--cards              // Cards variant
ps-carousel--loop               // Loop enabled
ps-carousel--auto-height        // Auto height enabled
ps-carousel--with-toolbar       // Toolbar enabled
ps-carousel--fit-contain        // Image object-fit: contain
ps-carousel__track              // Flex container for slides (swiper-wrapper)
ps-carousel__slide              // Individual slide (swiper-slide)
ps-carousel__image              // Image element
ps-carousel__card               // Card content wrapper
ps-carousel__controls           // Navigation controls
ps-carousel__button             // Navigation button
ps-carousel__prev               // Previous button
ps-carousel__next               // Next button
ps-carousel__icon               // Icon inside button
ps-carousel__pagination         // Pagination bullets (swiper-pagination)
ps-carousel__toolbar            // Media group toolbar (optional)
ps-carousel__toolbar-item       // Toolbar button
ps-carousel__toolbar-icon       // Toolbar icon
ps-carousel__toolbar-label      // Toolbar label
ps-carousel__toolbar-divider    // Toolbar divider
```

---

## 📐 Props (Component API)

group: molecules
description: 'Carousel responsive pour images ou cartes.'

| Prop            | Type    | Default     | Description |
|-----------------|---------|-------------|-------------|
| `slides`        | array   | `[]`        | **Required.** Array of slide objects: `{ id, type, image?, card? }` |
| `variant`       | string  | `'images'`  | Display variant: `images` or `cards` |
| `fit`           | string  | `'cover'`   | Image object-fit: `cover` or `contain` (images only) |
| `loop`          | boolean | `false`     | Enable infinite loop navigation |
| `autoHeight`    | boolean | `false`     | Auto adjust height based on slide content |
| `withPagination`| boolean | `true`      | Show pagination bullets |
| `toolbar`       | object  | `{}`        | Toolbar config for media navigation (optional) |
| `ariaLabel`     | string  | `'Carousel'`| Accessible label for carousel |
| `attributes`    | object  | `null`      | Additional HTML attributes |

---

## 🎭 Variants & Pixel Perfect Specs

### 1. Cards Carousel (Property Listings)
**Visual Reference**: Maquette 1 - 4 cards horizontales

**Dimensions**:
- Card image: ~250×188px (ratio 4:3)
- Card spacing: 16px between cards
- Navigation buttons: 40×40px (external, outside gradients)
- Favorite button: 48×48px circle, white background, positioned top-right of image (8px margins)
- Gradient overlays: 168px width each side (left/right), white to transparent

**Layout**:
- 4 cards visible on desktop (slidesPerView: 4)
- External navigation (buttons outside card area)
- No pagination bullets

**Card Content Structure**:
- Image (4:3 ratio) with favorite heart icon overlay
- Price + m² (color: --gray-400, size: 18px, weight: 600)
- Product title (color: --gray-800, size: 16px, weight: 600)
- Location (icon: pin-location, color: --gray-600, size: 14px)
- CTA link (color: --primary, underline, "View the property →")

**Buttons**:
- Size: 40×40px
- Background: transparent (no white background)
- Icon color: --gray-600 (default), --secondary (hover)
- No shadow
- Border-radius: 4px

### 2. Teaser Carousel (Property Preview)
**Visual Reference**: Maquette 2 - Single image

**Dimensions**:
- Container: max-width 400px
- Image: square ~240×240px (1:1 ratio)
- Navigation buttons: 40×40px white squares
- Button shadow: --shadow-3 (visible drop shadow)

**Buttons**:
- Size: 40×40px
- Background: white
- Icon: chevron-left/right, --primary color
- Shadow: --shadow-3
- Positioned overlay on image (centered vertically, 24px from edges)

### 3. Offer Carousel (Fullscreen + Toolbar)
**Visual Reference**: Maquette 3 - Toolbar navigation

**Dimensions**:
- Image: full width, responsive height
- Navigation buttons: 48×48px white squares (not rounded)
- Button shadow: --shadow-4 (strong)
- Toolbar: auto-width, centered horizontally
- Toolbar height: 44px (padding 8px 20px)
- Toolbar border-radius: 24px (pill shape)
- Toolbar bottom position: 20px from bottom

**Toolbar**:
- Background: --gray-50 (#F9F9FB)
- Items spacing: 8px gap
- Divider: 1px width, 22px height, --gray-400
- Icon size: 20×20px
- Label: 16px, --primary color, underline, BNPP Sans
- Active state: --secondary color (no underline)

**Toolbar Items**:
- Format: [icon] [label]
- Examples: "13 photos", "3D visit", "Plan"
- Icons: camera (photos), select-area-map (3D), last-articles (plans)

**Navigation Buttons**:
- Size: 48×48px
- Background: white
- Icon color: --primary
- Shadow: --shadow-4
- No border-radius (square)

### 4. Offer with Thumbnails
**Visual Reference**: Maquette 4 - Main + Thumbs

**Main Carousel**: Same as Offer Carousel (maquette 3)

**Thumbnails**:
- Height: 120px fixed
- Spacing: 8px between thumbs
- Visible count: 5 thumbnails (slidesPerView: 5)
- Inactive opacity: 0.5
- Hover opacity: 0.75
- Active: opacity 1.0 + border 2px --primary + outline-offset 2px

**Thumb Buttons**:
- Size: 32×32px
- Background: white
- Icon size: 16px
- Shadow: --shadow-2

**Synchronization**:
- Main carousel linked to thumbs via `data-carousel-thumbs="id"`
- Thumbs carousel: `data-carousel-role="thumbs" id="id"`
- Click thumb = jump to corresponding slide in main

---

## 🎨 Design Tokens (Pixel Perfect)

### Colors
- `--primary` (#00915A) - Navigation icons, CTA links, active states, focus
- `--secondary` (#A22B66) - Hover states, toolbar active
- `--white` (#FFFFFF) - Button backgrounds, pagination bullets, favorite button
- `--gray-50` (#F9F9FB) - Toolbar background
- `--gray-400` (#9AA6B2) - Dividers, secondary text
- `--gray-600` (#6A7078) - Card navigation icons, location text
- `--gray-800` (#333333) - Card titles

### Spacing & Sizing (Exact Pixels)
- `--size-2` (8px) - Toolbar gap, thumb spacing, button margins
- `--size-3` (12px) - Pagination bullet size
- `--size-4` (16px) - Card spacing, thumb icon size
- `--size-5` (20px) - Toolbar icons, toolbar bottom position
- `--size-6` (24px) - Button edge distance, toolbar border-radius
- `--size-8` (32px) - Thumb button size
- `--size-10` (40px) - Card/teaser button size
- `--size-12` (48px) - Offer button size, favorite button size
- `--size-16` (64px) - Pagination position with toolbar
- `--size-30` (120px) - Thumbnail height
- `--size-42` (168px) - Gradient overlay width

### Borders & Radius
- `--radius-round` (9999px) - Pagination bullets, favorite button
- `--radius-1` (2px) - Thumb active border width
- `--radius-2` (4px) - Card button radius
- `--radius-3` (12px) - Toolbar item radius
- `--radius-6` (24px) - Toolbar container radius
- `--border-size-1` (1px) - Toolbar divider
- `--border-size-2` (2px) - Focus outline, thumb active border

### Shadows (Exact Specs)
- `--shadow-2` (0 2px 4px rgba(0,0,0,0.1)) - Pagination bullets, thumb buttons
- `--shadow-3` (0 4px 8px rgba(0,0,0,0.15)) - Teaser buttons, active bullets
- `--shadow-4` (0 8px 16px rgba(0,0,0,0.2)) - Offer navigation buttons (strong)

### Typography
- Font family: 'BNPP Sans', sans-serif
- `--font-size-0` (16px) - Toolbar labels, card titles
- `--font-size--1` (14px) - Card location
- `--font-size-1` (18px) - Card price
- `--font-weight-400` (400) - Regular text
- `--font-weight-600` (600) - Bold titles, price

### Transitions
- Duration: 150ms
- Easing: cubic-bezier(0.4, 0, 0.2, 1)
- Properties: opacity, transform, color, box-shadow

---

## 🔧 Template Twig


```twig
{#
 * Carousel component (Drupal-ready)
 * Multi-media carousel with grouped navigation (photos, 3D visits, plans, brochures, etc.)
 * @param array slides - Array of slide objects with id, type, and content (required)
 *   Each slide: { id, type, image?, card? }
 * @param string variant - images|cards (default: images)
 * @param string fit - cover|contain (default: cover)
 * @param boolean loop - Enable looping (default: false)
 * @param boolean autoHeight - Auto adjust height (default: false)
 * @param boolean withPagination - Show pagination bullets (default: true)
 * @param object toolbar - Toolbar config (optional)
 * @param string ariaLabel - Accessible label (default: Carousel)
 * @param object attributes - Additional HTML attributes
#}

{%- set variant = variant|default('images') -%}
{%- set fit = fit|default('cover') -%}
{%- set loop = loop|default(false) -%}
{%- set autoHeight = autoHeight|default(false) -%}
{%- set withPagination = withPagination is defined ? withPagination : true -%}
{%- set toolbar = toolbar|default({}) -%}
{%- set ariaLabel = ariaLabel|default('Carousel') -%}
{%- set slides = slides|default([]) -%}

{%- set classes = [] -%}
{%- set classes = classes|merge(['ps-carousel']) -%}
{%- if variant != 'images' -%}{%- set classes = classes|merge(['ps-carousel--' ~ variant]) -%}{%- endif -%}
{%- if loop -%}{%- set classes = classes|merge(['ps-carousel--loop']) -%}{%- endif -%}
{%- if autoHeight -%}{%- set classes = classes|merge(['ps-carousel--auto-height']) -%}{%- endif -%}
{%- if toolbar.items|default([])|length > 0 -%}{%- set classes = classes|merge(['ps-carousel--with-toolbar']) -%}{%- endif -%}
{%- if variant == 'images' and fit == 'contain' -%}{%- set classes = classes|merge(['ps-carousel--fit-contain']) -%}{%- endif -%}

<section
  class="swiper {{ classes|join(' ')|trim }}"
  aria-label="{{ ariaLabel }}"
  data-carousel
  {%- if attributes %} {{ attributes }}{% endif -%}
>
  <div class="swiper-wrapper ps-carousel__track">
    {%- for slide in slides -%}
      <div
        class="swiper-slide ps-carousel__slide"
        role="group"
        aria-roledescription="slide"
        aria-label="Slide {{ loop.index }} of {{ slides|length }}"
      >
        {%- if variant == 'images' and slide.image -%}
          <img
            class="ps-carousel__image"
            src="{{ slide.image.src }}"
            alt="{{ slide.image.alt }}"
            loading="lazy"
          />
        {%- elseif variant == 'cards' and slide.card -%}
          <div class="ps-carousel__card">
            {{ slide.card }}
          </div>
        {%- endif -%}
      </div>
    {%- endfor -%}
  </div>

  <div class="ps-carousel__controls">
    <button
      class="ps-carousel__button ps-carousel__prev"
      type="button"
      aria-label="Previous slide"
      data-carousel-prev
    >
      <span class="ps-carousel__icon" data-icon="chevron-left" aria-hidden="true"></span>
    </button>
    <button
      class="ps-carousel__button ps-carousel__next"
      type="button"
      aria-label="Next slide"
      data-carousel-next
    >
      <span class="ps-carousel__icon" data-icon="chevron-right" aria-hidden="true"></span>
    </button>
  </div>

  {%- if withPagination -%}
  <div class="swiper-pagination ps-carousel__pagination"></div>
  {%- endif -%}

  {%- if toolbar.items|default([])|length > 0 -%}
  <div class="ps-carousel__toolbar">
    {%- for item in toolbar.items -%}
      <button
        class="ps-carousel__toolbar-item"
        type="button"
        data-toolbar-item
        data-slide-index="{{ item.slideIndex }}"
        data-media-type="{{ item.type }}"
        aria-label="Go to {{ item.label }}"
      >
        <span class="ps-carousel__toolbar-icon" data-icon="{{ item.icon }}" aria-hidden="true"></span>
        <span class="ps-carousel__toolbar-label">{{ item.label }}</span>
      </button>
      {%- if not loop.last -%}
        <span class="ps-carousel__toolbar-divider" aria-hidden="true"></span>
      {%- endif -%}
    {%- endfor -%}
  </div>
  {%- endif -%}
</section>
```

---

## 🎨 Styles SCSS


Voir `carousel.css` pour la totalité des styles BEM, tokens et variantes. Les styles sont organisés par :
- Base (`.ps-carousel`, `.ps-carousel__track`, `.ps-carousel__slide`, `.ps-carousel__image`)
- Modifiers (`--cards`, `--loop`, `--auto-height`, `--with-toolbar`, `--fit-contain`)
- Toolbar (`.ps-carousel__toolbar`, `.ps-carousel__toolbar-item`, ...)
- Pagination (`.ps-carousel__pagination`)
Tous les espacements, couleurs, tailles, transitions et ombres utilisent exclusivement les tokens du design system.

---

## ♿ Accessibilité


- `aria-roledescription="slide"` sur chaque slide
- `aria-label` sur le container pour décrire le contenu
- Navigation clavier : flèches, Home/End, Tab focus sur boutons et pagination
- Icônes décoratives avec `aria-hidden="true"`
- Focus visible sur tous les éléments interactifs
- Pagination et toolbar accessibles

---

## 🔌 JavaScript behavior (optionnel)


Le comportement JavaScript est assuré par Swiper.js v12 et un wrapper ES6 (`carousel.js`).
- Initialisation automatique via Drupal behaviors ou Storybook
- Navigation, pagination, clavier, touch, a11y gérés par Swiper modules
- Toolbar multi-média synchronisée avec les groupes de slides
- Voir le fichier `carousel.js` pour l’implémentation complète

---

## 🧪 Exemples d'usage


```twig
{% include '@components/carousel/carousel.twig' with {
  slides: [
    { id: 'slide1', image: { src: '/media/1.jpg', alt: 'Living room' } },
    { id: 'slide2', image: { src: '/media/2.jpg', alt: 'Kitchen' } },
    { id: 'slide3', image: { src: '/media/3.jpg', alt: 'Bedroom' } }
  ],
  ariaLabel: 'Property gallery',
  withPagination: true
} %}
```

```twig
{% include '@components/carousel/carousel.twig' with {
  variant: 'cards',
  loop: true,
  slides: [
    { id: 'card1', card: '<div class="property-card">...</div>' },
    { id: 'card2', card: '<div class="property-card">...</div>' },
    { id: 'card3', card: '<div class="property-card">...</div>' }
  ],
  ariaLabel: 'Featured properties',
  withPagination: false
} %}
```

```twig
{% include '@components/carousel/carousel.twig' with {
  slides: [...],
  toolbar: {
    items: [
      { type: 'photos', label: '13 photos', icon: 'camera', slideIndex: 0 },
      { type: '3d-visit', label: '3 visites 3D', icon: 'cube-focus', slideIndex: 13 },
      { type: 'plan', label: '6 plans', icon: 'cards', slideIndex: 16 }
    ]
  },
  ariaLabel: 'Property media gallery'
} %}
```

---

## 📚 Ressources


- [Swiper.js documentation](https://swiperjs.com/)
- WAI-ARIA carousel patterns
- Touch events and keyboard navigation
