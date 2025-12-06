---
applyTo:
  - ".github/**/*"
---

# Developer Workflows - PS Theme

**Version**: 3.0.0  
**Date**: 2025-12-05  
**Scope**: Component generation, conformity audits, standardization, build validation

---

## 🎯 Overview

This document consolidates all developer workflows for the PS Theme design system:
- **Component Generation**: Complete workflow from spec to implementation
- **Conformity Audit**: 100% score checklist for existing components
- **Standardization**: Refactoring legacy components to new standards
- **Build Validation**: Pre-commit checks
- **Changelog**: Documentation updates

---

## 🆕 Component Generation Workflow

### Step 1: Read Specification

**Locate spec file**:
```bash
docs/design/{level}/{component}.md
```

Where `{level}` is one of:
- `atoms/` (elements/)
- `molecules/` (components/)
- `organisms/` (collections/)
- `templates/` (layouts/)
- `pages/` (pages/)

**Extract key information**:
- Component name, category, description
- Props (required vs optional)
- Variants (sizes, colors, states)
- BEM structure
- Design tokens used
- Accessibility requirements
- Dependencies (other components to include)

---

### Step 2: Verify Dependencies

**Check if component reuses atoms**:

```bash
# Search for `{% include %}` in spec examples
grep -r "{% include" docs/design/{level}/{component}.md
```

**If molecule/organism, verify atoms exist**:

```bash
# Example: Alert (molecule) depends on icon (atom)
ls source/patterns/elements/icon/
# Should contain: icon.twig, icon.css, icon.yml, icon.stories.jsx, README.md
```

**If missing dependency, STOP and create atom first** (composition before creation).

---

### Step 3: Token Verification

**Check if required tokens exist**:

```bash
# Example: Component needs --size-badge
grep -r "--size-badge" source/props/
```

**If token missing**:
1. **DO NOT add token yourself** (see Critical Override in main rules)
2. Document the missing token
3. Propose addition via separate tokens-change PR/process
4. Wait for token approval before continuing component

**Common token files**:
- `source/props/colors.css` - Semantic colors (--primary, --danger, etc.)
- `source/props/sizes.css` - Spacing and sizing (--size-1 to --size-10)
- `source/props/fonts.css` - Typography (--font-size-*, --font-weight-*)
- `source/props/borders.css` - Border widths and radii
- `source/props/shadows.css` - Box shadows
- `source/props/animations.css` - Durations

---

### Step 4: Create 5 Required Files

**Use script to scaffold**:

```bash
npm run generate:pattern
# Prompts:
# - Pattern type: element | component | collection | layout | page
# - Pattern name: badge
```

**Or manually create**:

```
source/patterns/{level}/{component}/
├── {component}.twig
├── {component}.css
├── {component}.yml
├── {component}.stories.jsx
└── README.md
```

---

### Step 5: Implement Twig Template

**Header comment with params**:

```twig
{#
/**
 * @file
 * Badge component
 * 
 * Small label for statuses, categories, counts, or metadata.
 * 
 * @param {string} text - Badge label content (required)
 * @param {string} [variant='default'] - Color variant: default | primary | secondary | success | warning | danger | info
 * @param {string} [size='md'] - Size variant: sm | md | lg
 * @param {boolean} [pill=false] - Pill shape (rounded-full)
 * @param {string} [icon] - Optional icon name (no "icon-" prefix)
 * @param {string} [modifier_class] - Additional classes
 * @param {object} [attributes] - Additional HTML attributes
 */
#}

{% set variant = variant|default('default') %}
{% set size = size|default('md') %}
{% set pill = pill|default(false) %}
{% set icon = icon|default(null) %}

{% set classes = [
  'ps-badge',
  variant != 'default' ? 'ps-badge--' ~ variant : null,
  size != 'md' ? 'ps-badge--' ~ size : null,
  pill ? 'ps-badge--pill' : null,
  modifier_class,
]|filter(v => v) %}

<span{{ attributes.addClass(classes) }}>
  {% if icon %}
    {% include '@elements/icon/icon.twig' with {
      icon: icon,
      size: 'xs',
    } only %}
  {% endif %}
  
  {% if text %}
    <span class="ps-badge__text">{{ text }}</span>
  {% endif %}
</span>
```

**Key rules**:
- ✅ Default values with `|default()`
- ✅ Ternary with `null` for conditional classes: `condition ? 'class' : null`
- ❌ NEVER use arrow functions: `filter(v => v)` (Drupal Twig incompatible)
- ✅ Use `{% include %}` with `only` for composition
- ✅ Real Estate context for placeholders

---

### Step 6: Implement CSS with Nesting

