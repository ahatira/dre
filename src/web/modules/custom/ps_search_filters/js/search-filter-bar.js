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
      const capacityFilterLabel = settings.capacityFilterLabel || Drupal.t('Capacity');
      const currentParams = new URLSearchParams(window.location.search);

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
      let ceilingMin = currentParams.get('ceiling_height[min]') || '';
      let ceilingMax = currentParams.get('ceiling_height[max]') || '';
      let moreTransport = currentParams.get('nearby_transport') || '';
      let moreReference = currentParams.get('reference') || '';
      let moreBooleans = {};
      let moreCheckboxes = {};
      let countDebounce = null;
      let locationSuggestDebounce = null;
      let activeSuggestionIndex = -1;
      let currentCount = null;

      currentParams.forEach(function (value, key) {
        if (key === 'feature_accessibility' || key === 'has_immersive_tour' || key === 'has_video') {
          moreBooleans[key] = value === '1';
        }
      });
      const localityArrayParams = currentParams.getAll('locality[]');
      ['feature_equipments', 'feature_services', 'feature_building_type'].forEach(function (param) {
        const values = currentParams.getAll(param);
        if (values.length) {
          moreCheckboxes[param] = values;
        }
      });

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

      function updateBudgetHeading() {
        const heading = document.querySelector('.js-ps-budget-heading');
        const label = document.querySelector('.js-ps-budget-label');
        const text = selectedOp === 'VEN'
          ? Drupal.t('Price (€)')
          : Drupal.t('Rent (€/m²/year)');
        if (heading) {
          heading.textContent = text;
        }
        if (label && !budgetMin && !budgetMax) {
          label.textContent = Drupal.t('Price');
        }
      }

      function countMoreActive() {
        let count = 0;
        Object.keys(moreBooleans).forEach(function (key) {
          if (moreBooleans[key]) {
            count++;
          }
        });
        Object.keys(moreCheckboxes).forEach(function (key) {
          count += (moreCheckboxes[key] || []).length;
        });
        if (moreTransport.trim()) count++;
        if (moreReference.trim()) count++;
        if (ceilingMin || ceilingMax) count++;
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
        document.querySelectorAll('.js-ps-more-boolean').forEach(function (input) {
          const param = input.dataset.param;
          input.checked = !!moreBooleans[param];
        });
        document.querySelectorAll('.js-ps-more-checkbox').forEach(function (input) {
          const param = input.dataset.param;
          const values = moreCheckboxes[param] || [];
          input.checked = values.indexOf(input.value) !== -1;
        });
        const transport = document.querySelector('.js-ps-more-transport');
        if (transport) transport.value = moreTransport;
        const reference = document.querySelector('.js-ps-more-reference');
        if (reference) reference.value = moreReference;
        const cMin = document.querySelector('.js-ps-ceiling-min');
        if (cMin) cMin.value = ceilingMin;
        const cMax = document.querySelector('.js-ps-ceiling-max');
        if (cMax) cMax.value = ceilingMax;
        updateMoreLabel();
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
          if (!dropdownEl.classList.contains('ps-filter-bar__item--location')) {
            return;
          }
          const toggle = dropdownEl.querySelector('.js-ps-location-toggle');
          if (toggle) {
            toggle.setAttribute('aria-expanded', 'true');
          }
        });
        dropdownEl.addEventListener('hidden.bs.dropdown', function () {
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

      once('ps-bs-offcanvas-events', '#ps-more-offcanvas', context).forEach(function (offcanvasEl) {
        offcanvasEl.addEventListener('show.bs.offcanvas', function () {
          closeAllDropdowns();
          hideAllLocationSuggestions();
          fetchCount();
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
        if (ceilingMin) p.set('ceiling_height[min]', ceilingMin);
        if (ceilingMax) p.set('ceiling_height[max]', ceilingMax);
        if (moreTransport) p.set('nearby_transport', moreTransport);
        if (moreReference) p.set('reference', moreReference);
        Object.keys(moreBooleans).forEach(function (param) {
          if (moreBooleans[param]) {
            p.set(param, '1');
          }
        });
        Object.keys(moreCheckboxes).forEach(function (param) {
          (moreCheckboxes[param] || []).forEach(function (value) {
            p.append(param, value);
          });
        });
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
        if (ceilingMin) p.set('ceiling_height_min', ceilingMin);
        if (ceilingMax) p.set('ceiling_height_max', ceilingMax);
        if (moreTransport) p.set('nearby_transport', moreTransport);
        if (moreReference) p.set('reference', moreReference);
        Object.keys(moreBooleans).forEach(function (param) {
          if (moreBooleans[param]) {
            p.set(param, '1');
          }
        });
        Object.keys(moreCheckboxes).forEach(function (param) {
          (moreCheckboxes[param] || []).forEach(function (value) {
            p.append(param, value);
          });
        });
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
            updateBudgetHeading();
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
            updateBudgetHeading();
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
            updateBudgetHeading();
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
      function updateBudgetLabel() {
        const lbl = document.querySelector('.js-ps-budget-label');
        if (!lbl) return;
        if (budgetMin && budgetMax) lbl.textContent = budgetMin + '\u2013' + budgetMax + ' \u20ac';
        else if (budgetMin) lbl.textContent = '\u2265 ' + budgetMin + ' \u20ac';
        else if (budgetMax) lbl.textContent = '\u2264 ' + budgetMax + ' \u20ac';
        else lbl.textContent = Drupal.t('Price');
        const filterItem = document.querySelector('.ps-filter-bar__item--budget');
        if (filterItem) filterItem.classList.toggle('is-active', !!(budgetMin || budgetMax));
      }

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
            moreBooleans = {};
            moreCheckboxes = {};
            moreTransport = '';
            moreReference = '';
            ceilingMin = '';
            ceilingMax = '';
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

      once('ps-more-boolean', '.js-ps-more-boolean', context).forEach(function (input) {
        input.addEventListener('change', function () {
          moreBooleans[input.dataset.param] = input.checked;
          updateMoreLabel();
          scheduleCountUpdate();
        });
      });

      once('ps-more-checkbox', '.js-ps-more-checkbox', context).forEach(function (input) {
        input.addEventListener('change', function () {
          const param = input.dataset.param;
          const list = moreCheckboxes[param] ? moreCheckboxes[param].slice() : [];
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
          moreCheckboxes[param] = list;
          updateMoreLabel();
          scheduleCountUpdate();
        });
      });

      once('ps-more-transport', '.js-ps-more-transport', context).forEach(function (input) {
        input.value = moreTransport;
        input.addEventListener('input', function () {
          moreTransport = input.value;
          updateMoreLabel();
          scheduleCountUpdate();
        });
      });

      once('ps-more-reference', '.js-ps-more-reference', context).forEach(function (input) {
        input.value = moreReference;
        input.addEventListener('input', function () {
          moreReference = input.value;
          updateMoreLabel();
          scheduleCountUpdate();
        });
      });

      once('ps-ceiling-min', '.js-ps-ceiling-min', context).forEach(function (input) {
        input.value = ceilingMin;
        input.addEventListener('input', function () {
          ceilingMin = input.value;
          updateMoreLabel();
          scheduleCountUpdate();
        });
      });

      once('ps-ceiling-max', '.js-ps-ceiling-max', context).forEach(function (input) {
        input.value = ceilingMax;
        input.addEventListener('input', function () {
          ceilingMax = input.value;
          updateMoreLabel();
          scheduleCountUpdate();
        });
      });

      updateAssetMode();
      updateBudgetHeading();
      syncMoreInputsFromState();
      updateCapacityLabel();
      scheduleCountUpdate();
    },
  };

}(Drupal, drupalSettings, once));

