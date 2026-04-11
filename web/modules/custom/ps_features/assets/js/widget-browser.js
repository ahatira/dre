/**
 * @file
 * JavaScript for the Feature Browser modal widget.
 *
 * Handles feature selection, search filtering, group filtering,
 * and dynamic updates to the feature browser interface.
 */

(function (Drupal, drupalSettings, once) {
  'use strict';

  /**
   * Feature Browser behavior.
   */
  Drupal.behaviors.psFeatureBrowser = {
    attach: function (context, settings) {
      // Initialize browser when modal is opened.
      once('ps-feature-browser', '.ps-feature-browser', context).forEach(function (browser) {
        new FeatureBrowser(browser);
      });
    }
  };

  /**
   * Feature Browser class.
   */
  class FeatureBrowser {
    constructor(element) {
      this.browser = element;
      this.searchInput = element.querySelector('.browser-search');
      this.groupFilter = element.querySelector('.browser-group-filter');
      this.featureCards = element.querySelectorAll('.feature-card');
      this.submitButton = element.querySelector('.browser-submit');
      this.cancelButton = element.querySelector('.browser-cancel');
      this.selectedFeatures = new Set();

      this.init();
    }

    init() {
      // Bind event listeners.
      if (this.searchInput) {
        this.searchInput.addEventListener('input', this.handleSearch.bind(this));
      }

      if (this.groupFilter) {
        this.groupFilter.addEventListener('change', this.handleGroupFilter.bind(this));
      }

      // Handle feature selection.
      this.featureCards.forEach(card => {
        const checkbox = card.querySelector('.feature-select');
        if (checkbox) {
          checkbox.addEventListener('change', this.handleSelection.bind(this));
        }

        // Click anywhere on card to toggle selection.
        card.addEventListener('click', (e) => {
          if (e.target.tagName !== 'INPUT') {
            const checkbox = card.querySelector('.feature-select');
            if (checkbox) {
              checkbox.checked = !checkbox.checked;
              checkbox.dispatchEvent(new Event('change'));
            }
          }
        });
      });

      // Cancel button closes modal.
      if (this.cancelButton) {
        this.cancelButton.addEventListener('click', (e) => {
          e.preventDefault();
          this.close();
        });
      }

      // Submit button validation.
      if (this.submitButton) {
        this.updateSubmitButton();
      }

      // Add selection counter.
      this.addSelectionCounter();
    }

    /**
     * Handles search input filtering.
     */
    handleSearch(e) {
      const query = e.target.value.toLowerCase().trim();

      this.featureCards.forEach(card => {
        const label = (card.dataset.featureLabel || '').toLowerCase();
        const descriptionEl = card.querySelector('.feature-description');
        const description = descriptionEl ? descriptionEl.textContent.toLowerCase() : '';
        const matches = label.includes(query) || description.includes(query);

        if (query === '' || matches) {
          card.removeAttribute('data-hidden');
        } else {
          card.setAttribute('data-hidden', 'true');
        }
      });

      // Hide empty groups.
      this.updateGroupVisibility();
    }

    /**
     * Handles group filter dropdown.
     */
    handleGroupFilter(e) {
      const selectedGroup = e.target.value;

      const groups = this.browser.querySelectorAll('.feature-group');
      groups.forEach(group => {
        const groupId = group.dataset.groupId;

        if (selectedGroup === '' || groupId === selectedGroup) {
          group.removeAttribute('data-hidden');
        } else {
          group.setAttribute('data-hidden', 'true');
        }
      });
    }

    /**
     * Handles feature card selection.
     */
    handleSelection(e) {
      const checkbox = e.target;
      const card = checkbox.closest('.feature-card');
      const featureId = card.dataset.featureId;

      if (checkbox.checked) {
        this.selectedFeatures.add(featureId);
        card.classList.add('selected');
      } else {
        this.selectedFeatures.delete(featureId);
        card.classList.remove('selected');
      }

      this.updateSubmitButton();
      this.updateSelectionCounter();
    }

    /**
     * Updates submit button state based on selection.
     */
    updateSubmitButton() {
      if (!this.submitButton) {
        return;
      }

      if (this.selectedFeatures.size > 0) {
        this.submitButton.disabled = FALSE;
        this.submitButton.value = Drupal.t('Add @count feature(s)', {
          '@count': this.selectedFeatures.size
        });
      } else {
        this.submitButton.disabled = TRUE;
        this.submitButton.value = Drupal.t('Select features to add');
      }
    }

    /**
     * Adds a selection counter to the actions bar.
     */
    addSelectionCounter() {
      const actions = this.browser.querySelector('.browser-actions');
      if (!actions) { return;
      }

      const counter = document.createElement('div');
      counter.className = 'selection-counter';
      counter.dataset.count = '0';
      actions.insertBefore(counter, actions.firstChild);

      this.selectionCounter = counter;
      this.updateSelectionCounter();
    }

    /**
     * Updates the selection counter display.
     */
    updateSelectionCounter() {
      if (!this.selectionCounter) { return;
      }

      const count = this.selectedFeatures.size;
      this.selectionCounter.textContent = Drupal.formatPlural(
        count,
        '1 feature selected',
        '@count features selected',
        {'@count': count}
      );

      if (count > 0) {
        this.selectionCounter.classList.add('has-selection');
      } else {
        this.selectionCounter.classList.remove('has-selection');
      }
    }

    /**
     * Updates group visibility based on visible cards.
     */
    updateGroupVisibility() {
      const groups = this.browser.querySelectorAll('.feature-group');

      groups.forEach(group => {
        const cards = group.querySelectorAll('.feature-card:not([data-hidden="true"])');

        if (cards.length === 0 && !group.hasAttribute('data-hidden')) {
          group.setAttribute('data-hidden', 'true');
        } else if (cards.length > 0 && group.hasAttribute('data-hidden')) {
          group.removeAttribute('data-hidden');
        }
      });
    }

    /**
     * Closes the modal dialog.
     */
    close() {
      // Find the parent dialog and close it.
      const dialog = this.browser.closest('.ui-dialog-content');
      if (dialog && jQuery && jQuery.ui && jQuery.ui.dialog) {
        jQuery(dialog).dialog('close');
      }
    }
  }

  // Export for potential external use.
  Drupal.FeatureBrowser = FeatureBrowser;

})(Drupal, drupalSettings, once);
