/**
 * @file
 * Opens contact-family webforms from #form= or ?form= deep links.
 */
(function (Drupal, drupalSettings, once) {
  'use strict';

  Drupal.behaviors.psFormContactDeeplink = {
    attach(context) {
      once('ps-form-contact-deeplink', 'body', context).forEach(() => {
        const hash = window.location.hash.replace(/^#/, '');
        const hashParams = new URLSearchParams(hash);
        const queryParams = new URLSearchParams(window.location.search);
        const webformId = hashParams.get('form') || queryParams.get('form');
        if (!webformId) {
          return;
        }

        const settings = Drupal.psFormContactDisplay.getDisplayModeSettings();
        const paths = (drupalSettings.psForm || {}).webformPaths || {};
        const url = paths[webformId];
        if (!url) {
          return;
        }

        if (settings.mode === 'page') {
          window.location.href = Drupal.psFormContactDisplay.resolveLocalizedPath(url);
          return;
        }

        Drupal.psFormContactDisplay.openContactForm(url);
      });
    },
  };
})(Drupal, drupalSettings, once);
