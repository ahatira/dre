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
        component:
          'User or entity avatar with automatic fallbacks: image, initials, or generic icon. Supports shapes, sizes, status badge, optional border, and clickable variant using tokens.',
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
      ...sizes.map((s) => `<div class="cell cell--label">${s.toUpperCase()}</div>`),
    ].join('');
    const rows = shapes
      .map((shape) => {
        const label = `<div class=\"cell cell--label\">${shape}</div>`;
        const cells = sizes
          .map(
            (s) =>
              `<div class=\"cell\">${avatarTwig({ src: '', initials: 'JD', size: s, shape, alt: `Initials ${shape} ${s}` })}</div>`
          )
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
      ${['xs', 'sm', 'md', 'lg', 'xl'].map((s) => avatarTwig({ src: 'https://i.pravatar.cc/150?img=12', size: s, alt: s.toUpperCase() })).join('')}
    </div>
  `,
};

export const AllShapes = {
  render: () => `
    <div style="display:flex;gap:1rem;align-items:center;">
      ${['circle', 'square', 'rounded'].map((shape) => avatarTwig({ src: 'https://i.pravatar.cc/150?img=47', shape, size: 'lg', alt: shape })).join('')}
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
      ${['xs', 'sm', 'md', 'lg', 'xl'].map((s) => avatarTwig({ src: 'https://i.pravatar.cc/150?img=18', shape: 'rounded', size: s, alt: `Rounded ${s}` })).join('')}
    </div>
  `,
};
