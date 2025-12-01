# Heading Component

Semantic typographic headings (h1–h6) with color and weight variants. Uses component-scoped CSS variables (3-layer system) for easy customization. Provides proper document structure while supporting visual customization.

## Component Props

| Prop | Type | Options | Default | Description |
|------|------|---------|---------|-------------|
| `text` | string | any | - | Heading text content (required) |
| `level` | string | `'h1'`, `'h2'`, `'h3'`, `'h4'`, `'h5'`, `'h6'` | `'h1'` | Semantic heading level |
| `color` | string | `'default'`, `'primary'`, `'secondary'`, `'success'`, `'warning'`, `'danger'`, `'info'` | `'default'` | Semantic color variant |
| `weight` | string | `'light'`, `'regular'`, `'bold'`, `'extra'` | `'bold'` | Font weight |
| `align` | string | `'left'`, `'center'`, `'right'` | `'left'` | Text alignment |
| `visuallyHidden` | boolean | `true`, `false` | `false` | Hide visually but keep for screen readers |
| `attributes` | object | any | - | Additional HTML attributes |

## Heading Hierarchy & Typography Scale

| Level | Font Size | Line Height | Default Weight | Use Case |
|-------|-----------|-------------|----------------|----------|
| **h1** | 48px | 1.25 | Bold (700) | Main page title (once per page) |
| **h2** | 36px | 1.25 | Bold (700) | Major section headings |
| **h3** | 28px | 1.375 | Bold (700) | Subsection titles |
| **h4** | 24px | 1.375 | Bold (700) | Content block titles |
| **h5** | 20px | 1.5 | Semi-bold (600) | Small headings |
| **h6** | 16px | 1.5 | Semi-bold (600) | Micro titles (uppercase + wide tracking) |

## BEM Structure

```
ps-heading                       # Root element (<h1>-<h6>)
├── ps-heading--h2               # Level modifiers (h2-h6)
├── ps-heading--h3
├── ps-heading--h4
├── ps-heading--h5
├── ps-heading--h6
├── ps-heading--primary          # Color variants
├── ps-heading--secondary
├── ps-heading--success
├── ps-heading--warning
├── ps-heading--danger
├── ps-heading--info
├── ps-heading--light            # Weight variants
├── ps-heading--regular
├── ps-heading--extra
├── ps-heading--align-center     # Alignment
├── ps-heading--align-right
├── ps-heading--visually-hidden  # Screen reader only
│
└── ps-heading__text             # Text content wrapper
```

### Modifiers by Category

**Level** (default: h1)
- No modifier for h1
- `ps-heading--h2` through `ps-heading--h6` for other levels

**Color** (default: default/gray)
- No modifier for default
- `ps-heading--primary` - Brand primary (green)
- `ps-heading--secondary` - Brand secondary (purple)
- `ps-heading--success` - Success green
- `ps-heading--warning` - Warning yellow/orange
- `ps-heading--danger` - Danger red
- `ps-heading--info` - Info blue

**Weight** (default: bold)
- `ps-heading--light` - Font weight 300
- `ps-heading--regular` - Font weight 400
- No modifier for bold (700)
- `ps-heading--extra` - Font weight 800

**Alignment** (default: left)
- No modifier for left
- `ps-heading--align-center`
- `ps-heading--align-right`

**Special States**
- `ps-heading--with-icon` - Applied when icon prop is provided
**Special States**
- `ps-heading--visually-hidden` - Accessible but visually hidden
## Design Tokens (3-Layer System)

### Component-Scoped Variables (Layer 2 - Customizable)

These can be overridden in context without modifying the CSS:

```css
--ps-heading-margin-bottom     /* Default: var(--size-6) - 24px */
--ps-heading-color              /* Default: var(--gray-900) */
--ps-heading-font-family        /* Default: var(--font-sans) */
--ps-heading-font-weight        /* Default: var(--font-weight-700) */
--ps-heading-font-size          /* Default: var(--font-size-10) for h1 */
--ps-heading-line-height        /* Default: var(--leading-tight) for h1 */
--ps-heading-text-align         /* Default: left */
```

**Example - Override in context:**
```css
.sidebar .ps-heading {
  --ps-heading-font-size: var(--font-size-6);
  --ps-heading-color: var(--gray-700);
}
```

### Base Tokens Referenced (Layer 1)

#### Typography
- `--font-size-10` (48px) - h1
- `--font-size-8` (36px) - h2
- `--font-size-6` (28px) - h3
- `--font-size-5` (24px) - h4
- `--font-size-3` (20px) - h5
- `--font-size-1` (16px) - h6

#### Line Heights
- `--leading-tight` (1.25) - h1, h2
- `--leading-snug` (1.375) - h3, h4
- `--leading-normal` (1.5) - h5, h6

#### Font Weights
- `--font-weight-300` - Light
- `--font-weight-400` - Regular
- `--font-weight-600` - Semi-bold (h5/h6 default)
- `--font-weight-700` - Bold (default)
- `--font-weight-800` - Extra

#### Semantic Colors (from semantic.css)
- `--gray-900` - Default text color
- `--primary` - Primary variant (brand green)
- `--secondary` - Secondary variant (brand purple)
- `--success` - Success green
- `--warning` - Warning yellow
- `--danger` - Danger red
- `--info` - Info blue

#### Spacing
- `--size-6` (24px) - Bottom margin

#### Font Family
- `--font-sans` - BNPP Sans

#### Special (h6)
- `--tracking-wide` - Letter spacing for uppercase h6
## Usage Examples

### Default h1
```twig
{% include '@elements/heading/heading.twig' with {
  text: 'Main Page Title'
} %}
```

