# Component Development - PS Theme

**Version**: 4.0.0  
**Last Updated**: 2025-12-12  
**Status**: ACTIVE

---

## 📖 Purpose

This document provides the **complete workflow** for creating a component from scratch (spec → implementation → commit). Follow this guide step-by-step to ensure quality and consistency.

---

## 🎯 Pre-Development Phase

### Step 1: Read Specification

**Locate spec file**:
```bash
docs/design/{level}/{component}.md
```

Where `{level}` is one of:
- `atoms/` (elements/)
- `molecules/` (components/)
- `organisms/` (collections/)
- `templates/` (layouts/)
- `pages/` (pages/)

**Extract key information**:
- Component name, category, description
- Props (required vs optional)
- Variants (sizes, colors, states)
- BEM structure
- Design tokens used
- Accessibility requirements
- Dependencies (other components to include)

---

### Step 2: Verify Dependencies

**Check if component reuses atoms**:

```bash
# Search for `{% include %}` in spec examples
grep -r "{% include" docs/design/{level}/{component}.md
```

**If molecule/organism, verify atoms exist**:

```bash
# Example: Alert (molecule) depends on icon (atom)
ls source/patterns/elements/icon/
# Should contain: icon.twig, icon.css, icon.yml, icon.stories.jsx, README.md
```

**If missing dependency, STOP and create atom first** (composition before creation).

---

### Step 3: Token Verification

**Check if required tokens exist**:

```bash
# Example: Component needs --size-badge
grep -r "--size-badge" source/props/
```

**If token missing**:
1. **DO NOT add token yourself**
2. Document the missing token in component notes
3. See **05-maintenance.md** (Token Creation Process)
4. Wait for token approval before continuing

**Common token files**:
- `source/props/colors.css` - Semantic colors
- `source/props/sizes.css` - Spacing and sizing
- `source/props/fonts.css` - Typography
- `source/props/borders.css` - Border widths and radii
- `source/props/shadows.css` - Box shadows
- `source/props/animations.css` - Durations

---

## 🏗️ Implementation Phase

### Step 4: Create 5 Required Files

**Use script to scaffold**:

```bash
npm run generate:pattern
# Prompts:
# - Pattern type: element | component | collection | layout | page
# - Pattern name: badge
```

**Or manually create**:

```
source/patterns/{level}/{component}/
├── {component}.twig
├── {component}.css
├── {component}.yml
└── {component}.stories.jsx
```

---

### Step 5: Implement Twig Template

**Header comment with params**:

```twig
{#
/**
 * @file
 * Badge component
 * 
 * Small label for statuses, categories, counts, or metadata.
 * 
 * @param {string} text - Badge label content (required)
 * @param {string} [variant='default'] - Color variant
 * @param {string} [size='md'] - Size variant
 * @param {boolean} [pill=false] - Pill shape
 * @param {string} [icon] - Optional icon name
 * @param {object} [attributes] - Additional HTML attributes
 */
#}

{% set variant = variant|default('default') %}
{% set size = size|default('md') %}
{% set pill = pill|default(false) %}
{% set icon = icon|default(null) %}

{% set classes = [
  'ps-badge',
  variant != 'default' ? 'ps-badge--' ~ variant : null,
  size != 'md' ? 'ps-badge--' ~ size : null,
  pill ? 'ps-badge--pill' : null,
  modifier_class
] %}

<span class="{{ classes|join(' ')|trim }}"{{ attributes|default('') }}>
  {% if icon %}
    {% include '@elements/icon/icon.twig' with {
      icon: icon,
      size: 'xs',
    } only %}
  {% endif %}
  
  {% if text %}
    <span class="ps-badge__text">{{ text }}</span>
  {% endif %}
</span>
```

**Key rules**:
- ✅ Default values with `|default()`
- ✅ Ternary with `null`: `condition ? 'class' : null`
- ❌ NEVER arrow functions: `filter(v => v)` (Drupal incompatible)
- ✅ Use `{% include %}` with `only` for composition
- ✅ Real Estate vocabulary in text content (property, listing, agent - NOT lorem ipsum)
- ✅ **MANDATORY**: `attributes` parameter with `|without('class')` for Drupal integration

