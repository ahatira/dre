---
title: Troubleshooting Guide
version: 1.0.0
lastUpdated: 2025-12-12
priority: HIGH
related:
  - workflows.instructions.md
  - TOKEN_CREATION_PROCESS.md
  - css.instructions.md
  - components.instructions.md
status: ACTIVE
---

# Troubleshooting Guide - PS Theme

**Scope**: Common errors, solutions, debugging commands, prevention strategies

---

## 📖 When to Use This Guide

**Use this guide when you encounter:**
- ✅ Build failures (`npm run build` errors)
- ✅ Token not found errors (CSS variables undefined)
- ✅ BEM naming violations (linting errors)
- ✅ Twig syntax errors (Drupal incompatibility)
- ✅ Storybook rendering issues (missing stories, broken Autodocs)
- ✅ Focus-visible missing (accessibility violations)
- ✅ Git commit blocked (pre-commit hooks failing)

**This guide provides:**
- Error messages with explanations
- Step-by-step solutions
- Debug commands (grep, file checks)
- Prevention strategies
- Quick fixes

---

## 🚨 Build Failures

### Error 1: Token Not Found

**Error Message**:
```bash
ERROR: CSS variable '--primary-dark' is not defined
  → source/patterns/elements/button/button.css:23:18
```

**Cause**: Using a token that doesn't exist in `source/props/*.css`

**Solution**:
```bash
# Step 1: Search for the token across all props files
grep -r "--primary-dark" source/props/

# Step 2: Check if token exists with different name
grep -r "primary.*dark" source/props/

# Step 3: Verify available variants
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
/* button.css - BEFORE (wrong token) */
.ps-button--primary:hover {
  background: var(--primary-dark);  /* ❌ Token doesn't exist */
}

/* button.css - AFTER (correct token) */
.ps-button--primary:hover {
  background: var(--primary-hover);  /* ✅ Correct token */
}
```

**Prevention**:
- Always verify tokens exist before using: `npm run tokens:check -- --token-name`
- Follow token naming conventions: `--{color}-{state}` (hover, active, subtle, etc.)
- Never create tokens on-the-fly (see: `TOKEN_CREATION_PROCESS.md`)

---

### Error 2: Hardcoded Value Detected

**Error Message**:
```bash
Build Warning: Hardcoded color value '#00915A' detected
  → source/patterns/elements/badge/badge.css:12:15
```

**Cause**: Using raw hex/rgb values instead of design tokens

**Solution**:
```bash
# Step 1: Find all hardcoded colors in component
grep -E "#[0-9A-Fa-f]{6}|rgb\(|hsl\(" source/patterns/elements/badge/badge.css

# Step 2: Find matching token
grep -r "#00915A" source/props/
# Output: source/props/colors.css:  --green-600: hsl(162, 72%, 38%);
#         source/props/brand.css:    --primary: var(--green-600);

# Step 3: Check semantic token
grep -r "primary" source/props/brand.css
```

**Fix**:
```css
/* badge.css - BEFORE (hardcoded) */
.ps-badge--primary {
  background: #00915A;  /* ❌ Hardcoded hex */
  color: #FFFFFF;       /* ❌ Hardcoded hex */
}

/* badge.css - AFTER (tokens) */
.ps-badge--primary {
  background: var(--primary);  /* ✅ Semantic token */
  color: var(--white);         /* ✅ Global token */
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
| `0 2px 4px rgba(0,0,0,0.1)` | `var(--shadow-2)` | `shadows.css` |

**Prevention**:
- Use tokens for ALL values (no magic numbers)
- Run `npm run build` before committing (catches hardcoded values)
- Follow CSS standards: `.github/instructions/css.instructions.md`

---

### Error 3: CSS Nesting Syntax Error

**Error Message**:
```bash
ERROR: Unexpected selector '.ps-button__icon'
  → source/patterns/elements/button/button.css:15:3
Expected nested selector with '&'
```

**Cause**: Using flat CSS selectors instead of PostCSS nesting with `&`

**Solution**:
```bash
# Check if component uses nesting
grep -n "^\\.[a-z]" source/patterns/elements/button/button.css

