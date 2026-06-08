/**
 * @file
 * Search Filter Bar — BNPPRE-style filter behaviour.
 *
 * Filter sections (matching BNPPRE.fr):
 *  1. Type + need  — asset type cards + operation type buttons (popin).
 *  2. Localisation — chips + grouped suggest popin (Figma Location(s)).
 *  3. Surface      — min/max area range (popin).
 *  4. Budget       — min/max price range (popin).
 *
 * Shared behaviour:
 *  - No immediate navigation on selection — user confirms with "Afficher X".
 *  - All "Afficher X résultats" buttons show the SAME real-time count.
 *  - Navigating builds a SEO URL (for type+op) + query params (for other filters).
 *  - "Clear" in each section resets only that section's values.
 *  - Outside click / Escape closes all open popins.
 */

(function (Drupal, drupalSettings, once) {
  'use strict';

  Drupal.behaviors.psSearchFilterBar = {
    attach(context) {
      const settings = drupalSettings.psSearch || {};
      const langPrefix = settings.langPrefix || '';
      const opSlugs = settings.opSlugs || {};
      const assetSlugs = settings.assetSlugs || {};
      const countUrl = settings.countUrl || '/ps-search/count';
      const locationSuggestUrl = settings.locationSuggestUrl || '/ps-search/location-suggest';
      const locationDataUrl = settings.locationDataUrl || '/ps-search/location-data';
      const searchPath = settings.searchPath || '/find-property';
      const filterVisibilityByAsset = settings.filterVisibilityByAsset || {};
      const budgetFilterConfig = settings.budgetFilterConfig || {};
      const budgetFilterByAsset = settings.budgetFilterByAsset || {};
      const capacityFilterLabel = settings.capacityFilterLabel || Drupal.t('Capacity');
      const moreCriteriaGroupUrl = settings.moreCriteriaGroupUrl || '/ps-search-filters/more-criteria';
      const currentParams = new URLSearchParams(window.location.search);
      const loadedMoreGroups = {};

      function mountMoreOffcanvas(offcanvasEl) {
        if (offcanvasEl && offcanvasEl.parentElement !== document.body) {
          document.body.appendChild(offcanvasEl);
        }
      }

      once('ps-more-offcanvas-mount', '#ps-more-offcanvas', context).forEach(function (offcanvasEl) {
        mountMoreOffcanvas(offcanvasEl);
      });

      function getFilterVisibility(assetCode) {
        const key = assetCode || '';
        return filterVisibilityByAsset[key] || {
          show_surface: true,
          show_capacity: false,
          primary_filter: 'surface',
        };
      }

      // ── Shared state (all filter sections) ───────────────────────────────
      let selectedOp = settings.activeOp || null;
      let opFlexible = !selectedOp;
      let selectedAsset = settings.activeAsset || null;
      let selectedLocalityTokens = [];
      let selectedLocalityData = [];
      let selectedLocality = '';
      let surfaceMin = currentParams.get('surface[min]') || '';
      let surfaceMax = currentParams.get('surface[max]') || '';
      let budgetMin = currentParams.get('budget[min]') || '';
      let budgetMax = currentParams.get('budget[max]') || '';
      let capacityMin = currentParams.get('capacity[min]') || '';
      let capacityMax = currentParams.get('capacity[max]') || '';
      let moreFilterSchema = settings.moreFilterSchema || [];
      let moreFilters = {};
      const localityArrayParams = currentParams.getAll('locality[]');
      let countDebounce = null;
      let locationSuggestDebounce = null;
      let activeSuggestionIndex = -1;
      let currentCount = null;

      function initMoreFiltersFromUrl() {
        moreFilterSchema.forEach(function (schema) {
          const param = schema.param;
          const widget = schema.widget;
          if (!param) {
            return;
          }
          if (widget === 'checkbox') {
            if (currentParams.get(param) === '1') {
              moreFilters[param] = true;
            }
            return;
          }
          if (widget === 'yes_no') {
            const yn = currentParams.get(param);
            if (yn === '1' || yn === '0') {
              moreFilters[param] = yn;
            }
            return;
          }
          if (widget === 'tags') {
            const tags = currentParams.getAll(param);
            if (tags.length) {
              moreFilters[param] = tags.slice();
            }
            return;
          }
          if (widget === 'range') {
            const min = currentParams.get(param + '[min]') || currentParams.get(param + '_min') || '';
            const max = currentParams.get(param + '[max]') || currentParams.get(param + '_max') || '';
            if (min || max) {
              moreFilters[param] = { min: min, max: max };
            }
            return;
          }
          const value = currentParams.get(param);
          if (value) {
            moreFilters[param] = value;
          }
        });
      }

      initMoreFiltersFromUrl();

      function updateAssetMode() {
        const visibility = getFilterVisibility(selectedAsset);
        const surfaceItem = document.querySelector('.js-ps-surface-item');
        const capacityItem = document.querySelector('.js-ps-capacity-item');

        if (surfaceItem) {
          surfaceItem.hidden = !visibility.show_surface;
        }
        if (capacityItem) {
          capacityItem.hidden = !visibility.show_capacity;
        }

        if (!visibility.show_surface) {
          surfaceMin = '';
          surfaceMax = '';
          document.querySelectorAll('.js-ps-surface-min, .js-ps-surface-max').forEach(function (el) {
            el.value = '';
          });
          updateSurfaceLabel();
        }
        if (!visibility.show_capacity) {
          capacityMin = '';
          capacityMax = '';
          document.querySelectorAll('.js-ps-capacity-min, .js-ps-capacity-max').forEach(function (el) {
            el.value = '';
          });
          updateCapacityLabel();
        }
      }

      function getBudgetConfig() {
        const assetKey = selectedAsset || '';
        const opKey = selectedOp || '';
        const assetMap = budgetFilterByAsset[assetKey] || budgetFilterByAsset[''] || {};
        return assetMap[opKey] || assetMap[''] || budgetFilterConfig;
      }

      function updateBudgetUi() {
        const config = getBudgetConfig();
        const suffix = config.value_suffix || ' \u20ac';
        const fieldLabel = document.querySelector('.js-ps-budget-field-label');
        const minLabel = document.querySelector('.js-ps-budget-min-label');
        const maxLabel = document.querySelector('.js-ps-budget-max-label');
        const units = document.querySelectorAll('.js-ps-budget-unit');
        const minInput = document.querySelector('.js-ps-budget-min');
        const maxInput = document.querySelector('.js-ps-budget-max');

        if (fieldLabel && config.field_label) {
          fieldLabel.textContent = config.field_label;
        }
        if (minLabel && config.min_label) {
          minLabel.textContent = config.min_label;
        }
        if (maxLabel && config.max_label) {
          maxLabel.textContent = config.max_label;
        }
        units.forEach(function (unitEl) {
          if (config.input_unit) {
            unitEl.textContent = config.input_unit;
          }
        });
        if (minInput && config.step) {
          minInput.step = config.step;
        }
        if (maxInput && config.step) {
          maxInput.step = config.step;
        }

        updateBudgetLabel(config, suffix);
      }

      function updateBudgetLabel(config, suffixOverride) {
        const configResolved = config || getBudgetConfig();
        const suffix = suffixOverride || configResolved.value_suffix || ' \u20ac';
        const lbl = document.querySelector('.js-ps-budget-label');
        if (!lbl) return;
        if (budgetMin && budgetMax) lbl.textContent = budgetMin + '\u2013' + budgetMax + suffix;
        else if (budgetMin) lbl.textContent = '\u2265 ' + budgetMin + suffix;
        else if (budgetMax) lbl.textContent = '\u2264 ' + budgetMax + suffix;
        else lbl.textContent = configResolved.toggle_default || Drupal.t('Budget');
        const filterItem = document.querySelector('.ps-filter-bar__item--budget');
        if (filterItem) filterItem.classList.toggle('is-active', !!(budgetMin || budgetMax));
      }

      function getMoreFilterWidget(param) {
        const schema = moreFilterSchema.find(function (entry) {
          return entry.param === param;
        });
        return schema ? schema.widget : 'text';
      }

      function countMoreActive() {
        let count = 0;
        Object.keys(moreFilters).forEach(function (param) {
          const widget = getMoreFilterWidget(param);
          const value = moreFilters[param];
          if (widget === 'checkbox' && value) {
            count++;
            return;
          }
          if (widget === 'yes_no' && (value === '1' || value === '0')) {
            count++;
            return;
          }
          if (widget === 'tags' && Array.isArray(value) && value.length) {
            count += value.length;
            return;
          }
          if (widget === 'range' && value && (value.min || value.max)) {
            count++;
            return;
          }
          if ((widget === 'text' || widget === 'date') && String(value || '').trim()) {
            count++;
          }
        });
        return count;
      }

      function updateMoreLabel() {
        const lbl = document.querySelector('.js-ps-more-label');
        const item = document.querySelector('.ps-filter-bar__item--more');
        const active = countMoreActive();
        if (lbl) {
          lbl.textContent = active ? Drupal.t('More filters (@count)', { '@count': active }) : Drupal.t('More filters');
        }
        if (item) {
          item.classList.toggle('is-active', active > 0);
        }
      }

      function syncMoreInputsFromState() {
        document.querySelectorAll('.js-ps-more-filter').forEach(function (input) {
          const param = input.dataset.param;
          const widget = input.dataset.widget || getMoreFilterWidget(param);
          const state = moreFilters[param];

          if (widget === 'checkbox') {
            input.checked = !!state;
            return;
          }
          if (widget === 'yes_no') {
            input.value = state || '';
            return;
          }
          if (widget === 'tags') {
            const values = Array.isArray(state) ? state : [];
            input.checked = values.indexOf(input.value) !== -1;
            return;
          }
          if (widget === 'range') {
            const range = state || { min: '', max: '' };
            if (input.dataset.bound === 'min') {
              input.value = range.min || '';
            }
            else if (input.dataset.bound === 'max') {
              input.value = range.max || '';
            }
            return;
          }
          input.value = state || '';
        });
        updateMoreLabel();
      }

      function appendMoreFiltersToParams(p, forCount) {
        Object.keys(moreFilters).forEach(function (param) {
          const widget = getMoreFilterWidget(param);
          const value = moreFilters[param];
          if (widget === 'checkbox') {
            if (value) {
              p.set(param, '1');
            }
            return;
          }
          if (widget === 'yes_no') {
            if (value === '1' || value === '0') {
              p.set(param, value);
            }
            return;
          }
          if (widget === 'tags' && Array.isArray(value)) {
            value.forEach(function (tag) {
              p.append(param, tag);
            });
            return;
          }
          if (widget === 'range' && value) {
            const minKey = forCount ? param + '_min' : param + '[min]';
            const maxKey = forCount ? param + '_max' : param + '[max]';
            if (value.min) {
              p.set(minKey, value.min);
            }
            if (value.max) {
              p.set(maxKey, value.max);
            }
            return;
          }
          if ((widget === 'text' || widget === 'date') && String(value || '').trim()) {
            p.set(param, value);
          }
        });
      }

      function handleMoreFilterChange(input) {
        const param = input.dataset.param;
        const widget = input.dataset.widget || getMoreFilterWidget(param);
        if (widget === 'checkbox') {
          if (input.checked) {
            moreFilters[param] = true;
          }
          else {
            delete moreFilters[param];
          }
        }
        else if (widget === 'yes_no') {
          if (input.value === '') {
            delete moreFilters[param];
          }
          else {
            moreFilters[param] = input.value;
          }
        }
        else if (widget === 'tags') {
          const list = Array.isArray(moreFilters[param]) ? moreFilters[param].slice() : [];
          if (input.checked) {
            if (list.indexOf(input.value) === -1) {
              list.push(input.value);
            }
          }
          else {
            const idx = list.indexOf(input.value);
            if (idx !== -1) {
              list.splice(idx, 1);
            }
          }
          if (list.length) {
            moreFilters[param] = list;
          }
          else {
            delete moreFilters[param];
          }
        }
        updateMoreLabel();
        scheduleCountUpdate();
      }

      function handleMoreFilterInputEvent(input) {
        const param = input.dataset.param;
        const widget = input.dataset.widget || getMoreFilterWidget(param);
        if (widget === 'range') {
          const range = moreFilters[param] || { min: '', max: '' };
          if (input.dataset.bound === 'min') {
            range.min = input.value;
          }
          else if (input.dataset.bound === 'max') {
            range.max = input.value;
          }
          if (range.min || range.max) {
            moreFilters[param] = range;
          }
          else {
            delete moreFilters[param];
          }
        }
        else if (widget === 'text' || widget === 'date') {
          if (String(input.value || '').trim()) {
            moreFilters[param] = input.value;
          }
          else {
            delete moreFilters[param];
          }
        }
        else {
          return;
        }
        updateMoreLabel();
        scheduleCountUpdate();
      }

      function loadMoreCriteriaGroup(groupId, panel) {
        if (!groupId || !panel || panel.dataset.loaded === '1' || loadedMoreGroups[groupId]) {
          syncMoreInputsFromState();
          return Promise.resolve();
        }
        const content = panel.querySelector('.js-ps-more-group-content');
        if (!content) {
          return Promise.resolve();
        }
        const loading = content.querySelector('.ps-more-group__loading');
        if (loading) {
          loading.hidden = false;
        }
        let url = moreCriteriaGroupUrl + '/' + encodeURIComponent(groupId);
        if (selectedAsset) {
          url += '?asset_type=' + encodeURIComponent(selectedAsset);
        }
        return fetch(url, {
          headers: { Accept: 'application/json' },
          credentials: 'same-origin',
        })
          .then(function (response) {
            return response.ok ? response.json() : Promise.reject();
          })
          .then(function (data) {
            if (loading) {
              loading.hidden = true;
            }
            content.insertAdjacentHTML('beforeend', data.html || '');
            panel.dataset.loaded = '1';
            loadedMoreGroups[groupId] = true;
            syncMoreInputsFromState();
          })
          .catch(function () {
            if (loading) {
              loading.hidden = true;
            }
          });
      }

      // ── Bootstrap dropdown / offcanvas integration ───────────────────────
      function getBootstrap() {
        return window.bootstrap;
      }

      function closeAllDropdowns() {
        const bs = getBootstrap();
        if (!bs || !bs.Dropdown) {
          return;
        }
        document.querySelectorAll('.ps-filter-bar [data-bs-toggle="dropdown"], .ps-filter-bar .js-ps-location-toggle').forEach(function (toggle) {
          const instance = bs.Dropdown.getInstance(toggle);
          if (instance) {
            instance.hide();
          }
        });
      }

      function getFilterBarBackdrop() {
        return document.querySelector('.js-ps-filter-bar-backdrop');
      }

      function mountFilterBarBackdrop(backdrop) {
        const view = document.querySelector('.ps-search-view');
        if (view && backdrop.parentElement !== view) {
          view.appendChild(backdrop);
        }
      }

      function updateFilterBackdropGeometry() {
        const backdrop = getFilterBarBackdrop();
        const view = document.querySelector('.ps-search-view');
        const filterBar = document.querySelector('.ps-search-view__filter-bar');
        if (!backdrop || !view || !filterBar) {
          return;
        }
        const viewRect = view.getBoundingClientRect();
        const barRect = filterBar.getBoundingClientRect();
        backdrop.style.top = Math.max(0, barRect.bottom - viewRect.top) + 'px';
      }

      function isAnyFilterDropdownOpen() {
        return !!document.querySelector('.ps-filter-bar .dropdown-menu.show');
      }

      function isMoreOffcanvasOpen() {
        const offcanvasEl = document.getElementById('ps-more-offcanvas');
        return !!(offcanvasEl && offcanvasEl.classList.contains('show'));
      }

      function syncFilterBarBackdrop() {
        const backdrop = getFilterBarBackdrop();
        if (!backdrop) {
          return;
        }
        mountFilterBarBackdrop(backdrop);
        const shouldShow = isAnyFilterDropdownOpen() && !isMoreOffcanvasOpen();
        if (shouldShow) {
          updateFilterBackdropGeometry();
        }
        backdrop.classList.toggle('is-visible', shouldShow);
        backdrop.hidden = !shouldShow;
        backdrop.setAttribute('aria-hidden', shouldShow ? 'false' : 'true');
      }

      function hideAllLocationSuggestions() {
        document.querySelectorAll('.js-ps-location-suggest').forEach(function (box) {
          box.hidden = true;
          box.innerHTML = '';
        });
        document.querySelectorAll('.js-ps-locality-input').forEach(function (input) {
          input.setAttribute('aria-expanded', 'false');
        });
      }

      once('ps-bs-dropdown-events', '.ps-filter-bar .dropdown', context).forEach(function (dropdownEl) {
        dropdownEl.addEventListener('show.bs.dropdown', function () {
          if (!dropdownEl.classList.contains('ps-filter-bar__item--location')) {
            hideAllLocationSuggestions();
          }
          fetchCount();
        });
        dropdownEl.addEventListener('shown.bs.dropdown', function () {
          syncFilterBarBackdrop();
          if (!dropdownEl.classList.contains('ps-filter-bar__item--location')) {
            return;
          }
          const toggle = dropdownEl.querySelector('.js-ps-location-toggle');
          if (toggle) {
            toggle.setAttribute('aria-expanded', 'true');
          }
        });
        dropdownEl.addEventListener('hidden.bs.dropdown', function () {
          syncFilterBarBackdrop();
          if (dropdownEl.classList.contains('ps-filter-bar__item--type')) {
            updateTypeOpBtnLabel(dropdownEl);
          }
          if (dropdownEl.classList.contains('ps-filter-bar__item--location')) {
            hideAllLocationSuggestions();
            const toggle = dropdownEl.querySelector('.js-ps-location-toggle');
            if (toggle) {
              toggle.setAttribute('aria-expanded', 'false');
            }
          }
        });
      });

      once('ps-filter-bar-backdrop', '.js-ps-filter-bar-backdrop', context).forEach(function (backdrop) {
        mountFilterBarBackdrop(backdrop);
        backdrop.addEventListener('click', function () {
          closeAllDropdowns();
          hideAllLocationSuggestions();
          syncFilterBarBackdrop();
        });
      });

      once('ps-filter-backdrop-resize', 'html', context).forEach(function () {
        window.addEventListener('resize', function () {
          const activeBackdrop = getFilterBarBackdrop();
          if (activeBackdrop && activeBackdrop.classList.contains('is-visible')) {
            updateFilterBackdropGeometry();
          }
        });
      });

      once('ps-bs-offcanvas-events', '#ps-more-offcanvas', context).forEach(function (offcanvasEl) {
        offcanvasEl.addEventListener('show.bs.offcanvas', function () {
          closeAllDropdowns();
          hideAllLocationSuggestions();
          syncFilterBarBackdrop();
          fetchCount();
        });
        offcanvasEl.addEventListener('hidden.bs.offcanvas', function () {
          syncFilterBarBackdrop();
        });
      });

      once('ps-bs-more-trigger', '.js-ps-more-trigger', context).forEach(function (trigger) {
        trigger.addEventListener('click', function () {
          closeAllDropdowns();
          hideAllLocationSuggestions();
        });
      });

      once('ps-bs-dropdown-close-offcanvas', '.ps-filter-bar [data-bs-toggle="dropdown"]', context).forEach(function (toggle) {
        toggle.addEventListener('show.bs.dropdown', function () {
          const bs = getBootstrap();
          const offcanvasEl = document.getElementById('ps-more-offcanvas');
          if (bs?.Offcanvas && offcanvasEl) {
            bs.Offcanvas.getInstance(offcanvasEl)?.hide();
          }
        });
      });

      // ── Utilities ─────────────────────────────────────────────────────────
      function normalizeLocationTokens(tokens) {
        const deduped = [];
        const seen = {};
        tokens.forEach(function (token) {
          const cleaned = token.trim();
          if (!cleaned) return;
          const key = cleaned.toLowerCase();
          if (seen[key]) return;
          seen[key] = true;
          deduped.push(cleaned);
        });
        return deduped.slice(0, 10);
      }

      function parseLocationTokens(value) {
        return normalizeLocationTokens(String(value || '').split(','));
      }

      function syncSelectedLocality(value) {
        selectedLocalityTokens = parseLocationTokens(value);
        selectedLocalityData = selectedLocalityTokens.map(function (label) {
          return { label: label, type: 'city', locality: label, admin_area: '', postal_code: '' };
        });
        selectedLocality = selectedLocalityTokens.join(', ');
      }

      function refreshSelectedLocality() {
        selectedLocality = selectedLocalityTokens.join(', ');
      }

      function addLocationTokens(tokens) {
        selectedLocalityTokens = normalizeLocationTokens(selectedLocalityTokens.concat(tokens));
        refreshSelectedLocality();
      }

      function addLocationData(data) {
        if (selectedLocalityTokens.length >= 10) {
          showMaxLocalitiesWarning();
          return;
        }
        const newTokens = data.map(function (item) {
          if (item.type === 'arrondissement' && item.postal_code) {
            return item.postal_code;
          }
          if (item.type === 'department') {
            return item.department_code || item.admin_area || item.label;
          }
          return item.locality || item.label;
        });

        selectedLocalityTokens = normalizeLocationTokens(selectedLocalityTokens.concat(newTokens));

        const dataByToken = {};
        selectedLocalityData.forEach(function (item, idx) {
          const token = selectedLocalityTokens[idx];
          if (token) {
            dataByToken[String(token).toLowerCase()] = item;
          }
        });
        data.forEach(function (item, idx) {
          const token = newTokens[idx];
          if (token) {
            dataByToken[String(token).toLowerCase()] = item;
          }
        });
        selectedLocalityData = selectedLocalityTokens.map(function (token) {
          const key = String(token).toLowerCase();
          if (dataByToken[key]) {
            return dataByToken[key];
          }
          return {
            label: token,
            type: 'city',
            locality: token,
            admin_area: '',
            postal_code: '',
          };
        }).slice(0, 10);
        selectedLocalityTokens = selectedLocalityTokens.slice(0, 10);
        refreshSelectedLocality();
      }

      function showMaxLocalitiesWarning() {
        const filterItem = document.querySelector('.ps-filter-bar__item--location');
        if (!filterItem) return;
        const existing = filterItem.querySelector('.ps-location-max-warning');
        if (existing) return;
        const warning = document.createElement('div');
        warning.className = 'ps-location-max-warning';
        warning.textContent = Drupal.t('Maximum 10 locations');
        filterItem.appendChild(warning);
        setTimeout(function () {
          if (warning.parentNode) {
            warning.parentNode.removeChild(warning);
          }
        }, 3000);
      }

      function getLocationItemToken(item) {
        if (typeof item !== 'object' || item === null) {
          return String(item || '').trim();
        }
        if (item.type === 'arrondissement' && item.postal_code) {
          return item.postal_code;
        }
        if (item.type === 'department') {
          return item.department_code || item.admin_area || item.label || '';
        }
        return item.locality || item.label || '';
      }

      function buildSelectedTokenMap() {
        const selected = {};
        selectedLocalityTokens.forEach(function (token) {
          selected[String(token).toLowerCase()] = true;
        });
        return selected;
      }

      function openLocationDropdown(dropdownEl) {
        const instance = getLocationDropdown(dropdownEl);
        if (instance) {
          instance.show();
        }
      }

      function closeLocationDropdown(dropdownEl) {
        const instance = getLocationDropdown(dropdownEl);
        if (instance) {
          instance.hide();
        }
      }

      function getLocationDropdown(dropdownEl) {
        const bs = getBootstrap();
        const toggle = dropdownEl ? dropdownEl.querySelector('.js-ps-location-toggle') : null;
        if (!bs?.Dropdown || !toggle || !dropdownEl) {
          return null;
        }
        return bs.Dropdown.getOrCreateInstance(toggle, { autoClose: 'outside' });
      }

      function removeLocationTokenAt(index) {
        if (index < 0 || index >= selectedLocalityTokens.length) {
          return;
        }
        selectedLocalityTokens.splice(index, 1);
        selectedLocalityData.splice(index, 1);
        refreshSelectedLocality();
      }

      function buildSeoBase() {
        if (!selectedOp) return langPrefix + searchPath;
        const opSlug = opSlugs[selectedOp];
        if (!opSlug) return langPrefix + searchPath;
        if (selectedAsset && assetSlugs[selectedAsset]) {
          return langPrefix + '/' + opSlug + '/' + assetSlugs[selectedAsset] + '/';
        }
        return langPrefix + '/' + opSlug + '/';
      }

      function toSeoSlug(value) {
        return String(value || '')
          .normalize('NFD')
          .replace(/[\u0300-\u036f]/g, '')
          .toLowerCase()
          .replace(/[^a-z0-9]+/g, '-')
          .replace(/(^-|-$)/g, '');
      }

      function getPrimaryLocalityToken() {
        return selectedLocalityTokens.length ? selectedLocalityTokens[0] : '';
      }

      function getPrimaryLocalityData() {
        return selectedLocalityData.length ? selectedLocalityData[0] : null;
      }

      function parseCityPostalPathToken(segment) {
        const parts = String(segment || '').split('-');
        if (parts.length < 2) {
          return '';
        }
        const postal = parts[parts.length - 1];
        if (/^\d{5}$/.test(postal)) {
          return postal;
        }
        return '';
      }

      function looksLikeDeptSegment(segment) {
        return /^[a-z0-9].*-\d{2,3}$/i.test(String(segment || ''));
      }

      function extractLocalityFromPath() {
        const path = window.location.pathname || '';
        const withoutLang = (langPrefix && path.indexOf(langPrefix + '/') === 0)
          ? path.slice(langPrefix.length)
          : path;
        const segments = withoutLang.split('/').filter(Boolean);
        if (!segments.length) {
          return '';
        }

        const opSlugValues = Object.values(opSlugs).map(function (slug) {
          return String(slug || '').toLowerCase();
        });
        const assetSlugValues = Object.values(assetSlugs).map(function (slug) {
          return String(slug || '').toLowerCase();
        });

        if (opSlugValues.indexOf(String(segments[0]).toLowerCase()) === -1) {
          return '';
        }

        let restStart = 1;
        if (segments[1] && assetSlugValues.indexOf(String(segments[1]).toLowerCase()) !== -1) {
          restStart = 2;
        }
        else if (segments.length >= 3 && looksLikeDeptSegment(segments[2])) {
          restStart = 2;
        }

        const localitySegments = segments.slice(restStart);
        if (!localitySegments.length) {
          return '';
        }

        const cityPostalToken = parseCityPostalPathToken(localitySegments[localitySegments.length - 1]);
        if (cityPostalToken) {
          return cityPostalToken;
        }

        const last = localitySegments[localitySegments.length - 1];
        const deptMatch = String(last).match(/^.+-(\d{2,3})$/);
        if (deptMatch) {
          return deptMatch[1];
        }

        return String(last)
          .replace(/-/g, ' ')
          .trim()
          .split(/\s+/)
          .map(function (word) {
            return word.charAt(0).toUpperCase() + word.slice(1);
          })
          .join(' ');
      }

      function resolveInitialLocalityValue() {
        if (localityArrayParams.length) {
          return localityArrayParams.join(',');
        }
        const queryLocality = currentParams.get('locality');
        if (queryLocality) {
          return queryLocality;
        }
        if (settings.initialLocality) {
          return settings.initialLocality;
        }
        return extractLocalityFromPath() || '';
      }

      function setFacetQueryParam(params, key, value) {
        if (value) {
          params.append(key + '[' + value + ']', value);
        }
      }

      function appendLocalityQueryParams(params, tokens, skipFirst) {
        const list = skipFirst ? tokens.slice(1) : tokens;
        if (list.length) {
          params.set('locality', list.join(', '));
        }
      }

      function highlightSuggestionLabel(label, query) {
        const fragment = document.createDocumentFragment();
        const needle = String(query || '').trim();
        if (!needle) {
          fragment.appendChild(document.createTextNode(label));
          return fragment;
        }

        const lowerLabel = label.toLowerCase();
        const lowerNeedle = needle.toLowerCase();
        const index = lowerLabel.indexOf(lowerNeedle);
        if (index === -1) {
          fragment.appendChild(document.createTextNode(label));
          return fragment;
        }

        if (index > 0) {
          fragment.appendChild(document.createTextNode(label.slice(0, index)));
        }

        const strong = document.createElement('strong');
        strong.className = 'ps-location-suggest__match';
        strong.textContent = label.slice(index, index + needle.length);
        fragment.appendChild(strong);

        if (index + needle.length < label.length) {
          fragment.appendChild(document.createTextNode(label.slice(index + needle.length)));
        }

        return fragment;
      }

      function usesSeoLocalityPath(base) {
        const normalized = String(base || '').replace(/\/$/, '');
        const searchBase = String(searchPath || '').replace(/\/$/, '');
        return Boolean(selectedOp && normalized.indexOf(searchBase) === -1);
      }

      function buildNavigationUrl() {
        let base = buildSeoBase();
        const p = new URLSearchParams();
        const primaryData = getPrimaryLocalityData();
        const useSeoLocalityPath = usesSeoLocalityPath(base) && primaryData && (primaryData.locality || primaryData.postal_code);

        // Flexible (no operation): asset stays as query param on /find-property (BEF array format).
        if (selectedAsset && !selectedOp) {
          setFacetQueryParam(p, 'asset_type', selectedAsset);
        }

        // SEO locality segments only under /a-louer/… — never append to flexible search base (404).
        if (useSeoLocalityPath) {
          base = base.replace(/\/?$/, '/');
          const deptCode = primaryData.postal_code ? primaryData.postal_code.substring(0, 2) : '';
          const deptSlug = primaryData.admin_area ? toSeoSlug(primaryData.admin_area) : '';
          const localitySlug = toSeoSlug(primaryData.locality || primaryData.label || '');
          const postalSlug = primaryData.postal_code || '';

          if (deptSlug && deptCode) {
            base += deptSlug + '-' + deptCode + '/';
          }
          if (localitySlug) {
            base += localitySlug;
            if (postalSlug) {
              base += '-' + postalSlug;
            }
            base += '/';
          }

          appendLocalityQueryParams(p, selectedLocalityTokens, true);
        }
        else if (selectedLocalityTokens.length) {
          appendLocalityQueryParams(p, selectedLocalityTokens, false);
        }

        const visibility = getFilterVisibility(selectedAsset);
        if (visibility.show_surface) {
          if (surfaceMin) p.set('surface[min]', surfaceMin);
          if (surfaceMax) p.set('surface[max]', surfaceMax);
        }
        if (visibility.show_capacity) {
          if (capacityMin) p.set('capacity[min]', capacityMin);
          if (capacityMax) p.set('capacity[max]', capacityMax);
        }
        if (budgetMin) p.set('budget[min]', budgetMin);
        if (budgetMax) p.set('budget[max]', budgetMax);
        appendMoreFiltersToParams(p, false);
        const qs = p.toString();
        return qs ? base + '?' + qs : base;
      }

      function buildCountParams() {
        const p = new URLSearchParams();
        if (selectedOp) p.set('operation_type', selectedOp);
        if (selectedAsset) p.set('asset_type', selectedAsset);
        if (selectedLocalityTokens.length) {
          p.set('locality', selectedLocalityTokens.join(', '));
        }

        const visibility = getFilterVisibility(selectedAsset);
        if (visibility.show_surface) {
          if (surfaceMin) p.set('surface_min', surfaceMin);
          if (surfaceMax) p.set('surface_max', surfaceMax);
        }
        if (visibility.show_capacity) {
          if (capacityMin) p.set('capacity_min', capacityMin);
          if (capacityMax) p.set('capacity_max', capacityMax);
        }
        if (budgetMin) p.set('budget_min', budgetMin);
        if (budgetMax) p.set('budget_max', budgetMax);
        appendMoreFiltersToParams(p, true);
        return p;
      }

      function setApplyBtnsLoading(loading) {
        document.querySelectorAll('.js-ps-apply-btn').forEach(function (btn) {
          btn.disabled = loading;
          btn.classList.toggle('is-loading', loading);
        });
      }

      function updateCountDisplays(count) {
        currentCount = count;
        document.querySelectorAll('.js-ps-count-label').forEach(function (label) {
          label.textContent = count;
        });
        // Update location suggest box button if visible
        document.querySelectorAll('.ps-location-suggest__count').forEach(function (label) {
          label.textContent = count;
        });
        setApplyBtnsLoading(false);
      }

      function fetchCount() {
        setApplyBtnsLoading(true);
        fetch(countUrl + '?' + buildCountParams().toString(), {
          headers: { Accept: 'application/json' },
          credentials: 'same-origin',
        })
          .then(function (r) { return r.ok ? r.json() : Promise.reject(); })
          .then(function (data) { updateCountDisplays(data.count || 0); })
          .catch(function () { setApplyBtnsLoading(false); });
      }

      function scheduleCountUpdate() {
        clearTimeout(countDebounce);
        countDebounce = setTimeout(fetchCount, 300);
      }

      function navigate() {
        window.location.href = buildNavigationUrl();
      }

      // ── Apply buttons (all popins — same navigation) ──────────────────────
      once('ps-apply', '.js-ps-apply-btn', context).forEach(function (btn) {
        btn.addEventListener('click', function () {
          if (!btn.disabled) navigate();
        });
      });

      // ── 1. Type + Op filter ────────────────────────────────────────────────
      function syncOpButtonStates(wrapper) {
        wrapper.querySelectorAll('.js-ps-op-btn').forEach(function (b) {
          const code = b.dataset.code;
          const active = code === 'FLEX' ? opFlexible : code === selectedOp;
          b.classList.toggle('is-active', active);
          b.setAttribute('aria-pressed', String(active));
        });
      }

      function updateTypeOpBtnLabel(wrapper) {
        const labelEl = wrapper.querySelector('.ps-filter-bar__toggle-label');
        const mainBtn = wrapper.querySelector('.ps-filter-bar__toggle');
        if (!labelEl) return;
        const assetCard = selectedAsset
          ? wrapper.querySelector('.js-ps-asset-btn[data-code="' + selectedAsset + '"]')
          : null;
        let opLabel = null;
        if (selectedOp) {
          const opBtnEl = wrapper.querySelector('.js-ps-op-btn[data-code="' + selectedOp + '"]');
          opLabel = opBtnEl ? opBtnEl.textContent.trim() : null;
        }
        else if (opFlexible && !selectedAsset) {
          const flexBtn = wrapper.querySelector('.js-ps-op-btn[data-code="FLEX"]');
          opLabel = flexBtn ? flexBtn.textContent.trim() : null;
        }
        const assetLabel = assetCard ? assetCard.querySelector('.ps-asset-card__label') : null;
        if (assetLabel && opLabel) {
          labelEl.textContent = assetLabel.textContent.trim() + ' ' + opLabel.toLowerCase();
        } else if (assetLabel) {
          labelEl.textContent = assetLabel.textContent.trim();
        } else if (opLabel) {
          labelEl.textContent = opLabel;
        } else {
          labelEl.textContent = Drupal.t('Property type');
        }
        if (mainBtn) mainBtn.classList.toggle('is-active', !!(selectedAsset || selectedOp));
      }

      once('ps-type-filter', '.ps-filter-bar__item--type', context).forEach(function (wrapper) {
        syncOpButtonStates(wrapper);

        // Asset cards.
        wrapper.querySelectorAll('.js-ps-asset-btn').forEach(function (btn) {
          btn.addEventListener('click', function () {
            const code = btn.dataset.code;
            selectedAsset = (selectedAsset === code) ? null : code;
            wrapper.querySelectorAll('.js-ps-asset-btn').forEach(function (a) {
              const active = a.dataset.code === selectedAsset;
              a.closest('.ps-asset-card').classList.toggle('is-active', active);
              a.setAttribute('aria-pressed', String(active));
            });
            scheduleCountUpdate();
            updateAssetMode();
            updateBudgetUi();
          });
        });

        // Op buttons — radio: Buy / Rent / I'm flexible (no operation filter).
        wrapper.querySelectorAll('.js-ps-op-btn').forEach(function (btn) {
          btn.addEventListener('click', function () {
            const code = btn.dataset.code;
            if (code === 'FLEX') {
              selectedOp = null;
              opFlexible = true;
            }
            else {
              selectedOp = code;
              opFlexible = false;
            }
            syncOpButtonStates(wrapper);
            scheduleCountUpdate();
            updateBudgetUi();
          });
        });

        // Type+Op section clear.
        const clearBtn = wrapper.querySelector('.js-ps-clear-btn');
        if (clearBtn) {
          clearBtn.addEventListener('click', function () {
            selectedAsset = null;
            selectedOp = null;
            opFlexible = true;
            wrapper.querySelectorAll('.js-ps-asset-btn').forEach(function (a) {
              a.closest('.ps-asset-card').classList.remove('is-active');
              a.setAttribute('aria-pressed', 'false');
            });
            syncOpButtonStates(wrapper);
            scheduleCountUpdate();
            updateAssetMode();
            updateBudgetUi();
          });
        }
      });

      // ── 2. Localisation — chips + grouped autocomplete popin (Figma) ───────
      once('ps-locality', '.js-ps-locality-input', context).forEach(function (input) {
        const dropdownEl = input.closest('.ps-filter-bar__item--location');
        const editor = input.closest('.js-ps-location-editor');
        const chipsContainer = editor ? editor.querySelector('.js-ps-location-chips') : null;
        const popinChipsContainer = dropdownEl ? dropdownEl.querySelector('.js-ps-location-popin-chips') : null;
        const selectedPanel = dropdownEl ? dropdownEl.querySelector('.js-ps-location-selected-panel') : null;
        const suggestBox = dropdownEl ? dropdownEl.querySelector('.js-ps-location-suggest') : null;
        const clearInputBtn = dropdownEl ? dropdownEl.querySelector('.js-ps-location-clear-input') : null;
        const locationToggle = dropdownEl ? dropdownEl.querySelector('.ps-filter-bar__toggle--location') : null;
        let suggestionButtons = [];

        function appendLocationChip(container, displayLabel, index) {
          if (!container) {
            return;
          }
          const chip = document.createElement('span');
          chip.className = 'ps-location-chip';

          const label = document.createElement('span');
          label.className = 'ps-location-chip__label';
          label.textContent = displayLabel;
          label.title = displayLabel;

          const clearBtn = document.createElement('button');
          clearBtn.type = 'button';
          clearBtn.className = 'ps-location-chip__clear';
          clearBtn.setAttribute('aria-label', Drupal.t('Remove @value', { '@value': displayLabel }));
          clearBtn.textContent = '×';
          clearBtn.addEventListener('mousedown', function (e) {
            e.preventDefault();
          });
          clearBtn.addEventListener('click', function () {
            removeLocationTokenAt(index);
            renderAllChips();
            updateLocationActiveState();
            updateClearInputButton();
            scheduleCountUpdate();
            fetchLocationSuggestions(input.value.trim());
            input.focus();
          });

          chip.appendChild(label);
          chip.appendChild(clearBtn);
          container.appendChild(chip);
        }

        function renderChipList(container) {
          if (!container) {
            return;
          }
          container.innerHTML = '';
          selectedLocalityTokens.forEach(function (token, index) {
            const itemData = selectedLocalityData[index];
            const displayLabel = (itemData && itemData.label) ? itemData.label : token;
            appendLocationChip(container, displayLabel, index);
          });
        }

        function renderAllChips() {
          renderChipList(chipsContainer);
          renderChipList(popinChipsContainer);
        }

        function updateSelectedPanelVisibility() {
          if (selectedPanel) {
            selectedPanel.hidden = selectedLocalityTokens.length === 0;
          }
        }

        function updateClearInputButton() {
          if (!clearInputBtn) {
            return;
          }
          clearInputBtn.hidden = !input.value.trim();
        }

        function updateLocationActiveState() {
          if (dropdownEl) {
            dropdownEl.classList.toggle('has-value', !!(selectedLocalityTokens.length || input.value.trim()));
          }
        }

        function isFocusInsideLocation() {
          if (!dropdownEl) {
            return false;
          }
          const active = document.activeElement;
          return active instanceof Node && dropdownEl.contains(active);
        }

        function hideSuggestions() {
          if (!suggestBox) {
            return;
          }
          suggestBox.innerHTML = '';
          suggestBox.hidden = true;
          suggestionButtons = [];
          activeSuggestionIndex = -1;
          input.setAttribute('aria-expanded', 'false');
          input.removeAttribute('aria-activedescendant');
        }

        function setActiveSuggestion(index) {
          if (!suggestionButtons.length) {
            activeSuggestionIndex = -1;
            input.removeAttribute('aria-activedescendant');
            return;
          }
          const last = suggestionButtons.length - 1;
          activeSuggestionIndex = Math.max(0, Math.min(index, last));
          suggestionButtons.forEach(function (btn, btnIndex) {
            const active = btnIndex === activeSuggestionIndex;
            btn.classList.toggle('is-active', active);
            if (active) {
              input.setAttribute('aria-activedescendant', btn.id);
              btn.scrollIntoView({ block: 'nearest' });
            }
          });
        }

        function selectSuggestion(itemData) {
          addLocationData([itemData]);
          renderAllChips();
          updateSelectedPanelVisibility();
          input.value = '';
          updateClearInputButton();
          updateLocationActiveState();
          scheduleCountUpdate();
          fetchLocationSuggestions('');
        }

        function commitDraftTokens() {
          const draft = input.value.trim();
          if (!draft) {
            return false;
          }
          addLocationTokens(parseLocationTokens(draft));
          renderAllChips();
          updateSelectedPanelVisibility();
          input.value = '';
          updateClearInputButton();
          updateLocationActiveState();
          scheduleCountUpdate();
          return true;
        }

        function renderSuggestions(groups) {
          if (!suggestBox) {
            return;
          }

          suggestBox.innerHTML = '';
          suggestionButtons = [];

          if (groups && groups.length) {
            groups.forEach(function (group) {
              const title = document.createElement('div');
              title.className = 'ps-location-suggest__group-title';
              title.textContent = group.label;
              suggestBox.appendChild(title);

              group.items.forEach(function (itemData) {
                const isStructured = typeof itemData === 'object' && itemData !== null;
                const label = isStructured ? itemData.label : String(itemData);

                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'ps-location-suggest__item';
                btn.setAttribute('role', 'option');
                btn.id = 'ps-location-option-' + suggestionButtons.length;
                btn.appendChild(highlightSuggestionLabel(label, input.value.trim()));
                btn.addEventListener('mousedown', function (e) {
                  e.preventDefault();
                });
                btn.addEventListener('click', function () {
                  const data = isStructured ? itemData : {
                    label: label,
                    type: 'city',
                    locality: label,
                    admin_area: '',
                    postal_code: '',
                  };
                  selectSuggestion(data);
                });
                suggestionButtons.push(btn);
                suggestBox.appendChild(btn);
              });
            });
          }
          else if (!input.value.trim()) {
            const hint = document.createElement('div');
            hint.className = 'ps-location-suggest__hint';
            hint.textContent = Drupal.t('Start typing a city name or postal code');
            suggestBox.appendChild(hint);
          }
          else if (input.value.trim().length >= 2) {
            const hint = document.createElement('div');
            hint.className = 'ps-location-suggest__hint';
            hint.textContent = Drupal.t('No location found');
            suggestBox.appendChild(hint);
          }

          if (suggestBox.childElementCount === 0) {
            suggestBox.hidden = true;
            input.setAttribute('aria-expanded', 'false');
            return;
          }

          suggestBox.hidden = false;
          input.setAttribute('aria-expanded', 'true');
          if (suggestionButtons.length > 0) {
            setActiveSuggestion(0);
          }
        }

        function fetchLocationSuggestions(partialToken) {
          if (!suggestBox) {
            return;
          }

          if (partialToken.length === 0) {
            renderSuggestions([]);
            return;
          }

          if (partialToken.length < 2) {
            hideSuggestions();
            return;
          }

          fetch(locationSuggestUrl + '?q=' + encodeURIComponent(partialToken), {
            headers: { Accept: 'application/json' },
            credentials: 'same-origin',
          })
            .then(function (r) { return r.ok ? r.json() : Promise.reject(); })
            .then(function (data) {
              const selected = buildSelectedTokenMap();
              const groupsRaw = Array.isArray(data.groups) ? data.groups : [];
              const groups = groupsRaw
                .map(function (group) {
                  const label = String(group.label || '').trim();
                  const items = Array.isArray(group.items) ? group.items : [];
                  const filteredItems = items.filter(function (item) {
                    const token = getLocationItemToken(item).toLowerCase();
                    return token && !selected[token];
                  });
                  return {
                    label: label,
                    items: filteredItems,
                  };
                })
                .filter(function (group) {
                  return !!group.label && group.items.length > 0;
                });

              renderSuggestions(groups);
            })
            .catch(function () { hideSuggestions(); });
        }

        function moveSuggestion(direction) {
          if (!suggestionButtons.length) {
            return;
          }
          if (activeSuggestionIndex < 0) {
            setActiveSuggestion(direction > 0 ? 0 : suggestionButtons.length - 1);
            return;
          }
          const next = activeSuggestionIndex + direction;
          const wrapped = next < 0 ? suggestionButtons.length - 1 : (next >= suggestionButtons.length ? 0 : next);
          setActiveSuggestion(wrapped);
        }

        function enrichLocalityData() {
          const params = new URLSearchParams();
          selectedLocalityTokens.forEach(function (token) {
            params.append('localities[]', token);
          });
          fetch(locationDataUrl + '?' + params.toString(), {
            headers: { Accept: 'application/json' },
            credentials: 'same-origin',
          })
            .then(function (r) { return r.ok ? r.json() : Promise.reject(); })
            .then(function (response) {
              if (Array.isArray(response.data) && response.data.length > 0) {
                selectedLocalityData = response.data;
                renderAllChips();
                updateSelectedPanelVisibility();
              }
            })
            .catch(function () { /* Keep minimal chip labels on failure. */ });
        }

        if (localityArrayParams.length) {
          syncSelectedLocality(localityArrayParams.join(','));
        }
        else {
          syncSelectedLocality(resolveInitialLocalityValue());
        }
        renderAllChips();
        updateSelectedPanelVisibility();
        input.value = '';
        updateClearInputButton();
        updateLocationActiveState();

        if (selectedLocalityTokens.length > 0) {
          enrichLocalityData();
        }

        if (clearInputBtn) {
          clearInputBtn.addEventListener('mousedown', function (e) {
            e.preventDefault();
          });
          clearInputBtn.addEventListener('click', function () {
            input.value = '';
            updateClearInputButton();
            updateLocationActiveState();
            hideSuggestions();
            input.focus();
            openLocationDropdown(dropdownEl);
          });
        }

        const locationPopin = dropdownEl ? dropdownEl.querySelector('.ps-filter-popin--location') : null;
        if (locationPopin) {
          locationPopin.addEventListener('mousedown', function (e) {
            if (e.target !== input) {
              e.preventDefault();
            }
          });
        }

        if (locationToggle) {
          locationToggle.addEventListener('click', function (e) {
            if (e.target === input || e.target === clearInputBtn || input.contains(e.target)) {
              return;
            }
            e.preventDefault();
            input.focus();
            openLocationDropdown(dropdownEl);
          });
        }

        input.addEventListener('mousedown', function (e) {
          e.stopPropagation();
        });

        input.addEventListener('click', function (e) {
          e.stopPropagation();
          openLocationDropdown(dropdownEl);
        });

        input.addEventListener('input', function () {
          updateClearInputButton();
          updateLocationActiveState();
          openLocationDropdown(dropdownEl);
          const token = input.value.trim();
          clearTimeout(locationSuggestDebounce);
          locationSuggestDebounce = setTimeout(function () {
            fetchLocationSuggestions(token);
          }, 180);
        });

        input.addEventListener('focus', function () {
          openLocationDropdown(dropdownEl);
          fetchLocationSuggestions(input.value.trim());
        });

        input.addEventListener('blur', function () {
          setTimeout(function () {
            if (isFocusInsideLocation()) {
              return;
            }
            hideSuggestions();
            commitDraftTokens();
            closeLocationDropdown(dropdownEl);
          }, 150);
        });

        input.addEventListener('keydown', function (e) {
          if (e.key === 'ArrowDown') {
            if (suggestBox && !suggestBox.hidden) {
              e.preventDefault();
              moveSuggestion(1);
            }
            return;
          }
          if (e.key === 'ArrowUp') {
            if (suggestBox && !suggestBox.hidden) {
              e.preventDefault();
              moveSuggestion(-1);
            }
            return;
          }
          if (e.key === 'Enter') {
            if (suggestBox && !suggestBox.hidden && activeSuggestionIndex >= 0 && suggestionButtons[activeSuggestionIndex]) {
              e.preventDefault();
              suggestionButtons[activeSuggestionIndex].click();
              return;
            }
            if (commitDraftTokens()) {
              e.preventDefault();
              return;
            }
            e.preventDefault();
            navigate();
            return;
          }
          if (e.key === ',' || e.key === ';') {
            e.preventDefault();
            commitDraftTokens();
            fetchLocationSuggestions('');
            return;
          }
          if (e.key === 'Backspace' && input.value === '' && selectedLocalityTokens.length) {
            removeLocationTokenAt(selectedLocalityTokens.length - 1);
            renderAllChips();
            updateSelectedPanelVisibility();
            updateLocationActiveState();
            scheduleCountUpdate();
            return;
          }
          if (e.key === 'Escape') {
            hideSuggestions();
            closeLocationDropdown(dropdownEl);
            input.blur();
          }
        });
      });

      once('ps-location-apply', '.js-ps-location-apply', context).forEach(function (btn) {
        btn.addEventListener('click', function () {
          document.querySelectorAll('.js-ps-locality-input').forEach(function (input) {
            const draft = input.value.trim();
            if (draft) {
              addLocationTokens(parseLocationTokens(draft));
              input.value = '';
            }
          });
          navigate();
        });
      });

      function updateSurfaceLabel() {
        const lbl = document.querySelector('.js-ps-surface-label');
        if (!lbl) return;
        if (surfaceMin && surfaceMax) lbl.textContent = surfaceMin + '\u2013' + surfaceMax + ' m\u00b2';
        else if (surfaceMin) lbl.textContent = '\u2265 ' + surfaceMin + ' m\u00b2';
        else if (surfaceMax) lbl.textContent = '\u2264 ' + surfaceMax + ' m\u00b2';
        else lbl.textContent = Drupal.t('Surface');
        const filterItem = document.querySelector('.ps-filter-bar__item--surface');
        if (filterItem) filterItem.classList.toggle('is-active', !!(surfaceMin || surfaceMax));
      }

      once('ps-surf-min', '.js-ps-surface-min', context).forEach(function (input) {
        input.value = surfaceMin;
        input.addEventListener('input', function () {
          surfaceMin = input.value;
          updateSurfaceLabel();
          scheduleCountUpdate();
        });
      });
      once('ps-surf-max', '.js-ps-surface-max', context).forEach(function (input) {
        input.value = surfaceMax;
        input.addEventListener('input', function () {
          surfaceMax = input.value;
          updateSurfaceLabel();
          scheduleCountUpdate();
        });
      });

      // ── 5. Budget inputs ──────────────────────────────────────────────────
      once('ps-budget-min', '.js-ps-budget-min', context).forEach(function (input) {
        input.value = budgetMin;
        input.addEventListener('input', function () {
          budgetMin = input.value;
          updateBudgetLabel();
          scheduleCountUpdate();
        });
      });
      once('ps-budget-max', '.js-ps-budget-max', context).forEach(function (input) {
        input.value = budgetMax;
        input.addEventListener('input', function () {
          budgetMax = input.value;
          updateBudgetLabel();
          scheduleCountUpdate();
        });
      });

      // ── 6. Section-specific clear buttons (surface / budget) ──────────────
      once('ps-section-clear', '.js-ps-section-clear', context).forEach(function (btn) {
        btn.addEventListener('click', function () {
          const section = btn.dataset.section;
          if (section === 'surface') {
            surfaceMin = '';
            surfaceMax = '';
            ['.js-ps-surface-min', '.js-ps-surface-max'].forEach(function (sel) {
              const el = document.querySelector(sel);
              if (el) el.value = '';
            });
            updateSurfaceLabel();
          } else if (section === 'budget') {
            budgetMin = '';
            budgetMax = '';
            ['.js-ps-budget-min', '.js-ps-budget-max'].forEach(function (sel) {
              const el = document.querySelector(sel);
              if (el) el.value = '';
            });
            updateBudgetLabel();
          } else if (section === 'capacity') {
            capacityMin = '';
            capacityMax = '';
            ['.js-ps-capacity-min', '.js-ps-capacity-max'].forEach(function (sel) {
              const el = document.querySelector(sel);
              if (el) el.value = '';
            });
            updateCapacityLabel();
          } else if (section === 'more') {
            moreFilters = {};
            syncMoreInputsFromState();
          }
          scheduleCountUpdate();
        });
      });

      updateSurfaceLabel();
      updateBudgetLabel();

      function updateCapacityLabel() {
        const lbl = document.querySelector('.js-ps-capacity-label');
        if (!lbl) return;
        const unit = capacityFilterLabel;
        if (capacityMin && capacityMax) lbl.textContent = capacityMin + '\u2013' + capacityMax + ' ' + unit;
        else if (capacityMin) lbl.textContent = '\u2265 ' + capacityMin + ' ' + unit;
        else if (capacityMax) lbl.textContent = '\u2264 ' + capacityMax + ' ' + unit;
        else lbl.textContent = capacityFilterLabel;
        const filterItem = document.querySelector('.ps-filter-bar__item--capacity');
        if (filterItem) filterItem.classList.toggle('is-active', !!(capacityMin || capacityMax));
      }

      once('ps-capacity-min', '.js-ps-capacity-min', context).forEach(function (input) {
        input.value = capacityMin;
        input.addEventListener('input', function () {
          capacityMin = input.value;
          updateCapacityLabel();
          scheduleCountUpdate();
        });
      });
      once('ps-capacity-max', '.js-ps-capacity-max', context).forEach(function (input) {
        input.value = capacityMax;
        input.addEventListener('input', function () {
          capacityMax = input.value;
          updateCapacityLabel();
          scheduleCountUpdate();
        });
      });

      once('ps-more-offcanvas', '#ps-more-offcanvas', context).forEach(function (offcanvas) {
        offcanvas.addEventListener('shown.bs.offcanvas', function () {
          scheduleCountUpdate();
        });

        offcanvas.addEventListener('show.bs.collapse', function (event) {
          const panel = event.target;
          if (!panel.classList.contains('js-ps-more-group-panel')) {
            return;
          }
          loadMoreCriteriaGroup(panel.dataset.groupId, panel);
        });

        offcanvas.addEventListener('change', function (event) {
          if (event.target.matches('.js-ps-more-filter')) {
            handleMoreFilterChange(event.target);
          }
        });

        offcanvas.addEventListener('input', function (event) {
          if (event.target.matches('.js-ps-more-filter')) {
            handleMoreFilterInputEvent(event.target);
          }
        });
      });

      updateAssetMode();
      updateBudgetUi();
      syncMoreInputsFromState();
      updateCapacityLabel();
      scheduleCountUpdate();
    },
  };

}(Drupal, drupalSettings, once));

