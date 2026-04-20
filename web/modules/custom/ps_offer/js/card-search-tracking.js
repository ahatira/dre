(function (Drupal, once, drupalSettings) {
  'use strict';

  const STORAGE_KEY = 'ps_offer_viewed_ids';

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
    const settings = drupalSettings.psOfferCardSearch || {};
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

  Drupal.behaviors.psOfferCardSearchTracking = {
    attach(context) {
      const viewedIds = mergeViewedIdsFromSettings();
      revealViewedBadges(viewedIds, context);
      bindComparatorLinks(context);
      bindCardMapHover(context);
    },
  };
})(Drupal, once, drupalSettings);
