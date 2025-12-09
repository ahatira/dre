# Eyebrow Component

A contextual label or kicker placed above a heading to provide category or context. Typically short, uppercase text in a semantic color. Used for categorizing content, highlighting updates, or providing metadata (dates, tags) in real estate applications.

## Component Props

| Prop | Type | Options | Default | Description |
|------|------|---------|---------|-------------|
| `text` | string | any | `''` | Text content displayed (required) |
| `variant` | string | `'primary'`, `'secondary'`, `'accent'`, `'neutral'`, `'muted'` | `'neutral'` | Semantic color variant |
| `size` | string | `'small'`, `'medium'` | `'medium'` | Text size (12px/14px) |
| `uppercase` | boolean | `true`, `false` | `true` | Transform text to uppercase |
| `bold` | boolean | `true`, `false` | `false` | Apply bold font weight |
| `withLine` | boolean | `true`, `false` | `false` | Add decorative horizontal line before text |
| `withDot` | boolean | `true`, `false` | `false` | Add decorative dot before text |
| `icon` | string | icon name | `''` | Optional icon (without "icon-" prefix) |
| `attributes` | object | any | - | Additional HTML attributes |

## BEM Structure

```
ps-eyebrow                       # Root element (<span>)
├── ps-eyebrow--primary          # Primary color variant (green brand)
├── ps-eyebrow--secondary        # Secondary color variant (magenta)
├── ps-eyebrow--accent           # Accent color variant (gold - premium/featured)
├── ps-eyebrow--muted            # Muted color variant (subtle gray - metadata)
├── ps-eyebrow--small            # Small size (12px)
├── ps-eyebrow--uppercase        # Uppercase text transformation
├── ps-eyebrow--bold             # Bold font weight
├── ps-eyebrow--with-line        # Has decorative line
├── ps-eyebrow--with-dot         # Has decorative dot
│
├── ps-eyebrow__icon             # Icon element (decorative, data-icon attribute)
├── ps-eyebrow__text             # Text content wrapper
├── ps-eyebrow__line             # Decorative line element
└── ps-eyebrow__dot              # Decorative dot element
```

**Note on neutral**: The `neutral` variant is the default state. No modifier class is applied when `variant = 'neutral'`.

### Modifiers by Category

