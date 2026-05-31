/**
 * Feature Builder - YesNoPayloadEditor.
 *
 * Payload contract:
 * - `{ value: 'yes'|'no' }`
 *
 * Business rule:
 * - Unlike `flag`, a `no` value is still a meaningful displayed feature.
 * - This is why preview explicitly keeps both outcomes visible.
 */
(function () {
  'use strict';
  const Base = window.FeatureBuilderEditors.BasePayloadEditor;

  /**
   * Radio-based editor for binary explicit values.
   */
  class YesNoPayloadEditor extends Base {
    render() {
      const val = this.payload.value || 'no';
      const yesChecked = val === 'yes' ? 'checked' : '';
      const noChecked = val !== 'yes' ? 'checked' : '';
      const name = this.uid('yn');
      return `
<div class="fb-payload">
  <div class="fb-yesno-wrap">
    <label class="fb-yesno-label">
      <input type="radio" name="${name}" value="yes" ${yesChecked}>
      <span class="badge-type type-yes_no" style="background:#d1fae5;color:#065f46">${Drupal.t('Yes')}</span>
    </label>
    <label class="fb-yesno-label">
      <input type="radio" name="${name}" value="no" ${noChecked}>
      <span class="badge-type type-list" style="background:#fed7d7;color:#822727">${Drupal.t('No')}</span>
    </label>
  </div>
  <div class="fb-type-hint"><i class="bi bi-info-circle me-1"></i>${Drupal.t('Type')} <strong>yes_no</strong> : ${Drupal.t('always displayed on the listing, even if the value is No.')}</div>
  <div class="fb-preview"><span class="fb-preview-label">${Drupal.t('Preview:')}</span><span class="fb-preview-value">${this._previewText(val)}</span></div>
</div>`;
    }

    bindEvents(container) {
      const radios = container.querySelectorAll('[name="' + this.uid('yn') + '"]');
      radios.forEach(r => {
        r.addEventListener('change', () => {
          if (!r.checked) return;
          const preview = container.querySelector('.fb-preview-value');
          if (preview) preview.textContent = this._previewText(r.value);
          this.emit({ value: r.value });
        });
      });
    }

    formatPreview() {
      const val = this.payload.value || 'no';
      return this.definition.label + ' : ' + (val === 'yes' ? Drupal.t('Yes') : Drupal.t('No'));
    }

    _previewText(val) {
      return this.definition.label + ' : ' + (val === 'yes' ? Drupal.t('Yes') : Drupal.t('No'));
    }
  }

  window.FeatureBuilderEditors['yes_no'] = YesNoPayloadEditor;
})();
