(($, Drupal) => {
  /**
   * Theme function for a contextual trigger.
   *
   * @return {string}
   *   A string representing a DOM fragment.
   */
  Drupal.theme.contextualTrigger = () => {
    return (
      '<button class="trigger focusable visually-hidden dropdown-toggle" ' +
      'type="button" ' +
      'data-bs-toggle="dropdown" ' +
      'aria-expanded="false"' +
      '></button>'
    );
  };

  /**
   * Repair contextual buttons that are missing Bootstrap attributes.
   * This handles cases where buttons are created before our theme function
   * is loaded or when attributes are stripped by other code.
   */
  function repairContextualButtons() {
    const buttons = document.querySelectorAll('.contextual .trigger');

    buttons.forEach((button) => {
      // Check if Bootstrap attributes are missing
      if (!button.getAttribute('data-bs-toggle')) {
        // Add missing Bootstrap attributes
        button.classList.add('dropdown-toggle');
        button.setAttribute('data-bs-toggle', 'dropdown');

        if (!button.getAttribute('aria-expanded')) {
          button.setAttribute('aria-expanded', 'false');
        }

        // Initialize Bootstrap Dropdown if available and not already initialized
        if (typeof bootstrap !== 'undefined' && bootstrap.Dropdown) {
          if (!bootstrap.Dropdown.getInstance(button)) {
            new bootstrap.Dropdown(button);
          }
        }
      }
    });
  }

  // Listen to native Drupal event when contextual instances are added
  window.addEventListener('contextual-instances-added', repairContextualButtons);

  // Also repair on DOM ready and after a short delay (for cached content)
  $(document).ready(() => {
    repairContextualButtons();
    // Retry after 500ms in case some are loaded later
    setTimeout(repairContextualButtons, 500);
  });
})(jQuery, Drupal);
