# Core Principles - PS Theme

**Version**: 4.0.0  
**Last Updated**: 2025-12-12  
**Status**: ACTIVE

---

## 📖 Purpose

This document establishes the **foundational principles** of the PS Theme design system. Read this file **once** to understand the philosophy, methodology, and core concepts before creating components.

---

## 🎯 Design System Philosophy

### Greenfield Mindset

> **"Build components as if launching the project today, with zero legacy constraints."**

**Core Values**:
- **Simplicity**: Minimal markup, clear naming, intuitive structure
- **Consistency**: One way to do things (no "alternative" patterns)
- **Scalability**: 87 components (19 atoms → 20 molecules → 12 organisms → 8 templates → 8 pages)
- **Quality**: 100% conformity score mandatory (no compromises)
- **Maintainability**: Future developers understand code instantly

---

## 🧬 Atomic Design Methodology

### Five-Level Hierarchy

Based on **Brad Frost's Atomic Design** methodology:

```
Atoms → Molecules → Organisms → Templates → Pages
  ↓         ↓            ↓           ↓          ↓
Basic   Combinations  Sections    Layout    Content
```

### PS Theme Implementation

| Level | Directory | Count | Purpose | Examples |
|-------|-----------|-------|---------|----------|
| **Atoms** | `elements/` | 19 | Indivisible building blocks | button, icon, label, badge, divider |
| **Molecules** | `components/` | 20 | Groups of atoms functioning together | form-field, card, breadcrumb, dropdown |
| **Organisms** | `collections/` | 12 | Complex UI sections | header, footer, product-grid, navigation |
| **Templates** | `layouts/` | 8 | Page-level layouts | homepage, article, listing-page |
| **Pages** | `pages/` | 8 | Specific template instances | Homepage with real content |

### Key Principles

**Atomic Design is NOT linear** — it's a mental model for:
- Designing pages with real content
- Identifying sections (organisms)
- Breaking down into groups (molecules)
- Extracting basic elements (atoms)
- Refining at all levels simultaneously

---

## 🔧 Component Composition Rules

### Hierarchy Constraints (MANDATORY)

```
✅ ALLOWED:
- Molecules include Atoms
- Organisms include Molecules + Atoms
- Templates include all levels
- Pages include all levels

❌ FORBIDDEN:
- Atoms including other Atoms (exception: icon/flag system)
- Molecules including Organisms
- Circular dependencies
```

### Composition Before Creation

**Before creating ANY component above Atom level**:

1. **Identify required atoms** (smallest functional pieces)
2. **Check existing atoms** (`ls source/patterns/elements/`)
3. **Analyze composition strategy** (which atoms to combine)
4. **Document composition** (in component spec: `docs/02-composants/`)

**Decision Matrix**:

| Atom Exists? | Fits Need? | Action |
|--------------|------------|--------|
| ✅ Yes | ✅ Yes | **REUSE** via include |
| ✅ Yes | ❌ No | **EXTEND** atom (add variant/modifier) |
| ❌ No | — | **CREATE** new atom FIRST |

---

## 🎨 BEM Methodology (Strict)

### Naming Pattern

```css
.ps-{block}                    /* Block - Component root */
.ps-{block}__{element}         /* Element - Component part */
.ps-{block}--{modifier}        /* Modifier - Component variant */
.ps-{block}__{element}--{mod}  /* Element modifier */
```

### Prefix Mandatory

**ALL components MUST use `ps-` prefix**:

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

### Modifier Independence (CRITICAL)

**Each modifier MUST work independently**:

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

/* Both can be combined: .ps-badge .ps-badge--primary .ps-badge--large */

