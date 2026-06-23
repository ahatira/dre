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
 *  - No immediate navigation on selection — user confirms with the apply button.
 *  - All apply buttons show the SAME real-time count.
 *  - Navigating builds a SEO URL (for type+op) + query params (for other filters).
 *  - "Clear" in each section resets only that section's values.
 *  - Outside click / Escape closes all open popins.
 *  - Tab / focus entering a filter section opens its popin; leaving closes it (150 ms delay).
 *  - Escape closes the open popin and restores focus on its toggle/input.
 */

(function (Drupal, drupalSettings, once) {
  'use strict';

  Drupal.behaviors.psSearchFilterBar = {
    attach(context) {
      const htmxApi = Drupal.psSearchFilterHtmx;
      htmxApi.init(drupalSettings.psSearchFilterHtmx);

      once('ps-filter-bar-init', '.ps-search-view', context).forEach(function () {
      const settings = drupalSettings.psSearch || {};
      const useSearchContext = typeof Drupal.psSearchContext !== 'undefined'
        && Drupal.psSearchContext.isEnabled();
      const langPrefix = settings.langPrefix || '';
      const opSlugs = settings.opSlugs || {};
      const assetSlugs = settings.assetSlugs || {};
      const locationSuggestUrl = settings.locationSuggestUrl || '/api/ps/location-suggest';
      const locationDataUrl = settings.locationDataUrl || '/api/ps/location-data';
      const searchPath = settings.searchPath || '/find-property';
      const filterVisibilityByAsset = settings.filterVisibilityByAsset || {};
      const budgetFilterConfig = settings.budgetFilterConfig || {};
      const budgetFilterByAsset = settings.budgetFilterByAsset || {};
      const capacityFilterLabel = settings.capacityFilterLabel || Drupal.t('Capacity');
      const currentParams = new URLSearchParams(window.location.search);
      const loadedMoreGroups = {};

      function mountMoreOffcanvas(offcanvasEl) {
        if (offcanvasEl && offcanvasEl.parentElement !== document.body) {
          document.body.appendChild(offcanvasEl);
        }
      }

      function mountMobileFiltersOffcanvas(offcanvasEl) {
        if (offcanvasEl && offcanvasEl.parentElement !== document.body) {
          document.body.appendChild(offcanvasEl);
        }
      }

      once('ps-more-offcanvas-mount', '#ps-more-offcanvas', context).forEach(function (offcanvasEl) {
        mountMoreOffcanvas(offcanvasEl);
      });

      once('ps-mobile-filters-mount', '#ps-mobile-filters', context).forEach(function (offcanvasEl) {
        mountMobileFiltersOffcanvas(offcanvasEl);
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
      if (useSearchContext && settings.searchContext?.geo) {
        const geo = settings.searchContext.geo;
        selectedLocalityTokens = [geo.slug || geo.label];
        selectedLocalityData = [{
          slug: geo.slug,
          id: geo.id,
          type: geo.type,
          label: geo.label,
          lat: geo.lat,
          lng: geo.lng,
        }];
        selectedLocality = geo.label || geo.slug || '';
      }
      let surfaceMin = currentParams.get('surface[min]') || currentParams.get('surface_min') || '';
      let surfaceMax = currentParams.get('surface[max]') || currentParams.get('surface_max') || '';
      let budgetMin = currentParams.get('budget[min]') || currentParams.get('budget_min') || '';
      let budgetMax = currentParams.get('budget[max]') || currentParams.get('budget_max') || '';
      let capacityMin = currentParams.get('capacity[min]') || currentParams.get('capacity_min') || '';
      let capacityMax = currentParams.get('capacity[max]') || currentParams.get('capacity_max') || '';
      let moreFilterSchema = settings.moreFilterSchema || [];
      let moreFilters = {};
      const localityArrayParams = currentParams.getAll('locality[]');
      let countDebounce = null;
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
        syncActiveFilterCount();
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
        syncActiveFilterCount();
      }

      function countActiveFiltersForBadge() {
        let count = 0;
        if (selectedAsset || selectedOp) {
          count++;
        }
        if (selectedLocalityTokens.length) {
          count++;
        }
        if (surfaceMin || surfaceMax) {
          count++;
        }
        if (capacityMin || capacityMax) {
          count++;
        }
        if (budgetMin || budgetMax) {
          count++;
        }
        count += countMoreActive();
        return count;
      }

      function syncActiveFilterCount() {
        const count = countActiveFiltersForBadge();
        document.querySelectorAll('.js-ps-active-filter-count').forEach(function (el) {
          if (count > 0) {
            el.textContent = '(' + count + ')';
            el.hidden = false;
            el.removeAttribute('aria-hidden');
          }
          else {
            el.hidden = true;
            el.setAttribute('aria-hidden', 'true');
          }
        });
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

      function getMoreGroupScopeKey(groupId, panel) {
        const scope = panel.closest('#ps-mobile-filters') ? 'mobile' : 'desktop';
        return scope + ':' + groupId;
      }

      function buildMoreCriteriaQueryString(options) {
        const params = new URLSearchParams();
        if (selectedAsset) {
          params.set('asset_type', selectedAsset);
        }
        if (options && options.idPrefix) {
          params.set('id_prefix', options.idPrefix);
        }
        return params.toString();
      }

      function loadMoreCriteriaGroup(groupId, panel, options) {
        const scopeKey = getMoreGroupScopeKey(groupId, panel);
        if (!groupId || !panel || panel.dataset.loaded === '1' || loadedMoreGroups[scopeKey] === true || loadedMoreGroups[scopeKey] === 'pending') {
          syncMoreInputsFromState();
          return Promise.resolve();
        }
        const content = panel.classList.contains('js-ps-more-group-content')
          ? panel
          : panel.querySelector('.js-ps-more-group-content');
        if (!content) {
          return Promise.resolve();
        }
        const loading = content.querySelector('.ps-more-group__loading');
        if (loading) {
          loading.hidden = false;
        }
        loadedMoreGroups[scopeKey] = 'pending';
        const queryString = buildMoreCriteriaQueryString(options);

        if (typeof htmxApi.loadMoreCriteriaGroup === 'function') {
          return htmxApi.loadMoreCriteriaGroup(groupId, content, queryString)
            .then(function () {
              if (loading) {
                loading.hidden = true;
              }
              panel.dataset.loaded = '1';
              loadedMoreGroups[scopeKey] = true;
              syncMoreInputsFromState();
            })
            .catch(function () {
              if (loading) {
                loading.hidden = true;
              }
              delete loadedMoreGroups[scopeKey];
            });
        }

        delete loadedMoreGroups[scopeKey];
        return Promise.resolve();
      }

      let mobileMoreCriteriaObserver = null;

      function setupMobileMoreCriteriaLazyLoad(offcanvasEl) {
        const scrollRoot = offcanvasEl.querySelector('.offcanvas-body') || offcanvasEl;

        if (mobileMoreCriteriaObserver) {
          mobileMoreCriteriaObserver.disconnect();
          mobileMoreCriteriaObserver = null;
        }

        const panels = offcanvasEl.querySelectorAll('.js-ps-more-group-panel[data-loaded="0"]');
        if (!panels.length) {
          return;
        }

        mobileMoreCriteriaObserver = new IntersectionObserver(function (entries) {
          entries.forEach(function (entry) {
            if (!entry.isIntersecting) {
              return;
            }
            const panel = entry.target;
            mobileMoreCriteriaObserver.unobserve(panel);
            loadMoreCriteriaGroup(panel.dataset.groupId, panel, { idPrefix: 'ps-more-m' });
          });
        }, { root: scrollRoot, rootMargin: '80px 0px' });

        panels.forEach(function (panel) {
          mobileMoreCriteriaObserver.observe(panel);
        });
      }

      function teardownMobileMoreCriteriaLazyLoad() {
        if (mobileMoreCriteriaObserver) {
          mobileMoreCriteriaObserver.disconnect();
          mobileMoreCriteriaObserver = null;
        }
      }

      // ── Bootstrap dropdown / offcanvas integration ───────────────────────
      function getBootstrap() {
        return window.bootstrap;
      }

      function closeAllDropdowns(excludeDropdownEl) {
        const bs = getBootstrap();
        if (!bs || !bs.Dropdown) {
          return;
        }
        document.querySelectorAll('.ps-filter-bar [data-bs-toggle="dropdown"], .ps-filter-bar .js-ps-location-toggle').forEach(function (toggle) {
          if (excludeDropdownEl && excludeDropdownEl.contains(toggle)) {
            return;
          }
          const instance = bs.Dropdown.getInstance(toggle);
          if (instance) {
            instance.hide();
          }
        });
      }

      function closeOtherFilterPanels(excludeDropdownEl) {
        closeAllDropdowns(excludeDropdownEl);
        const bs = getBootstrap();
        const offcanvasEl = document.getElementById('ps-more-offcanvas');
        if (bs?.Offcanvas && offcanvasEl) {
          bs.Offcanvas.getInstance(offcanvasEl)?.hide();
        }
        if (!excludeDropdownEl || !excludeDropdownEl.classList.contains('ps-filter-bar__item--location')) {
          hideAllLocationSuggestions();
        }
        syncFilterBarBackdrop();
      }

      function getFilterItemToggle(item) {
        if (!item) {
          return null;
        }
        return item.querySelector('[data-bs-toggle="dropdown"], .js-ps-location-toggle');
      }

      function isFilterItemOpen(item) {
        if (!item) {
          return false;
        }
        return item.classList.contains('show') || !!item.querySelector('.dropdown-menu.show');
      }

      function closeFilterItemDropdown(item) {
        if (!item) {
          return;
        }
        const toggle = getFilterItemToggle(item);
        const bs = getBootstrap();
        if (toggle && bs?.Dropdown) {
          const instance = bs.Dropdown.getInstance(toggle);
          if (instance) {
            instance.hide();
            return;
          }
        }
        if (item.classList.contains('ps-filter-bar__item--location')) {
          closeLocationDropdown(item);
        }
      }

      function syncFilterItemExpandedState(item, expanded) {
        const toggle = getFilterItemToggle(item);
        if (toggle) {
          toggle.setAttribute('aria-expanded', expanded ? 'true' : 'false');
        }
        if (item?.classList.contains('ps-filter-bar__item--location')) {
          item.querySelectorAll('.js-ps-locality-input').forEach(function (input) {
            if (input.getAttribute('aria-controls') === 'ps-location-suggest' || input.id === 'ps-filter-location-input') {
              if (!expanded) {
                input.setAttribute('aria-expanded', 'false');
              }
            }
          });
        }
      }

      function openFilterItemDropdown(item) {
        if (!item || item.hasAttribute('hidden')) {
          return;
        }
        if (item.classList.contains('ps-filter-bar__item--location')) {
          openLocationDropdown(item);
          return;
        }
        const toggle = getFilterItemToggle(item);
        const bs = getBootstrap();
        if (toggle && bs?.Dropdown) {
          bs.Dropdown.getOrCreateInstance(toggle, { autoClose: 'outside' }).show();
        }
      }

      function isFocusInsideFilterItem(item) {
        if (!item) {
          return false;
        }
        const active = document.activeElement;
        return active instanceof Node && item.contains(active);
      }

      function bindFilterBarAccessibility(filterBar) {
        const FILTER_FOCUS_CLOSE_DELAY_MS = 150;

        filterBar.addEventListener('focusin', function (e) {
          const targetItem = e.target.closest('.ps-filter-bar__item');
          if (!targetItem || !filterBar.contains(targetItem)) {
            return;
          }

          if (targetItem.classList.contains('ps-filter-bar__item--location')) {
            const locationInput = targetItem.querySelector('.js-ps-locality-input');
            const inlineChip = e.target.closest('.ps-filter-bar__location-chips .ps-location-chip');
            if (inlineChip && locationInput && e.target !== locationInput) {
              if (!isFilterItemOpen(targetItem)) {
                closeOtherFilterPanels(targetItem);
                openFilterItemDropdown(targetItem);
              }
              locationInput.focus();
              return;
            }
          }

          filterBar.querySelectorAll('.ps-filter-bar__item.dropdown').forEach(function (item) {
            if (item === targetItem || !isFilterItemOpen(item)) {
              return;
            }
            closeFilterItemDropdown(item);
          });

          if (targetItem.classList.contains('dropdown') && !isFilterItemOpen(targetItem)) {
            closeOtherFilterPanels(targetItem);
            openFilterItemDropdown(targetItem);
          }
        });

        filterBar.querySelectorAll('.ps-filter-bar__item.dropdown').forEach(function (item) {
          item.addEventListener('focusout', function () {
            setTimeout(function () {
              if (!isFilterItemOpen(item)) {
                return;
              }
              if (isFocusInsideFilterItem(item)) {
                return;
              }
              closeFilterItemDropdown(item);
            }, FILTER_FOCUS_CLOSE_DELAY_MS);
          });
        });

        filterBar.addEventListener('keydown', function (e) {
          if (e.key !== 'Escape') {
            return;
          }

          const bs = getBootstrap();
          const moreOffcanvas = document.getElementById('ps-more-offcanvas');
          if (moreOffcanvas?.classList.contains('show') && bs?.Offcanvas) {
            e.preventDefault();
            e.stopPropagation();
            bs.Offcanvas.getInstance(moreOffcanvas)?.hide();
            document.querySelector('.js-ps-more-trigger')?.focus();
            syncFilterBarBackdrop();
            return;
          }

          const openItems = Array.from(filterBar.querySelectorAll('.ps-filter-bar__item.dropdown'))
            .filter(isFilterItemOpen);
          if (openItems.length === 0) {
            return;
          }
          e.preventDefault();
          e.stopPropagation();
          const item = openItems[0];
          let focusTarget = getFilterItemToggle(item);
          if (item.classList.contains('ps-filter-bar__item--location')) {
            focusTarget = item.querySelector('.js-ps-locality-input') || focusTarget;
          }
          closeFilterItemDropdown(item);
          hideAllLocationSuggestions();
          syncFilterBarBackdrop();
          if (focusTarget && typeof focusTarget.focus === 'function') {
            focusTarget.focus();
          }
        });
      }

      function bindMoreOffcanvasAccessibility() {
        const FILTER_FOCUS_CLOSE_DELAY_MS = 150;
        const offcanvasEl = document.getElementById('ps-more-offcanvas');
        const trigger = document.querySelector('.js-ps-more-trigger');
        if (!offcanvasEl || !trigger) {
          return;
        }

        trigger.addEventListener('focus', function () {
          const bs = getBootstrap();
          if (!bs?.Offcanvas || offcanvasEl.classList.contains('show')) {
            return;
          }
          closeAllDropdowns();
          hideAllLocationSuggestions();
          bs.Offcanvas.getOrCreateInstance(offcanvasEl).show();
        });

        offcanvasEl.addEventListener('focusout', function () {
          setTimeout(function () {
            if (!offcanvasEl.classList.contains('show')) {
              return;
            }
            const active = document.activeElement;
            if (active instanceof Node && (offcanvasEl.contains(active) || trigger.contains(active))) {
              return;
            }
            getBootstrap()?.Offcanvas.getInstance(offcanvasEl)?.hide();
          }, FILTER_FOCUS_CLOSE_DELAY_MS);
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

      function isMobileFiltersOffcanvasOpen() {
        const offcanvasEl = document.getElementById('ps-mobile-filters');
        return !!(offcanvasEl && offcanvasEl.classList.contains('show'));
      }

      function syncFilterBarBackdrop() {
        const backdrop = getFilterBarBackdrop();
        if (!backdrop) {
          return;
        }
        mountFilterBarBackdrop(backdrop);
        const shouldShow = isAnyFilterDropdownOpen() && !isMoreOffcanvasOpen() && !isMobileFiltersOffcanvasOpen();
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
          const popinKey = htmxApi.resolvePopinKeyFromDropdown(dropdownEl);
          if (popinKey && htmxApi.isHtmxPopin(popinKey)) {
            htmxApi.refreshCount(popinKey, buildCountParams().toString());
          }
          else {
            htmxApi.refreshGlobalCount(buildCountParams().toString());
          }
        });
        dropdownEl.addEventListener('shown.bs.dropdown', function () {
          syncFilterBarBackdrop();
          syncFilterItemExpandedState(dropdownEl, true);
          if (dropdownEl.classList.contains('ps-filter-bar__item--location')) {
            const locationInput = dropdownEl.querySelector('.js-ps-locality-input');
            const active = document.activeElement;
            if (locationInput && active instanceof Node
              && active !== locationInput
              && active.closest('.ps-filter-bar__location-chips')) {
              locationInput.focus();
            }
          }
        });
        dropdownEl.addEventListener('hidden.bs.dropdown', function () {
          syncFilterBarBackdrop();
          syncFilterItemExpandedState(dropdownEl, false);
          if (dropdownEl.classList.contains('ps-filter-bar__item--type')) {
            updateTypeOpBtnLabel(dropdownEl);
          }
          if (dropdownEl.classList.contains('ps-filter-bar__item--location')) {
            hideAllLocationSuggestions();
          }
        });
      });

      once('ps-filter-bar-a11y', '.ps-filter-bar', context).forEach(function (filterBar) {
        bindFilterBarAccessibility(filterBar);
      });

      once('ps-more-offcanvas-a11y', '#ps-more-offcanvas', context).forEach(function () {
        bindMoreOffcanvasAccessibility();
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

      function bindMoreOffcanvasEvents(offcanvasEl) {
        offcanvasEl.addEventListener('show.bs.offcanvas', function () {
          closeAllDropdowns();
          hideAllLocationSuggestions();
          syncFilterBarBackdrop();
          if (offcanvasEl.id === 'ps-more-offcanvas') {
            const trigger = document.querySelector('.js-ps-more-trigger');
            trigger?.setAttribute('aria-expanded', 'true');
            htmxApi.refreshCount('more', buildCountParams().toString());
          }
          if (offcanvasEl.id === 'ps-mobile-filters') {
            document.querySelector('.js-ps-mobile-filters-trigger')?.setAttribute('aria-expanded', 'true');
            htmxApi.refreshCount('mobile', buildCountParams().toString());
          }
        });
        offcanvasEl.addEventListener('hidden.bs.offcanvas', function () {
          if (offcanvasEl.id === 'ps-mobile-filters') {
            teardownMobileMoreCriteriaLazyLoad();
            document.querySelector('.js-ps-mobile-filters-trigger')?.setAttribute('aria-expanded', 'false');
          }
          if (offcanvasEl.id === 'ps-more-offcanvas') {
            document.querySelector('.js-ps-more-trigger')?.setAttribute('aria-expanded', 'false');
          }
          syncFilterBarBackdrop();
        });
        offcanvasEl.addEventListener('shown.bs.offcanvas', function () {
          if (offcanvasEl.id === 'ps-mobile-filters') {
            setupMobileMoreCriteriaLazyLoad(offcanvasEl);
          }
          document.querySelectorAll('.js-ps-surface-min').forEach(function (el) {
            el.value = surfaceMin;
          });
          document.querySelectorAll('.js-ps-surface-max').forEach(function (el) {
            el.value = surfaceMax;
          });
          document.querySelectorAll('.js-ps-capacity-min').forEach(function (el) {
            el.value = capacityMin;
          });
          document.querySelectorAll('.js-ps-capacity-max').forEach(function (el) {
            el.value = capacityMax;
          });
          document.querySelectorAll('.js-ps-budget-min').forEach(function (el) {
            el.value = budgetMin;
          });
          document.querySelectorAll('.js-ps-budget-max').forEach(function (el) {
            el.value = budgetMax;
          });
          syncAllTypeSectionUi();
          syncMoreInputsFromState();
          scheduleCountUpdate();
        });
        offcanvasEl.addEventListener('show.bs.collapse', function (event) {
          const panel = event.target;
          if (!panel.classList.contains('js-ps-more-group-panel')) {
            return;
          }
          loadMoreCriteriaGroup(panel.dataset.groupId, panel);
        });
        offcanvasEl.addEventListener('change', function (event) {
          if (event.target.matches('.js-ps-more-filter')) {
            handleMoreFilterChange(event.target);
          }
        });
        offcanvasEl.addEventListener('input', function (event) {
          if (event.target.matches('.js-ps-more-filter')) {
            handleMoreFilterInputEvent(event.target);
          }
        });
      }

      once('ps-bs-offcanvas-events', '#ps-more-offcanvas, #ps-mobile-filters', context).forEach(function (offcanvasEl) {
        bindMoreOffcanvasEvents(offcanvasEl);
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
        if (selectedOp) {
          const opSlug = opSlugs[selectedOp];
          if (!opSlug) {
            return langPrefix + searchPath;
          }
          if (selectedAsset && assetSlugs[selectedAsset]) {
            return langPrefix + '/' + opSlug + '/' + assetSlugs[selectedAsset] + '/';
          }
          return langPrefix + '/' + opSlug + '/';
        }
        if (opFlexible && selectedAsset && assetSlugs[selectedAsset]) {
          return langPrefix + '/' + assetSlugs[selectedAsset] + '/';
        }
        return langPrefix + searchPath;
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

        const firstSegment = String(segments[0]).toLowerCase();
        const firstIsOp = opSlugValues.indexOf(firstSegment) !== -1;
        const firstIsAsset = assetSlugValues.indexOf(firstSegment) !== -1;
        if (!firstIsOp && !firstIsAsset) {
          return '';
        }

        let restStart = 1;
        if (firstIsOp) {
          if (segments[1] && assetSlugValues.indexOf(String(segments[1]).toLowerCase()) !== -1) {
            restStart = 2;
          }
          else if (segments.length >= 3 && looksLikeDeptSegment(segments[2])) {
            restStart = 2;
          }
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

        if (String(last).indexOf('-') === -1) {
          return 'region:' + String(last).toLowerCase();
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
        if (useSearchContext && settings.searchContext?.geo?.label) {
          return settings.searchContext.geo.label;
        }
        if (localityArrayParams.length) {
          return localityArrayParams.join(',');
        }
        const queryLocations = currentParams.get('locations');
        if (queryLocations) {
          return queryLocations;
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

      function localityKey(tokens) {
        return normalizeLocationTokens(tokens.slice()).map(function (token) {
          return token.toLowerCase();
        }).sort().join('|');
      }

      let pageLoadLocalityKey = localityKey(parseLocationTokens(resolveInitialLocalityValue()));

      function hasLocalityChanged() {
        return localityKey(selectedLocalityTokens) !== pageLoadLocalityKey;
      }

      function setFacetQueryParam(params, key, value) {
        if (value) {
          params.append(key + '[' + value + ']', value);
        }
      }

      function clearFacetQueryParams(params, key) {
        Array.from(params.keys()).forEach(function (name) {
          if (name === key || name.indexOf(key + '[') === 0) {
            params.delete(name);
          }
        });
      }

      function buildLocalitySeoPathSegments(primaryData) {
        if (!primaryData) {
          return { region: '', dept: '', city: '' };
        }

        if (primaryData.type === 'region') {
          const regionSlug = primaryData.region_slug || primaryData.slug || '';
          if (regionSlug) {
            return { region: regionSlug, dept: '', city: '' };
          }
          const token = getPrimaryLocalityToken();
          if (String(token).indexOf('region:') === 0) {
            return { region: String(token).slice(7), dept: '', city: '' };
          }
        }

        if (primaryData.type === 'department') {
          const code = primaryData.department_code || '';
          const deptSlug = toSeoSlug(primaryData.admin_area || '');
          return {
            dept: (deptSlug && code) ? deptSlug + '-' + code : '',
            city: '',
          };
        }

        const postal = primaryData.postal_code || '';
        const deptCode = postal ? postal.substring(0, 2) : (primaryData.department_code || '');
        const deptSlug = toSeoSlug(primaryData.admin_area || '');
        let city = '';

        if (primaryData.type === 'arrondissement' && postal) {
          const arrNum = parseInt(postal.substring(3, 5), 10);
          const citySlug = toSeoSlug(primaryData.locality || '');
          if (citySlug && Number.isFinite(arrNum)) {
            city = citySlug + '-' + arrNum + '-' + postal;
          }
        }
        else if (primaryData.locality) {
          const citySlug = toSeoSlug(primaryData.locality);
          city = postal ? citySlug + '-' + postal : citySlug;
        }

        return {
          region: '',
          dept: (deptSlug && deptCode) ? deptSlug + '-' + deptCode : '',
          city: city,
        };
      }

      function appendLocationQueryParams(params, tokens) {
        if (!tokens.length) {
          return;
        }
        params.set('locations', tokens.join(','));
      }

      function usesSeoLocalityPath(base) {
        const normalized = String(base || '').replace(/\/$/, '');
        const searchBase = String(searchPath || '').replace(/\/$/, '');
        return normalized !== searchBase && normalized.indexOf(searchBase) === -1;
      }

      function syncStoreFromUiState() {
        if (!useSearchContext) {
          return;
        }

        Drupal.psSearchContext.setFilter('operationType', selectedOp || null);
        Drupal.psSearchContext.setFilter('assetType', selectedAsset || null);

        const visibility = getFilterVisibility(selectedAsset);
        Drupal.psSearchContext.setFilter('surface', visibility.show_surface && (surfaceMin || surfaceMax)
          ? { min: surfaceMin ? Number(surfaceMin) : null, max: surfaceMax ? Number(surfaceMax) : null }
          : null);
        Drupal.psSearchContext.setFilter('capacity', visibility.show_capacity && (capacityMin || capacityMax)
          ? { min: capacityMin ? Number(capacityMin) : null, max: capacityMax ? Number(capacityMax) : null }
          : null);
        Drupal.psSearchContext.setFilter('budget', (budgetMin || budgetMax)
          ? { min: budgetMin ? Number(budgetMin) : null, max: budgetMax ? Number(budgetMax) : null }
          : null);

        const primaryData = getPrimaryLocalityData();
        if (primaryData && (primaryData.slug || settings.searchContext?.geo?.slug)) {
          Drupal.psSearchContext.setGeo({
            id: primaryData.id || settings.searchContext?.geo?.id || '',
            slug: primaryData.region_slug || primaryData.slug || settings.searchContext?.geo?.slug || '',
            type: primaryData.type || settings.searchContext?.geo?.type || '',
            label: primaryData.label || primaryData.locality || primaryData.admin_area || getPrimaryLocalityToken(),
            lat: primaryData.lat ?? settings.searchContext?.geo?.lat ?? null,
            lng: primaryData.lng ?? settings.searchContext?.geo?.lng ?? null,
          });
        }
        else if (!selectedLocalityTokens.length) {
          Drupal.psSearchContext.setGeo(null);
        }

        if (hasLocalityChanged()) {
          Drupal.psSearchContext.clearSpatialViewport();
        }
        else {
          const activeMapBounds = new URLSearchParams(window.location.search).get('map_bounds');
          if (activeMapBounds) {
            Drupal.psSearchContext.setSpatialViewport(activeMapBounds);
          }
        }
      }

      function buildNavigationUrl() {
        if (useSearchContext) {
          syncStoreFromUiState();
          return Drupal.psSearchContext.buildUrl();
        }

        let base = buildSeoBase();
        const p = new URLSearchParams();
        const primaryData = getPrimaryLocalityData();
        const singleLocation = selectedLocalityTokens.length === 1;
        const useSeoLocalityPath = usesSeoLocalityPath(base) && singleLocation && primaryData && (
          primaryData.type === 'region'
          || primaryData.type === 'department'
          || primaryData.locality
          || primaryData.postal_code
        );

        // Asset-only (Indifférent): asset slug is in the SEO path, not query params.
        if (selectedAsset && !selectedOp && !opFlexible) {
          setFacetQueryParam(p, 'asset_type', selectedAsset);
        }

        // SEO locality segments on operation or asset-only SEO bases.
        if (useSeoLocalityPath) {
          base = base.replace(/\/?$/, '/');
          const segments = buildLocalitySeoPathSegments(primaryData);
          if (segments.region) {
            base += segments.region + '/';
          }
          if (segments.dept) {
            base += segments.dept + '/';
          }
          if (segments.city) {
            base += segments.city + '/';
          }
        }
        else if (selectedLocalityTokens.length) {
          appendLocationQueryParams(p, selectedLocalityTokens);
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

        // Rule A: keep map_bounds when locality is unchanged.
        // Rule B: locality change drops map_bounds so the server recomputes the zone.
        const activeMapBounds = new URLSearchParams(window.location.search).get('map_bounds');
        if (!hasLocalityChanged() && activeMapBounds) {
          p.set('map_bounds', activeMapBounds);
        }

        const qs = p.toString();
        return qs ? base + '?' + qs : base;
      }

      /**
       * Builds query params for Views AJAX from the navigation URL + filter state.
       *
       * SEO paths embed operation/asset in the path; Views AJAX needs facet params.
       *
       * @return {{browserUrl: string, params: URLSearchParams}}
       *   Browser URL and AJAX query parameters.
       */
      function buildViewAjaxParams() {
        if (useSearchContext) {
          syncStoreFromUiState();
          const browserUrl = Drupal.psSearchContext.buildUrl();
          const resolved = new URL(browserUrl, window.location.origin);
          const params = Drupal.psSearchContext.buildApiParams();
          params.delete('page');
          return {
            browserUrl: resolved.pathname + resolved.search,
            params: params,
          };
        }

        const navigationUrl = buildNavigationUrl();
        const resolved = new URL(navigationUrl, window.location.origin);
        const params = new URLSearchParams(resolved.search);

        if (selectedOp) {
          clearFacetQueryParams(params, 'operation_type');
          setFacetQueryParam(params, 'operation_type', selectedOp);
        }
        if (selectedAsset) {
          clearFacetQueryParams(params, 'asset_type');
          setFacetQueryParam(params, 'asset_type', selectedAsset);
        }
        if (selectedLocalityTokens.length) {
          params.set('locality', selectedLocalityTokens.join(', '));
        }

        params.delete('page');

        return {
          browserUrl: resolved.pathname + resolved.search,
          params: params,
        };
      }

      /**
       * Normalizes a pathname for stable comparison (trailing slash).
       *
       * @param {string} pathname
       *   URL pathname.
       *
       * @return {string}
       *   Normalized pathname.
       */
      function normalizePathname(pathname) {
        const path = String(pathname || '/');
        if (path.length > 1 && path.endsWith('/')) {
          return path.slice(0, -1);
        }
        return path;
      }

      /**
       * Whether the pathname is the flexible search base (no operation slug).
       *
       * @param {string} pathname
       *   URL pathname.
       *
       * @return {boolean}
       *   TRUE on /find-property or /fr/recherche-immobiliere style paths.
       */
      function isFlexibleSearchPathname(pathname) {
        const flexBase = normalizePathname(langPrefix + searchPath);
        return normalizePathname(pathname) === flexBase;
      }

      /**
       * Reads a facet value from query params (scalar or BEF bracket format).
       *
       * @param {URLSearchParams} params
       *   Query parameters.
       * @param {string} key
       *   Facet key (e.g. operation_type, asset_type).
       *
       * @return {string|null}
       *   Facet code or NULL.
       */
      function extractFacetQueryValue(params, key) {
        if (params.has(key)) {
          return params.get(key);
        }
        const prefix = key + '[';
        let found = null;
        params.forEach(function (value, name) {
          if (found !== null) {
            return;
          }
          if (name.indexOf(prefix) === 0 && name.endsWith(']')) {
            found = value || name.slice(prefix.length, -1);
          }
        });
        return found;
      }

      /**
       * Whether flexible-base query params should canonicalize via server 301.
       *
       * @param {URLSearchParams} params
       *   Target query parameters.
       *
       * @return {boolean}
       *   TRUE when operation_type and/or asset_type are present.
       */
      function hasCanonicalFacetQuery(params) {
        return Boolean(
          extractFacetQueryValue(params, 'operation_type')
          || extractFacetQueryValue(params, 'asset_type'),
        );
      }

      /**
       * Whether filter apply must trigger a full page load (SEO path change).
       *
       * Query-only updates on the flexible base with facet params use a full
       * load so SearchCanonicalRedirectSubscriber can 301 to SEO URLs.
       *
       * @param {string} browserUrl
       *   Target browser URL (path + query).
       *
       * @return {boolean}
       *   TRUE when pathname differs or canonical facet query needs server redirect.
       */
      function requiresFullNavigation(browserUrl) {
        if (useSearchContext) {
          return Drupal.psSearchContext.requiresFullNavigation(browserUrl);
        }

        const next = new URL(browserUrl, window.location.origin);
        if (normalizePathname(next.pathname) !== normalizePathname(window.location.pathname)) {
          return true;
        }
        return isFlexibleSearchPathname(next.pathname) && hasCanonicalFacetQuery(next.searchParams);
      }

      function appendContentLangParam(p) {
        if (Drupal.psSearchPage && typeof Drupal.psSearchPage.appendContentLangParam === 'function') {
          return Drupal.psSearchPage.appendContentLangParam(p);
        }
        if (!p.has('lang')) {
          const lang = drupalSettings.path?.currentLanguage
            || document.documentElement.lang?.split('-')[0]
            || '';
          if (lang) {
            p.set('lang', lang);
          }
        }
        return p;
      }

      function buildCountParams() {
        if (useSearchContext) {
          syncStoreFromUiState();
          return Drupal.psSearchContext.buildApiParams();
        }

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
        return appendContentLangParam(p);
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

      htmxApi.callbacks.setLoading = setApplyBtnsLoading;
      htmxApi.callbacks.onCountUpdated = function (count) {
        if (count !== null) {
          updateCountDisplays(count);
        }
      };
      const HTMX_POPIN_KEYS = ['type', 'location', 'surface', 'capacity', 'budget', 'mobile'];

      htmxApi.callbacks.onApply = function (detail) {
        if (detail && HTMX_POPIN_KEYS.indexOf(detail.popinKey) !== -1) {
          htmxApi.closePopinDropdown(detail.popinKey);
        }
        applyFilters();
      };

      function applyPopinViaHtmx(popinKey) {
        const payload = buildViewAjaxParams();
        if (htmxApi.applyPopin(popinKey, payload.params.toString())) {
          return;
        }
        applyFilters();
      }

      function applyTypePopinViaHtmx() {
        applyPopinViaHtmx('type');
      }

      function applyLocationPopinViaHtmx() {
        applyPopinViaHtmx('location');
      }

      /**
       * Schedules a live count refresh for the active popin context.
       */
      function scheduleLiveCountUpdate() {
        clearTimeout(countDebounce);
        countDebounce = setTimeout(function () {
          htmxApi.refreshGlobalCount(buildCountParams().toString());
        }, 300);
      }

      function scheduleCountUpdate() {
        scheduleLiveCountUpdate();
      }

      function applyFilters() {
        const ajaxPayload = buildViewAjaxParams();
        const root = document.querySelector('.ps-search-view');

        if (requiresFullNavigation(ajaxPayload.browserUrl)) {
          window.location.assign(ajaxPayload.browserUrl);
          return;
        }

        if (!root || typeof Drupal.psSearchPage?.reloadSearch !== 'function') {
          window.location.href = ajaxPayload.browserUrl;
          return;
        }

        setApplyBtnsLoading(true);
        Drupal.psSearchPage.reloadSearch(root, ajaxPayload)
          .then(function () {
            pageLoadLocalityKey = localityKey(selectedLocalityTokens);
          })
          .catch(function () {
            window.location.href = ajaxPayload.browserUrl;
          })
          .finally(function () {
            setApplyBtnsLoading(false);
          });
      }

      function navigate() {
        applyFilters();
      }

      function commitAllLocationDrafts() {
        document.querySelectorAll('.js-ps-locality-input').forEach(function (input) {
          const draft = input.value.trim();
          if (draft) {
            addLocationTokens(parseLocationTokens(draft));
            input.value = '';
          }
        });
      }

      // ── Apply buttons (all popins — same navigation) ──────────────────────
      once('ps-apply', '.js-ps-apply-btn', context).forEach(function (btn) {
        btn.addEventListener('click', function () {
          if (!btn.disabled) {
            commitAllLocationDrafts();
            const htmxApplyKey = btn.getAttribute('data-ps-htmx-apply');
            if (htmxApplyKey && htmxApi.isHtmxPopin(htmxApplyKey)) {
              applyPopinViaHtmx(htmxApplyKey);
              return;
            }
            if (btn.closest('.ps-filter-popin--type') && htmxApi.isHtmxPopin('type')) {
              applyTypePopinViaHtmx();
              return;
            }
            navigate();
          }
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

      function syncAllTypeSectionUi() {
        document.querySelectorAll('.ps-filter-bar__item--type').forEach(function (wrapper) {
          syncOpButtonStates(wrapper);
          wrapper.querySelectorAll('.js-ps-asset-btn').forEach(function (a) {
            const active = a.dataset.code === selectedAsset;
            const card = a.closest('.ps-asset-card');
            if (card) {
              card.classList.toggle('is-active', active);
            }
            a.setAttribute('aria-pressed', String(active));
          });
          updateTypeOpBtnLabel(wrapper);
        });
      }

      once('ps-type-filter', '.ps-filter-bar__item--type', context).forEach(function (wrapper) {
        syncOpButtonStates(wrapper);

        // Asset cards.
        wrapper.querySelectorAll('.js-ps-asset-btn').forEach(function (btn) {
          btn.addEventListener('click', function () {
            const code = btn.dataset.code;
            selectedAsset = (selectedAsset === code) ? null : code;
            syncAllTypeSectionUi();
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
            syncAllTypeSectionUi();
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
            syncAllTypeSectionUi();
            scheduleCountUpdate();
            updateAssetMode();
            updateBudgetUi();
          });
        }
      });

      // ── 2. Localisation — chips + grouped autocomplete popin (Figma) ───────
      once('ps-locality', '.js-ps-locality-input', context).forEach(function (input) {
        if (input.closest('[data-ps-homepage-search-entry]')) {
          return;
        }

        const dropdownEl = input.closest('.ps-filter-bar__item--location');
        const locationState = {
          get tokens() {
            return selectedLocalityTokens;
          },
          set tokens(value) {
            selectedLocalityTokens = value;
          },
          get data() {
            return selectedLocalityData;
          },
          set data(value) {
            selectedLocalityData = value;
          },
        };
        const initialLocality = localityArrayParams.length
          ? localityArrayParams.join(',')
          : resolveInitialLocalityValue();

        Drupal.psSearchLocationEditor.attach({
          input: input,
          rootEl: dropdownEl,
          mode: 'dropdown',
          state: locationState,
          locationSuggestUrl: locationSuggestUrl,
          locationDataUrl: locationDataUrl,
          appendContentLangParam: appendContentLangParam,
          initialValue: initialLocality,
          onChange: function () {
            refreshSelectedLocality();
            scheduleCountUpdate();
          },
          onEnter: navigate,
          closeOtherPanels: closeOtherFilterPanels,
          openDropdown: openLocationDropdown,
          closeDropdown: closeLocationDropdown,
        });
      });

      once('ps-location-apply', '.js-ps-location-apply', context).forEach(function (btn) {
        btn.addEventListener('click', function () {
          if (btn.disabled) {
            return;
          }
          commitAllLocationDrafts();
          if (htmxApi.isHtmxPopin('location')) {
            applyLocationPopinViaHtmx();
            return;
          }
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
        syncActiveFilterCount();
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
              document.querySelectorAll(sel).forEach(function (el) {
                el.value = '';
              });
            });
            updateSurfaceLabel();
          } else if (section === 'budget') {
            budgetMin = '';
            budgetMax = '';
            ['.js-ps-budget-min', '.js-ps-budget-max'].forEach(function (sel) {
              document.querySelectorAll(sel).forEach(function (el) {
                el.value = '';
              });
            });
            updateBudgetLabel();
          } else if (section === 'capacity') {
            capacityMin = '';
            capacityMax = '';
            ['.js-ps-capacity-min', '.js-ps-capacity-max'].forEach(function (sel) {
              document.querySelectorAll(sel).forEach(function (el) {
                el.value = '';
              });
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
        syncActiveFilterCount();
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

      once('ps-mobile-reset', '.js-ps-mobile-reset-all', context).forEach(function (btn) {
        btn.addEventListener('click', function () {
          selectedAsset = null;
          selectedOp = null;
          opFlexible = true;
          selectedLocalityTokens = [];
          selectedLocalityData = [];
          selectedLocality = '';
          surfaceMin = '';
          surfaceMax = '';
          capacityMin = '';
          capacityMax = '';
          budgetMin = '';
          budgetMax = '';
          moreFilters = {};
          document.querySelectorAll('.js-ps-locality-input').forEach(function (input) {
            input.value = '';
          });
          document.querySelectorAll('.js-ps-surface-min, .js-ps-surface-max, .js-ps-capacity-min, .js-ps-capacity-max, .js-ps-budget-min, .js-ps-budget-max').forEach(function (input) {
            input.value = '';
          });
          syncAllTypeSectionUi();
          syncMoreInputsFromState();
          updateSurfaceLabel();
          updateCapacityLabel();
          updateBudgetLabel();
          scheduleCountUpdate();
        });
      });

      once('ps-show-map', '.js-ps-show-map', context).forEach(function (btn) {
        const showMapLabel = btn.dataset.showMapLabel || btn.textContent.trim();
        const showListLabel = btn.dataset.showListLabel || 'Show list';

        const syncMobileMapToggle = function () {
          const root = document.querySelector('.ps-search-view');
          if (!root || typeof Drupal.psSearchMap?.isListVisible !== 'function') {
            return;
          }
          const listVisible = Drupal.psSearchMap.isListVisible(root);
          const label = listVisible ? showMapLabel : showListLabel;
          btn.setAttribute('aria-expanded', listVisible ? 'false' : 'true');
          let icon = btn.querySelector('.ps-filter-bar-mobile-actions__icon');
          if (!icon) {
            icon = document.createElement('span');
            icon.className = 'ps-filter-bar-mobile-actions__icon';
            icon.setAttribute('aria-hidden', 'true');
          }
          icon.classList.toggle('ps-filter-bar-mobile-actions__icon--map', listVisible);
          icon.classList.toggle('ps-filter-bar-mobile-actions__icon--list', !listVisible);
          btn.replaceChildren(icon, document.createTextNode(` ${label}`));
        };

        btn.addEventListener('click', function () {
          const root = document.querySelector('.ps-search-view');
          if (!root || typeof Drupal.psSearchMap?.setListVisible !== 'function') {
            return;
          }
          const showList = !Drupal.psSearchMap.isListVisible(root);
          Drupal.psSearchMap.setListVisible(root, showList);
          syncMobileMapToggle();
        });

        const root = document.querySelector('.ps-search-view');
        if (root) {
          root.addEventListener('ps-search-list-shown', syncMobileMapToggle);
          root.addEventListener('ps-search-map-mode', syncMobileMapToggle);
        }
        syncMobileMapToggle();
      });

      once('ps-search-back', '.js-ps-search-back', context).forEach(function (btn) {
        btn.addEventListener('click', function (event) {
          event.preventDefault();
          if (window.history.length > 1) {
            window.history.back();
            return;
          }
          const prefix = settings.langPrefix || '';
          window.location.href = prefix || '/';
        });
      });

      updateAssetMode();
      updateBudgetUi();
      syncMoreInputsFromState();
      updateCapacityLabel();
      syncActiveFilterCount();
      scheduleCountUpdate();
      });
    },
  };

}(Drupal, drupalSettings, once));

