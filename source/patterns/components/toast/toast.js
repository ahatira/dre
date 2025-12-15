/**
 * @file
 * Toast component behavior
 *
 * Manages toast notifications with auto-dismiss, manual close, and stacking.
 * Inspired by Bootstrap Toast component with improvements for Drupal integration.
 */

(() => {
  const DATA_KEY = 'ps.toast';
  const EVENT_KEY = `.${DATA_KEY}`;
  const EVENT_SHOWN = `shown${EVENT_KEY}`;
  const EVENT_HIDDEN = `hidden${EVENT_KEY}`;

  const CLASS_SHOWING = 'ps-toast--showing';
  const CLASS_SHOW = 'ps-toast--show';
  const CLASS_HIDE = 'ps-toast--hide';

  const SELECTOR_TOAST = '.ps-toast';
  const SELECTOR_CLOSE = '.ps-toast__close';

  /**
   * Toast Class - manages individual toast lifecycle
   */
  class Toast {
    constructor(element, config = {}) {
      this._element = element;
      this._config = {
        duration: parseInt(element.dataset.toastDuration, 10) || config.duration || 4000,
        dismissible: config.dismissible !== false,
      };
      this._timeout = null;
      this._isShown = false;

      // Store instance on element
      element[DATA_KEY] = this;
    }

    // Public methods
    show() {
      if (this._isShown) {
        return;
      }

      this._isShown = true;

      // Add showing class for animation
      this._element.classList.add(CLASS_SHOWING);

      // Trigger show animation
      requestAnimationFrame(() => {
        this._element.classList.add(CLASS_SHOW);
        this._element.classList.remove(CLASS_SHOWING);

        // Dispatch shown event
        this._element.dispatchEvent(new CustomEvent(EVENT_SHOWN, { bubbles: true }));

        // Auto-dismiss after duration
        if (this._config.duration > 0) {
          this._timeout = setTimeout(() => {
            this.hide();
          }, this._config.duration);
        }
      });
    }

    hide() {
      if (!this._isShown) {
        return;
      }

      this._isShown = false;

      // Clear auto-dismiss timeout
      if (this._timeout) {
        clearTimeout(this._timeout);
        this._timeout = null;
      }

      // Trigger hide animation
      this._element.classList.add(CLASS_HIDE);
      this._element.classList.remove(CLASS_SHOW);

      // Wait for animation, then remove
      const handleAnimationEnd = () => {
        this._element.removeEventListener('animationend', handleAnimationEnd);
        this._element.dispatchEvent(new CustomEvent(EVENT_HIDDEN, { bubbles: true }));
        this.dispose();
      };

      this._element.addEventListener('animationend', handleAnimationEnd);
    }

    dispose() {
      if (this._timeout) {
        clearTimeout(this._timeout);
      }

      if (this._element) {
        delete this._element[DATA_KEY];
        if (this._element.parentNode) {
          this._element.remove();
        }
        this._element = null;
      }
    }

    // Static methods
    static getInstance(element) {
      return element[DATA_KEY] || null;
    }

    static getOrCreateInstance(element, config) {
      return Toast.getInstance(element) || new Toast(element, config);
    }
  }

  /**
   * Toast Container - manages multiple toasts with stacking
   */
  class ToastContainer {
    constructor() {
      this._containers = new Map();
    }

    getContainer(position = 'bottom-right') {
      if (!this._containers.has(position)) {
        const container = document.createElement('div');
        container.className = `ps-toast-container ps-toast-container--${position}`;
        container.setAttribute('aria-live', 'polite');
        container.setAttribute('aria-atomic', 'false');
        document.body.appendChild(container);
        this._containers.set(position, container);
      }
      return this._containers.get(position);
    }
  }

  // Global container instance
  const toastContainer = new ToastContainer();

  /**
   * Initialize existing toast elements (skip static toasts)
   */
  function initExistingToasts(context = document) {
    const toasts = context.querySelectorAll(SELECTOR_TOAST);
    toasts.forEach((element) => {
      // Skip toasts marked as static (for Storybook design showcases)
      if (element.dataset.static === 'true') {
        return;
      }
      const toast = Toast.getOrCreateInstance(element);
      toast.show();
    });
  }

  /**
   * Create and show a toast programmatically
   */
  function createToast(options) {
    const {
      message,
      type = 'info',
      position = 'bottom-right',
      duration = 4000,
      dismissible = true,
    } = options;

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
      closeBtn.setAttribute('aria-label', 'Dismiss notification');
      closeBtn.setAttribute('data-icon', 'close');
      toast.appendChild(closeBtn);
    }

    // Add to appropriate container
    const container = toastContainer.getContainer(position);
    container.appendChild(toast);

    // Initialize and show
    const toastInstance = new Toast(toast, { duration, dismissible });
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
      const toast = Toast.getInstance(toastElement);
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
      const toast = Toast.getInstance(activeToast);
      if (toast) {
        toast.hide();
      }
    }
  });

  // Export for Drupal
  if (typeof Drupal !== 'undefined') {
    Drupal.behaviors.toast = {
      attach(context) {
        initExistingToasts(context);
      },
    };

    Drupal.toast = createToast;
  }

  // Export for standalone/Storybook
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
      // If already loaded, init immediately
      initExistingToasts();
    }
  }
})();
