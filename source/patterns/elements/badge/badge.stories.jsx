import component from './badge.twig';
import data from './badge.yml';
import './badge.css';
import iconsRegistry from '../../documentation/icons-registry.json';

export default {
  title: 'Elements/Badge',
  render: (args) => component(args),
  args: data,
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component:
          'Compact badge for statuses and metadata with semantic colors. Uses relative em units to scale with parent font-size. Fully token-based with bold text and saturated backgrounds for high visibility.',
      },
    },
  },
  argTypes: {
    // Content
    text: {
      control: 'text',
      description: 'Badge text (short: 1–2 words).',
      table: {
        category: 'Content',
        type: { summary: 'string', required: true },
        defaultValue: { summary: data.text || '""' },
      },
    },
    icon: {
      control: 'select',
      options: [null, ...iconsRegistry.names],
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
      options: [
        'primary',
        'secondary',
        'success',
        'danger',
        'warning',
        'info',
        'light',
        'dark',
        'gold',
      ],
      description: 'Semantic color variant with saturated backgrounds.',
      table: {
        category: 'Appearance',
        type: {
          summary: 'primary | secondary | success | danger | warning | info | light | dark | gold',
        },
        defaultValue: { summary: 'primary' },
      },
    },
    size: {
      control: { type: 'inline-radio' },
      options: ['small', 'medium', 'large'],
      description: 'Badge size using relative em units (scales with parent font-size).',
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
      description: 'Optional link URL (renders <a> with focus outline).',
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

// === Core Variants ===

// Background colors - All semantic color variants
export const BackgroundColors = {
  render: () => `
    <div style="display: flex; gap: var(--size-3); flex-wrap: wrap; align-items: center;">
      ${component({ color: 'primary', text: 'Primary' })}
      ${component({ color: 'secondary', text: 'Secondary' })}
      ${component({ color: 'success', text: 'Success' })}
      ${component({ color: 'danger', text: 'Danger' })}
      ${component({ color: 'warning', text: 'Warning' })}
      ${component({ color: 'info', text: 'Info' })}
      ${component({ color: 'light', text: 'Light' })}
      ${component({ color: 'dark', text: 'Dark' })}
      ${component({ color: 'gold', text: 'Gold' })}
    </div>
  `,
};

// Pill badges - Rounded pill shape
export const PillBadges = {
  render: () => `
    <div style="display: flex; gap: var(--size-3); flex-wrap: wrap; align-items: center;">
      ${component({ color: 'primary', text: 'Primary', pill: true })}
      ${component({ color: 'secondary', text: 'Secondary', pill: true })}
      ${component({ color: 'success', text: 'Success', pill: true })}
      ${component({ color: 'danger', text: 'Danger', pill: true })}
      ${component({ color: 'warning', text: 'Warning', pill: true })}
      ${component({ color: 'info', text: 'Info', pill: true })}
      ${component({ color: 'light', text: 'Light', pill: true })}
      ${component({ color: 'dark', text: 'Dark', pill: true })}
      ${component({ color: 'gold', text: 'Gold', pill: true })}
    </div>
  `,
};

// === Integration Examples ===

// Headings - Badges scale to match parent font size
export const InHeadings = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-4);">
      <h1 style="margin: 0; font-size: var(--font-size-7);">Example heading ${component({ text: 'New' })}</h1>
      <h2 style="margin: 0; font-size: var(--font-size-6);">Example heading ${component({ text: 'New' })}</h2>
      <h3 style="margin: 0; font-size: var(--font-size-5);">Example heading ${component({ text: 'New' })}</h3>
      <h4 style="margin: 0; font-size: var(--font-size-4);">Example heading ${component({ text: 'New' })}</h4>
      <h5 style="margin: 0; font-size: var(--font-size-3);">Example heading ${component({ text: 'New' })}</h5>
      <h6 style="margin: 0; font-size: var(--font-size-2);">Example heading ${component({ text: 'New' })}</h6>
    </div>
  `,
};

// Buttons - Badges as part of buttons to provide a counter
export const InButtons = {
  render: () => `
    <div style="display: flex; gap: var(--size-4); flex-wrap: wrap; align-items: center;">
      <button type="button" style="padding: var(--size-2) var(--size-4); background: var(--primary); color: var(--white); border: none; border-radius: var(--radius-1); font-size: var(--font-size-1); font-weight: 500; cursor: pointer;">
        Notifications ${component({ text: '4', color: 'secondary' })}
      </button>
      <button type="button" style="padding: var(--size-2) var(--size-4); background: var(--info); color: var(--white); border: none; border-radius: var(--radius-1); font-size: var(--font-size-1); font-weight: 500; cursor: pointer;">
        Messages ${component({ text: '9', color: 'light' })}
      </button>
      <button type="button" style="padding: var(--size-2) var(--size-4); background: var(--success); color: var(--white); border: none; border-radius: var(--radius-1); font-size: var(--font-size-1); font-weight: 500; cursor: pointer;">
        Updates ${component({ text: '12', color: 'danger' })}
      </button>
    </div>
  `,
};

// With icons
export const WithIcons = {
  render: () => `
    <div style="display: flex; gap: var(--size-4); flex-wrap: wrap; align-items: center;">
      ${component({ color: 'success', text: 'Verified', icon: 'check' })}
      ${component({ color: 'info', text: 'Info', icon: 'info' })}
      ${component({ color: 'warning', text: 'Warning', icon: 'alert', pill: true })}
      ${component({ color: 'gold', text: 'Premium', icon: 'award', pill: true })}
    </div>
  `,
};

// As links
export const AsLinks = {
  render: () => `
    <div style="display: flex; gap: var(--size-4); flex-wrap: wrap; align-items: center;">
      ${component({ color: 'primary', text: 'Link badge', url: '#' })}
      ${component({ color: 'info', text: 'Learn more', icon: 'info', url: '#', pill: true })}
      ${component({ color: 'secondary', text: 'Discover', url: '#' })}
    </div>
  `,
};
