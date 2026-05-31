/**
 * Feature Builder - ListPayloadEditor.
 *
 * Payload contract: `{ codes: string[] }`
 *
 * Data source:
 * - Checkbox options come from `definition.options` (dictionary entries).
 */
(function () {
  'use strict';
  const Base = window.FeatureBuilderEditors.BasePayloadEditor;

  /**
   * Multi-select list payload editor.
   */
  class ListPayloadEditor extends Base {
    render() {
      const selectedCodes = this.payload.codes || [];
      const options = this.definition.options || [];
      if (!options.length) {
        // Safe fallback for incomplete catalogue provisioning.
        return `<div class="fb-payload"><div class="fb-type-hint">${Drupal.t('No options configured for this feature.')}</div></div>`;
      }
      const items = options.map(opt => {
        const checked = selectedCodes.includes(opt.code) ? 'checked' : '';
        return `<label class="fb-list-item"><input type="checkbox" value="${this._esc(opt.code)}" ${checked}> ${this._esc(opt.label)}</label>`;
      }).join('');
      const preview = this._previewText(selectedCodes, options);
      return `
<div class="fb-payload">
  <div class="fb-list-wrap" id="${this.uid('list')}">${items}</div>
  <div class="fb-preview"><span class="fb-preview-label">${Drupal.t('Preview:')}</span><span class="fb-preview-value">${preview}</span></div>
</div>`;
    }

    bindEvents(container) {
      const listWrap = container.querySelector('#' + this.uid('list'));
      if (!listWrap) return;
      const options = this.definition.options || [];
      listWrap.addEventListener('change', () => {
        const codes = Array.from(listWrap.querySelectorAll('input[type="checkbox"]:checked')).map(el => el.value);
        const preview = container.querySelector('.fb-preview-value');
        if (preview) preview.textContent = this._previewText(codes, options);
        this.emit({ codes });
      });
    }

    formatPreview() {
      return this._previewText(this.payload.codes || [], this.definition.options || []);
    }

    _previewText(codes, options) {
      if (!codes.length) return this.definition.label + ' : ' + Drupal.t('(none)');
      const labels = codes.map(c => {
        const opt = options.find(o => o.code === c);
        return opt ? opt.label : c;
      });
      return this.definition.label + ' : ' + labels.join(', ');
    }

    _esc(str) {
      return String(str ?? '').replace(/"/g, '&quot;').replace(/</g, '&lt;');
    }
  }

  window.FeatureBuilderEditors.list = ListPayloadEditor;
})();