### h2 Section Title
```twig
{% include '@elements/heading/heading.twig' with {
  text: 'Section Heading',
  level: 'h2'
} %}
```

### Colored h3
```twig
{% include '@elements/heading/heading.twig' with {
  text: 'Important Section',
  level: 'h3',
  color: 'primary'
} %}
```

### Light Weight h4
```twig
{% include '@elements/heading/heading.twig' with {
  text: 'Subtle Heading',
  level: 'h4',
  weight: 'light'
} %}
```

### Centered h2
```twig
{% include '@elements/heading/heading.twig' with {
  text: 'Centered Title',
  level: 'h2',
  align: 'center'
} %}
```

### Visually Hidden (Accessibility)
```twig
{% include '@elements/heading/heading.twig' with {
  text: 'Navigation',
  level: 'h2',
  visuallyHidden: true
} %}
```

### Combined Props
```twig
{% include '@elements/heading/heading.twig' with {
  text: 'Featured Property',
  level: 'h2',
  color: 'primary',
  weight: 'extra',
  align: 'center'
} %}
```

## Common Use Cases

### 1. Page Structure Hierarchy
```twig
<main>
  {% include '@elements/heading/heading.twig' with {
    text: 'Real Estate Services',
    level: 'h1',
    color: 'primary'
  } %}
  
  <section>
    {% include '@elements/heading/heading.twig' with {
      text: 'Our Properties',
      level: 'h2'
    } %}
    
    <article>
      {% include '@elements/heading/heading.twig' with {
        text: 'Luxury Apartments',
        level: 'h3',
        weight: 'regular'
      } %}
    </article>
  </section>
</main>
```

### 2. Semantic Status Messages
```twig
{# Success message #}
{% include '@elements/heading/heading.twig' with {
  text: 'Operation Successful',
  level: 'h3',
  color: 'success'
} %}

{# Warning notice #}
{% include '@elements/heading/heading.twig' with {
  text: 'Important Notice',
  level: 'h3',
  color: 'warning'
} %}

{# Danger alert #}
{% include '@elements/heading/heading.twig' with {
  text: 'Critical Error',
  level: 'h3',
  color: 'danger'
} %}
```

### 3. Centered Hero Section
```twig
<section class="hero">
  {% include '@elements/heading/heading.twig' with {
    text: 'Welcome to BNP Paribas Real Estate',
    level: 'h1',
    align: 'center',
    color: 'primary'
  } %}
</section>
```

### 4. Weight Variations
```twig
{% include '@elements/heading/heading.twig' with {
  text: 'Bold Section Title',
  level: 'h2',
  weight: 'bold'
} %}

{% include '@elements/heading/heading.twig' with {
  text: 'Light Section Title',
  level: 'h2',
  weight: 'light'
} %}
```

## Accessibility

### Document Structure
- **Heading hierarchy** is critical for screen readers and keyboard navigation
- Headings create an outline that assistive technologies use for navigation
- Users can jump between headings to quickly scan content
- Logical hierarchy: h1 → h2 → h3 (don't skip levels)

### Best Practices
- **One h1 per page**: Establishes the main topic/purpose
- **Don't skip levels**: Going from h2 to h4 breaks structure
- **Use semantic levels**: Choose based on document structure, not visual appearance
- **Visual styling separate**: Use color/weight props for visual hierarchy without changing level

### Visually Hidden Option
- `visuallyHidden: true` removes visual presence but preserves semantic structure
- Useful for structural headings that don't need visual display
- Content remains in accessibility tree and keyboard navigation
- Example: "Navigation" heading for a nav section

### Color Contrast
- All color tokens meet WCAG AA standards (4.5:1 minimum for text)
- Don't rely on color alone to convey meaning (combine with text/icons)

## Responsive Behavior

- Typography scales via design tokens
- Font sizes, line heights, and spacing maintain proportions across breakpoints
- Adjust tokens at global level for responsive typography
- Component inherits token-based responsive behavior

## Best Practices

### DO ✅
- Use h1 once per page for the main title
- Maintain logical heading hierarchy (h1 → h2 → h3)
- Choose heading level based on semantic structure, not visual appearance
- Use color variants to emphasize importance (primary for key sections)
- Use weight variants to create visual hierarchy within same level
- Use visuallyHidden for structural headings that don't need display
- Use component-scoped CSS variables for contextual customization
- Always use design tokens for styling

### DON'T ❌
- Don't skip heading levels (h1 → h3)—it breaks document structure
- Don't use multiple h1 elements on the same page
- Don't choose heading level based on desired size (use weight/color instead)
- Don't hardcode font sizes, colors, or weights
- Don't use headings for non-heading content (use styled paragraphs instead)
- Don't rely on color alone to convey meaning

## Component Audit Checklist

- [x] Uses semantic HTML heading elements (h1–h6)
- [x] Uses semantic HTML heading elements (h1–h6)
- [x] Component-scoped CSS variables (3-layer system)
- [x] All typography uses design tokens (`--font-*`)
- [x] All colors use semantic tokens (`--primary`, `--gray-900`, etc.)
- [x] All spacing uses design tokens (`--size-*`)
- [x] BEM methodology with `ps-` prefix
- [x] CSS uses PostCSS nesting syntax
- [x] Minimal markup (default h1 requires no modifier classes)
- [x] Supports 6 semantic levels (h1–h6) with proper hierarchy
- [x] Supports 7 semantic colors (default, primary, secondary, success, warning, danger, info)
- [x] Supports 4 font weights (light, regular, bold, extra)
- [x] Supports 3 text alignments (left, center, right)
- [x] visuallyHidden option for accessibility
- [x] Proper document outline maintained
- [x] Color contrast meets WCAG AA standards
- [x] Storybook documentation complete with Autodocs
- [x] All content in English