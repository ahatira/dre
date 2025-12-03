# Image

Responsive image with lazy loading, srcset/sizes support, object-fit and border-radius options.

## Overview

The Image component is a fundamental atom for displaying images with built-in responsive behavior, lazy loading, and flexible styling options. It handles the `<img>` element only. For figures with captions, use the Figure component.

**Key Features:**
- Responsive by default (`max-width: 100%; height: auto`)
- Lazy loading enabled by default for performance
- Srcset/sizes support for responsive images
- Object-fit control for image scaling
- Border-radius variants for visual styling
- Accessibility-first with required alt text

## Usage

```twig
{# Basic responsive image #}
{% include '@elements/image/image.twig' with {
  src: '/images/hero.jpg',
  alt: 'Modern commercial property in downtown district',
  width: 800,
  height: 450
} %}

{# Image with object-fit and rounded corners #}
{% include '@elements/image/image.twig' with {
  src: '/images/avatar.jpg',
  alt: 'John Smith, Real Estate Broker',
  width: 200,
  height: 200,
  fit: 'cover',
  rounded: 'full'
} %}

{# Responsive image with srcset/sizes #}
{% include '@elements/image/image.twig' with {
  src: '/images/hero-800.jpg',
  alt: 'Luxury apartment building exterior',
  width: 800,
  height: 450,
  srcset: [
    '/images/hero-400.jpg 400w',
    '/images/hero-800.jpg 800w',
    '/images/hero-1200.jpg 1200w'
  ],
  sizes: '(min-width: 1024px) 960px, 100vw'
} %}
```

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `src` | `string` | *required* | Image source URL |
| `alt` | `string` | *required* | Alternative text for accessibility |
| `width` | `integer` | — | Intrinsic width in pixels (prevents CLS) |
| `height` | `integer` | — | Intrinsic height in pixels (prevents CLS) |
| `srcset` | `array<string>` | — | Responsive image sources (e.g., `['/img-400.jpg 400w']`) |
| `sizes` | `string` | — | Sizes attribute (e.g., `'(min-width: 768px) 50vw, 100vw'`) |
| `loading` | `string` | `'lazy'` | Loading strategy: `'lazy'` \| `'eager'` |
| `decoding` | `string` | `'auto'` | Decoding hint: `'auto'` \| `'async'` \| `'sync'` |
| `fit` | `string` | `'none'` | Object-fit: `'none'` \| `'cover'` \| `'contain'` \| `'fill'` \| `'scale-down'` |
| `rounded` | `string` | `'none'` | Border-radius: `'none'` \| `'sm'` \| `'md'` \| `'lg'` \| `'full'` |
| `baseClass` | `string` | `'ps-image'` | Override root class when composing inside other components (e.g., `'ps-avatar__image'`). Modifiers map to `baseClass--fit-*` and `baseClass--rounded-*`.
| `attributes` | `Attribute` | — | Additional HTML attributes |

## BEM Structure

```
ps-image                           // Base image element

Modifiers:
  ps-image--fit-cover              // Object-fit: cover
  ps-image--fit-contain            // Object-fit: contain
  ps-image--fit-fill               // Object-fit: fill
  ps-image--fit-none               // Object-fit: none
  ps-image--fit-scale-down         // Object-fit: scale-down
  
  ps-image--rounded-sm             // Border-radius: 4px
  ps-image--rounded-md             // Border-radius: 8px
  ps-image--rounded-lg             // Border-radius: 16px
  ps-image--rounded-full           // Border-radius: circular
```

**Minimal Markup:** By default, only `ps-image` class is applied. Modifiers are added only when values differ from defaults (`fit: 'none'`, `rounded: 'none'`).

**Composition with `baseClass`:** When `baseClass` is provided, the component uses it as the root class and maps modifiers accordingly (e.g., `ps-avatar__image--fit-cover`, `ps-avatar__image--rounded-full`). This keeps atomic elements clean inside higher-level components without leaking default atom classes.

## Design Tokens

| Token | Value | Usage |
|-------|-------|-------|
| `--radius-2` | `0.25rem` (4px) | Small border-radius |
| `--radius-4` | `0.5rem` (8px) | Medium border-radius |
| `--radius-6` | `1rem` (16px) | Large border-radius |
| `--radius-round` | `1e5px` | Full circular border-radius |

