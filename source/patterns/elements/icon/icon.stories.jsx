import iconsList from '../../documentation/icons-list.json';
import icon from './icon.twig';
import data from './icon.yml';

const settings = {
  title: 'Elements/Icon',
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component: `Système d'icônes (bnpre-icons + bnpre-icons-poi). 
Groupement visuel par catégorie selon la maquette: Generic, Mobile only, Tutoffice, Social media, Tools, Univers, Ad, Autres.
Toutes les classes proviennent de \`source/props/icons.css\` (générées depuis le SVG).`,
      },
    },
  },
  argTypes: {
    name: {
      description: 'Nom de classe icone',
      control: { type: 'select' },
      options: iconsList.all,
    },
    size: {
      description: 'Taille',
      control: { type: 'select' },
      options: ['small', 'medium', 'large', 'xlarge'],
    },
    color: {
      description: 'Couleur (token ou valeur CSS)',
      control: { type: 'color' },
    },
    disabled: {
      description: 'État disabled',
      control: { type: 'boolean' },
    },
    ariaLabel: {
      description: 'Label accessibilité',
      control: { type: 'text' },
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

export const Categories = {
  name: 'Galerie catégorisée',
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
          <h3 style="margin:0 0 var(--size-4); font-size: var(--font-size-3);">Autres</h3>
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

// Exemples spécifiques conservés
export const CheckboxIcons = {
  name: 'Exemple: Checkbox',
  render: () => `
    <div style="display:flex; gap:var(--size-6); align-items:center;">
      ${icon({ name: 'icon-checkbox', size: 'medium' })}
      ${icon({ name: 'icon-checkbox-checked', size: 'medium', color: 'var(--bnp-green)' })}
      ${icon({ name: 'icon-radio-unselected', size: 'medium' })}
      ${icon({ name: 'icon-radio-selected', size: 'medium', color: 'var(--bnp-green)' })}
    </div>
  `,
};

export default settings;
