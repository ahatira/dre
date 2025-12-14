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
      description: 'Icon name from sprite (no "icon-" prefix, e.g., "check", "arrow-right")',
      control: { type: 'select' },
      options: [null, ...iconsRegistry.names],
      table: {
        category: 'Content',
        type: { summary: 'string' },
      },
    },
    iconPosition: {
      description: 'Icon position: start (::before, default) or end (::after)',
      control: { type: 'inline-radio' },
      options: ['start', 'end'],
      table: {
        category: 'Appearance',
        type: { summary: 'start | end' },
        defaultValue: { summary: 'start' },
      },
    },
    // Appearance
    variant: {
      description:
        'Semantic variant: primary (green), secondary (pink), gold, success/info/warning/danger, light/dark. Omit for neutral (gray default)',
      control: { type: 'select' },
      options: [
        null,
        'primary',
        'secondary',
        'gold',
        'success',
        'info',
        'warning',
        'danger',
        'light',
        'dark',
      ],
      table: {
        category: 'Appearance',
        type: {
          summary: 'primary | secondary | gold | success | info | warning | danger | light | dark',
        },
        defaultValue: { summary: 'null (neutral/gray)' },
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
      description: 'Button size: small (32px), medium (36px, default/omit), large (40px)',
      control: { type: 'select' },
      options: [null, 'small', 'large'],
      table: {
        category: 'Appearance',
        type: { summary: 'small | large' },
        defaultValue: { summary: 'null (medium)' },
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

// ============================================
// 1. PLAYGROUND (Interactive Controls)
// ============================================

export const Default = {
  render: (args) => buttonTwig(args),
  args: { ...data, variant: 'primary', label: 'Button' },
};

// ============================================
// 2. VARIANTS (Color System)
// ============================================

export const Variants = {
  name: 'Variants',
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-6);">
      <div>
        <h3 style="margin: 0 0 var(--size-4) 0; font-size: var(--size-4); font-weight: var(--font-weight-bold); color: var(--gray-900);">Solid</h3>
        <div style="display: flex; gap: var(--size-3); flex-wrap: wrap;">
          ${buttonTwig({ label: 'Neutral', variant: null })}
          ${[
            'primary',
            'secondary',
            'gold',
            'success',
            'info',
            'warning',
            'danger',
            'light',
            'dark',
          ]
            .map((variant) =>
              buttonTwig({ label: variant.charAt(0).toUpperCase() + variant.slice(1), variant })
            )
            .join('')}
        </div>
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-4) 0; font-size: var(--size-4); font-weight: var(--font-weight-bold); color: var(--gray-900);">Outline</h3>
        <div style="display: flex; gap: var(--size-3); flex-wrap: wrap;">
          ${buttonTwig({ label: 'Neutral', variant: null, outline: true })}
          ${[
            'primary',
            'secondary',
            'gold',
            'success',
            'info',
            'warning',
            'danger',
            'light',
            'dark',
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
      </div>
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story:
          'All semantic color variants (neutral + 9 colors) in solid and outline styles. Each variant uses design tokens for consistent theming.',
      },
    },
  },
};

// ============================================
// 3. SIZES (Scale System)
// ============================================

