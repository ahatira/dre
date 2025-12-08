import iconsList from '../../documentation/icons-list.json';
import iconRegistry from '../../documentation/icons-registry.json';
import iconTwig from './icon.twig';
import data from './icon.yml';

export default {
  title: 'Elements/Icon',
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component:
          'Semantic SVG icon component using a generated sprite. Supports 6 sizes (xs to xxl) and semantic colors with full accessibility support.',
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

export const CategorizedGallery = {
  render: () => {
    const categoryColors = {
      ui: 'var(--primary)',
      navigation: 'var(--secondary)',
      forms: 'var(--success)',
      communication: 'var(--warning)',
      media: 'var(--danger)',
      business: 'var(--info)',
    };

    return `
      <div style="padding: var(--size-6); background: var(--white);">
        <div style="margin-bottom: var(--size-8);">
          <h2 style="margin: 0 0 var(--size-4) 0; font-size: var(--font-size-6); color: var(--gray-900);">All ${iconRegistry.total} Icons by Category</h2>
          <p style="margin: 0; color: var(--gray-600); font-size: var(--font-size-2);">Icons are auto-generated from source/icons-source/*.svg and categorized via source/patterns/documentation/icons-registry.json</p>
        </div>

        ${Object.entries(iconRegistry.categories)
          .map(
            ([category, icons]) => `
            <div style="margin-bottom: var(--size-8);">
              <h3 style="margin: 0 0 var(--size-4) 0; padding-bottom: var(--size-3); border-bottom: 2px solid ${categoryColors[category]}; color: ${categoryColors[category]}; text-transform: capitalize; font-size: var(--font-size-4);">
                ${category} (${icons.length} icons)
              </h3>
              <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); gap: var(--size-4);">
                ${icons
                  .map(
                    (iconName) => `
                    <div style="display: flex; flex-direction: column; align-items: center; gap: var(--size-2); padding: var(--size-3); border: 1px solid var(--gray-200); border-radius: var(--radius-2); background: var(--gray-50); transition: all 150ms var(--ease-3); cursor: pointer;" onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.1)'; this.style.borderColor='${categoryColors[category]}'; this.style.backgroundColor='${categoryColors[category]}15'" onmouseout="this.style.boxShadow='none'; this.style.borderColor='var(--gray-200)'; this.style.backgroundColor='var(--gray-50)'">
                      <div style="display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; color: ${categoryColors[category]};">
                        ${iconTwig({ ...data, name: iconName, size: 'lg' })}
                      </div>
                      <span style="font-size: var(--font-size-0); color: var(--gray-700); text-align: center; word-break: break-word; line-height: 1.2;">${iconName}</span>
                    </div>
                  `
                  )
                  .join('')}
              </div>
            </div>
          `
          )
          .join('')}

        <div style="margin-top: var(--size-8); padding: var(--size-5); background: var(--gray-50); border-left: 4px solid var(--primary); border-radius: var(--radius-2);">
          <h4 style="margin: 0 0 var(--size-3) 0; color: var(--gray-900);">How to Use Icons</h4>
          <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: var(--size-4); font-size: var(--font-size-1); color: var(--gray-700);">
            <div>
              <strong style="color: var(--primary);">Pattern 1: Twig Component</strong>
              <code style="display: block; margin-top: var(--size-2); padding: var(--size-2); background: var(--white); border-radius: var(--radius-1); font-family: monospace; overflow-x: auto;">{% include '@elements/icon/icon.twig' with { icon: 'check' } %}</code>
            </div>
            <div>
              <strong style="color: var(--secondary);">Pattern 2: data-icon</strong>
              <code style="display: block; margin-top: var(--size-2); padding: var(--size-2); background: var(--white); border-radius: var(--radius-1); font-family: monospace; overflow-x: auto;">&lt;span data-icon="check"&gt;&lt;/span&gt;</code>
            </div>
            <div>
              <strong style="color: var(--success);">Pattern 3: SVG Use</strong>
              <code style="display: block; margin-top: var(--size-2); padding: var(--size-2); background: var(--white); border-radius: var(--radius-1); font-family: monospace; overflow-x: auto;">&lt;use href="/icons/icons-sprite.svg#icon-check"&gt;</code>
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
          'Complete icon gallery organized by category (UI, Navigation, Forms, Communication, Media, Business). All ${iconRegistry.total} icons are auto-generated and validated. Use any of the 3 access patterns shown below.',
      },
    },
  },
};
