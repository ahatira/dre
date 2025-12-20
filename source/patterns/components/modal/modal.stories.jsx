import modalTwig from './modal.twig';
import data from './modal.yml';
import './modal.js';

export default {
  title: 'Components/Modal',
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component:
          'Dialog overlay with backdrop, header, body, and optional footer. Supports keyboard navigation (ESC to close), focus trap, and three sizes. Used for property inquiries, contact forms, and confirmations.',
      },
    },
  },
  argTypes: {
    // Content
    title: {
      description: 'Modal title displayed in header',
      control: { type: 'text' },
      table: {
        category: 'Content',
        type: { summary: 'string', required: true },
      },
    },
    content: {
      description: 'Main content of the modal (supports HTML)',
      control: { type: 'text' },
      table: {
        category: 'Content',
        type: { summary: 'string', required: true },
      },
    },
    footer: {
      description: 'Optional footer content with action buttons (supports HTML)',
      control: { type: 'text' },
      table: {
        category: 'Content',
        type: { summary: 'string' },
      },
    },
    // Appearance
    size: {
      description: 'Modal width: small (25rem/400px), medium (37.5rem/600px), large (50rem/800px)',
      control: { type: 'inline-radio' },
      options: ['small', 'medium', 'large'],
      table: {
        category: 'Appearance',
        type: { summary: 'small | medium | large' },
        defaultValue: { summary: 'medium' },
      },
    },
    backdrop: {
      description: 'Show dark overlay backdrop',
      control: { type: 'boolean' },
      table: {
        category: 'Appearance',
        type: { summary: 'boolean' },
        defaultValue: { summary: 'true' },
      },
    },
    // State
    show: {
      description: 'Visibility state (controlled by JavaScript)',
      control: { type: 'boolean' },
      table: {
        category: 'State',
        type: { summary: 'boolean' },
        defaultValue: { summary: 'false' },
      },
    },
    // Accessibility
    id: {
      description: 'Unique ID for aria-labelledby',
      control: { type: 'text' },
      table: {
        category: 'Accessibility',
        type: { summary: 'string' },
        defaultValue: { summary: 'modal' },
      },
    },
    attributes: {
      description: 'Additional HTML attributes for Drupal integration',
      control: { type: 'object' },
      table: {
        category: 'Accessibility',
        type: { summary: 'object' },
      },
    },
  },
};

/**
 * Default modal - Real Estate visit confirmation (Medium size)
 */
export const Default = {
  parameters: {
    docs: {
      story: { inline: false, iframeHeight: 500 },
    },
  },
  render: (args) => modalTwig({ ...args, show: true }),
  args: data,
};

/**
 * Small modal - Destructive action confirmation
 * Compact size (25rem) for simple confirmations and alerts
 */
export const Small = {
  parameters: {
    docs: {
      story: { inline: false, iframeHeight: 400 },
      description: {
        story:
          'Small modal (25rem/400px) for simple confirmations, delete actions, and quick alerts.',
      },
    },
  },
  render: (args) => modalTwig({ ...args, show: true }),
  args: {
    title: 'Supprimer la recherche',
    content:
      '<p>Êtes-vous sûr de vouloir supprimer <strong>Bureaux La Défense</strong> ? Cette action est irréversible.</p>',
    footer:
      '<button class="ps-button ps-button--secondary">Annuler</button><button class="ps-button ps-button--danger">Supprimer</button>',
    size: 'small',
    backdrop: true,
  },
};

/**
 * Medium modal - Standard content and forms
 * Default size (37.5rem) for most use cases
 */
export const Medium = {
  parameters: {
    docs: {
      story: { inline: false, iframeHeight: 500 },
      description: {
        story:
          'Medium modal (37.5rem/600px) - default size for standard content, short forms, and confirmations.',
      },
    },
  },
  render: (args) => modalTwig({ ...args, show: true }),
  args: {
    title: 'Confirmer la demande de visite',
    content:
      '<p>Vous êtes sur le point de réserver une visite pour le bien <strong>Bureau 250 m² - La Défense</strong>.</p><p>Un conseiller vous contactera dans les 24h pour confirmer le rendez-vous.</p>',
    footer:
      '<button class="ps-button ps-button--secondary">Annuler</button><button class="ps-button ps-button--primary">Confirmer la visite</button>',
    size: 'medium',
    backdrop: true,
  },
};