**Structure**:

```css
.ps-badge {
  /* Base styles (shared by all variants) */
  display: inline-flex;
  align-items: center;
  gap: var(--size-1);
  padding-block: var(--size-1);
  padding-inline: var(--size-2);
  border-radius: var(--radius-1);
  font-size: var(--font-size-0);
  font-weight: var(--font-weight-6);
  line-height: var(--leading-tight);
  background-color: var(--gray-100);
  color: var(--gray-900);
  
  /* Elements */
  &__text {
    /* Text-specific styles */
  }
  
  /* Size modifiers */
  &--sm {
    padding-block: var(--size-0);
    padding-inline: var(--size-1);
    font-size: var(--font-size--1);
  }
  
  &--lg {
    padding-block: var(--size-2);
    padding-inline: var(--size-3);
    font-size: var(--font-size-1);
  }
  
  /* Color modifiers (semantic) */
  &--primary {
    background-color: var(--primary-subtle);
    color: var(--primary);
  }
  
  &--success {
    background-color: var(--success-subtle);
    color: var(--success-dark);
  }
  
  &--danger {
    background-color: var(--danger-subtle);
    color: var(--danger-dark);
  }
  
  /* Pill modifier */
  &--pill {
    border-radius: var(--radius-round);
  }
}
```

**Critical rules**:
- ✅ Use `&` nesting syntax (postcss-nested)
- ✅ Order: Base → Elements → Size modifiers → Color modifiers → Other modifiers
- ✅ ALL values from tokens (NO hardcoded values)
- ✅ Component-scoped CSS variables for modifiers (Layer 2 system)
- ✅ Focus-visible for interactives (if applicable)

---

### Step 7: Create YAML with Realistic Data

**Use Faker.js context**:

```yaml
text: "New Listing"
variant: "primary"
size: "md"
pill: false
icon: "home"
```

**For molecules/organisms with included atoms**:

```yaml
heading:
  text: "Luxury Apartment for Sale"
  level: 2
  size: "lg"

content:
  - type: "paragraph"
    text: "Modern 3-bedroom apartment with stunning city views, hardwood floors, and gourmet kitchen."

cta:
  text: "Schedule Tour"
  variant: "primary"
  href: "/properties/luxury-apartment"
```

---

### Step 8: Create Storybook Stories

**Import and render**:

```jsx
import componentTwig from './component.twig';
import componentData from './component.yml';

export default {
  title: 'Elements/Badge',
  tags: ['autodocs'], // MANDATORY - Enables Autodocs
  parameters: {
    docs: {
      description: {
        component: 'Small label for statuses, categories, counts, or metadata. Supports semantic colors, sizes, and optional icons.',
      },
    },
  },
  render: (args) => componentTwig(args),
  argTypes: {
    // Content category
    text: {
      control: 'text',
      description: 'Badge label content',
      table: { category: 'Content' },
    },
    
    // Appearance category
    variant: {
      control: 'select',
      options: ['default', 'primary', 'secondary', 'success', 'warning', 'danger', 'info'],
      description: 'Color variant',
      table: { category: 'Appearance', defaultValue: { summary: 'default' } },
    },
    
    size: {
      control: 'select',
      options: ['sm', 'md', 'lg'],
      description: 'Size variant',
      table: { category: 'Appearance', defaultValue: { summary: 'md' } },
    },
    
    pill: {
      control: 'boolean',
      description: 'Pill shape (rounded-full)',
      table: { category: 'Appearance', defaultValue: { summary: false } },
    },
    
    icon: {
      control: 'text',
      description: 'Optional icon name (no "icon-" prefix)',
      table: { category: 'Appearance' },
    },
  },
};

// Default story
export const Default = {
  args: componentData,
};

// All colors showcase
export const AllColors = {
  render: () => {
    const variants = ['default', 'primary', 'secondary', 'success', 'warning', 'danger', 'info'];
    return `
      <div style="display: flex; gap: var(--size-3); flex-wrap: wrap;">
        ${variants.map(variant => componentTwig({ ...componentData, variant, text: variant })).join('')}
      </div>
    `;
  },
};

// All sizes showcase
export const AllSizes = {
  render: () => {
    const sizes = ['sm', 'md', 'lg'];
    return `
      <div style="display: flex; gap: var(--size-3); align-items: center;">
        ${sizes.map(size => componentTwig({ ...componentData, size, text: size })).join('')}
      </div>
    `;
  },
};

// With icon
export const WithIcon = {
  args: {
    ...componentData,
    text: 'New Listing',
    icon: 'home',
    variant: 'primary',
  },
};

// Pill shape
export const Pill = {
  args: {
    ...componentData,
    text: '3 Bedrooms',
    pill: true,
    variant: 'secondary',
  },
};
```

