/**
 * @file
 * Homepage hero search — transaction toggles, location chips, GET submit.
 */
(function (Drupal, drupalSettings, once) {
  'use strict';

  const parseLocationTokens = (value) => {
    return value
      .split(/[,;]+/)
      .map((token) => token.trim())
      .filter(Boolean);
  };

  Drupal.behaviors.psHomepageSearch = {
    attach(context) {
      const settings = drupalSettings.psSearch || {};
      const suggestUrl = settings.locationSuggestUrl || '/api/ps/location-suggest';

      once('ps-homepage-search-form', '.ps-homepage-search-form', context).forEach((form) => {
        const root = form.closest('[data-ps-homepage-search-entry]') || form.parentElement;
        const opInput = form.querySelector('.js-ps-homepage-operation');
        const assetSelect = form.querySelector('.js-ps-homepage-asset-select');
        const localityInput = form.querySelector('.js-ps-locality-input');
        const chipsContainer = form.querySelector('.js-ps-location-chips');
        const hiddenLocality = root ? root.querySelector('.js-ps-homepage-locality-hidden') : null;
        const suggestBox = root ? root.querySelector('.js-ps-location-suggest') : null;
        let selectedTokens = [];
        let debounceTimer = null;

        const setActiveOpButton = (button) => {
          form.querySelectorAll('.js-ps-op-btn').forEach((btn) => {
            const active = btn === button;
            btn.classList.toggle('is-active', active);
            btn.setAttribute('aria-pressed', active ? 'true' : 'false');
          });
        };

        const syncOperationField = () => {
          if (!opInput) {
            return;
          }
          const activeBtn = form.querySelector('.js-ps-op-btn.is-active');
          const code = activeBtn ? activeBtn.dataset.code : '';
          if (code === 'FLEX' || !code) {
            opInput.value = '';
            opInput.disabled = true;
          }
          else {
            opInput.disabled = false;
            opInput.value = code;
          }
        };

        const renderChips = () => {
          if (!chipsContainer) {
            return;
          }
          chipsContainer.innerHTML = '';
          selectedTokens.forEach((token, index) => {
            const chip = document.createElement('span');
            chip.className = 'ps-filter-bar__location-chip';
            chip.textContent = token;
            const remove = document.createElement('button');
            remove.type = 'button';
            remove.className = 'ps-filter-bar__location-chip-remove';
            remove.setAttribute('aria-label', Drupal.t('Remove @location', { '@location': token }));
            remove.textContent = '×';
            remove.addEventListener('click', () => {
              selectedTokens.splice(index, 1);
              renderChips();
            });
            chip.appendChild(remove);
            chipsContainer.appendChild(chip);
          });
        };

        const addTokens = (tokens) => {
          tokens.forEach((token) => {
            if (selectedTokens.indexOf(token) === -1 && selectedTokens.length < 5) {
              selectedTokens.push(token);
            }
          });
          renderChips();
        };

        const commitDraft = () => {
          if (!localityInput) {
            return;
          }
          const draft = localityInput.value.trim();
          if (!draft) {
            return;
          }
          addTokens(parseLocationTokens(draft));
          localityInput.value = '';
        };

        const renderSuggestions = (groups) => {
          if (!suggestBox) {
            return;
          }
          suggestBox.innerHTML = '';
          if (!groups || !groups.length) {
            suggestBox.hidden = true;
            return;
          }
          groups.forEach((group) => {
            const title = document.createElement('div');
            title.className = 'ps-location-suggest__group-title';
            title.textContent = group.label;
            suggestBox.appendChild(title);
            (group.items || []).forEach((item) => {
              const label = typeof item === 'string' ? item : (item.label || item.locality || '');
              const btn = document.createElement('button');
              btn.type = 'button';
              btn.className = 'ps-location-suggest__item';
              btn.textContent = label;
              btn.addEventListener('click', () => {
                const token = typeof item === 'string' ? item : (item.locality || item.label || '');
                addTokens([token]);
                localityInput.value = '';
                suggestBox.hidden = true;
              });
              suggestBox.appendChild(btn);
            });
          });
          suggestBox.hidden = false;
        };

        const fetchSuggestions = (query) => {
          if (!suggestBox) {
            return;
          }
          const url = new URL(suggestUrl, window.location.origin);
          url.searchParams.set('q', query);
          fetch(url.toString(), { credentials: 'same-origin' })
            .then((response) => response.json())
            .then((data) => renderSuggestions(data.groups || data))
            .catch(() => {
              suggestBox.hidden = true;
            });
        };

        form.querySelectorAll('.js-ps-op-btn').forEach((button) => {
          button.addEventListener('click', () => {
            setActiveOpButton(button);
            syncOperationField();
          });
        });

        if (localityInput) {
          localityInput.addEventListener('input', () => {
            window.clearTimeout(debounceTimer);
            const query = localityInput.value.trim();
            if (query.length < 2) {
              if (suggestBox) {
                suggestBox.hidden = true;
              }
              return;
            }
            debounceTimer = window.setTimeout(() => fetchSuggestions(query), 250);
          });

          localityInput.addEventListener('keydown', (event) => {
            if (event.key === 'Enter') {
              event.preventDefault();
              commitDraft();
              if (suggestBox) {
                suggestBox.hidden = true;
              }
            }
          });
        }

        form.addEventListener('submit', () => {
          commitDraft();
          syncOperationField();

          if (hiddenLocality) {
            hiddenLocality.innerHTML = '';
            selectedTokens.forEach((token) => {
              const input = document.createElement('input');
              input.type = 'hidden';
              input.name = 'locality[]';
              input.value = token;
              hiddenLocality.appendChild(input);
            });
          }

          if (assetSelect && !assetSelect.value) {
            assetSelect.removeAttribute('name');
          }
        });

        syncOperationField();
      });
    },
  };
})(Drupal, drupalSettings, once);
