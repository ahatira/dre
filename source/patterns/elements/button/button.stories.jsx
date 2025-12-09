import iconsRegistry from '../../documentation/icons-registry.json';
import buttonTwig from './button.twig';
import data from './button.yml';

export default {
  title: 'Elements/Button',
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component:
          'Interactive action trigger with semantic variants, sizes, and styles. Supports icons, loading/disabled states, links, and full-width layout using design tokens.',
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
      options: [null, ...iconsRegistry.names],
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
      description:
        'Semantic variant (neutral: gray default, primary: green, secondary: pink, success/info/warning/danger)',
      control: { type: 'select' },
      options: ['neutral', 'primary', 'secondary', 'success', 'info', 'warning', 'danger'],
      table: {
        category: 'Appearance',
        type: { summary: 'primary | secondary | neutral | success | info | warning | danger' },
        defaultValue: { summary: 'neutral' },
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
      description:
        'Button size scale (xs: 28px, sm: 32px, md: 36px, lg: 40px, xl: 44px, xxl: 48px)',
      control: { type: 'select' },
      options: ['xs', 'sm', 'md', 'lg', 'xl', 'xxl'],
      table: {
        category: 'Appearance',
        type: { summary: 'xs | sm | md | lg | xl | xxl' },
        defaultValue: { summary: 'md' },
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
    // Advanced
    baseClass: {
      description:
        'Override BEM block class name (for custom button variants in parent components)',
      control: { type: 'text' },
      table: {
        category: 'Advanced',
        type: { summary: 'string' },
        defaultValue: { summary: 'ps-button' },
      },
    },
    // Toggle
    toggle: {
      description:
        'Enable toggle functionality via data-ps-toggle="button". Toggles .active class and aria-pressed attribute on click.',
      control: { type: 'boolean' },
      table: {
        category: 'Behavior',
        type: { summary: 'boolean' },
        defaultValue: { summary: false },
      },
    },
    active: {
      description:
        'Pre-toggled state (only applies when toggle=true). Renders .active class and aria-pressed="true".',
      control: { type: 'boolean' },
      table: {
        category: 'Behavior',
        type: { summary: 'boolean' },
        defaultValue: { summary: false },
      },
    },
  },
};

export const Default = {
  render: (args) => buttonTwig(args),
  args: { ...data, variant: 'neutral' },
};

export const Variants = {
  render: () => `
    <div style="display: flex; gap: var(--size-4); flex-wrap: wrap;">
      ${['neutral', 'primary', 'secondary', 'success', 'info', 'warning', 'danger'].map((variant) => buttonTwig({ label: variant.charAt(0).toUpperCase() + variant.slice(1), variant })).join('')}
    </div>
  `,
};

export const Outlines = {
  render: () => `
    <div style="display: flex; gap: var(--size-4); flex-wrap: wrap;">
      ${[
        // Show neutral outline first as the default
        'neutral',
        'primary',
        'secondary',
        'success',
        'info',
        'warning',
        'danger',
      ]
        .map((variant) =>
          buttonTwig({
            label: variant.charAt(0).toUpperCase() + variant.slice(1),
            variant,
            outline: true,
          })
        )
        .join('')}
    </div>
  `,
};

export const Sizes = {
  render: () => `
    <div style="display: flex; gap: var(--size-4); align-items: flex-end;">
      ${['xs', 'sm', 'md', 'lg', 'xl', 'xxl'].map((size) => buttonTwig({ label: size.toUpperCase(), variant: 'primary', size })).join('')}
    </div>
  `,
};

export const WithIcons = {
  render: () => `
    <div style="display: flex; gap: var(--size-4); flex-wrap: wrap;">
      ${buttonTwig({ label: 'Search', variant: 'primary', icon: 'search', iconPosition: 'left' })}
      ${buttonTwig({ label: 'Next', variant: 'primary', icon: 'arrow-right', iconPosition: 'right' })}
      ${buttonTwig({ icon: 'close', variant: 'primary', size: 'md' })}
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

export const Toggle = {
  name: 'Toggle',
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-6);">
      <div>
        <h3 style="margin: 0 0 var(--size-4) 0; font-size: var(--size-305); font-weight: var(--font-weight-bold); color: var(--gray-900);">Inactive State</h3>
        <div style="display: flex; gap: var(--size-4); flex-wrap: wrap;">
          ${['neutral', 'primary', 'secondary', 'success', 'info', 'warning', 'danger']
            .map((variant) =>
              buttonTwig({
                label: variant.charAt(0).toUpperCase() + variant.slice(1),
                variant,
                icon: 'heart',
                toggle: true,
              })
            )
            .join('')}
        </div>
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-4) 0; font-size: var(--size-305); font-weight: var(--font-weight-bold); color: var(--gray-900);">Active State (Pre-Toggled)</h3>
        <div style="display: flex; gap: var(--size-4); flex-wrap: wrap;">
          ${['neutral', 'primary', 'secondary', 'success', 'info', 'warning', 'danger']
            .map((variant) =>
              buttonTwig({
                label: variant.charAt(0).toUpperCase() + variant.slice(1),
                variant,
                icon: 'heart',
                toggle: true,
                active: true,
              })
            )
            .join('')}
        </div>
      </div>
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story:
          'Toggle button functionality with all color variants. Click to toggle .active class and aria-pressed attribute. Includes both inactive and active states with all variants. Uses data-ps-toggle="button" behavior.',
      },
    },
  },
};

export const ToggleIconsOnly = {
  name: 'Toggle Icons Only',
  render: () => `
    <div style="display: flex; gap: var(--size-4); flex-wrap: wrap; align-items: center;">
      ${['neutral', 'primary', 'secondary', 'success', 'info', 'warning', 'danger']
        .map(
          (variant) => `
        <div style="display: flex; flex-direction: column; align-items: center; gap: var(--size-2);">
          <span style="font-size: var(--size-305); color: var(--gray-700);">${variant}</span>
          ${buttonTwig({ icon: 'heart', variant, toggle: true, label: '' })}
        </div>
      `
        )
        .join('')}
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story:
          'Toggle icon-only buttons with all color variants. Inactive: gray (#333333), Active: variant color. Perfect for favorite/like/bookmark actions.',
      },
    },
  },
};
