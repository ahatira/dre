import avatarTwig from './avatar.twig';

export default {
  title: 'Components/Avatar',
  tags: ['autodocs'],
  render: (args) => avatarTwig(args),
  argTypes: {
    src: {
      control: 'text',
      description: 'Avatar image URL. If omitted, falls back to initials or icon.',
      table: { category: 'Content', type: { summary: 'string' } },
    },
    alt: {
      control: 'text',
      description: 'Alternative text for the image. Required when src is provided.',
      table: { category: 'Content', type: { summary: 'string' } },
    },
    initials: {
      control: 'text',
      description: 'Initials text (2 letters max). Fallback if no image.',
      table: { category: 'Content', type: { summary: 'string' } },
    },
    gender: {
      control: 'select',
      options: ['male', 'female'],
      description: 'Gender for icon fallback image (agent silhouette).',
      table: { category: 'Content', type: { summary: 'string' } },
    },
    size: {
      control: 'select',
      options: ['xs', 'sm', 'md', 'lg', 'xl'],
      description: 'Avatar size: xs (28px) | sm (48px) | md (68px) | lg (88px) | xl (112px)',
      table: {
        category: 'Appearance',
        type: { summary: 'string' },
        defaultValue: { summary: 'lg' },
      },
    },
    shape: {
      control: 'select',
      options: ['circle', 'square', 'rounded'],
      description: 'Avatar shape',
      table: {
        category: 'Appearance',
        type: { summary: 'string' },
        defaultValue: { summary: 'circle' },
      },
    },
    status: {
      control: 'select',
      options: ['', 'online', 'offline', 'busy'],
      description: 'Status badge indicator',
      table: { category: 'Appearance', type: { summary: 'string' } },
    },
    bordered: {
      control: 'boolean',
      description: 'Add white border around avatar',
      table: { category: 'Appearance', type: { summary: 'boolean' } },
    },
    clickable: {
      control: 'boolean',
      description: 'Enable hover/focus interactive effect',
      table: { category: 'Behavior', type: { summary: 'boolean' } },
    },
    href: {
      control: 'text',
      description: 'URL if avatar should be clickable link. Transforms element to <a>.',
      table: { category: 'Link', type: { summary: 'string' } },
    },
  },
  parameters: {
    docs: {
      description: {
        component:
          'User or agent visual representation for real estate. Fallback: image → initials → icon.',
      },
    },
  },
};

export const Default = {
  args: {
    src: 'https://loremflickr.com/300/300/building,office?random=1',
    alt: 'Real estate agent',
    initials: '',
  },
};

export const Initials = {
  render: () => {
    const sizes = ['xs', 'sm', 'md', 'lg', 'xl'];
    return `<div style="display:flex; gap: 24px; align-items:center; flex-wrap:wrap;">
      ${sizes
        .map((size) =>
          avatarTwig({
            src: '',
            alt: '',
            initials: 'AG',
            size,
          })
        )
        .join('')}
    </div>`;
  },
  parameters: {
    docs: { description: { story: 'Avatar with initials fallback across all sizes.' } },
  },
};

export const FallbackIcon = {
  render: () => {
    const sizes = ['xs', 'sm', 'md', 'lg', 'xl'];
    const genders = ['male', 'female'];
    return `<div style="display:flex; flex-direction:column; gap: 32px;">
      ${genders
        .map(
          (gender) => `
        <div style="display:flex; gap: 24px; align-items:center; flex-wrap:wrap;">
          ${sizes
            .map((size) =>
              avatarTwig({
                src: '',
                initials: '',
                gender,
                size,
              })
            )
            .join('')}
        </div>
      `
        )
        .join('')}
    </div>`;
  },
  parameters: {
    docs: {
      description: {
        story:
          'Icon fallback when neither image nor initials are provided. Shows male and female silhouettes across all sizes.',
      },
    },
  },
};

