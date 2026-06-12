(function (Drupal, once, drupalSettings) {
  let csrfTokenPromise;

  const updateButtonState = (button, isFavorite) => {
    button.classList.toggle('is-active', isFavorite);
    button.setAttribute('aria-pressed', isFavorite ? 'true' : 'false');
    const label = isFavorite ? button.dataset.labelRemove : button.dataset.labelAdd;
    const text = isFavorite ? button.dataset.textRemove : button.dataset.textAdd;
    button.setAttribute('aria-label', label);
    button.setAttribute('title', label);
    const textNode = button.querySelector('.js-ps-favorite-text');
    const srNode = button.querySelector('.js-ps-favorite-sr');
    if (textNode) {
      textNode.textContent = text;
    }
    if (srNode) {
      srNode.textContent = label;
    }
  };

  const updateCount = (count) => {
    document.querySelectorAll('[data-ps-favorite-count]').forEach((badge) => {
      badge.textContent = String(count);
      if (count > 0) {
        badge.removeAttribute('hidden');
      }
      else {
        badge.setAttribute('hidden', 'hidden');
      }
    });
  };

  const refreshEmptyStates = () => {
    document.querySelectorAll('[data-ps-favorite-list]').forEach((list) => {
      const items = list.querySelectorAll('[data-ps-favorite-card]');
      const empty = list.querySelector('[data-ps-favorite-empty-state]');
      if (!empty) {
        return;
      }
      empty.classList.toggle('is-hidden', items.length > 0);
    });
  };

  const removeCards = (entityTypeId, entityId) => {
    document.querySelectorAll(`[data-ps-favorite-card][data-entity-type-id="${entityTypeId}"][data-entity-id="${entityId}"]`).forEach((card) => {
      card.remove();
    });
    refreshEmptyStates();
  };

  const fetchCount = async () => {
    const endpoint = drupalSettings.psFavorite?.countEndpoint;
    if (!endpoint) {
      return;
    }

    try {
      const response = await fetch(endpoint, {
        method: 'GET',
        credentials: 'same-origin',
        headers: { 'Accept': 'application/json' },
      });
      if (!response.ok) {
        return;
      }
      const payload = await response.json();
      if (typeof payload.count === 'number') {
        updateCount(payload.count);
      }
    }
    catch (e) {
      // Silent on purpose: periodic refresh should never break UX.
    }
  };

  const getCsrfToken = async () => {
    if (!csrfTokenPromise) {
      csrfTokenPromise = fetch('/session/token', {
        method: 'GET',
        credentials: 'same-origin',
        headers: { 'Accept': 'text/plain' },
      })
        .then((response) => (response.ok ? response.text() : ''))
        .catch(() => '');
    }

    return csrfTokenPromise;
  };

  Drupal.behaviors.psFavorite = {
    attach(context) {
      once('ps-favorite-count-refresh', 'body', context).forEach(() => {
        fetchCount();
        const refreshMs = Number(drupalSettings.psFavorite?.countRefreshMs || 0);
        if (Number.isFinite(refreshMs) && refreshMs > 0) {
          window.setInterval(fetchCount, refreshMs);
        }
      });

      once('ps-favorite-toggle', '[data-ps-favorite-toggle]', context).forEach((button) => {
        button.addEventListener('click', async (event) => {
          event.preventDefault();
          if (button.disabled) {
            return;
          }

          button.disabled = true;
          button.classList.add('is-loading');
          button.setAttribute('aria-busy', 'true');

          try {
            const csrfToken = await getCsrfToken();
            const response = await fetch(button.dataset.url, {
              method: 'POST',
              credentials: 'same-origin',
              headers: {
                'Accept': 'application/json',
                'X-CSRF-Token': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
              },
            });

            const payload = await response.json();
            if (!response.ok) {
              throw new Error(payload.message || Drupal.t('Unable to update favorite.'));
            }

            document.querySelectorAll(`[data-ps-favorite-toggle][data-entity-type-id="${payload.entityTypeId}"][data-entity-id="${payload.entityId}"]`).forEach((currentButton) => {
              updateButtonState(currentButton, payload.isFavorite);
            });
            updateCount(payload.count);
            if (!payload.isFavorite) {
              removeCards(payload.entityTypeId, payload.entityId);
            }

            Drupal.announce(payload.message);
            document.dispatchEvent(new CustomEvent('psFavorite:updated', { detail: payload }));
          }
          catch (error) {
            Drupal.announce(error.message || Drupal.t('Unable to update favorite.'));
          }
          finally {
            button.disabled = false;
            button.classList.remove('is-loading');
            button.removeAttribute('aria-busy');
          }
        });
      });
    },
  };
})(Drupal, once, drupalSettings);
