# Image Component

**Type:** Element / Atom  
**Category:** Media  
**Status:** ✅ Complete  
**Version:** 1.0.0

## Description

Responsive image component with lazy loading, srcset/sizes support, optional aspect ratios, and border radius variants. Designed for optimal performance (Core Web Vitals: CLS/LCP) with built-in accessibility features.

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `src` | `string` | `''` | **Required.** Image source URL |
| `alt` | `string` | `''` | **Required.** Alternative text for accessibility |
| `width` | `int` | - | Intrinsic width in pixels (prevents CLS) |
| `height` | `int` | - | Intrinsic height in pixels (prevents CLS) |
| `srcset` | `array<string>` | - | Array of srcset strings (e.g., `['/img-400.jpg 400w', '/img-800.jpg 800w']`) |
| `sizes` | `string` | - | Sizes attribute (e.g., `'(min-width: 768px) 50vw, 100vw'`) |
| `loading` | `string` | `'lazy'` | Loading strategy: `'lazy'` \| `'eager'` |
| `decoding` | `string` | `'auto'` | Decoding strategy: `'auto'` \| `'async'` \| `'sync'` |
| `fit` | `string` | `'cover'` | Object-fit value: `'cover'` \| `'contain'` |
| `rounded` | `string` | `'none'` | Border radius: `'none'` \| `'sm'` \| `'md'` \| `'lg'` \| `'full'` |
| `ratio` | `string` | `'none'` | Aspect ratio: `'none'` \| `'16x9'` \| `'1x1'` \| `'4x3'` |
| `attributes` | `object` | - | Additional HTML attributes |

## BEM Structure

```text
ps-image                 // Block (figure wrapper)
  ps-image__img          // Image element
  ps-image__ratio        // Ratio helper element (only if ratio != 'none')

Modifiers:
  ps-image--fit-contain  // Object-fit: contain
  ps-image--rounded-sm   // Small border radius (4px)
  ps-image--rounded-md   // Medium border radius (6px)
  ps-image--rounded-lg   // Large border radius (12px)
  ps-image--rounded-full // Full circle (9999px)
  ps-image--ratio-16x9   // 16:9 aspect ratio
  ps-image--ratio-1x1    // 1:1 aspect ratio (square)
  ps-image--ratio-4x3    // 4:3 aspect ratio
```

**Note:** Only modifier classes are added when values differ from defaults. The base class `.ps-image` contains all default styles.

## Design Tokens Used

### Colors

- `--ps-color-neutral-100` (fallback: `--gray-50`) - Placeholder background

### Border Radius Tokens

- `--radius-2` (4px) - Small rounded
- `--radius-3` (6px) - Medium rounded
- `--radius-5` (12px) - Large rounded
- `--radius-round` (9999px) - Full circle

## Usage Examples

### Basic Responsive Image

```twig
{% include '@atoms/image/image.twig' with {
  src: '/images/hero.jpg',
  alt: 'Hero banner image',
  width: 1200,
  height: 675,
} %}
```

### Image with Srcset for Responsive Loading

```twig
{% include '@atoms/image/image.twig' with {
  src: '/images/hero-800.jpg',
  alt: 'Responsive hero image',
  width: 800,
  height: 450,
  srcset: [
    '/images/hero-400.jpg 400w',
    '/images/hero-800.jpg 800w',
    '/images/hero-1200.jpg 1200w',
    '/images/hero-1600.jpg 1600w',
  ],
  sizes: '(min-width: 1024px) 960px, 100vw',
  rounded: 'md',
} %}
```

### Fixed Aspect Ratio (16:9)

```twig
{% include '@atoms/image/image.twig' with {
  src: '/images/video-thumbnail.jpg',
  alt: 'Video thumbnail',
  width: 1600,
  height: 900,
  ratio: '16x9',
  rounded: 'md',
} %}
```

### Avatar (Circular)

```twig
{% include '@atoms/image/image.twig' with {
  src: '/images/avatar.jpg',
  alt: 'User avatar',
  width: 200,
  height: 200,
  ratio: '1x1',
  rounded: 'full',
} %}
```

### Object-fit Contain

```twig
{% include '@atoms/image/image.twig' with {
  src: '/images/portrait.jpg',
  alt: 'Portrait photo',
  width: 400,
  height: 600,
  fit: 'contain',
  ratio: '16x9',
} %}
```

## Real-World Use Cases

### 1. Hero Banner

Full-width hero image with 16:9 ratio and medium rounded corners:

```twig
{% include '@atoms/image/image.twig' with {
  src: '/images/hero-banner.jpg',
  alt: 'Modern office building facade',
  width: 1600,
  height: 900,
  ratio: '16x9',
  rounded: 'md',
  srcset: [
    '/images/hero-banner-800.jpg 800w',
    '/images/hero-banner-1200.jpg 1200w',
    '/images/hero-banner-1600.jpg 1600w',
  ],
  sizes: '100vw',
} %}
```

### 2. Card Thumbnail

Card images with consistent 4:3 ratio:

```twig
{% include '@atoms/image/image.twig' with {
  src: '/images/property-card.jpg',
  alt: 'Luxury apartment interior',
  width: 800,
  height: 600,
  ratio: '4x3',
  rounded: 'md',
} %}
```