**Critical rules**:
- ✅ **MANDATORY**: `tags: ['autodocs']` in export default
- ✅ Import: `import componentTwig from './component.twig';`
- ✅ Render: `render: (args) => componentTwig(args)`
- ✅ ArgTypes categorized: Content | Appearance | Behavior | Link | Accessibility | Layout
- ✅ Description ≤ 2 lines in `parameters.docs.description.component`
- ✅ Stories: Default + Showcases (AllColors, AllSizes, UseCases)
- ❌ NO individual variant stories (Primary, Secondary, Small, etc.)
- ❌ NO React/JSX (HTML/Vite edition only)

---

### Step 9: Create README Documentation

**Structure**:

```markdown
# Badge

Small label for statuses, categories, counts, or metadata.

## Usage

```twig
{% include '@elements/badge/badge.twig' with {
  text: 'New Listing',
  variant: 'primary',
  size: 'md',
} only %}
```

## Props

| Name | Type | Default | Description |
|------|------|---------|-------------|
| `text` | `string` | required | Badge label content |
| `variant` | `string` | `'default'` | Color variant: `default` \| `primary` \| `secondary` \| `success` \| `warning` \| `danger` \| `info` |
| `size` | `string` | `'md'` | Size variant: `sm` \| `md` \| `lg` |
| `pill` | `boolean` | `false` | Pill shape (rounded-full) |
| `icon` | `string` | `null` | Optional icon name (no "icon-" prefix) |
| `modifier_class` | `string` | `null` | Additional classes |
| `attributes` | `object` | `{}` | Additional HTML attributes |

## BEM Structure

```
.ps-badge                     # Base component
├── .ps-badge__text           # Text wrapper
├── .ps-badge--sm             # Small size
├── .ps-badge--lg             # Large size
├── .ps-badge--primary        # Primary color
├── .ps-badge--success        # Success color
└── .ps-badge--pill           # Pill shape
```

## Design Tokens

```css
/* Spacing */
--size-0: 2px;
--size-1: 4px;
--size-2: 8px;
--size-3: 12px;

/* Typography */
--font-size--1: 12px;
--font-size-0: 14px;
--font-size-1: 16px;
--font-weight-6: 600;

/* Colors */
--gray-100: hsl(210, 17%, 95%);
--primary-subtle: hsl(157, 100%, 95%);
--primary: hsl(157, 100%, 29%);

/* Borders */
--radius-1: 4px;
--radius-round: 9999px;
```

## Accessibility

- ✅ Semantic HTML (`<span>`)
- ✅ WCAG AA contrast (4.5:1 for text)
- ✅ Readable font size (min 12px for small)
- ⚠️ Not focusable (decorative label, not interactive)

## Examples

### Property Status
```twig
{% include '@elements/badge/badge.twig' with {
  text: 'For Sale',
  variant: 'success',
  icon: 'check',
} only %}
```

### Agent Count
```twig
{% include '@elements/badge/badge.twig' with {
  text: '5 Available',
  variant: 'info',
  size: 'sm',
} only %}
```
```

---

### Step 10: Build and Validate

**Run build**:

```bash
npm run build
```

**Expected output**:
- ✅ No lint errors (Biome)
- ✅ CSS compiled (Vite)
- ✅ No token errors (missing variables)

**Start watch mode**:

```bash
npm run watch
```

**Visual validation in Storybook**:
- Open http://localhost:6006
- Navigate to component (e.g., Elements/Badge)
- Verify all variants render correctly
- Test interactive stories (if applicable)
- Check Docs tab (Autodocs generated)

---

### Step 11: Commit and Update Changelog

**Commit message format**:

```
feat(elements): implement Badge component

- Add ps-badge with 7 semantic color variants
- Add 3 size variants (sm, md, lg)
- Add pill modifier for rounded shape
- Add optional icon support via icon.twig
- Implement CSS nesting with design tokens
- Add Storybook stories with Autodocs
- Document BEM structure and accessibility

Refs: docs/design/atoms/badge.md
```

**Update changelog**:

```bash
# Edit docs/ps-design/CHANGELOG.md
```

```markdown
## [Unreleased]

### Added
- **Badge** (element) - Small label for statuses, categories, counts, or metadata
  - 7 semantic color variants: default, primary, secondary, success, warning, danger, info
  - 3 size variants: sm, md, lg
  - Pill modifier for rounded shape
  - Optional icon integration
  - Full Storybook documentation with Autodocs
```

---

