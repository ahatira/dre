/**
 * @file
 * Autocomplete for the Nearby transport More-filters field.
 */
(function (Drupal, once) {
  'use strict';

  function appendContentLangParam(params) {
    if (Drupal.psSearchPage && typeof Drupal.psSearchPage.appendContentLangParam === 'function') {
      return Drupal.psSearchPage.appendContentLangParam(params);
    }
    return params;
  }

  function highlightMatch(label, query) {
    const needle = String(query || '').trim();
    if (!needle) {
      return label;
    }
    const lowerLabel = label.toLowerCase();
    const lowerNeedle = needle.toLowerCase();
    const index = lowerLabel.indexOf(lowerNeedle);
    if (index === -1) {
      return label;
    }
    const before = document.createElement('span');
    before.className = 'ps-location-suggest__text';
    before.textContent = label.slice(0, index);
    const match = document.createElement('strong');
    match.className = 'ps-location-suggest__match';
    match.textContent = label.slice(index, index + needle.length);
    const after = document.createElement('span');
    after.className = 'ps-location-suggest__text';
    after.textContent = label.slice(index + needle.length);
    const wrap = document.createElement('span');
    wrap.append(before, match, after);
    return wrap;
  }

  function attachTransportSuggest(input) {
    const settings = (drupalSettings.psSearch || {});
    const suggestUrl = input.dataset.suggestUrl || settings.transportSuggestUrl || '/api/ps/transport-suggest';
    const list = input.parentElement ? input.parentElement.querySelector('.js-ps-transport-suggest-list') : null;
    if (!list) {
      return;
    }

    let activeIndex = -1;
    let debounceTimer = null;
    let flatItems = [];

    function hideList() {
      list.hidden = true;
      list.innerHTML = '';
      activeIndex = -1;
      flatItems = [];
      input.setAttribute('aria-expanded', 'false');
    }

    function selectItem(item) {
      const value = String(item.value || item.label || '').trim();
      if (!value) {
        return;
      }
      input.value = value;
      hideList();
      input.dispatchEvent(new Event('input', { bubbles: true }));
      input.dispatchEvent(new Event('change', { bubbles: true }));
      input.focus();
    }

    function renderGroups(groups) {
      list.innerHTML = '';
      flatItems = [];
      activeIndex = -1;

      if (!groups.length) {
        hideList();
        return;
      }

      groups.forEach(function (group) {
        const title = document.createElement('div');
        title.className = 'ps-location-suggest__group-title';
        title.textContent = String(group.label || '');
        list.appendChild(title);

        const items = Array.isArray(group.items) ? group.items : [];
        items.forEach(function (item) {
          flatItems.push(item);
          const btn = document.createElement('button');
          btn.type = 'button';
          btn.className = 'ps-location-suggest__item';
          btn.setAttribute('role', 'option');
          const labelWrap = document.createElement('span');
          labelWrap.className = 'ps-location-suggest__label';
          labelWrap.appendChild(highlightMatch(String(item.label || ''), input.value));
          btn.appendChild(labelWrap);
          btn.addEventListener('mousedown', function (event) {
            event.preventDefault();
            selectItem(item);
          });
          list.appendChild(btn);
        });
      });

      list.hidden = false;
      input.setAttribute('aria-expanded', 'true');
    }

    function fetchSuggestions(query) {
      if (query.length < 2) {
        hideList();
        return;
      }

      const params = appendContentLangParam(new URLSearchParams({ q: query }));
      fetch(suggestUrl + '?' + params.toString(), {
        headers: { Accept: 'application/json' },
        credentials: 'same-origin',
      })
        .then(function (response) {
          return response.ok ? response.json() : Promise.reject();
        })
        .then(function (data) {
          const groups = Array.isArray(data.groups) ? data.groups : [];
          renderGroups(groups);
        })
        .catch(function () {
          hideList();
        });
    }

    function moveActive(direction) {
      const options = list.querySelectorAll('.ps-location-suggest__item');
      if (!options.length) {
        return;
      }
      activeIndex += direction;
      if (activeIndex < 0) {
        activeIndex = options.length - 1;
      }
      if (activeIndex >= options.length) {
        activeIndex = 0;
      }
      options.forEach(function (option, index) {
        option.classList.toggle('is-active', index === activeIndex);
      });
      options[activeIndex].scrollIntoView({ block: 'nearest' });
    }

    input.addEventListener('input', function () {
      clearTimeout(debounceTimer);
      const query = String(input.value || '').trim();
      debounceTimer = setTimeout(function () {
        fetchSuggestions(query);
      }, 200);
    });

    input.addEventListener('keydown', function (event) {
      if (event.key === 'ArrowDown') {
        event.preventDefault();
        moveActive(1);
        return;
      }
      if (event.key === 'ArrowUp') {
        event.preventDefault();
        moveActive(-1);
        return;
      }
      if (event.key === 'Escape') {
        hideList();
        return;
      }
      if (event.key === 'Enter' && !list.hidden && activeIndex >= 0) {
        const item = flatItems[activeIndex];
        if (item) {
          event.preventDefault();
          selectItem(item);
        }
      }
    });

    input.addEventListener('blur', function () {
      setTimeout(hideList, 150);
    });
  }

  Drupal.behaviors.psSearchTransportSuggest = {
    attach(context) {
      once('ps-transport-suggest', '.js-ps-transport-suggest', context).forEach(attachTransportSuggest);
    },
  };
})(Drupal, once);
