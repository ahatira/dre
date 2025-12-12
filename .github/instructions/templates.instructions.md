---
title: Twig Template Standards
version: 3.0.0
lastUpdated: 2025-12-05
applyTo:
  - "**/*.twig"
  - "**/*.yml"
priority: HIGH
related:
  - components.instructions.md
  - accessibility.instructions.md
status: ACTIVE
---

# Twig Templates & YAML Standards - PS Theme

**Scope**: Twig templates (Drupal-compatible), YAML configuration

---

## 🔧 Twig Templates

### Drupal 10/11 Compatibility (CRITICAL)

**⚠️ MANDATORY**: All Twig templates MUST be compatible with Drupal 10/11.

**Drupal Twig limitations**:
- ❌ NO arrow functions (`v => v`)
- ❌ NO JavaScript methods (`.filter()`, `.map()`, `.reduce()`)
- ❌ NO spread operator (`...`)
- ✅ ONLY Twig native functions and filters

---

### Header Comment (MANDATORY)

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
 * @param string image_alt - Image alt text (optional, default: '')
 * @param string badge_text - Badge text (optional)
 * @param string badge_color - Badge color (optional, default: 'primary')
 * @param string cta_text - CTA button text (optional)
 * @param string cta_url - CTA button URL (optional)
 * @param string cta_color - CTA button color (optional, default: 'primary')
 * @param object attributes - Additional HTML attributes (optional)
 #}
```

---

### Default Values

```twig
{%- set color = color|default('primary') -%}
{%- set size = size|default('md') -%}
{%- set disabled = disabled|default(false) -%}
{%- set orientation = orientation|default('horizontal') -%}
```

**Pattern**: `{% set prop = prop|default('defaultValue') %}`

---

### Classes Construction (Drupal-Compatible)

**✅ CORRECT - Ternary with `null`**:

```twig
{%- set classes = [
  'ps-component',
  size != 'md' ? 'ps-component--' ~ size : null,
  color != 'primary' ? 'ps-component--' ~ color : null,
  orientation != 'horizontal' ? 'ps-component--' ~ orientation : null,
  disabled ? 'ps-component--disabled' : null,
  pill ? 'ps-component--pill' : null
] -%}

<div class="{{ classes|join(' ')|trim }}"
  {%- if attributes %} {{ attributes }}{% endif -%}
