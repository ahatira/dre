# Prompt: Audit Component (90-Point Checklist)

> **⚠️ NOTE (December 2025)**: This file contains the old 100-point scoring system with 5-file structure.  
> **CURRENT STANDARD**: Use **90-point system** with **4-file structure** (README.md removed).  
> **Reference**: See `.github/instructions/04-quality-assurance.md` for up-to-date audit checklist.

**Purpose**: Run complete conformity audit on any component and generate detailed report.

---

## 📋 Prompt Template

```
Audit component: {COMPONENT_NAME}
Location: source/patterns/{level}/{component}/

Run COMPLETE 100-point conformity audit per 04-quality-assurance.md standards.

SCORING BREAKDOWN (100 points total):

=== 1. FILE STRUCTURE (10 points) ===
☐ 5 files exist: {component}.twig, .css, .yml, .stories.jsx, README.md (2 pts each)
☐ All files named correctly (lowercase, kebab-case)
☐ NO {component}.js unless documented behavior required

=== 2. TWIG TEMPLATE (15 points) ===
☐ Header comment with component description (2 pts)
☐ @param entries for ALL props (type, required/optional, description) (3 pts)
☐ ALL defaults use: {%- set prop = prop|default('value') -%} (3 pts)
☐ Classes use ternary + null (NO filter(v => v), .map(), .includes()) (3 pts)
☐ Minimal markup (defaults = NO modifier classes) (2 pts)
☐ Composition with {% include %} + 'only' keyword (1 pt)
☐ Real Estate context (NOT generic placeholders) (1 pt)

=== 3. CSS STYLES (20 points) ===
☐ ALL values from tokens (ZERO hardcoded #colors, 16px, 150ms) (5 pts)
☐ Nesting with & (.ps-component { &__element { } &--modifier { } }) (3 pts)
☐ Cascade order: Base → Elements → Modifiers → States (3 pts)
☐ Semantic colors ONLY (--primary, --success, NOT --green-600) (3 pts)
☐ Focus-visible on ALL interactives (2 pts)
☐ Component-scoped variables (Layer 2: --ps-{component}-{property}) (2 pts)
☐ Modifier independence (each works alone on base) (2 pts)

=== 4. STORYBOOK (20 points) ===
☐ tags: ['autodocs'] in export default (CRITICAL) (5 pts)
☐ Import: import componentTwig from './{component}.twig'; (2 pts)
☐ Render: render: (args) => componentTwig(args) (2 pts)
☐ NO React/JSX (2 pts)
☐ ArgTypes categorized (Content|Appearance|Behavior|Link|A11y|Layout) (3 pts)
☐ Description ≤ 2 lines (1 pt)
☐ Stories: Default + Showcases (AllColors, AllSizes, UseCases) (3 pts)
☐ NO individual variant stories (Primary, Secondary, Small...) (2 pts)

=== 5. YAML DATA (10 points) ===
☐ Valid YAML format (3 pts)
☐ Real Estate realistic data (addresses, types, prices) (4 pts)
☐ All required props defined (3 pts)

=== 6. README.md (10 points) ===
☐ Usage section with Twig example (2 pts)
☐ Props table (name | type | default | description) (2 pts)
☐ BEM Structure in tree format (2 pts)
☐ Design Tokens section (list all tokens used) (2 pts)
☐ Accessibility checklist (WCAG AA) (1 pt)
☐ Examples (3+ real-world use cases) (1 pt)

=== 7. BEM NAMING (10 points) ===
☐ ps- prefix on block class (3 pts)
☐ Correct format: .ps-block__element--modifier (3 pts)
☐ NO double underscore (__) in element names (2 pts)
☐ NO camelCase or PascalCase (2 pts)

=== 8. ACCESSIBILITY (5 points) ===
☐ Contrast ratios: 4.5:1 text, 3:1 UI (2 pts)
☐ Focus-visible indicators present (1 pt)
☐ ARIA attributes when semantic HTML insufficient (1 pt)
☐ Keyboard navigable (1 pt)

=== 9. COMPOSITION (IF MOLECULE/ORGANISM) (Bonus 10 pts) ===
☐ Token-First workflow used (STEP 3 preferred: token overrides) (5 pts)
☐ NO direct atom CSS modification (2 pts)
☐ NO baseClass parameter (removed v4.0.0) (1 pt)
☐ attributes.addClass() for context classes (2 pts)

SCORING INTERPRETATION:
- 90-100: ✅ Production ready
- 75-89:  ⚠️  Minor fixes needed
- 60-74:  ⚠️  Moderate refactoring
- <60:    ❌ Major refactoring required

OUTPUT FORMAT:

# Audit Report: {Component}

**Date**: {YYYY-MM-DD}  
**Location**: source/patterns/{level}/{component}/  
**Score**: X/100

## Summary
[Overall assessment in 2-3 sentences]

## Scoring by Category

| Category | Score | Max | Status |
|----------|-------|-----|--------|
| File Structure | X | 10 | ✅/⚠️/❌ |
| Twig Template | X | 15 | ✅/⚠️/❌ |
| CSS Styles | X | 20 | ✅/⚠️/❌ |
| Storybook | X | 20 | ✅/⚠️/❌ |
| YAML Data | X | 10 | ✅/⚠️/❌ |
| README | X | 10 | ✅/⚠️/❌ |
| BEM Naming | X | 10 | ✅/⚠️/❌ |
| Accessibility | X | 5 | ✅/⚠️/❌ |
| Composition* | X | 10 | ✅/⚠️/❌ |
| **TOTAL** | **X** | **100** | **Status** |

*Composition only for molecules/organisms

## Critical Issues (P0)
[List issues preventing production deployment]

## Important Issues (P1)
[List issues requiring fix before next release]

## Minor Issues (P2)
[List nice-to-have improvements]

## Files Checked
- [ ] {component}.twig
- [ ] {component}.css
- [ ] {component}.yml
- [ ] {component}.stories.jsx
- [ ] README.md

## Recommendations
[Ordered list of fixes with priority]

## Next Steps
1. [First action]
2. [Second action]
3. [Third action]

---

**Auditor**: AI Agent  
**Reference**: .github/instructions/04-quality-assurance.md
```

---

## 🎯 Usage Example

```
Audit component: badge
Location: source/patterns/elements/badge/

Run COMPLETE 100-point conformity audit per 04-quality-assurance.md standards.
[...rest of prompt...]
```

---

**Estimated Time**: 20-30 minutes  
**Output**: Detailed report with scoring and actionable recommendations