**attributes pattern**:
```twig
{# In header comment #}
 * @param {object} [attributes] - Additional HTML attributes

{# In root element #}
<span class="{{ classes|join(' ')|trim }}"
  {%- if attributes %} {{ attributes|without('class') }}{% endif -%}
>
```

**See 03-technical-implementation.md → Twig Standards (section 2.5) for complete `attributes` documentation.**

---

### Step 6: Implement CSS with Nesting

**Structure**:

```css
.ps-badge {
  /* ═══ Component-scoped variables (Layer 2) ═══ */
  --ps-badge-padding-x: var(--size-2);
  --ps-badge-padding-y: var(--size-1);
  --ps-badge-font-size: var(--font-size-0);
  --ps-badge-bg: var(--gray-100);
  --ps-badge-color: var(--gray-900);
  
  /* ═══ Base styles ═══ */
  display: inline-flex;
  align-items: center;
  gap: var(--size-1);
  padding: var(--ps-badge-padding-y) var(--ps-badge-padding-x);
  border-radius: var(--radius-1);
  font-size: var(--ps-badge-font-size);
  font-weight: var(--font-weight-6);
  background: var(--ps-badge-bg);
  color: var(--ps-badge-color);
  
  /* ═══ Elements ═══ */
  &__text {
    line-height: var(--leading-tight);
  }
  
  /* ═══ Size modifiers ═══ */
  &--sm {
    --ps-badge-padding-x: var(--size-1);
    --ps-badge-padding-y: var(--size-0);
    --ps-badge-font-size: var(--font-size--1);
  }
  
  &--lg {
    --ps-badge-padding-x: var(--size-3);
    --ps-badge-padding-y: var(--size-2);
    --ps-badge-font-size: var(--font-size-1);
  }
  
  /* ═══ Color modifiers (semantic) ═══ */
  &--primary {
    --ps-badge-bg: var(--primary-subtle);
    --ps-badge-color: var(--primary);
  }
  
  &--success {
    --ps-badge-bg: var(--success-subtle);
    --ps-badge-color: var(--success-dark);
  }
  
  &--danger {
    --ps-badge-bg: var(--danger-subtle);
    --ps-badge-color: var(--danger-dark);
  }
  
  /* ═══ Other modifiers ═══ */
  &--pill {
    border-radius: var(--radius-round);
  }
}
```

**Critical rules**:
- ✅ Use `&` nesting syntax (postcss-nested)
- ✅ Order: Variables → Base → Elements → Modifiers → States
- ✅ ALL values from tokens (NO hardcoded values)
- ✅ Component-scoped variables (Layer 2 system)

**See 03-technical-implementation.md → CSS Standards for complete reference.**

---

### Step 7: Create YAML with Realistic Data

**Use Real Estate vocabulary in content** (property, listing, agent - NOT generic placeholders):

```yaml
text: "New Listing"
variant: "primary"
size: "md"
pill: false
icon: "home"
```

**For molecules with included atoms**:

```yaml
heading:
  text: "Luxury Apartment for Sale"
  level: 2

description: "Modern 3-bedroom apartment with stunning city views."

cta:
  text: "Schedule Tour"
  variant: "primary"
  href: "/properties/luxury-apartment"
```

---

### Step 8: Create Storybook Stories

**Import and render**:

