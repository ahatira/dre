/**
 * @file
 * Shared helpers for contact-family form display modes.
 */
(function (Drupal, drupalSettings) {
  'use strict';

  /**
   * Returns psForm display mode settings from drupalSettings.
   *
   * @return {object}
   *   Display mode settings.
   */
  function getDisplayModeSettings() {
    const settings = drupalSettings.psForm || {};
    return settings.displayMode || {
      mode: 'offcanvas',
      offcanvasClass: settings.offcanvasClass || 'ps-contact-offcanvas',
      modalOptions: { width: 800, dialogClass: 'ps-contact-modal modal-dialog-centered modal-lg' },
    };
  }

  /**
   * Prefixes a site path with the current language prefix when needed.
   *
   * @param {string} path
   *   Internal path (e.g. /form/contact).
   *
   * @return {string}
   *   Path ready for Drupal.ajax or navigation.
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
   * Updates the visible dialog title for contact-family panels.
   *
   * @param {string} title
   *   Translated panel title.
   */
  function updateContactDialogTitle(title) {
    if (!title) {
      return;
    }

    document
      .querySelectorAll(
        '#drupal-off-canvas .offcanvas-title, .ps-contact-offcanvas .offcanvas-title, .ui-dialog.ps-contact-modal .ui-dialog-title',
      )
      .forEach((element) => {
        element.textContent = title;
      });
  }

  /**
   * Opens a contact-family webform using the configured display mode.
   *
   * @param {string} url
   *   Form route URL.
   * @param {string} [title]
   *   Optional dialog title.
   */
  function openContactForm(url, title) {
    const localizedUrl = resolveLocalizedPath(url);
    const displayMode = getDisplayModeSettings();

    if (displayMode.mode === 'page') {
      window.location.href = localizedUrl;
      return;
    }

    if (displayMode.mode === 'modal') {
      const ajaxSettings = {
        url: localizedUrl,
        dialogType: 'modal',
        dialog: displayMode.modalOptions || {},
        progress: {
          type: 'throbber',
        },
      };

      if (title) {
        ajaxSettings.dialog.title = title;
      }

      const ajaxObject = Drupal.ajax(ajaxSettings);
      if (title) {
        const originalSuccess = ajaxObject.success.bind(ajaxObject);
        ajaxObject.success = (response, status) => {
          originalSuccess(response, status);
          updateContactDialogTitle(title);
        };
      }
      ajaxObject.execute();
      return;
    }

    const dialog = {
      dialogClass: displayMode.offcanvasClass || 'ps-contact-offcanvas',
    };
    if (title) {
      dialog.title = title;
      updateContactDialogTitle(title);
    }

    const ajaxSettings = {
      url: localizedUrl,
      dialogType: 'dialog',
      dialogRenderer: 'off_canvas',
      dialog,
      progress: {
        type: 'throbber',
      },
    };

    const ajaxObject = Drupal.ajax(ajaxSettings);
    if (title) {
      const originalSuccess = ajaxObject.success.bind(ajaxObject);
      ajaxObject.success = (response, status) => {
        originalSuccess(response, status);
        updateContactDialogTitle(title);
      };
    }
    ajaxObject.execute();
  }

  Drupal.psFormContactDisplay = {
    getDisplayModeSettings,
    resolveLocalizedPath,
    updateContactDialogTitle,
    openContactForm,
  };
})(Drupal, drupalSettings);
