# Prompt: Update Storybook Configuration

**Purpose**: Fix Storybook configuration issues (autodocs, argTypes, stories).

---

## 📋 Prompt Template

```
Fix Storybook configuration for: {COMPONENT_NAME}
Location: source/patterns/{level}/{component}/{component}.stories.jsx

OBJECTIVE: Ensure full Storybook compliance with PS Theme v4.0.0 standards

COMMON ISSUES TO FIX:

1. Missing autodocs tags
2. Wrong import/render format (React/JSX instead of Twig)
3. Uncategorized argTypes
4. Missing story variations
5. Inconsistent export naming

STEP 1: CHECK CURRENT STATE

Verify export default:
- Must have: tags: ['autodocs']
- Must have: title: '{Level}/{ComponentName}'
- Must have: parameters: { docs: { description: { component: '...' }}}

Verify imports:
- Must use: import componentTwig from './component.twig'
- Must NOT use: import { Component } from './component'

Verify render function:
- Must use: render: (args) => componentTwig(args)
- Must NOT use: render: (args) => <Component {...args} />

STEP 2: FIX EXPORT DEFAULT

BEFORE (missing autodocs):
export default {
  title: 'Elements/Button',
  parameters: {
    docs: {
      description: {
        component: 'Interactive button component',
      },
    },
  },
};

AFTER (with autodocs):
export default {
  title: 'Elements/Button',
  tags: ['autodocs'],  // ✅ CRITICAL - Enables Autodocs
  parameters: {
    docs: {
      description: {
        component: 'Interactive button component with semantic variants.',  // Max 2 lines
      },
    },
  },
};

STEP 3: FIX IMPORTS & RENDER

BEFORE (wrong format - React/JSX):
import Button from './button';

export const Default = {
  render: (args) => <Button {...args} />,
  args: { ... }
};

AFTER (correct format - Twig):
import buttonTwig from './button.twig';

export const Default = {
  render: (args) => buttonTwig(args),  // ✅ Twig template function
  args: { ... }
};

STEP 4: CATEGORIZE ARGTYPES

Organize argTypes into 6 standard categories:

argTypes: {
  // CATEGORY 1: Content
  text: {
    control: 'text',
    description: 'Button label text',
    table: { category: 'Content' },
  },
  icon: {
    control: 'text',
    description: 'Icon name (optional)',
    table: { category: 'Content' },
  },
  
  // CATEGORY 2: Appearance
  variant: {
    control: 'select',
    options: ['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'light', 'dark'],
    description: 'Visual variant (semantic color)',
    table: { category: 'Appearance', defaultValue: { summary: 'primary' } },
  },
  size: {
    control: 'select',
    options: ['sm', 'md', 'lg'],
    description: 'Button size',
    table: { category: 'Appearance', defaultValue: { summary: 'md' } },
  },
  
  // CATEGORY 3: Behavior
  disabled: {
    control: 'boolean',
    description: 'Disable button interaction',
    table: { category: 'Behavior', defaultValue: { summary: false } },
  },
  loading: {
    control: 'boolean',
    description: 'Show loading state',
    table: { category: 'Behavior', defaultValue: { summary: false } },
  },
  
  // CATEGORY 4: Layout
  fullWidth: {
    control: 'boolean',
    description: 'Expand to full container width',
    table: { category: 'Layout', defaultValue: { summary: false } },
  },
  
  // CATEGORY 5: Links
  url: {
    control: 'text',
    description: 'Link destination (optional)',
    table: { category: 'Links' },
  },
  target: {
    control: 'select',
    options: ['_self', '_blank'],
    description: 'Link target attribute',
    table: { category: 'Links', defaultValue: { summary: '_self' } },
  },
  
  // CATEGORY 6: Accessibility
  ariaLabel: {
    control: 'text',
    description: 'Accessible label for screen readers',
    table: { category: 'Accessibility' },
  },
  ariaDescribedBy: {
    control: 'text',
    description: 'ID of element describing button',
    table: { category: 'Accessibility' },
  },
}

STEP 5: ADD REQUIRED STORIES

Minimum: Default + 3-4 Showcases

Default Story (interactive playground):
export const Default = {
  render: (args) => buttonTwig(args),
  args: {
    text: 'Button Label',
    variant: 'primary',
    size: 'md',
  },
};

Showcase 1 (Variants):
export const Variants = {
  render: () => `
    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
      ${buttonTwig({ text: 'Primary', variant: 'primary' })}
      ${buttonTwig({ text: 'Secondary', variant: 'secondary' })}
      ${buttonTwig({ text: 'Success', variant: 'success' })}
      ${buttonTwig({ text: 'Danger', variant: 'danger' })}
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story: 'All semantic color variants.',
      },
    },
  },
};

