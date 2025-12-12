---
title: Atomic Design Methodology
version: 3.0.0
lastUpdated: 2025-12-05
applyTo:
  - "source/patterns/**/*"
priority: CRITICAL
related:
  - composition-token-first.instructions.md
  - components.instructions.md
status: ACTIVE
---

# Atomic Design Methodology - PS Theme

**Scope**: Component composition & reusability rules

> **🧩 This file covers WHY and WHEN to compose components** (design philosophy).  
> For technical implementation details (BEM, file structure, markup), see: [components.instructions.md](components.instructions.md)

---

## 🎯 Core Philosophy

> **"Interfaces are a cohesive whole composed of parts that can be broken down into smaller parts."**  
> — Brad Frost, Atomic Design

Atomic Design is NOT a linear process—it's a mental model for:
- Designing pages with real content
- Identifying sections (organisms)
- Breaking down into groups (molecules)
- Extracting basic elements (atoms)
- Refining at all levels simultaneously

---

## 📊 Five-Level Hierarchy

```
Atoms → Molecules → Organisms → Templates → Pages
  ↓         ↓            ↓           ↓          ↓
Basic   Combinations  Sections    Layout    Content
```

### PS Theme Implementation

| Level | Directory | Count | Purpose | Examples |
|-------|-----------|-------|---------|----------|
| **Atoms** | `elements/` | 19 | Indivisible building blocks | button, icon, label, badge, divider |
| **Molecules** | `components/` | 20 | Groups of atoms functioning together | form-field, card, breadcrumb, dropdown |
| **Organisms** | `collections/` | 12 | Complex UI sections | header, footer, product-grid, navigation |
| **Templates** | `layouts/` | 8 | Page-level layouts | homepage, article, listing-page |
| **Pages** | `pages/` | 8 | Specific template instances | Homepage with real content |

---

## 🔒 Composition Rules (MANDATORY)

### Token-First Workflow for Composition

**📘 Complete workflow**: [composition-token-first.instructions.md](composition-token-first.instructions.md)

**4-step cascade**: params → utils → **override tokens** ⭐ (preferred) → targeted CSS (last resort)

**Applies to**: Molecules, Organisms, Templates, Pages | **NOT Atoms**

---

### Rule 1: Atoms Are Indivisible

**Atoms MUST**:
- Map to basic HTML elements or irreducible components
- Have intrinsic visual properties (size, color, spacing)
- Stand alone (but may be useless without context)
- NEVER include other pattern components
- Demonstrate all base style variants
- **❌ Token-First workflow does NOT apply** (atoms are autonomous)

```twig
{# ✅ CORRECT - Button atom (standalone) #}
<button class="ps-button ps-button--primary">
  <span class="ps-button__icon" data-icon="check"></span>
  <span class="ps-button__text">{{ text }}</span>
</button>

{# ❌ WRONG - Button including badge (composition = molecule) #}
<button class="ps-button">
  {% include '@elements/badge/badge.twig' %}
</button>
```

**CSS Pattern** (atoms expose tokens for others to override):

```css
/* button.css - ATOM */
.ps-button {
  /* Expose tokens for consuming components to override */
  --ps-button-padding-x: var(--size-3);
  --ps-button-padding-y: var(--size-2);
  --ps-button-font-size: var(--font-size-1);
  
  /* No token overrides from other components */
}
```

---

### Rule 2: Molecules Compose Atoms

**Molecules MUST**:
- Combine 2+ atoms via `{% include %}`
- Add context and meaning to atoms
- Do ONE thing well (single responsibility)
- Be portable (drop anywhere needed)
- **✅ FOLLOW Token-First workflow** (see `composition-token-first.instructions.md`)

```twig
{# ✅ CORRECT - FormField molecule composes atoms #}
<div class="ps-form-field">
  {% include '@elements/label/label.twig' with {
    text: label,
    forId: id,
    required: required
  } only %}
  
  {% include '@elements/field/field.twig' with {
    type: type,
    id: id,
    name: name,
    value: value
  } only %}
  
  {% if helper_text %}
    {% include '@elements/text/text.twig' with {
      text: helper_text,
      size: 'small',
      muted: true
    } only %}
  {% endif %}
</div>

{# ❌ WRONG - FormField duplicates atom markup #}
<div class="ps-form-field">
  <label class="ps-label">{{ label }}</label>
  <input class="ps-field" type="{{ type }}" />
  <span class="ps-text ps-text--small">{{ helper_text }}</span>
</div>
```

