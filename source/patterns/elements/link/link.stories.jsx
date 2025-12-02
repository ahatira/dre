import iconsList from '../../documentation/icons-list.json';
import linkTwig from './link.twig';
import data from './link.yml';

const settings = {
  title: 'Elements/Link',
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component: `Semantic text link with optional icon and variant colors.
Supports underline control, external target handling, and focus-visible accessibility.`,
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
      description:
        'Icon name without "icon-" prefix (e.g., arrow-right, arrow-left, external-link, download)',
      control: { type: 'select' },
      options: ['', ...iconsList.all],
      table: {
        category: 'Content',
        type: { summary: 'string' },
        defaultValue: { summary: '' },
      },
    },
    // Appearance
    color: {
      description:
        'Link color variant: semantic colors for navigation, CTAs, and status indicators',
      control: { type: 'select' },
      options: ['default', 'primary', 'secondary', 'info', 'warning', 'success', 'danger', 'dark', 'light'],
      table: {
        category: 'Appearance',
        type: { summary: 'default | primary | secondary | info | warning | success | danger | dark | light' },
        defaultValue: { summary: 'primary' },
      },
    },
    size: {
      description: 'Link size variant: adapt for hierarchy, accessibility, and context',
      control: { type: 'select' },
      options: ['xs', 'sm', 'md', 'lg', 'xl', 'xxl'],
      table: {
        category: 'Appearance',
        type: { summary: 'xs | sm | md | lg | xl | xxl' },
        defaultValue: { summary: 'md' },
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
      description:
        'Link target (_self for same window, _blank for new tab with security attributes)',
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
    <p style="margin-bottom: var(--size-3); color: var(--gray-700); font-size: var(--font-size-1);">All available color variants for links. Use semantic colors for real estate navigation, CTAs, and status indicators.</p>
    <div style="display: flex; flex-direction: column; gap: var(--size-4);">
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Default (base text color)</p>
        ${linkTwig({ text: 'Default link', url: '#', color: 'default' })}
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
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Warning variant</p>
        ${linkTwig({ text: 'Warning link', url: '#', color: 'warning' })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Success variant</p>
        ${linkTwig({ text: 'Success link', url: '#', color: 'success' })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Danger variant</p>
        ${linkTwig({ text: 'Danger link', url: '#', color: 'danger' })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Dark variant</p>
        ${linkTwig({ text: 'Dark link', url: '#', color: 'dark' })}
      </div>
      <div style="background-color: var(--gray-800); padding: var(--size-4); border-radius: var(--radius-2);">
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--white);">Light variant (for dark backgrounds)</p>
        ${linkTwig({ text: 'Light link', url: '#', color: 'light' })}
      </div>
    </div>
  `,
};

export const AllSizes = {
  render: () => `
    <p style="margin-bottom: var(--size-3); color: var(--gray-700); font-size: var(--font-size-1);">All supported link sizes. Adapt link size for hierarchy, accessibility, and context (menus, footers, property listings).</p>
    <div style="display: flex; flex-direction: column; gap: var(--size-4);">
      <div>${linkTwig({ text: 'Extra small', url: '#', size: 'xs' })}</div>
      <div>${linkTwig({ text: 'Small', url: '#', size: 'sm' })}</div>
      <div>${linkTwig({ text: 'Medium', url: '#', size: 'md' })}</div>
      <div>${linkTwig({ text: 'Large', url: '#', size: 'lg' })}</div>
      <div>${linkTwig({ text: 'Extra large', url: '#', size: 'xl' })}</div>
      <div>${linkTwig({ text: 'XXL', url: '#', size: 'xxl' })}</div>
    </div>
  `,
};

export const AllStates = {
  render: () => `
    <p style="margin-bottom: var(--size-3); color: var(--gray-700); font-size: var(--font-size-1);">All interactive states and icon options. Demonstrates underline, disabled, external, and icon positioning for real estate use.</p>
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
    <p style="margin-bottom: var(--size-3); color: var(--gray-700); font-size: var(--font-size-1);">Typical real estate use cases: navigation, call-to-action, external resources, and footer links. All examples use contextual real estate content.</p>
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
        ${linkTwig({ text: 'Contact us', url: '#', color: 'light', underline: true })}
      </div>
    </div>
  `,
};

export default settings;
