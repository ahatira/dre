# Typography System - PS Design System

**Documentation Pattern** | **Complete Reference** | **Base Component**

---

## Overview

The PS Design System Typography System provides a **token-driven, semantic, and accessible** approach to text styling across all BNP Paribas Real Estate digital properties. This pattern documents the complete typography scale, usage guidelines, and best practices.

### Key Characteristics

- ✅ **Token-First**: All values defined in CSS custom properties (zero hardcoded values)
- ✅ **Semantic HTML**: Always use native elements (`<h1>–<h6>`, `<strong>`, `<em>`, etc.)
- ✅ **Accessible**: WCAG 2.2 AA compliance by default
- ✅ **Responsive**: rem-based sizing scales seamlessly across all devices
- ✅ **Real Estate Context**: Examples and language tailored for BNP Paribas Real Estate

---

## Files

| File | Purpose |
|------|---------|
| `typography.twig` | Complete typography demo with 9 sections and live examples |
| `typography.yml` | Metadata for Storybook story (demo data structure) |
| `typography.stories.jsx` | Storybook story with comprehensive Markdown documentation |
| `README.md` | This file (usage guidelines) |

---

## Structure

### 1. Heading Elements (h1 → h6)

Semantic heading levels with CSS class equivalents (`.h1`–`.h6`) using **Bold (700)** weight:

```html
<h1>Main page heading</h1>        <!-- Desktop: 48px / 60px | Mobile: 40px / 52px | Weight: 700 -->
<h2>Section title</h2>            <!-- Desktop: 44px / 44px | Mobile: 36px / 44px | Weight: 700 -->
<h3>Subsection</h3>               <!-- Desktop: 32px / 40px | Mobile: 28px / 40px | Weight: 700 -->
<h4>Tertiary title</h4>           <!-- 24px / 30px | Weight: 700 -->
<h5>Minor heading</h5>            <!-- 20px / 24px | Weight: 700 -->
<h6>Small heading</h6>            <!-- 14px / 20px | Weight: 700 -->
```

**Rules:**
- Always use native heading elements, never skip levels (`h1 → h3` is wrong)
- Use `.h*` classes only when semantic markup isn't possible
- One h1 per page (SEO, accessibility)
- All headings use **Bold (700)** font weight for strong visual hierarchy
- h1, h2, h3 are responsive (scale mobile → desktop)
- h4, h5, h6 are fixed size on all devices

### 2. Display Classes (.display-1 → .display-6)

Non-semantic display utilities for applying heading-like styles to generic elements with **Bold (700)** weight:

```html
<div class="display-1">Hero text</div>              <!-- 7.5rem / 120px | Weight: 700 -->
<div class="display-2">Large impact</div>           <!-- 5rem / 80px | Weight: 700 -->
<div class="display-3">Campaign headline</div>     <!-- 4rem / 64px | Weight: 700 -->
<div class="display-4">Feature title</div>         <!-- 3.5rem / 56px | Weight: 700 -->
<div class="display-5">Emphasis text</div>         <!-- 3rem / 48px | Weight: 700 -->
<div class="display-6">Alternative heading</div>  <!-- 2.5rem / 40px | Weight: 700 -->
```

**When to use:**
- ✅ Non-semantic content requiring heading-like styling
- ✅ CMS-generated content where HTML structure is limited
- ✅ Hero sections, campaigns, promotional content
- ❌ Replace native heading elements
- ❌ Skip semantic markup for convenience

**Note**: Display classes are presented as visual cards in the Storybook story for easy comparison.

### 3. Body Text & Paragraph Utilities

Standard paragraph text with semantic size variations:

```html
<!-- Default body paragraph -->
<p>Standard paragraph text (--font-size-1, 1rem/16px)</p>

<!-- Lead text for introductions -->
<p class="lead">Lead paragraph for article intro or abstract (--font-size-2, 1.125rem/18px)</p>

<!-- Small text for secondary info -->
<small>Small text for captions and metadata (--font-size-0, 0.875rem/14px)</small>

<!-- Micro text for minimal UI labels -->
<p class="micro">Micro text for avatar initials or compact UI (--font-size--1, 0.75rem/12px)</p>
```

### 4. Font Weight Variations

Three primary weights from BNPP Sans:

```css
font-weight: var(--font-weight-300);  /* Light (rarely used) */
font-weight: var(--font-weight-400);  /* Regular (body text, default) */
font-weight: var(--font-weight-700);  /* Bold (headings, emphasis) */
```

### 5. Inline Semantic Elements

Always use semantic elements for proper meaning:

```html
<strong>Important emphasis</strong>              <!-- Strong importance -->
<em>Alternative tone or mood</em>               <!-- Emphasis -->
<mark>Highlighted content</mark>                <!-- Visual highlight -->
<del>Deleted text</del>                         <!-- Deletion -->
<ins>Inserted text</ins>                        <!-- Insertion -->
<kbd>Ctrl + Alt + Delete</kbd>                  <!-- Keyboard input -->
<code>calculateTotal(items)</code>              <!-- Inline code -->
<abbr title="BNP Paribas Real Estate">BNPE</abbr>  <!-- Abbreviation -->
<cite>Citation source</cite>                    <!-- Citation -->
```