**CSS Pattern** (molecules override parent/child tokens):

```css
/* card.css - MOLECULE composing atoms */
.ps-card {
  /* ═══ STEP 3: Override child atoms tokens (Token-First) ═══ */
  --ps-badge-font-size: var(--font-size-0);
  --ps-button-size: var(--size-6);
  --ps-heading-margin-bottom: var(--size-2);
  
  /* ═══ Own component tokens ═══ */
  --ps-card-padding: var(--size-6);
  --ps-card-gap: var(--size-4);
  
  /* Own styles */
  padding: var(--ps-card-padding);
  display: flex;
  gap: var(--ps-card-gap);
}
```

---

### Rule 3: Organisms Compose Molecules + Atoms

**Organisms MUST**:
- Assemble molecules and/or atoms into sections
- Add complex layout and logic
- Represent distinct sections of interface
- **✅ FOLLOW Token-First workflow** (see `composition-token-first.instructions.md`)

```twig
{# ✅ CORRECT - Header organism composes molecules #}
<header class="ps-header">
  {% include '@elements/logo/logo.twig' %}
  
  {% include '@components/search-form/search-form.twig' with {
    placeholder: 'Search properties...'
  } only %}
  
  {% include '@components/nav-primary/nav-primary.twig' with {
    items: nav_items
  } only %}
</header>

{# ❌ WRONG - Header recreates molecule markup #}
<header class="ps-header">
  <form class="ps-search-form">
    <!-- Duplicating search-form internals -->
  </form>
</header>
```

**CSS Pattern** (organisms override multiple components):

```css
/* header.css - ORGANISM composing molecules */
.ps-header {
  /* ═══ STEP 3: Override search-form tokens ═══ */
  --ps-search-form-width: 100%;
  --ps-search-form-max-width: 500px;
  
  /* ═══ STEP 3: Override nav-primary tokens ═══ */
  --ps-nav-primary-gap: var(--size-4);
  --ps-nav-primary-font-size: var(--font-size-1);
  
  /* Own layout */
  display: grid;
  grid-template-columns: auto 1fr auto;
  gap: var(--size-6);
}
```

---

## 🛠 Composition Before Creation (4-Step Workflow)

**⚠️ MANDATORY**: Before creating ANY component above atom level:

### Step 1: Identify Required Atoms

Ask: "What are the smallest functional pieces?"

**Example (Card molecule)**:
- Image → `@elements/image/image.twig`
- Badge/tag → `@elements/badge/badge.twig`
- Heading → `@elements/heading/heading.twig`
- Text/description → `@elements/text/text.twig`
- Button/CTA → `@elements/button/button.twig`

### Step 2: Check Existing Atoms

```bash
# List all available atoms
ls source/patterns/elements/

# Search for similar patterns
grep -r "label" source/patterns/elements/
```

**Decision Matrix**:

| Atom Exists? | Fits Need? | Action |
|--------------|------------|--------|
| ✅ Yes | ✅ Yes | **REUSE** via include |
| ✅ Yes | ❌ No | **EXTEND** atom (add variant/modifier) |
| ❌ No | — | **CREATE** new atom FIRST |

### Step 3: Analyze Composition Strategy

**Questions to ask**:
1. Which atoms need grouping?
2. What's the relationship between them?
3. What new functionality emerges from this combination?
4. Does this have a single clear purpose?
5. Can this combination be reused elsewhere?
6. **How will I customize composed atoms?** → Use Token-First workflow (see `composition-token-first.instructions.md`)

**Example Analysis (FormField)**:

```yaml
Purpose: "Group label, field, and contextual text for forms"

Atoms Required:
  - Label: Provides field description
  - Field: Accepts user input
  - Text: Shows helper/error messages

Relationship:
  - Label associates via for/id
  - Helper/error displayed conditionally
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

### Step 4: Document Composition

In component `README.md`, add:

```markdown
## Composition

This component is built from the following atoms:

- **Label** (`@elements/label/label.twig`) - Field label text
- **Field** (`@elements/field/field.twig`) - Input element
- **Text** (`@elements/text/text.twig`) - Helper and error messages

### Why These Atoms?

FormField follows the single responsibility principle: it ONLY handles
the composition and coordination of these atoms to create form field
semantics. It does NOT reimplement field functionality.

### Reuse Strategy

