/**
 * Feature Builder - HiddenFieldSync.
 *
 * Serializes JS state into a hidden input consumed by Drupal Form API.
 * This is the persistence bridge between client-side state management and
 * backend field extraction logic.
 */
(function () {
  'use strict';

  /**
   * Hidden input synchronizer.
   */
  class HiddenFieldSync {
    /**
     * @param {HTMLElement} hiddenField - L'input[type=hidden] fb-state-{fieldName}
     * @param {FeatureBuilderStateManager} state
     */
    constructor(hiddenField, state) {
      this.field = hiddenField;
      this.state = state;
      this._timer = null;
    }

    /**
     * Subscribe state changes and keep hidden field updated.
     */
    attach() {
      // Initial sync.
      this._write();
      this.state.subscribe(() => {
        clearTimeout(this._timer);
        // Debounce reduces unnecessary JSON writes while user is typing.
        this._timer = setTimeout(() => this._write(), 150);
      });

      // Immediate flush on submit to avoid race condition with debounce timer.
      // Without this, quick submit actions can lose the latest input changes.
      const form = this.field && this.field.closest('form');
      if (form) {
        form.addEventListener('submit', () => {
          clearTimeout(this._timer);
          this._write();
        }, { capture: true });
      }
    }

    _write() {
      if (!this.field) return;
      this.field.value = JSON.stringify(this.state.getState());
    }
  }

  window.FeatureBuilderHiddenFieldSync = HiddenFieldSync;
})();
