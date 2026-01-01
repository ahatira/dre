/**
 * Contact Block Modal Handler
 *
 * Manages modal dialog for contact form submission.
 * - Triggers modal on button click
 * - Loads contact form via AJAX
 * - Handles modal close actions (ESC, overlay click, close button)
 */

class ContactBlockModal {
  constructor(blockElement) {
    this.blockElement = blockElement;
    this.triggerButton = blockElement.querySelector('[data-modal-trigger]');
    this.modal = null;
    this.modalId = null;
    this.formUrl = null;

    if (!this.triggerButton) {
      return;
    }

    this.modalId = this.triggerButton.getAttribute('data-modal-trigger');
    this.formUrl = this.triggerButton.getAttribute('data-modal-url');
    this.modal = blockElement.querySelector(`#${this.modalId}`);

    if (!this.modal) {
      return;
    }

    this.init();
  }

  init() {
    this.attachEventListeners();
  }

  attachEventListeners() {
    // Open modal on button click
    this.triggerButton.addEventListener('click', (e) => {
      e.preventDefault();
      this.openModal();
    });

    // Close on overlay click
    const overlay = this.modal.querySelector('[data-modal-close]');
    if (overlay) {
      overlay.addEventListener('click', () => this.closeModal());
    }

    // Close on close button click
    const closeBtn = this.modal.querySelector('.ps-modal__close');
    if (closeBtn) {
      closeBtn.addEventListener('click', () => this.closeModal());
    }

    // Close on ESC key
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && !this.modal.hasAttribute('hidden')) {
        this.closeModal();
      }
    });
  }

  async openModal() {
    // Remove hidden attribute
    this.modal.removeAttribute('hidden');

    // Load form if not already loaded
    const formContainer = this.modal.querySelector('.ps-modal__form-container');
    const loader = this.modal.querySelector('.ps-modal__loader');

    if (!formContainer.innerHTML.trim()) {
      // Show loader
      if (loader) {
        loader.removeAttribute('hidden');
      }

      try {
        const response = await fetch(this.formUrl);
        if (!response.ok) {
          throw new Error(`Failed to load form: ${response.statusText}`);
        }

        const html = await response.text();
        formContainer.innerHTML = html;

        // Hide loader
        if (loader) {
          loader.setAttribute('hidden', '');
        }

        // Trigger any form initialization scripts
        this.initializeForm();
      } catch (error) {
        console.error('Error loading contact form:', error);
        formContainer.innerHTML =
          '<p class="ps-text--error">Error loading contact form. Please try again.</p>';
        if (loader) {
          loader.setAttribute('hidden', '');
        }
      }
    }

    // Focus modal for accessibility
    this.modal.focus();
  }

  closeModal() {
    this.modal.setAttribute('hidden', '');
  }

  initializeForm() {
    // Re-initialize any form-related behaviors (validation, etc.)
    // This hook allows for custom form initialization
    const event = new CustomEvent('contact-form-loaded', {
      detail: { form: this.modal.querySelector('form') },
    });
    this.modal.dispatchEvent(event);
  }
}

// Auto-initialize on DOMContentLoaded
document.addEventListener('DOMContentLoaded', () => {
  const blocks = document.querySelectorAll('[data-contact-block]');
  blocks.forEach((block) => {
    new ContactBlockModal(block);
  });
});

// Export for manual initialization if needed
if (typeof window !== 'undefined') {
  window.ContactBlockModal = ContactBlockModal;
}
