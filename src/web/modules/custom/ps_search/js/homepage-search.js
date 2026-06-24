/**
 * @file
 * Homepage hero search — Figma layout, context matrix rules, hero backgrounds.
 */
(function (Drupal, drupalSettings, once) {
  'use strict';

  Drupal.behaviors.psHomepageSearch = {
    attach(context) {
      const settings = drupalSettings.psSearch || {};
      const filterVisibilityByAsset = settings.filterVisibilityByAsset || {};
      const homepageBudgetByAsset = settings.homepageBudgetByAsset || {};
      const homepageBudgetConfig = settings.homepageBudgetFilterConfig || {};
      const heroBackgroundByAsset = settings.heroBackgroundByAsset || {};
      const heroBackgroundDefault = settings.heroBackgroundDefault || '';

      once('ps-homepage-search-form', '.ps-homepage-search-form', context).forEach((form) => {
        const root = form.closest('[data-ps-homepage-search-entry]') || form.parentElement;
        const heroShell = form.closest('.ps-search-hero');
        const opInput = form.querySelector('.js-ps-homepage-operation');
        const opGroup = form.querySelector('.js-ps-homepage-op-group');
        const assetSelect = form.querySelector('.js-ps-homepage-asset-select');
        const localityInput = form.querySelector('.js-ps-locality-input');
        const locationSection = form.querySelector('.js-ps-homepage-location-section');
        const locationRoot = form.querySelector('.ps-filter-bar__item--location');
        const assetSection = form.querySelector('.js-ps-homepage-asset-section');
        const surfaceField = form.querySelector('.js-ps-homepage-surface-field');
        const capacityField = form.querySelector('.js-ps-homepage-capacity-field');
        const surfaceInput = form.querySelector('.js-ps-homepage-surface-min');
        const capacityInput = form.querySelector('.js-ps-homepage-capacity-min');
        const budgetMaxInput = form.querySelector('.js-ps-homepage-budget-max');
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

        const getSelectedOp = () => {
          const activeBtn = form.querySelector('.js-ps-op-btn.is-active');
          const code = activeBtn ? activeBtn.dataset.code : '';
          if (!code || code === 'FLEX') {
            return null;
          }
          return code;
        };

        const getActiveOperationCode = () => {
          const activeBtn = form.querySelector('.js-ps-op-btn.is-active');
          return activeBtn ? activeBtn.dataset.code : '';
        };

        const getFilterVisibility = (assetCode) => {
          const key = assetCode || '';
          return filterVisibilityByAsset[key] || {
            show_surface: true,
            show_capacity: false,
            primary_filter: 'surface',
          };
        };

        const getHomepageBudgetConfig = () => {
          const assetKey = assetSelect && assetSelect.value ? assetSelect.value : '';
          const opKey = getSelectedOp() || '';
          const assetMap = homepageBudgetByAsset[assetKey] || homepageBudgetByAsset[''] || {};
          return assetMap[opKey] || assetMap[''] || homepageBudgetConfig;
        };

        const setFieldHidden = (fieldEl, inputEl, hidden) => {
          if (!fieldEl) {
            return;
          }
          fieldEl.hidden = hidden;
          if (inputEl && hidden) {
            inputEl.value = '';
          }
        };

        const updateHeroBackground = () => {
          if (!heroShell) {
            return;
          }
          const assetCode = assetSelect && assetSelect.value ? assetSelect.value : '';
          const nextUrl = heroBackgroundByAsset[assetCode] || heroBackgroundDefault;
          if (!nextUrl) {
            return;
          }

          heroShell.querySelectorAll('.ps-search-hero__image, .ps-search-hero__promo-image').forEach((img) => {
            if (img.src === nextUrl || img.getAttribute('src') === nextUrl) {
              return;
            }
            img.classList.add('is-fading');
            const preload = new Image();
            preload.onload = () => {
              img.src = nextUrl;
              img.classList.remove('is-fading');
            };
            preload.onerror = () => {
              img.classList.remove('is-fading');
            };
            preload.src = nextUrl;
          });
        };

        const updateContextFilters = () => {
          const assetCode = assetSelect && assetSelect.value ? assetSelect.value : '';
          const visibility = getFilterVisibility(assetCode);
          const showSurface = visibility.show_surface;
          const showCapacity = visibility.show_capacity;

          setFieldHidden(surfaceField, surfaceInput, !showSurface);
          setFieldHidden(capacityField, capacityInput, !showCapacity);

          const config = getHomepageBudgetConfig();
          if (budgetMaxInput && config.max_placeholder) {
            budgetMaxInput.placeholder = config.max_placeholder;
          }
          if (budgetMaxInput && config.step) {
            budgetMaxInput.step = config.step;
          }

          updateHeroBackground();
        };

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
          const code = getSelectedOp();
          if (!code) {
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
          form.querySelectorAll('.js-ps-homepage-surface-min, .js-ps-homepage-capacity-min, .js-ps-homepage-budget-max').forEach((input) => {
            const field = input.closest('.ps-homepage-search-entry__field');
            if (field && field.hidden) {
              input.removeAttribute('name');
              return;
            }
            if (!String(input.value || '').trim()) {
              input.removeAttribute('name');
            }
          });
        };

        const getSelectedTokens = () => {
          return locationEditor ? locationEditor.getTokens() : [];
        };

        const validateForm = () => {
          if (locationEditor) {
            locationEditor.commitDraft();
          }
          syncOperationField();
          updateContextFilters();
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
            updateContextFilters();
          });
        });

        if (assetSelect) {
          assetSelect.addEventListener('change', () => {
            assetSelect.classList.remove('is-invalid');
            if (assetSection) {
              assetSection.classList.remove('is-invalid');
            }
            updateContextFilters();
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
        updateContextFilters();
      });

      once('ps-homepage-delegate-info', '[data-ps-delegate-info]', context).forEach((trigger) => {
        const tooltip = trigger.closest('.ps-homepage-search-entry__tooltip');
        const panel = tooltip?.querySelector('.ps-homepage-search-entry__tooltip-panel');
        if (!tooltip || !panel) {
          return;
        }

        const closePanel = () => {
          panel.classList.add('is-hidden');
          trigger.setAttribute('aria-expanded', 'false');
        };

        const openPanel = () => {
          panel.classList.remove('is-hidden');
          trigger.setAttribute('aria-expanded', 'true');
        };

        trigger.addEventListener('click', (event) => {
          event.preventDefault();
          event.stopPropagation();
          if (panel.classList.contains('is-hidden')) {
            openPanel();
          }
          else {
            closePanel();
          }
        });

        document.addEventListener('click', (event) => {
          if (!tooltip.contains(event.target)) {
            closePanel();
          }
        });

        document.addEventListener('keydown', (event) => {
          if (event.key === 'Escape') {
            closePanel();
            trigger.focus();
          }
        });
      });
    },
  };
})(Drupal, drupalSettings, once);
