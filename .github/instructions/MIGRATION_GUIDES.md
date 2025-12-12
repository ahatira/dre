---
title: Migration Guides
version: 1.0.0
lastUpdated: 2025-12-12
applyTo:
  - "**/*"
priority: HIGH
related:
  - workflows.instructions.md
  - TROUBLESHOOTING_GUIDE.md
  - DECISION_FLOWCHARTS.md
status: ACTIVE
---

# Migration Guides - PS Theme

**Scope**: Legacy pattern migration, deprecation process, breaking changes, upgrade workflows

---

## 📖 When to Use This File

**Use this file when you need to:**
- ✅ **Migrate legacy components** to new standards (flat CSS, hardcoded values, non-BEM)
- ✅ **Understand deprecation process** (timeline, communication, sunset)
- ✅ **Document breaking changes** (changelog format, semantic versioning)
- ✅ **Follow upgrade workflows** (step-by-step component conversion)
- ✅ **Plan migration strategy** (prioritization, risk assessment, rollback)

**DO NOT use this file for:**
- ❌ Creating **new components** (see: workflows.instructions.md)
- ❌ **Troubleshooting errors** (see: TROUBLESHOOTING_GUIDE.md)
- ❌ Making **architecture decisions** (see: DECISION_FLOWCHARTS.md)
- ❌ Learning **CSS patterns** (see: css.instructions.md)
- ❌ Understanding **composition** (see: atomic-design.instructions.md)

**Audience**: Developers migrating legacy code, maintainers managing deprecations, teams planning upgrades

---

## 🎯 Overview

This guide provides comprehensive workflows for:

1. **Legacy Pattern Catalog** - Common patterns requiring migration
2. **Migration Workflows** - Step-by-step conversion processes
3. **Deprecation Process** - Formal deprecation lifecycle
4. **Breaking Changes** - Documentation and versioning
5. **Upgrade Checklists** - Component-by-component validation

---

## 📋 Table of Contents

