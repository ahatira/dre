import avatarTwig from './avatar.twig';
import data from './avatar.yml';

export default {
  title: 'Elements/Avatar',
  tags: ['autodocs'],
  render: (args) => avatarTwig(args),
  args: data,
  parameters: {
    docs: {
      description: {
        component: `User or entity visual representation with automatic fallback modes.

**Key Features:**
- 3 display modes: image (src) → initials fallback → gender icon fallback
- 5 sizes: xs (24px), sm (32px), md (40px, default), lg (48px), xl (80px)
- 3 shapes: circle (default), square, rounded (adaptive radius per size)
- Status badge: online (green), offline (gray), busy (red) at bottom-right corner
- Optional white border for dark backgrounds
- Clickable variant with hover scale and focus outline

**Usage Guidelines:**
- Always provide alt text when image is present
- Use initials (2 chars max) as first fallback before icon
- Prefer circle for profiles; rounded for team/group contexts
- Status badge only for real-time presence indicators
- Size xs for buttons/lists, sm for comments, md for headers, lg/xl for profiles
- Border only on dark/image backgrounds where contrast is low

**Accessibility:**
- alt attribute required when src provided (screen reader label)
- Initials displayed as readable text (no aria-label needed)
- Icon fallback marked aria-hidden (decorative only)
- Status badge includes descriptive aria-label ("Online", "Busy", "Offline")
- Focus outline only when clickable=true (keyboard navigation)
- Contrast verified for all color variants (WCAG AA)

**Design Tokens:**
- Sizing: --size-6 (xs), --size-8 (sm), --size-10 (md), --size-12 (lg), --size-20 (xl)
- Typography: --font-size-xs/sm/0/2/4 --font-weight-600
- Colors: --brand-primary (initials bg), --gray-* (backgrounds), --green/red-600 (status)
- Radius: --radius-2/3/4/5/6 (adaptive rounded scaling)
- Border: --border-size-1/2

**Do Not:**
- Use avatar as sole identifier (pair with name text)
- Omit alt when image present
- Rely on status color alone (include text context)
- Hardcode dimensions or colors`,
      },
    },
  },
  argTypes: {
    // Content
    src: {
      control: 'text',
      description: 'Avatar image URL',
      table: {
        category: 'Content',
        type: { summary: 'string' },
      },
    },
    initials: {
      control: 'text',
      description: 'Initials (2 letters max, fallback if no image)',
      table: {
        category: 'Content',
        type: { summary: 'string' },
      },
    },
    // Appearance
    size: {
      control: 'select',
      options: ['xs', 'sm', 'md', 'lg', 'xl'],
      description: 'Avatar size (xs: 24px, sm: 32px, md: 40px, lg: 48px, xl: 80px)',
      table: {
        category: 'Appearance',
        type: { summary: 'xs | sm | md | lg | xl' },
        defaultValue: { summary: 'md' },
      },
    },
    shape: {
      control: 'select',
      options: ['circle', 'square', 'rounded'],
      description: 'Avatar shape (rounded has adaptive radius per size)',
      table: {
        category: 'Appearance',
        type: { summary: 'circle | square | rounded' },
        defaultValue: { summary: 'circle' },
      },
    },
    status: {
      control: 'select',
      options: ['', 'online', 'offline', 'busy'],
      description: 'Status badge (leave empty for none)',
      table: {
        category: 'Appearance',
        type: { summary: 'online | offline | busy' },
      },
    },
    bordered: {
      control: 'boolean',
      description: 'Add white border (for dark backgrounds)',
      table: {
        category: 'Appearance',
        type: { summary: 'boolean' },
        defaultValue: { summary: false },
      },
    },
    gender: {
      control: 'select',
      options: ['male', 'female'],
      description: 'Fallback icon gender (when image missing and no initials)',
      table: {
        category: 'Appearance',
        type: { summary: 'male | female' },
        defaultValue: { summary: 'male' },
      },
    },
    // Behavior
    clickable: {
      control: 'boolean',
      description: 'Enable hover + focus visible',
      table: {
        category: 'Behavior',
        type: { summary: 'boolean' },
        defaultValue: { summary: false },
      },
    },
    // Link
    href: {
      control: 'text',
      description: 'URL (transforms to <a> link)',
      table: {
        category: 'Link',
        type: { summary: 'string' },
      },
    },
    // Accessibility
    alt: {
      control: 'text',
      description: 'Alternative text (required if image)',
      table: {
        category: 'Accessibility',
        type: { summary: 'string' },
      },
    },
  },
};

