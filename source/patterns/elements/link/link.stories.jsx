import linkTwig from './link.twig';
import data from './link.yml';

const settings = {
  title: 'Elements/Link',
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component:
          'Text link with interactive states (hover, active, visited) and optional icon. Supports semantic color variants: primary (default), secondary, info, inverse. Can be disabled and supports external links with target="_blank".',
      },
    },
  },
  argTypes: {
    text: {
      description: 'Link text content',
      control: { type: 'text' },
      table: {
        type: { summary: 'string' },
        defaultValue: { summary: 'Link text' },
      },
    },
    url: {
      description: 'Link URL',
      control: { type: 'text' },
      table: {
        type: { summary: 'string' },
        defaultValue: { summary: '#' },
      },
    },
    color: {
      description: 'Link color variant (optional)',
      control: { type: 'select' },
      options: ['primary', 'secondary', 'info', 'inverse'],
      table: {
        type: { summary: 'primary | secondary | info | inverse' },
        defaultValue: { summary: '(base text color)' },
      },
    },
    underline: {
      description: 'Show underline on link',
      control: { type: 'boolean' },
      table: {
        type: { summary: 'boolean' },
        defaultValue: { summary: 'true' },
      },
    },
    icon: {
      description: 'Icon name to display',
      control: { type: 'text' },
      table: {
        type: { summary: 'string' },
        defaultValue: { summary: '' },
        iconPosition: {
          description: 'Icon position (left or right of text)',
          control: { type: 'select' },
          options: ['left', 'right'],
          table: {
            type: { summary: 'left | right' },
            defaultValue: { summary: 'right' },
          },
        },
      },
    },
    target: {
      description: 'Link target attribute',
      control: { type: 'select' },
      options: ['_self', '_blank'],
      table: {
        type: { summary: '_self | _blank' },
        defaultValue: { summary: '_self' },
      },
    },
    rel: {
      description: 'Link rel attribute',
      control: { type: 'text' },
      table: {
        type: { summary: 'string' },
        defaultValue: { summary: '' },
      },
    },
    disabled: {
      description: 'Disabled state',
      control: { type: 'boolean' },
      table: {
        type: { summary: 'boolean' },
        defaultValue: { summary: 'false' },
      },
    },
  },
};

export const Default = {
  render: (args) => linkTwig(args),
  args: { ...data },
};

export const Primary = {
  render: () => linkTwig({ text: 'Primary link', url: '#', color: 'primary', underline: true }),
};

export const Secondary = {
  render: () => linkTwig({ text: 'Secondary link', url: '#', color: 'secondary', underline: true }),
};

export const Inverse = {
  render: () => `
    <div style="background-color: var(--gray-800); padding: var(--size-6); border-radius: var(--radius-2);">
      ${linkTwig({ text: 'Inverse link', url: '#', color: 'inverse', underline: true })}
    </div>
  `,
};

export const Info = {
  render: () => linkTwig({ text: 'Info link', url: '#', color: 'info', underline: true }),
};

export const WithIcon = {
  render: () =>
    linkTwig({
      text: 'Link with icon right',
      url: '#',
      icon: 'arrow-right',
      iconPosition: 'right',
      underline: true,
    }),
};

export const WithIconLeft = {
  render: () =>
    linkTwig({
      text: 'Link with icon left',
      url: '#',
      icon: 'arrow-left',
      iconPosition: 'left',
      underline: true,
    }),
};

export const External = {
  render: () =>
    linkTwig({
      text: 'External link',
      url: 'https://example.com',
      target: '_blank',
      underline: true,
    }),
};

export const WithoutUnderline = {
  render: () => linkTwig({ text: 'Link without underline', url: '#', underline: false }),
};

export const Disabled = {
  render: () => `
    <div style="display: flex; gap: var(--size-6); align-items: center;">
      ${linkTwig({ text: 'Disabled link', url: '#', disabled: false })}
      ${linkTwig({ text: 'Disabled link', url: '#', disabled: true })}
    </div>
  `,
};

export const AllColorVariants = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-4);">
      <div>
        <strong>Default (base text color):</strong> ${linkTwig({ text: 'Default link', url: '#' })}
      </div>
      <div>
        <strong>Primary (variant):</strong> ${linkTwig({ text: 'Primary link', url: '#', color: 'primary' })}
      </div>
      <div>
        <strong>Secondary:</strong> ${linkTwig({ text: 'Secondary link', url: '#', color: 'secondary' })}
      </div>
      <div>
        <strong>Info:</strong> ${linkTwig({ text: 'Info link', url: '#', color: 'info' })}
      </div>
      <div style="background-color: var(--gray-800); padding: var(--size-4); border-radius: var(--radius-2);">
        <strong style="color: var(--white);">Inverse:</strong> ${linkTwig({ text: 'Inverse link', url: '#', color: 'inverse' })}
      </div>
    </div>
  `,
};

export const UseCases = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-6);">
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0;">Standard link in paragraph</h3>
        <p style="max-width: 600px;">
          Lorem ipsum dolor sit amet, consectetur adipiscing elit. 
          ${linkTwig({ text: 'Learn more', url: '#', color: 'primary' })} 
          about our services and how we can help you achieve your goals.
        </p>
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0;">Navigation link with icon</h3>
        <div style="display: flex; flex-direction: column; gap: var(--size-3);">
          ${linkTwig({ text: 'Next page', url: '#', icon: 'arrow-right', iconPosition: 'right', underline: false })}
          ${linkTwig({ text: 'Previous page', url: '#', icon: 'arrow-left', iconPosition: 'left', underline: false })}
        </div>
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0;">External resource</h3>
        ${linkTwig({ text: 'Open documentation', url: 'https://example.com', target: '_blank' })}
      </div>
      <div style="background-color: var(--gray-800); padding: var(--size-6); border-radius: var(--radius-2);">
        <h3 style="margin: 0 0 var(--size-3) 0; color: var(--white);">Link on dark background</h3>
        ${linkTwig({ text: 'Contact us', url: '#', color: 'inverse', underline: true })}
      </div>
    </div>
  `,
};

export default settings;
