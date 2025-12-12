---
title: Token Creation Process
version: 1.0.0
lastUpdated: 2025-12-12
applyTo:
  - "source/props/**/*.css"
priority: HIGH
related:
  - css.instructions.md
  - core.instructions.md
  - workflows.instructions.md
status: ACTIVE
---

# Token Creation Process

**Purpose**: Define the complete workflow for proposing, approving, and implementing new design tokens.

---

## 🎯 When to Create a Token

**Create a token when**:
- ✅ Value is used **3+ times** across different components
- ✅ Value represents a **design decision** (not arbitrary)
- ✅ Value follows an **existing pattern** (e.g., size scale, color palette)
- ✅ Value has **semantic meaning** (--primary, --success, --large)
- ✅ Value needs **global consistency** (spacing, colors, typography)

**DO NOT create a token when**:
- ❌ Value is **component-specific** (use component-scoped variables)
- ❌ Value is used **only once** (hardcode locally with comment)
- ❌ Value is **experimental** (test in component first)
- ❌ Value doesn't fit **existing system** (propose system change first)

---

## 📋 Token Verification Checklist

**Before proposing a new token**:

### 1. Search Existing Tokens

```bash
# Search all token files
grep -r "--keyword" source/props/

# Example: Check if size token exists
grep -r "--size-" source/props/sizes.css

# Example: Check if color exists
grep -r "--primary" source/props/brand.css
```

**Check these files**:
- `source/props/colors.css` - Primitive colors (--green-600, --gray-400)
- `source/props/brand.css` - Semantic colors (--primary, --secondary, --success)
- `source/props/sizes.css` - Spacing scale (--size-1 to --size-20)
- `source/props/fonts.css` - Typography (--font-size-*, --font-weight-*)
- `source/props/borders.css` - Radii and widths
- `source/props/shadows.css` - Box shadows (--shadow-1 to --shadow-5)
- `source/props/animations.css` - Durations (--duration-fast, --duration-base)
- `source/props/easing.css` - Timing functions
- `source/props/zindex.css` - Stacking context (--z-dropdown, --z-modal)

---

### 2. Verify Naming Convention

**Token naming follows strict patterns**:

| Category | Pattern | Examples |
|----------|---------|----------|
| **Primitive Colors** | `--{color}-{shade}` | `--green-600`, `--gray-400` |
| **Semantic Colors** | `--{meaning}` | `--primary`, `--success`, `--danger` |
| **Sizes** | `--size-{number}` | `--size-4` (16px), `--size-6` (24px) |
| **Font Sizes** | `--font-size-{number}` | `--font-size-2` (16px) |
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

---

### 3. Check Value Progression

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
| **Font Sizes** | Type scale (1.2 ratio) | --font-size-0 (14px) to --font-size-9 (60px) |
| **Shadows** | Visual depth | --shadow-1 (subtle) to --shadow-5 (heavy) |
| **Durations** | 2x multiplier | --duration-fast (150ms), --duration-base (300ms) |

---

### 4. Document Use Cases

**Prepare examples** showing where token will be used:

```markdown
## Proposed Token: --size-7 (30px)

**Use Cases**:
1. Card padding-y on large screens (card.css)
2. Section spacing in organisms (header.css, footer.css)
3. Modal dialog padding (modal.css)

**Current Workaround**:
- card.css: hardcoded `30px` (line 45)
- header.css: hardcoded `30px` (line 23)
- footer.css: hardcoded `30px` (line 67)

**Impact**:
- Consolidates 3 hardcoded values
- Enables consistent spacing across layouts
- Future-proofs for theming/dark mode
```

---

## 🔄 Token Creation Workflow

### Step 1: Document Need in Component

**When you discover missing token during component work**:

