/**
 * @file
 * Hub contact step_need: Continue opens direct webforms by webform id.
 */
(function (Drupal, drupalSettings, once) {
  'use strict';

  const TARGET_FIELD = 'target_webform';

  /**
   * Checks whether the hub wizard is still on the target step.
   *
   * @param {HTMLFormElement} form
   *   Hub contact form element.
   *
   * @return {boolean}
   *   TRUE when step_need is the active wizard page.
   */
  function isHubTargetStep(form) {
    const activeStep = form.querySelector('.progress-step.is-active[data-webform-page]');
    return activeStep?.getAttribute('data-webform-page') === 'step_need';
  }

  /**
   * Loads a direct contact webform (hub continuation).
   *
   * @param {string} webformId
   *   Hub target radio value (webform machine name).
   * @param {object} settings
   *   psForm drupalSettings.
   */
  function openDirectWebformFromHub(webformId, settings) {
    const paths = settings.webformPaths || {};
    const titles = settings.webformTitles || {};
    const baseUrl = Drupal.psFormContactDisplay.resolveLocalizedPath(paths[webformId]);
    const title = titles[webformId];
    if (!baseUrl) {
      return;
    }

    const separator = baseUrl.includes('?') ? '&' : '?';
    const url = `${baseUrl}${separator}from_hub=1`;

    Drupal.psFormContactDisplay.openContactForm(url, title);
  }

  Drupal.behaviors.psFormContactHubNeed = {
    attach(context) {
      once('ps-form-contact-hub-need', 'form.ps-contact-wizard--contact', context).forEach((form) => {
        const settings = drupalSettings.psForm || {};

        form.addEventListener('change', (event) => {
          const target = event.target;
          if (!(target instanceof HTMLInputElement) || target.type !== 'radio' || target.name !== TARGET_FIELD) {
            return;
          }

          const title = (settings.webformTitles || {})[target.value];
          if (title) {
            Drupal.psFormContactDisplay.updateContactDialogTitle(title);
          }
        });

        form.addEventListener(
          'click',
          (event) => {
            const target = event.target;
            if (!(target instanceof Element)) {
              return;
            }

            if (
              !target.closest('[data-drupal-selector="edit-wizard-next"]')
              && !target.closest('[data-drupal-selector="edit-submit"]')
            ) {
              return;
            }

            if (!isHubTargetStep(form)) {
              return;
            }

            const selected = form.querySelector(`input[name="${TARGET_FIELD}"]:checked`);
            if (!selected) {
              return;
            }

            event.preventDefault();
            event.stopPropagation();
            event.stopImmediatePropagation();

            openDirectWebformFromHub(selected.value, settings);
          },
          true,
        );
      });
    },
  };
})(Drupal, drupalSettings, once);
