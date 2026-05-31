/**
 * Feature Builder - SortableController.
 *
 * Isolates SortableJS integration from rendering/state concerns.
 * Supports re-initialization after renderer structural updates.
 */
(function () {
  'use strict';

  /**
   * Controller responsible for drag-and-drop ordering.
   */
  class SortableController {
    /**
     * @param {HTMLElement} widgetEl - Le mount point complet du widget
     * @param {FeatureBuilderStateManager} state
     */
    constructor(widgetEl, state) {
      this.widgetEl = widgetEl;
      this.state = state;
      this._sortableInstances = [];
    }

    /**
     * Attach Sortable on all group containers.
     */
    attach() {
      this._detach();
      if (typeof Sortable === 'undefined') return;
      this.widgetEl.querySelectorAll('.fb-items').forEach(list => {
        const instance = Sortable.create(list, {
          // Shared group allows moving items across feature groups.
          group: 'features',
          handle: '.drag-handle',
          animation: 150,
          ghostClass: 'sortable-ghost',
          chosenClass: 'sortable-chosen',
          onEnd: () => this._syncOrder(),
        });
        this._sortableInstances.push(instance);
      });
    }

    /**
     * Reattach after renderer has replaced DOM nodes.
     */
    reattach() {
      this.attach();
    }

    _detach() {
      this._sortableInstances.forEach(s => s.destroy());
      this._sortableInstances = [];
    }

    _syncOrder() {
      // Build a global order across all groups based on actual DOM position.
      // This keeps state canonical even when items move between groups.
      const allIds = [];
      this.widgetEl.querySelectorAll('.fb-items').forEach(list => {
        list.querySelectorAll('.fb-item[data-feature-id]').forEach(el => {
          allIds.push(el.dataset.featureId);
        });
      });
      this.state.reorder(allIds);
    }
  }

  window.FeatureBuilderSortableController = SortableController;
})();
