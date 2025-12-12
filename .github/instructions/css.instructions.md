---
title: CSS Standards
version: 3.1.0
lastUpdated: 2025-12-12
applyTo:
  - "**/*.css"
  - "source/props/**/*"
priority: CRITICAL
related:
  - composition-token-first.instructions.md
  - components.instructions.md
  - atomic-design.instructions.md
status: ACTIVE
---

# CSS Standards - PS Theme

**Scope**: CSS authoring, tokens, variables, nesting, performance

---

## 🎯 Token-First Composition

> **For composing components** (Molecules+), follow the **4-step cascade**: params → utils → **override tokens** ⭐ → targeted CSS.

**📘 Complete workflow + examples**: [composition-token-first.instructions.md](composition-token-first.instructions.md)

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
line-height: 1.5;
```

**✅ ALWAYS**:
```css
color: var(--primary);
padding: var(--size-4) var(--size-6);
box-shadow: var(--shadow-2);
font-size: var(--font-size-3);
transition: var(--duration-fast) var(--ease-3);
line-height: var(--leading-normal);
```

**Exceptions** (must be documented):
- `0` values (margin: 0, padding: 0)
- `1px` borders (WCAG minimum)
- Browser-specific hacks (with W3C reference comment)

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

### Token Verification Workflow

**BEFORE creating a token**:

```bash
# 1. Search if similar token exists
grep -r "--token-name" source/props/

# 2. Check naming consistency
# Example: --size-* uses incremental numbers (1-20)
#          --font-size-* uses incremental numbers (0-9)

# 3. Verify value fits existing progression
# Example: sizes progress: 0.25rem, 0.5rem, 0.75rem, 1rem...
```

**⚠️ CRITICAL**: NEVER edit `source/props/*.css` directly during component work. If token needed, document in component README and propose via dedicated PR.

---

## 🧩 CSS Variables System (3-Layer Architecture)

### Layer 1: Root Primitives (Global Tokens)

```css
/* source/props/colors.css (Layer 1 primitives) */
:root {
  --green-600: hsl(162, 72%, 38%);
  --gray-900: hsl(222, 47%, 11%);
  --size-4: 1rem;
  --primary: var(--green-600);   /* Semantic alias */
  --white: hsl(0, 0%, 100%);
}
```

**Purpose**: Foundation values that rarely change. Use semantic aliases (primary, secondary) rather than raw colors.

### Layer 2: Component-Scoped Variables (Defaults)

**MANDATORY for ALL new components**. These are component-level defaults that enable overrides:

```css
/* button.css - ATOM (autonomous) */
.ps-button {
  /* Layer 2: Component-scoped variables (with defaults from Layer 1) */
  --ps-button-padding-y: var(--size-3);
  --ps-button-padding-x: var(--size-6);
  --ps-button-bg: var(--primary);
  --ps-button-color: var(--white);
  --ps-button-border-radius: var(--radius-1);
  --ps-button-border-width: 1px;
  --ps-button-border-color: transparent;
  
  /* Use component variables (not Layer 1 tokens directly) */
  padding: var(--ps-button-padding-y) var(--ps-button-padding-x);
  background: var(--ps-button-bg);
  color: var(--ps-button-color);
  border: var(--ps-button-border-width) solid var(--ps-button-border-color);
  border-radius: var(--ps-button-border-radius);
}
```

**For composing components** (Molecules+), also include token overrides:

```css
/* card.css - MOLECULE (composes atoms) */
.ps-card {
  /* ═══ Token-First STEP 3: Override child atoms tokens ═══ */
  --ps-button-size: var(--size-6);
  --ps-badge-font-size: var(--font-size-0);
  --ps-heading-margin-bottom: var(--size-2);
  
  /* ═══ Layer 2: Own component-scoped variables ═══ */
  --ps-card-padding: var(--size-6);
  --ps-card-gap: var(--size-4);
  --ps-card-border-radius: var(--radius-2);
  
  /* Use component variables */
  padding: var(--ps-card-padding);
  display: flex;
  gap: var(--ps-card-gap);
  border-radius: var(--ps-card-border-radius);
}
```

**See**: `composition-token-first.instructions.md` for complete workflow.

**Purpose**: 
- Centralize component defaults in one place
- Enable context-specific overrides (next section)
- Support dark mode, theming, customization
- **For composing components**: Override parent/child tokens

### Layer 3: Context Overrides

```css
/* Override via modifier (within .ps-button nesting) */
.ps-button--large {
  --ps-button-padding-y: var(--size-4);
  --ps-button-padding-x: var(--size-8);
  font-size: var(--font-size-3);
}

.ps-button--primary {
  --ps-button-bg: var(--primary);
  --ps-button-color: var(--white);
}

.ps-button--secondary {
  --ps-button-bg: var(--secondary);
  --ps-button-color: var(--white);
}

