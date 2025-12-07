# Typography

Comprehensive typography system providing semantic scales for headings, body text, and utility styles.

## Overview

PS Theme typography is built on a modular scale system with:
- **6 heading levels** (h1-h6) for hierarchical structure
- **3 body text sizes** for content hierarchy
- **3 utility styles** for labels, captions, and code
- **Complete token scales** for all font sizes, weights, and line heights

All typography is token-based using CSS custom properties for consistency and maintainability.

## Heading Levels

| Level | Usage | Size | Weight |
|-------|-------|------|--------|
| h1 | Page titles, main headings | 3rem (48px) | 700 |
| h2 | Section titles | 2.25rem (36px) | 700 |
| h3 | Subsection titles | 1.75rem (28px) | 700 |
| h4 | Component headings | 1.5rem (24px) | 700 |
| h5 | Card titles | 1.25rem (20px) | 700 |
| h6 | Minor labels | 1.125rem (18px) | 600 |

## Body Text Styles

### Body Large (Lead)
- **Size**: 1.125rem (18px)
- **Weight**: 400 (Regular)
- **Line Height**: 1.75rem (28px)
- **Use case**: Introductory paragraphs, emphasized content
- **Token**: `--font-size-2`

### Body Regular (Default)
- **Size**: 1rem (16px)
- **Weight**: 400 (Regular)
- **Line Height**: 1.5rem (24px)
- **Use case**: Standard paragraph text, article content
- **Token**: `--font-size-1`

### Body Small
- **Size**: 0.875rem (14px)
- **Weight**: 400 (Regular)
- **Line Height**: 1.25rem (20px)
- **Use case**: Captions, secondary content, metadata
- **Token**: `--font-size-0`

## Utility Styles

### Overline
- **Size**: 0.75rem (12px)
- **Weight**: 600 (Semibold)
- **Transform**: UPPERCASE
- **Letter Spacing**: wide (0.025em)
- **Use case**: Category labels, tags, badges
- **Token**: `--font-size--1` + `--tracking-wide`

### Caption
- **Size**: 0.75rem (12px)
- **Weight**: 400 (Regular)
- **Color**: secondary text
- **Use case**: Timestamps, hints, supplementary info
- **Token**: `--font-size--1`

### Code
- **Size**: 0.75rem (12px)
- **Weight**: 400 (Regular)
- **Family**: Monospace
- **Use case**: Code snippets, technical identifiers
- **Token**: `--font-size--1` + `--font-mono`

## Font Families

| Token | Font | Use Case |
|-------|------|----------|
| `--font-sans` | BNPP Sans (primary) | Body text, UI |
| `--font-condensed` | BNPP Sans Condensed | Compact layouts, headlines |
| `--font-alt` | Open Sans (fallback) | Secondary text |
| `--font-system` | System fonts | Fallback chain |
| `--font-mono` | Monospace | Code, technical text |

## Font Sizes Scale

Complete scale from extra-small to display sizes:

```css
--font-size--2: 0.625rem  /* 10px */
--font-size--1: 0.75rem   /* 12px */
--font-size-0:  0.875rem  /* 14px */
--font-size-1:  1rem      /* 16px - Default */
--font-size-2:  1.125rem  /* 18px */
--font-size-3:  1.25rem   /* 20px */
--font-size-4:  1.375rem  /* 22px */
--font-size-5:  1.5rem    /* 24px */
--font-size-6:  1.75rem   /* 28px */
--font-size-7:  2rem      /* 32px */
--font-size-8:  2.25rem   /* 36px */
--font-size-9:  2.5rem    /* 40px */
--font-size-10: 3rem      /* 48px */
--font-size-11: 3.5rem    /* 56px */
--font-size-12: 4rem      /* 64px */
--font-size-13: 5rem      /* 80px */
--font-size-14: 7.5rem    /* 120px */
```

## Line Height Scale

Proportional line heights for optimal readability:

```css
--leading-none:    1
--leading-tight:   1.25
--leading-snug:    1.375
--leading-normal:  1.5     /* Default */
--leading-relaxed: 1.625
--leading-loose:   2

/* Fixed pixel values */
--leading-3: 0.75rem   /* 12px */
--leading-4: 1rem      /* 16px */
--leading-5: 1.25rem   /* 20px */
--leading-6: 1.5rem    /* 24px */
--leading-7: 1.75rem   /* 28px */
--leading-8: 2rem      /* 32px */
--leading-9: 2.25rem   /* 36px */
--leading-10: 2.5rem   /* 40px */
```

## Font Weight Scale

```css
--font-weight-300: 300 /* Light */
--font-weight-400: 400 /* Regular (Default) */
--font-weight-500: 500 /* Medium */
--font-weight-600: 600 /* Semibold */
--font-weight-700: 700 /* Bold */
--font-weight-800: 800 /* Extra Bold */
```

## Letter Spacing Scale

```css
--tracking-tighter: -0.05em
--tracking-tight:   -0.025em
--tracking-normal:  0        /* Default */
--tracking-wide:    0.025em
--tracking-wider:   0.05em
--tracking-widest:  0.1em
```

## Usage Examples

### Heading with all styles
```html
<h1 style="
  font-size: var(--font-size-10);
  font-weight: var(--font-weight-700);
  line-height: var(--leading-8);
  font-family: var(--font-heading);
">
  Page Title
</h1>
```

### Body text with optimal readability
```html
<p style="
  font-size: var(--font-size-1);
  font-weight: var(--font-weight-400);
  line-height: var(--leading-6);
  color: var(--text-primary);
">
  Standard paragraph text...
</p>
```

### Overline label
```html
<span style="
  font-size: var(--font-size--1);
  font-weight: var(--font-weight-600);
  text-transform: uppercase;
  letter-spacing: var(--tracking-wide);
">
  CATEGORY
</span>
```

## Accessibility

- **Font sizes**: Respect user preferences, don't disable zoom
- **Line height**: Minimum 1.5 for body text (WCAG AA)
- **Contrast**: All text meets WCAG AA standards (4.5:1 minimum)
- **Font families**: Include system font fallbacks for performance

## Tokens

All typography uses CSS custom properties from `source/props/fonts.css`:
- Font families
- Font sizes
- Font weights
- Line heights
- Letter spacing

## See Also

- [Bootstrap Typography](https://getbootstrap.com/docs/5.3/content/typography/)
- [Design System - Fonts](../fonts/)
- [WCAG Text Guidelines](https://www.w3.org/WAI/WCAG22/Understanding/font-size)
