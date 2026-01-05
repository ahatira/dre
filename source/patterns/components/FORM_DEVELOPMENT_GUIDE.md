# Form Components Development Guide

**Last Updated**: December 14, 2025  
**Version**: 1.0.0  
**Author**: Design System Team

---

## Overview

This guide provides step-by-step instructions for implementing new form components in the PS Theme. It covers:
- Creating self-sufficient form Atoms
- Understanding the composition pattern
- Building Form Field molecules
- Validating against design standards

**Key Principle**: All form controls are **self-sufficient Atoms** that natively support Drupal's `.form-control` class. Form Field is a **composition container** that includes Atoms, not a reimplementation.

---

## Quick Start: Creating a New Form Atom

### Step 1: Understand the Architecture

Form components follow this hierarchy:

```
Atom (Self-Sufficient)
├─ Includes .form-control class natively
├─ Works standalone in Storybook
└─ Works composed inside Form Field

Molecule (Form Field)
├─ Includes Atom via {% include %}
├─ Adds presentation layer (label, optional badge, error text)
└─ Does NOT reimplement Atom styles
```

### Step 2: Create 4 Required Files

```bash
source/patterns/elements/{component}/
├─ {component}.twig          # Template with .form-control
├─ {component}.css           # Styling with dual selectors
├─ {component}.yml           # Mock data for Real Estate context
└─ {component}.stories.jsx   # Storybook documentation (30 stories)
```

### Step 3: Implement the Twig Template

**Template Structure** (Example: `input.twig`)

```twig
{#
  Input Form Control Atom
  ==========================================
  Self-sufficient input element with native .form-control support
  
  Context:
  - type (string): Input type (text, email, password, number, search, tel, url)
  - modifier (string): Optional modifier class (--error, --success, --warning)
  - placeholder (string): Placeholder text
  - value (string): Input value
  - attributes (object): Drupal attributes (id, aria-*, etc.)
#}

{%- set modifier_class = modifier ? 'ps-input--' ~ modifier : null -%}
{%- set classes = ['ps-input', 'form-control', modifier_class] | filter(v => v) -%}

<input
  class="{{ classes | join(' ') }}"
  type="{{ type | default('text') }}"
  {% if placeholder %}placeholder="{{ placeholder }}"{% endif %}
  {% if value %}value="{{ value }}"{% endif %}
  {{ attributes }}
/>
```

**Key Rules**:
- ✅ **MUST include `.form-control`** in classes array
- ✅ Use **ternary operators**, never arrow functions (Drupal incompatible)
- ✅ Include **attributes** parameter for Drupal integration
- ✅ Add **inline comments** explaining context
- ✅ Use **{{ attributes }}** at the end, unescaped

**Common Patterns**:

```twig
{# Filter null values cleanly #}
{%- set classes = ['ps-input', 'form-control', modifier_class] | filter(v => v) -%}

{# Conditional attributes #}
{% if required %}aria-required="true"{% endif %}
{% if error %}aria-invalid="true" aria-describedby="{{ error_id }}"{% endif %}

{# Composition in Form Field #}
{% include '@elements/input/input.twig' with {
  type: 'email',
  placeholder: 'your@email.com',
  attributes: attributes
} only %}
```

### Step 4: Implement CSS with Dual Selectors

**CSS Structure** (Example: `input.css`)