Showcase 2 (Sizes):
export const Sizes = {
  render: () => `
    <div style="display: flex; gap: 1rem; align-items: center;">
      ${buttonTwig({ text: 'Small', size: 'sm' })}
      ${buttonTwig({ text: 'Medium', size: 'md' })}
      ${buttonTwig({ text: 'Large', size: 'lg' })}
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story: 'Button sizes: small, medium (default), large.',
      },
    },
  },
};

Showcase 3 (States):
export const States = {
  render: () => `
    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
      ${buttonTwig({ text: 'Normal' })}
      ${buttonTwig({ text: 'Disabled', disabled: true })}
      ${buttonTwig({ text: 'Loading', loading: true })}
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story: 'Interactive states: normal, disabled, loading.',
      },
    },
  },
};

Showcase 4 (With Icons):
export const WithIcons = {
  render: () => `
    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
      ${buttonTwig({ text: 'Search', icon: 'search' })}
      ${buttonTwig({ text: 'Download', icon: 'download' })}
      ${buttonTwig({ text: 'Delete', icon: 'trash', variant: 'danger' })}
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story: 'Buttons with icons from icon atom.',
      },
    },
  },
};

STEP 6: REMOVE INDIVIDUAL VARIANT STORIES

❌ DON'T create individual stories for each variant:

// ❌ WRONG - Clutters sidebar
export const Primary = { args: { variant: 'primary' } };
export const Secondary = { args: { variant: 'secondary' } };
export const Success = { args: { variant: 'success' } };

✅ DO create Showcase stories demonstrating ALL variants:

// ✅ CORRECT - Single story with all variants
export const Variants = {
  render: () => `
    ${/* All variants rendered together */}
  `,
};

STEP 7: VALIDATE

A. Check Autodocs:
npm run storybook
→ Navigate to component
→ Verify "Docs" tab exists
→ Verify Props table auto-generated
→ Verify categorized controls

B. Verify Controls:
→ Click "Default" story
→ Check Controls panel
→ Verify categories visible
→ Test each control updates component

C. Check Stories:
→ Verify Default story works (interactive)
→ Verify Showcase stories render
→ Verify story descriptions appear

D. Lint check:
npm run build
→ Must pass without errors

STEP 8: UPDATE README (if needed)

Add Storybook section:

## Storybook

View live examples: [Elements/Button](http://localhost:6006/?path=/docs/elements-button--docs)

Available stories:
- **Default**: Interactive playground with all controls
- **Variants**: All semantic color variants
- **Sizes**: Small, medium, large
- **States**: Normal, disabled, loading
- **With Icons**: Button + icon combinations

STEP 9: COMMIT

Format:
fix({level}): Fix {component} Storybook configuration

Changes:
- Add missing tags: ['autodocs'] ✅
- Fix import/render format (Twig not React)
- Categorize argTypes (6 categories: Content, Appearance, Behavior, Layout, Links, Accessibility)
- Add showcase stories: {list}
- Remove individual variant stories (clutter)
- Update story descriptions

Autodocs: Now fully functional ✅
Controls: Organized and working ✅

References: .github/instructions/03-technical-implementation.md (section 3)

TROUBLESHOOTING:

Issue: Autodocs not showing
→ Check: tags: ['autodocs'] in export default
→ Check: Rebuild Storybook (npm run storybook)

Issue: Controls not working
→ Check: argTypes defined correctly
→ Check: render function uses (args) => componentTwig(args)

Issue: Props table empty
→ Check: Twig template has @param comments
→ Check: argTypes match Twig params

Issue: React error in console
→ Check: Using import componentTwig not import { Component }
→ Check: NO JSX syntax (<Component />)

SUCCESS CRITERIA:
✅ Autodocs tab visible with Props table
✅ Controls panel organized by categories
✅ Default + 3-4 showcase stories
✅ All stories render without errors
✅ Build passes
```

---

**Estimated Time**: 30-45 minutes  
**Difficulty**: Easy-Medium  
**Prerequisites**: Understanding of Storybook HTML edition (Twig templates)