```jsx
import componentTwig from './component.twig';
import componentData from './component.yml';

export default {
  title: 'Elements/Badge',
  tags: ['autodocs'], // MANDATORY
  parameters: {
    docs: {
      description: {
        component: 'Small label for statuses, categories, counts.',
      },
    },
  },
  render: (args) => componentTwig(args),
  argTypes: {
    // Content
    text: {
      control: 'text',
      description: 'Badge label',
      table: { category: 'Content' },
    },
    
    // Appearance
    variant: {
      control: 'select',
      options: ['default', 'primary', 'secondary', 'success', 'danger'],
      description: 'Color variant',
      table: { 
        category: 'Appearance',
        defaultValue: { summary: 'default' }
      },
    },
    
    size: {
      control: 'select',
      options: ['sm', 'md', 'lg'],
      description: 'Size variant',
      table: { 
        category: 'Appearance',
        defaultValue: { summary: 'md' }
      },
    },
    
    pill: {
      control: 'boolean',
      description: 'Pill shape',
      table: { 
        category: 'Appearance',
        defaultValue: { summary: false }
      },
    },
  },
};

// Default story
export const Default = {
  args: componentData,
};

// Showcases
export const AllColors = {
  render: () => {
    const variants = ['default', 'primary', 'secondary', 'success'];
    return `
      <div style="display: flex; gap: var(--size-3);">
        ${variants.map(v => componentTwig({ ...componentData, variant: v, text: v })).join('')}
      </div>
    `;
  },
};

export const AllSizes = {
  render: () => {
    const sizes = ['sm', 'md', 'lg'];
    return `
      <div style="display: flex; gap: var(--size-3); align-items: center;">
        ${sizes.map(s => componentTwig({ ...componentData, size: s, text: s })).join('')}
      </div>
    `;
  },
};
```

**Critical rules**:
- ✅ `tags: ['autodocs']` MANDATORY
- ✅ Import: `import componentTwig from './component.twig';`
- ✅ Render: `render: (args) => componentTwig(args)`
- ✅ ArgTypes categorized (Content, Appearance, Behavior, etc.)
- ✅ Stories: Default + Showcases (NOT individual variants)

**See 03-technical-implementation.md → Storybook Standards for complete reference.**

---

### Step 9: Create README Documentation

**Structure**:

```markdown
# Badge

Small label for statuses, categories, counts, or metadata.

## Usage

\`\`\`twig
{% include '@elements/badge/badge.twig' with {
  text: 'New',
  variant: 'primary',
} only %}
\`\`\`

## Props

| Name | Type | Default | Description |
|------|------|---------|-------------|
| `text` | `string` | required | Badge label |
| `variant` | `string` | `'default'` | Color variant |
| `size` | `string` | `'md'` | Size variant |
| `pill` | `boolean` | `false` | Pill shape |

## BEM Structure

\`\`\`
.ps-badge
├── .ps-badge__text
├── .ps-badge--sm
├── .ps-badge--lg
├── .ps-badge--primary
└── .ps-badge--pill
\`\`\`

## Design Tokens

- `--ps-badge-padding-x`, `--ps-badge-padding-y` - Spacing
- `--ps-badge-font-size` - Text size
- `--ps-badge-bg`, `--ps-badge-color` - Colors

## Accessibility

- ✅ Semantic HTML (`<span>`)
- ✅ WCAG AA contrast (4.5:1)
- ⚠️ Not focusable (decorative only)

## Examples

### Property Status
\`\`\`twig
{% include '@elements/badge/badge.twig' with {
  text: 'For Sale',
  variant: 'success',
} only %}
\`\`\`
```

---

## 🔄 Token-First Composition Workflow

### When to Apply

**Token-First workflow applies ONLY to**:
- ✅ Molecules (components/)
- ✅ Organisms (collections/)
- ✅ Templates (layouts/)
- ✅ Pages (pages/)

**Token-First workflow does NOT apply to**:
- ❌ Atoms (elements/) - They are autonomous

### The 4-Step Cascade

**When composing other components (include, embed, extends)**:

#### **STEP 1: Check Native Parameters**

Does the parent component provide the needed functionality via parameters?

```twig
{% embed '@components/card/card.twig' with {
  layout: 'horizontal',  ← Native param?
  size: 'large',         ← Native param?
} %}
```

**✅ IF YES:** Use native params, **STOP**  
**❌ IF NO:** Go to Step 2

---

#### **STEP 2: Check Utility Classes**

Can a utility/helper class solve the need?

```twig
{% embed '@components/card/card.twig' with {
  attributes: attributes
    .addClass('u-padding-large')   ← Utility?
    .addClass('u-gap-4')
} %}
```

