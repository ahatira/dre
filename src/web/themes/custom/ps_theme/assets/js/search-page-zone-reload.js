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

    hint.dataset.globalCount = String(globalCount);

    if (zoneCount !== globalCount) {
      hint.textContent = formatZoneHint(zoneCount);
      hint.hidden = false;
      return;
    }

    hint.textContent = '';
    hint.hidden = true;
  }

  /**
   * Syncs global + zone counts in the results header after list/map reload.
   *
   * @param {HTMLElement} root
   *   Search view root.
   * @param {number} globalCount
   *   Business-filter total.
   * @param {number} zoneCount
   *   Results in the active map zone.
   */
  function syncResultsHeaderCounts(root, globalCount, zoneCount) {
    const countParagraph = root.querySelector('.js-ps-results-header-count');
    if (!countParagraph || !Number.isFinite(globalCount)) {
      return;
    }

    let hint = root.querySelector('.js-ps-zone-hint');
    if (!hint) {
      hint = document.createElement('span');
      hint.className = 'ps-search-view__zone-hint js-ps-zone-hint';
      countParagraph.appendChild(hint);
    }

    const existingText = countParagraph.textContent.replace(hint.textContent, '').trim();
    const resultWordMatch = existingText.match(/\b(result|results|résultat|résultats)\b/i);
    const resultWord = resultWordMatch ? resultWordMatch[0] : (globalCount === 1 ? 'result' : 'results');
    const formattedGlobal = Number(globalCount).toLocaleString();

    Array.from(countParagraph.childNodes).forEach(function (node) {
      if (node.nodeType === Node.TEXT_NODE) {
        node.remove();
      }
    });
    countParagraph.insertBefore(
      document.createTextNode(`${formattedGlobal} ${resultWord} `),
      hint,
    );

    updateZoneHint(root, zoneCount, globalCount);

    drupalSettings.psSearch = drupalSettings.psSearch || {};
    drupalSettings.psSearch.globalCount = globalCount;
    drupalSettings.psSearch.zoneCount = zoneCount;
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

    const filterParams = new URLSearchParams(params);
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

        if (typeof Drupal.psSearchPage.getListScrollEl === 'function') {
          const listScroll = Drupal.psSearchPage.getListScrollEl(root);
          if (listScroll) {
            listScroll.scrollTop = 0;
          }
        }

        const htmxApi = Drupal.psSearchFilterHtmx;
        if (typeof htmxApi?.refreshResultsHeader === 'function' && htmxApi.isAvailable()) {
          return htmxApi.refreshResultsHeader(filterParams.toString()).then(function (headerTarget) {
            if (headerTarget) {
              Drupal.attachBehaviors(headerTarget);
            }
          }).catch(function () {
            const headerHtml = extractInnerHtml(replaceCommand.data, '.ps-search-view__results-header');
            const header = root.querySelector('.ps-search-view__results-header');
            if (header && headerHtml !== null) {
              header.innerHTML = headerHtml;
              Drupal.attachBehaviors(header);
            }
          });
        }

        const headerHtml = extractInnerHtml(replaceCommand.data, '.ps-search-view__results-header');
        const header = root.querySelector('.ps-search-view__results-header');
        if (header && headerHtml !== null) {
          header.innerHTML = headerHtml;
          Drupal.attachBehaviors(header);
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
   * @param {object} options
   *   Marker reload options.
   *
   * @return {Promise<object>}
   *   Markers API payload.
   */
  function reloadMarkers(root, params, options) {
    if (typeof Drupal.psSearchPage.loadMarkers !== 'function') {
      return Promise.resolve({ markers: [], zone_count: 0 });
    }

    const query = params.toString();
    const preserveViewport = options?.preserveViewport === true;
    return Drupal.psSearchPage.loadMarkers(root, query, { preserveViewport: preserveViewport }).then(function (data) {
      return data || { markers: [], zone_count: 0 };
    });
  }

  /**
   * Applies shared post-reload bookkeeping for list + map sync.
   *
   * @param {HTMLElement} root
   *   Search view root.
   * @param {URLSearchParams} params
   *   Active search query parameters.
   * @param {object} options
   *   Completion options.
   *
   * @return {Promise<object>}
   *   Markers API payload.
   */
  function completeSearchReload(root, params, options) {
    const threshold = Number(drupalSettings.psSearch?.listPagerThreshold || 100);
    const preserveViewport = options?.preserveViewport === true;
    const mapBoundsValue = options?.mapBoundsValue || params.get('map_bounds') || '';

    return reloadMarkers(root, params, { preserveViewport: preserveViewport }).then(function (payload) {
      const zoneCount = Number(payload.zone_count || 0);
      const globalFromMarkers = Number(payload.global_count);
      const hint = root.querySelector('.js-ps-zone-hint');
      const globalFromHint = Number(hint?.dataset.globalCount || 0);
      const resolvedGlobal = Number.isFinite(globalFromMarkers) && globalFromMarkers >= 0
        ? globalFromMarkers
        : (globalFromHint || Number(drupalSettings.psSearch?.globalCount) || zoneCount);

      drupalSettings.psSearch = drupalSettings.psSearch || {};
      drupalSettings.psSearch.zoneCount = zoneCount;
      drupalSettings.psSearch.globalCount = resolvedGlobal;
      drupalSettings.psSearch.listLoadAll = zoneCount > 0 && zoneCount <= threshold;

      if (mapBoundsValue) {
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
      }
      else if (!params.has('map_bounds')) {
        drupalSettings.psSearch.mapBounds = Object.assign(
          {},
          drupalSettings.psSearch.mapBounds || {},
          {
            queryValue: '',
            explicit: false,
          },
        );
      }

      syncResultsHeaderCounts(root, resolvedGlobal, zoneCount);
      root.dispatchEvent(new CustomEvent('ps-search-map-marker-clear'));

      const eventName = options?.eventName || 'ps-search-results-reloaded';
      root.dispatchEvent(new CustomEvent(eventName, {
        detail: Object.assign({
          zoneCount: zoneCount,
          mapBounds: mapBoundsValue,
          listLoadAll: drupalSettings.psSearch.listLoadAll,
        }, options?.eventDetail || {}),
      }));

      return payload;
    });
  }

  /**
   * Reloads list, markers and browser URL without a full page navigation.
   *
   * @param {HTMLElement} root
   *   Search view root.
   * @param {object} options
   *   Reload options.
   * @param {string} [options.browserUrl]
   *   Browser URL (path + query) to persist via history.pushState.
   * @param {URLSearchParams} [options.params]
   *   Query parameters for Views AJAX and markers API.
   * @param {boolean} [options.preserveViewport]
   *   Whether the map viewport should stay unchanged.
   *
   * @return {Promise<void>}
   *   Resolves when the partial reload completed.
   */
  Drupal.psSearchPage.reloadSearch = function (root, options) {
    options = options || {};
    const params = options.params instanceof URLSearchParams
      ? new URLSearchParams(options.params)
      : (typeof Drupal.psSearchPage?.buildSearchParams === 'function'
        ? Drupal.psSearchPage.buildSearchParams()
        : new URLSearchParams(window.location.search));
    params.delete('page');

    if (options.browserUrl) {
      window.history.pushState({}, '', options.browserUrl);
    }

    return reloadListPane(root, params)
      .then(function () {
        return completeSearchReload(root, params, {
          preserveViewport: options.preserveViewport === true,
          mapBoundsValue: params.get('map_bounds') || '',
          eventName: options.eventName || 'ps-search-filters-applied',
          eventDetail: options.eventDetail || {},
        });
      });
  };

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
    const params = typeof Drupal.psSearchPage?.buildSearchParams === 'function'
      ? Drupal.psSearchPage.buildSearchParams()
      : new URLSearchParams(window.location.search);
    params.set('map_bounds', mapBoundsValue);
    params.delete('page');

    const nextUrl = `${window.location.pathname}?${params.toString()}`;
    return Drupal.psSearchPage.reloadSearch(root, {
      browserUrl: nextUrl,
      params: params,
      preserveViewport: true,
      eventName: 'ps-search-zone-reloaded',
      eventDetail: {
        mapBounds: mapBoundsValue,
      },
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
