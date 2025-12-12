---
title: Composition Token-First Workflow
version: 1.0.0
lastUpdated: 2025-12-12
applyTo:
  - "source/patterns/components/**/*"
  - "source/patterns/collections/**/*"
  - "source/patterns/layouts/**/*"
  - "source/patterns/pages/**/*"
priority: CRITICAL
related:
  - css.instructions.md
  - components.instructions.md
  - atomic-design.instructions.md
  - card-inheritance.instructions.md
---

# Composition Token-First Workflow

## 🎯 Core Principle

> **When a component uses another component (include, embed, extends), follow the Token-First cascade approach to respect design specs without breaking parent components or duplicating code.**

---

## 📐 Applicability by Atomic Level

### ❌ **ATOMS (Elements)** - Does NOT apply

**Atoms are autonomous base components:**
- They define their own tokens
- They don't depend on other components
- They are consumed by others, not consumers themselves
- Examples: Button, Badge, Link, Icon, Heading, Input

```css
/* button.css - ATOM (autonomous) */
.ps-button {
  /* Exposes tokens for others to override */
  --ps-button-padding-x: var(--size-3);
  --ps-button-padding-y: var(--size-2);
  --ps-button-font-size: var(--font-size-1);
  
  /* No token overrides from other components */
}
```

---

### ✅ **MOLECULES (Components)** - APPLIES

**Molecules compose Atoms and other Molecules:**
- Must follow Token-First workflow
- Override parent/child tokens in their own CSS
- Examples: Card, Card Offer Search, Alert, Form Field, Navigation

```css
/* card-offer-search.css - MOLECULE using Card + Atoms */
.ps-card-offer-search {
  /* STEP 3: Override parent (Card) tokens */
  --ps-card-padding-x: var(--size-6);
  --ps-card-padding-y: var(--size-7);
  --ps-card-gap: var(--size-6);
  
  /* Override child Atoms tokens */
  --ps-badge-font-size: var(--font-size-0);
  --ps-button-size: var(--size-6);
  --ps-link-text-decoration: none;
  
  /* Own specific tokens */
  --ps-card-offer-search-title-size: var(--font-size-1);
}
```

---

### ✅ **ORGANISMS (Collections)** - APPLIES

**Organisms compose Molecules and Atoms:**
- Must follow Token-First workflow
- Can override multiple component tokens
- Examples: Header, Footer, Property Grid, Article List

```css
/* header.css - ORGANISM using Navigation, Button, Logo */
.ps-header {
  /* Override Navigation tokens */
  --ps-navigation-gap: var(--size-4);
  --ps-navigation-font-size: var(--font-size-1);
  
  /* Override Button tokens */
  --ps-button-variant: 'primary';
  --ps-button-size: var(--size-3);
}
```

---

### ✅ **TEMPLATES (Layouts)** - APPLIES

**Templates orchestrate full page layouts:**
- Must follow Token-First workflow
- Can cascade overrides down to all levels
- Examples: Page Layout, Grid System, Section Wrapper

---

### ✅ **PAGES** - APPLIES

**Pages are complete implementations:**
- Must follow Token-First workflow
- Final level of token overrides

---

## 🔄 The 4-Step Token-First Workflow

### **STEP 1: Check Native Parameters**

**Question:** Does the component already provide the needed functionality via parameters?

```twig
{# Example: Card provides layout, size, radius params #}
{% embed '@components/card/card.twig' with {
  layout: 'horizontal',  ← Native param?
  size: 'large',         ← Native param?
  radius: 'md'           ← Native param?
} %}
```

**✅ IF YES:** Use native params, **STOP**  
**❌ IF NO:** Go to Step 2

---

### **STEP 2: Check Utility Classes**

**Question:** Can a utility/helper class solve the need?

```twig
{% embed '@components/card/card.twig' with {
  attributes: attributes
    .addClass('u-padding-large')   ← Utility class?
    .addClass('u-gap-4')            ← Utility class?
    .addClass('u-border-primary')   ← Utility class?
} %}
```

