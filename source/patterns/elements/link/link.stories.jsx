import iconsRegistry from '../../documentation/icons-registry.json';
import linkTwig from './link.twig';
import data from './link.yml';

const settings = {
  title: 'Elements/Link',
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component: `Semantic text link with optional icon and variant colors. Supports underline control, external target handling, and focus-visible accessibility.`,
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
      options: [null, ...iconsRegistry.names],
      table: {
        category: 'Content',
        type: { summary: 'string' },
        defaultValue: { summary: '' },
      },
    },
    // Appearance
    color: {
      description:
        'Link color variant: semantic colors for navigation, CTAs, and status indicators. Default (no class) uses current text color.',
      control: { type: 'select' },
      options: [
        null,
        'primary',
        'secondary',
        'gold',
        'info',
        'warning',
        'success',
        'danger',
        'dark',
        'light',
      ],
      table: {
        category: 'Appearance',
        type: {
          summary:
            'null | primary | secondary | gold | info | warning | success | danger | dark | light',
        },
        defaultValue: { summary: 'null (currentColor)' },
      },
    },
    size: {
      description:
        'Link size variant: adapt for hierarchy, accessibility, and context. Default (no class) uses md (16px).',
      control: { type: 'select' },
      options: [null, 'xs', 'sm', 'md', 'lg', 'xl', 'xxl'],
      table: {
        category: 'Appearance',
        type: { summary: 'null | xs | sm | md | lg | xl | xxl' },
        defaultValue: { summary: 'null (md)' },
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

// Stories
export const Default = {
  render: (args) => linkTwig(args),
  args: { ...data },
};

/**
 * 10 semantic colors: Default, Primary, Secondary, Gold, Info, Warning, Success, Danger, Dark, Light.
 * Each color represents a specific context (CTAs, status, navigation, etc.).
 */
export const Colors = {
  render: () => {
    const colors = [
      { name: 'Default', color: null, text: 'Voir tous les biens', desc: 'Inherited color' },
      { name: 'Primary', color: 'primary', text: 'Planifier une visite', desc: 'Brand actions' },
      { name: 'Secondary', color: 'secondary', text: 'Contacter un conseiller', desc: 'Alternative actions' },
      { name: 'Gold', color: 'gold', text: 'Biens premium', desc: 'Luxury properties' },
      { name: 'Info', color: 'info', text: 'En savoir plus', desc: 'Informational' },
      { name: 'Warning', color: 'warning', text: 'Offre limitée', desc: 'Time-sensitive' },
      { name: 'Success', color: 'success', text: 'Bien disponible', desc: 'Available status' },
      { name: 'Danger', color: 'danger', text: 'Bien vendu', desc: 'Sold/unavailable' },
      { name: 'Dark', color: 'dark', text: 'Navigation', desc: 'Light backgrounds' },
    ];
    
    return `
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: var(--size-4);">
        ${colors.map(({ name, color, text, desc }) => `
          <div>
            <strong style="display: block; margin-bottom: var(--size-2); font-size: var(--font-size-1);">${name}</strong>
            ${linkTwig({ text, url: '#', color })}
            <small style="display: block; margin-top: var(--size-1); color: var(--gray-500);">${desc}</small>
          </div>
        `).join('')}
        <div style="background: var(--gray-800); padding: var(--size-3); border-radius: var(--radius-2);">
          <strong style="display: block; margin-bottom: var(--size-2); font-size: var(--font-size-1); color: var(--white);">Light</strong>
          ${linkTwig({ text: 'Pied de page', url: '#', color: 'light' })}
          <small style="display: block; margin-top: var(--size-1); color: var(--gray-300);">Dark backgrounds</small>
        </div>
      </div>
    `;
  },
};

/**
 * 6 size variants (xs to xxl) for hierarchy and context adaptation.
 */
export const Sizes = {
  render: () => {
    const sizes = [
      { name: 'xs', px: '12px', context: 'Footnotes' },
      { name: 'sm', px: '14px', context: 'Secondary nav' },
      { name: 'md', px: '16px', context: 'Body (default)' },
      { name: 'lg', px: '18px', context: 'Features' },
      { name: 'xl', px: '22px', context: 'Hero sections' },
      { name: 'xxl', px: '24px', context: 'Major CTAs' },
    ];
    
    return `
      <div style="display: flex; flex-direction: column; gap: var(--size-4);">
        ${sizes.map(({ name, px, context }) => `
          <div>
            ${linkTwig({ text: `Lien ${name.toUpperCase()} (${px})`, url: '#', size: name, color: 'primary' })}
            <small style="display: block; margin-top: var(--size-1); color: var(--gray-500);">${context}</small>
          </div>
        `).join('')}
      </div>
    `;
  },
};

/**
 * Icon integration: left/right positioning, external links, downloads.
 */
export const Icons = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-3);">
      ${linkTwig({ text: 'Suivant', url: '#', icon: 'arrow-right', underline: false, color: 'primary' })}
      ${linkTwig({ text: 'Précédent', url: '#', icon: 'arrow-left', iconPosition: 'left', underline: false, color: 'primary' })}
      ${linkTwig({ text: 'Site externe', url: 'https://example.com', target: '_blank', icon: 'external-link', underline: false, color: 'primary' })}
      ${linkTwig({ text: 'Télécharger PDF', url: '#', icon: 'download', underline: false, color: 'primary' })}
      ${linkTwig({ text: 'Appeler', url: 'tel:+33123456789', icon: 'phone', iconPosition: 'left', underline: false, color: 'primary' })}
    </div>
  `,
};

/**
 * States: underline control, disabled state, interactive variations.
 */
export const States = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-3);">
      ${linkTwig({ text: 'Avec soulignement (défaut)', url: '#', color: 'primary', underline: true })}
      ${linkTwig({ text: 'Sans soulignement', url: '#', color: 'primary', underline: false })}
      ${linkTwig({ text: 'État désactivé', url: '#', disabled: true })}
    </div>
  `,
};

export default settings;
