import avatarTwig from './avatar.twig';
import data from './avatar.yml';

export default {
  title: 'Elements/Avatar',
  tags: ['autodocs'],
  render: (args) => avatarTwig(args),
  args: { ...data },
  argTypes: {
    src: {
      control: 'text',
      description: 'Avatar image URL. If omitted, falls back to initials or icon.',
      table: { category: 'Content', type: { summary: 'string' } },
    },
    alt: {
      control: 'text',
      description: 'Alternative text for the image (required when src is provided).',
      table: {
        category: 'Accessibility',
        type: { summary: 'string' },
        defaultValue: { summary: data.alt },
      },
    },
    initials: {
      control: 'text',
      description: 'Initials text (2 letters max). Fallback if no image.',
      table: { category: 'Content', type: { summary: 'string' } },
    },
    gender: {
      control: 'select',
      options: ['male', 'female'],
      description: 'Silhouette variant used as fallback when no image or initials are provided.',
      table: {
        category: 'Appearance',
        type: { summary: 'male | female' },
        defaultValue: { summary: data.gender },
      },
    },
    size: {
      control: 'select',
      options: ['small', 'medium', 'large'],
      description: 'Avatar size: small (48px) | medium (88px - default) | large (112px)',
      table: {
        category: 'Appearance',
        type: { summary: 'small | medium | large' },
        defaultValue: { summary: 'medium' },
      },
    },
    shape: {
      control: 'select',
      options: ['circle', 'square', 'rounded'],
      description: 'Avatar shape variant.',
      table: {
        category: 'Appearance',
        type: { summary: 'circle | square | rounded' },
        defaultValue: { summary: 'circle' },
      },
    },
    status: {
      control: 'select',
      options: ['', 'online', 'offline', 'busy'],
      description: 'Optional status badge indicator.',
      table: {
        category: 'Appearance',
        type: { summary: 'online | offline | busy' },
        defaultValue: { summary: data.status || 'none' },
      },
    },
    bordered: {
      control: 'boolean',
      description: 'Add white border around avatar for contrast on colored backgrounds.',
      table: {
        category: 'Appearance',
        type: { summary: 'boolean' },
        defaultValue: { summary: data.bordered },
      },
    },
    clickable: {
      control: 'boolean',
      description: 'Enable hover/focus interactive effect (auto-enabled when href is provided).',
      table: {
        category: 'Behavior',
        type: { summary: 'boolean' },
        defaultValue: { summary: data.clickable },
      },
    },
    href: {
      control: 'text',
      description: 'URL for turning the avatar into a link.',
      table: { category: 'Link', type: { summary: 'string' } },
    },
    attributes: {
      control: 'object',
      description:
        'Additional HTML attributes for Drupal integration (data attributes, ARIA, custom classes).',
      table: { category: 'Accessibility', type: { summary: 'object' } },
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
  args: { ...data },
};

export const Initials = {
  render: () => {
    const sizes = ['small', 'medium', 'large'];
    return `<div style="display:flex; gap: var(--size-6); align-items:center; flex-wrap:wrap;">
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
    const sizes = ['small', 'medium', 'large'];
    const genders = ['male', 'female'];
    return `<div style="display:flex; flex-direction:column; gap: var(--size-8);">
      ${genders
        .map(
          (gender) => `
        <div style="display:flex; gap: var(--size-6); align-items:center; flex-wrap:wrap;">
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
      { value: 'small', label: 'Small (48px)' },
      { value: 'medium', label: 'Medium (88px - default)' },
      { value: 'large', label: 'Large (112px)' },
    ];
    return `<div style="display:flex; gap: var(--size-8); align-items:center; flex-wrap:wrap;">
      ${sizes
        .map(
          ({ value, label }) => `
        <div style="text-align:center;">
          ${avatarTwig({
            src: '/source/assets/images/1-1.jpg',
            alt: data.alt,
            size: value,
          })}
          <div style="margin-top:var(--size-2); font-family:var(--font-sans); font-size:var(--font-size-0); color:var(--gray-700);">
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
    return `<div style="display:flex; gap: var(--size-8); align-items:center;">
      ${shapes
        .map(
          ({ value, label }) => `
        <div style="text-align:center;">
          ${avatarTwig({
            src: '/source/assets/images/1-1.jpg',
            alt: data.alt,
            shape: value,
          })}
          <div style="margin-top:var(--size-2); font-family:var(--font-sans); font-size:var(--font-size-0); color:var(--gray-700);">
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
    return `<div style="display:flex; gap: var(--size-8); align-items:center;">
      ${statuses
        .map(
          ({ value, label }) => `
        <div style="text-align:center;">
          ${avatarTwig({
            src: '/source/assets/images/1-1.jpg',
            alt: data.alt,
            status: value,
          })}
          <div style="margin-top:var(--size-2); font-family:var(--font-sans); font-size:var(--font-size-0); color:var(--gray-700);">
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
    const sizes = ['small', 'medium', 'large'];
    return `<div style="display:flex; gap: var(--size-6); align-items:center; padding:var(--size-6); background:var(--gray-100); border-radius:var(--radius-3);">
      ${sizes
        .map((size) =>
          avatarTwig({
            src: '/source/assets/images/1-1.jpg',
            alt: data.alt,
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
    return `<div style="display:flex; gap: var(--size-6); align-items:center;">
      ${avatarTwig({
        src: '/source/assets/images/1-1.jpg',
        alt: data.alt,
        clickable: true,
      })}
      ${avatarTwig({
        src: '/source/assets/images/1-1.jpg',
        alt: data.alt,
        clickable: true,
        href: '/agents/profile',
      })}
      ${avatarTwig({
        initials: 'JD',
        clickable: true,
        href: '/agents/john-doe',
      })}
    </div>
    <p style="margin-top:var(--size-4); font-family:var(--font-sans); font-size:var(--font-size-0); color:var(--gray-700);">
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
