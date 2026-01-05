# Prompt: Refactor CSS (Flat → Nested with Tokens)

**Purpose**: Convert legacy flat CSS to modern nested syntax with design tokens.

---

## 📋 Prompt Template

```
Refactor CSS for component: {COMPONENT_NAME}
Location: source/patterns/{level}/{component}/{component}.css

OBJECTIVE: Convert flat CSS to nested + replace all hardcoded values with tokens

CURRENT ISSUES (Legacy Pattern 1 + 2):
- Flat CSS without nesting (hard to maintain)
- Hardcoded values (#colors, px, ms) breaking token system

TARGET STATE:
- PostCSS nesting with & syntax
- 100% design tokens (zero hardcoded values)
- Proper cascade order (Base → Elements → Modifiers → States)

STEP 1: BACKUP & ANALYZE

Create backup:
cp {component}.css {component}.css.backup

Analyze current CSS:
- Count selectors: grep "^\." {component}.css | wc -l
- Find hardcoded colors: grep -E "#[0-9a-fA-F]{3,6}" {component}.css
- Find hardcoded sizes: grep -E "[0-9]+px|[0-9]+rem" {component}.css | grep -v "var(--"
- Find hardcoded durations: grep -E "[0-9]+ms|[0-9]+s" {component}.css | grep -v "var(--"

STEP 2: IDENTIFY NESTING OPPORTUNITIES

Map selectors to hierarchy:

FLAT (before):
.ps-component { }
.ps-component__element { }
.ps-component__element-inner { }
.ps-component--modifier { }
.ps-component--modifier .ps-component__element { }
.ps-component:hover { }
.ps-component:focus-visible { }

NESTED (after):
.ps-component {
  /* Base */
  
  &__element {
    /* Element */
    
    &-inner {
      /* Nested element */
    }
  }
  
  &--modifier {
    /* Modifier */
    
    .ps-component__element {
      /* Element in modifier context */
    }
  }
  
  &:hover { }
  &:focus-visible { }
}

STEP 3: CREATE TOKEN MAPPING

For each hardcoded value, find token replacement:

COLORS:
#00915A → var(--primary)
#A12B66 → var(--secondary)
#198754 → var(--success)
#EB3636 → var(--danger)
#FBBF24 → var(--warning)
#2563EB → var(--info)
#FFFFFF → var(--white)
#000000 → var(--black)
rgba(0,0,0,0.1) → var(--overlay-dark-light)

SIZES:
4px → var(--size-1)
8px → var(--size-2)
12px → var(--size-3)
16px → var(--size-4)
24px → var(--size-6)
32px → var(--size-8)

TYPOGRAPHY:
11px → var(--font-size-2)
13px → var(--font-size-3)
16px → var(--font-size-4)
19px → var(--font-size-5)
400 → var(--font-weight-normal)
600 → var(--font-weight-semibold)
700 → var(--font-weight-bold)

BORDERS:
4px → var(--radius-1)
8px → var(--radius-2)
16px → var(--radius-4)
1px solid #ccc → 1px solid var(--border-light)

SHADOWS:
0 1px 2px rgba(0,0,0,0.05) → var(--shadow-1)
0 4px 6px rgba(0,0,0,0.1) → var(--shadow-2)

DURATIONS:
100ms → var(--duration-instant)
200ms → var(--duration-fast)
300ms → var(--duration-base)

EASING:
ease → var(--ease-3)
ease-in-out → var(--ease-in-out)

STEP 4: RESTRUCTURE WITH NESTING

Apply nesting systematically:

BEFORE (flat, 30 lines):
.ps-button {
  display: inline-flex;
  padding: 12px 24px;
  background: #00915A;
  color: #FFFFFF;
  border-radius: 8px;
  transition: background 200ms ease;
}

.ps-button__icon {
  width: 16px;
  height: 16px;
}

.ps-button--small {
  padding: 8px 16px;
  font-size: 13px;
}

.ps-button:hover {
  background: #007A4D;
}

.ps-button:focus-visible {
  outline: 2px solid #00915A;
  outline-offset: 2px;
}

AFTER (nested + tokens, 35 lines):
.ps-button {
  /* Base */
  display: inline-flex;
  align-items: center;
  gap: var(--size-2);
  padding: var(--size-3) var(--size-6);
  background: var(--primary);
  color: var(--white);
  border: none;
  border-radius: var(--radius-2);
  font-size: var(--font-size-4);
  font-weight: var(--font-weight-semibold);
  transition: background var(--duration-fast) var(--ease-3);
  cursor: pointer;
  
  /* Elements */
  &__icon {
    width: var(--size-4);
    height: var(--size-4);
  }
  
  /* Modifiers */
  &--small {
    padding: var(--size-2) var(--size-4);
    font-size: var(--font-size-3);
  }
  
  /* States */
  &:hover:not(:disabled) {
    background: var(--primary-hover);
  }
  
  &:focus-visible {
    outline: 2px solid var(--border-focus);
    outline-offset: 2px;
  }
  
  &:disabled {
    opacity: 0.5;
    cursor: not-allowed;
  }
}

STEP 5: VERIFY CASCADE ORDER

Ensure correct order:

1. Base block styles
2. Elements (&__element)
3. Modifiers (&--modifier)
4. States (&:hover, &:focus-visible, &:disabled)

WRONG ORDER (causes specificity issues):
.ps-component {
  &--modifier { }  ❌ Modifier before elements
  &__element { }
  &:hover { }
}

CORRECT ORDER:
.ps-component {
  /* Base */
  
  &__element { }   ✅ Elements first
  
  &--modifier { }  ✅ Then modifiers
  
  &:hover { }      ✅ Then states
}

STEP 6: ADD COMPONENT-SCOPED VARIABLES

Add Layer 2 tokens for customization:

.ps-{component} {
  /* Layer 2: Component-scoped variables */
  --ps-{component}-padding: var(--size-4);
  --ps-{component}-gap: var(--size-3);
  --ps-{component}-bg: var(--white);
  --ps-{component}-color: var(--text-primary);
  --ps-{component}-border: var(--border-light);
  
  /* Use Layer 2 variables in styles */
  padding: var(--ps-{component}-padding);
  gap: var(--ps-{component}-gap);
  background: var(--ps-{component}-bg);
  color: var(--ps-{component}-color);
  border: 1px solid var(--ps-{component}-border);
}

STEP 7: VALIDATE

A. Check syntax:
npm run build
→ Must pass without CSS errors

B. Visual validation:
npm run watch
→ Check all variants render correctly
→ Verify no visual regressions

C. Verify token coverage:
grep -E "#[0-9a-fA-F]{3,6}|[0-9]+px|[0-9]+ms" {component}.css | grep -v "var(--"
→ Should return 0 results

D. Compare before/after:
diff {component}.css.backup {component}.css
→ Review all changes

STEP 8: UPDATE README

Document token usage:

## Design Tokens

### Colors
- Background: `--primary` / `--white`
- Text: `--text-primary` / `--white`
- Border: `--border-light`

### Spacing
- Padding: `--size-3` × `--size-6`
- Gap: `--size-2`

### Typography
- Font size: `--font-size-4`
- Font weight: `--font-weight-semibold`

### Other
- Border radius: `--radius-2`
- Transition: `--duration-fast` + `--ease-3`
- Shadow: `--shadow-2`

STEP 9: COMMIT

Format:
refactor({level}): Convert {component} CSS to nested with tokens

Migration: Flat CSS → PostCSS nesting + 100% design tokens

Changes:
- Add nesting with & syntax (improved maintainability)
- Replace ALL hardcoded values with design tokens:
  * Colors: {count} replacements (--primary, --success, etc.)
  * Sizes: {count} replacements (--size-*, --font-size-*)
  * Durations: {count} replacements (--duration-*)
  * Other: {count} replacements (--radius-*, --shadow-*)
- Fix cascade order (Base → Elements → Modifiers → States)
- Add component-scoped variables (Layer 2 customization)

Before: {X} hardcoded values
After: 0 hardcoded values ✅

Visual regression: None (verified in Storybook)
Build: Passes ✅

References: .github/instructions/05-maintenance.md (Legacy Pattern 1+2)

COMMON PITFALLS:

❌ Forgetting :not(:disabled) on hover
→ &:hover:not(:disabled) { }

❌ Wrong & placement
→ & must be inside parent selector

❌ Breaking modifier independence
→ Each modifier must work alone on base

❌ Missing fallback on token override
→ --ps-comp-color: var(--text-primary); not just var(--text);

SUCCESS CRITERIA:
✅ All selectors properly nested
✅ Zero hardcoded values (grep verified)
✅ Cascade order correct
✅ Build passes
✅ Visual regression: None
✅ README documents all tokens
```

---

**Estimated Time**: 30-60 minutes  
**Difficulty**: Medium  
**Prerequisites**: Basic understanding of PostCSS nesting and design tokens
