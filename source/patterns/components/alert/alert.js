/**
 * Alert Component - Dismissible Behavior
 * Handles close button interaction with fade-out animation
 */

(() => {
  /**
   * Initialize alert dismiss functionality
   */
  function initAlerts() {
    // Attach delegated click handler for dismissal
    document.addEventListener('click', (evt) => {
      const target = evt.target;
      // Match button or its child (e.g., inner span)
      const closeBtn = target.closest?.('.ps-alert__close');
      if (closeBtn) {
        const alert = closeBtn.closest('.ps-alert');
        if (alert) {
          dismissAlert(alert);
        }
      }
    });
  }

  /**
   * Dismiss an alert with fade-out animation
   * @param {HTMLElement} alert - The alert element to dismiss
   */
  function dismissAlert(alert) {
    // Add closing class for animation
    alert.classList.add('is-closing');

    // Remove element after animation completes
    alert.addEventListener(
      'animationend',
      () => {
        alert.remove();
      },
      { once: true }
    );
  }

  /**
   * Expose API for programmatic dismissal
   */
  window.PSAlert = {
    dismiss: (alertElement) => {
      if (alertElement?.classList.contains('ps-alert')) {
        dismissAlert(alertElement);
      }
    },

    dismissAll: () => {
      const alerts = document.querySelectorAll('.ps-alert');
      alerts.forEach((alert) => dismissAlert(alert));
    },
  };

  // Auto-initialize on DOM ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAlerts);
  } else {
    initAlerts();
  }

  // Re-initialize for dynamically added alerts
  const observer = new MutationObserver((mutations) => {
    mutations.forEach((mutation) => {
      mutation.addedNodes.forEach((node) => {
        if (node.nodeType === 1 && node.classList?.contains('ps-alert')) {
          const closeButton = node.querySelector('.ps-alert__close');
          if (closeButton) {
            closeButton.addEventListener('click', () => {
              dismissAlert(node);
            });
          }
        }
      });
    });
  });

  observer.observe(document.body, {
    childList: true,
    subtree: true,
  });
})();
