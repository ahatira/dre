/**
 * @file
 * Toast component behavior
 *
 * Manages toast notifications with auto-dismiss, manual close, and stacking.
 * Compatible with Drupal behaviors pattern and standalone usage.
 *
 */

((Drupal, _once) => {
  const DATA_KEY = 'ps-toast';
  const EVENT_SHOWN = 'toast:shown';
  const EVENT_HIDDEN = 'toast:hidden';

  const CLASS_SHOW = 'ps-toast--show';
  const CLASS_HIDE = 'ps-toast--hide';

  const SELECTOR_TOAST = '.ps-toast';
  const SELECTOR_CLOSE = '.ps-toast__close';

  /**
   * Create a toast instance
   *
   * @param {HTMLElement} element - Toast DOM element
   * @param {Object} config - Configuration options
   * @param {number} config.duration - Auto-dismiss duration in ms
   * @param {boolean} config.dismissible - Whether toast is dismissible
   * @returns {Object} Toast instance with public methods
   */
  function createToastInstance(element, config) {
    const instance = {
      element: element,
      config: {
        duration: parseInt(element.dataset.toastDuration, 10) || config.duration || 4000,
        dismissible: config.dismissible !== false,
      },
      timeout: null,
      isShown: false,

      /**
       * Show the toast with animation
       */
      show: function () {
        if (this.isShown) {
          return;
        }

        this.isShown = true;

        // Trigger show animation
        requestAnimationFrame(() => {
          this.element.classList.add(CLASS_SHOW);

          // Dispatch shown event
          const event = new CustomEvent(EVENT_SHOWN, {
            bubbles: true,
            detail: { toast: this },
          });
          this.element.dispatchEvent(event);

          // Auto-dismiss after duration
          if (this.config.duration > 0) {
            this.timeout = setTimeout(() => {
              this.hide();
            }, this.config.duration);
          }
        });
      },

      /**
       * Hide the toast with animation
       */
      hide: function () {
        if (!this.isShown) {
          return;
        }

        this.isShown = false;

        // Clear auto-dismiss timeout
        if (this.timeout) {
          clearTimeout(this.timeout);
          this.timeout = null;
        }

        // Trigger hide animation
        this.element.classList.add(CLASS_HIDE);
        this.element.classList.remove(CLASS_SHOW);

        // Wait for transition, then remove
        const transitionDuration =
          parseFloat(getComputedStyle(this.element).transitionDuration) * 1000 || 300;

        setTimeout(() => {
          const event = new CustomEvent(EVENT_HIDDEN, {
            bubbles: true,
            detail: { toast: this },
          });
          this.element.dispatchEvent(event);
          this.dispose();
        }, transitionDuration);
      },

      /**
       * Clean up and remove toast
       */
      dispose: function () {
        if (this.timeout) {
          clearTimeout(this.timeout);
        }

        if (this.element) {
          delete this.element[DATA_KEY];
          if (this.element.parentNode) {
            this.element.remove();
          }
          this.element = null;
        }
      },
    };

    // Store instance on element
    element[DATA_KEY] = instance;

    return instance;
  }

  /**
   * Get toast instance from element
   *
   * @param {HTMLElement} element - Toast DOM element
   * @returns {Object|null} Toast instance or null
   */
  function getInstance(element) {
    return element[DATA_KEY] || null;
  }

  /**
   * Get or create toast instance
   *
   * @param {HTMLElement} element - Toast DOM element
   * @param {Object} config - Configuration options
   * @returns {Object} Toast instance
   */
  function getOrCreateInstance(element, config) {
    return getInstance(element) || createToastInstance(element, config || {});
  }

  /**
   * Toast container manager
   */
  const toastContainers = {};

  /**
   * Get or create container for position
   *
   * @param {string} position - Container position (bottom-right, etc.)
   * @returns {HTMLElement} Container element
   */
  function getContainer(position) {
    position = position || 'bottom-right';

    if (!toastContainers[position]) {
      const container = document.createElement('div');
      container.className = `ps-toast-container ps-toast-container--${position}`;
      container.setAttribute('aria-live', 'polite');
      container.setAttribute('aria-atomic', 'false');
      document.body.appendChild(container);
      toastContainers[position] = container;
    }

    return toastContainers[position];
  }

  /**
   * Initialize existing toast elements
   *
   * @param {HTMLElement} context - Context to search within
   */
  function initExistingToasts(context) {
    context = context || document;

    const toasts = context.querySelectorAll(SELECTOR_TOAST);

    toasts.forEach((element) => {
      // Skip toasts marked as static (for Storybook design showcases)
      if (element.dataset.static === 'true') {
        return;
      }

      const toast = getOrCreateInstance(element, {});
      toast.show();
    });
  }

  /**
   * Create and show a toast programmatically
   *
   * @param {Object} options - Toast options
   * @param {string} options.message - Toast message text
   * @param {string} options.type - Toast type (success, error, warning, info)
   * @param {string} options.position - Screen position
   * @param {number} options.duration - Auto-dismiss duration
   * @param {boolean} options.dismissible - Whether dismissible
   * @returns {HTMLElement} Created toast element
   */
  function createToast(options) {
    const message = options.message || '';
    const type = options.type || 'info';
    const position = options.position || 'bottom-right';
    const duration = options.duration || 4000;
    const dismissible = options.dismissible !== false;

    // Create toast element
    const toast = document.createElement('div');
    toast.className = 'ps-toast';

    if (type !== 'info') {
      toast.classList.add(`ps-toast--${type}`);
    }

    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');
    toast.dataset.toastDuration = duration;

    // Create content
    const content = document.createElement('div');
    content.className = 'ps-toast__content';
    content.textContent = message;
    toast.appendChild(content);

    // Create close button if dismissible
    if (dismissible) {
      const closeBtn = document.createElement('button');
      closeBtn.type = 'button';
      closeBtn.className = 'ps-toast__close';
      closeBtn.setAttribute('aria-label', 'Fermer la notification');
      closeBtn.setAttribute('data-icon', 'close');
      toast.appendChild(closeBtn);
    }

    // Add to appropriate container
    const container = getContainer(position);
    container.appendChild(toast);

    // Initialize and show
    const toastInstance = createToastInstance(toast, {
      duration: duration,
      dismissible: dismissible,
    });
    toastInstance.show();

    return toast;
  }

  /**
   * Event delegation for close buttons
   */
  document.addEventListener('click', (event) => {
    const closeButton = event.target.closest(SELECTOR_CLOSE);

    if (!closeButton) {
      return;
    }

    const toastElement = closeButton.closest(SELECTOR_TOAST);

    if (toastElement) {
      const toast = getInstance(toastElement);
      if (toast) {
        toast.hide();
      }
    }
  });

  /**
   * Keyboard support - Escape to close focused toast
   */
  document.addEventListener('keydown', (event) => {
    if (event.key !== 'Escape') {
      return;
    }

    const activeToast = document.querySelector(
      `${SELECTOR_TOAST}:focus-within, ${SELECTOR_TOAST}:hover`
    );

    if (activeToast) {
      const toast = getInstance(activeToast);
      if (toast) {
        toast.hide();
      }
    }
  });

  // Drupal behavior integration
  if (typeof Drupal !== 'undefined') {
    Drupal.behaviors.toast = {
      attach: (context) => {
        initExistingToasts(context);
      },
    };

    // Export for Drupal
    Drupal.toast = createToast;
  }

  // Standalone/Storybook export
  if (typeof window !== 'undefined') {
    window.Toast = {
      create: createToast,
      init: initExistingToasts,
    };

    // Auto-initialize on load
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', () => {
        initExistingToasts();
      });
    } else {
      initExistingToasts();
    }
  }
})(
  typeof Drupal !== 'undefined' ? Drupal : {},
  typeof once !== 'undefined'
    ? once
    : (id, selector, context) => {
        const safeContext = context || document;
        const elements =
          typeof selector === 'string' ? safeContext.querySelectorAll(selector) : [selector];
        return Array.from(elements).filter((el) => {
          const key = `data-once-${id}`;
          if (el.hasAttribute(key)) {
            return false;
          }
          el.setAttribute(key, 'true');
          return true;
        });
      }
);
