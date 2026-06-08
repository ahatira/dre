/**
 * @file
 * Search Filter Bar — BNPPRE-style filter behaviour.
 *
 * Filter sections (matching BNPPRE.fr):
 *  1. Type + need  — asset type cards + operation type buttons (popin).
 *  2. Localisation — text input directly in the bar (no popin).
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
      const currentParams = new URLSearchParams(window.location.search);

      // ── Shared state (all filter sections) ───────────────────────────────
      let selectedOp = settings.activeOp || null;
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
      ['feature_equipments', 'feature_services', 'feature_building_type'].forEach(function (param) {
        const values = currentParams.getAll(param);
        if (values.length) {
          moreCheckboxes[param] = values;
        }
      });

      function updateAssetMode() {
        const isCow = selectedAsset === 'COW';
        const surfaceItem = document.querySelector('.js-ps-surface-item');
        const capacityItem = document.querySelector('.js-ps-capacity-item');
        if (surfaceItem) {
          surfaceItem.hidden = isCow;
        }
        if (capacityItem) {
          capacityItem.hidden = !isCow;
        }
        if (isCow) {
          surfaceMin = '';
          surfaceMax = '';
          document.querySelectorAll('.js-ps-surface-min, .js-ps-surface-max').forEach(function (el) {
            el.value = '';
          });
          updateSurfaceLabel();
        }
        else {
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
          label.textContent = selectedOp === 'VEN' ? Drupal.t('Price') : Drupal.t('Rent');
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

      // ── Utilities ─────────────────────────────────────────────────────────
      function closeAllPopins() {
        document.querySelectorAll('.ps-type-popin:not([hidden])').forEach(function (p) {
          p.hidden = true;
        });
        document.querySelectorAll('.ps-filter-bar__btn[aria-expanded="true"]').forEach(function (b) {
          b.setAttribute('aria-expanded', 'false');
        });
      }

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
        // Extract tokens based on type for precise backend filtering
        const newTokens = data.map(function (item) {
          // For arrondissements, use postal_code for precise filtering (e.g., "75015")
          if (item.type === 'arrondissement' && item.postal_code) {
            return item.postal_code;
          }
          // For departments, use admin_area or the full name
          if (item.type === 'department') {
            return item.admin_area || item.label;
          }
          // For cities, use locality
          return item.locality || item.label;
        });
        
        selectedLocalityTokens = normalizeLocationTokens(selectedLocalityTokens.concat(newTokens));
        selectedLocalityData = selectedLocalityData.concat(data);
        
        // Deduplicate by token
        const uniqueData = [];
        const seenTokens = {};
        selectedLocalityData.forEach(function (item, idx) {
          if (idx >= selectedLocalityTokens.length) return;
          const token = selectedLocalityTokens[idx];
          const key = String(token).toLowerCase();
          if (!seenTokens[key]) {
            seenTokens[key] = true;
            uniqueData.push(item);
          }
        });
        selectedLocalityData = uniqueData.slice(0, 10);
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

      function removeLocationTokenAt(index) {
        if (index < 0 || index >= selectedLocalityTokens.length) {
          return;
        }
        selectedLocalityTokens.splice(index, 1);
        selectedLocalityData.splice(index, 1);
        refreshSelectedLocality();
      }

      function buildSeoBase() {
        if (!selectedOp) return langPrefix + '/recherche';
        const opSlug = opSlugs[selectedOp];
        if (!opSlug) return langPrefix + '/recherche';
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

        let localityIndex = 1;
        if (segments[1] && assetSlugValues.indexOf(String(segments[1]).toLowerCase()) !== -1) {
          localityIndex = 2;
        }

        if (!segments[localityIndex]) {
          return '';
        }

        return segments[localityIndex]
          .replace(/-/g, ' ')
          .trim()
          .split(/\s+/)
          .map(function (word) {
            return word.charAt(0).toUpperCase() + word.slice(1);
          })
          .join(' ');
      }

      function buildNavigationUrl() {
        let base = buildSeoBase();
        const p = new URLSearchParams();
        const primaryData = getPrimaryLocalityData();

        if (primaryData && primaryData.locality) {
          base = base.replace(/\/?$/, '/');
          const deptCode = primaryData.postal_code ? primaryData.postal_code.substring(0, 2) : '';
          const deptSlug = primaryData.admin_area ? toSeoSlug(primaryData.admin_area) : '';
          const localitySlug = toSeoSlug(primaryData.locality);
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

          if (selectedLocalityTokens.length > 1) {
            p.set('locality', selectedLocality);
          }
        } else if (selectedLocality) {
          p.set('locality', selectedLocality);
        }

        if (surfaceMin) p.set('surface[min]', surfaceMin);
        if (surfaceMax) p.set('surface[max]', surfaceMax);
        if (budgetMin) p.set('budget[min]', budgetMin);
        if (budgetMax) p.set('budget[max]', budgetMax);
        if (capacityMin) p.set('capacity[min]', capacityMin);
        if (capacityMax) p.set('capacity[max]', capacityMax);
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
        // Locality is passed to count endpoint for approximate filtering.
        if (selectedLocality) p.set('locality', selectedLocality);
        if (surfaceMin) p.set('surface_min', surfaceMin);
        if (surfaceMax) p.set('surface_max', surfaceMax);
        if (budgetMin) p.set('budget_min', budgetMin);
        if (budgetMax) p.set('budget_max', budgetMax);
        if (capacityMin) p.set('capacity_min', capacityMin);
        if (capacityMax) p.set('capacity_max', capacityMax);
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
      function updateTypeOpBtnLabel(wrapper) {
        const labelEl = wrapper.querySelector('.ps-filter-bar__btn-label');
        const mainBtn = wrapper.querySelector('.ps-filter-bar__btn');
        if (!labelEl) return;
        const assetCard = selectedAsset
          ? wrapper.querySelector('.js-ps-asset-btn[data-code="' + selectedAsset + '"]')
          : null;
        const opBtnEl = selectedOp
          ? wrapper.querySelector('.js-ps-op-btn[data-code="' + selectedOp + '"]')
          : null;
        const assetLabel = assetCard ? assetCard.querySelector('.ps-asset-card__label') : null;
        const opLabel = opBtnEl ? opBtnEl.textContent.trim() : null;
        if (assetLabel && opLabel) {
          labelEl.textContent = assetLabel.textContent.trim() + ' \u2014 ' + opLabel;
        } else if (assetLabel) {
          labelEl.textContent = assetLabel.textContent.trim();
        } else if (opLabel) {
          labelEl.textContent = opLabel;
        } else {
          labelEl.textContent = Drupal.t('Type & need');
        }
        if (mainBtn) mainBtn.classList.toggle('is-active', !!(selectedAsset || selectedOp));
      }

      once('ps-type-filter', '.js-ps-filter-dropdown', context).forEach(function (wrapper) {
        const mainBtn = wrapper.querySelector('.ps-filter-bar__btn');
        const popin = wrapper.querySelector('.ps-type-popin');
        if (!mainBtn || !popin) return;

        mainBtn.addEventListener('click', function () {
          const isOpen = mainBtn.getAttribute('aria-expanded') === 'true';
          if (!isOpen) {
            closeAllPopins();
            fetchCount();
          } else {
            updateTypeOpBtnLabel(wrapper);
          }
          mainBtn.setAttribute('aria-expanded', String(!isOpen));
          popin.hidden = isOpen;
        });

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

        // Op buttons.
        wrapper.querySelectorAll('.js-ps-op-btn').forEach(function (btn) {
          btn.addEventListener('click', function () {
            const code = btn.dataset.code;
            selectedOp = (selectedOp === code) ? null : code;
            wrapper.querySelectorAll('.js-ps-op-btn').forEach(function (b) {
              const active = b.dataset.code === selectedOp;
              b.classList.toggle('is-active', active);
              b.setAttribute('aria-pressed', String(active));
            });
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
            wrapper.querySelectorAll('.js-ps-asset-btn').forEach(function (a) {
              a.closest('.ps-asset-card').classList.remove('is-active');
              a.setAttribute('aria-pressed', 'false');
            });
            wrapper.querySelectorAll('.js-ps-op-btn').forEach(function (b) {
              b.classList.remove('is-active');
              b.setAttribute('aria-pressed', 'false');
            });
            scheduleCountUpdate();
          });
        }
      });

      // ── 2. Localisation input (chips + grouped autocomplete) ─────────────
      once('ps-locality', '.js-ps-locality-input', context).forEach(function (input) {
        const editor = input.closest('.js-ps-location-editor');
        const chipsContainer = editor ? editor.querySelector('.js-ps-location-chips') : null;
        const locationWrap = input.closest('.ps-filter-bar__location-wrap');
        const suggestBox = locationWrap ? locationWrap.querySelector('.js-ps-location-suggest') : null;
        let suggestionButtons = [];

        function updateLocationActiveState() {
          const filterItem = input.closest('.ps-filter-bar__item--location');
          if (filterItem) {
            filterItem.classList.toggle('is-active', !!(selectedLocalityTokens.length || input.value.trim()));
          }
        }

        function renderChips() {
          if (!chipsContainer) {
            return;
          }
          chipsContainer.innerHTML = '';
          selectedLocalityTokens.forEach(function (token, index) {
            const chip = document.createElement('span');
            chip.className = 'ps-location-chip';

            const label = document.createElement('span');
            label.className = 'ps-location-chip__label';
            // Use formatted label from data if available, fallback to token
            const itemData = selectedLocalityData[index];
            const displayLabel = (itemData && itemData.label) ? itemData.label : token;
            label.textContent = displayLabel;

            const clearBtn = document.createElement('button');
            clearBtn.type = 'button';
            clearBtn.className = 'ps-location-chip__clear';
            clearBtn.setAttribute('aria-label', Drupal.t('Remove @value', { '@value': displayLabel }));
            clearBtn.textContent = '×';
            clearBtn.addEventListener('click', function () {
              removeLocationTokenAt(index);
              renderChips();
              updateLocationActiveState();
              scheduleCountUpdate();
              input.focus();
            });

            chip.appendChild(label);
            chip.appendChild(clearBtn);
            chipsContainer.appendChild(chip);
          });
        }

        function hideSuggestions() {
          if (!suggestBox) return;
          suggestBox.hidden = true;
          suggestBox.innerHTML = '';
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
          renderChips();
          input.value = '';
          hideSuggestions();
          updateLocationActiveState();
          scheduleCountUpdate();
        }

        function commitDraftTokens() {
          const draft = input.value.trim();
          if (!draft) {
            return false;
          }
          addLocationTokens(parseLocationTokens(draft));
          renderChips();
          input.value = '';
          updateLocationActiveState();
          scheduleCountUpdate();
          return true;
        }

        function renderSuggestions(groups) {
          if (!suggestBox) return;

          suggestBox.innerHTML = '';
          suggestionButtons = [];
          
          // Render suggestion groups
          if (groups && groups.length) {
            groups.forEach(function (group) {
              const title = document.createElement('div');
              title.className = 'ps-location-suggest__group-title';
              title.textContent = group.label;
              suggestBox.appendChild(title);

              group.items.forEach(function (itemData) {
                const isStructured = typeof itemData === 'object' && itemData !== null;
                const label = isStructured ? itemData.label : String(itemData);
                const displayText = label;

                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'ps-location-suggest__item';
                btn.setAttribute('role', 'option');
                btn.id = 'ps-location-option-' + suggestionButtons.length;
                btn.textContent = displayText;
                btn.addEventListener('mousedown', function (e) {
                  e.preventDefault();
                });
                btn.addEventListener('click', function () {
                  const data = isStructured ? itemData : { label: label, type: 'city', locality: label, admin_area: '', postal_code: '' };
                  selectSuggestion(data);
                });
                suggestionButtons.push(btn);
                suggestBox.appendChild(btn);
              });
            });
          } else if (!input.value.trim()) {
            // Show hint when no input and no suggestions
            const hint = document.createElement('div');
            hint.className = 'ps-location-suggest__hint';
            hint.textContent = Drupal.t('Start typing a city name or postal code');
            suggestBox.appendChild(hint);
          }

          // Add "Show results" button at the bottom
          const actionBar = document.createElement('div');
          actionBar.className = 'ps-location-suggest__actions';
          
          const showBtn = document.createElement('button');
          showBtn.type = 'button';
          showBtn.className = 'ps-location-suggest__show-btn';
          showBtn.addEventListener('mousedown', function (e) {
            e.preventDefault();
          });
          showBtn.addEventListener('click', function () {
            commitDraftTokens();
            hideSuggestions();
            window.location.href = buildNavigationUrl();
          });
          
          // Button text with count
          const countSpan = document.createElement('span');
          countSpan.className = 'ps-location-suggest__count';
          countSpan.textContent = currentCount !== null ? String(currentCount) : '…';
          showBtn.appendChild(document.createTextNode(Drupal.t('Show ')));
          showBtn.appendChild(countSpan);
          showBtn.appendChild(document.createTextNode(' ' + Drupal.t('results')));
          
          actionBar.appendChild(showBtn);
          suggestBox.appendChild(actionBar);

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
          
          // Show box even without input (on focus)
          if (partialToken.length === 0) {
            renderSuggestions([]);
            return;
          }
          
          // Require at least 2 characters for actual search
          if (partialToken.length < 2) {
            return;
          }

          fetch(locationSuggestUrl + '?q=' + encodeURIComponent(partialToken), {
            headers: { Accept: 'application/json' },
            credentials: 'same-origin',
          })
            .then(function (r) { return r.ok ? r.json() : Promise.reject(); })
            .then(function (data) {
              const selected = {};
              selectedLocalityTokens.forEach(function (token) {
                selected[token.toLowerCase()] = true;
              });

              const groupsRaw = Array.isArray(data.groups) ? data.groups : [];
              const groups = groupsRaw
                .map(function (group) {
                  const label = String(group.label || '').trim();
                  const items = Array.isArray(group.items) ? group.items : [];
                  const filteredItems = items.filter(function (item) {
                    const key = String(item).toLowerCase();
                    return key && !selected[key];
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

        syncSelectedLocality(currentParams.get('locality') || extractLocalityFromPath() || '');
        renderChips();
        input.value = '';
        updateLocationActiveState();

        // Enrich locality data from backend if missing (page reload/history).
        if (selectedLocalityTokens.length > 0 && selectedLocalityData.length > 0) {
          const needsEnrichment = selectedLocalityData.some(function (item) {
            return !item.postal_code || !item.admin_area;
          });
          if (needsEnrichment) {
            enrichLocalityData();
          }
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
              }
            })
            .catch(function () { /* Silent fail, keep minimal data */ });
        }

        input.addEventListener('input', function () {
          updateLocationActiveState();
          const token = input.value.trim();
          clearTimeout(locationSuggestDebounce);
          locationSuggestDebounce = setTimeout(function () {
            fetchLocationSuggestions(token);
          }, 180);
        });

        input.addEventListener('focus', function () {
          const token = input.value.trim();
          // Always show suggest box on focus (with button)
          fetchLocationSuggestions(token);
        });

        input.addEventListener('blur', function () {
          setTimeout(function () {
            hideSuggestions();
            commitDraftTokens();
          }, 200);
        });

        input.addEventListener('keydown', function (e) {
          if (e.key === 'ArrowDown') {
            if (!suggestBox.hidden) {
              e.preventDefault();
              moveSuggestion(1);
            }
            return;
          }
          if (e.key === 'ArrowUp') {
            if (!suggestBox.hidden) {
              e.preventDefault();
              moveSuggestion(-1);
            }
            return;
          }
          if (e.key === 'Enter') {
            if (!suggestBox.hidden && activeSuggestionIndex >= 0 && suggestionButtons[activeSuggestionIndex]) {
              e.preventDefault();
              suggestionButtons[activeSuggestionIndex].click();
              return;
            }
            if (commitDraftTokens()) {
              e.preventDefault();
              hideSuggestions();
              return;
            }
            e.preventDefault();
            hideSuggestions();
            navigate();
            return;
          }
          if (e.key === ',' || e.key === ';') {
            e.preventDefault();
            commitDraftTokens();
            hideSuggestions();
            return;
          }
          if (e.key === 'Backspace' && input.value === '' && selectedLocalityTokens.length) {
            removeLocationTokenAt(selectedLocalityTokens.length - 1);
            renderChips();
            updateLocationActiveState();
            scheduleCountUpdate();
            return;
          }
          if (e.key === 'Escape') {
            hideSuggestions();
          }
        });
      });

      // ── 3. Secondary filter toggle buttons (surface, budget) ──────────────
      once('ps-filter-toggle', '.js-ps-filter-toggle', context).forEach(function (toggleBtn) {
        const popinId = toggleBtn.getAttribute('aria-controls');
        const popin = popinId ? document.getElementById(popinId) : null;
        if (!popin) return;

        toggleBtn.addEventListener('click', function () {
          const isOpen = toggleBtn.getAttribute('aria-expanded') === 'true';
          if (!isOpen) {
            closeAllPopins();
            fetchCount();
          }
          toggleBtn.setAttribute('aria-expanded', String(!isOpen));
          popin.hidden = isOpen;
        });
      });

      // ── 4. Surface inputs ─────────────────────────────────────────────────
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
        else lbl.textContent = Drupal.t('Budget');
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
        if (capacityMin && capacityMax) lbl.textContent = capacityMin + '\u2013' + capacityMax;
        else if (capacityMin) lbl.textContent = '\u2265 ' + capacityMin;
        else if (capacityMax) lbl.textContent = '\u2264 ' + capacityMax;
        else lbl.textContent = Drupal.t('Capacity');
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

      // ── 7. Close on outside click / Escape (bound once globally) ─────────
      once('ps-filter-global-events', 'html', document).forEach(function () {
        document.addEventListener('click', function (e) {
          const filterBar = document.querySelector('.ps-filter-bar');
          if (filterBar && !filterBar.contains(e.target)) {
            // Update type+op label before closing.
            const typeWrapper = document.querySelector('.js-ps-filter-dropdown');
            if (typeWrapper) {
              const mainBtn = typeWrapper.querySelector('.ps-filter-bar__btn');
              if (mainBtn && mainBtn.getAttribute('aria-expanded') === 'true') {
                updateTypeOpBtnLabel(typeWrapper);
              }
            }
            closeAllPopins();
          }
        });

        document.addEventListener('keydown', function (e) {
          if (e.key === 'Escape') {
            const typeWrapper = document.querySelector('.js-ps-filter-dropdown');
            if (typeWrapper) {
              const mainBtn = typeWrapper.querySelector('.ps-filter-bar__btn');
              if (mainBtn && mainBtn.getAttribute('aria-expanded') === 'true') {
                updateTypeOpBtnLabel(typeWrapper);
                mainBtn.focus();
              }
            }
            closeAllPopins();
          }
        });
      });
    },
  };

}(Drupal, drupalSettings, once));