>
```

**Why `null`?** Twig's `join()` automatically skips `null` values.

**❌ WRONG - Arrow functions (NOT Drupal-compatible)**:

```twig
{%- set classes = ['ps-component', size, color]|filter(v => v) -%}
{# ERROR: Arrow functions not supported in Drupal Twig #}
```

**❌ WRONG - `.merge()` with inline conditions**:

```twig
{%- set classes = ['ps-component'] -%}
{%- set classes = classes|merge([size != 'md' ? 'ps-component--' ~ size]) -%}
{# Complex and error-prone #}
```

### Minimal Markup (No Defaults in Classes)

```twig
{%- set size = size|default('md') -%}
{%- set shape = shape|default('circle') -%}

{%- set classes = [
  'ps-avatar',
  size != 'md' ? 'ps-avatar--' ~ size : null,
  shape != 'circle' ? 'ps-avatar--' ~ shape : null
] -%}

{# Default output: <div class="ps-avatar"> (no size or shape class) #}
{# With size="lg": <div class="ps-avatar ps-avatar--lg"> #}
```

---

### Conditional Rendering

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

---

### Composition (Molecules/Organisms)

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

---

### Attributes Handling

```twig
{# Accept attributes parameter #}
<div class="{{ classes|join(' ')|trim }}"
  {%- if attributes %} {{ attributes }}{% endif -%}
  {%- if ariaLabel %} aria-label="{{ ariaLabel }}"{% endif -%}
  {%- if role %} role="{{ role }}"{% endif -%}
>
```

**When composing atoms**:

```twig
{%- include '@elements/atom/atom.twig' with {
  prop: value,
  attributes: create_attribute()
    .addClass('ps-component__atom')
    .setAttribute('data-custom', 'value')
} only -%}
```

---

### Dynamic Tags

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

---

### Whitespace Control

```twig
{# Remove whitespace with - #}
{%- set var = value -%}

{# Keep whitespace (default) #}
{% set var = value %}

{# Inline elements (no whitespace) #}
<span class="ps-component__text">{%- if text -%}{{ text }}{%- endif -%}</span>

{# Block elements (whitespace OK) #}
<div class="ps-component__content">
  {% if content %}
    {{ content }}
  {% endif %}
</div>
```

---

### Real Estate Context Placeholders

**All placeholder content should evoke Real Estate**:

```twig
{# ✅ GOOD - Real Estate context #}
text: 'View Property Details'
title: 'Modern Downtown Loft'
description: 'Spacious 3-bedroom apartment with stunning city views'
label: 'Property Location'
button_text: 'Schedule a Visit'
search_placeholder: 'Search properties...'

{# ❌ BAD - Generic placeholders #}
text: 'Click here'
title: 'Lorem ipsum'
description: 'Some text here'
```

**Real Estate vocabulary**:
- Property, listing, apartment, house, office, commercial
- Agent, broker, landlord, tenant, buyer, seller
- Visit, viewing, tour, showing
- Location, neighborhood, area, district
- Price, rent, sale, lease
- Bedroom, bathroom, square footage, amenities

---

## 📄 YAML Configuration

### Structure

```yaml
# Default: Brief description of default state
prop1: 'value'
color: 'primary'
size: 'md'
disabled: false

# Commented enum options for clarity
# color options: primary | secondary | success | warning | danger | info
# size options: xs | sm | md | lg | xl
```

### Realistic Data (Real Estate)

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

### Data Types

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

### Comments for Documentation

```yaml
# Brief description of default state
title: 'Property Title'
description: 'Property description'

# Enum options (for Storybook controls)
# color: primary | secondary | success | warning | danger | info
# size: xs | sm | md | lg | xl

# Complex props explanation
# attributes: Additional HTML attributes (key-value pairs)
# items: Array of navigation items with text and url properties
```

---

## 🎯 Faker.js for Stories (Recommended)

**For `.stories.jsx` files** (NOT `.yml`), use Faker.js for realistic data:

```jsx
import { faker } from '@faker-js/faker/locale/fr';

export const RandomProperties = {
  render: () => {
    const properties = Array.from({ length: 6 }, () => ({
      title: faker.location.streetAddress(),
      description: faker.lorem.sentences(2),
      price: faker.commerce.price({ min: 200000, max: 1500000, symbol: '€' }),
      location: `${faker.location.city()}, ${faker.location.state()}`,
      image_src: faker.image.urlLoremFlickr({ category: 'building', width: 800, height: 600 }),
      bedrooms: faker.number.int({ min: 1, max: 5 }),
      bathrooms: faker.number.int({ min: 1, max: 3 }),
      area: faker.number.int({ min: 50, max: 300 }),
    }));

    return `
      <div class="property-grid">
        ${properties.map(prop => cardTwig(prop)).join('\n')}
      </div>
    `;
  },
};
```

**Useful Faker methods for Real Estate**:
- `faker.location.streetAddress()` - Address
- `faker.location.city()` - City name
- `faker.person.fullName()` - Agent name
- `faker.person.jobTitle()` - Job title (e.g., "Real Estate Agent")
- `faker.commerce.price()` - Price
- `faker.image.urlLoremFlickr({ category: 'building' })` - Building images
- `faker.lorem.sentence()` - Short descriptions
- `faker.number.int({ min, max })` - Numbers (bedrooms, area, etc.)

---

## 🚫 Anti-Patterns

### 1. Arrow Functions (Drupal Incompatible)

```twig
❌ {% set classes = items|filter(v => v) %}
❌ {% set names = items|map(i => i.name) %}
```

### 2. JavaScript Methods

```twig
❌ {% set classes = items|reduce(...) %}
❌ {% set total = numbers|forEach(...) %}
```

### 3. Spread Operator

```twig
❌ {% set merged = [...array1, ...array2] %}
```

### 4. `.merge()` Overuse

```twig
❌ {% set classes = ['base'] %}
   {% set classes = classes|merge(['modifier1']) %}
   {% set classes = classes|merge(['modifier2']) %}
   
✅ {% set classes = [
     'base',
     condition1 ? 'modifier1' : null,
     condition2 ? 'modifier2' : null
   ] %}
```

### 5. Missing `only` Keyword

```twig
❌ {% include '@elements/button/button.twig' with { text: 'Click' } %}
   {# Variables leak from parent scope #}

✅ {% include '@elements/button/button.twig' with { text: 'Click' } only %}
```

### 6. Hardcoded Classes for Defaults

```twig
❌ {% set classes = ['ps-component', 'ps-component--md', 'ps-component--circle'] %}
   {# Includes default modifiers #}

✅ {% set classes = [
     'ps-component',
     size != 'md' ? 'ps-component--' ~ size : null
   ] %}
```

### 7. Generic Placeholders

```yaml
❌ text: 'Lorem ipsum dolor sit amet'
❌ title: 'Example Title'

✅ text: 'Schedule a Property Visit'
✅ title: 'Luxury Waterfront Apartment'
```

---

## 🔗 Cross-References

- **Component Structure**: `instructions/components.instructions.md`
- **Atomic Design Composition**: `instructions/atomic-design.instructions.md`
- **Storybook Stories**: `instructions/storybook.instructions.md`

---

**Last Updated**: 2025-12-05  
**Maintainers**: Design System Team
