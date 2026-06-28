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

        const settings = drupalSettings.psForm || {};
        const url = (settings.webformPaths || {})[webformId];
        if (!url) {
          return;
        }

        Drupal.ajax({
          url,
          dialogType: 'dialog',
          dialogRenderer: 'off_canvas',
          dialog: {
            dialogClass: settings.offcanvasClass || 'ps-contact-offcanvas',
          },
          progress: {
            type: 'throbber',
          },
        }).execute();
      });
    },
  };
})(Drupal, drupalSettings, once);