export const AllSizes = {
  render: () => {
    const sizes = [
      { value: 'xs', label: 'XS (28px)' },
      { value: 'sm', label: 'SM (48px)' },
      { value: 'md', label: 'MD (68px)' },
      { value: 'lg', label: 'LG (88px)' },
      { value: 'xl', label: 'XL (112px)' },
    ];
    return `<div style="display:flex; gap: 32px; align-items:center; flex-wrap:wrap;">
      ${sizes
        .map(
          ({ value, label }) => `
        <div style="text-align:center;">
          ${avatarTwig({
            src: 'https://loremflickr.com/300/300/building,office?random=2',
            alt: 'Agent',
            size: value,
          })}
          <div style="margin-top:8px; font-family:var(--font-sans); font-size:var(--font-size-0); color:var(--gray-700);">
            ${label}
          </div>
        </div>
      `
        )
        .join('')}
    </div>`;
  },
  parameters: {
    docs: { description: { story: 'All available avatar sizes with pixel dimensions.' } },
  },
};

export const AllShapes = {
  render: () => {
    const shapes = [
      { value: 'circle', label: 'Circle (default)' },
      { value: 'square', label: 'Square' },
      { value: 'rounded', label: 'Rounded' },
    ];
    return `<div style="display:flex; gap: 32px; align-items:center;">
      ${shapes
        .map(
          ({ value, label }) => `
        <div style="text-align:center;">
          ${avatarTwig({
            src: 'https://loremflickr.com/300/300/building,office?random=3',
            alt: 'Agent',
            shape: value,
          })}
          <div style="margin-top:8px; font-family:var(--font-sans); font-size:var(--font-size-0); color:var(--gray-700);">
            ${label}
          </div>
        </div>
      `
        )
        .join('')}
    </div>`;
  },
  parameters: {
    docs: { description: { story: 'All available shapes: circle, square, and rounded corners.' } },
  },
};

export const StatusVariants = {
  render: () => {
    const statuses = [
      { value: 'online', label: 'Online' },
      { value: 'offline', label: 'Offline' },
      { value: 'busy', label: 'Busy' },
    ];
    return `<div style="display:flex; gap: 32px; align-items:center;">
      ${statuses
        .map(
          ({ value, label }) => `
        <div style="text-align:center;">
          ${avatarTwig({
            src: 'https://loremflickr.com/300/300/building,office?random=4',
            alt: 'Agent',
            status: value,
          })}
          <div style="margin-top:8px; font-family:var(--font-sans); font-size:var(--font-size-0); color:var(--gray-700);">
            ${label}
          </div>
        </div>
      `
        )
        .join('')}
    </div>`;
  },
  parameters: {
    docs: {
      description: {
        story:
          'Status badge indicators: online (success), offline (gray), busy (danger). Badge scales with avatar size.',
      },
    },
  },
};

export const Bordered = {
  render: () => {
    const sizes = ['sm', 'md', 'lg', 'xl'];
    return `<div style="display:flex; gap: 24px; align-items:center; padding:24px; background:var(--gray-100); border-radius:var(--radius-3);">
      ${sizes
        .map((size) =>
          avatarTwig({
            src: 'https://loremflickr.com/300/300/building,office?random=5',
            alt: 'Agent',
            size,
            bordered: true,
          })
        )
        .join('')}
    </div>`;
  },
  parameters: {
    docs: {
      description: {
        story: 'White border around avatars. Useful for avatars displayed on colored backgrounds.',
      },
    },
  },
};

export const Clickable = {
  render: () => {
    return `<div style="display:flex; gap: 24px; align-items:center;">
      ${avatarTwig({
        src: 'https://loremflickr.com/300/300/building,office?random=6',
        alt: 'Agent',
        clickable: true,
      })}
      ${avatarTwig({
        src: 'https://loremflickr.com/300/300/building,office?random=7',
        alt: 'Agent',
        clickable: true,
        href: '/agents/profile',
      })}
      ${avatarTwig({
        initials: 'JD',
        clickable: true,
        href: '/agents/john-doe',
      })}
    </div>
    <p style="margin-top:16px; font-family:var(--font-sans); font-size:var(--font-size-0); color:var(--gray-700);">
      First avatar: clickable (div), Second & Third: links (a) with hover scale effect
    </p>`;
  },
  parameters: {
    docs: {
      description: {
        story:
          'Interactive avatars with hover scale effect and focus outline. Provide href to render as link.',
      },
    },
  },
};