## 🔍 Conformity Audit Workflow

**When to audit**:
- After implementing a new component
- Before marking component as "done"
- When refactoring legacy components
- During code reviews

### Audit Checklist (100% Score Required)

#### 1. File Structure (10 points)

- [ ] **5 required files exist**: `.twig`, `.css`, `.yml`, `.stories.jsx`, `README.md`
- [ ] Files named correctly: `{component}.{ext}` (lowercase, hyphenated)

#### 2. Twig Template (15 points)

- [ ] Header comment with `@param` docs
- [ ] Default values: `variant|default('default')`
- [ ] Classes construction with ternary + `null`: `condition ? 'class' : null`
- [ ] **NO arrow functions**: `filter(v => v)` ❌ (Drupal incompatible)
- [ ] **NO JavaScript methods**: `.map()`, `.filter()`, `.includes()` ❌
- [ ] Composition via `{% include %}` with `only` keyword
- [ ] Real Estate context placeholders

#### 3. CSS (20 points)

- [ ] **ALL values from tokens** (NO hardcoded: `#00915A`, `16px`, `150ms`)
- [ ] **Nesting with `&` syntax** (postcss-nested)
- [ ] **Cascade order**: Base → Elements → Modifiers → States
- [ ] **Minimal markup principle**: Default = no modifier classes
- [ ] **Modifiers independence**: Each works alone on base class
- [ ] **Semantic colors**: primary | secondary | success | warning | danger | info (NOT green | blue | red)
- [ ] **Focus-visible** for interactives (buttons, links, inputs)
- [ ] **Component-scoped variables** (Layer 2 system) for modifiers

#### 4. Storybook (20 points)

- [ ] **`tags: ['autodocs']` in export default** (MANDATORY)
- [ ] Import: `import componentTwig from './component.twig';`
- [ ] Render: `render: (args) => componentTwig(args)`
- [ ] **NO React/JSX** (HTML/Vite edition)
- [ ] ArgTypes categorized: Content | Appearance | Behavior | Link | Accessibility | Layout
- [ ] Description **≤ 2 lines** in `parameters.docs.description.component`
- [ ] Stories: **Default + Showcases** (AllColors, AllSizes, UseCases)
- [ ] **NO individual variant stories** (Primary, Secondary, Small, etc.)

#### 5. YAML (10 points)

- [ ] Realistic Real Estate data
- [ ] All required props defined
- [ ] Optional props with meaningful defaults

#### 6. README (10 points)

- [ ] Section: Usage (Twig example)
- [ ] Section: Props (table with Name | Type | Default | Description)
- [ ] Section: BEM Structure (tree format)
- [ ] Section: Design Tokens (CSS variables used)
- [ ] Section: Accessibility (checklist + notes)
- [ ] Section: Examples (2-3 real use cases)

#### 7. BEM Naming (10 points)

- [ ] **Prefix `ps-` mandatory**: `.ps-badge`, `.ps-badge__text`
- [ ] Format: `.ps-block__element--modifier`
- [ ] **NO double underscore**: `.ps-badge__icon__wrapper` ❌
- [ ] Modifiers work independently (not chained)

#### 8. Accessibility (5 points)

- [ ] Contrast: Text 4.5:1, UI 3:1 (WCAG AA)
- [ ] Focus-visible for interactives
- [ ] ARIA attributes (when semantic HTML insufficient)
- [ ] Keyboard navigable (Tab, Enter, Space, Escape)
- [ ] Alt text for images (or `alt=""` if decorative)

### Scoring

**100 points total**:
- **90-100**: ✅ Production ready
- **75-89**: ⚠️ Minor fixes required
- **Below 75**: ❌ Major refactoring needed

### Running Audit

**Manual checklist**:
1. Open component files
2. Go through each checklist item
3. Mark violations
4. Calculate score
5. Document fixes needed

**Example audit report**:

```markdown
## Badge Conformity Audit

**Score**: 85/100 ⚠️ Minor fixes required

### Violations

1. **Twig Template** (-5 points)
   - ❌ Uses `filter(v => v)` (Drupal incompatible)
   - Fix: Use ternary with `null` in array

2. **CSS** (-5 points)
   - ❌ Hardcoded color: `background: #00915A;`
   - Fix: Use `var(--primary)`

3. **Storybook** (-5 points)
   - ❌ Missing `tags: ['autodocs']`
   - Fix: Add to export default

### Required Actions

1. Replace `filter(v => v)` with ternary pattern
2. Replace `#00915A` with `var(--primary)`
3. Add `tags: ['autodocs']` to export default

