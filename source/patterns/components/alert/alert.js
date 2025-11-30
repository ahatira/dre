/**
 * @file Alert Component - Dismissible Behavior (Drupal-friendly)
 * Handles close button interaction with fade-out animation.
 */

((Drupal, once) => {
  function dismissAlert(alert) {
    alert.classList.add('is-closing');
    alert.addEventListener(
      'animationend',
      () => {
        alert.remove();
      },
      { once: true }
    );
  }

  Drupal.behaviors.psAlert = {
    attach(context) {
      // Bind click handlers to close buttons within context (once)
      once('ps-alert-close', '.ps-alert__close', context).forEach((btn) => {
        btn.addEventListener('click', () => {
          const alert = btn.closest('.ps-alert');
          if (alert) {
            dismissAlert(alert);
          }
        });
      });

      // Expose minimal API for programmatic dismissal (optional)
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
    },
  };
})(Drupal, once);