**✅ IF YES:** Use utility classes, **STOP**  
**❌ IF NO:** Go to Step 3

---

### **STEP 3: Override Parent/Child Tokens** ⭐ **PREFERRED**

**Question:** Can we adjust via CSS tokens from parent/child components?

**PRINCIPLE:** The consuming component overrides tokens in **its own CSS**, never modifying the source component's CSS.

```css
/* card-offer-search.css (CONSUMER) */
.ps-card-offer-search {
  /* ═══════════════════════════════════════════
     Override PARENT tokens (Card)
     ═══════════════════════════════════════════ */
  --ps-card-padding-x: var(--size-6);  /* 24px */
  --ps-card-padding-y: var(--size-7);  /* 30px */
  --ps-card-gap: var(--size-6);        /* 24px */
  --ps-card-border-width: 1.5px;
  --ps-card-border-color: var(--gray-200);
  
  /* ═══════════════════════════════════════════
     Override CHILD tokens (Atoms used inside)
     ═══════════════════════════════════════════ */
  --ps-badge-padding-x: var(--size-3);
  --ps-badge-font-size: var(--font-size-0);
  
  --ps-button-size: var(--size-6);
  
  --ps-link-font-size: var(--font-size-1);
  --ps-link-text-decoration: none;
  
  /* ═══════════════════════════════════════════
     Own specific tokens
     ═══════════════════════════════════════════ */
  --ps-card-offer-search-title-size: var(--font-size-1);
  --ps-card-offer-search-price-size: var(--font-size-4);
  --ps-card-offer-search-header-gap: var(--size-3);
}
```

**✅ IF POSSIBLE:** Override tokens, **STOP**  
**❌ IF IMPOSSIBLE:** Go to Step 4

---

### **STEP 4: Targeted CSS Override** (Last Resort)

**Question:** Do we really need custom CSS targeting?

**PRINCIPLE:** Target parent classes **from the consumer's scope**.

```css
/* card-offer-search.css */
.ps-card-offer-search {
  /* ⚠️ Last resort: target parent elements */
  & .ps-card__content {
    padding: var(--size-7) var(--size-6);
    display: flex;
    flex-direction: column;
  }
  
  & .ps-card__media {
    flex: 0 0 33.6%;  /* Specific to this design */
  }
}
```

**⚠️ CAUTION:**
- Use `&` to maintain specificity scope
- Never modify source component's CSS directly
- Stay within `.ps-card-offer-search` scope
- Only for truly unique cases not covered by tokens

---

## 📊 Decision Tree Diagram

```
┌─────────────────────────────────────────┐
│ Need: Component styling customization   │
└───────────────┬─────────────────────────┘
                │
                ▼
┌──────────────────────────────────────────┐
│ STEP 1: Does parent have native params?  │
└───────┬──────────────────────────────────┘
        │
    YES │ NO
        ▼    ▼
    ✅ STOP │
            │
┌───────────▼─────────────────────────────┐
│ STEP 2: Utility class exists?           │
└───────┬─────────────────────────────────┘
        │
    YES │ NO
        ▼    ▼
    ✅ STOP │
            │
┌───────────▼─────────────────────────────┐
│ STEP 3: Parent/child expose tokens?     │
└───────┬─────────────────────────────────┘
        │
    YES │ NO
        ▼    ▼
┌────────────────────────┐  │
│ Override tokens in     │  │
│ consumer's CSS         │  │
│ (PREFERRED METHOD)     │  │
└────────────────────────┘  │
        │                   │
    ✅ STOP                │
                           ▼
┌────────────────────────────────────────┐
│ STEP 4: Targeted CSS override          │
│ & .ps-parent__element { ... }          │
│ (LAST RESORT)                          │
└────────────────────────────────────────┘
        │
    ✅ STOP
```

---

## 🚫 Anti-Patterns to Avoid

