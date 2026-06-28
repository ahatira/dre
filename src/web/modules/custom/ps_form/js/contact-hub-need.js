/**
 * @file
 * Hub contact step_need: Continue opens direct webforms for non-rent needs.
 */
(function (Drupal, drupalSettings, once) {
  'use strict';

  /**
   * Prefixes a site path with the current language prefix when needed.
   *
   * @param {string} path
   *   Internal path (e.g. /form/entrust-search).
   *
   * @return {string}
   *   Path ready for Drupal.ajax.
   */
  function resolveLocalizedPath(path) {
    if (!path || path.startsWith('http')) {
      return path;
    }

    const prefix = drupalSettings.path?.pathPrefix || '';
    const normalized = path.startsWith('/') ? path : `/${path}`;
    if (!prefix) {
      return normalized;
    }

    const prefixPath = `/${prefix.replace(/\/$/, '')}`;
    if (normalized.startsWith(`${prefixPath}/`) || normalized === prefixPath) {
      return normalized;
    }

    return `${prefixPath}${normalized}`;
  }

  /**
   * Updates the visible offcanvas title for contact-family panels.
   *
   * @param {string} title
   *   Translated panel title.
   */
  function updateOffcanvasTitle(title) {
    if (!title) {
      return;
    }

    document
      .querySelectorAll('#drupal-off-canvas .offcanvas-title, .ps-contact-offcanvas .offcanvas-title')
      .forEach((element) => {
        element.textContent = title;
      });
  }

  /**
   * Loads a direct contact webform in the offcanvas (hub continuation).
   *
   * @param {string} need
   *   Hub need radio value.
   * @param {object} settings
   *   psForm drupalSettings.
   */
  function openDirectWebformFromHub(need, settings) {
    const baseUrl = resolveLocalizedPath((settings.needPaths || {})[need]);
    const title = (settings.needTitles || {})[need];
    if (!baseUrl) {
      return;
    }

    const separator = baseUrl.includes('?') ? '&' : '?';
    const url = `${baseUrl}${separator}from_hub=1`;

    if (title) {
      updateOffcanvasTitle(title);
    }

    const ajaxSettings = {
      url,
      dialogType: 'dialog',
      dialogRenderer: 'off_canvas',
      dialog: {
        dialogClass: settings.offcanvasClass || 'ps-contact-offcanvas',
        title,
      },
      progress: {
        type: 'throbber',
      },
    };

    const ajaxObject = Drupal.ajax(ajaxSettings);
    const originalSuccess = ajaxObject.success.bind(ajaxObject);
    ajaxObject.success = (response, status) => {
      originalSuccess(response, status);
      if (title) {
        updateOffcanvasTitle(title);
      }
    };
    ajaxObject.execute();
  }

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
            updateOffcanvasTitle(settings.hubTitle || '');
            return;
          }

          const title = (settings.needTitles || {})[target.value];
          if (title) {
            updateOffcanvasTitle(title);
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
