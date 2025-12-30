import logoTwig from './logo.twig';
import data from './logo.yml';

export default {
  title: 'Components/Logo',
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component: `Logo de marque BNP Paribas Real Estate. Composant flexible basé sur les données : affiche l'image, le texte et le slogan si fournis.`,
      },
    },
  },
  argTypes: {
    image: {
      description: "Chemin de l'image du logo",
      control: { type: 'text' },
      table: {
        category: 'Content',
        type: { summary: 'string' },
        defaultValue: { summary: '/logo/logo.svg' },
      },
    },
    text: {
      description: 'Texte/label du logo (optionnel)',
      control: { type: 'text' },
      table: {
        category: 'Content',
        type: { summary: 'string' },
      },
    },
    slogan: {
      description: 'Slogan/tagline (optionnel, affiché si fourni)',
      control: { type: 'text' },
      table: {
        category: 'Content',
        type: { summary: 'string' },
      },
    },
    alt: {
      description: "Texte alternatif pour l'image",
      control: { type: 'text' },
      table: {
        category: 'Accessibility',
        type: { summary: 'string' },
        defaultValue: { summary: 'BNP Paribas Real Estate' },
      },
    },
    href: {
      description: 'URL du lien (optionnel)',
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
};

export const WithSlogan = {
  render: (args) =>
    logoTwig({
      ...args,
      slogan: 'Real Estate for a Changing World',
    }),
  parameters: {
    docs: {
      description: {
        story: 'Logo avec slogan affiché à côté',
      },
    },
  },
};

export const Linked = {
  render: (args) =>
    logoTwig({
      ...args,
      href: '/',
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
      href: '/',
      slogan: 'Real Estate for a Changing World',
    }),
  parameters: {
    docs: {
      description: {
        story: 'Logo cliquable avec slogan (configuration en-tête)',
      },
    },
  },
};
