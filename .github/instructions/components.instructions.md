---
applyTo:
  - "source/patterns/**/*.twig"
  - "source/patterns/**/*.css"
  - "source/patterns/**/*.yml"
  - "source/patterns/**/*.stories.jsx"
  - "source/patterns/**/README.md"
---

# Component Structure Standards - PS Theme

**Version**: 3.0.0  
**Date**: 2025-12-05  
**Scope**: File structure, BEM, naming, markup principles

---

## 🗂 Required File Structure

### For `elements/`, `components/`, `collections/` (ACTUAL COMPONENTS)

Every component MUST contain exactly **5 files**:

```
source/patterns/{level}/{component}/
├── {component}.twig          # Twig template
├── {component}.css           # CSS styles  
├── {component}.yml           # Default data
├── {component}.stories.jsx   # Storybook stories
└── README.md                 # Documentation (English)
```

**Optional 6th file** (only if JavaScript behavior needed):
```
└── {component}.js            # JavaScript behaviors
```

### ⚠️ Exception: `base/*` Directory

**Stories in `source/patterns/base/` are NOT components** — they document design tokens (colors, typography, spacing, etc.).

**Different structure for base stories** (only 4 files required):
```
source/patterns/base/{story}/
├── {story}.twig          # Documentation markup
├── {story}.yml           # Data/context
├── {story}.stories.jsx   # Story export (NO autodocs tag)
└── {story}.css           # (Optional) Custom styles
```

❌ **DO NOT create `README.md` in `base/*`** — documentation is in the story itself.

See `instructions/base-stories.instructions.md` for complete base stories standards.

### Naming Convention

- **Component name**: `kebab-case` (lowercase with hyphens)
- **All files share same base name**: `button.twig`, `button.css`, `button.yml`, etc.
- **Directory name = component name**: `button/`, `form-field/`, `progress-bar/`

**❌ NEVER**:
```
button/
├── button.twig      ✅
├── btn.css          ❌ (wrong name)
├── buttonData.yml   ❌ (wrong name)
└── Button.stories.jsx ❌ (wrong capitalization)
```

---

## 🎨 BEM Methodology (Strict)

### Naming Pattern

```css
.ps-{block}                    /* Block */
.ps-{block}__{element}         /* Element */
.ps-{block}--{modifier}        /* Modifier */
.ps-{block}__{element}--{mod}  /* Element modifier */
```

### Prefix Mandatory

**ALL** new components MUST use `ps-` prefix:

```css
/* ✅ CORRECT */
.ps-button { }
.ps-button__icon { }
.ps-button--primary { }
.ps-button__text--truncate { }

/* ❌ WRONG */
.button { }                    /* Missing prefix */
.ps-button-icon { }            /* Wrong separator (- instead of __) */
.ps-button_modifier { }        /* Wrong separator (_ instead of --) */
.ps-button__icon__nested { }   /* Double underscore forbidden */
```

### BEM Structure Rules

**1. Block = Component root**
```css
.ps-avatar {
  /* Base component styles */
}
```

**2. Element = Part of component**
```css
.ps-avatar__image {
  /* Image inside avatar */
}

.ps-avatar__text {
  /* Text inside avatar (initials) */
}
```

**3. Modifier = Variant of block OR element**
```css
/* Block modifier */
.ps-avatar--large {
  /* Large variant of avatar */
}

/* Element modifier */
.ps-avatar__text--uppercase {
  /* Uppercase variant of text */
}
```

**❌ NEVER** create dependent modifiers:
```css
/* ❌ WRONG - Requires both classes to work */
.ps-button--primary.ps-button--large {
  /* This is forbidden */
}

/* ✅ CORRECT - Each modifier works independently */
.ps-button--primary {
  background: var(--primary);
}

.ps-button--large {
  padding: var(--size-4) var(--size-8);
}
```

---

## 📝 File 1: Twig Template

### Header Comment (MANDATORY)

```twig
{#
 * Component Name (Level/Type)
 * @param type name - Description (required/optional, default: value)
 * @param type name - Description (required/optional, default: value)
 #}
```

