(function (Drupal, once, drupalSettings) {
  'use strict';

  const pageSettings = () => drupalSettings.psComparePage || {};
  const panelSettings = () => drupalSettings.psCompare || {};

  const getShareModalEndpoint = () => (
    pageSettings().shareModalEndpoint || panelSettings().shareModalEndpoint || ''
  );

  const getShareModalElement = () => document.querySelector('[data-ps-compare-share-modal]');

  const getShareModalBody = () => {
    const modal = getShareModalElement();
    return modal?.querySelector('[data-ps-compare-share-modal-body]') || null;
  };

  const getShareModalInstance = () => {
    const modal = getShareModalElement();
    const bootstrap = window.bootstrap;
    if (!modal || !bootstrap?.Modal) {
      return null;
    }
    return bootstrap.Modal.getOrCreateInstance(modal);
  };

  const SHARE_MODAL_Z = 1075;
  const SHARE_BACKDROP_Z = 1070;
  const COMPARE_MODAL_Z = 1055;
  const COMPARE_BACKDROP_Z = 1050;

  const adjustShareModalStack = (shareModal) => {
    const compareModal = document.querySelector('[data-ps-compare-modal].show');
    if (!compareModal) {
      return;
    }

    compareModal.style.zIndex = String(COMPARE_MODAL_Z);
    shareModal.style.zIndex = String(SHARE_MODAL_Z);

    const backdrops = document.querySelectorAll('.modal-backdrop');
    if (backdrops.length >= 2) {
      backdrops[0].style.zIndex = String(COMPARE_BACKDROP_Z);
      backdrops[backdrops.length - 1].style.zIndex = String(SHARE_BACKDROP_Z);
    }
    else if (backdrops.length === 1) {
      backdrops[0].style.zIndex = String(SHARE_BACKDROP_Z);
    }
  };

  const resetShareModalStack = (shareModal) => {
    shareModal.style.zIndex = '';
    const compareModal = document.querySelector('[data-ps-compare-modal]');
    if (compareModal) {
      compareModal.style.zIndex = '';
    }
    document.querySelectorAll('.modal-backdrop').forEach((backdrop) => {
      backdrop.style.zIndex = '';
    });
  };

  const createShareAjaxStub = (form) => ({
    element: form,
    wrapper: '#ps-compare-share-form-wrapper',
    method: 'html',
    settings: drupalSettings,
    getEffect: () => ({
      showEffect: 'show',
      hideEffect: 'hide',
      showSpeed: 0,
      hideSpeed: 0,
    }),
  });

  const processShareAjaxResponse = (ajaxElement, commands) => {
    if (!Array.isArray(commands) || typeof Drupal.AjaxCommands !== 'function') {
      return;
    }
    const ajaxStub = createShareAjaxStub(ajaxElement);
    const handler = new Drupal.AjaxCommands();
    commands.forEach((command) => {
      const method = handler[command.command];
      if (typeof method === 'function') {
        method.call(handler, ajaxStub, command, 200);
      }
    });
  };

  const bindShareFormAjax = (container) => {
    const form = container.querySelector('form.ps-compare-share-form');
    if (!form || form.dataset.psCompareShareBound === '1') {
      return;
    }
    form.dataset.psCompareShareBound = '1';
    form.addEventListener('submit', async(event) => {
      if (!form.checkValidity()) {
        return;
      }
      event.preventDefault();
      const submit = form.querySelector('[data-drupal-selector="edit-submit"]');
      const formData = new FormData(form);
      if (submit?.name) {
        formData.set(submit.name, submit.value);
        formData.set('_triggering_element_name', submit.name);
        formData.set('_triggering_element_value', submit.value);
      }

      const formId = formData.get('form_id');
      const url = new URL(form.getAttribute('action') || getShareModalEndpoint(), window.location.origin);
      url.searchParams.set('ajax_form', '1');
      if (formId) {
        url.searchParams.set('form_id', formId);
      }

      if (submit) {
        submit.disabled = true;
      }

      try {
        const response = await fetch(url.toString(), {
          method: 'POST',
          body: formData,
          credentials: 'same-origin',
          headers: {
            Accept: 'application/vnd.drupal-ajax',
            'X-Requested-With': 'XMLHttpRequest',
          },
        });
        if (!response.ok) {
          throw new Error('Share submit failed');
        }
        const commands = await response.json();
        processShareAjaxResponse(form, commands);
        Drupal.attachBehaviors(container);
      }
      catch (error) {
        const feedback = form.querySelector('[data-ps-compare-share-feedback]');
        if (feedback) {
          feedback.innerHTML = [
            '<div class="alert alert-warning mb-0" role="alert">',
            Drupal.t('Unable to send the comparison. Please try again.'),
            '</div>',
          ].join('');
        }
      }
      finally {
        if (submit) {
          submit.disabled = false;
        }
      }
    });
  };

  const loadShareForm = async() => {
    const endpoint = getShareModalEndpoint();
    const body = getShareModalBody();
    if (!endpoint || !body) {
      return false;
    }

    body.innerHTML = [
      '<div class="ps-compare-share-modal__loading d-flex justify-content-center py-4" data-ps-compare-share-modal-loading>',
      '<div class="spinner-border text-primary" role="status">',
      '<span class="visually-hidden">Loading</span>',
      '</div></div>',
    ].join('');

    const response = await fetch(endpoint, {
      method: 'GET',
      credentials: 'same-origin',
      headers: { Accept: 'text/html' },
    });

    if (!response.ok) {
      body.innerHTML = [
        '<div class="alert alert-warning mb-0" role="alert">',
        Drupal.t('Unable to load the share form. Select at least two properties to compare.'),
        '</div>',
      ].join('');
      return false;
    }

    const html = await response.text();
    body.innerHTML = html.trim();
    bindShareFormAjax(body);
    Drupal.attachBehaviors(body);
    return true;
  };

  const openShareModal = async(trigger) => {
    const instance = getShareModalInstance();
    const modal = getShareModalElement();
    if (!instance || !modal) {
      return;
    }

    modal.classList.remove('ps-compare-share-modal--sent');

    trigger.disabled = true;
    const loaded = await loadShareForm();
    trigger.disabled = false;

    if (!loaded) {
      instance.show();
      return;
    }

    instance.show();
  };

  Drupal.behaviors.psCompareShare = {
    attach(context) {
      once('ps-compare-share-modal-root', '[data-ps-compare-share-modal]', context).forEach((modal) => {
        if (modal.parentElement !== document.body) {
          document.body.appendChild(modal);
        }
        modal.addEventListener('shown.bs.modal', () => adjustShareModalStack(modal));
        modal.addEventListener('hidden.bs.modal', () => {
          resetShareModalStack(modal);
          modal.classList.remove('ps-compare-share-modal--sent');
        });
      });

      once('ps-compare-share-trigger', '[data-ps-compare-share]', context).forEach((trigger) => {
        trigger.addEventListener('click', (event) => {
          event.preventDefault();
          openShareModal(trigger);
        });
      });
    },
  };

  Drupal.behaviors.psCompareShareSuccess = {
    attach(context) {
      once('ps-compare-share-success', '[data-ps-compare-share-success]', context).forEach((node) => {
        if (typeof Drupal.announce === 'function') {
          const text = node.textContent?.trim();
          if (text) {
            Drupal.announce(text);
          }
        }
      });
    },
  };
})(Drupal, once, drupalSettings);
