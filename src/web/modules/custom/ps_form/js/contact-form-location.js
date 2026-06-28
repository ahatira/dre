/**
 * @file
 * Location autocomplete for contact wizard search_territory fields.
 */
(function (Drupal, drupalSettings, once) {
  'use strict';

  /**
   * Syncs chip tokens back into the webform textfield value.
   *
   * @param {HTMLInputElement} input
   * @param {object} editor
   */
  function syncLocationValue(input, editor) {
    if (!editor || typeof editor.getTokens !== 'function') {
      return;
    }
    editor.commitDraft();
    const tokens = editor.getTokens();
    input.value = tokens.length ? tokens.join(', ') : '';
  }

  Drupal.behaviors.psContactFormLocation = {
    attach(context) {
      if (!Drupal.psSearchLocationEditor) {
        return;
      }

      const settings = drupalSettings.psForm || {};
      const locationSuggestUrl = settings.locationSuggestUrl || '/api/ps/location-suggest';
      const locationDataUrl = settings.locationDataUrl || '/api/ps/location-data';
      const appendContentLangParam = (params) => {
        const langcode = settings.contentLangcode || '';
        if (langcode) {
          params.set('langcode', langcode);
        }
        return params;
      };

      once('ps-contact-form-location', '.js-ps-contact-location-input', context).forEach((input) => {
        const rootEl = input.closest('.ps-form-location');
        if (!rootEl) {
          return;
        }

        const form = input.closest('form');
        const editor = Drupal.psSearchLocationEditor.attach({
          input: input,
          rootEl: rootEl,
          mode: 'inline',
          locationSuggestUrl: locationSuggestUrl,
          locationDataUrl: locationDataUrl,
          appendContentLangParam: appendContentLangParam,
          initialValue: input.value,
          onChange() {
            syncLocationValue(input, editor);
          },
        });

        if (!editor) {
          return;
        }

        syncLocationValue(input, editor);

        if (form) {
          form.addEventListener('submit', () => {
            syncLocationValue(input, editor);
          });
        }
      });
    },
  };
})(Drupal, drupalSettings, once);
