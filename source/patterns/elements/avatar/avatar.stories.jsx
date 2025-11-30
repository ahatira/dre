import avatarTwig from './avatar.twig';
import data from './avatar.yml';
import sizesList from '../../documentation/sizes-list.json';
import variantsList from '../../documentation/variants-list.json';

export default {
  title: 'Elements/Avatar',
  tags: ['autodocs'],
  render: (args) => avatarTwig(args),
  args: data,
  parameters: {
    docs: {
      description: {
        component:
          'Avatar affichant une photo, des initiales ou une icône par défaut.\n\n' +
          '- Modes: image (`src`), initiales (`initials`), fallback icône (si `src` et `initials` sont vides).\n' +
          '- Tailles: xs, sm, md (défaut), lg, xl — valeurs exactes via tokens.\n' +
          '- Formes: circle (défaut), square, rounded.\n' +
          '- Statut: `status` = online | offline | busy (badge en bas à droite).\n' +
          '- Bordure: `bordered` ajoute un liseré blanc pour fond sombre.\n' +
          '- Accessibilité: `alt` requis si image; les initiales servent de contenu textuel; focus visible quand `clickable` est vrai.\n' +
          "- Rendu minimal: la classe de base applique les styles par défaut; les modificateurs n'apparaissent que si l'option change du défaut.",
      },
    },
  },
  argTypes: {
    src: {
      control: 'text',
      description: "URL de l'image avatar",
      table: { category: 'Content' },
    },
    alt: {
      control: 'text',
      description: 'Texte alternatif (requis si image)',
      table: { category: 'Accessibility' },
    },
    initials: {
      control: 'text',
      description: 'Initiales (2 lettres max, fallback si pas d’image)',
      table: { category: 'Content' },
    },
    size: {
      control: 'select',
      options: sizesList.avatar.values,
      description: `Taille (md défaut) → ${sizesList.avatar.values.map(v => v + ':' + sizesList.avatar.tokens[v]).join(' ')}`,
      table: { category: 'Appearance' },
    },
    shape: {
      control: 'select',
      options: variantsList.shape.avatar,
      description: 'Forme (circle défaut)',
      table: { category: 'Appearance' },
    },
    status: {
      control: 'select',
      options: ['', 'online', 'offline', 'busy'],
      description: 'Badge de statut (laisser vide pour aucun)',
      table: { category: 'Appearance' },
    },
    bordered: {
      control: 'boolean',
      description: 'Ajoute une bordure blanche (fond sombre)',
      table: { category: 'Appearance' },
    },
    clickable: {
      control: 'boolean',
      description: 'Active hover + focus visible',
      table: { category: 'Behavior' },
    },
    href: {
      control: 'text',
      description: 'URL (transforme en lien <a>)',
      table: { category: 'Link' },
    },
    gender: {
      control: 'select',
      options: ['male', 'female'],
      description: 'Icône fallback selon le genre (image manquante et sans initiales)',
      table: { category: 'Appearance' },
    },
  },
};

export const Default = { args: { ...data } };

export const Initials = {
  render: () => {
    const sizes = sizesList.avatar.values;
    const shapes = variantsList.shape.avatar;
    const gridCols = sizes.length + 1; // 1 for row labels
    const headRow = [
      '<div class="cell cell--label"></div>',
      ...sizes.map(s => `<div class="cell cell--label">${s.toUpperCase()}</div>`),
    ].join('');
    const rows = shapes
      .map(shape => {
        const label = `<div class=\"cell cell--label\">${shape}</div>`;
        const cells = sizes
          .map(s => `<div class=\"cell\">${avatarTwig({ src: '', initials: 'JD', size: s, shape, alt: `Initials ${shape} ${s}` })}</div>`)
          .join('');
        return label + cells;
      })
      .join('');

    return `
      <div style="display:grid;grid-template-columns:repeat(${gridCols}, auto);gap:12px;align-items:center;">
        <style>
          .cell--label{font:600 var(--font-size-0)/1 var(--font-sans);color:var(--ps-color-neutral-600);text-transform:capitalize}
          .cell{display:flex;align-items:center;justify-content:center}
        </style>
        ${headRow}
        ${rows}
      </div>
    `;
  },
};

export const FallbackIcon = {
  args: { ...data, src: '', initials: '', size: 'xs', gender: 'female' },
};

export const StatusVariants = {
  render: () => `
    <div style="display:flex;gap:1rem;align-items:center;">
      ${avatarTwig({ src: 'https://i.pravatar.cc/150?img=12', status: 'online', size: 'lg', alt: 'Online' })}
      ${avatarTwig({ src: 'https://i.pravatar.cc/150?img=12', status: 'offline', size: 'lg', alt: 'Offline' })}
      ${avatarTwig({ src: 'https://i.pravatar.cc/150?img=12', status: 'busy', size: 'lg', alt: 'Busy' })}
    </div>
  `,
};

export const AllSizes = {
  render: () => `
    <div style="display:flex;gap:1rem;align-items:center;">
      ${sizesList.avatar.values.map(s => avatarTwig({ src: 'https://i.pravatar.cc/150?img=12', size: s, alt: s.toUpperCase() })).join('')}
    </div>
  `,
};

export const AllShapes = {
  render: () => `
    <div style="display:flex;gap:1rem;align-items:center;">
      ${['circle','square','rounded'].map(shape => avatarTwig({ src: 'https://i.pravatar.cc/150?img=47', shape, size: 'lg', alt: shape })).join('')}
    </div>
  `,
};

export const Modes = {
  render: () => `
    <div style="display:flex;gap:1rem;align-items:center;">
      ${avatarTwig({ src: 'https://i.pravatar.cc/150?img=47', size: 'lg', alt: 'Image' })}
      ${avatarTwig({ initials: 'JD', src: '', size: 'lg', alt: 'Initials' })}
      ${avatarTwig({ src: '', initials: '', size: 'lg', alt: 'Icon Fallback' })}
    </div>
  `,
};

export const RoundedScaling = {
  render: () => `
    <div style="display:flex;gap:1rem;align-items:center;">
      ${sizesList.avatar.values.map(s => avatarTwig({ src: 'https://i.pravatar.cc/150?img=18', shape: 'rounded', size: s, alt: `Rounded ${s}` })).join('')}
    </div>
  `,
};
