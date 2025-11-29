import avatar from './avatar.twig';
import data from './avatar.yml';

export default {
  title: 'Elements/Avatar',
  tags: ['autodocs'],
  render: (args) => avatar(args),
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
    },
    alt: {
      control: 'text',
      description: 'Texte alternatif',
    },
    initials: {
      control: 'text',
      description: 'Initiales (2 lettres max)',
    },
    size: {
      control: 'select',
      options: ['xs', 'sm', 'md', 'lg', 'xl'],
      description: "Taille de l'avatar",
    },
    shape: {
      control: 'select',
      options: ['circle', 'square', 'rounded'],
      description: "Forme de l'avatar",
    },
    status: {
      control: 'select',
      options: ['', 'online', 'offline', 'busy'],
      description: 'Badge de statut',
    },
    bordered: {
      control: 'boolean',
      description: 'Bordure blanche',
    },
    clickable: {
      control: 'boolean',
      description: 'Cliquable avec hover',
    },
    href: {
      control: 'text',
      description: 'URL si cliquable',
    },
  },
};

export const Default = {
  args: { ...data },
};

export const Woman = {
  args: {
    ...data,
    src: 'https://i.pravatar.cc/150?img=47',
    alt: 'Jane Doe',
  },
};

export const Initials = {
  args: {
    ...data,
    src: '',
    initials: 'JD',
    size: 'lg',
    shape: 'rounded',
  },
};

export const Icon = {
  args: {
    ...data,
    src: '',
    initials: '',
    size: 'md',
  },
};

export const WithStatus = {
  args: {
    ...data,
    status: 'online',
  },
};

export const Clickable = {
  args: {
    ...data,
    clickable: true,
    href: '#',
  },
};

export const AllSizes = {
  render: () => `
    <div style="display: flex; gap: 1rem; align-items: center;">
      ${avatar({ src: 'https://i.pravatar.cc/150?img=12', size: 'xs', alt: 'XS' })}
      ${avatar({ src: 'https://i.pravatar.cc/150?img=12', size: 'sm', alt: 'SM' })}
      ${avatar({ src: 'https://i.pravatar.cc/150?img=12', size: 'md', alt: 'MD' })}
      ${avatar({ src: 'https://i.pravatar.cc/150?img=12', size: 'lg', alt: 'LG' })}
      ${avatar({ src: 'https://i.pravatar.cc/150?img=12', size: 'xl', alt: 'XL' })}
    </div>
  `,
};

export const AllShapes = {
  render: () => `
    <div style="display: flex; gap: 1rem; align-items: center;">
      ${avatar({ src: 'https://i.pravatar.cc/150?img=47', shape: 'circle', size: 'lg', alt: 'Circle' })}
      ${avatar({ src: 'https://i.pravatar.cc/150?img=47', shape: 'square', size: 'lg', alt: 'Square' })}
      ${avatar({ src: 'https://i.pravatar.cc/150?img=47', shape: 'rounded', size: 'lg', alt: 'Rounded' })}
    </div>
  `,
};

export const AllStatuses = {
  render: () => `
    <div style="display: flex; gap: 1rem; align-items: center;">
      ${avatar({ src: 'https://i.pravatar.cc/150?img=12', status: 'online', size: 'lg', alt: 'Online' })}
      ${avatar({ src: 'https://i.pravatar.cc/150?img=12', status: 'offline', size: 'lg', alt: 'Offline' })}
      ${avatar({ src: 'https://i.pravatar.cc/150?img=12', status: 'busy', size: 'lg', alt: 'Busy' })}
    </div>
  `,
};

export const AllTypes = {
  render: () => `
    <div style="display: flex; gap: 1rem; align-items: center;">
      ${avatar({ src: 'https://i.pravatar.cc/150?img=47', size: 'lg', alt: 'Image' })}
      ${avatar({ initials: 'JD', size: 'lg', src: '' })}
      ${avatar({ src: '', initials: '', size: 'lg', alt: 'Icon fallback' })}
    </div>
  `,
};
