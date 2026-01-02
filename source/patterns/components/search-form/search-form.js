/**
 * Search Form Component
 * Drupal behavior for expandable search form below header
 *
 * Features:
 * - Toggle search form visibility via trigger button
 * - ESC key closes search form
 * - Close button handler
 * - Auto-focus search input on open
 * - Drupal integration via .ps-search-trigger class
 */

((Drupal, once) => {
  Drupal.behaviors.psSearchForm = {
    /**
     * Attach search form behavior
     * @param {Document|HTMLElement} context - DOM context (entire document or subtree)
     */
    attach(context) {
      // Initialize all search forms
      once('psSearchFormInit', '[data-search-form]', context).forEach((searchForm) => {
        this.initializeSearchForm(searchForm);
      });

      // Attach trigger buttons
      once('psSearchFormTrigger', '.ps-search-trigger', context).forEach((trigger) => {
        trigger.addEventListener('click', (e) => {
          e.preventDefault();
          const searchForm = document.querySelector('[data-search-form]');
          if (searchForm) {
            this.openSearchForm(searchForm);
          }
        });
      });
    },

    /**
     * Initialize search form with event listeners
     * @private
     */
    initializeSearchForm(searchForm) {
      const closeBtn = searchForm.querySelector('[data-search-close]');
      const searchInput = searchForm.querySelector('[data-search-input]');

      // Store input reference for focus management
      searchForm.__searchInput = searchInput;

      // Close button
      if (closeBtn) {
        closeBtn.addEventListener('click', (e) => {
          e.preventDefault();
          this.closeSearchForm(searchForm);
        });
      }

      // Keyboard handling (ESC)
      document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && searchForm.classList.contains('ps-search-form--open')) {
          e.preventDefault();
          this.closeSearchForm(searchForm);
        }
      });
    },

    /**
     * Open search form: show, manage focus, update trigger aria-expanded
     * @public
     * @param {HTMLElement} searchForm - Search form element
     */
    openSearchForm(searchForm) {
      // Already visible
      if (searchForm.classList.contains('ps-search-form--open')) {
        return;
      }

      // Show search form
      searchForm.classList.add('ps-search-form--open');

      // Update trigger button aria-expanded
      const trigger = document.querySelector('[data-search-trigger]');
      if (trigger) {
        trigger.setAttribute('aria-expanded', 'true');
      }

      // Focus search input
      if (searchForm.__searchInput) {
        // Small delay ensures focus after render/animation
        setTimeout(() => {
          searchForm.__searchInput.focus();
        }, 100);
      }
    },

    /**
     * Close search form: hide, clear input (optional), update trigger aria-expanded
     * @public
     * @param {HTMLElement} searchForm - Search form element
     */
    closeSearchForm(searchForm) {
      // Already hidden
      if (!searchForm.classList.contains('ps-search-form--open')) {
        return;
      }

      // Hide search form
      searchForm.classList.remove('ps-search-form--open');

      // Update trigger button aria-expanded
      const trigger = document.querySelector('[data-search-trigger]');
      if (trigger) {
        trigger.setAttribute('aria-expanded', 'false');
      }
    },

    /**
     * Detach behavior (cleanup)
     */
    detach(_context, _settings, trigger) {
      if (trigger === 'unload') {
        // Cleanup if needed (none required for search form)
      }
    },
  };
})(Drupal, once);
