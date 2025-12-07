/**
 * @file
 * Base/Typography - Complete PS Design System Typography Documentation & Demo
 *
 * Provides comprehensive documentation and live demonstration of the PS Design System
 * typography scale, including:
 * - Semantic heading levels (h1–h6) and display utilities (.display-1 → .display-6)
 * - Body text scales with font weight variations
 * - Inline semantic elements and their proper usage
 * - Lists (unordered, ordered, definition) with nesting
 * - Blockquotes and citations
 * - Text utility classes (.text-*, .uppercase, .underline, etc.)
 * - Complete token reference (families, weights, sizes, line heights, tracking)
 * - Accessibility checklist and responsive design principles
 *
 * Stack:
 * - Storybook HTML Edition (no component framework)
 * - Vite + PostCSS for CSS compilation
 * - Twig templates for rendering
 * - YAML data structure for demo metadata
 *
 * Usage in Components:
 * All components must reference these typography tokens:
 * - source/props/fonts.css → CSS custom properties (--font-size-*, --font-weight-*, --leading-*)
 * - source/patterns/base/utilities/typography.css → Utility classes (.h1–.h6, .display-1–.display-6, etc.)
 *
 * Document Structure:
 * The Markdown documentation below includes:
 * 1. System Overview & Architecture
 * 2. Heading Strategy (Semantic HTML best practices)
 * 3. Font Scale Explanation
 * 4. Utility Classes & Modifiers
 * 5. Token Reference Tables
 * 6. Accessibility Requirements
 * 7. Usage Examples with Real Estate Context
 * 8. Related Patterns (Button, Avatar, Badge, etc.)
 *
 * Design Philosophy:
 * - Token-first approach: No hardcoded values (#00915A, 16px, 150ms)
 * - Semantic HTML: Always prefer native elements (h1–h6, strong, em, etc.) over divs
 * - Accessibility: WCAG 2.2 AA minimum, focus indicators, high contrast
 * - Real Estate Context: Examples use BNP Paribas Real Estate domain language
 * - Responsive: rem-based scaling works seamlessly across all viewport sizes
 */

import typography from './typography.twig';
import data from './typography.yml';

/**
 * Storybook story settings and metadata
 * Note: No 'tags: ["autodocs"]' because this is a documentation pattern,
 * not a component. Autodocs are disabled to control the layout fully.
 */
