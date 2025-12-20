/**
 * Modal Component
 * Drupal behavior for dialog interactions: open/close, focus trap, keyboard handling
 *
 * Features:
 * - Focus trap (Tab cycles within modal)
 * - ESC key closes modal
 * - Backdrop click closes modal (if enabled)
 * - Auto-focus close button on open
 * - Custom events: 'modal:opened', 'modal:closed'
 * - Drupal integration via data-modal-trigger attribute
 */

((Drupal, once) => {
  Drupal.behaviors.psModal = {
    /**
     * Attach modal behavior
     * @param {Document|HTMLElement} context - DOM context (entire document or subtree)
     */
    attach(context) {
      // Initialize all modals and triggers
      once('psModalInit', '.ps-modal', context).forEach((modal) => {
        this.initializeModal(modal);
      });

      // Attach trigger buttons
      // IMPORTANT: use once() with '[data-modal-trigger]' selector for idempotent binding
      once('psModalTrigger', '[data-modal-trigger]', context).forEach((trigger) => {
        trigger.addEventListener('click', (e) => {
          e.preventDefault();
          const modalId = trigger.getAttribute('data-modal-trigger');
          const modal = document.querySelector(`#${modalId}`);
          if (modal) {
            this.openModal(modal);
            // Store reference to trigger for focus restoration on close
            modal.dataset.triggerElement = trigger.id || null;
          }
        });
      });
    },

    /**
     * Initialize modal with event listeners
     * @private
     */
    initializeModal(modal) {
      const closeBtn = modal.querySelector('.ps-modal__close');
      const backdrop = modal.querySelector('.ps-modal__backdrop');

      // Close button
      if (closeBtn) {
        closeBtn.addEventListener('click', (e) => {
          e.preventDefault();
          this.closeModal(modal);
        });
      }

      // Backdrop click (if backdrop exists)
      if (backdrop) {
        backdrop.addEventListener('click', (e) => {
          e.preventDefault();
          this.closeModal(modal);
        });
      }

      // Keyboard handling (ESC and Tab focus trap)
      modal.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
          e.preventDefault();
          this.closeModal(modal);
        }

        // Tab focus trap
        if (e.key === 'Tab') {
          this.handleTabKey(e, modal);
        }
      });
    },

    /**
     * Open modal: show, manage focus, dispatch event
     * @public
     * @param {HTMLElement} modal - Modal element
     */
    openModal(modal) {
      // Already visible
      if (modal.classList.contains('ps-modal--visible')) {
        return;
      }

      // Show modal
      modal.classList.add('ps-modal--visible');
      modal.setAttribute('aria-hidden', 'false');

      // Disable body scroll (prevent background content scroll)
      document.body.style.overflow = 'hidden';

      // Focus close button or modal element
      const closeBtn = modal.querySelector('.ps-modal__close');
      if (closeBtn) {
        // Small delay ensures focus after render
        requestAnimationFrame(() => closeBtn.focus());
      } else {
        modal.focus();
      }

      // Dispatch custom event
      modal.dispatchEvent(new CustomEvent('modal:opened', { bubbles: true }));
    },

    /**
     * Close modal: hide, restore focus, dispatch event
     * @public
     * @param {HTMLElement} modal - Modal element
     */
    closeModal(modal) {
      // Already hidden
      if (!modal.classList.contains('ps-modal--visible')) {
        return;
      }

      // Hide modal
      modal.classList.remove('ps-modal--visible');
      modal.setAttribute('aria-hidden', 'true');

      // Re-enable body scroll
      document.body.style.overflow = '';

      // Restore focus to trigger button or document
      const triggerId = modal.dataset.triggerElement;
      if (triggerId) {
        const trigger = document.getElementById(triggerId);
        if (trigger) {
          trigger.focus();
        }
      } else {
        // Fallback: focus first trigger button that opened this modal
        const trigger = document.querySelector(`[data-modal-trigger="${modal.id}"]`);
        if (trigger) {
          trigger.focus();
        }
      }

      // Dispatch custom event
      modal.dispatchEvent(new CustomEvent('modal:closed', { bubbles: true }));
    },

    /**
     * Tab key handler: prevent focus from leaving modal (focus trap)
     * @private
     */
    handleTabKey(e, modal) {
      // Get all focusable elements within modal
      const focusableElements = modal.querySelectorAll(
        'a, button, [tabindex]:not([tabindex="-1"]), input:not([disabled]), select:not([disabled]), textarea:not([disabled])'
      );

      if (focusableElements.length === 0) {
        e.preventDefault();
        return;
      }

      const firstElement = focusableElements[0];
      const lastElement = focusableElements[focusableElements.length - 1];
      const activeElement = document.activeElement;

      // Shift+Tab on first element → focus last
      if (e.shiftKey && activeElement === firstElement) {
        e.preventDefault();
        lastElement.focus();
      }
      // Tab on last element → focus first
      else if (!e.shiftKey && activeElement === lastElement) {
        e.preventDefault();
        firstElement.focus();
      }
    },

    /**
     * Detach behavior (cleanup)
     */
    detach(_context, _settings, trigger) {
      if (trigger === 'unload') {
        document.body.style.overflow = '';
      }
    },
  };
})(Drupal, once);