export const Sizes = {
  name: 'Sizes',
  render: () => `
    <div style="display: flex; gap: var(--size-4); align-items: flex-end; flex-wrap: wrap;">
      ${buttonTwig({ label: 'Small', variant: 'primary', size: 'small' })}
      ${buttonTwig({ label: 'Medium', variant: 'primary', size: null })}
      ${buttonTwig({ label: 'Large', variant: 'primary', size: 'large' })}
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story:
          'Three size scales: small (32px), medium (36px, default), large (40px). Medium achieved by omitting size prop (null).',
      },
    },
  },
};

// ============================================
// 4. WITH ICONS (Icon Integration)
// ============================================

export const WithIcons = {
  name: 'With Icons',
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-6);">
      <div>
        <h3 style="margin: 0 0 var(--size-4) 0; font-size: var(--size-4); font-weight: var(--font-weight-bold); color: var(--gray-900);">Icon Positions</h3>
        <div style="display: flex; gap: var(--size-3); flex-wrap: wrap;">
          ${buttonTwig({ label: 'Search', variant: 'primary', icon: 'search', iconPosition: 'start' })}
          ${buttonTwig({ label: 'Next', variant: 'primary', icon: 'arrow-right', iconPosition: 'end' })}
          ${buttonTwig({ label: 'Download', variant: 'secondary', icon: 'download', iconPosition: 'start' })}
        </div>
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-4) 0; font-size: var(--size-4); font-weight: var(--font-weight-bold); color: var(--gray-900);">Icon Only</h3>
        <div style="display: flex; gap: var(--size-3); flex-wrap: wrap; align-items: center;">
          ${['xs', 'sm', 'md', 'lg', 'xl', 'xxl']
            .map((size) => buttonTwig({ icon: 'heart', variant: 'primary', size, label: '' }))
            .join('')}
        </div>
      </div>
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story:
          'Buttons with icons at start/end positions, plus icon-only variants across all sizes. Label is visually hidden for accessibility.',
      },
    },
  },
};

// ============================================
// 5. STATES (Interactive States)
// ============================================

export const States = {
  name: 'States',
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-6);">
      <div>
        <h3 style="margin: 0 0 var(--size-4) 0; font-size: var(--size-4); font-weight: var(--font-weight-bold); color: var(--gray-900);">Loading</h3>
        <div style="display: flex; gap: var(--size-3); flex-wrap: wrap;">
          ${buttonTwig({ label: 'Processing...', variant: 'primary', loading: true })}
          ${buttonTwig({ label: 'Loading...', variant: 'secondary', loading: true })}
          ${buttonTwig({ label: 'Saving...', variant: 'success', outline: true, loading: true })}
        </div>
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-4) 0; font-size: var(--size-4); font-weight: var(--font-weight-bold); color: var(--gray-900);">Disabled</h3>
        <div style="display: flex; gap: var(--size-3); flex-wrap: wrap;">
          ${buttonTwig({ label: 'Disabled', variant: 'primary', disabled: true })}
          ${buttonTwig({ label: 'Disabled', variant: 'secondary', disabled: true })}
          ${buttonTwig({ label: 'Disabled', variant: 'danger', outline: true, disabled: true })}
        </div>
      </div>
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story:
          'Loading state shows spinner (pointer-events: none), disabled state reduces opacity to 50%.',
      },
    },
  },
};

// ============================================
// 6. TOGGLES (Toggle Functionality)
// ============================================

export const Toggles = {
  name: 'Toggles',
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-6);">
      <div>
        <h3 style="margin: 0 0 var(--size-4) 0; font-size: var(--size-4); font-weight: var(--font-weight-bold); color: var(--gray-900);">Toggle Buttons (Click to toggle)</h3>
        <div style="display: flex; gap: var(--size-3); flex-wrap: wrap;">
          ${['primary', 'secondary', 'success', 'danger']
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
        <h3 style="margin: 0 0 var(--size-4) 0; font-size: var(--size-4); font-weight: var(--font-weight-bold); color: var(--gray-900);">Icon Only Toggles</h3>
        <div style="display: flex; gap: var(--size-3); flex-wrap: wrap;">
          ${['primary', 'secondary', 'success', 'danger']
            .map((variant) => buttonTwig({ icon: 'heart', variant, toggle: true, label: '' }))
            .join('')}
        </div>
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-4) 0; font-size: var(--size-4); font-weight: var(--font-weight-bold); color: var(--gray-900);">Pre-toggled (Active State)</h3>
        <div style="display: flex; gap: var(--size-3); flex-wrap: wrap;">
          ${['primary', 'secondary', 'success', 'danger']
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
          'Toggle functionality with data-ps-toggle="button". Inactive: subtle background, Active: full color. Perfect for favorites, bookmarks, filters.',
      },
    },
  },
};

// ============================================
// End of Stories
// ============================================
