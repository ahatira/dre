/**
 * Offer Search - Hide/Show List Toggle
 */
(function () {
  'use strict';

  function initToggleList() {
    const toggleButton = document.querySelector('.ps-offer-search-view__toggle-list');
    const contentWrapper = document.querySelector('.ps-offer-search-view__content');
    const listColumn = document.querySelector('.ps-offer-search-view__list');

    if (!toggleButton || !contentWrapper || !listColumn) {
      return;
    }

    // Remove any existing listener to avoid duplicates
    const newButton = toggleButton.cloneNode(true);
    toggleButton.parentNode.replaceChild(newButton, toggleButton);

    newButton.addEventListener('click', function (e) {
      e.preventDefault();
      const isPressed = this.getAttribute('aria-pressed') === 'true';
      const newState = !isPressed;

      // Update button state
      this.setAttribute('aria-pressed', newState);
      contentWrapper.setAttribute('data-list-visible', newState);

      // Update button text
      const textElement = this.querySelector('.ps-offer-search-view__toggle-list-text');
      if (textElement) {
        textElement.textContent = newState ? (typeof Drupal !== 'undefined' ? Drupal.t('Hide list') : 'Hide list') : (typeof Drupal !== 'undefined' ? Drupal.t('Show list') : 'Show list');
      }

      // Toggle list visibility - CSS handles animation via opacity/visibility
      if (newState) {
        listColumn.style.opacity = '';
        listColumn.style.visibility = '';
      } else {
        listColumn.style.opacity = '0';
        listColumn.style.visibility = 'hidden';
      }
    });
  }

  // Initialize on DOM ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initToggleList);
  } else {
    initToggleList();
  }

  // Re-initialize for AJAX/Drupal behaviors
  if (typeof Drupal !== 'undefined') {
    Drupal.behaviors.offerSearchToggleList = {
      attach: function () {
        initToggleList();
      },
    };
  }
})();