**Example**:
```twig
{#
 * Button (Element/Atom)
 * @param string text - Button text (required)
 * @param string color - Color variant (optional, default: 'primary')
 * @param string size - Size variant (optional, default: 'md')
 * @param string icon_start - Icon name before text (optional)
 * @param string icon_end - Icon name after text (optional)
 * @param boolean disabled - Disabled state (optional, default: false)
 * @param string url - Link URL, renders <a> instead of <button> (optional)
 * @param object attributes - Additional HTML attributes (optional)
 #}
```

### Default Values

```twig
{% set color = color|default('primary') %}
{% set size = size|default('md') %}
{% set disabled = disabled|default(false) %}
```

### Classes Construction (BEM with Minimal Markup)

**⚠️ CRITICAL**: Use ternary with `null`, NEVER `.merge()` with conditions or arrow functions:

```twig
{# ✅ CORRECT - Drupal-compatible (Twig native only) #}
{%- set classes = [
  'ps-component',
  size != 'md' ? 'ps-component--' ~ size : null,
  color != 'primary' ? 'ps-component--' ~ color : null,
  disabled ? 'ps-component--disabled' : null
] -%}

<div class="{{ classes|join(' ')|trim }}">
  {# Component content #}
</div>
```

**Why?**
- Twig's `join()` automatically skips `null` values (no `.filter()` needed)
- No arrow functions (Drupal incompatible)
- No `.merge()` complexity (use array syntax)
- Clean, readable, maintainable

**❌ WRONG patterns**:

```twig
{# Wrong: .merge() with conditions #}
{%- if size != 'md' -%}
  {%- set classes = classes|merge(['ps-component--' ~ size]) -%}
{%- endif -%}

{# Wrong: Arrow functions (NOT Drupal-compatible) #}
{%- set classes = ['ps-component', size, color]|filter(v => v) -%}

{# Wrong: Using .filter() unnecessarily #}
{%- set classes = [
  'ps-component',
  size != 'md' ? 'ps-component--' ~ size : null
]|filter(v => v) -%}
{# join() already skips null - no filter needed! #}
```

### Element Tag

```twig
<{{ tag|default('div') }}
  class="{{ classes|join(' ')|trim }}"
  {% if attributes %}{{ attributes }}{% endif %}
  {% if ariaLabel %}aria-label="{{ ariaLabel }}"{% endif %}
>
  {# Component content #}
</{{ tag|default('div') }}>
```

---

## 🎨 File 2: CSS Styles

### Header Comment

```css
/**
 * Component Name (Level/Type)
 * Brief description
 * 
 * BEM: ps-component, ps-component__element, ps-component--modifier
 * Variants: variant1 | variant2 | variant3
 * Modifiers: --modifier1, --modifier2
 * Sizes: xs, sm, md (default), lg, xl
 */
```

### Structure with Nesting (MANDATORY for new components)

```css
.ps-component {
  /* ==========================================
   * Component-Scoped Variables (Layer 2)
   * ========================================== */
  --ps-component-padding-y: var(--size-3);
  --ps-component-padding-x: var(--size-6);
  --ps-component-bg: var(--primary);
  --ps-component-color: var(--white);
  
  /* ==========================================
   * Base Styles
   * ========================================== */
  display: inline-flex;
  align-items: center;
  gap: var(--size-2);
  padding: var(--ps-component-padding-y) var(--ps-component-padding-x);
  
  font-family: var(--font-sans);
  font-size: var(--font-size-2);
  font-weight: var(--font-weight-600);
  
  color: var(--ps-component-color);
  background: var(--ps-component-bg);
  border-radius: var(--radius-2);
  
  transition: 
    background var(--duration-fast) var(--ease-3),
    transform var(--duration-fast) var(--ease-3);
  
  /* ==========================================
   * Elements
   * ========================================== */
  &__icon {
    font-family: 'bnpre-icons';
    line-height: 1;
  }
  
  &__text {
    flex: 1;
  }
  
  /* ==========================================
   * Modifiers - Variants
   * ========================================== */
  &--primary {
    --ps-component-bg: var(--primary);
  }
  
  &--secondary {
    --ps-component-bg: var(--secondary);
  }
  
  /* ==========================================
   * Modifiers - Sizes
   * ========================================== */
  &--small {
    --ps-component-padding-y: var(--size-2);
    --ps-component-padding-x: var(--size-4);
    font-size: var(--font-size-1);
  }
  
  &--large {
    --ps-component-padding-y: var(--size-4);
    --ps-component-padding-x: var(--size-8);
    font-size: var(--font-size-3);
  }
  
  /* ==========================================
   * States
   * ========================================== */
  &:hover:not(:disabled) {
    transform: translateY(-1px);
  }
  
  &:active:not(:disabled) {
    transform: translateY(0);
  }
  
  &:focus-visible {
    outline: var(--border-size-2) solid var(--secondary);
    outline-offset: var(--border-size-2);
  }
  
  &:disabled,
  &--disabled {
    opacity: 0.5;
    cursor: not-allowed;
    pointer-events: none;
  }
}
```

