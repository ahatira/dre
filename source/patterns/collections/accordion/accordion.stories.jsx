import accordionTwig from './accordion.twig';
import data from './accordion.yml';

const settings = {
  title: 'Collections/Accordion',
  tags: ['autodocs'],
  render: (args) => accordionTwig(args),
  args: data.args || data,
  parameters: {
    docs: {
      description: {
        component:
          'Orchestrates multiple Collapse elements with optional single-open coordination. Collection-level component managing group behavior.',
      },
    },
  },
  argTypes: {
    items: {
      description: 'Array of collapse items. Each item: { id?, title, content?, expanded? }',
      table: {
        category: 'Content',
        type: { summary: 'Array<{ id?, title, content?, expanded? }>' },
      },
    },
    single_open: {
      description: 'When true, only one section can be expanded at a time',
      control: { type: 'boolean' },
      table: {
        category: 'Behavior',
        type: { summary: 'boolean' },
        defaultValue: { summary: 'true' },
      },
    },
    variant: {
      description: 'Visual style variant',
      control: { type: 'select' },
      options: [
        'default',
        'flush',
        'primary',
        'secondary',
        'success',
        'warning',
        'danger',
        'info',
        'dark',
        'light',
      ],
      table: {
        category: 'Appearance',
        type: {
          summary:
            'default | flush | primary | secondary | success | warning | danger | info | dark | light',
        },
        defaultValue: { summary: 'default' },
      },
    },
    attributes: {
      description: 'Drupal attributes object for root element',
      table: {
        category: 'Layout',
        type: { summary: 'Drupal.Attribute' },
      },
    },
  },
};

export const Default = {
  render: (args) => accordionTwig(args),
  args: data.args || data,
};

export const SingleOpenMode = {
  name: 'Single Open Mode (Default)',
  render: (args) => accordionTwig({ ...args, single_open: true }),
  args: data.args || data,
};

export const MultipleOpenMode = {
  name: 'Multiple Open Mode',
  render: (args) => {
    const baseData = data.args || data;
    const multipleOpenItems = (baseData.items || []).map((item, idx) => ({
      ...item,
      expanded: idx < 2,
    }));
    return accordionTwig({ ...args, single_open: false, items: multipleOpenItems });
  },
  args: data.args || data,
};

export const ComposedWithAtoms = {
  name: 'Property Listing Example',
  render: (args) =>
    accordionTwig({
      items: [
        {
          title: 'Détails du bien immobilier',
          content:
            '<p><strong>Surface:</strong> 2 500 m² | <strong>Type:</strong> Bureaux modernes | <strong>Étages:</strong> 3-5 | <strong>État:</strong> Rénové HQE.</p>',
          expanded: true,
        },
        {
          title: 'Tarifs et conditions',
          content:
            '<p><strong>Loyer:</strong> 15 000 € HT/mois | <strong>Charges:</strong> 250 € HT/m²/an | <strong>Durée:</strong> 3-9 ans | <strong>Disponibilité:</strong> Janvier 2026.</p>',
        },
        {
          title: 'Informations de contact',
          content:
            '<p>Pour une visite, contactez notre équipe immobilière au <strong>+33 1 23 45 67 89</strong> ou <strong>commercial@bnpparibas-realestate.com</strong>. Visite sur rendez-vous uniquement.</p>',
        },
      ],
      ...args,
    }),
  args: data.args || data,
};

export default settings;