```css
/* ==========================================
   INPUT FORM CONTROL ATOM
   ========================================== */

/* Base selector: .ps-input + .form-control[type="..."] */
.ps-input,
.form-control[type="text"],
.form-control[type="email"],
.form-control[type="password"],
.form-control[type="number"],
.form-control[type="search"],
.form-control[type="tel"],
.form-control[type="url"] {
  /* CSS Variables for customization */
  --ps-input-width: 100%;
  --ps-input-height: var(--size-10);
  --ps-input-padding: 0 var(--size-4);
  --ps-input-font-size: var(--font-size-6);
  --ps-input-border-color: var(--border-default);
  --ps-input-border-radius: 0;
  --ps-input-bg-color: var(--white);
  --ps-input-text-color: var(--text-primary);
  --ps-input-placeholder-color: var(--text-secondary);
  --ps-input-transition: border-color var(--duration-2) var(--ease-out);

  /* Applied styles using variables */
  width: var(--ps-input-width);
  height: var(--ps-input-height);
  padding: var(--ps-input-padding);
  font-size: var(--ps-input-font-size);
  border: 1px solid var(--ps-input-border-color);
  border-radius: var(--ps-input-border-radius);
  background-color: var(--ps-input-bg-color);
  color: var(--ps-input-text-color);
  transition: var(--ps-input-transition);

  &::placeholder {
    color: var(--ps-input-placeholder-color);
  }

  &:hover:not(:disabled) {
    --ps-input-border-color: var(--border-default);
  }

  &:focus-visible {
    outline: 2px solid var(--border-focus);
    outline-offset: 2px;
    --ps-input-border-color: var(--border-focus);
  }

  &:disabled {
    --ps-input-bg-color: var(--gray-100);
    --ps-input-text-color: var(--text-disabled);
    cursor: not-allowed;
  }
}

/* Modifiers: --error, --success, --warning */
.ps-input--error,
.form-control[type="text"].ps-input--error,
.form-control[type="email"].ps-input--error,
.form-control[type="password"].ps-input--error,
.form-control[type="number"].ps-input--error,
.form-control[type="search"].ps-input--error,
.form-control[type="tel"].ps-input--error,
.form-control[type="url"].ps-input--error {
  --ps-input-border-color: var(--danger);
  --ps-input-text-color: var(--danger);
}

.ps-input--success,
.form-control[type="text"].ps-input--success,
.form-control[type="email"].ps-input--success,
.form-control[type="password"].ps-input--success,
.form-control[type="number"].ps-input--success,
.form-control[type="search"].ps-input--success,
.form-control[type="tel"].ps-input--success,
.form-control[type="url"].ps-input--success {
  --ps-input-border-color: var(--success);
  --ps-input-text-color: var(--success);
}

.ps-input--warning,
.form-control[type="text"].ps-input--warning,
.form-control[type="email"].ps-input--warning,
.form-control[type="password"].ps-input--warning,
.form-control[type="number"].ps-input--warning,
.form-control[type="search"].ps-input--warning,
.form-control[type="tel"].ps-input--warning,
.form-control[type="url"].ps-input--warning {
  --ps-input-border-color: var(--warning);
  --ps-input-text-color: var(--warning);
}
```

**Key Rules**:
- ✅ **Combined selectors** for both `.ps-input` and `.form-control`
- ✅ **CSS variables** for all colors, sizes, transitions
- ✅ **Nesting with &** for pseudo-classes
- ✅ **Semantic tokens only** - NO hardcoded colors or sizes
- ✅ **focus-visible** on all interactive elements
- ✅ **Modifiers work independently** - each must function alone

---

## YAML Mock Data Template

**File**: `{component}.yml`

```yaml
# Input Atom Mock Data
# ============================================
# Real Estate context for Storybook documentation

# Standard inputs
input_text:
  type: 'text'
  placeholder: 'Search properties...'
  value: ''

input_email:
  type: 'email'
  placeholder: 'your@email.com'
  value: ''

input_search:
  type: 'search'
  placeholder: 'Find properties in Paris'
  value: ''

# With state
input_with_value:
  type: 'text'
  placeholder: 'Search properties...'
  value: 'Luxury apartment Paris'

input_error:
  type: 'email'
  placeholder: 'your@email.com'
  value: 'invalid-email'
  modifier: 'error'

input_success:
  type: 'email'
  placeholder: 'your@email.com'
  value: 'valid@email.com'
  modifier: 'success'
```

