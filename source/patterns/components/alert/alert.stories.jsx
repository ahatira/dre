import alertTwig from './alert.twig';
import alertData from './alert.yml';
import './alert.css';

export default {
  title: 'Components/Alert',
  tags: ['autodocs'],
  render: (args) => alertTwig(args),
  argTypes: {
    variant: {
      description: 'Semantic variant defining color scheme and ARIA severity',
      control: { type: 'select' },
      options: ['info', 'success', 'warning', 'error'],
      table: {
        category: 'Appearance',
        type: { summary: 'string' },
        defaultValue: { summary: 'info' },
      },
    },

    title: {
      description: 'Optional alert title (HTML allowed)',
      control: { type: 'text' },
      table: {
        category: 'Content',
        type: { summary: 'string (HTML)' },
        defaultValue: { summary: '' },
      },
    },

    message: {
      description: 'Alert message content (HTML allowed)',
      control: { type: 'text' },
      table: {
        category: 'Content',
        type: { summary: 'string (HTML)' },
        defaultValue: { summary: '' },
      },
    },

    dismissible: {
      description: 'Show close button with dismiss behavior',
      control: { type: 'boolean' },
      table: {
        category: 'Behavior',
        type: { summary: 'boolean' },
        defaultValue: { summary: false },
      },
    },

    compact: {
      description: 'Reduce padding for dense layouts',
      control: { type: 'boolean' },
      table: {
        category: 'Appearance',
        type: { summary: 'boolean' },
        defaultValue: { summary: false },
      },
    },
  },
};

// ============================================
// BASIC STORIES
// ============================================

export const Default = {
  name: 'Default (Info)',
  args: alertData,
};

export const AllVariants = {
  name: 'All Variants',
  render: () => {
    const variants = [
      {
        variant: 'info',
        title: '<strong>Information</strong>',
        message: '5 nouveaux biens correspondent à votre recherche sauvegardée à Paris 15e.',
      },
      {
        variant: 'success',
        title: '<strong>Visite confirmée</strong>',
        message:
          'Votre rendez-vous pour visiter le bien au 42 rue de Vaugirard est fixé au 20 janvier à 14h.',
      },
      {
        variant: 'warning',
        title: '<strong>Offre expirante</strong>',
        message: 'Votre offre pour le bien situé au 12 avenue Montaigne expire dans 24 heures.',
      },
      {
        variant: 'error',
        title: '<strong>Erreur de téléchargement</strong>',
        message:
          'Impossible de télécharger le diagnostic énergétique. Veuillez réessayer ultérieurement.',
      },
    ];

    return variants
      .map((v) => alertTwig(v))
      .join('<div style="margin-bottom: var(--size-4);"></div>');
  },
};

export const WithoutTitle = {
  name: 'Without Title',
  render: () => {
    const variants = [
      {
        variant: 'info',
        message: 'Nouvelle fonctionnalité disponible : recherche par plan interactif.',
      },
      {
        variant: 'success',
        message: 'Le bien a été ajouté à votre liste de favoris.',
      },
      {
        variant: 'warning',
        message:
          'Ce bien a reçu plusieurs offres. Nous vous conseillons de faire votre proposition rapidement.',
      },
      {
        variant: 'error',
        message: 'Votre session a expiré. Veuillez vous reconnecter.',
      },
    ];

    return variants
      .map((v) => alertTwig(v))
      .join('<div style="margin-bottom: var(--size-4);"></div>');
  },
};

export const Dismissible = {
  name: 'Dismissible Alerts',
  render: () => {
    const alerts = [
      {
        variant: 'info',
        title: '<strong>Astuce</strong>',
        message: 'Activez les notifications pour être alerté en temps réel des nouveaux biens.',
        dismissible: true,
      },
      {
        variant: 'success',
        message: 'Document téléchargé : Compromis de vente - Appartement 75015.pdf',
        dismissible: true,
      },
      {
        variant: 'warning',
        title: '<strong>Dernier créneau</strong>',
        message: "Plus qu'un seul créneau de visite disponible pour ce bien.",
        dismissible: true,
      },
    ];

    return alerts
      .map((a) => alertTwig(a))
      .join('<div style="margin-bottom: var(--size-4);"></div>');
  },
};

export const Compact = {
  name: 'Compact Size',
  render: () => {
    const alerts = [
      {
        variant: 'info',
        message: 'Version compacte pour les espaces restreints (sidebar, modales).',
        compact: true,
      },
      {
        variant: 'success',
        title: '<strong>Sauvegardé</strong>',
        message: 'Votre recherche a été enregistrée.',
        compact: true,
        dismissible: true,
      },
    ];

    return alerts
      .map((a) => alertTwig(a))
      .join('<div style="margin-bottom: var(--size-3);"></div>');
  },
};

export const RichContent = {
  name: 'Rich HTML Content',
  args: {
    variant: 'info',
    title: '<strong>Nouvelle alerte immobilière</strong>',
    message: `
      <p>3 biens correspondent à vos critères :</p>
      <ul>
        <li><a href="/bien/1">Appartement 3 pièces - 75015 Paris</a> - 450 000 €</li>
        <li><a href="/bien/2">Studio rénové - 75014 Paris</a> - 280 000 €</li>
        <li><a href="/bien/3">Duplex avec terrasse - 75016 Paris</a> - 680 000 €</li>
      </ul>
      <p><a href="/recherche/123">Voir tous les résultats</a></p>
    `,
    dismissible: true,
  },
};

export const LongContent = {
  name: 'Long Content',
  args: {
    variant: 'warning',
    title: '<strong>Conditions générales de vente mises à jour</strong>',
    message: `
      Nous avons mis à jour nos conditions générales de vente pour mieux protéger vos données personnelles 
      et clarifier les modalités de transaction immobilière. Les principales modifications concernent :
      <ul>
        <li>La protection des données bancaires lors des transactions</li>
        <li>Les délais de rétractation pour les offres d'achat</li>
        <li>Les conditions d'annulation des visites de biens</li>
        <li>La gestion des litiges et réclamations</li>
      </ul>
      Nous vous invitons à prendre connaissance de ces nouvelles conditions avant votre prochaine transaction.
      <a href="/cgv">Lire les conditions complètes</a>
    `,
    dismissible: true,
  },
};
