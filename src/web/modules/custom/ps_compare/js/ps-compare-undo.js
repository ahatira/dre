/**
 * @file
 * Undo toast after removing a property from the comparison list.
 */
(function (Drupal) {
  'use strict';

  const STORAGE_KEY = 'psCompareLastRemoved';
  const UNDO_DELAY_MS = 5000;
  const TOAST_CONTAINER_ID = 'ps-compare-toast-container';

  let activeToast = null;
  let undoTimeoutId = null;
  let deferredReloadCallback = null;

  const compareSettings = () => drupalSettings.psCompare || drupalSettings.psComparePage || {};

  const isUndoEnabled = () => compareSettings().undoRemoval !== false;

  const getMinItems = () => (
    typeof compareSettings().minItems === 'number' ? compareSettings().minItems : 2
  );

  const isCompareContext = () => (
    document.querySelector('[data-ps-compare-page]')
    || document.querySelector('[data-ps-compare-modal].show')
    || document.querySelector('[data-ps-compare-modal-body] .ps-compare-page')
    || document.querySelector('[data-ps-compare-widget]')
  );

  const saveRemoved = (detail, toggleUrl) => {
    sessionStorage.setItem(STORAGE_KEY, JSON.stringify({
      entityTypeId: detail.entityTypeId,
      entityId: String(detail.entityId),
      toggleUrl,
      countAfterRemove: detail.count,
      removedAt: Date.now(),
    }));
  };

  const clearSavedRemoved = () => {
    sessionStorage.removeItem(STORAGE_KEY);
  };

  const getSavedRemoved = () => {
    try {
      const raw = sessionStorage.getItem(STORAGE_KEY);
      if (!raw) {
        return null;
      }

      const data = JSON.parse(raw);
      if (!data?.toggleUrl || Date.now() - data.removedAt > UNDO_DELAY_MS) {
        clearSavedRemoved();
        return null;
      }

      return data;
    }
    catch (e) {
      return null;
    }
  };

  const getCsrfToken = async () => {
    const response = await fetch('/session/token', {
      method: 'GET',
      credentials: 'same-origin',
      headers: { Accept: 'text/plain' },
    });
    return response.ok ? response.text() : '';
  };

  const findToggleUrl = (detail) => {
    const selector = `[data-ps-compare-toggle][data-entity-type-id="${detail.entityTypeId}"][data-entity-id="${detail.entityId}"]`;
    const button = document.querySelector(selector);
    return button?.dataset?.url || '';
  };

  const ensureToastContainer = () => {
    let container = document.getElementById(TOAST_CONTAINER_ID);
    if (container) {
      return container;
    }

    container = document.createElement('div');
    container.id = TOAST_CONTAINER_ID;
    container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
    container.setAttribute('aria-live', 'polite');
    container.setAttribute('aria-atomic', 'true');
    document.body.appendChild(container);
    return container;
  };

  const dismissActiveToast = () => {
    if (activeToast) {
      activeToast.hide();
      activeToast = null;
    }
    if (undoTimeoutId !== null) {
      window.clearTimeout(undoTimeoutId);
      undoTimeoutId = null;
    }
  };

  const showUndoToast = (message, onUndo) => {
    if (typeof bootstrap === 'undefined' || !bootstrap.Toast) {
      return;
    }

    dismissActiveToast();
    const container = ensureToastContainer();
    const toastNode = document.createElement('div');
    toastNode.className = 'toast align-items-center text-bg-dark border-0 ps-compare-undo-toast';
    toastNode.setAttribute('role', 'status');
    toastNode.setAttribute('aria-live', 'polite');
    toastNode.setAttribute('aria-atomic', 'true');
    toastNode.innerHTML = `
      <div class="d-flex align-items-center gap-2 w-100">
        <div class="toast-body flex-grow-1">${Drupal.checkPlain(message)}</div>
        <button type="button" class="btn btn-sm btn-light me-1 ps-compare-undo-toast__action" data-ps-compare-undo>${Drupal.t('Undo')}</button>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="${Drupal.t('Close')}"></button>
      </div>`;

    container.appendChild(toastNode);
    const toast = bootstrap.Toast.getOrCreateInstance(toastNode, {
      delay: UNDO_DELAY_MS,
      autohide: true,
    });
    activeToast = toast;

    toastNode.querySelector('[data-ps-compare-undo]')?.addEventListener('click', async (event) => {
      event.preventDefault();
      toast.hide();
      await onUndo();
    });

    toast.show();

    toastNode.addEventListener('hidden.bs.toast', () => {
      toastNode.remove();
      if (activeToast === toast) {
        activeToast = null;
      }
      clearSavedRemoved();
      if (typeof deferredReloadCallback === 'function') {
        const callback = deferredReloadCallback;
        deferredReloadCallback = null;
        callback();
      }
    });

    undoTimeoutId = window.setTimeout(() => {
      undoTimeoutId = null;
    }, UNDO_DELAY_MS);
  };

  const restoreRemoved = async () => {
    const saved = getSavedRemoved();
    if (!saved?.toggleUrl) {
      return false;
    }

    try {
      const csrfToken = await getCsrfToken();
      const response = await fetch(saved.toggleUrl, {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
          Accept: 'application/json',
          'X-CSRF-Token': csrfToken,
        },
      });
      const payload = await response.json();
      clearSavedRemoved();
      deferredReloadCallback = null;

      document.dispatchEvent(new CustomEvent('psCompare:changed', {
        detail: { ...payload, restored: true },
      }));
      return response.ok && payload.isCompared === true;
    }
    catch (e) {
      return false;
    }
  };

  const refreshComparePage = async () => {
    const page = document.querySelector('[data-ps-compare-page]');
    const route = document.querySelector('.ps-compare-route');
    const host = route || page?.closest('.ps-compare-route') || page;
    if (!host) {
      return false;
    }

    const response = await fetch(`${window.location.pathname}${window.location.search}`, {
      method: 'GET',
      credentials: 'same-origin',
      headers: { Accept: 'text/html' },
    });
    if (!response.ok) {
      return false;
    }

    const html = await response.text();
    const parser = new DOMParser();
    const doc = parser.parseFromString(html, 'text/html');
    const newHost = doc.querySelector('.ps-compare-route') || doc.querySelector('[data-ps-compare-page]');
    if (!newHost) {
      return false;
    }

    host.replaceWith(newHost);
    Drupal.attachBehaviors(newHost);
    return true;
  };

  Drupal.psCompareUndo = {
    shouldDeferReload: () => getSavedRemoved() !== null,
    shouldDeferModalClose: () => getSavedRemoved() !== null,
    scheduleDeferredReload: (callback) => {
      if (getSavedRemoved()) {
        deferredReloadCallback = callback;
      }
      else {
        callback();
      }
    },
    refreshComparePage,
  };

  document.addEventListener('psCompare:changed', (event) => {
    const detail = event.detail || {};
    if (detail.restored) {
      dismissActiveToast();
      return;
    }
    // Max-limit responses (409) also set isCompared to false — only the red panel alert applies.
    if (detail.limit) {
      return;
    }
    if (detail.isCompared !== false || !isCompareContext()) {
      return;
    }
    if (!isUndoEnabled()) {
      return;
    }

    const toggleUrl = findToggleUrl(detail);
    if (!toggleUrl) {
      return;
    }

    saveRemoved(detail, toggleUrl);
    showUndoToast(
      detail.message || Drupal.t('Removed from comparison.'),
      restoreRemoved,
    );

    const minItems = getMinItems();
    if (
      document.querySelector('[data-ps-compare-page]')
      && typeof detail.count === 'number'
      && detail.count >= minItems
    ) {
      refreshComparePage();
    }
  });
})(Drupal);
