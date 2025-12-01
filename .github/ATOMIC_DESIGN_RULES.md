# Atomic Design Methodology - PS Theme Implementation Rules

**Version**: 1.0.0  
**Date**: 2025-12-01  
**Status**: 🔒 **MANDATORY REFERENCE - STRICT APPLICATION REQUIRED**

---

## 📚 Table of Contents

1. [Atomic Design Principles](#1-atomic-design-principles)
2. [Hierarchy & Composition](#2-hierarchy--composition)
3. [Component Analysis Workflow](#3-component-analysis-workflow)
4. [Atoms (Elements)](#4-atoms-elements)
5. [Molecules (Components)](#5-molecules-components)
6. [Organisms (Collections)](#6-organisms-collections)
7. [Templates (Layouts)](#7-templates-layouts)
8. [Pages](#8-pages)
9. [Single Responsibility Principle](#9-single-responsibility-principle)
10. [Composition Before Creation](#10-composition-before-creation)
11. [Component Reusability Matrix](#11-component-reusability-matrix)
12. [Practical Examples](#12-practical-examples)
13. [Anti-Patterns to Avoid](#13-anti-patterns-to-avoid)

---

## 1. Atomic Design Principles

### Core Philosophy

> **"In interfaces, molecules are relatively simple groups of UI elements functioning together as a unit."**  
> — Brad Frost, Atomic Design

Atomic Design is a methodology for creating interface design systems by thinking of UIs as:
- **A cohesive whole** (pages, templates, organisms)
- **A collection of parts** (molecules, atoms)

### The Five Stages

```
Atoms → Molecules → Organisms → Templates → Pages
  ↓         ↓            ↓           ↓          ↓
Basic   Combinations  Sections    Layout    Content
```

### Non-Linear Process

**⚠️ CRITICAL**: Atomic Design is **NOT** a linear waterfall process.

```
❌ WRONG: Step 1 Atoms → Step 2 Molecules → Step 3 Organisms

✅ CORRECT: Concurrent mental model
   - Design pages with real content
   - Identify organisms (sections)
   - Break down into molecules (groups)
   - Extract atoms (elements)
   - Refine at all levels simultaneously
```

---

## 2. Hierarchy & Composition

### PS Theme Structure

```
source/patterns/
├── elements/      → Atoms (19 total)
│   ├── button/
│   ├── icon/
│   ├── label/
│   ├── field/     ← Base input WITHOUT label
│   └── ...
├── components/    → Molecules (20 total)
│   ├── form-field/ ← Combines label + field + helper/error
│   ├── dropdown/
│   └── ...
├── collections/   → Organisms (12 total)
├── layouts/       → Templates (8 total)
└── pages/         → Pages (8 total)
```

### Composition Rules

**Rule 1**: Molecules **MUST** be composed of existing Atoms

```twig
{# ✅ CORRECT: FormField uses existing Field atom #}
{% include '@elements/field/field.twig' with field %}

{# ❌ WRONG: Recreating field markup inside FormField #}
<input type="text" class="ps-form-field__input" />
```

**Rule 2**: Organisms **MUST** be composed of Molecules and/or Atoms

```twig
{# ✅ CORRECT: Header organism uses existing molecules #}
{% include '@components/search-form/search-form.twig' %}
{% include '@components/nav-primary/nav-primary.twig' %}
{% include '@elements/logo/logo.twig' %}

{# ❌ WRONG: Header recreates everything internally #}
```

**Rule 3**: Each level adds **meaning and context**, not just markup

```
Atom (field):     Base input - reusable, no context
Molecule (form-field): Adds label, helper, error - form context
Organism (form):  Groups related fields - business logic context
```

---

## 3. Component Analysis Workflow

### Before Creating ANY Component Above Atom Level

**MANDATORY 4-STEP PROCESS:**

#### Step 1: Identify Required Atoms

```markdown
Question: "What are the smallest functional pieces?"

Example (FormField molecule):
- Label text (atom: label or just text)
- Input field (atom: field)
- Helper text (atom: text/paragraph)
- Error message (atom: text/paragraph with icon)
- Required indicator (atom: icon or text "*")
```

#### Step 2: Check Existing Atoms

```bash
# Search existing atoms
ls source/patterns/elements/

# Search for similar patterns
grep -r "label" source/patterns/elements/
```

**Decision Matrix:**

| Atom Exists? | Fits Need? | Action |
|--------------|------------|--------|
| ✅ Yes | ✅ Yes | **REUSE** via include |
| ✅ Yes | ❌ No | **EXTEND** atom with new variant/modifier |
| ❌ No | — | **CREATE** new atom first |

#### Step 3: Analyze Composition Strategy

```markdown
Questions to ask:
1. Which atoms need to be grouped together?
2. What's the relationship between these atoms?
3. What new functionality emerges from this combination?
4. Does this combination have a single clear purpose?
5. Can this combination be reused elsewhere?
```

**Example Analysis (FormField):**

```yaml
Purpose: "Group label, field, and contextual text for forms"

Composition:
  - Label (atom) → provides field description
  - Field (atom) → accepts user input
  - Helper (atom text) → provides guidance
  - Error (atom text) → shows validation feedback

Relationship: 
  - Label associates via for/id
  - Helper/error conditionally displayed
  - Error state cascades to field

Emergent Functionality:
  - Accessibility (label association)
  - Validation feedback
  - Form semantics
  - Consistent field patterns

Reusability: HIGH
  - Contact forms
  - Login forms
  - Settings pages
  - Any form input
```

#### Step 4: Document Composition

**In component README.md**, add:

```markdown
## Composition

This component is built from the following atoms:

- **Label** (`@elements/label/label.twig`) - Field label text
- **Field** (`@elements/field/field.twig`) - Input/textarea/select element
- **Text** (native HTML) - Helper and error messages
- **Icon** (`@elements/icon/icon.twig`) - Required indicator (*)

### Why These Atoms?

FormField follows the single responsibility principle: it ONLY handles
the composition and coordination of these atoms to create form field
semantics. It does NOT reimplement field functionality.

### Reuse Strategy

All field behavior (types, icons, disabled state) is delegated to the
Field atom via pass-through props. FormField adds the wrapper context:
label association, helper text, and error handling.
```

---

## 4. Atoms (Elements)

### Definition

> **"Atoms are UI elements that can't be broken down any further without ceasing to be functional."**

### Characteristics

- **Indivisible**: Breaking down further would lose functionality
- **Basic HTML**: Often map 1:1 with HTML elements
- **No composition**: Don't include other atoms
- **Intrinsic properties**: Have their own unique styles
- **Demonstrate base styles**: Show all variants at a glance

### PS Theme Atoms

| Atom | Purpose | Key Variants |
|------|---------|--------------|
| `button` | Click actions | primary, secondary, sizes |
| `field` | Input (NO label) | text, email, number, textarea |
| `label` | Standalone label | required, disabled |
| `icon` | Icon font glyph | size, color |
| `badge` | Status indicator | semantic colors, sizes |
| `avatar` | User image | sizes, shapes, fallback |
| `divider` | Visual separator | orientation |

### Atom Creation Checklist

- [ ] Maps to a basic HTML element or irreducible component
- [ ] Has intrinsic visual properties (size, color, spacing)
- [ ] Can stand alone (but may be useless without context)
- [ ] Does NOT include other pattern components
- [ ] Demonstrates all base style variants
- [ ] Highly reusable across many contexts

---

## 5. Molecules (Components)

### Definition

> **"Molecules are relatively simple groups of UI elements functioning together as a unit."**

### Characteristics

- **Composition**: Combine 2+ atoms
- **Purpose**: Take on distinct new properties as a group
- **Function**: Do one thing well (single responsibility)
- **Portable**: Can be dropped anywhere needed
- **Context**: Atoms gain meaning when combined

### Key Insight: Labels Define Inputs

```twig
{# ❌ WRONG: Field atom includes label internally #}
<div class="ps-field">
  <label>Email</label>
  <input type="email" />
</div>

{# ✅ CORRECT: FormField molecule combines separate atoms #}
<div class="ps-form-field">
  <label class="ps-form-field__label">Email</label>
  <div class="ps-form-field__input-wrapper">
    {% include '@elements/field/field.twig' %}
  </div>
</div>
```

**Why?** The `field` atom is the base input. The `form-field` molecule adds the **context** (label, helper, error) that gives the field **meaning** in a form.

### Molecule Examples

| Molecule | Atoms Used | Purpose |
|----------|------------|---------|
| `form-field` | label + field + text | Form input with label & validation |
| `search-form` | label + field + button | Search functionality |
| `card` | heading + text + image + button | Content preview |
| `breadcrumb` | link + icon + text | Navigation path |

### Molecule Creation Checklist

- [ ] Combines existing atoms (not recreating markup)
- [ ] Single clear purpose
- [ ] Emergent functionality (atoms gain meaning together)
- [ ] Reusable across multiple contexts
- [ ] Simple enough to test easily
- [ ] Pass-through props to child atoms
- [ ] Documents which atoms are used

---

## 6. Organisms (Collections)

### Definition

> **"Organisms are relatively complex UI components composed of groups of molecules and/or atoms."**

### Characteristics

- **Complexity**: More elaborate than molecules
- **Sections**: Form distinct interface sections
- **Repetition**: May repeat same molecule multiple times
- **Context**: Demonstrate molecules in action
- **Patterns**: Create reusable section patterns

### Examples

**Header Organism:**
```
Header (organism)
├── Logo (atom)
├── Primary Nav (molecule)
│   ├── Nav Item (atom link)
│   └── Nav Item (atom link)
└── Search Form (molecule)
    ├── Label (atom)
    ├── Field (atom)
    └── Button (atom)
```

**Product Grid Organism:**
```
Product Grid (organism)
├── Product Card (molecule) × N
│   ├── Image (atom)
│   ├── Heading (atom)
│   ├── Text (atom)
│   └── Button (atom)
```

### Organism Checklist

- [ ] Composes multiple molecules/atoms
- [ ] Forms a distinct section of interface
- [ ] Provides context for child components
- [ ] Reusable pattern (header, footer, sidebar, grid)
- [ ] Documents all molecules/atoms used
- [ ] Demonstrates children in action

---

## 7. Templates (Layouts)

### Definition

> **"Templates are page-level objects that place components into a layout."**

### Characteristics

- **Layout focus**: Arrange organisms/molecules in grid
- **Content structure**: Define skeleton, NOT final content
- **Placeholders**: Use Lorem ipsum, image dimensions
- **Articulate structure**: Show character limits, image sizes

### Content Structure vs Content

```html
<!-- ✅ TEMPLATE: Shows structure -->
<h1>Lorem ipsum dolor sit amet (max 60 characters)</h1>
<img src="placeholder-1200x600.jpg" alt="Feature image" />
<p>Lorem ipsum dolor sit... (2-3 paragraphs, 150 chars each)</p>

<!-- ✅ PAGE: Shows real content -->
<h1>New Waterfront Property Available Downtown</h1>
<img src="property-123-waterfront.jpg" alt="Waterfront property" />
<p>Discover this stunning 3-bedroom apartment...</p>
```

### Why Content Structure Matters

> **"You can create good experiences without knowing the content. What you can't do is create good experiences without knowing your content structure."**  
> — Mark Boulton

### Template Checklist

- [ ] Demonstrates organisms in layout context
- [ ] Shows content structure (lengths, sizes)
- [ ] Uses placeholder content
- [ ] Defines grid/layout system
- [ ] Articulates responsive behavior
- [ ] Provides guardrails for dynamic content

---

## 8. Pages

### Definition

> **"Pages are specific instances of templates with real representative content."**

### Characteristics

- **Real content**: Actual text, images, media
- **Testing ground**: Validate design system effectiveness
- **Sign-off stage**: What stakeholders approve
- **Variations**: Show edge cases (1 item vs 100 items)

### Content Variations to Test

```markdown
1. Length variations:
   - Short headline (20 chars) vs long headline (200 chars)
   - 1 item in cart vs 50 items
   - Empty state vs full state

2. User variations:
   - Admin user vs regular user
   - First-time user vs returning user
   - Logged in vs logged out

3. Content variations:
   - With image vs without image
   - With video vs without video
   - Full bio vs minimal bio
```

### Page Checklist

- [ ] Uses template as base
- [ ] Real representative content
- [ ] Tests edge cases (empty, full, extremes)
- [ ] Validates component resilience
- [ ] Ready for stakeholder review
- [ ] Loop back to fix molecules/organisms if needed

---

## 9. Single Responsibility Principle

### Definition

> **"Do one thing and do it well."**

### Application to Components

**Atoms**: Single HTML element or irreducible function
```css
/* ✅ Button atom: ONLY handles button styles */
.ps-button { }

/* ❌ Button atom including form layout */
.ps-button-group { } /* This is a molecule */
```

**Molecules**: Single group function
```twig
{# ✅ FormField: ONLY label + field + helper/error composition #}
{# Delegates field behavior to Field atom #}

{# ❌ FormField with validation logic, submission, etc. #}
{# That's an organism or page-level concern #}
```

### Benefits

1. **Testing**: Simple components → simple tests
2. **Reusability**: Single purpose → more use cases
3. **Consistency**: One way to do one thing
4. **Maintainability**: Small scope → easy to change
5. **Composability**: Simple parts → complex wholes

---

## 10. Composition Before Creation

### The Golden Rule

> **"Before creating ANY component above atom level, analyze what atoms can be composed to build it."**

### Workflow Enforcement

```bash
# 1. Analyze requested component
Component: "FormField molecule"
Purpose: "Input with label and validation"

# 2. List required atoms
- Label text
- Input field
- Helper text
- Error message
- Required indicator

# 3. Check existing atoms
$ ls source/patterns/elements/
button/ field/ label/ icon/ ... ✅

# 4. Decision
Atoms exist → COMPOSE them
No label atom → Field IS the input only
Label is added by FormField molecule

# 5. Implementation
FormField includes Field atom
FormField adds label, helper, error wrapper
```

### Composition Patterns

**Pattern 1: Direct Include**
```twig
{# Molecule includes atom directly #}
{% include '@elements/button/button.twig' with buttonProps %}
```

**Pattern 2: Pass-Through Props**
```twig
{# Molecule passes props to atom #}
{% set fieldProps = field|merge({ id: id, disabled: disabled }) %}
{% include '@elements/field/field.twig' with fieldProps %}
```

**Pattern 3: Wrapper + Include**
```twig
{# Molecule adds context around atom #}
<div class="ps-form-field__input-wrapper">
  {% include '@elements/field/field.twig' with field %}
</div>
```

### Anti-Pattern: Duplication

```twig
{# ❌ WRONG: Recreating atom markup #}
<div class="ps-form-field">
  <label>Email</label>
  <input type="text" class="ps-field__input" /> {# Duplicates field atom #}
</div>

{# ✅ CORRECT: Composing atoms #}
<div class="ps-form-field">
  <label class="ps-form-field__label">Email</label>
  {% include '@elements/field/field.twig' %}
</div>
```

---

## 11. Component Reusability Matrix

### Measuring Reusability

| Level | Reusability | Context | Examples |
|-------|-------------|---------|----------|
| **Atoms** | 🔴 **Highest** | No context | button, icon, field |
| **Molecules** | 🟡 **High** | Basic context | form-field, search-form |
| **Organisms** | 🟢 **Medium** | Section context | header, footer, product-grid |
| **Templates** | 🔵 **Low** | Page context | homepage, article, listing |
| **Pages** | ⚫ **Single-use** | Specific content | Homepage 2024 Q4 promo |

### Reusability Guidelines

**Atoms: Maximum Reusability**
```yaml
Usage count target: 50+ instances
Contexts: Anywhere UI needs basic element
Changes: Minimal (affects everything)
```

**Molecules: High Reusability**
```yaml
Usage count target: 10-20 instances
Contexts: Multiple sections, pages
Changes: Moderate impact
```

**Organisms: Selective Reusability**
```yaml
Usage count target: 3-5 instances
Contexts: Similar sections across pages
Changes: Localized impact
```

---

## 12. Practical Examples

### Example 1: FormField Molecule (CORRECT Implementation)

**Composition Analysis:**
```
FormField molecule NEEDS:
├── Label (text) → Use native <label>
├── Field (input) → ✅ EXISTS: @elements/field/field.twig
├── Helper text → Use native <div>
├── Error text → Use native <div>
└── Required indicator → Use <span>*</span>

Decision: COMPOSE using existing Field atom
```

**Implementation:**
```twig
{# form-field.twig - Molecule #}
<div class="ps-form-field">
  <label class="ps-form-field__label" for="{{ id }}">
    {{ label }}
    {% if required %}<span>*</span>{% endif %}
  </label>
  
  <div class="ps-form-field__input-wrapper">
    {# ✅ COMPOSITION: Include existing Field atom #}
    {% include '@elements/field/field.twig' with field|merge({ 
      attributes: create_attribute({ 'id': id })
    }) %}
  </div>
  
  {% if helperText %}
    <div class="ps-form-field__helper">{{ helperText }}</div>
  {% endif %}
  
  {% if error %}
    <div class="ps-form-field__error">{{ error }}</div>
  {% endif %}
</div>
```

### Example 2: SearchForm Molecule

**Composition Analysis:**
```
SearchForm molecule NEEDS:
├── Label → ✅ EXISTS: Native <label> or @elements/label/
├── Field → ✅ EXISTS: @elements/field/field.twig
└── Button → ✅ EXISTS: @elements/button/button.twig

Decision: COMPOSE all three atoms
```

**Implementation:**
```twig
{# search-form.twig - Molecule #}
<form class="ps-search-form" role="search">
  <label class="ps-search-form__label" for="search-input">
    {{ label|default('Search') }}
  </label>
  
  {% include '@elements/field/field.twig' with {
    type: 'search',
    id: 'search-input',
    placeholder: placeholder,
    icon: 'search',
    iconPosition: 'left'
  } %}
  
  {% include '@elements/button/button.twig' with {
    type: 'submit',
    variant: 'primary',
    text: buttonText|default('Search')
  } %}
</form>
```

### Example 3: Header Organism

**Composition Analysis:**
```
Header organism NEEDS:
├── Logo → ✅ EXISTS: @elements/logo/ or @elements/image/
├── Primary Nav → ⚠️ NEEDS MOLECULE: nav-primary
│   └── Nav items → ✅ EXISTS: @elements/link/
└── Search Form → ✅ EXISTS: @components/search-form/ (from Example 2)

Decision: CREATE nav-primary molecule first, then compose organism
```

---

## 13. Anti-Patterns to Avoid

### ❌ Anti-Pattern 1: Monolithic Components

```twig
{# ❌ WRONG: FormField recreates field markup internally #}
<div class="ps-form-field">
  <label>Email</label>
  <input type="text" class="some-custom-input" />
  <span class="error">Error</span>
</div>
```

**Why wrong?** Breaks reusability, duplicates field styles, violates composition.

**Fix:**
```twig
{# ✅ CORRECT: FormField composes Field atom #}
{% include '@elements/field/field.twig' with field %}
```

### ❌ Anti-Pattern 2: Premature Abstraction

```twig
{# ❌ WRONG: Creating "form-input-email" molecule #}
{# Too specific, not reusable #}
```

**Fix:** Use `form-field` molecule with `type: 'email'` prop.

### ❌ Anti-Pattern 3: Skipping Atoms

```twig
{# ❌ WRONG: Molecule combines markup without atom foundation #}
<div class="ps-card">
  <h3>Title</h3>
  <p>Text</p>
  <button>Action</button>
</div>
```

**Fix:**
```twig
{# ✅ CORRECT: Molecule composes atoms #}
<div class="ps-card">
  {% include '@elements/heading/heading.twig' with { level: 3, text: title } %}
  {% include '@elements/text/text.twig' with { content: text } %}
  {% include '@elements/button/button.twig' with button %}
</div>
```

### ❌ Anti-Pattern 4: Wrong Hierarchy

```
❌ Atom includes Molecule
❌ Molecule includes Organism
❌ Template includes Page
```

**Correct flow:**
```
Atoms → Molecules → Organisms → Templates → Pages
```

### ❌ Anti-Pattern 5: God Components

```twig
{# ❌ WRONG: FormField handling validation, submission, state #}
{# That's organism or page-level logic #}
```

**Single responsibility:** FormField ONLY composes label + field + helper/error.

---

## 📋 Quick Reference Checklist

### Before Creating Any Component

- [ ] **Read** design specs completely
- [ ] **Identify** required atoms (smallest pieces)
- [ ] **Check** existing atoms (`ls source/patterns/elements/`)
- [ ] **Decide** reuse vs extend vs create
- [ ] **Analyze** composition strategy (which atoms, why, how)
- [ ] **Document** composition in README
- [ ] **Implement** via `{% include %}` not duplication
- [ ] **Test** reusability in multiple contexts
- [ ] **Validate** single responsibility maintained

### Component Level Decisions

```
Is it basic HTML? → Atom
Combines 2-3 atoms? → Molecule
Complex section? → Organism
Page layout? → Template
Real content? → Page
```

---

## 📚 Additional Reading

- [Atomic Design by Brad Frost](https://atomicdesign.bradfrost.com/)
- [Single Responsibility Principle](https://en.wikipedia.org/wiki/Single_responsibility_principle)
- [The Shape of Design - Frank Chimero](http://read.shapeofdesignbook.com/)
- [Structure First, Content Always - Mark Boulton](http://www.markboulton.co.uk/journal/structure-first-content-always)

---

**This document is the MANDATORY reference for all component development in PS Theme. Zero tolerance for violations.**