**Estimated time**: 15 minutes
```

---

## 🔧 Standardization Workflow

**Purpose**: Refactor legacy components to meet current standards.

### Step 1: Audit Existing Component

Run conformity audit (see above) to identify violations.

### Step 2: Prioritize Fixes

**Critical (must fix)**:
- Hardcoded values (colors, sizes, transitions)
- Missing files (twig, css, yml, stories, README)
- Drupal Twig incompatibilities (arrow functions, `.filter()`)
- Missing `tags: ['autodocs']` in Storybook
- Wrong BEM prefix (missing `ps-`)

**Important (should fix)**:
- Flat CSS (no nesting)
- Wrong cascade order (modifiers before base)
- Semantic color names (green → success)
- Missing focus-visible
- Missing Layer 2 component-scoped variables

**Nice to have (can defer)**:
- Documentation improvements
- Additional story showcases
- Extra examples in README

### Step 3: Apply Fixes Systematically

**Order of operations**:
1. Fix Twig (Drupal compatibility)
2. Fix CSS (tokens, nesting, cascade)
3. Fix Storybook (Autodocs, argTypes, stories)
4. Update YAML (realistic data)
5. Update README (sections, examples)

### Step 4: Test and Validate

```bash
npm run build  # Must pass
npm run watch  # Visual validation in Storybook
```

### Step 5: Document Changes

**Commit message**:

```
refactor(elements): standardize Badge component

- Replace hardcoded colors with semantic tokens
- Add CSS nesting with & syntax
- Fix Twig template: remove filter(v => v), use ternary with null
- Add tags: ['autodocs'] in Storybook
- Add Layer 2 component-scoped variables
- Add focus-visible for interactive states
- Update README with BEM structure and tokens

Conformity score: 65/100 → 100/100 ✅
```

**Update changelog**:

```markdown
## [Unreleased]

### Changed
- **Badge** (element) - Standardized to meet current guidelines
  - Replaced hardcoded colors with design tokens
  - Added CSS nesting
  - Fixed Drupal Twig compatibility
  - Added Storybook Autodocs
  - Conformity score: 100/100 ✅
```

---

## 🚨 Build Validation

**Pre-commit checks**:

```bash
npm run build
```

**Validates**:
- ✅ Biome lint (JavaScript, JSON)
- ✅ Biome format (all files)
- ✅ CSS compilation (Vite + PostCSS)
- ✅ No syntax errors (Twig rendered via Storybook)

**If build fails**:
1. Read error message carefully
2. Fix indicated file/line
3. Run `npm run build` again
4. Repeat until passing

**Common errors**:

| Error | Cause | Fix |
|-------|-------|-----|
| `Unknown CSS token --primary` | Typo in token name | Check `source/props/*.css` |
| `Unexpected token =>` | Arrow function in Twig | Use ternary instead |
| `Missing property 'tags'` | Storybook config | Add `tags: ['autodocs']` |
| `Lint error: unused variable` | Unused JS variable | Remove or add `// eslint-disable-line` |

---

## 📊 Changelog Updates

**Location**: `docs/ps-design/CHANGELOG.md`

**Format** (Keep a Changelog):

```markdown
# Changelog

All notable changes to the PS Theme design system.

## [Unreleased]

### Added
- **Component Name** (level) - Brief description
  - Feature 1
  - Feature 2

### Changed
- **Component Name** (level) - What changed
  - Change 1
  - Change 2

### Fixed
- **Component Name** (level) - What was fixed
  - Fix 1

### Deprecated
- **Component Name** (level) - What's deprecated (with migration path)

### Removed
- **Component Name** (level) - What was removed

## [1.2.0] - 2025-12-01

### Added
- **Badge** (element) - Small label for statuses and categories
  - 7 semantic color variants
  - 3 size variants
  - Pill modifier
  - Optional icon support
```

**Rules**:
- Update on every component addition/change
- Use semantic versioning
- Group by type (Added, Changed, Fixed, etc.)
- Include component level (element, component, collection, etc.)
- Link to spec file if applicable

---

## 🔗 Cross-References

- **Core Standards**: `instructions/core.instructions.md` (Tokens, build system)
- **Component Structure**: `instructions/components.instructions.md` (5 required files)
- **CSS Standards**: `instructions/css.instructions.md` (Nesting, cascade, tokens)
- **Storybook**: `instructions/storybook.instructions.md` (Autodocs, stories)
- **Templates**: `instructions/templates.instructions.md` (Twig, YAML)
- **Accessibility**: `instructions/accessibility.instructions.md` (WCAG, ARIA, keyboard)

---

**Last Updated**: 2025-12-05  
**Maintainers**: Design System Team
