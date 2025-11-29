import iconsList from '../../documentation/icons-list.json';
import icon from './icon.twig';
import data from './icon.yml';

const settings = {
  title: 'Elements/Icon',
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component: `Icon font system using bnpre-icons and bnpre-icons-poi fonts. 
        
**Fonts**: bnpre-icons (75 icons) + bnpre-icons-poi (14 POI icons)  
**Sizes**: small (16px), medium (20px), large (24px), xlarge (32px)  
**Usage**: \`<i class="icon-search ps-icon--medium"></i>\`  

The icon classes are defined in \`source/props/icons.css\` and should NOT be modified.`,
      },
    },
  },
  argTypes: {
    name: {
      description: 'Icon class name (e.g., icon-search, icon-account, icon-poi-hotel)',
      control: { type: 'select' },
      options: iconsList.all,
      table: {
        type: { summary: 'string' },
        defaultValue: { summary: 'icon-search' },
      },
    },
    size: {
      description: 'Icon size',
      control: { type: 'select' },
      options: ['small', 'medium', 'large', 'xlarge'],
      table: {
        type: { summary: 'small | medium | large | xlarge' },
        defaultValue: { summary: 'medium' },
      },
    },
    color: {
      description: 'Custom icon color (CSS color value)',
      control: { type: 'color' },
      table: {
        type: { summary: 'string' },
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
    ariaLabel: {
      description: 'Accessibility label (use for informative icons)',
      control: { type: 'text' },
      table: {
        type: { summary: 'string' },
      },
    },
  },
};

export const Default = {
  render: (args) => icon(args),
  args: { ...data },
};

export const AllSizes = {
  render: () => `
    <div style="display: flex; gap: var(--size-6); align-items: center;">
      ${icon({ name: 'icon-search', size: 'small' })}
      <span style="font-size: var(--font-size-0); color: var(--gray-600);">small (16px)</span>
      ${icon({ name: 'icon-search', size: 'medium' })}
      <span style="font-size: var(--font-size-0); color: var(--gray-600);">medium (20px)</span>
      ${icon({ name: 'icon-search', size: 'large' })}
      <span style="font-size: var(--font-size-0); color: var(--gray-600);">large (24px)</span>
      ${icon({ name: 'icon-search', size: 'xlarge' })}
      <span style="font-size: var(--font-size-0); color: var(--gray-600);">xlarge (32px)</span>
    </div>
  `,
};

export const CustomColors = {
  render: () => `
    <div style="display: flex; gap: var(--size-6); align-items: center;">
      ${icon({ name: 'icon-check', size: 'xlarge', color: 'var(--bnp-green)' })}
      ${icon({ name: 'icon-close', size: 'xlarge', color: 'var(--red-600)' })}
      ${icon({ name: 'icon-infos', size: 'xlarge', color: 'var(--blue-500)' })}
      ${icon({ name: 'icon-help', size: 'xlarge', color: 'var(--amber-500)' })}
    </div>
  `,
};

export const Disabled = {
  render: () => `
    <div style="display: flex; gap: var(--size-6); align-items: center;">
      ${icon({ name: 'icon-search', size: 'xlarge', disabled: false })}
      <span style="font-size: var(--font-size-1); color: var(--gray-600);">Normal</span>
      ${icon({ name: 'icon-search', size: 'xlarge', disabled: true })}
      <span style="font-size: var(--font-size-1); color: var(--gray-600);">Disabled (50% opacity)</span>
    </div>
  `,
};

export const AllRegularIcons = {
  name: 'Gallery: Regular Icons (75)',
  render: () => `
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: var(--size-6); padding: var(--size-4);">
      ${iconsList.regular
        .map(
          (iconName) => `
        <div style="display: flex; flex-direction: column; align-items: center; gap: var(--size-3); padding: var(--size-4); border: 1px solid var(--gray-200); border-radius: var(--radius-2); transition: all 0.2s;" onmouseover="this.style.borderColor='var(--bnp-green)'; this.style.background='var(--gray-50)';" onmouseout="this.style.borderColor='var(--gray-200)'; this.style.background='transparent';">
          ${icon({ name: iconName, size: 'xlarge' })}
          <code style="font-size: var(--font-size-0); text-align: center; word-break: break-word; color: var(--gray-700);">${iconName}</code>
        </div>
      `
        )
        .join('')}
    </div>
  `,
};

export const AllPOIIcons = {
  name: 'Gallery: POI Icons (14)',
  render: () => `
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: var(--size-6); padding: var(--size-4);">
      ${iconsList.poi
        .map(
          (iconName) => `
        <div style="display: flex; flex-direction: column; align-items: center; gap: var(--size-3); padding: var(--size-4); border: 1px solid var(--gray-200); border-radius: var(--radius-2); transition: all 0.2s;" onmouseover="this.style.borderColor='var(--bnp-green)'; this.style.background='var(--gray-50)';" onmouseout="this.style.borderColor='var(--gray-200)'; this.style.background='transparent';">
          ${icon({ name: iconName, size: 'xlarge' })}
          <code style="font-size: var(--font-size-0); text-align: center; word-break: break-word; color: var(--gray-700);">${iconName}</code>
        </div>
      `
        )
        .join('')}
    </div>
  `,
};

export const SearchExample = {
  name: 'Example: Search Icons',
  render: () => `
    <div style="display: flex; gap: var(--size-6); align-items: center;">
      ${icon({ name: 'icon-search', size: 'medium' })}
      ${icon({ name: 'icon-pin-map', size: 'medium' })}
      ${icon({ name: 'icon-map', size: 'medium' })}
      ${icon({ name: 'icon-around-me', size: 'medium' })}
    </div>
  `,
};

export const NavigationExample = {
  name: 'Example: Navigation Icons',
  render: () => `
    <div style="display: flex; gap: var(--size-6); align-items: center;">
      ${icon({ name: 'icon-arrow-left', size: 'medium' })}
      ${icon({ name: 'icon-arrow-right', size: 'medium' })}
      ${icon({ name: 'icon-arrow-top', size: 'medium' })}
      ${icon({ name: 'icon-arrow-down', size: 'medium' })}
      ${icon({ name: 'icon-big-arrow-left', size: 'medium' })}
      ${icon({ name: 'icon-big-arrow-right', size: 'medium' })}
    </div>
  `,
};

export const ActionsExample = {
  name: 'Example: Action Icons',
  render: () => `
    <div style="display: flex; gap: var(--size-6); align-items: center;">
      ${icon({ name: 'icon-check', size: 'medium', color: 'var(--bnp-green)' })}
      ${icon({ name: 'icon-close', size: 'medium', color: 'var(--red-600)' })}
      ${icon({ name: 'icon-edit', size: 'medium' })}
      ${icon({ name: 'icon-bin', size: 'medium' })}
      ${icon({ name: 'icon-share', size: 'medium' })}
      ${icon({ name: 'icon-send', size: 'medium' })}
    </div>
  `,
};

export const CheckboxIcons = {
  name: 'Example: Checkbox Icons',
  render: () => `
    <div style="display: flex; gap: var(--size-6); align-items: center;">
      ${icon({ name: 'icon-checkbox', size: 'medium' })}
      ${icon({ name: 'icon-checkbox-checked', size: 'medium', color: 'var(--bnp-green)' })}
    </div>
  `,
};

export default settings;
