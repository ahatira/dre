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
    site_name: {
      description: 'Nom du site/entreprise (optionnel)',
      control: { type: 'text' },
      table: {
        category: 'Content',
        type: { summary: 'string' },
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

export const Default = {
  render: (args) => logoTwig(args),
  args: data,
  parameters: {
    docs: {
      description: {
        story: 'Logo par défaut avec image seule',
      },
    },
  },
};

export const WithName = {
  render: (args) => logoTwig(args),
  args: {
    ...data,
    site_name: 'BNP Paribas Real Estate',
  },
  parameters: {
    docs: {
      description: {
        story: "Logo avec nom d'entreprise affiché",
      },
    },
  },
};

export const WithSlogan = {
  render: (args) =>
    logoTwig({
      ...args,
      site_slogan: 'Real Estate for a Changing World',
    }),
  parameters: {
    docs: {
      description: {
        story: 'Logo avec slogan (ISO maquette Desktop avec slogan)',
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

export const LinkedWithSlogan = {
  render: (args) =>
    logoTwig({
      ...args,
      url: '/',
      rel: 'home',
      site_slogan: 'Real Estate for a Changing World',
    }),
  parameters: {
    docs: {
      description: {
        story: 'Logo cliquable avec slogan (configuration en-tête Drupal)',
      },
    },
  },
};
