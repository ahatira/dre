import badgeTwig from './badge.twig';
import data from './badge.yml';
import './badge.css';
import iconsList from '../../documentation/icons-list.json';

export default {
  title: 'Elements/Badge',
  render: (args) => badgeTwig(args),
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
    text: {
      control: 'text',
      description: 'Badge text label (short, 1–2 words).',
      table: {
        category: 'Content',
        type: { summary: 'string', required: true },
        defaultValue: { summary: data.text },
      },
    },
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
      description: 'Semantic color variant.',
      table: {
        category: 'Appearance',
        type: {
          summary: 'primary | secondary | success | danger | warning | info | light | dark | gold',
        },
        defaultValue: { summary: data.color },
      },
    },
    pill: {
      control: 'boolean',
      description: 'Fully rounded pill shape.',
      table: {
        category: 'Appearance',
        type: { summary: 'boolean' },
        defaultValue: { summary: data.pill },
      },
    },
    icon: {
      control: 'select',
      options: [null, ...iconsList.all],
      description: 'Icon name (without icon- prefix).',
      table: {
        category: 'Content',
        type: { summary: 'string' },
        defaultValue: { summary: data.icon || '""' },
      },
    },
    iconPosition: {
      control: { type: 'inline-radio' },
      options: ['start', 'end'],
      description: 'Icon position (start or end).',
      table: {
        category: 'Appearance',
        type: { summary: 'start | end' },
        defaultValue: { summary: data.iconPosition },
      },
    },
    url: {
      control: 'text',
      description: 'Link URL (renders as <a> tag).',
      table: {
        category: 'Link',
        type: { summary: 'string' },
        defaultValue: { summary: data.url || '""' },
      },
    },
  },
};

// Main interactive playground
export const ParDéfaut = {
  name: 'Default',
  args: { ...data },
};

// All color variants
export const Couleurs = {
  name: 'Colors',
  render: () => `
    <div style="display: flex; gap: var(--size-3); flex-wrap: wrap; align-items: center;">
      ${badgeTwig({ color: 'primary', text: 'Primary' })}
      ${badgeTwig({ color: 'secondary', text: 'Secondary' })}
      ${badgeTwig({ color: 'success', text: 'Success' })}
      ${badgeTwig({ color: 'danger', text: 'Danger' })}
      ${badgeTwig({ color: 'warning', text: 'Warning' })}
      ${badgeTwig({ color: 'info', text: 'Info' })}
      ${badgeTwig({ color: 'light', text: 'Light' })}
      ${badgeTwig({ color: 'dark', text: 'Dark' })}
      ${badgeTwig({ color: 'gold', text: 'Gold' })}
    </div>
  `,
};

// Pill shape variant
export const FormePill = {
  name: 'Pill Shape',
  render: () => `
    <div style="display: flex; gap: var(--size-3); flex-wrap: wrap; align-items: center;">
      ${badgeTwig({ color: 'primary', text: 'Primary', pill: true })}
      ${badgeTwig({ color: 'secondary', text: 'Secondary', pill: true })}
      ${badgeTwig({ color: 'success', text: 'Success', pill: true })}
      ${badgeTwig({ color: 'danger', text: 'Danger', pill: true })}
      ${badgeTwig({ color: 'warning', text: 'Warning', pill: true })}
      ${badgeTwig({ color: 'info', text: 'Info', pill: true })}
      ${badgeTwig({ color: 'light', text: 'Light', pill: true })}
      ${badgeTwig({ color: 'dark', text: 'Dark', pill: true })}
      ${badgeTwig({ color: 'gold', text: 'Gold', pill: true })}
    </div>
  `,
};

// With icons
export const AvecIcone = {
  name: 'With Icons',
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-4);">
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); font-weight: 600; color: var(--gray-700);">Icon start (default)</p>
        <div style="display: flex; gap: var(--size-3); flex-wrap: wrap;">
          ${badgeTwig({ color: 'success', text: 'Available', icon: 'check' })}
          ${badgeTwig({ color: 'danger', text: 'Sold', icon: 'check' })}
          ${badgeTwig({ color: 'info', text: 'Alert', icon: 'alert' })}
          ${badgeTwig({ color: 'warning', text: 'Pending', icon: 'clock' })}
        </div>
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); font-weight: 600; color: var(--gray-700);">Icon start + Pill</p>
        <div style="display: flex; gap: var(--size-3); flex-wrap: wrap;">
          ${badgeTwig({ color: 'primary', text: 'New', icon: 'plus', pill: true })}
          ${badgeTwig({ color: 'secondary', text: 'Special', icon: 'star', pill: true })}
          ${badgeTwig({ color: 'gold', text: 'Premium', icon: 'award', pill: true })}
        </div>
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); font-weight: 600; color: var(--gray-700);">Icon end</p>
        <div style="display: flex; gap: var(--size-3); flex-wrap: wrap;">
          ${badgeTwig({ color: 'info', text: 'Learn more', icon: 'arrow-right', iconPosition: 'end' })}
          ${badgeTwig({ color: 'primary', text: 'Link', icon: 'external-link', iconPosition: 'end' })}
        </div>
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); font-weight: 600; color: var(--gray-700);">Icon end + Pill</p>
        <div style="display: flex; gap: var(--size-3); flex-wrap: wrap;">
          ${badgeTwig({ color: 'secondary', text: 'Visit', icon: 'arrow-right', iconPosition: 'end', pill: true })}
          ${badgeTwig({ color: 'info', text: 'Discover', icon: 'external-link', iconPosition: 'end', pill: true })}
        </div>
      </div>
    </div>
  `,
};

// Real-world context example
export const EnContexte = {
  name: 'In Context',
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-4);">
      <div>
        <h3 style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-3);">Paris Apartment ${badgeTwig({ color: 'success', text: 'Available', pill: true })}</h3>
        <p style="margin: 0; color: var(--gray-600);">125 m² • 2 bedrooms</p>
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-3);">Neuilly House ${badgeTwig({ color: 'danger', text: 'Sold' })} ${badgeTwig({ color: 'gold', text: 'Premium' })}</h3>
        <p style="margin: 0; color: var(--gray-600);">280 m² • 5 bedrooms</p>
      </div>
    </div>
  `,
};
