import logoTwig from './logo.twig';
import data from './logo.yml';

export default {
  title: 'Components/Logo',
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component: `Logo de marque BNP Paribas Real Estate. Composant flexible basé sur les données Drupal (site_logo, site_name, site_slogan).`,
      },
    },
  },
  argTypes: {
    site_logo: {
      description: "Chemin/URI de l'image du logo",
      control: { type: 'text' },
      table: {
        category: 'Content',
        type: { summary: 'string' },
        defaultValue: { summary: '/logo/logo.svg' },
      },
    },
    site_slogan: {
      description: 'Slogan du site/entreprise (optionnel)',
      control: { type: 'text' },
      table: {
        category: 'Content',
        type: { summary: 'string' },
      },
    },
    url: {
      description: "URL du lien (par ex. path('<front>') en Drupal)",
      control: { type: 'text' },
      table: {
        category: 'Link',
        type: { summary: 'string' },
      },
    },
    rel: {
      description: 'Attribut rel du lien (par ex. "home")',
      control: { type: 'text' },
      table: {
        category: 'Link',
        type: { summary: 'string' },
      },
    },
  },
  args: data,
};

export const Desktop = {
  render: (args) => logoTwig(args),
  args: data,
  parameters: {
    docs: {
      description: {
        story: 'ISO maquette Desktop - Logo seul',
      },
    },
  },
};

export const DesktopWithSlogan = {
  render: (args) => logoTwig(args),
  args: {
    ...data,
    site_slogan: 'Real Estate for a Changing World',
  },
  parameters: {
    docs: {
      description: {
        story: 'ISO maquette Desktop avec Slogan - Logo + Slogan centré',
      },
    },
  },
};

export const Mobile = {
  render: (args) => logoTwig(args),
  args: data,
  parameters: {
    viewport: {
      defaultViewport: 'mobile1',
    },
    docs: {
      description: {
        story: 'ISO maquette Mobile - Logo compact',
      },
    },
  },
};

export const LogoOnly = {
  render: (args) => logoTwig(args),
  args: data,
  parameters: {
    docs: {
      description: {
        story: 'Logo avec image seule (sans texte ni slogan)',
      },
    },
  },
};

export const LinkedLogo = {
  render: (args) =>
    logoTwig({
      ...args,
      url: '/',
      rel: 'home',
    }),
  parameters: {
    docs: {
      description: {
        story: "Logo cliquable vers la page d'accueil",
      },
    },
  },
};
