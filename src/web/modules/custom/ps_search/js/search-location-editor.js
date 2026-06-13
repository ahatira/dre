/**
 * @file
 * Shared location editor — chips, suggest, keyboard (filter bar + homepage hero).
 */
(function (Drupal) {
  'use strict';

  function normalizeLocationTokens(tokens) {
    const deduped = [];
    const seen = {};
    tokens.forEach(function (token) {
      const cleaned = token.trim();
      if (!cleaned) {
        return;
      }
      const key = cleaned.toLowerCase();
      if (seen[key]) {
        return;
      }
      seen[key] = true;
      deduped.push(cleaned);
    });
    return deduped.slice(0, 10);
  }

  function parseLocationTokens(value) {
    return normalizeLocationTokens(String(value || '').split(/[,;]+/));
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

    const endIndex = index + needle.length;

    if (index > 0) {
      const before = document.createElement('span');
      before.className = 'ps-location-suggest__text';
      before.textContent = label.slice(0, index);
      fragment.appendChild(before);
    }

    const strong = document.createElement('strong');
    strong.className = 'ps-location-suggest__match';
    strong.textContent = label.slice(index, endIndex);
    fragment.appendChild(strong);

    if (endIndex < label.length) {
      const after = document.createElement('span');
      after.className = 'ps-location-suggest__text';
      after.textContent = label.slice(endIndex);
      fragment.appendChild(after);
    }

    return fragment;
  }

  function compareSuggestionLabels(a, b) {
    const labelA = typeof a === 'object' && a !== null ? String(a.label || '') : String(a || '');
    const labelB = typeof b === 'object' && b !== null ? String(b.label || '') : String(b || '');
    return labelA.localeCompare(labelB, undefined, { sensitivity: 'base' });
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

  /**
   * @param {object} config
   * @return {object|null}
   */
  function attachLocationEditor(config) {
    const input = config.input;
    if (!input) {
      return null;
    }

    const mode = config.mode || 'dropdown';
    const isDropdown = mode === 'dropdown';
    const rootEl = config.rootEl || input.closest('.ps-filter-bar__item--location');
    const editorEl = input.closest('.js-ps-location-editor');
    const chipsContainer = editorEl ? editorEl.querySelector('.js-ps-location-chips') : null;
    const popinChipsContainer = rootEl ? rootEl.querySelector('.js-ps-location-popin-chips') : null;
    const selectedPanel = rootEl ? rootEl.querySelector('.js-ps-location-selected-panel') : null;
    const suggestBox = rootEl ? rootEl.querySelector('.js-ps-location-suggest') : null;
    const clearInputBtn = rootEl ? rootEl.querySelector('.js-ps-location-clear-input') : null;
    const locationToggle = rootEl ? rootEl.querySelector('.ps-filter-bar__toggle--location') : null;
    const locationSuggestUrl = config.locationSuggestUrl || '/api/ps/location-suggest';
    const locationDataUrl = config.locationDataUrl || '/api/ps/location-data';
    const appendContentLangParam = typeof config.appendContentLangParam === 'function'
      ? config.appendContentLangParam
      : function (params) { return params; };
    const onChange = typeof config.onChange === 'function' ? config.onChange : function () {};
    const onEnter = typeof config.onEnter === 'function' ? config.onEnter : null;
    const closeOtherPanels = typeof config.closeOtherPanels === 'function' ? config.closeOtherPanels : function () {};
    const openDropdown = typeof config.openDropdown === 'function' ? config.openDropdown : function () {};
    const closeDropdown = typeof config.closeDropdown === 'function' ? config.closeDropdown : function () {};

    const state = config.state || {
      tokens: [],
      data: [],
    };

    let suggestionButtons = [];
    let activeSuggestionIndex = -1;
    let suggestDebounce = null;
    let optionCounter = 0;

    function getTokens() {
      return state.tokens.slice();
    }

    function getData() {
      return state.data.slice();
    }

    function refreshDataFromTokens() {
      state.data = state.tokens.map(function (token) {
        return {
          label: token,
          type: 'city',
          locality: token,
          admin_area: '',
          postal_code: '',
        };
      });
    }

    function syncFromValue(value) {
      state.tokens = parseLocationTokens(value);
      refreshDataFromTokens();
    }

    function notifyChange() {
      onChange(getTokens(), getData());
    }

    function removeLocationTokenAt(index) {
      if (index < 0 || index >= state.tokens.length) {
        return;
      }
      state.tokens.splice(index, 1);
      state.data.splice(index, 1);
      notifyChange();
    }

    function addLocationTokens(tokens) {
      state.tokens = normalizeLocationTokens(state.tokens.concat(tokens));
      const dataByToken = {};
      state.data.forEach(function (item, idx) {
        const token = state.tokens[idx];
        if (token) {
          dataByToken[String(token).toLowerCase()] = item;
        }
      });
      state.data = state.tokens.map(function (token) {
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
      state.tokens = state.tokens.slice(0, 10);
      notifyChange();
    }

    function showMaxLocalitiesWarning() {
      if (!rootEl) {
        return;
      }
      const existing = rootEl.querySelector('.ps-location-max-warning');
      if (existing) {
        return;
      }
      const warning = document.createElement('div');
      warning.className = 'ps-location-max-warning';
      warning.textContent = Drupal.t('Maximum 10 locations');
      rootEl.appendChild(warning);
      setTimeout(function () {
        if (warning.parentNode) {
          warning.parentNode.removeChild(warning);
        }
      }, 3000);
    }

    function addLocationData(data) {
      if (state.tokens.length >= 10) {
        showMaxLocalitiesWarning();
        return;
      }
      const newTokens = data.map(function (item) {
        return getLocationItemToken(item);
      });

      state.tokens = normalizeLocationTokens(state.tokens.concat(newTokens));

      const dataByToken = {};
      state.data.forEach(function (item, idx) {
        const token = state.tokens[idx];
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
      state.data = state.tokens.map(function (token) {
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
      state.tokens = state.tokens.slice(0, 10);
      notifyChange();
    }

    function buildSelectedTokenMap() {
      const selected = {};
      state.tokens.forEach(function (token) {
        selected[String(token).toLowerCase()] = true;
      });
      return selected;
    }

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
      state.tokens.forEach(function (token, index) {
        const itemData = state.data[index];
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
        selectedPanel.hidden = state.tokens.length === 0;
      }
    }

    function updateClearInputButton() {
      if (!clearInputBtn) {
        return;
      }
      clearInputBtn.hidden = !input.value.trim();
    }

    function updateLocationActiveState() {
      if (rootEl) {
        rootEl.classList.toggle('has-value', !!(state.tokens.length || input.value.trim()));
      }
    }

    function isFocusInsideLocation() {
      if (!rootEl) {
        return false;
      }
      const active = document.activeElement;
      return active instanceof Node && rootEl.contains(active);
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

          const items = Array.isArray(group.items) ? group.items.slice() : [];
          items.sort(compareSuggestionLabels);

          items.forEach(function (itemData) {
            const isStructured = typeof itemData === 'object' && itemData !== null;
            const label = isStructured ? itemData.label : String(itemData);

            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'ps-location-suggest__item';
            btn.setAttribute('role', 'option');
            btn.id = 'ps-location-option-' + (optionCounter++);

            const labelWrap = document.createElement('span');
            labelWrap.className = 'ps-location-suggest__label';
            labelWrap.appendChild(highlightSuggestionLabel(label, input.value.trim()));
            btn.appendChild(labelWrap);
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

      const suggestParams = appendContentLangParam(new URLSearchParams({
        q: partialToken,
      }));
      fetch(locationSuggestUrl + '?' + suggestParams.toString(), {
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
      if (!state.tokens.length) {
        return;
      }
      const params = new URLSearchParams();
      state.tokens.forEach(function (token) {
        params.append('localities[]', token);
      });
      appendContentLangParam(params);
      fetch(locationDataUrl + '?' + params.toString(), {
        headers: { Accept: 'application/json' },
        credentials: 'same-origin',
      })
        .then(function (r) { return r.ok ? r.json() : Promise.reject(); })
        .then(function (response) {
          if (Array.isArray(response.data) && response.data.length > 0) {
            state.data = response.data;
            renderAllChips();
            updateSelectedPanelVisibility();
          }
        })
        .catch(function () { /* Keep minimal chip labels on failure. */ });
    }

    function maybeOpenDropdown() {
      if (isDropdown) {
        openDropdown(rootEl);
      }
    }

    if (config.initialValue) {
      syncFromValue(config.initialValue);
    }
    renderAllChips();
    updateSelectedPanelVisibility();
    input.value = '';
    updateClearInputButton();
    updateLocationActiveState();

    if (state.tokens.length > 0) {
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
        maybeOpenDropdown();
      });
    }

    const locationPopin = rootEl ? rootEl.querySelector('.ps-filter-popin--location') : null;
    if (locationPopin) {
      locationPopin.addEventListener('mousedown', function (e) {
        if (e.target !== input) {
          e.preventDefault();
        }
      });
    }

    if (locationToggle && isDropdown) {
      locationToggle.addEventListener('click', function (e) {
        if (e.target === input || e.target === clearInputBtn || input.contains(e.target)) {
          return;
        }
        e.preventDefault();
        closeOtherPanels(rootEl);
        input.focus();
        openDropdown(rootEl);
      });
    }

    input.addEventListener('mousedown', function (e) {
      if (isDropdown) {
        e.stopPropagation();
      }
    });

    input.addEventListener('click', function (e) {
      if (!isDropdown) {
        return;
      }
      e.stopPropagation();
      closeOtherPanels(rootEl);
      openDropdown(rootEl);
    });

    input.addEventListener('input', function () {
      updateClearInputButton();
      updateLocationActiveState();
      if (isDropdown) {
        closeOtherPanels(rootEl);
        openDropdown(rootEl);
      }
      const token = input.value.trim();
      clearTimeout(suggestDebounce);
      suggestDebounce = setTimeout(function () {
        fetchLocationSuggestions(token);
      }, 180);
    });

    input.addEventListener('focus', function () {
      if (isDropdown) {
        closeOtherPanels(rootEl);
        openDropdown(rootEl);
      }
      fetchLocationSuggestions(input.value.trim());
    });

    input.addEventListener('blur', function () {
      setTimeout(function () {
        if (isFocusInsideLocation()) {
          return;
        }
        hideSuggestions();
        commitDraftTokens();
        if (isDropdown) {
          closeDropdown(rootEl);
        }
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
        if (onEnter) {
          e.preventDefault();
          onEnter();
        }
        return;
      }
      if (e.key === ',' || e.key === ';') {
        e.preventDefault();
        commitDraftTokens();
        fetchLocationSuggestions('');
        return;
      }
      if (e.key === 'Backspace' && input.value === '' && state.tokens.length) {
        removeLocationTokenAt(state.tokens.length - 1);
        renderAllChips();
        updateSelectedPanelVisibility();
        updateLocationActiveState();
        return;
      }
      if (e.key === 'Escape') {
        hideSuggestions();
        if (isDropdown) {
          closeDropdown(rootEl);
        }
        input.blur();
      }
    });

    return {
      commitDraft: commitDraftTokens,
      getTokens: getTokens,
      getData: getData,
      syncFromValue: syncFromValue,
      renderChips: renderAllChips,
      enrichLocalityData: enrichLocalityData,
    };
  }

  Drupal.psSearchLocationEditor = {
    attach: attachLocationEditor,
    parseLocationTokens: parseLocationTokens,
    normalizeLocationTokens: normalizeLocationTokens,
  };
})(Drupal);
