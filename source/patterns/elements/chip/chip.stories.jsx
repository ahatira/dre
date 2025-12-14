// Import icon registry for icon dropdown
import iconsRegistry from '../../documentation/icons-registry.json';
import chipTwig from './chip.twig';
import chipData from './chip.yml';

const iconOptions = ['', ...iconsRegistry.map((icon) => icon.name)];

export default {
  title: 'Elements/Chip',
  tags: ['autodocs'],
  args: chipData,
  argTypes: {
    label: {
      description: 'Chip text content',
      control: 'text',
      table: { category: 'Content' },
    },
    variant: {
      description: 'Semantic color variant',
      control: 'select',
      options: [
        'neutral',
        'primary',
        'success',
        'danger',
        'warning',
        'info',
        'gold',
        'light',
        'dark',
      ],
      table: { category: 'Style' },
    },
    pill: {
      description: 'Fully rounded pill shape',
      control: 'boolean',
      table: { category: 'Style' },
    },
    icon: {
      description: 'Icon name (from sprite)',
      control: 'select',
      options: iconOptions,
      table: { category: 'Content' },
    },
    removable: {
      description: 'Show remove button',
      control: 'boolean',
      table: { category: 'Interaction' },
    },
  },
};

// Default story
export const Default = {
  name: 'Default (neutral)',
  render: (args) => chipTwig(args),
  args: chipData,
};

// All color variants
export const Colors = {
  name: 'Colors',
  render: () => `
    <div style="display: flex; gap: var(--size-3); flex-wrap: wrap; align-items: center;">
      ${chipTwig({ label: 'Neutral', variant: 'neutral' })}
      ${chipTwig({ label: 'Primary', variant: 'primary' })}
      ${chipTwig({ label: 'Success', variant: 'success' })}
      ${chipTwig({ label: 'Danger', variant: 'danger' })}
      ${chipTwig({ label: 'Warning', variant: 'warning' })}
      ${chipTwig({ label: 'Info', variant: 'info' })}
      ${chipTwig({ label: 'Light', variant: 'light' })}
      ${chipTwig({ label: 'Dark', variant: 'dark' })}
      ${chipTwig({ label: 'Gold', variant: 'gold' })}
    </div>
  `,
};

// Pill shape variant
export const PillShape = {
  name: 'Pill Shape',
  render: () => `
    <div style="display: flex; gap: var(--size-3); flex-wrap: wrap; align-items: center;">
      ${chipTwig({ label: 'Neutral', variant: 'neutral', pill: true })}
      ${chipTwig({ label: 'Primary', variant: 'primary', pill: true })}
      ${chipTwig({ label: 'Success', variant: 'success', pill: true })}
      ${chipTwig({ label: 'Danger', variant: 'danger', pill: true })}
      ${chipTwig({ label: 'Warning', variant: 'warning', pill: true })}
      ${chipTwig({ label: 'Info', variant: 'info', pill: true })}
      ${chipTwig({ label: 'Light', variant: 'light', pill: true })}
      ${chipTwig({ label: 'Dark', variant: 'dark', pill: true })}
      ${chipTwig({ label: 'Gold', variant: 'gold', pill: true })}
    </div>
  `,
};

// With icons
export const WithIcons = {
  name: 'With Icons',
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-4);">
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); font-weight: 600; color: var(--gray-700);">Default shape</p>
        <div style="display: flex; gap: var(--size-3); flex-wrap: wrap;">
          ${chipTwig({ label: 'Office', variant: 'primary', icon: 'building' })}
          ${chipTwig({ label: 'Retail', variant: 'info', icon: 'shopping-bag' })}
          ${chipTwig({ label: 'Verified', variant: 'success', icon: 'check' })}
          ${chipTwig({ label: 'Alert', variant: 'warning', icon: 'alert-triangle' })}
        </div>
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); font-weight: 600; color: var(--gray-700);">Pill shape</p>
        <div style="display: flex; gap: var(--size-3); flex-wrap: wrap;">
          ${chipTwig({ label: 'Premium', variant: 'gold', icon: 'award', pill: true })}
          ${chipTwig({ label: 'Featured', variant: 'primary', icon: 'star', pill: true })}
          ${chipTwig({ label: 'Location', variant: 'info', icon: 'map-pin', pill: true })}
        </div>
      </div>
    </div>
  `,
};

// Removable chips
export const Removable = {
  name: 'Removable',
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-4);">
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); font-weight: 600; color: var(--gray-700);">Active filters (removable)</p>
        <div style="display: flex; gap: var(--size-3); flex-wrap: wrap;">
          ${chipTwig({ label: 'Paris', variant: 'primary', removable: true })}
          ${chipTwig({ label: 'Office', variant: 'info', removable: true })}
          ${chipTwig({ label: '100-200m²', variant: 'success', removable: true })}
        </div>
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); font-weight: 600; color: var(--gray-700);">Removable + Pill</p>
        <div style="display: flex; gap: var(--size-3); flex-wrap: wrap;">
          ${chipTwig({ label: 'Parking', variant: 'neutral', icon: 'check', removable: true, pill: true })}
          ${chipTwig({ label: 'Terrace', variant: 'success', icon: 'check', removable: true, pill: true })}
          ${chipTwig({ label: 'Elevator', variant: 'info', icon: 'check', removable: true, pill: true })}
        </div>
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); font-weight: 600; color: var(--gray-700);">All variants removable</p>
        <div style="display: flex; gap: var(--size-3); flex-wrap: wrap;">
          ${chipTwig({ label: 'Neutral', variant: 'neutral', removable: true })}
          ${chipTwig({ label: 'Primary', variant: 'primary', removable: true })}
          ${chipTwig({ label: 'Success', variant: 'success', removable: true })}
          ${chipTwig({ label: 'Danger', variant: 'danger', removable: true })}
          ${chipTwig({ label: 'Warning', variant: 'warning', removable: true })}
          ${chipTwig({ label: 'Info', variant: 'info', removable: true })}
          ${chipTwig({ label: 'Light', variant: 'light', removable: true })}
          ${chipTwig({ label: 'Dark', variant: 'dark', removable: true })}
          ${chipTwig({ label: 'Gold', variant: 'gold', removable: true })}
        </div>
      </div>
    </div>
  `,
};
