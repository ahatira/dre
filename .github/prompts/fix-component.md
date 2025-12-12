# Prompt: Fix Failing Component

**Purpose**: Fix a component with audit score < 90/100 using step-by-step validation.

---

## 📋 Prompt Template

```
Fix component: {COMPONENT_NAME}
Location: source/patterns/{level}/{component}/
Current audit score: {SCORE}/100

OBJECTIVE: Increase conformity score to ≥ 90/100

STEP 1: RUN AUDIT
Use audit-component.md prompt to generate current report
Identify all issues by priority (P0 → P1 → P2)

STEP 2: PRIORITIZE FIXES
Group issues by category:

CRITICAL (P0 - Must fix):
- Hardcoded values (prevents token system)
- Missing files (breaks structure)
- Drupal incompatibilities (breaks in production)
- Missing autodocs (breaks documentation)
- Wrong BEM prefix (inconsistent naming)

IMPORTANT (P1 - Should fix):
- Flat CSS (hard to maintain)
- Wrong cascade order (visual bugs)
- Semantic color names (use --success not green)
- Missing focus-visible (accessibility violation)
- Missing Layer 2 variables (limited customization)

NICE TO HAVE (P2 - Can defer):
- Documentation improvements
- Additional showcases
- Extra README examples

STEP 3: FIX SYSTEMATICALLY
Work in this order (most impactful first):

A. FIX TWIG (Drupal compatibility - breaks builds)
   - Replace arrow functions: filter(v => v) → ternary + null
   - Replace .map(), .filter(), .includes()
   - Add missing @param comments
   - Fix default values format

B. FIX CSS (Visual + maintainability)
   - Replace ALL hardcoded values with tokens
   - Add nesting with &
   - Reorder cascade: Base → Elements → Modifiers → States
   - Change to semantic colors (--primary not --green-600)
   - Add focus-visible to interactives
   - Add component-scoped variables (--ps-{component}-*)

C. FIX STORYBOOK (Documentation)
   - Add tags: ['autodocs'] to export default
   - Fix import/render format
   - Categorize argTypes
   - Add Default + Showcases stories
   - Remove individual variant stories

D. FIX YAML (Data quality)
   - Use Real Estate realistic data
   - Define all required props

E. FIX README (Documentation)
   - Add missing sections (Usage, Props, BEM, Tokens, A11y, Examples)
   - Format props table correctly
   - Document all tokens used

STEP 4: VALIDATE INCREMENTALLY
After each fix category:

1. Run: npm run build
   - Must pass with 0 errors
   - Fix any build errors before continuing

2. Check Storybook: npm run watch
   - Visual validation: All variants render correctly
   - No console errors
   - Autodocs tab appears and is complete

3. Test interactions:
   - Keyboard navigation (Tab, Enter, Escape)
   - Focus indicators visible
   - States work (hover, active, disabled)

STEP 5: RE-AUDIT
Run audit-component.md prompt again
Calculate new score
Compare before/after

STEP 6: COMMIT
Format:
refactor({level}): Standardize {component} component

Before: X/100
After: Y/100 ✅

Fixes applied:
- [Category A]: List of fixes
- [Category B]: List of fixes
- [Category C]: List of fixes

Critical issues resolved:
- Issue 1 (detailed description)
- Issue 2 (detailed description)

References: .github/instructions/04-quality-assurance.md

TROUBLESHOOTING GUIDE:
Use 04-quality-assurance.md Section 2 for common errors:

ERROR 1: Token not found
→ grep -r "token-name" source/props/
→ If missing: Document need in README, use closest alternative

ERROR 2: Hardcoded value
→ Find token mapping in source/props/README.md
→ Replace with var(--token-name)

ERROR 3: CSS nesting syntax error
→ Verify & usage: .ps-component { &__element { } }
→ Check PostCSS config

ERROR 4: Arrow function in Twig
→ Replace: filter(v => v) with ternary: condition ? 'class' : null

ERROR 5: Autodocs not showing
→ Verify: tags: ['autodocs'] in export default
→ Rebuild: npm run storybook:build

ERROR 6: React/JSX detected
→ Wrong Storybook edition
→ Use: import componentTwig from './{component}.twig';
→ Use: render: (args) => componentTwig(args)

SUCCESS CRITERIA:
✅ Audit score increased to ≥ 90/100
✅ Build passes (npm run build)
✅ Storybook renders without errors
✅ All P0 issues resolved
✅ All P1 issues resolved (or documented as future work)
✅ Commit message documents before/after scores
```

---

## 🎯 Real-World Example

**Before** (Link component - 65/100):
- Flat CSS (no nesting)
- Hardcoded values (#007BFF, 16px)
- Missing autodocs
- Arrow function in Twig

**After** (100/100):
- Nested CSS with &
- All tokens (--primary, --size-4)
- tags: ['autodocs'] added
- Ternary pattern

**Commit**: `refactor(elements): Standardize link component (65/100 → 100/100 ✅)`

---

**Estimated Time**: 1-2 hours (depending on score)  
**Difficulty**: Medium  
**Prerequisites**: Run audit first to identify issues
