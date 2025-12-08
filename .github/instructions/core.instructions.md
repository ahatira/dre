---
applyTo:
  - "**/*"
---

# Core Standards - PS Theme

**Version**: 3.0.0  
**Date**: 2025-12-05  
**Scope**: Foundation rules applicable to all project files

---

## 🎯 Project Overview

**PS Theme** is a custom Drupal theme for BNP Paribas Real Estate, compatible with Drupal 10/11.

- **Design System**: Built with Storybook (HTML edition) + Vite (Vanilla JS)
- **87 Components**: Following Atomic Design methodology
  - 19 Atoms (elements/)
  - 20 Molecules (components/)
  - 12 Organisms (collections/)
  - 8 Templates (layouts/)
  - 8 Pages (pages/)
- **Pixel Perfect**: Components must EXACTLY match design specifications
- **Drupal Integration**: Templates compatible with Drupal 10/11 Twig

---

## 🛠 Stack Technique

### Build System

```yaml
Bundler: Vite 5.x
CSS Processor: PostCSS
  Plugins:
    - postcss-import          # @import resolution
    - postcss-import-ext-glob # Glob patterns
    - postcss-nested          # CSS nesting with & syntax
    - postcss-global-data     # Custom media queries
    - postcss-preset-env      # Stage 4 features
    - autoprefixer            # Browser prefixes

Linters:
  - Stylelint: CSS linting (config-standard + order)
  - Biome: JS/JSX linting (CSS disabled)

Browserslist:
  - "last 2 versions and not dead"
  - ">= 1%"
```

### Design System Tools

```yaml
Storybook: HTML/Vite edition (NOT React)
Template Engine: Twig (Drupal 10/11 compatible)
Pattern Library: Atomic Design
Documentation: Autodocs via Storybook
```

### Node.js Tooling

```bash
# Development
npm run watch         # Vite dev server + Storybook (http://localhost:6006)

# Build
npm run build         # Compile assets + lint checks

# Storybook
npm run storybook:build  # Static build to storybook/

# Lint (auto-run via build/watch)
npm run lint          # Biome JS + Stylelint CSS
```

---

## 🗂 Project Architecture

### Directory Structure

```
ps_theme/
├── .github/                      # Documentation + CI/CD
│   ├── copilot-instructions.md  # Lightweight overview
│   └── instructions/            # Modular domain instructions
│       ├── core.instructions.md
│       ├── atomic-design.instructions.md
│       ├── components.instructions.md
│       ├── css.instructions.md
│       ├── storybook.instructions.md
│       ├── javascript.instructions.md
│       ├── templates.instructions.md
│       ├── accessibility.instructions.md
│       └── workflows.instructions.md
│
├── docs/
│   ├── design/                  # Component specifications (87 MD files)
│   │   ├── atoms/               # 19 atom specs
│   │   ├── molecules/           # 20 molecule specs
│   │   ├── organisms/           # 12 organism specs
│   │   ├── templates/           # 8 template specs
│   │   ├── pages/               # 8 page specs
│   │   └── tokens/              # 7 token reference YAMLs
│   └── ps-design/               # Implementation tracking
│       ├── README.md            # Design system overview
│       ├── INDEX.md             # Component inventory (progress)
│       └── CHANGELOG.md         # Implementation history
│
├── source/
│   ├── props/                   # Design Tokens (CSS Custom Properties)
│   │   ├── index.css           # Central import
│   │   ├── colors.css          # Color scales + semantics
│   │   ├── brand.css           # Brand colors
│   │   ├── fonts.css           # Typography scales
│   │   ├── sizes.css           # Spacing/sizing scales
│   │   ├── borders.css         # Radii + widths
│   │   ├── shadows.css         # Box shadows
│   │   ├── animations.css      # Durations + delays
│   │   ├── easing.css          # Timing functions
│   │   ├── zindex.css          # Z-index scales
│   │   └── icons.css           # Generated from SVG sprite (auto-compiled)
│   │
│   └── patterns/               # Component source code
│       ├── base/               # Base stories (token documentation)
│       ├── elements/           # Atoms (19 components)
│       ├── components/         # Molecules (20 components)
│       ├── collections/        # Organisms (12 components)
│       ├── layouts/            # Templates (8 components)
│       └── pages/              # Pages (8 components)
│
├── storybook/                   # Built Storybook (static site)
├── templates/                   # Drupal integration examples
├── vite.config.js              # Build configuration
└── package.json                # Dependencies + scripts
```