/* Contextual override (only if necessary) */
.sidebar .ps-button {
  --ps-button-padding-y: var(--size-2);
  --ps-button-bg: var(--gray-700);
}
```

**Purpose**: 
- Component modifiers customize variables (preferred)
- Contextual selectors for edge cases only
- JavaScript can also override (via inline styles, but rarely needed)

### Benefits

- **Cascade control**: Override variables without specificity wars
- **Runtime customization**: Change via JavaScript
- **Dark mode**: `[data-theme="dark"] { --ps-button-bg: ... }`
- **Reusability**: Component maintains defaults, contexts customize

### Naming Convention

```css
/* Layer 1 tokens: Global (no ps- prefix needed in component CSS) */
--primary: var(--green-600);
--secondary: var(--blue-600);
--size-4: 1rem;
--radius-1: 4px;

/* Layer 2: Component variables (always use --ps-{component}-{property}) */
--ps-button-bg: var(--primary);
--ps-button-padding-y: var(--size-3);
--ps-button-hover-bg: var(--primary-dark);
--ps-button-disabled-opacity: 0.5;
```

**Key rule**: Component variables reference Layer 1 tokens directly (no `ps-` prefix in Layer 1).

### Migration Strategy

- ✅ **NEW components**: MUST use component-scoped variables (Layer 2)
- ⏳ **Legacy components**: Gradual migration (opportunistic)
- 📚 **Reference**: See `CSS_VARIABLES_SYSTEM.md` (archived) for deep dive

---

## 🪆 CSS Nesting (MANDATORY for New Components)

### Syntax: postcss-nested

PS Theme uses `postcss-nested` plugin supporting `&` syntax:

```css
.ps-component {
  /* Base styles */
  display: flex;
  gap: var(--size-2);
  
  /* Elements */
  &__icon {
    font-size: var(--font-size-3);
  }
  
  &__text {
    flex: 1;
  }
  
  /* Modifiers */
  &--primary {
    background: var(--primary);
  }
  
  &--large {
    padding: var(--size-4);
  }
  
  /* States */
  &:hover:not(:disabled) {
    transform: translateY(-1px);
  }
  
  &:focus-visible {
    outline: var(--border-size-2) solid var(--secondary);
    outline-offset: var(--border-size-2);
  }
}
```

### Nesting Order (Standard)

```css
.ps-component {
  /* 1. Component-scoped variables */
  --ps-component-bg: var(--primary);
  
  /* 2. Base styles */
  display: block;
  background: var(--ps-component-bg);
  
  /* 3. Elements */
  &__element {
    /* Element styles */
  }
  
  /* 4. Modifiers - Variants */
  &--variant {
    /* Variant styles */
  }
  
  /* 5. Modifiers - Sizes */
  &--small {
    /* Size styles */
  }
  
  /* 6. States */
  &:hover { }
  &:focus-visible { }
  &:active { }
  &:disabled { }
}
```

### Nesting Depth Limit

**Maximum 3 levels**:

```css
/* ✅ CORRECT - 3 levels max */
.ps-component {
  &__element {
    &--modifier {
      /* 3 levels - OK */
    }
  }
}

/* ❌ TOO DEEP - 4+ levels */
.ps-component {
  &__wrapper {
    &__inner {
      &__content {
        /* 4 levels - FORBIDDEN */
      }
    }
  }
}
```

**Solution**: Flatten structure with direct BEM selectors:

```css
/* ✅ BETTER - Flat BEM */
.ps-component {
  &__wrapper { }
  &__inner { }
  &__content { }
}
```

---

## 🎯 Cascade & Specificity

### Critical Rule: Base BEFORE Modifiers

**Source order matters** for cascade to work correctly:

```css
/* ✅ CORRECT - Base first, modifiers after */
.ps-avatar {
  &__text {
    font-size: var(--font-size-2); /* Base md = 16px */
  }
  
  /* Modifiers AFTER base to override */
  &--xs &__text {
    font-size: var(--font-size-0); /* 12px - overrides base */
  }
  
  &--lg &__text {
    font-size: var(--font-size-4); /* 22px - overrides base */
  }
}

/* ❌ WRONG - Modifiers before base */
.ps-avatar {
  &--xs &__text {
    font-size: var(--font-size-0); /* Will be overridden! */
  }
  
  &__text {
    font-size: var(--font-size-2); /* Wins (written after) */
  }
}
```

### Specificity Principle

**Modifiers should NOT increase specificity unnecessarily**:

```css
/* ✅ CORRECT - Same specificity */
.ps-component {
  color: var(--gray-700);
}

.ps-component--primary {
  color: var(--primary);
}

