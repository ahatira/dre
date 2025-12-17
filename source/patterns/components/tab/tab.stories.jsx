/**
 * Tab (Molecule)
 *
 * Individual tab button for use within Tabs organism.
 * Visual presentation only - interactive logic managed by Tabs parent.
 */

import tabTemplate from './tab.twig';
import data from './tab.yml';

export default {
  title: 'Components/Tab',
  tags: ['autodocs'],
  parameters: {
    layout: 'centered',
    docs: {
      description: {
        component:
          'Autonomous tab button component. Designed for composition within Tabs organism. Supports semantic color variants and pill modifier.',
      },
    },
  },
  render: (args) => tabTemplate(args),
  argTypes: {
    label: {
      control: 'text',
      description: 'Tab label text (required).',
      table: {
        category: 'Content',
        type: { summary: 'string' },
      },
    },
    id: {
      control: 'text',
      description: 'Unique tab identifier.',
      table: {
        category: 'Accessibility',
        type: { summary: 'string' },
      },
    },
    panel_id: {
      control: 'text',
      description: 'Associated panel ID (aria-controls).',
      table: {
        category: 'Accessibility',
        type: { summary: 'string' },
      },
    },
    variant: {
      control: 'select',
      options: [
        'neutral',
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
      description: 'Semantic color variant.',
      table: {
        category: 'Appearance',
        type: { summary: 'string' },
        defaultValue: { summary: 'neutral' },
      },
    },
    pill: {
      control: 'boolean',
      description: 'Toggle pill presentation style (rounded background).',
      table: {
        category: 'Appearance',
        type: { summary: 'boolean' },
        defaultValue: { summary: 'false' },
      },
    },
    active: {
      control: 'boolean',
      description: 'Mark tab as selected/active.',
      table: {
        category: 'State',
        type: { summary: 'boolean' },
        defaultValue: { summary: 'false' },
      },
    },
    disabled: {
      control: 'boolean',
      description: 'Disable tab interaction.',
      table: {
        category: 'State',
        type: { summary: 'boolean' },
        defaultValue: { summary: 'false' },
      },
    },
    icon: {
      control: 'select',
      options: [
        null,
        'accessibility',
        'account',
        'air-conditioning',
        'alert',
        'check',
        'info',
        'arrow-right',
        'calendar',
        'chart',
        'energy',
        'file',
        'map',
        'photo',
        'settings',
      ],
      description: 'Icon name displayed before label.',
      table: {
        category: 'Content',
        type: { summary: 'string' },
      },
    },
  },
};

/**
 * Default tab (active primary)
 */
export const Default = {
  args: data,
};

/**
 * All semantic color variants (active)
 */
export const AllVariants = {
  render: () => {
    const variants = [
      'neutral',
      'primary',
      'secondary',
      'success',
      'danger',
      'warning',
      'info',
      'gold',
      'light',
      'dark',
    ];
    return `
      <div style="display: flex; flex-wrap: wrap; gap: 1rem;">
        ${variants
          .map(
            (variant) => `
          ${tabTemplate({
            label: variant.charAt(0).toUpperCase() + variant.slice(1),
            variant: variant,
            active: true,
          })}
        `
          )
          .join('')}
      </div>
    `;
  },
};

/**
 * Pill modifier (all variants)
 */
export const PillVariants = {
  render: () => {
    const variants = [
      'neutral',
      'primary',
      'secondary',
      'success',
      'danger',
      'warning',
      'info',
      'gold',
      'light',
      'dark',
    ];
    return `
      <div style="display: flex; flex-wrap: wrap; gap: 1rem; padding: 1rem; background: var(--gray-50);">
        ${variants
          .map(
            (variant) => `
          ${tabTemplate({
            label: variant.charAt(0).toUpperCase() + variant.slice(1),
            variant: variant,
            pill: true,
            active: true,
          })}
        `
          )
          .join('')}
      </div>
    `;
  },
};

/**
 * With icons (active + inactive)
 */
export const WithIcons = {
  render: () => {
    return `
      <div style="display: flex; gap: 1rem;">
        ${tabTemplate({
          label: 'Description',
          icon: 'file',
          variant: 'primary',
          active: true,
        })}
        ${tabTemplate({
          label: 'Photos',
          icon: 'photo',
          variant: 'primary',
          active: false,
        })}
        ${tabTemplate({
          label: 'Energy',
          icon: 'energy',
          variant: 'primary',
          active: false,
        })}
      </div>
    `;
  },
};

/**
 * State variations (active, inactive, disabled)
 */
export const States = {
  render: () => {
    return `
      <div style="display: flex; gap: 1rem;">
        ${tabTemplate({
          label: 'Active',
          variant: 'primary',
          active: true,
        })}
        ${tabTemplate({
          label: 'Inactive',
          variant: 'primary',
          active: false,
        })}
        ${tabTemplate({
          label: 'Disabled',
          variant: 'primary',
          disabled: true,
        })}
      </div>
    `;
  },
};

/**
 * Pill state variations
 */
export const PillStates = {
  render: () => {
    return `
      <div style="display: flex; gap: 1rem; padding: 1rem; background: var(--gray-50);">
        ${tabTemplate({
          label: 'Active',
          variant: 'primary',
          pill: true,
          active: true,
        })}
        ${tabTemplate({
          label: 'Inactive',
          variant: 'primary',
          pill: true,
          active: false,
        })}
        ${tabTemplate({
          label: 'Disabled',
          variant: 'primary',
          pill: true,
          disabled: true,
        })}
      </div>
    `;
  },
};
