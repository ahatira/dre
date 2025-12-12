# Prompt: Find and Fix Component Issues

**Purpose**: Systematically identify and resolve common component issues.

---

## 📋 Prompt Template

```
Find and fix issues in: {COMPONENT_NAME}
Location: source/patterns/{level}/{component}/

OBJECTIVE: Identify ALL issues using automated checks + manual inspection

SEARCH STRATEGY:

1. Automated grep/search patterns
2. Visual inspection (Storybook)
3. Build validation
4. Conformity audit

---

## ISSUE CATEGORY 1: HARDCODED VALUES

**Detection**:
# Colors
grep -E "#[0-9a-fA-F]{3,6}" source/patterns/{level}/{component}/{component}.css

# Sizes
grep -E "[^-][0-9]+px|[0-9]+rem|[0-9]+em" source/patterns/{level}/{component}/{component}.css | grep -v "var(--"

# Durations
grep -E "[0-9]+ms|[0-9\.]+s" source/patterns/{level}/{component}/{component}.css | grep -v "var(--"

**Expected**: 0 results ✅

**If found**:
→ Map each value to token (see source/props/README.md)
→ Replace systematically
→ Verify visually (npm run watch)

**Example fix**:
BEFORE: background: #00915A;
AFTER:  background: var(--primary);

BEFORE: padding: 12px 24px;
AFTER:  padding: var(--size-3) var(--size-6);

BEFORE: transition: 200ms ease;
AFTER:  transition: var(--duration-fast) var(--ease-3);

---

## ISSUE CATEGORY 2: DRUPAL INCOMPATIBILITIES

**Detection**:
# Arrow functions
grep -E "=>\|filter\(" source/patterns/{level}/{component}/{component}.twig

# Array methods
grep -E "\.map\(|\.filter\(|\.includes\(|\.find\(|\.reduce\(" source/patterns/{level}/{component}/{component}.twig

**Expected**: 0 results ✅

**If found**:
→ Replace with Twig ternary + null
→ Replace with Twig for loops
→ Test in Drupal environment

**Example fix**:
BEFORE: {{ classes|filter(v => v)|join(' ') }}
AFTER:  {{ classes|join(' ') }}  {# Twig auto-filters null #}

BEFORE: {% set validItems = items.filter(item => item.visible) %}
AFTER:  {% for item in items %}
          {% if item.visible %}
            {# Use item #}
          {% endif %}
        {% endfor %}

---

## ISSUE CATEGORY 3: MISSING AUTODOCS

**Detection**:
grep -L "tags: \['autodocs'\]" source/patterns/{level}/{component}/{component}.stories.jsx

**Expected**: 0 results ✅

**If found**:
→ Add tags: ['autodocs'] to export default
→ Rebuild Storybook
→ Verify Docs tab appears

**Example fix**:
BEFORE:
export default {
  title: 'Elements/Component',
  // Missing tags
};

AFTER:
export default {
  title: 'Elements/Component',
  tags: ['autodocs'],  // ✅ Added
};

---

## ISSUE CATEGORY 4: FLAT CSS

**Detection**:
# Count top-level selectors (should be 1)
grep -c "^\.[a-z]" source/patterns/{level}/{component}/{component}.css

# Check for & nesting
grep -c "&" source/patterns/{level}/{component}/{component}.css

**Expected**: 
- Top-level selectors: 1
- Nesting (&): >5 (depends on complexity)

**If low nesting count**:
→ Component likely flat CSS (Legacy Pattern 1)
→ Refactor using refactor-css.md prompt

**Visual check**:
FLAT (bad):
.ps-component { }
.ps-component__element { }
.ps-component__element-inner { }
.ps-component--modifier { }

NESTED (good):
.ps-component {
  &__element {
    &-inner { }
  }
  &--modifier { }
}

---

## ISSUE CATEGORY 5: WRONG CASCADE ORDER

**Detection**:
Manual inspection of CSS file order

**Check order**:
1. Base styles (root .ps-component)
2. Elements (&__element)
3. Modifiers (&--modifier)
4. States (&:hover, &:focus-visible)

**If wrong order**:
→ Reorder blocks
→ Verify no visual regressions

**Example fix**:
BEFORE (wrong order):
.ps-component {
  &--modifier { }  ❌ Modifier before element
  &__element { }
  &:hover { }
}

AFTER (correct order):
.ps-component {
  /* Base */
  
  &__element { }   ✅ Elements first
  &--modifier { }  ✅ Modifiers second
  &:hover { }      ✅ States last
}

---

## ISSUE CATEGORY 6: MISSING FOCUS INDICATORS

**Detection**:
# Check :focus-visible count
grep -c "focus-visible" source/patterns/{level}/{component}/{component}.css

# List interactive elements
grep -E "button|a href|input|select|textarea" source/patterns/{level}/{component}/{component}.twig

**Expected**: Each interactive element has :focus-visible style

**If missing**:
→ Add :focus-visible to each interactive element
→ Use standard pattern:
  &:focus-visible {
    outline: 2px solid var(--border-focus);
    outline-offset: 2px;
  }

---

## ISSUE CATEGORY 7: SEMANTIC COLORS

**Detection**:
# Check for palette colors (should use semantic)
grep -E "var\(--green|--red|--blue|--yellow|--pink|--teal" source/patterns/{level}/{component}/{component}.css

**Expected**: 0 results ✅ (use semantic: --primary, --danger, --success, etc.)

**If found**:
→ Map palette → semantic
  --green-600 → --primary or --success
  --red-600 → --danger
  --blue-600 → --info
  --yellow-500 → --warning
  --pink-600 → --secondary

**Example fix**:
BEFORE: background: var(--green-600);
AFTER:  background: var(--primary);  /* or --success depending on context */

---

## ISSUE CATEGORY 8: BEM VIOLATIONS

**Detection**:
# Check BEM format
grep -E "\.[a-z]+-[a-z]+__[a-z]+__|\.[a-z]+--[a-z]+--" source/patterns/{level}/{component}/{component}.css

**BEM Rules**:
✅ .ps-component
✅ .ps-component__element
✅ .ps-component__element-sub
✅ .ps-component--modifier
❌ .ps-component__element__nested (double underscore)
❌ .ps-component--modifier--variant (double dash)
❌ .psComponent (camelCase)

**If violations**:
→ Flatten nesting: __element__nested → __element-nested
→ Single modifier: --modifier--variant → --modifier-variant
→ Lowercase + hyphens only

---

## ISSUE CATEGORY 9: MISSING README SECTIONS

**Detection**:
grep -L "## Usage\|## Design Tokens\|## Accessibility" source/patterns/{level}/{component}/README.md

**Required sections**:
1. Component description
2. Usage
3. Props
4. BEM Structure
5. Design Tokens
6. Accessibility
7. Examples

**If missing**:
→ Add section with appropriate content
→ See reference components: button, badge, avatar

---

## ISSUE CATEGORY 10: WRONG ICON PREFIX

**Detection**:
grep -E "icon-[a-z]+" source/patterns/{level}/{component}/{component}.twig

**Expected**: 0 results ✅ (NO icon- prefix in code)

**If found**:
→ Remove icon- prefix from data-icon attributes
→ Icon names only: "check" not "icon-check"

**Example fix**:
BEFORE: <span data-icon="icon-search"></span>
AFTER:  <span data-icon="search"></span>

BEFORE: {% include '@elements/icon/icon.twig' with { icon: 'icon-close' } %}
AFTER:  {% include '@elements/icon/icon.twig' with { icon: 'close' } %}

---

## COMPREHENSIVE CHECK SCRIPT

Run all checks at once:

```bash
# 1. Hardcoded colors
echo "=== Hardcoded Colors ==="
grep -Hn -E "#[0-9a-fA-F]{3,6}" source/patterns/{level}/{component}/*.css

# 2. Hardcoded sizes
echo "=== Hardcoded Sizes ==="
grep -Hn -E "[^-][0-9]+px|[0-9]+rem" source/patterns/{level}/{component}/*.css | grep -v "var(--"

# 3. Arrow functions
echo "=== Arrow Functions ==="
grep -Hn -E "=>|\.filter\(|\.map\(" source/patterns/{level}/{component}/*.twig

# 4. Missing autodocs
echo "=== Missing Autodocs ==="
grep -L "tags: \['autodocs'\]" source/patterns/{level}/{component}/*.stories.jsx

# 5. Flat CSS (low nesting)
echo "=== Nesting Check ==="
echo "Top-level selectors:" $(grep -c "^\.[a-z]" source/patterns/{level}/{component}/*.css)
echo "Nesting instances:" $(grep -c "&" source/patterns/{level}/{component}/*.css)

# 6. Missing focus-visible
echo "=== Focus Indicators ==="
grep -Hn "focus-visible" source/patterns/{level}/{component}/*.css

# 7. Semantic colors
echo "=== Palette Colors (should be semantic) ==="
grep -Hn -E "var\(--green|--red|--blue|--yellow" source/patterns/{level}/{component}/*.css

# 8. Wrong icon prefix
echo "=== Icon Prefix ==="
grep -Hn "icon-[a-z]" source/patterns/{level}/{component}/*.twig

# 9. Build validation
echo "=== Build Check ==="
npm run build

# 10. Conformity audit
echo "=== Conformity Audit ==="
# Use audit-component.md prompt
```

---

## VISUAL INSPECTION CHECKLIST

Open in Storybook: http://localhost:6006

1. Default Story
   - [ ] Renders without errors
   - [ ] All controls work
   - [ ] Props update component

2. Variants Showcase
   - [ ] All variants visible
   - [ ] Visual differences clear
   - [ ] No layout breaks

3. Responsive
   - [ ] Mobile (375px): Stacks/adjusts correctly
   - [ ] Tablet (768px): Layout appropriate
   - [ ] Desktop (1280px): Full design

4. States
   - [ ] Hover: Visual feedback clear
   - [ ] Focus: Visible 2px outline
   - [ ] Active: Distinct from hover
   - [ ] Disabled: Clearly disabled
   - [ ] Loading: Indicator visible

5. Dark Mode (if supported)
   - [ ] Text readable
   - [ ] Contrast maintained
   - [ ] Borders visible

6. Browser Compatibility
   - [ ] Chrome: Works
   - [ ] Firefox: Works
   - [ ] Safari: Works (especially backdrop-filter, gap)

---

## ISSUE PRIORITIZATION

**CRITICAL (P0) - Fix immediately**:
- Hardcoded values (breaks token system)
- Arrow functions (breaks Drupal)
- Missing files (incomplete)
- Build errors (blocks deployment)

**IMPORTANT (P1) - Fix soon**:
- Missing autodocs (poor docs)
- Flat CSS (hard to maintain)
- Missing focus-visible (accessibility)
- Wrong semantic colors (inconsistent)
- BEM violations (confusing)

**NICE TO HAVE (P2) - Improve later**:
- Missing README sections (incomplete docs)
- Additional showcases (better examples)
- Enhanced responsive (better UX)

---

## RESOLUTION WORKFLOW

1. **RUN CHECKS**: Execute grep commands above
2. **CATEGORIZE**: Group issues by category
3. **PRIORITIZE**: P0 → P1 → P2
4. **FIX P0**: Critical issues first
5. **VALIDATE**: npm run build after each fix
6. **FIX P1**: Important issues next
7. **VISUAL CHECK**: Storybook verification
8. **AUDIT**: Run conformity checklist
9. **COMMIT**: Document all fixes

---

## COMMIT FORMAT

fix({level}): Resolve {count} issues in {component}

Critical (P0):
- {Issue 1}: {Fix}
- {Issue 2}: {Fix}

Important (P1):
- {Issue 3}: {Fix}
- {Issue 4}: {Fix}

Changes:
- Replace {X} hardcoded values with tokens
- Fix {Y} Drupal incompatibilities (arrow functions)
- Add missing autodocs + focus-visible
- Refactor flat CSS to nested
- Fix BEM violations: {list}

Before: {X} issues, audit score Y/100
After: 0 critical issues ✅, audit score Z/100 ✅

References: 
- .github/instructions/04-quality-assurance.md
- .github/prompts/fix-component.md

SUCCESS CRITERIA:
✅ All P0 issues resolved
✅ Build passes without errors
✅ Visual regression: None
✅ Conformity audit ≥90/100
```

---

**Estimated Time**: 1-3 hours (depending on issue count)  
**Difficulty**: Medium-High  
**Prerequisites**: Understanding of grep patterns, PS Theme standards, debugging skills
