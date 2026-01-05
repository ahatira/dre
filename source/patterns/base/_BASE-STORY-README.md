# Base Story Template

**File**: `_base-story.twig`  
**Version**: 2.0.0  
**Purpose**: Ready-to-use template for design system documentation stories

---

## 🎯 What It Provides

✅ **Complete Styling**: Professional design with design tokens  
✅ **Semantic HTML**: Accessible structure with proper landmarks  
✅ **Responsive Layout**: Mobile-first with breakpoints  
✅ **Consistent Classes**: `.ps-base-*` prefix for all elements  
✅ **Print Styles**: Optimized for documentation printing

---

## 📖 Usage

### Basic Example

```twig
{#
/**
 * @file
 * My System Documentation
 */
#}

{% include '../_base-story.twig' with {
  header: {
    title: 'Color System',
    description: 'Complete color palette with semantic tokens.',
    badge: 'Design Tokens',
    meta: [
      { label: 'Total Colors', value: '97' },
      { label: 'Categories', value: '9' },
      { label: 'Source', value: 'props/colors.css' }
    ]
  },
  sections: [
    {
      badge: 'Semantic',
      title: 'Brand Colors',
      description: 'Primary colors for UI components.',
      content: brand_colors_html
    },
    {
      title: 'Gray Scale',
      description: 'Neutral colors from white to black.',
      content: gray_scale_html
    }
  ]
} only %}

{# Define your content blocks #}
{% set brand_colors_html %}
  <div class="color-grid">
    {# Your custom markup using storybook.css classes #}
  </div>
{% endset %}

{% set gray_scale_html %}
  <div class="color-grid">
    {# Your custom markup #}
  </div>
{% endset %}
```

---

## 📋 Data Structure

### Header Object

```yaml
header:
  title: string (required)        # Main page title (h1)
  description: string (optional)  # Lead paragraph
  badge: string (optional)        # Category badge (e.g., "Design Tokens")
  meta: array (optional)          # Metadata items
    - label: string               # Label (e.g., "Total")
      value: string               # Value (e.g., "42")
```

### Sections Array

```yaml
sections:
  - badge: string (optional)      # Section badge
    title: string (optional)      # Section heading (h2)
    description: string (optional) # Section intro paragraph
    content: html (required)      # Raw HTML content
    class: string (optional)      # Custom CSS class
```

---

## 🎨 CSS Classes Reference

### Story Level
- `.ps-base-story` - Main container (max-width 1400px, centered)
- `.ps-base-story__header` - Story header wrapper

### Header Elements
- `.ps-story-badge` - Category badge (primary color, uppercase)
- `.ps-story-title` - Main title (h1, 48px desktop)
- `.ps-story-lead` - Lead paragraph (20px, secondary color)
- `.ps-story-meta` - Metadata grid
- `.ps-meta-item` - Individual meta (dt/dd pair)

### Section Elements
- `.ps-base-section` - Section container
- `.ps-section-header` - Section header wrapper
- `.ps-section-badge` - Section category badge (gray)
- `.ps-section-title` - Section heading (h2, 32px)
- `.ps-section-intro` - Section description
- `.ps-section-content` - Content wrapper

---

## 🎨 Design Tokens Used

### Spacing
- `--size-1` to `--size-12` - Margins, paddings, gaps

### Typography
- `--font-size-2` to `--font-size-17` - Text sizes
- `--font-weight-600`, `--font-weight-700` - Weights
- `--leading-tight`, `--leading-relaxed` - Line heights

### Colors
- `--primary-subtle`, `--primary-text-emphasis` - Badge colors
- `--text-primary`, `--text-secondary`, `--text-tertiary` - Text colors
- `--gray-50`, `--gray-100` - Backgrounds
- `--border-light` - Borders
- `--white` - Page background

### Borders & Radius
- `--radius-1`, `--radius-3`, `--radius-pill` - Border radius

---

## 📱 Responsive Behavior

### Desktop (≥768px)
- Story padding: `80px 64px`
- Title: `48px`
- Lead: `20px`
- Meta: Multi-column grid
- Section title: `32px`

### Mobile (<768px)
- Story padding: `64px 40px`
- Title: `32px`
- Lead: `16px`
- Meta: Single column
- Section title: `24px`

---

## ✅ Best Practices

### DO
✅ Use existing Storybook classes (`.color-grid`, `.demo-table`) for content  
✅ Leverage design tokens (`var(--*)`) in custom content  
✅ Keep sections focused (one concept per section)  
✅ Provide metadata for context (total items, source file)  
✅ Write descriptive section intros

### DON'T
❌ Add custom `<style>` blocks in story files (template has all styles)  
❌ Override `.ps-base-*` classes (use them as-is)  
❌ Use hardcoded values (colors, sizes) in content  
❌ Create deeply nested sections (keep it flat)  
❌ Mix different visual patterns (stay consistent)

---

## 🔍 Complete Example

See `source/patterns/base/example/` for a working demonstration.

**Files**:
- `example.yml` - Data structure
- `example.twig` - Template usage
- `example.stories.jsx` - Storybook configuration

**Storybook**: `Base/Example`

---

## 🛠️ Customization

### Adding Custom Classes

Use the `class` property on sections:

```twig
sections: [
  {
    title: 'Special Section',
    content: html_content,
    class: 'has-dark-background'  # Your custom class
  }
]
```

Then style in `storybook.css`:

```css
.ps-base-section.has-dark-background {
  background: var(--gray-900);
  color: var(--white);
  padding: var(--size-6);
  border-radius: var(--radius-3);
}
```

---

## 📚 Related Documentation

- **Storybook Standards**: `.github/instructions/storybook.instructions.md`
- **Base Stories Rules**: `.github/instructions/base-stories.instructions.md`
- **CSS Guidelines**: `.github/instructions/css.instructions.md`

---

**Maintainers**: Design System Team  
**Last Updated**: 2025-12-07