1. [Legacy Pattern Catalog](#1-legacy-pattern-catalog)
2. [Migration Workflows](#2-migration-workflows)
3. [Deprecation Process](#3-deprecation-process)
4. [Breaking Changes Documentation](#4-breaking-changes-documentation)
5. [Component Upgrade Checklist](#5-component-upgrade-checklist)
6. [Migration Tools & Scripts](#6-migration-tools--scripts)
7. [Risk Assessment Matrix](#7-risk-assessment-matrix)

---

## 1. Legacy Pattern Catalog

### 1.1 Flat CSS (No Nesting)

**Legacy Pattern**:
```css
/* ❌ OLD - Flat CSS without nesting */
.ps-component { }
.ps-component__element { }
.ps-component--modifier { }
```

**Target Pattern**:
```css
/* ✅ NEW - PostCSS nesting with & syntax */
.ps-component {
  /* Base styles */
  
  &__element {
    /* Element styles */
  }
  
  &--modifier {
    /* Modifier styles */
  }
}
```

**Migration Impact**: Medium (structural change, no behavioral change)

---

### 1.2 Hardcoded Values

**Legacy Pattern**:
```css
/* ❌ OLD - Hardcoded colors, sizes, durations */
.ps-button {
  padding: 16px 24px;
  background: #00915A;
  color: #FFFFFF;
  border-radius: 8px;
  transition: background 150ms ease;
}
```

**Target Pattern**:
```css
/* ✅ NEW - Design tokens only */
.ps-button {
  padding: var(--size-4) var(--size-6);
  background: var(--primary);
  color: var(--white);
  border-radius: var(--radius-2);
  transition: background var(--duration-fast) var(--ease-3);
}
```

**Migration Impact**: High (visual changes if tokens differ from hardcoded values)

---

### 1.3 Non-BEM Class Names

**Legacy Pattern**:
```html
<!-- ❌ OLD - No prefix, inconsistent naming -->
<div class="button button-primary button-large">
  <span class="icon">...</span>
  <span class="text">...</span>
</div>
```

**Target Pattern**:
```html
<!-- ✅ NEW - BEM with ps- prefix -->
<button class="ps-button ps-button--primary ps-button--large">
  <span class="ps-button__icon">...</span>
  <span class="ps-button__text">...</span>
</button>
```

**Migration Impact**: High (CSS selector changes, template updates)

---

### 1.4 Inline Twig Markup (No Composition)

**Legacy Pattern**:
```twig
{# ❌ OLD - Duplicated atom markup #}
<div class="ps-card">
  <button class="ps-button ps-button--primary">
    <span class="ps-button__icon" data-icon="check"></span>
    <span class="ps-button__text">{{ cta_text }}</span>
  </button>
</div>
```

**Target Pattern**:
```twig
{# ✅ NEW - Composition with {% include %} #}
<div class="ps-card">
  {% include '@elements/button/button.twig' with {
    text: cta_text,
    color: 'primary',
    icon_start: 'check'
  } only %}
</div>
```

**Migration Impact**: Medium (template restructure, no visual change)

---

### 1.5 Arrow Functions in Twig

**Legacy Pattern**:
```twig
{# ❌ OLD - Arrow function (Drupal incompatible) #}
{% set classes = [
  'ps-component',
  size,
  color
]|filter(v => v) %}
```

**Target Pattern**:
```twig
{# ✅ NEW - Ternary with null (Drupal compatible) #}
{% set classes = [
  'ps-component',
  size != 'md' ? 'ps-component--' ~ size : null,
  color != 'primary' ? 'ps-component--' ~ color : null
] %}
```

**Migration Impact**: Critical (breaks in Drupal, must fix before deployment)

---

### 1.6 Missing Focus-Visible

**Legacy Pattern**:
```css
/* ❌ OLD - No focus indicator */
.ps-button {
  &:hover {
    background: var(--primary-hover);
  }
  /* Missing :focus-visible */
}
```

**Target Pattern**:
```css
/* ✅ NEW - WCAG AA compliant focus indicator */
.ps-button {
  &:hover:not(:disabled) {
    background: var(--primary-hover);
  }
  
  &:focus-visible {
    outline: var(--border-size-2) solid var(--secondary);
    outline-offset: var(--border-size-2);
  }
}
```

**Migration Impact**: Critical (accessibility violation, legal requirement)

---

### 1.7 Missing Storybook Autodocs

**Legacy Pattern**:
```jsx
// ❌ OLD - No autodocs tag
export default {
  title: 'Elements/Button',
  render: (args) => buttonTwig(args),
};
```

**Target Pattern**:
```jsx
// ✅ NEW - Autodocs enabled
export default {
  title: 'Elements/Button',
  tags: ['autodocs'], // ← MANDATORY
  render: (args) => buttonTwig(args),
  argTypes: {
    // Categorized argTypes
  },
};
```

**Migration Impact**: Low (documentation only, no runtime change)

---

### 1.8 Wrong Cascade Order

**Legacy Pattern**:
```css
/* ❌ OLD - Modifiers before base (cascade broken) */
.ps-avatar {
  &--large {
    width: var(--size-12);
  }
  
  /* Base comes after modifier - will override! */
  width: var(--size-10);
}
```

**Target Pattern**:
```css
/* ✅ NEW - Base first, modifiers after */
.ps-avatar {
  /* Base styles first */
  width: var(--size-10);
  height: var(--size-10);
  
  /* Modifiers after base */
  &--large {
    width: var(--size-12);
    height: var(--size-12);
  }
}
```

**Migration Impact**: High (visual changes if modifiers don't work)

---

### 1.9 Data-Icon Attribute Pattern

**Legacy Pattern**:
```html
<!-- ❌ OLD - data-icon attribute (CSS-only) -->
<span class="ps-button__icon" data-icon="check"></span>
```

**Target Pattern**:
```twig
{# ✅ NEW - SVG sprite via icon component #}
{% include '@elements/icon/icon.twig' with {
  name: 'check',
  size: 'md',
  attributes: create_attribute().addClass('ps-button__icon')
} only %}
```

**Migration Impact**: Medium (markup change, visual parity if sprite correct)

**Reference**: See `docs/ps-design/MIGRATION_INDEX.md` for Icon System migration

---

### 1.10 Component-Scoped Variables Missing (Layer 2)

**Legacy Pattern**:
```css
/* ❌ OLD - Direct token usage (no override capability) */
.ps-button {
  padding: var(--size-4);
  background: var(--primary);
}
```

**Target Pattern**:
```css
/* ✅ NEW - Component-scoped variables (Layer 2) */
.ps-button {
  /* Layer 2: Component-scoped variables */
  --ps-button-padding-y: var(--size-3);
  --ps-button-padding-x: var(--size-6);
  --ps-button-bg: var(--primary);
  
  /* Use component variables */
  padding: var(--ps-button-padding-y) var(--ps-button-padding-x);
  background: var(--ps-button-bg);
}
```

**Migration Impact**: Medium (enables customization, no visual change)

---

## 2. Migration Workflows

### 2.1 Flat CSS → Nested CSS

**Time Estimate**: 15-30 minutes per component

**Prerequisites**:
- Component has flat CSS
- PostCSS configured with `postcss-nested`

**Steps**:

1. **Backup Original**:
```bash
cp source/patterns/{level}/{component}/{component}.css source/patterns/{level}/{component}/{component}.css.bak
```

2. **Identify Nesting Opportunities**:
```bash
# Find all selectors for this component
grep -n "^\\.ps-{component}" source/patterns/{level}/{component}/{component}.css
```

3. **Restructure with Nesting**:

**Before**:
```css
.ps-badge { }
.ps-badge__text { }
.ps-badge--primary { }
.ps-badge--large { }
```

**After**:
```css
.ps-badge {
  /* Base styles */
  
  &__text {
    /* Element styles */
  }
  
  &--primary {
    /* Modifier styles */
  }
  
  &--large {
    /* Size modifier */
  }
}
```

4. **Validate Cascade Order**:
- ✅ Base styles FIRST
- ✅ Elements next
- ✅ Modifiers after
- ✅ States last (`:hover`, `:focus-visible`)

5. **Test**:
```bash
npm run build  # CSS must compile
npm run watch  # Visual check in Storybook
```

6. **Commit**:
```bash
git add source/patterns/{level}/{component}/{component}.css
git commit -m "refactor({level}): migrate {component} to nested CSS

- Restructure flat CSS to PostCSS nesting with & syntax
- Maintain cascade order: base → elements → modifiers → states
- No visual changes (compiled output identical)
- Conformity: Nested CSS pattern"
```

---

### 2.2 Hardcoded Values → Design Tokens

**Time Estimate**: 30-60 minutes per component

**Prerequisites**:
- Identify all hardcoded values (colors, sizes, durations, shadows, etc.)
- Verify target tokens exist in `source/props/`

**Steps**:

1. **Identify Hardcoded Values**:
```bash
# Find hardcoded colors (hex)
grep -rE "#[0-9a-fA-F]{3,6}" source/patterns/{level}/{component}/{component}.css

# Find hardcoded sizes (px, rem)
grep -rE "[0-9]+(px|rem)" source/patterns/{level}/{component}/{component}.css

# Find hardcoded durations (ms, s)
grep -rE "[0-9]+(ms|s)" source/patterns/{level}/{component}/{component}.css
```

2. **Create Token Mapping Table**:

| Hardcoded Value | Token | File |
|-----------------|-------|------|
| `#00915A` | `var(--primary)` | `brand.css` |
| `16px` / `1rem` | `var(--size-4)` | `sizes.css` |
| `150ms` | `var(--duration-fast)` | `animations.css` |
| `8px` | `var(--radius-2)` | `borders.css` |
| `0 2px 4px rgba(0,0,0,0.2)` | `var(--shadow-2)` | `shadows.css` |

3. **Verify Tokens Exist**:
```bash
# Search for token in props
grep -r "--primary" source/props/
grep -r "--size-4" source/props/
grep -r "--duration-fast" source/props/
```

4. **Replace Values Systematically**:

**Before**:
```css
.ps-button {
  padding: 16px 24px;
  background: #00915A;
  border-radius: 8px;
  transition: background 150ms ease;
}
```

**After**:
```css
.ps-button {
  padding: var(--size-4) var(--size-6);
  background: var(--primary);
  border-radius: var(--radius-2);
  transition: background var(--duration-fast) var(--ease-3);
}
```

5. **Visual Regression Test**:
- Compare before/after in Storybook
- Check all variants (sizes, colors, states)
- Verify responsive behavior

6. **Document Token Mapping**:
Update `README.md` with token reference:
```markdown
## Design Tokens

- `--primary` - Primary brand color (#00915A)
- `--size-4` - Padding base (16px / 1rem)
- `--duration-fast` - Transition speed (150ms)
```

7. **Commit**:
```bash
git add source/patterns/{level}/{component}/
git commit -m "refactor({level}): replace hardcoded values with tokens in {component}

- Replace #00915A → var(--primary)
- Replace 16px → var(--size-4), 24px → var(--size-6)
- Replace 150ms → var(--duration-fast)
- Replace 8px radius → var(--radius-2)
- Visual parity verified (no changes to rendered output)
- Conformity: Zero hardcoded values"
```

---

### 2.3 Non-BEM → BEM Class Names

**Time Estimate**: 60-90 minutes per component (high impact)

**Prerequisites**:
- Component uses non-BEM classes
- Templates and CSS both require updates

**Steps**:

1. **Audit Current Classes**:
```bash
# Find all class names in template
grep -oP 'class="[^"]*"' source/patterns/{level}/{component}/{component}.twig

# Find all CSS selectors
grep -n "^\\." source/patterns/{level}/{component}/{component}.css
```

2. **Create BEM Mapping**:

| Old Class | New BEM Class | Type |
|-----------|---------------|------|
| `.button` | `.ps-button` | Block |
| `.button-primary` | `.ps-button--primary` | Modifier |
| `.icon` | `.ps-button__icon` | Element |
| `.text` | `.ps-button__text` | Element |

3. **Update Twig Template**:

**Before**:
```twig
<button class="button button-primary">
  <span class="icon" data-icon="{{ icon }}"></span>
  <span class="text">{{ text }}</span>
</button>
```

**After**:
```twig
{% set classes = [
  'ps-button',
  color != 'primary' ? 'ps-button--' ~ color : null,
  size != 'md' ? 'ps-button--' ~ size : null
] %}

<button class="{{ classes|join(' ')|trim }}">
  {% if icon_start %}
    <span class="ps-button__icon" data-icon="{{ icon_start }}"></span>
  {% endif %}
  
  <span class="ps-button__text">{{ text }}</span>
</button>
```

4. **Update CSS**:

**Before**:
```css
.button { }
.button-primary { }
.icon { }
.text { }
```

**After**:
```css
.ps-button {
  /* Base styles */
  
  &__icon {
    /* Element styles */
  }
  
  &__text {
    /* Element styles */
  }
  
  &--primary {
    /* Modifier styles */
  }
}
```

5. **Update Stories**:
```jsx
// Update argTypes and examples
export default {
  title: 'Elements/Button',
  // ... rest of config
};
```

6. **Update README**:
```markdown
## BEM Structure

```css
.ps-button              /* Block */
.ps-button__icon        /* Element */
.ps-button__text        /* Element */
.ps-button--primary     /* Modifier */
```
```

7. **Test All Variants**:
- Check every size variant
- Check every color variant
- Check every state (hover, focus, active, disabled)
- Check composition contexts (card, form, etc.)

8. **Commit**:
```bash
git add source/patterns/{level}/{component}/
git commit -m "refactor({level}): migrate {component} to BEM naming

- Rename .button → .ps-button (add ps- prefix)
- Rename .button-primary → .ps-button--primary (use -- for modifiers)
- Rename .icon → .ps-button__icon (use __ for elements)
- Rename .text → .ps-button__text
- Update Twig template with BEM classes
- Update CSS with BEM selectors + nesting
- Update README with BEM structure
- Conformity: BEM naming 100%"
```

---

### 2.4 Inline Twig → Composition

**Time Estimate**: 45-60 minutes per component

**Prerequisites**:
- Component duplicates atom markup inline
- Target atoms exist and support `attributes` parameter

**Steps**:

1. **Identify Duplicated Atoms**:
```bash
# Search for inline button markup
grep -n "ps-button" source/patterns/{level}/{component}/{component}.twig

# Search for inline icon markup
grep -n "data-icon" source/patterns/{level}/{component}/{component}.twig
```

2. **Replace with {% include %}**:

**Before**:
```twig
{# ❌ OLD - Duplicated button markup #}
<div class="ps-card__footer">
  <button class="ps-button ps-button--{{ cta_color }}">
    <span class="ps-button__text">{{ cta_text }}</span>
  </button>
</div>
```

**After**:
```twig
{# ✅ NEW - Composition with include #}
<div class="ps-card__footer">
  {% include '@elements/button/button.twig' with {
    text: cta_text,
    color: cta_color|default('primary'),
    attributes: create_attribute().addClass('ps-card__cta')
  } only %}
</div>
```

3. **Update CSS** (if needed):
```css
.ps-card {
  &__footer {
    /* Layout only, no button styles */
    display: flex;
    justify-content: flex-end;
    margin-top: auto;
  }
  
  /* Token overrides (Token-First pattern) */
  --ps-button-size: var(--size-6);
}
```

4. **Test Composition**:
- Verify button renders correctly
- Check all button variants work
- Verify `attributes.addClass()` applies class correctly

5. **Commit**:
```bash
git add source/patterns/{level}/{component}/
git commit -m "refactor({level}): migrate {component} to composition pattern

- Replace inline button markup with {% include %}
- Pass props via 'with' clause + 'only' keyword
- Use attributes.addClass() for parent context class
- Remove duplicated button styles from CSS
- Override button tokens via Token-First pattern
- Conformity: Composition 100%"
```

---

### 2.5 Arrow Functions → Ternary Pattern

**Time Estimate**: 10-15 minutes per component

**Prerequisites**:
- Component uses arrow functions in Twig (Drupal incompatible)
- Must fix BEFORE Drupal deployment

**Steps**:

1. **Identify Arrow Functions**:
```bash
# Search for arrow functions
grep -n "=>" source/patterns/{level}/{component}/{component}.twig
```

2. **Replace with Ternary + null**:

**Before**:
```twig
{# ❌ OLD - Arrow function (Drupal incompatible) #}
{% set classes = [
  'ps-component',
  size,
  color,
  disabled ? 'ps-component--disabled' : ''
]|filter(v => v) %}
```

**After**:
```twig
{# ✅ NEW - Ternary with null (Drupal compatible) #}
{% set classes = [
  'ps-component',
  size != 'md' ? 'ps-component--' ~ size : null,
  color != 'primary' ? 'ps-component--' ~ color : null,
  disabled ? 'ps-component--disabled' : null
] %}
```

**Key Changes**:
- Remove `.filter(v => v)` (Twig's `join()` skips `null` automatically)
- Use explicit condition checks (`size != 'md'` instead of just `size`)
- Return `null` instead of empty string for false conditions

3. **Test in Drupal Environment**:
```bash
# Verify no Twig errors
drush cache:rebuild
```

4. **Commit**:
```bash
git add source/patterns/{level}/{component}/{component}.twig
git commit -m "fix({level}): remove arrow functions in {component} template

- Replace array.filter(v => v) with ternary + null pattern
- Use explicit condition checks (size != 'md')
- Drupal compatibility: 100% (no arrow functions)
- join() automatically skips null values"
```

---

### 2.6 Missing Focus-Visible → WCAG AA

**Time Estimate**: 10-20 minutes per component

**Prerequisites**:
- Component is interactive (button, link, input, etc.)
- Missing `:focus-visible` styles

**Steps**:

1. **Identify Interactive Elements**:
```bash
# Search for hover states (usually need focus too)
grep -n ":hover" source/patterns/{level}/{component}/{component}.css

# Check if focus-visible exists
grep -n ":focus-visible" source/patterns/{level}/{component}/{component}.css
```

2. **Add WCAG AA Focus Indicator**:

**Template**:
```css
.ps-component {
  /* Existing styles */
  
  &:hover:not(:disabled) {
    /* Hover state */
  }
  
  /* ✅ ADD: WCAG AA focus-visible (MANDATORY for interactives) */
  &:focus-visible {
    outline: var(--border-size-2) solid var(--secondary);
    outline-offset: var(--border-size-2);
  }
  
  &:active:not(:disabled) {
    /* Active state */
  }
}
```

**Requirements**:
- Outline width: Minimum 2px (`var(--border-size-2)`)
- Outline color: Sufficient contrast (3:1 minimum) - `var(--secondary)` meets AA
- Outline offset: Visual separation (`var(--border-size-2)`)

3. **Test Keyboard Navigation**:
- Tab through interactive elements
- Verify focus indicator visible on all elements
- Check contrast ratio (DevTools or axe)

4. **Commit**:
```bash
git add source/patterns/{level}/{component}/{component}.css
git commit -m "fix({level}): add WCAG AA focus indicator to {component}

- Add :focus-visible with 2px outline (var(--border-size-2))
- Use var(--secondary) for sufficient contrast (3:1+ meets AA)
- Add outline-offset for visual separation
- Accessibility: WCAG 2.2 AA compliant (keyboard navigation)
- Test: Tab navigation, contrast verified"
```

---

### 2.7 Missing Autodocs → Storybook Standards

**Time Estimate**: 20-30 minutes per component

**Prerequisites**:
- Component stories exist but missing `tags: ['autodocs']`
- argTypes may need categorization

**Steps**:

1. **Check Current State**:
```bash
# Search for autodocs tag
grep -n "tags:" source/patterns/{level}/{component}/{component}.stories.jsx
```

2. **Add Autodocs Tag**:

**Before**:
```jsx
export default {
  title: 'Elements/Button',
  render: (args) => buttonTwig(args),
  args: data,
};
```

**After**:
```jsx
export default {
  title: 'Elements/Button',
  tags: ['autodocs'], // ← ADD THIS
  render: (args) => buttonTwig(args),
  args: data,
  argTypes: {
    // Categorize all props
  },
};
```

3. **Categorize ArgTypes**:

```jsx
argTypes: {
  // Content category
  text: {
    control: 'text',
    description: 'Button text content',
    table: { category: 'Content' },
  },
  
  // Appearance category
  color: {
    control: { type: 'select' },
    options: ['primary', 'secondary', 'success', 'warning', 'danger', 'info'],
    description: 'Color variant',
    table: {
      category: 'Appearance',
      defaultValue: { summary: 'primary' },
    },
  },
  
  size: {
    control: { type: 'inline-radio' },
    options: ['xs', 'sm', 'md', 'lg', 'xl'],
    description: 'Size variant',
    table: {
      category: 'Appearance',
      defaultValue: { summary: 'md' },
    },
  },
  
  // Behavior category
  disabled: {
    control: 'boolean',
    description: 'Disabled state',
    table: {
      category: 'Behavior',
      defaultValue: { summary: false },
    },
  },
  
  // Link category
  url: {
    control: 'text',
    description: 'Link URL (renders <a> instead of <button>)',
    table: { category: 'Link' },
  },
  
  // Accessibility category
  ariaLabel: {
    control: 'text',
    description: 'ARIA label for screen readers',
    table: { category: 'Accessibility' },
  },
}
```

**Categories**: Content | Appearance | Behavior | Link | Accessibility | Layout

4. **Test Autodocs Generation**:
```bash
npm run watch
# Navigate to component in Storybook
# Check "Docs" tab → Autodocs should display
```

5. **Commit**:
```bash
git add source/patterns/{level}/{component}/{component}.stories.jsx
git commit -m "docs({level}): add Storybook Autodocs to {component}

- Add tags: ['autodocs'] to export default
- Categorize all argTypes (Content, Appearance, Behavior, Link, Accessibility)
- Add descriptions to all props
- Add defaultValue summaries
- Conformity: Storybook Autodocs 100%"
```

---

## 3. Deprecation Process

### 3.1 Deprecation Lifecycle

```
┌─────────────────────────────────────────────────────────────────┐
│                    DEPRECATION TIMELINE                          │
└─────────────────────────────────────────────────────────────────┘

Week 0: ANNOUNCE
├─ Document pattern as deprecated
├─ Add @deprecated JSDoc comments
├─ Add console warnings (if applicable)
└─ Announce to team (Slack, email, meeting)

Week 1-4: MIGRATION PERIOD (1 month)
├─ Provide migration guide
├─ Update documentation with alternatives
├─ Support team during migration
└─ Track migration progress

Week 5-8: DEPRECATION WARNINGS (1 month)
├─ Add visible warnings in Storybook
├─ Add build warnings (non-blocking)
├─ Remind team of sunset date
└─ Final migration assistance

Week 9-12: GRACE PERIOD (1 month)
├─ Increase warning severity
├─ Block new usages (linting rules)
├─ Audit remaining usages
└─ Plan removal

Week 13+: REMOVAL
├─ Remove deprecated pattern from codebase
├─ Update CHANGELOG with breaking change
├─ Increment major version (semantic versioning)
└─ Archive documentation

TOTAL: 3 months minimum (standard deprecation period)
```

---

### 3.2 Deprecation Announcement Template

**File**: `docs/ps-design/DEPRECATIONS.md`

```markdown
# Design System Deprecations

## Active Deprecations

### [Pattern Name] - Deprecated 2025-12-12

**Sunset Date**: 2026-03-12 (3 months)

**Reason**: [Brief explanation of why deprecated]
- Issue 1: [e.g., "Not accessible (missing focus-visible)"]
- Issue 2: [e.g., "Not composable (duplicated markup)"]
- Issue 3: [e.g., "Not maintainable (hardcoded values)"]

**Migration Path**:
1. Read migration guide: [Link to MIGRATION_GUIDES.md section]
2. Identify usages: `grep -r "old-pattern" source/patterns/`
3. Replace with new pattern: [Code example]
4. Test: `npm run build && npm run watch`
5. Commit: [Commit message template]

**Support**:
- Migration guide: `.github/instructions/MIGRATION_GUIDES.md#section`
- Questions: [Slack channel or issue tracker]
- Timeline: 3 months from announcement to removal

**Impact**:
- Components affected: [List of components]
- Estimated migration time: [e.g., "2-3 hours per component"]
- Breaking change: [Yes/No - If yes, major version bump]

---

### [Pattern Name] - Deprecated 2025-11-01 ✅ REMOVED

**Sunset Date**: 2026-02-01 (COMPLETED)

**Status**: ✅ Removed in v2.0.0

**Reason**: [Brief explanation]

**Archive**: See `docs/archive/DEPRECATED_PATTERNS.md` for historical reference
```

---

### 3.3 Code-Level Deprecation Markers

**Twig Templates**:
```twig
{#
 * @deprecated Since v1.5.0, will be removed in v2.0.0
 * Use {@link @elements/new-component/new-component.twig} instead
 * Migration guide: .github/instructions/MIGRATION_GUIDES.md#old-to-new
 #}
```

**CSS**:
```css
/**
 * @deprecated Since v1.5.0, will be removed in v2.0.0
 * Use .ps-new-component instead
 * Migration: Replace .old-component with .ps-new-component
 */
.old-component {
  /* Legacy styles */
}
```

**JavaScript**:
```js
/**
 * @deprecated Since v1.5.0, will be removed in v2.0.0
 * Use newFunction() instead
 * @see {@link ./new-function.js}
 */
export function oldFunction() {
  console.warn(
    'oldFunction() is deprecated and will be removed in v2.0.0. ' +
    'Use newFunction() instead. See MIGRATION_GUIDES.md'
  );
  // Legacy implementation
}
```

---

### 3.4 Storybook Deprecation Warning

**Add to deprecated story**:
```jsx
export default {
  title: 'Elements/OldComponent',
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component:
          '⚠️ **DEPRECATED** - This component will be removed in v2.0.0 (sunset: 2026-03-12)\n\n' +
          '**Use instead**: [NewComponent](?path=/docs/elements-newcomponent--default)\n\n' +
          '**Migration guide**: `.github/instructions/MIGRATION_GUIDES.md#old-to-new`\n\n' +
          '---\n\n' +
          '[Original description continues...]',
      },
    },
  },
};
```

---

### 3.5 Build-Time Warnings

**ESLint Rule** (custom):
```js
// .eslintrc.js
module.exports = {
  rules: {
    'no-deprecated-patterns': [
      'warn',
      {
        patterns: [
          {
            pattern: 'oldFunction',
            message: 'oldFunction() is deprecated. Use newFunction() instead.',
            link: '.github/instructions/MIGRATION_GUIDES.md#old-to-new',
          },
        ],
      },
    ],
  },
};
```

**Stylelint Rule** (custom):
```js
// stylelint.config.js
module.exports = {
  rules: {
    'plugin/no-deprecated-selectors': [
      true,
      {
        selectors: [
          {
            pattern: /\.old-component/,
            message: '.old-component is deprecated. Use .ps-new-component instead.',
            link: '.github/instructions/MIGRATION_GUIDES.md#old-to-new',
          },
        ],
      },
    ],
  },
};
```

---

## 4. Breaking Changes Documentation

### 4.1 CHANGELOG Format (Keep a Changelog)

**File**: `docs/ps-design/CHANGELOG.md`

```markdown
# Changelog

All notable changes to the PS Theme design system.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- New features for next release

### Changed
- Changes in existing functionality

### Deprecated
- Soon-to-be removed features

### Removed
- Removed features

### Fixed
- Bug fixes

### Security
- Vulnerability fixes

---

## [2.0.0] - 2026-03-12

### 🚨 BREAKING CHANGES

#### Removed Deprecated Patterns

**OldComponent removed** (deprecated since v1.5.0):
- **Impact**: Components using `@elements/old-component/old-component.twig` will break
- **Migration**: Replace with `@elements/new-component/new-component.twig`
- **Guide**: See `.github/instructions/MIGRATION_GUIDES.md#old-to-new`
- **Timeline**: 3-month deprecation period (2025-12-12 → 2026-03-12)

**Example Migration**:
```diff
- {% include '@elements/old-component/old-component.twig' with {
-   legacyProp: value
- } only %}
+ {% include '@elements/new-component/new-component.twig' with {
+   newProp: value
+ } only %}
```

#### Token Name Changes

**`--primary-green` → `--primary`** (deprecated since v1.8.0):
- **Impact**: CSS using `var(--primary-green)` will break
- **Migration**: Find/replace `--primary-green` → `--primary`
- **Command**: `grep -rl "--primary-green" source/patterns/ | xargs sed -i 's/--primary-green/--primary/g'`

#### BEM Prefix Requirement

**All components MUST use `ps-` prefix** (announced v1.9.0):
- **Impact**: Components with `.component` (no prefix) will fail linting
- **Migration**: Rename `.component` → `.ps-component` in templates + CSS
- **Guide**: See `.github/instructions/MIGRATION_GUIDES.md#non-bem-to-bem`

### Added
- **NewComponent** (element) - Replacement for deprecated OldComponent
- **Token-First Composition Workflow** - Systematic approach for component customization

### Changed
- **Button** (element) - Migrated to PostCSS nesting (no visual changes)
- **Card** (molecule) - Added Layer 2 component-scoped variables for customization

### Deprecated
- **AnotherOldPattern** - Will be removed in v3.0.0 (sunset: 2026-06-12)
  - Migration guide: `.github/instructions/MIGRATION_GUIDES.md#another-old-to-new`

### Fixed
- **Badge** (element) - Added focus-visible for accessibility (WCAG 2.2 AA)

---

## [1.9.0] - 2025-12-01

### Added
- ...

[1.9.0]: https://github.com/org/ps_theme/compare/v1.8.0...v1.9.0
[2.0.0]: https://github.com/org/ps_theme/compare/v1.9.0...v2.0.0
```

---

### 4.2 Semantic Versioning Strategy

**Version Format**: `MAJOR.MINOR.PATCH`

```
Given a version number MAJOR.MINOR.PATCH, increment:

1. MAJOR version (2.0.0) when you make incompatible API changes
   - Breaking changes (removed components, renamed tokens, changed props)
   - Example: Remove deprecated component
   
2. MINOR version (1.5.0) when you add functionality in a backward compatible manner
   - New components
   - New optional props to existing components
   - Deprecations (mark as deprecated, but NOT removed)
   - Example: Add new component, deprecate old pattern
   
3. PATCH version (1.4.1) when you make backward compatible bug fixes
   - Bug fixes
   - Documentation updates
   - Accessibility fixes (if no API changes)
   - Example: Fix focus-visible, update README
```

**PS Theme Strategy**:
- **Major releases**: Every 6-12 months (planned breaking changes)
- **Minor releases**: Monthly (new components, deprecations)
- **Patch releases**: As needed (bug fixes, hotfixes)

---

### 4.3 Breaking Change Checklist

Before introducing a breaking change:

- [ ] **Document deprecation** (DEPRECATIONS.md)
- [ ] **Add @deprecated comments** (code-level)
- [ ] **Announce to team** (Slack, email, meeting)
- [ ] **Provide migration guide** (MIGRATION_GUIDES.md)
- [ ] **Set sunset date** (3-6 months minimum)
- [ ] **Add warnings** (console, Storybook, build)
- [ ] **Track migration progress** (grep usages)
- [ ] **Support team** (answer questions, pair programming)
- [ ] **Final reminder** (1 month before removal)
- [ ] **Remove pattern** (after grace period)
- [ ] **Update CHANGELOG** (breaking changes section)
- [ ] **Increment major version** (semantic versioning)
- [ ] **Archive documentation** (historical reference)

---

## 5. Component Upgrade Checklist

### 5.1 Pre-Migration Audit

**Conformity Score** (workflows.instructions.md):
```bash
# Run conformity audit
# Manual checklist: 100-point score system

File Structure (10 points):
  [ ] 5 required files exist (.twig, .css, .yml, .stories.jsx, README.md)
  
Twig Template (15 points):
  [ ] Header comment with @param docs
  [ ] Default values with |default()
  [ ] Classes construction with ternary + null
  [ ] NO arrow functions (Drupal compatible)
  [ ] Composition with {% include %} + only
  
CSS (20 points):
  [ ] ALL values from tokens (NO hardcoded)
  [ ] Nesting with & syntax
  [ ] Cascade order: base → elements → modifiers → states
  [ ] Component-scoped variables (Layer 2)
  [ ] Focus-visible for interactives
  
Storybook (20 points):
  [ ] tags: ['autodocs'] in export default
  [ ] argTypes categorized
  [ ] Default + Showcases stories
  
YAML (10 points):
  [ ] Realistic Real Estate data
  [ ] All required props defined
  
README (10 points):
  [ ] Usage section
  [ ] Props table
  [ ] BEM structure
  [ ] Design tokens
  [ ] Accessibility
  [ ] Examples
  
Accessibility (5 points):
  [ ] Contrast 4.5:1 text, 3:1 UI
  [ ] Focus-visible on interactives
  [ ] ARIA when semantic HTML insufficient
  [ ] Keyboard navigable

SCORE: ___/100 (100% required for production)
```

---

### 5.2 Usage Analysis

**Identify all usages**:
```bash
# Find all includes of this component
grep -r "{% include '@{level}/{component}/" source/patterns/

# Find all CSS class usages
grep -r "ps-{component}" source/patterns/

# Count total usages
grep -rc "ps-{component}" source/patterns/ | grep -v ":0" | wc -l
```

**Impact Assessment**:
- **Low Impact** (< 5 usages): Can migrate quickly
- **Medium Impact** (5-20 usages): Requires systematic approach
- **High Impact** (20+ usages): Requires planning, phased rollout

---

### 5.3 Step-by-Step Upgrade

**For each component requiring migration**:

1. **Backup Original**:
```bash
mkdir -p backup/{level}/{component}
cp -r source/patterns/{level}/{component}/* backup/{level}/{component}/
```

2. **Create Feature Branch**:
```bash
git checkout -b refactor/{component}-migration
```

3. **Apply Migrations** (in order):
   - [ ] Fix arrow functions (if Twig has them) - CRITICAL for Drupal
   - [ ] Add focus-visible (if interactive) - CRITICAL for accessibility
   - [ ] Replace hardcoded values with tokens
   - [ ] Migrate to nested CSS
   - [ ] Update to BEM naming (if non-BEM)
   - [ ] Replace inline markup with composition
   - [ ] Add component-scoped variables (Layer 2)
   - [ ] Add Storybook Autodocs

4. **Test After Each Migration**:
```bash
npm run build  # Must pass
npm run watch  # Visual check
```

5. **Visual Regression**:
   - Compare before/after screenshots in Storybook
   - Test all variants (sizes, colors, states)
   - Test in all contexts (standalone, composed)

6. **Commit Each Migration**:
```bash
git add source/patterns/{level}/{component}/
git commit -m "{type}({level}): {specific migration} in {component}

[Detailed commit message per migration workflow]"
```

7. **Update Documentation**:
   - Update README.md with new patterns
   - Update CHANGELOG.md with changes
   - Update DEPRECATIONS.md if applicable

8. **Merge & Deploy**:
```bash
git checkout master
git merge refactor/{component}-migration
git push origin master
```

---

### 5.4 Post-Migration Validation

**Full Validation Checklist**:

- [ ] **Build Passes**: `npm run build` (0 errors, 0 warnings)
- [ ] **Visual Parity**: Before/after screenshots identical
- [ ] **Conformity Score**: 100/100 (audit checklist)
- [ ] **All Variants Work**: Sizes, colors, states, compositions
- [ ] **Accessibility**: WCAG 2.2 AA (focus-visible, contrast, keyboard, ARIA)
- [ ] **Documentation Updated**: README, CHANGELOG, DEPRECATIONS (if applicable)
- [ ] **Team Notified**: Migration complete, new patterns available
- [ ] **Backup Archived**: `backup/{level}/{component}/` saved

---

## 6. Migration Tools & Scripts

### 6.1 Find/Replace Scripts

**Hardcoded Colors → Tokens**:
```bash
#!/bin/bash
# migrate-colors.sh

COMPONENT_PATH="source/patterns/$1/$2"
CSS_FILE="$COMPONENT_PATH/$2.css"

if [ ! -f "$CSS_FILE" ]; then
  echo "CSS file not found: $CSS_FILE"
  exit 1
fi

# Backup
cp "$CSS_FILE" "$CSS_FILE.bak"

# Replace common colors
sed -i 's/#00915A/var(--primary)/g' "$CSS_FILE"
sed -i 's/#FFFFFF/var(--white)/g' "$CSS_FILE"
sed -i 's/#000000/var(--black)/g' "$CSS_FILE"

# Replace sizes
sed -i 's/16px/var(--size-4)/g' "$CSS_FILE"
sed -i 's/24px/var(--size-6)/g' "$CSS_FILE"

# Replace durations
sed -i 's/150ms/var(--duration-fast)/g' "$CSS_FILE"
sed -i 's/300ms/var(--duration-base)/g' "$CSS_FILE"

echo "Migration complete. Backup saved: $CSS_FILE.bak"
echo "Review changes before committing."
```

**Usage**:
```bash
chmod +x scripts/migrate-colors.sh
./scripts/migrate-colors.sh elements button
```

---

### 6.2 BEM Rename Script

**Non-BEM → BEM**:
```bash
#!/bin/bash
# migrate-bem.sh

LEVEL=$1
COMPONENT=$2
OLD_PREFIX=$3  # e.g., "button"
NEW_PREFIX="ps-$3"  # e.g., "ps-button"

COMPONENT_PATH="source/patterns/$LEVEL/$COMPONENT"

if [ ! -d "$COMPONENT_PATH" ]; then
  echo "Component not found: $COMPONENT_PATH"
  exit 1
fi

# Backup
cp -r "$COMPONENT_PATH" "$COMPONENT_PATH.bak"

# Rename in Twig
sed -i "s/class=\"$OLD_PREFIX\"/class=\"$NEW_PREFIX\"/g" "$COMPONENT_PATH/$COMPONENT.twig"
sed -i "s/class=\"$OLD_PREFIX-/class=\"$NEW_PREFIX--/g" "$COMPONENT_PATH/$COMPONENT.twig"

# Rename in CSS
sed -i "s/\\.$OLD_PREFIX\\b/.$NEW_PREFIX/g" "$COMPONENT_PATH/$COMPONENT.css"
sed -i "s/\\.$OLD_PREFIX-/.$NEW_PREFIX--/g" "$COMPONENT_PATH/$COMPONENT.css"

echo "BEM migration complete. Backup saved: $COMPONENT_PATH.bak"
echo "Review changes before committing."
```

**Usage**:
```bash
chmod +x scripts/migrate-bem.sh
./scripts/migrate-bem.sh elements button button
```

---

### 6.3 Audit All Components Script

**Check conformity scores for all components**:
```bash
#!/bin/bash
# audit-all.sh

echo "Component Conformity Audit"
echo "=========================="
echo ""

for LEVEL in elements components collections layouts pages; do
  LEVEL_PATH="source/patterns/$LEVEL"
  
  if [ ! -d "$LEVEL_PATH" ]; then
    continue
  fi
  
  echo "Auditing $LEVEL..."
  
  for COMPONENT_PATH in "$LEVEL_PATH"/*; do
    COMPONENT=$(basename "$COMPONENT_PATH")
    
    # Check 5-file structure
    FILES=0
    [ -f "$COMPONENT_PATH/$COMPONENT.twig" ] && ((FILES++))
    [ -f "$COMPONENT_PATH/$COMPONENT.css" ] && ((FILES++))
    [ -f "$COMPONENT_PATH/$COMPONENT.yml" ] && ((FILES++))
    [ -f "$COMPONENT_PATH/$COMPONENT.stories.jsx" ] && ((FILES++))
    [ -f "$COMPONENT_PATH/README.md" ] && ((FILES++))
    
    # Check for hardcoded values
    HARDCODED=0
    if [ -f "$COMPONENT_PATH/$COMPONENT.css" ]; then
      HARDCODED=$(grep -cE "#[0-9a-fA-F]{3,6}|[0-9]+(px|rem)" "$COMPONENT_PATH/$COMPONENT.css" || true)
    fi
    
    # Check for arrow functions
    ARROWS=0
    if [ -f "$COMPONENT_PATH/$COMPONENT.twig" ]; then
      ARROWS=$(grep -c "=>" "$COMPONENT_PATH/$COMPONENT.twig" || true)
    fi
    
    # Check for autodocs
    AUTODOCS=0
    if [ -f "$COMPONENT_PATH/$COMPONENT.stories.jsx" ]; then
      AUTODOCS=$(grep -c "tags:.*autodocs" "$COMPONENT_PATH/$COMPONENT.stories.jsx" || true)
    fi
    
    # Report
    STATUS="✅"
    if [ $FILES -lt 5 ] || [ $HARDCODED -gt 0 ] || [ $ARROWS -gt 0 ] || [ $AUTODOCS -eq 0 ]; then
      STATUS="❌"
    fi
    
    echo "$STATUS $COMPONENT (Files: $FILES/5, Hardcoded: $HARDCODED, Arrows: $ARROWS, Autodocs: $AUTODOCS)"
  done
  
  echo ""
done
```

**Usage**:
```bash
chmod +x scripts/audit-all.sh
./scripts/audit-all.sh
```

---

## 7. Risk Assessment Matrix

### 7.1 Migration Risk Levels

| Risk Level | Description | Impact | Timeline | Rollback Plan Required |
|------------|-------------|--------|----------|------------------------|
| **🟢 Low** | Visual-only changes, no breaking changes | Minimal (documentation, Storybook) | 1-2 days | No |
| **🟡 Medium** | Template/CSS changes, backward compatible | Moderate (rebuild required) | 1-2 weeks | Recommended |
| **🔴 High** | Breaking API changes, component removal | High (usages break) | 1-3 months | MANDATORY |
| **🔴 Critical** | Drupal incompatibility, accessibility violations | System-breaking | ASAP (hours) | MANDATORY |

---

### 7.2 Pre-Migration Risk Assessment

**For each migration, assess**:

1. **Breaking Changes?**
   - Yes → High/Critical risk
   - No → Low/Medium risk

2. **Drupal Compatibility?**
   - Arrow functions in Twig → Critical (fix immediately)
   - Other Twig issues → High
   - CSS/Stories only → Low

3. **Accessibility Impact?**
   - Missing focus-visible → Critical (legal requirement)
   - Contrast issues → High
   - Documentation only → Low

4. **Usage Count?**
   - 20+ usages → High risk (wide impact)
   - 5-20 usages → Medium risk
   - <5 usages → Low risk

5. **Visual Changes?**
   - Tokens differ from hardcoded → High risk (visual regression)
   - BEM rename (same output) → Low risk
   - Documentation only → Low risk

---

### 7.3 Rollback Plan Template

**For High/Critical migrations**:

```markdown
## Rollback Plan: {Component} Migration

**Date**: 2025-12-12
**Risk Level**: 🔴 High
**Reason**: Breaking API changes (prop renames)

### Backup Location
- Files: `backup/{level}/{component}/` (timestamped)
- Git commit: `abc123def` (before migration)

### Rollback Steps

1. **Stop Deployment**:
   ```bash
   # Halt any ongoing deployments
   kubectl rollout undo deployment/frontend
   ```

2. **Restore Files**:
   ```bash
   cp -r backup/{level}/{component}/* source/patterns/{level}/{component}/
   ```

3. **Revert Git**:
   ```bash
   git revert {migration-commit-hash}
   git push origin master
   ```

4. **Rebuild**:
   ```bash
   npm run build
   npm run storybook:build
   ```

5. **Redeploy**:
   ```bash
   # Deploy rollback version
   git tag v1.8.1-rollback
   # Trigger deployment
   ```

6. **Notify Team**:
   - Slack: #design-system channel
   - Message: "Migration rolled back due to {reason}. Investigating."

### Prevention

- [ ] More thorough testing in staging
- [ ] Phased rollout (20% → 50% → 100%)
- [ ] Feature flag for new pattern
- [ ] Longer deprecation period

### Post-Mortem

- Schedule retrospective meeting
- Document lessons learned
- Update migration process
```

---

## 🔗 Cross-References

- **Workflows**: `workflows.instructions.md` - Component creation & standardization
- **Troubleshooting**: `TROUBLESHOOTING_GUIDE.md` - Error fixes during migration
- **Decision Flowcharts**: `DECISION_FLOWCHARTS.md` - Workflow selection
- **Token Creation**: `TOKEN_CREATION_PROCESS.md` - Token proposal during migration
- **CSS Standards**: `css.instructions.md` - Token usage, nesting patterns
- **Components**: `components.instructions.md` - BEM, file structure
- **Accessibility**: `accessibility.instructions.md` - WCAG compliance

---

## 📊 Success Metrics

**Track migration progress**:

- **Conformity Score**: 65% → 100% (target)
- **Hardcoded Values**: 50+ → 0 (design tokens 100%)
- **Arrow Functions**: 10+ → 0 (Drupal compatibility)
- **Focus-Visible**: 20% → 100% (accessibility)
- **Autodocs**: 30% → 100% (documentation)
- **BEM Compliance**: 70% → 100% (naming consistency)

**Timeline**:
- Low-risk migrations: 1-2 days per component
- Medium-risk migrations: 1-2 weeks per component
- High-risk migrations: 1-3 months (phased rollout)

**Team Impact**:
- Support requests during migration: +50% (temporary)
- Post-migration support: -70% (self-service via docs)
- Development velocity: +30% (consistent patterns)

---

## 🎓 Learning Resources

- **Official Docs**: This file (MIGRATION_GUIDES.md)
- **Workflows**: `workflows.instructions.md` (standardization section)
- **Examples**: `docs/ps-design/MIGRATION_INDEX.md` (Icon System migration)
- **Changelog**: `docs/ps-design/CHANGELOG.md` (past migrations)
- **Troubleshooting**: `TROUBLESHOOTING_GUIDE.md` (error solutions)

---

**Last Updated**: 2025-12-12  
**Maintainers**: Design System Team  
**Version**: 1.0.0
