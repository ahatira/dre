/**
 * Feature Builder - StateManager.
 *
 * Single source of truth for active features in the current form session.
 *
 * Design goals:
 * - Keep state serializable for hidden input sync.
 * - Emit explicit change types to avoid unnecessary re-render work.
 * - Preserve deterministic feature order through `delta`.
 */
(function () {
  'use strict';

  /**
   * Observable store for feature items.
   */
  class StateManager {
    constructor(initialState) {
      this._features = (initialState && initialState.features) ? [...initialState.features] : [];
      this._subscribers = [];
    }

    /** Retourne une copie des features. */
    getFeatures() {
      return [...this._features];
    }

    /** Retourne l'état sérialisable. */
    getState() {
      return { features: this.getFeatures() };
    }

    /** Abonne un callback aux changements d'état. */
    subscribe(fn) {
      this._subscribers.push(fn);
      return () => {
        this._subscribers = this._subscribers.filter(s => s !== fn);
      };
    }

    /**
     * Add a feature if it does not already exist.
     *
     * Edge case:
     * - Duplicate additions can happen from rapid user clicks in the catalogue.
     * - Guarding by id keeps state stable and avoids duplicate payload editors.
     */
    addFeature(definition) {
      if (this._features.some(f => f.id === definition.id)) return;
      // Do not clone full payload_defaults into runtime payload.
      // payload_defaults also contains editor configuration (rows, max_length, date_range,
      // dictionary metadata, etc.) that must not be persisted as business data values.
      // Only recognized business keys are copied.
      const initialPayload = this._buildInitialPayload(definition);
      this._features.push({
        id: definition.id,
        type: definition.type,
        group: definition.group,
        label: definition.label,
        payload: initialPayload,
        delta: this._features.length,
      });
      this._notify('structure');
    }

    /**
     * Build initial runtime payload from definition defaults.
     *
     * Compatibility note:
     * - Flag canonical key is `present`.
     * - Legacy `presence` may exist in saved content and is handled by formatter/editor logic.
     */
    _buildInitialPayload(definition) {
      const defaults = definition.payload_defaults || {};
      const type = definition.type;
      // Business payload keys by type.
      const payloadKeys = {
        numeric: ['value', 'unit'],
        range:   ['min', 'max', 'unit'],
        text:    ['value'],
        flag:    ['present'],
        yes_no:  ['value'],
        date:    ['date', 'end_date'],
        list:    ['codes'],
        dictionary: ['code'],
      };
      const allowed = payloadKeys[type] || [];
      const payload = {};
      allowed.forEach(key => {
        if (key in defaults) payload[key] = defaults[key];
      });
      return payload;
    }

    /** Supprime une feature par son id. */
    removeFeature(featureId) {
      this._features = this._features.filter(f => f.id !== featureId);
      this._reIndex();
      this._notify('structure');
    }

    /** Met à jour le payload d'une feature. */
    updatePayload(featureId, newPayload) {
      const f = this._features.find(f => f.id === featureId);
      if (f) {
        f.payload = { ...newPayload };
        this._notify('payload');
      }
    }

    /**
     * Reorder features according to DOM order emitted by Sortable.
     */
    reorder(orderedIds) {
      const map = Object.fromEntries(this._features.map(f => [f.id, f]));
      this._features = orderedIds.map((id, idx) => {
        const f = map[id];
        if (f) f.delta = idx;
        return f;
      }).filter(Boolean);
      this._notify('structure');
    }

    _reIndex() {
      this._features.forEach((f, i) => f.delta = i);
    }

    /**
     * Notify subscribers with immutable snapshot.
     */
    _notify(changeType = 'structure') {
      const state = this.getState();
      this._subscribers.forEach(fn => fn(state, changeType));
    }
  }

  window.FeatureBuilderStateManager = StateManager;
})();
