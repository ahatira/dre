(function (Drupal, once, drupalSettings) {
  const STORAGE_KEY = 'ps:favorites:offers';
  const TOAST_CONTAINER_ID = 'ps-favorites-toast-container';

  function ensureToastContainer() {
    let container = document.getElementById(TOAST_CONTAINER_ID);
    if (container) {
      return container;
    }

    container = document.createElement('div');
    container.id = TOAST_CONTAINER_ID;
    container.className = 'toast-container position-fixed top-0 end-0 p-3';
    container.setAttribute('aria-live', 'polite');
    container.setAttribute('aria-atomic', 'true');
    document.body.appendChild(container);

    return container;
  }

  function showFavoriteToast(message) {
    const text = typeof message === 'string' ? message.trim() : '';
    if (!text) {
      return;
    }

    const container = ensureToastContainer();
    const toast = document.createElement('div');
    toast.className = 'toast text-bg-success border-0';
    toast.setAttribute('role', 'status');
    toast.setAttribute('aria-live', 'polite');
    toast.setAttribute('aria-atomic', 'true');

    const body = document.createElement('div');
    body.className = 'd-flex';
    body.innerHTML = `<div class="toast-body">${Drupal.checkPlain(text)}</div>`;

    const closeButton = document.createElement('button');
    closeButton.type = 'button';
    closeButton.className = 'btn-close btn-close-white me-2 m-auto';
    closeButton.setAttribute('data-bs-dismiss', 'toast');
    closeButton.setAttribute('aria-label', Drupal.t('Close'));

    body.appendChild(closeButton);
    toast.appendChild(body);
    container.appendChild(toast);

    if (window.bootstrap && typeof window.bootstrap.Toast === 'function') {
      const instance = new window.bootstrap.Toast(toast, {
        autohide: true,
        delay: 2500,
      });
      toast.addEventListener('hidden.bs.toast', () => {
        toast.remove();
      }, { once: true });
      instance.show();
      return;
    }

    toast.classList.add('show');
    window.setTimeout(() => {
      toast.remove();
    }, 2500);
  }

  function processFlagMessageElement(element) {
    if (!element || !element.matches || !element.matches('.js-flag-message')) {
      return;
    }

    const toggleScope = element.closest('.ps-favorites-toggle, .ps-offer-hero__favorite-wrapper');
    if (!toggleScope) {
      return;
    }

    const flagWrapper = element.closest('.flag');
    if (flagWrapper) {
      const currentCount = Number.parseInt(
        document.querySelector('[data-ps-favorites-count]')?.textContent || '0',
        10,
      );

      if (Number.isInteger(currentCount)) {
        const isNowActive = flagWrapper.classList.contains('action-unflag');
        const nextCount = isNowActive ? currentCount + 1 : Math.max(0, currentCount - 1);
        updateHeaderCount(nextCount);
      }
    }

    showFavoriteToast(element.textContent || '');
    element.remove();
  }

  function observeFlagFlashMessages(context) {
    once('ps-favorites-flag-toast', 'html', context).forEach(() => {
      document.querySelectorAll('.js-flag-message').forEach((element) => {
        processFlagMessageElement(element);
      });

      const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
          mutation.addedNodes.forEach((node) => {
            if (!(node instanceof Element)) {
              return;
            }

            if (node.matches('.js-flag-message')) {
              processFlagMessageElement(node);
            }

            node.querySelectorAll('.js-flag-message').forEach((element) => {
              processFlagMessageElement(element);
            });
          });
        });
      });

      observer.observe(document.body, {
        childList: true,
        subtree: true,
      });
    });
  }

  function initFlagLoadingState(context) {
    once('ps-favorites-flag-loading', '.ps-favorites-toggle .flag a, .ps-offer-hero__favorite-wrapper .flag a', context).forEach((link) => {
      link.addEventListener('click', () => {
        const wrapper = link.closest('.flag');
        if (!wrapper) {
          return;
        }

        wrapper.classList.add('is-loading');
        link.setAttribute('aria-busy', 'true');

        // Fallback cleanup if ajax completion is not detected.
        window.setTimeout(() => {
          wrapper.classList.remove('is-loading');
          link.removeAttribute('aria-busy');
        }, 4000);
      });
    });

    once('ps-favorites-flag-loading-cleanup', 'html', context).forEach(() => {
      if (!window.jQuery) {
        return;
      }

      window.jQuery(document).on('ajaxComplete.psFavoritesLoading', () => {
        document.querySelectorAll('.ps-favorites-toggle .flag.is-loading, .ps-offer-hero__favorite-wrapper .flag.is-loading').forEach((wrapper) => {
          wrapper.classList.remove('is-loading');

          const link = wrapper.querySelector('a[aria-busy="true"]');
          if (link) {
            link.removeAttribute('aria-busy');
          }
        });
      });
    });
  }

  function getStoredFavorites() {
    try {
      const raw = window.localStorage.getItem(STORAGE_KEY);
      const parsed = raw ? JSON.parse(raw) : [];
      if (!Array.isArray(parsed)) {
        return [];
      }
      return parsed
        .map((value) => Number.parseInt(value, 10))
        .filter((value) => Number.isInteger(value) && value > 0);
    }
    catch (e) {
      return [];
    }
  }

  function saveStoredFavorites(values) {
    const unique = [...new Set(values)];
    window.localStorage.setItem(STORAGE_KEY, JSON.stringify(unique));
  }

  function updateHeaderCount(count) {
    document.querySelectorAll('[data-ps-favorites-count]').forEach((element) => {
      element.textContent = String(count);
      element.classList.toggle('d-none', count <= 0);
    });

    document.querySelectorAll('[data-ps-favorites-header]').forEach((header) => {
      const stroke = header.querySelector('[data-ps-favorites-icon-stroke]');
      const filled = header.querySelector('[data-ps-favorites-icon-filled]');
      if (!stroke || !filled) {
        return;
      }
      const hasFavorites = count > 0;
      stroke.classList.toggle('d-none', hasFavorites);
      filled.classList.toggle('d-none', !hasFavorites);
    });
  }

  function syncButtons(values) {
    const set = new Set(values);
    document.querySelectorAll('[data-ps-favorite-toggle]').forEach((button) => {
      const nid = Number.parseInt(button.getAttribute('data-ps-favorite-nid'), 10);
      const active = set.has(nid);
      button.classList.toggle('is-active', active);
      button.setAttribute('aria-pressed', active ? 'true' : 'false');
      button.setAttribute('aria-label', active ? 'Remove from favorites' : 'Add to favorites');

      const stroke = button.querySelector('[data-ps-favorite-icon-stroke]');
      const filled = button.querySelector('[data-ps-favorite-icon-filled]');
      if (stroke && filled) {
        stroke.classList.toggle('d-none', active);
        filled.classList.toggle('d-none', !active);
      }
    });
  }

  Drupal.behaviors.psFavorites = {
    attach(context) {
      observeFlagFlashMessages(context);
      initFlagLoadingState(context);

      const isAnonymousHeader = document.querySelector('[data-ps-favorites-auth="0"]') !== null;

      const values = getStoredFavorites();
      syncButtons(values);
      if (isAnonymousHeader) {
        updateHeaderCount(values.length);
      }

      once('ps-favorites-toggle', '[data-ps-favorite-toggle]', context).forEach((button) => {
        button.addEventListener('click', (event) => {
          event.preventDefault();

          const nid = Number.parseInt(button.getAttribute('data-ps-favorite-nid'), 10);
          if (!Number.isInteger(nid) || nid <= 0) {
            return;
          }

          const current = getStoredFavorites();
          const set = new Set(current);
          if (set.has(nid)) {
            set.delete(nid);
          }
          else {
            set.add(nid);
          }

          const next = [...set];
          saveStoredFavorites(next);
          syncButtons(next);
          if (isAnonymousHeader) {
            updateHeaderCount(next.length);
          }
        });
      });
    },
  };

  /**
   * On first authenticated page load, merge any localStorage favorites
   * accumulated while the user was anonymous, then clear the local store.
   */
  Drupal.behaviors.psFavoritesMerge = {
    attach(context) {
      once('ps-favorites-merge', 'html', context).forEach(() => {
        const authEl = document.querySelector('[data-ps-favorites-auth]');
        if (!authEl || authEl.getAttribute('data-ps-favorites-auth') !== '1') {
          return;
        }

        const nids = getStoredFavorites();
        if (nids.length === 0) {
          return;
        }

        /** @type {string} */
        const base = (drupalSettings.path && drupalSettings.path.baseUrl) || '/';

        fetch(`${base}session/token`)
          .then((r) => r.text())
          .then((token) => fetch(`${base}ps-favorites/merge`, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-Token': token.trim(),
            },
            body: JSON.stringify({ nids }),
          }))
          .then((response) => {
            if (response.ok) {
              window.localStorage.removeItem(STORAGE_KEY);
              // Reload so the server-side header count reflects the merge.
              window.location.reload();
            }
          })
          .catch(() => {
            // Silent fail — will retry on next page load.
          });
      });
    },
  };

})(Drupal, once, drupalSettings);

