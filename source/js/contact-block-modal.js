/**
 * Contact Block Modal - AJAX Form Loader
 *
 * Loads contact form via AJAX when modal opens.
 * Works with Modal component (ps/modal) for dialog handling.
 *
 * Features:
 * - Listens for 'modal:opened' event
 * - Fetches contact form from URL
 * - Replaces loader with form content
 * - Error handling with user feedback
 */

((Drupal, once) => {
  Drupal.behaviors.psContactBlockModal = {
    attach(context) {
      // Find all modals with AJAX form loading
      once('contactAjaxModal', '[data-ajax-modal]', context).forEach((modal) => {
        const formUrl = modal.getAttribute('data-form-url');
        if (!formUrl) {
          return;
        }

        // Listen for modal:opened event
        modal.addEventListener('modal:opened', () => {
          this.loadContactForm(modal, formUrl);
        });
      });
    },

    /**
     * Load contact form via AJAX
     * @private
     */
    loadContactForm(modal, formUrl) {
      const body = modal.querySelector('.ps-modal__body');
      if (!body) {
        return;
      }

      // Check if form already loaded
      if (body.querySelector('form')) {
        return;
      }

      // Show loader, hide any existing content
      const loader = body.querySelector('.ps-loader');
      if (loader) {
        loader.parentElement.style.display = 'flex';
      }

      fetch(formUrl)
        .then((response) => {
          if (!response.ok) {
            throw new Error(`Failed to load form: ${response.statusText}`);
          }
          return response.text();
        })
        .then((html) => {
          // Clear loader
          if (loader) {
            loader.parentElement.style.display = 'none';
          }

          // Inject form
          body.innerHTML = html;

          // Trigger behaviors for newly added content
          if (Drupal.attachBehaviors) {
            Drupal.attachBehaviors(body, Drupal.settings);
          }

          // Dispatch custom event
          modal.dispatchEvent(
            new CustomEvent('contact-form-loaded', {
              detail: { form: body.querySelector('form') },
            })
          );
        })
        .catch((error) => {
          console.error('Error loading contact form:', error);

          // Hide loader
          if (loader) {
            loader.parentElement.style.display = 'none';
          }

          // Show error message
          body.innerHTML =
            '<div class="ps-alert ps-alert--error" role="alert"><p>Erreur lors du chargement du formulaire. Veuillez réessayer.</p></div>';
        });
    },
  };
})(Drupal, once);
