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
        'Semantic variant (neutral: gray default, primary: green, secondary: pink, success/info/warning/danger)',
      control: { type: 'select' },
      options: ['neutral', 'primary', 'secondary', 'success', 'info', 'warning', 'danger', 'gold'],
      table: {
        category: 'Appearance',
        type: {
          summary: 'neutral | primary | secondary | gold | success | info | warning | danger',
        },
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
          ${['neutral', 'primary', 'secondary', 'gold', 'success', 'info', 'warning', 'danger']
            .map((variant) =>
              buttonTwig({ label: variant.charAt(0).toUpperCase() + variant.slice(1), variant })
            )
            .join('')}
        </div>
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-4) 0; font-size: var(--size-4); font-weight: var(--font-weight-bold); color: var(--gray-900);">Outline</h3>
        <div style="display: flex; gap: var(--size-3); flex-wrap: wrap;">
          ${['neutral', 'primary', 'secondary', 'gold', 'success', 'info', 'warning', 'danger']
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
          '10 semantic color variants in solid and outline styles. Each variant uses design tokens for consistent theming.',
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
      ${['xs', 'sm', 'md', 'lg', 'xl', 'xxl']
        .map((size) => buttonTwig({ label: size.toUpperCase(), variant: 'primary', size }))
        .join('')}
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story:
          'Six size scales: xs (28px), sm (32px), md (36px, default), lg (40px), xl (44px), xxl (48px).',
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
// 7. REAL ESTATE USE CASES (Context Examples)
// ============================================

export const UseCases = {
  name: 'Use Cases',
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-8);">
      <!-- Property Search -->
      <div>
        <h3 style="margin: 0 0 var(--size-4) 0; font-size: var(--size-4); font-weight: var(--font-weight-bold); color: var(--gray-900);">Property Search</h3>
        <div style="display: flex; gap: var(--size-3); flex-wrap: wrap;">
          ${buttonTwig({ label: 'Search Properties', variant: 'primary', icon: 'search', size: 'lg' })}
          ${buttonTwig({ label: 'Advanced Filters', variant: 'neutral', icon: 'filter', outline: true })}
          ${buttonTwig({ icon: 'map-marker', variant: 'secondary', outline: true, label: 'Location' })}
        </div>
      </div>

      <!-- Property Actions -->
      <div>
        <h3 style="margin: 0 0 var(--size-4) 0; font-size: var(--size-4); font-weight: var(--font-weight-bold); color: var(--gray-900);">Property Card Actions</h3>
        <div style="display: flex; gap: var(--size-3); flex-wrap: wrap;">
          ${buttonTwig({ label: 'View Details', variant: 'primary', icon: 'arrow-right', iconPosition: 'end' })}
          ${buttonTwig({ label: 'Contact Agent', variant: 'secondary', icon: 'envelope' })}
          ${buttonTwig({ icon: 'heart', variant: 'neutral', outline: true, toggle: true, label: 'Save to favorites' })}
          ${buttonTwig({ icon: 'share', variant: 'neutral', outline: true, label: 'Share property' })}
        </div>
      </div>

      <!-- Form Actions -->
      <div>
        <h3 style="margin: 0 0 var(--size-4) 0; font-size: var(--size-4); font-weight: var(--font-weight-bold); color: var(--gray-900);">Contact Form</h3>
        <div style="display: flex; gap: var(--size-3); flex-wrap: wrap;">
          ${buttonTwig({ label: 'Send Message', variant: 'primary', icon: 'send', iconPosition: 'end', size: 'lg' })}
          ${buttonTwig({ label: 'Cancel', variant: 'neutral', outline: true })}
        </div>
      </div>

      <!-- Investment Actions -->
      <div>
        <h3 style="margin: 0 0 var(--size-4) 0; font-size: var(--size-4); font-weight: var(--font-weight-bold); color: var(--gray-900);">Investment Opportunities</h3>
        <div style="display: flex; gap: var(--size-3); flex-wrap: wrap;">
          ${buttonTwig({ label: 'Download Prospectus', variant: 'gold', icon: 'download' })}
          ${buttonTwig({ label: 'Schedule Visit', variant: 'primary', icon: 'calendar' })}
          ${buttonTwig({ label: 'Request Info', variant: 'secondary', outline: true })}
        </div>
      </div>

      <!-- Alert Actions -->
      <div>
        <h3 style="margin: 0 0 var(--size-4) 0; font-size: var(--size-4); font-weight: var(--font-weight-bold); color: var(--gray-900);">Alerts & Confirmations</h3>
        <div style="display: flex; gap: var(--size-3); flex-wrap: wrap;">
          ${buttonTwig({ label: 'Confirm Booking', variant: 'success', icon: 'check' })}
          ${buttonTwig({ label: 'Delete Property', variant: 'danger', icon: 'trash' })}
          ${buttonTwig({ label: 'Save Draft', variant: 'info', icon: 'save' })}
          ${buttonTwig({ label: 'Review Required', variant: 'warning' })}
        </div>
      </div>

      <!-- Full Width -->
      <div style="max-width: 400px;">
        <h3 style="margin: 0 0 var(--size-4) 0; font-size: var(--size-4); font-weight: var(--font-weight-bold); color: var(--gray-900);">Full Width (Mobile)</h3>
        ${buttonTwig({ label: 'Submit Application', variant: 'primary', icon: 'arrow-right', iconPosition: 'end', fullWidth: true, size: 'lg' })}
      </div>
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story:
          'Real-world button combinations for BNP Paribas Real Estate: property search, contact forms, investment actions, alerts, and mobile layouts.',
      },
    },
  },
};
