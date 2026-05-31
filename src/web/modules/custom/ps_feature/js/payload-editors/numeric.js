/**
 * Feature Builder - NumericPayloadEditor.
 *
 * Payload contract: `{ value: number, unit: string }`
 *
 * Rendering strategy:
 * - editor uses input events for real-time preview updates.
 * - emitted value is normalized to number (fallback 0) to keep payload shape stable.
 */
(function () {
  'use strict';
  const Base = window.FeatureBuilderEditors.BasePayloadEditor;

  /**
   * Numeric payload editor.
   */
  class NumericPayloadEditor extends Base {
    render() {
      const val = this.payload.value ?? '';
      const unit = this.payload.unit ?? (this.definition.payload_defaults?.unit || '');
      return `
<div class="fb-payload">
  <div class="fb-numeric-wrap">
    <input type="number" class="form-control" id="${this.uid('num')}" value="${this._esc(val)}" step="any" min="0" placeholder="0">
    <input type="text" class="form-control" id="${this.uid('unit')}" value="${this._esc(unit)}" placeholder="${Drupal.t('unit')}" style="width:90px">
  </div>
  <div class="fb-preview"><span class="fb-preview-label">${Drupal.t('Preview:')}</span><span class="fb-preview-value">${this._previewTextHtml(val, unit)}</span></div>
</div>`;
    }

    bindEvents(container) {
      const numEl = container.querySelector('#' + this.uid('num'));
      const unitEl = container.querySelector('#' + this.uid('unit'));
      const preview = container.querySelector('.fb-preview-value');
      const update = () => {
        const v = numEl.value, u = unitEl.value;
        if (preview) preview.textContent = this._previewText(v, u);
        // Normalize to numeric payload to align with backend validators/formatters.
        this.emit({ value: parseFloat(v) || 0, unit: u });
      };
      numEl && numEl.addEventListener('input', update);
      unitEl && unitEl.addEventListener('input', update);
    }

    formatPreview() {
      const { value = '', unit = '' } = this.payload;
      return this._previewText(value, unit);
    }

    _previewText(val, unit) {
      if (!val && val !== 0) return this.definition.label + ' : ' + Drupal.t('(to complete)');
      return this.definition.label + ' : ' + val + (unit ? ' ' + unit : '');
    }

    /** HTML-escaped version for innerHTML insertion (render()). */
    _previewTextHtml(val, unit) {
      if (!val && val !== 0) return this._escHtml(this.definition.label) + ' : ' + this._escHtml(Drupal.t('(to complete)'));
      return this._escHtml(this.definition.label) + ' : ' + this._escHtml(String(val)) + (unit ? ' ' + this._escHtml(unit) : '');
    }
  }

  window.FeatureBuilderEditors.numeric = NumericPayloadEditor;
})();
