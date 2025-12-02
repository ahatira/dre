import avatarTwig from './avatar.twig';
import avatarData from './avatar.yml';

/**
 * Storybook Definition - Avatar (Component/Molecule)
 */
export default {
  title: 'Components/Avatar',
  tags: ['autodocs'],
  render: (args) => avatarTwig(args),
  argTypes: {
    // Content
    src: {
      control: 'text',
      description: 'Avatar image URL. If omitted, falls back to initials or icon.',
      table: {
        category: 'Content',
        type: { summary: 'string' },
      },
    },
    alt: {
      control: 'text',
      description: 'Alternative text for the image. Required when src is provided.',
      table: {
        category: 'Content',
        type: { summary: 'string' },
      },
    },
    initials: {
      control: 'text',
      description: 'Initials text (2 letters max, e.g. "JD"). Fallback if no image.',
      table: {
        category: 'Content',
        type: { summary: 'string' },
      },
    },
    gender: {
      control: 'select',
      options: ['male', 'female'],
      description: 'Gender for icon fallback image (agent silhouette).',
      table: {
        category: 'Content',
        type: { summary: 'string' },
        defaultValue: { summary: 'male' },
      },
    },

    // Appearance
    size: {
      control: 'select',
      options: ['xs', 'sm', 'md', 'lg', 'xl'],
      description: 'Avatar size: xs (24px) | sm (32px) | md (40px) | lg (48px) | xl (80px)',
      table: {
        category: 'Appearance',
        type: { summary: 'string' },
        defaultValue: { summary: 'md' },
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
      table: {
        category: 'Appearance',
        type: { summary: 'string' },
      },
    },
    bordered: {
      control: 'boolean',
      description: 'Add white border around avatar',
      table: {
        category: 'Appearance',
        type: { summary: 'boolean' },
        defaultValue: { summary: false },
      },
    },

    // Behavior
    clickable: {
      control: 'boolean',
      description: 'Enable hover/focus interactive effect',
      table: {
        category: 'Behavior',
        type: { summary: 'boolean' },
        defaultValue: { summary: false },
      },
    },

    // Link
    href: {
      control: 'text',
      description: 'URL if avatar should be clickable link. Transforms element to <a>.',
      table: {
        category: 'Link',
        type: { summary: 'string' },
      },
    },
  },
  parameters: {
    docs: {
      description: {
        component:
          'User or entity visual representation with automatic fallback hierarchy: image → initials → icon.',
      },
    },
  },
};

/**
 * Default Story - Image Avatar
 */
export const Default = {
  args: {
    ...avatarData,
  },
};

/**
 * Initials Avatars - Complete Grid
 */
export const Initials = {
  render: () => {
    const sizes = ['xs', 'sm', 'md', 'lg', 'xl'];
    const shapes = ['circle', 'square', 'rounded'];

    const shapeRows = shapes
      .map((shape) => {
        const avatars = sizes
          .map((size) =>
            avatarTwig({
              initials: 'JD',
              size: size,
              shape: shape,
              src: '',
            })
          )
          .join('');

        return `<div>
        <h3 style="margin: 0 0 16px; font-family: var(--font-sans); font-size: var(--font-size-3); color: var(--gray-900);">
          Shape: ${shape}
        </h3>
        <div style="display: flex; gap: 24px; align-items: center;">
          ${avatars}
        </div>
      </div>`;
      })
      .join('');

    return `<div style="display: flex; flex-direction: column; gap: 32px;">
      ${shapeRows}
    </div>`;
  },
  parameters: {
    docs: {
      description: {
        story:
          'Initials avatars in all size/shape combinations. Uses `--primary` background automatically when initials are provided.',
      },
    },
  },
};

/**
 * Icon Fallback - No Image or Initials
 */
export const FallbackIcon = {
  render: () => {
    const sizes = ['xs', 'sm', 'md', 'lg', 'xl'];
    const genders = ['male', 'female'];

    return `
      <div style="display: flex; flex-direction: column; gap: 32px;">
        ${genders
          .map(
            (gender) => `
          <div>
            <h4 style="margin: 0 0 16px; font-family: var(--font-sans); font-size: var(--font-size-1); color: var(--gray-900); text-transform: capitalize;">
              ${gender} avatar
            </h4>
            <div style="display: flex; gap: 24px; align-items: center;">
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
          </div>
        `
          )
          .join('')}
      </div>
    `;
  },
  parameters: {
    docs: {
      description: {
        story:
          'Icon fallback when neither image nor initials are provided. Uses gender-specific agent silhouette images (male.svg / female.svg).',
      },
    },
  },
};

/**
 * Status Variants
 */
export const StatusVariants = {
  render: () => {
    const statuses = [
      { value: 'online', label: 'Online' },
      { value: 'offline', label: 'Offline' },
      { value: 'busy', label: 'Busy' },
    ];

    return `
      <div style="display: flex; gap: 32px; align-items: center;">
        ${statuses
          .map(
            ({ value, label }) => `
          <div style="text-align: center;">
            ${avatarTwig({
              src: 'https://i.pravatar.cc/150?img=3',
              alt: 'User',
              status: value,
              size: 'lg',
            })}
            <div style="margin-top: 8px; font-family: var(--font-sans); font-size: var(--font-size-0); color: var(--gray-700);">
              ${label}
            </div>
          </div>
        `
          )
          .join('')}
      </div>
    `;
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

/**
 * All Sizes
 */
export const AllSizes = {
  render: () => {
    const sizes = [
      { value: 'xs', label: 'XS (24px)' },
      { value: 'sm', label: 'SM (32px)' },
      { value: 'md', label: 'MD (40px)' },
      { value: 'lg', label: 'LG (48px)' },
      { value: 'xl', label: 'XL (80px)' },
    ];

    return `
      <div style="display: flex; gap: 32px; align-items: center;">
        ${sizes
          .map(
            ({ value, label }) => `
          <div style="text-align: center;">
            ${avatarTwig({
              src: 'https://i.pravatar.cc/150?img=5',
              alt: 'User',
              size: value,
            })}
            <div style="margin-top: 8px; font-family: var(--font-sans); font-size: var(--font-size-0); color: var(--gray-700);">
              ${label}
            </div>
          </div>
        `
          )
          .join('')}
      </div>
    `;
  },
  parameters: {
    docs: {
      description: {
        story: 'All available avatar sizes with corresponding pixel dimensions.',
      },
    },
  },
};

/**
 * All Shapes
 */
export const AllShapes = {
  render: () => {
    const shapes = [
      { value: 'circle', label: 'Circle (default)' },
      { value: 'square', label: 'Square' },
      { value: 'rounded', label: 'Rounded' },
    ];

    return `
      <div style="display: flex; gap: 32px; align-items: center;">
        ${shapes
          .map(
            ({ value, label }) => `
          <div style="text-align: center;">
            ${avatarTwig({
              src: 'https://i.pravatar.cc/150?img=7',
              alt: 'User',
              size: 'lg',
              shape: value,
            })}
            <div style="margin-top: 8px; font-family: var(--font-sans); font-size: var(--font-size-0); color: var(--gray-700);">
              ${label}
            </div>
          </div>
        `
          )
          .join('')}
      </div>
    `;
  },
  parameters: {
    docs: {
      description: {
        story:
          'All available shapes. Rounded corners scale with avatar size (xs: 4px, md: 6px, xl: 12px).',
      },
    },
  },
};

/**
 * Bordered Avatars
 */
export const Bordered = {
  render: () => {
    const sizes = ['sm', 'md', 'lg', 'xl'];

    return `
      <div style="display: flex; gap: 24px; align-items: center; padding: 24px; background: var(--gray-100); border-radius: var(--radius-3);">
        ${sizes
          .map((size) =>
            avatarTwig({
              src: 'https://i.pravatar.cc/150?img=9',
              alt: 'User',
              size,
              bordered: true,
            })
          )
          .join('')}
      </div>
    `;
  },
  parameters: {
    docs: {
      description: {
        story: 'White border around avatars. Useful for avatars displayed on colored backgrounds.',
      },
    },
  },
};

/**
 * Clickable Avatars
 */
export const Clickable = {
  render: () => {
    return `
      <div style="display: flex; gap: 24px; align-items: center;">
        ${avatarTwig({
          src: 'https://i.pravatar.cc/150?img=11',
          alt: 'User',
          size: 'md',
          clickable: true,
        })}
        ${avatarTwig({
          src: 'https://i.pravatar.cc/150?img=13',
          alt: 'User',
          size: 'lg',
          clickable: true,
          href: '#',
        })}
        ${avatarTwig({
          initials: 'AB',
          size: 'lg',
          clickable: true,
          href: '/profile',
        })}
      </div>
      <p style="margin-top: 16px; font-family: var(--font-sans); font-size: var(--font-size-0); color: var(--gray-700);">
        First avatar: clickable (div), Second & Third: links (a) with hover scale effect
      </p>
    `;
  },
  parameters: {
    docs: {
      description: {
        story:
          'Interactive avatars with hover scale effect and focus outline. Provide `href` to render as `<a>` link.',
      },
    },
  },
};

/**
 * Complete Matrix - Sizes × Status
 */
export const StatusMatrix = {
  render: () => {
    const sizes = ['xs', 'sm', 'md', 'lg', 'xl'];
    const statuses = ['online', 'offline', 'busy'];

    const statusRows = statuses
      .map((status) => {
        const avatars = sizes
          .map((size) =>
            avatarTwig({
              initials: 'JD',
              size: size,
              status: status,
            })
          )
          .join('');

        return `<div>
        <h4 style="margin: 0 0 12px; font-family: var(--font-sans); font-size: var(--font-size-1); color: var(--gray-900); text-transform: capitalize;">
          ${status}
        </h4>
        <div style="display: flex; gap: 20px; align-items: center;">
          ${avatars}
        </div>
      </div>`;
      })
      .join('');

    return `<div style="display: flex; flex-direction: column; gap: 24px;">
      ${statusRows}
    </div>`;
  },
  parameters: {
    docs: {
      description: {
        story:
          'Status badge scaling across all avatar sizes. Badge size and border width adapt proportionally.',
      },
    },
  },
};
