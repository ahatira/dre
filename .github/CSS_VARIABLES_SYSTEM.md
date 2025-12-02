# CSS Variables System - PS Theme (Bootstrap 5 Inspired)

**Version**: 1.0.0  
**Date**: 2025-12-01  
**Status**: 🔒 **MANDATORY REFERENCE - GRADUAL MIGRATION STRATEGY**

---

## 📚 Table of Contents

1. [System Overview](#1-system-overview)
2. [Architecture Layers](#2-architecture-layers)
3. [Root-Level Variables](#3-root-level-variables)
4. [Component-Scoped Variables](#4-component-scoped-variables)
5. [Cascade System](#5-cascade-system)
6. [Naming Conventions](#6-naming-conventions)
7. [Migration Strategy](#7-migration-strategy)
8. [Performance Considerations](#8-performance-considerations)
9. [Dark Mode Support](#9-dark-mode-support)
10. [Practical Examples](#10-practical-examples)

---

## 1. System Overview

### Current State vs Target State

**Current System (Legacy):**
```css
/* Global tokens only */
:where(html) {
  --size-4: 1rem;
  --primary: hsl(157, 100%, 28%);
}

/* Components use global tokens directly */
.ps-button {
  padding: var(--size-3) var(--size-6);
  color: var(--primary);
}
```

**Target System (Bootstrap 5 Inspired):**
```css
/* Layer 1: Root primitives */
:root {
  --ps-green-600: hsl(162, 72%, 38%);
  --ps-size-4: 1rem;
}

/* Layer 2: Component-scoped variables with defaults */
.ps-button {
  --ps-button-padding-y: var(--ps-size-3);
  --ps-button-padding-x: var(--ps-size-6);
  --ps-button-bg: var(--ps-brand-primary);
  --ps-button-color: var(--ps-white);
  
  /* Use component variables */
  padding: var(--ps-button-padding-y) var(--ps-button-padding-x);
  background: var(--ps-button-bg);
  color: var(--ps-button-color);
}

/* Layer 3: Easy customization */
.custom-form .ps-button {
  --ps-button-bg: var(--ps-purple-600); /* Override */
}
```

### Key Benefits

1. **Cascade Control**: Override at any level without touching CSS
2. **Contextual Theming**: Different button styles in different contexts
3. **Runtime Customization**: Change via JavaScript
4. **Reduced Specificity Wars**: No `!important` needed
5. **Better DevTools**: Inspect computed values easily

---

## 2. Architecture Layers

### Three-Layer System

```
┌─────────────────────────────────────────┐
│ Layer 3: Context Overrides             │
│ .sidebar .ps-button { --ps-button-bg } │
└─────────────────────────────────────────┘
              ↑ overrides
┌─────────────────────────────────────────┐
│ Layer 2: Component Variables           │
│ .ps-button { --ps-button-bg: var(...) }│
└─────────────────────────────────────────┘
              ↑ references
┌─────────────────────────────────────────┐
│ Layer 1: Root Primitives               │
│ :root { --ps-green-600, --ps-size-4 }  │
└─────────────────────────────────────────┘
```

### Layer 1: Root Primitives (Global Design Tokens)

**Location**: `source/props/*.css`

**Purpose**: Foundation values that rarely change

```css
:root {
  /* Colors - Raw values */
  --ps-green-600: hsl(162, 72%, 38%);
  --ps-blue-600: hsl(220, 89%, 53%);
  --ps-gray-900: hsl(222, 47%, 11%);
  
  /* Sizes - Raw values */
  --ps-size-1: 0.25rem;
  --ps-size-4: 1rem;
  --ps-size-6: 1.5rem;
  
  /* Semantic aliases */
  --ps-brand-primary: var(--ps-green-600);
  --ps-brand-secondary: var(--ps-purple-600);
  
  /* Typography */
  --ps-font-sans: 'BNPP Sans', system-ui, sans-serif;
  --ps-font-size-1: 1rem;
  --ps-font-weight-600: 600;
}
```

### Layer 2: Component Variables (Local Defaults)

**Location**: Component CSS files (e.g., `button.css`)

**Purpose**: Component-specific defaults that reference Layer 1

```css
.ps-button {
  /* Component-scoped variables with defaults */
  --ps-button-padding-y: var(--ps-size-3);
  --ps-button-padding-x: var(--ps-size-6);
  --ps-button-bg: var(--ps-brand-primary);
  --ps-button-color: var(--ps-white);
  --ps-button-border-radius: var(--ps-radius-1);
  --ps-button-font-weight: var(--ps-font-weight-600);
  --ps-button-transition: var(--ps-duration-fast) var(--ps-ease-3);
  
  /* Apply component variables */
  padding: var(--ps-button-padding-y) var(--ps-button-padding-x);
  background: var(--ps-button-bg);
  color: var(--ps-button-color);
  border-radius: var(--ps-button-border-radius);
  font-weight: var(--ps-button-font-weight);
  transition: var(--ps-button-transition);
}
```

### Layer 3: Context Overrides (Runtime Customization)

**Location**: Page-level CSS, inline styles, or JavaScript

**Purpose**: Override component defaults in specific contexts

```css
/* Override in specific context */
.sidebar .ps-button {
  --ps-button-bg: var(--ps-gray-700);
  --ps-button-padding-y: var(--ps-size-2);
}

/* Override with modifier */
.ps-button--large {
  --ps-button-padding-y: var(--ps-size-4);
  --ps-button-padding-x: var(--ps-size-8);
}

/* Override via inline style (JavaScript) */
<button class="ps-button" style="--ps-button-bg: var(--ps-purple-600);">
  Custom
</button>
```

---

## 3. Root-Level Variables

### Current File Structure

```
source/props/
├── index.css          → Imports all token files
├── colors.css         → Color primitives + semantics
├── brand.css          → Brand-specific mappings
├── fonts.css          → Typography scales
├── sizes.css          → Spacing/sizing scales
├── borders.css        → Border radii + widths
├── shadows.css        → Box shadows
├── animations.css     → Durations + delays
├── easing.css         → Timing functions
├── zindex.css         → Z-index scales
└── icons.css          → Icon font mappings
```

### Recommended Additions

**New file**: `source/props/semantic.css`

```css
/**
 * Semantic Design Tokens
 * Higher-level meanings that reference primitive tokens
 */

:root {
  /* Contextual colors */
  --ps-color-primary: var(--ps-brand-primary);
  --ps-color-secondary: var(--ps-gray-600);
  --ps-color-success: var(--ps-green-600);
  --ps-color-danger: var(--ps-red-600);
  --ps-color-warning: var(--ps-yellow-500);
  --ps-color-info: var(--ps-blue-600);
  
  /* Text colors */
  --ps-color-text-default: var(--ps-gray-900);
  --ps-color-text-muted: var(--ps-gray-600);
  --ps-color-text-disabled: var(--ps-gray-400);
  --ps-color-text-inverse: var(--ps-white);
  
  /* Background colors */
  --ps-color-bg-default: var(--ps-white);
  --ps-color-bg-subtle: var(--ps-gray-50);
  --ps-color-bg-muted: var(--ps-gray-100);
  --ps-color-bg-disabled: var(--ps-gray-200);
  
  /* Border colors */
  --ps-color-border-default: var(--ps-gray-300);
  --ps-color-border-hover: var(--ps-gray-400);
  --ps-color-border-focus: var(--ps-blue-600);
  --ps-color-border-error: var(--ps-red-600);
  
  /* Interactive states */
  --ps-color-hover-bg: rgba(0, 0, 0, 0.05);
  --ps-color-active-bg: rgba(0, 0, 0, 0.1);
  --ps-color-focus-ring: rgba(13, 110, 253, 0.25);
}
```

---

## 4. Component-Scoped Variables

### Component Variable Pattern

**Template Structure:**

```css
.ps-{component} {
  /* 1. Define all component variables with defaults */
  --ps-{component}-{property}: {default-value};
  
  /* 2. Use component variables in properties */
  {property}: var(--ps-{component}-{property});
  
  /* 3. Variants override component variables */
  &--{variant} {
    --ps-{component}-{property}: {variant-value};
  }
}
```

### Example: Button Component

```css
.ps-button {
  /* Spacing */
  --ps-button-padding-y: var(--ps-size-3);
  --ps-button-padding-x: var(--ps-size-6);
  --ps-button-gap: var(--ps-size-2);
  
  /* Colors */
  --ps-button-bg: var(--ps-brand-primary);
  --ps-button-color: var(--ps-white);
  --ps-button-border-color: transparent;
  
  /* Typography */
  --ps-button-font-family: var(--ps-font-sans);
  --ps-button-font-size: var(--ps-font-size-1);
  --ps-button-font-weight: var(--ps-font-weight-600);
  --ps-button-line-height: var(--ps-leading-5);
  
  /* Visual */
  --ps-button-border-radius: var(--ps-radius-1);
  --ps-button-border-width: var(--ps-border-size-1);
  
  /* States */
  --ps-button-hover-bg: var(--ps-green-700);
  --ps-button-active-bg: var(--ps-green-800);
  --ps-button-disabled-opacity: 0.5;
  
  /* Transitions */
  --ps-button-transition-duration: 150ms;
  --ps-button-transition-timing: cubic-bezier(0.4, 0.0, 0.2, 1);
  
  /* Apply variables */
  display: inline-flex;
  align-items: center;
  gap: var(--ps-button-gap);
  padding: var(--ps-button-padding-y) var(--ps-button-padding-x);
  
  font-family: var(--ps-button-font-family);
  font-size: var(--ps-button-font-size);
  font-weight: var(--ps-button-font-weight);
  line-height: var(--ps-button-line-height);
  
  color: var(--ps-button-color);
  background: var(--ps-button-bg);
  border: var(--ps-button-border-width) solid var(--ps-button-border-color);
  border-radius: var(--ps-button-border-radius);
  
  transition: 
    background var(--ps-button-transition-duration) var(--ps-button-transition-timing),
    color var(--ps-button-transition-duration) var(--ps-button-transition-timing);
  
  /* States */
  &:hover:not(:disabled) {
    background: var(--ps-button-hover-bg);
  }
  
  &:active:not(:disabled) {
    background: var(--ps-button-active-bg);
  }
  
  &:disabled {
    opacity: var(--ps-button-disabled-opacity);
    cursor: not-allowed;
  }
  
  /* Variants override variables */
  &--secondary {
    --ps-button-bg: var(--ps-gray-600);
    --ps-button-hover-bg: var(--ps-gray-700);
    --ps-button-active-bg: var(--ps-gray-800);
  }
  
  &--large {
    --ps-button-padding-y: var(--ps-size-4);
    --ps-button-padding-x: var(--ps-size-8);
    --ps-button-font-size: var(--ps-font-size-2);
  }
  
  &--small {
    --ps-button-padding-y: var(--ps-size-2);
    --ps-button-padding-x: var(--ps-size-4);
    --ps-button-font-size: var(--ps-font-size-0);
  }
}
```

### Benefits of This Pattern

1. **Easy Customization**: Override one variable instead of rewriting properties
2. **Consistent Variants**: All modifiers use same mechanism
3. **DevTools Friendly**: See computed values in inspector
4. **No Specificity Wars**: Variables cascade naturally
5. **JavaScript Integration**: Easy to manipulate at runtime

---

## 5. Cascade System

### How Variables Cascade

```css
/* Step 1: Root defines primitive */
:root {
  --ps-green-600: hsl(162, 72%, 38%);
  --ps-brand-primary: var(--ps-green-600);
}

/* Step 2: Component sets default using root */
.ps-button {
  --ps-button-bg: var(--ps-brand-primary);
  background: var(--ps-button-bg);
}

/* Step 3: Variant overrides component variable */
.ps-button--secondary {
  --ps-button-bg: var(--ps-gray-600); /* Cascades down */
}

/* Step 4: Context overrides variant */
.sidebar .ps-button {
  --ps-button-bg: var(--ps-purple-600); /* Most specific */
}

/* Step 5: Inline style wins */
<button class="ps-button" style="--ps-button-bg: var(--ps-red-600);">
```

### Cascade Priority (Lowest to Highest)

```
1. :root primitives           (lowest specificity)
2. Component defaults
3. Modifier classes
4. Context selectors
5. Inline styles              (highest specificity)
```

### Cascade Example

```html
<!-- Root: --ps-button-bg = var(--ps-brand-primary) = green-600 -->
<button class="ps-button">Default</button>

<!-- Variant override: --ps-button-bg = var(--ps-gray-600) -->
<button class="ps-button ps-button--secondary">Secondary</button>

<!-- Context override: --ps-button-bg = var(--ps-purple-600) -->
<div class="sidebar">
  <button class="ps-button">Sidebar Button</button>
</div>

<!-- Inline override: --ps-button-bg = var(--ps-red-600) -->
<button class="ps-button" style="--ps-button-bg: var(--ps-red-600);">
  Custom
</button>
```

---

## 6. Naming Conventions

### Prefix System

**All PS Theme variables MUST use `--ps-` prefix.**

```css
/* ✅ CORRECT */
--ps-brand-primary
--ps-button-bg
--ps-form-field-padding-y

/* ❌ WRONG */
--primary    (no prefix)
--ps_button_bg     (underscores)
--PSButtonBg       (camelCase)
```

### Component Variable Naming

**Pattern**: `--ps-{component}-{property}-{modifier?}`

```css
/* Base property */
--ps-button-bg
--ps-button-color

/* State variant */
--ps-button-hover-bg
--ps-button-active-color
--ps-button-disabled-opacity

/* Size/spacing */
--ps-button-padding-y
--ps-button-padding-x
--ps-button-gap

/* Modifier context */
--ps-button-primary-bg
--ps-button-secondary-color
```

### Root Token Naming

**Pattern**: `--ps-{category}-{name}-{scale?}`

```css
/* Colors with scale */
--ps-gray-100
--ps-gray-900
--ps-green-600

/* Semantic colors */
--ps-brand-primary
--ps-color-text-default
--ps-color-border-hover

/* Sizes with scale */
--ps-size-1
--ps-size-12
--ps-spacing-3

/* Typography */
--ps-font-sans
--ps-font-size-1
--ps-font-weight-600
--ps-leading-5
```

---

## 7. Migration Strategy

### Gradual Migration (Recommended)

**Phase 1: New Components (Immediate)**
```
All NEW components MUST use component-scoped variables
```

**Phase 2: Refactored Components (Opportunistic)**
```
When refactoring existing components, migrate to new system
```

**Phase 3: Legacy Components (Long-term)**
```
Eventually migrate all existing components
No timeline pressure, migrate as touched
```

### Migration Example: Button

**Before (Legacy):**
```css
.ps-button {
  padding: var(--size-3) var(--size-6);
  background: var(--primary);
  color: var(--white);
  border-radius: var(--radius-1);
}

.ps-button--secondary {
  background: var(--gray-600);
}
```

**After (New System):**
```css
.ps-button {
  /* Define component variables */
  --ps-button-padding-y: var(--ps-size-3);
  --ps-button-padding-x: var(--ps-size-6);
  --ps-button-bg: var(--ps-brand-primary);
  --ps-button-color: var(--ps-white);
  --ps-button-border-radius: var(--ps-radius-1);
  
  /* Use component variables */
  padding: var(--ps-button-padding-y) var(--ps-button-padding-x);
  background: var(--ps-button-bg);
  color: var(--ps-button-color);
  border-radius: var(--ps-button-border-radius);
}

.ps-button--secondary {
  /* Override component variable */
  --ps-button-bg: var(--ps-gray-600);
}
```

### Coexistence Strategy

**Both systems can coexist:**

```css
/* Legacy components continue working */
.ps-avatar {
  width: var(--size-12);
  background: var(--gray-200);
}

/* New components use scoped variables */
.ps-form-field {
  --ps-form-field-gap: var(--ps-size-2);
  gap: var(--ps-form-field-gap);
}
```

---

## 8. Performance Considerations

### CSS Custom Properties Performance

**✅ GOOD for Performance:**
- Cascade naturally (no extra computations)
- Inherit values efficiently
- Update in real-time without reflow
- Smaller CSS bundle (less duplication)

**⚠️ Watch Out For:**
- Too many custom properties (100s per component)
- Deep nesting (10+ levels of overrides)
- Unused variables (dead code)

### Best Practices

```css
/* ✅ GOOD: Reasonable number of variables */
.ps-button {
  --ps-button-bg: ...;
  --ps-button-color: ...;
  --ps-button-padding-y: ...;
  /* Total: ~15 variables */
}

/* ❌ AVOID: Too many granular variables */
.ps-button {
  --ps-button-padding-top: ...;
  --ps-button-padding-right: ...;
  --ps-button-padding-bottom: ...;
  --ps-button-padding-left: ...;
  /* Better: Use --ps-button-padding-y/x */
}

/* ✅ GOOD: Group related properties */
--ps-button-padding-y
--ps-button-padding-x

/* ❌ AVOID: Individual sides unless necessary */
--ps-button-padding-top
--ps-button-padding-right
```

---

## 9. Dark Mode Support

### Approach: Theme-Scoped Variables

```css
/* Root defines both themes */
:root {
  /* Light mode (default) */
  --ps-color-bg-default: var(--ps-white);
  --ps-color-text-default: var(--ps-gray-900);
  --ps-color-border-default: var(--ps-gray-300);
}

/* Dark mode overrides */
[data-theme="dark"] {
  --ps-color-bg-default: var(--ps-gray-900);
  --ps-color-text-default: var(--ps-gray-100);
  --ps-color-border-default: var(--ps-gray-700);
}

/* Components use semantic tokens */
.ps-card {
  background: var(--ps-color-bg-default);
  color: var(--ps-color-text-default);
  border: 1px solid var(--ps-color-border-default);
  /* Automatically adapts to theme */
}
```

### Theme Toggle (JavaScript)

```javascript
// Toggle theme
document.documentElement.setAttribute('data-theme', 'dark');

// Or override specific component
document.querySelector('.ps-button').style.setProperty(
  '--ps-button-bg', 
  'var(--ps-purple-600)'
);
```

---

## 10. Practical Examples

### Example 1: FormField Component

```css
.ps-form-field {
  /* Spacing */
  --ps-form-field-gap: var(--ps-size-2);
  
  /* Label */
  --ps-form-field-label-font-size: var(--ps-font-size-0);
  --ps-form-field-label-font-weight: var(--ps-font-weight-600);
  --ps-form-field-label-color: var(--ps-gray-900);
  
  /* Helper text */
  --ps-form-field-helper-font-size: var(--ps-font-size-sm);
  --ps-form-field-helper-color: var(--ps-gray-600);
  
  /* Error state */
  --ps-form-field-error-color: var(--ps-red-600);
  
  /* Apply variables */
  display: flex;
  flex-direction: column;
  gap: var(--ps-form-field-gap);
  
  & .ps-form-field__label {
    font-size: var(--ps-form-field-label-font-size);
    font-weight: var(--ps-form-field-label-font-weight);
    color: var(--ps-form-field-label-color);
  }
  
  & .ps-form-field__helper {
    font-size: var(--ps-form-field-helper-font-size);
    color: var(--ps-form-field-helper-color);
  }
  
  /* Error state overrides */
  &--error {
    --ps-form-field-label-color: var(--ps-red-600);
  }
}
```

### Example 2: Context Override

```css
/* Sidebar has tighter spacing */
.sidebar .ps-form-field {
  --ps-form-field-gap: var(--ps-size-1);
  --ps-form-field-label-font-size: var(--ps-font-size-sm);
}

/* Modal has different colors */
.modal .ps-form-field {
  --ps-form-field-label-color: var(--ps-white);
  --ps-form-field-helper-color: var(--ps-gray-300);
}
```

### Example 3: Runtime Customization

```javascript
// JavaScript API for theming
const formField = document.querySelector('.ps-form-field');

// Override single property
formField.style.setProperty('--ps-form-field-gap', 'var(--ps-size-4)');

// Override multiple properties
Object.assign(formField.style, {
  '--ps-form-field-label-color': 'var(--ps-purple-600)',
  '--ps-form-field-helper-color': 'var(--ps-purple-400)'
});

// Read computed value
const gap = getComputedStyle(formField).getPropertyValue('--ps-form-field-gap');
console.log(gap); // "var(--ps-size-2)" or "0.5rem"
```

---

## 📋 Quick Reference

### Component Variable Checklist

When creating a new component:

- [ ] Define all component variables at component root
- [ ] Use `--ps-{component}-{property}` naming
- [ ] Reference root tokens as defaults
- [ ] Apply variables in properties
- [ ] Variants override variables, not properties
- [ ] Document variables in README
- [ ] Test cascade with context overrides

### Variable Naming Quick Guide

```
Root Tokens:     --ps-{category}-{name}
Component Vars:  --ps-{component}-{property}
State Variants:  --ps-{component}-{state}-{property}
```

---

## 📚 Additional Resources

- [Bootstrap 5 CSS Variables](https://getbootstrap.com/docs/5.3/customize/css-variables/)
- [MDN: Using CSS Custom Properties](https://developer.mozilla.org/en-US/docs/Web/CSS/Using_CSS_custom_properties)
- [CSS Tricks: A Complete Guide to Custom Properties](https://css-tricks.com/a-complete-guide-to-custom-properties/)

---

**This system is the TARGET architecture. New components MUST use it. Legacy components migrate gradually.**
