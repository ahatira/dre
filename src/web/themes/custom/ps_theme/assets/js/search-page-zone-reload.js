(function (Drupal, once, $) {
  'use strict';

  /**
   * Returns Views AJAX settings for the search list display.
   *
   * @param {HTMLElement} root
   *   Search view root.
   *
   * @return {object|null}
   *   Views AJAX settings bucket.
   */
  function getAjaxViewSettings(root) {
    const match = root.className.match(/js-view-dom-id-([a-f0-9]+)/);
    if (!match) {
      return null;
    }
    const key = `views_dom_id:${match[1]}`;
    const buckets = Drupal.settings?.views?.ajaxViews || drupalSettings.views?.ajaxViews || {};
    return buckets[key] || null;
  }

  /**
   * Merges Drupal settings commands from a Views AJAX payload.
   *
   * @param {Array<object>} commands
   *   Drupal AJAX commands.
   */
  function mergeSettingsCommands(commands) {
    commands.forEach(function (command) {
      if (command.command !== 'settings' || !command.settings) {
        return;
      }
      $.extend(true, Drupal.settings, command.settings);
      $.extend(true, drupalSettings, command.settings);
    });
  }

  /**
   * Extracts inner HTML for a selector from a Views AJAX replace payload.
   *
   * @param {string} html
   *   Full view HTML from Views AJAX.
   * @param {string} selector
   *   CSS selector to extract.
   *
   * @return {string|null}
   *   Inner HTML or NULL when missing.
   */
  function extractInnerHtml(html, selector) {
    const doc = new DOMParser().parseFromString(html, 'text/html');
    const element = doc.querySelector(selector);
    return element ? element.innerHTML : null;
  }

  /**
   * Formats the zone hint label.
   *
   * @param {number} zoneCount
   *   Results in the active map zone.
   *
   * @return {string}
   *   Translated hint prefix + count.
   */
  function formatZoneHint(zoneCount) {
    const formatted = Number(zoneCount).toLocaleString();
    return ` — Showing ${formatted} in this area`;
  }

  /**
   * Updates the secondary zone counter in the results header.
   *
   * @param {HTMLElement} root
   *   Search view root.
   * @param {number} zoneCount
   *   Zone result count.
   * @param {number} globalCount
   *   Global business-filter count.
   */
  function updateZoneHint(root, zoneCount, globalCount) {
    const hint = root.querySelector('.js-ps-zone-hint');
    if (!hint) {
      return;
    }

    if (zoneCount > 0 && zoneCount !== globalCount) {
      hint.textContent = formatZoneHint(zoneCount);
      hint.hidden = false;
      return;
    }

    hint.textContent = '';
    hint.hidden = true;
  }

  /**
   * Reloads the list pane via Views AJAX without replacing the map shell.
   *
   * @param {HTMLElement} root
   *   Search view root.
   * @param {URLSearchParams} params
   *   Active search query parameters.
   *
   * @return {Promise<void>}
   *   Resolves when the list pane has been updated.
   */
  function reloadListPane(root, params) {
    const ajaxView = getAjaxViewSettings(root);
    if (!ajaxView) {
      return Promise.reject(new Error('missing_ajax_view_settings'));
    }

    const requestParams = new URLSearchParams(params);
    requestParams.set('_drupal_ajax', '1');
    requestParams.set('view_name', ajaxView.view_name);
    requestParams.set('view_display_id', ajaxView.view_display_id);
    requestParams.set('view_args', ajaxView.view_args || '');
    requestParams.set('view_path', ajaxView.view_path || window.location.pathname.replace(/^\//, ''));
    requestParams.set('view_dom_id', ajaxView.view_dom_id);
    requestParams.set('pager_element', String(ajaxView.pager_element ?? 0));
    requestParams.set('page', '0');

    return fetch(`${Drupal.url('views/ajax')}?${requestParams.toString()}`, {
      credentials: 'same-origin',
      headers: {
        Accept: 'application/vnd.drupal-ajax',
      },
    })
      .then(function (response) {
        if (!response.ok) {
          throw new Error('views_ajax_failed');
        }
        return response.json();
      })
      .then(function (commands) {
        if (!Array.isArray(commands)) {
          throw new Error('invalid_views_ajax_payload');
        }

        mergeSettingsCommands(commands);

        const replaceCommand = commands.find(function (command) {
          return command.command === 'insert' && command.method === 'replaceWith' && command.data;
        });
        if (!replaceCommand) {
          throw new Error('missing_replace_command');
        }

        const listHtml = extractInnerHtml(replaceCommand.data, '.js-ps-search-list-panel');
        const listPanel = root.querySelector('.js-ps-search-list-panel');
        if (!listPanel || listHtml === null) {
          throw new Error('missing_list_panel');
        }

        listPanel.innerHTML = listHtml;
        Drupal.attachBehaviors(listPanel);

        const headerHtml = extractInnerHtml(replaceCommand.data, '.ps-search-view__results-header');
        const header = root.querySelector('.ps-search-view__results-header');
        if (header && headerHtml !== null) {
          header.innerHTML = headerHtml;
        }
      });
  }

  /**
   * Parses map_bounds query value into corner coordinates.
   *
   * @param {string} mapBoundsValue
   *   map_bounds query value.
   *
   * @return {{swLat: number, swLng: number, neLat: number, neLng: number}|null}
   *   Parsed corners or NULL when invalid.
   */
  function parseMapBoundsValue(mapBoundsValue) {
    const parts = String(mapBoundsValue || '').split(',').map(function (part) {
      return parseFloat(part.trim());
    });
    if (parts.length !== 4 || parts.some(function (value) {
      return !Number.isFinite(value);
    })) {
      return null;
    }
    return {
      swLat: parts[0],
      swLng: parts[1],
      neLat: parts[2],
      neLng: parts[3],
    };
  }

  /**
   * Reloads markers and returns the markers API payload.
   *
   * @param {HTMLElement} root
   *   Search view root.
   * @param {URLSearchParams} params
   *   Active search query parameters.
   *
   * @return {Promise<object>}
   *   Markers API payload.
   */
  function reloadMarkers(root, params) {
    if (typeof Drupal.psSearchPage.loadMarkers !== 'function') {
      return Promise.resolve({ markers: [], zone_count: 0 });
    }

    const query = params.toString();
    return Drupal.psSearchPage.loadMarkers(root, query, { preserveViewport: true }).then(function (data) {
      return data || { markers: [], zone_count: 0 };
    });
  }

  /**
   * Reloads list, markers and URL for a new map zone.
   *
   * @param {HTMLElement} root
   *   Search view root.
   * @param {string} mapBoundsValue
   *   map_bounds query value.
   *
   * @return {Promise<void>}
   *   Resolves when the zone reload completed.
   */
  Drupal.psSearchPage.reloadZoneSearch = function (root, mapBoundsValue) {
    const params = new URLSearchParams(window.location.search);
    params.set('map_bounds', mapBoundsValue);
    params.delete('page');

    const nextUrl = `${window.location.pathname}?${params.toString()}`;
    window.history.pushState({ mapBounds: mapBoundsValue }, '', nextUrl);

    const threshold = Number(drupalSettings.psSearch?.listPagerThreshold || 100);
    const globalCount = Number(root.querySelector('.js-ps-zone-hint')?.dataset.globalCount || drupalSettings.psSearch?.globalCount || 0);

    return reloadListPane(root, params)
      .then(function () {
        return reloadMarkers(root, params);
      })
      .then(function (payload) {
        const zoneCount = Number(payload.zone_count || 0);
        const resolvedGlobal = globalCount || Number(drupalSettings.psSearch?.globalCount || zoneCount);

        drupalSettings.psSearch = drupalSettings.psSearch || {};
        drupalSettings.psSearch.zoneCount = zoneCount;
        drupalSettings.psSearch.listLoadAll = zoneCount > 0 && zoneCount <= threshold;
        const parsedBounds = parseMapBoundsValue(mapBoundsValue);
        drupalSettings.psSearch.mapBounds = Object.assign(
          {},
          drupalSettings.psSearch.mapBounds || {},
          parsedBounds || {},
          {
            queryValue: mapBoundsValue,
            explicit: true,
          },
        );

        updateZoneHint(root, zoneCount, resolvedGlobal);

        root.dispatchEvent(new CustomEvent('ps-search-map-marker-clear'));

        root.dispatchEvent(new CustomEvent('ps-search-zone-reloaded', {
          detail: {
            zoneCount: zoneCount,
            mapBounds: mapBoundsValue,
            listLoadAll: drupalSettings.psSearch.listLoadAll,
          },
        }));
      });
  };

  Drupal.behaviors.psSearchPageZoneReload = {
    attach(context) {
      once('ps-search-zone-hint-init', '.js-ps-zone-hint', context).forEach(function (hint) {
        const root = hint.closest('.ps-search-view');
        if (!root) {
          return;
        }
        const zoneCount = Number(drupalSettings.psSearch?.zoneCount || 0);
        const globalCount = Number(hint.dataset.globalCount || 0);
        updateZoneHint(root, zoneCount, globalCount);
      });
    },
  };
}(Drupal, once, jQuery));
