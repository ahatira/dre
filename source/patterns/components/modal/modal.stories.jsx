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
  render: (_args) => `
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
  name: 'With AJAX Loading (Drupal use-ajax)',
  parameters: {
    docs: {
      description: {
        story:
          'Demonstrates Drupal native AJAX integration using the "use-ajax" class. Click the link/button to load modal content via AJAX. This shows Drupal.ajax() handling with data-dialog-type="modal".',
      },
    },
  },
  render: (_args) => `
    <div>
      <a
        href="#"
        class="ps-button ps-button--primary use-ajax"
        data-dialog-type="modal"
        data-dialog-options='{"width": 600, "dialogClass": "ps-modal"}'
      >
        <span class="ps-button__label">Charger un bien immobilier</span>
      </a>
    </div>

    <script>
      // Mock Drupal AJAX for Storybook
      if (typeof Drupal === 'undefined') {
        window.Drupal = {
          behaviors: {},
          ajax: () => {},
          t: (str) => str,
        };
      }

      // Mock Drupal.ajax command handlers
      if (!window.Drupal.AjaxCommands) {
        window.Drupal.AjaxCommands = {
          insert: function (ajax, response, status) {
            console.log('AJAX insert:', response);
          },
          openDialog: function (ajax, response, status) {
            console.log('AJAX openDialog:', response);
            // Simulate opening modal
            const modal = document.createElement('div');
            modal.id = 'ajax-dialog-' + Math.random();
            modal.className = 'ps-modal ps-modal--visible';
            modal.innerHTML = \`
              <div class="ps-modal__backdrop"></div>
              <div class="ps-modal__content ps-modal__content--medium">
                <div class="ps-modal__header">
                  <h2 class="ps-modal__title">Détails du bien</h2>
                  <button class="ps-modal__close" type="button" data-icon="close" aria-label="Fermer la modale"></button>
                </div>
                <div class="ps-modal__body">
                  <h3 style="margin-top: 0;">Bureau à La Défense</h3>
                  <p><strong>Prix:</strong> 2 500 000 € HT</p>
                  <p><strong>Surface:</strong> 250 m²</p>
                  <p><strong>Étage:</strong> 12</p>
                  <p><strong>État:</strong> Neuf</p>
                  <p style="margin-bottom: 0;"><strong>Disponibilité:</strong> Immédiate</p>
                </div>
                <div class="ps-modal__footer">
                  <button class="ps-button ps-button--secondary" type="button">Fermer</button>
                  <button class="ps-button ps-button--primary" type="button">Contacter l'expert</button>
                </div>
              </div>
            \`;
            document.body.appendChild(modal);

            // Close button handler
            modal.querySelector('.ps-modal__close').addEventListener('click', () => {
              modal.remove();
            });
          },
        };
      }

      // Attach Drupal.ajax handlers to use-ajax elements
      const ajaxLink = document.querySelector('.use-ajax[data-dialog-type="modal"]');
      if (ajaxLink && !ajaxLink.dataset.ajaxAttached) {
        ajaxLink.dataset.ajaxAttached = 'true';
        ajaxLink.addEventListener('click', (e) => {
          e.preventDefault();
          console.log('AJAX triggered via use-ajax class');
          // Simulate Drupal.ajax response with openDialog command
          window.Drupal.AjaxCommands.openDialog(null, { html: '<p>Contenu chargé via AJAX</p>' }, 'success');
        });
      }
    </script>
  `,
  args: {
    size: 'medium',
    backdrop: true,
  },
};
