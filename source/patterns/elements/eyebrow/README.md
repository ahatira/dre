# Eyebrow Component

A contextual label or kicker placed above a heading to provide category or context. Typically short, uppercase text in a semantic color.

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
├── ps-eyebrow--primary          # Primary color variant
├── ps-eyebrow--secondary        # Secondary color variant
├── ps-eyebrow--accent           # Accent color variant
├── ps-eyebrow--muted            # Muted color variant (subtle)
├── ps-eyebrow--small            # Small size (12px)
├── ps-eyebrow--uppercase        # Uppercase text transformation
├── ps-eyebrow--bold             # Bold font weight
├── ps-eyebrow--with-line        # Has decorative line
├── ps-eyebrow--with-dot         # Has decorative dot
│
├── ps-eyebrow__icon             # Icon element (decorative)
├── ps-eyebrow__text             # Text content wrapper
├── ps-eyebrow__line             # Decorative line element
└── ps-eyebrow__dot              # Decorative dot element
```

### Modifiers by Category

**Color Variants**
- Default (neutral) - No modifier class
- `ps-eyebrow--primary` - Brand primary (green)
- `ps-eyebrow--secondary` - Brand secondary (purple)
- `ps-eyebrow--accent` - Accent gold
- `ps-eyebrow--muted` - Muted gray (for subtle labels like "DATE")

**Size**
- `ps-eyebrow--small` - 12px text
- Default (medium) - 14px text, no modifier class

**Text Styles**
- `ps-eyebrow--uppercase` - Applied by default (text-transform: uppercase)
- `ps-eyebrow--bold` - Bold font weight (600)

**Decorations**
- `ps-eyebrow--with-line` - Shows decorative line before text
- `ps-eyebrow--with-dot` - Shows decorative dot before text

## Design Tokens Used

### Colors
- `--brand-primary` - Primary variant (green)
- `--brand-secondary` - Secondary variant (purple)
- `--accent-gold` - Accent variant (gold)
- `--ps-color-neutral-600` (fallback `--gray-600`) - Default neutral color
- `--ps-color-neutral-500` (fallback `--gray-500`) - Muted variant color

### Typography
- `--font-size-00` (12px) - Small size
- `--font-size-0` (14px) - Medium size (default)
- `--font-weight-regular` (400) - Regular weight
- `--font-weight-semibold` (600) - Bold weight
- `--line-height-1` (20px) - Line height
- `--font-sans` - Font family (BNPP Sans)

### Spacing
- `--size-2` (8px) - Gap for decorative elements
- `--size-3` (12px) - Bottom margin (spacing below eyebrow)

## Usage Examples

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
- **Primary**: Key sections, important announcements, featured content
- **Secondary**: Blog posts, articles, alternative categories
- **Accent**: Special promotions, case studies, highlighted content
- **Neutral**: General sections, standard categories (default)
- **Muted**: Subtle labels like "DATE" in cards, less prominent context

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
- [x] Supports 5 semantic variants (primary, secondary, accent, neutral, muted)
- [x] Supports 2 size options (small, medium)
- [x] Text transformations (uppercase, bold) properly implemented
- [x] Optional decorations (line, dot, icon) marked `aria-hidden="true"`
- [x] Icons use `data-icon` attribute pattern
- [x] Non-interactive (no focus states)
- [x] Accessible color contrast (WCAG AA)
- [x] Placed before heading in DOM order
- [x] Storybook documentation complete with structured sections
- [x] All content in English
