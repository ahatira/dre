/**
 * Feature Builder - RangePayloadEditor.
 *
 * Payload contract: `{ min: number, max: number, unit: string }`
 *
 * UX note:
 * - No hard validation is enforced in JS on min/max ordering.
 * - Server-side validation remains the source of truth.
 */
(function () {
  'use strict';
  const Base = window.FeatureBuilderEditors.BasePayloadEditor;

  /**
   * Range payload editor.
   */
  class RangePayloadEditor extends Base {
    render() {
      const min = this.payload.min ?? '';
      const max = this.payload.max ?? '';
      const unit = this.payload.unit ?? (this.definition.payload_defaults?.unit || '');
      return `
<div class="fb-payload">
  <div class="fb-range-wrap">
    <input type="number" class="form-control" id="${this.uid('min')}" value="${this._esc(min)}" step="any" min="0" placeholder="${Drupal.t('min')}">
    <span class="fb-range-sep">→</span>
    <input type="number" class="form-control" id="${this.uid('max')}" value="${this._esc(max)}" step="any" min="0" placeholder="${Drupal.t('max')}">
    <input type="text" class="form-control" id="${this.uid('unit')}" value="${this._esc(unit)}" placeholder="${Drupal.t('unit')}" style="width:90px">
  </div>
  <div class="fb-preview"><span class="fb-preview-label">${Drupal.t('Preview:')}</span><span class="fb-preview-value">${this._previewTextHtml(min, max, unit)}</span></div>
</div>`;
    }

    bindEvents(container) {
      const minEl = container.querySelector('#' + this.uid('min'));
      const maxEl = container.querySelector('#' + this.uid('max'));
      const unitEl = container.querySelector('#' + this.uid('unit'));
      const preview = container.querySelector('.fb-preview-value');
      const update = () => {
        const mn = minEl.value, mx = maxEl.value, u = unitEl.value;
        if (preview) preview.textContent = this._previewText(mn, mx, u);
        // Keep payload strictly numeric for consistent formatter behavior.
        this.emit({ min: parseFloat(mn) || 0, max: parseFloat(mx) || 0, unit: u });
      };
      [minEl, maxEl, unitEl].forEach(el => el && el.addEventListener('input', update));
    }

    formatPreview() {
      const { min = '', max = '', unit = '' } = this.payload;
      return this._previewText(min, max, unit);
    }

    _previewText(min, max, unit) {
      if (!min && !max) return this.definition.label + ' : ' + Drupal.t('(to complete)');
      const u = unit ? ' ' + unit : '';
      return this.definition.label + ' : ' + min + u + ' → ' + max + u;
    }

    /** HTML-escaped version for innerHTML insertion (render()). */
    _previewTextHtml(min, max, unit) {
      if (!min && !max) return this._escHtml(this.definition.label) + ' : ' + this._escHtml(Drupal.t('(to complete)'));
      const u = unit ? ' ' + this._escHtml(unit) : '';
      return this._escHtml(this.definition.label) + ' : ' + this._escHtml(String(min)) + u + ' → ' + this._escHtml(String(max)) + u;
    }
  }

  window.FeatureBuilderEditors.range = RangePayloadEditor;
})();
