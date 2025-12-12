---
title: Base Stories Standards
version: 1.0.0
lastUpdated: 2025-12-07
applyTo:
  - "source/patterns/base/**/*.twig"
  - "source/patterns/base/**/*.yml"
  - "source/patterns/base/**/*.stories.jsx"
priority: MEDIUM
related:
  - storybook.instructions.md
  - components.instructions.md
status: ACTIVE
---

# Base Stories Standards - PS Theme

**Scope**: Documentation stories for design tokens and utilities (`base/*`)

---

## 📁 File Structure

Base stories use a **simplified 4-file structure** (NO README.md):

```
source/patterns/base/{story}/
├── {story}.twig          # Twig template (documentation markup)
├── {story}.yml           # Default data / context
├── {story}.stories.jsx   # Storybook stories (NO tags: ['autodocs'])
└── {story}.css           # (Optional) If custom styling needed
```

❌ **DO NOT CREATE** `README.md` in `base/*` directories — these are documentation stories, not components.

---

## 📝 Storybook Stories Format

### Story Export Pattern

Base stories MUST follow this exact naming pattern:

```jsx
// ✅ CORRECT - Named export matches story name
import aspects from './aspects.twig';
import data from './aspects.yml';

const settings = {
  title: 'Base/Aspects',
};

const Aspects = {
  name: 'Aspect Ratios',
  render: (args) => aspects(args),
  args: { ...data },
};

export default settings;
export { Aspects };
```

```jsx
// ❌ WRONG - Using generic "Default" name
const Default = {
  name: 'Aspect Ratios',
  render: (args) => aspects(args),
  args: { ...data },
};

export default settings;
export { Default };  // ❌ Avoid generic names
```

