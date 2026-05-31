/**
 * Feature Builder - DatePayloadEditor.
 *
 * Payload contract:
 * - `{ date: 'YYYY-MM-DD', end_date?: 'YYYY-MM-DD' }`
 *
 * Locale strategy:
 * - Preview formatting uses `document.documentElement.lang`.
 * - Avoids hardcoded locale and stays aligned with Drupal language context.
 */
(function () {
  'use strict';
  const Base = window.FeatureBuilderEditors.BasePayloadEditor;

  /**
   * Date payload editor.
   */
  class DatePayloadEditor extends Base {
    render() {
      const date = this.payload.date ?? '';
      const endDate = this.payload.end_date ?? '';
      // date_range is an editor config flag (not a persisted payload value).
      const isRange = this.definition.payload_defaults?.date_range === true;
      const endField = isRange ? `
    <span class="fb-range-sep">→</span>
    <input type="date" class="form-control" id="${this.uid('end')}" value="${this._esc(endDate)}" style="width:auto">` : '';
      return `
<div class="fb-payload">
  <div class="fb-range-wrap">
    <input type="date" class="form-control" id="${this.uid('date')}" value="${this._esc(date)}" style="width:auto">${endField}
  </div>
  <div class="fb-preview"><span class="fb-preview-label">${Drupal.t('Preview:')}</span><span class="fb-preview-value">${this._previewText(date, endDate)}</span></div>
</div>`;
    }

    bindEvents(container) {
      const dateEl = container.querySelector('#' + this.uid('date'));
      const endEl = container.querySelector('#' + this.uid('end'));
      const preview = container.querySelector('.fb-preview-value');
      const update = () => {
        const d = dateEl ? dateEl.value : '';
        const e = endEl ? endEl.value : '';
        if (preview) preview.textContent = this._previewText(d, e);
        const payload = { date: d };
        if (e) payload.end_date = e;
        this.emit(payload);
      };
      dateEl && dateEl.addEventListener('change', update);
      endEl && endEl.addEventListener('change', update);
    }

    formatPreview() {
      return this._previewText(this.payload.date || '', this.payload.end_date || '');
    }

    _previewText(date, endDate) {
      if (!date) return this.definition.label + ' : ' + Drupal.t('(to complete)');
      const lang = document.documentElement.lang || 'en';
      const fmt = (d) => d ? new Date(d + 'T00:00:00').toLocaleDateString(lang) : '';
      if (endDate) return this.definition.label + ' : ' + fmt(date) + ' → ' + fmt(endDate);
      return this.definition.label + ' : ' + fmt(date);
    }

    _esc(str) {
      return String(str ?? '').replace(/"/g, '&quot;');
    }
  }

  window.FeatureBuilderEditors.date = DatePayloadEditor;
})();