export const Default = { args: { ...data } };

export const Initials = {
  render: () => {
    const sizes = ['xs', 'sm', 'md', 'lg', 'xl'];
    const shapes = ['circle', 'square', 'rounded'];
    const gridCols = sizes.length + 1;
    const headRow = [
      '<div class="cell cell--label"></div>',
      ...sizes.map(s => `<div class="cell cell--label">${s.toUpperCase()}</div>`),
    ].join('');
    const rows = shapes
      .map(shape => {
        const label = `<div class=\"cell cell--label\">${shape}</div>`;
        const cells = sizes
          .map(s => `<div class=\"cell\">${avatarTwig({ src: '', initials: 'JD', size: s, shape, alt: `Initials ${shape} ${s}` })}</div>`)
          .join('');
        return label + cells;
      })
      .join('');

    return `
      <div style="display:grid;grid-template-columns:repeat(${gridCols}, auto);gap:12px;align-items:center;">
        <style>
          .cell--label{font:600 var(--font-size-0)/1 var(--font-sans);color:var(--ps-color-neutral-600);text-transform:capitalize}
          .cell{display:flex;align-items:center;justify-content:center}
        </style>
        ${headRow}
        ${rows}
      </div>
    `;
  },
};

export const FallbackIcon = {
  args: { ...data, src: '', initials: '', size: 'xs', gender: 'female' },
};

export const StatusVariants = {
  render: () => `
    <div style="display:flex;gap:1rem;align-items:center;">
      ${avatarTwig({ src: 'https://i.pravatar.cc/150?img=12', status: 'online', size: 'lg', alt: 'Online' })}
      ${avatarTwig({ src: 'https://i.pravatar.cc/150?img=12', status: 'offline', size: 'lg', alt: 'Offline' })}
      ${avatarTwig({ src: 'https://i.pravatar.cc/150?img=12', status: 'busy', size: 'lg', alt: 'Busy' })}
    </div>
  `,
};

export const AllSizes = {
  render: () => `
    <div style="display:flex;gap:1rem;align-items:center;">
      ${['xs', 'sm', 'md', 'lg', 'xl'].map(s => avatarTwig({ src: 'https://i.pravatar.cc/150?img=12', size: s, alt: s.toUpperCase() })).join('')}
    </div>
  `,
};

export const AllShapes = {
  render: () => `
    <div style="display:flex;gap:1rem;align-items:center;">
      ${['circle','square','rounded'].map(shape => avatarTwig({ src: 'https://i.pravatar.cc/150?img=47', shape, size: 'lg', alt: shape })).join('')}
    </div>
  `,
};

export const Modes = {
  render: () => `
    <div style="display:flex;gap:1rem;align-items:center;">
      ${avatarTwig({ src: 'https://i.pravatar.cc/150?img=47', size: 'lg', alt: 'Image' })}
      ${avatarTwig({ initials: 'JD', src: '', size: 'lg', alt: 'Initials' })}
      ${avatarTwig({ src: '', initials: '', size: 'lg', alt: 'Icon Fallback' })}
    </div>
  `,
};

export const RoundedScaling = {
  render: () => `
    <div style="display:flex;gap:1rem;align-items:center;">
      ${['xs', 'sm', 'md', 'lg', 'xl'].map(s => avatarTwig({ src: 'https://i.pravatar.cc/150?img=18', shape: 'rounded', size: s, alt: `Rounded ${s}` })).join('')}
    </div>
  `,
};
