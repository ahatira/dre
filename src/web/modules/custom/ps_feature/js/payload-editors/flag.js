/**
 * Feature Builder - FlagPayloadEditor.
 *
 * Payload contract:
 * - Canonical key: `present` (boolean)
 * - Backward compatibility read: `presence` (legacy payloads)
 *
 * Business semantics:
 * - A disabled flag means "feature not present" and may be hidden by formatter
 *   depending on BO settings (`hide_disabled_flags`).
 */
(function () {
  'use strict';
  const Base = window.FeatureBuilderEditors.BasePayloadEditor;

  /**
   * UI editor for flag payloads.
   */
  class FlagPayloadEditor extends Base {
    /**
     * Resolve current state from canonical or legacy payload key.
     */
    _isPresent() {
      if (Object.prototype.hasOwnProperty.call(this.payload, 'present')) {
        return this.payload.present !== false;
      }
      if (Object.prototype.hasOwnProperty.call(this.payload, 'presence')) {
        return this.payload.presence !== false;
      }
      return true;
    }

    render() {
      const checked = this._isPresent() ? 'checked' : '';
      const uid = this.uid('flag');
      return `
<div class="fb-payload">
  <div class="fb-switch-wrap">
    <label class="fb-switch">
      <input type="checkbox" id="${uid}" ${checked}>
      <span class="fb-switch-slider"></span>
    </label>
    <label for="${uid}" class="small" style="cursor:pointer">${Drupal.t('Present on this offer')}</label>
  </div>
  <div class="fb-type-hint"><i class="bi bi-info-circle me-1"></i>${Drupal.t('Type')} <strong>flag</strong> : ${Drupal.t('if disabled, the feature will not appear on the listing page.')}</div>
  <div class="fb-preview"><span class="fb-preview-label">${Drupal.t('Preview:')}</span><span class="fb-preview-value">${this._previewText(checked !== '')}</span></div>
</div>`;
    }

    bindEvents(container) {
      const input = container.querySelector('#' + this.uid('flag'));
      if (!input) return;
      input.addEventListener('change', () => {
        const preview = container.querySelector('.fb-preview-value');
        if (preview) preview.textContent = this._previewText(input.checked);
        // Always emit canonical key to progressively migrate stored payloads.
        this.emit({ present: input.checked });
      });
    }

    formatPreview() {
      return this._isPresent() ? '✓ ' + this.definition.label : '–';
    }

    /**
     * Preview intentionally mirrors business phrasing used in BO.
     */
    _previewText(checked) {
      return checked ? '✓ ' + this.definition.label : Drupal.t('(not present)');
    }
  }

  window.FeatureBuilderEditors.flag = FlagPayloadEditor;
})();
