---
title: Storybook Standards
version: 3.0.0
lastUpdated: 2025-12-05
applyTo:
  - "**/*.stories.jsx"
  - "**/*.stories.js"
priority: HIGH
related:
  - components.instructions.md
  - base-stories.instructions.md
status: ACTIVE
---

# Storybook Documentation Standards - PS Theme

**Scope**: Storybook format, Autodocs, argTypes, stories structure

---

## 📖 When to Use This File

**Use this file when you need to:**
- ✅ Create **Storybook stories** (.stories.jsx format)
- ✅ Enable **Autodocs** (tags: ['autodocs'])
- ✅ Define **argTypes** with categories (Content, Appearance, Behavior, etc.)
- ✅ Write **showcase stories** (AllColors, AllSizes, UseCases)
- ✅ Follow **HTML/Vite edition** conventions (NOT React/JSX)
- ✅ Import **centralized lists** (colors-list.json, icons-registry.json)

**DO NOT use this file for:**
- ❌ Writing **Twig templates** (see: templates.instructions.md)
- ❌ Learning **CSS patterns** (see: css.instructions.md)
- ❌ Understanding **component structure** (see: components.instructions.md)
- ❌ Implementing **JavaScript behaviors** (see: javascript.instructions.md)
- ❌ Following **workflow steps** (see: workflows.instructions.md)

**Audience**: Developers creating Storybook documentation, AI agents generating stories

---

## 🎯 Core Principles

### Storybook Edition: HTML/Vite (NOT React)

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

---

## 📄 File Structure

### Import Pattern

```jsx
// 1. Import Twig template (unique naming)
import buttonTwig from './button.twig';

// 2. Import default data
import data from './button.yml';

// 3. Import centralized lists (relative paths)
import colorsList from '../../documentation/colors-list.json';
import sizesList from '../../documentation/sizes-list.json';
import iconsRegistry from '../../documentation/icons-registry.json';
import variantsList from '../../documentation/variants-list.json';
```

**⚠️ CRITICAL**: Use **relative paths** (`../../documentation/`), NOT aliases (`@patterns`).

### Export Default

```jsx
export default {
  title: 'Elements/Button',          // Category/Name
  tags: ['autodocs'],                 // MANDATORY - Enables Autodocs
  render: (args) => buttonTwig(args), // Twig render function
  args: data,                         // Default args from YAML
  
  parameters: {
    docs: {
      description: {
        component:
          'Brief description in two lines maximum summarizing role and behavior.\n\n' +
          'Additional details follow in sections (Props, Variants, Accessibility, etc.).',
      },
    },
  },
  
  argTypes: {
    // See ArgTypes section below
  },
};
```

---

## 🏷️ ArgTypes (MANDATORY)

### Categorization

**ALL argTypes MUST be categorized** into one of these groups:

| Category | Purpose | Examples |
|----------|---------|----------|
| **Content** | Content to display | text, icon, label, title, description, children |
| **Appearance** | Visual styling | color, variant, size, shape, pill, bordered, striped |
| **Behavior** | Interactive behavior | disabled, loading, active, expanded, dismissible, clickable |
| **Link** | Navigation/linking | url, href, target, rel |
| **Accessibility** | A11y attributes | ariaLabel, ariaDescribedBy, role, tabIndex |
| **Layout** | Spatial arrangement | alignment, position, orientation, spacing, width |

