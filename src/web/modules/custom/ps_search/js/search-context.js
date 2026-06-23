/**
 * @file
 * Client-side SearchContext store — single source of truth for search v2 URLs.
 */
(function (Drupal, drupalSettings) {
  'use strict';

  /** @type {object|null} */
  let state = null;

  /** @type {object} */
  let settings = {};

  const DEFAULT_SORT_BY = 'surface_total';
  const DEFAULT_SORT_ORDER = 'ASC';

  /**
   * @return {boolean}
   */
  function isEnabled() {
    return Boolean(drupalSettings.psSearch?.useSearchContext);
  }

  /**
   * @param {object} src
   * @return {object}
   */
  function cloneState(src) {
    return JSON.parse(JSON.stringify(src || {}));
  }

  /**
   * @param {string} pathname
   * @return {string}
   */
  function normalizePathname(pathname) {
    const path = String(pathname || '/');
    if (path.length > 1 && path.endsWith('/')) {
      return path.slice(0, -1);
    }
    return path;
  }

  /**
   * @param {?string} operationType
   * @param {?string} assetType
   * @return {?string}
   */
  function buildSeoFilterPathPrefix(operationType, assetType) {
    const opSlugs = settings.opSlugs || {};
    const assetSlugs = settings.assetSlugs || {};
    const op = operationType ? String(operationType).toUpperCase() : null;
    const asset = assetType ? String(assetType).toUpperCase() : null;

    if (op) {
      const opSlug = opSlugs[op];
      if (!opSlug) {
        return null;
      }
      let path = '/' + opSlug;
      if (asset && assetSlugs[asset]) {
        path += '/' + assetSlugs[asset];
      }
      return path;
    }

    if (asset && assetSlugs[asset]) {
      return '/' + assetSlugs[asset];
    }

    return null;
  }

  /**
   * @param {object} ctx
   * @return {boolean}
   */
  function isFlexibleBase(ctx) {
    const filters = ctx.filters || {};
    return buildSeoFilterPathPrefix(filters.operationType, filters.assetType) === null;
  }

  /**
   * @param {object} ctx
   * @return {string}
   */
  function buildSeoPath(ctx) {
    const langPrefix = settings.langPrefix || '';
    const filters = ctx.filters || {};
    let path = buildSeoFilterPathPrefix(filters.operationType, filters.assetType);

    if (!path) {
      const searchPath = String(settings.searchPath || '/find-property').replace(/^\/+/, '').replace(/\/+$/, '');
      path = '/' + searchPath;
    }

    if (ctx.geo && ctx.geo.slug) {
      path = path.replace(/\/+$/, '') + '/' + ctx.geo.slug;
    }

    return langPrefix + path + '/';
  }

  /**
   * @param {object} range
   * @param {string} minKey
   * @param {string} maxKey
   * @param {object} query
   */
  function appendRangeParams(range, minKey, maxKey, query) {
    if (!range || typeof range !== 'object') {
      return;
    }
    if (range.min !== null && range.min !== undefined && range.min !== '') {
      query[minKey] = range.min;
    }
    if (range.max !== null && range.max !== undefined && range.max !== '') {
      query[maxKey] = range.max;
    }
  }

  /**
   * @param {object} ctx
   * @return {object}
   */
  function buildQueryObject(ctx) {
    const query = {};
    const filters = ctx.filters || {};
    const flexible = isFlexibleBase(ctx);

    if (flexible && filters.operationType) {
      query.operation_type = filters.operationType;
    }
    if (flexible && filters.assetType) {
      query.asset_type = filters.assetType;
    }
    if (flexible && ctx.geo && ctx.geo.slug) {
      query.zone = ctx.geo.slug;
    }

    appendRangeParams(filters.surface, 'surface_min', 'surface_max', query);
    appendRangeParams(filters.budget, 'budget_min', 'budget_max', query);
    appendRangeParams(filters.capacity, 'capacity_min', 'capacity_max', query);

    const sort = ctx.sort || {};
    if (sort.sortBy && sort.sortBy !== DEFAULT_SORT_BY) {
      query.sort_by = sort.sortBy;
    }
    if (sort.sortOrder && sort.sortOrder !== DEFAULT_SORT_ORDER) {
      query.sort_order = sort.sortOrder;
    }

    const spatial = ctx.spatial || {};
    if (spatial.mode === 'viewport' && spatial.viewport) {
      query.map_bounds = spatial.viewport;
    }

    return query;
  }

  /**
   * @param {object} queryObj
   * @return {URLSearchParams}
   */
  function queryObjectToParams(queryObj) {
    const params = new URLSearchParams();
    Object.keys(queryObj).forEach(function (key) {
      const value = queryObj[key];
      if (value !== null && value !== undefined && value !== '') {
        params.set(key, String(value));
      }
    });
    return params;
  }

  /**
   * @param {URLSearchParams} params
   * @param {string} key
   * @return {?string}
   */
  function readFacetValue(params, key) {
    if (params.has(key)) {
      return params.get(key);
    }
    const prefix = key + '[';
    let found = null;
    params.forEach(function (value, name) {
      if (found !== null) {
        return;
      }
      if (name.indexOf(prefix) === 0 && name.endsWith(']')) {
        found = value || name.slice(prefix.length, -1);
      }
    });
    return found;
  }

  /**
   * Parses SEO path segments into operation/asset codes and geo slug tail.
   *
   * @return {{operationType: ?string, assetType: ?string, geoSlug: ?string}}
   */
  function resolveFromPathname() {
    const opSlugs = settings.opSlugs || {};
    const assetSlugs = settings.assetSlugs || {};
    const langPrefix = settings.langPrefix || '';
    let path = window.location.pathname || '';

    if (langPrefix && path.indexOf(langPrefix + '/') === 0) {
      path = path.slice(langPrefix.length);
    }

    const segments = path.split('/').filter(Boolean);
    const searchPath = String(settings.searchPath || '/find-property').replace(/^\/+/, '').replace(/\/+$/, '');
    if (!segments.length || segments[0] === searchPath) {
      return { operationType: null, assetType: null, geoSlug: null };
    }

    let operationType = null;
    let assetType = null;
    let restStart = 0;

    Object.keys(opSlugs).forEach(function (code) {
      if (opSlugs[code] === segments[0]) {
        operationType = code;
        restStart = 1;
      }
    });

    if (operationType && segments[1]) {
      Object.keys(assetSlugs).forEach(function (code) {
        if (assetSlugs[code] === segments[1]) {
          assetType = code;
          restStart = 2;
        }
      });
    }
    else if (!operationType) {
      Object.keys(assetSlugs).forEach(function (code) {
        if (assetSlugs[code] === segments[0]) {
          assetType = code;
          restStart = 1;
        }
      });
    }

    const tail = segments.slice(restStart);
    const geoSlug = tail.length ? tail[tail.length - 1] : null;

    return { operationType: operationType, assetType: assetType, geoSlug: geoSlug };
  }

  /**
   * @param {URLSearchParams} params
   * @param {string} legacyMin
   * @param {string} legacyMax
   * @param {string} flatMin
   * @param {string} flatMax
   * @return {?object}
   */
  function readRangeFromParams(params, legacyMin, legacyMax, flatMin, flatMax) {
    const min = params.get(flatMin) || params.get(legacyMin);
    const max = params.get(flatMax) || params.get(legacyMax);
    if (!min && !max) {
      return null;
    }
    return {
      min: min ? Number(min) : null,
      max: max ? Number(max) : null,
    };
  }

  function initFromSettings() {
    settings = drupalSettings.psSearch || {};
    if (!isEnabled()) {
      state = null;
      return;
    }
    state = cloneState(settings.searchContext || {});
    state.filters = state.filters || {};
    state.sort = state.sort || { sortBy: DEFAULT_SORT_BY, sortOrder: DEFAULT_SORT_ORDER };
    state.spatial = state.spatial || { mode: 'bbox_and_postal', viewport: null };
  }

  Drupal.psSearchContext = {
    isEnabled: isEnabled,

    getState: function () {
      return cloneState(state);
    },

    setGeo: function (geo) {
      if (!isEnabled()) {
        return;
      }
      state.geo = geo ? cloneState(geo) : null;
    },

    setFilter: function (key, value) {
      if (!isEnabled()) {
        return;
      }
      state.filters = state.filters || {};
      state.filters[key] = value;
    },

    setSort: function (sortBy, sortOrder) {
      if (!isEnabled()) {
        return;
      }
      state.sort = {
        sortBy: sortBy || DEFAULT_SORT_BY,
        sortOrder: sortOrder || DEFAULT_SORT_ORDER,
      };
    },

    setSpatialViewport: function (viewport) {
      if (!isEnabled()) {
        return;
      }
      state.spatial = state.spatial || {};
      state.spatial.mode = 'viewport';
      state.spatial.viewport = viewport || null;
    },

    clearSpatialViewport: function () {
      if (!isEnabled() || !state.spatial) {
        return;
      }
      if (state.geo) {
        state.spatial.mode = 'bbox_and_postal';
      }
      state.spatial.viewport = null;
    },

    buildSeoPath: function () {
      return buildSeoPath(state || {});
    },

    buildUrl: function () {
      if (!isEnabled()) {
        return window.location.pathname + window.location.search;
      }
      const path = buildSeoPath(state);
      const query = buildQueryObject(state);
      const params = queryObjectToParams(query);
      const qs = params.toString();
      return qs ? path + '?' + qs : path;
    },

    buildApiParams: function () {
      if (!isEnabled()) {
        return new URLSearchParams(window.location.search);
      }

      const params = queryObjectToParams(buildQueryObject(state));
      const filters = state.filters || {};

      if (filters.operationType) {
        params.set('operation_type', filters.operationType);
      }
      if (filters.assetType) {
        params.set('asset_type', filters.assetType);
      }
      if (state.geo && state.geo.slug) {
        params.set('zone', state.geo.slug);
      }

      if (typeof Drupal.psSearchPage?.appendContentLangParam === 'function') {
        Drupal.psSearchPage.appendContentLangParam(params);
      }

      return params;
    },

    requiresFullNavigation: function (browserUrl) {
      const next = new URL(String(browserUrl || ''), window.location.origin);
      if (normalizePathname(next.pathname) !== normalizePathname(window.location.pathname)) {
        return true;
      }

      const flexBase = normalizePathname((settings.langPrefix || '') + String(settings.searchPath || '/find-property'));
      if (normalizePathname(next.pathname) !== flexBase) {
        return false;
      }

      return Boolean(
        readFacetValue(next.searchParams, 'operation_type')
        || readFacetValue(next.searchParams, 'asset_type'),
      );
    },

    syncFromUrl: function () {
      if (!isEnabled()) {
        return;
      }

      const params = new URLSearchParams(window.location.search);
      const pathFacets = resolveFromPathname();

      state.filters = state.filters || {};
      state.filters.operationType = pathFacets.operationType
        || readFacetValue(params, 'operation_type')
        || state.filters.operationType
        || null;
      state.filters.assetType = pathFacets.assetType
        || readFacetValue(params, 'asset_type')
        || state.filters.assetType
        || null;

      const zoneSlug = params.get('zone') || pathFacets.geoSlug;
      if (zoneSlug) {
        const current = state.geo || {};
        state.geo = Object.assign({}, current, { slug: zoneSlug });
      }
      else if (!params.get('locality') && !params.get('locations')) {
        state.geo = null;
      }

      state.filters.surface = readRangeFromParams(params, 'surface[min]', 'surface[max]', 'surface_min', 'surface_max');
      state.filters.budget = readRangeFromParams(params, 'budget[min]', 'budget[max]', 'budget_min', 'budget_max');
      state.filters.capacity = readRangeFromParams(params, 'capacity[min]', 'capacity[max]', 'capacity_min', 'capacity_max');

      const sortBy = params.get('sort_by');
      const sortOrder = params.get('sort_order');
      if (sortBy || sortOrder) {
        state.sort = {
          sortBy: sortBy || DEFAULT_SORT_BY,
          sortOrder: sortOrder || DEFAULT_SORT_ORDER,
        };
      }

      const mapBounds = params.get('map_bounds');
      if (mapBounds) {
        state.spatial = state.spatial || {};
        state.spatial.mode = 'viewport';
        state.spatial.viewport = mapBounds;
      }
    },

    syncPathCurrentQuery: function () {
      if (!isEnabled()) {
        return;
      }
      if (typeof Drupal.psSearchPage?.syncPathCurrentQueryFromParams === 'function') {
        Drupal.psSearchPage.syncPathCurrentQueryFromParams(this.buildApiParams());
      }
    },

    apply: function (root) {
      if (!isEnabled()) {
        return Promise.resolve(false);
      }

      const url = this.buildUrl();
      const resolved = new URL(url, window.location.origin);
      const payload = {
        browserUrl: resolved.pathname + resolved.search,
        params: this.buildApiParams(),
      };

      if (this.requiresFullNavigation(payload.browserUrl)) {
        window.location.assign(payload.browserUrl);
        return Promise.resolve(true);
      }

      if (!root || typeof Drupal.psSearchPage?.reloadSearch !== 'function') {
        window.location.href = payload.browserUrl;
        return Promise.resolve(true);
      }

      this.syncPathCurrentQuery();
      return Drupal.psSearchPage.reloadSearch(root, payload).then(function () {
        return true;
      });
    },
  };

  initFromSettings();

  window.addEventListener('popstate', function () {
    if (isEnabled()) {
      Drupal.psSearchContext.syncFromUrl();
    }
  });

})(Drupal, drupalSettings);
