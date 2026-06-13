/**
 * @file
 * Homepage hero search — transaction toggles, location chips, GET submit.
 */
(function (Drupal, drupalSettings, once) {
  'use strict';

  Drupal.behaviors.psHomepageSearch = {
    attach(context) {
      const settings = drupalSettings.psSearch || {};

      once('ps-homepage-search-form', '.ps-homepage-search-form', context).forEach((form) => {
        const root = form.closest('[data-ps-homepage-search-entry]') || form.parentElement;
        const opInput = form.querySelector('.js-ps-homepage-operation');
        const opGroup = form.querySelector('.js-ps-homepage-op-group');
        const assetSelect = form.querySelector('.js-ps-homepage-asset-select');
        const localityInput = form.querySelector('.js-ps-locality-input');
        const locationSection = form.querySelector('.js-ps-homepage-location-section');
        const locationRoot = form.querySelector('.ps-filter-bar__item--location');
        const assetSection = form.querySelector('.js-ps-homepage-asset-section');
        const hiddenLocality = root ? root.querySelector('.js-ps-homepage-locality-hidden') : null;
        const errorsBox = root ? root.querySelector('.js-ps-homepage-errors') : null;
        let locationEditor = null;

        const appendContentLangParam = (params) => {
          if (Drupal.psSearchPage && typeof Drupal.psSearchPage.appendContentLangParam === 'function') {
            return Drupal.psSearchPage.appendContentLangParam(params);
          }
          return params;
        };

        if (localityInput && Drupal.psSearchLocationEditor) {
          locationEditor = Drupal.psSearchLocationEditor.attach({
            input: localityInput,
            rootEl: locationRoot,
            mode: 'inline',
            locationSuggestUrl: settings.locationSuggestUrl || '/api/ps/location-suggest',
            locationDataUrl: settings.locationDataUrl || '/api/ps/location-data',
            appendContentLangParam: appendContentLangParam,
            onChange: () => {
              localityInput.classList.remove('is-invalid');
              if (locationSection) {
                locationSection.classList.remove('is-invalid');
              }
            },
          });
        }

        const setActiveOpButton = (button) => {
          form.querySelectorAll('.js-ps-op-btn').forEach((btn) => {
            const active = btn === button;
            btn.classList.toggle('is-active', active);
            btn.setAttribute('aria-pressed', active ? 'true' : 'false');
          });
          if (opGroup) {
            opGroup.classList.remove('is-invalid');
          }
        };

        const syncOperationField = () => {
          if (!opInput) {
            return;
          }
          const activeBtn = form.querySelector('.js-ps-op-btn.is-active');
          const code = activeBtn ? activeBtn.dataset.code : '';
          if (!code || code === 'FLEX') {
            opInput.value = '';
            opInput.disabled = true;
            return;
          }
          opInput.disabled = false;
          opInput.value = code;
        };

        const clearErrors = () => {
          if (errorsBox) {
            errorsBox.hidden = true;
            errorsBox.textContent = '';
          }
          form.querySelectorAll('.is-invalid').forEach((el) => {
            el.classList.remove('is-invalid');
          });
        };

        const showErrors = (messages) => {
          if (!errorsBox || !messages.length) {
            return;
          }
          errorsBox.innerHTML = '';
          if (messages.length === 1) {
            errorsBox.textContent = messages[0];
          }
          else {
            const list = document.createElement('ul');
            list.className = 'ps-homepage-search-entry__errors-list';
            messages.forEach((message) => {
              const item = document.createElement('li');
              item.textContent = message;
              list.appendChild(item);
            });
            errorsBox.appendChild(list);
          }
          errorsBox.hidden = false;
        };

        const stripEmptyOptionalFields = () => {
          form.querySelectorAll('.js-ps-homepage-surface-min, .js-ps-homepage-budget-max').forEach((input) => {
            if (!String(input.value || '').trim()) {
              input.removeAttribute('name');
            }
          });
        };

        const getSelectedTokens = () => {
          return locationEditor ? locationEditor.getTokens() : [];
        };

        const getActiveOperationCode = () => {
          const activeBtn = form.querySelector('.js-ps-op-btn.is-active');
          return activeBtn ? activeBtn.dataset.code : '';
        };

        const validateForm = () => {
          if (locationEditor) {
            locationEditor.commitDraft();
          }
          syncOperationField();
          clearErrors();

          const messages = [];
          const opCode = getActiveOperationCode();
          if (!opCode) {
            messages.push(Drupal.t('Please select a transaction type.'));
            if (opGroup) {
              opGroup.classList.add('is-invalid');
            }
          }

          if (!getSelectedTokens().length) {
            messages.push(Drupal.t('Please enter at least one location.'));
            if (locationSection) {
              locationSection.classList.add('is-invalid');
            }
            if (localityInput) {
              localityInput.classList.add('is-invalid');
            }
          }

          if (!assetSelect || !assetSelect.value) {
            messages.push(Drupal.t('Please select a property type.'));
            if (assetSection) {
              assetSection.classList.add('is-invalid');
            }
            if (assetSelect) {
              assetSelect.classList.add('is-invalid');
            }
          }

          if (messages.length) {
            showErrors(messages);
            return false;
          }

          return true;
        };

        form.querySelectorAll('.js-ps-op-btn').forEach((button) => {
          button.addEventListener('click', () => {
            setActiveOpButton(button);
            syncOperationField();
          });
        });

        if (assetSelect) {
          assetSelect.addEventListener('change', () => {
            assetSelect.classList.remove('is-invalid');
            if (assetSection) {
              assetSection.classList.remove('is-invalid');
            }
          });
        }

        form.addEventListener('submit', (event) => {
          if (!validateForm()) {
            event.preventDefault();
            return;
          }

          stripEmptyOptionalFields();

          if (hiddenLocality) {
            hiddenLocality.innerHTML = '';
            getSelectedTokens().forEach((token) => {
              const input = document.createElement('input');
              input.type = 'hidden';
              input.name = 'locality[]';
              input.value = token;
              hiddenLocality.appendChild(input);
            });
          }
        });

        syncOperationField();
      });
    },
  };
})(Drupal, drupalSettings, once);