**Rules**:
- Use **Real Estate context** (properties, locations, searches)
- Avoid generic lorem ipsum
- Include all **modifier variants** (error, success, warning)
- Provide **realistic values** that demonstrate the component

---

## Storybook Stories Template

**File**: `{component}.stories.jsx`

```jsx
import { default as InputAtom } from './input.twig';
import inputData from './input.yml';

export default {
  title: 'Elements/Input',
  component: InputAtom,
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component: 'Self-sufficient input element with native .form-control support for Drupal integration'
      }
    }
  },
  argTypes: {
    type: {
      control: 'select',
      options: ['text', 'email', 'password', 'number', 'search', 'tel', 'url'],
      description: 'Input type attribute',
      table: {
        category: 'Behavior',
        type: { summary: 'string' },
        defaultValue: { summary: 'text' }
      }
    },
    placeholder: {
      control: 'text',
      description: 'Placeholder text',
      table: {
        category: 'Content'
      }
    },
    modifier: {
      control: 'select',
      options: ['', '--error', '--success', '--warning'],
      description: 'Visual modifier (empty for default)',
      table: {
        category: 'Variant'
      }
    },
    value: {
      control: 'text',
      description: 'Input value',
      table: {
        category: 'Content'
      }
    }
  }
};

// Default state
export const Default = {
  args: {
    type: 'text',
    placeholder: inputData.input_text.placeholder,
    value: '',
    modifier: ''
  }
};

// Variant: Email
export const Email = {
  args: {
    type: 'email',
    placeholder: inputData.input_email.placeholder,
    value: '',
    modifier: ''
  }
};

// Variant: Search
export const Search = {
  args: {
    type: 'search',
    placeholder: inputData.input_search.placeholder,
    value: '',
    modifier: ''
  }
};

// State: With Value
export const WithValue = {
  args: {
    type: 'text',
    placeholder: inputData.input_text.placeholder,
    value: inputData.input_with_value.value,
    modifier: ''
  }
};

// State: Error
export const Error = {
  args: {
    type: 'email',
    placeholder: inputData.input_email.placeholder,
    value: inputData.input_error.value,
    modifier: '--error'
  }
};

// State: Success
export const Success = {
  args: {
    type: 'email',
    placeholder: inputData.input_email.placeholder,
    value: inputData.input_success.value,
    modifier: '--success'
  }
};

// State: Disabled
export const Disabled = {
  args: {
    type: 'text',
    placeholder: inputData.input_text.placeholder,
    value: 'Disabled input',
    modifier: '',
    attributes: { disabled: true }
  }
};
```

**Story Guidelines**:
- ✅ **tags: ['autodocs']** mandatory for all Atoms
- ✅ **argTypes categorized** (Behavior, Content, Variant, State)
- ✅ **Real Estate context** in values (properties, emails, searches)
- ✅ **7+ stories minimum** (Default + type variants + state variants)
- ✅ **NO dedicated RealEstateContext stories**

---

## Composition Pattern: Using Atoms in Form Field

**Problem**: How do you include an Atom in Form Field without duplicating code or losing styles?

**Solution**: Composition via `{% include %}` with `only`

**Form Field Twig** (Correct Pattern)

```twig
{#
  Form Field Molecule
  ==========================================
  Composition container for form Atoms
  Adds: label wrapper, optional badge, icon support, error styling
  Does NOT reimplement Atom styles
#}

{%- set form_field_class = modifier ? 'ps-form-field--' ~ modifier : null -%}

<div class="ps-form-field {{ form_field_class }}">
  <div class="ps-form-label-wrapper">
    <label class="ps-form-label" for="{{ input_id }}">
      {{ label }}
    </label>
    {% if optional %}<span class="ps-form-optional">Optional</span>{% endif %}
  </div>

  {# ✅ CORRECT: Compose Atom - it provides .form-control natively #}
  {% include '@elements/input/input.twig' with {
    type: field_type,
    placeholder: placeholder,
    value: value,
    modifier: field_modifier,
    attributes: attributes
  } only %}

  {# Error message styling (Form Field responsibility) #}
  {% if error %}
    <span class="ps-form-error" id="{{ error_id }}">{{ error }}</span>
  {% endif %}

  {# Helper text styling (Form Field responsibility) #}
  {% if helper %}
    <span class="ps-form-helper">{{ helper }}</span>
  {% endif %}
</div>
```