### ArgType Structure

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
  
  icon: {
    description: 'Icon name (without "icon-" prefix)',
    control: { type: 'select' },
    options: iconsRegistry.names,
    table: {
      category: 'Content',
      type: { summary: 'string' },
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
      type: { summary: 'xs | sm | md | lg | xl' },
      defaultValue: { summary: 'md' },
    },
  },
  
  pill: {
    description: 'Rounded pill shape',
    control: 'boolean',
    table: {
      category: 'Appearance',
      defaultValue: { summary: false },
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
  
  clickable: {
    description: 'Clickable (renders as button)',
    control: 'boolean',
    table: {
      category: 'Behavior',
      defaultValue: { summary: false },
    },
  },
  
  // Link
  url: {
    description: 'Link URL (renders <a> instead of <button>)',
    control: 'text',
    table: {
      category: 'Link',
      type: { summary: 'string' },
    },
  },
  
  target: {
    description: 'Link target',
    control: { type: 'select' },
    options: ['_self', '_blank', '_parent', '_top'],
    table: {
      category: 'Link',
      type: { summary: '_self | _blank | _parent | _top' },
      defaultValue: { summary: '_self' },
    },
  },
  
  // Accessibility
  ariaLabel: {
    description: 'ARIA label for accessibility',
    control: 'text',
    table: {
      category: 'Accessibility',
      type: { summary: 'string' },
    },
  },
  
  // Layout
  alignment: {
    description: 'Text alignment',
    control: { type: 'inline-radio' },
    options: ['left', 'center', 'right'],
    table: {
      category: 'Layout',
      defaultValue: { summary: 'left' },
    },
  },
}
```

### Control Types

| Type | Use Case | Example |
|------|----------|---------|
| `'text'` | String input | `text`, `label`, `ariaLabel` |
| `'boolean'` | True/false toggle | `disabled`, `pill`, `clickable` |
| `'number'` | Numeric input | `value`, `min`, `max` |
| `{ type: 'select' }` | Single select dropdown | `color`, `size`, `icon` |
| `{ type: 'inline-radio' }` | Radio buttons | `size`, `alignment` (< 5 options) |
| `{ type: 'range' }` | Slider | `value` (with min/max) |
| `'object'` | JSON object | `attributes` |

---

## 📖 Documentation Format

### Description Structure

**Opening (≤ 2 lines)**: Brief role and behavior summary  
**Sections**: Detailed information organized by topic

```jsx
parameters: {
  docs: {
    description: {
      component:
        'Button component for user actions with semantic colors, sizes, and optional icons.\n\n' +
        '- **Colors**: primary (default), secondary, success, warning, danger, info — tokens via `--ps-color-*-600`.\n' +
        '- **Sizes**: xs (24px), sm (32px), md (40px, default), lg (48px), xl (56px).\n' +
        '- **Icons**: Optional leading/trailing icons via `icon_start`/`icon_end` props.\n' +
        '- **States**: hover, focus-visible, active, disabled.\n' +
        '- **Link mode**: Pass `url` prop to render as `<a>` instead of `<button>`.\n' +
        '- **Accessibility**: WCAG AA contrast, visible focus indicator, keyboard support (Space/Enter).\n' +
        '- **Design tokens**: `--ps-button-*` component-scoped variables for customization.\n' +
        '- **Minimal markup**: Default styles applied via base class; modifiers only when different from default.',
    },
  },
},
```

### Section Format

Use Markdown with specific sections:

```markdown
- **Colors**: List options + token reference
- **Sizes**: List options + pixel values
- **Icons**: How icons work (prop names, positioning)
- **States**: hover, focus, active, disabled, loading
- **Variants**: Different appearances (solid, outline, ghost)
- **Accessibility**: WCAG compliance, ARIA, keyboard
- **Design tokens**: Component-scoped variables
- **Minimal markup**: Default classes vs modifiers
- **Use cases**: Real-world examples (Real Estate context)
```

---

## 📚 Stories Structure

### Story Types

**1. Default Story** (REQUIRED):
```jsx
export const Default = {
  render: (args) => buttonTwig(args),
  args: { ...data },
};
```
**Purpose**: Interactive controls playground.

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

export const WithIcons = {
  render: () => `
    <div style="display: flex; gap: var(--size-4); flex-wrap: wrap;">
      ${buttonTwig({ text: 'Icon Start', icon_start: 'check' })}
      ${buttonTwig({ text: 'Icon End', icon_end: 'arrow-right' })}
      ${buttonTwig({ text: 'Both', icon_start: 'check', icon_end: 'arrow-right' })}
    </div>
  `,
};

export const States = {
  render: () => `
    <div style="display: flex; gap: var(--size-4);">
      ${buttonTwig({ text: 'Normal' })}
      ${buttonTwig({ text: 'Disabled', disabled: true })}
      ${buttonTwig({ text: 'Loading', loading: true })}
    </div>
  `,
};

export const UseCases = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-4); max-width: 400px;">
      <h3>Property Listing CTA</h3>
      ${buttonTwig({ text: 'View Property Details', color: 'primary', icon_end: 'arrow-right' })}
      
      <h3>Search Form</h3>
      ${buttonTwig({ text: 'Search Properties', icon_start: 'search', color: 'secondary' })}
      
      <h3>Contact Agent</h3>
      ${buttonTwig({ text: 'Schedule a Visit', icon_start: 'calendar', color: 'success' })}
    </div>
  `,
};
```

**3. Forbidden Stories** (DO NOT CREATE):
```jsx
❌ export const Primary = { ... };      // Redundant with AllColors
❌ export const Secondary = { ... };    // Redundant with AllColors
❌ export const Small = { ... };        // Redundant with AllSizes
❌ export const Large = { ... };        // Redundant with AllSizes
```

**Why?** Individual stories create noise. Showcases group related variants together.

---

## 🌍 Language Rule

**ALL Storybook documentation MUST be in English.**

**English required**:
- `parameters.docs.description.component`
- `argTypes[prop].description`
- Story names (`AllColors`, `UseCases`, not `ToutesLesCouleurs`)
- Section titles ("Variants", "Accessibility", not "Variantes")

**French allowed**:
- User-facing demo content (`text: 'Rechercher'` in stories)

**Never translate**:
- Token names (`--primary`, `--size-4`)
- Class names (`.ps-button`, `.ps-button--large`)
- Prop names (`color`, `size`, `disabled`)

---

## 📋 Centralized Lists

### Import Pattern

```jsx
import colorsList from '../../documentation/colors-list.json';
import sizesList from '../../documentation/sizes-list.json';
import iconsRegistry from '../../documentation/icons-registry.json';
import variantsList from '../../documentation/variants-list.json';
```

### List Structure

**colors-list.json**:
```json
{
  "semantic": {
    "name": "Semantic Colors",
    "values": ["primary", "secondary", "success", "warning", "danger", "info"]
  },
  "extended": {
    "name": "Extended Colors",
    "values": ["default", "primary", "secondary", "gold", "success", "warning", "danger", "info"]
  }
}
```

**sizesList.json**:
```json
{
  "compact": {
    "name": "Standard Sizes",
    "values": ["xs", "sm", "md", "lg", "xl"]
  },
  "extended": {
    "name": "Extended Sizes",
    "values": ["2xs", "xs", "sm", "md", "lg", "xl", "2xl"]
  }
}
```

### Usage in ArgTypes

```jsx
color: {
  control: { type: 'select' },
  options: colorsList.semantic.values, // ← Centralized list
  description: 'Color variant',
},

