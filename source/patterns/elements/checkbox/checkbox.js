/**
 * Checkbox Indeterminate State Handler
 *
 * Handles indeterminate state for checkboxes with data-indeterminate="true".
 * The indeterminate property cannot be set via HTML attribute - it must be
 * set via JavaScript (element.indeterminate = true).
 *
 * Usage in HTML/Twig:
 * <input type="checkbox" data-indeterminate="true" />
 *
 * This behavior applies indeterminate state on initialization and preserves
 * it when the checkbox is re-attached to the DOM (e.g., AJAX operations).
 */

((Drupal, once) => {
  Drupal.behaviors.psCheckbox = {
    attach(context) {
      // Find all checkboxes with data-indeterminate attribute
      const indeterminateCheckboxes = once(
        'psCheckbox',
        'input[type="checkbox"][data-indeterminate="true"]',
        context
      );

      indeterminateCheckboxes.forEach((checkbox) => {
        // Set indeterminate state (only way to set it is via JavaScript)
        checkbox.indeterminate = true;

        // Optional: Listen to change events to clear indeterminate state
        // when user explicitly checks/unchecks (typical UX pattern)
        checkbox.addEventListener('change', () => {
          // When user clicks, remove indeterminate state
          checkbox.indeterminate = false;
          checkbox.removeAttribute('data-indeterminate');
        });
      });
    },
  };
})(Drupal, once);

// Manual trigger for immediate execution (Storybook compatibility)
if (typeof window !== 'undefined' && window.Drupal && window.once) {
  // Execute immediately on load
  window.Drupal.behaviors.psCheckbox.attach(document);

  // Re-execute on DOM changes (Storybook story switches)
  if (typeof window.addEventListener === 'function') {
    window.addEventListener('load', () => {
      window.Drupal.behaviors.psCheckbox.attach(document);
    });
  }
}
