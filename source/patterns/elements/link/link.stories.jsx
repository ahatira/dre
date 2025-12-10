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

export const Default = {
  render: (args) => linkTwig(args),
  args: { ...data },
};

export const Colors = {
  render: () => `
    <p style="margin-bottom: var(--size-4); color: var(--gray-700); font-size: var(--font-size-1);">All semantic color variants for links. Default (no class) uses current text color. Use semantic colors for navigation, CTAs, and status indicators in real estate applications.</p>
    <div style="display: flex; flex-direction: column; gap: var(--size-4);">
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Default (currentColor, no class)</p>
        ${linkTwig({ text: 'Consulter les détails du bien', url: '/property/details' })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Primary (brand green)</p>
        ${linkTwig({ text: 'Planifier une visite immobilière', url: '#', color: 'primary' })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Secondary (brand pink)</p>
        ${linkTwig({ text: 'Contacter un conseiller', url: '#', color: 'secondary' })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Gold (premium properties)</p>
        ${linkTwig({ text: 'Découvrir biens premium', url: '#', color: 'gold' })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Info (informational content)</p>
        ${linkTwig({ text: 'Informations sur le bien', url: '#', color: 'info' })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Warning (limited time)</p>
        ${linkTwig({ text: 'Offre à durée limitée', url: '#', color: 'warning' })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Success (available)</p>
        ${linkTwig({ text: 'Bien disponible immédiatement', url: '#', color: 'success' })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Danger (sold/unavailable)</p>
        ${linkTwig({ text: 'Bien vendu', url: '#', color: 'danger' })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Dark (for light backgrounds)</p>
        ${linkTwig({ text: 'Voir tous les biens', url: '#', color: 'dark' })}
      </div>
      <div style="background-color: var(--gray-800); padding: var(--size-4); border-radius: var(--radius-2);">
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--white);">Light (for dark backgrounds)</p>
        ${linkTwig({ text: 'Navigation pied de page', url: '#', color: 'light' })}
      </div>
    </div>
  `,
};

export const WithIcons = {
  render: () => `
    <p style="margin-bottom: var(--size-4); color: var(--gray-700); font-size: var(--font-size-1);">Links with icons for navigation, CTAs, and external resources. Demonstrates icon positioning, underline control, and disabled state.</p>
    <div style="display: flex; flex-direction: column; gap: var(--size-4);">
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">With underline (default, hover removes it)</p>
        ${linkTwig({ text: 'Bien immobilier avec soulignement', url: '#', color: 'primary', underline: true })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Without underline</p>
        ${linkTwig({ text: 'Bien immobilier sans soulignement', url: '#', color: 'primary', underline: false })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">With icon right (navigation)</p>
        ${linkTwig({ text: 'Annonce suivante', url: '#', icon: 'arrow-right', iconPosition: 'right', color: 'primary', underline: false })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">With icon left (back navigation)</p>
        ${linkTwig({ text: 'Annonce précédente', url: '#', icon: 'arrow-left', iconPosition: 'left', color: 'primary', underline: false })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">External link (target="_blank" with security rel)</p>
        ${linkTwig({ text: 'Portail immobilier externe', url: 'https://example.com', target: '_blank', color: 'primary', icon: 'external-link' })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Disabled (renders as span with aria-disabled)</p>
        ${linkTwig({ text: 'Bien indisponible', url: '#', disabled: true })}
      </div>
    </div>
  `,
};

export const UseCases = {
  render: () => `
    <p style="margin-bottom: var(--size-4); color: var(--gray-700); font-size: var(--font-size-1);">Real estate use cases: inline links, navigation with icons, external resources, different sizes, and footer links.</p>
    <div style="display: flex; flex-direction: column; gap: var(--size-6);">
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-2);">Lien dans un paragraphe</h3>
        <p style="max-width: 600px; color: var(--gray-700); line-height: var(--leading-normal);">
          Découvrez notre portfolio d'immeubles de bureaux modernes à Paris. 
          ${linkTwig({ text: 'En savoir plus sur les propriétés commerciales', url: '/properties/commercial', color: 'primary' })} 
          et trouvez l'espace parfait pour votre entreprise.
        </p>
      </div>

      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-2);">Navigation avec icône</h3>
        <div style="display: flex; flex-direction: column; gap: var(--size-3);">
          ${linkTwig({ text: 'Annonce suivante', url: '#', icon: 'arrow-right', iconPosition: 'right', underline: false, color: 'primary' })}
          ${linkTwig({ text: 'Annonce précédente', url: '#', icon: 'arrow-left', iconPosition: 'left', underline: false, color: 'primary' })}
        </div>
      </div>

      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-2);">Tailles variées (hiérarchie)</h3>
        <div style="display: flex; flex-direction: column; gap: var(--size-3);">
          ${linkTwig({ text: 'Petit lien (footnote)', url: '#', size: 'xs', color: 'primary' })}
          ${linkTwig({ text: 'Lien standard (body)', url: '#', size: 'md', color: 'primary' })}
          ${linkTwig({ text: 'Grand lien (feature)', url: '#', size: 'lg', color: 'primary' })}
          ${linkTwig({ text: 'Très grand lien (hero)', url: '#', size: 'xl', color: 'primary' })}
        </div>
      </div>

      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-2);">Ressource externe</h3>
        ${linkTwig({ text: 'Voir le bien sur portail externe', url: 'https://example.com', target: '_blank', color: 'primary', icon: 'external-link' })}
      </div>

      <div style="background-color: var(--gray-800); padding: var(--size-6); border-radius: var(--radius-2);">
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-2); color: var(--white);">Lien sur fond sombre</h3>
        <div style="display: flex; flex-direction: column; gap: var(--size-3);">
          ${linkTwig({ text: 'Contactez notre équipe immobilière', url: '/contact', color: 'light' })}
          ${linkTwig({ text: 'Politique de confidentialité', url: '/privacy', color: 'light', size: 'sm' })}
        </div>
      </div>
    </div>
  `,
};

export default settings;
