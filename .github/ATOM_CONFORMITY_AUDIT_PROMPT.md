# 🔍 Atom Component Conformity Audit Prompt

**Version**: 4.0.0  
**Date**: 2025-12-08  
**Purpose**: Generic prompt to verify strict compliance of ANY atom component with PS Theme rules

---

## 📋 UNIVERSAL AUDIT PROMPT

Use this prompt with AI agents to verify ANY atom component conforms to 100% of project standards.

### For AI Agent / Copilot

```
Audit the atom component: {COMPONENT_NAME} located in source/patterns/elements/{component}/

Verify STRICT compliance with ALL rules below. Return FAIL immediately if ANY rule is violated.
Score 100/100 points only if ALL checks pass (no partial credit).

=== FILE STRUCTURE (MANDATORY - 0 TOLERANCE) ===
☐ 5 files exist: {component}.twig, {component}.css, {component}.yml, {component}.stories.jsx, README.md
☐ All files named exactly: {component}.{ext} (lowercase, kebab-case)
☐ NO {component}.js present (unless documented behavior required)

=== TWIG TEMPLATE ===

☐ Header comment present with:
  - Component purpose/description
  - @param entries for all props (type, required/optional, description)
  Example:
  {#
   * Component description
   * @param string text - Text content (required)
   * @param string size - xs|sm|md|lg|xl|xxl (optional, default: md)
   * @param object attributes - Additional HTML attributes (optional)
  #}

☐ ALL default values use: {%- set prop = prop|default('value') -%}
  - NO unset variables without defaults
  - Format MUST be: varname|default('defaultvalue')

☐ Classes construction uses ternary + null:
  {%- set classes = [
    'ps-component',
    condition ? 'ps-component--modifier' : null,
    another ? 'ps-component--state' : null
  ] -%}
  - NEVER: filter(v => v), map(), includes(), .filter()
  - NEVER: if-else blocks for classes
  - MUST: ternary: condition ? 'class' : null

☐ Markup principle - MINIMAL:
  - Default values = NO modifier classes in output
  - Example: <button class="ps-button"> (not <button class="ps-button ps-button--md">)

☐ Composition uses ONLY attributes.addClass():
  {%- include '@elements/element/element.twig' with {
    prop: value,
    attributes: create_attribute().addClass('ps-parent__element')
  } only -%}
  - FORBIDDEN: baseClass parameter (REMOVED in v4.0.0)
  - REQUIRED: only keyword at end

☐ Real Estate context:
  - Property-related content (addresses, property types, prices, contact info)
  - NOT generic placeholder text
  - Example: "Luxury apartment in Marais" (NOT "Some text here")

=== CSS STYLES ===

☐ ALL values from tokens (ZERO hardcoded values):
  ✅ background: var(--primary); font-size: var(--font-size-1);
  ❌ background: #00915A; font-size: 16px; color: green;
  - Check EVERY value: colors, sizes, durations, shadows, borders, spacing
  - Must reference: source/props/*.css tokens

☐ Nesting syntax with & (postcss-nested required):
  .ps-component {
    color: var(--text-primary);
    
    &__element {
      padding: var(--size-2);
    }
    
    &--modifier {
      background: var(--primary);
    }
    
    &:hover {
      color: var(--primary-hover);
    }
  }
  - NEVER flat CSS without nesting
  - NEVER .ps-component-element (use __element)

☐ Cascade order (CRITICAL):
  1. Block base: .ps-component { } — Layout, typography
  2. Elements: &__element { } — Sub-elements
  3. Modifiers: &--variant { } — Size, color, state
  4. States: &:hover, &:focus-visible { } — Interactions
  - NEVER modifiers BEFORE base

☐ Semantic colors ONLY:
  ✅ var(--primary), var(--secondary), var(--success), var(--warning), var(--danger), var(--info)
  ❌ var(--green-600), #00915A, green
  - Check: Colors, borders, backgrounds, text, shadows

☐ Focus-visible for ALL interactives (buttons, links, inputs):
  &:focus-visible {
    outline: var(--border-focus);
    outline-offset: 2px;
  }
  - REQUIRED for keyboard accessibility (WCAG 2.2 AA)

☐ Component-scoped variables (Layer 2 system):
  --ps-component-size: var(--size-4);
  --ps-component-color: var(--primary);
  - Used BEFORE applying to elements
  - Prevents duplication, enables easy modification

☐ Modifier independence:
  - Each modifier works ALONE on base class
  - Example: .ps-button--primary (works), .ps-button--lg (works)
  - NOT: .ps-button--primary + .ps-button--lg required together

=== STORYBOOK STORIES ===

☐ Import syntax EXACT:
  import componentTwig from './component.twig';

☐ Export default with tags MANDATORY:
  export default {
    title: 'Elements/Component',
    render: (args) => componentTwig(args),
    tags: ['autodocs'],  {# REQUIRED #}
    parameters: { ... },
    argTypes: { ... }
  }

☐ NO React/JSX:
  ❌ <Component prop={value} />
  ❌ import React from 'react';
  ✅ render: (args) => componentTwig(args)

☐ ArgTypes structured by CATEGORY (exact names):
  Content | Appearance | Behavior | Link | Accessibility | Layout
  Example:
  argTypes: {
    text: {
      description: 'Display text',
      control: 'text',
      table: { category: 'Content' }
    },
    size: {
      description: 'xs|sm|md|lg|xl|xxl',
      control: 'select',
      options: ['xs', 'sm', 'md', 'lg', 'xl', 'xxl'],
      table: { category: 'Appearance' }
    }
  }

☐ Description ≤ 2 lines in parameters.docs.description.component:
  parameters: {
    docs: {
      description: {
        component: 'Single-line or two-line max description'
      }
    }
  }

☐ Stories structure - ONLY 2 types:
  1. Default: Shows standard usage
  2. Showcase stories: AllVariants, AllColors, AllSizes, UseCases, Combinations
  - FORBIDDEN: Individual variant stories (Primary, Secondary, Small, etc.)
  - Example story name: export const AllColors = { ... }

☐ Story args use YAML defaults:
  - All args MUST be defined in {component}.yml
  - No hardcoded fallbacks in stories
  - Stories pull from YAML context

=== YAML DATA ===

☐ File format valid YAML (check indentation, no tabs)

☐ Real Estate context:
  - Property-realistic values
  - French or bilingual content (per project lang directive)
  - Examples: addresses, property types, price ranges, agent names

☐ ALL required props defined with meaningful values

☐ Optional props have sensible defaults

☐ NO empty/null values (use realistic fallbacks)

=== README.md ===

☐ Section present: **# Component Name**

☐ Section present: **## Props**
  Table format:
  | Prop | Type | Default | Description |
  |------|------|---------|-------------|

☐ Section present: **## BEM Structure**
  Tree format showing block/elements/modifiers:
  ```
  .ps-component
    .ps-component__element
    .ps-component--variant
  ```

☐ Section present: **## Usage**
  Twig include example with real Estate data

☐ Section present: **## Design Tokens**
  CSS variables used:
  - var(--primary)
  - var(--size-2)

☐ Section present: **## Accessibility**
  WCAG 2.2 AA checklist:
  - [ ] Contrast: 4.5:1 text, 3:1 UI components
  - [ ] Focus-visible indicator on interactives
  - [ ] ARIA attributes (if needed)
  - [ ] Keyboard navigation support

☐ Section present: **## Examples** (2-3 real use cases)

☐ Written in English (code documentation standard)

=== BEM NAMING ===

☐ Prefix **ps-** mandatory on ALL classes:
  ✅ .ps-button, .ps-button__icon, .ps-button--primary
  ❌ .button, .btn, .button-icon

☐ Format: .ps-{block}__{element}--{modifier}
  - Block: alphanumeric + hyphens
  - Element: __ (double underscore)
  - Modifier: -- (double hyphen)
  - NO: single underscore, single hyphen for elements/modifiers

☐ NO nested elements:
  ✅ .ps-card__image, .ps-card__title
  ❌ .ps-card__header__title (double __)

☐ Modifiers logical + independent:
  ✅ .ps-button--primary, .ps-button--lg, .ps-button--disabled
  ❌ .ps-button--primary-disabled (chain modifiers in one class)

=== ACCESSIBILITY (WCAG 2.2 AA) ===

☐ Contrast ratios (checked via color tool):
  - Text on background: minimum 4.5:1
  - UI components (borders, icons): minimum 3:1
  - NOT required: Decorative elements

☐ Focus-visible indicator present on:
  - All buttons
  - All links
  - All form inputs
  - All interactive elements
  - Style: outline or border with sufficient contrast

☐ ARIA attributes (when needed):
  - aria-label for icon-only buttons
  - aria-hidden="true" for decorative icons
  - role attributes for complex patterns
  - aria-live for dynamic content

☐ Keyboard navigation:
  - Tab: Focus all interactive elements
  - Enter/Space: Activate buttons/links
  - Escape: Close modals (if applicable)
  - Arrow keys: Navigation (if carousel/menu)

☐ Semantic HTML:
  ✅ <button> for buttons, <a> for links, <input> for fields
  ❌ <div role="button">, <span onclick="">

☐ Alt text:
  - Informative images: descriptive alt text
  - Decorative images: alt=""
  - Icons: aria-label if informative, aria-hidden if decorative

=== DOCUMENTATION REFERENCES ===

☐ All rules traced to instruction files:
  - File structure: instructions/components.instructions.md
  - CSS standards: instructions/css.instructions.md
  - BEM: instructions/components.instructions.md
  - Storybook: instructions/storybook.instructions.md
  - Composition: instructions/atomic-design.instructions.md
  - Accessibility: instructions/accessibility.instructions.md
  - Twig: instructions/javascript.instructions.md (Drupal compatibility)

=== FINAL VALIDATION ===

☐ npm run build: PASS (no lint/format errors)
☐ Storybook preview: Component renders correctly
☐ Visual check: Matches design specification (if exists)
☐ Responsive: Works on mobile/tablet/desktop (if applicable)

=== SCORING ===

Subtract points for each violation:
- Critical violations (0 tolerance): -50 points (File structure, baseClass, arrow functions, hardcoded values)
- Major violations: -10 points each (Missing sections, invalid syntax, Drupal incompatibility)
- Minor violations: -5 points each (Whitespace, naming inconsistencies)

**90-100 points**: ✅ PRODUCTION READY
**75-89 points**: ⚠️  MINOR FIXES REQUIRED
**Below 75 points**: ❌ MAJOR REFACTORING NEEDED

=== REPORT TEMPLATE ===

Return audit as:

## Audit Report: {COMPONENT_NAME}

**Score**: {TOTAL}/100

### ✅ Passed Checks ({COUNT})
- Check 1: ✓
- Check 2: ✓

### ❌ Failed Checks ({COUNT})
- **Check name** (Category): Description of violation
  - File affected: path/to/file
  - Required action: What needs to be fixed

### 🔧 Recommended Fixes (Priority)
1. [CRITICAL] Fix violation X
2. [MAJOR] Fix violation Y
3. [MINOR] Fix violation Z

### 📝 Notes
- Overall assessment
- Critical issues blocking production
- Suggestions for improvement
```

