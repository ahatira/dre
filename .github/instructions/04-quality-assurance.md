---
title: Quality Assurance & Validation
version: 4.0.0
lastUpdated: 2025-12-12
priority: HIGH
status: ACTIVE
---

# 04 - Quality Assurance & Validation

**Purpose**: Validation workflows, conformity audits, error troubleshooting, decision flowcharts.

**When to use**: Consult when validating implementations, fixing errors, scoring components, or making architectural decisions.

**Related files**:
- [01-core-principles.md](01-core-principles.md) - Foundational rules
- [02-component-development.md](02-component-development.md) - Complete workflow
- [03-technical-implementation.md](03-technical-implementation.md) - Code standards
- [05-maintenance.md](05-maintenance.md) - Refactoring and token creation

---

## 📐 Table of Contents

1. [Conformity Audit](#1-conformity-audit)
2. [Build Validation](#2-build-validation)
3. [Troubleshooting Guide](#3-troubleshooting-guide)
4. [Decision Flowcharts](#4-decision-flowcharts)
5. [Testing Checklist](#5-testing-checklist)

---

## 1. Conformity Audit

### 1.1 100-Point Scoring System

**When to audit**:
- After implementing a new component
- Before marking component as "done"
- When refactoring legacy components
- During code reviews

**Minimum passing score**: 80/90 for production

### 1.2 Audit Checklist

**Total**: 90 points (README.md supprimés décembre 2025)

#### Architecture & Dependencies (10 points)

- [ ] **5 pts** - Component respects atomic hierarchy (no circular dependencies)
- [ ] **5 pts** - All dependencies exist and are conformant

**Validation**:
```bash
# Check dependencies in Twig
grep -n "{% include" source/patterns/{level}/{component}/{component}.twig

# Verify atoms exist
ls source/patterns/elements/{atom}/
```

#### File Structure (8 points)

- [ ] **2 pts** - `.twig` file exists with header comment
- [ ] **2 pts** - `.css` file exists with nesting
- [ ] **2 pts** - `.yml` file exists with Real Estate data
- [ ] **2 pts** - `.stories.jsx` file exists with Autodocs

**Validation**:
```bash
# Check 4-file structure (README.md supprimés décembre 2025)
ls -la source/patterns/{level}/{component}/
# MUST have: .twig, .css, .yml, .stories.jsx
```

#### Twig Template (15 points)

- [ ] **2 pts** - Header comment with `@param` docs
- [ ] **2 pts** - `attributes` parameter present with `|without('class')`
- [ ] **3 pts** - Default values: `prop|default('value')`
- [ ] **3 pts** - Classes with ternary + `null` (NO arrow functions)
- [ ] **3 pts** - Composition via `{% include %}` with `only`
- [ ] **2 pts** - Real Estate vocabulary in text content (NOT generic lorem ipsum)

**Validation**:
```bash
# Check for attributes parameter
grep -n "attributes" source/patterns/{level}/{component}/{component}.twig

# Check for arrow functions (should return NOTHING)
grep -n "=>" source/patterns/{level}/{component}/{component}.twig

# Check for 'only' keyword
grep -n "only" source/patterns/{level}/{component}/{component}.twig
```

#### CSS Styles (20 points)

- [ ] **5 pts** - ALL values from tokens (no hardcoded: `#00915A`, `16px`)
- [ ] **5 pts** - Nesting with `&` syntax
- [ ] **3 pts** - Cascade order: Base → Elements → Modifiers → States
- [ ] **3 pts** - Modifiers work independently
- [ ] **2 pts** - Semantic colors (primary, not green)
- [ ] **2 pts** - Focus-visible for interactives

**Validation**:
```bash
# Check for hardcoded colors
grep -rE "#[0-9A-Fa-f]{6}" source/patterns/{level}/{component}/

# Check for hardcoded sizes
grep -rE "[0-9]+px|[0-9]+rem" source/patterns/{level}/{component}/{component}.css

# Check for focus-visible
grep -n "focus-visible" source/patterns/{level}/{component}/{component}.css
```

#### Storybook (20 points)

- [ ] **5 pts** - `tags: ['autodocs']` in export default
- [ ] **5 pts** - Import Twig: `import componentTwig from './component.twig'`
- [ ] **4 pts** - ArgTypes categorized (6 categories)
- [ ] **3 pts** - Description ≤ 2 lines
- [ ] **3 pts** - Stories: Default + Showcases (NO individual variants)

**Validation**:
```bash
# Check for autodocs tag
grep -n "tags.*autodocs" source/patterns/{level}/{component}/{component}.stories.jsx

# Check for React imports (should return NOTHING)
grep -n "import React" source/patterns/{level}/{component}/{component}.stories.jsx
```

#### YAML Configuration (10 points)

- [ ] **5 pts** - Realistic Real Estate data
- [ ] **3 pts** - All required props defined
- [ ] **2 pts** - Optional props with meaningful defaults

#### BEM Naming (5 points)

- [ ] **2 pts** - Prefix `ps-` mandatory
- [ ] **2 pts** - Format: `.ps-block__element--modifier`
- [ ] **1 pt** - No double underscore

**Validation**:
```bash
# Check BEM classes
grep -n "^\\." source/patterns/{level}/{component}/{component}.css
```

#### Accessibility (5 points)

- [ ] **2 pts** - Contrast: Text 4.5:1, UI 3:1 (WCAG AA)
- [ ] **2 pts** - Focus-visible for interactives
- [ ] **1 pt** - ARIA attributes (when needed)

**See**: [03-technical-implementation.md](03-technical-implementation.md) (Section 6: Accessibility)

### 1.3 Scoring Interpretation

| Score | Status | Action |
|-------|--------|--------|
| **90-100** | ✅ Production ready | Ship it |
| **75-89** | ⚠️ Minor fixes required | Fix violations, re-audit |
| **Below 75** | ❌ Major refactoring needed | Follow refactoring workflow |

### 1.4 Example Audit Report

```markdown
## Badge Conformity Audit

**Score**: 85/100 ⚠️ Minor fixes required
### 1.3 Scoring Interpretation

| Score | Status | Action |
|-------|--------|--------|
| **80-90** | ✅ Production ready | Ship it |
| **65-79** | ⚠️ Minor fixes required | Fix violations, re-audit |
| **Below 65** | ❌ Major refactoring needed | Follow refactoring workflow |

### 1.4 Example Audit Report

```markdown
## Badge Conformity Audit

**Score**: 75/90 ⚠️ Minor fixes required

### Required Actions

1. Replace `filter(v => v)` with ternary pattern
2. Replace `#00915A` with `var(--primary)`
3. Add `tags: ['autodocs']` to export default

**Estimated time**: 15 minutes
```

---

## 2. Build Validation

### 2.1 Pre-Commit Validation

**MANDATORY before every commit**:

```bash
npm run build
```

**Validates**:
- ✅ Biome lint (JavaScript, JSON)
- ✅ Biome format (all files)
- ✅ CSS compilation (Vite + PostCSS)
- ✅ No syntax errors (Twig rendered via Storybook)

**If build fails**: See [Section 3: Troubleshooting](#3-troubleshooting-guide)

### 2.2 Visual Validation

**Start watch mode**:
```bash
npm run watch
```

**Check in Storybook** (http://localhost:6006):
- [ ] Component renders correctly
- [ ] All variants/modifiers work
- [ ] Interactive features function
- [ ] Docs tab shows Autodocs
- [ ] No console errors/warnings

### 2.3 Common Build Errors

**See [Section 3: Troubleshooting](#3-troubleshooting-guide) for detailed solutions**

| Error | Quick Fix | Section |
|-------|-----------|---------|
| Token not found | `grep -r "--token-name" source/props/` | 3.1 |
| Hardcoded value | Replace with token | 3.2 |
| CSS nesting syntax | Use `&` syntax | 3.3 |
| Biome lint error | `npm run lint:fix` | 3.4 |
| Arrow function in Twig | Use ternary with `null` | 3.5 |
| Missing `only` | Add `only` to `{% include %}` | 3.7 |
| Missing autodocs | Add `tags: ['autodocs']` | 3.8 |
| React/JSX syntax | Use Twig renderer | 3.9 |

---

## 3. Troubleshooting Guide

### 3.1 Token Not Found

**Error Message**:
```bash
ERROR: CSS variable '--primary-dark' is not defined
  → source/patterns/elements/button/button.css:23:18
```

**Cause**: Using a token that doesn't exist in `source/props/*.css`

**Solution**:
```bash
# Step 1: Search for the token
grep -r "--primary-dark" source/props/

# Step 2: Check similar tokens
grep -r "--primary" source/props/brand.css
```

**Expected Output**:
```css
/* source/props/brand.css */
--primary: hsl(157, 100%, 29%);
--primary-hover: hsl(157, 100%, 24%);  /* Use this instead! */
--primary-active: hsl(157, 100%, 19%);
```

**Fix**:
```css
/* button.css - BEFORE */
.ps-button--primary:hover {
  background: var(--primary-dark);  /* ❌ Doesn't exist */
}

/* button.css - AFTER */
.ps-button--primary:hover {
  background: var(--primary-hover);  /* ✅ Correct token */
}
```

**Prevention**:
- Always verify tokens exist: `npm run tokens:check -- --token-name`
- Follow token naming: `--{color}-{state}` (hover, active, subtle)
- Never create tokens on-the-fly (see: [05-maintenance.md](05-maintenance.md))

### 3.2 Hardcoded Value Detected

**Error Message**:
```bash
Build Warning: Hardcoded color '#00915A' detected
  → source/patterns/elements/badge/badge.css:12:15
```

**Cause**: Using raw hex/rgb values instead of design tokens

**Solution**:
```bash
# Find all hardcoded colors
grep -E "#[0-9A-Fa-f]{6}|rgb\(|hsl\(" source/patterns/{component}/{component}.css

# Find matching token
grep -r "#00915A" source/props/
# Output: --primary: var(--green-600);
```

**Fix**:
```css
/* badge.css - BEFORE */
.ps-badge--primary {
  background: #00915A;  /* ❌ Hardcoded */
  color: #FFFFFF;       /* ❌ Hardcoded */
}

/* badge.css - AFTER */
.ps-badge--primary {
  background: var(--primary);  /* ✅ Token */
  color: var(--white);         /* ✅ Token */
}
```

**Common Hardcoded Values & Tokens**:

| Hardcoded | Use Token | Location |
|-----------|-----------|----------|
| `#00915A` | `var(--primary)` | `brand.css` |
| `#FFFFFF` | `var(--white)` | `colors.css` |
| `16px` | `var(--size-4)` | `sizes.css` |
| `150ms` | `var(--duration-fast)` | `animations.css` |
| `4px` | `var(--radius-1)` | `borders.css` |

### 3.3 CSS Nesting Syntax Error

**Error Message**:
```bash
ERROR: Unexpected selector '.ps-button__icon'
Expected nested selector with '&'
```

**Cause**: Using flat CSS selectors instead of PostCSS nesting

**Fix**:
```css
/* button.css - BEFORE (flat CSS) */
.ps-button {
  display: flex;
}

.ps-button__icon {  /* ❌ Flat selector */
  margin-right: var(--size-2);
}

/* button.css - AFTER (nested) */
.ps-button {
  display: flex;
  
  &__icon {  /* ✅ Nested with & */
    margin-right: var(--size-2);
  }
}
```

**Prevention**:
- ALL new components MUST use CSS nesting with `&`
- Follow CSS order: Base → Elements → Modifiers → States

### 3.4 Biome Lint Errors

**Error Message**:
```bash
ERROR: Unused variable 'icon'
  → button.stories.jsx:12:7
```

**Cause**: Variable declared but never used

**Solution**:
```bash
# Auto-fix
npm run lint:fix

# Check specific file
npx biome check --write source/patterns/{component}/{component}.stories.jsx
```

**Fix**:
```jsx
// BEFORE
import icon from './icon.svg';  // ❌ Imported but not used

// AFTER (remove unused)
// ✅ Removed unused import
```

**Common Biome Errors**:
- Unused variable → Remove or use it
- Missing semicolon → Add `;`
- Prefer `const` over `let` → Use `const` for immutable values
- Unnecessary template literal → Use plain string

### 3.5 Arrow Function in Twig (Drupal Incompatible)

**Error Message**:
```bash
ERROR: Unexpected '=>' (arrow function not supported)
  → badge.twig:10
```

**Cause**: Using JavaScript arrow functions in Twig

**Solution**:
```bash
# Find arrow functions in Twig (should return NOTHING)
grep -r "=>" source/patterns/ --include="*.twig"
```

**Fix**:
```twig
{# BEFORE (arrow function - ❌ FORBIDDEN) #}
{%- set classes = [
  'ps-badge',
  size,
  variant
]|filter(v => v) -%}  {# ❌ NOT Drupal-compatible #}

{# AFTER (ternary with null) #}
{%- set classes = [
  'ps-badge',
  size != 'md' ? 'ps-badge--' ~ size : null,
  variant != 'default' ? 'ps-badge--' ~ variant : null
] -%}  {# ✅ Twig's join() skips null automatically #}
```

**Why This Works**: Twig's `join()` filter automatically skips `null` values.

**Prevention**:
- NEVER use arrow functions in Twig: `v => v`
- Use ternary operator: `condition ? 'value' : null`

### 3.6 JavaScript Methods in Twig

**Error Message**:
```bash
ERROR: Unknown method 'map' in template
  → card.twig:25
```

**Cause**: Using JavaScript array methods (`.map()`, `.filter()`, `.includes()`)

**Solution**:
```bash
# Find JS methods in Twig
grep -rE "\\.map\\(|\\.filter\\(|\\.includes\\(" source/patterns/ --include="*.twig"
```

**Fix**:
```twig
{# BEFORE (JS methods - ❌ FORBIDDEN) #}
{%- set badges = items.map(i => i.badge) -%}  {# ❌ .map() not supported #}

{# AFTER (Twig loops) #}
{%- set badges = [] -%}
{% for item in items %}
  {%- set badges = badges|merge([item.badge]) -%}
{% endfor %}
```

**Drupal Twig Limitations**:
- ❌ NO `.map()`, `.filter()`, `.reduce()`
- ❌ NO arrow functions (`=>`)
- ❌ NO spread operator (`...`)
- ✅ USE Twig native: `for`, `if`, `merge`, `slice`

### 3.7 Missing `only` Keyword in Include

**Error Message**:
```bash
WARNING: Variable pollution detected
  → card.twig:18
```

**Cause**: Using `{% include %}` without `only` keyword

**Solution**:
```bash
# Find includes without 'only'
grep -rn "{% include" source/patterns/ --include="*.twig" | grep -v "only"
```

**Fix**:
```twig
{# BEFORE (risky - variables leak) #}
{% include '@elements/button/button.twig' with {
  text: cta_text
} %}  {# ❌ Missing 'only' #}

{# AFTER (safe - isolated scope) #}
{% include '@elements/button/button.twig' with {
  text: cta_text
} only %}  {# ✅ Button only receives specified props #}
```

**Why `only` is CRITICAL**:
- Prevents variable pollution
- Makes dependencies explicit
- Avoids naming conflicts
- Improves maintainability

### 3.8 Missing `tags: ['autodocs']`

**Error Message**:
```bash
WARNING: Component Docs page is empty
  → Badge has no Autodocs
```

**Cause**: Storybook export missing `tags: ['autodocs']`

**Solution**:
```bash
# Find stories without autodocs
grep -L "tags.*autodocs" source/patterns/elements/*/\*.stories.jsx

# Check specific file
grep "tags" source/patterns/elements/badge/badge.stories.jsx
```

**Fix**:
```jsx
// BEFORE (no autodocs)
export default {
  title: 'Elements/Badge',
  // ❌ Missing tags!
};

// AFTER (with autodocs)
export default {
  title: 'Elements/Badge',
  tags: ['autodocs'],  // ✅ MANDATORY
};
```

**Exception**: Base stories (`source/patterns/base/*`) don't use Autodocs.

### 3.9 React/JSX in Storybook (Wrong Edition)

**Error Message**:
```bash
ERROR: Unexpected token '<'
  → button.stories.jsx:8:10
PS Theme uses HTML edition, not React
```

**Cause**: Using React JSX syntax in Storybook HTML edition

**Solution**:
```bash
# Find React imports
grep -rn "import React" source/patterns/ --include="*.stories.jsx"

# Find JSX syntax
grep -rn "return <" source/patterns/ --include="*.stories.jsx"
```

**Fix**:
```jsx
// BEFORE (React - ❌ WRONG)
import React from 'react';

export const Default = () => (
  <button className="ps-button">Click</button>
);

// AFTER (HTML edition - ✅ CORRECT)
import buttonTwig from './button.twig';
import data from './button.yml';

export const Default = {
  render: (args) => buttonTwig(args),
  args: data,
};
```

**PS Theme Storybook Edition**: HTML/Vite (NOT React)

---

## 4. Decision Flowcharts

### 4.1 Component Level Selection

**Question**: "What atomic level should my component be?"

**Decision Criteria**:

**ATOM** (Elements):
- ✅ Indivisible (can't break into smaller components)
- ✅ Maps to basic HTML element
- ✅ Standalone (no composition)
- ✅ NO composition (doesn't include others)
- ❌ Token-First workflow does NOT apply
- **Examples**: Button, Badge, Icon, Heading, Link

**MOLECULE** (Components):
- ✅ Composes 2+ atoms
- ✅ Single responsibility
- ✅ Portable (drop anywhere)
- ✅ **Token-First workflow APPLIES**
- **Examples**: Card, Form Field, Alert, Breadcrumb

**ORGANISM** (Collections):
- ✅ Composes molecules and/or atoms
- ✅ Complex layout
- ✅ Distinct UI section
- ✅ **Token-First workflow APPLIES**
- **Examples**: Header, Footer, Property Grid, Navigation

**TEMPLATE** (Layouts):
- ✅ Full page layout
- ✅ Composes organisms + molecules
- ✅ **Token-First workflow APPLIES**
- **Examples**: Homepage Layout, Article Page

**See**: [01-core-principles.md](01-core-principles.md) (Section 2: Atomic Design)

### 4.2 Composition Method Selection

**Question**: "How should I compose/reuse another component?"

**{% include %}** - Simple Prop Passing (MOST COMMON):
```twig
{% include '@elements/button/button.twig' with {
  text: 'Submit',
  color: 'primary'
} only %}
```
- ✅ Most common (90% of cases)
- ✅ Clean prop contract
- ❌ Can't customize inner structure

**{% embed %}** - Block Replacement (ADVANCED):
```twig
{% embed '@components/card/card.twig' %}
  {% block card_header %}
    <div class="custom-header">...</div>
  {% endblock %}
{% endembed %}
```
- ✅ Replace specific blocks
- ⚠️ Use sparingly (complexity risk)

**{% extends %}** - Base Template Extension (RARE):
```twig
{% extends '@layouts/base.twig' %}
{% block content %}
  <!-- Page content -->
{% endblock %}
```
- ✅ Page-level templates only
- ⚠️ Creates tight coupling

### 4.3 Token Usage Decision

**Question**: "Should I use an existing token or create a new one?"

**Decision Tree**:

```
Does token exist?
├─ YES → Use it ✅
│
└─ NO → Is it component-specific customization?
    ├─ YES → Use component-scoped variable (Layer 2) ✅
    │         Example: --ps-button-padding-x: var(--size-3);
    │
    └─ NO → Is it a new global design value?
        ├─ YES → Follow token creation process ⚠️
        │         (See: 05-maintenance.md, Section 1)
        │
        └─ NO → Re-evaluate: Can you compose existing tokens?
                 Try: calc(), fallback values, utilities
```

**Token Verification**:
```bash
# Search for token
npm run tokens:check -- --token-name

# Check token file
grep -r "--token-name" source/props/
```

**See**: [05-maintenance.md](05-maintenance.md) (Section 1: Token Creation Process)

### 4.4 Workflow Selection

**Question**: "Which workflow should I follow?"

**New Component** → Follow [02-component-development.md](02-component-development.md) (Steps 1-11)

**Refactor Legacy** → Follow [05-maintenance.md](05-maintenance.md) (Section 3: Standardization)

**Audit Conformity** → Follow [Section 1](#1-conformity-audit) above

**Fix Build Error** → Follow [Section 3](#3-troubleshooting-guide) above

**Create Token** → Follow [05-maintenance.md](05-maintenance.md) (Section 1: Token Creation)

### 4.5 CSS Override Strategy (Token-First)

**Question**: "How should I customize a composed component?"

**4-Step Token-First Cascade**:

1. **Check Native Parameters** → Does component provide param?
2. **Check Utility Classes** → Can utility class solve it?
3. **Override Tokens** ⭐ **PREFERRED** → Override parent/child tokens
4. **Targeted CSS** (Last Resort) → Write scoped CSS override

**See**: [01-core-principles.md](01-core-principles.md) (Section 5: Token-First Composition)

---

## 5. Testing Checklist

### 5.1 Component Testing

**Before marking component "done"**:

#### Visual Testing
- [ ] Renders correctly in all variants
- [ ] Responsive (mobile, tablet, desktop)
- [ ] All states work (hover, focus, active, disabled)
- [ ] Icons display correctly
- [ ] Images load (with fallbacks)

#### Functional Testing
- [ ] Interactive features work (click, toggle, etc.)
- [ ] Keyboard navigation (Tab, Enter, Space, Escape)
- [ ] Form inputs validate
- [ ] Links navigate correctly
- [ ] Modals trap focus

#### Accessibility Testing
- [ ] WCAG AA contrast ratios (4.5:1 text, 3:1 UI)
- [ ] Focus-visible on all interactives
- [ ] ARIA attributes present (when needed)
- [ ] Screen reader announces correctly
- [ ] Keyboard-only navigation works
- [ ] Alt text on images

#### Cross-Browser Testing
- [ ] Chrome (latest)
- [ ] Firefox (latest)
- [ ] Safari (latest)
- [ ] Edge (latest)

#### Build Validation
- [ ] `npm run build` passes (no errors)
- [ ] No console errors in Storybook
- [ ] Autodocs generate correctly
- [ ] README documentation complete

### 5.2 Pre-Commit Checklist

**MANDATORY before every commit**:

- [ ] Run `npm run build` (passes with 0 errors)
- [ ] Visual check in Storybook (http://localhost:6006)
- [ ] Conformity audit score ≥ 80/90
- [ ] Git commit message structured
- [ ] CHANGELOG.md updated

### 5.3 Pre-Merge Checklist

**Before merging feature branch**:

- [ ] All components 100% conformity
- [ ] Build passes on CI/CD
- [ ] Visual regression tests pass
- [ ] Accessibility audit WCAG AA
- [ ] Code review approved
- [ ] Documentation complete

---

## 📝 Quick Reference Summary

| Need | Tool/Command | Section |
|------|--------------|---------|
| Audit component | 100-point checklist | 1.2 |
| Score component | Scoring system (90+ required) | 1.3 |
| Validate build | `npm run build` | 2.1 |
| Visual check | `npm run watch` → localhost:6006 | 2.2 |
| Token not found | `npm run tokens:check` | 3.1 |
| Hardcoded value | `grep -E "#[0-9A-Fa-f]{6}"` | 3.2 |
| Arrow function | Replace with ternary + null | 3.5 |
| Missing autodocs | Add `tags: ['autodocs']` | 3.8 |
| Component level | Decision flowchart | 4.1 |
| Composition method | Include vs Embed vs Extend | 4.2 |
| Token usage | Use vs Create decision tree | 4.3 |
| CSS override | Token-First 4-step cascade | 4.5 |

---

**Version**: 4.0.0  
**Last Updated**: 2025-12-12  
**Maintainers**: Design System Team
