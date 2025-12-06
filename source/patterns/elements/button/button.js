/**
 * Button Toggle Behavior
 *
 * Handles toggle state for buttons with data-ps-toggle="button".
 * Toggles .active class and aria-pressed attribute on click.
 * If pre-toggling, manually add .active class and aria-pressed="true" in markup.
 */

((Drupal, once) => {
  Drupal.behaviors.psButton = {
    attach(context) {
      // Find all toggleable buttons
      const toggleButtons = once('psButton', '[data-ps-toggle="button"]', context);

      toggleButtons.forEach((button) => {
        // Initialize aria-pressed if not already set
        if (!button.hasAttribute('aria-pressed')) {
          const isActive = button.classList.contains('active');
          button.setAttribute('aria-pressed', isActive ? 'true' : 'false');
        }

        // Click handler for toggle
        button.addEventListener('click', () => {
          const isActive = button.classList.contains('active');

          // Toggle class
          button.classList.toggle('active');

          // Toggle ARIA attribute
          button.setAttribute('aria-pressed', !isActive);

          // Dispatch custom event for external listeners
          button.dispatchEvent(
            new CustomEvent('button:toggle', {
              bubbles: true,
              detail: { button, active: !isActive },
            })
          );
        });

        // Keyboard support: Space and Enter
        button.addEventListener('keydown', (e) => {
          if (e.key === ' ' || e.key === 'Enter') {
            e.preventDefault();
            button.click();
          }
        });
      });
    },

    detach(context, _settings, trigger) {
      // Cleanup on unload (AJAX fragment removal)
      if (trigger !== 'unload') {
        return;
      }

      context.querySelectorAll('[data-ps-toggle="button"]').forEach(() => {
        // Event listeners automatically cleaned up when element removed
        // aria-pressed can remain as-is (just data attribute)
      });
    },
  };
})(Drupal, once);