**✅ IF YES:** Use utility classes, **STOP**  
**❌ IF NO:** Go to Step 3

---

#### **STEP 3: Override Parent/Child Tokens** ⭐ **PREFERRED**

Can we adjust via CSS tokens?

**PRINCIPLE:** The consuming component overrides tokens in **its own CSS**.

```css
/* card-offer-search.css (CONSUMER) */
.ps-card-offer-search {
  /* Override PARENT tokens (Card) */
  --ps-card-padding-x: var(--size-6);
  --ps-card-padding-y: var(--size-7);
  --ps-card-gap: var(--size-6);
  
  /* Override CHILD tokens (Atoms) */
  --ps-badge-font-size: var(--font-size-0);
  --ps-button-size: var(--size-6);
  --ps-link-text-decoration: none;
  
  /* Own tokens */
  --ps-card-offer-search-title-size: var(--font-size-1);
}
```

**✅ IF POSSIBLE:** Override tokens, **STOP**  
**❌ IF IMPOSSIBLE:** Go to Step 4

---

#### **STEP 4: Targeted CSS Override** (Last Resort)

```css
.ps-card-offer-search {
  /* ⚠️ Last resort only */
  & .ps-card__media {
    flex: 0 0 33.6%;  /* Figma spec */
  }
}
```

**⚠️ CAUTION:**
- Use `&` to maintain scope
- Never modify source component
- Only for truly unique cases

---

### Token Discovery

**Before writing CSS**:

```bash
# Search parent component tokens
grep -r "--ps-card-" source/patterns/components/card/card.css

# Check atom tokens
grep -r "--ps-button-" source/patterns/elements/button/button.css
```

### CSS Organization

```css
.ps-card-offer-search {
  /* ═══ SECTION 1: Parent token overrides ═══ */
  --ps-card-padding-x: var(--size-6);
  
  /* ═══ SECTION 2: Child token overrides ═══ */
  --ps-badge-font-size: var(--font-size-0);
  
  /* ═══ SECTION 3: Own tokens ═══ */
  --ps-card-offer-search-title-size: var(--font-size-1);
  
  /* ═══ SECTION 4: Targeted overrides (last resort) ═══ */
  & .ps-card__media {
    flex: 0 0 33.6%;
  }
  
  /* ═══ SECTION 5: Own elements ═══ */
  &__title {
    font-size: var(--ps-card-offer-search-title-size);
  }
}
```

---

## ✅ Validation Phase

### Step 10: Build and Validate

**Run build**:

```bash
npm run build
```

**Expected output**:
- ✅ No lint errors (Biome)
- ✅ CSS compiled (Vite)
- ✅ No token errors

**Start watch mode**:

```bash
npm run watch
```

