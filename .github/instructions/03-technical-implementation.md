---
title: Technical Implementation Standards
version: 4.0.0
lastUpdated: 2025-12-12
priority: HIGH
status: ACTIVE
---

# 03 - Technical Implementation Standards

**Purpose**: Technical reference for writing code across all languages (CSS, Twig, Storybook, JavaScript, Accessibility).

**When to use**: Consult when implementing component code, need syntax examples, or verify technical standards.

**Related files**:
- [01-core-principles.md](01-core-principles.md) - Design philosophy and foundations
- [02-component-development.md](02-component-development.md) - Complete workflow
- [04-quality-assurance.md](04-quality-assurance.md) - Validation and testing
- [05-maintenance.md](05-maintenance.md) - Token creation and refactoring

---

## 📐 Table of Contents

1. [CSS Standards](#1-css-standards)
2. [Twig Standards](#2-twig-standards)
3. [YAML Standards](#3-yaml-standards)
4. [Storybook Standards](#4-storybook-standards)
5. [JavaScript Standards](#5-javascript-standards)
6. [Accessibility Standards](#6-accessibility-standards)

---

## 1. CSS Standards

### 1.1 Design Tokens (ABSOLUTE RULE)

**❌ NEVER hardcode values**:
```css
/* ❌ WRONG */
color: #00915A;
padding: 16px 24px;
transition: 150ms ease;
font-size: 18px;
```

**✅ ALWAYS use tokens**:
```css
/* ✅ CORRECT */
color: var(--primary);
padding: var(--size-4) var(--size-6);
transition: var(--duration-fast) var(--ease-3);
font-size: var(--font-size-3);
```

**Token Categories**:

| Category | File | Examples |
|----------|------|----------|
| Colors | `colors.css` | `--gray-600`, `--green-600` |
| Brand | `brand.css` | `--primary`, `--secondary`, `--success` |
| Fonts | `fonts.css` | `--font-size-2`, `--font-weight-600` |
| Sizes | `sizes.css` | `--size-4`, `--size-6` |
| Borders | `borders.css` | `--radius-2`, `--border-size-1` |
| Shadows | `shadows.css` | `--shadow-2`, `--shadow-4` |
| Animations | `animations.css` | `--duration-fast`, `--duration-normal` |
| Easing | `easing.css` | `--ease-3`, `--ease-in-out-4` |
| Z-Index | `zindex.css` | `--z-dropdown`, `--z-modal` |

### 1.2 CSS Variables System (3-Layer Architecture)

**Layer 1: Root Primitives** (global tokens):
```css
/* source/props/colors.css */
:root {
  --green-600: hsl(162, 72%, 38%);
  --primary: var(--green-600);   /* Semantic alias */
  --size-4: 1rem;
}
```

**Layer 2: Component-Scoped Variables** (MANDATORY for all components):
```css
/* button.css - ATOM (autonomous) */
.ps-button {
  /* Layer 2: Component defaults (enable overrides) */
  --ps-button-padding-y: var(--size-3);
  --ps-button-padding-x: var(--size-6);
  --ps-button-bg: var(--primary);
  --ps-button-color: var(--white);
  
  /* Use component variables */
  padding: var(--ps-button-padding-y) var(--ps-button-padding-x);
  background: var(--ps-button-bg);
  color: var(--ps-button-color);
}
```

**Layer 2 for composing components** (Molecules+):
```css
/* card.css - MOLECULE (composes atoms) */
.ps-card {
  /* Token-First STEP 3: Override child tokens */
  --ps-button-size: var(--size-6);
  --ps-badge-font-size: var(--font-size-0);
  
  /* Own component variables */
  --ps-card-padding: var(--size-6);
  --ps-card-gap: var(--size-4);
  
  /* Use component variables */
  padding: var(--ps-card-padding);
  gap: var(--ps-card-gap);
}
```

**Layer 3: Context Overrides** (modifiers):
```css
.ps-button--large {
  --ps-button-padding-y: var(--size-4);
  --ps-button-padding-x: var(--size-8);
}

.ps-button--primary {
  --ps-button-bg: var(--primary);
}
```

### 1.3 CSS Nesting (MANDATORY for new components)

**Use `postcss-nested` with `&` syntax**:

```css
.ps-component {
  /* 1. Component-scoped variables */
  --ps-component-bg: var(--primary);
  
  /* 2. Base styles */
  display: flex;
  gap: var(--size-2);
  background: var(--ps-component-bg);
  
  /* 3. Elements */
  &__icon {
    font-size: var(--font-size-3);
  }
  
  &__text {
    flex: 1;
  }
  
  /* 4. Modifiers - Variants */
  &--primary {
    --ps-component-bg: var(--primary);
  }
  
  &--secondary {
    --ps-component-bg: var(--secondary);
  }
  
  /* 5. Modifiers - Sizes */
  &--large {
    padding: var(--size-4);
  }
  
  /* 6. States */
  &:hover:not(:disabled) {
    transform: translateY(-1px);
  }
  
  &:focus-visible {
    outline: var(--border-size-2) solid var(--secondary);
    outline-offset: var(--border-size-2);
  }
  
  &:disabled {
    opacity: 0.5;
    cursor: not-allowed;
  }
}
```

**Nesting depth limit: Maximum 3 levels**

```css
/* ✅ CORRECT - 3 levels max */
.ps-component {
  &__element {
    &--modifier {
      /* OK */
    }
  }
}

/* ❌ WRONG - 4+ levels */
.ps-component {
  &__wrapper {
    &__inner {
      &__content { /* FORBIDDEN */ }
    }
  }
}
```

### 1.4 Cascade Order (CRITICAL)

**Base styles MUST come before modifiers**:

```css
/* ✅ CORRECT - Base first, modifiers after */
.ps-avatar {
  &__text {
    font-size: var(--font-size-2); /* Base md = 16px */
  }
  
  /* Modifiers AFTER base */
  &--xs &__text {
    font-size: var(--font-size-0); /* 12px - overrides base */
  }
  
  &--lg &__text {
    font-size: var(--font-size-4); /* 22px - overrides base */
  }
}

/* ❌ WRONG - Modifiers before base */
.ps-avatar {
  &--xs &__text {
    font-size: var(--font-size-0); /* Will be overridden! */
  }
  
  &__text {
    font-size: var(--font-size-2); /* Wins (written after) */
  }
}
```

### 1.5 Focus-Visible (MANDATORY for interactives)

**ALL interactive elements MUST have focus-visible**:

```css
.ps-button {
  /* Base and hover states */
  &:hover:not(:disabled) {
    background: var(--primary-hover);
  }
  
  /* MANDATORY focus-visible */
  &:focus-visible {
    outline: var(--border-size-2) solid var(--secondary);
    outline-offset: var(--border-size-2);
  }
  
  /* Active state */
  &:active:not(:disabled) {
    transform: translateY(0);
  }
  
  /* Disabled state */
  &:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    pointer-events: none;
  }
}
```

### 1.6 Responsive Design (MANDATORY)

**Mobile-first approach** with PostCSS custom media queries.

#### Breakpoints Reference

Available breakpoints from `source/props/media.css`:

| Breakpoint | Min Width | Usage |
|------------|-----------|-------|
| `--mobile-sm` | 400px | Small mobile adjustments |
| `--mobile` | 640px | Large mobile / Small tablet |
| `--tablet` | 768px | Tablet portrait |
| `--laptop` | 1024px | Laptop / Small desktop |
| `--desktop` | 1280px | Desktop |
| `--desktop-large` | 1440px | Large desktop |

#### Standard Pattern (REQUIRED)

**ALL components MUST include ALL breakpoints**, even if empty (as comments):

```css
.ps-component {
  /* Base styles = mobile-first (no media query) */
  --ps-component-padding: var(--size-2);
  --ps-component-gap: var(--size-2);
  
  padding: var(--ps-component-padding);
  gap: var(--ps-component-gap);
  
  /* Mobile-sm (400px+) */
  @media (--mobile-sm) {
    /* Component-specific adjustments if needed */
  }
  
  /* Mobile (640px+) */
  @media (--mobile) {
    --ps-component-padding: var(--size-3);
  }
  
  /* Tablet (768px+) */
  @media (--tablet) {
    --ps-component-padding: var(--size-4);
    --ps-component-gap: var(--size-3);
  }
  
  /* Laptop (1024px+) */
  @media (--laptop) {
    /* Desktop-specific adjustments if needed */
  }
  
  /* Desktop (1280px+) */
  @media (--desktop) {
    --ps-component-padding: var(--size-6);
  }
  
  /* Desktop-large (1440px+) */
  @media (--desktop-large) {
    --ps-component-padding: var(--size-8);
  }
}
```

#### Container Pattern (Reference Example)

Standard container with responsive padding:

```css
.container {
  margin-inline: auto;
  max-inline-size: var(--size-max-content-width); /* 1376px */
  padding-inline: var(--size-4);

  @media (--desktop) {
    padding-inline: var(--size-8);
  }

  @media (--desktop-large) { /* 1440px */
    padding-inline: 0;
  }
}
```

#### Rules

1. **Mobile-first** = Base styles without media query (smallest screens)
2. **Override component variables** = Change CSS custom properties, not direct values
3. **All breakpoints present** = Include empty blocks with comments for future adjustments
4. **PostCSS syntax** = Use `@media (--breakpoint-name)`, not `@media (min-width: ...)`
5. **Logical properties** = Use `inline`/`block` axis (`padding-inline`, `margin-block`)

#### Examples

**Simple responsive padding**:
```css
.ps-card {
  --ps-card-padding: var(--size-4);
  padding: var(--ps-card-padding);
  
  @media (--tablet) {
    --ps-card-padding: var(--size-6);
  }
  
  @media (--desktop) {
    --ps-card-padding: var(--size-8);
  }
}
```

**Layout changes**:
```css
.ps-navigation {
  flex-direction: column;
  
  @media (--tablet) {
    flex-direction: row;
  }
}
```

**Visibility toggles**:
```css
.ps-mobile-menu {
  display: flex;
  
  @media (--tablet) {
    display: none; /* Hide on tablet+ */
  }
}

.ps-desktop-menu {
  display: none;
  
  @media (--tablet) {
    display: flex; /* Show on tablet+ */
  }
}
```

### 1.7 Performance Best Practices

**Animate transform/opacity only** (GPU-accelerated):

```css
/* ✅ GOOD - GPU-accelerated */
.ps-component {
  transition: 
    transform var(--duration-fast) var(--ease-3),
    opacity var(--duration-fast) var(--ease-3);
  
  &:hover {
    transform: translateY(-2px); /* GPU */
    opacity: 0.9; /* GPU */
  }
}

/* ❌ BAD - Causes reflow */
.ps-component:hover {
  width: 110%; /* Reflow */
  margin-top: -5px; /* Reflow */
}
```

---

## 2. Twig Standards

### 2.1 Drupal Compatibility (CRITICAL)

**⚠️ MANDATORY**: All Twig MUST be Drupal 10/11 compatible.

**Drupal Twig limitations**:
- ❌ NO arrow functions (`v => v`)
- ❌ NO JavaScript methods (`.filter()`, `.map()`, `.reduce()`)
- ❌ NO spread operator (`...`)
- ✅ ONLY Twig native functions and filters

### 2.2 Header Comment (MANDATORY)

```twig
{#
 * Component Name (Level/Type)
 * @param type name - Description (required/optional, default: value)
 * @param type name - Description (required/optional, default: value)
 #}
```

**Example**:
```twig
{#
 * Card (Component/Molecule)
 * @param string title - Card title (required)
 * @param string description - Card description (optional)
 * @param string image_src - Image source URL (optional)
 * @param string badge_text - Badge text (optional)
 * @param string badge_color - Badge color (optional, default: 'primary')
 * @param object attributes - Additional HTML attributes (optional)
 #}
```

### 2.3 Whitespace Control (MANDATORY)

**Use Twig's whitespace control operators** to generate clean, single-line HTML output:

**Operators**:
- `{%-` - Trim whitespace **before** tag
- `-%}` - Trim whitespace **after** tag
- `{{-` - Trim whitespace **before** output
- `-}}` - Trim whitespace **after** output

**✅ CORRECT - Clean single-line output**:
```twig
<div class="{{ classes|join(' ')|trim }}"
  {%- if attributes %} {{ attributes }}{% endif -%}
>
  {{- content -}}
  {%- if icon -%}
    <span class="icon" data-icon="{{ icon }}"></span>
  {%- endif -%}
</div>
```

**Output**:
```html
<div class="ps-component" data-id="123">Content<span class="icon" data-icon="check"></span></div>
```

**❌ WRONG - No whitespace control**:
```twig
<div class="{{ classes|join(' ')|trim }}"
  {% if attributes %} {{ attributes }}{% endif %}
>
  {{ content }}
  {% if icon %}
    <span class="icon" data-icon="{{ icon }}"></span>
  {% endif %}
</div>
```

**Output** (with unwanted line breaks and spaces):
```html
<div class="ps-component" data-id="123">
  Content
  
    <span class="icon" data-icon="check"></span>
  
</div>
```

**Best practices**:
1. **Variables**: Always use `{%-` and `-%}` for `{% set %}` blocks
2. **Conditionals**: Use `{%- if %}` and `{%- endif -%}` to remove surrounding whitespace
3. **Content**: Use `{{-` and `-}}` around text/variables that should be inline
4. **Readability**: Keep template readable with indentation, Twig removes whitespace at render time

### 2.4 Default Values

```twig
{%- set color = color|default('primary') -%}
{%- set size = size|default('medium') -%}
{%- set disabled = disabled|default(false) -%}
{%- set orientation = orientation|default('horizontal') -%}
```

### 2.5 Classes Construction (Drupal-Compatible)

**✅ CORRECT - Ternary with `null`**:

```twig
{%- set classes = [
  'ps-component',
  size != 'medium' ? 'ps-component--' ~ size : null,
  color != 'primary' ? 'ps-component--' ~ color : null,
  disabled ? 'ps-component--disabled' : null,
  pill ? 'ps-component--pill' : null
] -%}

<div class="{{ classes|join(' ')|trim }}"
  {%- if attributes %} {{ attributes }}{% endif -%}
>
```

**Why `null`?** Twig's `join()` automatically skips `null` values.

**❌ WRONG - Arrow functions**:
```twig
{%- set classes = ['ps-component', size, color]|filter(v => v) -%}
{# ERROR: Arrow functions NOT supported in Drupal Twig #}
```

### 2.6 Attributes Parameter (MANDATORY for Drupal)

**⚠️ CRITICAL**: ALL components MUST include `attributes` parameter for Drupal integration.

**Why mandatory?**
- Drupal adds CSS classes, IDs, data attributes via `{{ attributes }}`
- Without this, components cannot be customized in Drupal templates
- Required for accessibility (ARIA attributes), JavaScript hooks, styling overrides

**Standard pattern**:

```twig
{#
 * Component Name
 * @param object attributes - Additional HTML attributes (optional)
 #}

<div class="{{ classes|join(' ')|trim }}"
  {%- if attributes %} {{ attributes|without('class') }}{% endif -%}
>
  <!-- Component content -->
</div>
```

**✅ CORRECT - Full example**:

```twig
{#
 * Badge component
 * @param string text - Badge text (required)
 * @param string color - primary|secondary|success|danger|warning|info (default: primary)
 * @param object attributes - Additional HTML attributes (optional)
 #}

{%- set color = color|default('primary') -%}
{%- set class = class|default(null) -%}

{%- set badge_classes = [
  'ps-badge',
  color != 'primary' ? 'ps-badge--' ~ color : null,
  class ? class : null
] -%}

<span class="{{ badge_classes|join(' ')|trim }}"
  {%- if attributes %} {{ attributes|without('class') }}{% endif -%}
>
  {{ text }}
</span>
```

**Key rules**:
1. **Always use `|without('class')`** - Prevents class duplication (classes handled separately)
2. **Document in header** - `@param object attributes - Additional HTML attributes (optional)`
3. **Conditional check** - `{%- if attributes %}` prevents errors when not passed
4. **Apply to root element** - Main component container gets attributes

**Drupal usage example**:
```twig
{# In Drupal template (e.g., node--article.html.twig) #}
{% include '@elements/badge/badge.twig' with {
  text: 'New',
  color: 'success',
  attributes: create_attribute()
    .addClass('custom-badge')
    .setAttribute('data-tracking', 'badge-click')
    .setAttribute('id', 'badge-' ~ node.id)
} only %}

{# Renders: <span class="ps-badge ps-badge--success custom-badge" data-tracking="badge-click" id="badge-123">New</span> #}
```

**Exception**: Base/Documentation components (colors.stories.jsx, fonts.stories.jsx) may omit `attributes` as they're not used in Drupal.

### 2.7 Composition with Includes

**Use `{% include %}` with `only` keyword**:

```twig
{# Include atom with specific props #}
{%- include '@elements/icon/icon.twig' with {
  icon: icon_name,
  size: 'medium',
  attributes: create_attribute().addClass('ps-component__icon')
} only -%}

{# Include with conditional props #}
{%- include '@elements/button/button.twig' with {
  text: cta_text,
  color: cta_color|default('primary'),
  url: cta_url,
  size: size
} only -%}
```

**Why `only`?** Prevents variable pollution—only specified props are passed.

### 2.8 Conditional Rendering

```twig
{# Simple conditional #}
{%- if icon -%}
  <span class="ps-component__icon" data-icon="{{ icon }}"></span>
{%- endif -%}

{# Conditional with else #}
{%- if has_image -%}
  <img src="{{ src }}" alt="{{ alt }}" />
{%- else -%}
  <span class="ps-avatar__text">{{ initials }}</span>
{%- endif -%}

{# Multiple conditions #}
{%- if condition_a -%}
  <!-- Content A -->
{%- elseif condition_b -%}
  <!-- Content B -->
{%- else -%}
  <!-- Fallback -->
{%- endif -%}
```

### 2.7 Dynamic Tags

```twig
{%- set tag = url ? 'a' : 'button' -%}

<{{ tag }}
  class="{{ classes|join(' ')|trim }}"
  {%- if url %} href="{{ url }}"{% endif -%}
  {%- if target %} target="{{ target }}"{% endif -%}
  {%- if disabled and not url %} disabled{% endif -%}
>
  {{ text }}
</{{ tag }}>
```

### 2.8 Whitespace Control

```twig
{# Remove whitespace with - #}
{%- set var = value -%}

{# Inline elements (no whitespace) #}
<span class="ps-component__text">{%- if text -%}{{ text }}{%- endif -%}</span>

{# Block elements (whitespace OK) #}
<div class="ps-component__content">
  {% if content %}
    {{ content }}
  {% endif %}
</div>
```

### 2.9 Real Estate Content Vocabulary

**ALL text content should use Real Estate vocabulary** (for realism, NOT for creating dedicated "RealEstateContext" stories):

```twig
{# ✅ GOOD - Real Estate vocabulary in content #}
text: 'View Property Details'
title: 'Modern Downtown Loft'
description: 'Spacious 3-bedroom apartment with stunning city views'
label: 'Property Location'
button_text: 'Schedule a Visit'

{# ❌ BAD - Generic placeholders #}
text: 'Click here'
title: 'Lorem ipsum'
```

**Real Estate vocabulary**: Property, listing, apartment, agent, broker, visit, viewing, location, price, rent, sale, lease, bedroom, bathroom, area, amenities.

**IMPORTANT**: This applies to YAML data and story content ONLY. Do NOT create separate "RealEstateContext" or "InContext" stories - keep story structure simple (Default + AllStates + type variants).

---

## 3. YAML Standards

### 3.1 Structure

```yaml
# Default: Brief description of default state
prop1: 'value'
color: 'primary'
size: 'medium'
disabled: false

# Commented enum options for clarity
# color options: primary | secondary | success | warning | danger | info
# size options: small | medium | large
```

### 3.2 Realistic Data (Real Estate)

```yaml
# Card example
title: 'Modern Downtown Loft'
description: 'Spacious 3-bedroom apartment in the heart of downtown with panoramic city views. Features include hardwood floors, open kitchen, and private balcony.'
image_src: 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=800'
image_alt: 'Modern downtown loft exterior'
badge_text: 'New Listing'
badge_color: 'success'
cta_text: 'Schedule Viewing'
cta_url: '/properties/downtown-loft'

# Button example
text: 'Search Properties'
icon_start: 'search'
color: 'primary'

# Form field example
label: 'Property Location'
placeholder: 'Enter city or neighborhood'
helper_text: 'Start typing to see suggestions'
```

### 3.3 Data Types

```yaml
# String
text: 'View Details'

# Number
value: 50
min: 0
max: 100

# Boolean
disabled: false
required: true

# Array
items:
  - 'Home'
  - 'Properties'
  - 'Contact'

# Object
attributes:
  data-test: 'value'
  aria-label: 'Label'
```

---

## 4. Storybook Standards

### 4.1 Storybook Edition: HTML/Vite (NOT React)

**⚠️ CRITICAL**: PS Theme uses Storybook **HTML edition**, NOT React.

**❌ NEVER**:
```jsx
import React from 'react';
export const Default = () => <div className="component">Text</div>;
```

**✅ ALWAYS**:
```jsx
import componentTwig from './component.twig';
export const Default = {
  render: (args) => componentTwig(args),
};
```

### 4.2 Import Pattern

```jsx
// 1. Import Twig template
import buttonTwig from './button.twig';

// 2. Import default data
import data from './button.yml';

// 3. Import centralized lists (relative paths)
import colorsList from '../../documentation/colors-list.json';
import sizesList from '../../documentation/sizes-list.json';
import iconsRegistry from '../../documentation/icons-registry.json';
```

**⚠️ CRITICAL**: Use **relative paths**, NOT aliases (`@patterns`).

**⚠️ CRITICAL - Icon Registry Structure**:
```js
// ❌ WRONG - iconsRegistry is NOT an array
const iconOptions = ['', ...iconsRegistry.map(icon => icon.name)];

// ✅ CORRECT - iconsRegistry has { names: [] } structure
const iconOptions = ['', ...iconsRegistry.names];

// Structure: { generated: "ISO date", total: 141, names: ["icon1", "icon2", ...] }
```

### 4.3 Export Default

```jsx
export default {
  title: 'Elements/Button',          // Category/Name
  tags: ['autodocs'],                 // MANDATORY
  render: (args) => buttonTwig(args),
  args: data,
  
  parameters: {
    docs: {
      description: {
        component:
          'Brief description (≤ 2 lines).\n\n' +
          '- **Colors**: primary, secondary, success, warning, danger, info\n' +
          '- **Sizes**: small, medium (default), large\n' +
          '- **States**: hover, focus-visible, active, disabled\n' +
          '- **Accessibility**: WCAG AA, keyboard support',
      },
    },
  },
  
  argTypes: {
    // See ArgTypes section below
  },
};
```

### 4.4 ArgTypes (MANDATORY Categorization)

**ALL argTypes MUST be categorized**:

| Category | Purpose | Examples |
|----------|---------|----------|
| **Content** | Content to display | text, icon, label, title, description |
| **Appearance** | Visual styling | color, variant, size, shape, pill |
| **Behavior** | Interactive behavior | disabled, loading, active, dismissible |
| **Link** | Navigation/linking | url, href, target, rel |
| **Accessibility** | A11y attributes | ariaLabel, ariaDescribedBy, role |
| **Layout** | Spatial arrangement | alignment, position, orientation |

**Example**:

```jsx
argTypes: {
  // Content
  text: {
    description: 'Button text content',
    control: 'text',
    table: {
      category: 'Content',
      type: { summary: 'string', required: true },
    },
  },
  
  // Appearance
  color: {
    description: 'Color variant',
    control: { type: 'select' },
    options: colorsList.semantic.values,
    table: {
      category: 'Appearance',
      type: { summary: 'primary | secondary | success | warning | danger | info' },
      defaultValue: { summary: 'primary' },
    },
  },
  
  size: {
    description: 'Size variant',
    control: { type: 'inline-radio' },
    options: sizesList.compact.values,
    table: {
      category: 'Appearance',
      defaultValue: { summary: 'medium' },
    },
  },
  
  // Behavior
  disabled: {
    description: 'Disabled state',
    control: 'boolean',
    table: {
      category: 'Behavior',
      defaultValue: { summary: false },
    },
  },
  
  // Link
  url: {
    description: 'Link URL (renders <a>)',
    control: 'text',
    table: {
      category: 'Link',
      type: { summary: 'string' },
    },
  },
  
  // Accessibility
  ariaLabel: {
    description: 'ARIA label',
    control: 'text',
    table: {
      category: 'Accessibility',
      type: { summary: 'string' },
    },
  },
}
```

### 4.5 Stories Structure

**1. Default Story** (REQUIRED):
```jsx
export const Default = {
  render: (args) => buttonTwig(args),
  args: { ...data },
};
```

**2. Showcase Stories** (REQUIRED):
```jsx
export const AllColors = {
  render: () => `
    <div style="display: flex; gap: var(--size-4); flex-wrap: wrap;">
      ${colorsList.semantic.values.map(color =>
        buttonTwig({ text: color, color })
      ).join('\n')}
    </div>
  `,
};

export const AllSizes = {
  render: () => `
    <div style="display: flex; gap: var(--size-4); align-items: center;">
      ${sizesList.compact.values.map(size =>
        buttonTwig({ text: size, size })
      ).join('\n')}
    </div>
  `,
};

export const UseCases = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-4);">
      <h3>Property Listing CTA</h3>
      ${buttonTwig({ text: 'View Property Details', color: 'primary' })}
      
      <h3>Search Form</h3>
      ${buttonTwig({ text: 'Search Properties', icon_start: 'search' })}
    </div>
  `,
};
```

**3. Forbidden Stories** (DO NOT CREATE):
```jsx
❌ export const Primary = { ... };      // Redundant with AllColors
❌ export const Secondary = { ... };    // Redundant with AllColors
❌ export const Small = { ... };        // Redundant with AllSizes
```

**Why?** Showcases group related variants—individual stories create noise.

### 4.6 Language Rule

**ALL Storybook documentation MUST be in English.**

**English required**:
- `parameters.docs.description.component`
- `argTypes[prop].description`
- Story names (`AllColors`, `UseCases`)

**French allowed**:
- User-facing demo content (`text: 'Rechercher'`)

**Never translate**:
- Token names (`--primary`, `--size-4`)
- Class names (`.ps-button`, `.ps-button--large`)

---

## 5. JavaScript Standards

### 5.1 Core Principles

1. **Progressive enhancement**: Components work without JS; JS adds interaction only
2. **Simplicity first**: Use simple init function for trivial; use class for stateful
3. **Drupal behaviors**: Idempotent `attach` using `once()`
4. **Accessibility-first**: Keyboard support, ARIA, focus management
5. **Cleanup**: Use `AbortController` to avoid memory leaks

### 5.2 When to Create `.js` File

**Create JavaScript file for**:
- ✅ Interactive behavior (dropdowns, accordions, modals, tabs)
- ✅ Stateful components (toggles, counters, carousels)
- ✅ Keyboard navigation (arrow keys, Escape handling)
- ✅ AJAX interactions, timers, animations

**Do NOT create for**:
- ❌ Static display components (no JS needed)

### 5.3 Component Class Pattern

**Use class for multi-listener stateful components**:

```js
// source/patterns/components/{component}/{component}.js
export class PsComponent {
  constructor(root, options = {}) {
    this.root = root;
    this.options = { ...PsComponent.defaults, ...options };
    this.controllers = []; // AbortController instances
    this.initialized = false;
  }

  static defaults = {
    timeout: 150,
    dismissible: false,
    keys: ['Escape', 'Enter'],
  };

  init() {
    if (this.initialized) return;
    this.initialized = true;
    
    const ac = new AbortController();
    this.controllers.push(ac);
    
    // Keyboard listener
    this.root.addEventListener('keydown', this.onKeyDown.bind(this), {
      signal: ac.signal,
    });
    
    // Click listener
    const button = this.root.querySelector('[data-component-trigger]');
    if (button) {
      button.addEventListener('click', this.onClick.bind(this), {
        signal: ac.signal,
      });
    }
  }

  onKeyDown(e) {
    if (e.key === 'Escape') {
      this.close();
    }
  }

  onClick(e) {
    this.toggle();
  }

  toggle() {
    // Toggle logic
  }

  close() {
    // Close logic
  }

  destroy() {
    this.controllers.forEach((c) => c.abort());
    this.controllers = [];
    this.initialized = false;
  }
}
```

**Use simple function for one-time initialization**:

```js
// source/patterns/components/alert/alert.js
export function initAlert(context = document) {
  const alerts = context.querySelectorAll('[data-dismissible="true"]');
  
  alerts.forEach((alert) => {
    const button = alert.querySelector('[data-alert-dismiss]');
    if (!button) return;
    
    button.addEventListener('click', () => {
      alert.remove();
    }, { once: true }); // Cleanup automatic
  });
}
```

### 5.4 Drupal Behaviors Pattern

```js
// source/patterns/base/behaviors.js
import { PsDropdown } from '../components/dropdown/dropdown.js';
import { initAlert } from '../components/alert/alert.js';
import { once } from 'drupal/once';

(function (Drupal, drupalSettings) {
  
  // Simple pattern: function + once
  Drupal.behaviors.psAlert = {
    attach(context) {
      once('psAlert', '[data-dismissible="true"]', context).forEach((alert) => {
        const button = alert.querySelector('[data-alert-dismiss]');
        if (!button) return;
        button.addEventListener('click', () => alert.remove());
      });
    },
  };
  
  // Complex pattern: class + once
  Drupal.behaviors.psDropdown = {
    attach(context) {
      const globalConfig = drupalSettings.psTheme?.components?.dropdown || {};
      
      once('psDropdown', '.ps-dropdown', context).forEach((root) => {
        if (root.__psInstance) return; // Skip if initialized
        
        const localConfig = {
          timeout: Number(root.dataset.timeout) || undefined,
        };
        
        const instance = new PsDropdown(root, { ...globalConfig, ...localConfig });
        instance.init();
        root.__psInstance = instance;
      });
    },
    
    detach(context, settings, trigger) {
      if (trigger !== 'unload') return; // Only cleanup on unload
      
      context.querySelectorAll('.ps-dropdown').forEach((root) => {
        if (root.__psInstance) {
          root.__psInstance.destroy();
          root.__psInstance = null;
        }
      });
    },
  };
  
})(Drupal, drupalSettings);
```

### 5.5 Storybook Integration

**⚠️ CRITICAL**: Import behaviors **globally** in `.storybook/preview.js`, NOT in stories.

```js
// .storybook/preview.js
import '../source/patterns/components/dropdown/dropdown.js';
import '../source/patterns/components/accordion/accordion.js';
// ... all interactive components
```

```jsx
// component.stories.jsx
// ❌ NEVER import behaviors here
// ❌ import './component.js'; // WRONG

// ✅ Behaviors already loaded globally
export const Interactive = {
  render: (args) => componentTwig(args),
  args: { ...data },
};
```

**Why?** Drupal.attachBehaviors() timing issue—stories load before behaviors if imported locally.

### 5.6 Keyboard Navigation Patterns

**Menu/Dropdown**:
```js
dropdown.addEventListener('keydown', (e) => {
  const items = Array.from(dropdown.querySelectorAll('[role="menuitem"]'));
  const current = items.indexOf(document.activeElement);
  
  switch (e.key) {
    case 'ArrowDown':
      e.preventDefault();
      items[(current + 1) % items.length].focus();
      break;
    
    case 'ArrowUp':
      e.preventDefault();
      items[(current - 1 + items.length) % items.length].focus();
      break;
    
    case 'Home':
      e.preventDefault();
      items[0].focus();
      break;
    
    case 'End':
      e.preventDefault();
      items[items.length - 1].focus();
      break;
    
    case 'Escape':
      closeDropdown();
      trigger.focus();
      break;
  }
});
```

---

## 6. Accessibility Standards

### 6.1 Core Requirements

**ALL components MUST meet WCAG 2.2 Level AA**:
- Contrast ratios (text, UI components)
- Keyboard navigation
- Focus indicators
- ARIA attributes
- Screen reader support
- Semantic HTML

### 6.2 Contrast Ratios (WCAG 2.2 AA)

**Text Contrast**:

| Content Type | Minimum Ratio | Example Tokens |
|--------------|---------------|----------------|
| Normal text (<18px / <14px bold) | **4.5:1** | `--gray-900` on `--white` |
| Large text (≥18px / ≥14px bold) | **3:1** | `--gray-700` on `--white` |

```css
/* ✅ GOOD - Meets 4.5:1 */
.ps-text {
  color: var(--gray-900); /* 14.8:1 on white */
}

/* ❌ BAD - Fails AA */
.ps-text {
  color: var(--gray-400); /* 2.4:1 - FAIL */
}
```

**UI Component Contrast**:

| Element Type | Minimum Ratio |
|--------------|---------------|
| Borders | **3:1** |
| Icons | **3:1** |
| Focus indicators | **3:1** |
| Active UI components | **3:1** |

**Exception**: Disabled elements (no minimum contrast required).

### 6.3 Keyboard Navigation

**ALL interactive elements MUST be keyboard accessible**:
- Buttons, links, inputs, selects
- Custom controls (toggles, tabs, accordions)
- Dropdowns, modals, tooltips

**Focus order**: Natural DOM order = tab order (avoid `tabindex` override).

**Skip to content**: Provide skip link on every page.

```html
<a href="#main-content" class="ps-skip-link">
  Skip to main content
</a>

<main id="main-content">
  <!-- Page content -->
</main>
```

```css
.ps-skip-link {
  position: absolute;
  top: var(--size-2);
  left: var(--size-2);
  transform: translateY(-150%);
  
  &:focus-visible {
    transform: translateY(0); /* Show on focus */
  }
}
```

### 6.4 ARIA Attributes

**ARIA Rules**:
1. **No ARIA is better than bad ARIA**
2. **Use semantic HTML first** (prefer `<button>` over `<div role="button">`)
3. **Add ARIA only when semantic HTML insufficient**

**Common patterns**:

**Button-like**:
```html
<!-- ✅ BEST - Native button -->
<button class="ps-button">Click me</button>

<!-- ⚠️ OK - Custom element with ARIA -->
<div class="ps-button" role="button" tabindex="0">
  Click me
</div>
```

**Disclosure (Expandable)**:
```html
<button class="ps-accordion__trigger"
  aria-expanded="false"
  aria-controls="panel-1"
>
  Section Title
</button>

<div id="panel-1" class="ps-accordion__panel" hidden>
  Panel content
</div>
```

**Labels**:
```html
<!-- ✅ Visible label -->
<label for="email-input">Email Address</label>
<input id="email-input" type="email" />

<!-- ✅ aria-label (no visible label) -->
<button aria-label="Close dialog">
  <span data-icon="close"></span>
</button>

<!-- ✅ aria-labelledby (reference element) -->
<div role="dialog" aria-labelledby="dialog-title">
  <h2 id="dialog-title">Confirm Action</h2>
</div>
```

**Descriptions**:
```html
<!-- aria-describedby for context -->
<input
  id="password"
  type="password"
  aria-describedby="password-hint"
/>
<span id="password-hint" class="ps-form-field__helper">
  Must be at least 8 characters
</span>
```

**Live Regions**:
```html
<!-- Announce changes (urgent) -->
<div class="ps-alert" role="alert">
  Form submitted successfully!
</div>

<!-- Announce changes (polite) -->
<div class="ps-status" role="status" aria-live="polite">
  Saving...
</div>
```

**Hidden Content**:
```html
<!-- Visually hidden but accessible -->
<span class="ps-visually-hidden">
  New messages
</span>

<!-- Hidden from everyone -->
<div hidden>
  Not accessible
</div>

<!-- aria-hidden (visible but not announced) -->
<span aria-hidden="true" data-icon="decorative"></span>
```

### 6.5 Semantic HTML

**Use proper elements**:

```html
<!-- ✅ GOOD - Semantic HTML -->
<header class="ps-header">
  <nav class="ps-nav" aria-label="Main navigation">
    <ul>
      <li><a href="/">Home</a></li>
    </ul>
  </nav>
</header>

<main class="ps-main">
  <article class="ps-article">
    <h1>Article Title</h1>
    <p>Content...</p>
  </article>
</main>

<footer class="ps-footer">
  <!-- Footer content -->
</footer>

<!-- ❌ BAD - Divs for everything -->
<div class="header">
  <div class="nav">
    <div class="link">Home</div>
  </div>
</div>
```

**Headings hierarchy**:
```html
<!-- ✅ GOOD - Logical hierarchy -->
<h1>Page Title</h1>
  <h2>Section 1</h2>
    <h3>Subsection 1.1</h3>
  <h2>Section 2</h2>

<!-- ❌ BAD - Skipped levels -->
<h1>Title</h1>
  <h3>Subsection</h3> <!-- Skipped h2 -->
```

### 6.6 Screen Reader Support

**Accessible names** (every interactive needs one):

```html
<!-- ✅ Text content -->
<button>Submit Form</button>

<!-- ✅ aria-label -->
<button aria-label="Close dialog">
  <span data-icon="close"></span>
</button>

<!-- ❌ No accessible name -->
<button>
  <span data-icon="close"></span>
</button>
```

**Alt text**:
```html
<!-- ✅ Descriptive alt -->
<img src="property.jpg" alt="Modern 3-bedroom apartment with balcony" />

<!-- ✅ Decorative (empty alt) -->
<img src="decorative.svg" alt="" />

<!-- ❌ Missing alt -->
<img src="property.jpg" />
```

### 6.7 Visually Hidden Class

```css
.ps-visually-hidden {
  position: absolute;
  width: 1px;
  height: 1px;
  margin: -1px;
  padding: 0;
  overflow: hidden;
  clip: rect(0, 0, 0, 0);
  white-space: nowrap;
  border: 0;
}
```

**Usage**:
```html
<button>
  <span data-icon="search"></span>
  <span class="ps-visually-hidden">Search properties</span>
</button>
```

### 6.8 Testing Tools

- **Browser DevTools**: ARIA inspector, contrast checker
- **axe DevTools**: Automated accessibility testing (browser extension)
- **Lighthouse**: Accessibility audit in Chrome DevTools
- **Screen readers**: NVDA (Windows), VoiceOver (Mac), JAWS (Windows)

---

## 📝 Quick Reference Summary

| Need | Section | Key Rule |
|------|---------|----------|
| CSS tokens | 1.1 | Zero hardcoded values |
| CSS nesting | 1.3 | Use `&` syntax, max 3 levels |
| CSS cascade | 1.4 | Base BEFORE modifiers |
| Focus visible | 1.5 | MANDATORY for interactives |
| Twig classes | 2.4 | Ternary with `null`, NO arrow functions |
| Twig includes | 2.5 | Always use `only` keyword |
| YAML data | 3.2 | Real Estate context |
| Storybook edition | 4.1 | HTML/Vite, NOT React |
| Storybook tags | 4.3 | `tags: ['autodocs']` MANDATORY |
| ArgTypes | 4.4 | MUST categorize (6 categories) |
| JavaScript | 5.3 | Class for stateful, function for simple |
| Drupal behaviors | 5.4 | Use `once()` for idempotency |
| Contrast ratios | 6.2 | 4.5:1 text, 3:1 UI (WCAG AA) |
| ARIA | 6.4 | Semantic HTML first, ARIA when needed |
| Screen readers | 6.6 | Every interactive needs accessible name |

---

**Version**: 4.0.0  
**Last Updated**: 2025-12-12  
**Maintainers**: Design System Team
