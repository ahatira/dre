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
      control: { type: 'text', rows: 4 },
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
    id: {
      description: 'Unique ID for aria-labelledby',
      control: { type: 'text' },
      table: {
        category: 'Accessibility',
        type: { summary: 'string' },
        defaultValue: { summary: 'modal-title' },
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
    // Drupal
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

export const Default = {
  name: 'Default',
  render: (args) => modalTwig({ ...args, show: true }),
  args: data,
};

export const Small = {
  name: 'Small Size',
  render: (args) => modalTwig({ ...args, show: true }),
  args: {
    title: 'Supprimer la recherche sauvegardée',
    content:
      '<p>Êtes-vous sûr de vouloir supprimer cette recherche ? Cette action est irréversible.</p>',
    footer:
      '<button class="ps-button ps-button--secondary">Annuler</button><button class="ps-button ps-button--danger">Supprimer</button>',
    size: 'small',
    backdrop: true,
  },
};

export const Medium = {
  name: 'Medium Size',
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

export const Large = {
  name: 'Large Size',
  render: (args) => modalTwig({ ...args, show: true }),
  args: {
    title: 'Contactez un expert immobilier',
    content:
      '<form class="ps-form"><div class="ps-form-field"><label for="name">Nom complet</label><input type="text" id="name" class="ps-input" placeholder="Votre nom"></div><div class="ps-form-field"><label for="email">Email professionnel</label><input type="email" id="email" class="ps-input" placeholder="nom@entreprise.com"></div><div class="ps-form-field"><label for="message">Votre projet immobilier</label><textarea id="message" class="ps-textarea" rows="4" placeholder="Décrivez votre projet..."></textarea></div></form>',
    footer:
      '<button class="ps-button ps-button--secondary">Fermer</button><button class="ps-button ps-button--primary">Envoyer la demande</button>',
    size: 'large',
    backdrop: true,
  },
};

export const WithoutFooter = {
  name: 'Without Footer',
  render: (args) => modalTwig({ ...args, show: true }),
  args: {
    title: 'Informations sur le bien',
    content:
      "<p>Ce bien est situé au cœur du quartier d'affaires de La Défense, à proximité immédiate des transports.</p><p>Surface totale : 250 m²<br>Étage : 12<br>Disponibilité : Immédiate</p>",
    size: 'medium',
    backdrop: true,
  },
};

export const WithTriggerButton = {
  name: 'With Trigger Button (Interactive)',
  parameters: {
    docs: {
      description: {
        story:
          'Example showing how to use the modal with a trigger button. Click the button to open the modal. This demonstrates the Drupal integration pattern with `data-modal-trigger` attribute.',
      },
    },
  },
  render: (args) => `
    <div>
      <button class="ps-button ps-button--primary" data-modal-trigger="demo-modal">
        <span class="ps-button__label">Ouvrir le modal</span>
      </button>
      
      ${modalTwig({
        ...args,
        id: 'demo-modal',
        show: false,
      })}
    </div>
    
    <script>
      // Simulate Drupal behavior for Storybook preview
      if (typeof Drupal === 'undefined') {
        window.Drupal = { behaviors: {} };
      }
      if (typeof once === 'undefined') {
        window.once = (id, selector, context = document) => {
          const elements = context.querySelectorAll(selector);
          return Array.from(elements).filter(el => {
            if (el.hasAttribute('data-once-' + id)) return false;
            el.setAttribute('data-once-' + id, '');
            return true;
          });
        };
      }

      if (window.Drupal.behaviors && window.Drupal.behaviors.psModal) {
        window.Drupal.behaviors.psModal.attach(document);
      }
    </script>
  `,
  args: {
    title: 'Modal avec bouton trigger',
    content:
      '<p>Ce modal s\'ouvre via un bouton avec l\'attribut <code>data-modal-trigger="demo-modal"</code>.</p><p>Vous pouvez fermer avec:</p><ul><li>Le bouton X</li><li>La touche ESC</li><li>Un clic sur le backdrop</li></ul>',
    footer:
      '<button class="ps-button ps-button--secondary">Annuler</button><button class="ps-button ps-button--primary">Confirmer</button>',
    size: 'medium',
    backdrop: true,
  },
};

export const WithoutBackdrop = {
  name: 'Without Backdrop',
  render: (args) => modalTwig({ ...args, show: true }),
  args: {
    title: 'Modal sans fond sombre',
    content: "<p>Ce modal s'affiche sans backdrop pour des cas d'usage spécifiques.</p>",
    footer: '<button class="ps-button ps-button--secondary">Fermer</button>',
    size: 'medium',
    backdrop: false,
  },
};

export const WithAjaxLoading = {
  name: 'With AJAX Loading (Simulated)',
  parameters: {
    docs: {
      description: {
        story:
          'Demonstrates AJAX content loading with loading state. Click the button to trigger the modal, which shows a loading spinner while fetching content, then displays the loaded data.',
      },
    },
  },
  render: (args) => `
    <div>
      <button class="ps-button ps-button--primary" data-modal-trigger="ajax-modal">
        <span class="ps-button__label">Charger un bien immobilier</span>
      </button>
      
      ${modalTwig({
        ...args,
        id: 'ajax-modal',
        show: false,
        title: 'Détails du bien',
        content:
          '<div class="ps-modal__loading" style="text-align: center; padding: 40px; color: var(--text-secondary);"><p>Chargement en cours...</p></div>',
      })}
    </div>
    
    <script>
      // Simulate Drupal behavior
      if (typeof Drupal === 'undefined') {
        window.Drupal = { behaviors: {} };
      }
      if (typeof once === 'undefined') {
        window.once = (id, selector, context = document) => {
          const elements = context.querySelectorAll(selector);
          return Array.from(elements).filter(el => {
            if (el.hasAttribute('data-once-' + id)) return false;
            el.setAttribute('data-once-' + id, '');
            return true;
          });
        };
      }

      if (window.Drupal.behaviors && window.Drupal.behaviors.psModal) {
        window.Drupal.behaviors.psModal.attach(document);
      }

      // Simulate AJAX loading when modal opens
      const ajaxModal = document.getElementById('ajax-modal');
      if (ajaxModal) {
        ajaxModal.addEventListener('modal:opened', async () => {
          const contentBody = ajaxModal.querySelector('.ps-modal__body');
          
          // Simulate AJAX delay (2 seconds)
          await new Promise(resolve => setTimeout(resolve, 2000));
          
          // Update content after "loading"
          contentBody.innerHTML = \`
            <h3 style="margin-top: 0;">Bureau à La Défense</h3>
            <p><strong>Prix:</strong> 2 500 000 € HT</p>
            <p><strong>Surface:</strong> 250 m²</p>
            <p><strong>Étage:</strong> 12</p>
            <p><strong>État:</strong> Neuf</p>
            <p style="margin-bottom: 0;"><strong>Disponibilité:</strong> Immédiate</p>
          \`;
        });
        
        // Reset content on close
        ajaxModal.addEventListener('modal:closed', () => {
          const contentBody = ajaxModal.querySelector('.ps-modal__body');
          contentBody.innerHTML = '<div class="ps-modal__loading" style="text-align: center; padding: 40px; color: var(--text-secondary);"><p>Chargement en cours...</p></div>';
        });
      }
    </script>
  `,
  args: {
    size: 'medium',
    backdrop: true,
    footer:
      '<button class="ps-button ps-button--secondary">Fermer</button><button class="ps-button ps-button--primary">Contacter l\'expert</button>',
  },
};