const settings = {
  title: 'Base/Typography',
  parameters: {
    layout: 'fullscreen',
    docs: {
      description: {
        component: `
# PS Design System - Complete Typography Reference

The PS Design System typography system provides a **token-driven, scalable, and accessible** approach to text styling across all BNP Paribas Real Estate digital properties.

## System Overview

The typography system is built on **3 foundational layers**:

\`\`\`
Semantic HTML Elements (h1–h6, p, strong, em, etc.)
                ↓
CSS Custom Properties / Tokens (--font-size-*, --font-weight-*, --leading-*)
                ↓
Utility Classes (.h1–.h6, .display-1–.display-6, .text-primary, etc.)
                ↓
Components (Button, Badge, Card, etc.)
\`\`\`

### Key Principles

✅ **Token-First**: All typography values are defined in \`source/props/fonts.css\`. No hardcoded values.

✅ **Semantic HTML**: Always use native heading levels (\`<h1>–<h6>\`) and inline elements (\`<strong>\`, \`<em>\`, \`<abbr>\`).

✅ **Accessibility**: WCAG 2.2 AA compliance guaranteed. High contrast, focus indicators, proper heading hierarchy.

✅ **Responsive**: rem-based sizing (1rem = 16px) scales smoothly across all devices. No breakpoint declarations needed.

✅ **Real Estate Context**: Examples and use cases tailored for BNP Paribas Real Estate domain (assets, portfolios, properties, etc.).

---

## Heading Strategy & Hierarchy

### Native Heading Elements (h1–h6)

Use semantic heading elements **always**. They provide:
- Document outline for screen readers
- SEO signal for search engines
- Logical hierarchy for page structure
- Proper heading nesting (no skipped levels: \`h1 → h3\`)

| Level | CSS Class | Font Size | Font Weight | Line Height | Usage |
|-------|-----------|-----------|-------------|-------------|-------|
| \`<h1>\` | \`.h1\` | --font-size-10 (3rem/48px) | 700 | 1.25 | Main page heading, hero title |
| \`<h2>\` | \`.h2\` | --font-size-9 (2.5rem/40px) | 700 | 1.25 | Section title, major division |
| \`<h3>\` | \`.h3\` | --font-size-8 (2.25rem/36px) | 700 | 1.25 | Subsection, feature group |
| \`<h4>\` | \`.h4\` | --font-size-7 (2rem/32px) | 700 | 1.375 | Tertiary title, card title |
| \`<h5>\` | \`.h5\` | --font-size-6 (1.75rem/28px) | 700 | 1.375 | Minor heading, list header |
| \`<h6>\` | \`.h6\` | --font-size-5 (1.5rem/24px) | 700 | 1.375 | Smallest heading, metadata |

### Example: Proper Heading Hierarchy

\`\`\`html
<main>
  <h1>Real Estate Portfolio Management</h1>   <!-- Page title -->
  
  <section>
    <h2>Asset Overview</h2>               <!-- Section -->
    <p>Brief introduction to the portfolio...</p>
    
    <h3>Residential Properties</h3>       <!-- Subsection -->
    <article>
      <h4>Property Details</h4>           <!-- Article title -->
      <p>Details about the property...</p>
    </article>
  </section>
  
  <section>
    <h2>Market Analysis</h2>              <!-- Another section -->
    <p>Market insights and trends...</p>
  </section>
</main>
\`\`\`

---

## Display Classes (.display-1 → .display-6)

Non-semantic display classes for applying heading-like styles to generic elements (\`<div>\`, \`<span>\`) when semantic markup isn't possible. **Use sparingly.**

| Class | Font Size | Font Weight | Line Height | Usage |
|-------|-----------|-------------|-------------|-------|
| \`.display-1\` | --font-size-14 (7.5rem/120px) | 700 | 1.25 | Hero sections, campaign headlines |
| \`.display-2\` | --font-size-13 (5rem/80px) | 700 | 1.25 | Large impact text, promotions |
| \`.display-3\` | --font-size-12 (4rem/64px) | 700 | 1.25 | Campaign headlines, feature titles |
| \`.display-4\` | --font-size-11 (3.5rem/56px) | 700 | 1.25 | Feature sections, emphasis |
| \`.display-5\` | --font-size-10 (3rem/48px) | 700 | 1.25 | Emphasis text, special callouts |
| \`.display-6\` | --font-size-9 (2.5rem/40px) | 700 | 1.25 | Alternative heading size |

### When to Use Display Classes

✅ **Yes**: Non-semantic content requiring heading-like styling
✅ **Yes**: CMS-generated content where HTML structure is limited
✅ **Yes**: Rapid prototyping with generic layout

❌ **No**: Replace native heading elements
❌ **No**: Skip semantic markup for convenience
❌ **No**: Stack multiple display classes (use one size per element)

---

## Body Text & Paragraph Scale

### Standard Paragraph (\`<p>\`)

\`\`\`css
font-size: var(--font-size-1);     /* 1rem / 16px */
font-weight: var(--font-weight-400);
line-height: var(--leading-normal); /* 1.5 (24px) */
font-family: var(--font-body);     /* BNPP Sans */
\`\`\`

All paragraphs inherit from this baseline. It provides excellent readability for body text.

### Lead Text (\`.lead\`)

For introductions, abstracts, and article summaries:

\`\`\`css
font-size: var(--font-size-2);      /* 1.125rem / 18px */
line-height: var(--leading-relaxed); /* 1.625 (29px) */
\`\`\`

**Usage**:
\`\`\`html
<article>
  <h1>Real Estate Investment Trends 2025</h1>
  <p class="lead">
    A comprehensive analysis of emerging trends shaping the real estate market
    for institutional investors and portfolio managers.
  </p>
  <p>The market analysis shows...</p>
</article>
\`\`\`

### Small Text (\`<small>\`)

Secondary information, disclaimers, captions:

\`\`\`css
font-size: var(--font-size-0); /* 0.875rem / 14px */
\`\`\`

### Micro Text (\`.micro\`)

Minimal UI labels, timestamps, metadata:

\`\`\`css
font-size: var(--font-size--1); /* 0.75rem / 12px */
\`\`\`

---

## Font Weight Variations

The system supports three primary font weights from BNPP Sans:

| Weight | Token | Value | Usage | File |
|--------|-------|-------|-------|------|
| Light | \`--font-weight-300\` | 300 | Subtle emphasis, elegant typography (rarely) | BNPPSans-Light.woff2 |
| Regular | \`--font-weight-400\` | 400 | Body text, paragraphs (default) | BNPPSans-Regular.woff2 |
| Bold | \`--font-weight-700\` | 700 | Headings, emphasis, important information | BNPPSans-Bold.woff2 |

**Best Practices**:
- Use weight variation to indicate hierarchy, not color alone
- Bold for headings and emphasis
- Regular for body text
- Light only for very subtle, large headlines (limited accessibility)

---

## Line Height (Leading)

Line height affects readability, accessibility, and visual balance. The system provides six leading values:

| Token | Value | Usage |
|-------|-------|-------|
| \`--leading-none\` | 1 | Display headlines, abbreviations (tight) |
| \`--leading-tight\` | 1.25 | Headings (h1–h3), compact display |
| \`--leading-snug\` | 1.375 | Headings (h4–h6), medium text |
| \`--leading-normal\` | 1.5 | **Body text (recommended default)** |
| \`--leading-relaxed\` | 1.625 | Long-form content, accessibility focus |
| \`--leading-loose\` | 2 | Form fields, widely spaced blocks (rare) |

**Accessibility Rule**: For body text, maintain **at least 1.5x line height** (--leading-normal) to ensure readability for people with dyslexia and vision impairments.

---

## Letter Spacing (Tracking)

Letter spacing should be used **sparingly** and only for specific design effects:

| Token | Value | Usage |
|-------|-------|-------|
| \`--tracking-tighter\` | -0.05em | Tight uppercase headlines (rare) |
| \`--tracking-tight\` | -0.025em | Condensed display text (use cautiously) |
| \`--tracking-normal\` | 0 | Standard spacing (default) |
| \`--tracking-wide\` | 0.025em | Uppercase labels, form hints |
| \`--tracking-wider\` | 0.05em | Spaced uppercase headings |
| \`--tracking-widest\` | 0.1em | Decorative text (very rare) |

**⚠️ Caution**: Excessive letter spacing reduces readability. Use only on short text (headlines, labels) and test with screen readers.

---

## Text Utility Classes

Reusable utility classes from \`source/patterns/base/utilities/typography.css\`:

### Color Utilities

\`\`\`html
<p class="text-primary">Primary text (main content)</p>
<p class="text-secondary">Secondary text (metadata)</p>
<p class="text-tertiary">Tertiary text (hints, captions)</p>
<p class="text-link">Link text (uses --primary)</p>
<p class="text-inverse">Inverse text (on dark backgrounds)</p>
\`\`\`

All color tokens are semantic and WCAG AA compliant.

### Text Alignment

\`\`\`html
<p class="text-left">Left-aligned (default)</p>
<p class="text-center">Center-aligned</p>
<p class="text-right">Right-aligned</p>
<p class="text-justify">Justified (avoid—hyphens interfere with screen readers)</p>
\`\`\`

### Text Transform

\`\`\`html
<p class="uppercase">uppercase text</p>
<p class="lowercase">LOWERCASE TEXT</p>
<p class="capitalize">capitalize text</p>
<p class="normal-case">NORMAL CASE TEXT</p>
\`\`\`

### Text Decoration

\`\`\`html
<p class="underline">Underlined text</p>
<p class="line-through">Struck-through text</p>
<p class="overline-text">Overlined text</p>
<a href="#" class="no-underline">Link without underline</a>
\`\`\`

---

## Inline Semantic Elements

Always use semantic elements for proper meaning and accessibility:

| Element | Purpose | Accessibility |
|---------|---------|----------------|
| \`<strong>\` | Important emphasis | Announced as "strong" by screen readers |
| \`<em>\` | Alternative tone/mood | Announced as "emphasized" by screen readers |
| \`<mark>\` | Highlighted/highlighted text | Visual highlight, no semantic meaning |
| \`<del>\` | Deleted text | "Deleted" announced, visually struck |
| \`<ins>\` | Inserted text | "Inserted" announced, visually underlined |
| \`<kbd>\` | Keyboard input | Monospace font for commands |
| \`<code>\` | Inline code | Monospace font for snippets |
| \`<abbr title="">\` | Abbreviation | Title attribute read on hover/focus |
| \`<cite>\` | Citation/source | Italicized, indicates external reference |

### Example: Semantic Markup in Real Estate Context

\`\`\`html
<p>
  The <strong>client retention rate</strong> increased by 23% this quarter,
  demonstrating <em>successful portfolio management strategies</em>. See the
  <cite>Q4 Market Analysis Report</cite> for details.
</p>

<p>
  <abbr title="Customer Relationship Management">CRM</abbr> integration
  allows property managers to track <kbd>investor@bnpparibas.com</kbd> communications.
</p>
\`\`\`

---

## Lists

### Unordered List (\`<ul>\`)

For non-sequential items (features, benefits, recommendations):

\`\`\`html
<ul>
  <li>Residential properties in prime locations</li>
  <li>Commercial office spaces with modern amenities</li>
  <li>Mixed-use developments with strong growth potential</li>
</ul>
\`\`\`

### Ordered List (\`<ol>\`)

For sequential steps, rankings, or numbered processes:

\`\`\`html
<ol>
  <li>Perform initial property assessment and valuation</li>
  <li>Conduct market analysis and competitive benchmarking</li>
  <li>Develop investment strategy and risk mitigation plan</li>
  <li>Execute acquisition and integrate into portfolio</li>
</ol>
\`\`\`

### Definition List (\`<dl>\`)

For terms and definitions (glossaries, specifications):

\`\`\`html
<dl>
  <dt>Cap Rate</dt>
  <dd>Capitalization rate; net operating income divided by property price.</dd>
  
  <dt>Due Diligence</dt>
  <dd>Comprehensive assessment of property condition, financials, and market position.</dd>
  
  <dt>IRR</dt>
  <dd>Internal Rate of Return; expected annual profit on an investment.</dd>
</dl>
\`\`\`

### Nested Lists

Lists can be nested for hierarchical information:

\`\`\`html
<ul>
  <li>Asset Categories
    <ul>
      <li>Residential (1,250 properties)</li>
      <li>Commercial (340 properties)</li>
      <li>Industrial (180 properties)</li>
    </ul>
  </li>
  <li>Regional Breakdown
    <ol>
      <li>Europe (45%)</li>
      <li>Asia Pacific (35%)</li>
      <li>Americas (20%)</li>
    </ol>
  </li>
</ul>
\`\`\`

---

## Blockquotes & Citations

Use semantic \`<blockquote>\` for quoted content, with \`<footer>\` and \`<cite>\` for attribution:

\`\`\`html
<blockquote>
  <p>
    Successful real estate investment requires deep market knowledge,
    disciplined analysis, and long-term commitment to sustainable returns.
  </p>
  <footer>
    — <cite>Investment Strategy Guide, BNP Paribas Real Estate</cite>
  </footer>
</blockquote>
\`\`\`

---

## Complete Token Reference

See \`source/props/fonts.css\` for all token definitions.

### Font Families

| Token | Value | Usage |
|-------|-------|-------|
| \`--font-body\`, \`--font-sans\` | 'BNPP Sans', system fonts | Primary body & headings |
| \`--font-alt\` | 'Open Sans', system fonts | Alternative paragraph text |
| \`--font-mono\` | Courier New, monospace | Code and technical content |

### Font Sizes (14-point scale)

From \`--font-size--2\` (10px) to \`--font-size-14\` (120px). All rem-based for user-controlled scaling.

### Line Heights

Six predefined leading values from \`--leading-none\` (1) to \`--leading-loose\` (2).

### Letter Spacing

Six tracking values from \`--tracking-tighter\` (-0.05em) to \`--tracking-widest\` (0.1em).

---

## Accessibility Checklist

Before deploying any typography changes, verify:

✅ **Semantic HTML**: Headings use \`<h1>–<h6>\`, inline emphasis uses \`<strong>\`, \`<em>\`, etc.

✅ **Heading Hierarchy**: No skipped levels (e.g., \`<h1>\` directly to \`<h3>\`). Proper outline.

✅ **Contrast**: WCAG 2.2 AA minimum: 4.5:1 for normal text, 3:1 for large text (18pt+).

✅ **Focus Indicators**: All interactive text (links, buttons) has visible \`:focus-visible\` styling.

✅ **Font Scaling**: Uses rem units (not px). Users can resize via browser zoom (100–200%).

✅ **Line Height**: Body text uses \`--leading-normal\` (1.5) or better for readability.

✅ **Color Alone**: Not the only indicator of meaning. Use weight, icons, or text labels.

✅ **Lists**: Semantic markup (\`<ul>\`, \`<ol>\`, \`<dl>\`). Screen readers announce list structure.

✅ **Links**: Visually distinct from body text (underlined, colored, or other). Not just underline.

✅ **Code**: Escaped and syntax-highlighted. Monospace font (\`--font-mono\`) for clarity.

✅ **Justified Text**: Avoided. Right-edge alignment causes word-spacing inconsistencies.

---

## Usage Examples in Components

### Example 1: Card Component with Typography

\`\`\`html
<article class="card">
  <h3 class="card__title">Investment Opportunity</h3>
  <p class="card__description">
    High-yield residential property in central Paris with strong rental demand.
  </p>
  <small class="card__meta">Updated 3 hours ago</small>
  <a href="/properties/paris-001" class="card__link">View Details</a>
</article>
\`\`\`

**Typography tokens**:
- \`<h3>\` → --font-size-8, --font-weight-700, --leading-tight
- \`<p>\` → --font-size-1, --leading-normal
- \`<small>\` → --font-size-0

### Example 2: Form Labels

\`\`\`html
<label for="property-type">
  Property Type <span class="text-danger">*</span>
</label>
<select id="property-type">
  <option>Residential</option>
  <option>Commercial</option>
  <option>Industrial</option>
</select>
\`\`\`

**Typography tokens**:
- Label → --font-weight-500 (emphasized)
- Asterisk → --primary-danger (error color)

### Example 3: Button with Typography

\`\`\`html
<button class="btn btn--primary">
  <span class="btn__label">Add Property</span>
  <span class="btn__icon" aria-hidden="true">+</span>
</button>
\`\`\`

**Typography tokens**:
- Button text → --font-weight-600, --font-size-1

---

## Related Patterns

This typography system is used by all PS Design System components:

- **[Button](/?path=/story/elements-button--default)**: Heading levels + font weights for labels
- **[Avatar](/?path=/story/elements-avatar--default)**: Micro text (--font-size--2) for initials
- **[Badge](/?path=/story/elements-badge--default)**: Small text with semantic colors
- **[Breadcrumb](/?path=/story/components-breadcrumb--default)**: Navigation hierarchy
- **[Card](/?path=/story/components-card--default)**: Title + description + metadata
- **[Form Controls](/?path=/story/components-input--default)**: Labels, hints, error messages
- **[Table](/?path=/story/components-table--default)**: Column headers + row data
- **[Navigation](/?path=/story/components-navigation--default)**: Menu items, active states

---

## File References

| File | Purpose |
|------|---------|
| \`source/props/fonts.css\` | Official token definitions (families, weights, sizes, leading, tracking) |
| \`source/patterns/base/utilities/typography.css\` | Utility classes (.h1–.h6, .display-*, .text-*) |
| \`source/patterns/base/typography/typography.twig\` | This documentation + demo (complete examples) |
| \`.github/instructions/core.instructions.md\` | Token usage rules and guidelines |
| \`.github/instructions/css.instructions.md\` | CSS implementation patterns |

---

## Design Philosophy

The PS Design System typography is built on three core principles:

1. **Token-First**: All values are defined once in CSS custom properties. Components reference tokens, never hardcoded values.

2. **Semantic HTML**: Always use native elements (\`<h1>–<h6>\`, \`<strong>\`, \`<em>\`, etc.). Meaningful markup is accessible markup.

3. **Inclusive Design**: WCAG 2.2 AA compliance by default. Font sizes, contrast ratios, line heights, and focus indicators all meet accessibility standards.

---

## Questions or Issues?

See \`README.md\` for support channels and contribution guidelines.
        `,
      },
    },
  },
};

/**
 * Typography Story
 *
 * Renders the complete typography demo using the Twig template.
 * Spread all YAML data as args for flexibility in future customization.
 */
const Typography = {
  name: 'Typography',
  render: (args) => typography(args),
  args: {
    ...data,
  },
};

export default settings;
export { Typography };
