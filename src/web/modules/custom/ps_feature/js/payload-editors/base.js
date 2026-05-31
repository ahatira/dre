/**
 * Feature Builder - BasePayloadEditor.
 *
 * Shared contract for all payload editors used by the JS Feature Builder widget.
 *
 * Responsibilities:
 * - render payload-specific UI
 * - bind event listeners
 * - expose a preview string
 * - emit normalized payload updates to state manager
 */
(function () {
  'use strict';

  /**
   * Base editor class.
   */
  class BasePayloadEditor {
    /**
     * @param {object} definition - La définition du catalogue (id, label, type, payload_defaults, options)
     * @param {object} payload    - Valeur courante du payload
     * @param {Function} onChange - Callback quand le payload change (reçoit le nouveau payload)
     */
    constructor(definition, payload, onChange) {
      this.definition = definition;
      this.payload = payload || {};
      this.onChange = onChange;
      this._el = null;
    }

    /**
     * Render editor HTML. Must be overridden.
     */
    render() {
      return '<div class="fb-payload">(editor not implemented)</div>';
    }

    /**
     * Bind DOM events after insertion. Must be overridden when needed.
     */
    bindEvents(container) {}

    /**
     * Return plain-text preview representation.
     */
    formatPreview() {
      return this.definition.label;
    }

    /** Valide le payload et retourne un tableau d'erreurs (vide = OK). */
    validate() {
      return [];
    }

    /**
     * Emit normalized payload change to renderer/state pipeline.
     */
    emit(newPayload) {
      this.payload = newPayload;
      this.onChange(newPayload);
    }

    /**
     * Build deterministic DOM id for editor controls.
     */
    uid(suffix) {
      return 'fb-' + this.definition.id + '-' + suffix;
    }

    /**
     * Escape for full HTML content insertion via innerHTML.
     * Use for any user-controlled value in templates.
     */
    _escHtml(str) {
      return String(str ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
    }

    /**
      * Escape for HTML attribute values.
     */
    _esc(str) {
      return String(str ?? '').replace(/"/g, '&quot;');
    }
  }

  window.FeatureBuilderEditors = window.FeatureBuilderEditors || {};
  window.FeatureBuilderEditors.BasePayloadEditor = BasePayloadEditor;
})();