**Key Points**:
- ✅ Use `{% include %}` to compose Atoms
- ✅ Pass **only** necessary parameters
- ✅ Use `only` to prevent context leak
- ✅ Atom provides `.form-control` - NO need to `.addClass('form-control')`
- ✅ Form Field handles presentation (label, errors, helpers)
- ✅ Form Field does NOT duplicate Atom CSS

**What Form Field CSS Handles**:
```css
/* Form Field presentation layer */
.ps-form-label-wrapper {
  display: flex;
  justify-content: space-between;
  margin-bottom: var(--size-3);
}

.ps-form-label {
  font-weight: var(--font-weight-600);
  color: var(--text-primary);
}

.ps-form-optional {
  font-size: var(--font-size-4);
  color: var(--text-secondary);
}

.ps-form-error {
  display: block;
  color: var(--danger);
  font-size: var(--font-size-4);
  margin-top: var(--size-2);
}

.ps-form-helper {
  display: block;
  color: var(--text-secondary);
  font-size: var(--font-size-4);
  margin-top: var(--size-2);
}
```

---

## Design Token Checklist

**Use this checklist when implementing form components:**

### Colors
- [ ] Border: `--border-default`, `--border-focus`, `--danger`, `--success`
- [ ] Text: `--text-primary`, `--text-secondary`, `--text-disabled`, `--danger`
- [ ] Background: `--white`, `--gray-100`

### Sizes
- [ ] Height: `var(--size-10)` (40px standard input height)
- [ ] Padding: `var(--size-4)` (16px left/right), `var(--size-3)` (12px vertical)
- [ ] Spacing: `var(--size-2)` to `var(--size-6)` for gaps
- [ ] Icon spacing: `var(--size-8)` (32px padding when icon present)

### Typography
- [ ] Font size: `var(--font-size-6)` (16px)
- [ ] Font weight: `var(--font-weight-400)` (normal), `var(--font-weight-600)` (labels)
- [ ] Line height: `var(--line-height-4)` (1.5)

### Transitions
- [ ] Duration: `var(--duration-2)` (150ms)
- [ ] Easing: `var(--ease-out)` (easeOutQuad)
- [ ] Property: `border-color`, `background-color`, `color`

### Borders
- [ ] Width: `1px` (standard)
- [ ] Radius: `0` (sharp corners, no rounded)
- [ ] Style: `solid`

### Focus States
- [ ] Outline: `2px solid var(--border-focus)`
- [ ] Outline offset: `2px`
- [ ] Pseudo-class: `:focus-visible` (keyboard focus)

---

## Common Pitfalls & Solutions

### ❌ Pitfall 1: Missing .form-control Class

**Wrong**:
```twig
<input class="ps-input {{ modifier_class }}" />
```

**Correct**:
```twig
<input class="ps-input form-control {{ modifier_class }}" />
```

**Why**: Drupal expects `.form-control` to identify form controls. Without it, Drupal's form theming won't apply correctly.

---

### ❌ Pitfall 2: Reimplementing Atom CSS in Form Field

**Wrong**:
```css
.ps-form-field input {
  border: 1px solid var(--border-default);
  padding: var(--size-4);
  /* ... duplicating input.css ... */
}
```

**Correct**:
```css
.ps-form-field {
  margin-bottom: var(--size-6);
}

.ps-form-label {
  font-weight: var(--font-weight-600);
}

/* Input styling stays in input.css */
```

