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
    const requestParams = typeof Drupal.psSearchPage?.buildViewsAjaxParams === 'function'
      ? Drupal.psSearchPage.buildViewsAjaxParams(params)
      : (typeof Drupal.psSearchPage?.normalizeFacetParamsForViewsAjax === 'function'
        ? Drupal.psSearchPage.normalizeFacetParamsForViewsAjax(params)
        : new URLSearchParams(params));
    requestParams.set('_drupal_ajax', '1');
    requestParams.set('view_name', ajaxView.view_name);
    requestParams.set('view_display_id', ajaxView.view_display_id);
    requestParams.set('view_args', ajaxView.view_args || '');
    const viewPath = String(ajaxView.view_path || 'find-property').replace(/^\/+/, '');
    requestParams.set('view_path', viewPath);
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
   * Reloads map markers via the markers API (persistent PS map shell).
   *
   * @param {HTMLElement} root
   *   Search view root.
   * @param {URLSearchParams} params
   *   Active search query parameters.
   * @param {object} options
   *   Reload options.
   *
   * @return {Promise<void>}
   *   Resolves when markers have been refreshed.
   */
  function reloadMapMarkers(root, params, options) {
    const preserveViewport = options?.preserveViewport === true;

    let savedCenter = null;
    let savedZoom = null;
    if (preserveViewport) {
      const mapData = Drupal.psSearchMap.getMapData(root);
      const map = mapData?.map;
      if (map && typeof map.getCenter === 'function') {
        savedCenter = map.getCenter();
        savedZoom = map.getZoom();
      }
    }

    return new Promise(function (resolve, reject) {
      Drupal.psSearchMap.whenMapShellReady(root, function () {
        const queryString = params.toString();
        const loadPromise = typeof Drupal.psSearchPage.loadMarkers === 'function'
          ? Drupal.psSearchPage.loadMarkers(root, queryString, { preserveViewport: preserveViewport })
          : Promise.resolve();

        loadPromise.then(function () {
          if (preserveViewport && savedCenter && typeof savedZoom === 'number') {
            const mapData = Drupal.psSearchMap.getMapData(root);
            if (mapData?.map) {
              mapData.map.setCenter(savedCenter);
              mapData.map.setZoom(savedZoom);
            }
          }
          resolve();
        }).catch(reject);
      });
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
   *   Reload metadata.
   */
  function completeSearchReload(root, params, options) {
    const threshold = Number(drupalSettings.psSearch?.listPagerThreshold || 100);
    const preserveViewport = options?.preserveViewport === true;
    const mapBoundsValue = options?.mapBoundsValue || params.get('map_bounds') || '';

    const markerParams = buildMapReloadParams(root, params);

    return reloadMapMarkers(root, markerParams, { preserveViewport: preserveViewport }).then(function () {
      const zoneCount = Number(drupalSettings.psSearch?.zoneCount || 0);
      const globalCount = Number(drupalSettings.psSearch?.globalCount || 0);
      const hint = root.querySelector('.js-ps-zone-hint');
      const globalFromHint = Number(hint?.dataset.globalCount || 0);
      const resolvedGlobal = Number.isFinite(globalCount) && globalCount >= 0
        ? globalCount
        : (globalFromHint || zoneCount);

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

      return {
        zone_count: zoneCount,
        global_count: resolvedGlobal,
        display_mode: 'ps-map',
      };
    });
  }

  /**
   * Builds Views / markers API params scoped to list rows currently loaded.
   *
   * @param {HTMLElement} root
   *   Search view root.
   * @param {URLSearchParams} [baseParams]
   *   Optional params merged before list scoping (filters, sort, map_bounds).
   *
   * @return {URLSearchParams}
   *   Params for markers API reload.
   */
  function buildMapReloadParams(root, baseParams) {
    const params = baseParams instanceof URLSearchParams
      ? new URLSearchParams(baseParams)
      : (typeof Drupal.psSearchPage?.buildSearchParams === 'function'
        ? Drupal.psSearchPage.buildSearchParams()
        : new URLSearchParams(window.location.search));
    params.delete('page');
    params.delete('ps_list_loaded_count');

    const listNids = typeof Drupal.psSearchPage?.getListOfferNids === 'function'
      ? Drupal.psSearchPage.getListOfferNids(root)
      : new Set();
    if (listNids.size > 0) {
      params.set('ps_list_loaded_count', String(listNids.size));
    }

    return params;
  }

  Drupal.psSearchPage = Drupal.psSearchPage || {};
  Drupal.psSearchPage.buildMapReloadParams = function (root, baseParams) {
    return buildMapReloadParams(root, baseParams);
  };

  /**
   * Reloads map markers mirroring the loaded list (PS map shell).
   *
   * @param {HTMLElement} root
   *   Search view root.
   * @param {object} [options]
   *   Reload options.
   * @param {boolean} [options.preserveViewport]
   *   Keep current map zoom/center.
   *
   * @return {Promise<object>}
   *   Reload metadata.
   */
  Drupal.psSearchPage.reloadMapForList = function (root, options) {
    options = options || {};
    const params = buildMapReloadParams(root);
    return reloadMapMarkers(root, params, {
      preserveViewport: options.preserveViewport === true,
    }).then(function () {
      return {
        zone_count: Number(drupalSettings.psSearch?.zoneCount || 0),
        display_mode: 'ps-map',
      };
    });
  };

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
      once('ps-search-load-more-map-sync', '.ps-search-view', context).forEach(function (root) {
        root.addEventListener('ps-search-list-new-content', function () {
          if (typeof Drupal.psSearchPage.reloadMapForList !== 'function') {
            return;
          }
          Drupal.psSearchPage.reloadMapForList(root, { preserveViewport: true }).catch(function () {
            // Keep the list usable when marker reload fails.
          });
        });
      });

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
