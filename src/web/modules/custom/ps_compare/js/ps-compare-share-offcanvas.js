/**
 * @file
 * Opens the compare share webform in a Bootstrap offcanvas panel.
 */
(function (Drupal, once, drupalSettings) {
  'use strict';

  const COMPARE_MODAL_Z = 1055;
  const COMPARE_BACKDROP_Z = 1050;
  const SHARE_OFFCANVAS_Z = 1085;
  const SHARE_BACKDROP_Z = 1080;

  const pageSettings = () => drupalSettings.psComparePage || {};
  const panelSettings = () => drupalSettings.psCompare || {};

  const getShareOffcanvasEndpoint = () => (
    pageSettings().shareOffcanvasEndpoint || panelSettings().shareOffcanvasEndpoint || ''
  );

  const buildOffcanvasUrl = () => {
    const endpoint = getShareOffcanvasEndpoint();
    if (!endpoint) {
      return '';
    }

    const compareUrl = panelSettings().compareUrl || pageSettings().compareUrl || window.location.pathname;
    const params = new URLSearchParams();
    if (compareUrl) {
      params.set('compare_url', compareUrl);
    }

    const query = params.toString();
    return query ? `${endpoint}?${query}` : endpoint;
  };

  const adjustShareOffcanvasStack = (offcanvasEl) => {
    const compareModal = document.querySelector('[data-ps-compare-modal].show');
    if (!compareModal || !offcanvasEl) {
      return;
    }

    compareModal.style.zIndex = String(COMPARE_MODAL_Z);
    offcanvasEl.style.zIndex = String(SHARE_OFFCANVAS_Z);

    const backdrops = document.querySelectorAll('.offcanvas-backdrop, .modal-backdrop');
    backdrops.forEach((backdrop, index) => {
      if (backdrop.classList.contains('offcanvas-backdrop')) {
        backdrop.style.zIndex = String(SHARE_BACKDROP_Z);
        return;
      }
      if (index === 0) {
        backdrop.style.zIndex = String(COMPARE_BACKDROP_Z);
      }
    });
  };

  const resetShareOffcanvasStack = (offcanvasEl) => {
    if (offcanvasEl) {
      offcanvasEl.style.zIndex = '';
    }

    const compareModal = document.querySelector('[data-ps-compare-modal]');
    if (compareModal) {
      compareModal.style.zIndex = '';
    }

    document.querySelectorAll('.offcanvas-backdrop, .modal-backdrop').forEach((backdrop) => {
      backdrop.style.zIndex = '';
    });
  };

  const bindOffcanvasStack = (offcanvasEl) => {
    offcanvasEl.addEventListener('show.bs.offcanvas', () => adjustShareOffcanvasStack(offcanvasEl));
    offcanvasEl.addEventListener('hidden.bs.offcanvas', () => resetShareOffcanvasStack(offcanvasEl));
  };

  const openShareOffcanvas = () => {
    const url = buildOffcanvasUrl();
    if (!url || typeof Drupal.ajax !== 'function') {
      return;
    }

    Drupal.ajax({
      url,
      dialogType: 'dialog',
      dialogRenderer: 'off_canvas',
      dialog: {
        dialogClass: 'ps-compare-share-offcanvas',
        width: 420,
      },
      progress: {
        type: 'throbber',
      },
    }).execute();
  };

  Drupal.behaviors.psCompareShareOffcanvas = {
    attach(context) {
      once('ps-compare-share-offcanvas-stack', '.ps-compare-share-offcanvas.offcanvas', context).forEach((offcanvasEl) => {
        if (offcanvasEl.parentElement !== document.body) {
          document.body.appendChild(offcanvasEl);
        }
        bindOffcanvasStack(offcanvasEl);
      });

      once('ps-compare-share-open', '[data-ps-compare-share], [data-ps-compare-share-open]', context).forEach((trigger) => {
        trigger.addEventListener('click', (event) => {
          event.preventDefault();
          openShareOffcanvas();
        });
      });
    },
  };
})(Drupal, once, drupalSettings);