/* ❌ WRONG - Increased specificity */
.ps-component.ps-component--primary {
  color: var(--primary); /* .class.class = higher specificity */
}
```

---

## ♿ Accessibility (WCAG 2.2 AA)

> **Complete accessibility guide**: [accessibility.instructions.md](accessibility.instructions.md) (contrast ratios, keyboard patterns, ARIA, screen readers)

### Focus-Visible (MANDATORY for Interactives)

**ALL interactive elements MUST have focus-visible**:

```css
.ps-button {
  /* Base and hover states */
  &:hover:not(:disabled) {
    background: var(--primary-hover);
  }
  
  /* MANDATORY focus-visible */
  &:focus-visible {
    outline: var(--border-size-2) solid var(--secondary);
    outline-offset: var(--border-size-2);
  }
  
  /* Active state */
  &:active:not(:disabled) {
    transform: translateY(0);
  }
  
  /* Disabled state */
  &:disabled,
  &--disabled {
    opacity: 0.5;
    cursor: not-allowed;
    pointer-events: none;
  }
}
```

**Interactive elements**: buttons, links, form inputs, toggles, tabs, accordions, dropdowns.

### Contrast Ratios

| Content Type | Minimum Ratio | Token Example |
|--------------|---------------|---------------|
| Normal text (<18px) | 4.5:1 | `--gray-900` on `--white` |
| Large text (≥18px or ≥14px bold) | 3:1 | `--gray-700` on `--white` |
| UI components (borders, icons) | 3:1 | `--gray-400` border |
| Active UI components | 3:1 | Focus outline |

**Exception**: Disabled elements (no minimum ratio required).

### Keyboard Navigation

```css
/* Support keyboard interactions */
.ps-dropdown__trigger {
  &:focus-visible {
    outline: var(--border-size-2) solid var(--secondary);
  }
  
  /* Arrow keys handled in JS */
  &[aria-expanded="true"] {
    /* Visual state for keyboard users */
  }
}
```

---

## 🚀 Performance

### Critical CSS Patterns

**Use efficient selectors**:

```css
/* ✅ GOOD - Class selectors (fast) */
.ps-component { }
.ps-component__element { }

/* ⚠️ MODERATE - Attribute selectors */
[data-icon="check"] { }

/* ❌ SLOW - Universal + descendant */
* { } /* Avoid */
.ps-component * { } /* Avoid */
```

### Minimize Repaints/Reflows

**Animate transform/opacity only** (GPU-accelerated):

```css
/* ✅ GOOD - GPU-accelerated */
.ps-component {
  transition: 
    transform var(--duration-fast) var(--ease-3),
    opacity var(--duration-fast) var(--ease-3);
  
  &:hover {
    transform: translateY(-2px); /* GPU */
    opacity: 0.9; /* GPU */
  }
}

/* ❌ BAD - Causes reflow */
.ps-component {
  &:hover {
    width: 110%; /* Reflow */
    margin-top: -5px; /* Reflow */
  }
}
```

### Use `will-change` Sparingly

```css
/* ✅ GOOD - Only for animated elements */
.ps-modal {
  will-change: transform, opacity;
  
  &.ps-modal--closing {
    will-change: auto; /* Remove after animation */
  }
}

/* ❌ BAD - Everything */
* {
  will-change: transform; /* Performance disaster */
}
```

---

## 📐 Property Order (Recommended)

```css
.ps-component {
  /* 1. Component variables */
  --ps-component-bg: var(--primary);
  
  /* 2. Positioning */
  position: relative;
  top: 0;
  z-index: var(--z-dropdown);
  
  /* 3. Box model */
  display: flex;
  width: 100%;
  height: auto;
  margin: 0;
  padding: var(--size-4);
  
  /* 4. Typography */
  font-family: var(--font-sans);
  font-size: var(--font-size-2);
  font-weight: var(--font-weight-400);
  line-height: var(--leading-normal);
  text-align: left;
  
  /* 5. Visual */
  color: var(--gray-900);
  background: var(--ps-component-bg);
  border: var(--border-size-1) solid var(--gray-300);
  border-radius: var(--radius-2);
  box-shadow: var(--shadow-2);
  
  /* 6. Animations */
  transition: background var(--duration-fast) var(--ease-3);
  
  /* 7. Other */
  cursor: pointer;
}
```

**Automated**: Stylelint with `stylelint-order` plugin enforces this.

---

## 🚫 Anti-Patterns

### 1. Hardcoded Values

```css
❌ color: #00915A;
❌ padding: 16px;
❌ transition: 150ms ease;
```

### 2. Flat CSS (New Components)

```css
❌ .ps-component { }
❌ .ps-component__element { }
❌ .ps-component--modifier { }

/* Should use nesting with & */
```

### 3. Over-Nesting

```css
❌ .ps-component {
     &__wrapper {
       &__inner {
         &__content { /* 4 levels */ }
       }
     }
   }
```

### 4. Missing Focus-Visible

```css
❌ .ps-button:hover { }
   /* Missing :focus-visible */
```

### 5. Combined Modifiers

```css
❌ .ps-button--primary.ps-button--large { }
   /* Modifiers must work independently */
```

### 6. Direct Token Editing

```css
❌ Editing source/props/colors.css during component work
```

### 7. Non-Semantic Colors

```css
❌ .ps-button--green { }
   /* Use --primary, not color names */
```

### 8. Wrong Cascade Order

```css
❌ .ps-component--modifier { }
   .ps-component { } /* Base after modifier */
```

---

## 🔗 Cross-References

- **Core Standards**: `instructions/core.instructions.md`
- **Component Structure**: `instructions/components.instructions.md`
- **Accessibility**: `instructions/accessibility.instructions.md`
- **CSS Variables Deep Dive**: See archived `CSS_VARIABLES_SYSTEM.md`

---

**Last Updated**: 2025-12-05  
**Maintainers**: Design System Team
