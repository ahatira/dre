# Form Atoms Refactor Plan - Drupal Class Consistency

**Objective**: Make all form Atoms (Input, Textarea, Select, Label) use Drupal standard classes for consistency with Form Field molecule

**Status**: 🔄 PLANNING PHASE

---

## Current State Analysis

### Form Field (Molecule) - DONE ✅
- Uses Drupal classes: `form-item`, `form-group`, `form-control`, `form-error`, `form-helper`, `form-label-wrapper`
- Passes `.form-control` to Atoms via `attributes.addClass('form-control')`
- Atoms should render with both `ps-*` AND `form-control` classes

### Input Atom - NEEDS UPDATE
**Current**:
```twig
class="ps-input ps-input--disabled"
```

**Should be**:
```twig
class="ps-input form-control ps-input--disabled"
```

### Textarea Atom - NEEDS UPDATE
**Current**:
```twig
class="ps-textarea ps-textarea--disabled"
```

**Should be**:
```twig
class="ps-textarea form-control ps-textarea--disabled"
```

### Select Atom - NEEDS UPDATE
**Current**:
```twig
class="ps-select ps-select--error"
```

**Should be**:
```twig
class="ps-select form-control ps-select--error"
```

### Label Atom - ALREADY CORRECT ✅
- Uses `ps-label` class (not form-specific)
- Correctly placed by Form Field

---

## Affected Components

### Form Controls That Need `.form-control` Class
1. **Input** (`source/patterns/elements/input/input.twig`)
   - Add `form-control` to default classes
   - CSS selector change in input.css

2. **Textarea** (`source/patterns/elements/textarea/textarea.twig`)
   - Add `form-control` to default classes
   - CSS selector change in textarea.css

3. **Select** (`source/patterns/elements/select/select.twig`)
   - Add `form-control` to wrapper or inner element
   - Verify structure (usually wraps the native `<select>`)
   - CSS selector change in select.css

### CSS Files That Need Updates
1. **input.css** - Change `.ps-input` to `.ps-input, .form-control[type="text"]...` or simplify to `.form-control.ps-input`
2. **textarea.css** - Similar pattern
3. **select.css** - Similar pattern

---

## Implementation Strategy

### Phase 1: Update Atom Templates (Twig)
Add `form-control` to the classes array in each Atom:

**Before**:
```twig
{%- set classes = [
  'ps-input',
  disabled ? 'ps-input--disabled' : null,
  state ? 'ps-input--' ~ state : null
] -%}
```

**After**:
```twig
{%- set classes = [
  'ps-input',
  'form-control',
  disabled ? 'ps-input--disabled' : null,
  state ? 'ps-input--' ~ state : null
] -%}
```

### Phase 2: Update Atom CSS
Ensure selectors work with both `ps-input` and `form-control` classes:

**Option A - Combined selector**:
```css
.ps-input,
.form-control:not([type="hidden"]):not([type="checkbox"]):not([type="radio"]) {
  /* Base input styles */
}
```

**Option B - Simple (Recommended)**:
```css
.form-control {
  /* Base control styles - works for input, textarea, select */
}

.ps-input {
  /* PS Theme specific tweaks */
}

.ps-input--error {
  /* Modifiers */
}
```

### Phase 3: Verify Form Field Integration
Ensure Form Field correctly passes `.form-control` class to Atoms via `attributes.addClass('form-control')`

### Phase 4: Test All Components
- Storybook atom stories should still work
- Form Field should render with proper classes
- No visual regressions

---

## Timeline

| Phase | Task | Status | Commit |
|-------|------|--------|--------|
| 1 | Update input.twig with form-control class | ✅ DONE | d3cbd5a |
| 2 | Update textarea.twig with form-control class | ✅ DONE | d3cbd5a |
| 3 | Update select.twig with form-control class | ✅ DONE | d3cbd5a |
| 4 | Update input.css for dual-class support | ✅ DONE | 1159320 |
| 5 | Update textarea.css for dual-class support | ✅ DONE | 1159320 |
| 6 | Update select.css for dual-class support | ✅ DONE | 1159320 |
| 7 | Simplify Form Field - remove redundant class passing | ✅ DONE | ec40457 |
| 8 | Test all changes in Storybook | 🔄 NEXT | - |
| 9 | Update copilot-instructions.md with form rules | 🔄 NEXT | - |
| 10 | Create final summary and best practices doc | 🔄 NEXT | - |

---

## Git Commits Planned

```
refactor(elements): Input Atom - Add form-control class for Drupal consistency
refactor(elements): Textarea Atom - Add form-control class for Drupal consistency
refactor(elements): Select Atom - Add form-control class for Drupal consistency
docs(instructions): Add form component standards to copilot-instructions.md
```

---

## Key Design Decisions

### Why Add `form-control` to Atoms?
1. **Drupal Compatibility**: Drupal themes expect `.form-control` on form controls
2. **Consistency**: Form Field already applies this class, Atoms should render it natively
3. **Future-Proof**: If Atoms are used standalone, they're already Drupal-compatible
4. **Simplification**: No need for Form Field to pass class, Atoms render it by default

### Class Order in Atoms
```twig
[
  'ps-input',              # Design system class (FIRST - lowest specificity)
  'form-control',          # Drupal base class (SECOND)
  disabled ? '...' : null, # State modifiers (LAST - highest specificity)
]
```

---

## Risks & Mitigations

| Risk | Impact | Mitigation |
|------|--------|-----------|
| Breaking Atom stories | Medium | Atom stories will still pass `attributes.addClass('form-control')` |
| CSS specificity issues | Low | Ensure `.form-control` specificity matches `.ps-input` |
| Drupal integration fails | High | Test with actual Drupal forms after changes |
| Visual regression | Medium | Full visual QA on all form variations |

---

## Success Criteria

- ✅ All Atoms render with both `ps-*` and `form-control` classes
- ✅ Form Field integration still works (passes attributes)
- ✅ No visual regressions in Storybook
- ✅ Drupal forms render correctly with Atoms
- ✅ Instructions updated for future form components
- ✅ All builds pass without errors

---

## Implementation Summary

### ✅ Phases 1-3 COMPLETED

**All Atoms now support Drupal .form-control class natively**

#### Phase 1: Twig Updates (Commit: d3cbd5a)
- Added `form-control` to Input classes array
- Added `form-control` to Textarea classes array
- Added `form-control` to Select __input element class

#### Phase 2: CSS Selectors (Commit: 1159320)
- Input: Combined selectors for all input types + form-control variants
- Textarea: Combined selector for textarea + form-control variant
- Select: Full rule set for form-control:is(select) with complete styling
- All modifiers (--error, --success, --warning) updated to support form-control

#### Phase 3: Form Field Simplification (Commit: ec40457)
- Removed attributes.addClass('form-control') from all Atom includes
- Form Field now depends on Atoms to provide their own form-control class
- Cleaner template logic, no redundant class passing

### 🔄 Phases 4+ IN PROGRESS

Next steps:
1. Test in Storybook (visual validation)
2. Update copilot-instructions.md
3. Create final documentation

---

**Current Build Status**: ✅ PASSING (2.97s, no errors)
**Last Updated**: 2025-12-14
