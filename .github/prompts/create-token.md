# Prompt: Create New Design Token

**Purpose**: Propose a new design token following governance workflow.

---

## 📋 Prompt Template

```
Propose new design token: {TOKEN_NAME}

CONTEXT:
Token creation follows strict governance process (05-maintenance.md Section 1)
Timeline: 2-5 days (includes design review)

STEP 1: VERIFY NEED (5 criteria must be met)

1. Used 3+ times across components
   - List all current/planned usages
   - Provide specific examples

2. Represents design decision (not arbitrary)
   - Explain design rationale
   - Reference design system principles

3. Fits into existing token pattern/scale
   - Check progression in source/props/{file}.css
   - Verify value aligns with scale (e.g., 0.25rem increments for sizes)

4. Has semantic meaning
   - Name describes purpose, not value
   - ✅ --spacing-card-padding (semantic)
   - ❌ --spacing-24px (value-based)

5. Benefits global consistency
   - How does this token improve system coherence?
   - What problem does it solve?

STEP 2: SEARCH EXISTING TOKENS
Run these commands to verify token doesn't exist:

npm run tokens:check -- --{token-name}
grep -r "similar-pattern" source/props/

Check these files:
- source/props/sizes.css (spacing, widths, heights)
- source/props/colors.css (palette)
- source/props/brand.css (semantic colors)
- source/props/fonts.css (typography)
- source/props/borders.css (radius, width)
- source/props/shadows.css (box-shadow)
- source/props/animations.css (durations)
- source/props/easing.css (timing functions)
- source/props/zindex.css (layering)

STEP 3: VERIFY NAMING CONVENTION
Follow these patterns (from 05-maintenance.md):

| Category | Pattern | Example |
|----------|---------|---------|
| Primitive colors | --{color}-{shade} | --blue-600 |
| Semantic colors | --{meaning} | --primary, --success |
| Sizes | --size-{number} | --size-4, --size-12 |
| Font sizes | --font-size-{number} | --font-size-5 |
| Font weights | --font-weight-{number} | --font-weight-600 |
| Border radius | --radius-{number} | --radius-2 |
| Shadows | --shadow-{number} | --shadow-3 |
| Durations | --duration-{speed} | --duration-fast |
| Easing | --ease-{type} | --ease-in-out |
| Z-index | --z-{context} | --z-modal |

FORBIDDEN PATTERNS:
❌ --button-primary (component-specific in global)
❌ --color-primary (redundant prefix)
❌ --size-medium (use numbers not t-shirt sizes)
❌ --16px (value in name)

STEP 4: CHECK SCALE PROGRESSION
Verify value fits existing scale:

SIZES (0.25rem increments):
--size-0: 0
--size-1: 0.25rem (4px)
--size-2: 0.5rem (8px)
--size-3: 0.75rem (12px)
--size-4: 1rem (16px)
--size-6: 1.5rem (24px)
--size-8: 2rem (32px)
[...continues...]

FONT-SIZES (1.2 ratio):
--font-size-2: 0.694rem (~11px)
--font-size-3: 0.833rem (~13px)
--font-size-4: 1rem (16px base)
--font-size-5: 1.2rem (~19px)
--font-size-6: 1.44rem (~23px)
[...continues...]

SHADOWS (visual depth progression):
--shadow-1: subtle
--shadow-2: light
--shadow-3: medium
--shadow-4: strong
--shadow-5: very strong

DURATIONS (2x multiplier):
--duration-instant: 100ms
--duration-fast: 200ms
--duration-base: 300ms
--duration-slow: 500ms
--duration-slower: 700ms

STEP 5: DOCUMENT USE CASES (3+ required)
Format:
1. Component: {name}
   File: source/patterns/{level}/{component}/{component}.css
   Usage: {describe how token will be used}
   Current: {current implementation (hardcoded value or workaround)}
   
2. Component: {name}
   [...]

3. Component: {name}
   [...]

STEP 6: CREATE PROPOSAL ISSUE
Generate GitHub issue with this template:

---
Title: [Token Proposal] {TOKEN_NAME}

**Category**: Spacing | Color | Typography | Border | Shadow | Animation | Z-Index

**Token Name**: {TOKEN_NAME}

**Value**: {VALUE}

**Use Cases** (minimum 3):
1. {Component 1 - detailed usage}
2. {Component 2 - detailed usage}
3. {Component 3 - detailed usage}

**Current State**:
- [ ] Token doesn't exist (verified via grep)
- [ ] Name follows convention (verified against 05-maintenance.md)
- [ ] Value fits scale (verified progression)
- [ ] Semantic naming (not value-based)
- [ ] 3+ use cases documented

**Alternatives Considered**:
- Alternative 1: {name} - {reason rejected}
- Alternative 2: {name} - {reason rejected}

**Naming Verification**:
- [ ] Follows pattern: {pattern from table above}
- [ ] No component-specific prefix
- [ ] No value in name
- [ ] Semantic meaning clear

**Impact Assessment**:
- Components affected: {list}
- Files to update: {list}
- Breaking change: Yes/No
- Migration required: Yes/No

**Figma Reference**: {link to design file if available}

**Checklist**:
- [ ] Verified need (5 criteria met)
- [ ] Searched existing tokens (none found)
- [ ] Naming convention validated
- [ ] Scale progression verified
- [ ] 3+ use cases documented
---

STEP 7: DESIGN REVIEW (Wait for approval)
Design team will validate:
- Design alignment
- Naming convention
- Scale fit
- Semantic meaning
- Reusability (3+ cases confirmed)

Outcomes:
- ✅ Approved → Proceed to implementation
- ⚠️  Needs revision → Update proposal
- ❌ Rejected → Use alternative approach

STEP 8: IMPLEMENTATION (After approval only)
DO NOT implement before approval!

A. Add token to props file:
source/props/{category}.css

/**
 * {TOKEN_NAME}
 * Usage: {brief description}
 * Used in: {list 3+ components}
 */
--{token-name}: {value};

B. Update components (replace hardcoded values):
Find: {hardcoded-value}
Replace: var(--{token-name})

C. Update documentation:
- source/props/README.md (add to relevant section)
- Component READMEs (list in Design Tokens section)

STEP 9: VALIDATE
Run tests:
npm run build                  # Must pass
npm run watch                  # Visual validation
grep "var(--{token-name})" source/patterns/**/*.css  # Verify usage
grep "{hardcoded-value}" source/patterns/**/*.css    # Should be 0 results

STEP 10: COMMIT
Format:
feat(tokens): Add {token-name} token

Category: {category}
Value: {value}

Usage:
- {Component 1}: {usage description}
- {Component 2}: {usage description}
- {Component 3}: {usage description}

Replaced hardcoded values in:
- {file 1}
- {file 2}
- {file 3}

References: GitHub issue #{issue-number}
Approved by: Design Team

ANTI-PATTERNS TO AVOID:
❌ Creating token without approval
❌ Component-specific token in global props (use Layer 2 variables)
❌ Breaking existing scale progression
❌ Vague names (--spacing-large instead of --size-8)
❌ Creating during component work (document need, create separately)

SUCCESS CRITERIA:
✅ All 5 need criteria met
✅ Token doesn't exist (grep verified)
✅ Naming convention followed
✅ 3+ use cases documented
✅ Design team approval received
✅ Implementation complete
✅ All hardcoded values replaced
✅ Documentation updated
```

---

## 🎯 Usage Example

```
Propose new design token: --spacing-card-content

CONTEXT:
Token creation follows strict governance process (05-maintenance.md Section 1)
Timeline: 2-5 days (includes design review)

STEP 1: VERIFY NEED (5 criteria must be met)

1. Used 3+ times across components
   - Card: Internal content padding
   - Card Offer Search: Content wrapper padding
   - Card Property: Main content area padding

2. Represents design decision
   - Design system defines consistent card content spacing
   - Ensures visual rhythm across all card variants

[...continue with all steps...]
```

---

**Estimated Time**: 2-5 days (includes review)  
**Difficulty**: High  
**Prerequisites**: Read 05-maintenance.md Section 1
