import iconsList from '../../documentation/icons-list.json';
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
  args: { ...data, variant: 'neutral' },
};

export const AllVariants = {
  render: () => `
    <div style="display: flex; gap: var(--size-4); flex-wrap: wrap;">
      ${['neutral', 'primary', 'secondary', 'success', 'info', 'warning', 'danger'].map((variant) => buttonTwig({ label: variant.charAt(0).toUpperCase() + variant.slice(1), variant })).join('')}
    </div>
  `,
};

export const AllOutlines = {
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

export const AllSizes = {
  render: () => `
    <div style="display: flex; gap: var(--size-4); align-items: center;">
      ${['small', 'medium', 'large'].map((size) => buttonTwig({ label: size.charAt(0).toUpperCase() + size.slice(1), variant: 'primary', size })).join('')}
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
