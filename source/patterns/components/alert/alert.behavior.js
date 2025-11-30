((Drupal, once) => {
  function dismissAlert(alert) {
    alert.classList.add('is-closing');
    alert.addEventListener('animationend', () => alert.remove(), { once: true });
  }

  Drupal.behaviors.psAlert = {
    attach(_context) {
      // Delegated click: register once on document to cover dynamic content
      once('ps-alert-dismiss-delegated', document).forEach(() => {
        document.addEventListener('click', (evt) => {
          const closeBtn = evt.target.closest?.('.ps-alert__close');
          if (!closeBtn) {
            return;
          }
          const alert = closeBtn.closest('.ps-alert');
          if (alert) {
            dismissAlert(alert);
          }
        });
      });

      // Optional: expose API for programmatic dismissal
      window.PSAlert = window.PSAlert || {
        dismiss: (el) => el?.classList?.contains('ps-alert') && dismissAlert(el),
        dismissAll: () => document.querySelectorAll('.ps-alert').forEach(dismissAlert),
      };
    },
  };
})(Drupal, once);
