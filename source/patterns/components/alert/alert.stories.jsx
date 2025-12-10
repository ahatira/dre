import alertTwig from './alert.twig';
import data from './alert.yml';

export default {
  title: 'Components/Alert',
  tags: ['autodocs'],
  render: (args) => alertTwig(args),
  args: data,

  parameters: {
    docs: {
      description: {
        component:
          'Semantic alert component with 10 color variants for displaying important feedback messages.\n\n' +
          'Supports free HTML content, optional dismissal, and rounded corners. Automatically applies appropriate ARIA roles and live regions.',
      },
    },
  },

  argTypes: {
    variant: {
      description: 'Semantic variant defining color scheme and intent',
      control: { type: 'select' },
      options: [
        'default',
        'primary',
        'secondary',
        'success',
        'danger',
        'warning',
        'info',
        'gold',
        'light',
        'dark',
      ],
      table: {
        category: 'Appearance',
        type: {
          summary:
            'default | primary | secondary | success | danger | warning | info | gold | light | dark',
        },
        defaultValue: { summary: 'default' },
      },
    },

    content: {
      description: 'HTML content for alert body (headings, paragraphs, links)',
      control: { type: 'text' },
      table: {
        category: 'Content',
        type: { summary: 'string (HTML)' },
        defaultValue: { summary: '' },
      },
    },

    dismissible: {
      description: 'Show close button with dismiss behavior',
      control: { type: 'boolean' },
      table: {
        category: 'Behavior',
        type: { summary: 'boolean' },
        defaultValue: { summary: false },
      },
    },

    rounded: {
      description: 'Apply border radius',
      control: { type: 'boolean' },
      table: {
        category: 'Appearance',
        type: { summary: 'boolean' },
        defaultValue: { summary: false },
      },
    },
  },
};

// ============================================
// BASIC STORIES
// ============================================

export const Default = {
  name: 'Default (Neutral)',
  args: {
    variant: 'default',
    content: '<strong>Information:</strong> This is a neutral default alert.',
  },
};

// ============================================
// ALL VARIANTS SHOWCASE
// ============================================

export const AllVariants = {
  name: 'Variants',
  render: () => {
    const variants = [
      { variant: 'default', label: 'Default', content: 'Standard neutral message' },
      { variant: 'primary', label: 'Primary', content: 'Brand highlight (BNP green)' },
      { variant: 'secondary', label: 'Secondary', content: 'Secondary information (BNP pink)' },
      { variant: 'success', label: 'Success', content: 'Success confirmation' },
      { variant: 'danger', label: 'Danger', content: 'Error or critical action' },
      { variant: 'warning', label: 'Warning', content: 'Important warning' },
      { variant: 'info', label: 'Info', content: 'Contextual information' },
      { variant: 'gold', label: 'Gold', content: 'Premium or exclusive content' },
      { variant: 'light', label: 'Light', content: 'General non-critical announcement' },
      { variant: 'dark', label: 'Dark', content: 'High contrast notification' },
    ];

    return variants
      .map((v) =>
        alertTwig({
          variant: v.variant,
          content: `<strong>${v.label} :</strong> ${v.content}`,
        })
      )
      .join('<br style="margin-bottom: var(--size-4)">');
  },
};

// ============================================
// MODIFIERS
// ============================================

export const Dismissible = {
  name: 'Dismissible',
  render: () => {
    const variants = [
      { variant: 'default', label: 'Default' },
      { variant: 'primary', label: 'Primary' },
      { variant: 'secondary', label: 'Secondary' },
      { variant: 'success', label: 'Success' },
      { variant: 'danger', label: 'Danger' },
      { variant: 'warning', label: 'Warning' },
      { variant: 'info', label: 'Info' },
      { variant: 'gold', label: 'Gold' },
      { variant: 'light', label: 'Light' },
      { variant: 'dark', label: 'Dark' },
    ];

    return variants
      .map((v) =>
        alertTwig({
          variant: v.variant,
          dismissible: true,
          content: `<strong>${v.label}:</strong> This alert can be dismissed by user.`,
        })
      )
      .join('<br style="margin-bottom: var(--size-4)">');
  },
};

export const Rounded = {
  name: 'Rounded Corners',
  args: {
    variant: 'success',
    rounded: true,
    content: '<strong>Modern design:</strong> Alert with rounded corners.',
  },
};

export const RoundedDismissible = {
  name: 'Rounded + Dismissible',
  args: {
    variant: 'warning',
    rounded: true,
    dismissible: true,
    content: '<strong>Session expired:</strong> Please reconnect.',
  },
};