# Should see '&' for nested selectors
grep -n "&" source/patterns/elements/button/button.css
```

**Fix**:
```css
/* button.css - BEFORE (flat CSS - forbidden for new components) */
.ps-button {
  display: flex;
}

.ps-button__icon {  /* ❌ Flat selector */
  margin-right: var(--size-2);
}

.ps-button--primary {  /* ❌ Flat selector */
  background: var(--primary);
}

/* button.css - AFTER (nested with &) */
.ps-button {
  display: flex;
  
  &__icon {  /* ✅ Nested element */
    margin-right: var(--size-2);
  }
  
  &--primary {  /* ✅ Nested modifier */
    background: var(--primary);
  }
}
```

**Prevention**:
- ALL new components MUST use CSS nesting with `&`
- Follow CSS order: Base → Elements → Modifiers → States
- See: `.github/instructions/css.instructions.md` (Section: CSS Nesting)

---

### Error 4: Biome Lint Errors

**Error Message**:
```bash
ERROR: Unused variable 'icon'
  → source/patterns/elements/button/button.stories.jsx:12:7
```

**Cause**: JavaScript variable declared but never used

**Solution**:
```bash
# Run Biome with auto-fix
npm run lint:fix

# Check specific file
npx biome check --write source/patterns/elements/button/button.stories.jsx
```

**Fix**:
```jsx
// button.stories.jsx - BEFORE
import buttonTwig from './button.twig';
import data from './button.yml';
import icon from './icon.svg';  // ❌ Imported but not used

export default {
  title: 'Elements/Button',
};

// button.stories.jsx - AFTER (remove unused import)
import buttonTwig from './button.twig';
import data from './button.yml';
// ✅ Removed unused import

export default {
  title: 'Elements/Button',
};
```

**Common Biome Errors**:
| Error | Fix |
|-------|-----|
| Unused variable | Remove variable or use it |
| Missing semicolon | Add `;` at end of statement |
| Prefer `const` over `let` | Use `const` for values that don't change |
| Unnecessary template literal | Use plain string: `'text'` not `` `text` `` |
| Dangling comma | Add comma after last item (consistency) |

**Prevention**:
- Run `npm run lint:check` before committing
- Use VS Code Biome extension (real-time linting)
- Fix errors as you code (don't accumulate tech debt)

---

## 🧩 Twig Template Errors

### Error 5: Arrow Function in Twig (Drupal Incompatible)

**Error Message**:
```bash
ERROR: Syntax error in template
  → source/patterns/elements/badge/badge.twig:10
Unexpected '=>' (arrow function not supported in Drupal Twig)
```

**Cause**: Using JavaScript arrow functions in Twig (Drupal Twig doesn't support ES6 syntax)

**Solution**:
```bash
# Find arrow functions in Twig files
grep -r "=>" source/patterns/ --include="*.twig"