/* ❌ WRONG - Dependent modifiers */
.ps-badge--primary.ps-badge--large {
  /* Requires BOTH classes - FORBIDDEN */
}
```

---

## 🏗️ Component Architecture

### 4-File Structure (MANDATORY)

Every component MUST contain exactly **4 files**:

```
source/patterns/{level}/{component}/
├── {component}.twig          # Twig template
├── {component}.css           # CSS styles  
├── {component}.yml           # Default data
└── {component}.stories.jsx   # Storybook stories
```

**Optional 5th file** (only if JavaScript behavior needed):
```
└── {component}.js            # JavaScript behaviors
```

**Exception**: `base/*` stories (design token documentation) use 4 files only.

### Naming Convention

- **Component name**: `kebab-case` (lowercase with hyphens)
- **All files share same base name**: `button.twig`, `button.css`, `button.yml`, etc.
- **Directory name = component name**: `button/`, `form-field/`, `progress-bar/`

---

## 🎨 Design Tokens (ABSOLUTE RULE)

### Zero Hardcoded Values

**❌ NEVER**:
```css
color: #00915A;
padding: 16px 24px;
box-shadow: 0 2px 4px rgba(0,0,0,0.2);
font-size: 18px;
transition: 150ms ease;
```

**✅ ALWAYS**:
```css
color: var(--primary);
padding: var(--size-4) var(--size-6);
box-shadow: var(--shadow-2);
font-size: var(--font-size-3);
transition: var(--duration-fast) var(--ease-3);
```

### Token Categories

| Category | File | Examples | When to Use |
|----------|------|----------|-------------|
| **Colors** | `colors.css` | `--gray-600`, `--green-600` | Raw color values |
| **Brand** | `brand.css` | `--primary`, `--secondary`, `--success` | Semantic colors |
| **Fonts** | `fonts.css` | `--font-size-2`, `--font-weight-600` | Typography |
| **Sizes** | `sizes.css` | `--size-4`, `--size-6` | Spacing, sizing |
| **Borders** | `borders.css` | `--radius-2`, `--border-size-1` | Radii, widths |
| **Shadows** | `shadows.css` | `--shadow-2`, `--shadow-4` | Box shadows |
| **Animations** | `animations.css` | `--duration-fast`, `--duration-normal` | Timings |
| **Easing** | `easing.css` | `--ease-3`, `--ease-in-out-4` | Transitions |
| **Z-Index** | `zindex.css` | `--z-dropdown`, `--z-modal` | Stacking |

**⚠️ CRITICAL**: NEVER edit `source/props/*.css` directly during component work. If token needed, see **05-maintenance.md** (Token Creation Process).

---

## 📏 Minimal Markup Principle

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

### HTML Structure Simplification

**CRITICAL RULE**: Use the **simplest possible HTML structure** for all components.

**Principle**: Single element with data attributes (PREFERRED) over nested child wrappers.

#### ✅ CORRECT - Single Element Pattern

```html
<!-- Basic component - single element -->
<span class="ps-badge ps-badge--primary">Verified</span>

<!-- With icon at start (default position) -->
<span class="ps-badge ps-badge--success" data-icon="check">Verified</span>

<!-- With icon at end -->
<a class="ps-badge ps-badge--info" data-icon="arrow-right" data-icon-position="end" href="#">Learn more</a>

<!-- Interactive with href (no separate clickable prop needed) -->
<a class="ps-badge" href="/category">Category</a>
```

#### ❌ INCORRECT - Unnecessary Child Wrappers

```html
<!-- ❌ WRONG - Nested child elements -->
<span class="ps-badge ps-badge--primary">
  <span class="ps-badge__icon" data-icon="check"></span>
  <span class="ps-badge__text">Verified</span>
</span>

<!-- ❌ WRONG - Separate clickable prop when href exists -->
{% include '@elements/badge/badge.twig' with {
  text: 'Category',
  clickable: true,  /* Redundant! href already makes it clickable */
  href: '/category'
} %}
```

#### Icon System Standardization

**Use data attributes on main container** (handled by global `icons.css`):

| Attribute | Values | Default | Purpose |
|-----------|--------|---------|---------|
| `data-icon` | Icon name (without `icon-` prefix) | - | Renders icon via CSS `::before` or `::after` |
| `data-icon-position` | `'start'` \| `'end'` | `'start'` | Icon placement relative to text |

**Implementation**:
```twig
{# Icon at start (default) - omit data-icon-position #}
<{{ tag }} data-icon="{{ icon }}">{{ text }}</{{ tag }}>

{# Icon at end - specify position #}
<{{ tag }} data-icon="{{ icon }}" data-icon-position="end">{{ text }}</{{ tag }}>
```

**CSS Rendering** (automatic via `source/props/icons.css`):
```css
/* Icons at start (default) */
[data-icon]::before {
  content: '';
  background-image: url('/icons/icons-sprite.svg#icon-' attr(data-icon));
  /* ...sizing... */
}

/* Icons at end */
[data-icon][data-icon-position="end"]::after {
  content: '';
  background-image: url('/icons/icons-sprite.svg#icon-' attr(data-icon));
  /* ...sizing... */
}
```

**Benefits**:
- ✅ Minimal DOM (1 element vs 3+)
- ✅ Centralized icon rendering system
- ✅ Consistent pattern across all components
- ✅ Flexible positioning (start/end)
- ✅ No child wrappers needed for icons

**Exceptions** (child elements ONLY when necessary):
- Complex interactive patterns (e.g., form controls with multiple focusable elements)
- Layout containers requiring specific DOM structure (e.g., grid/flex wrappers)
- Components with multiple semantic sections (e.g., card with header/body/footer)

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
| `position` | start, end | Spatial position |

---

## 📚 Terminology Reference

### Atomic Design Levels

| Term | Definition | Directory | Examples |
|------|------------|-----------|----------|
| **Atom** | Smallest indivisible UI element | `elements/` | button, icon, label, badge |
| **Molecule** | Group of atoms functioning together | `components/` | card, form-field, alert |
| **Organism** | Complex UI section composed of molecules | `collections/` | header, footer, navigation |
| **Template** | Page-level layout structure | `layouts/` | homepage-layout, article-layout |
| **Page** | Specific template instance with content | `pages/` | homepage, about-page |

**Forbidden Synonyms**:
- ❌ "Element" when referring to Atom level (use "Atom" or "element atom")
- ❌ "Component" generically (specify: Atom, Molecule, Organism)
- ❌ "Block" for Molecule (BEM context only)

### Composition Methods

| Term | Twig Syntax | Meaning | When to Use |
|------|-------------|---------|-------------|
| **Include** | `{% include %}` | Insert external template | Atoms in Molecules (most common) |
| **Embed** | `{% embed %}` | Insert template with block overrides | Templates extending base layouts |
| **Extend** | `{% extends %}` | Inherit from parent template | Page-level inheritance |
| **Compose** | N/A | Combine multiple components | Architectural discussion |

---

## ♿ Accessibility (WCAG 2.2 AA)

### Core Requirements

**ALL components MUST**:
- Contrast ratios: Text 4.5:1, UI 3:1
- Focus indicators: Visible on ALL interactives
- Keyboard navigation: Full keyboard access
- ARIA attributes: When semantic HTML insufficient
- Screen reader support: Meaningful accessible names

### Focus-Visible (MANDATORY)

```css
.ps-button {
  &:focus-visible {
    outline: var(--border-size-2) solid var(--secondary);
    outline-offset: var(--border-size-2);
  }
}
```

---

## 🌍 Language Policy

**Primary Language**: American English (color, behavior, organization)

**Exceptions**:
- **AI Chat**: French (copilot responses to user)
- **Commit Messages**: French body (English type/scope)
- **User-Facing Content**: French (button labels, YAML mocks)

**Rationale**: English documentation = universal accessibility + industry standard

---

## 🔗 Next Steps

**After understanding these principles**:
- **To create a component** → Read **02-component-development.md**
- **For technical reference** → See **03-technical-implementation.md**
- **To validate work** → Use **04-quality-assurance.md**
- **For tokens/migration** → Consult **05-maintenance.md**

---

**Maintainers**: Design System Team  
**Version**: 4.0.0 (Complete restructuring - December 2025)