/**
 * Large modal - Contact forms and complex content
 * Extended size (50rem) for detailed forms
 */
export const Large = {
  parameters: {
    docs: {
      story: { inline: false, iframeHeight: 600 },
      description: {
        story:
          'Large modal (50rem/800px) for contact forms, detailed content, and multi-field forms.',
      },
    },
  },
  render: (args) => modalTwig({ ...args, show: true }),
  args: {
    title: "Demande d'information",
    content: `
      <p style="margin-top: 0;">Bien concerné : <strong>Bureau 250 m² - La Défense</strong></p>
      <form>
        <div class="ps-form-field">
          <label for="contact-name">Nom complet</label>
          <input type="text" id="contact-name" class="ps-input" placeholder="Votre nom" />
        </div>
        <div class="ps-form-field">
          <label for="contact-email">Email professionnel</label>
          <input type="email" id="contact-email" class="ps-input" placeholder="nom@entreprise.com" />
        </div>
        <div class="ps-form-field">
          <label for="contact-phone">Téléphone</label>
          <input type="tel" id="contact-phone" class="ps-input" placeholder="+33 1 23 45 67 89" />
        </div>
        <div class="ps-form-field">
          <label for="contact-message">Votre projet</label>
          <textarea id="contact-message" class="ps-textarea" rows="4" placeholder="Décrivez votre projet immobilier..."></textarea>
        </div>
      </form>
    `,
    footer:
      '<button class="ps-button ps-button--secondary">Annuler</button><button class="ps-button ps-button--primary">Envoyer la demande</button>',
    size: 'large',
    backdrop: true,
  },
};

/**
 * Without footer - Informational content only
 * Modal without action buttons, closed via X or ESC
 */
export const WithoutFooter = {
  parameters: {
    docs: {
      story: { inline: false, iframeHeight: 450 },
      description: {
        story:
          'Modal without footer buttons - useful for informational content that only needs to be closed.',
      },
    },
  },
  render: (args) => modalTwig({ ...args, show: true }),
  args: {
    title: 'Détails du bien',
    content:
      '<p><strong>Surface:</strong> 250 m²<br><strong>Étage:</strong> 12<br><strong>État:</strong> Neuf<br><strong>Disponibilité:</strong> Immédiate<br><strong>Parking:</strong> 2 places incluses<br><strong>Loyer:</strong> 6 000 € / mois HT</p>',
    size: 'medium',
    backdrop: true,
  },
};

/**
 * Interactive - Trigger button integration
 * Demonstrates Drupal integration with data-modal-trigger
 */
export const Interactive = {
  parameters: {
    docs: {
      story: { inline: false, iframeHeight: 500 },
      description: {
        story:
          'Interactive modal with trigger button. Uses `data-modal-trigger="modal-id"` attribute for Drupal integration. Click button to open, close with X button, ESC key, or backdrop click.',
      },
    },
  },
  render: () => `
    <div style="display: flex; gap: 1rem;">
      <button class="ps-button ps-button--primary" data-modal-trigger="interactive-modal">
        Ouvrir le modal
      </button>
      <button class="ps-button ps-button--secondary" data-modal-trigger="info-modal">
        Informations du bien
      </button>
    </div>
    
    ${modalTwig({
      id: 'interactive-modal',
      title: 'Demande de visite',
      content:
        '<p>Sélectionnez une date pour votre visite du bien <strong>Bureau 250 m² - La Défense</strong>.</p><p style="margin-bottom: 0;"><em>Fermez avec le bouton X, la touche ESC, ou un clic sur le fond sombre.</em></p>',
      footer:
        '<button class="ps-button ps-button--secondary">Annuler</button><button class="ps-button ps-button--primary">Confirmer</button>',
      size: 'medium',
      backdrop: true,
      show: false,
    })}
    
    ${modalTwig({
      id: 'info-modal',
      title: 'Caractéristiques du bien',
      content:
        '<p><strong>Type:</strong> Bureau<br><strong>Surface:</strong> 250 m²<br><strong>Loyer:</strong> 6 000 € / mois HT<br><strong>Charges:</strong> 1 200 € / mois<br><strong>Disponibilité:</strong> Immédiate</p>',
      size: 'small',
      backdrop: true,
      show: false,
    })}
    
    <script>
      if (typeof Drupal !== 'undefined' && Drupal.behaviors.psModal) {
        Drupal.behaviors.psModal.attach(document);
      }
    </script>
  `,
};
