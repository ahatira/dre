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
      const homepageCapacityByAsset = settings.homepageCapacityByAsset || {};
      const homepageCapacityConfig = settings.homepageCapacityFilterConfig || {};
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
        const opSection = form.querySelector('.js-ps-homepage-op-section');
        const surfaceField = form.querySelector('.js-ps-homepage-surface-field');
        const capacityField = form.querySelector('.js-ps-homepage-capacity-field');
        const capacityLabel = form.querySelector('.js-ps-homepage-capacity-min-label');
        const budgetMaxLabel = form.querySelector('.js-ps-homepage-budget-max-label');
        const surfaceInput = form.querySelector('.js-ps-homepage-surface-min');
        const capacityInput = form.querySelector('.js-ps-homepage-capacity-min');
        const budgetMaxInput = form.querySelector('.js-ps-homepage-budget-max');
        const hiddenLocality = root ? root.querySelector('.js-ps-homepage-locality-hidden') : null;
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

        const isFlexibleOpSelected = () => {
          const activeBtn = form.querySelector('.js-ps-op-btn.is-active');
          return Boolean(activeBtn && activeBtn.dataset.code === 'FLEX');
        };

        const getFilterVisibility = (assetCode) => {
          const key = assetCode || '';
          return filterVisibilityByAsset[key] || {
            show_surface: true,
            show_capacity: false,
            primary_filter: 'surface',
          };
        };

        const isRentOnlyAsset = () => {
          const assetKey = assetSelect && assetSelect.value ? assetSelect.value : '';
          if (!assetKey) {
            return false;
          }
          const assetMap = homepageCapacityByAsset[assetKey] || {};
          return Boolean(assetMap.LOC && assetMap.LOC.hide_operation);
        };

        const enforceRentOnlyFlexibleMode = () => {
          if (!isRentOnlyAsset()) {
            return false;
          }
          form.querySelectorAll('.js-ps-op-btn').forEach((btn) => {
            btn.classList.remove('is-active');
            btn.setAttribute('aria-pressed', 'false');
          });
          const flexBtn = form.querySelector('.js-ps-op-btn[data-code="FLEX"]');
          if (flexBtn) {
            flexBtn.classList.add('is-active');
            flexBtn.setAttribute('aria-pressed', 'true');
          }
          if (opInput) {
            opInput.value = '';
            opInput.disabled = true;
          }
          return true;
        };

        const getHomepageCapacityConfig = () => {
          const assetKey = assetSelect && assetSelect.value ? assetSelect.value : '';
          const assetMap = homepageCapacityByAsset[assetKey] || homepageCapacityByAsset[''] || {};
          const locConfig = assetMap.LOC;
          if (locConfig && locConfig.hide_operation) {
            return locConfig;
          }
          const opKey = getSelectedOp() || '';
          return assetMap[opKey] || assetMap[''] || homepageCapacityConfig;
        };

        const getHomepageBudgetConfig = () => {
          const assetKey = assetSelect && assetSelect.value ? assetSelect.value : '';
          const capacityConfig = getHomepageCapacityConfig();
          // Rent-only assets use LOC label profile keys only — not a selected operation.
          const labelProfileOp = capacityConfig.hide_operation ? 'LOC' : (getSelectedOp() || '');
          const assetMap = homepageBudgetByAsset[assetKey] || homepageBudgetByAsset[''] || {};
          return assetMap[labelProfileOp] || assetMap[''] || homepageBudgetConfig;
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
          enforceRentOnlyFlexibleMode();
          const assetCode = assetSelect && assetSelect.value ? assetSelect.value : '';
          const visibility = getFilterVisibility(assetCode);
          const showSurface = visibility.show_surface;
          const showCapacity = visibility.show_capacity;
          const capacityConfig = getHomepageCapacityConfig();
          const budgetConfig = getHomepageBudgetConfig();

          setFieldHidden(surfaceField, surfaceInput, !showSurface);
          setFieldHidden(capacityField, capacityInput, !showCapacity);

          if (capacityLabel && capacityConfig.field_label) {
            capacityLabel.textContent = capacityConfig.field_label;
          }
          if (capacityInput && capacityConfig.field_label) {
            capacityInput.placeholder = capacityConfig.field_label;
          }

          if (budgetMaxLabel && budgetConfig.max_label) {
            budgetMaxLabel.textContent = budgetConfig.max_label;
          }
          if (budgetMaxInput && budgetConfig.max_label) {
            budgetMaxInput.placeholder = budgetConfig.max_label;
          }
          if (budgetMaxInput && budgetConfig.step) {
            budgetMaxInput.step = budgetConfig.step;
          }

          if (opSection) {
            opSection.hidden = Boolean(capacityConfig.hide_operation);
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
          const capacityConfig = getHomepageCapacityConfig();
          if (capacityConfig.hide_operation) {
            opInput.value = '';
            opInput.disabled = true;
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

        const clearInvalidStates = () => {
          form.querySelectorAll('.is-invalid').forEach((el) => {
            el.classList.remove('is-invalid');
          });
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
          clearInvalidStates();

          let valid = true;
          const capacityConfig = getHomepageCapacityConfig();
          if (!capacityConfig.hide_operation && !getSelectedOp() && !isFlexibleOpSelected()) {
            if (opGroup) {
              opGroup.classList.add('is-invalid');
            }
            valid = false;
          }

          if (!getSelectedTokens().length) {
            if (locationSection) {
              locationSection.classList.add('is-invalid');
            }
            if (localityInput) {
              localityInput.classList.add('is-invalid');
            }
            valid = false;
          }

          if (!assetSelect || !assetSelect.value) {
            if (assetSection) {
              assetSection.classList.add('is-invalid');
            }
            if (assetSelect) {
              assetSelect.classList.add('is-invalid');
            }
            valid = false;
          }

          return valid;
        };

        form.querySelectorAll('.js-ps-op-btn').forEach((button) => {
          button.addEventListener('click', () => {
            if (button.dataset.code !== 'FLEX' && isRentOnlyAsset()) {
              return;
            }
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
            syncOperationField();
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
