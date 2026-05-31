/**
 * Feature Builder - DictionaryPayloadEditor.
 *
 * Payload contract: `{ code: string }`
 *
 * Data source:
 * - Options are hydrated server-side into `definition.options` from ps_dictionary.
 */
(function () {
  'use strict';
  const Base = window.FeatureBuilderEditors.BasePayloadEditor;

  /**
   * Dictionary payload editor.
   */
  class DictionaryPayloadEditor extends Base {
    render() {
      const code = this.payload.code ?? '';
      const options = this.definition.options || [];
      if (!options.length) {
        // Keep editor resilient if dictionary catalogue is missing/misconfigured.
        return `<div class="fb-payload"><div class="fb-type-hint">${Drupal.t('No dictionary entry configured.')}</div></div>`;
      }
      const opts = options.map(opt =>
        `<option value="${this._esc(opt.code)}" ${opt.code === code ? 'selected' : ''}>${this._esc(opt.label)}</option>`
      ).join('');
      return `
<div class="fb-payload">
  <select class="form-select" id="${this.uid('dict')}" style="max-width:280px">
    <option value="">${Drupal.t('— Choose —')}</option>
    ${opts}
  </select>
  <div class="fb-preview"><span class="fb-preview-label">${Drupal.t('Preview:')}</span><span class="fb-preview-value">${this._previewText(code, options)}</span></div>
</div>`;
    }

    bindEvents(container) {
      const select = container.querySelector('#' + this.uid('dict'));
      if (!select) return;
      const options = this.definition.options || [];
      select.addEventListener('change', () => {
        const preview = container.querySelector('.fb-preview-value');
        if (preview) preview.textContent = this._previewText(select.value, options);
        this.emit({ code: select.value });
      });
    }

    formatPreview() {
      return this._previewText(this.payload.code || '', this.definition.options || []);
    }

    _previewText(code, options) {
      if (!code) return this.definition.label + ' : ' + Drupal.t('(to choose)');
      const opt = options.find(o => o.code === code);
      return this.definition.label + ' : ' + (opt ? opt.label : code);
    }

    _esc(str) {
      return String(str ?? '').replace(/"/g, '&quot;').replace(/</g, '&lt;');
    }
  }

  window.FeatureBuilderEditors.dictionary = DictionaryPayloadEditor;
})();
