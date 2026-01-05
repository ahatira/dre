---
title: Maintenance & Evolution
version: 4.0.0
lastUpdated: 2025-12-12
priority: MEDIUM
status: ACTIVE
---

# 05 - Maintenance & Evolution

**Purpose**: Token creation process, legacy pattern migration, deprecation lifecycle, refactoring workflows.

**When to use**: Consult when creating new tokens, migrating legacy components, managing deprecations, or planning system evolution.

**Related files**:
- [01-core-principles.md](01-core-principles.md) - Token system foundations
- [02-component-development.md](02-component-development.md) - New component workflow
- [03-technical-implementation.md](03-technical-implementation.md) - Technical standards
- [04-quality-assurance.md](04-quality-assurance.md) - Conformity audits

---

## 📐 Table of Contents

1. [Token Creation Process](#1-token-creation-process)
2. [Legacy Pattern Catalog](#2-legacy-pattern-catalog)
3. [Migration Workflows](#3-migration-workflows)
4. [Deprecation Lifecycle](#4-deprecation-lifecycle)
5. [Breaking Changes Management](#5-breaking-changes-management)

---

## 1. Token Creation Process

### 1.1 When to Create a Token

**Create a token when**:
- ✅ Value is used **3+ times** across different components
- ✅ Value represents a **design decision** (not arbitrary)
- ✅ Value follows an **existing pattern** (size scale, color palette)
- ✅ Value has **semantic meaning** (--primary, --success, --large)
- ✅ Value needs **global consistency** (spacing, colors, typography)

**DO NOT create a token when**:
- ❌ Value is **component-specific** (use component-scoped variables)
- ❌ Value is used **only once** (hardcode locally with comment)
- ❌ Value is **experimental** (test in component first)
- ❌ Value doesn't fit **existing system** (propose system change first)

### 1.2 Token Verification Checklist

**Before proposing a new token**:

#### Search Existing Tokens

```bash
# Search all token files
grep -r "--keyword" source/props/

# Check if size token exists
grep -r "--size-" source/props/sizes.css

# Check if color exists
grep -r "--primary" source/props/brand.css
```

**Token files to check**:
- `source/props/colors.css` - Primitive colors (--green-600, --gray-400)
- `source/props/brand.css` - Semantic colors (--primary, --secondary, --success)
- `source/props/sizes.css` - Spacing scale (--size-1 to --size-20)
- `source/props/fonts.css` - Typography (--font-size-*, --font-weight-*)
- `source/props/borders.css` - Radii and widths
- `source/props/shadows.css` - Box shadows (--shadow-1 to --shadow-5)
- `source/props/animations.css` - Durations (--duration-fast, --duration-base)
- `source/props/easing.css` - Timing functions
- `source/props/zindex.css` - Stacking context (--z-dropdown, --z-modal)

#### Verify Naming Convention

**Token naming follows strict patterns**:

| Category | Pattern | Examples |
|----------|---------|----------|
| **Primitive Colors** | `--{color}-{shade}` | `--green-600`, `--gray-400` |
| **Semantic Colors** | `--{meaning}` | `--primary`, `--success`, `--danger` |
| **Sizes** | `--size-{number}` | `--size-4` (16px), `--size-6` (24px) |
| **Font Sizes** | `--font-size-{number}` | `--font-size-4` (16px) |
| **Font Weights** | `--font-weight-{number}` | `--font-weight-600` |
| **Borders** | `--radius-{number}` | `--radius-2` (8px) |
| **Shadows** | `--shadow-{number}` | `--shadow-3` |
| **Durations** | `--duration-{speed}` | `--duration-fast` (150ms) |
| **Easing** | `--ease-{type}` | `--ease-in-out-3` |
| **Z-Index** | `--z-{context}` | `--z-dropdown`, `--z-modal` |

**Forbidden patterns**:
- ❌ `--button-primary` (component-scoped, not global)
- ❌ `--color-primary` (redundant, use `--primary`)
- ❌ `--size-medium` (use numbers, not words)
- ❌ `--16px` (value in name, use semantic)

#### Check Value Progression

**Tokens must fit existing scales**:

```css
/* ✅ GOOD - Fits size scale */
--size-1: 0.25rem;  /* 4px */
--size-2: 0.5rem;   /* 8px */
--size-3: 0.75rem;  /* 12px */
--size-4: 1rem;     /* 16px */
/* New: --size-5: 1.25rem (20px) fits progression */

/* ❌ BAD - Breaks progression */
/* New: --size-15px doesn't fit scale */
```

**Existing scales**:

| Scale | Progression | Range |
|-------|-------------|-------|
| **Sizes** | 0.25rem increments | --size-1 (4px) to --size-20 (80px) |
| **Font Sizes** | Type scale (1.2 ratio) | --font-size-2 (14px) to --font-size-15 (60px) |
| **Shadows** | Visual depth | --shadow-1 (subtle) to --shadow-5 (heavy) |
| **Durations** | 2x multiplier | --duration-fast (150ms), --duration-base (300ms) |

### 1.3 Token Creation Workflow (4 Steps)

#### Step 1: Document Need in Component

**When you discover missing token during component work**:

```markdown
<!-- In component CSS file comment or docs/design/{level}/{component}.md -->

## Missing Tokens

### --size-7 (30px)

**Reason**: Card padding-y on desktop requires 30px per Figma spec.

**Current Workaround**: Hardcoded value in card.css:
```css
@media (min-width: 768px) {
  .ps-card {
    padding-block: 30px; /* TODO: Replace with --size-7 */
  }
}
```

**Proposal**: Add --size-7: 1.875rem (30px) to sizes.css

**Alternatives Considered**:
- --size-6 (24px) - Too small per design
- --size-8 (32px) - Too large, doesn't fit Figma
```

**❌ DO NOT edit `source/props/*.css` directly during component work.**

#### Step 2: Create Token Proposal Issue

**Open GitHub issue** with this template:

```markdown
## Token Proposal: --size-7

**Category**: Sizes

**Token Name**: `--size-7`

**Value**: `1.875rem` (30px)

**Use Cases**:
1. Card padding-y on desktop (card.css, line 45)
2. Section spacing in header (header.css, line 23)
3. Modal dialog padding (modal.css, line 67)

**Current State**:
- [x] Hardcoded in 3 components
- [x] Currently using workaround: 30px hardcoded

**Alternatives Considered**:
1. --size-6 (24px) - Too small per Figma spec
2. --size-8 (32px) - Too large, breaks design

**Naming Verification**:
- [x] Follows convention for category
- [x] Fits existing scale/progression
- [x] No similar token exists

**Impact**:
- Components affected: card, header, footer, modal
- Breaking changes: None (replacing hardcoded)
- Migration required: Yes (3 components)

**Figma Reference**: [link]

**Checklist**:
- [x] Searched existing tokens (no --size-7 found)
- [x] Verified naming convention (--size-{number})
- [x] Documented use cases (3+)
- [x] Checked scale progression (fits between --size-6 and --size-8)
```

**Label**: `tokens`, `proposal`, `design-system`

#### Step 3: Design Team Review

**Design team validates**:
1. ✅ **Design alignment**: Token matches Figma/design specs
2. ✅ **Naming**: Follows convention and fits existing system
3. ✅ **Scale fit**: Value fits progression (sizes, colors, etc.)
4. ✅ **Semantic meaning**: Clear purpose and context
5. ✅ **Reusability**: 3+ use cases confirmed

**Outcomes**:
- ✅ **Approved**: Proceed to implementation
- ⚠️ **Needs revision**: Address feedback and resubmit
- ❌ **Rejected**: Use alternative approach

#### Step 4: Implementation

**Once approved**, create PR with:

**A. Add Token to Props File**:

```css
/* source/props/sizes.css */

:root {
  /* Existing tokens */
  --size-6: 1.5rem;  /* 24px */
  
  /* New token (with comment explaining use) */
  --size-7: 1.875rem;  /* 30px - Card padding-y on desktop, section spacing */
  
  --size-8: 2rem;    /* 32px */
}
```

**Comment format**: `/* [computed-value] - [primary-use-cases] */`

**B. Update Component Files**:

```css
/* card.css - BEFORE */
@media (min-width: 768px) {
  .ps-card {
    padding-block: 30px; /* Hardcoded */
  }
}

/* card.css - AFTER */
@media (min-width: 768px) {
  .ps-card {
    padding-block: var(--size-7); /* Token usage */
  }
}
```

**C. Update Documentation**:

```markdown
<!-- source/props/README.md -->

### Recent Additions

**v1.2.0** (2025-12-12):
- `--size-7`: 30px - Card desktop padding, section spacing
  - Use cases: card.css, header.css, footer.css
  - Figma: [link]
```

**D. Testing & Validation**:

```bash
# Build passes
npm run build

# Check token usage
grep -r "var(--size-7)" source/patterns/

# Verify no hardcoded values remain
grep -r "30px" source/patterns/
```

**E. Merge & Announce**:

```
feat(tokens): Add --size-7 (30px) for card desktop padding

- Add --size-7: 1.875rem (30px) to sizes.css
- Replace hardcoded 30px in card.css, header.css, footer.css
- Standardizes section spacing across layouts

Use cases:
- Card padding-y on desktop (>768px)
- Section spacing in organisms
- Modal dialog padding

Closes #123
```

### 1.4 Token Anti-Patterns

**❌ Creating Token Without Approval**:
```css
/* source/props/sizes.css - WRONG */
--size-custom: 30px; /* Added without process */
```

**❌ Component-Specific Tokens in Global Props**:
```css
/* source/props/colors.css - WRONG */
--button-primary-bg: var(--green-600);
```
**Correct**: Use component-scoped variables instead.

**❌ Breaking Existing Scale**:
```css
/* source/props/sizes.css - WRONG */
--size-6: 1.5rem;   /* 24px */
--size-15: 30px;    /* Breaks scale, use --size-7 instead */
--size-8: 2rem;     /* 32px */
```

---

## 2. Legacy Pattern Catalog

### 2.1 Common Legacy Patterns

**Priority Legend**: 🔴 Critical | 🟠 High | 🟡 Medium | 🟢 Low

#### Pattern 1: Flat CSS (No Nesting) 🟠

**Legacy**:
```css
/* ❌ OLD - Flat CSS without nesting */
.ps-component { }
.ps-component__element { }
.ps-component--modifier { }
```

**Target**:
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

**Impact**: Medium (structural change, no behavioral change)

#### Pattern 2: Hardcoded Values 🔴

**Legacy**:
```css
/* ❌ OLD - Hardcoded colors, sizes, durations */
.ps-button {
  padding: 16px 24px;
  background: #00915A;
  transition: 150ms ease;
}
```

**Target**:
```css
/* ✅ NEW - Design tokens only */
.ps-button {
  padding: var(--size-4) var(--size-6);
  background: var(--primary);
  transition: var(--duration-fast) var(--ease-3);
}
```

**Impact**: High (visual changes if tokens differ)

#### Pattern 3: Arrow Functions in Twig 🔴

**Legacy**:
```twig
{# ❌ OLD - Arrow function (Drupal incompatible) #}
{% set classes = [
  'ps-component',
  size,
  color
]|filter(v => v) %}
```

**Target**:
```twig
{# ✅ NEW - Ternary with null (Drupal compatible) #}
{% set classes = [
  'ps-component',
  size != 'medium' ? 'ps-component--' ~ size : null,
  color != 'primary' ? 'ps-component--' ~ color : null
] %}
```

**Impact**: Critical (breaks in Drupal)

#### Pattern 4: Missing Focus-Visible 🔴

**Legacy**:
```css
/* ❌ OLD - No focus indicator */
.ps-button {
  &:hover {
    background: var(--primary-hover);
  }
  /* Missing :focus-visible */
}
```

**Target**:
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

**Impact**: Critical (accessibility violation)

#### Pattern 5: Missing Autodocs 🟡

**Legacy**:
```jsx
// ❌ OLD - No autodocs tag
export default {
  title: 'Elements/Button',
  render: (args) => buttonTwig(args),
};
```

**Target**:
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

**Impact**: Low (documentation only)

#### Pattern 6: Wrong Cascade Order 🟠

**Legacy**:
```css
/* ❌ OLD - Modifiers before base */
.ps-avatar {
  &--large {
    width: var(--size-12);
  }
  
  /* Base after modifier - will override! */
  width: var(--size-10);
}
```

**Target**:
```css
/* ✅ NEW - Base first, modifiers after */
.ps-avatar {
  /* Base styles first */
  width: var(--size-10);
  
  /* Modifiers after base */
  &--large {
    width: var(--size-12);
  }
}
```

**Impact**: High (visual changes)

#### Pattern 7: Component-Scoped Variables Missing 🟡

**Legacy**:
```css
/* ❌ OLD - Direct token usage (no override capability) */
.ps-button {
  padding: var(--size-4);
  background: var(--primary);
}
```

**Target**:
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

**Impact**: Medium (enables customization)

---

## 3. Migration Workflows

### 3.1 Refactoring Legacy Components

**When to refactor**:
- Component fails conformity audit (<80/90)
- Component breaks current standards
- Component blocks other work
- Scheduled refactoring sprint

**Workflow** (from [04-quality-assurance.md](04-quality-assurance.md)):

1. **Run conformity audit** (90-point checklist)
2. **Prioritize fixes** (Critical → High → Medium)
3. **Apply fixes systematically** (Twig → CSS → Storybook → YAML)
4. **Test and validate** (`npm run build` + visual check)
5. **Document changes** (commit + changelog)

**See**: [04-quality-assurance.md](04-quality-assurance.md) (Section 1: Conformity Audit)

### 3.2 Common Migration Tasks

#### Task: Flat CSS → Nested CSS

**Time**: 15-30 minutes per component

**Steps**:

1. **Backup original**:
```bash
cp source/patterns/{level}/{component}/{component}.css source/patterns/{level}/{component}/{component}.css.bak
```

2. **Identify nesting opportunities**:
```bash
grep -n "^\\.ps-{component}" source/patterns/{level}/{component}/{component}.css
```

3. **Restructure with nesting**:

```css
/* BEFORE */
.ps-badge { }
.ps-badge__text { }
.ps-badge--primary { }

/* AFTER */
.ps-badge {
  /* Base */
  
  &__text {
    /* Element */
  }
  
  &--primary {
    /* Modifier */
  }
}
```

4. **Test**: `npm run build` + visual check

#### Task: Hardcoded Values → Tokens

**Time**: 20-40 minutes per component

**Steps**:

1. **Find hardcoded values**:
```bash
# Colors
grep -E "#[0-9A-Fa-f]{6}|rgb\(|hsl\(" source/patterns/{component}/{component}.css

# Sizes
grep -E "[0-9]+px|[0-9]+rem" source/patterns/{component}/{component}.css
```

2. **Map to tokens**:

| Hardcoded | Token |
|-----------|-------|
| `#00915A` | `var(--primary)` |
| `16px` | `var(--size-4)` |
| `150ms` | `var(--duration-fast)` |

3. **Replace systematically**:
```css
/* BEFORE */
.ps-button {
  padding: 16px 24px;
  background: #00915A;
}

/* AFTER */
.ps-button {
  padding: var(--size-4) var(--size-6);
  background: var(--primary);
}
```

4. **Test**: Visual regression + contrast check

#### Task: Arrow Functions → Ternary

**Time**: 10-15 minutes per component

**Steps**:

1. **Find arrow functions**:
```bash
grep -n "=>" source/patterns/{component}/{component}.twig
```

2. **Convert to ternary**:
```twig
{# BEFORE #}
{% set classes = [
  'ps-component',
  size,
  variant
]|filter(v => v) %}

{# AFTER #}
{% set classes = [
  'ps-component',
  size != 'medium' ? 'ps-component--' ~ size : null,
  variant != 'default' ? 'ps-component--' ~ variant : null
] %}
```

3. **Test**: `npm run build` (Twig compilation)

---

## 4. Deprecation Lifecycle

### 4.1 Deprecation Process (3 Phases)

#### Phase 1: Announcement (Month 1)

**Actions**:
1. Add `@deprecated` comment in code
2. Update CHANGELOG with deprecation notice
3. Announce in team channels
4. Update documentation

**Example**:
```css
/**
 * @deprecated Use --primary instead. Will be removed in v2.0.0
 */
--brand-green: var(--primary);
```

```markdown
<!-- CHANGELOG.md -->
## [Unreleased]

### Deprecated
- **--brand-green**: Use `--primary` instead (removal: v2.0.0)
  - Migration: Find/replace `--brand-green` → `--primary`
  - Affected components: badge, button, card
```

#### Phase 2: Migration Period (Month 2-3)

**Actions**:
1. Provide migration guide
2. Support team during migration
3. Track usage of deprecated pattern
4. Offer migration assistance

**Migration Guide Template**:
```markdown
## Migration: --brand-green → --primary

**Timeline**: Removal in v2.0.0 (Q2 2026)

**Steps**:
1. Search: `grep -r "--brand-green" source/patterns/`
2. Replace: `--brand-green` → `--primary`
3. Test: `npm run build` + visual check
4. Commit: "refactor: migrate from --brand-green to --primary"

**Support**: Contact #design-system for assistance
```

#### Phase 3: Removal (Month 4+)

**Actions**:
1. Remove deprecated code
2. Bump major version
3. Update CHANGELOG with breaking change
4. Announce removal

**Example**:
```markdown
<!-- CHANGELOG.md -->
## [2.0.0] - 2026-03-15

### BREAKING CHANGES
- **Removed --brand-green**: Use `--primary` instead
  - Migration guide: [link]
  - Components affected: [list]
```

### 4.2 Deprecation Communication

**Announcement Template**:
```markdown
⚠️ Deprecation Notice: --brand-green

**Status**: Deprecated as of v1.5.0
**Removal**: v2.0.0 (Q2 2026)
**Replacement**: --primary

**Why?**: Consolidating semantic colors to single source of truth.

**Migration**: Find/replace `--brand-green` → `--primary`

**Support**: #design-system channel

**Docs**: [Migration guide link]
```

---

## 5. Breaking Changes Management

### 5.1 Breaking Change Criteria

**What constitutes a breaking change**:
- ✅ Removing a token (--old-token no longer exists)
- ✅ Renaming a component class (.ps-old → .ps-new)
- ✅ Changing prop interface (required prop added/removed)
- ✅ Changing component behavior (default changes)
- ✅ Removing a file from 4-file structure

**NOT breaking changes**:
- ❌ Adding new token
- ❌ Adding new component
- ❌ Adding optional prop
- ❌ Fixing bugs
- ❌ Improving documentation

### 5.2 Semantic Versioning

**Version Format**: MAJOR.MINOR.PATCH (e.g., 2.3.1)

- **MAJOR**: Breaking changes (v1.0.0 → v2.0.0)
- **MINOR**: New features, backward-compatible (v1.0.0 → v1.1.0)
- **PATCH**: Bug fixes, backward-compatible (v1.0.0 → v1.0.1)

### 5.3 Breaking Change Workflow

1. **Document breaking change**:
```markdown
<!-- CHANGELOG.md -->
## [2.0.0] - 2026-03-15

### BREAKING CHANGES

#### Removed --brand-green token
**Impact**: All usages must migrate to --primary
**Migration**: Find/replace `--brand-green` → `--primary`
**Affected**: badge, button, card components
**Deprecation**: Announced v1.5.0 (2025-12-15)
```

2. **Provide migration script** (if applicable):
```bash
#!/bin/bash
# migrate-brand-green.sh

echo "Migrating --brand-green to --primary..."
find source/patterns -name "*.css" -exec sed -i 's/--brand-green/--primary/g' {} +
echo "Migration complete. Run 'npm run build' to verify."
```

3. **Communication**:
- [ ] Update CHANGELOG.md
- [ ] Announce in team channels
- [ ] Update documentation
- [ ] Provide migration guide
- [ ] Offer migration support

4. **Rollback plan**:
```bash
# Rollback to previous version
git checkout v1.9.9

# Or restore specific file
git checkout v1.9.9 -- source/props/brand.css
```

---

## 📝 Quick Reference

| Task | File | Section |
|------|------|---------|
| Create new token | [05-maintenance.md](#1-token-creation-process) | 1.3 |
| Check if token exists | `grep -r "--token" source/props/` | 1.2 |
| Legacy pattern catalog | [05-maintenance.md](#2-legacy-pattern-catalog) | 2.1 |
| Refactor component | [04-quality-assurance.md](04-quality-assurance.md) | Section 1 |
| Deprecate feature | [05-maintenance.md](#4-deprecation-lifecycle) | 4.1 |
| Document breaking change | [05-maintenance.md](#5-breaking-changes-management) | 5.3 |
| Migration workflow | [05-maintenance.md](#3-migration-workflows) | 3.2 |

---

**Version**: 4.0.0  
**Last Updated**: 2025-12-12  
**Maintainers**: Design System Team
