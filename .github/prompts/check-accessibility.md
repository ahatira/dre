# Prompt: Check Accessibility Compliance

**Purpose**: Audit component for WCAG 2.2 AA accessibility compliance.

---

## 📋 Prompt Template

```
Accessibility audit for: {COMPONENT_NAME}
Location: source/patterns/{level}/{component}/

OBJECTIVE: Ensure WCAG 2.2 Level AA compliance

AUDIT CATEGORIES:

1. COLOR CONTRAST
2. KEYBOARD NAVIGATION
3. FOCUS INDICATORS
4. ARIA ATTRIBUTES
5. SEMANTIC HTML
6. SCREEN READER SUPPORT
7. FORM ACCESSIBILITY (if applicable)
8. ERROR HANDLING (if applicable)

---

## 1. COLOR CONTRAST

**Requirement**: 4.5:1 for text, 3:1 for UI components

CHECK:
- All text vs background combinations
- Icon contrast (if conveying meaning)
- Border contrast for interactive elements
- State indicators (hover, focus, active)

TOOLS:
- Chrome DevTools: Inspect → Accessibility → Contrast ratio
- Online: https://contrast-ratio.com
- Token reference: source/props/COLORS_REFERENCE.md

VERIFY TOKENS:
grep "color:\|background:" {component}.css

Example results:
color: var(--text-primary);        ✅ 15:1 on white
background: var(--primary);        ✅ 4.7:1 with white text
color: var(--text-secondary);      ✅ 7:1 on white
border: var(--border-light);       ✅ 3.2:1 on white

COMMON FAILURES:
❌ Light text on light background
❌ Gray text <4.5:1 ratio
❌ Insufficient border contrast on inputs
❌ Hover states losing contrast

FIXES:
- Use semantic tokens: --text-primary (guaranteed 4.5:1+)
- For interactive elements: --border-default (3:1+)
- Avoid custom grays: Use --gray-* scale (tested ratios)

---

## 2. KEYBOARD NAVIGATION

**Requirement**: All interactive elements reachable and operable via keyboard

CHECK:
A. Tab order logical (left-to-right, top-to-bottom)
B. All buttons/links focusable (no tabindex="-1" unless intentional)
C. Tab traps avoided (modals must allow Escape)
D. Enter/Space activate buttons
E. Arrow keys for groups (radio, tabs, menus)

TEST:
1. Start at top of page
2. Press Tab repeatedly
3. Verify reach all interactive elements
4. Press Shift+Tab (reverse)
5. Verify no trapped focus

TWIG CHECK:
grep -E "tabindex|aria-hidden" {component}.twig

Example results:
✅ No tabindex="-1" on interactive elements
✅ aria-hidden only on decorative elements
❌ Found: <button tabindex="-1"> (remove unless modal close)

COMMON FAILURES:
❌ Interactive div without tabindex="0"
❌ Links with pointer-events: none (still focusable)
❌ Hidden elements focusable (use aria-hidden + tabindex="-1")

FIXES:
- Use semantic HTML: <button>, <a>, <input> (native focus)
- If div must be interactive: tabindex="0" + role="button"
- Hidden elements: aria-hidden="true" tabindex="-1"

---

## 3. FOCUS INDICATORS

**Requirement**: Visible focus indicator (min 2px, 3:1 contrast)

CHECK:
- All interactive elements have :focus-visible styles
- Focus indicator ≥2px thickness
- Focus color contrasts 3:1 with background
- Focus not hidden by other elements

CSS CHECK:
grep "focus-visible" {component}.css

Example results:
✅ &:focus-visible { outline: 2px solid var(--border-focus); }
❌ Missing :focus-visible on .ps-{component}__link

VERIFY TOKEN:
--border-focus → #2563EB (blue, 4.7:1 on white) ✅

COMMON FAILURES:
❌ outline: none without replacement
❌ Focus same color as element (invisible)
❌ outline-offset too large (indicator outside viewport)
❌ Missing focus on custom interactive elements

FIXES:
- ALL interactive elements MUST have :focus-visible:
  &:focus-visible {
    outline: 2px solid var(--border-focus);
    outline-offset: 2px;
  }
- Remove outline: none unless replacing with box-shadow/border

---

## 4. ARIA ATTRIBUTES

**Requirement**: Proper ARIA roles, states, properties

CHECK:
A. role attribute correct for custom elements
B. aria-label/aria-labelledby for icon buttons
C. aria-expanded for collapsible sections
D. aria-live for dynamic content
E. aria-describedby for supplementary info
F. aria-hidden for decorative elements

TWIG CHECK:
grep -E "aria-|role=" {component}.twig

Example results:
✅ <button aria-label="Close modal">
✅ <div role="alert" aria-live="polite">
✅ <span aria-hidden="true" class="icon">
❌ <button><span class="icon-only"></span></button>  (Missing aria-label)

COMMON FAILURES:
❌ Icon buttons without aria-label
❌ role="button" on <button> (redundant)
❌ aria-live on static content
❌ aria-hidden on interactive elements

FIXES:
Icon-only buttons:
<button aria-label="{{ ariaLabel|default('Button action') }}">
  {% include '@elements/icon/icon.twig' with { icon: 'close' } only %}
</button>

Collapsible sections:
<button aria-expanded="{{ expanded ? 'true' : 'false' }}" aria-controls="content-id">
  Toggle
</button>
<div id="content-id" {{ expanded ? '' : 'hidden' }}>
  Content
</div>

Decorative icons:
<span aria-hidden="true" data-icon="decorative"></span>

Dynamic updates:
<div role="status" aria-live="polite" aria-atomic="true">
  {{ statusMessage }}
</div>

---

## 5. SEMANTIC HTML

**Requirement**: Use appropriate HTML5 elements

CHECK:
- Buttons for actions: <button> not <div>
- Links for navigation: <a> not <span>
- Headings hierarchy: <h1> → <h2> → <h3> (no skipping)
- Lists for groups: <ul>/<ol> not <div>
- Forms: <form>, <label>, <input>, <fieldset>

TWIG CHECK:
grep -E "<div|<span" {component}.twig | grep -E "onclick|role="

Example results:
❌ <div onclick="..."> (use <button>)
❌ <span role="button"> (use <button>)
✅ <button type="button"> (correct)

COMMON FAILURES:
❌ <div class="button" onclick="...">
❌ <a href="#" onclick="preventDefault">  (use <button>)
❌ <span tabindex="0" role="link">  (use <a>)

FIXES:
Actions (no navigation):
<button type="button">Click Me</button>

Navigation (URL change):
<a href="/page">Go to Page</a>

Groups:
<ul>
  {% for item in items %}
    <li>{{ item }}</li>
  {% endfor %}
</ul>

---

## 6. SCREEN READER SUPPORT

**Requirement**: Meaningful content for screen readers

CHECK:
A. All images have alt text (or alt="" if decorative)
B. Icon-only buttons have aria-label
C. Complex components have aria-describedby
D. Form inputs have associated <label>
E. Error messages announced (aria-live)

TWIG CHECK:
grep -E "<img|<input|icon" {component}.twig

Example results:
✅ <img src="..." alt="{{ imageAlt|default('Description') }}">
✅ <input id="field-id"> <label for="field-id">
❌ <img src="..." alt="">  (not decorative - needs alt text)
❌ <input placeholder="Name">  (placeholder not label)

COMMON FAILURES:
❌ Placeholder as label (not read correctly)
❌ Icons without alt/aria-label
❌ Complex widgets without instructions

FIXES:
Images:
<img src="{{ src }}" alt="{{ alt|default('Descriptive alternative text') }}">

Icon buttons:
<button aria-label="{{ label|default('Perform action') }}">
  {% include '@elements/icon/icon.twig' with { icon: iconName } only %}
</button>

Form fields:
<label for="field-{{ id }}">{{ label }}</label>
<input id="field-{{ id }}" name="{{ name }}" {{ required ? 'required' : '' }}>

---

## 7. FORM ACCESSIBILITY (if applicable)

**Requirement**: Forms fully accessible

CHECK:
A. All inputs have associated labels
B. Required fields marked (aria-required="true")
C. Error messages linked (aria-describedby)
D. Fieldsets group related inputs
E. Submit button accessible

TWIG CHECK:
grep -E "<input|<label|<select|<textarea" {component}.twig

Example:
✅ ACCESSIBLE FORM:

<form>
  <div class="field">
    <label for="name-{{ id }}">
      Name
      {% if required %}<span aria-label="required">*</span>{% endif %}
    </label>
    <input 
      id="name-{{ id }}"
      name="name"
      type="text"
      {{ required ? 'required' : '' }}
      aria-required="{{ required ? 'true' : 'false' }}"
      aria-describedby="{{ error ? 'error-' ~ id : null }}"
      aria-invalid="{{ error ? 'true' : 'false' }}"
    >
    {% if error %}
      <div id="error-{{ id }}" role="alert" class="error">
        {{ error }}
      </div>
    {% endif %}
  </div>
  
  <button type="submit">Submit</button>
</form>

---

## 8. ERROR HANDLING (if applicable)

**Requirement**: Errors announced and clear

CHECK:
A. Errors have role="alert" or aria-live="assertive"
B. Error messages descriptive (not just "Error")
C. Error associated with field (aria-describedby)
D. Visual error indicator + text (not color alone)

EXAMPLE:

{% if errors %}
  <div role="alert" aria-live="assertive" class="alert alert--danger">
    <strong>Form Errors:</strong>
    <ul>
      {% for error in errors %}
        <li>{{ error.message }}</li>
      {% endfor %}
    </ul>
  </div>
{% endif %}

---

## AUDIT REPORT TEMPLATE

# Accessibility Audit: {Component Name}

**Date**: {YYYY-MM-DD}  
**WCAG Level**: AA  
**Result**: PASS ✅ / FAIL ❌ / PARTIAL ⚠️

---

## Summary

Total Issues: {count}
- Critical (P0): {count}
- Important (P1): {count}
- Minor (P2): {count}

---

## 1. Color Contrast

**Status**: {PASS/FAIL/PARTIAL}

Checks:
- [ ] Text contrast ≥4.5:1
- [ ] UI component contrast ≥3:1
- [ ] Focus indicator contrast ≥3:1
- [ ] All semantic tokens used

Issues: {list or "None"}

---

## 2. Keyboard Navigation

**Status**: {PASS/FAIL/PARTIAL}

Checks:
- [ ] All interactive elements focusable
- [ ] Tab order logical
- [ ] No focus traps
- [ ] Enter/Space activate buttons

Issues: {list or "None"}

---

## 3. Focus Indicators

**Status**: {PASS/FAIL/PARTIAL}

Checks:
- [ ] :focus-visible on ALL interactive elements
- [ ] Indicator ≥2px thickness
- [ ] Contrast ≥3:1
- [ ] Not obscured by other elements

Issues: {list or "None"}

---

## 4. ARIA Attributes

**Status**: {PASS/FAIL/PARTIAL}

Checks:
- [ ] Proper roles (if custom elements)
- [ ] aria-label on icon-only buttons
- [ ] aria-expanded for collapsible
- [ ] aria-live for dynamic content
- [ ] aria-hidden on decorative only

Issues: {list or "None"}

---

## 5. Semantic HTML

**Status**: {PASS/FAIL/PARTIAL}

Checks:
- [ ] <button> for actions
- [ ] <a> for navigation
- [ ] <ul>/<ol> for lists
- [ ] No div/span with role="button"

Issues: {list or "None"}

---

## 6. Screen Reader Support

**Status**: {PASS/FAIL/PARTIAL}

Checks:
- [ ] Images have alt text
- [ ] Icon-only buttons have aria-label
- [ ] Form inputs have labels
- [ ] Error messages announced

Issues: {list or "None"}

---

## Recommendations

### Immediate (P0)
- {Fix 1}
- {Fix 2}

### Important (P1)
- {Fix 1}

### Nice to Have (P2)
- {Enhancement 1}

---

## Testing Notes

- **Keyboard**: {summary}
- **Screen Reader**: {summary} (NVDA/JAWS/VoiceOver)
- **Browser**: {Chrome/Firefox/Safari versions}

SUCCESS CRITERIA:
✅ All P0 issues resolved
✅ WCAG 2.2 AA compliant
✅ Keyboard navigable
✅ Screen reader friendly
```

---

**Estimated Time**: 20-30 minutes  
**Difficulty**: Medium  
**Prerequisites**: Understanding of WCAG 2.2 AA requirements  
**Tools**: Chrome DevTools, screen reader (NVDA/JAWS/VoiceOver), contrast checker