### 6. Lists

Semantic list structures:

```html
<!-- Unordered list (bullets) -->
<ul>
  <li>First item</li>
  <li>Second item</li>
</ul>

<!-- Ordered list (numbers) -->
<ol>
  <li>First step</li>
  <li>Second step</li>
</ol>

<!-- Definition list (term-definition pairs) -->
<dl>
  <dt>Cap Rate</dt>
  <dd>Capitalization rate; net operating income divided by property price.</dd>
</dl>

<!-- Nested lists -->
<ul>
  <li>Parent item
    <ul>
      <li>Nested item 1.1</li>
      <li>Nested item 1.2</li>
    </ul>
  </li>
</ul>
```

### 7. Blockquotes & Citations

Semantic markup for quoted content:

```html
<blockquote>
  <p>
    Real estate is the best investment for long-term wealth creation.
    It provides tangible assets, steady income, and capital appreciation potential.
  </p>
  <footer>
    <cite>Investment Strategy Guide</cite>
  </footer>
</blockquote>
```

### 8. Text Utility Classes

Reusable utility classes from `source/patterns/base/utilities/typography.css`:

```html
<!-- Color utilities -->
<p class="text-primary">Primary text (main content)</p>
<p class="text-secondary">Secondary text (metadata)</p>
<p class="text-tertiary">Tertiary text (hints)</p>
<p class="text-link">Link text (--primary color)</p>

<!-- Alignment -->
<p class="text-left">Left-aligned</p>
<p class="text-center">Center-aligned</p>
<p class="text-right">Right-aligned</p>

<!-- Transform -->
<p class="uppercase">uppercase text</p>
<p class="lowercase">LOWERCASE TEXT</p>
<p class="capitalize">capitalize text</p>

<!-- Decoration -->
<p class="underline">Underlined text</p>
<p class="line-through">Struck-through text</p>
<p class="no-underline"><a href="#">Link without underline</a></p>
```

---

## Token Reference

### Font Families

| Token | Value | Usage |
|-------|-------|-------|
| `--font-body`, `--font-sans` | 'BNPP Sans', system fonts | Primary body & headings |
| `--font-alt` | 'Open Sans', system fonts | Alternative paragraph text |
| `--font-mono` | Courier New, monospace | Code and technical content |

### Font Sizes (14-point scale)

From `--font-size--2` (10px) to `--font-size-14` (120px). All rem-based for user-controlled scaling.

| Token | Value (rem) | Pixels (16px base) | Usage |
|-------|-------------|-------------------|-------|
| `--font-size--2` | 0.625rem | 10px | Avatar initials |
| `--font-size--1` | 0.75rem | 12px | Micro labels |
| `--font-size-0` | 0.875rem | 14px | Small text |
| `--font-size-1` | 1rem | 16px | Body text (default) |
| `--font-size-2` | 1.125rem | 18px | Lead text |
| `--font-size-3` | 1.25rem | 20px | Emphasis |
| `--font-size-4` | 1.375rem | 22px | Minor emphasis |
| `--font-size-5` | 1.5rem | 24px | h4 and .h4 |
| `--font-size-6` | 1.75rem | 28px | h3 and .h3 (mobile) |
| `--font-size-7` | 2rem | 32px | h3 and .h3 (desktop) |
| `--font-size-8` | 2.25rem | 36px | h2 and .h2 (mobile) |
| `--font-size-9` | 2.5rem | 40px | h1 (mobile) / h2 (desktop) |
| `--font-size-10` | 3rem | 48px | h1 and .h1 (desktop) |
| `--font-size-11` | 3.5rem | 56px | .display-4 |
| `--font-size-12` | 4rem | 64px | .display-3 |
| `--font-size-13` | 5rem | 80px | .display-2 |
| `--font-size-14` | 7.5rem | 120px | .display-1 |

### Line Height (Leading)

| Token | Value | Usage |
|-------|-------|-------|
| `--leading-none` | 1 | Display headlines, abbreviations |
| `--leading-tight` | 1.25 | Headings (h1–h3), compact |
| `--leading-snug` | 1.375 | Headings (h4–h6), medium |
| `--leading-normal` | 1.5 | **Body text (recommended)** |
| `--leading-relaxed` | 1.625 | Long-form content, accessibility |
| `--leading-loose` | 2 | Form fields, widely spaced |

**Accessibility Rule**: Maintain at least 1.5x line height (`--leading-normal`) for body text to ensure readability for people with dyslexia and vision impairments.

### Letter Spacing (Tracking)