**Rules**:
- ✅ Named export should match the story subject (e.g., `Aspects`, `Colors`, `Typography`)
- ✅ Use PascalCase for the constant name
- ❌ **DO NOT** use generic names like `Default`, `Story`, or `Base`
- ❌ **NO** `tags: ['autodocs']` in settings (base stories don't use Autodocs)

---

## 🎯 Purpose

Stories in `source/patterns/base/` document **design tokens and utilities** (colors, typography, spacing, backgrounds, etc.). They are NOT components with BEM classes or behaviors.

**Key Distinction**:
- ✅ `base/*` = Design system documentation (tokens, utilities)
- ✅ `elements/`, `components/`, `collections/` = Actual UI components (BEM, behaviors)

---

## 📐 Base Story Template

### Minimal Semantic Structure

All `base/*` stories SHOULD use the minimal template: `_base-story.twig`

**Template provides**:
- ✅ Semantic HTML structure (`<article>`, `<header>`, `<section>`)
- ✅ Consistent class names (`.story-title`, `.section-title`, `.section-intro`)
- ✅ Support for header + repeatable sections
- ❌ **NO CSS** (use existing Storybook styles)

### Usage Pattern

```twig
{#
/**
 * @file
 * [System Name] documentation
 */
#}

{# Include base template #}
{% include '../_base-story.twig' with {
  header: {
    title: 'System Title',
    description: 'Lead paragraph...',
    badge: 'Design Tokens',  // Optional
    meta: [                  // Optional
      { label: 'Total', value: '42' },
      { label: 'Source', value: 'props/colors.css' }
    ]
  },
  sections: [
    {
      badge: 'Category',      // Optional
      title: 'Section Title',
      description: 'Section intro...',  // Optional
      content: content_block  // HTML from {% set %}
    }
  ]
} only %}

{# Content blocks with existing Storybook classes #}
{% set content_block %}
  <div class="color-grid">
    {# Use classes from storybook.css #}
  </div>
{% endset %}
```

---

## 🎨 CSS Strategy: Use Existing Styles

### ❌ FORBIDDEN: Custom CSS in Base Stories

**NEVER create** custom `<style>` blocks in `base/*` stories:

```twig
{# ❌ WRONG - Custom CSS in story #}
<style>
  .my-custom-grid { ... }
  .my-special-card { ... }
</style>
```

**Why forbidden**:
- Creates style duplication
- Breaks design system consistency
- Makes maintenance harder
- Increases CSS bundle size unnecessarily

### ✅ REQUIRED: Reuse Existing Classes

**ALWAYS use** classes from these sources:

#### 1. Design Token Utilities (`source/props/`)
```html
<!-- Spacing, colors, typography from tokens -->
<div class="p-4 bg-gray-50 rounded-2">
  <h3 class="font-weight-700 text-primary">Title</h3>
</div>
```

#### 2. Utility Classes (`source/patterns/base/utilities/`)
```html
<!-- Background utilities -->
<div class="bg-primary text-white">Primary background</div>
<div class="bg-gradient-gold">Gold gradient</div>
```

#### 3. Storybook Demo Styles (`source/patterns/storybook.css`)

**Available classes** (already defined):
- `.demo-colors`, `.demo-brands` - Wrapper sections
- `.section-title` - Section headings (h2/h3)
- `.section-intro` - Section descriptions
- `.color-grid`, `.color-column` - Color palette grids
- `.theme-colors-grid`, `.theme-color-card` - Semantic color displays
- `.brand-grid`, `.brand-table` - Brand token tables
- `.demo-table` - Reference tables
- `.demo-code` - Code/token labels
- `.demo-notes` - Helper text
- `.typography-demo`, `.typography-section` - Typography displays
- `.demo-fonts` - Font family displays
- `.demo-shadows` - Shadow demonstrations
- `.demo-sizes` - Spacing/sizing displays

**Example** (Colors story):
```twig
<div class="color-grid">
  <div class="color-column">
    <div class="swatch-main" style="background-color: var(--primary)">
      <strong>--primary</strong>
    </div>
  </div>
</div>
```

#### 4. Bootstrap Utilities (Legacy - Minimize Use)
```html
<!-- Only when necessary, prefer tokens -->
<div class="d-flex gap-3 p-3">...</div>
```

---

## 📋 Workflow: Creating a Base Story

### Step 1: Analyze Existing Styles

Before creating markup, check what's available:

```bash
# Search for existing demo classes
grep -r "\.demo-" source/patterns/storybook.css
grep -r "\.section-" source/patterns/storybook.css

# Check similar stories
ls source/patterns/base/
```

### Step 2: Reuse or Request

**Option A: Reuse existing** (90% of cases)
- Use classes from `storybook.css` (`.demo-*`, `.section-*`)
- Use utility classes (`.bg-*`, spacing, typography)
- Compose with tokens (`var(--*)`)

**Option B: Add to storybook.css** (10% of cases)
- If truly new demo pattern needed
- Add to `storybook.css` with `.demo-*` prefix
- Document in comments
- Benefits ALL stories

**❌ Option C: Custom CSS** (NEVER for base stories)
- Not allowed in `base/*`
- Use for component-specific demos only

### Step 3: Structure with Template

```twig
{% include '@base/_base-story.twig' with {
  header: header_data,
  sections: sections_array
} only %}

{% set section_content %}
  {# Use existing classes here #}
  <div class="demo-grid">...</div>
{% endset %}
```

---

## 🔍 Examples

### Good Example: Colors Story

```twig
{# ✅ Uses template + existing Storybook classes #}
{% include '@base/_base-story.twig' with {
  header: {
    title: 'Color System',
    description: 'Complete color palette...'
  },
  sections: [...]
} only %}

{% set palette_section %}
  <div class="color-grid">
    {# Existing class from storybook.css #}
    <div class="color-column">
      <div class="swatch-main" style="background-color: var(--primary)">
        <strong>--primary</strong>
        #00915A
      </div>
    </div>
  </div>
{% endset %}
```

**Why good**:
- Uses `_base-story.twig` template
- Uses `.color-grid` from `storybook.css`
- Uses `var(--primary)` token
- No custom CSS

### Bad Example: Custom Styles

```twig
{# ❌ Creates custom CSS instead of reusing #}
<article class="my-custom-story">
  <h1>Colors</h1>
  <div class="my-special-grid">...</div>
</article>

<style>
  .my-custom-story { padding: 2rem; }
  .my-special-grid { 
    display: grid;
    grid-template-columns: repeat(4, 1fr);
  }
</style>
```

**Why bad**:
- Ignores template structure
- Creates duplicate CSS
- Uses magic numbers instead of tokens
- Not reusable by other stories

---

## 🚨 Validation Checklist

Before committing a base story:

- [ ] Uses `_base-story.twig` template (or has valid reason not to)
- [ ] **Zero** `<style>` blocks in `.twig` file
- [ ] All classes from: tokens, utilities, or `storybook.css`
- [ ] No hardcoded sizes/colors (use `var(--*)`)
- [ ] Checked existing stories for patterns first
- [ ] Markup is semantic HTML (`<article>`, `<section>`, `<header>`)
- [ ] Works with Storybook Autodocs (if applicable)

---

## 📚 Reference: Available Storybook Classes

### Layout
- `.demo-colors`, `.demo-brands`, `.demo-fonts`, `.demo-shadows`, `.demo-sizes`
- `.section-title` (h2/h3 for section headers)
- `.section-intro` (lead paragraph)

### Grids
- `.color-grid` - Multi-column color swatches
- `.color-column` - Single color family column
- `.theme-colors-grid` - Semantic color cards
- `.brand-grid` - Brand token display
- `.typography-demo__grid` - Typography samples

### Components
- `.swatch-main` - Primary color swatch (large)
- `.swatch-light` / `.swatch-dark` - Contrast variants
- `.theme-color-card` - Large semantic color display
- `.brand-table` - Token reference table
- `.demo-table` - General reference table
- `.demo-code` - Inline code/token display
- `.demo-notes` - Helper/caption text

### Typography
- `.typography-section` - Typography section wrapper
- `.typography-section__header` - Section header
- `.typography-section__badge` - Category badge
- `.typography-demo__item` - Individual type sample

---

## 🔗 Related Instructions

- **Storybook Format**: `storybook.instructions.md` - Story structure, argTypes
- **CSS Standards**: `css.instructions.md` - Token usage, nesting rules
- **Components**: `components.instructions.md` - For actual UI components (not base stories)

---

**Maintainers**: Design System Team  
**Last Review**: 2025-12-07
