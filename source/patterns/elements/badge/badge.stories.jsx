import component from './badge.twig';
import data from './badge.yml';
import './badge.css';
import iconsList from '../../documentation/icons-list.json';

export default {
  title: 'Elements/Badge',
  render: (args) => component(args),
  args: data,
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component: `Compact semantic label / status indicator.

**Key Features:**
- 8 color variants: default, primary, secondary, gold (legacy accent), info, success, warning, danger
- 3 sizes: small | medium (default) | large
- Optional pill shape for fully rounded ends
- Optional decorative icon (never replaces text)
- Link support: render as <a> when url provided
- Pure token implementation (spacing, font, color, radius, transition)

**Usage Guidelines:**
- Keep text short (1–2 words) for scannability
- Prefer semantic color matching meaning (success for positive, warning for caution)
- Use pill shape for tags or emphasis, not for every badge
- Use small in dense metadata contexts; large for emphasis in hero/feature areas
- Avoid stacking too many variants together (visual noise)

**Accessibility:**
- Text always present (icon is decorative via data-icon mapping)
- Focus outline only on interactive link badges (url provided)
- Color palette chosen for sufficient contrast on light backgrounds
- Do not use badge as the only indicator of critical state (pair with text context)

**Design Tokens:**
- Spacing: --size-05 --size-1 --size-2 --size-3
- Typography: --font-size-xs --font-size-sm --font-size-0 --font-weight-500
- Colors: --gray-* --brand-primary --brand-secondary --accent-gold semantic (blue/green/yellow/red)
- Radius: --radius-2 --radius-round
- Transition: --ps-transition-duration-fast

**Do Not:**
- Hardcode hex/HSL values
- Rely on icon alone for meaning
- Combine multiple shape modifiers (only pill)
- Use badge for interactive actions beyond simple link navigation
`,
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
      options: ['default','primary','secondary','gold','info','success','warning','danger'],
      description: 'Semantic color variant (supports legacy gold accent).',
      table: {
        category: 'Appearance',
        type: { summary: 'default | primary | secondary | gold | info | success | warning | danger' },
        defaultValue: { summary: 'default' },
      },
    },
    size: {
      control: { type: 'inline-radio' },
      options: ['small','medium','large'],
      description: 'Badge size driven by typography & padding tokens.',
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