```markdown
<!-- In component README.md -->

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

**DO NOT** edit `source/props/*.css` directly during component work.

---

### Step 2: Create Token Proposal Issue

**Open GitHub issue** with this template:

```markdown
## Token Proposal: [Token Name]

**Category**: [Colors / Sizes / Fonts / Borders / Shadows / Animations / Other]

**Token Name**: `--token-name`

**Value**: `value` (with unit and computed value)

**Use Cases**:
1. Component A - [specific usage]
2. Component B - [specific usage]
3. Component C - [specific usage]

**Current State**:
- [ ] Hardcoded in X components
- [ ] Currently using workaround: [describe]
- [ ] Blocking component: [name]

**Alternatives Considered**:
1. [Alternative 1] - [why rejected]
2. [Alternative 2] - [why rejected]

**Naming Verification**:
- [ ] Follows convention for category
- [ ] Fits existing scale/progression
- [ ] No similar token exists

**Impact**:
- Components affected: [list]
- Breaking changes: [none/list]
- Migration required: [yes/no]

**Figma Reference**: [link if applicable]

**Checklist**:
- [ ] Searched existing tokens (grep results attached)
- [ ] Verified naming convention
- [ ] Documented use cases (3+)
- [ ] Checked scale progression
- [ ] Prepared example implementations
```

**Label**: `tokens`, `proposal`, `design-system`

---

### Step 3: Design Team Review

**Design team validates**:
1. ✅ **Design alignment**: Token matches Figma/design specs
2. ✅ **Naming**: Follows convention and fits existing system
3. ✅ **Scale fit**: Value fits progression (sizes, colors, etc.)
4. ✅ **Semantic meaning**: Clear purpose and context
5. ✅ **Reusability**: 3+ use cases confirmed

**Review checklist**:
- [ ] Token matches design system rules
- [ ] No duplicate or overlapping tokens
- [ ] Value is future-proof (themeable, scalable)
- [ ] Documentation is clear and complete

**Outcomes**:
- ✅ **Approved**: Proceed to implementation
- ⚠️ **Needs revision**: Address feedback and resubmit
- ❌ **Rejected**: Use alternative approach (component-scoped variable, existing token)

---

### Step 4: Implementation

**Once approved**, create PR with:

#### A. Add Token to Props File

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

#### B. Update Component Files

Replace hardcoded values with token:

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

#### C. Update Documentation

**Add to `source/props/README.md`**:

```markdown
### Recent Additions

**v1.2.0** (2025-12-12):
- `--size-7`: 30px - Card desktop padding, section spacing
  - Use cases: card.css, header.css, footer.css
  - Figma: [link]
```

**Update component READMEs**:

```markdown
## Design Tokens

- `--size-7`: Card padding-y on desktop (30px)
```

---

### Step 5: Testing & Validation

**Before merging PR**:

```bash
# 1. Build passes
npm run build

# 2. Visual regression test (if available)
npm run test:visual

# 3. Check token usage
grep -r "var(--size-7)" source/patterns/

# 4. Verify no hardcoded values remain
grep -r "30px" source/patterns/
# Should only show comments or non-token contexts
```

**Manual validation**:
1. Open Storybook: `npm run watch`
2. Check all components using new token
3. Test responsive breakpoints
4. Verify dark mode (if applicable)
5. Check accessibility (contrast, spacing)

---

### Step 6: Merge & Announce

**Merge PR** with commit message:

```
feat(tokens): Add --size-7 (30px) for card desktop padding

- Add --size-7: 1.875rem (30px) to sizes.css
- Replace hardcoded 30px in card.css, header.css, footer.css
- Standardizes section spacing across layouts

Use cases:
- Card padding-y on desktop (>768px)
- Section spacing in organisms
- Modal dialog padding

Closes #123 (token proposal issue)
```

**Announce in team channels**:
```
🎨 New Token: --size-7 (30px)

Use for: Card desktop padding, section spacing
Files updated: card.css, header.css, footer.css

See: PR #456
```

---

## 🚫 Anti-Patterns

### 1. Creating Token Without Approval

```css
❌ /* source/props/sizes.css - WRONG */
   --size-custom: 30px; /* Added without process */
```

**Why wrong**: No review, no documentation, breaks governance.

---

### 2. Component-Specific Tokens in Global Props

```css
❌ /* source/props/colors.css - WRONG */
   --button-primary-bg: var(--green-600);
