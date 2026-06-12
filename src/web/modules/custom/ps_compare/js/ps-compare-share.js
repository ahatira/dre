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
    Drupal.attachBehaviors(body);
    return true;
  };

  const openShareModal = async(trigger) => {
    const instance = getShareModalInstance();
    if (!instance) {
      return;
    }

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
      });

      once('ps-compare-share-trigger', '[data-ps-compare-share]', context).forEach((trigger) => {
        trigger.addEventListener('click', (event) => {
          event.preventDefault();
          openShareModal(trigger);
        });
      });
    },
  };

  document.addEventListener('psCompare:shareSent', () => {
    window.setTimeout(() => {
      getShareModalInstance()?.hide();
    }, 1800);
  });

  Drupal.behaviors.psCompareShareSuccess = {
    attach(context) {
      once('ps-compare-share-success', '[data-ps-compare-share-success]', context).forEach(() => {
        window.setTimeout(() => {
          getShareModalInstance()?.hide();
        }, 1800);
      });
    },
  };
})(Drupal, once, drupalSettings);
