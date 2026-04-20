(function (Drupal, once, drupalSettings) {
  'use strict';

  const STORAGE_KEY = 'ps_search_viewed_offer_ids';

  function toNumber(value) {
    const parsed = Number.parseInt(String(value || ''), 10);
    return Number.isNaN(parsed) ? 0 : parsed;
  }

  function readViewedIds() {
    try {
      const raw = window.localStorage.getItem(STORAGE_KEY);
      const parsed = raw ? JSON.parse(raw) : [];
      if (!Array.isArray(parsed)) {
        return [];
      }
      return parsed.map(toNumber).filter((id) => id > 0);
    }
    catch (error) {
      return [];
    }
  }

  function writeViewedIds(ids) {
    const unique = Array.from(new Set(ids.map(toNumber).filter((id) => id > 0)));
    try {
      window.localStorage.setItem(STORAGE_KEY, JSON.stringify(unique));
    }
    catch (error) {
      // Ignore storage errors and keep progressive behavior.
    }
    return unique;
  }

  function mergeViewedIdsFromSettings() {
    const settings = drupalSettings.psSearchCardSearch || {};
    const incoming = Array.isArray(settings.viewedOfferIds) ? settings.viewedOfferIds : [];
    if (incoming.length === 0) {
      return readViewedIds();
    }

    const current = readViewedIds();
    return writeViewedIds(current.concat(incoming));
  }

  function revealViewedBadges(viewedIds, context) {
    const viewedSet = new Set(viewedIds);

    once('ps-offer-viewed-card', '.ps-card-offer-search[data-offer-id]', context).forEach((card) => {
      const offerId = toNumber(card.getAttribute('data-offer-id'));
      if (!viewedSet.has(offerId)) {
        return;
      }

      card.classList.add('is-viewed');
      const badge = card.querySelector('.js-ps-offer-viewed-badge');
      if (badge) {
        badge.classList.remove('is-hidden');
      }
    });
  }

  function setComparatorState(link, isActive) {
    link.classList.toggle('is-active', isActive);
    link.setAttribute('aria-pressed', isActive ? 'true' : 'false');
  }

  function bindComparatorLinks(context) {
    once('ps-offer-compare-link', '.js-ps-offer-compare', context).forEach((link) => {
      link.addEventListener('click', (event) => {
        event.preventDefault();

        const endpoint = (link.getAttribute('data-ps-compare-url') || '').trim();
        const nextState = link.getAttribute('aria-pressed') !== 'true';

        if (!endpoint) {
          setComparatorState(link, nextState);
          return;
        }

        fetch(endpoint, {
          method: 'POST',
          credentials: 'same-origin',
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            Accept: 'application/json',
          },
        })
          .then((response) => {
            if (!response.ok) {
              throw new Error('Comparator endpoint error');
            }
            return response.json();
          })
          .then(() => {
            setComparatorState(link, nextState);
          })
          .catch(() => {
            // Keep UI responsive even while compare backend is pending.
            setComparatorState(link, nextState);
          });
      });
    });
  }

  function getMapIds() {
    const settings = drupalSettings.geofield_google_map || {};
    return Object.keys(settings);
  }

  function getMapData(mapId) {
    if (!Drupal.geoFieldMapFormatter || !Drupal.geoFieldMapFormatter.map_data) {
      return null;
    }

    return Drupal.geoFieldMapFormatter.map_data[mapId] || null;
  }

  function bindCardMapHover(context) {
    const viewRoot = context.querySelector && context.querySelector('.ps-offer-search-view')
      ? context.querySelector('.ps-offer-search-view')
      : document.querySelector('.ps-offer-search-view');

    if (!viewRoot) {
      return;
    }

    const cards = Array.from(viewRoot.querySelectorAll('.ps-card-offer-search[data-offer-id]'));
    if (cards.length === 0) {
      return;
    }

    const cardsById = new Map();
    cards.forEach((card) => {
      const id = toNumber(card.getAttribute('data-offer-id'));
      if (id > 0) {
        cardsById.set(String(id), card);
      }
    });

    const markerState = {
      activeId: null,
      markersById: new Map(),
    };

    function readPriceLabel(card) {
      const valueElement = card.querySelector('.ps-card-offer-search__price-value');
      if (!valueElement) {
        return '';
      }

      let label = (valueElement.textContent || '').replace(/\s+/g, ' ').trim();
      if (!label || /request/i.test(label)) {
        return '';
      }

      if (/\d/.test(label) && !/[\u20ac$£]/.test(label)) {
        label += ' \u20ac';
      }

      return label;
    }

    function escapeSvgText(value) {
      return String(value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
    }

    function buildGreenDotIcon() {
      if (typeof google === 'undefined' || !google.maps) {
        return null;
      }

      const size = 18;
      const cx = 9;
      const svg = '<svg xmlns="http://www.w3.org/2000/svg" width="' + size + '" height="' + size + '">' +
        '<circle cx="' + cx + '" cy="' + cx + '" r="' + cx + '" fill="#009258" fill-opacity="0.22"/>' +
        '<circle cx="' + cx + '" cy="' + cx + '" r="7" fill="#009258"/>' +
        '</svg>';

      return {
        url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(svg),
        scaledSize: new google.maps.Size(size, size),
        anchor: new google.maps.Point(cx, cx),
      };
    }

    function buildPriceMarkerIcon(priceLabel, isHover) {
      if (typeof google === 'undefined' || !google.maps || !priceLabel) {
        return null;
      }

      const safeLabel = escapeSvgText(priceLabel);
      const W = Math.max(74, Math.min(132, 30 + safeLabel.length * 11));
      const H = 32;
      const tw = 14;
      const th = 10;
      const dotR = 6;
      const dotGap = 3;
      const cx = Math.round(W / 2);
      const svgHeight = H + th + dotGap + dotR * 2;
      const dotCy = H + th + dotGap + dotR;

      const bgFill = isHover ? '#009258' : '#EAF7F1';
      const textFill = isHover ? '#ffffff' : '#009258';
      const strokeColor = '#009258';

      // Single compound path: sharp-corner rect + triangle pointer, no internal seam.
      const halfTw = Math.round(tw / 2);
      const path =
        'M 1,1 ' +
        'H ' + (W - 1) + ' ' +
        'V ' + (H - 1) + ' ' +
        'H ' + (cx + halfTw) + ' ' +
        'L ' + cx + ',' + (H - 1 + th) + ' ' +
        'L ' + (cx - halfTw) + ',' + (H - 1) + ' ' +
        'H 1 Z';

      const svg =
        '<svg xmlns="http://www.w3.org/2000/svg" width="' + W + '" height="' + svgHeight + '" viewBox="0 0 ' + W + ' ' + svgHeight + '" fill="none">' +
        '<path d="' + path + '" fill="' + bgFill + '" stroke="' + strokeColor + '" stroke-width="2"/>' +
        '<circle cx="' + cx + '" cy="' + dotCy + '" r="' + dotR + '" fill="' + strokeColor + '"/>' +
        '<text x="50%" y="' + Math.round(H * 0.66) + '" fill="' + textFill + '" font-family="BNPP Sans, Arial, sans-serif" font-size="14" font-weight="700" text-anchor="middle">' + safeLabel + '</text>' +
        '</svg>';

      return {
        url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(svg),
        scaledSize: new google.maps.Size(W, svgHeight),
        anchor: new google.maps.Point(cx, dotCy),
      };
    }

    function clearCardHighlight() {
      cards.forEach((card) => card.classList.remove('is-map-linked-hover'));
    }

    function applyCardHighlight(offerId) {
      clearCardHighlight();
      const card = cardsById.get(String(offerId));
      if (card) {
        card.classList.add('is-map-linked-hover');
      }
    }

    function applyMarkerHighlight(activeId) {
      markerState.activeId = String(activeId || '');

      markerState.markersById.forEach((marker, markerId) => {
        const isActive = markerId === markerState.activeId;
        const hasPrice = !!marker.__psPriceLabel;

        if (marker && typeof marker.setIcon === 'function' && hasPrice) {
          marker.setIcon(buildPriceMarkerIcon(marker.__psPriceLabel, isActive));
        }

        if (marker && typeof marker.setOpacity === 'function') {
          marker.setOpacity(!isActive && markerState.activeId ? 0.45 : 1);
        }

        if (marker && typeof marker.setZIndex === 'function') {
          marker.setZIndex(isActive ? 999 : undefined);
        }
      });
    }

    function clearHighlights() {
      markerState.activeId = null;
      clearCardHighlight();
      applyMarkerHighlight(null);
    }

    function buildClusterSvg(size, radius) {
      const outerRadius = Math.round(size / 2);
      const cx = outerRadius;
      const cy = outerRadius;
      return '<svg xmlns="http://www.w3.org/2000/svg" width="' + size + '" height="' + size + '">' +
        '<circle cx="' + cx + '" cy="' + cy + '" r="' + outerRadius + '" fill="#009258" fill-opacity="0.22"/>' +
        '<circle cx="' + cx + '" cy="' + cy + '" r="' + radius + '" fill="#009258"/>' +
        '</svg>';
    }

    function applyGreenClusterStyles(cluster) {
      if (!cluster || cluster.__psClusterStyled) {
        return;
      }

      const clusterStyles = [
        { size: 44, innerRadius: 15 },
        { size: 50, innerRadius: 18 },
        { size: 56, innerRadius: 21 },
      ].map((spec) => ({
        url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(buildClusterSvg(spec.size, spec.innerRadius)),
        width: spec.size,
        height: spec.size,
        textColor: '#ffffff',
        textSize: 13,
        fontWeight: 'bold',
        fontFamily: 'BNPP Sans, Arial, sans-serif',
        textLineHeight: spec.size,
        backgroundPosition: '0 0',
      }));

      if (typeof cluster.setStyles === 'function') {
        cluster.setStyles(clusterStyles);
        cluster.__psClusterStyled = true;
      }

      if (typeof cluster.repaint === 'function') {
        cluster.repaint();
      }
    }

    function registerMarkerBindings() {
      const mapIds = getMapIds();
      if (mapIds.length === 0) {
        return;
      }

      markerState.markersById.clear();

      mapIds.forEach((mapId) => {
        const mapData = getMapData(mapId);

        if (mapData && mapData.markerCluster) {
          applyGreenClusterStyles(mapData.markerCluster);
        }
        if (!mapData || !mapData.markers) {
          return;
        }

        Object.keys(mapData.markers).forEach((rawKey) => {
          const marker = mapData.markers[rawKey];
          const offerId = String(rawKey).split('-')[0];

          if (!cardsById.has(offerId) || !marker || marker.__psHoverBound) {
            return;
          }

          const card = cardsById.get(offerId);
          const priceLabel = card ? readPriceLabel(card) : '';
          if (typeof marker.setIcon === 'function' && !marker.__psPriceIconApplied) {
            const customIcon = priceLabel
              ? buildPriceMarkerIcon(priceLabel, false)
              : buildGreenDotIcon();
            if (customIcon) {
              marker.setIcon(customIcon);
              marker.__psPriceIconApplied = true;
              if (priceLabel) {
                marker.__psPriceLabel = priceLabel;
              }
            }
          }

          markerState.markersById.set(offerId, marker);
          marker.__psHoverBound = true;

          if (typeof google !== 'undefined' && google.maps && google.maps.event) {
            google.maps.event.addListener(marker, 'mouseover', () => {
              applyCardHighlight(offerId);
              applyMarkerHighlight(offerId);
            });

            google.maps.event.addListener(marker, 'mouseout', () => {
              clearHighlights();
            });
          }
        });
      });

      return markerState.markersById.size > 0;
    }

    once('ps-offer-card-map-hover-card', '.ps-card-offer-search[data-offer-id]', viewRoot).forEach((card) => {
      const offerId = String(toNumber(card.getAttribute('data-offer-id')));

      card.addEventListener('mouseenter', () => {
        applyCardHighlight(offerId);
        applyMarkerHighlight(offerId);
      });

      card.addEventListener('mouseleave', () => {
        clearHighlights();
      });
    });

    if (!registerMarkerBindings()) {
      let attempts = 0;
      const maxAttempts = 20;
      const retryTimer = window.setInterval(() => {
        attempts += 1;
        const ready = registerMarkerBindings();
        if (ready || attempts >= maxAttempts) {
          window.clearInterval(retryTimer);
        }
      }, 500);
    }
  }

  function bindMoreFiltersToggle(context) {
    once('ps-offer-more-filters', '.ps-offer-search-exposed', context).forEach((form) => {
      const closedLabel = 'Plus de filtres';
      const openLabel = 'Moins de filtres';
      let toggle = form.querySelector('.ps-offer-search-more-filters-toggle');
      if (!toggle) {
        const actions = form.querySelector(':scope > .form-actions');
        if (!actions) {
          return;
        }

        toggle = document.createElement('button');
        toggle.type = 'button';
        toggle.className = 'button ps-offer-search-more-filters-toggle';
        toggle.textContent = closedLabel;
        toggle.setAttribute('aria-expanded', 'false');
        actions.insertBefore(toggle, actions.firstChild);
      }

      function findTopLevelFilterByInputName(inputName) {
        const input = form.querySelector('[name="' + inputName + '"]');
        if (!input) {
          return null;
        }

        let node = input;
        while (node && node.parentElement !== form) {
          node = node.parentElement;
        }

        return node && node.parentElement === form ? node : null;
      }

      const surfaceFilter = findTopLevelFilterByInputName('surface[min]');
      const priceFilter = findTopLevelFilterByInputName('price[min]');
      const secondaryFilters = [surfaceFilter, priceFilter].filter(Boolean);

      if (secondaryFilters.length === 0) {
        toggle.style.display = 'none';
        return;
      }

      const syncToggleState = (expanded) => {
        toggle.setAttribute('aria-expanded', expanded ? 'true' : 'false');
        toggle.textContent = expanded ? openLabel : closedLabel;
        toggle.setAttribute('title', expanded ? openLabel : closedLabel);
      };

      if (surfaceFilter) {
        surfaceFilter.classList.add('ps-offer-search-exposed__surface');
      }
      if (priceFilter) {
        priceFilter.classList.add('ps-offer-search-exposed__price');
      }

      secondaryFilters.forEach((filter) => {
        filter.classList.add('ps-offer-search-exposed__secondary-filter');
        filter.classList.add('is-collapsed');
      });

      syncToggleState(false);

      toggle.addEventListener('click', () => {
        const expanded = toggle.getAttribute('aria-expanded') === 'true';
        const nextExpanded = !expanded;

        syncToggleState(nextExpanded);
        form.classList.toggle('ps-offer-search-exposed--more-open', nextExpanded);

        secondaryFilters.forEach((filter) => {
          filter.classList.toggle('is-collapsed', !nextExpanded);
        });
      });
    });
  }
  const PS_PROPERTY_FALLBACK_ICONS = {
    BUR: 'offices',
    COW: 'coworking',
    LOG: 'logistic-warehouses',
    COM: 'shops',
    RES: 'common-areas',
    TER: 'terrain',
    ACT: 'settings',
  };

  const PS_TRANSACTION_FALLBACK_LABELS = {
    VEN: Drupal.t('Buy'),
    LOC: Drupal.t('Rent'),
  };

  function sanitizeIconId(iconId) {
    const token = String(iconId || '').trim().toLowerCase();
    return /^[a-z0-9-]+$/.test(token) ? token : '';
  }

  function parseUiOptions(rawOptions) {
    let parsed = {};
    try {
      parsed = JSON.parse(rawOptions || '{}');
    }
    catch (e) {
      parsed = {};
    }

    const options = {};
    Object.keys(parsed).forEach((code) => {
      const value = parsed[code];
      if (typeof value === 'string') {
        options[code] = {
          label: value,
          uiLabel: value,
          icon: '',
          visible: true,
        };
        return;
      }

      if (value && typeof value === 'object') {
        options[code] = {
          label: value.label || code,
          uiLabel: value.ui_label || value.label || code,
          icon: value.icon || '',
          visible: value.visible === undefined ? true : String(value.visible) !== '0',
        };
      }
    });

    return options;
  }

  function extractResultsCountFromRoot(root) {
    if (!root) {
      return null;
    }

    const candidates = [
      root.querySelector('.ps-offer-search-view__header'),
      root.querySelector('.ps-offer-search-view__map [class*="result"]'),
      root.querySelector('.result'),
    ].filter(Boolean);

    for (const node of candidates) {
      const text = (node.textContent || '').replace(/\u00a0/g, ' ').trim();
      const match = text.match(/(\d[\d\s]*)/);
      if (!match) {
        continue;
      }
      const normalized = match[1].replace(/\s+/g, '');
      const count = Number.parseInt(normalized, 10);
      if (!Number.isNaN(count)) {
        return count;
      }
    }

    return null;
  }

  function extractResultsCountFromGeofieldSettings(settings) {
    if (!settings || typeof settings !== 'object') {
      return null;
    }

    const entries = Object.values(settings);
    if (entries.length === 0) {
      return null;
    }

    let best = null;
    entries.forEach((entry) => {
      const features = entry && entry.data && Array.isArray(entry.data.features)
        ? entry.data.features
        : null;
      if (!features) {
        return;
      }
      const count = features.length;
      if (best === null || count > best) {
        best = count;
      }
    });

    return best;
  }

  function readResultsCountFromRuntimeSettings() {
    const settings = drupalSettings.geofield_google_map || {};
    return extractResultsCountFromGeofieldSettings(settings);
  }

  function readResultsCountFromHtmlDocument(doc) {
    if (!doc) {
      return null;
    }

    const settingsNode = doc.querySelector('#drupal-settings-json');
    if (!settingsNode) {
      return null;
    }

    try {
      const payload = JSON.parse(settingsNode.textContent || '{}');
      return extractResultsCountFromGeofieldSettings(payload.geofield_google_map || {});
    }
    catch (error) {
      return null;
    }
  }

  function readResultsCount(form) {
    const view = form.closest('.ps-offer-search-view') || document.querySelector('.ps-offer-search-view');
    const fromView = extractResultsCountFromRoot(view);
    if (fromView !== null) {
      return fromView;
    }

    return readResultsCountFromRuntimeSettings();
  }

  function getSharedResultsCtaElements(form) {
    const nodes = [
      ...form.querySelectorAll('.ps-filter-panel__apply'),
      ...form.querySelectorAll('.ps-offer-more-filters__apply'),
    ];

    const submit = form.querySelector('.ps-offer-search-bar__submit');
    if (submit) {
      nodes.push(submit);
    }

    return Array.from(new Set(nodes));
  }

  function setSharedResultsCtaLabel(form, label) {
    getSharedResultsCtaElements(form).forEach((el) => {
      if (el.tagName === 'INPUT') {
        el.value = label;
      }
      else {
        el.textContent = label;
      }
    });
  }

  function setSharedResultsCtaLoading(form, isLoading) {
    form.__psCtaLoading = !!isLoading;
    const loadingLabel = Drupal.t('Loading...');

    getSharedResultsCtaElements(form).forEach((el) => {
      el.classList.toggle('is-loading', !!isLoading);
      el.setAttribute('aria-busy', isLoading ? 'true' : 'false');

      if (el.tagName !== 'INPUT') {
        el.classList.toggle('d-inline-flex', !!isLoading);
        el.classList.toggle('justify-content-center', !!isLoading);
        el.classList.toggle('align-items-center', !!isLoading);
        if (isLoading) {
          el.style.display = 'inline-flex';
          el.style.justifyContent = 'center';
          el.style.alignItems = 'center';
        }
        else {
          el.style.removeProperty('display');
          el.style.removeProperty('justify-content');
          el.style.removeProperty('align-items');
        }
      }

      if (el.tagName === 'BUTTON' || el.tagName === 'INPUT') {
        el.disabled = !!isLoading;
      }

      if (el.tagName !== 'INPUT') {
        if (isLoading) {
          el.innerHTML = '<span class="spinner-border spinner-border-sm" aria-hidden="true"></span>';
        }
      }
    });

    if (isLoading) {
      getSharedResultsCtaElements(form).forEach((el) => {
        if (el.tagName === 'INPUT') {
          el.value = loadingLabel;
        }
      });
    }
  }

  function updateResultsCta(form) {
    if (form.__psCtaLoading) {
      const loadingLabel = Drupal.t('Loading...');
      getSharedResultsCtaElements(form).forEach((el) => {
        if (el.tagName === 'INPUT') {
          el.value = loadingLabel;
        }
      });
      return;
    }

    const count = Number.isInteger(form.__psPreviewCount) ? form.__psPreviewCount : readResultsCount(form);
    const label = count === null
      ? Drupal.t('Show results')
      : Drupal.formatPlural(count, 'Show 1 result', 'Show @count results');

    setSharedResultsCtaLabel(form, label);
  }

  function submitResultsFromCta(form, delay = 0) {
    if (!form) {
      return;
    }

    if (form.__psCtaLoading) {
      return;
    }

    if (form.__psRefreshTimer) {
      window.clearTimeout(form.__psRefreshTimer);
    }

    form.__psRefreshTimer = window.setTimeout(() => {
      setSharedResultsCtaLoading(form, false);
      delete form.__psPreviewCount;
      const submit = form.querySelector('.ps-offer-search-bar__submit');
      if (submit) {
        submit.click();
      }

      // Ajax update is async; refresh CTA label shortly after request.
      window.setTimeout(() => updateResultsCta(form), 500);
      window.setTimeout(() => updateResultsCta(form), 1000);
    }, delay);
  }

  function buildSearchPreviewUrl(form) {
    const url = new URL(window.location.href);
    const params = new URLSearchParams(new FormData(form));

    // Ignore Drupal form internals for preview reads.
    ['form_build_id', 'form_id', 'form_token', 'op', '_drupal_ajax'].forEach((key) => {
      params.delete(key);
    });

    url.search = params.toString();
    return url.toString();
  }

  function requestResultsCountPreview(form, delay = 250) {
    if (!form) {
      return;
    }

    setSharedResultsCtaLoading(form, true);

    if (form.__psCountTimer) {
      window.clearTimeout(form.__psCountTimer);
    }

    form.__psCountTimer = window.setTimeout(() => {
      if (form.__psCountAbortController) {
        form.__psCountAbortController.abort();
      }

      const requestId = (form.__psCountRequestId || 0) + 1;
      form.__psCountRequestId = requestId;

      const controller = new AbortController();
      form.__psCountAbortController = controller;

      fetch(buildSearchPreviewUrl(form), {
        method: 'GET',
        credentials: 'same-origin',
        signal: controller.signal,
        headers: {
          Accept: 'text/html',
        },
      })
        .then((response) => {
          if (!response.ok) {
            throw new Error('Preview count request failed');
          }
          return response.text();
        })
        .then((html) => {
          if (requestId !== form.__psCountRequestId) {
            return;
          }

          const parser = new DOMParser();
          const doc = parser.parseFromString(html, 'text/html');
          const previewRoot = doc.querySelector('.ps-offer-search-view') || doc;
          const count = extractResultsCountFromRoot(previewRoot);
          const fallbackCount = count === null ? readResultsCountFromHtmlDocument(doc) : count;

          if (fallbackCount !== null) {
            form.__psPreviewCount = fallbackCount;
          }
          else {
            delete form.__psPreviewCount;
          }
        })
        .catch((error) => {
          if (error && error.name === 'AbortError') {
            return;
          }
        })
        .finally(() => {
          if (requestId !== form.__psCountRequestId) {
            return;
          }

          setSharedResultsCtaLoading(form, false);
          updateResultsCta(form);
        });
    }, delay);
  }

  /**
   * Close all open dropdown panels within a form.
   *
   * @param {HTMLElement} form
   * @param {HTMLElement|null} except - Panel to skip (stay open).
   */
  function closePanels(form, except) {
    form.querySelectorAll('[data-ps-panel]').forEach((panel) => {
      if (panel === except) {
        return;
      }
      const trigger = panel.querySelector(':scope > .ps-filter-panel__trigger');
      const content = panel.querySelector(':scope > .ps-filter-panel__content');
      if (trigger) {
        trigger.setAttribute('aria-expanded', 'false');
      }
      if (content) {
        content.setAttribute('hidden', '');
      }
      panel.classList.remove('is-open');
    });
  }

  /**
   * Open a single dropdown panel.
   *
   * @param {HTMLElement} panel
   */
  function openPanel(panel) {
    const trigger = panel.querySelector(':scope > .ps-filter-panel__trigger');
    const content = panel.querySelector(':scope > .ps-filter-panel__content');
    if (trigger) {
      trigger.setAttribute('aria-expanded', 'true');
    }
    if (content) {
      content.removeAttribute('hidden');
      // Align right if panel overflows viewport.
      const rect = content.getBoundingClientRect();
      if (rect.right > (window.innerWidth - 8)) {
        content.classList.add('ps-filter-panel__content--align-right');
      }
      else {
        content.classList.remove('ps-filter-panel__content--align-right');
      }
    }
    panel.classList.add('is-open');
  }

  /**
   * Build a property-type icon grid that mirrors the hidden native select.
   *
   * @param {HTMLElement} panel
   * @param {HTMLElement} form
   */
  function buildPropertyTypeGrid(panel, form) {
    const nativeInput = panel.querySelector('[data-ps-filter="property-type"]');
    if (!nativeInput) {
      return;
    }

    const options = parseUiOptions(nativeInput.getAttribute('data-ps-options'));

    const codes = Object.keys(options).filter((code) => options[code].visible);
    if (codes.length === 0) {
      return;
    }

    // Hide native element.
    const nativeWrapper = nativeInput.closest('.form-item') || nativeInput;
    nativeWrapper.classList.add('js-enhanced');
    nativeWrapper.setAttribute('aria-hidden', 'true');
    nativeWrapper.setAttribute('hidden', 'hidden');
    nativeWrapper.style.display = 'none';

    // Read current value from URL / form.
    function getCurrentValues() {
      const current = nativeInput.value || '';
      return current ? current.split(',').map((s) => s.trim()) : [];
    }

    function selectSingleCode(code) {
      const nextCode = code || '';
      grid.querySelectorAll('.ps-property-type-tile').forEach((tile) => {
        const tileCode = tile.getAttribute('data-ps-code') || '';
        const selected = nextCode !== '' && tileCode === nextCode;
        tile.classList.toggle('is-selected', selected);
        tile.setAttribute('aria-pressed', selected ? 'true' : 'false');
      });
      nativeInput.value = nextCode;
    }

    // Build grid.
    const grid = document.createElement('div');
    grid.className = 'ps-property-type-grid';
    grid.setAttribute('role', 'group');
    grid.setAttribute('aria-label', Drupal.t('Property type'));

    codes.forEach((code) => {
      const option = options[code] || {};
      const label = option.uiLabel || option.label || code;
      const iconName = sanitizeIconId(option.icon || PS_PROPERTY_FALLBACK_ICONS[code] || '');

      const tile = document.createElement('button');
      tile.type = 'button';
      tile.className = 'ps-property-type-tile';
      tile.setAttribute('data-ps-code', code);
      tile.setAttribute('aria-pressed', 'false');

      const iconEl = document.createElement('span');
      iconEl.className = 'ps-property-type-tile__icon';
      iconEl.setAttribute('aria-hidden', 'true');
      if (iconName) {
        const iconNode = document.createElement('span');
        iconNode.className = 'ps-icon';
        iconNode.setAttribute('data-icon', iconName);
        iconNode.setAttribute('aria-hidden', 'true');
        iconEl.appendChild(iconNode);
      }
      else {
        iconEl.textContent = '\u25A1';
      }

      const labelEl = document.createElement('span');
      labelEl.className = 'ps-property-type-tile__label';
      labelEl.textContent = label;

      tile.appendChild(iconEl);
      tile.appendChild(labelEl);
      grid.appendChild(tile);

      tile.addEventListener('click', () => {
        const isSelected = tile.classList.contains('is-selected');
        const nextCode = isSelected ? '' : (tile.getAttribute('data-ps-code') || '');
        selectSingleCode(nextCode);

        // Update trigger label.
        updatePanelTriggerLabel(panel);
        requestResultsCountPreview(form, 120);
      });
    });

    // Restore pre-selected value (single selection only).
    const initialValues = getCurrentValues();
    const initialCode = initialValues.find((code) => codes.includes(code)) || '';
    selectSingleCode(initialCode);

    // Insert before the footer (apply button).
    const footer = panel.querySelector('.ps-filter-panel__footer');
    const content = panel.querySelector('.ps-filter-panel__content');
    if (content) {
      content.insertBefore(grid, footer || null);
    }
  }

  /**
   * Build transaction-type segmented buttons in property panel.
   *
   * @param {HTMLElement} form
   * @param {HTMLElement} propertyPanel
   */
  function buildTransactionTypeButtons(form, propertyPanel) {
    const nativeInput = form.querySelector('[data-ps-filter="transaction-type"]');
    if (!nativeInput || !propertyPanel) {
      return;
    }

    const options = parseUiOptions(nativeInput.getAttribute('data-ps-options'));
    const codes = Object.keys(options).filter((code) => options[code].visible);
    if (codes.length === 0) {
      return;
    }

    const nativeWrapper = nativeInput.closest('.form-item') || nativeInput;
    nativeWrapper.classList.add('js-enhanced');
    nativeWrapper.setAttribute('aria-hidden', 'true');
    nativeWrapper.setAttribute('hidden', 'hidden');
    nativeWrapper.style.display = 'none';

    const section = document.createElement('div');
    section.className = 'ps-transaction-type';

    const heading = document.createElement('h3');
    heading.className = 'ps-transaction-type__title';
    heading.textContent = Drupal.t('Transaction type');
    section.appendChild(heading);

    const group = document.createElement('div');
    group.className = 'ps-transaction-type__group';
    group.setAttribute('role', 'group');
    group.setAttribute('aria-label', Drupal.t('Transaction type'));

    const buttonDefs = codes.map((code) => ({
      code,
      label: (options[code].uiLabel || options[code].label || PS_TRANSACTION_FALLBACK_LABELS[code] || code),
    }));

    buttonDefs.push({
      code: '',
      label: Drupal.t("I'm flexible"),
    });

    function syncButtons() {
      const value = (nativeInput.value || '').trim();
      group.querySelectorAll('.ps-transaction-type__btn').forEach((btn) => {
        const code = btn.getAttribute('data-ps-code') || '';
        const selected = code === value || (value === '' && code === '');
        btn.classList.toggle('is-selected', selected);
        btn.setAttribute('aria-pressed', selected ? 'true' : 'false');
      });
    }

    buttonDefs.forEach((def) => {
      const btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'ps-transaction-type__btn';
      btn.setAttribute('data-ps-code', def.code);
      btn.setAttribute('aria-pressed', 'false');
      btn.textContent = def.label;

      btn.addEventListener('click', () => {
        nativeInput.value = def.code;
        syncButtons();
        updatePanelTriggerLabel(propertyPanel);
        requestResultsCountPreview(form, 120);
      });

      group.appendChild(btn);
    });

    section.appendChild(group);

    const footer = propertyPanel.querySelector('.ps-filter-panel__footer');
    const content = propertyPanel.querySelector('.ps-filter-panel__content');
    if (content) {
      content.insertBefore(section, footer || null);
    }

    syncButtons();
  }

  /**
   * Update a panel trigger to show an active-filter summary.
   *
   * @param {HTMLElement} panel
   */
  function updatePanelTriggerLabel(panel) {
    const panelId = panel.getAttribute('data-ps-panel');
    const trigger = panel.querySelector(':scope > .ps-filter-panel__trigger');
    if (!trigger || !panelId) {
      return;
    }

    const valueNode = trigger.querySelector('.ps-filter-panel__value');
    if (valueNode && !valueNode.dataset.defaultValue) {
      valueNode.dataset.defaultValue = (valueNode.textContent || '').trim();
    }

    let hasValue = false;

    // Property type: count selected tiles.
    if (panelId === 'property-type') {
      const selectedTile = panel.querySelector('.ps-property-type-tile.is-selected');
      const propertyLabel = selectedTile
        ? ((selectedTile.querySelector('.ps-property-type-tile__label')?.textContent || '').trim())
        : '';
      hasValue = propertyLabel !== '';

      const form = panel.closest('form');
      const transactionInput = form ? form.querySelector('[data-ps-filter="transaction-type"]') : null;
      let transactionLabel = '';

      if (transactionInput) {
        const txValue = (transactionInput.value || '').trim();
        if (txValue === '') {
          transactionLabel = Drupal.t("I'm flexible");
        }
        else {
          const txOptions = parseUiOptions(transactionInput.getAttribute('data-ps-options'));
          transactionLabel = (txOptions[txValue]?.uiLabel || txOptions[txValue]?.label || PS_TRANSACTION_FALLBACK_LABELS[txValue] || txValue);
        }
      }

      if (valueNode) {
        if (propertyLabel && transactionLabel) {
          valueNode.textContent = `${propertyLabel} ${Drupal.t('for')} ${String(transactionLabel).toLowerCase()}`;
        }
        else if (propertyLabel) {
          valueNode.textContent = propertyLabel;
        }
        else {
          valueNode.textContent = valueNode.dataset.defaultValue || '';
        }
      }
    }
    else {
      // Generic: check if any input/select in the panel has a non-empty value.
      panel.querySelectorAll('input:not([type=hidden]):not([type=submit]):not([type=button]), select').forEach((el) => {
        if (el.value && el.value.trim()) {
          hasValue = true;
        }
      });
    }

    panel.classList.toggle('is-active', hasValue);
  }

  /**
   * Bind dropdown panel open/close behavior for the offer search bar.
   *
   * @param {HTMLElement} context
   */
  function bindOfferSearchPanels(context) {
    once('ps-offer-search-panels', '.ps-offer-search-bar', context).forEach((form) => {
      const panels = Array.from(form.querySelectorAll('[data-ps-panel]'));
      if (panels.length === 0) {
        return;
      }

      // Enhance property type panel with icon grid.
      const propertyPanel = panels.find((p) => p.getAttribute('data-ps-panel') === 'property-type');
      if (propertyPanel) {
        buildPropertyTypeGrid(propertyPanel, form);
        buildTransactionTypeButtons(form, propertyPanel);
      }

      // Initialize active state from URL params.
      panels.forEach((panel) => updatePanelTriggerLabel(panel));
      updateResultsCta(form);

      // Bind trigger clicks.
      panels.forEach((panel) => {
        const trigger = panel.querySelector(':scope > .ps-filter-panel__trigger');
        if (!trigger) {
          return;
        }

        trigger.addEventListener('click', () => {
          const content = panel.querySelector(':scope > .ps-filter-panel__content');
          if (!content) {
            return;
          }

          const isOpen = trigger.getAttribute('aria-expanded') === 'true';
          closePanels(form, null);
          if (!isOpen) {
            openPanel(panel);
          }
        });

        trigger.addEventListener('keydown', (e) => {
          if (e.key !== 'Enter' && e.key !== ' ') {
            return;
          }
          e.preventDefault();
          trigger.click();
        });
      });

      // Bind "Show results" submit buttons inside panels.
      form.querySelectorAll('.ps-filter-panel__apply').forEach((btn) => {
        btn.addEventListener('click', (e) => {
          e.preventDefault();
          if (form.__psCtaLoading) {
            return;
          }
          // Close all panels before submitting.
          closePanels(form, null);
          submitResultsFromCta(form, 0);
        });
      });

      form.querySelectorAll('.ps-offer-more-filters__apply').forEach((btn) => {
        btn.addEventListener('click', (e) => {
          e.preventDefault();
          if (form.__psCtaLoading) {
            return;
          }
          closePanels(form, null);
          submitResultsFromCta(form, 0);
        });
      });

      // Bind value changes inside panels to update active state.
      form.addEventListener('change', (e) => {
        const panelEl = e.target.closest('[data-ps-panel]');
        if (panelEl) {
          updatePanelTriggerLabel(panelEl);
        }
        requestResultsCountPreview(form, 220);
      });

      form.addEventListener('input', (e) => {
        const target = e.target;
        if (!(target instanceof HTMLInputElement)) {
          return;
        }
        if (target.type === 'text' || target.type === 'search' || target.type === 'number') {
          requestResultsCountPreview(form, 420);
        }
      });

      // Close open panels when clicking outside the search bar.
      document.addEventListener('click', (e) => {
        if (!form.contains(e.target)) {
          closePanels(form, null);
        }
      }, { capture: false });

      // Close open panels on Escape key.
      document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
          closePanels(form, null);
        }
      });
    });
  }

  Drupal.behaviors.psSearchCardSearchTracking = {
    attach(context) {
      const viewedIds = mergeViewedIdsFromSettings();
      revealViewedBadges(viewedIds, context);
      bindComparatorLinks(context);
      bindCardMapHover(context);
      bindOfferSearchPanels(context);
    },
  };

})(Drupal, once, drupalSettings);
