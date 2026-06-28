/**
 * @file
 * Direct contact webforms opened from the hub: back to step "Need".
 */
(function (Drupal, drupalSettings, once) {
  'use strict';

  /**
   * Prefixes a site path with the current language prefix when needed.
   *
   * @param {string} path
   *   Internal path (e.g. /form/contact).
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

  Drupal.behaviors.psFormContactWizardFromHub = {
    attach(context) {
      once('ps-form-contact-from-hub', 'form.ps-contact-wizard--from-hub', context).forEach((form) => {
        const backButton = form.querySelector('.ps-contact-hub-back');
        if (!backButton) {
          return;
        }

        backButton.addEventListener('click', (event) => {
          event.preventDefault();

          const settings = drupalSettings.psForm || {};
          const url = resolveLocalizedPath(settings.hubPath || '/form/contact');
          const title = settings.hubTitle || '';

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
            updateOffcanvasTitle(title);
          };
          ajaxObject.execute();
        });
      });
    },
  };
})(Drupal, drupalSettings, once);
