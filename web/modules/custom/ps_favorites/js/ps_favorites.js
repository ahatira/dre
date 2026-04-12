(function (Drupal, once, drupalSettings) {
  const STORAGE_KEY = 'ps:favorites:offers';

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