### CSS Order (Standard)

1. **Component variables** (Layer 2: `--ps-component-*`)
2. **Base styles** (display, layout, typography, visual, transitions)
3. **Elements** (nested with `&__element`)
4. **Modifiers - Variants** (color/appearance)
5. **Modifiers - Sizes** (spacing/typography)
6. **States** (`:hover`, `:active`, `:focus-visible`, `:disabled`)

---

## 📄 File 3: YAML Configuration

```yaml
# Default: Brief description of default state
prop1: 'value'
color: 'primary'
size: 'md'
disabled: false

# Prop options (comments for clarity)
# color: primary | secondary | success | warning | danger | info
# size: xs | sm | md | lg | xl
```

**Guidelines**:
- Document default values
- Add comments for enum options
- Use realistic placeholder data (Real Estate context)
- Keep data minimal (only essentials for preview)

---

## 📖 File 4: Storybook Stories

See `instructions/storybook.instructions.md` for complete format.

**Quick template**:
```jsx
import componentTwig from './component.twig';
import data from './component.yml';

export default {
  title: 'Elements/Component',
  tags: ['autodocs'],
  render: (args) => componentTwig(args),
  args: data,
  parameters: {
    docs: {
      description: {
        component: 'Brief description (≤ 2 lines).\n\nDetails in sections below.',
      },
    },
  },
  argTypes: {
    // See storybook.instructions.md
  },
};

export const Default = {
  render: (args) => componentTwig(args),
  args: { ...data },
};

export const AllVariants = {
  // Showcase story
};
```

---

## 📚 File 5: README.md

### Structure (MANDATORY, English)

```markdown
# Component Name

Brief description (max 2 lines).

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| text | string | '' | Component text content |
| color | string | 'primary' | Color variant: primary \| secondary \| success \| warning \| danger \| info |
| size | string | 'md' | Size variant: xs \| sm \| md \| lg \| xl |
| disabled | boolean | false | Disabled state |

## BEM Structure

```css
.ps-component              /* Block */
.ps-component__element     /* Element */
.ps-component--modifier    /* Modifier */
```

## Design Tokens

- `--primary` - Primary brand color
- `--size-4` - Default padding (1rem)
- `--font-size-2` - Text size (1rem)
- `--duration-fast` - Transition speed (150ms)
- `--ease-3` - Easing function

## Variants

### Colors
- **primary** (default): Primary brand color
- **secondary**: Secondary brand color
- **success**: Success/positive state
- **warning**: Warning/caution state
- **danger**: Error/destructive state
- **info**: Information state

### Sizes
- **xs**: Extra small (height: 24px)
- **sm**: Small (height: 32px)
- **md** (default): Medium (height: 40px)
- **lg**: Large (height: 48px)
- **xl**: Extra large (height: 56px)

## Accessibility

- **Contrast**: Meets WCAG AA (4.5:1 for text, 3:1 for UI)
- **Focus**: Visible focus indicator on interactive elements
- **ARIA**: `aria-label` when text insufficient
- **Keyboard**: Standard button interactions (Space, Enter)
- **Screen readers**: Semantic HTML + accessible labels

## Usage

```twig
{% include '@elements/component/component.twig' with {
  text: 'Example text',
  color: 'primary',
  size: 'md'
} only %}
```

## Real-World Use Cases

- **Property listing**: CTA button "View Details"
- **Search form**: "Search Properties" button
- **Contact form**: Submit button

## Composition (if Molecule+)

This component uses:
- **Atom Name** (`@elements/atom/atom.twig`) - Purpose

## References

- Design spec: `docs/design/{level}/{component}.md`
- Storybook: [Component Docs](http://localhost:6006)
```

