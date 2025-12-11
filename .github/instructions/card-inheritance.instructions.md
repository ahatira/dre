---
title: Card Component Inheritance Pattern
version: 3.0.0
lastUpdated: 2025-12-11
applyTo:
  - "source/patterns/components/card-*.twig"
  - "source/patterns/components/card-*.css"
  - "source/patterns/components/card-*.js"
  - "source/patterns/components/card-*.yml"
  - "source/patterns/components/card-*.stories.jsx"
priority: CRITICAL
changelog:
  - version: 3.0.0
    date: 2025-12-11
    changes:
      - "MAJOR UPDATE: Complete rewrite based on Card Offer Slide reference implementation"
      - "Added Section 8: Reference Implementation with complete Card Offer Slide analysis"
      - "Added Section 9: JavaScript Behaviors Pattern with auto-fit algorithm"
      - "Added Section 10: Storybook Stories Best Practices with PropertyGrid pattern"
      - "Added Section 12: Complete Card Generation Workflow (step-by-step guide)"
      - "Documented :where() pattern for overlay tokens (40-token architecture)"
      - "Documented argTypes categorization (5 categories: Content, Appearance, Behavior, CTA, Drupal)"
      - "Documented PropertyGrid story pattern with 6+ varied examples"
      - "Documented Real Estate data patterns (Faker.js-style realistic content)"
      - "Documented auto-fit text algorithm (ratio-based font-size calculation)"
      - "Added complete generation template (copy/find-replace/customize workflow)"
      - "Updated all examples to use card-{bundle}-{view_mode} naming"
      - "Clarified variable passing requirement (ALL variables in embed with {} hash)"
  - version: 2.1.0
    date: 2025-12-10
    changes:
      - "Added CRITICAL note in section 6.2 about explicit variable passing in embed blocks"
      - "Documented variable scope issue with {% embed %} blocks and solutions"
      - "Added correct/wrong examples with card-offer-slide reference implementation"
  - version: 2.0.0
    date: 2025-12-10
    changes:
      - "Added section 1 Naming Convention with card-{bundle}-{view_mode} pattern"
      - "Documented Drupal-inspired naming for Card-based components"
      - "Added examples table and automatic CSS class generation rules"
---

# Card Component Inheritance Pattern

## 📋 Table of Contents

