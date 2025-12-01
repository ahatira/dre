import iconsList from '../../source/patterns/documentation/icons-list.json';
import icon from './icon.twig';
import data from './icon.yml';

const settings = {
  title: 'Elements/Icon',
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component:
          'Semantic icon component supporting 6 color variants and 4 sizes. Accessible for both decorative and informative use.',
      },
    },
  },
  argTypes: {
    // Content
    name: {
      description: 'Icon name without "icon-" prefix (e.g., search, check, pin-map, poi-hotel)',
      control: { type: 'select' },
      options: iconsList.all,
      table: {
        category: 'Content',
        type: { summary: 'string', required: true },
        defaultValue: { summary: 'search' },
      },
    },
    // Appearance
    size: {
      description: 'Icon size (small: 16px, medium: 20px, large: 24px, xlarge: 32px)',
      control: { type: 'select' },
      options: ['small', 'medium', 'large', 'xlarge'],
      table: {
        category: 'Appearance',
        type: { summary: 'small | medium | large | xlarge' },
        defaultValue: { summary: 'medium' },
      },
    },
    color: {
      description: 'Semantic color variant (primary, secondary, success, warning, danger, info)',
      control: { type: 'select' },
      options: ['primary', 'secondary', 'success', 'warning', 'danger', 'info'],
      table: {
        category: 'Appearance',
        type: { summary: 'string' },
        defaultValue: { summary: 'primary' },
      },
    },
    // Behavior
    disabled: {
      description: 'Disabled state (50% opacity, no pointer events)',
      control: { type: 'boolean' },
      table: {
        category: 'Behavior',
        type: { summary: 'boolean' },
        defaultValue: { summary: 'false' },
      },
    },
    // Accessibility
    ariaLabel: {
      description: 'Accessibility label (use for informative icons, omit for decorative)',
      control: { type: 'text' },
      table: {
        category: 'Accessibility',
        type: { summary: 'string' },
        defaultValue: { summary: '' },
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
      ${icon({ name: 'search', size: 'small' })}
      <span style="font-size: var(--font-size-0); color: var(--gray-600);">small (16px)</span>
      ${icon({ name: 'search', size: 'medium' })}
      <span style="font-size: var(--font-size-0); color: var(--gray-600);">medium (20px)</span>
      ${icon({ name: 'search', size: 'large' })}
      <span style="font-size: var(--font-size-0); color: var(--gray-600);">large (24px)</span>
      ${icon({ name: 'search', size: 'xlarge' })}
      <span style="font-size: var(--font-size-0); color: var(--gray-600);">xlarge (32px)</span>
    </div>
  `,
};

export const AllColors = {
  render: () => `
    <div style="display: flex; gap: var(--size-6); align-items: center;">
      ${icon({ name: 'check', size: 'xlarge', color: 'primary' })}
      <span>Primary</span>
      ${icon({ name: 'check', size: 'xlarge', color: 'secondary' })}
      <span>Secondary</span>
      ${icon({ name: 'check', size: 'xlarge', color: 'success' })}
      <span>Success</span>
      ${icon({ name: 'check', size: 'xlarge', color: 'warning' })}
      <span>Warning</span>
      ${icon({ name: 'check', size: 'xlarge', color: 'danger' })}
      <span>Danger</span>
      ${icon({ name: 'check', size: 'xlarge', color: 'info' })}
      <span>Info</span>
    </div>
  `,
};

export const AllStates = {
  render: () => `
    <div style="display: flex; gap: var(--size-6); align-items: center;">
      ${icon({ name: 'search', size: 'xlarge', disabled: false })}
      <span style="font-size: var(--font-size-1); color: var(--gray-600);">Normal</span>
      ${icon({ name: 'search', size: 'xlarge', disabled: true })}
      <span style="font-size: var(--font-size-1); color: var(--gray-600);">Disabled (50% opacity)</span>
    </div>
  `,
};

export const AllIcons = {
  name: 'All Icons (Categorized)',
  render: () => {
    const categories = iconsList.categories || {};
    const used = new Set();
    Object.values(categories).forEach((arr) => arr.forEach((i) => used.add(i)));
    const others = iconsList.all.filter((i) => !used.has(i));
    return `
      <div style="display:flex; flex-direction:column; gap:var(--size-8);">
        ${Object.entries(categories)
          .map(
            ([key, list]) => `
          <section>
            <h3 style="margin:0 0 var(--size-4); font-size: var(--font-size-3);">${key.charAt(0).toUpperCase() + key.slice(1)}</h3>
            <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(120px,1fr)); gap:var(--size-4);">
              ${list
                .map(
                  (name) => `
                <div style="display:flex; flex-direction:column; align-items:center; gap:var(--size-2); padding:var(--size-3); border:1px solid var(--gray-200); border-radius: var(--radius-2);">
                  ${icon({ name, size: 'large' })}
                  <span style="font-size: var(--font-size--1); text-align:center;">${name.replace('icon-', '')}</span>
                </div>
              `
                )
                .join('')}
            </div>
          </section>
        `
          )
          .join('')}
        <section>
          <h3 style="margin:0 0 var(--size-4); font-size: var(--font-size-3);">Others</h3>
          <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(120px,1fr)); gap:var(--size-4);">
            ${others
              .map(
                (name) => `
              <div style="display:flex; flex-direction:column; align-items:center; gap:var(--size-2); padding:var(--size-3); border:1px solid var(--gray-100); border-radius: var(--radius-2);">
                ${icon({ name, size: 'large' })}
                <span style="font-size: var(--font-size--1); text-align:center;">${name.replace('icon-', '')}</span>
              </div>
            `
              )
              .join('')}
          </div>
        </section>
      </div>
    `;
  },
};

export const UseCases = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-8);">
      <div>
        <h3 style="margin: 0 0 var(--size-4) 0; font-size: var(--size-4);">Search & Navigation</h3>
        <div style="display: flex; gap: var(--size-6); align-items: center;">
          ${icon({ name: 'search', size: 'medium' })}
          ${icon({ name: 'pin-map', size: 'medium' })}
          ${icon({ name: 'arrow-left', size: 'medium' })}
          ${icon({ name: 'arrow-right', size: 'medium' })}
        </div>
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-4) 0; font-size: var(--size-4);">Actions</h3>
        <div style="display: flex; gap: var(--size-6); align-items: center;">
          ${icon({ name: 'check', size: 'medium', color: 'success' })}
          ${icon({ name: 'close', size: 'medium', color: 'danger' })}
          ${icon({ name: 'edit', size: 'medium' })}
          ${icon({ name: 'bin', size: 'medium' })}
          ${icon({ name: 'share', size: 'medium', color: 'info' })}
        </div>
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-4) 0; font-size: var(--size-4);">Form Controls</h3>
        <div style="display: flex; gap: var(--size-6); align-items: center;">
          ${icon({ name: 'checkbox', size: 'medium' })}
          ${icon({ name: 'checkbox-checked', size: 'medium', color: 'success' })}
          ${icon({ name: 'radio-unselected', size: 'medium' })}
          ${icon({ name: 'radio-selected', size: 'medium', color: 'success' })}
        </div>
      </div>
    </div>
  `,
};

export default settings;
