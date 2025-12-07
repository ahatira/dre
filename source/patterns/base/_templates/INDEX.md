# Base Templates Directory

**Purpose**: Reusable component library for documentation stories

---

## 📂 Structure

```
_templates/
├── README.md                  # This file
├── components.css             # All component styles (import once)
├── color-column.twig          # Vertical color scale
├── color-cards.twig           # Large semantic color cards
├── token-table.twig           # Reference table
├── type-sample.twig           # Typography specimen
└── swatch-grid.twig           # Generic token swatches
```

---

## 🎯 Philosophy

Instead of duplicating CSS in every story, we provide:

1. **Base Template** (`_base-story.twig`) - Page structure with header + sections
2. **Component Library** (this folder) - Reusable UI components
3. **Single CSS File** (`components.css`) - All component styles

**Benefits**:
- ✅ Zero CSS duplication
- ✅ Consistent design across all stories
- ✅ Easy maintenance (update once, applies everywhere)
- ✅ Fast development (compose, don't create)

---

## 🚀 Quick Start

### 1. Basic Story Structure

```twig
{# Your story file: colors.twig #}

{% include '../_base-story.twig' with {
  header: { title, description, badge, meta },
  sections: [...]
} only %}

{% set section_content %}
  {# Use template components here #}
{% endset %}

<link rel="stylesheet" href="../_templates/components.css">
```

### 2. Use Components

```twig
{# Color cards #}
{% include '../_templates/color-cards.twig' with {
  colors: [{ name: 'Primary', slug: 'primary' }]
} only %}

{# Token table #}
{% include '../_templates/token-table.twig' with {
  headers: [...],
  rows: [...]
} only %}

{# Swatch grid #}
{% include '../_templates/swatch-grid.twig' with {
  items: [...],
  columns: 3
} only %}
```

---

## 📚 Components Reference

| Component | File | Purpose | Docs |
|-----------|------|---------|------|
| Color Column | `color-column.twig` | Vertical color scale | Full color palettes |
| Color Cards | `color-cards.twig` | Large semantic cards | Theme colors |
| Token Table | `token-table.twig` | Reference table | Any token list |
| Type Sample | `type-sample.twig` | Typography display | Font specimens |
| Swatch Grid | `swatch-grid.twig` | Generic swatches | Shadows, sizes, etc. |

**Detailed docs**: See `README.md` in same folder

---

## 🎨 Design Principles

All components follow:

1. **Token-First**: 100% design tokens, zero hardcoded values
2. **Prefixed Classes**: `.ps-*` to avoid conflicts
3. **Semantic HTML**: Proper landmarks and ARIA
4. **Responsive**: Mobile-first with breakpoints
5. **Accessible**: WCAG 2.2 AA compliant

---

## ✅ Example Story

See `base/example/` for complete demonstration:

```twig
{% include '../_base-story.twig' with {
  header: {
    title: 'Color System',
    badge: 'Design Tokens'
  },
  sections: [
    {
      title: 'Semantic Colors',
      content: color_cards
    }
  ]
} only %}

{% set color_cards %}
  {% include '../_templates/color-cards.twig' with {
    colors: [
      { name: 'Primary', slug: 'primary' },
      { name: 'Success', slug: 'success' }
    ]
  } only %}
{% endset %}

<link rel="stylesheet" href="../_templates/components.css">
```

---

## 🔧 Customization

### Adding New Components

1. Create `my-component.twig` in `_templates/`
2. Add styles to `components.css` with `.ps-my-*` prefix
3. Document in component README
4. Update this index

### Modifying Existing Components

1. Edit component `.twig` file (props structure)
2. Update styles in `components.css`
3. Test in `base/example/`
4. Update component README

---

## 📦 Import Strategy

**Option A: Per Story** (recommended)
```twig
<link rel="stylesheet" href="../_templates/components.css">
```

**Option B: Global** (if used in >50% stories)
Add to `.storybook/preview-head.html`:
```html
<link rel="stylesheet" href="/source/patterns/base/_templates/components.css">
```

---

## 🚨 Rules

### DO
✅ Use template components for all new stories  
✅ Import `components.css` once per story  
✅ Follow component prop structure exactly  
✅ Use design tokens in custom content  
✅ Test in multiple browsers

### DON'T
❌ Create custom CSS in story files  
❌ Modify component styles inline  
❌ Use hardcoded values  
❌ Override `.ps-*` classes  
❌ Create duplicate components

---

## 📖 Related Docs

- **Base Story Template**: `../_base-story.twig` + `_BASE-STORY-README.md`
- **Instructions**: `.github/instructions/base-stories.instructions.md`
- **CSS Guidelines**: `.github/instructions/css.instructions.md`

---

**Version**: 1.0.0  
**Maintainers**: Design System Team  
**Last Updated**: 2025-12-07