| Token | Value | Usage |
|-------|-------|-------|
| `--tracking-tighter` | -0.05em | Tight uppercase headlines (rare) |
| `--tracking-tight` | -0.025em | Condensed display text |
| `--tracking-normal` | 0 | Standard spacing (default) |
| `--tracking-wide` | 0.025em | Uppercase labels, form hints |
| `--tracking-wider` | 0.05em | Spaced uppercase headings |
| `--tracking-widest` | 0.1em | Decorative text (very rare) |

**⚠️ Warning**: Excessive letter spacing reduces readability. Use only on short text (headlines, labels) and test with screen readers.

---

## Accessibility Checklist

Before deploying typography changes:

- ✅ **Semantic HTML**: Headings use `<h1>–<h6>`, inline emphasis uses `<strong>`, `<em>`, etc.
- ✅ **Heading Hierarchy**: No skipped levels (e.g., `<h1>` to `<h3>`). Proper outline.
- ✅ **Contrast**: WCAG 2.2 AA minimum: 4.5:1 for normal text, 3:1 for large text (18pt+)
- ✅ **Focus Indicators**: All interactive text (links, buttons) has visible `:focus-visible`
- ✅ **Font Scaling**: Uses rem units (not px). Users can resize via browser zoom (100–200%)
- ✅ **Line Height**: Body text uses `--leading-normal` (1.5) or better
- ✅ **Color Alone**: Never the only indicator of meaning. Use weight, icons, or text
- ✅ **Lists**: Semantic markup (`<ul>`, `<ol>`, `<dl>`). Screen readers announce structure
- ✅ **Links**: Visually distinct from body text (underlined, colored, or other)
- ✅ **Code**: Escaped and syntax-highlighted. Monospace font for clarity
- ✅ **Justified Text**: Avoided. Right-edge alignment causes word-spacing issues

---

## Usage in Components

All components must use typography tokens from this system:

```css
/* ✅ CORRECT - Token reference */
h2 {
  font-size: var(--font-size-9);
  font-weight: var(--font-weight-700);
  line-height: var(--leading-tight);
}

/* ❌ INCORRECT - Hardcoded value */
h2 {
  font-size: 2.5rem;        /* NO! Use --font-size-9 */
  font-weight: 700;         /* NO! Use --font-weight-700 */
  line-height: 1.25;        /* NO! Use --leading-tight */
}
```

### Example: Card Component

```html
<article class="card">
  <h3 class="card__title">Investment Opportunity</h3>
  <p class="card__description">
    High-yield residential property in central Paris.
  </p>
  <small class="card__meta">Updated 3 hours ago</small>
  <a href="/properties/paris-001" class="card__link">View Details</a>
</article>
```

**Typography tokens:**
- `<h3>` → --font-size-8, --font-weight-700, --leading-tight
- `<p>` → --font-size-1, --leading-normal
- `<small>` → --font-size-0

---

## Related Patterns

This typography system is referenced by all PS Design System components:

- **[Button](/?path=/story/elements-button--default)** - Heading levels + font weights
- **[Avatar](/?path=/story/elements-avatar--default)** - Micro text for initials
- **[Badge](/?path=/story/elements-badge--default)** - Small text with semantic colors
- **[Breadcrumb](/?path=/story/components-breadcrumb--default)** - Navigation hierarchy
- **[Card](/?path=/story/components-card--default)** - Title + description + metadata
- **[Form Controls](/?path=/story/components-input--default)** - Labels, hints, errors
- **[Table](/?path=/story/components-table--default)** - Headers + row data
- **[Navigation](/?path=/story/components-navigation--default)** - Menu items, states

---

## File References

| File | Purpose |
|------|---------|
| `source/props/fonts.css` | Official token definitions (families, weights, sizes, leading, tracking) |
| `source/patterns/base/utilities/typography.css` | Utility classes (.h1–.h6, .display-*, .text-*) |
| `source/patterns/base/typography/` | This documentation pattern |
| `.github/instructions/core.instructions.md` | Token usage rules |
| `.github/instructions/css.instructions.md` | CSS implementation patterns |

---

## Design Philosophy

The PS Design System typography is built on three core principles:

1. **Token-First**: All values defined once in CSS custom properties. Components reference tokens, never hardcoded values.

2. **Semantic HTML**: Always use native elements (`<h1>–<h6>`, `<strong>`, `<em>`, etc.). Meaningful markup is accessible markup.

3. **Inclusive Design**: WCAG 2.2 AA compliance by default. Font sizes, contrast ratios, line heights, and focus indicators meet accessibility standards.

---

## Storybook Story

View the complete interactive demo and documentation:

```
Base/Typography → Typography
```

The story includes:
- Live demonstration of all heading levels and display classes
- Body text scales with font weight variations
- Inline semantic elements
- Lists (unordered, ordered, definition, nested)
- Blockquotes and citations
- Text utility classes
- Complete token reference tables
- Accessibility guidelines and best practices

---

## Questions or Issues?

See project README for support channels and contribution guidelines.

**Last Updated**: 2025-12-07
**Version**: 1.0.0