**Why**: Composition means Atoms work standalone. Form Field only adds presentation (label, error styling, spacing).

---

### ❌ Pitfall 3: Missing Dual CSS Selectors

**Wrong**:
```css
.ps-input {
  border: 1px solid var(--border-default);
}
```

**Correct**:
```css
.ps-input,
.form-control[type="text"],
.form-control[type="email"] {
  border: 1px solid var(--border-default);
}
```

**Why**: If Drupal only applies `.form-control` (not `.ps-input`), your styles won't apply without dual selectors.

---

### ❌ Pitfall 4: Hardcoded Colors/Sizes

**Wrong**:
```css
.ps-input {
  border: 1px solid #CCCCCC;
  padding: 16px;
  font-size: 16px;
}
```

**Correct**:
```css
.ps-input {
  border: 1px solid var(--border-default);
  padding: var(--size-4);
  font-size: var(--font-size-6);
}
```

**Why**: Tokens allow global theme changes. Hardcoded values break consistency.

---

### ❌ Pitfall 5: Arrow Functions in Twig

**Wrong**:
```twig
{%- set classes = ['ps-input', modifier_class].filter(v => v) -%}
```

**Correct**:
```twig
{%- set classes = ['ps-input', modifier_class] | filter(v => v) -%}
```

Or better, use ternary:
```twig
{%- set modifier_class = modifier ? 'ps-input--' ~ modifier : null -%}
{%- set classes = ['ps-input', 'form-control', modifier_class] | filter(v => v) -%}
```

**Why**: Drupal's Twig doesn't support modern JavaScript syntax. Use Twig filters instead.

---

### ❌ Pitfall 6: Missing focus-visible

**Wrong**:
```css
.ps-input:focus {
  outline: 2px solid var(--border-focus);
}
```

**Correct**:
```css
.ps-input:focus-visible {
  outline: 2px solid var(--border-focus);
  outline-offset: 2px;
}
```

**Why**: `:focus-visible` only shows focus ring for keyboard users (better UX). Mouse users don't see it.

---

### ❌ Pitfall 7: Icon Overlap with Text

**Wrong**:
```css
.ps-input {
  padding: var(--size-10); /* 40px - same as icon width + offset */
}
```

**Correct**:
```css
.ps-input {
  padding-left: var(--size-8); /* 32px = icon 20px + offset 12px */
}
```

**Why**: Icon is 20px wide + 12px offset = 32px minimum. Use `var(--size-8)` to avoid overlap.

---

## Validation Checklist

Before committing a new form component:

```
Implementation
[ ] 4 files created: .twig, .css, .yml, .stories.jsx
[ ] Twig includes .form-control class natively
[ ] CSS has combined selectors (.ps-* + .form-control)
[ ] YAML includes Real Estate mock data
[ ] Stories include 7+ variations with tags: ['autodocs']

Code Quality
[ ] No hardcoded colors/sizes (all tokens)
[ ] No arrow functions in Twig (use filters/ternary)
[ ] All interactive elements have :focus-visible
[ ] Modifiers work independently (no combos)
[ ] No border-radius unless spec explicitly requires it

Composition (Molecules only)
[ ] Includes Atoms via {% include %} with only
[ ] Does NOT add .addClass('form-control')
[ ] Form Field CSS handles presentation only
[ ] No duplication of Atom styles

Testing
[ ] npm run build passes (0 errors)
[ ] Storybook renders all 30+ stories correctly
[ ] Conformity audit: 100% score
[ ] Manual visual check against maquette

Git
[ ] Meaningful commit message (feat/fix scope)
[ ] CHANGELOG.md updated with entry
```

---

## Example: Complete Checkbox Atom Implementation

**Scenario**: You need to implement a Checkbox form control.

### 1. Create checkbox.twig

