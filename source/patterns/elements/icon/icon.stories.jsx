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

**Key Features:**
- Icon fonts defined in \`source/props/icons.css\` (generated from SVGs)
- Organized by category: Generic, Mobile, Tutoffice, Social, Tools, Univers, Ad, POI
- 4 sizes: small (16px), medium (20px), large (24px), xlarge (32px)
- Supports custom colors via CSS color property
- Accessibility: aria-label for informative icons, aria-hidden for decorative
- Icon names without "icon-" prefix in component prop

**Usage:**
- Use \`@elements/icon/icon.twig\` component for controllable icons
- Icon name prop: \`search\`, \`check\`, \`poi-hotel\` (without "icon-" prefix)
- All icon mappings centralized in \`source/props/icons.css\``,
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
      description: 'Custom color (CSS color value or design token)',
      control: { type: 'color' },
      table: {
        category: 'Appearance',
        type: { summary: 'string' },
        defaultValue: { summary: 'inherit' },
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
      ${icon({ name: 'check', size: 'xlarge', color: 'var(--bnp-green)' })}
      <span>Success (green)</span>
      ${icon({ name: 'close', size: 'xlarge', color: 'var(--red-600)' })}
      <span>Danger (red)</span>
      ${icon({ name: 'infos', size: 'xlarge', color: 'var(--blue-500)' })}
      <span>Info (blue)</span>
      ${icon({ name: 'help', size: 'xlarge', color: 'var(--amber-500)' })}
      <span>Warning (amber)</span>
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
          ${icon({ name: 'check', size: 'medium', color: 'var(--bnp-green)' })}
          ${icon({ name: 'close', size: 'medium', color: 'var(--red-600)' })}
          ${icon({ name: 'edit', size: 'medium' })}
          ${icon({ name: 'bin', size: 'medium' })}
          ${icon({ name: 'share', size: 'medium' })}
        </div>
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-4) 0; font-size: var(--size-4);">Form Controls</h3>
        <div style="display: flex; gap: var(--size-6); align-items: center;">
          ${icon({ name: 'checkbox', size: 'medium' })}
          ${icon({ name: 'checkbox-checked', size: 'medium', color: 'var(--bnp-green)' })}
          ${icon({ name: 'radio-unselected', size: 'medium' })}
          ${icon({ name: 'radio-selected', size: 'medium', color: 'var(--bnp-green)' })}
        </div>
      </div>
    </div>
  `,
};

export default settings;