```

**Why wrong**: Component-scoped tokens belong in component CSS, not global props.

**Correct approach**:
```css
/* source/patterns/elements/button/button.css */
.ps-button {
  --ps-button-bg: var(--primary); /* Component-scoped */
}
```

---

### 3. Breaking Existing Scale

```css
❌ /* source/props/sizes.css - WRONG */
   --size-6: 1.5rem;   /* 24px */
   --size-15: 30px;    /* Breaks scale, use --size-7 instead */
   --size-8: 2rem;     /* 32px */
```

**Why wrong**: Disrupts progression, confusing naming.

---

### 4. Vague Token Names

```css
❌ --spacing-medium: 20px;  /* Ambiguous */
❌ --color-green: #00915A;  /* Not semantic */
❌ --big-shadow: 0 4px 6px rgba(0,0,0,0.2);  /* Informal */
```

**Correct approach**:
```css
✅ --size-5: 1.25rem;       /* Clear scale position */
✅ --primary: #00915A;      /* Semantic meaning */
✅ --shadow-3: 0 4px 6px rgba(0,0,0,0.2);  /* Numbered scale */
```

---

### 5. Hardcoding During Component Work

```css
❌ /* card.css - WRONG */
   .ps-card {
     padding: 30px; /* TODO: Use token later */
   }
```

**Why wrong**: "Later" never happens, creates technical debt.

**Correct approach**:
```markdown
<!-- card/README.md -->
## Missing Tokens
- --size-7 (30px) needed for desktop padding
- Opened issue #123 for token proposal
```

```css
/* card.css - TEMPORARY WORKAROUND */
.ps-card {
  padding: 1.875rem; /* TEMP: 30px - Pending --size-7 token (issue #123) */
}
```

---

## 📊 Token Lifecycle

```
1. NEED IDENTIFIED
   └─ Component work reveals missing token
      └─ Document in component README
         └─ Continue with workaround (commented)

2. PROPOSAL
   └─ Create GitHub issue with template
      └─ Design team review
         ├─ Approved → Proceed
         ├─ Needs revision → Update proposal
         └─ Rejected → Use alternative

3. IMPLEMENTATION
   └─ Create PR with token addition
      └─ Update affected components
         └─ Update documentation

4. VALIDATION
   └─ Build passes
      └─ Visual testing
         └─ Accessibility check

5. MERGE & ANNOUNCE
   └─ Merge PR with descriptive commit
      └─ Announce in team channels
         └─ Update changelog

6. MAINTENANCE
   └─ Monitor usage
      └─ Refactor if needed
         └─ Deprecate old patterns
```

**Average Timeline**: 2-5 days (depending on review availability)

---

## ✅ Quick Reference

**I need a token for...**

| Need | Action | Token File |
|------|--------|------------|
| **Spacing** (padding, margin, gap) | Check `--size-*` scale | `sizes.css` |
| **Color** (brand, semantic) | Check `--primary`, `--success`, etc. | `brand.css` |
| **Typography** (size, weight, line-height) | Check `--font-size-*`, `--font-weight-*` | `fonts.css` |
| **Border** (radius, width) | Check `--radius-*`, `--border-size-*` | `borders.css` |
| **Shadow** (elevation) | Check `--shadow-*` | `shadows.css` |
| **Animation** (duration, easing) | Check `--duration-*`, `--ease-*` | `animations.css`, `easing.css` |
| **Z-Index** (stacking) | Check `--z-*` | `zindex.css` |

**Token doesn't exist?**
1. Search thoroughly (grep all props files)
2. Document need in component README
3. Open token proposal issue
4. Wait for approval
5. Implement after approval

**Never edit `source/props/*.css` directly during component work.**

---

## 🔗 Related Documentation

- **css.instructions.md**: Token verification workflow (Section: Token Verification)
- **workflows.instructions.md**: Component creation process (Step 3: Token Verification)
- **core.instructions.md**: Design token system overview
- **source/props/README.md**: Token catalog and usage guide

---

## 🔄 Maintenance

**When to review this process**:
- Design system major version changes
- Token governance changes
- Team structure changes
- Tooling updates (linting, automation)

**Process improvements**:
- Add automated token linting (P2 future work)
- Create token proposal CLI tool
- Integrate with Figma token sync
- Add visual token browser/explorer

---

**Maintainers**: Design System Team  
**Last Updated**: 2025-12-12  
**Next Review**: Q2 2026 or when governance changes
