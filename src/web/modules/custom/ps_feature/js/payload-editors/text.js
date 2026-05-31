/**
 * Feature Builder - TextPayloadEditor.
 *
 * Payload contract: `{ value: string }`
 *
 * Notes:
 * - Placeholder can be driven by payload defaults.
 * - Preview is updated in real-time for immediate author feedback.
 */
(function () {
  'use strict';
  const Base = window.FeatureBuilderEditors.BasePayloadEditor;

  /**
   * Text payload editor.
   */
  class TextPayloadEditor extends Base {
    render() {
      const val = this.payload.value ?? '';
      const placeholder = this.definition.payload_defaults?.placeholder || '';
      return `
<div class="fb-payload">
  <input type="text" class="form-control" id="${this.uid('text')}" value="${this._esc(val)}" placeholder="${this._esc(placeholder)}">
  <div class="fb-preview"><span class="fb-preview-label">${Drupal.t('Preview:')}</span><span class="fb-preview-value">${this._previewTextHtml(val)}</span></div>
</div>`;
    }

    bindEvents(container) {
      const input = container.querySelector('#' + this.uid('text'));
      if (!input) return;
      input.addEventListener('input', () => {
        const preview = container.querySelector('.fb-preview-value');
        if (preview) preview.textContent = this._previewText(input.value);
        this.emit({ value: input.value });
      });
    }

    formatPreview() {
      return this._previewText(this.payload.value ?? '');
    }

    _previewText(val) {
      if (!val) return this.definition.label + ' : ' + Drupal.t('(to complete)');
      return this.definition.label + ' : ' + val;
    }

    /** HTML-escaped version for innerHTML insertion (render()). */
    _previewTextHtml(val) {
      if (!val) return this._escHtml(this.definition.label) + ' : ' + this._escHtml(Drupal.t('(to complete)'));
      return this._escHtml(this.definition.label) + ' : ' + this._escHtml(val);
    }
  }

  window.FeatureBuilderEditors.text = TextPayloadEditor;
})();
