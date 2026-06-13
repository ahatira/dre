/**
 * @file
 * Opens the compare share webform in a Drupal off-canvas dialog.
 */
(function (Drupal, once, drupalSettings, $) {
  'use strict';

  const COMPARE_MODAL_Z = 1055;
  const COMPARE_BACKDROP_Z = 1050;
  const SHARE_OFFCANVAS_Z = 1090;

  /** @type {object|null} */
  let compareModalWithPausedFocus = null;

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

  const getShareOffcanvasElement = () => (
    document.querySelector('#drupal-off-canvas.ps-compare-share-offcanvas')
    || document.querySelector('.offcanvas.ps-compare-share-offcanvas')
    || document.querySelector('.ui-dialog.ps-compare-share-offcanvas')
  );

  const getOpenCompareModal = () => document.querySelector('[data-ps-compare-modal].show');

  const isCompareModalOpen = () => Boolean(getOpenCompareModal());

  const getCompareModalInstance = () => {
    if (typeof bootstrap === 'undefined' || !bootstrap.Modal) {
      return null;
    }
    const compareModal = getOpenCompareModal();
    if (!compareModal) {
      return null;
    }
    return bootstrap.Modal.getInstance(compareModal) || bootstrap.Modal.getOrCreateInstance(compareModal);
  };

  /**
   * Bootstrap modal focus trap blocks keyboard input in stacked off-canvas.
   */
  const pauseCompareModalFocusTrap = () => {
    if (!isCompareModalOpen()) {
      return;
    }

    const instance = getCompareModalInstance();
    if (!instance?._focustrap?.deactivate) {
      return;
    }

    instance._focustrap.deactivate();
    compareModalWithPausedFocus = instance;
  };

  const resumeCompareModalFocusTrap = () => {
    const instance = compareModalWithPausedFocus;
    compareModalWithPausedFocus = null;
    if (!instance?._focustrap?.activate || !instance._isShown) {
      return;
    }
    instance._focustrap.activate();
  };

  const adjustShareOffcanvasStack = () => {
    if (!isCompareModalOpen()) {
      return;
    }

    const compareModal = getOpenCompareModal();
    if (compareModal) {
      compareModal.style.zIndex = String(COMPARE_MODAL_Z);
      // Let clicks and focus reach the off-canvas above the modal.
      compareModal.style.pointerEvents = 'none';
    }

    const offcanvasEl = getShareOffcanvasElement();
    const wrapper = document.querySelector('#drupal-off-canvas-wrapper');
    if (wrapper) {
      wrapper.style.zIndex = String(SHARE_OFFCANVAS_Z);
      wrapper.style.pointerEvents = 'auto';
    }
    if (offcanvasEl) {
      offcanvasEl.style.zIndex = String(SHARE_OFFCANVAS_Z);
      offcanvasEl.style.pointerEvents = 'auto';
    }

    document.querySelectorAll('.modal-backdrop.show').forEach((backdrop) => {
      backdrop.style.zIndex = String(COMPARE_BACKDROP_Z);
    });
  };

  const resetShareOffcanvasStack = () => {
    resumeCompareModalFocusTrap();

    const compareModal = document.querySelector('[data-ps-compare-modal]');
    if (compareModal) {
      compareModal.style.zIndex = '';
      compareModal.style.pointerEvents = '';
    }

    const offcanvasEl = getShareOffcanvasElement();
    const wrapper = document.querySelector('#drupal-off-canvas-wrapper');
    if (wrapper) {
      wrapper.style.zIndex = '';
      wrapper.style.pointerEvents = '';
    }
    if (offcanvasEl) {
      offcanvasEl.style.zIndex = '';
      offcanvasEl.style.pointerEvents = '';
    }

    document.querySelectorAll('.modal-backdrop.show').forEach((backdrop) => {
      backdrop.style.zIndex = '';
    });
  };

  const onShareOffcanvasOpen = () => {
    adjustShareOffcanvasStack();
    pauseCompareModalFocusTrap();
  };

  const onShareOffcanvasClose = () => {
    resetShareOffcanvasStack();
  };

  const scheduleStackAdjust = () => {
    window.requestAnimationFrame(onShareOffcanvasOpen);
    window.setTimeout(onShareOffcanvasOpen, 50);
    window.setTimeout(onShareOffcanvasOpen, 300);
  };

  const focusShareConfirmation = () => {
    const offcanvasEl = getShareOffcanvasElement();
    if (!offcanvasEl) {
      return;
    }

    const confirmation = offcanvasEl.querySelector('.webform-confirmation, [data-ps-compare-share-success]');
    if (!confirmation) {
      return;
    }

    const scrollHost = offcanvasEl.querySelector('.offcanvas-body, .ui-dialog-content, .ps-compare-share-offcanvas__content')
      || offcanvasEl;
    scrollHost.scrollTop = 0;
    confirmation.scrollIntoView({ block: 'nearest' });
    confirmation.setAttribute('tabindex', '-1');
    confirmation.focus({ preventScroll: true });
  };

  const bindShareFormAjax = () => {
    if (typeof $ === 'undefined') {
      return;
    }

    $(document).off('ajaxComplete.psCompareShareForm').on('ajaxComplete.psCompareShareForm', (_event, _xhr, settings) => {
      const payload = typeof settings?.data === 'string' ? settings.data : '';
      if (!payload.includes('compare_share') && !String(settings?.url || '').includes('compare_share')) {
        return;
      }
      window.requestAnimationFrame(focusShareConfirmation);
    });
  };

  const bindOffcanvasStack = (offcanvasEl) => {
    offcanvasEl.addEventListener('show.bs.offcanvas', onShareOffcanvasOpen);
    offcanvasEl.addEventListener('shown.bs.offcanvas', onShareOffcanvasOpen);
    offcanvasEl.addEventListener('hidden.bs.offcanvas', onShareOffcanvasClose);
  };

  const openShareOffcanvas = () => {
    const url = buildOffcanvasUrl();
    if (!url || typeof Drupal.ajax !== 'function') {
      return;
    }

    const ajaxObject = Drupal.ajax({
      url,
      dialogType: 'dialog',
      dialogRenderer: 'off_canvas',
      dialog: {
        dialogClass: 'ps-compare-share-offcanvas',
      },
      progress: {
        type: 'throbber',
      },
    });

    const originalSuccess = ajaxObject.success?.bind(ajaxObject);
    ajaxObject.success = function (response, status) {
      if (originalSuccess) {
        originalSuccess(response, status);
      }
      else {
        Drupal.Ajax.prototype.success.call(this, response, status);
      }
      onShareOffcanvasOpen();
    };

    ajaxObject.execute();
  };

  Drupal.behaviors.psCompareShareOffcanvas = {
    attach(context) {
      once('ps-compare-share-stack', 'body', context).forEach(() => {
        $(document).on('dialogopen.psCompareShare', '#drupal-off-canvas', onShareOffcanvasOpen);
        $(document).on('dialogclose.psCompareShare', '#drupal-off-canvas', onShareOffcanvasClose);
        bindShareFormAjax();
      });

      once('ps-compare-share-offcanvas-stack', '#drupal-off-canvas.ps-compare-share-offcanvas, .offcanvas.ps-compare-share-offcanvas', context).forEach((offcanvasEl) => {
        bindOffcanvasStack(offcanvasEl);
        onShareOffcanvasOpen();
      });

      once('ps-compare-share-open', '[data-ps-compare-share], [data-ps-compare-share-open]', context).forEach((trigger) => {
        trigger.addEventListener('click', (event) => {
          event.preventDefault();
          openShareOffcanvas();
        });
      });
    },
  };
})(Drupal, once, drupalSettings, jQuery);