---

## 🎯 Usage Examples

### Example 1: Audit existing atom
```
Audit the atom component: button located in source/patterns/elements/button/

[Paste audit prompt above]
```

### Example 2: Audit new atom before commit
```
I just created a new atom: avatar in source/patterns/elements/avatar/

[Paste audit prompt above]

Return audit report with 100/100 score confirmation or list failures.
```

### Example 3: During code review
```
Review atom component: badge at source/patterns/elements/badge/

[Paste audit prompt above]

If score < 90, provide exact file locations and code snippets for fixes needed.
```

---

## ✨ Key Features

✅ **Zero Tolerance for Critical Rules**:
- baseClass composition (removed in v4.0.0)
- Arrow functions in Twig
- Hardcoded values (colors, sizes, durations)
- File structure violations
- Missing tags: ['autodocs']

✅ **Comprehensive Coverage**:
- All 8 categories: Structure, Twig, CSS, Storybook, YAML, README, BEM, A11y
- 80+ individual checks
- Scoring system with weighted violations
- Report template included

✅ **Real Estate Context Enforcement**:
- Requires property-realistic data
- No generic placeholder text
- Bilingual content support

✅ **Production-Ready Standard**:
- 90/100 = Ready to ship
- Traceability to instruction files
- Accessibility (WCAG 2.2 AA)
- Build validation included

---

## 📌 Rules NOT Negotiable

These will **ALWAYS** result in FAIL:

1. ❌ `baseClass` parameter used anywhere
2. ❌ Arrow functions: `filter(v => v)` 
3. ❌ Hardcoded colors/sizes/durations
4. ❌ Missing `tags: ['autodocs']`
5. ❌ File structure incomplete (< 5 files)
6. ❌ Files named incorrectly
7. ❌ React/JSX in Storybook
8. ❌ Flat CSS (no nesting)
9. ❌ Non-semantic colors (green/blue/red)
10. ❌ Missing focus-visible on interactives

---

**Maintainer**: Design System Team  
**Last Updated**: 2025-12-08  
**Version Compliance**: PS Theme 4.0.0+