### 3. User Profile Avatar

Circular avatar with 1:1 ratio:

```twig
{% include '@atoms/image/image.twig' with {
  src: '/images/user-avatar.jpg',
  alt: 'Jean Dupont profile picture',
  width: 200,
  height: 200,
  ratio: '1x1',
  rounded: 'full',
  loading: 'eager', {# Above the fold #}
} %}
```

### 4. Gallery Thumbnail

Square thumbnails for image galleries:

```twig
{% include '@atoms/image/image.twig' with {
  src: '/images/gallery-thumb.jpg',
  alt: 'Gallery image 1',
  width: 400,
  height: 400,
  ratio: '1x1',
  rounded: 'sm',
} %}
```

### 5. Logo/Brand Image with Contain

Preserve logo aspect ratio within container:

```twig
{% include '@atoms/image/image.twig' with {
  src: '/images/partner-logo.png',
  alt: 'Partner company logo',
  width: 300,
  height: 150,
  fit: 'contain',
  ratio: '16x9',
  loading: 'lazy',
} %}
```

## Accessibility

### WCAG 2.1 Compliance

1. **Alternative Text (Level A)**
   - `alt` attribute is **required** for all images
   - Descriptive alt text should accurately describe the image content
   - For decorative images, use `alt=""` (empty string)
   - Avoid phrases like "image of" or "picture of"

2. **Prevent Layout Shift (CLS)**
   - Always provide `width` and `height` attributes
   - These prevent Cumulative Layout Shift (CLS) during page load
   - Improves Core Web Vitals score

3. **Lazy Loading**
   - Default `loading="lazy"` improves page load performance
   - Use `loading="eager"` for above-the-fold images (LCP candidates)

4. **Semantic HTML**
   - Uses `<figure>` element for semantic structure
   - Ratio helper uses `aria-hidden="true"` to hide from screen readers

### Best Practices

```twig
{# ✅ GOOD: Descriptive alt text #}
{% include '@atoms/image/image.twig' with {
  src: '/images/building.jpg',
  alt: 'Three-story brick office building with glass entrance',
  width: 800,
  height: 600,
} %}

{# ✅ GOOD: Decorative image #}
{% include '@atoms/image/image.twig' with {
  src: '/images/decorative-pattern.svg',
  alt: '',
  width: 100,
  height: 100,
} %}

{# ❌ BAD: Missing alt text #}
{% include '@atoms/image/image.twig' with {
  src: '/images/photo.jpg',
} %}

{# ❌ BAD: Generic alt text #}
{% include '@atoms/image/image.twig' with {
  src: '/images/building.jpg',
  alt: 'Image',
} %}
```

## Performance Considerations

### Core Web Vitals

1. **LCP (Largest Contentful Paint)**
   - Use `loading="eager"` for hero images above the fold
   - Provide appropriate srcset for different screen sizes
   - Consider using preload for critical images

2. **CLS (Cumulative Layout Shift)**
   - Always specify `width` and `height` attributes
   - Use ratio modifiers for consistent aspect ratios
   - Browser will reserve space before image loads

3. **Responsive Images**
   - Use `srcset` and `sizes` attributes
   - Deliver appropriate image sizes for different devices
   - Example:

     ```twig
     srcset: [
       '/img-400.jpg 400w',
       '/img-800.jpg 800w',
       '/img-1200.jpg 1200w',
     ],
     sizes: '(min-width: 768px) 50vw, 100vw',
     ```

### Lazy Loading

- Default `loading="lazy"` defers offscreen images
- Reduces initial page weight and improves load time
- Browser native, no JavaScript required

## Variants

### Object Fit

- **cover** (default): Fills container, may crop image
- **contain**: Entire image visible, may show letterboxing

### Rounded Corners

- **none** (default): No rounded corners
- **sm**: 4px rounded corners
- **md**: 6px rounded corners
- **lg**: 12px rounded corners
- **full**: Full circle (for avatars)

### Aspect Ratios

- **none** (default): Natural image dimensions
- **16x9**: Widescreen (video/hero banners)
- **1x1**: Square (avatars/thumbnails)
- **4x3**: Classic photo ratio (cards)

## Browser Support

- Modern browsers with native lazy loading support
- Fallback: Images load normally in older browsers
- srcset/sizes supported in all modern browsers (IE11+)

## Implementation Notes

1. **Minimal HTML Output**: Base class only by default, modifiers added only when needed
2. **Independent Modifiers**: All modifier classes work independently on the base class
3. **Token-based**: All values use design tokens from `source/props/*.css`
4. **Responsive**: Uses srcset/sizes for art direction
5. **Performance**: Lazy loading by default, supports preload patterns

## Related Components

- **Avatar**: Specialized circular image component with status indicators
- **Icon**: Vector icons using icon font
- **Logo**: Brand logo component with proper sizing

## References

- [MDN: Responsive Images](https://developer.mozilla.org/en-US/docs/Learn/HTML/Multimedia_and_embedding/Responsive_images)
- [Web.dev: Optimize LCP](https://web.dev/optimize-lcp/)
- [Web.dev: Image Best Practices](https://web.dev/fast/#optimize-your-images)
- Design spec: `docs/design/atoms/image.md`

---

**Last Updated:** November 29, 2025  
**Author:** PS Theme Team
