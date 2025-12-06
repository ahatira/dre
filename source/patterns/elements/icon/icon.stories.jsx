import iconsList from '../../documentation/icons-list.json';
import iconTwig from './icon.twig';
import data from './icon.yml';

export default {
  title: 'Elements/Icon',
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component:
          'Semantic SVG icon component using a generated sprite. Supports 6 sizes (xs to xxl), semantic colors, accessibility labels, and optional icon-font fallback for legacy use.',
      },
    },
  },
  argTypes: {
    baseClass: {
      description:
        'Override root BEM class for composition. When provided, Icon emits only this class and mapped modifiers; otherwise emits ps-icon classes.',
      control: { type: 'text' },
      table: {
        category: 'Structure',
        type: { summary: 'string' },
        defaultValue: { summary: null },
      },
    },
    name: {
      description:
        'Icon name without "icon-" prefix. Backed by sprite generated from source/assets/icons/*.svg.',
      control: { type: 'select' },
      options: iconsList.all,
      table: {
        category: 'Content',
        type: { summary: 'string', required: true },
        defaultValue: { summary: 'search' },
      },
    },
    size: {
      description: 'Size: xs (10px), sm (16px), md (20px), lg (24px), xl (32px), xxl (48px)',
      control: { type: 'select' },
      options: ['xs', 'sm', 'md', 'lg', 'xl', 'xxl'],
      table: {
        category: 'Appearance',
        type: { summary: 'xs|sm|md|lg|xl|xxl' },
        defaultValue: { summary: 'md' },
      },
    },
    color: {
      description:
        'Semantic color: default (gray), primary, secondary, success, warning, danger, info',
      control: { type: 'select' },
      options: ['default', 'primary', 'secondary', 'success', 'warning', 'danger', 'info'],
      table: {
        category: 'Appearance',
        type: { summary: 'string' },
        defaultValue: { summary: 'default' },
      },
    },
    disabled: {
      description: 'Disabled state (50% opacity)',
      control: { type: 'boolean' },
      table: {
        category: 'Behavior',
        type: { summary: 'boolean' },
        defaultValue: { summary: 'false' },
      },
    },
    ariaLabel: {
      description: 'Accessibility label (informative icons)',
      control: { type: 'text' },
      table: { category: 'Accessibility', type: { summary: 'string' } },
    },
  },
};

export const Default = {
  render: (args) => iconTwig(args),
  args: { ...data },
  parameters: {
    docs: {
      description: {
        story: 'Default icon configuration with medium size and default gray color.',
      },
    },
  },
};

export const AllSizes = {
  render: (args) => `
    <div style="display: flex; align-items: center; gap: var(--size-6);">
      ${iconTwig({ ...args, size: 'xs' })}
      ${iconTwig({ ...args, size: 'sm' })}
      ${iconTwig({ ...args, size: 'md' })}
      ${iconTwig({ ...args, size: 'lg' })}
      ${iconTwig({ ...args, size: 'xl' })}
      ${iconTwig({ ...args, size: 'xxl' })}
    </div>
  `,
  args: { ...data },
  parameters: {
    docs: {
      description: {
        story: 'All available sizes from xs (10px) to xxl (48px), scaling via design tokens.',
      },
    },
  },
};

export const AllColors = {
  render: (args) => `
    <div style="display: flex; align-items: center; gap: var(--size-6);">
      ${iconTwig({ ...args, color: 'default' })}
      ${iconTwig({ ...args, color: 'primary' })}
      ${iconTwig({ ...args, color: 'secondary' })}
      ${iconTwig({ ...args, color: 'success' })}
      ${iconTwig({ ...args, color: 'warning' })}
      ${iconTwig({ ...args, color: 'danger' })}
      ${iconTwig({ ...args, color: 'info' })}
    </div>
  `,
  args: { ...data, name: 'check', size: 'xl' },
  parameters: {
    docs: {
      description: {
        story:
          'All semantic colors: default (gray), primary (BNP green), secondary (magenta), success, warning, danger, and info.',
      },
    },
  },
};

export const AllStates = {
  render: (args) => `
    <div style="display: flex; align-items: center; gap: var(--size-8);">
      <div style="display: flex; flex-direction: column; align-items: center; gap: var(--size-2);">
        ${iconTwig({ ...args, disabled: false })}
        <span style="font-size: var(--font-size-0); color: var(--gray-600);">Enabled</span>
      </div>
      <div style="display: flex; flex-direction: column; align-items: center; gap: var(--size-2);">
        ${iconTwig({ ...args, disabled: true })}
        <span style="font-size: var(--font-size-0); color: var(--gray-600);">Disabled</span>
      </div>
    </div>
  `,
  args: { ...data, name: 'search', size: 'xl', color: 'primary' },
  parameters: {
    docs: {
      description: {
        story: 'Enabled vs disabled state (50% opacity when disabled).',
      },
    },
  },
};

export const Gallery = {
  render: () => `
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: var(--size-5); padding: var(--size-6);">
      ${iconsList.all
        .map(
          (iconName) => `
          <div style="display: flex; flex-direction: column; align-items: center; gap: var(--size-3); padding: var(--size-4); border: 1px solid var(--gray-300); border-radius: var(--radius-2); background: var(--white); transition: all 150ms var(--ease-3); cursor: pointer;" onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.1)'; this.style.borderColor='var(--primary)'" onmouseout="this.style.boxShadow='none'; this.style.borderColor='var(--gray-300)'">
            <div style="display: flex; align-items: center; justify-content: center; width: 48px; height: 48px;">
              ${iconTwig({ ...data, name: iconName, size: 'xl' })}
            </div>
            <span style="font-size: var(--font-size-0); color: var(--gray-700); text-align: center; word-break: break-word; font-weight: 500; line-height: 1.3;">${iconName}</span>
          </div>
        `
        )
        .join('')}
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story:
          'Full gallery of all available SVG icons built from source/assets/icons/*.svg via the generated sprite. Hover over icons for a subtle highlight.',
      },
    },
  },
};