**Visual validation in Storybook** (http://localhost:6006):
- Verify all variants render
- Test interactive stories
- Check Docs tab (Autodocs)

**See 04-quality-assurance.md for complete validation checklist.**

---

### Step 11: Commit and Update Changelog

**Commit message format**:

```
feat(elements): implement Badge component

- Add ps-badge with 7 semantic color variants
- Add 3 size variants (sm, md, lg)
- Add pill modifier for rounded shape
- Add optional icon support
- Implement CSS nesting with design tokens
- Add Storybook stories with Autodocs
- Document BEM structure and accessibility

Refs: docs/design/atoms/badge.md
```

**Update changelog**:

```markdown
## [Unreleased]

### Added
- **Badge** (element) - Small label for statuses and categories
  - 7 semantic color variants
  - 3 size variants (sm, md, lg)
  - Pill modifier
  - Optional icon integration
```

---

## 📋 Pre-Commit Checklist

**Before committing**:

- [ ] **4 files exist**: twig, css, yml, stories
- [ ] **Build passes**: `npm run build`
- [ ] **Visual check**: Storybook renders correctly
- [ ] **Zero hardcoded values**: All tokens used
- [ ] **BEM consistent**: Proper naming
- [ ] **Twig Drupal-compatible**: No arrow functions
- [ ] **Storybook Autodocs**: `tags: ['autodocs']` present
- [ ] **Accessibility**: Focus-visible, contrast, ARIA
- [ ] **Changelog**: Updated

---

## 🎯 Real-World Example: Card Offer Search

### Scenario

Implementing property search card (Molecule composing Card + Atoms).

### Requirements (from Figma)

- Padding: 30px vertical, 24px horizontal
- Image/Content: 33.6% / 66.4% proportions
- Title: 16px Regular
- Price: 20px Bold
- No underline on CTA link
- Badge gap: 8px

### Implementation

**1. Twig** (card-offer-search.twig):

```twig
{#
 * Card Offer Search
 * Specialized card for property search results
 #}

{% embed '@components/card/card.twig' with {
  layout: 'horizontal',
  attributes: attributes.addClass('ps-card-offer-search')
} %}
  
  {% block media %}
    {% include '@elements/image/image.twig' with {
      src: image_src,
      alt: image_alt,
      fit: 'cover'
    } only %}
  {% endblock %}
  
  {% block content %}
    <div class="ps-card-offer-search__header">
      {% include '@elements/badge/badge.twig' with {
        text: badge_text,
        variant: 'success'
      } only %}
    </div>
    
    <h3 class="ps-card-offer-search__title">{{ title }}</h3>
    
    <div class="ps-card-offer-search__price">
      <span class="ps-card-offer-search__price-value">{{ price }}</span>
    </div>
    
    {% include '@elements/link/link.twig' with {
      text: cta_text,
      url: cta_url
    } only %}
  {% endblock %}
  
{% endembed %}
```

**2. CSS** (card-offer-search.css):

```css
.ps-card-offer-search {
  /* ═══ Override PARENT (Card) tokens ═══ */
  --ps-card-padding-x: var(--size-6);  /* 24px (Figma) */
  --ps-card-padding-y: var(--size-7);  /* 30px (Figma) */
  --ps-card-gap: var(--size-6);
  
  /* ═══ Override CHILD (Atoms) tokens ═══ */
  --ps-badge-font-size: var(--font-size-0);  /* 14px */
  --ps-link-text-decoration: none;  /* No underline */
  
  /* ═══ Own tokens ═══ */
  --ps-card-offer-search-title-size: var(--font-size-1);  /* 16px */
  --ps-card-offer-search-price-size: var(--font-size-4);  /* 20px */
  --ps-card-offer-search-header-gap: var(--size-2);  /* 8px */
  
  /* ═══ Targeted overrides (Figma proportions) ═══ */
  @media (min-width: 768px) {
    & .ps-card__media {
      flex: 0 0 33.6%;  /* Figma spec: 242px/721px */
    }
    
    & .ps-card__content {
      flex: 1 1 66.4%;
    }
  }
  
  /* ═══ Own elements ═══ */
  &__header {
    display: flex;
    gap: var(--ps-card-offer-search-header-gap);
  }
  
  &__title {
    font-size: var(--ps-card-offer-search-title-size);
    font-weight: var(--font-weight-400);
  }
  
  &__price-value {
    font-size: var(--ps-card-offer-search-price-size);
    font-weight: var(--font-weight-700);
    color: var(--primary);
  }
}
```

**3. YAML** (card-offer-search.yml):

```yaml
image_src: "https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=400"
image_alt: "Modern apartment exterior"
badge_text: "New Listing"
title: "Luxury Downtown Loft"
price: "€850,000"
cta_text: "View Details"
cta_url: "/properties/downtown-loft"
```

**Why this works**:
- ✅ Respects Card's base structure (no modification)
- ✅ Uses Token-First workflow (STEP 3 preferred)
- ✅ Figma specs met via tokens + targeted CSS
- ✅ All atoms customized via tokens
- ✅ Reusable for other property card variants

---

## 🔗 Next Steps

**After creating component**:
- **For validation** → Read **04-quality-assurance.md**
- **For technical details** → See **03-technical-implementation.md**
- **For tokens/migration** → Consult **05-maintenance.md**

---

**Maintainers**: Design System Team  
**Version**: 4.0.0 (Complete restructuring - December 2025)
