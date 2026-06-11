/**
 * @file
 * Shared HTMX helpers for the BNPPRE search filter bar (Phase 5A).
 *
 * Dispatches: ps-search-filter-htmx-count-updated, ps-search-filter-htmx-apply
 * Detail: { popinKey, targetId, count }
 */
(function (Drupal, drupalSettings, once) {
  'use strict';

  /**
   * @namespace
   */
  Drupal.psSearchFilterHtmx = Drupal.psSearchFilterHtmx || {
    settings: {},
    callbacks: {
      onCountUpdated: null,
      onApply: null,
      setLoading: null,
    },
  };

  const api = Drupal.psSearchFilterHtmx;

  /**
   * Loads settings from drupalSettings once per page.
   *
   * @param {object} [settings]
   *   Optional settings override (tests).
   */
  api.init = function (settings) {
    api.settings = settings || drupalSettings.psSearchFilterHtmx || {};
  };

  /**
   * Whether HTMX count fragments are enabled and htmx is present.
   */
  api.isAvailable = function () {
    return api.settings.enabled === true && typeof htmx !== 'undefined';
  };

  /**
   * @param {string} key
   *   Popin registry key (e.g. "type").
   *
   * @return {object|null}
   */
  api.getPopin = function (key) {
    const popins = api.settings.popins || {};
    return popins[key] || null;
  };

  /**
   * @param {string} key
   *   Popin registry key.
   */
  api.isHtmxPopin = function (key) {
    return api.isAvailable() && api.getPopin(key) !== null;
  };

  /**
   * @param {string} key
   *   Popin registry key.
   */
  api.isPopinOpen = function (key) {
    const popin = api.getPopin(key);
    if (!popin || !popin.openSelector) {
      return false;
    }
    return !!document.querySelector(popin.openSelector);
  };

  /**
   * Returns the first open popin key that uses HTMX, if any.
   *
   * @return {string|null}
   */
  api.getOpenHtmxPopinKey = function () {
    const popins = api.settings.popins || {};
    const keys = Object.keys(popins);
    for (let i = 0; i < keys.length; i++) {
      const key = keys[i];
      if (api.isPopinOpen(key)) {
        return key;
      }
    }
    return null;
  };

  /**
   * Resolves popin key from a desktop dropdown element class list.
   *
   * @param {HTMLElement} dropdownEl
   *   Bootstrap dropdown root for a filter field.
   *
   * @return {string|null}
   */
  api.resolvePopinKeyFromDropdown = function (dropdownEl) {
    const popins = api.settings.popins || {};
    const keys = Object.keys(popins);
    for (let i = 0; i < keys.length; i++) {
      const key = keys[i];
      const dropdownClass = popins[key].dropdownClass;
      if (dropdownClass && dropdownEl.classList.contains(dropdownClass)) {
        return key;
      }
    }
    return null;
  };

  /**
   * Finds popin key by HTMX target element id.
   *
   * @param {string} targetId
   *   DOM id swapped by HTMX.
   *
   * @return {string|null}
   */
  api.resolvePopinKeyByTargetId = function (targetId) {
    const popins = api.settings.popins || {};
    const keys = Object.keys(popins);
    for (let i = 0; i < keys.length; i++) {
      const key = keys[i];
      if (popins[key].targetId === targetId) {
        return key;
      }
    }
    return null;
  };

  /**
   * Triggers server-side apply for a popin (HX-Trigger-After-Settle).
   *
   * @param {string} popinKey
   *   Registry key.
   * @param {string} queryString
   *   URL-encoded filter query (without leading "?").
   *
   * @return {boolean}
   *   TRUE when an HTMX request was started.
   */
  api.applyPopin = function (popinKey, queryString) {
    const popin = api.getPopin(popinKey);
    if (!popin || !popin.applyUrl || !api.isAvailable()) {
      return false;
    }

    if (typeof api.callbacks.setLoading === 'function') {
      api.callbacks.setLoading(true);
    }

    const url = queryString ? popin.applyUrl + '?' + queryString : popin.applyUrl;
    htmx.ajax('GET', url, {
      swap: 'none',
    });
    return true;
  };

  /**
   * Closes the desktop Bootstrap dropdown for a popin key.
   *
   * @param {string} popinKey
   *   Registry key.
   */
  api.closePopinDropdown = function (popinKey) {
    const popin = api.getPopin(popinKey);
    if (!popin) {
      return;
    }

    if (popin.offcanvasId) {
      const offcanvasEl = document.getElementById(popin.offcanvasId);
      if (offcanvasEl && typeof bootstrap !== 'undefined') {
        const instance = bootstrap.Offcanvas.getOrCreateInstance(offcanvasEl);
        instance.hide();
      }
      return;
    }

    if (!popin.dropdownClass) {
      return;
    }
    const toggleSelector = popin.toggleSelector || '[data-bs-toggle="dropdown"]';
    document.querySelectorAll('.' + popin.dropdownClass).forEach(function (el) {
      const toggle = el.querySelector(toggleSelector);
      if (toggle && typeof bootstrap !== 'undefined') {
        bootstrap.Dropdown.getOrCreateInstance(toggle).hide();
      }
    });
  };

  api._countSeq = 0;
  api._countSeqByPopin = {};

  /**
   * Whether an HTMX count response is older than the latest refresh request.
   *
   * @param {string} popinKey
   *   Registry key.
   * @param {XMLHttpRequest|null} xhr
   *   XHR from the HTMX event detail.
   *
   * @return {boolean}
   *   TRUE when the response must be ignored.
   */
  api.isStaleCountRequest = function (popinKey, xhr) {
    const responseUrl = xhr && xhr.responseURL ? xhr.responseURL : '';
    const match = responseUrl.match(/[?&]_ps_count_seq=(\d+)/);
    if (!match) {
      return false;
    }
    const reqSeq = parseInt(match[1], 10);
    if (!Number.isFinite(reqSeq) || reqSeq <= 0) {
      return false;
    }
    return reqSeq < (api._countSeqByPopin[popinKey] || 0);
  };

  /**
   * Refreshes a popin count label via HTMX fragment route.
   *
   * @param {string} popinKey
   *   Registry key.
   * @param {string} queryString
   *   URL-encoded filter query (without leading "?").
   *
   * @return {boolean}
   *   TRUE when an HTMX request was started.
   */
  /**
   * Refreshes the live count for the active popin, or a default target.
   *
   * @param {string} queryString
   *   URL-encoded filter query (without leading "?").
   *
   * @return {boolean}
   *   TRUE when an HTMX request was started.
   */
  api.refreshGlobalCount = function (queryString) {
    const openKey = api.getOpenHtmxPopinKey();
    const popinKey = (openKey && api.isHtmxPopin(openKey)) ? openKey : 'type';
    return api.refreshCount(popinKey, queryString);
  };

  api.refreshCount = function (popinKey, queryString) {
    const popin = api.getPopin(popinKey);
    if (!popin || !api.isAvailable()) {
      return false;
    }

    const target = document.getElementById(popin.targetId);
    if (!target) {
      return false;
    }

    const seq = ++api._countSeq;
    api._countSeqByPopin[popinKey] = seq;

    const baseUrl = api.settings.countUrl || '/api/ps/htmx/count-label';
    const params = new URLSearchParams(queryString || '');
    params.set('_ps_count_seq', String(seq));
    const url = baseUrl + '?' + params.toString();
    htmx.ajax('GET', url, {
      target: '#' + popin.targetId,
      swap: 'innerHTML',
    });
    return true;
  };

  /**
   * Lazy-loads a More criteria group fragment via HTMX.
   *
   * @param {string} groupId
   *   Group key (e.g. "equipements").
   * @param {HTMLElement} targetEl
   *   Swap target container.
   * @param {string} [queryString]
   *   URL-encoded query without leading "?".
   *
   * @return {Promise<void>}
   *   Resolves after successful swap.
   */
  api.loadMoreCriteriaGroup = function (groupId, targetEl, queryString) {
    if (!groupId || !targetEl || !api.isAvailable()) {
      return Promise.reject(new Error('HTMX more criteria unavailable'));
    }

    const baseUrl = (api.settings.moreCriteriaGroupUrl || '/api/ps/htmx/more-criteria')
      + '/' + encodeURIComponent(groupId);
    const url = queryString ? baseUrl + '?' + queryString : baseUrl;

    return new Promise(function (resolve, reject) {
      const onSwap = function (event) {
        if (event.detail.target !== targetEl) {
          return;
        }
        const xhr = event.detail.xhr;
        const responseUrl = xhr && xhr.responseURL ? xhr.responseURL : '';
        if (responseUrl.indexOf('/api/ps/htmx/more-criteria/') === -1) {
          return;
        }
        document.body.removeEventListener('htmx:afterSwap', onSwap);
        document.body.removeEventListener('htmx:responseError', onError);
        resolve();
      };

      const onError = function (event) {
        if (event.detail.target !== targetEl) {
          return;
        }
        document.body.removeEventListener('htmx:afterSwap', onSwap);
        document.body.removeEventListener('htmx:responseError', onError);
        reject(new Error('HTMX more criteria load failed'));
      };

      document.body.addEventListener('htmx:afterSwap', onSwap);
      document.body.addEventListener('htmx:responseError', onError);

      htmx.ajax('GET', url, {
        target: targetEl,
        swap: 'beforeend',
      });
    });
  };

  /**
   * Refreshes the search results pane header via HTMX fragment route.
   *
   * @param {string} queryString
   *   URL-encoded filter query (without leading "?").
   *
   * @return {Promise<HTMLElement>}
   *   Resolves with the swapped target element.
   */
  api.refreshResultsHeader = function (queryString) {
    const targetId = api.settings.resultsHeaderTargetId || 'ps-search-results-header';
    const target = document.getElementById(targetId);
    if (!target || !api.isAvailable()) {
      return Promise.reject(new Error('HTMX results header unavailable'));
    }

    const baseUrl = api.settings.resultsHeaderUrl || '/api/ps/htmx/results-header';
    const params = new URLSearchParams(queryString || '');
    const url = params.toString() ? baseUrl + '?' + params.toString() : baseUrl;

    return new Promise(function (resolve, reject) {
      const onSwap = function (event) {
        if (event.detail.target !== target) {
          return;
        }
        const xhr = event.detail.xhr;
        const responseUrl = xhr && xhr.responseURL ? xhr.responseURL : '';
        if (responseUrl.indexOf('/api/ps/htmx/results-header') === -1) {
          return;
        }
        document.body.removeEventListener('htmx:afterSwap', onSwap);
        document.body.removeEventListener('htmx:responseError', onError);
        resolve(target);
      };

      const onError = function (event) {
        if (event.detail.target !== target) {
          return;
        }
        document.body.removeEventListener('htmx:afterSwap', onSwap);
        document.body.removeEventListener('htmx:responseError', onError);
        reject(new Error('HTMX results header refresh failed'));
      };

      document.body.addEventListener('htmx:afterSwap', onSwap);
      document.body.addEventListener('htmx:responseError', onError);

      htmx.ajax('GET', url, {
        target: '#' + targetId,
        swap: 'innerHTML',
      });
    });
  };

  /**
   * Dispatches count update event and optional callback.
   *
   * @param {string} popinKey
   * @param {string} targetId
   * @param {number|null} count
   */
  api.notifyCountUpdated = function (popinKey, targetId, count) {
    document.dispatchEvent(new CustomEvent('ps-search-filter-htmx-count-updated', {
      detail: {
        popinKey: popinKey,
        targetId: targetId,
        count: count,
      },
    }));

    if (typeof api.callbacks.onCountUpdated === 'function') {
      api.callbacks.onCountUpdated(count, popinKey, targetId);
    }
  };

  Drupal.behaviors.psSearchFilterHtmx = {
    attach(context) {
      api.init(drupalSettings.psSearchFilterHtmx);

      once('ps-search-filter-htmx-events', 'body', context).forEach(function (body) {
        body.addEventListener('ps-search-filter-htmx-apply', function (event) {
          if (typeof api.callbacks.onApply === 'function') {
            api.callbacks.onApply(event.detail || {});
          }
        });

        body.addEventListener('htmx:beforeSwap', function (event) {
          const target = event.detail.target;
          if (!target || !target.id) {
            return;
          }

          const popinKey = api.resolvePopinKeyByTargetId(target.id);
          if (!popinKey) {
            return;
          }

          if (api.isStaleCountRequest(popinKey, event.detail.xhr)) {
            event.preventDefault();
          }
        });

        body.addEventListener('htmx:afterSwap', function (event) {
          const target = event.detail.target;
          if (!target || !target.id) {
            return;
          }

          const popinKey = api.resolvePopinKeyByTargetId(target.id);
          if (!popinKey) {
            return;
          }

          if (api.isStaleCountRequest(popinKey, event.detail.xhr)) {
            return;
          }

          const parsed = parseInt(String(target.textContent || '').trim(), 10);
          const count = Number.isFinite(parsed) ? parsed : null;
          api.notifyCountUpdated(popinKey, target.id, count);
        });

        body.addEventListener('htmx:afterSettle', function (event) {
          const xhr = event.detail.xhr;
          const responseUrl = xhr && xhr.responseURL ? xhr.responseURL : '';
          if (responseUrl.indexOf('/api/ps/htmx/count-label') === -1) {
            return;
          }
          if (typeof api.callbacks.setLoading === 'function') {
            api.callbacks.setLoading(false);
          }
        });

        body.addEventListener('htmx:responseError', function (event) {
          const target = event.detail.target;
          if (target && target.id && api.resolvePopinKeyByTargetId(target.id)) {
            if (typeof api.callbacks.setLoading === 'function') {
              api.callbacks.setLoading(false);
            }
            return;
          }
          if (typeof api.callbacks.setLoading === 'function') {
            api.callbacks.setLoading(false);
          }
        });
      });
    },
  };
}(Drupal, drupalSettings, once));