### Component File Structure (MANDATORY)

Each component MUST contain exactly **5 files**:

```
source/patterns/{level}/{component}/
├── {component}.twig          # Twig template
├── {component}.css           # CSS styles
├── {component}.yml           # Default data
├── {component}.stories.jsx   # Storybook stories
└── README.md                 # Documentation (English)
```

**Optional 6th file**:
```
└── {component}.js            # JavaScript behaviors (if needed)
```

**❌ NEVER**:
- Missing files (all 5 required)
- Inconsistent naming (`button.twig` vs `btn.css` ❌)
- Extra undocumented files

---

## 🎨 Design Tokens Organization

### Token Architecture (3 Layers)

```
colors.css (Base Palettes)        ← Official BNP color specifications
    ↓
brand.css (Semantic Tokens)       ← Meaningful names for use cases
    ↓
Components (var(--primary), etc.) ← Never reference palettes directly
```

### Token Files (source/props/*.css)

**⚠️ CRITICAL RULE**: NEVER edit `source/props/*.css` directly during component work.

Tokens are organized by category:

| File | Content | Examples |
|------|---------|----------|
| `colors.css` | **Official BNP Palettes** (50-900 scales) | `--green-600` (#00915A), `--pink-700` (#A12B66), `--teal-600` (#198754), `--red-600` (#EB3636), `--yellow-400` (#FBBF24), `--blue-600` (#2563EB), `--gold-400` (#D1AE6E), `--gray-900` (#333333) |
| `brand.css` | **Semantic tokens** mapping palettes to use cases | `--primary` (green-600), `--secondary` (pink-700), `--success` (teal-600), `--danger` (red-600) |
| `fonts.css` | Typography scales | `--font-size--2` (10px), `--font-size--1` (12px), `--font-size-0` to `--font-size-14` (14px-120px), `--font-weight-300/400/700` |
| `sizes.css` | Spacing/sizing scales | `--size-1` (4px), `--size-4` (16px), `--size-96` (384px), `--size-fluid-*` (clamp), `--size-content-*` (ch), `--size-header-*` (ch) |
| `borders.css` | Radii + border widths | `--radius-2`, `--radius-round` (pill), `--border-size-1` (1px), `--border-size-15` (1.5px), `--border-size-2` to `--border-size-5` |
| `shadows.css` | Box shadows | `--shadow-2`, `--shadow-4` |
| `animations.css` | Animation durations | `--duration-fast`, `--duration-normal` |
| `easing.css` | Timing functions | `--ease-3`, `--ease-in-out-4` |
| `zindex.css` | Z-index layers | `--z-dropdown`, `--z-modal` |
| `icons.css` | SVG sprite CSS | Auto-generated from `source/icons-source/` SVG files |

### Official BNP Color Palettes

**colors.css contains 5 complete 50-900 palettes:**

```css
/* PRIMARY GREEN - Brand Primary (#00915A) */
--green-50: #ebf7f4;   --green-600: #00915a;   --green-900: #01563a;

/* SECONDARY PINK - Brand Secondary (#A12B66) */
--pink-50: #f9ecf2;    --pink-700: #a12b66;    --pink-900: #751d4e;

/* SUCCESS TEAL - Distinct Success Green (#198754) */
--teal-50: #e7f4f1;    --teal-600: #198754;    --teal-900: #124a3b;

/* ERROR RED - Error/Danger (#EB3636) */
--red-50: #fef7f7;     --red-600: #eb3636;     --red-900: #a62626;

/* GREY SCALE - Neutrals (#333333 → #FFFFFF) */
--gray-50: #f9f9fb;    --gray-700: #434f57;    --gray-900: #333333;
```

**Key Design Decision**: PRIMARY green (#00915A) ≠ SUCCESS teal (#198754)
- Prevents color bleeding between brand identity and system feedback
- Enables distinct border colors: `--border-primary` vs `--border-success`
- Proper semantic meaning in UI

### Semantic Tokens in brand.css

```css
/* Use ONLY these in components, never reference palettes directly */
--primary: var(--green-600);      /* #00915A - Brand primary */
--secondary: var(--pink-700);     /* #A12B66 - Brand secondary */
--success: var(--teal-600);       /* #198754 - Success feedback (distinct!) */
--danger: var(--red-600);         /* #EB3636 - Error feedback */
--warning: var(--yellow-400);     /* #FBBF24 - Warning feedback */
--info: var(--blue-600);          /* #2563EB - Info feedback */
--gold: var(--gold-400);          /* #D1AE6E - Premium/accent */
--text-primary: #364152;          /* Custom text (not gray-700) */
--text-secondary: #76808d;        /* Secondary text */
--border-default: var(--gray-200);/* #D6DBDE - Standard borders */
--border-success: var(--teal-600);/* #198754 - Success borders (uses teal, not primary) */
```

### Token Naming Convention

```css
/* Primitive tokens - raw values from BNP specs */
--{color}-{scale}

/* Examples */
--green-600      /* Green palette, scale 600 (primary) */
--pink-50        /* Pink palette, scale 50 (lightest) */
--gray-700       /* Gray palette, scale 700 (dark) */

/* Semantic tokens - meaningful names for use cases */
--{purpose}[-{state}]

/* Examples */
--primary              /* Primary color (main use) */
--primary-hover        /* Primary on hover */
--success              /* Success state (distinct from primary) */
--border-success       /* Success borders (uses teal, not primary) */
```

### Token Usage Workflow

**COMPONENTS: Use semantic tokens ONLY**

```css
/* ✅ CORRECT: Reference semantic tokens */
.ps-button {
  background-color: var(--primary);
  color: var(--primary-text);
}

/* ❌ WRONG: Never reference palettes in components */
.ps-button {
  background-color: var(--green-600);  /* Don't do this */
}

/* ❌ WRONG: Never hardcode colors */
.ps-button {
  background-color: #00915A;  /* Don't do this */
}
```

**BEFORE creating a new token**:

1. **Search existing tokens**:
   ```bash
   grep -r "--token-name" source/props/
   ```

2. **Verify naming consistency** with existing tokens

3. **Reuse if similar** token exists

4. **If truly needed**:
   - Document need in component README
   - Propose via dedicated tokens PR/process
   - NEVER add directly to `source/props/*.css`

---

## 🚨 Zero Tolerance Rules

These violations will ALWAYS be rejected:

### 1. Hardcoded Values (ABSOLUTE ZERO TOLERANCE)

```css
/* ❌ NEVER */
color: #00915A;
padding: 16px 24px;
box-shadow: 0 2px 4px rgba(0,0,0,0.2);
transition: 150ms ease;

/* ✅ ALWAYS */
color: var(--primary);
padding: var(--size-4) var(--size-6);
box-shadow: var(--shadow-2);
transition: var(--duration-fast) var(--ease-3);
```

**Exceptions**:
- `0` values (margin, padding, border)
- `1px` for borders (WCAG minimum)
- Documented browser hacks (with comment + W3C reference)

### 2. Missing Required Files

All 5 files MUST exist:
- `.twig` ✅
- `.css` ✅
- `.yml` ✅
- `.stories.jsx` ✅
- `README.md` ✅

### 3. BEM Violations

```css
/* ❌ NEVER */
.component { }              /* Missing ps- prefix */
.ps-component-element { }   /* Wrong separator (- instead of __) */
.ps-component__el__nested { } /* Double underscore */

/* ✅ ALWAYS */
.ps-component { }
.ps-component__element { }
.ps-component--modifier { }
```

### 4. Non-Semantic Color Names

```yaml
# ❌ NEVER
color: 'green'
options: [green, blue, red, yellow]

# ✅ ALWAYS
color: 'primary'
options: [primary, secondary, success, warning, danger, info]
```

### 5. React/JSX in Storybook

```jsx
// ❌ NEVER (Storybook HTML edition, NOT React)
import React from 'react';
export const Default = () => <div>Content</div>;

// ✅ ALWAYS (Import Twig)
import componentTwig from './component.twig';
export const Default = {
  render: (args) => componentTwig(args),
};
```

### 6. Flat CSS (New Components)

```css
/* ❌ NEVER (new components) */
.ps-component { }
.ps-component__element { }
.ps-component--modifier { }

/* ✅ ALWAYS (use nesting) */
.ps-component {
  /* Base */
  
  &__element {
    /* Element styles */
  }
  
  &--modifier {
    /* Modifier styles */
  }
}
```

### 7. Missing Accessibility

```css
/* ❌ NEVER - Interactive without focus-visible */
.ps-button:hover { }
.ps-button:active { }

/* ✅ ALWAYS - Include focus-visible */
.ps-button {
  &:hover:not(:disabled) { }
  &:active:not(:disabled) { }
  &:focus-visible {
    outline: var(--border-size-2) solid var(--secondary);
    outline-offset: var(--border-size-2);
  }
}
```

### 8. Autodocs Missing

```jsx
// ❌ NEVER
export default {
  title: 'Elements/Component',
  // Missing tags: ['autodocs']
};

// ✅ ALWAYS
export default {
  title: 'Elements/Component',
  tags: ['autodocs'], // MANDATORY
};
```

---

## 🔧 Developer Workflow

### Standard Development Flow

1. **Read spec**: `docs/design/{level}/{component}.md`
2. **Check composition**: If molecule+, identify required atoms (see `atomic-design.instructions.md`)
3. **Create 5 files**: Follow `components.instructions.md` structure
4. **Apply standards**: CSS tokens, BEM, nesting, accessibility
5. **Build & test**: `npm run build` then `npm run watch`
6. **Audit**: Verify conformity (see `workflows.instructions.md`)
7. **Commit**: Structured message + update CHANGELOG
8. **Documentation**: Update `docs/ps-design/CHANGELOG.md`

### Build Commands

```bash
# Development (recommended)
npm run watch
# → Starts Vite + Storybook at http://localhost:6006
# → Auto-reload on file changes
# → Includes lint checks

# Production build
npm run build
# → Compiles all assets
# → Runs lint + format checks
# → Outputs to dist/ and storybook/

# Individual tasks
npm run lint              # Biome + Stylelint
npm run storybook:build   # Static Storybook only
```

### Configuration Files

| File | Purpose |
|------|---------|
| `vite.config.js` | Build tasks, PostCSS, entry points |
| `postcss.config.js` | PostCSS plugins configuration |
| `biome.json` | Biome linter + formatter settings |
| `.stylelintrc.json` | Stylelint CSS rules |
| `.storybook/` | Storybook configuration + preview |
| `package.json` | Dependencies, scripts, browserslist |

---

## 📖 Documentation Language

**CRITICAL**: ALL documentation MUST be in English.

**English Required**:
- README.md files
- Storybook descriptions (`parameters.docs.description.component`)
- ArgTypes descriptions
- Code comments (Twig, CSS, JS)
- Props tables
- Accessibility notes
- Git commit messages (structured)

**French Allowed**:
- User-facing content in templates (button labels, form text)
- Chat responses to team (conversation only)

**Never Translate**:
- Token names (`--primary`, `--size-4`)
- Class names (`.ps-button`, `.ps-badge--small`)
- File paths, variable names, code identifiers
- ARIA attributes (`aria-label`, `role`)

---

## 🎓 Reference Components

Study these for perfect implementations:

| Component | Path | Demonstrates |
|-----------|------|--------------|
| **Button** | `source/patterns/elements/button/` | CSS nesting, all states, semantic colors, complete stories |
| **Avatar** | `source/patterns/elements/avatar/` | Minimal markup, adaptive sizing, fallback patterns |
| **Badge** | `source/patterns/elements/badge/` | Semantic colors, pill variant, icon integration |
| **Divider** | `source/patterns/elements/divider/` | Simplicity, orientation variants, minimal code |
| **Heading** | `source/patterns/elements/heading/` | Typography tokens, semantic HTML, accessibility |
| **Link** | `source/patterns/elements/link/` | Interaction states, icon positioning, disabled state |

---

## 🔗 Cross-References

For domain-specific details, see:

- **Atomic Design**: `instructions/atomic-design.instructions.md`
- **Component Structure**: `instructions/components.instructions.md`
- **CSS Standards**: `instructions/css.instructions.md`
- **Storybook Format**: `instructions/storybook.instructions.md`
- **JavaScript Behaviors**: `instructions/javascript.instructions.md`
- **Twig Templates**: `instructions/templates.instructions.md`
- **Accessibility**: `instructions/accessibility.instructions.md`
- **Workflows & Prompts**: `instructions/workflows.instructions.md`

---

**Last Updated**: 2025-12-05  
**Maintainers**: Design System Team
