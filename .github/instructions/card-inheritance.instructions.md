---
title: Card Component Inheritance Pattern
version: 2.1.0
lastUpdated: 2025-12-10
applyTo:
  - "source/patterns/components/card-*.twig"
  - "source/patterns/components/card-*.css"
priority: CRITICAL
changelog:
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

1. [Naming Convention](#naming-convention)
2. [Architecture Overview](#architecture-overview)
3. [Card Component Analysis](#card-component-analysis)
4. [Twig Inheritance Pattern](#twig-inheritance-pattern)
5. [CSS Integration Strategy](#css-integration-strategy)
6. [Atomic Component Reuse](#atomic-component-reuse)
7. [Implementation Checklist](#implementation-checklist)
8. [Validation & Testing](#validation--testing)

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

## 8. Reference Example

**See `offer-card-list` for complete reference implementation:**

- **Twig**: `source/patterns/components/offer-card-list/offer-card-list.twig`
- **CSS**: `source/patterns/components/offer-card-list/offer-card-list.css`
- **YAML**: `source/patterns/components/offer-card-list/offer-card-list.yml`
- **Stories**: `source/patterns/components/offer-card-list/offer-card-list.stories.jsx`
- **README**: `source/patterns/components/offer-card-list/README.md`

**Key patterns demonstrated:**
- ✅ Card embed with `attributes.addClass('ps-offer-card-list')`
- ✅ Image atom reuse with custom class
- ✅ Button atom reuse with `create_attribute()`
- ✅ All tokens in single CSS block
- ✅ Overlay positioning in media block
- ✅ Complete accessibility (ARIA, focus-visible)
- ✅ Real Estate context data
- ✅ Autodocs with categorized argTypes

---

## 9. Common Issues & Solutions

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

## 12. Summary Checklist

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