### ❌ **BAD: Modifying Source Component Directly**

```css
/* ❌ WRONG: Modifying card.css breaks ALL cards */
/* card.css */
.ps-card {
  padding: 30px 24px;  /* Affects all cards globally! */
}
```

### ❌ **BAD: Duplicating Parent Styles**

```css
/* ❌ WRONG: Duplicating Card's existing styles */
/* card-offer-search.css */
.ps-card-offer-search {
  border: 1.5px solid #EBEDEF;  /* Already in Card! */
  background: white;             /* Already in Card! */
  display: flex;                 /* Already in Card! */
}
```

### ❌ **BAD: Hardcoding Instead of Tokens**

```css
/* ❌ WRONG: Hardcoded values */
.ps-card-offer-search {
  padding: 30px 24px;           /* Use --ps-card-padding-* instead! */
  font-size: 16px;              /* Use var(--font-size-1) instead! */
  color: #333333;               /* Use var(--text-primary) instead! */
}
```

### ❌ **BAD: Forgetting Component Scope**

```css
/* ❌ WRONG: Global selector */
.ps-card__content {
  padding: 30px;  /* Affects ALL cards! */
}

/* ✅ CORRECT: Scoped selector */
.ps-card-offer-search .ps-card__content {
  padding: var(--size-7);
}

/* ✅ BETTER: Using & nesting */
.ps-card-offer-search {
  & .ps-card__content {
    padding: var(--size-7);
  }
}
```

---

## ✅ Best Practices

### 1. **Token Discovery**

Before writing CSS, discover available tokens:

```bash
# Search for tokens in parent component
grep -r "--ps-card-" source/patterns/components/card/card.css

# Check atom tokens
grep -r "--ps-button-" source/patterns/elements/button/button.css
```

### 2. **Token Naming Convention**

```css
/* Parent component tokens (override these) */
--ps-card-padding-x
--ps-card-padding-y
--ps-card-gap

/* Own component tokens (define these) */
--ps-card-offer-search-title-size
--ps-card-offer-search-price-size
--ps-card-offer-search-header-gap
```

### 3. **CSS Organization**

```css
.ps-card-offer-search {
  /* ═══ SECTION 1: Parent token overrides ═══ */
  --ps-card-padding-x: var(--size-6);
  --ps-card-gap: var(--size-6);
  
  /* ═══ SECTION 2: Child token overrides ═══ */
  --ps-badge-font-size: var(--font-size-0);
  --ps-button-size: var(--size-6);
  
  /* ═══ SECTION 3: Own tokens ═══ */
  --ps-card-offer-search-title-size: var(--font-size-1);
  
  /* ═══ SECTION 4: Targeted overrides (last resort) ═══ */
  & .ps-card__media {
    flex: 0 0 33.6%;
  }
  
  /* ═══ SECTION 5: Own elements ═══ */
  &__title {
    font-size: var(--ps-card-offer-search-title-size);
  }
}
```

### 4. **Documentation Comments**

Always document why you're overriding:

```css
.ps-card-offer-search {
  /* Override Card padding to match Figma spec (30px vertical) */
  --ps-card-padding-y: var(--size-7);
  
  /* Specific proportions from Figma design (33.6% / 66.4%) */
  & .ps-card__media {
    flex: 0 0 33.6%;
  }
}
```

---

## 📋 Pre-Implementation Checklist

Before writing CSS for a composed component:

- [ ] Identified parent component and its available params
- [ ] Checked if utility classes can solve the need
- [ ] Reviewed parent component's CSS tokens (grep search)
- [ ] Reviewed child atoms' CSS tokens (if using atoms)
- [ ] Determined which tokens to override vs define new
- [ ] Organized CSS in sections (parent overrides → child overrides → own tokens → own elements)
- [ ] Added documentation comments explaining overrides
- [ ] Tested that parent component isn't broken elsewhere

---

## 🎯 Real-World Example: Card Offer Search