**Color Variants**
- Default (neutral) - No modifier class
- `ps-eyebrow--primary` - Brand primary (green #00915A) — Main actions, market news
- `ps-eyebrow--secondary` - Brand secondary (magenta #A12B66) — Secondary actions, blog articles
- `ps-eyebrow--accent` - Accent/Gold (#D1AE6E) — Premium features, featured properties
- `ps-eyebrow--muted` - Muted gray (#B5BCC9) — Subtle metadata like publication dates

**Size**
- `ps-eyebrow--small` - 12px text (compact, ideal for metadata)
- Default (medium) - 14px text, no modifier class

**Text Styles**
- `ps-eyebrow--uppercase` - Applied by default (text-transform: uppercase)
- `ps-eyebrow--bold` - Bold font weight (600)

**Decorations**
- `ps-eyebrow--with-line` - Shows decorative line before text (section dividers)
- `ps-eyebrow--with-dot` - Shows decorative dot before text (bullet point style)

## Design Tokens Used

### Layer 1: Root Tokens (Global)

#### Colors (Semantic)
- `--primary` - Primary variant (green #00915A - BNP official brand)
- `--secondary` - Secondary variant (magenta #A12B66 - BNP official brand)
- `--gold` - Gold accent (#D1AE6E - premium/featured content)
- `--text-secondary` - Neutral color (gray-500)
- `--text-disabled` - Muted variant (gray-400, subtle metadata)

#### Typography
- `--font-condensed` - Condensed font family for eyebrows (BNPP Sans Condensed)
- `--font-size-0` - Small size (12px)
- `--font-size-1` - Medium size (14px, default)
- `--font-weight-500` - Regular weight
- `--font-weight-600` - Bold weight
- `--tracking-wide` - Letter spacing (wider for uppercase)
- `--tracking-wider` - Letter spacing (even wider for emphasis uppercase)
- `--leading-tight` - Line height (1.2)

#### Spacing
- `--size-05` - Line height (0.125rem / 2px)
- `--size-2` - Gap between elements (0.5rem / 8px)
- `--size-3` - Icon size (0.75rem / 12px)
- `--size-8` - Small line width (2rem / 32px)
- `--size-10` - Default line width (2.5rem / 40px)

#### Borders
- `--border-size-1` - Small line height (1px)

### Layer 2: Component-Scoped Variables

The eyebrow component uses component-scoped variables for easy customization (Bootstrap 5 pattern):

```css
/* Layout */
--ps-eyebrow-gap: var(--size-2);
--ps-eyebrow-display: inline-flex;

/* Typography */
--ps-eyebrow-font-family: var(--font-condensed);
--ps-eyebrow-font-size: var(--font-size-1);
--ps-eyebrow-font-weight: var(--font-weight-500);
--ps-eyebrow-line-height: 1.2;
--ps-eyebrow-letter-spacing: var(--tracking-wide);

/* Colors */
--ps-eyebrow-color: var(--text-secondary);

/* Icon */
--ps-eyebrow-icon-size: var(--size-3);

/* Line decoration */
--ps-eyebrow-line-width: var(--size-10);
--ps-eyebrow-line-height: var(--size-05);
--ps-eyebrow-line-opacity: 0.3;

/* Dot decoration */
--ps-eyebrow-dot-size: 1.2em;
--ps-eyebrow-dot-opacity: 0.5;
```

#### Customization Examples

**Context Override:**
```css
/* Sidebar has different spacing and size */
.sidebar .ps-eyebrow {
  --ps-eyebrow-gap: var(--size-1);
  --ps-eyebrow-font-size: var(--font-size-0);
}
```

**Inline Override:**
```html
<span class="ps-eyebrow ps-eyebrow--primary" style="--ps-eyebrow-color: var(--secondary);">
  Custom Color Override
</span>
```

**JavaScript Override:**
```javascript
document.querySelector('.ps-eyebrow').style.setProperty(
  '--ps-eyebrow-color', 
  'var(--accent)'
);
```

## Accessibility

- **Semantics**: Uses neutral `<span>` element (appropriate for labels, not headings)
- **Decorative elements**: `aria-hidden="true"` applied to:
  - Icon (via `data-icon` attribute with `aria-hidden`)
  - Decorative line (`ps-eyebrow__line`)
  - Decorative dot (`ps-eyebrow__dot`)
- **Contrast**: All text colors meet WCAG AA standard (4.5:1 minimum for normal text)
- **No interactivity**: Eyebrow is a static label; no keyboard/focus requirements
- **Screen reader**: Text content is announced naturally; decorations are hidden
- **DOM Order**: Eyebrow placed above heading in DOM order (correct reading order)

## Real Estate Use Cases

### 1. Market News / Actualités Marché
**Primary variant** for market updates, announcements
```twig
{% include '@elements/eyebrow/eyebrow.twig' with {
  text: 'Actualité marché',
  variant: 'primary'
} %}
```

### 2. Featured Property / Bien Phare
**Accent (Gold) variant** with icon for premium/featured properties
```twig
{% include '@elements/eyebrow/eyebrow.twig' with {
  text: 'Bien phare',
  variant: 'accent',
  bold: true,
  icon: 'medal'
} %}
```

### 3. Blog Articles / Articles Blog
**Secondary variant** for blog content
```twig
{% include '@elements/eyebrow/eyebrow.twig' with {
  text: 'Article blog',
  variant: 'secondary',
  withDot: true
} %}
```

### 4. Publication Metadata / Métadonnées
**Muted variant** for dates, author, reading time (small size)
```twig
{% include '@elements/eyebrow/eyebrow.twig' with {
  text: 'Publié 5 décembre',
  variant: 'muted',
  size: 'small'
} %}
```

### 5. Section Headers / En-têtes de section
**Neutral variant** with decorative line for section breaks
```twig
{% include '@elements/eyebrow/eyebrow.twig' with {
  text: 'Nos services',
  variant: 'neutral',
  withLine: true,
  size: 'small'
} %}
```

### 6. Category Tags / Tags de catégorie
**Neutral variant** for property types or filters (small, no uppercase)
```twig
{% include '@elements/eyebrow/eyebrow.twig' with {
  text: 'Bureau',
  variant: 'neutral',
  size: 'small',
  uppercase: false
} %}
```

## Usage Examples

### Simple Text Label
```twig
{% include '@elements/eyebrow/eyebrow.twig' with {
  text: 'Nouvelle annonce',
  variant: 'primary'
} %}
```

### With Icon (Featured Content)
```twig
{% include '@elements/eyebrow/eyebrow.twig' with {
  text: 'Sélection',
  variant: 'accent',
  icon: 'medal',
  bold: true
} %}
```

### With Decorative Line (Section Separator)
```twig
{% include '@elements/eyebrow/eyebrow.twig' with {
  text: 'Portfolio immobilier',
  variant: 'neutral',
  withLine: true,
  size: 'small'
} %}
```

### Card Header with Date (Muted Metadata)
```html
<div class="card">
  <div style="margin-bottom: 12px;">
    {% include '@elements/eyebrow/eyebrow.twig' with {
      text: 'Publié 9 décembre',
      variant: 'muted',
      size: 'small'
    } %}
  </div>
  <h3>Titre de l'article</h3>
  <p>Contenu...</p>
</div>
```

### Multiple Categories (Tags)
```html
<div style="display: flex; gap: 8px;">
  {% include '@elements/eyebrow/eyebrow.twig' with {
    text: 'Bureau',
    variant: 'neutral',
    size: 'small',
    uppercase: false
  } %}
  {% include '@elements/eyebrow/eyebrow.twig' with {
    text: 'Île-de-France',
    variant: 'neutral',
    size: 'small',
    uppercase: false
  } %}
</div>
```

## CSS Classes Reference

All available CSS classes for the eyebrow component:

| Class | Type | Purpose |
|-------|------|---------|
| `.ps-eyebrow` | Block | Root container |
| `.ps-eyebrow--primary` | Modifier | Primary green color |
| `.ps-eyebrow--secondary` | Modifier | Secondary magenta color |
| `.ps-eyebrow--accent` | Modifier | Accent gold color (premium) |
| `.ps-eyebrow--muted` | Modifier | Muted gray color (subtle) |
| `.ps-eyebrow--small` | Modifier | Small size (12px) |
| `.ps-eyebrow--uppercase` | Modifier | Uppercase transformation |
| `.ps-eyebrow--bold` | Modifier | Bold font weight (600) |
| `.ps-eyebrow--with-line` | Modifier | Decorative line visible |
| `.ps-eyebrow--with-dot` | Modifier | Decorative dot visible |
| `.ps-eyebrow__icon` | Element | Icon placeholder |
| `.ps-eyebrow__text` | Element | Text content wrapper |
| `.ps-eyebrow__line` | Element | Decorative line |
| `.ps-eyebrow__dot` | Element | Decorative dot |

## Browser Compatibility

- Modern browsers (Chrome, Firefox, Safari, Edge)
- CSS variables (all modern browsers, IE 11 not supported)
- Flexbox layout (all modern browsers)
- `data-icon` attribute for icon system (all modern browsers)
### Basic Eyebrow
```twig
{% include '@elements/eyebrow/eyebrow.twig' with {
  text: 'News',
  variant: 'primary'
} %}
<h2>Latest Updates</h2>
```

### Date Label (Card Context)
```twig
{% include '@elements/eyebrow/eyebrow.twig' with {
  text: 'DATE',
  variant: 'muted',
  size: 'small',
  uppercase: true
} %}
<h3>Article Title</h3>
```

### With Decorative Line
```twig
{% include '@elements/eyebrow/eyebrow.twig' with {
  text: 'Our Services',
  variant: 'neutral',
  withLine: true,
  size: 'small'
} %}
<h2>What We Offer</h2>
```

### With Decorative Dot
```twig
{% include '@elements/eyebrow/eyebrow.twig' with {
  text: 'Blog',
  variant: 'secondary',
  withDot: true
} %}
<h2>Latest Articles</h2>
```

### With Icon
```twig
{% include '@elements/eyebrow/eyebrow.twig' with {
  text: 'Featured',
  variant: 'primary',
  icon: 'medal',
  bold: true
} %}
<h3>Premium Content</h3>
```

**Note**: Icons use the centralized `data-icon` attribute pattern. The icon name is passed WITHOUT the "icon-" prefix.

### Bold Variant
```twig
{% include '@elements/eyebrow/eyebrow.twig' with {
  text: 'Case Study',
  variant: 'accent',
  bold: true
} %}
<h2>Project Overview</h2>
```

### Lowercase (No Uppercase)
```twig
{% include '@elements/eyebrow/eyebrow.twig' with {
  text: 'Article',
  variant: 'primary',
  uppercase: false
} %}
<h2>Main Heading</h2>
```

## Common Use Cases

### 1. Page Hero Section
```twig
<section class="hero">
  {% include '@elements/eyebrow/eyebrow.twig' with {
    text: 'News',
    variant: 'primary',
    uppercase: true
  } %}
  <h1>Main Page Title</h1>
  <p>Introduction text...</p>
</section>
```

### 2. News Card with Date Label
```twig
<article class="card">
  {% include '@elements/eyebrow/eyebrow.twig' with {
    text: 'DATE',
    variant: 'muted',
    size: 'small'
  } %}
  <h3>News Article Title</h3>
  <p>Article summary...</p>
</article>
```

### 3. Study Card with Date Label
```twig
<article class="card">
  {% include '@elements/eyebrow/eyebrow.twig' with {
    text: 'DATE',
    variant: 'muted',
    size: 'small'
  } %}
  <h3>Study Title</h3>
  <p>Study description...</p>
</article>
```

### 4. Publication Card
```twig
<article class="card">
  {% include '@elements/eyebrow/eyebrow.twig' with {
    text: 'DATE',
    variant: 'muted',
    size: 'small'
  } %}
  <h3>Publication Title</h3>
  <p>Publication excerpt...</p>
</article>
```

### 5. Blog Section with Decorative Dot
```twig
<section>
  {% include '@elements/eyebrow/eyebrow.twig' with {
    text: 'Blog',
    variant: 'secondary',
    withDot: true
  } %}
  <h2>Latest Articles</h2>
</section>
```

### 6. Services Section with Line
```twig
<section>
  {% include '@elements/eyebrow/eyebrow.twig' with {
    text: 'Our Services',
    variant: 'neutral',
    withLine: true,
    size: 'small'
  } %}
  <h2>What We Offer</h2>
</section>
```

### 7. Featured Content with Icon
```twig
<article>
  {% include '@elements/eyebrow/eyebrow.twig' with {
    text: 'Featured',
    variant: 'primary',
    icon: 'medal',
    bold: true
  } %}
  <h3>Premium Content</h3>
</article>
```

## Accessibility

- **Semantic Element**: Uses `<span>` (not heading) to avoid disrupting document outline
- **DOM Order**: Always placed before the associated heading for logical screen reader flow
- **Decorative Elements**: Line, dot, and icon marked with `aria-hidden="true"` (purely visual)
- **Color Contrast**: All color tokens ensure WCAG AA compliance (minimum 4.5:1 contrast)
- **Non-interactive**: Eyebrows are purely informational and receive no keyboard focus
- **No Heading Role**: Eyebrows provide context but don't replace semantic heading structure

### Best Practices
- Always associate eyebrows with a heading (h1-h6) below
- Don't rely solely on color to convey meaning (use text labels)
- Ensure sufficient contrast when using custom background colors
- Place eyebrows in logical reading order before their associated headings

## Design Patterns

### Variant Usage
- **Primary**: Key sections, important announcements, featured content (green #00915A)
- **Secondary**: Blog posts, articles, alternative categories (magenta #A12B66)
- **Info**: Informational content, case studies, highlights (blue #2563EB)
- **Neutral**: General sections, standard categories (default - gray)
- **Muted**: Subtle labels like "DATE" in cards, less prominent context (light gray)

### Size Guidelines
- **Small (12px)**: Subtle labels, date indicators, secondary context
- **Medium (14px)**: Standard eyebrows for most use cases (default)

### Decoration Guidelines
- **No decoration**: Clean, minimal appearance (default)
- **With line**: Adds visual weight, good for section headers
- **With dot**: Subtle separation, works well for blog/article categories
- **With icon**: Emphasizes importance or category type (use sparingly)

## Best Practices

### DO ✅
- Keep text short (1-3 words maximum)
- Use uppercase for maximum visibility and recognition
- Place eyebrows directly above headings (h1-h6)
- Use muted variant with "DATE" text for date labels in cards
- Use consistent variants for similar content types
- Always use design tokens for colors and typography
- Maintain logical DOM order (eyebrow before heading)

### DON'T ❌
- Don't use long text (more than 3 words)—it diminishes impact
- Don't use eyebrows as headings—they provide context, not structure
- Don't place eyebrows after headings—they must come first
- Don't combine multiple decorations (line + dot + icon)—choose one or none
- Don't hardcode colors, sizes, or spacing values
- Don't use eyebrows without an associated heading below
- Don't rely on color alone to convey meaning

## Component Audit Checklist

- [x] Uses semantic `<span>` element (not heading)
- [x] All colors use design tokens (no hardcoded values)
- [x] All typography uses design tokens (`--font-*`)
- [x] All spacing uses design tokens (`--size-*`)
- [x] BEM methodology with `ps-` prefix
- [x] CSS uses PostCSS nesting syntax
- [x] Minimal markup (default props require no modifier classes)
- [x] Supports semantic variants (primary, secondary, info, neutral, muted)
- [x] Supports 2 size options (small, medium)
- [x] Text transformations (uppercase, bold) properly implemented
- [x] Optional decorations (line, dot, icon) marked `aria-hidden="true"`
- [x] Icons use `data-icon` attribute pattern (centralized system)
- [x] Non-interactive (no focus states)
- [x] Accessible color contrast (WCAG AA)
- [x] Placed before heading in DOM order
- [x] Storybook documentation complete with structured sections
- [x] All content in English
- [x] **Component-scoped variables implemented (Bootstrap 5 pattern)**
- [x] **3-layer variable cascade (Root → Component → Context)**
- [x] **Runtime customization support via CSS variables**
