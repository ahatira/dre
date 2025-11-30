import linkTwig from './link.twig';
import data from './link.yml';

const settings = {
  title: 'Elements/Link',
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component: `Text link element with interactive states and optional icon.

**Key Features:**
- Semantic color variants: primary, secondary, info, inverse (default uses base text color)
- Interactive states: hover removes underline, visited/active states
- Optional icon with configurable position (left/right)
- External link support with automatic rel="noopener noreferrer"
- Disabled state renders as <span> with aria-disabled
- Focus-visible outline for keyboard navigation

**Usage:**
- Default link uses base text color with underline
- Use color variants for semantic emphasis
- Icon names without "icon-" prefix (handled automatically)
- External links with target="_blank" get security attributes`,
      },
    },
  },
  argTypes: {
    // Content
    text: {
      description: 'Link text content displayed to user',
      control: { type: 'text' },
      table: {
        category: 'Content',
        type: { summary: 'string', required: true },
        defaultValue: { summary: 'Link text' },
      },
    },
    icon: {
      description: 'Icon name without "icon-" prefix (e.g., arrow-right, arrow-left, external-link, download)',
      control: { type: 'text' },
      table: {
        category: 'Content',
        type: { summary: 'string' },
        defaultValue: { summary: '' },
      },
    },
    // Appearance
    color: {
      description: 'Link color variant (omit for default text color, or use: primary, secondary, info, inverse)',
      control: { type: 'select' },
      options: ['', 'primary', 'secondary', 'info', 'inverse'],
      table: {
        category: 'Appearance',
        type: { summary: 'primary | secondary | info | inverse' },
        defaultValue: { summary: '(base text color)' },
      },
    },
    underline: {
      description: 'Show underline decoration (hover removes it, default: true)',
      control: { type: 'boolean' },
      table: {
        category: 'Appearance',
        type: { summary: 'boolean' },
        defaultValue: { summary: 'true' },
      },
    },
    iconPosition: {
      description: 'Icon position relative to text (left or right, default: right)',
      control: { type: 'select' },
      options: ['left', 'right'],
      table: {
        category: 'Appearance',
        type: { summary: 'left | right' },
        defaultValue: { summary: 'right' },
      },
    },
    // Link
    url: {
      description: 'Link destination URL or anchor',
      control: { type: 'text' },
      table: {
        category: 'Link',
        type: { summary: 'string', required: true },
        defaultValue: { summary: '#' },
      },
    },
    target: {
      description: 'Link target (_self for same window, _blank for new tab with security attributes)',
      control: { type: 'select' },
      options: ['_self', '_blank'],
      table: {
        category: 'Link',
        type: { summary: '_self | _blank' },
        defaultValue: { summary: '_self' },
      },
    },
    rel: {
      description: 'Custom rel attribute (auto-set to "noopener noreferrer" for target="_blank")',
      control: { type: 'text' },
      table: {
        category: 'Link',
        type: { summary: 'string' },
        defaultValue: { summary: '' },
      },
    },
    // Behavior
    disabled: {
      description: 'Disabled state (renders as <span> with aria-disabled, pointer-events: none)',
      control: { type: 'boolean' },
      table: {
        category: 'Behavior',
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

export const AllColors = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-4);">
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Default (base text color)</p>
        ${linkTwig({ text: 'Default link', url: '#' })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Primary variant</p>
        ${linkTwig({ text: 'Primary link', url: '#', color: 'primary' })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Secondary variant</p>
        ${linkTwig({ text: 'Secondary link', url: '#', color: 'secondary' })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Info variant</p>
        ${linkTwig({ text: 'Info link', url: '#', color: 'info' })}
      </div>
      <div style="background-color: var(--gray-800); padding: var(--size-4); border-radius: var(--radius-2);">
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--white);">Inverse variant (for dark backgrounds)</p>
        ${linkTwig({ text: 'Inverse link', url: '#', color: 'inverse' })}
      </div>
    </div>
  `,
};

export const AllStates = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-4);">
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">With underline (default, hover removes it)</p>
        ${linkTwig({ text: 'Link with underline', url: '#', underline: true })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Without underline</p>
        ${linkTwig({ text: 'Link without underline', url: '#', underline: false })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Disabled (renders as span with aria-disabled)</p>
        ${linkTwig({ text: 'Disabled link', url: '#', disabled: true })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">With icon right</p>
        ${linkTwig({ text: 'Next page', url: '#', icon: 'arrow-right', iconPosition: 'right' })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">With icon left</p>
        ${linkTwig({ text: 'Previous page', url: '#', icon: 'arrow-left', iconPosition: 'left' })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">External (target="_blank" with security rel)</p>
        ${linkTwig({ text: 'External resource', url: 'https://example.com', target: '_blank' })}
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
