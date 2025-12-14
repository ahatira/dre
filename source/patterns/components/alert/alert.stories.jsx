import iconsRegistry from '../../documentation/icons-registry.json';
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
      options: [
        'neutral',
        'primary',
        'success',
        'danger',
        'warning',
        'info',
        'gold',
        'light',
        'dark',
      ],
      table: {
        category: 'Appearance',
        type: { summary: 'string' },
        defaultValue: { summary: 'neutral' },
      },
    },

    content: {
      description: 'Alert message content (HTML allowed)',
      control: { type: 'text' },
      table: {
        category: 'Content',
        type: { summary: 'string (HTML)' },
        defaultValue: { summary: '' },
      },
    },

    icon: {
      description: 'Icon name (without icon- prefix) for leading icon',
      control: { type: 'select' },
      options: [null, ...iconsRegistry.names],
      table: {
        category: 'Appearance',
        type: { summary: 'string' },
        defaultValue: { summary: 'null' },
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
  },
};

// ============================================
// BASIC STORIES
// ============================================

export const Default = {
  name: 'Default (Neutral)',
  args: alertData,
};

export const AllVariants = {
  name: 'All Variants',
  render: () => {
    const variants = [
      {
        variant: 'neutral',
        label: 'Neutral',
        content:
          '<strong>Information générale :</strong> Le marché immobilier parisien reste dynamique avec une augmentation de 3% des transactions.',
      },
      {
        variant: 'primary',
        label: 'Primary',
        content:
          '<strong>Mise en avant BNP :</strong> Découvrez notre nouvelle offre exclusive pour les primo-accédants.',
      },
      {
        variant: 'success',
        label: 'Success',
        content:
          '<strong>Visite confirmée :</strong> Votre rendez-vous pour le bien au 42 rue de Vaugirard est fixé au 20 janvier à 14h.',
      },
      {
        variant: 'danger',
        label: 'Danger',
        content:
          "<strong>Offre expirée :</strong> Votre proposition pour le bien situé au 12 avenue Montaigne n'est plus valide.",
      },
      {
        variant: 'warning',
        label: 'Warning',
        content:
          '<strong>Dernière chance :</strong> Votre offre expire dans 24 heures. Nous vous conseillons de la renouveler.',
      },
      {
        variant: 'info',
        label: 'Info',
        content:
          '<strong>Nouvelle recherche :</strong> 5 nouveaux biens correspondent à vos critères à Paris 15e.',
      },
      {
        variant: 'gold',
        label: 'Gold',
        content:
          "<strong>Bien premium :</strong> Ce bien d'exception situé dans le Triangle d'Or est maintenant disponible.",
      },
      {
        variant: 'light',
        label: 'Light',
        content:
          '<strong>Astuce :</strong> Activez les notifications pour être alerté en temps réel des nouveaux biens.',
      },
      {
        variant: 'dark',
        label: 'Dark',
        content:
          '<strong>Maintenance programmée :</strong> Le service sera indisponible le 15 janvier de 2h à 4h.',
      },
    ];

    return variants
      .map((v) =>
        alertTwig({
          variant: v.variant,
          content: v.content,
        })
      )
      .join('<div style="margin-bottom: var(--size-4);"></div>');
  },
};

export const WithIcons = {
  name: 'With Icons',
  render: () => {
    const alerts = [
      {
        variant: 'neutral',
        icon: 'info',
        content:
          "<strong>Information :</strong> Les frais de notaire sont estimés à 8% du prix d'achat.",
      },
      {
        variant: 'success',
        icon: 'check',
        content:
          '<strong>Dossier complet :</strong> Tous vos documents ont été validés avec succès.',
      },
      {
        variant: 'danger',
        icon: 'alert-circle',
        content:
          "<strong>Document manquant :</strong> Veuillez fournir votre dernier avis d'imposition.",
      },
      {
        variant: 'warning',
        icon: 'alert-triangle',
        content:
          '<strong>Attention :</strong> Ce bien a reçu plusieurs offres. Agissez rapidement.',
      },
      {
        variant: 'info',
        icon: 'info',
        content:
          '<strong>Nouvelle fonctionnalité :</strong> Recherche par plan interactif maintenant disponible.',
      },
      {
        variant: 'gold',
        icon: 'star',
        content:
          "<strong>Bien d'exception :</strong> Hôtel particulier avec jardin de 500m² dans le 16e.",
      },
    ];

    return alerts
      .map((a) => alertTwig(a))
      .join('<div style="margin-bottom: var(--size-4);"></div>');
  },
};

export const Dismissible = {
  name: 'Dismissible Alerts',
  render: () => {
    const alerts = [
      {
        variant: 'neutral',
        content:
          '<strong>Cookie :</strong> Ce site utilise des cookies pour améliorer votre expérience.',
        dismissible: true,
      },
      {
        variant: 'success',
        icon: 'check',
        content: '<strong>Sauvegardé :</strong> Le bien a été ajouté à vos favoris.',
        dismissible: true,
      },
      {
        variant: 'warning',
        icon: 'alert-triangle',
        content: '<strong>Session :</strong> Votre session expirera dans 5 minutes.',
        dismissible: true,
      },
      {
        variant: 'info',
        icon: 'info',
        content: '<strong>Astuce :</strong> Créez une alerte e-mail pour ne manquer aucun bien.',
        dismissible: true,
      },
    ];

    return alerts
      .map((a) => alertTwig(a))
      .join('<div style="margin-bottom: var(--size-4);"></div>');
  },
};

export const RichContent = {
  name: 'Rich HTML Content',
  args: {
    variant: 'info',
    icon: 'info',
    content: `
      <p><strong>Nouveaux biens disponibles :</strong></p>
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
    icon: 'alert-triangle',
    content: `
      <strong>Conditions générales mises à jour</strong>
      <p>Nous avons mis à jour nos conditions générales de vente pour mieux protéger vos données personnelles 
      et clarifier les modalités de transaction immobilière. Les principales modifications concernent :</p>
      <ul>
        <li>La protection des données bancaires lors des transactions</li>
        <li>Les délais de rétractation pour les offres d'achat</li>
        <li>Les conditions d'annulation des visites de biens</li>
        <li>La gestion des litiges et réclamations</li>
      </ul>
      <p>Nous vous invitons à prendre connaissance de ces nouvelles conditions avant votre prochaine transaction.
      <a href="/cgv">Lire les conditions complètes</a></p>
    `,
    dismissible: true,
  },
};
