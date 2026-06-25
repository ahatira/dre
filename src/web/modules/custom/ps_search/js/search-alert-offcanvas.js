/**
 * @file
 * Opens the search alert webform in a Bootstrap offcanvas dialog.
 */
(function (Drupal, once) {
  'use strict';

  const OFFCANVAS_URL = '/api/ps/search-alert/offcanvas';

  function buildOffcanvasUrl() {
    const params = typeof Drupal.psSearchPage?.buildSearchParams === 'function'
      ? Drupal.psSearchPage.buildSearchParams()
      : new URLSearchParams(window.location.search);

    params.set('search_url', window.location.href);
    params.set('search_path', window.location.pathname);

    const query = params.toString();
    return query ? `${OFFCANVAS_URL}?${query}` : OFFCANVAS_URL;
  }

  function openAlertDialog(trigger) {
    Drupal.ajax({
      url: buildOffcanvasUrl(),
      element: trigger,
      dialogType: 'dialog',
      dialogRenderer: 'off_canvas',
      dialog: {
        dialogClass: 'ps-search-alert-offcanvas',
      },
      progress: {
        type: 'throbber',
      },
    }).execute();
  }

  Drupal.behaviors.psSearchAlertOffcanvas = {
    attach(context) {
      once('ps-search-alert-open', '[data-ps-search-alert-open]', context).forEach(function (trigger) {
        trigger.addEventListener('click', function (event) {
          event.preventDefault();
          openAlertDialog(trigger);
        });
      });
    },
  };
})(Drupal, once);
