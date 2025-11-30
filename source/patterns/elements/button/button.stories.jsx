import buttonTwig from './button.twig';
import data from './button.yml';
import iconsList from '../../documentation/icons-list.json';

export default {
  title: 'Elements/Button',
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component: `Interactive action trigger with semantic variants and multiple states.

**Key Features:**
- 8 semantic variants: primary (green), secondary (pink), success, info, warning, danger, dark, light
- 2 styles: filled (default), outline (transparent bg + border)
- 3 sizes: small (34px), medium (36px, default), large (40px)
- Icon support: optional left/right positioning, icon-only mode
- States: disabled (50% opacity), loading (spinner overlay)
- Layout: fullWidth option for block-level buttons
- Pure token implementation (colors, spacing, typography, transitions)

**Usage Guidelines:**
- Use primary for main actions; secondary for alternatives
- Prefer semantic colors (success/danger) over generic for contextual actions
- Outline style for less prominent actions or when stacking multiple buttons
- Icon-only buttons require accessible label (aria-label)
- Loading state automatically disables interaction
- Size small for compact UIs; large for hero/CTA contexts
- Full width for forms or mobile layouts

**Accessibility:**
- Renders <button> by default; <a> when url provided
- Disabled state: aria-disabled + disabled attribute (button) or pointer-events none (link)
- Loading state: aria-busy="true" announces to screen readers
- Focus outline visible for keyboard navigation (:focus-visible)
- Icons marked aria-hidden (label always present, visually hidden if icon-only)
- Minimum touch target 36px (WCAG 2.2 Level A)
- Color contrast verified for all variants (AA minimum)

**Design Tokens:**
- Colors: --btn-primary/secondary/success/info/warning/danger/dark/light + hover/active variants
- Sizing: --size-2 (gap), --size-4 (padding), --size-9 (height md), --size-10 (height lg)
- Typography: --font-sans, --font-weight-400, --size-305/4 (font sizes)
- Border: --border-size-2 (outline + focus)
- Transition: cubic-bezier(0.4, 0.0, 0.2, 1) 150ms

**Do Not:**
- Use for navigation alone (prefer link component unless button styling required)
- Stack too many primary buttons (max 1 per screen section)
- Omit label text (icon-only requires aria-label)
- Hardcode colors or dimensions`,
      },
    },
  },
  argTypes: {
    // Content
    label: {
      description: 'Button text',
      control: { type: 'text' },
      table: {
        category: 'Content',
        type: { summary: 'string', required: true },
        defaultValue: { summary: 'Button' },
      },
    },
    icon: {
      description: 'Icon name to display (optional)',
      control: { type: 'select' },
      options: iconsList.categories.generic,
      table: {
        category: 'Content',
        type: { summary: 'string' },
      },
    },
    iconPosition: {
      description: 'Icon position relative to text',
      control: { type: 'select' },
      options: ['left', 'right'],
      table: {
        category: 'Content',
        type: { summary: 'left | right' },
        defaultValue: { summary: 'right' },
      },
    },
    // Appearance
    variant: {
      description: 'Semantic variant (primary: green, secondary: pink, success/info/warning/danger, dark/light)',
      control: { type: 'select' },
      options: ['primary', 'secondary', 'success', 'info', 'warning', 'danger', 'dark', 'light'],
      table: {
        category: 'Appearance',
        type: { summary: 'primary | secondary | success | info | warning | danger | dark | light' },
        defaultValue: { summary: 'primary' },
      },
    },
    outline: {
      description: 'Outline version (border only, transparent background)',
      control: { type: 'boolean' },
      table: {
        category: 'Appearance',
        type: { summary: 'boolean' },
        defaultValue: { summary: false },
      },
    },
    size: {
      description: 'Button size (small: 34px, medium: 36px, large: 40px)',
      control: { type: 'select' },
      options: ['small', 'medium', 'large'],
      table: {
        category: 'Appearance',
        type: { summary: 'small | medium | large' },
        defaultValue: { summary: 'medium' },
      },
    },
    fullWidth: {
      description: 'Full width button (width: 100%)',
      control: { type: 'boolean' },
      table: {
        category: 'Appearance',
        type: { summary: 'boolean' },
        defaultValue: { summary: false },
      },
    },
    // Behavior
    disabled: {
      description: 'Disable button (reduces opacity to 50%)',
      control: { type: 'boolean' },
      table: {
        category: 'Behavior',
        type: { summary: 'boolean' },
        defaultValue: { summary: false },
      },
    },
    loading: {
      description: 'Display loading state',
      control: { type: 'boolean' },
      table: {
        category: 'Behavior',
        type: { summary: 'boolean' },
        defaultValue: { summary: false },
      },
    },
    // Link
    url: {
      description: 'Destination URL (transforms button to link)',
      control: { type: 'text' },
      table: {
        category: 'Link',
        type: { summary: 'string' },
      },
    },
    target: {
      description: 'Link target attribute',
      control: { type: 'select' },
      options: ['_self', '_blank'],
      table: {
        category: 'Link',
        type: { summary: '_self | _blank' },
        defaultValue: { summary: '_self' },
      },
    },
  },
};

export const Default = {
  render: (args) => buttonTwig(args),
  args: { ...data },
};

export const AllVariants = {
  render: () => `
    <div style="display: flex; gap: var(--size-4); flex-wrap: wrap;">
      ${['primary', 'secondary', 'success', 'info', 'warning', 'danger', 'dark', 'light'].map(variant => buttonTwig({ label: variant.charAt(0).toUpperCase() + variant.slice(1), variant })).join('')}
    </div>
  `,
};

export const AllOutlines = {
  render: () => `
    <div style="display: flex; gap: var(--size-4); flex-wrap: wrap;">
      ${['primary', 'secondary', 'success', 'info', 'warning', 'danger', 'dark', 'light'].map(variant => buttonTwig({ label: variant.charAt(0).toUpperCase() + variant.slice(1), variant, outline: true })).join('')}
    </div>
  `,
};

export const AllSizes = {
  render: () => `
    <div style="display: flex; gap: var(--size-4); align-items: center;">
      ${['small', 'medium', 'large'].map(size => buttonTwig({ label: size.charAt(0).toUpperCase() + size.slice(1), variant: 'primary', size })).join('')}
    </div>
  `,
};

export const WithIcons = {
  render: () => `
    <div style="display: flex; gap: var(--size-4); flex-wrap: wrap;">
      ${buttonTwig({ label: 'Search', variant: 'primary', icon: 'search', iconPosition: 'left' })}
      ${buttonTwig({ label: 'Next', variant: 'primary', icon: 'arrow-right', iconPosition: 'right' })}
      ${buttonTwig({ icon: 'close', variant: 'primary', size: 'medium' })}
    </div>
  `,
};

export const FullWidth = {
  render: () => buttonTwig({ label: 'Full Width Button', variant: 'primary', fullWidth: true }),
};

export const Loading = {
  render: () => `
    <div style="display: flex; gap: var(--size-4); align-items: center;">
      ${buttonTwig({ label: 'Loading...', variant: 'primary', loading: true })}
      ${buttonTwig({ label: 'Loading...', variant: 'secondary', outline: true, loading: true })}
    </div>
  `,
};

export const Disabled = {
  render: () => `
    <div style="display: flex; gap: var(--size-4); align-items: center;">
      ${buttonTwig({ label: 'Disabled', variant: 'primary', disabled: true })}
      ${buttonTwig({ label: 'Disabled', variant: 'secondary', outline: true, disabled: true })}
    </div>
  `,
};
