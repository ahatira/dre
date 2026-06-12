(function (Drupal, once) {
  const TOAST_CONTAINER_ID = 'ps-compare-toast-container';

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

  const shouldUseToast = (button) => {
    if (!button.classList.contains('ps-compare-button--teaser')) {
      return false;
    }
    return !document.querySelector('[data-ps-compare-widget]');
  };

  Drupal.psCompareToast = Drupal.psCompareToast || {};
  Drupal.psCompareToast.show = (message) => {
    if (!message || typeof bootstrap === 'undefined' || !bootstrap.Toast) {
      return;
    }

    const container = ensureToastContainer();
    const toastNode = document.createElement('div');
    toastNode.className = 'toast align-items-center text-bg-primary border-0';
    toastNode.setAttribute('role', 'status');
    toastNode.setAttribute('aria-live', 'polite');
    toastNode.setAttribute('aria-atomic', 'true');
    toastNode.innerHTML = `
      <div class="d-flex">
        <div class="toast-body">${Drupal.checkPlain(message)}</div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="${Drupal.t('Close')}"></button>
      </div>`;
    container.appendChild(toastNode);
    const toast = bootstrap.Toast.getOrCreateInstance(toastNode, { delay: 4000 });
    toast.show();
    toastNode.addEventListener('hidden.bs.toast', () => toastNode.remove());
  };

  document.addEventListener('psCompare:changed', (event) => {
    const detail = event.detail || {};
    if (!detail.isCompared || !detail.message) {
      return;
    }
    const sourceButton = document.querySelector(
      `[data-ps-compare-toggle][data-entity-type-id="${detail.entityTypeId}"][data-entity-id="${detail.entityId}"].ps-compare-button--teaser`,
    );
    if (sourceButton && shouldUseToast(sourceButton)) {
      Drupal.psCompareToast.show(detail.message);
    }
  });
})(Drupal, once);