1. [Naming Convention](#1-naming-convention)
2. [Architecture Overview](#2-architecture-overview)
3. [Card Component Analysis](#3-card-component-analysis)
4. [Twig Inheritance Pattern](#4-twig-inheritance-pattern)
5. [CSS Integration Strategy](#5-css-integration-strategy)
6. [Atomic Component Reuse](#6-atomic-component-reuse)
7. [Implementation Checklist](#7-implementation-checklist)
8. [Reference Implementation: Card Offer Slide](#8-reference-implementation-card-offer-slide) ⭐ **NEW**
9. [JavaScript Behaviors Pattern](#9-javascript-behaviors-pattern) ⭐ **NEW**
10. [Storybook Stories Best Practices](#10-storybook-stories-best-practices) ⭐ **NEW**
11. [Common Issues & Solutions](#11-common-issues--solutions)
12. [Complete Card Generation Workflow](#12-complete-card-generation-workflow) ⭐ **NEW**
13. [Summary Checklist](#13-summary-checklist)

---

## 1. Naming Convention

### 🎯 CRITICAL RULE: Card-Based Component Naming

**All components inheriting from Card MUST follow the Drupal-inspired naming pattern:**

```
card-{bundle}-{view_mode}
```

This convention aligns with Drupal's automatic class generation on the base Card component.

### Pattern Breakdown

| Part | Description | Examples |
|------|-------------|----------|
| **`card-`** | Mandatory prefix for all Card-based components | `card-` (fixed) |
| **`{bundle}`** | Content type / entity bundle | `offer`, `article`, `property`, `product`, `event` |
| **`{view_mode}`** | Display mode / context | `slide`, `list`, `teaser`, `full`, `search`, `grid` |

### Examples

| Component Type | Pattern | Folder Name | Class Name |
|----------------|---------|-------------|------------|
| Property listing card | `card-offer-slide` | `source/patterns/components/card-offer-slide/` | `.ps-card-offer-slide` |
| Property search result | `card-offer-search` | `source/patterns/components/card-offer-search/` | `.ps-card-offer-search` |
| Article teaser | `card-article-teaser` | `source/patterns/components/card-article-teaser/` | `.ps-card-article-teaser` |
| Product grid item | `card-product-grid` | `source/patterns/components/card-product-grid/` | `.ps-card-product-grid` |
| Event full display | `card-event-full` | `source/patterns/components/card-event-full/` | `.ps-card-event-full` |

### Drupal Integration

When passing `bundle` and `view_mode` parameters to the Card component, Drupal automatically generates contextual classes:

```twig
{% embed '@components/card/card.twig' with {
  attributes: attributes.addClass('ps-card-offer-slide'),
  bundle: 'offer',
  view_mode: 'slide'
} %}
```

Generates:
```html
<article class="ps-card ps-card-offer-slide ps-card--type-offer ps-card--view-mode-slide">
```

This creates a consistent naming system across:
- ✅ Component folder structure
- ✅ CSS class names
- ✅ BEM selectors
- ✅ Drupal entity classes
- ✅ Storybook organization

### Benefits

1. **Predictability**: Developers instantly understand component context
2. **Drupal alignment**: Matches entity bundle + view mode structure
3. **CSS specificity**: Clear hierarchy (`.ps-card.ps-card-offer-slide`)
4. **Searchability**: Easy to find related components (`card-offer-*`)
5. **Scalability**: Supports multiple view modes per content type

---

## 2. Architecture Overview

### Card Component Purpose

The **Card component** (`@components/card/card.twig`) is a **generic flexible container** that provides:

- ✅ **Visual structure**: Border, radius, shadow, padding
- ✅ **Layout variants**: Vertical (default) | Horizontal
- ✅ **Extensible blocks**: Media, Header, Body, Footer
- ✅ **State management**: Clickable links, hover effects
- ✅ **Responsive behavior**: Mobile stacking for horizontal layouts

### When to Inherit Card

**✅ Use Card inheritance when:**
- Component has image/media + content structure
- Needs standard card visual treatment (border, shadow, radius)
- Benefits from layout variants (vertical/horizontal)
- Requires clickable card behavior (entire card as link)

**❌ Don't use Card when:**
- Component is a simple list item without card visual
- Custom layout too different from Card structure
- Performance critical (too many nested components)

---

## 2. Card Component Analysis

### 2.1 Available Twig Blocks

| Block | Purpose | Default Content | Optional |
|-------|---------|-----------------|----------|
| `media` | Image/visual zone | `{{ image\|raw }}` prop | ✅ Yes |
| `media_overlay` | Badges/actions overlaid on media | Empty | ✅ Yes |
| `header` | Top metadata (tags, dates, location) | `{{ header\|raw }}` prop | ✅ Yes |
| `body` / `content` | Main text content | `{{ body\|raw }}` or `{{ content\|raw }}` | ✅ Yes |
| `footer` | Bottom actions/CTAs | `{{ footer\|raw }}` prop | ✅ Yes |

### 2.2 Available Parameters

#### Visual Variants
```twig
{% embed '@components/card/card.twig' with {
  variant: 'default'  {# default | outlined | flat | elevated #}
} %}
```

- **`default`**: Border + no shadow
- **`outlined`**: Thicker border (2px)
- **`flat`**: No border, no shadow
- **`elevated`**: No border, shadow-2 (shadow-4 on hover)

#### Layout Options
```twig
{% embed '@components/card/card.twig' with {
  layout: 'vertical',      {# vertical | horizontal #}
  imagePosition: 'start'   {# start | end #}
} %}
```

- **`vertical`**: Image top, content bottom (default)
- **`horizontal`**: Image left (40%), content right (60%)
- **`imagePosition: 'end'`**: Image right (horizontal) or bottom (vertical)

#### Sizing
```twig
{% embed '@components/card/card.twig' with {
  size: 'medium',    {# small | medium | large #}
  radius: 'none'     {# none | sm | md | lg #}
} %}
```

- **`small`**: padding 16px, gap 12px
- **`medium`**: padding 32px/24px, gap 16px (default)
- **`large`**: padding 32px, gap 20px
- **`radius`**: Border radius (none=0, sm=4px, md=8px, lg=16px)

#### Clickable Card
```twig
{% embed '@components/card/card.twig' with {
  url: '/property/123'  {# Makes entire card <a> element #}
} %}
```

### 2.3 CSS Variables Available

Card exposes these CSS custom properties that **child components can override**:

```css
.ps-your-card {
  /* Override Card defaults */
  --ps-card-padding-y: var(--size-6);        /* Vertical padding */
  --ps-card-padding-x: var(--size-5);        /* Horizontal padding */
  --ps-card-gap: var(--size-3);              /* Gap between header/body/footer */
  --ps-card-bg: var(--white);                /* Background color */
  --ps-card-border-color: var(--gray-200);   /* Border color */
  --ps-card-border-width: var(--border-size-1); /* Border width */
  --ps-card-border-radius: 0;                /* Border radius */
  --ps-card-hover-shadow: var(--shadow-3);   /* Shadow on hover */
}
```

### 2.4 HTML Structure Generated

```html
<article class="ps-card ps-card--elevated ps-card--radius-md">
  
  <!-- Media block (if defined) -->
  <div class="ps-card__media">
    <!-- Your {% block media %} content -->
    <!-- Optional: media_overlay block -->
  </div>
  
  <!-- Content wrapper -->
  <div class="ps-card__content">
    
    <!-- Header block (if defined) -->
    <div class="ps-card__header">
      <!-- Your {% block header %} content -->
    </div>
    
    <!-- Body block (if defined) -->
    <div class="ps-card__body">
      <!-- Your {% block body %} content -->
    </div>
    
    <!-- Footer block (if defined) -->
    <div class="ps-card__footer">
      <!-- Your {% block footer %} content -->
    </div>
    
  </div>
  
</article>
```

**Key observations:**
- `.ps-card__media` is **OUTSIDE** `.ps-card__content`
- Media overlay uses `.ps-card__media` as position:relative parent
- `.ps-card__content` wraps header/body/footer with padding/gap

---

## 3. Twig Inheritance Pattern

### 3.1 Basic Embed Structure

```twig
{#
/**
 * Your Card Component (Component/Molecule)
 * Description of your card variant.
 * 
 * @param [document all your specific params]
 */
#}

{# Set defaults for your component params #}
{%- set title = title|default('Default title') -%}
{%- set image = image|default(null) -%}

{# Embed Card component with configuration #}
{% embed '@components/card/card.twig' with {
  variant: 'elevated',              {# Choose visual variant #}
  radius: 'md',                     {# Choose border radius #}
  attributes: attributes.addClass('ps-your-card')  {# Add your custom class #}
} %}

  {# Override blocks as needed #}
  {% block media %}
    {# Your media content #}
  {% endblock %}
  
  {% block body %}
    {# Your body content #}
  {% endblock %}
  
  {% block footer %}
    {# Your footer content #}
  {% endblock %}

{% endembed %}
```

### 3.2 Reusing Atomic Components

**✅ CORRECT - Use component includes:**

```twig
{% block media %}
  {# Reuse Image atom #}
  {% include '@elements/image/image.twig' with {
    src: image.url,
    alt: image.alt,
    class: 'ps-your-card__image'  {# Custom class for specific styling #}
  } only %}
{% endblock %}

{% block media_overlay %}
  {# ⚠️ MANDATORY: Always wrap media_overlay content in a container div #}
  <div class="ps-your-card__overlay">
    {# Reuse Button atom for favorite #}
    {% include '@elements/button/button.twig' with {
      class: 'ps-your-card__favorite',
      icon: 'heart',
      attributes: create_attribute()
        .setAttribute('aria-label', 'Add to favorites')
    } only %}
    
    {# Other overlay elements (badges, navigation, etc.) #}
  </div>
{% endblock %}
```

**⚠️ CRITICAL CSS NOTE:** Elements in `media_overlay` are children of `.ps-card__media` (Card's wrapper), NOT direct children of `.ps-your-card`. They **cannot access component tokens** defined on `.ps-your-card {}`. Use global tokens (`var(--size-*)`) or direct values instead. See Section 4.2 for details.

**❌ WRONG - Don't write raw HTML:**

```twig
{% block media %}
  {# Bad: Raw HTML instead of component reuse #}
  <img src="{{ image.url }}" alt="{{ image.alt }}">
  <button type="button">Favorite</button>
{% endblock %}
```

**❌ WRONG - media_overlay without wrapper:**

```twig
{% block media_overlay %}
  {# Bad: No wrapper div, limits CSS flexibility #}
  {% include '@elements/button/button.twig' with {
    class: 'ps-your-card__favorite',
    icon: 'heart'
  } only %}
{% endblock %}
```

**Why wrapper is required:**
- Provides single positioning context for all overlay elements
- Allows flexible layout (flexbox, grid) for multiple overlays
- Simplifies CSS with single container (top-right, bottom-left zones, etc.)
- Enables backdrop effects, gradients, or shared overlays styles

### 3.3 Passing Variables Through Embed

**⚠️ CRITICAL**: Variables from parent scope are **NOT automatically available** inside blocks when using `only`.

**✅ SOLUTION 1 - Don't use `only` in embed:**

```twig
{% embed '@components/card/card.twig' with {
  attributes: attributes.addClass('ps-your-card')
} %}
  {# Variables from parent scope ARE available #}
  {% block body %}
    <h3>{{ title }}</h3>  {# ✅ Works #}
  {% endblock %}
{% endembed %}
```

**✅ SOLUTION 2 - Explicitly pass variables:**

```twig
{% embed '@components/card/card.twig' with {
  attributes: attributes.addClass('ps-your-card'),
  _title: title,          {# Pass variables with _ prefix #}
  _image: image,
  _location: location
} only %}
  {% block body %}
    <h3>{{ _title }}</h3>  {# ✅ Access via _ prefix #}
  {% endblock %}
{% endembed %}
```

**Recommendation**: Use Solution 1 (no `only`) unless you have strict isolation requirements.

---

## 4. CSS Integration Strategy

### 4.1 CSS Architecture with Card Inheritance

```css
/**
 * Your Card Component - Extends Card base
 * 
 * Structure:
 * 1. Override Card CSS variables (if needed)
 * 2. Define your component tokens
 * 3. Style your custom elements
 */

.ps-your-card {
  /* ==========================================
     Override Card Component Variables
     ========================================== */
  
  --ps-card-padding-y: var(--size-5);   /* Override default padding */
  --ps-card-padding-x: var(--size-5);
  --ps-card-gap: var(--size-3);         /* Override default gap */
  
  /* ==========================================
     Your Component Tokens (Layer 2)
     MUST be defined INSIDE .ps-your-card {}
     ========================================== */
  
  /* Image */
  --ps-your-card-image-aspect-ratio: 3/2;
  
  /* Overlay wrapper */
  --ps-your-card-overlay-inset: var(--size-3);  /* Distance from edges */
  
  /* Button overlay */
  --ps-your-card-favorite-size: var(--size-10);
  --ps-your-card-favorite-bg: var(--white);
  
  /* Typography */
  --ps-your-card-title-font-size: var(--font-size-2);
  --ps-your-card-title-font-weight: var(--font-weight-600);
  
  /* ==========================================
     Your Component Styles (Layer 3)
     ========================================== */
  
  max-width: 320px;  /* Component-specific constraint */
  
  /* Image styling */
  &__image {
    aspect-ratio: var(--ps-your-card-image-aspect-ratio);
    object-fit: cover;
  }
  
  /* Overlay wrapper - provides positioning context */
  &__overlay {
    position: absolute;
    inset: var(--ps-your-card-overlay-inset);
    display: flex;
    justify-content: space-between;  /* Separate left/right elements */
    align-items: flex-start;         /* Align to top */
    pointer-events: none;            /* Allow clicks through wrapper */
  }
  
  /* Overlay button */
  &__favorite {
    width: var(--ps-your-card-favorite-size);
    height: var(--ps-your-card-favorite-size);
    pointer-events: auto;  /* Re-enable clicks on button */
    /* ... more styles */
  }
  
  /* Custom elements inside .ps-card__body */
  &__title {
    font-size: var(--ps-your-card-title-font-size);
    font-weight: var(--ps-your-card-title-font-weight);
    /* ... more styles */
  }
}
```

### 4.2 CSS Token Scope Rules

**🚨 CRITICAL RULE #1**: All component tokens MUST be defined **inside** `.ps-your-card {}` selector.

**❌ WRONG - Tokens in separate Layer 2 block:**

```css
/* This DOES NOT WORK with Card inheritance */
.ps-your-card {
  --ps-your-card-title-font-size: var(--font-size-2);
}

.ps-your-card {
  &__title {
    font-size: var(--ps-your-card-title-font-size);  /* ❌ Token not accessible */
  }
}
```

**✅ CORRECT - All tokens in same block:**

```css
.ps-your-card {
  /* Tokens defined here */
  --ps-your-card-title-font-size: var(--font-size-2);
  
  /* Styles using tokens immediately after */
  &__title {
    font-size: var(--ps-your-card-title-font-size);  /* ✅ Token accessible */
  }
}
```

**Why?** Card component creates its own CSS scope. Tokens defined outside `.ps-your-card {}` are not inherited by child elements due to CSS cascade rules.

---

**🚨 CRITICAL RULE #2**: Elements inside `media_overlay` block **CANNOT** access component tokens.

**DOM Structure generated by Card:**

```html
<article class="ps-card ps-your-card">  <!-- Your component class here -->
  <div class="ps-card__media">           <!-- Card's media wrapper -->
    <img class="ps-your-card__image">
    <!-- media_overlay content goes here -->
    <div class="ps-your-card__overlay">  <!-- ⚠️ NOT a child of .ps-your-card -->
      <button class="ps-your-card__favorite">
```

**The problem:** `.ps-your-card__overlay` is a child of `.ps-card__media`, NOT `.ps-your-card`, so it cannot access tokens defined on `.ps-your-card`.

**❌ WRONG - Using component tokens in overlay:**

```css
.ps-your-card {
  --ps-your-card-overlay-inset: var(--size-3);
  --ps-your-card-overlay-z-index: 10;
  
  &__overlay {
    inset: var(--ps-your-card-overlay-inset);  /* ❌ Token NOT accessible */
    z-index: var(--ps-your-card-overlay-z-index);  /* ❌ Token NOT accessible */
  }
}
```

**✅ CORRECT - Use global tokens or direct values:**

```css
.ps-your-card {
  /* No need for overlay-specific tokens */
  
  &__overlay {
    inset: var(--size-3);  /* ✅ Global token from source/props/sizes.css */
    z-index: 10;           /* ✅ Direct value */
  }
}
```

**Alternative - Define tokens on the element itself:**

```css
.ps-your-card {
  &__overlay {
    --overlay-inset: var(--size-3);  /* Define token HERE */
    inset: var(--overlay-inset);     /* Use it immediately */
    z-index: 10;
  }
}
```

**Rule of thumb:** Elements in `media_overlay` block should use:
- ✅ Global tokens from `source/props/*.css` (`--size-*`, `--color-*`, etc.)
- ✅ Direct values (numbers, keywords)
- ✅ Tokens defined on the element itself
- ✅ **Tokens from `:where(.ps-card.ps-your-card)`** (recommended long-term solution)
- ❌ Component tokens from parent `.ps-your-card {}`

**Long-term solution - Using `:where()` for overlay tokens:**

```css
/* Define tokens on Card selector for cascade access */
:where(.ps-card.ps-your-card) {
  --favorite-size: var(--size-8);
  --favorite-bg: var(--white);
  --favorite-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.ps-your-card {
  /* Component tokens for body/footer */
  --ps-your-card-title-font-size: var(--font-size-2);
  
  &__favorite {
    width: var(--favorite-size);  /* ✅ Accessible via :where() */
    background: var(--favorite-bg);
  }
}
```

**Why `:where(.ps-card.ps-your-card)` is the best solution:**
- ✅ Specificity 0,0,0 (easily overridable)
- ✅ Tokens available for all Card descendants (including `.ps-card__media`)
- ✅ Stays in component file (not global pollution)
- ✅ Clear separation: `:where()` = overlay tokens, `.ps-your-card` = body tokens
- ✅ Reusable pattern for all Card-based components

### 4.3 Handling Card Media Block

The `.ps-card__media` wrapper is **generated by Card component**, not your component.

**For overlays (buttons, badges) - ALWAYS use wrapper:**

```css
.ps-your-card {
  /* Overlay wrapper provides flexible positioning */
  &__overlay {
    position: absolute;
    inset: var(--size-3);  /* Distance from all edges */
    display: flex;
    justify-content: space-between;  /* Left/right zones */
    align-items: flex-start;         /* Top alignment */
    pointer-events: none;            /* Allow clicks through to image */
  }
  
  /* Overlay elements re-enable pointer-events */
  &__favorite {
    width: var(--size-10);
    height: var(--size-10);
    pointer-events: auto;  /* Clickable button */
    /* Card component already sets .ps-card__media { position: relative } */
  }
}
```

**Benefits of wrapper approach:**
- Single positioning context (`position: absolute` on wrapper only)
- Flexbox/grid for multiple overlay elements (favorite + badge + navigation)
- Easy alignment zones (top-left, top-right, bottom-center, etc.)
- Shared backdrop effects or gradients on wrapper
- Pointer-events control (clicks through wrapper, active on children)

**For images:**

```css
.ps-your-card {
  /* Style the image itself, not the wrapper */
  &__image {
    aspect-ratio: 3/2;
    object-fit: cover;
    /* Card component handles .ps-card__media img { width: 100%; } */
  }
}
```

---

## 5. Atomic Component Reuse

### 5.1 Image Atom Integration

**Image component parameters:**

```twig
{% include '@elements/image/image.twig' with {
  src: string (required),         {# Image URL #}
  alt: string (required),         {# Alt text #}
  class: string (optional),       {# Custom class (e.g., 'ps-your-card__image') #}
  width: int (optional),          {# Intrinsic width #}
  height: int (optional),         {# Intrinsic height #}
  srcset: array (optional),       {# Responsive sources #}
  sizes: string (optional),       {# Sizes attribute #}
  loading: 'lazy'|'eager',        {# Loading strategy (default: lazy) #}
  fit: 'cover'|'contain'|...,     {# Object-fit (default: none) #}
  rounded: 'sm'|'md'|'lg'|'full'  {# Border-radius (default: none) #}
} only %}
```

**CSS generated:**

```html
<img 
  class="ps-image ps-your-card__image" 
  src="/images/..." 
  alt="..." 
  loading="lazy" 
  decoding="auto"
/>
```

**Styling in your CSS:**

```css
.ps-your-card {
  &__image {
    /* Add your specific styles */
    aspect-ratio: 3/2;
    object-fit: cover;
  }
}
```

### 5.2 Button Atom Integration

**Button component parameters:**

```twig
{% include '@elements/button/button.twig' with {
  label: string (optional),           {# Button text #}
  class: string (optional),           {# Custom class #}
  variant: 'neutral'|'primary'|...,   {# Color variant (default: neutral) #}
  size: 'xs'|'sm'|'md'|'lg'|...,      {# Size (default: md) #}
  icon: 'heart'|'check'|...,          {# Icon name (no icon- prefix) #}
  iconPosition: 'start'|'end',        {# Icon position (default: start) #}
  disabled: boolean,                  {# Disabled state #}
  attributes: Attribute object        {# Additional HTML attributes #}
} only %}
```

**For icon-only buttons (like favorite):**

```twig
{% include '@elements/button/button.twig' with {
  class: 'ps-your-card__favorite',    {# Custom class for positioning #}
  icon: 'heart',                      {# Icon name #}
  {# label is omitted → automatically gets ps-button--icon-only modifier #}
  attributes: create_attribute()
    .setAttribute('aria-label', 'Add to favorites')  {# Required for a11y #}
    .setAttribute('aria-pressed', 'false')
} only %}
```

**CSS generated:**

```html
<button 
  class="ps-button ps-button--icon-only ps-your-card__favorite" 
  aria-label="Add to favorites" 
  aria-pressed="false" 
  data-icon="heart"
>
</button>
```

**Styling in your CSS:**

```css
.ps-your-card {
  &__favorite {
    /* Override button defaults */
    position: absolute;
    top: var(--size-3);
    right: var(--size-3);
    width: var(--size-10);   /* Override --ps-button-height */
    height: var(--size-10);
    padding: 0;
    background: var(--white);
    color: var(--danger);
    border-radius: var(--radius-full);
    box-shadow: var(--shadow-2);
    
    &:hover {
      color: var(--danger-hover);
    }
  }
}
```

### 5.3 Common Pitfalls

**❌ PITFALL 1 - Attribute object syntax:**

```twig
{# ❌ WRONG - Plain object doesn't work #}
{% include '@elements/button/button.twig' with {
  attributes: {
    'aria-label': 'Favorite',
    'aria-pressed': 'false'
  }
} only %}

{# ✅ CORRECT - Use create_attribute() #}
{% include '@elements/button/button.twig' with {
  attributes: create_attribute()
    .setAttribute('aria-label', 'Favorite')
    .setAttribute('aria-pressed', 'false')
} only %}
```

**❌ PITFALL 2 - Icon prefix:**

```twig
{# ❌ WRONG - Including icon- prefix #}
icon: 'icon-heart'

{# ✅ CORRECT - No prefix (auto-added by CSS) #}
icon: 'heart'
```

**❌ PITFALL 3 - Missing aria-label for icon-only buttons:**

```twig
{# ❌ WRONG - No accessible label #}
{% include '@elements/button/button.twig' with {
  icon: 'heart'
} only %}

{# ✅ CORRECT - Always provide aria-label #}
{% include '@elements/button/button.twig' with {
  icon: 'heart',
  attributes: create_attribute()
    .setAttribute('aria-label', 'Add to favorites')
} only %}
```

---

## 6. Implementation Checklist

### 6.1 Pre-Implementation Analysis

- [ ] **Read design spec** (`docs/design/{level}/{component}.md`)
- [ ] **Identify Card benefits**: Does this need image+content structure, border/shadow, layouts?
- [ ] **Choose component name**: Follow `card-{bundle}-{view_mode}` convention (e.g., `card-offer-slide`, `card-article-teaser`)
- [ ] **Map blocks**: Which Card blocks will you use? (media, header, body, footer)
- [ ] **List atoms**: Which atomic components can be reused? (Image, Button, Link, Badge, etc.)
- [ ] **Check dependencies**: Do required atoms exist? Are they production-ready?

### 6.2 Twig Implementation

- [ ] **File header**: Complete JSDoc comment with all params documented
- [ ] **Set defaults**: All params have `|default()` values
- [ ] **Embed Card**: Use `{% embed '@components/card/card.twig' with { ... } %}`
- [ ] **Pass custom class**: `attributes: attributes.addClass('ps-card-{bundle}-{view_mode}')` (e.g., `'ps-card-offer-slide'`)
- [ ] **Pass Drupal params**: `bundle: bundle|default('offer')`, `view_mode: view_mode|default('slide')`
- [ ] **Choose Card params**: `variant`, `radius`, `size` if needed
- [ ] **Pass all variables**: **CRITICAL** - Include ALL data in embed `with {}` block (title, price, image, location, cta, etc.) to make accessible in blocks
- [ ] **Override blocks**: `{% block media %}`, `{% block body %}`, etc.
- [ ] **Reuse atoms**: Use `{% include '@elements/...` NOT raw HTML
- [ ] **Pass custom classes**: Each atom gets `class: 'ps-card-{bundle}-{view_mode}__element'`
- [ ] **Accessibility**: `aria-label`, `aria-pressed`, etc. for all interactive elements
- [ ] **No `only`**: Don't use `only` in embed unless you have strict isolation needs

**⚠️ CRITICAL - Variable Scope in Embed Blocks:**

Variables are **NOT automatically accessible** inside `{% block %}` definitions unless:
1. They are **explicitly passed** in the `with {}` hash of the embed, OR
2. The embed does NOT use the `only` keyword (not recommended - less explicit)

**Example (CORRECT):**
```twig
{% embed '@components/card/card.twig' with {
  attributes: attributes.addClass('ps-card-offer-slide'),
  bundle: bundle,
  view_mode: view_mode,
  title: title,           {# ← Pass ALL variables used in blocks #}
  price: price,           {# ← Required for body block #}
  surface: surface,       {# ← Required for body block #}
  image: image,           {# ← Required for media block #}
  location: location,     {# ← Required for body block #}
  locationIcon: locationIcon,
  cta: cta,              {# ← Required for footer block #}
  isFavorite: isFavorite {# ← Required for media_overlay block #}
} %}
  {% block body %}
    {# price and surface are NOW accessible because passed in with {} #}
    {% if price or surface %}
      <div class="ps-card-offer-slide__header">
        {{ price }}{% if price and surface %} • {% endif %}{{ surface }}
      </div>
    {% endif %}
  {% endblock %}
{% endembed %}
```

**Example (WRONG - Missing Variables):**
```twig
{% embed '@components/card/card.twig' with {
  attributes: attributes.addClass('ps-card-offer-slide'),
  bundle: bundle,
  view_mode: view_mode
  {# ❌ Missing title, price, surface, image, etc. #}
} %}
  {% block body %}
    {# ❌ price and surface are UNDEFINED here - condition always false #}
    {% if price or surface %}
      <div class="ps-card-offer-slide__header">
        {{ price }} • {{ surface }}
      </div>
    {% endif %}
  {% endblock %}
{% endembed %}
```

**Reference Implementation**: See `source/patterns/components/card-offer-slide/card-offer-slide.twig` for complete example.

### 6.3 CSS Implementation

- [ ] **Overlay tokens with `:where()`** (if using `media_overlay`): Define in `:where(.ps-card.ps-card-{bundle}-{view_mode}) { }` block BEFORE main selector
- [ ] **Single block structure**: All body/footer tokens + styles in ONE `.ps-card-{bundle}-{view_mode} {}` block
- [ ] **Override Card vars** (if needed): `--ps-card-padding-y`, `--ps-card-gap`, etc. at top
- [ ] **Define component tokens**: All `--ps-card-{bundle}-{view_mode}-*` variables after Card overrides
- [ ] **BEM nesting**: Use `&__element` syntax for all custom elements
- [ ] **Token usage**: ALL values use tokens (`var(--token-name)`), zero hardcoded values
- [ ] **Semantic colors**: Use `--primary`, `--danger`, etc. NOT `--green-600`
- [ ] **Overlay positioning**: Wrapper with `position: absolute`, flexbox for children
- [ ] **Focus-visible**: All interactive elements have visible focus indicator
- [ ] **Transitions**: Smooth state changes (`transition: color ...`)

**CSS structure example:**

```css
/* Overlay tokens (accessible in .ps-card__media descendants) */
:where(.ps-card.ps-your-card) {
  --favorite-size: var(--size-8);
  --overlay-inset: var(--size-3);
}

/* Component tokens + styles */
.ps-your-card {
  /* Override Card defaults */
  --ps-card-padding-y: var(--size-5);
  
  /* Body/footer tokens */
  --ps-your-card-title-font-size: var(--font-size-2);
  
  /* Styles */
  max-width: 320px;
  
  &__overlay {
    position: absolute;
    inset: var(--overlay-inset);  /* ✅ From :where() */
  }
  
  &__favorite {
    width: var(--favorite-size);  /* ✅ From :where() */
  }
  
  &__title {
    font-size: var(--ps-your-card-title-font-size);  /* ✅ Local token */
  }
}
```

### 6.4 YAML Data

- [ ] **Remove Drupal schemas**: No `$schema`, `name`, `status` keys (causes conflicts)
- [ ] **Real Estate context**: Use property-related data (offices, warehouses, retail)
- [ ] **Faker.js examples**: Realistic addresses, prices, areas
- [ ] **Simple structure**: Data only, no validation schemas

### 6.5 Storybook Stories

- [ ] **Export default**: `tags: ['autodocs']` MANDATORY
- [ ] **argTypes**: Categorized (Content, Appearance, Link, Layout, Behavior)
- [ ] **Default story**: Shows all features with real data
- [ ] **Variant stories**: Showcase different states/configurations (3-5 stories)
- [ ] **Real Estate data**: Use Faker.js for realistic content
- [ ] **No Drupal schemas**: Import YAML data directly, no schema validation

### 6.6 README Documentation

- [ ] **Overview**: Component purpose + use cases
- [ ] **Props table**: All parameters with types, defaults, descriptions
- [ ] **BEM structure**: Complete HTML structure documentation
- [ ] **Tokens section**: List all CSS variables with purposes
- [ ] **Accessibility**: WCAG 2.2 AA compliance documentation
- [ ] **Usage examples**: Twig code samples with real data
- [ ] **Card inheritance note**: Document Card parent usage + block overrides

---

## 7. Validation & Testing

### 7.1 Build Validation

```bash
npm run build
```

**Must pass:**
- ✅ Biome lint check (no errors)
- ✅ Biome format check (all files formatted)
- ✅ Vite build (CSS compiled without errors)
- ✅ No "token is not defined" warnings

### 7.2 Visual Testing (Storybook)

```bash
npm run watch  # Opens http://localhost:6006
```

**Test checklist:**
- [ ] Default story renders correctly
- [ ] All variant stories work
- [ ] Images load properly (local images from `/images/`)
- [ ] Buttons are interactive (hover, focus states)
- [ ] Icons display correctly (no missing icons)
- [ ] Typography matches design spec
- [ ] Spacing matches design spec (padding, gaps, margins)
- [ ] Colors use semantic tokens (inspect with DevTools)
- [ ] Responsive behavior (resize browser to mobile)
- [ ] Focus indicators visible on keyboard navigation

### 7.3 CSS Inspection

**Use browser DevTools:**

1. **Inspect component root** (`.ps-your-card`)
   - [ ] Card CSS variables applied correctly
   - [ ] Your component tokens defined
   - [ ] No "invalid property value" errors

2. **Inspect child elements** (`.ps-your-card__title`, etc.)
   - [ ] Tokens accessible via cascade
   - [ ] All values resolved (not "unset")
   - [ ] No hardcoded values (16px, #00915A, etc.)

3. **Inspect atomic components** (`.ps-image`, `.ps-button`)
   - [ ] Custom classes applied (e.g., `.ps-your-card__favorite`)
   - [ ] Your overrides take precedence over atom defaults

### 7.4 Conformity Audit

**Score: 100% required before commit**

Check against:
- [ ] All zero tolerance rules (see `copilot-instructions.md`)
- [ ] Component structure (5 files: twig, css, yml, stories, README)
- [ ] Twig standards (header, defaults, ternary, no arrow functions)
- [ ] CSS standards (all tokens, nesting, semantic colors)
- [ ] Storybook standards (autodocs, categories, Real Estate data)
- [ ] Accessibility (WCAG 2.2 AA minimum)

### 7.5 Git Commit

**Structured commit message:**

```
feat(components): Add [your-card] component with Card inheritance

- Implement 5-file structure (twig, css, yml, stories, README)
- Embed Card component for visual structure reuse
- Reuse Image and Button atoms
- Support [list key features]
- Full Autodocs with categorized argTypes
- WCAG 2.2 AA compliant (keyboard navigation, focus-visible, ARIA)
- References spec: docs/design/molecules/[your-card].md
```

**Update changelog:**

Add entry to `docs/ps-design/CHANGELOG.md`:

```markdown
## [Date] - [Your Card Component]

### Added
- **[Your Card]** (Component/Molecule)
  - Card inheritance pattern for visual structure
  - Image atom integration for responsive images
  - Button atom integration for favorite/actions
  - [List other features]
  - Status: ✅ Complete (6/87 components)
```

---

## 8. Reference Implementation: Card Offer Slide

**PRODUCTION-READY REFERENCE:** `card-offer-slide` (Complete 100% implementation)

**Location:** `source/patterns/components/card-offer-slide/`

### 8.1 Files Overview

| File | Purpose | Key Patterns |
|------|---------|--------------|
| **card-offer-slide.twig** | Template | ✅ Embed Card with all variables passed<br>✅ 4 blocks overridden (media, media_overlay, body, footer)<br>✅ Atoms reused (Image, Button, Heading, Icon)<br>✅ No `only` in embed |
| **card-offer-slide.css** | Styling | ✅ `:where()` tokens for overlay (12 lines)<br>✅ Component tokens + styles in single block<br>✅ 196 lines total (tokens + BEM nesting)<br>✅ Zero hardcoded values |
| **card-offer-slide.js** | Behavior | ✅ Auto-fit text algorithm (0.48 kB gzipped)<br>✅ Drupal behavior pattern<br>✅ `once()` for idempotency<br>✅ Ratio-based font-size calculation |
| **card-offer-slide.yml** | Data | ✅ Real Estate context (office property)<br>✅ Faker.js-style realistic data<br>✅ Complete prop coverage |
| **card-offer-slide.stories.jsx** | Stories | ✅ 2 stories (Default + PropertyGrid)<br>✅ argTypes categorized (5 categories)<br>✅ PropertyGrid with 6 varied aspect ratios<br>✅ Autodocs enabled |

### 8.2 Key Architectural Decisions

#### Naming Convention
```
card-{bundle}-{view_mode}
card-offer-slide
```
- **Bundle**: `offer` (content type)
- **View mode**: `slide` (display context)
- **CSS Classes**: `.ps-card-offer-slide` (custom) + `.ps-card--offer--slide` (Drupal auto-generated)

#### Block Structure
```twig
{% embed '@components/card/card.twig' with {
  attributes: attributes.addClass('ps-card-offer-slide'),
  bundle: 'offer',
  view_mode: 'slide',
  /* ALL variables passed explicitly for block access */
  title: title,
  price: price,
  surface: surface,
  image: image,
  location: location,
  locationIcon: locationIcon,
  cta: cta,
  isFavorite: isFavorite
} %}
  {% block media %}...{% endblock %}
  {% block media_overlay %}...{% endblock %}
  {% block body %}...{% endblock %}
  {% block footer %}...{% endblock %}
{% endembed %}
```

#### CSS Token Architecture
```css
/* Overlay tokens (accessible in .ps-card__media descendants) */
:where(.ps-card.ps-card--offer--slide),
:where(.ps-card.ps-card-offer-slide) {
  --ps-card-offer-slide-overlay-inset: var(--size-3);
}

/* Component tokens + styles (single block) */
.ps-card-offer-slide {
  /* Tokens */
  --ps-card-offer-slide-max-width: 320px;
  --ps-card-offer-slide-image-aspect-ratio: 3/2;
  --ps-card-offer-slide-header-font-size: var(--font-size-6);
  /* ...38 more tokens */
  
  /* Styles with BEM nesting */
  max-width: var(--ps-card-offer-slide-max-width);
  
  &__image { aspect-ratio: var(--ps-card-offer-slide-image-aspect-ratio); }
  &__overlay { position: absolute; inset: var(--ps-card-offer-slide-overlay-inset); }
  &__header { display: inline-flex; white-space: nowrap; }
  /* ...15 more BEM elements */
}
```

#### JavaScript Behavior Pattern
```javascript
((Drupal, once) => {
  Drupal.behaviors.psCardOfferSlide = {
    attach(context) {
      const elements = once('psCardOfferSlideAutofit', '.ps-card-offer-slide__header', context);
      
      elements.forEach((element) => {
        // Auto-fit logic here (ratio-based font-size calculation)
      });
    },
  };
})(Drupal, once);
```

#### Storybook argTypes Pattern
```javascript
argTypes: {
  // Content category (primary data)
  title: { control: 'text', description: '...', table: { category: 'Content' } },
  price: { control: 'text', description: '...', table: { category: 'Content' } },
  
  // Appearance category (visual options)
  locationIcon: { control: 'text', description: '...', table: { category: 'Appearance' } },
  
  // Behavior category (interactions)
  isFavorite: { control: 'boolean', description: '...', table: { category: 'Behavior' } },
  
  // CTA category (actions)
  'cta.text': { control: 'text', description: '...', table: { category: 'CTA' } },
  
  // Drupal category (integration)
  bundle: { control: 'text', description: '...', table: { category: 'Drupal' } },
}
```

### 8.3 Complete Pattern Checklist

**✅ Study Card Offer Slide for these patterns:**

1. **Twig Architecture**
   - [ ] Variable defaults with `|default()`
   - [ ] Embed Card with `attributes.addClass()`
   - [ ] Pass ALL variables in `with {}` hash (no `only`)
   - [ ] Override 4 blocks: media, media_overlay, body, footer
   - [ ] Reuse 4 atoms: Image, Button, Heading, Icon
   - [ ] Wrapper div in media_overlay block
   - [ ] Custom BEM classes on all atoms

2. **CSS Architecture**
   - [ ] `:where(.ps-card.ps-card-{bundle}-{view_mode})` for overlay tokens
   - [ ] Single `.ps-card-{bundle}-{view_mode} {}` block for everything else
   - [ ] 40+ component tokens (all prefixed with component name)
   - [ ] BEM nesting with `&__element` syntax
   - [ ] Zero hardcoded values (all `var(--token)`)
   - [ ] Semantic colors (`--primary`, `--danger`, NOT raw colors)
   - [ ] Overlay wrapper positioning pattern
   - [ ] Focus-visible on all interactives

3. **JavaScript Pattern**
   - [ ] Drupal behaviors wrapper
   - [ ] `once()` for idempotency
   - [ ] Selector: `.ps-card-{bundle}-{view_mode}__element`
   - [ ] Lightweight algorithm (<50 lines)
   - [ ] Optional enhancement (graceful degradation)

4. **YAML Data**
   - [ ] Real Estate context (property-related)
   - [ ] Realistic Faker.js-style data
   - [ ] Complete prop coverage
   - [ ] NO Drupal schemas (`$schema`, `name`, `status`)

5. **Storybook Stories**
   - [ ] `tags: ['autodocs']` in export default
   - [ ] 2 core stories: Default + PropertyGrid/Showcase
   - [ ] argTypes categorized (Content, Appearance, Behavior, CTA, Drupal)
   - [ ] PropertyGrid with 6+ examples
   - [ ] Varied test data (different aspect ratios, states)
   - [ ] Real Estate context throughout

6. **README Documentation**
   - [ ] 269 lines total (complete reference)
   - [ ] Overview + Use Cases
   - [ ] Props table with all parameters
   - [ ] BEM structure diagram
   - [ ] Design tokens section (all 40+ tokens documented)
   - [ ] Accessibility section (WCAG 2.2 AA)
   - [ ] 4 usage examples (basic, favorite, minimal, grid)
   - [ ] Technical notes (Card inheritance, `:where()` pattern)
   - [ ] Browser support matrix
   - [ ] Changelog

### 8.4 Generation Template (Use This for New Cards)

```bash
# 1. Create component folder
cd source/patterns/components
mkdir card-{bundle}-{view_mode}
cd card-{bundle}-{view_mode}

# 2. Copy Card Offer Slide as template
cp ../card-offer-slide/card-offer-slide.twig card-{bundle}-{view_mode}.twig
cp ../card-offer-slide/card-offer-slide.css card-{bundle}-{view_mode}.css
cp ../card-offer-slide/card-offer-slide.yml card-{bundle}-{view_mode}.yml
cp ../card-offer-slide/card-offer-slide.stories.jsx card-{bundle}-{view_mode}.stories.jsx
cp ../card-offer-slide/README.md README.md

# 3. Find/replace throughout all files
# card-offer-slide → card-{bundle}-{view_mode}
# offer → {bundle}
# slide → {view_mode}
# Card Offer Slide → Your Card Title

# 4. Customize blocks, tokens, data based on your design spec
# 5. Test: npm run build && npm run watch
# 6. Commit with structured message
```

**Files to study line-by-line:**
- **Twig**: `card-offer-slide.twig` (150 lines) - Complete embed pattern
- **CSS**: `card-offer-slide.css` (196 lines) - Token + BEM architecture
- **JS**: `card-offer-slide.js` (36 lines) - Drupal behavior pattern
- **Stories**: `card-offer-slide.stories.jsx` (256 lines) - argTypes + PropertyGrid
- **README**: `README.md` (269 lines) - Complete documentation template

---

## 9. JavaScript Behaviors Pattern

### 9.1 When to Add JavaScript

**✅ Add JavaScript when:**
- Component needs client-side interaction (toggle, accordion, auto-fit)
- Behavior cannot be achieved with CSS alone
- Enhancement improves UX but page works without it (progressive enhancement)

**❌ Don't add JavaScript if:**
- Pure CSS solution exists (`:hover`, `:focus`, `:checked`)
- Behavior can be handled server-side (Drupal form API)
- Adds unnecessary complexity (YAGNI principle)

### 9.2 Drupal Behaviors Pattern

**Required structure** (see `card-offer-slide.js`):

```javascript
/**
 * Component Name - Behavior Description
 *
 * What it does, why it exists, algorithm summary.
 */

((Drupal, once) => {
  Drupal.behaviors.psCardYourName = {
    attach(context) {
      // Use once() for idempotency (prevents re-initialization)
      const elements = once(
        'psCardYourNameUniqueId',  // Unique identifier
        '.ps-card-your-name__target',  // Selector
        context  // Drupal context (document or AJAX-loaded fragment)
      );

      elements.forEach((element) => {
        // Your behavior logic here
        // Keep it lightweight (<50 lines)
        // Use vanilla JS (no jQuery)
      });
    },
  };
})(Drupal, once);
```

### 9.3 Best Practices

1. **Idempotency**: Always use `once()` to prevent duplicate initialization
2. **Context-aware**: Use `context` parameter for AJAX compatibility
3. **Lightweight**: Keep under 1 KB gzipped (50-100 lines max)
4. **Progressive enhancement**: Page works without JavaScript
5. **No dependencies**: Vanilla JS only (no jQuery, Lodash, etc.)
6. **Performance**: Use `requestAnimationFrame` for layout changes
7. **Accessibility**: Maintain keyboard navigation, ARIA states

### 9.4 Auto-Fit Text Algorithm (Card Offer Slide Reference)

**Use case**: Prevent text overflow by dynamically reducing font-size.

**Implementation** (36 lines, 0.48 kB gzipped):

```javascript
((Drupal, once) => {
  Drupal.behaviors.psCardOfferSlide = {
    attach(context) {
      const headers = once('psCardOfferSlideAutofit', '.ps-card-offer-slide__header', context);

      headers.forEach((header) => {
        // 1. Get container width
        const containerWidth = header.parentElement.clientWidth;
        const originalFontSize = parseFloat(window.getComputedStyle(header).fontSize);
        
        // 2. Wrap content in span for measurement
        const wrapper = document.createElement('span');
        wrapper.style.cssText = 'display: inline-block; white-space: nowrap;';
        while (header.firstChild) {
          wrapper.appendChild(header.firstChild);
        }
        header.appendChild(wrapper);
        
        // 3. Measure natural width
        const naturalWidth = wrapper.offsetWidth;

        // 4. Calculate ratio and apply new font-size
        if (naturalWidth > containerWidth) {
          const ratio = containerWidth / naturalWidth;
          const targetSize = Math.max(8, originalFontSize * ratio * 0.95); // Min 8px, 5% safety margin
          header.style.fontSize = `${targetSize}px`;
        }
      });
    },
  };
})(Drupal, once);
```

**Key techniques:**
- **Wrapper span**: Measure content width without layout constraints
- **Ratio-based**: `targetSize = originalSize × (containerWidth / naturalWidth) × 0.95`
- **Safety margin**: 0.95 factor prevents edge-case overflows
- **Minimum size**: `Math.max(8, ...)` ensures readability
- **Single-pass**: O(1) calculation, no binary search needed

**CSS companion** (enables transition):

```css
.ps-card-offer-slide__header {
  display: inline-flex;
  white-space: nowrap;
  max-width: 100%;
  transition: font-size 0.2s ease;  /* Smooth adjustment */
}
```

### 9.5 Library Registration (ps.libraries.yml)

**Manual entry required:**

```yaml
card-offer-slide:
  version: VERSION
  js:
    dist/js/card-offer-slide.js: { minified: true, preprocess: false }
  dependencies:
    - core/drupal
    - core/once
```

**Auto-sync script** (`scripts/sync-libraries.mjs`):
- Scans `source/patterns/` for `.js` files
- Generates library entries in `ps.libraries.yml`
- Runs automatically on `npm run build`

---

## 10. Storybook Stories Best Practices

### 10.1 Story Structure (Card Offer Slide Pattern)

**Required stories** (minimum 2):

1. **Default**: Interactive playground with all controls
2. **Showcase/Grid**: Multiple examples demonstrating variants

**Optional stories** (add if relevant):

3. **States**: Error, loading, empty states
4. **Responsive**: Mobile, tablet, desktop breakpoints
5. **A11y**: Keyboard navigation, screen reader demo

### 10.2 argTypes Categorization

**Standard categories** (use consistently across all card components):

```javascript
argTypes: {
  // ==========================================
  // Content - Primary data
  // ==========================================
  title: {
    control: 'text',
    description: 'Component title or heading',
    table: {
      category: 'Content',
      type: { summary: 'string' },
      defaultValue: { summary: 'Default Title' },
    },
  },
  
  // ==========================================
  // Appearance - Visual options
  // ==========================================
  variant: {
    control: { type: 'select', options: ['default', 'primary', 'secondary'] },
    description: 'Visual style variant',
    table: {
      category: 'Appearance',
      type: { summary: 'string' },
    },
  },
  
  // ==========================================
  // Behavior - Interactions
  // ==========================================
  isActive: {
    control: 'boolean',
    description: 'Active/selected state',
    table: {
      category: 'Behavior',
      type: { summary: 'boolean' },
      defaultValue: { summary: false },
    },
  },
  
  // ==========================================
  // Link/CTA - Actions
  // ==========================================
  'cta.text': {
    control: 'text',
    description: 'Call-to-action button text',
    table: {
      category: 'CTA',
      type: { summary: 'string' },
    },
  },
  
  // ==========================================
  // Drupal - Integration parameters
  // ==========================================
  bundle: {
    control: 'text',
    description: 'Drupal entity bundle (generates CSS classes)',
    table: {
      category: 'Drupal',
      type: { summary: 'string' },
    },
  },
}
```

**Benefits:**
- ✅ Consistent organization across components
- ✅ Easy to find related controls
- ✅ Clean Storybook UI (collapsible categories)
- ✅ Self-documenting structure

### 10.3 PropertyGrid Story Pattern

**Purpose**: Demonstrate component in realistic grid layout with varied data.

**Implementation** (Card Offer Slide reference):

```javascript
export const PropertyGrid = {
  render: () => {
    // Array of 6+ examples with VARIED data
    const properties = [
      {
        title: 'Office PARIS',
        price: '650 €/m²/an',
        image: { url: '/images/1-1.jpg', alt: '...' },  // Different aspect ratio
        isFavorite: true,  // Different state
      },
      {
        title: 'Retail LYON',
        price: '4 500 €/mois',
        image: { url: '/images/3-2.jpg', alt: '...' },
        isFavorite: false,
      },
      // ...4 more with varied data
    ];

    return `
      <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: var(--size-6); padding: var(--size-4);">
        ${properties.map((property) => componentTwig(property)).join('')}
      </div>
    `;
  },
  
  parameters: {
    docs: {
      description: {
        story:
          '**Responsive grid layout** demonstrating component with varied data.\n\n' +
          '**Grid Features**:\n' +
          '- Auto-fill responsive columns\n' +
          '- Mixed states (active/inactive)\n' +
          '- Varied content length\n' +
          '- Different image aspect ratios\n\n' +
          '**Use Cases**: Property listings, search results, product grids.',
      },
    },
  },
};
```

**Key principles:**
- **6+ examples**: Show diversity (not just 2-3 copies)
- **Varied data**: Different lengths, states, images
- **Responsive grid**: `auto-fill` + `minmax()` for fluid columns
- **Real context**: Use Real Estate data (offices, retail, warehouses)
- **Documentation**: Explain grid features and use cases

### 10.4 Image Asset Strategy

**Card Offer Slide demonstrates varied aspect ratios:**

```javascript
const properties = [
  { image: { url: '/images/1-1.jpg' } },    // 1:1 (square)
  { image: { url: '/images/3-2.jpg' } },    // 3:2 (native)
  { image: { url: '/images/16-9.jpg' } },   // 16:9 (wide)
  { image: { url: '/images/4-3.jpg' } },    // 4:3 (traditional)
  { image: { url: '/images/2-3.jpg' } },    // 2:3 (portrait)
  { image: { url: '/images/3-4.jpg' } },    // 3:4 (tall portrait)
];
```

**Available images:**
- `source/assets/images/1-1.jpg` - Square (1:1)
- `source/assets/images/3-2.jpg` - Standard (3:2)
- `source/assets/images/16-9.jpg` - Wide (16:9)
- `source/assets/images/4-3.jpg` - Traditional (4:3)
- `source/assets/images/2-3.jpg` - Portrait (2:3)
- `source/assets/images/3-4.jpg` - Tall portrait (3:4)
- `source/assets/images/building.jpg` - Real Estate generic

**Use case**: Test `aspect-ratio` CSS property normalization.

### 10.5 Real Estate Data Patterns

**Faker.js-style realistic content** (use throughout stories):

```javascript
// Property types
const propertyTypes = [
  'Bureau',           // Office
  'Local Commercial', // Retail space
  'Entrepôt',        // Warehouse
  'Immeuble',        // Building
  'Surface',         // Generic space
];

// Locations (European cities)
const locations = [
  'Paris - La Défense',
  'Lyon - Part-Dieu',
  'Marseille - Fos-sur-Mer',
  'Barcelona - Passeig de Gràcia',
  'Madrid - Salamanca',
  'Lisbon - Avenida da Liberdade',
];

// Price formats
const priceFormats = [
  '650 €/m²/an',     // Per m² per year
  '4 500 €/mois',    // Per month
  '20 000 € HT/HC',  // Excluding taxes
  '1 200 €',         // Simple amount
];

// Surface areas
const surfaceAreas = [
  '180 m²',
  '2 450 m²',
  '8 000 m²',
  '15 000 m²',
];
```

**Benefits:**
- ✅ Realistic preview (matches production content)
- ✅ Tests text overflow/wrapping with real lengths
- ✅ Multilingual context (French Real Estate terms)
- ✅ Consistent with project domain (BNP Paribas Real Estate)

---

## 11. Common Issues & Solutions

### Issue 1: "Token is not defined" in CSS (Component body elements)

**Symptom:** Browser shows `--ps-your-card-*` as invalid/unset.

**Cause:** Tokens defined outside main component selector.

**Solution:** Move ALL tokens inside `.ps-your-card {}` block:

```css
.ps-your-card {
  /* ALL tokens here */
  --ps-your-card-title-font-size: var(--font-size-2);
  
  /* Styles immediately after */
  &__title {
    font-size: var(--ps-your-card-title-font-size);
  }
}
```

---

### Issue 2: "Token is not defined" in overlay elements (media_overlay block)

**Symptom:** Browser shows `--ps-your-card-overlay-*` as invalid/unset for elements in `media_overlay`.

**Cause:** Elements in `media_overlay` are children of `.ps-card__media`, NOT `.ps-your-card`, so they cannot access component tokens.

**DOM structure causing the issue:**

```html
<article class="ps-card ps-your-card">  <!-- Tokens defined here -->
  <div class="ps-card__media">           <!-- Card's wrapper -->
    <div class="ps-your-card__overlay">  <!-- ❌ NOT a child of .ps-your-card -->
```

**Solution 1 - Quick fix (global tokens):**

```css
.ps-your-card {
  &__overlay {
    inset: var(--size-3);  /* Use global token directly */
    z-index: 10;           /* Or direct value */
  }
}
```

**Solution 2 - Long-term (`:where()` tokens - RECOMMENDED):**

```css
/* Define overlay tokens on Card selector for cascade access */
:where(.ps-card.ps-your-card) {
  --overlay-inset: var(--size-3);
  --favorite-size: var(--size-8);
  --favorite-bg: var(--white);
}

.ps-your-card {
  /* Body tokens stay here */
  --ps-your-card-title-font-size: var(--font-size-2);
  
  &__overlay {
    inset: var(--overlay-inset);  /* ✅ Accessible via :where() */
  }
  
  &__favorite {
    width: var(--favorite-size);   /* ✅ Works! */
  }
}
```

**Why `:where()` solution is better:**
- Tokens properly scoped to component
- Clear separation: `:where()` = overlay, selector = body
- Reusable values (not hardcoded)
- No global namespace pollution
- Zero specificity (easily overridable)

**Why this happens:** CSS cascade rules. `.ps-your-card__overlay` inherits from `.ps-card__media`, not from `.ps-your-card`.

---

### Issue 3: Variables not available in embed blocks

**Symptom:** `{{ title }}` is empty inside `{% block body %}`.

**Cause:** Using `only` in embed blocks scopes.

**Solution:** Remove `only` from embed (NOT from includes):

```twig
{# ✅ CORRECT #}
{% embed '@components/card/card.twig' with {
  attributes: attributes.addClass('ps-your-card')
} %}  {# NO only here #}
  {% block body %}
    <h3>{{ title }}</h3>  {# ✅ Works #}
  {% endblock %}
{% endembed %}
```

---

### Issue 4: Button/Image renders with malformed HTML

**Symptom:** `<button ps-button,,,,ps-button--icon-only,,,,...>`

**Cause:** Atom component uses old `attributes.addClass()` pattern.

**Solution:** Atom must handle `class` parameter directly:

```twig
{# In button.twig #}
{%- set class = class|default(null) -%}
{%- set classes = [
  'ps-button',
  class ? class : null  {# Add custom class #}
] -%}

<button class="{{ classes|join(' ')|trim }}"
  {%- if attributes %} {{ attributes|without('class') }}{% endif -%}
>
```

---

### Issue 5: Overlay button hidden behind image

**Symptom:** Favorite button not visible on image.

**Cause:** Missing wrapper div or z-index issue.

**Solution 1:** Always wrap overlay content in container div:

```twig
{% block media_overlay %}
  <div class="ps-your-card__overlay">  {# ← Wrapper required #}
    {% include '@elements/button/button.twig' %}
  </div>
{% endblock %}
```

**Solution 2:** Ensure proper CSS positioning:

```css
.ps-your-card {
  &__overlay {
    position: absolute;
    inset: var(--size-3);
    z-index: 10;  /* Above image */
  }
}
```

Card component already sets `.ps-card__media { position: relative }`.

---
    z-index: 1;  /* If still hidden */
  }
}
```

---

## 10. Advanced Patterns

### 10.1 Conditional Card Parameters

```twig
{# Use ternary for conditional params #}
{% embed '@components/card/card.twig' with {
  variant: isPremium ? 'elevated' : 'default',
  radius: hasImage ? 'md' : 'none',
  url: isClickable ? propertyUrl : null
} %}
```

### 10.2 Multiple Overlays in Media Block

```twig
{% block media %}
  {% include '@elements/image/image.twig' with {...} only %}
  
  {# Multiple overlays #}
  {% include '@elements/badge/badge.twig' with {
    class: 'ps-your-card__badge',
    text: 'New'
  } only %}
  
  {% include '@elements/button/button.twig' with {
    class: 'ps-your-card__favorite',
    icon: 'heart'
  } only %}
{% endblock %}
```

**CSS positioning:**

```css
.ps-your-card {
  &__badge {
    position: absolute;
    top: var(--size-3);
    left: var(--size-3);  /* Top left */
  }
  
  &__favorite {
    position: absolute;
    top: var(--size-3);
    right: var(--size-3);  /* Top right */
  }
}
```

### 10.3 Custom Content Layout

```twig
{% block body %}
  <div class="ps-your-card__meta">
    <span class="ps-your-card__date">{{ date }}</span>
    <span class="ps-your-card__category">{{ category }}</span>
  </div>
  
  <h3 class="ps-your-card__title">{{ title }}</h3>
  
  <p class="ps-your-card__description">{{ description }}</p>
{% endblock %}
```

**CSS using Card gap:**

```css
.ps-your-card {
  /* Card already handles .ps-card__body gap */
  /* Just style your elements */
  
  &__meta {
    display: flex;
    gap: var(--size-2);
    font-size: var(--font-size-0);
    color: var(--text-secondary);
  }
  
  &__title {
    font-size: var(--font-size-3);
    font-weight: var(--font-weight-600);
  }
}
```

---

## 11. Performance Considerations

### Component Nesting Limits

**Acceptable:** Card → (Image, Button) = 2 levels

**Caution:** Card → Component → (Image, Button) = 3 levels

**Avoid:** Card → Component → Component → (Image, Button) = 4+ levels

### When to Flatten Structure

If performance is critical or component is very simple:

```twig
{# Simple flat version without Card #}
<article class="ps-simple-card">
  <img src="{{ image }}" alt="{{ alt }}">
  <div class="ps-simple-card__content">
    <h3>{{ title }}</h3>
  </div>
</article>
```

**Trade-offs:**
- ✅ Faster rendering
- ✅ Less CSS cascade
- ❌ Must reimplement Card features (border, shadow, hover)
- ❌ No layout variants
- ❌ More maintenance

---

## 12. Complete Card Generation Workflow

### 12.1 Pre-Generation Phase (Planning)

**Input**: Design specification (`docs/design/molecules/{component}.md`)

**Steps:**

1. **Analyze design spec**
   - [ ] Identify content elements (title, price, image, etc.)
   - [ ] Map to Card blocks (which go in media/header/body/footer?)
   - [ ] List required atoms (Image, Button, Heading, Icon, Badge, etc.)
   - [ ] Note unique features (auto-fit text, image carousel, etc.)

2. **Choose naming**
   - [ ] Determine bundle (content type): `offer`, `article`, `product`, `event`
   - [ ] Determine view_mode (display context): `slide`, `teaser`, `full`, `search`
   - [ ] Component name: `card-{bundle}-{view_mode}` (e.g., `card-offer-slide`)

3. **Check dependencies**
   - [ ] Verify all required atoms exist in `source/patterns/elements/`
   - [ ] Check atom versions are production-ready (no WIP/deprecated)
   - [ ] Identify missing atoms → create them first (separate workflow)

### 12.2 Scaffolding Phase (File Creation)

**Method 1 - Manual copy from Card Offer Slide:**

```bash
cd source/patterns/components
mkdir card-{bundle}-{view_mode}
cd card-{bundle}-{view_mode}

# Copy all 5 files from reference implementation
cp ../card-offer-slide/card-offer-slide.twig card-{bundle}-{view_mode}.twig
cp ../card-offer-slide/card-offer-slide.css card-{bundle}-{view_mode}.css
cp ../card-offer-slide/card-offer-slide.yml card-{bundle}-{view_mode}.yml
cp ../card-offer-slide/card-offer-slide.stories.jsx card-{bundle}-{view_mode}.stories.jsx
cp ../card-offer-slide/README.md README.md

# Optional: Copy JavaScript if behavior needed
cp ../card-offer-slide/card-offer-slide.js card-{bundle}-{view_mode}.js
```

**Method 2 - Generator script:**

```bash
npm run generate:pattern -- --type=component --name="card-{bundle}-{view_mode}"
```

**Method 3 - VS Code snippets:**

Type `pscomponent` in `.twig` file → Tab → Fill prompts

### 12.3 Find/Replace Phase (Bulk Renaming)

**Use VS Code Find/Replace (Cmd+Shift+H / Ctrl+Shift+H):**

| Find | Replace | Scope |
|------|---------|-------|
| `card-offer-slide` | `card-{bundle}-{view_mode}` | All 5 files |
| `Card Offer Slide` | `Card {Bundle} {ViewMode}` | All 5 files |
| `offer` | `{bundle}` | Twig (bundle param only) |
| `slide` | `{view_mode}` | Twig (view_mode param only) |
| `psCardOfferSlide` | `psCard{Bundle}{ViewMode}` | JS (behavior name) |

**Examples:**

```bash
# For card-article-teaser:
card-offer-slide → card-article-teaser
Card Offer Slide → Card Article Teaser
offer → article
slide → teaser
psCardOfferSlide → psCardArticleTeaser

# For card-product-grid:
card-offer-slide → card-product-grid
Card Offer Slide → Card Product Grid
offer → product
slide → grid
psCardOfferSlide → psCardProductGrid
```

### 12.4 Customization Phase (Content Adaptation)

**File-by-file modifications:**

#### A. Twig Template (`card-{bundle}-{view_mode}.twig`)

1. **Header comment**
   - [ ] Update description (what makes this variant unique)
   - [ ] Update `@param` list (add/remove/modify parameters)
   - [ ] Update usage example with your data

2. **Variable defaults**
   - [ ] Remove unused props (e.g., if no `price`, delete `price|default()`)
   - [ ] Add new props (e.g., `author`, `date`, `category`)
   - [ ] Update default values

3. **Embed block**
   - [ ] Update `with {}` hash with your variables
   - [ ] Choose Card params (`variant`, `radius`, `size`)
   - [ ] Keep `attributes.addClass()` with your component name

4. **Block overrides**
   - [ ] `media`: Customize image or replace with video/carousel
   - [ ] `media_overlay`: Add/remove badges, buttons (favorite, share, etc.)
   - [ ] `header`: Add metadata (date, category, tags)
   - [ ] `body`: Customize content layout (title, description, stats)
   - [ ] `footer`: Customize CTA (button, link, author info)

5. **Atom includes**
   - [ ] Update atom parameters (icon names, button variants, etc.)
   - [ ] Update custom classes (`.ps-card-{bundle}-{view_mode}__element`)

#### B. CSS Stylesheet (`card-{bundle}-{view_mode}.css`)

1. **`:where()` tokens** (if using media_overlay)
   - [ ] Update token names: `--{component-name}-overlay-*`
   - [ ] Update values based on design spec

2. **Component tokens**
   - [ ] Update all token names: `--ps-card-{bundle}-{view_mode}-*`
   - [ ] Add new tokens for your elements
   - [ ] Remove unused tokens
   - [ ] Update values from design spec (sizes, colors, spacing)

3. **BEM selectors**
   - [ ] Update class names: `.ps-card-{bundle}-{view_mode}__*`
   - [ ] Add selectors for new elements
   - [ ] Remove selectors for unused elements
   - [ ] Update styles based on design spec

4. **Validation**
   - [ ] Zero hardcoded values (all `var(--token)`)
   - [ ] Semantic colors only (`--primary`, NOT `--green-600`)
   - [ ] Focus-visible on all interactives
   - [ ] Transitions on state changes

#### C. JavaScript (if needed)

1. **Behavior name**
   - [ ] Update: `Drupal.behaviors.psCard{Bundle}{ViewMode}`
   - [ ] Update once ID: `psCard{Bundle}{ViewMode}UniqueId`

2. **Selector**
   - [ ] Update: `.ps-card-{bundle}-{view_mode}__target`

3. **Algorithm**
   - [ ] Customize logic based on your needs
   - [ ] Keep lightweight (<50 lines)
   - [ ] Test idempotency (run multiple times)

4. **Library registration**
   - [ ] Add entry in `ps.libraries.yml` (or run `npm run build` to auto-generate)

#### D. YAML Data (`card-{bundle}-{view_mode}.yml`)

1. **Update all data**
   - [ ] Replace with your component's data structure
   - [ ] Use Real Estate context (or project domain)
   - [ ] Use Faker.js-style realistic values
   - [ ] Ensure complete prop coverage

2. **Examples**
   ```yaml
   # Article teaser
   title: 'Nouveau rapport marché immobilier Q4 2025'
   author: 'Marie Dupont'
   date: '15 décembre 2025'
   category: 'Analyse'
   image: { url: '/images/article.jpg', alt: 'Chart' }
   
   # Product grid
   title: 'Pack Services Premium'
   price: '1 200 €/mois'
   features: ['Support 24/7', 'API Access', 'Analytics']
   badge: 'Popular'
   ```

#### E. Storybook Stories (`card-{bundle}-{view_mode}.stories.jsx`)

1. **Export default**
   - [ ] Update title: `'Components/Card {Bundle} {ViewMode}'`
   - [ ] Keep `tags: ['autodocs']`

2. **Component description**
   - [ ] Update key features list
   - [ ] Update use cases

3. **argTypes**
   - [ ] Remove unused controls
   - [ ] Add new controls for your props
   - [ ] Keep categorization (Content, Appearance, Behavior, CTA, Drupal)
   - [ ] Update descriptions

4. **Default story**
   - [ ] Update story description
   - [ ] Test all controls work

5. **Grid/Showcase story**
   - [ ] Update story name (PropertyGrid, ArticleList, ProductGrid, etc.)
   - [ ] Create 6+ varied examples
   - [ ] Use different images (varied aspect ratios)
   - [ ] Mix states (active, favorite, disabled, etc.)
   - [ ] Use realistic Real Estate data
   - [ ] Update grid description

#### F. README (`README.md`)

1. **Overview**
   - [ ] Update description
   - [ ] Update use cases list

2. **Props table**
   - [ ] Add/remove/modify rows for your props
   - [ ] Update types, defaults, descriptions

3. **BEM structure**
   - [ ] Update HTML structure diagram
   - [ ] Update class names

4. **Tokens section**
   - [ ] Document all your CSS variables
   - [ ] Update purposes and values

5. **Examples**
   - [ ] Update Twig code samples with your data
   - [ ] Update use case scenarios

### 12.5 Testing Phase

**Build validation:**

```bash
npm run build
```

**Expected output:**
```
✓ Biome lint check (no errors)
✓ Biome format check (all formatted)
✓ Icons build (sprite generated)
✓ Vite build (CSS compiled)
✓ dist/css/styles.css (454.30 kB)
✓ dist/js/card-{bundle}-{view_mode}.js (if JS file exists)
```

**Visual testing:**

```bash
npm run watch
# Opens http://localhost:6006
```

**Test checklist:**
- [ ] Find your component in sidebar: `Components/Card {Bundle} {ViewMode}`
- [ ] Default story renders correctly
- [ ] All controls in Storybook work (change values, see updates)
- [ ] Grid/Showcase story shows 6+ varied examples
- [ ] Images load (check aspect-ratio normalization)
- [ ] Buttons/links are interactive (hover, focus)
- [ ] Icons display correctly (no missing icons)
- [ ] Typography matches design spec
- [ ] Colors use semantic tokens (inspect with DevTools)
- [ ] Spacing matches design spec
- [ ] Responsive behavior (resize to mobile)
- [ ] Keyboard navigation works (Tab, Enter)
- [ ] Focus indicators visible

**Conformity audit (100% required):**

```bash
# Use checklist from Section 7.4
# Or create audit script (future enhancement)
```

### 12.6 Commit Phase

**Structured commit message:**

```bash
git add source/patterns/components/card-{bundle}-{view_mode}/
git commit -m "feat(components): Add Card {Bundle} {ViewMode} with Card inheritance

- Implement 5-file structure (twig, css, yml, stories, README)
- Embed Card component for visual structure reuse
- Reuse {list atoms} atoms
- Support {list key features}
- {list unique features: auto-fit, carousel, etc.}
- Full Autodocs with categorized argTypes
- WCAG 2.2 AA compliant (keyboard, focus-visible, ARIA)
- References spec: docs/design/molecules/card-{bundle}-{view_mode}.md
- Pattern: card-{bundle}-{view_mode} naming convention"
```

**Update changelog:**

```bash
# Edit docs/ps-design/CHANGELOG.md
## [YYYY-MM-DD] - Card {Bundle} {ViewMode}

### Added
- **Card {Bundle} {ViewMode}** (Component/Molecule)
  - Card inheritance pattern for visual structure
  - {Atom 1}, {Atom 2}, {Atom 3} integration
  - {Feature 1}: {description}
  - {Feature 2}: {description}
  - Status: ✅ Complete ({X}/87 components)
```

### 12.7 Success Criteria

**Component is COMPLETE when:**

- ✅ Build passes without errors/warnings
- ✅ All 5 files present (6 if JavaScript needed)
- ✅ Storybook renders correctly (2+ stories)
- ✅ Visual matches design spec (Typography, spacing, colors)
- ✅ Accessibility validated (WCAG 2.2 AA minimum)
- ✅ Conformity audit: 100% score
- ✅ README complete (269+ lines with full documentation)
- ✅ Git commit with structured message
- ✅ Changelog updated

**Ready for code review when:**

- ✅ All success criteria met
- ✅ No console errors in browser
- ✅ No "token is not defined" in DevTools
- ✅ Keyboard navigation works (Tab, Enter, Space)
- ✅ Focus indicators visible
- ✅ Tokens use semantic colors (no raw hex codes)
- ✅ BEM naming consistent
- ✅ Atoms reused (not reimplemented)

---

## 13. Summary Checklist

**Before starting:**
- [ ] Read this entire document
- [ ] Study Card component (`card.twig`, `card.css`)
- [ ] Review reference example (`offer-card-list`)
- [ ] Read design spec for your component

**During implementation:**
- [ ] Use `{% embed '@components/card/card.twig' %}`
- [ ] Pass `attributes.addClass('ps-your-card')`
- [ ] Override only needed blocks (media, body, footer)
- [ ] Reuse atoms with `{% include %}` + custom classes
- [ ] Define ALL tokens in single `.ps-your-card {}` block
- [ ] Use semantic colors (`--primary`, `--danger`, NOT raw colors)
- [ ] Test in Storybook frequently

**Before commit:**
- [ ] Build passes: `npm run build`
- [ ] Visual check: `npm run watch`
- [ ] 100% conformity audit
- [ ] Structured commit message
- [ ] Changelog updated

---

**Maintainers:** Design System Team  
**Last Updated:** 2025-12-10  
**Version:** 1.0.0
