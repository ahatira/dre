# Heading Component

Semantic typographic headings (h1–h6) with color and weight variants. Provides proper document structure while supporting visual customization through design tokens.

## Component Props

| Prop | Type | Options | Default | Description |
|------|------|---------|---------|-------------|
| `text` | string | any | - | Heading text content (required) |
| `level` | string | `'h1'`, `'h2'`, `'h3'`, `'h4'`, `'h5'`, `'h6'` | `'h1'` | Semantic heading level |
| `color` | string | `'default'`, `'primary'`, `'secondary'`, `'success'`, `'warning'`, `'danger'`, `'info'` | `'default'` | Semantic color variant |
| `weight` | string | `'light'`, `'regular'`, `'bold'`, `'extra'` | `'bold'` | Font weight |
| `align` | string | `'left'`, `'center'`, `'right'` | `'left'` | Text alignment |
| `icon` | string | icon class | `''` | Optional icon class (e.g., 'icon-pin-map') |
| `iconPosition` | string | `'left'`, `'right'` | `'left'` | Icon position relative to text |
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
├── ps-heading--with-icon        # Icon presence
├── ps-heading--visually-hidden  # Screen reader only
│
├── ps-heading__text             # Text content wrapper
└── ps-heading__icon             # Icon element (decorative)
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
- `ps-heading--visually-hidden` - Accessible but visually hidden

## Design Tokens Used

### Typography
- `--ps-heading-h1-size` (fallback `--font-size-10`) - 48px
- `--ps-heading-h2-size` (fallback `--font-size-8`) - 36px
- `--ps-heading-h3-size` (fallback `--font-size-6`) - 28px
- `--ps-heading-h4-size` (fallback `--font-size-5`) - 24px
- `--ps-heading-h5-size` (fallback `--font-size-3`) - 20px
- `--ps-heading-h6-size` (fallback `--font-size-1`) - 16px

### Line Heights
- `--leading-tight` (1.25) - h1, h2
- `--leading-snug` (1.375) - h3, h4
- `--leading-normal` (1.5) - h5, h6

### Font Weights
- `--font-weight-300` - Light
- `--font-weight-400` - Regular
- `--font-weight-600` - Semi-bold (h5/h6 default)
- `--ps-font-weight-bold` (fallback `--font-weight-700`) - Bold (default)
- `--font-weight-800` - Extra

### Colors
- `--ps-color-text` (fallback `--gray-900`) - Default text color
- `--brand-primary` - Primary variant
- `--brand-secondary` - Secondary variant
- `--btn-success` (fallback `--green-600`) - Success
- `--btn-warning` (fallback `--yellow-500`) - Warning
- `--btn-danger` (fallback `--red-600`) - Danger
- `--btn-info` (fallback `--blue-600`) - Info

### Spacing
- `--ps-spacing-6` (fallback `--size-6`) - Bottom margin (24px)
- `--size-2` (8px) - Icon gap

### Font Family
- `--font-sans` - BNPP Sans

### Special (h6)
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

### h3 with Left Icon
```twig
{% include '@elements/heading/heading.twig' with {
  text: 'Location',
  level: 'h3',
  icon: 'icon-pin-map'
} %}
```

### h3 with Right Icon
```twig
{% include '@elements/heading/heading.twig' with {
  text: 'View Details',
  level: 'h3',
  icon: 'icon-arrow-right',
  iconPosition: 'right'
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
  align: 'center',
  icon: 'icon-offices'
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

### 2. Content Cards with Icons
```twig
<div class="card">
  {% include '@elements/heading/heading.twig' with {
    text: 'Contact Us',
    level: 'h3',
    icon: 'icon-phone',
    color: 'primary'
  } %}
  <p>Get in touch with our team...</p>
</div>
```

### 3. Semantic Status Messages
```twig
{# Success message #}
{% include '@elements/heading/heading.twig' with {
  text: 'Operation Successful',
  level: 'h3',
  color: 'success',
  icon: 'icon-check'
} %}

{# Warning notice #}
{% include '@elements/heading/heading.twig' with {
  text: 'Important Notice',
  level: 'h3',
  color: 'warning',
  icon: 'icon-infos'
} %}
```

### 4. Centered Hero Section
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

### 5. Property Search Section
```twig
{% include '@elements/heading/heading.twig' with {
  text: 'Property Search',
  level: 'h2',
  icon: 'icon-search',
  color: 'primary'
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

### Icons
- Icons marked with `aria-hidden="true"` (decorative only)
- Icon presence doesn't convey essential information
- Text content always present for screen readers

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
- Add icons to enhance meaning (location, events, actions)
- Use visuallyHidden for structural headings that don't need display
- Always use design tokens for styling

### DON'T ❌
- Don't skip heading levels (h1 → h3)—it breaks document structure
- Don't use multiple h1 elements on the same page
- Don't choose heading level based on desired size (use weight/color instead)
- Don't hardcode font sizes, colors, or weights
- Don't use headings for non-heading content (use styled paragraphs instead)
- Don't combine multiple icons in one heading
- Don't rely on color alone to convey meaning

## Component Audit Checklist

- [x] Uses semantic HTML heading elements (h1–h6)
- [x] All typography uses design tokens (`--ps-heading-*`, `--font-*`)
- [x] All colors use design tokens (no hardcoded values)
- [x] All spacing uses design tokens (`--ps-spacing-*`, `--size-*`)
- [x] BEM methodology with `ps-` prefix
- [x] CSS uses PostCSS nesting syntax
- [x] Minimal markup (default h1 requires no modifier classes)
- [x] Supports 6 semantic levels (h1–h6) with proper hierarchy
- [x] Supports 7 semantic colors (default, primary, secondary, success, warning, danger, info)
- [x] Supports 4 font weights (light, regular, bold, extra)
- [x] Supports 3 text alignments (left, center, right)
- [x] Optional icon support with left/right positioning
- [x] Icons marked `aria-hidden="true"` (decorative)
- [x] visuallyHidden option for accessibility
- [x] Proper document outline maintained
- [x] Color contrast meets WCAG AA standards
- [x] Storybook documentation complete with structured sections
- [x] All content in English
