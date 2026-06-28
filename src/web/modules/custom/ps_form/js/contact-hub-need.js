/**
 * @file
 * Hub contact step_need: Continue opens direct webforms for non-rent needs.
 */
(function (Drupal, drupalSettings, once) {
  'use strict';

  /**
   * Checks whether the hub wizard is still on the need step.
   *
   * @param {HTMLFormElement} form
   *   Hub contact form element.
   *
   * @return {boolean}
   *   TRUE when step_need is the active wizard page.
   */
  function isHubNeedStep(form) {
    const activeStep = form.querySelector('.progress-step.is-active[data-webform-page]');
    return activeStep?.getAttribute('data-webform-page') === 'step_need';
  }

  /**
   * Loads a direct contact webform (hub continuation).
   *
   * @param {string} need
   *   Hub need radio value.
   * @param {object} settings
   *   psForm drupalSettings.
   */
  function openDirectWebformFromHub(need, settings) {
    const baseUrl = Drupal.psFormContactDisplay.resolveLocalizedPath((settings.needPaths || {})[need]);
    const title = (settings.needTitles || {})[need];
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
        const rentNeed = settings.rentNeed || 'rent';

        form.addEventListener('change', (event) => {
          const target = event.target;
          if (!(target instanceof HTMLInputElement) || target.type !== 'radio' || target.name !== 'need') {
            return;
          }

          if (target.value === rentNeed) {
            Drupal.psFormContactDisplay.updateContactDialogTitle(settings.hubTitle || '');
            return;
          }

          const title = (settings.needTitles || {})[target.value];
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

            if (!target.closest('[data-drupal-selector="edit-wizard-next"]')) {
              return;
            }

            if (!isHubNeedStep(form)) {
              return;
            }

            const selectedNeed = form.querySelector('input[name="need"]:checked');
            if (!selectedNeed || selectedNeed.value === rentNeed) {
              return;
            }

            event.preventDefault();
            event.stopPropagation();
            event.stopImmediatePropagation();

            openDirectWebformFromHub(selectedNeed.value, settings);
          },
          true,
        );
      });
    },
  };
})(Drupal, drupalSettings, once);