**Scenario:** Implementing Figma design for property search card

**Requirements:**
- Padding: 30px vertical, 24px horizontal (Figma spec)
- Image/Content proportions: 33.6% / 66.4%
- Title: 16px Regular
- Price: 20px Bold
- No underline on CTA link
- Badges gap: 8px
- Actions gap: 12px

**Implementation:**

```css
/* card-offer-search.css */
.ps-card-offer-search {
  /* ═══════════════════════════════════════════
     STEP 3: Override PARENT (Card) tokens
     ═══════════════════════════════════════════ */
  
  /* Figma: Padding 30px 24px */
  --ps-card-padding-x: var(--size-6);  /* 24px */
  --ps-card-padding-y: var(--size-7);  /* 30px */
  
  /* Figma: Gap between image and content */
  --ps-card-gap: var(--size-6);  /* 24px */
  
  /* Figma: Border 1.5px solid #EBEDEF */
  --ps-card-border-width: 1.5px;
  --ps-card-border-color: var(--gray-200);
  
  /* ═══════════════════════════════════════════
     STEP 3: Override CHILD (Atoms) tokens
     ═══════════════════════════════════════════ */
  
  /* Badge customization */
  --ps-badge-font-size: var(--font-size-0);  /* 14px */
  
  /* Link customization (CTA) */
  --ps-link-text-decoration: none;  /* No underline per Figma */
  --ps-link-font-size: var(--font-size-1);
  
  /* ═══════════════════════════════════════════
     Own specific tokens
     ═══════════════════════════════════════════ */
  
  /* Figma: Title 16px Regular */
  --ps-card-offer-search-title-size: var(--font-size-1);
  --ps-card-offer-search-title-weight: var(--font-weight-400);
  
  /* Figma: Price 20px Bold */
  --ps-card-offer-search-price-size: var(--font-size-4);
  --ps-card-offer-search-price-weight: var(--font-weight-700);
  
  /* Figma: Actions gap 12px */
  --ps-card-offer-search-actions-gap: var(--size-3);
  
  /* ═══════════════════════════════════════════
     STEP 4: Targeted overrides (last resort)
     Only for specs not covered by tokens
     ═══════════════════════════════════════════ */
  
  @media (min-width: 768px) {
    /* Figma: Specific proportions 242px / 479px on 721px */
    & .ps-card__media {
      flex: 0 0 33.6%;
      max-width: 33.6%;
    }
    
    & .ps-card__content {
      flex: 1 1 66.4%;
    }
  }
  
  /* ═══════════════════════════════════════════
     Own elements styling
     ═══════════════════════════════════════════ */
  
  &__title {
    font-size: var(--ps-card-offer-search-title-size);
    font-weight: var(--ps-card-offer-search-title-weight);
  }
  
  &__price-value {
    font-size: var(--ps-card-offer-search-price-size);
    font-weight: var(--ps-card-offer-search-price-weight);
  }
  
  &__actions {
    gap: var(--ps-card-offer-search-actions-gap);
  }
}
```

---

## 🔗 Related Documentation

- **CSS Guidelines**: `.github/instructions/css.instructions.md`
- **Component Structure**: `.github/instructions/components.instructions.md`
- **Atomic Design**: `.github/instructions/atomic-design.instructions.md`
- **Card Inheritance**: `.github/instructions/card-inheritance.instructions.md`

---

## 📝 Summary

**Golden Rule:**  
> When composing components, override parent/child tokens in your own CSS first. Only write targeted CSS overrides as a last resort for truly unique cases.

**Hierarchy:**
1. Use native params → 2. Use utility classes → 3. Override tokens (PREFERRED) → 4. Targeted CSS (last resort)

**Applies to:**  
✅ Molecules, Organisms, Templates, Pages  
❌ Atoms (they're autonomous)

---

**Version**: 1.0.0  
**Last Updated**: 2025-12-12  
**Maintainers**: Design System Team
