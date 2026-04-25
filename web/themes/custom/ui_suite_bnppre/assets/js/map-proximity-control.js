/**
 * Map Proximity Control - Floating controls on map for proximity search
 *
 * Features:
 * - Viewport-driven radius (minimum 20km)
 * - Auto-refresh search checkbox on map overlay
 * - Map-driven proximity filtering
 */
(function () {
  'use strict';

  const MIN_RADIUS_KM = 20;
  const MAP_MOVE_REFRESH_DEBOUNCE_MS = 450;

  // Configuration
  const config = {
    formSelector: 'form[data-drupal-selector="views-exposed-form-ps-offer-search-page-1"]',
    mapContainerSelector: '#geofield-map-view-ps-offer-search-attachment-1',
    hiddenLatSelector: 'input[name="nearby_lat"]',
    hiddenLonSelector: 'input[name="nearby_lon"]',
    hiddenRadiusSelector: 'input[name="nearby_radius_km"]',
  };

  // Global map instance reference
  let googleMapInstance = null;
  let refreshTimeoutId = null;
  let hasMapUserInteraction = false;
  let isAjaxRefreshing = false;

  function getQueryFloatParam(name) {
    const raw = getQueryParam(name);
    if (raw === null || raw === '') {
      return null;
    }

    const parsed = Number.parseFloat(raw);
    return Number.isFinite(parsed) ? parsed : null;
  }

  /**
   * Initialize proximity UI controls - floating on map
   */
  function initProximityUI() {
    const mapContainer = document.querySelector(config.mapContainerSelector);
    if (!mapContainer) {
      console.warn('[MapProximity] Map container not found');
      return;
    }

    const mapHostContainer = mapContainer.parentElement || mapContainer;

    // Check if UI already exists
    if (mapHostContainer.querySelector('[data-map-proximity-floating]')) {
      return;
    }

    // Create floating controls panel
    const floatingPanel = document.createElement('div');
    floatingPanel.classList.add('map-proximity-floating');
    floatingPanel.setAttribute('data-map-proximity-floating', 'true');
    floatingPanel.style.cssText = `
      position: absolute;
      top: 10px;
      right: 10px;
      background: white;
      padding: 10px 12px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
      font-size: 13px;
      z-index: 10;
      min-width: 210px;
    `;

    // Auto-refresh checkbox
    const autoRefreshGroup = document.createElement('div');

    const autoRefreshLabel = document.createElement('label');
    autoRefreshLabel.style.cssText = 'display: flex; align-items: center; gap: 8px; cursor: pointer; margin: 0;';

    const autoRefreshCheckbox = document.createElement('input');
    autoRefreshCheckbox.type = 'checkbox';
    autoRefreshCheckbox.checked = getQueryParam('auto_refresh_on_pan') === '1';
    autoRefreshCheckbox.style.cssText = 'width: 18px; height: 18px; cursor: pointer;';
    autoRefreshCheckbox.classList.add('map-proximity-checkbox');
    autoRefreshCheckbox.addEventListener('change', function () {
      updateAutoRefreshState(this.checked);
      if (this.checked) {
        syncViewportProximity(true);
      }
    });

    const autoRefreshText = document.createElement('span');
    autoRefreshText.textContent = 'Refresh when map moves';
    autoRefreshText.style.cssText = 'flex: 1; color: #333; font-size: 13px;';

    autoRefreshLabel.appendChild(autoRefreshCheckbox);
    autoRefreshLabel.appendChild(autoRefreshText);
    autoRefreshGroup.appendChild(autoRefreshLabel);

    // Assemble floating panel
    floatingPanel.appendChild(autoRefreshGroup);

    // Insert into a stable wrapper around map content.
    mapHostContainer.style.position = 'relative';
    mapHostContainer.appendChild(floatingPanel);

    // Populate existing hidden fields if needed
    initHiddenFields();

    console.log('[MapProximity] Floating UI controls created successfully');
  }

  /**
   * Initialize hidden input fields for proximity params
   */
  function initHiddenFields() {
    const form = document.querySelector(config.formSelector);
    if (!form) return;

    // Ensure hidden fields exist
    ensureHiddenField(form, 'nearby_lat', getQueryParam('nearby_lat') || '');
    ensureHiddenField(form, 'nearby_lon', getQueryParam('nearby_lon') || '');
    ensureHiddenField(form, 'nearby_radius_km', getClampedRadius(getQueryParam('nearby_radius_km')));
    ensureHiddenField(form, 'auto_refresh_on_pan', getQueryParam('auto_refresh_on_pan') || '0');
  }

  /**
   * Ensure a hidden input field exists in the form
   */
  function ensureHiddenField(form, fieldName, fieldValue) {
    let field = form.querySelector(`input[name="${fieldName}"]`);
    if (!field) {
      field = document.createElement('input');
      field.type = 'hidden';
      field.name = fieldName;
      form.appendChild(field);
    }
    if (fieldValue) {
      field.value = fieldValue;
    }
  }

  function updateProximityFilter(lat, lon, radiusValue) {
    const form = document.querySelector(config.formSelector);
    if (!form) return;

    ensureHiddenField(form, 'nearby_lat', lat);
    ensureHiddenField(form, 'nearby_lon', lon);
    ensureHiddenField(form, 'nearby_radius_km', getClampedRadius(radiusValue));
  }

  function updateAutoRefreshState(enabled) {
    const form = document.querySelector(config.formSelector);
    if (!form) return;
    ensureHiddenField(form, 'auto_refresh_on_pan', enabled ? '1' : '0');
  }

  function getClampedRadius(value) {
    const parsed = Number.parseFloat(String(value));
    if (!Number.isFinite(parsed)) {
      return String(MIN_RADIUS_KM);
    }
    return String(Math.max(MIN_RADIUS_KM, parsed));
  }

  function toRadians(degrees) {
    return (degrees * Math.PI) / 180;
  }

  function haversineDistanceKm(lat1, lon1, lat2, lon2) {
    const earthRadiusKm = 6371;
    const dLat = toRadians(lat2 - lat1);
    const dLon = toRadians(lon2 - lon1);
    const a = Math.sin(dLat / 2) * Math.sin(dLat / 2)
      + Math.cos(toRadians(lat1)) * Math.cos(toRadians(lat2))
      * Math.sin(dLon / 2) * Math.sin(dLon / 2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    return earthRadiusKm * c;
  }

  function computeViewportRadiusKm(map) {
    const center = map.getCenter();
    const bounds = map.getBounds();
    if (!center || !bounds) {
      return MIN_RADIUS_KM;
    }

    const ne = bounds.getNorthEast();
    const sw = bounds.getSouthWest();
    if (!ne || !sw) {
      return MIN_RADIUS_KM;
    }

    const centerLat = center.lat();
    const centerLon = center.lng();
    const neDistance = haversineDistanceKm(centerLat, centerLon, ne.lat(), ne.lng());
    const swDistance = haversineDistanceKm(centerLat, centerLon, sw.lat(), sw.lng());

    return Math.max(MIN_RADIUS_KM, neDistance, swDistance);
  }

  function syncViewportProximity(shouldTriggerSearch) {
    if (!googleMapInstance) {
      return;
    }

    const center = googleMapInstance.getCenter();
    if (!center) {
      return;
    }

    const radiusKm = computeViewportRadiusKm(googleMapInstance);
    updateProximityFilter(center.lat(), center.lng(), radiusKm);

    if (shouldTriggerSearch) {
      triggerSearch();
    }
  }

  /**
   * Check if auto-refresh is enabled
   */
  function isAutoRefreshEnabled() {
    const checkbox = document.querySelector('[data-map-proximity-floating] input[type="checkbox"]');
    return checkbox && checkbox.checked;
  }

  /**
   * Trigger search without a full page navigation.
   */
  async function triggerSearch() {
    const form = document.querySelector(config.formSelector);
    const currentView = document.querySelector('.ps-offer-search-view');

    if (!form || !currentView || isAjaxRefreshing || typeof window.fetch !== 'function') {
      return;
    }

    isAjaxRefreshing = true;

    try {
      const formData = new FormData(form);
      const requestUrl = new URL(form.getAttribute('action') || window.location.pathname, window.location.origin);
      const requestParams = new URLSearchParams(formData);
      requestUrl.search = requestParams.toString();

      const response = await fetch(requestUrl.toString(), {
        method: 'GET',
        credentials: 'same-origin',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
        },
      });

      if (!response.ok) {
        throw new Error('Search refresh failed with status ' + response.status);
      }

      const html = await response.text();
      const parser = new DOMParser();
      const nextDocument = parser.parseFromString(html, 'text/html');
      const nextView = nextDocument.querySelector('.ps-offer-search-view');

      if (!nextView) {
        throw new Error('Updated search view markup not found');
      }

      currentView.replaceWith(nextView);

      const visibleUrl = new URL(requestUrl.toString());
      visibleUrl.searchParams.delete('nearby_radius_km');
      window.history.replaceState(window.history.state, '', visibleUrl.toString());

      if (window.Drupal && typeof window.Drupal.attachBehaviors === 'function') {
        window.Drupal.attachBehaviors(document, window.drupalSettings || {});
      }
    }
    catch (error) {
      console.warn('[MapProximity] AJAX refresh failed, falling back to form submit.', error);
      form.submit();
    }
    finally {
      isAjaxRefreshing = false;
    }
  }

  /**
   * Get query parameter from URL
   */
  function getQueryParam(name) {
    const url = new URL(window.location);
    return url.searchParams.get(name);
  }

  /**
   * Keep radius internal while removing it from the visible browser URL.
   */
  function sanitizeVisibleUrl() {
    try {
      const url = new URL(window.location.href);
      if (!url.searchParams.has('nearby_radius_km')) {
        return;
      }

      url.searchParams.delete('nearby_radius_km');
      window.history.replaceState(window.history.state, '', url.toString());
    }
    catch (e) {
      console.debug('[MapProximity] Could not sanitize URL:', e.message);
    }
  }

  /**
   * Try to attach to Google Map if available
   */
  function attachToGoogleMap() {
    // This is an optional enhancement - only if Google Maps is available.
    setTimeout(() => {
      try {
        if (typeof google === 'undefined') {
          return;
        }

        const mapId = config.mapContainerSelector.replace(/^#/, '');
        const formatter = Drupal && Drupal.geoFieldMapFormatter ? Drupal.geoFieldMapFormatter : null;
        const mapData = formatter && formatter.map_data ? formatter.map_data[mapId] : null;

        if (mapData && mapData.map && typeof mapData.map.getCenter === 'function') {
          googleMapInstance = mapData.map;
        }
        else {
          const mapElement = document.querySelector(config.mapContainerSelector);
          if (!mapElement) {
            return;
          }

          // Fallback for environments where only internal canvas is exposed.
          const mapCanvas = mapElement.querySelector('[data-map-canvas]')
            || mapElement.querySelector('div[style*="width"][style*="height"]');

          googleMapInstance = mapCanvas && mapCanvas.__gm ? mapCanvas.__gm.map : null;
        }

        if (!googleMapInstance) {
          return;
        }

        const initialLat = getQueryFloatParam('nearby_lat');
        const initialLon = getQueryFloatParam('nearby_lon');
        if (initialLat !== null && initialLon !== null && typeof google.maps.LatLng === 'function') {
          googleMapInstance.setCenter(new google.maps.LatLng(initialLat, initialLon));
        }

        googleMapInstance.addListener('dragstart', function () {
          hasMapUserInteraction = true;
        });

        googleMapInstance.addListener('zoom_changed', function () {
          hasMapUserInteraction = true;
        });

        // Listen after map movement finishes to reduce reload noise.
        googleMapInstance.addListener('idle', function () {
          if (!hasMapUserInteraction) {
            return;
          }

          if (refreshTimeoutId) {
            window.clearTimeout(refreshTimeoutId);
          }
          refreshTimeoutId = window.setTimeout(function () {
            syncViewportProximity(isAutoRefreshEnabled());
            hasMapUserInteraction = false;
          }, MAP_MOVE_REFRESH_DEBOUNCE_MS);
        });

        console.log('[MapProximity] Attached to Google Map successfully');
      } catch (e) {
        console.debug('[MapProximity] Could not attach to Google Map:', e.message);
      }
    }, 1000);
  }

  /**
   * Initialize on DOM ready and Drupal behaviors
   */
  function initialize() {
    initProximityUI();
    attachToGoogleMap();
    sanitizeVisibleUrl();
  }

  // Expose a single AJAX refresh entry point for sort/map interactions.
  window.BnppreMapProximityTriggerSearch = triggerSearch;

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initialize);
  } else {
    initialize();
  }

  // Re-attach for AJAX/Views refresh
  if (typeof Drupal !== 'undefined') {
    Drupal.behaviors.mapProximityControl = {
      attach: function () {
        initialize();
      },
    };
  }
})();