All field behavior (types, disabled state) is delegated to the Field
atom via pass-through props. FormField adds wrapper context: label
association, helper text, and error handling.
```

---

## 📋 Component Creation Checklist

Before creating/validating a molecule, organism, or template:

- [ ] **Identify atoms**: List all required atomic components
- [ ] **Check availability**: Verify atoms exist in `elements/`
- [ ] **Verify conformity**: Ensure atoms support `attributes` parameter
- [ ] **Plan composition**: Map props from molecule → atoms
- [ ] **Document strategy**: Add "Composition" section to README
- [ ] **Avoid duplication**: NEVER recreate atom markup
- [ ] **CSS scope**: Only layout/positioning, NO atom styles
- [ ] **Test includes**: Verify `{% include %}` works with `only`

---

## 🚫 Anti-Patterns to Avoid

> **For BEM naming and markup standards**, see: [components.instructions.md](components.instructions.md)

### 1. Duplicating Atom Markup in Molecule

```twig
{# ❌ WRONG - Card recreates button atom #}
<div class="ps-card">
  <button class="ps-button ps-button--primary">
    {{ cta_text }}
  </button>
</div>

{# ✅ CORRECT - Card composes button atom #}
<div class="ps-card">
  {% include '@elements/button/button.twig' with {
    text: cta_text,
    color: 'primary'
  } only %}
</div>
```

### 2. Styling Atoms in Molecule CSS

```css
/* ❌ WRONG - Card CSS styles button internals */
.ps-card__cta {
  background: var(--primary);
  padding: var(--size-3) var(--size-5);
  border-radius: var(--radius-2);
}

/* ✅ CORRECT - Card CSS only positions button */
.ps-card__cta {
  margin-top: auto; /* Layout only */
  align-self: flex-start; /* Position only */
}
```

### 3. Over-Nesting Components

```twig
{# ❌ WRONG - Molecule includes another molecule #}
{# (Unless truly necessary and documented) #}
<div class="ps-molecule-a">
  {% include '@components/molecule-b/molecule-b.twig' %}
</div>

{# ✅ CORRECT - Molecule includes only atoms #}
<div class="ps-molecule-a">
  {% include '@elements/atom-x/atom-x.twig' %}
  {% include '@elements/atom-y/atom-y.twig' %}
</div>
```

**Exception**: Organisms CAN include molecules (that's their purpose).

### 4. Atoms Including Other Atoms (Special Cases)

**General Rule**: Atoms should be indivisible and self-contained.

**Exception**: Atoms CAN include **rendering system atoms** (icons, flags) when:
1. The included atom is purely presentational (no logic/state)
2. The included atom is a symbol/glyph system (icon font, SVG sprite, flag system)
3. The parent atom manages the semantic context

```twig
{# ✅ ALLOWED - Button atom includes icon system #}
<button class="ps-button">
  {% if icon_start %}
    {% include '@elements/icon/icon.twig' with {
      icon: icon_start,
      size: 'inherit'
    } only %}
  {% endif %}
  <span class="ps-button__text">{{ text }}</span>
</button>
```

**Why allowed?** Icon atom is a **rendering system** (displays glyph via CSS), not a full component. Button maintains semantic control.

**❌ NOT ALLOWED - Atoms including functional atoms**:
```twig
{# ❌ WRONG - Button includes badge (functional component) #}
<button class="ps-button">
  {% include '@elements/badge/badge.twig' with { text: '3' } %}
  {{ text }}
</button>
{# This creates a molecule, not an atom #}
```

**Decision Matrix**:

| Included Atom Type | Allowed in Atom? | Reason |
|-------------------|------------------|---------|
| Icon system | ✅ Yes | Presentational glyph renderer |
| Flag system | ✅ Yes | Symbol/image display |
| Badge | ❌ No | Functional component with state |
| Label | ❌ No | Semantic form element |
| Button | ❌ No | Interactive component |

**Alternative for non-rendering atoms**:
```twig
{# ✅ CORRECT - Render icon internally (no include) #}
<button class="ps-button">
  {% if icon_start %}
    <span class="ps-button__icon" data-icon="{{ icon_start }}"></span>
  {% endif %}
  <span class="ps-button__text">{{ text }}</span>
</button>
```

### 5. Skipping Atom Creation

```twig
{# ❌ WRONG - Molecule creates new "atom" inline #}
<div class="ps-alert">
  <svg class="ps-alert__icon">...</svg>
  <h3 class="ps-alert__title">{{ title }}</h3>
</div>

{# ✅ CORRECT - Use existing icon + heading atoms #}
<div class="ps-alert">
  {% include '@elements/icon/icon.twig' with {
    icon: icon_name,
    size: 'medium'
  } only %}
  
  {% include '@elements/heading/heading.twig' with {
    text: title,
    level: 3
  } only %}
</div>
```

### 6. Props Mismatch

```twig
{# ❌ WRONG - Molecule accepts props but doesn't pass to atoms #}
{% set size = size|default('md') %}
<div class="ps-card ps-card--{{ size }}">
  {% include '@elements/button/button.twig' with {
    text: cta_text
    {# Missing: size prop NOT passed to button #}
  } only %}
</div>

{# ✅ CORRECT - Props cascade to composed atoms #}
{% set size = size|default('md') %}
<div class="ps-card ps-card--{{ size }}">
  {% include '@elements/button/button.twig' with {
    text: cta_text,
    size: size  {# ✅ Cascade size prop #}
  } only %}
</div>
```

### 7. Molecule Trying to Be Atom

```twig
{# ❌ WRONG - "Badge with icon" as separate molecule #}
{# (Badge atom already supports icons) #}

{# ✅ CORRECT - Use badge atom with icon prop #}
{% include '@elements/badge/badge.twig' with {
  text: 'New',
  icon: 'star',
  color: 'success'
} only %}
```

**When to create a molecule?** Only when atoms GAIN NEW MEANING through combination (e.g., label + field = form-field).

### 8. Missing `only` Keyword

```twig
{# ❌ RISKY - Variables leak to included template #}
{% include '@elements/button/button.twig' with {
  text: cta_text
} %}

{# ✅ SAFE - Only specified props passed #}
{% include '@elements/button/button.twig' with {
  text: cta_text,
  color: 'primary'
} only %}
```

**Always use `only`** to prevent variable pollution.

### 9. Composition Without Context

```twig
{# ❌ WRONG - Atoms combined without wrapper/context #}
{% include '@elements/label/label.twig' %}
{% include '@elements/field/field.twig' %}

{# ✅ CORRECT - Wrapper provides context and structure #}
<div class="ps-form-field">
  {% include '@elements/label/label.twig' with {
    forId: field_id
  } only %}
  
  <div class="ps-form-field__input-wrapper">
    {% include '@elements/field/field.twig' with {
      id: field_id
    } only %}
  </div>
</div>
```

### 10. Organism Skipping Molecules

```twig
{# ❌ WRONG - Header organism composes atoms directly #}
{# (Should use SearchForm molecule) #}
<header class="ps-header">
  <form class="ps-header__search">
    {% include '@elements/field/field.twig' %}
    {% include '@elements/button/button.twig' %}
  </form>
</header>

{# ✅ CORRECT - Header uses SearchForm molecule #}
<header class="ps-header">
  {% include '@components/search-form/search-form.twig' with {
    placeholder: 'Search properties...'
  } only %}
</header>
```

### 11. Tight Coupling

```twig
{# ❌ WRONG - Molecule hardcodes specific atom configuration #}
<div class="ps-card">
  {% include '@elements/button/button.twig' with {
    text: 'View Details',  {# Hardcoded #}
    color: 'primary'        {# Hardcoded #}
  } only %}
</div>

{# ✅ CORRECT - Molecule accepts props for flexibility #}
<div class="ps-card">
  {% include '@elements/button/button.twig' with {
    text: cta_text|default('View Details'),
    color: cta_color|default('primary')
  } only %}
</div>
```

### 12. Reimplementing Atom Logic

```css
/* ❌ WRONG - Card CSS reimplements button hover state */
.ps-card__cta:hover {
  background: var(--primary-hover);
  transform: translateY(-1px);
}

/* ✅ CORRECT - Button atom already has hover state */
/* Card CSS doesn't touch button internals */
```

### 13. Forgetting Documentation

```markdown
❌ WRONG - README.md without composition section

✅ CORRECT - Document atoms used:

## Composition

This molecule uses:
- Badge atom for status
- Heading atom for title
- Text atom for description
- Button atom for action
```

---

## 🎯 Practical Examples

### Example 1: Alert Molecule (Simple)

**Atoms Required**: Icon, Heading, Text, Button

```twig
{# alert.twig #}
<div class="ps-alert {{ classes|join(' ')|trim }}"
  role="alert"
  {% if attributes %}{{ attributes }}{% endif %}
>
  {# Icon atom #}
  {% if icon %}
    {% include '@elements/icon/icon.twig' with {
      icon: icon,
      size: 'medium',
      attributes: create_attribute().addClass('ps-alert__icon')
    } only %}
  {% endif %}
  
  {# Content wrapper #}
  <div class="ps-alert__content">
    {# Heading atom #}
    {% if title %}
      {% include '@elements/heading/heading.twig' with {
        text: title,
        level: heading_level|default(3),
        attributes: create_attribute().addClass('ps-alert__title')
      } only %}
    {% endif %}
    
    {# Text atom #}
    {% if message %}
      {% include '@elements/text/text.twig' with {
        text: message,
        attributes: create_attribute().addClass('ps-alert__message')
      } only %}
    {% endif %}
  </div>
  
  {# Button atom (dismiss) #}
  {% if dismissible %}
    {% include '@elements/button/button.twig' with {
      text: '',
      icon_start: 'close',
      variant: 'ghost',
      size: 'small',
      attributes: create_attribute()
        .addClass('ps-alert__close')
        .setAttribute('aria-label', 'Close alert')
        .setAttribute('data-alert-dismiss', '')
    } only %}
  {% endif %}
</div>
```

**CSS (layout only)**:
```css
.ps-alert {
  display: flex;
  gap: var(--size-4);
  padding: var(--size-4);
  
  &__icon {
    flex-shrink: 0; /* Position only */
  }
  
  &__content {
    flex: 1; /* Layout only */
  }
  
  &__close {
    margin-left: auto; /* Position only */
  }
}
```

### Example 2: Card Molecule (Complex)

**Atoms Required**: Image, Badge, Eyebrow, Heading, Text, Button

```twig
{# card.twig #}
<article class="ps-card {{ classes|join(' ')|trim }}"
  {% if attributes %}{{ attributes }}{% endif %}
>
  {# Image atom #}
  {% if image_src %}
    <div class="ps-card__media">
      {% include '@elements/image/image.twig' with {
        src: image_src,
        alt: image_alt,
        loading: 'lazy',
        fit: 'cover',
        attributes: create_attribute().addClass('ps-card__image')
      } only %}
      
      {# Badge atom (overlay) #}
      {% if badge_text %}
        {% include '@elements/badge/badge.twig' with {
          text: badge_text,
          color: badge_color|default('primary'),
          size: 'small',
          attributes: create_attribute().addClass('ps-card__badge')
        } only %}
      {% endif %}
    </div>
  {% endif %}
  
  <div class="ps-card__content">
    {# Eyebrow atom #}
    {% if eyebrow %}
      {% include '@elements/eyebrow/eyebrow.twig' with {
        text: eyebrow,
        attributes: create_attribute().addClass('ps-card__eyebrow')
      } only %}
    {% endif %}
    
    {# Heading atom #}
    {% include '@elements/heading/heading.twig' with {
      text: title,
      level: heading_level|default(3),
      attributes: create_attribute().addClass('ps-card__title')
    } only %}
    
    {# Text atom #}
    {% if description %}
      {% include '@elements/text/text.twig' with {
        text: description,
        attributes: create_attribute().addClass('ps-card__description')
      } only %}
    {% endif %}
  </div>
  
  {# Button atom #}
  {% if cta_text %}
    <div class="ps-card__footer">
      {% include '@elements/button/button.twig' with {
        text: cta_text,
        url: cta_url,
        color: cta_color|default('primary'),
        attributes: create_attribute().addClass('ps-card__cta')
      } only %}
    </div>
  {% endif %}
</article>
```

---

## 📊 Component Reusability Matrix

| Level | Can Include | Cannot Include | CSS Scope |
|-------|-------------|----------------|-----------|
| **Atom** | Nothing | Other atoms/molecules/organisms | All styles |
| **Molecule** | Atoms only | Other molecules/organisms | Layout + positioning |
| **Organism** | Molecules + Atoms | Other organisms | Layout + grid/flex |
| **Template** | All levels | N/A | Page layout only |
| **Page** | All levels | N/A | Content overrides only |

---

## 🔗 Cross-References

- **Component Structure**: `instructions/components.instructions.md`
- **CSS Standards**: `instructions/css.instructions.md`
- **Twig Templates**: `instructions/templates.instructions.md`
- **Workflow Prompts**: `instructions/workflows.instructions.md`

---

**Last Updated**: 2025-12-05  
**Maintainers**: Design System Team
