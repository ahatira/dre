/**
 * Offer Search - Hide/Show List Toggle
 */
(function () {
  'use strict';

  function getToggleLabel(isVisible) {
    const hideLabel = typeof Drupal !== 'undefined' ? Drupal.t('Hide list') : 'Hide list';
    const showLabel = typeof Drupal !== 'undefined' ? Drupal.t('Show list') : 'Show list';

    return isVisible ? hideLabel : showLabel;
  }

  function syncPanelsHeight(contentWrapper) {
    if (!contentWrapper) {
      return;
    }

    if (window.matchMedia('(max-width: 767.98px)').matches) {
      contentWrapper.style.removeProperty('--offer-search-panels-height');
      return;
    }

    const topOffset = contentWrapper.getBoundingClientRect().top;
    const clampedTopOffset = Math.max(0, topOffset);
    const viewportHeight = window.innerHeight || document.documentElement.clientHeight;
    const availableHeight = Math.max(320, Math.round(viewportHeight - clampedTopOffset - 16));

    contentWrapper.style.setProperty('--offer-search-panels-height', availableHeight + 'px');
  }

  function initToggleList() {
    const toggleButton = document.querySelector('.ps-offer-search-view__toggle-list');
    const contentWrapper = document.querySelector('.ps-offer-search-view__content');
    const listColumn = document.querySelector('.ps-offer-search-view__list');
    const mapColumn = document.querySelector('.ps-offer-search-view__map');

    if (!toggleButton || !contentWrapper || !listColumn || !mapColumn) {
      return;
    }

    // Remove any existing listener to avoid duplicates
    const newButton = toggleButton.cloneNode(true);
    toggleButton.parentNode.replaceChild(newButton, toggleButton);

    syncPanelsHeight(contentWrapper);

    window.addEventListener('resize', function () {
      syncPanelsHeight(contentWrapper);
    });

    window.addEventListener('load', function () {
      syncPanelsHeight(contentWrapper);
    });

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
        textElement.textContent = getToggleLabel(newState);
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
