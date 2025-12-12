# Prompt: Standardize Legacy Component

**Purpose**: Migrate Legacy Pattern 1 or 2 component to v4.0.0 standards.

---

## 📋 Prompt Template

```
Standardize legacy component: {COMPONENT_NAME}
Location: source/patterns/{level}/{component}/

OBJECTIVE: Migrate from Legacy Pattern 1/2 to v4.0.0 standards

LEGACY PATTERNS (Pre-v4.0.0):

**Legacy Pattern 1**: Flat CSS without nesting
- Single-level selectors repeated
- Hard to maintain, verbose
- Example: .ps-button { } .ps-button__icon { } .ps-button--primary { }

**Legacy Pattern 2**: Some nesting but hardcoded values
- Basic nesting present
- Colors/sizes hardcoded (#00915A, 16px)
- Breaks design token system

**Legacy Pattern 3**: Missing Autodocs
- Storybook stories exist
- tags: ['autodocs'] missing
- Props table not generated

---

## STANDARDIZATION WORKFLOW

### STEP 1: BACKUP

Create backup before changes:
cp -r source/patterns/{level}/{component} source/patterns/{level}/{component}.backup

Document current state:
- Audit score: {run audit-component.md}
- Legacy patterns detected: {1/2/3}
- Files present: {list}

---

### STEP 2: IDENTIFY LEGACY PATTERNS

Run detection:

# Pattern 1: Flat CSS
echo "Top-level selectors:" $(grep -c "^\.[a-z]" {component}.css)
echo "Nesting count:" $(grep -c "&" {component}.css)
→ If top-level >1 and nesting <5: Legacy Pattern 1 ✅

# Pattern 2: Hardcoded values
grep -E "#[0-9a-fA-F]{3,6}|[0-9]+px|[0-9]+ms" {component}.css | grep -v "var(--" | wc -l
→ If >0: Legacy Pattern 2 ✅

# Pattern 3: Missing Autodocs
grep -c "tags: \['autodocs'\]" {component}.stories.jsx
→ If 0: Legacy Pattern 3 ✅

---

### STEP 3: FIX LEGACY PATTERN 1 (Flat CSS)

**Use refactor-css.md prompt**

Key actions:
1. Identify selector hierarchy
2. Convert to PostCSS nesting with &
3. Maintain correct cascade order
4. Test visual regression

BEFORE (flat, 40 lines):
.ps-card { display: block; }
.ps-card__header { padding: 16px; }
.ps-card__title { font-size: 19px; }
.ps-card__body { padding: 16px; }
.ps-card__footer { padding: 16px; border-top: 1px solid #ccc; }
.ps-card--featured { border: 2px solid #00915A; }
.ps-card--featured .ps-card__title { color: #00915A; }

AFTER (nested, 45 lines):
.ps-card {
  display: block;
  
  &__header {
    padding: var(--size-4);
  }
  
  &__title {
    font-size: var(--font-size-3);
  }
  
  &__body {
    padding: var(--size-4);
  }
  
  &__footer {
    padding: var(--size-4);
    border-top: 1px solid var(--border-light);
  }
  
  &--featured {
    border: 2px solid var(--primary);
    
    .ps-card__title {
      color: var(--primary);
    }
  }
}

---

### STEP 4: FIX LEGACY PATTERN 2 (Hardcoded Values)

**Use find-issues.md prompt** (Category 1)

Map ALL hardcoded values to tokens:

COLORS:
#00915A → var(--primary)
#A12B66 → var(--secondary)
#198754 → var(--success)
#EB3636 → var(--danger)
#FFFFFF → var(--white)
#F9F9F9 → var(--gray-50)
rgba(0,0,0,0.1) → var(--overlay-dark-light)

SIZES:
4px → var(--size-1)
8px → var(--size-2)
12px → var(--size-3)
16px → var(--size-4)
20px → var(--size-5)
24px → var(--size-6)

TYPOGRAPHY:
13px → var(--font-size-1)
16px → var(--font-size-2)
19px → var(--font-size-3)
400 → var(--font-weight-normal)
600 → var(--font-weight-semibold)

BORDERS:
4px → var(--radius-1)
8px → var(--radius-2)
1px solid #ccc → 1px solid var(--border-light)

DURATIONS:
150ms → var(--duration-fast)
300ms → var(--duration-base)

Verify 100% token coverage:
grep -E "#[0-9a-fA-F]{3,6}|[0-9]+px|[0-9]+ms" {component}.css | grep -v "var(--"
→ Should return 0 results ✅

---

### STEP 5: FIX LEGACY PATTERN 3 (Missing Autodocs)

**Use update-storybook.md prompt**

Key actions:
1. Add tags: ['autodocs'] to export default
2. Verify import/render format (Twig not React)
3. Categorize argTypes (6 categories)
4. Add showcase stories
5. Rebuild Storybook

BEFORE:
export default {
  title: 'Components/Card',
  // Missing tags
};

AFTER:
export default {
  title: 'Components/Card',
  tags: ['autodocs'],  // ✅ Added
  parameters: {
    docs: {
      description: {
        component: 'Flexible card component for content display.',
      },
    },
  },
};

Verify Autodocs:
npm run storybook
→ Open component
→ Check "Docs" tab exists ✅
→ Verify Props table generated ✅

---

### STEP 6: ADDITIONAL STANDARDIZATIONS

**A. Twig Template**:
- [ ] Header comment with @param entries
- [ ] All defaults: |default()
- [ ] Classes with ternary + null
- [ ] NO arrow functions (.filter(v => v))
- [ ] Composition with {% include %} + 'only'

**B. CSS Styles**:
- [ ] All values from tokens (0 hardcoded)
- [ ] Nesting with & syntax
- [ ] Cascade order correct
- [ ] Semantic colors only
- [ ] focus-visible on ALL interactive elements
- [ ] Component-scoped variables (Layer 2)

**C. YAML Data**:
- [ ] Real Estate context
- [ ] All props defined
- [ ] Valid format

**D. Storybook**:
- [ ] tags: ['autodocs']
- [ ] argTypes categorized
- [ ] Default + Showcases
- [ ] NO individual variant stories

**E. README**:
- [ ] All 7 sections present (Usage, Props, BEM, Tokens, A11y, Examples, Notes)
- [ ] Token list complete
- [ ] BEM structure documented

---

### STEP 7: VALIDATE

**A. Build check**:
npm run build
→ Must pass ✅

**B. Visual regression**:
npm run watch
→ Open http://localhost:6006
→ Verify ALL stories render identically
→ Check variants, sizes, states
→ Test responsive breakpoints

**C. Conformity audit**:
Use audit-component.md prompt
→ Target: 95-100/100 (legacy was likely 50-70)

**D. Token coverage**:
npm run tokens:check -- {component-name}
→ Verify all tokens exist
→ Verify no hardcoded values

**E. Accessibility**:
Use check-accessibility.md prompt
→ Verify WCAG 2.2 AA compliance
→ Check focus indicators
→ Test keyboard navigation

---

### STEP 8: DOCUMENT CHANGES

Create migration notes:

## Migration Report: {Component}

**Date**: {YYYY-MM-DD}  
**From**: Legacy Pattern {1/2/3}  
**To**: v4.0.0 Standards

---

### Changes

**Pattern 1 (Flat CSS)**:
- Converted {X} flat selectors to nested
- Improved maintainability
- Reduced CSS from {X} to {Y} lines (after token replacement expansion)

**Pattern 2 (Hardcoded Values)**:
- Replaced {X} hardcoded colors → semantic tokens
- Replaced {Y} hardcoded sizes → size tokens
- Replaced {Z} hardcoded durations → animation tokens
- 100% token coverage ✅

**Pattern 3 (Missing Autodocs)**:
- Added tags: ['autodocs']
- Categorized {X} argTypes
- Added {Y} showcase stories
- Props table auto-generated ✅

**Additional**:
- Fixed {X} Twig issues (arrow functions, defaults, etc.)
- Added {Y} missing focus-visible styles
- Updated README with token documentation

---

### Metrics

| Metric | Before | After | Improvement |
|--------|--------|-------|-------------|
| Audit Score | {X}/100 | {Y}/100 | +{Z} points |
| Hardcoded Values | {X} | 0 | -100% ✅ |
| Nesting Depth | Flat | 2-3 levels | Better maintainability |
| Autodocs | ❌ | ✅ | Full props documentation |
| Focus Indicators | {X}/{Y} | {Y}/{Y} | 100% coverage ✅ |

---

### Visual Regression

**Result**: None ✅

All variants, sizes, and states render identically to legacy version.

---

### Breaking Changes

{List any breaking changes, if applicable}

Example:
- Removed non-standard variant: `--custom` (use `--primary` or `--secondary`)
- Changed prop name: `theme` → `variant` (consistency with v4.0.0)

---

### Migration Guide (for consumers)

If this component is used in Drupal:

**No action required** (visual parity maintained) ✅

OR

**Action required**:
1. Update variant values: {old} → {new}
2. Update prop names: {old} → {new}
3. Test in staging environment

---

## COMMIT FORMAT

refactor({level}): Standardize {component} to v4.0.0

Migration: Legacy Pattern {1/2/3} → v4.0.0 standards

Changes:
- Convert flat CSS to nested PostCSS (Pattern 1)
- Replace {X} hardcoded values with design tokens (Pattern 2)
  * Colors: {X} → semantic tokens
  * Sizes: {Y} → size tokens
  * Durations: {Z} → animation tokens
- Add missing autodocs + categorized argTypes (Pattern 3)
- Fix Twig issues: {list}
- Add missing focus-visible styles
- Update README with token documentation

Metrics:
- Audit score: {before}/100 → {after}/100 (+{delta} points) ✅
- Hardcoded values: {X} → 0 (-100%) ✅
- Autodocs: ❌ → ✅
- Visual regression: None ✅

References:
- .github/instructions/05-maintenance.md (Legacy Patterns)
- .github/prompts/refactor-css.md
- .github/prompts/update-storybook.md

---

## SUCCESS CRITERIA

✅ All legacy patterns resolved
✅ Audit score ≥95/100 (target: 100)
✅ 100% token coverage (0 hardcoded values)
✅ Autodocs fully functional
✅ Build passes without errors
✅ Visual regression: None
✅ WCAG 2.2 AA compliant
✅ README complete
✅ Migration report documented

---

## ROLLBACK (if needed)

If issues found:
rm -rf source/patterns/{level}/{component}
mv source/patterns/{level}/{component}.backup source/patterns/{level}/{component}

Investigate issues before re-attempting.
```

---

**Estimated Time**: 2-4 hours (depending on complexity + patterns)  
**Difficulty**: High  
**Prerequisites**: 
- Understanding of all 3 legacy patterns
- Token system knowledge
- PostCSS nesting syntax
- Storybook Autodocs configuration  
**Related Prompts**: 
- refactor-css.md (Pattern 1)
- find-issues.md (Pattern 2)
- update-storybook.md (Pattern 3)