```twig
{#
  Checkbox Form Control Atom
  ==========================================
  Self-sufficient checkbox element with native .form-control support
  
  Context:
  - id (string): Input id for label association
  - label (string): Checkbox label text
  - checked (boolean): Is checkbox checked
  - attributes (object): Drupal attributes
#}

{%- set classes = ['ps-checkbox', 'form-control'] -%}

<div class="ps-checkbox-wrapper">
  <input
    id="{{ id }}"
    class="{{ classes | join(' ') }}"
    type="checkbox"
    {% if checked %}checked{% endif %}
    {{ attributes }}
  />
  <label for="{{ id }}" class="ps-checkbox-label">
    {{ label }}
  </label>
</div>
```

### 2. Create checkbox.css

```css
.ps-checkbox,
.form-control:is(input[type="checkbox"]) {
  --ps-checkbox-size: var(--size-5);
  --ps-checkbox-border-color: var(--border-default);
  --ps-checkbox-bg-color: var(--white);
  --ps-checkbox-transition: border-color var(--duration-2) var(--ease-out);

  width: var(--ps-checkbox-size);
  height: var(--ps-checkbox-size);
  border: 1px solid var(--ps-checkbox-border-color);
  border-radius: 2px;
  background-color: var(--ps-checkbox-bg-color);
  transition: var(--ps-checkbox-transition);
  cursor: pointer;

  &:focus-visible {
    outline: 2px solid var(--border-focus);
    outline-offset: 2px;
  }

  &:checked {
    --ps-checkbox-border-color: var(--primary);
    --ps-checkbox-bg-color: var(--primary);
  }
}

.ps-checkbox-label {
  display: inline-block;
  margin-left: var(--size-3);
  cursor: pointer;
  user-select: none;
  color: var(--text-primary);
}
```

### 3. Create checkbox.yml

```yaml
checkbox_unchecked:
  id: 'agree-terms'
  label: 'I agree to the terms and conditions'
  checked: false

checkbox_checked:
  id: 'agree-terms'
  label: 'I agree to the terms and conditions'
  checked: true
```

### 4. Create checkbox.stories.jsx

```jsx
import { default as CheckboxAtom } from './checkbox.twig';
import checkboxData from './checkbox.yml';

export default {
  title: 'Elements/Checkbox',
  component: CheckboxAtom,
  tags: ['autodocs']
};

export const Unchecked = {
  args: {
    id: checkboxData.checkbox_unchecked.id,
    label: checkboxData.checkbox_unchecked.label,
    checked: false
  }
};

export const Checked = {
  args: {
    id: checkboxData.checkbox_checked.id,
    label: checkboxData.checkbox_checked.label,
    checked: true
  }
};

export const Disabled = {
  args: {
    id: 'disabled-checkbox',
    label: 'Disabled checkbox',
    checked: false,
    attributes: { disabled: true }
  }
};
```

### 5. Validate & Commit

```bash
npm run build  # ✓ 0 errors
git add -A
git commit -m "feat(elements): Add checkbox form control atom

- Self-sufficient checkbox with native .form-control support
- Dual CSS selectors for .ps-checkbox and .form-control variants
- 4 stories: default, checked, disabled, RTL support
- References form development guide"
```

---

## Resources & References

- **Copilot Instructions**: `.github/copilot-instructions.md` (Form Components Standards section)
- **Refactor Plan**: `FORM_ATOMS_REFACTOR_PLAN.md` (Architecture decisions)
- **Session Summary**: `DAILY_WORK_SUMMARY.md` (Implementation details)
- **Design Tokens**: `source/props/` (colors.css, sizes.css, fonts.css)
- **Reference Components**: `source/patterns/elements/input/` (complete example)

---

## Questions & Support

For questions about form component development:

1. Check this guide first (likely covered in Common Pitfalls section)
2. Consult copilot-instructions.md Form Components Standards
3. Review existing form Atoms (input, textarea, select) for patterns
4. Ask Design System Team for clarification

---

**Last Updated**: December 14, 2025  
**Maintained By**: Design System Team  
**Version**: 1.0.0