size: {
  control: { type: 'inline-radio' },
  options: sizesList.compact.values, // ← Centralized list
  description: 'Size variant',
},

icon: {
  control: { type: 'select' },
  options: iconsRegistry.names, // ← Complete generated registry
  description: 'Icon name',
},
```

---

## 🚫 Anti-Patterns

### 1. React/JSX

```jsx
❌ import React from 'react';
❌ export const Default = () => <button>Text</button>;
```

### 2. Missing tags: ['autodocs']

```jsx
❌ export default {
     title: 'Elements/Button',
     // Missing tags!
   };
```

### 3. Long Opening Description

```jsx
❌ component: 'Button is a component that allows users to trigger actions...' (50+ lines)

✅ component: 'Brief summary (≤ 2 lines).\n\nDetailed sections follow.'
```

### 4. Individual Stories

```jsx
❌ export const Primary = { ... };
❌ export const Small = { ... };
```

### 5. Hardcoded Lists

```jsx
❌ options: ['primary', 'secondary', 'success', ...] // Hardcoded

✅ options: colorsList.semantic.values // Centralized
```

### 6. Missing ArgTypes Categories

```jsx
❌ text: {
     control: 'text',
     // Missing table.category!
   }

✅ text: {
     control: 'text',
     table: {
       category: 'Content',
     },
   }
```

### 7. Alias Imports

```jsx
❌ import colorsList from '@patterns/documentation/colors-list.json';

✅ import colorsList from '../../documentation/colors-list.json';
```

---

## 🔗 Cross-References

- **Component Structure**: `instructions/components.instructions.md`
- **Twig Templates**: `instructions/templates.instructions.md`
- **Accessibility**: `instructions/accessibility.instructions.md`

---

**Last Updated**: 2025-12-05  
**Maintainers**: Design System Team