---

## 🔒 Minimal Markup Principle

### Rule: No Classes for Default Values

```twig
{# ✅ CORRECT - Minimal markup (md size, circle shape) #}
<div class="ps-avatar">
  <img src="..." alt="User" />
</div>

{# ✅ CORRECT - Modifiers only when different from default #}
<div class="ps-avatar ps-avatar--lg ps-avatar--square">
  <span class="ps-avatar__text">JD</span>
</div>

{# ❌ WRONG - Redundant classes for defaults #}
<div class="ps-avatar ps-avatar--md ps-avatar--circle">
  <img src="..." alt="User" />
</div>
```

### CSS Defaults

```css
.ps-avatar {
  /* Defaults applied in base class */
  width: var(--size-10);    /* md = 40px (default) */
  height: var(--size-10);
  border-radius: 50%;       /* circle (default) */
}

/* Modifiers override only what changes */
.ps-avatar--lg {
  width: var(--size-12);    /* 48px */
  height: var(--size-12);
}

.ps-avatar--square {
  border-radius: 0;         /* Override circle */
}
```

---

## 🔄 Modifiers Independence

### Rule: Each Modifier Works Alone

```css
/* ✅ CORRECT - Independent modifiers */
.ps-badge {
  background: var(--gray-200);  /* default */
}

.ps-badge--primary {
  background: var(--primary);   /* Works alone */
}

.ps-badge--large {
  padding: var(--size-3);       /* Works alone */
}

/* Both modifiers can be combined: */
/* <div class="ps-badge ps-badge--primary ps-badge--large"> */

/* ❌ WRONG - Dependent modifiers */
.ps-badge--primary.ps-badge--large {
  /* Requires BOTH classes - FORBIDDEN */
}
```

### Test: Modifier Checklist

For each modifier, verify it works with **ONLY** the base class:

- [ ] `.ps-component` alone ✅
- [ ] `.ps-component .ps-component--variant` ✅
- [ ] `.ps-component .ps-component--size` ✅
- [ ] `.ps-component .ps-component--variant .ps-component--size` ✅ (combination)

---

## 🎯 Semantic Naming

### Color Variants (MANDATORY)

**✅ ALWAYS** use semantic names:
```
primary | secondary | success | warning | danger | info | default
```

**❌ NEVER** use color names:
```
green | blue | red | yellow | purple | gray
```

### Size Variants (STANDARD)

```
xs | sm | md | lg | xl
```

### Other Common Variants

| Prop Name | Values | Purpose |
|-----------|--------|---------|
| `variant` | solid, outline, ghost, text | Visual style |
| `orientation` | horizontal, vertical | Direction |
| `shape` | circle, square, rounded, pill | Geometric form |
| `appearance` | filled, outlined, minimal | Visual treatment |
| `alignment` | left, center, right, justify | Text alignment |
| `position` | top, bottom, left, right | Spatial position |

---

## 🚫 Anti-Patterns

### 1. Inconsistent Naming

```
❌ button/ with btn.css
❌ formField/ instead of form-field/
❌ ProgressBar/ (PascalCase)
```

### 2. Missing Files

```
❌ Only .twig + .css (missing .yml, .stories.jsx, README.md)
```

### 3. Wrong BEM

```css
❌ .button__icon { }             /* Missing ps- prefix */
❌ .ps-button-icon { }           /* - instead of __ */
❌ .ps-button__icon__inner { }   /* Double __ */
❌ .ps-button--primary.ps-button--large { } /* Combined modifiers */
```

### 4. Hardcoded Values

```css
❌ color: #00915A;
❌ padding: 16px 24px;
❌ transition: 150ms ease;
```

### 5. Classes for Defaults

```html
❌ <div class="ps-avatar ps-avatar--md ps-avatar--circle">
```

### 6. Non-Semantic Colors

```yaml
❌ color: 'green'
❌ options: [red, blue, yellow]
```

---

## 🔗 Cross-References

- **Atomic Design Composition**: `instructions/atomic-design.instructions.md`
- **CSS Standards**: `instructions/css.instructions.md`
- **Twig Templates**: `instructions/templates.instructions.md`
- **Storybook Stories**: `instructions/storybook.instructions.md`

---

**Last Updated**: 2025-12-05  
**Maintainers**: Design System Team
