(function (Drupal, once, drupalSettings) {
  const settings = () => drupalSettings.psCompare || {};

  const formatBarLabel = (node, count, isOpen) => {
    const base = node.dataset.baseLabel || 'Comparator';
    if (!isOpen || count <= 0) {
      return base;
    }
    const expanded = node.dataset.expandedLabel || `${base} (${count})`;
    return expanded.replace('@count', String(count));
  };

  const isPanelOpen = (widget) => {
    const collapseEl = widget?.querySelector('[data-ps-compare-panel-collapse]')
      || document.querySelector('[data-ps-compare-panel-collapse]');
    return collapseEl?.classList.contains('show') ?? false;
  };

  const getPanelCount = (widget) => widget?.querySelectorAll('[data-ps-compare-panel-item]').length ?? 0;

  const updateBarCount = (count) => {
    const widget = document.querySelector('[data-ps-compare-widget]');
    const open = isPanelOpen(widget);
    document.querySelectorAll('[data-ps-compare-count]').forEach((node) => {
      node.textContent = String(count);
      node.hidden = count <= 0;
    });
    document.querySelectorAll('[data-ps-compare-bar-label]').forEach((node) => {
      node.textContent = formatBarLabel(node, count, open);
    });
  };

  const refreshBarLabels = (widget, count) => {
    const open = isPanelOpen(widget);
    document.querySelectorAll('[data-ps-compare-bar-label]').forEach((node) => {
      node.textContent = formatBarLabel(node, count, open);
    });
  };

  const syncPanelState = (widget, open) => {
    const panel = widget?.querySelector('[data-ps-compare-panel]');
    panel?.classList.toggle('ps-compare-panel--open', open);
    panel?.classList.toggle('ps-compare-panel--collapsed', !open);
    widget?.querySelectorAll('[data-ps-compare-panel-toggle]').forEach((control) => {
      control.classList.toggle('collapsed', !open);
      control.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
    syncMobileSheetChrome(widget, open);
    refreshBarLabels(widget, getPanelCount(widget));
  };

  const syncMobileSheetChrome = (widget, open) => {
    const mobile = isMobileSearchPage();
    const backdrop = widget?.querySelector('[data-ps-compare-panel-backdrop]');
    if (backdrop) {
      backdrop.hidden = !mobile || !open;
      backdrop.classList.toggle('is-visible', mobile && open);
      backdrop.setAttribute('aria-hidden', mobile && open ? 'false' : 'true');
    }
    document.body.classList.toggle('ps-search-compare-sheet-open', mobile && open);
  };

  const getPanelCollapse = (widget) => {
    const collapseEl = widget?.querySelector('[data-ps-compare-panel-collapse]');
    const bootstrap = window.bootstrap;
    if (!collapseEl || !bootstrap?.Collapse) {
      return null;
    }
    return bootstrap.Collapse.getOrCreateInstance(collapseEl, { toggle: false });
  };

  const isMobileSearchPage = () => (
    window.matchMedia('(max-width: 991.98px)').matches
    && !!document.querySelector('.ps-search-view')
  );

  const setWidgetVisible = (widget, visible) => {
    if (!widget) {
      return;
    }
    const legacyMobileBar = widget.querySelector('[data-ps-compare-mobile-open]');
    if (legacyMobileBar) {
      legacyMobileBar.hidden = true;
    }
    if (isMobileSearchPage()) {
      widget.hidden = false;
      widget.classList.toggle('is-visible', visible);
      widget.classList.toggle('ps-compare-widget--empty', !visible);
      return;
    }
    widget.hidden = !visible;
    widget.classList.toggle('is-visible', visible);
  };

  const setCtaState = (widget, canCompare) => {
    const cta = widget?.querySelector('[data-ps-compare-cta]');
    if (!cta) {
      return;
    }
    cta.classList.toggle('disabled', !canCompare);
    cta.toggleAttribute('disabled', !canCompare);
    cta.setAttribute('aria-disabled', canCompare ? 'false' : 'true');
  };

  let limitAlertTimeoutId = null;

  const hideLimitAlert = (widget) => {
    if (limitAlertTimeoutId !== null) {
      window.clearTimeout(limitAlertTimeoutId);
      limitAlertTimeoutId = null;
    }
    const alert = widget?.querySelector('[data-ps-compare-limit-alert]');
    if (!alert) {
      return;
    }
    alert.classList.add('d-none');
    alert.hidden = true;
  };

  const showLimitAlert = (widget, show) => {
    if (!show) {
      hideLimitAlert(widget);
      return;
    }

    const alert = widget?.querySelector('[data-ps-compare-limit-alert]');
    if (!alert) {
      return;
    }

    alert.classList.remove('d-none');
    alert.hidden = false;

    if (limitAlertTimeoutId !== null) {
      window.clearTimeout(limitAlertTimeoutId);
    }

    const delay = settings().limitAlertDelayMs || 6000;
    limitAlertTimeoutId = window.setTimeout(() => {
      limitAlertTimeoutId = null;
      hideLimitAlert(widget);
    }, delay);
  };

  const getModalElement = () => document.querySelector('[data-ps-compare-modal]');

  const getModalBody = () => {
    const modal = getModalElement();
    return modal?.querySelector('[data-ps-compare-modal-body]') || null;
  };

  const getModalInstance = () => {
    const modal = getModalElement();
    const bootstrap = window.bootstrap;
    if (!modal || !bootstrap?.Modal) {
      return null;
    }
    return bootstrap.Modal.getOrCreateInstance(modal);
  };

  const loadCompareModalContent = async() => {
    const endpoint = settings().modalEndpoint;
    const body = getModalBody();
    if (!endpoint || !body) {
      return false;
    }

    body.innerHTML = '<div class="ps-compare-modal__loading d-flex justify-content-center py-5" data-ps-compare-modal-loading><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading</span></div></div>';

    const response = await fetch(endpoint, {
      method: 'GET',
      credentials: 'same-origin',
      headers: { Accept: 'text/html' },
    });
    if (!response.ok) {
      body.innerHTML = '';
      return false;
    }

    const html = await response.text();
    body.innerHTML = html.trim();
    Drupal.attachBehaviors(body);
    return true;
  };

  const refreshCompareModalIfOpen = async(eventDetail) => {
    const modal = getModalElement();
    if (!modal?.classList.contains('show')) {
      return;
    }
    if (eventDetail?.restored) {
      if (typeof eventDetail?.count === 'number' && eventDetail.count >= (settings().minItems || 2)) {
        await loadCompareModalContent();
      }
      return;
    }
    const minItems = settings().minItems || 2;
    const count = typeof eventDetail?.count === 'number' ? eventDetail.count : null;
    if (eventDetail?.isCompared === false || (count !== null && count < minItems)) {
      if (count !== null && count >= minItems) {
        await loadCompareModalContent();
        return;
      }
      if (Drupal.psCompareUndo?.shouldDeferModalClose?.()) {
        return;
      }
      getModalInstance()?.hide();
      return;
    }
    await loadCompareModalContent();
  };

  const openCompareModal = async() => {
    const instance = getModalInstance();
    if (!instance) {
      return;
    }
    const loaded = await loadCompareModalContent();
    if (!loaded) {
      return;
    }
    instance.show();
  };

  Drupal.psComparePanel = {
    loadCompareModalContent,
    refreshCompareModalIfOpen,
  };

  const fetchPanel = async() => {
    const endpoint = settings().panelEndpoint;
    if (!endpoint) {
      return null;
    }
    const response = await fetch(endpoint, {
      method: 'GET',
      credentials: 'same-origin',
      headers: { Accept: 'application/json' },
    });
    if (!response.ok) {
      return null;
    }
    return response.json();
  };

  const refreshPanelList = async(widget) => {
    const listEndpoint = settings().panelListEndpoint;
    const list = widget?.querySelector('[data-ps-compare-panel-list]');
    if (!list || !listEndpoint) {
      return;
    }
    const response = await fetch(listEndpoint, {
      method: 'GET',
      credentials: 'same-origin',
      headers: { Accept: 'text/html' },
    });
    if (!response.ok) {
      return;
    }
    const html = await response.text();
    const template = document.createElement('template');
    template.innerHTML = html.trim();
    const newList = template.content.querySelector('[data-ps-compare-panel-list]');
    if (newList) {
      list.replaceWith(newList);
      Drupal.attachBehaviors(newList);
    }
  };

  const applyPanelPayload = async(widget, payload) => {
    if (!widget || !payload) {
      return;
    }
    const count = payload.count || 0;
    updateBarCount(count);
    setWidgetVisible(widget, count > 0);
    await refreshPanelList(widget);
    setCtaState(widget, !!payload.canCompare);
  };

  const syncCardStates = async(root) => {
    const endpoint = settings().stateEndpoint;
    if (!endpoint) {
      return;
    }
    const scope = root || document;
    const buttons = scope.querySelectorAll('[data-ps-compare-toggle][data-entity-id]');
    const ids = Array.from(new Set(Array.from(buttons).map((button) => button.dataset.entityId).filter(Boolean)));
    if (ids.length === 0) {
      return;
    }
    const params = new URLSearchParams();
    ids.forEach((id) => params.append('ids[]', id));
    const response = await fetch(`${endpoint}?${params.toString()}`, {
      method: 'GET',
      credentials: 'same-origin',
      headers: { Accept: 'application/json' },
    });
    if (!response.ok) {
      return;
    }
    const payload = await response.json();
    if (payload.states) {
      Object.entries(payload.states).forEach(([entityId, isCompared]) => {
        document.querySelectorAll(
          `[data-ps-compare-toggle][data-entity-type-id="node"][data-entity-id="${entityId}"]`,
        ).forEach((button) => {
          button.classList.toggle('is-active', !!isCompared);
          button.setAttribute('aria-pressed', isCompared ? 'true' : 'false');
          const label = isCompared ? button.dataset.labelRemove : button.dataset.labelAdd;
          if (label) {
            button.setAttribute('aria-label', label);
            button.setAttribute('title', label);
          }
        });
      });
    }
    updateBarCount(payload.count || 0);
  };

  const bindPanelInteractions = (widget) => {
    const collapseEl = widget.querySelector('[data-ps-compare-panel-collapse]');
    const close = widget.querySelector('[data-ps-compare-panel-close]');
    const limitDismiss = widget.querySelector('[data-ps-compare-limit-dismiss]');
    const backdrop = widget.querySelector('[data-ps-compare-panel-backdrop]');
    const cta = widget.querySelector('[data-ps-compare-cta]');
    const collapse = getPanelCollapse(widget);

    collapseEl?.addEventListener('shown.bs.collapse', () => {
      syncPanelState(widget, true);
    });
    collapseEl?.addEventListener('hidden.bs.collapse', () => {
      syncPanelState(widget, false);
    });

    close?.addEventListener('click', () => collapse?.hide());
    backdrop?.addEventListener('click', () => collapse?.hide());
    limitDismiss?.addEventListener('click', () => hideLimitAlert(widget));

    cta?.addEventListener('click', (event) => {
      if (cta.disabled || cta.classList.contains('disabled')) {
        event.preventDefault();
        return;
      }
      event.preventDefault();
      openCompareModal();
    });
  };

  Drupal.behaviors.psComparePanel = {
    attach(context) {
      once('ps-compare-modal-root', '[data-ps-compare-modal]', context).forEach((modal) => {
        if (modal.parentElement !== document.body) {
          document.body.appendChild(modal);
        }
      });

      once('ps-compare-bottom-open', '[data-ps-compare-bottom-open]', context).forEach((button) => {
        button.addEventListener('click', () => {
          const widget = document.querySelector('[data-ps-compare-widget]');
          getPanelCollapse(widget)?.show();
        });
      });

      once('ps-compare-panel', '[data-ps-compare-widget]', context).forEach((widget) => {
        if (isMobileSearchPage()) {
          widget.hidden = false;
          widget.classList.add('ps-compare-widget--empty');
          const legacyMobileBar = widget.querySelector('[data-ps-compare-mobile-open]');
          if (legacyMobileBar) {
            legacyMobileBar.hidden = true;
          }
        }
        bindPanelInteractions(widget);
        fetchPanel().then((payload) => applyPanelPayload(widget, payload));
      });
    },
  };

  document.addEventListener('psCompare:changed', async(event) => {
    const widget = document.querySelector('[data-ps-compare-widget]');
    const detail = event.detail || {};
    if (typeof detail.count === 'number') {
      updateBarCount(detail.count);
      setWidgetVisible(widget, detail.count > 0);
    }
    showLimitAlert(widget, !!detail.limit);
    const payload = await fetchPanel();
    await applyPanelPayload(widget, payload);
    if (detail.limit) {
      return;
    }
    if (detail.restored) {
      const modal = getModalElement();
      if (modal?.classList.contains('show') || (typeof detail.count === 'number' && detail.count >= (settings().minItems || 2))) {
        await loadCompareModalContent();
      }
      return;
    }
    if (detail.isCompared === false || (typeof detail.count === 'number' && detail.count < 2)) {
      await refreshCompareModalIfOpen(detail);
    }
    else {
      const modal = getModalElement();
      if (modal?.classList.contains('show')) {
        await loadCompareModalContent();
      }
    }
  });

  document.addEventListener('ps-search-list-new-content', async(event) => {
    const root = event.target || document;
    await syncCardStates(root);
  });
})(Drupal, once, drupalSettings);
