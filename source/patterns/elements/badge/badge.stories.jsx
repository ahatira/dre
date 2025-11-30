import component from './badge.twig';
import data from './badge.yml';
import './badge.css';
import colorsList from '../../documentation/colors-list.json';
import sizesList from '../../documentation/sizes-list.json';
import iconsList from '../../documentation/icons-list.json';

export default {
  title: 'Elements/Badge',
  render: (args) => component(args),
  args: data,
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component:
          'Compact badge indicating a status or label.\n\n' +
          '- **Colors**: default (gray), primary (green), secondary (purple), gold, info, success, warning, danger — semantic token colors.\n' +
          '- **Sizes**: small, medium (default), large — spacing/typography driven by tokens.\n' +
          '- **Shape**: rounded base (default radius), `pill` option for fully rounded.\n' +
          '- **Icons**: via icon name (font `bnpre-icons`) without extra markup.\n' +
          '- **Links**: `url` renders an accessible <a> tag.\n' +
          '- **Accessibility**: always readable text (contrast defined by tokens); visible focus on links; implicit label role.\n' +
          '- **Design tokens**: spacing (--size-*), typography (--font-size-*, --font-weight-*), colors (--brand-primary, --brand-secondary, --btn-*), borders (--radius-*).\n' +
          '- **Minimal markup**: `.ps-badge` provides default styles; modifiers appear only when an option differs from default.',
      },
    },
  },
  argTypes: {
    // Content
    text: {
      control: 'text',
      description: 'Text content displayed in the badge.',
      table: {
        category: 'Content',
        type: { summary: 'string', required: true },
        defaultValue: { summary: 'Badge' },
      },
    },
    icon: {
      control: 'select',
      options: ['', ...iconsList.categories.generic],
      description: 'Optional icon name (without "icon-" prefix).',
      table: {
        category: 'Content',
        type: { summary: 'string' },
        defaultValue: { summary: '""' },
      },
    },

    // Appearance
    color: {
      control: { type: 'select' },
      options: colorsList.extended.values,
      description: 'Semantic color variant (default: gray, primary: green, secondary: purple, etc.).',
      table: {
        category: 'Appearance',
        type: { summary: 'default | primary | secondary | gold | info | success | warning | danger' },
        defaultValue: { summary: 'default' },
      },
    },
    size: {
      control: { type: 'inline-radio' },
      options: sizesList.compact.values,
      description: 'Badge size (small: 11px, medium: 12px, large: 14px).',
      table: {
        category: 'Appearance',
        type: { summary: 'small | medium | large' },
        defaultValue: { summary: 'medium' },
      },
    },
    pill: {
      control: 'boolean',
      description: 'Apply fully rounded pill shape.',
      table: {
        category: 'Appearance',
        type: { summary: 'boolean' },
        defaultValue: { summary: false },
      },
    },

    // Link
    url: {
      control: 'text',
      description: 'Optional link URL (renders <a> instead of <span>).',
      table: {
        category: 'Link',
        type: { summary: 'string' },
        defaultValue: { summary: '""' },
      },
    },
  },
};

// Default story
export const Default = {
  render: (args) => component(args),
  args: { ...data },
};

// === Showcase Stories ===

// All colors
export const AllColors = {
  render: () => `
    <div style="display: flex; gap: var(--size-4); flex-wrap: wrap; align-items: center;">
      ${component({ color: 'default', text: 'Default' })}
      ${component({ color: 'primary', text: 'Primary' })}
      ${component({ color: 'secondary', text: 'Secondary' })}
      ${component({ color: 'gold', text: 'Gold' })}
      ${component({ color: 'info', text: 'Info' })}
      ${component({ color: 'success', text: 'Success' })}
      ${component({ color: 'warning', text: 'Warning' })}
      ${component({ color: 'danger', text: 'Danger' })}
    </div>
  `,
};

// All sizes
export const AllSizes = {
  render: () => `
    <div style="display: flex; gap: var(--size-4); align-items: center;">
      ${component({ size: 'small', text: 'Small', color: 'primary' })}
      ${component({ size: 'medium', text: 'Medium', color: 'primary' })}
      ${component({ size: 'large', text: 'Large', color: 'primary' })}
    </div>
  `,
};

// All shapes (pill vs default)
export const AllShapes = {
  render: () => `
    <div style="display: flex; gap: var(--size-4); align-items: center;">
      ${component({ text: 'Rounded', color: 'primary', pill: false })}
      ${component({ text: 'Pill', color: 'primary', pill: true })}
    </div>
  `,
};

// With icons
export const WithIcons = {
  render: () => `
    <div style="display: flex; gap: var(--size-4); flex-wrap: wrap; align-items: center;">
      ${component({ color: 'info', text: 'Location', icon: 'pin-map' })}
      ${component({ color: 'success', text: 'Calendar', icon: 'calendar', pill: true })}
      ${component({ color: 'gold', text: 'Exclusive', icon: 'medal', pill: true })}
      ${component({ color: 'primary', text: 'Verified', icon: 'check' })}
    </div>
  `,
};

// As links
export const AsLinks = {
  render: () => `
    <div style="display: flex; gap: var(--size-4); flex-wrap: wrap; align-items: center;">
      ${component({ color: 'primary', text: 'Link badge', url: '#' })}
      ${component({ color: 'info', text: 'Learn more', icon: 'infos', url: '#', pill: true })}
      ${component({ color: 'secondary', text: 'Discover', url: '#' })}
    </div>
  `,
};

// Use cases
export const UseCases = {
  render: () => `
    <div style="display: grid; gap: var(--size-6); grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));">
      <div>
        <h4 style="margin: 0 0 var(--size-3); font-size: var(--font-size-1); font-weight: 500;">Status badges</h4>
        <div style="display: flex; gap: var(--size-3); flex-wrap: wrap;">
          ${component({ color: 'success', text: 'Available', icon: 'check' })}
          ${component({ color: 'warning', text: 'Pending', icon: 'help' })}
          ${component({ color: 'danger', text: 'Sold', icon: 'close' })}
        </div>
      </div>
      <div>
        <h4 style="margin: 0 0 var(--size-3); font-size: var(--font-size-1); font-weight: 500;">Property features</h4>
        <div style="display: flex; gap: var(--size-3); flex-wrap: wrap;">
          ${component({ color: 'gold', text: 'Exclusive', icon: 'medal', pill: true })}
          ${component({ color: 'info', text: 'New', pill: true })}
          ${component({ color: 'secondary', text: 'Premium', pill: true })}
        </div>
      </div>
      <div>
        <h4 style="margin: 0 0 var(--size-3); font-size: var(--font-size-1); font-weight: 500;">Interactive labels</h4>
        <div style="display: flex; gap: var(--size-3); flex-wrap: wrap;">
          ${component({ color: 'primary', text: 'View details', url: '#' })}
          ${component({ color: 'info', text: 'Learn more', icon: 'infos', url: '#', pill: true })}
        </div>
      </div>
    </div>
  `,
};