**Note:** Object-fit values use native CSS properties and don't require custom tokens.

## Variants

### Object-fit

Controls how the image fills its container:

- **`none`** (default): Original dimensions, may overflow
- **`cover`**: Fills container, maintains aspect ratio, may crop
- **`contain`**: Fits within container, maintains aspect ratio, may letterbox
- **`fill`**: Stretches to fill container, may distort
- **`scale-down`**: Scales down like `contain` or `none` (whichever is smaller)

### Border-radius

- **`none`** (default): No border-radius
- **`sm`**: Small rounded corners (4px)
- **`md`**: Medium rounded corners (8px)
- **`lg`**: Large rounded corners (16px)
- **`full`**: Circular/pill shape (for avatars)

## Real-World Use Cases

### Hero Image
```twig
{% include '@elements/image/image.twig' with {
  src: '/images/hero-800.jpg',
  alt: 'Panoramic view of downtown business district',
  width: 1200,
  height: 600,
  srcset: ['/images/hero-400.jpg 400w', '/images/hero-800.jpg 800w', '/images/hero-1200.jpg 1200w'],
  sizes: '100vw',
  loading: 'eager',
  fit: 'cover'
} %}
```

### Avatar
```twig
{% include '@elements/image/image.twig' with {
  src: '/images/users/jane-doe.jpg',
  alt: 'Jane Doe, Senior Property Consultant',
  width: 48,
  height: 48,
  fit: 'cover',
  rounded: 'full'
} %}
```

### Composed inside another component (using `baseClass`)
```twig
{# Inside Avatar component template #}
{% include '@elements/image/image.twig' with {
  src: src,
  alt: alt,
  width: size_px,
  height: size_px,
  fit: 'cover',
  rounded: shape == 'circle' ? 'full' : 'md',
  baseClass: 'ps-avatar__image'
} %}
```

### Card Thumbnail
```twig
{% include '@elements/image/image.twig' with {
  src: '/images/properties/listing-123.jpg',
  alt: 'Contemporary apartment with open floor plan',
  width: 400,
  height: 300,
  fit: 'cover',
  rounded: 'md'
} %}
```

### Decorative Image (no alt needed)
```twig
{% include '@elements/image/image.twig' with {
  src: '/images/patterns/decorative.svg',
  alt: '',
  width: 100,
  height: 100
} %}
```

## Accessibility

- **`alt` is required**: Provide descriptive text for screen readers. For decorative images, use `alt=""` (empty string).
- **Dimensions prevent CLS**: Always provide `width` and `height` to avoid Cumulative Layout Shift.
- **Lazy loading**: Enabled by default for performance. Use `loading: 'eager'` for above-the-fold images (LCP optimization).
- **Keyboard**: Images are not interactive by default. Wrap in `<a>` or `<button>` if clickable.

## Responsive Behavior

- **Responsive by default**: `max-width: 100%; height: auto` ensures images scale with their container.
- **Srcset/Sizes**: Use for art direction and resolution switching. The browser selects the optimal image based on viewport and pixel density.
- **Aspect ratio**: This component doesn't enforce aspect ratios. For fixed ratios, use the Figure component with ratio wrapper.

## Performance

- **Lazy loading**: Images load when near the viewport, reducing initial page weight.
- **Decoding**: `decoding="auto"` lets the browser optimize image decode timing.
- **CLS prevention**: Providing dimensions reserves layout space, preventing content shift.

## Browser Support

- **Object-fit**: Supported in all modern browsers (IE11 requires polyfill).
- **Lazy loading**: Native support in modern browsers; gracefully degrades to eager loading.
- **Srcset/Sizes**: Widely supported; falls back to `src` in older browsers.

## Related Components

- **Figure**: Wrapper component with caption support
- **Avatar**: Specialized image component for user profiles
- **Icon**: For vector graphics and UI icons

## Resources

- [MDN: `<img>` element](https://developer.mozilla.org/en-US/docs/Web/HTML/Element/img)
- [Web.dev: Responsive Images](https://web.dev/learn/design/responsive-images/)
- [Core Web Vitals](https://web.dev/vitals/)
- Design spec: `docs/design/atoms/image.md`

---

**Component Status**: ✅ Complete  
**Last Updated**: December 3, 2025  
**Maintainer**: Design System Team