# Should return NO results (all should use ternary)
```

**Fix**:
```twig
{# badge.twig - BEFORE (arrow function - ❌ FORBIDDEN) #}
{%- set classes = [
  'ps-badge',
  size,
  variant
]|filter(v => v) -%}  {# ❌ Arrow function NOT Drupal-compatible #}

{# badge.twig - AFTER (ternary with null) #}
{%- set classes = [
  'ps-badge',
  size != 'md' ? 'ps-badge--' ~ size : null,
  variant != 'default' ? 'ps-badge--' ~ variant : null
] -%}  {# ✅ Twig's join() automatically skips null values #}
```

**Why This Works**:
- Twig's `join()` filter automatically skips `null` values
- No need for `.filter()` method (which requires arrow function)
- 100% Drupal-compatible

**Prevention**:
- NEVER use arrow functions in Twig: `v => v`, `i => i.name`
- Use ternary operator: `condition ? 'value' : null`
- Follow Twig standards: `.github/instructions/templates.instructions.md`

---

### Error 6: JavaScript Methods in Twig

**Error Message**:
```bash
ERROR: Unknown method 'map' in template
  → source/patterns/components/card/card.twig:25
```

**Cause**: Using JavaScript array methods (`.map()`, `.filter()`, `.includes()`) in Twig

**Solution**:
```bash
# Find JavaScript methods in Twig
grep -rE "\\.map\\(|\\.filter\\(|\\.includes\\(|\\.reduce\\(" source/patterns/ --include="*.twig"
```

**Fix**:
```twig
{# card.twig - BEFORE (JS methods - ❌ FORBIDDEN) #}
{%- set badges = items.map(i => i.badge) -%}  {# ❌ .map() not supported #}
{%- set filtered = items.filter(i => i.active) -%}  {# ❌ .filter() not supported #}

{# card.twig - AFTER (Twig loops) #}
{%- set badges = [] -%}
{% for item in items %}
  {%- set badges = badges|merge([item.badge]) -%}
{% endfor %}

{# OR use conditional in loop #}
{% for item in items %}
  {% if item.active %}
    {# Render active item #}
  {% endif %}
{% endfor %}
```

**Drupal Twig Limitations**:
- ❌ NO `.map()`, `.filter()`, `.reduce()`
- ❌ NO arrow functions (`=>`)
- ❌ NO spread operator (`...`)
- ✅ USE Twig native: `for`, `if`, `merge`, `slice`

**Prevention**:
- Stick to Twig native syntax
- Use `for` loops with conditionals
- See: `.github/instructions/templates.instructions.md` (Drupal Compatibility)

---

### Error 7: Missing `only` Keyword in Include

**Error Message**:
```bash
WARNING: Variable pollution detected in included template
  → source/patterns/components/card/card.twig:18
Included template has access to parent scope variables
```

**Cause**: Using `{% include %}` without `only` keyword (variables leak from parent)

**Solution**:
```bash
# Find includes without 'only'
grep -rn "{% include" source/patterns/ --include="*.twig" | grep -v "only"
```

**Fix**:
```twig
{# card.twig - BEFORE (risky - variables leak) #}
{% include '@elements/button/button.twig' with {
  text: cta_text,
  color: 'primary'
} %}  {# ❌ Missing 'only' - button has access to ALL card variables #}

{# card.twig - AFTER (safe - isolated scope) #}
{% include '@elements/button/button.twig' with {
  text: cta_text,
  color: 'primary'
} only %}  {# ✅ Button only receives specified props #}
```

**Why `only` is CRITICAL**:
- Prevents variable pollution (child can't access parent scope)
- Makes dependencies explicit (clear prop passing)
- Avoids naming conflicts (parent/child vars with same name)
- Improves maintainability (visible prop contract)

**Prevention**:
- ALWAYS use `only` with `{% include %}`
- Make it a habit: `} only %}` (2 words, always together)

---

## 📚 Storybook Issues

### Error 8: Missing `tags: ['autodocs']`

**Error Message**:
```bash
WARNING: Component Docs page is empty
  → Badge component has no Autodocs
Check: export default settings missing tags: ['autodocs']
```

**Cause**: Storybook export missing `tags: ['autodocs']` (Autodocs not generated)

**Solution**:
```bash
# Find stories without autodocs
grep -L "tags.*autodocs" source/patterns/elements/*/\*.stories.jsx

# Verify specific file
grep "tags" source/patterns/elements/badge/badge.stories.jsx
```

**Fix**:
```jsx
// badge.stories.jsx - BEFORE (no autodocs)
export default {
  title: 'Elements/Badge',
  render: (args) => badgeTwig(args),
  args: data,
  // ❌ Missing tags!
};

// badge.stories.jsx - AFTER (with autodocs)
export default {
  title: 'Elements/Badge',
  tags: ['autodocs'],  // ✅ MANDATORY for all component stories
  render: (args) => badgeTwig(args),
  args: data,
};
```

**Exception**: Base stories (`source/patterns/base/*`) don't use Autodocs:
```jsx
// base/colors/colors.stories.jsx - NO autodocs for base stories
export default {
  title: 'Base/Colors',
  // ✅ NO tags - base stories document tokens, not components
};
```

**Prevention**:
- ALL component stories REQUIRE `tags: ['autodocs']`
- Exception: `base/*` directory (token documentation)
- See: `.github/instructions/storybook.instructions.md`

---

### Error 9: React/JSX in Storybook (Wrong Edition)

**Error Message**:
```bash
ERROR: Unexpected token '<'
  → source/patterns/elements/button/button.stories.jsx:8:10
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
// button.stories.jsx - BEFORE (React - ❌ WRONG)
import React from 'react';

export const Default = () => (
  <button className="ps-button">Click me</button>
);  // ❌ JSX not supported

// button.stories.jsx - AFTER (HTML edition - ✅ CORRECT)
import buttonTwig from './button.twig';
import data from './button.yml';

export const Default = {
  render: (args) => buttonTwig(args),  // ✅ Twig renderer
  args: data,
};
```

**PS Theme Storybook Edition**: HTML/Vite (NOT React)
- ✅ Import Twig templates: `import componentTwig from './component.twig'`
- ✅ Use `render: (args) => componentTwig(args)`
- ❌ NO React imports
- ❌ NO JSX syntax

**Prevention**:
- Follow Storybook HTML edition patterns
- Use Twig for all templates
- See: `.github/instructions/storybook.instructions.md`

---

### Error 10: Wrong Import Path (Aliases Not Working)

**Error Message**:
```bash
ERROR: Cannot resolve '@patterns/documentation/colors-list.json'
  → source/patterns/elements/badge/badge.stories.jsx:3:28
```

**Cause**: Using path aliases (`@patterns`) instead of relative paths

**Solution**:
```bash
# Verify file structure
ls source/patterns/documentation/

# Check import from current location
# From: source/patterns/elements/badge/badge.stories.jsx
# To:   source/patterns/documentation/colors-list.json
# Path: ../../documentation/colors-list.json
```

**Fix**:
```jsx
// badge.stories.jsx - BEFORE (alias - ❌ NOT WORKING)
import colorsList from '@patterns/documentation/colors-list.json';  // ❌

// badge.stories.jsx - AFTER (relative path - ✅ CORRECT)
import colorsList from '../../documentation/colors-list.json';  // ✅
```

**Path Calculation**:
```
From: source/patterns/elements/badge/badge.stories.jsx
      source/patterns/elements/badge/ (current directory)
                    ↑ up one: elements/
                    ↑ up two: patterns/
                    ↓ into:   documentation/
To:   source/patterns/documentation/colors-list.json

Result: ../../documentation/colors-list.json
```

**Prevention**:
- ALWAYS use relative paths for Storybook imports
- Calculate: Count levels up (`../`) then down to target
- Don't rely on path aliases (not configured in this project)

---

## ♿ Accessibility Violations

### Error 11: Missing Focus-Visible

**Error Message**:
```bash
Accessibility Audit: focus-visible missing on interactive element
  → source/patterns/elements/button/button.css
All interactive elements MUST have visible focus indicator (WCAG 2.2 AA)
```

**Cause**: Interactive element (button, link, input) missing `:focus-visible` styles

**Solution**:
```bash
# Check if focus-visible exists in CSS
grep -n "focus-visible" source/patterns/elements/button/button.css

# Should find at least one match
# If no results, focus-visible is MISSING
```

**Fix**:
```css
/* button.css - BEFORE (missing focus-visible) */
.ps-button {
  display: inline-flex;
  
  &:hover {
    background: var(--primary-hover);
  }
  
  &:active {
    transform: translateY(0);
  }
  
  /* ❌ MISSING :focus-visible! */
}

/* button.css - AFTER (with focus-visible) */
.ps-button {
  display: inline-flex;
  
  &:hover {
    background: var(--primary-hover);
  }
  
  &:focus-visible {  /* ✅ MANDATORY for all interactives */
    outline: var(--border-size-2) solid var(--secondary);
    outline-offset: var(--border-size-2);
  }
  
  &:active {
    transform: translateY(0);
  }
}
```

**Focus-Visible Requirements**:
- **Mandatory for**: buttons, links, inputs, selects, textareas, custom controls
- **Contrast**: Minimum 3:1 (WCAG AA UI component contrast)
- **Visibility**: Must be clearly visible (not subtle)
- **Token**: Use `var(--secondary)` or `var(--border-focus)`

**Prevention**:
- Add `:focus-visible` when creating interactive components
- Test with keyboard navigation (Tab key)
- Run accessibility audit in Storybook (a11y addon)
- See: `.github/instructions/accessibility.instructions.md`

---

### Error 12: Low Contrast Ratio

**Error Message**:
```bash
Accessibility Audit: contrast ratio 2.8:1 fails WCAG AA
  → Text color var(--gray-400) on var(--white) background
Required: 4.5:1 for normal text
```

**Cause**: Text color doesn't meet WCAG 2.2 AA contrast requirements

**Solution**:
```bash
# Check text colors in component
grep -n "color:" source/patterns/elements/text/text.css

# Verify tokens used
grep -n "--gray-" source/patterns/elements/text/text.css

# Check token values
grep -r "--gray-400" source/props/colors.css
```

**Fix**:
```css
/* text.css - BEFORE (low contrast) */
.ps-text--muted {
  color: var(--gray-400);  /* ❌ Only 2.8:1 on white - FAILS AA */
}

/* text.css - AFTER (sufficient contrast) */
.ps-text--muted {
  color: var(--gray-600);  /* ✅ 4.6:1 on white - PASSES AA */
}
```

**WCAG 2.2 AA Contrast Requirements**:
| Content | Minimum Ratio | Examples |
|---------|---------------|----------|
| Normal text (<18px) | **4.5:1** | Body text, labels, descriptions |
| Large text (≥18px or ≥14px bold) | **3:1** | Headings, buttons, large UI text |
| UI components | **3:1** | Borders, icons, focus indicators |

**Common Token Contrasts** (on white background):
| Token | Contrast | AA Pass? |
|-------|----------|----------|
| `--gray-900` | 14.8:1 | ✅ Yes (normal + large) |
| `--gray-700` | 7.2:1 | ✅ Yes (normal + large) |
| `--gray-600` | 4.6:1 | ✅ Yes (normal + large) |
| `--gray-500` | 3.1:1 | ⚠️ Large text only |
| `--gray-400` | 2.8:1 | ❌ Fails AA |

**Prevention**:
- Use `--gray-600` or darker for normal text
- Use `--gray-500` minimum for large text (≥18px)
- Test with browser DevTools contrast checker
- See: `.github/instructions/accessibility.instructions.md`

---

## 🏗️ BEM Naming Violations

### Error 13: Missing `ps-` Prefix

**Error Message**:
```bash
BEM Violation: Class '.button' missing 'ps-' prefix
  → source/patterns/elements/button/button.twig:12
All component classes MUST use 'ps-' prefix
```

**Cause**: CSS class without required `ps-` prefix

**Solution**:
```bash
# Find classes without ps- prefix
grep -rn "class=\"[^p][^s]-" source/patterns/ --include="*.twig"

# More thorough check (classes not starting with ps-)
grep -rE "class=\"[^\"]*\\b[a-z][a-z-]+__" source/patterns/ --include="*.twig" | grep -v "ps-"
```

**Fix**:
```twig
{# button.twig - BEFORE (missing prefix) #}
<button class="button button--primary">  {# ❌ No ps- prefix #}
  <span class="button__icon">...</span>  {# ❌ No ps- prefix #}
</button>

{# button.twig - AFTER (with prefix) #}
<button class="ps-button ps-button--primary">  {# ✅ ps- prefix #}
  <span class="ps-button__icon">...</span>      {# ✅ ps- prefix #}
</button>
```

**BEM Naming Rules**:
- ✅ ALL classes MUST start with `ps-`
- ✅ Format: `.ps-{block}`, `.ps-{block}__{element}`, `.ps-{block}--{modifier}`
- ❌ NO generic classes: `.button`, `.card`, `.icon`

**Prevention**:
- Use component generator: `npm run generate:pattern` (auto-adds prefix)
- Follow BEM standards: `.github/instructions/components.instructions.md`

---

### Error 14: Double Underscore in BEM

**Error Message**:
```bash
BEM Violation: '.ps-card__header__title' has double underscore
  → source/patterns/components/card/card.css:25
Maximum depth: .ps-block__element (one level only)
```

**Cause**: Nested BEM element (double `__`)

**Solution**:
```bash
# Find double underscores
grep -rn "__.*__" source/patterns/ --include="*.css" --include="*.twig"
```

**Fix**:
```css
/* card.css - BEFORE (double __ - ❌ FORBIDDEN) */
.ps-card {
  &__header {
    &__title {  /* ❌ .ps-card__header__title (double __) */
      font-size: var(--font-size-4);
    }
  }
}

/* card.css - AFTER (flat BEM - ✅ CORRECT) */
.ps-card {
  &__header {
    font-size: var(--font-size-3);
  }
  
  &__title {  /* ✅ .ps-card__title (direct child of block) */
    font-size: var(--font-size-4);
  }
}
```

**BEM Structure Rules**:
- ✅ One level: `.ps-block__element`
- ❌ NO nesting: `.ps-block__element__sub-element`
- ✅ Flat hierarchy: All elements are direct children of block

**HTML Structure**:
```html
<!-- Correct BEM mapping -->
<div class="ps-card">
  <div class="ps-card__header">
    <h3 class="ps-card__title">Title</h3>  <!-- NOT __header__title -->
  </div>
</div>
```

**Prevention**:
- Keep BEM flat (max 1 `__`)
- Rethink structure if you need nesting
- See: `.github/instructions/components.instructions.md` (BEM section)

---

## 🔧 Git & Pre-Commit Hooks

### Error 15: Pre-Commit Hook Failed

**Error Message**:
```bash
husky > pre-commit hook failed (add --no-verify to skip)

✖ Validation failed:
  - Biome lint errors: 3
  - Biome format errors: 5
  
Run 'npm run lint:fix' and 'npm run format:write' to fix
```

**Cause**: Code doesn't pass lint/format checks (Biome)

**Solution**:
```bash
# Step 1: See what's failing
npm run lint:check
npm run format:check

# Step 2: Auto-fix what's possible
npm run lint:fix
npm run format:write

# Step 3: Manually fix remaining issues
# (Review Biome output for errors that can't be auto-fixed)

# Step 4: Try commit again
git add .
git commit -m "fix: resolve lint errors"
```

**Common Pre-Commit Failures**:
| Issue | Check | Fix |
|-------|-------|-----|
| Lint errors | `npm run lint:check` | `npm run lint:fix` |
| Format errors | `npm run format:check` | `npm run format:write` |
| Build failure | `npm run build` | Fix errors, rebuild |
| Icon build error | `npm run icons:build` | Check SVG files in `source/icons-source/` |

**Skip Pre-Commit** (NOT recommended):
```bash
# Only use if absolutely necessary (emergency hotfix)
git commit --no-verify -m "hotfix: critical bug"
```

**Prevention**:
- Run `npm run build` before committing
- Use VS Code extensions (Biome, Prettier)
- Fix errors incrementally (don't accumulate)

---

## 🔍 Debug Commands Reference

### Token Search

```bash
# Find token definition
grep -r "--primary" source/props/

# Find token usage
grep -r "var(--primary)" source/patterns/

# Check all tokens in category
grep -r "--size-" source/props/sizes.css

# Find hardcoded values
grep -rE "#[0-9A-Fa-f]{6}|rgb\(|hsl\(" source/patterns/elements/badge/
```

### File Structure Validation

```bash
# Check 5-file structure
component="badge"
for file in twig css yml stories.jsx README.md; do
  [ -f "source/patterns/elements/$component/$component.$file" ] && echo "✅ $file" || echo "❌ $file MISSING"
done

# Find components missing files
for dir in source/patterns/elements/*/; do
  component=$(basename $dir)
  [ ! -f "$dir/$component.stories.jsx" ] && echo "Missing stories: $component"
done
```

### BEM Validation

```bash
# Find classes without ps- prefix
grep -rE "class=\"[^\"]*\\b[a-z][a-z-]+(__|--).*\"" source/patterns/ --include="*.twig" | grep -v "ps-"

# Find double underscores
grep -rn "__.*__" source/patterns/ --include="*.css"

# Check cascade order (modifiers before base)
grep -n "^\\s*&--" source/patterns/elements/button/button.css
```

### Accessibility Checks

```bash
# Find missing focus-visible
for file in source/patterns/elements/*/\*.css; do
  grep -q "focus-visible" "$file" || echo "Missing focus-visible: $file"
done

# Check contrast ratios (manual - use DevTools)
# Search for --gray-400 or lighter on light backgrounds
grep -rn "color: var(--gray-[0-4][0-9][0-9])" source/patterns/
```

### Storybook Validation

```bash
# Find stories without autodocs
grep -L "tags.*autodocs" source/patterns/elements/*/\*.stories.jsx

# Find React imports (wrong edition)
grep -rn "import React" source/patterns/ --include="*.stories.jsx"

# Find alias imports (should use relative)
grep -rn "from '@patterns" source/patterns/ --include="*.stories.jsx"
```

---

## 🎯 Quick Fixes Checklist

Before asking for help, try these quick fixes:

- [ ] **Build failing?**
  ```bash
  npm run lint:fix && npm run format:write && npm run build
  ```

- [ ] **Token not found?**
  ```bash
  npm run tokens:check -- --token-name
  ```

- [ ] **Twig syntax error?**
  - Remove arrow functions (`=>`)
  - Remove JS methods (`.map()`, `.filter()`)
  - Add `only` to all `{% include %}`

- [ ] **Storybook blank?**
  - Add `tags: ['autodocs']` to export default
  - Check import path (use relative, not alias)
  - Verify Twig import: `import componentTwig from './component.twig'`

- [ ] **Focus-visible missing?**
  ```css
  &:focus-visible {
    outline: var(--border-size-2) solid var(--secondary);
    outline-offset: var(--border-size-2);
  }
  ```

- [ ] **Low contrast?**
  - Use `--gray-600` or darker for normal text
  - Use `--gray-500` minimum for large text (≥18px)

- [ ] **BEM violation?**
  - Add `ps-` prefix to all classes
  - Remove double underscores (`__element__sub`)
  - Make BEM flat (one level only)

- [ ] **Pre-commit hook failing?**
  ```bash
  npm run lint:fix && npm run format:write && git add . && git commit -m "fix: resolve errors"
  ```

---

## 📚 Related Documentation

- **Build System**: `.github/instructions/workflows.instructions.md` (Step 10: Build Validation)
- **Token System**: `.github/instructions/TOKEN_CREATION_PROCESS.md` (Token verification)
- **CSS Standards**: `.github/instructions/css.instructions.md` (Tokens, nesting, cascade)
- **Twig Standards**: `.github/instructions/templates.instructions.md` (Drupal compatibility)
- **Storybook Format**: `.github/instructions/storybook.instructions.md` (HTML edition)
- **Accessibility**: `.github/instructions/accessibility.instructions.md` (WCAG 2.2 AA)
- **BEM Naming**: `.github/instructions/components.instructions.md` (BEM methodology)

---

## 🆘 Still Stuck?

If you've tried the above and still have issues:

1. **Check build output carefully** - Error messages contain file paths and line numbers
2. **Search existing components** - Find similar working examples in `source/patterns/`
3. **Run debug commands** - Use grep searches to find patterns
4. **Verify file structure** - Ensure all 5 required files exist
5. **Test incrementally** - Isolate the problem (comment out code sections)

**Common patterns to search for working examples**:
```bash
# Find components with similar features
grep -r "focus-visible" source/patterns/elements/*/\*.css  # Focus examples
grep -r "tags.*autodocs" source/patterns/elements/*/\*.stories.jsx  # Autodocs examples
grep -r "{% include.*only" source/patterns/components/*/\*.twig  # Include examples
```

---

**Last Updated**: 2025-12-12  
**Maintainers**: Design System Team
