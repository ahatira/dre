(function (Drupal, once, drupalSettings) {
  let csrfTokenPromise;

  const settings = () => drupalSettings.psCompare || {};

  const updateButtonState = (button, isCompared) => {
    button.classList.toggle('is-active', isCompared);
    button.setAttribute('aria-pressed', isCompared ? 'true' : 'false');
    const label = isCompared ? button.dataset.labelRemove : button.dataset.labelAdd;
    button.setAttribute('aria-label', label);
    button.setAttribute('title', label);
    const srNode = button.querySelector('.js-ps-compare-sr');
    if (srNode) {
      srNode.textContent = label;
    }
  };

  const syncButtons = (entityTypeId, entityId, isCompared) => {
    document.querySelectorAll(
      `[data-ps-compare-toggle][data-entity-type-id="${entityTypeId}"][data-entity-id="${entityId}"]`,
    ).forEach((button) => {
      updateButtonState(button, isCompared);
    });
  };

  const updateBarCount = (count) => {
    document.querySelectorAll('[data-ps-compare-count]').forEach((node) => {
      node.textContent = String(count);
      node.hidden = count <= 0;
    });
    document.querySelectorAll('[data-ps-compare-bar-label]').forEach((node) => {
      const base = node.dataset.baseLabel || node.textContent.trim();
      node.textContent = count > 0 ? `${base} (${count})` : base;
    });
  };

  const announce = (message) => {
    if (message && typeof Drupal.announce === 'function') {
      Drupal.announce(message);
    }
  };

  const getCsrfToken = async() => {
    if (!csrfTokenPromise) {
      csrfTokenPromise = Promise.resolve(settings().csrfToken || '').then((token) => {
        if (token) {
          return token;
        }

        return fetch('/session/token', {
          method: 'GET',
          credentials: 'same-origin',
          headers: { Accept: 'text/plain' },
        })
          .then((response) => (response.ok ? response.text() : ''))
          .catch(() => '');
      });
    }

    return csrfTokenPromise;
  };

  Drupal.behaviors.psCompareToggle = {
    attach(context) {
      once('ps-compare-toggle', '[data-ps-compare-toggle]', context).forEach((button) => {
        button.addEventListener('click', async(event) => {
          event.preventDefault();
          event.stopPropagation();

          if (button.classList.contains('is-loading')) {
            return;
          }

          button.classList.add('is-loading');
          button.setAttribute('aria-busy', 'true');

          try {
            const csrfToken = await getCsrfToken();
            const response = await fetch(button.dataset.url, {
              method: 'POST',
              credentials: 'same-origin',
              headers: {
                Accept: 'application/json',
                'X-CSRF-Token': csrfToken,
              },
            });

            const payload = await response.json();
            if (typeof payload.isCompared === 'boolean') {
              syncButtons(payload.entityTypeId, String(payload.entityId), payload.isCompared);
            }
            if (typeof payload.count === 'number') {
              updateBarCount(payload.count);
            }
            if (payload.message) {
              announce(payload.message);
            }

            document.dispatchEvent(new CustomEvent('psCompare:changed', {
              detail: payload,
            }));

            if (!response.ok && response.status === 409) {
              return;
            }
          }
          catch (e) {
            announce(Drupal.t('Unable to update comparison right now.'));
          }
          finally {
            button.classList.remove('is-loading');
            button.removeAttribute('aria-busy');
          }
        });
      });
    },
  };
})(Drupal, once, drupalSettings);
