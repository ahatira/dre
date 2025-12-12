---
title: Code Examples Style Guide
version: 1.0.0
lastUpdated: 2025-12-12
applyTo:
  - ".github/instructions/**/*.md"
priority: LOW
related:
  - README.md
status: ACTIVE
---

# Code Examples Style Guide

**Purpose**: Standardize code example formatting across all instruction files for consistency and readability.

---

## 🎯 Core Principles

1. **Clarity**: Examples should be self-explanatory
2. **Brevity**: Keep examples minimal (5-10 lines preferred)
3. **Context**: Use inline comments to explain WHY, not WHAT
4. **Consistency**: Follow established patterns

---

## 📝 Example Markers

### Positive Examples (What TO Do)

**Primary format**: ✅ **GOOD**
```css
/* ✅ GOOD - Semantic color tokens */
.ps-button {
  background: var(--primary);
}
```

**Alternative format** (when comparing levels): ✅ **BEST** / ⚠️ **OK**
```html
<!-- ✅ BEST - Native button -->
<button>Click</button>

<!-- ⚠️ OK - Custom element (when necessary) -->
<div role="button" tabindex="0">Click</div>
```

**When to use**:
- GOOD: Standard correct approach (90% of cases)
- BEST: Optimal solution among multiple options
- OK: Acceptable but not ideal (with explanation)

---

### Negative Examples (What NOT to Do)

**Primary format**: ❌ **WRONG**
```css
/* ❌ WRONG - Hardcoded color */
.ps-button {
  background: #00915A;
}
```

**Alternative format** (legacy): ❌ **BAD**
```css
/* ❌ BAD - Low contrast */
.ps-text {
  color: var(--gray-300);
}
```

**When to use**:
- WRONG: Incorrect approach (technical violation)
- BAD: Poor practice (maintainability issue)
- Both are acceptable, prefer WRONG for consistency

---

### Comparative Examples

**Format**: Show WRONG first, then CORRECT

```css
/* ❌ WRONG - Hardcoded value */
padding: 16px;

/* ✅ GOOD - Token usage */
padding: var(--size-4);
```

**Rationale**: Show problem before solution (pedagogical order)

---

## 🔤 Language Markers

### CSS Comments
```css
/* ✅ GOOD - Description */
.selector { }

/* ❌ WRONG - Description */
.selector { }
```

### HTML Comments
```html
<!-- ✅ GOOD - Description -->
<div></div>

<!-- ❌ WRONG - Description -->
<div></div>
```

### Twig Comments
```twig
{# ✅ GOOD - Description #}
{% set var = value %}

{# ❌ WRONG - Description #}
{% set var = value %}
```

### JavaScript Comments
```js
// ✅ GOOD - Description
const value = 'correct';

// ❌ WRONG - Description
const value = 'incorrect';
```

---

## 📐 Code Block Structure

### Minimal Example (Preferred)

**5-10 lines max**, focus on single concept:

```css
/* ✅ GOOD - Focus indicator */
.ps-button:focus-visible {
  outline: var(--border-size-2) solid var(--secondary);
  outline-offset: var(--border-size-2);
}
```

**When to use**:
- Demonstrating single rule/pattern
- Quick reference
- Anti-pattern illustration

---

### Complete Example (When Necessary)

**20-30 lines max**, show full context:

```css
/* Full example - Complete button styles */
.ps-button {
  /* Base styles */
  padding: var(--size-3) var(--size-6);
  background: var(--primary);
  color: var(--white);
  
  /* States */
  &:hover {
    background: var(--primary-hover);
  }
  
  &:focus-visible {
    outline: var(--border-size-2) solid var(--secondary);
  }
  
  &:disabled {
    opacity: 0.5;
  }
}
```

**When to use**:
- Component structure demonstration
- Multiple related patterns
- Real-world implementation

**Mark clearly**: Add `/* Full example */` or `/* Complete implementation */` comment at start

---

## 💬 Inline Comments

### Explanatory Comments

**Format**: `/* Why this works / Why this fails */`

```css
/* ✅ GOOD - GPU-accelerated (no reflow) */
transform: translateY(-2px);

/* ❌ WRONG - Causes reflow (performance issue) */
margin-top: -2px;
```

**Purpose**: Explain the reasoning, not the syntax

---

### Contextual Comments

**Format**: `/* Specific use case or constraint */`

```twig
{# ✅ GOOD - Drupal-compatible (no arrow functions) #}
{% set classes = [
  'ps-component',
  size != 'md' ? 'ps-component--' ~ size : null
] %}

{# ❌ WRONG - Arrow function (Drupal incompatible) #}
{% set classes = items|filter(v => v) %}
```

---

