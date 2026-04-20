(function (Drupal, drupalSettings) {
  'use strict';

  const LOCATION_MULTI_DELIMITER = '||';

  function normalizeLocationTerm(value) {
    return String(value || '').replace(/\s+/g, ' ').trim();
  }

  function parseLocationTermsFromUrl(nativeInput) {
    const params = new URLSearchParams(window.location.search);
    const rawMulti = params.get('location_multi') || '';
    const multiTerms = rawMulti
      .split(LOCATION_MULTI_DELIMITER)
      .map((value) => normalizeLocationTerm(value))
      .filter((value) => value !== '');

    if (multiTerms.length > 0) {
      return Array.from(new Set(multiTerms));
    }

    const fallback = normalizeLocationTerm(nativeInput.value || '');
    return fallback ? [fallback] : [];
  }

  /**
   * Initializes location multi-value autocomplete in the search panel.
   *
   * @param {{
   *   panel: HTMLElement,
   *   form: HTMLElement,
   *   requestResultsCountPreview: function(HTMLElement, number=): void,
   *   updatePanelTriggerLabel: function(HTMLElement): void
   * }} config
   *   Runtime wiring from the main search behavior.
   */
  function init(config) {
    const panel = config && config.panel;
    const form = config && config.form;
    const requestResultsCountPreview = config && config.requestResultsCountPreview;
    const updatePanelTriggerLabel = config && config.updatePanelTriggerLabel;

    if (!panel || !form || typeof requestResultsCountPreview !== 'function' || typeof updatePanelTriggerLabel !== 'function') {
      return;
    }

    if (panel.__psLocationAutocompleteBound) {
      return;
    }
    panel.__psLocationAutocompleteBound = true;

    const nativeInput = panel.querySelector('[data-ps-filter="location"]');
    const panelContent = panel.querySelector(':scope > .ps-filter-panel__content');
    const footer = panel.querySelector(':scope > .ps-filter-panel__content > .ps-filter-panel__footer');
    const endpoint = drupalSettings.psSearch?.locationAutocompleteEndpoint || '';

    if (!nativeInput || !panelContent || !footer) {
      return;
    }

    const formItem = nativeInput.closest('.form-item') || nativeInput.parentElement;
    if (!formItem) {
      return;
    }

    const originalName = nativeInput.getAttribute('name') || 'location';

    let locationHidden = form.querySelector('input[data-ps-location-hidden="1"]');
    if (!locationHidden) {
      locationHidden = document.createElement('input');
      locationHidden.type = 'hidden';
      locationHidden.name = originalName;
      locationHidden.setAttribute('data-ps-location-hidden', '1');
      form.appendChild(locationHidden);
    }

    let locationMulti = form.querySelector('input[data-ps-location-multi="1"]');
    if (!locationMulti) {
      locationMulti = document.createElement('input');
      locationMulti.type = 'hidden';
      locationMulti.name = 'location_multi';
      locationMulti.setAttribute('data-ps-location-multi', '1');
      form.appendChild(locationMulti);
    }

    nativeInput.setAttribute('name', `${originalName}_search`);
    nativeInput.setAttribute('autocomplete', 'off');
    nativeInput.setAttribute('placeholder', Drupal.t('City, district or zip code'));
    nativeInput.setAttribute('data-ps-location-search', '1');

    formItem.classList.add('ps-location-autocomplete');

    const state = {
      terms: parseLocationTermsFromUrl(nativeInput),
      activeIndex: -1,
      suggestions: [],
    };

    let inputWrap = formItem.querySelector('.ps-location-autocomplete__input-wrap');
    if (!inputWrap) {
      inputWrap = document.createElement('div');
      inputWrap.className = 'ps-location-autocomplete__input-wrap';
      formItem.appendChild(inputWrap);
      inputWrap.appendChild(nativeInput);
    }

    let clearButton = inputWrap.querySelector('.ps-location-autocomplete__clear');
    if (!clearButton) {
      clearButton = document.createElement('button');
      clearButton.type = 'button';
      clearButton.className = 'ps-location-autocomplete__clear';
      clearButton.setAttribute('aria-label', Drupal.t('Clear location input'));
      clearButton.innerHTML = '&times;';
      inputWrap.appendChild(clearButton);
    }

    let selectedBlock = panelContent.querySelector('.ps-location-autocomplete__selected');
    if (!selectedBlock) {
      selectedBlock = document.createElement('div');
      selectedBlock.className = 'ps-location-autocomplete__selected';
      selectedBlock.innerHTML = '<div class="ps-location-autocomplete__selected-title">' + Drupal.t('Location :') + '</div><div class="ps-location-autocomplete__chips"></div>';
      panelContent.insertBefore(selectedBlock, footer);
    }

    let suggestions = panelContent.querySelector('.ps-location-autocomplete__suggestions');
    if (!suggestions) {
      suggestions = document.createElement('div');
      suggestions.className = 'ps-location-autocomplete__suggestions';
      suggestions.hidden = true;
      panelContent.insertBefore(suggestions, footer);
    }

    const chips = selectedBlock.querySelector('.ps-location-autocomplete__chips');

    function setTriggerChips() {
      const valueNode = panel.querySelector(':scope > .ps-filter-panel__trigger .ps-filter-panel__value');
      if (!valueNode) {
        return;
      }

      if (!valueNode.dataset.defaultValue) {
        valueNode.dataset.defaultValue = (valueNode.textContent || '').trim();
      }

      valueNode.innerHTML = '';
      if (state.terms.length === 0) {
        valueNode.textContent = valueNode.dataset.defaultValue || '';
        return;
      }

      const list = document.createElement('span');
      list.className = 'ps-location-trigger-chips';

      state.terms.forEach((term, index) => {
        const chip = document.createElement('span');
        chip.className = 'ps-location-trigger-chip';

        const label = document.createElement('span');
        label.className = 'ps-location-trigger-chip__label';
        label.textContent = term;

        const remove = document.createElement('button');
        remove.type = 'button';
        remove.className = 'ps-location-trigger-chip__remove';
        remove.setAttribute('aria-label', Drupal.t('Remove @value', { '@value': term }));
        remove.innerHTML = '&times;';
        remove.addEventListener('click', (event) => {
          event.preventDefault();
          event.stopPropagation();
          removeTermAt(index);
        });

        chip.appendChild(label);
        chip.appendChild(remove);
        list.appendChild(chip);
      });

      valueNode.appendChild(list);
    }

    function closeSuggestions() {
      state.activeIndex = -1;
      state.suggestions = [];
      suggestions.hidden = true;
      suggestions.innerHTML = '';
    }

    function renderSelectedTerms() {
      if (!chips) {
        return;
      }

      chips.innerHTML = '';

      selectedBlock.hidden = state.terms.length === 0;

      state.terms.forEach((term, index) => {
        const chip = document.createElement('button');
        chip.type = 'button';
        chip.className = 'ps-location-chip';
        chip.innerHTML = '<span class="ps-location-chip__label"></span><span class="ps-location-chip__remove" aria-hidden="true">&times;</span>';
        const labelNode = chip.querySelector('.ps-location-chip__label');
        if (labelNode) {
          labelNode.textContent = term;
        }
        chip.setAttribute('aria-label', Drupal.t('Remove @value', { '@value': term }));
        chip.addEventListener('click', () => removeTermAt(index));
        chips.appendChild(chip);
      });
    }

    function syncLocationFields() {
      panel.__psLocationTerms = [...state.terms];
      locationHidden.value = '';
      locationMulti.value = state.terms.join(LOCATION_MULTI_DELIMITER);
      clearButton.hidden = normalizeLocationTerm(nativeInput.value) === '';
      setTriggerChips();
      renderSelectedTerms();
      updatePanelTriggerLabel(panel);
    }

    function addTerm(value) {
      const normalized = normalizeLocationTerm(value);
      if (!normalized) {
        return;
      }

      const exists = state.terms.some((term) => term.toLowerCase() === normalized.toLowerCase());
      if (exists) {
        nativeInput.value = '';
        closeSuggestions();
        syncLocationFields();
        return;
      }

      state.terms.push(normalized);
      nativeInput.value = '';
      closeSuggestions();
      syncLocationFields();
      requestResultsCountPreview(form, 120);
    }

    function removeTermAt(index) {
      if (index < 0 || index >= state.terms.length) {
        return;
      }

      state.terms.splice(index, 1);
      closeSuggestions();
      syncLocationFields();
      requestResultsCountPreview(form, 120);
    }

    function renderSuggestions(items) {
      state.suggestions = items;
      state.activeIndex = items.length > 0 ? 0 : -1;
      suggestions.innerHTML = '';

      if (items.length === 0) {
        suggestions.hidden = true;
        return;
      }

      const list = document.createElement('ul');
      list.className = 'ps-location-suggestion-list';

      items.forEach((item, index) => {
        const value = normalizeLocationTerm(item.value || item.label || '');
        if (!value) {
          return;
        }

        const row = document.createElement('li');
        row.className = 'ps-location-suggestion-list__item';
        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'ps-location-suggestion';
        button.textContent = value;
        button.classList.toggle('is-active', index === state.activeIndex);
        button.addEventListener('click', () => addTerm(value));
        row.appendChild(button);
        list.appendChild(row);
      });

      suggestions.appendChild(list);
      suggestions.hidden = false;
    }

    function highlightActiveSuggestion() {
      suggestions.querySelectorAll('.ps-location-suggestion').forEach((button, index) => {
        button.classList.toggle('is-active', index === state.activeIndex);
      });
    }

    function fetchSuggestions() {
      const term = normalizeLocationTerm(nativeInput.value);
      if (!endpoint || term.length < 2) {
        closeSuggestions();
        return;
      }

      const requestId = (form.__psLocationAutocompleteRequestId || 0) + 1;
      form.__psLocationAutocompleteRequestId = requestId;

      fetch(`${endpoint}?q=${encodeURIComponent(term)}&limit=8`, {
        method: 'GET',
        credentials: 'same-origin',
        headers: {
          Accept: 'application/json',
        },
      })
        .then((response) => {
          if (!response.ok) {
            throw new Error('Location autocomplete failed');
          }
          return response.json();
        })
        .then((payload) => {
          if (requestId !== form.__psLocationAutocompleteRequestId) {
            return;
          }

          const items = Array.isArray(payload?.items) ? payload.items : [];
          renderSuggestions(items);
        })
        .catch(() => {
          if (requestId !== form.__psLocationAutocompleteRequestId) {
            return;
          }
          closeSuggestions();
        });
    }

    clearButton.addEventListener('click', () => {
      nativeInput.value = '';
      clearButton.hidden = true;
      closeSuggestions();
      nativeInput.focus();
    });

    nativeInput.addEventListener('input', () => {
      clearButton.hidden = normalizeLocationTerm(nativeInput.value) === '';

      if (form.__psLocationAutocompleteTimer) {
        window.clearTimeout(form.__psLocationAutocompleteTimer);
      }

      form.__psLocationAutocompleteTimer = window.setTimeout(fetchSuggestions, 140);
    });

    nativeInput.addEventListener('keydown', (event) => {
      if (event.key === 'ArrowDown') {
        if (state.suggestions.length === 0) {
          return;
        }
        event.preventDefault();
        state.activeIndex = Math.min(state.activeIndex + 1, state.suggestions.length - 1);
        highlightActiveSuggestion();
        return;
      }

      if (event.key === 'ArrowUp') {
        if (state.suggestions.length === 0) {
          return;
        }
        event.preventDefault();
        state.activeIndex = Math.max(state.activeIndex - 1, 0);
        highlightActiveSuggestion();
        return;
      }

      if (event.key === 'Escape') {
        closeSuggestions();
        return;
      }

      if (event.key !== 'Enter') {
        return;
      }

      event.preventDefault();

      if (state.suggestions.length > 0 && state.activeIndex >= 0) {
        const active = state.suggestions[state.activeIndex];
        addTerm(active.value || active.label || '');
        return;
      }

      const typed = normalizeLocationTerm(nativeInput.value);
      if (typed.length >= 2) {
        addTerm(typed);
      }
    });

    document.addEventListener('click', (event) => {
      const path = (typeof event.composedPath === 'function') ? event.composedPath() : [];
      const clickedInsidePanel = panel.contains(event.target) || path.indexOf(panel) !== -1;
      if (!clickedInsidePanel) {
        closeSuggestions();
      }
    });

    syncLocationFields();
  }

  window.psSearchUi = window.psSearchUi || {};
  window.psSearchUi.location = {
    init,
  };
})(Drupal, drupalSettings);
