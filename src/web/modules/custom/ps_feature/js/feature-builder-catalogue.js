/**
 * Feature Builder - CatalogueService.
 *
 * Read-only view model over drupalSettings catalogue payload.
 * Keeps grouping/search logic centralized so renderer stays focused on DOM.
 */
(function () {
  'use strict';

  /**
   * Immutable catalogue accessor.
   */
  class CatalogueService {
    constructor(catalogue) {
      this._groups = catalogue.groups || [];
      this._defs = catalogue.definitions || [];
      // Fast O(1) lookup by definition id for renderer/editor lifecycle.
      this._byId = Object.fromEntries(this._defs.map(d => [d.id, d]));
    }

    getGroups() {
      return [...this._groups];
    }

    getDefinitions() {
      return [...this._defs];
    }

    getById(id) {
      return this._byId[id] || null;
    }

    /**
     * Search definitions by label/id (case-insensitive).
     *
     * The `addedIds` argument is intentionally unused here.
     * Added-state styling is handled at renderer level to avoid filtering-out
     * entries from catalogue history/context.
     */
    search(query, addedIds) {
      const q = (query || '').toLowerCase().trim();
      return this._defs.filter(d => {
        if (q && !d.label.toLowerCase().includes(q) && !d.id.toLowerCase().includes(q)) return false;
        return true;
      });
    }

    /**
     * Return definitions grouped by known group ids.
     *
     * Unknown/missing groups are routed to a synthetic `_other` group,
     * keeping catalogue rendering resilient to partial configuration.
     */
    groupedDefinitions() {
      const result = [];
      this._groups.forEach(group => {
        const defs = this._defs.filter(d => d.group === group.id);
        if (defs.length) {
          result.push({ group, definitions: defs });
        }
      });
      // Definitions without a known group remain visible through a safe fallback bucket.
      const ungrouped = this._defs.filter(d => !this._groups.some(g => g.id === d.group));
      if (ungrouped.length) {
        result.push({ group: { id: '_other', label: Drupal.t('Other') }, definitions: ungrouped });
      }
      return result;
    }
  }

  window.FeatureBuilderCatalogueService = CatalogueService;
})();
