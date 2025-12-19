/**
 * Search Form Behavior
 * Handles show/hide toggle for search form below header.
 *
 * Interactions:
 * - Click search icon in header → show search form
 * - Click close button in search form → hide search form
 * - Submit search form → submit form normally (no special handling)
 * - Escape key → hide search form
 *
 * Accessibility: Focus management, keyboard support, ARIA states
 * Drupal: Uses Drupal.behaviors with once() for idempotency
 */

(function () {
  Drupal.behaviors.psSearchForm = {
    attach: function (context) {
      // Find search form container
      const searchForm = context.querySelector('[data-search-form]');
      if (!searchForm) {
        return;
      }

      // Get search-related buttons and inputs
      const searchInput = searchForm.querySelector('[data-search-input]');
      const closeButton = searchForm.querySelector('[data-search-close]');

      // Find all search trigger buttons in header (could be multiple icon buttons)
      const searchTriggers = context.querySelectorAll(
        '[data-icon="search"].ps-header-actions__icon-link'
      );

      // Process each search trigger button
      searchTriggers.forEach((trigger) => {
        // Use once() to ensure handler runs only once
        once('ps-search-form-trigger', trigger, (element) => {
          element.addEventListener('click', (e) => {
            e.preventDefault();
            openSearchForm();
          });
        });
      });

      // Close button handler
      if (closeButton) {
        closeButton.addEventListener('click', (e) => {
          e.preventDefault();
          closeSearchForm();
        });
      }

      // Escape key to close
      document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && searchForm.classList.contains('ps-search-form--open')) {
          closeSearchForm();
        }
      });

      // Focus search input when form opens
      function openSearchForm() {
        searchForm.classList.add('ps-search-form--open');
        if (searchInput) {
          setTimeout(() => searchInput.focus(), 100);
        }
      }

      // Close search form
      function closeSearchForm() {
        searchForm.classList.remove('ps-search-form--open');
      }

      // Attach functions to window for potential external use
      window.psSearchForm = {
        open: openSearchForm,
        close: closeSearchForm,
      };
    },
  };
})();