## 🎨 Formatting Standards

### Indentation
- **2 spaces** for CSS, JavaScript, YAML
- **2 spaces** for Twig (consistent with project)
- **No tabs** (spaces only)

### Line Length
- **80 characters preferred** (readability)
- **100 characters maximum** (hard limit)
- Break long lines at logical points

### Spacing
```css
/* ✅ GOOD - Proper spacing */
.selector {
  property: value;
}

/* ❌ WRONG - Inconsistent spacing */
.selector{property:value;}
```

---

## 📚 Example Types by File

### CSS Files (`css.instructions.md`)
- Token usage: ✅ GOOD / ❌ WRONG
- Selectors: ✅ GOOD / ❌ WRONG
- Nesting: ✅ GOOD / ❌ WRONG
- Performance: ✅ GOOD / ❌ BAD

### Twig Files (`templates.instructions.md`, `components.instructions.md`)
- Syntax: ✅ GOOD / ❌ WRONG
- Drupal compatibility: ✅ GOOD / ❌ WRONG
- Composition: ✅ GOOD / ❌ WRONG

### Accessibility Files (`accessibility.instructions.md`)
- Semantic HTML: ✅ BEST / ⚠️ OK / ❌ BAD
- ARIA: ✅ GOOD / ❌ WRONG
- Keyboard: ✅ GOOD / ❌ BAD

### JavaScript Files (`javascript.instructions.md`)
- Drupal behaviors: ✅ GOOD / ❌ WRONG
- Event handling: ✅ GOOD / ❌ BAD

---

## 🚫 Anti-Patterns to Avoid

### 1. Ambiguous Comments
```css
❌ /* Bad */
✅ /* ✅ GOOD - Specific reason */
```

### 2. Missing Context
```css
❌ .selector { property: value; }
✅ /* ✅ GOOD - Token usage for consistency */
   .selector { property: var(--token); }
```

### 3. Over-Commenting
```css
❌ /* Set the padding */ /* Use tokens */ /* Responsive */
✅ /* ✅ GOOD - Responsive padding with tokens */
```

### 4. Inconsistent Markers
```css
❌ Mix of GOOD/CORRECT/OK without clear distinction
✅ Consistent use of GOOD for standard correct examples
```

### 5. No Comparative Examples
```css
❌ Only show correct version (reader doesn't see mistake)
✅ Show wrong version first, then correct (pedagogical)
```

---

## 📊 Current Usage Statistics

Based on analysis of 15 instruction files:

| Marker | Usage Count | Primary Files |
|--------|-------------|---------------|
| ✅ GOOD | ~45 instances | css, accessibility, templates |
| ❌ WRONG | ~30 instances | components, atomic-design |
| ❌ BAD | ~25 instances | accessibility, css |
| ✅ CORRECT | ~20 instances | composition-token-first |
| ✅ BEST | ~5 instances | accessibility |
| ⚠️ OK | ~5 instances | accessibility |

**Verdict**: Predominantly GOOD/WRONG pattern with context-specific variations (BEST/OK for accessibility hierarchy).

---

## 🔄 Migration Strategy

**Status**: ✅ Current state is acceptable (85% consistent)

**Recommendation**: 
- **DO NOT** mass-replace existing markers (too invasive)
- **DO** use GOOD/WRONG for new examples
- **DO** document exceptions (BEST/OK in accessibility)
- **DO** maintain inline context comments

**Future work** (if needed):
- P2-X: Gradual harmonization (opportunistic)
- Script to detect inconsistent patterns
- Linting rule for new contributions

---

## ✅ Checklist for New Examples

When adding code examples to instruction files:

- [ ] Use ✅ GOOD / ❌ WRONG markers (or BEST/OK if comparing levels)
- [ ] Include inline comment explaining WHY (not WHAT)
- [ ] Keep minimal (5-10 lines) unless full context needed
- [ ] Mark complete examples with `/* Full example */`
- [ ] Show WRONG before CORRECT (pedagogical order)
- [ ] Use proper language comment syntax (CSS `/**/`, HTML `<!---->`, Twig `{##}`)
- [ ] Verify indentation (2 spaces, no tabs)
- [ ] Check line length (≤100 chars)
- [ ] Test code snippet compiles/renders correctly
- [ ] Cross-reference related examples if applicable

---

## 🔗 Related Documentation

- **README**: Navigation and file overview
- **Components**: Component structure examples
- **CSS**: CSS token and nesting examples
- **Accessibility**: Semantic HTML and ARIA examples
- **Templates**: Twig syntax examples

---

**Maintainers**: Design System Team  
**Last Updated**: 2025-12-12  
**Next Review**: When adding 5+ new instruction files
