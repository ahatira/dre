/**
 * Map Utils (shared, headless)
 * - Pure helpers shared by map-based components (no framework deps)
 * - Keep generic data handling and icon option factories
 */

/**
 * Read dataset from canvas element attributes
 * @param {HTMLElement} canvas
 * @returns {{lat:number,lng:number,zoom:number,provider:string,markers:Array}}
 */
export function readMapDataset(canvas) {
  const lat = parseFloat(canvas.getAttribute('data-lat')) || 48.8566;
  const lng = parseFloat(canvas.getAttribute('data-lng')) || 2.3522;
  const zoom = Number.parseInt(canvas.getAttribute('data-zoom'), 10) || 14;
  const provider = canvas.getAttribute('data-provider') || 'google';
  const markersData = canvas.getAttribute('data-markers');

  let markers = [];
  if (markersData) {
    try {
      markers = JSON.parse(markersData);
    } catch (e) {
      // eslint-disable-next-line no-console
      console.warn('map-utils: Invalid markers JSON', e);
    }
  }

  return { lat, lng, zoom, provider, markers };
}

/**
 * Default marker icon URL (brand-safe)
 * @returns {string}
 */
export function getDefaultIconUrl() {
  return '/images/markers/default.svg';
}

/**
 * Leaflet icon options for default marker
 * @returns {{iconUrl:string,iconSize:number[],iconAnchor:number[],popupAnchor:number[]}}
 */
export function getLeafletIconOptions() {
  return {
    iconUrl: getDefaultIconUrl(),
    iconSize: [32, 40],
    iconAnchor: [16, 40],
    popupAnchor: [0, -40],
  };
}

/**
 * Google Maps icon options for default marker
 * @returns {{url:string,scaledSize:any,anchor:any}}
 */
export function getGoogleIconOptions(googleNS) {
  const g = googleNS || (typeof google !== 'undefined' ? google : null);
  return {
    url: getDefaultIconUrl(),
    scaledSize: g ? new g.maps.Size(32, 40) : undefined,
    anchor: g ? new g.maps.Point(16, 40) : undefined,
  };
}

/**
 * Compute Leaflet bounds from markers list
 * @param {Array<{lat:number,lng:number}>} markers
 * @returns {Array<[number,number]>} bounds list suitable for L.latLngBounds
 */
export function computeLeafletBounds(markers) {
  return markers
    .filter((m) => !Number.isNaN(parseFloat(m.lat)) && !Number.isNaN(parseFloat(m.lng)))
    .map((m) => [parseFloat(m.lat), parseFloat(m.lng)]);
}
