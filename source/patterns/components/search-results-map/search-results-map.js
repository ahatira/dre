import { readMapDataset } from '../../base/js/map-utils.js';
import './map-marker.js';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import 'leaflet.markercluster';
import 'leaflet.markercluster/dist/MarkerCluster.css';
import 'leaflet.markercluster/dist/MarkerCluster.Default.css';

((Drupal, _drupalSettings, once) => {
  // Fix Leaflet default icon path
  delete L.Icon.Default.prototype._getIconUrl;
  L.Icon.Default.mergeOptions({
    iconRetinaUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon-2x.png',
    iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
    shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
  });
  Drupal.behaviors.psSearchResultsMap = {
    attach(context, settings) {
      once('psSearchResultsMap', '.ps-map__canvas', context).forEach((canvas) => {
        this.init(canvas, settings);
      });
    },

    detach(_context, _settings, trigger) {
      if (trigger === 'unload') {
        // Clean up all map instances and listeners when view is destroyed
        document.querySelectorAll('.ps-map__canvas').forEach((canvas) => {
          this.cleanupCanvas(canvas);
        });
      }
    },

    cleanupCanvas(canvas) {
      // Remove anchor recalc listeners
      if (canvas.__cleanupAnchorRecalc && typeof canvas.__cleanupAnchorRecalc === 'function') {
        canvas.__cleanupAnchorRecalc();
      }
      // Destroy map instance
      if (canvas.googleMap && typeof canvas.googleMap.setMap === 'function') {
        canvas.googleMap.setMap(null);
        delete canvas.googleMap;
      }
      // Clear clusterer
      if (canvas.clusterer) {
        delete canvas.clusterer;
      }
      // Clear radius circle
      if (canvas.radiusCircle) {
        canvas.radiusCircle.setMap(null);
        delete canvas.radiusCircle;
      }
    },

    /**
     * Read configuration from drupalSettings OR data-attributes (fallback)
     * Priority: drupalSettings.psSearchResultsMap[mapId] > data-attributes
     */
    readConfig(canvas, settings) {
      const mapId = canvas.getAttribute('data-map-id') || 'default';
      const drupalConfig = settings?.psSearchResultsMap?.[mapId] || {};

      const dataConfig = readMapDataset(canvas);

      const parseResults = () => {
        if (drupalConfig.results) {
          return drupalConfig.results;
        }
        const resultsData = canvas.getAttribute('data-results');
        if (!resultsData) {
          return [];
        }
        try {
          return JSON.parse(resultsData);
        } catch (e) {
          console.warn('psSearchResultsMap: Invalid results JSON', e);
          return [];
        }
      };

      const resolveRadius = () => {
        if (drupalConfig.radiusMeters) {
          return drupalConfig.radiusMeters;
        }
        const radiusValue = canvas.getAttribute('data-radius-meters');
        if (radiusValue) {
          return Number.parseInt(radiusValue, 10);
        }
        return 1200;
      };

      const resolveBoolean = (primary, fallback) => {
        if (primary !== undefined) {
          return primary;
        }
        return fallback;
      };

      return {
        lat: drupalConfig.lat || dataConfig.lat,
        lng: drupalConfig.lng || dataConfig.lng,
        zoom: drupalConfig.zoom || dataConfig.zoom,
        provider: drupalConfig.provider || dataConfig.provider || 'google',
        results: parseResults(),
        showRadius: resolveBoolean(
          drupalConfig.showRadius,
          canvas.getAttribute('data-show-radius') === 'true'
        ),
        radiusMeters: resolveRadius(),
        cluster: resolveBoolean(
          drupalConfig.cluster,
          canvas.getAttribute('data-cluster') !== 'false'
        ),
        selectedId: drupalConfig.selectedId || canvas.getAttribute('data-selected-id') || null,
        mapId: drupalConfig.mapId || 'DEMO_MAP_ID',
        autoFit: drupalConfig.autoFit ?? true,
        viewId: drupalConfig.viewId || null,
        displayId: drupalConfig.displayId || null,
        contextualFilters: drupalConfig.contextualFilters || [],
      };
    },

    async init(canvas, settings) {
      const config = this.readConfig(canvas, settings);
      const {
        lat,
        lng,
        zoom,
        provider,
        results,
        showRadius,
        radiusMeters,
        cluster,
        selectedId,
        autoFit,
      } = config;

      // Auto-detect provider: try Google first, fallback to Leaflet
      let actualProvider = provider;
      if (provider === 'google' && (typeof google === 'undefined' || !google.maps)) {
        console.warn('Google Maps not available, falling back to Leaflet');
        actualProvider = 'leaflet';
      }

      if (actualProvider === 'leaflet') {
        this.initLeafletMap(canvas, config);
        return;
      }

      // Google Maps initialization
      const map = new google.maps.Map(canvas, {
        center: { lat, lng },
        zoom,
        mapId: config.mapId || 'DEMO_MAP_ID', // Required for AdvancedMarkerElement
        mapTypeControl: false,
        fullscreenControl: true,
        zoomControl: true,
      });

      const AdvancedMarkerElement = await this.loadAdvancedMarker();
      const gmarkers = results
        .map((r) =>
          this.buildResultMarker({
            map,
            AdvancedMarkerElement,
            canvas,
            result: { ...r, selected: r.selected || (selectedId && r.id === selectedId) },
          })
        )
        .filter((m) => !!m);

      // Auto-fit map to include all markers if enabled
      let centerForRadius = { lat, lng };
      if (autoFit && gmarkers.length > 1) {
        const bounds = new google.maps.LatLngBounds();
        gmarkers.forEach((m) => {
          const pos = m.getPosition?.() || m.position;
          if (pos) {
            bounds.extend(pos);
          }
        });
        if (!bounds.isEmpty()) {
          map.fitBounds(bounds);
          const c = map.getCenter();
          centerForRadius = { lat: c.lat(), lng: c.lng() };
        }
      }

      this.setupClustering(canvas, map, gmarkers, cluster, AdvancedMarkerElement);
      this.setupBoundsEvents(canvas, map);
      this.setupRadius(canvas, map, centerForRadius, showRadius, radiusMeters);
      this.setupAnchorRecalc(canvas, map, gmarkers);

      canvas.googleMap = map;
      canvas.mapConfig = config; // Store for external access (Views, etc.)
      this.emit(canvas, 'map:initialized', { map, results, config });
    },

    buildResultMarker({ map, AdvancedMarkerElement, canvas, result }) {
      const rLat = parseFloat(result.lat);
      const rLng = parseFloat(result.lng);
      if (Number.isNaN(rLat) || Number.isNaN(rLng)) {
        return null;
      }

      if (AdvancedMarkerElement) {
        return this.createAdvancedMarker({
          map,
          canvas,
          position: { lat: rLat, lng: rLng },
          result,
          AdvancedMarkerElement,
        });
      }

      return this.createSimpleMarker({
        map,
        canvas,
        position: { lat: rLat, lng: rLng },
        result,
      });
    },

    createAdvancedMarker({ map, canvas, position, result, AdvancedMarkerElement }) {
      const isNC = result.price === null || typeof result.price === 'undefined';
      const labelText = isNC ? 'NC' : `${result.price} €`;

      // Create simple HTML marker for Google Maps AdvancedMarkerElement
      const markerDiv = document.createElement('div');
      markerDiv.className = isNC ? 'ps-map__marker ps-map__marker--nc' : 'ps-map__marker';
      if (result.selected) {
        markerDiv.classList.add('ps-map__marker--selected');
      }

      const badge = document.createElement('div');
      badge.className = 'ps-map__marker__badge';
      badge.textContent = labelText;

      const circle = document.createElement('div');
      circle.className = 'ps-map__marker__circle';

      markerDiv.appendChild(badge);
      markerDiv.appendChild(circle);

      // Compute anchor point for precise positioning
      const { x: anchorX, y: anchorY } = this.computeMarkerAnchor(markerDiv, badge, circle);

      const am = new AdvancedMarkerElement({
        map,
        position,
        content: markerDiv,
        title: labelText,
        anchorPoint: new google.maps.Point(anchorX, anchorY),
      });

      // Store refs for anchor recalculation
      am.__wrapper = markerDiv;
      am.__badge = badge;
      am.__circle = circle;
      am.addListener('gmp-click', () =>
        this.emit(canvas, 'result:click', { id: result.id, result })
      );

      return am;
    },

    /**
     * Compute anchor point for markers
     */
    computeMarkerAnchor(wrapper, badge, circle) {
      let anchorX = 36;
      let anchorY = 44;

      try {
        const attached = document.contains(wrapper);
        if (!attached) {
          wrapper.style.position = 'absolute';
          wrapper.style.visibility = 'hidden';
          wrapper.style.left = '-10000px';
          wrapper.style.top = '0';
          document.body.appendChild(wrapper);
        }

        const badgeRect = badge.getBoundingClientRect();
        const circleRect = circle.getBoundingClientRect();

        anchorX = badgeRect.width / 2;
        anchorY = badgeRect.height + 6 + circleRect.height / 2;

        if (!attached) {
          document.body.removeChild(wrapper);
        }
      } catch (_e) {
        // Keep defaults
      }

      return { x: anchorX, y: anchorY };
    },

    createSimpleMarker({ map, canvas, position, result }) {
      const isNC = result.price === null || typeof result.price === 'undefined';
      const labelText = isNC ? 'NC' : `${result.price} €`;
      const rootStyle = getComputedStyle(document.documentElement);
      const primaryColor = rootStyle.getPropertyValue('--primary').trim() || 'rgb(0, 145, 90)';
      const grayColor = rootStyle.getPropertyValue('--gray-700').trim() || 'rgb(55, 65, 81)';

      const m = new google.maps.Marker({
        position,
        map,
        title: labelText,
        label: { text: labelText, fontWeight: '700', color: isNC ? grayColor : primaryColor },
      });

      m.addListener('click', () => this.emit(canvas, 'result:click', { id: result.id, result }));
      return m;
    },

    setupAnchorRecalc(canvas, map, markers) {
      const recalc = () => {
        markers.forEach((m) => {
          if (!m || !m.__wrapper || !m.__badge || !m.__circle) {
            return;
          }
          const { x, y } = this.computeMarkerAnchor(m.__wrapper, m.__badge, m.__circle);
          if (typeof m.setOptions === 'function') {
            m.setOptions({ anchorPoint: new google.maps.Point(x, y) });
          } else {
            m.anchorPoint = new google.maps.Point(x, y);
          }
        });
      };

      const zoomListener = map.addListener('zoom_changed', recalc);
      const onResize = () => recalc();
      window.addEventListener('resize', onResize);

      canvas.__cleanupAnchorRecalc = () => {
        if (zoomListener && typeof zoomListener.remove === 'function') {
          zoomListener.remove();
        }
        window.removeEventListener('resize', onResize);
      };
    },

    setupClustering(canvas, map, markers, enabled, AdvancedMarkerElement) {
      if (!enabled || markers.length === 0) {
        return;
      }

      // Only attempt clustering if MarkerClusterer is available
      if (!window.markerClusterer || !window.markerClusterer.MarkerClusterer) {
        console.warn('MarkerClusterer library not loaded, skipping clustering');
        return;
      }

      const { MarkerClusterer } = window.markerClusterer;

      const renderer = AdvancedMarkerElement
        ? {
            render: ({ count, position }) => {
              const clusterDiv = document.createElement('div');
              clusterDiv.className = 'ps-map__cluster';
              clusterDiv.textContent = String(count || 0);
              clusterDiv.dataset.type = 'cluster';
              return new AdvancedMarkerElement({ map, position, content: clusterDiv });
            },
          }
        : undefined;

      const clusterer = new MarkerClusterer({ map, markers, renderer });
      canvas.clusterer = clusterer;
    },

    async loadAdvancedMarker() {
      try {
        const lib = await google.maps.importLibrary('marker');
        return lib.AdvancedMarkerElement;
      } catch (_) {
        return null;
      }
    },

    setupRadius(canvas, map, center, show, radiusMeters) {
      if (show && radiusMeters > 0) {
        const primaryColor =
          getComputedStyle(document.documentElement).getPropertyValue('--primary').trim() ||
          'rgb(0, 145, 90)';
        const circle = new google.maps.Circle({
          map,
          center,
          radius: radiusMeters,
          strokeColor: primaryColor,
          strokeOpacity: 0.4,
          strokeWeight: 2,
          fillColor: primaryColor,
          fillOpacity: 0.15,
        });
        canvas.radiusCircle = circle;
      }
    },

    emit(el, name, detail) {
      el.dispatchEvent(new CustomEvent(name, { detail, bubbles: true }));
    },

    setupBoundsEvents(canvas, map) {
      const emitBounds = () => {
        const b = map.getBounds();
        if (!b) {
          return;
        }
        const ne = b.getNorthEast();
        const sw = b.getSouthWest();
        const center = map.getCenter();
        this.emit(canvas, 'bounds:changed', {
          bounds: {
            north: ne.lat(),
            east: ne.lng(),
            south: sw.lat(),
            west: sw.lng(),
          },
          zoom: map.getZoom(),
          center: { lat: center.lat(), lng: center.lng() },
        });
      };
      map.addListener('idle', emitBounds);
      map.addListener('dragend', emitBounds);
      map.addListener('zoom_changed', emitBounds);
    },

    /**
     * Initialize Leaflet map with custom markers
     */
    initLeafletMap(canvas, config) {
      const { lat, lng, zoom, results, showRadius, radiusMeters, selectedId } = config;

      // Create Leaflet map
      const map = L.map(canvas, {
        center: [lat, lng],
        zoom,
        zoomControl: true,
        fullscreenControl: false,
      });

      // Add OpenStreetMap layer
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution:
          '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19,
      }).addTo(map);

      // Create marker group (with clustering if available)
      let markerGroup = map;
      const { cluster } = config;

      // Use Leaflet.markercluster if enabled
      if (cluster && L.markerClusterGroup) {
        markerGroup = L.markerClusterGroup({
          maxClusterRadius: 80,
          disableClusteringAtZoom: 16,
          iconCreateFunction: (clusterMarker) => {
            const count = clusterMarker.getChildCount();
            return L.divIcon({
              html: `<div class="ps-map__cluster">${count}</div>`,
              className: 'ps-map__cluster-icon',
              iconSize: [44, 44],
              iconAnchor: [22, 22],
            });
          },
        });
        markerGroup.addTo(map);
      }

      // Create markers
      const markers = [];
      results.forEach((result) => {
        const rLat = parseFloat(result.lat);
        const rLng = parseFloat(result.lng);
        if (Number.isNaN(rLat) || Number.isNaN(rLng)) {
          return;
        }

        const markerElement = this.createLeafletMarker(result, selectedId);
        const marker = L.marker([rLat, rLng], {
          icon: L.divIcon({
            html: markerElement.outerHTML,
            className: 'ps-map__leaflet-icon',
            iconSize: [72, 44], // Approximate size
            iconAnchor: [36, 44], // Center bottom
          }),
        });

        marker.on('click', () => {
          this.emit(canvas, 'result:click', { id: result.id, result });
        });

        markerGroup.addLayer(marker);
        markers.push(marker);
      });

      // Add radius circle if enabled
      if (showRadius && radiusMeters > 0) {
        const primaryColor =
          getComputedStyle(document.documentElement).getPropertyValue('--primary').trim() ||
          'rgb(0, 145, 90)';
        L.circle([lat, lng], {
          radius: radiusMeters,
          color: primaryColor,
          fillColor: primaryColor,
          fillOpacity: 0.15,
          weight: 2,
          opacity: 0.4,
        }).addTo(map);
      }

      // Store map instance
      canvas.leafletMap = map;
      canvas.mapConfig = config;

      // Emit initialization event
      this.emit(canvas, 'map:initialized', { map, results, config, provider: 'leaflet' });

      // Setup bounds events for Leaflet
      map.on('moveend', () => {
        const bounds = map.getBounds();
        const center = map.getCenter();
        this.emit(canvas, 'bounds:changed', {
          bounds: {
            north: bounds.getNorth(),
            east: bounds.getEast(),
            south: bounds.getSouth(),
            west: bounds.getWest(),
          },
          zoom: map.getZoom(),
          center: { lat: center.lat, lng: center.lng },
        });
      });
    },

    /**
     * Create Leaflet marker element using Drupal.theme
     */
    createLeafletMarker(result, selectedId) {
      const isNC = result.price === null || typeof result.price === 'undefined';
      const selected = result.selected || (selectedId && result.id === selectedId);

      return Drupal.theme('mapMarker', {
        id: result.id,
        price: result.price,
        currency: result.currency || 'EUR',
        selected,
        type: isNC ? 'nc' : 'price',
      });
    },
  };
})(Drupal, drupalSettings, once);
