import avatarTwig from './avatar.twig';

export default {
  title: 'Elements/Avatar',
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
    src: '/source/assets/images/1-1.jpg',
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
            src: '/source/assets/images/1-1.jpg',
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
            src: '/source/assets/images/1-1.jpg',
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
            src: '/source/assets/images/1-1.jpg',
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
            src: '/source/assets/images/1-1.jpg',
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
        src: '/source/assets/images/1-1.jpg',
        alt: 'Agent',
        clickable: true,
      })}
      ${avatarTwig({
        src: '/source/assets/images/1-1.jpg',
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

/**
 * Real Estate context examples
 */
export const RealEstateContext = {
  render: () => {
    return `
      <div style="display: flex; flex-direction: column; gap: var(--size-10);">
        <div>
          <h4 style="margin: 0 0 var(--size-4); color: var(--text-primary); font-size: var(--font-size-3); font-weight: 600;">Agent Profile Card (lg, online)</h4>
          <div style="background: var(--white); padding: var(--size-6); border-radius: var(--radius-3); border: 1px solid var(--border-light); display: inline-flex; flex-direction: column; align-items: center; gap: var(--size-3); box-shadow: var(--shadow-sm);">
            ${avatarTwig({
              src: '/source/assets/images/1-1.jpg',
              alt: 'Sophie Martin, Agent immobilier',
              size: 'lg',
              status: 'online',
              clickable: true,
              href: '/agents/sophie-martin',
            })}
            <div style="text-align: center;">
              <div style="font-weight: 600; color: var(--text-primary); font-size: var(--font-size-2);">Sophie Martin</div>
              <div style="font-size: var(--font-size-1); color: var(--text-secondary);">Agent commercial</div>
            </div>
          </div>
        </div>

        <div>
          <h4 style="margin: 0 0 var(--size-4); color: var(--text-primary); font-size: var(--font-size-3); font-weight: 600;">Property Listing Attribution (sm, no status)</h4>
          <div style="background: var(--white); padding: var(--size-5); border-radius: var(--radius-3); border: 1px solid var(--border-light); max-width: 400px;">
            <div style="display: flex; align-items: center; gap: var(--size-3);">
              ${avatarTwig({
                src: '/source/assets/images/1-1.jpg',
                alt: 'Jean Dupont',
                size: 'sm',
                shape: 'circle',
              })}
              <div style="flex: 1;">
                <div style="font-weight: 500; color: var(--text-primary); font-size: var(--font-size-1);">Jean Dupont</div>
                <div style="font-size: var(--font-size-0); color: var(--text-secondary);">+33 1 23 45 67 89</div>
              </div>
              <button style="padding: var(--size-2) var(--size-4); background: var(--primary); color: var(--white); border: none; border-radius: var(--radius-2); font-size: var(--font-size-0); cursor: pointer;">
                Contacter
              </button>
            </div>
          </div>
        </div>

        <div>
          <h4 style="margin: 0 0 var(--size-4); color: var(--text-primary); font-size: var(--font-size-3); font-weight: 600;">Team Directory (md, status badges)</h4>
          <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: var(--size-5);">
            ${[
              { initials: 'SM', status: 'online', name: 'Sophie Martin', role: 'Agent Commercial' },
              { initials: 'JD', status: 'busy', name: 'Jean Dupont', role: 'Conseiller' },
              { initials: 'MC', status: 'offline', name: 'Marie Curie', role: 'Responsable' },
            ]
              .map(
                (agent) => `
              <div style="background: var(--white); padding: var(--size-4); border-radius: var(--radius-3); border: 1px solid var(--border-light); text-align: center;">
                ${avatarTwig({
                  initials: agent.initials,
                  status: agent.status,
                  size: 'md',
                  clickable: true,
                })}
                <div style="margin-top: var(--size-3); font-weight: 600; color: var(--text-primary); font-size: var(--font-size-1);">${agent.name}</div>
                <div style="font-size: var(--font-size-0); color: var(--text-secondary);">${agent.role}</div>
              </div>
            `
              )
              .join('')}
          </div>
        </div>

        <div>
          <h4 style="margin: 0 0 var(--size-4); color: var(--text-primary); font-size: var(--font-size-3); font-weight: 600;">Fallback Icons (xs, male/female)</h4>
          <div style="display: flex; gap: var(--size-4); align-items: center;">
            <div style="text-align: center;">
              ${avatarTwig({
                gender: 'male',
                size: 'xs',
              })}
              <div style="margin-top: var(--size-2); font-size: var(--font-size-0); color: var(--text-secondary);">Agent non renseigné</div>
            </div>
            <div style="text-align: center;">
              ${avatarTwig({
                gender: 'female',
                size: 'xs',
              })}
              <div style="margin-top: var(--size-2); font-size: var(--font-size-0); color: var(--text-secondary);">Conseillère non renseignée</div>
            </div>
          </div>
        </div>

        <div>
          <h4 style="margin: 0 0 var(--size-4); color: var(--text-primary); font-size: var(--font-size-3); font-weight: 600;">Featured Agent Spotlight (xl, bordered)</h4>
          <div style="background: linear-gradient(135deg, var(--primary-bg-subtle) 0%, var(--secondary-bg-subtle) 100%); padding: var(--size-8); border-radius: var(--radius-4); text-align: center;">
            ${avatarTwig({
              src: '/source/assets/images/1-1.jpg',
              alt: 'Agent du mois',
              size: 'xl',
              bordered: true,
              status: 'online',
            })}
            <div style="margin-top: var(--size-5);">
              <div style="font-weight: 700; color: var(--text-primary); font-size: var(--font-size-4); margin-bottom: var(--size-2);">Agent du mois</div>
              <div style="font-size: var(--font-size-2); color: var(--text-secondary);">Plus de 25 transactions réussies</div>
            </div>
          </div>
        </div>
      </div>
    `;
  },
  parameters: {
    docs: {
      description: {
        story:
          'Common avatar usage patterns in real estate: agent profile cards (lg with status), property listing attribution (sm), team directory (md with status badges), fallback icons (xs), and featured agent spotlight (xl bordered).',
      },
    },
  },
};
