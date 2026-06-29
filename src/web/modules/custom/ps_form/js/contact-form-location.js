/**
 * @file
 * Location autocomplete for contact wizard search_territory fields.
 *
 * Tagify-like UX: chips show labels, the visible input is draft-only.
 * Tokens are serialized into the textfield only on form submit.
 */
(function (Drupal, drupalSettings, once) {
  'use strict';

  /**
   * Serializes chip tokens into the webform textfield (submit / validation).
   *
   * @param {HTMLInputElement} input
   * @param {object} editor
   */
  function syncLocationValueForSubmit(input, editor) {
    if (!editor || typeof editor.getTokens !== 'function') {
      return;
    }
    if (typeof editor.commitDraft === 'function') {
      editor.commitDraft();
    }
    const tokens = editor.getTokens();
    input.value = tokens.length ? tokens.join(', ') : '';
  }

  /**
   * Keeps the visible input free of serialized tokens (chips carry labels).
   *
   * @param {HTMLInputElement} input
   * @param {object} editor
   */
  function clearTokenEchoFromInput(input, editor) {
    if (!editor || typeof editor.getTokens !== 'function') {
      return;
    }
    const tokens = editor.getTokens();
    if (!tokens.length) {
      return;
    }
    const draft = input.value.trim();
    if (!draft) {
      return;
    }
    const serialized = tokens.join(', ');
    if (draft === serialized || tokens.includes(draft)) {
      input.value = '';
    }
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
          resizeInput: false,
          locationSuggestUrl: locationSuggestUrl,
          locationDataUrl: locationDataUrl,
          appendContentLangParam: appendContentLangParam,
          initialValue: input.value,
          onChange() {
            clearTokenEchoFromInput(input, editor);
          },
        });

        if (!editor) {
          return;
        }

        // Editor parses initialValue into chips; keep the visible input draft-only.
        if (editor.getTokens().length > 0) {
          input.value = '';
        }

        if (form) {
          form.addEventListener('submit', () => {
            syncLocationValueForSubmit(input, editor);
          });
        }
      });
    },
  };
})(Drupal, drupalSettings, once);
