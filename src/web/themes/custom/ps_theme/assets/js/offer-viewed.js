(function (Drupal) {
  'use strict';

  const STORAGE_KEY = 'ps_offer_viewed';
  const MAX_ITEMS = 100;

  /**
   * @namespace
   */
  Drupal.psOfferViewed = {
    /**
     * Returns viewed offer node IDs (most recent first).
     *
     * @return {string[]}
     */
    getIds() {
      try {
        const raw = window.localStorage.getItem(STORAGE_KEY);
        const parsed = raw ? JSON.parse(raw) : [];
        return Array.isArray(parsed) ? parsed.map(String) : [];
      }
      catch (e) {
        return [];
      }
    },

    /**
     * Whether a node ID was already viewed.
     *
     * @param {string|number} nodeId
     *   Offer node ID.
     *
     * @return {boolean}
     */
    has(nodeId) {
      return this.getIds().includes(String(nodeId));
    },

    /**
     * Marks an offer as viewed in localStorage.
     *
     * @param {string|number} nodeId
     *   Offer node ID.
     */
    mark(nodeId) {
      const id = String(nodeId);
      if (!id) {
        return;
      }

      const ids = this.getIds().filter((storedId) => storedId !== id);
      ids.unshift(id);
      window.localStorage.setItem(STORAGE_KEY, JSON.stringify(ids.slice(0, MAX_ITEMS)));
    },
  };

  Drupal.behaviors.psOfferViewedRecord = {
    attach() {
      const currentId = drupalSettings.psOfferViewed?.currentId;
      if (!currentId) {
        return;
      }
      Drupal.psOfferViewed.mark(currentId);
    },
  };
})(Drupal);
