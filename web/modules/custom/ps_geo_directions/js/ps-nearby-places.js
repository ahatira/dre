// Configuration visuelle par type de POI
const POI_CONFIG = {
		transit_station: { color: '#1565c0', initial: 'T', label: 'Transport' },
		parking:         { color: '#2e7d32', initial: 'P', label: 'Parking' },
		restaurant:      { color: '#e65100', initial: 'R', label: 'Restaurant' },
		lodging:         { color: '#6a1b9a', initial: 'H', label: 'Hotel' },
};

// Génère une icône SVG personnalisée pour le marker
function buildMarkerSvg(color, initial) {
		const svg = [
			'<svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 30 30">',
			'  <circle cx="15" cy="15" r="13" fill="' + color + '" stroke="#fff" stroke-width="2"/>',
			'  <text x="15" y="20" font-size="13" font-family="Arial,sans-serif"',
			'        font-weight="bold" fill="#fff" text-anchor="middle">' + initial + '</text>',
			'</svg>',
		].join('');
		return 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(svg);
}
/**
 * Script pour interagir avec la Google Map et cacher les POI natifs par défaut.
 * Nécessite que la carte Google Maps soit déjà initialisée sur la page.
 */
(function (Drupal, drupalSettings) {
	Drupal.behaviors.psNearbyPlaces = {
		attach: function (context, settings) {
			var debug = settings.ps_geo_directions && settings.ps_geo_directions.enable_debug;
			if (debug) {
				console.log('[ps-nearby-places] Initialisation du behavior psNearbyPlaces...');
			}
			// S'assure que ce code ne s'exécute qu'une fois par page.
			if (context !== document) {
				if (debug) {
					console.log('[ps-nearby-places] Contexte != document, script ignoré.');
				}
				return;
			}

			// Récupère les types de POI et le radius depuis drupalSettings
			// Récupère les types de POI et le radius depuis drupalSettings injecté par DirectionsForm
			var poiTypes = (settings.ps_nearby_places && Array.isArray(settings.ps_nearby_places.categories)) ? settings.ps_nearby_places.categories : ['transports', 'parkings', 'restaurants', 'hotels'];
			var poiRadius = (settings.ps_nearby_places && settings.ps_nearby_places.radius) ? parseInt(settings.ps_nearby_places.radius, 10) : 1000;
			var categoryMap = (settings.ps_nearby_places && settings.ps_nearby_places.category_map) ? settings.ps_nearby_places.category_map : {
				'transports': 'transit_station',
				'parkings': 'parking',
				'restaurants': 'restaurant',
				'hotels': 'lodging'
			};

			// Gestion dynamique des POI custom via Google PlacesService
			function updatePOIVisibilityOnMaps(categories) {
							var maps = [];
							if (typeof Drupal !== 'undefined' && Drupal.geoFieldMapFormatter && Drupal.geoFieldMapFormatter.map_data) {
								var formatterMaps = Drupal.geoFieldMapFormatter.map_data;
								Object.keys(formatterMaps).forEach(function(mapid, idx) {
									var map = formatterMaps[mapid].map;
									if (map) maps.push(map);
								});
							}
							if (window.geofieldMap && Array.isArray(window.geofieldMap.maps)) {
								maps = maps.concat(window.geofieldMap.maps);
							} else if (window.geofieldMap && window.geofieldMap.map) {
								maps.push(window.geofieldMap.map);
							}
							if (window.psGeoDirectionsMapInstance) {
								maps.push(window.psGeoDirectionsMapInstance);
							}
							if (!maps.length) return;

							// Types Google Places valides
							var checkboxToPlacesType = categoryMap;

							// Masque tous les POI natifs
							maps.forEach(function(map) {
								try {
									map.setOptions({ styles: [{ featureType: 'poi', elementType: 'all', stylers: [{ visibility: 'off' }] }] });
								} catch (e) {}
							});

							// Store markers by type for cleanup
							window.psNearbyPlacesMarkers = window.psNearbyPlacesMarkers || {};

							// Pour chaque type, afficher ou supprimer les markers custom
							Object.keys(checkboxToPlacesType).forEach(function(key) {
								var type = checkboxToPlacesType[key];
								if (categories.indexOf(key) !== -1) {
									maps.forEach(function(map) {
										showPOICategory(map, type);
									});
								} else {
									maps.forEach(function(map) {
										hidePOICategory(map, type);
									});
								}
							});
						}

						// Affiche les POI d'une catégorie (type Google Places) avec icône personnalisée
			function showPOICategory(map, type) {
				if (!window.google || !window.google.maps || !window.google.maps.places) return;
				var service = new google.maps.places.PlacesService(map);
				var center = map.getCenter();
				var radius = poiRadius;
				service.nearbySearch({
					location: center,
					radius: radius,
					type: type
				}, function(results, status) {
					if (status !== google.maps.places.PlacesServiceStatus.OK) return;
					window.psNearbyPlacesMarkers[type] = window.psNearbyPlacesMarkers[type] || [];
					results.forEach(function(place) {
						if (!place.geometry || !place.geometry.location) return;
						var config = POI_CONFIG[type] || { color: '#555', initial: '?', label: type };
						var marker = new google.maps.Marker({
							position: place.geometry.location,
							map: map,
							title: place.name || '',
							icon: {
								url: buildMarkerSvg(config.color, config.initial),
								scaledSize: new google.maps.Size(30, 30),
								anchor: new google.maps.Point(15, 30)
							}
						});
						window.psNearbyPlacesMarkers[type].push(marker);
					});
				});
			}

						// Supprime tous les markers d'une catégorie
						function hidePOICategory(map, type) {
							if (!window.psNearbyPlacesMarkers || !window.psNearbyPlacesMarkers[type]) return;
							window.psNearbyPlacesMarkers[type].forEach(function(marker) {
								marker.setMap(null);
							});
							window.psNearbyPlacesMarkers[type] = [];
						}

			// Polling pour attendre l'initialisation de la carte si besoin
			function hideNativePOIWithRetry(maxTries, delay) {
				var tries = 0;
				function tryHide() {
					var maps = [];
					if (typeof Drupal !== 'undefined' && Drupal.geoFieldMapFormatter && Drupal.geoFieldMapFormatter.map_data) {
						var formatterMaps = Drupal.geoFieldMapFormatter.map_data;
						Object.keys(formatterMaps).forEach(function(mapid, idx) {
							var map = formatterMaps[mapid].map;
							if (map) {
								maps.push(map);
							}
						});
					}
					if (window.geofieldMap && Array.isArray(window.geofieldMap.maps)) {
						maps = maps.concat(window.geofieldMap.maps);
					} else if (window.geofieldMap && window.geofieldMap.map) {
						maps.push(window.geofieldMap.map);
					}
					if (window.psGeoDirectionsMapInstance) {
						maps.push(window.psGeoDirectionsMapInstance);
					}
					if (!maps.length) {
						tries++;
						if (tries < maxTries) {
							setTimeout(tryHide, delay);
						}
						return;
					}
					// Par défaut, on masque tout
					updatePOIVisibilityOnMaps([]);
				}
				tryHide();
			}

			// Attente du chargement de Google Maps puis polling sur la carte
			var maxWaitGoogle = 30; // 30 tentatives max (~3s)
			var attemptsGoogle = 0;
			function waitForGoogleMaps() {
				if (typeof window.google !== 'undefined' && typeof window.google.maps !== 'undefined') {
					hideNativePOIWithRetry(30, 200);
					// Ajout listeners sur les checkboxes de POI (si présents)
					var $checkboxes = document.querySelectorAll('.ps-nearby-places-checkbox input[type="checkbox"]');
					if ($checkboxes.length) {
						$checkboxes.forEach(function(checkbox) {
							checkbox.addEventListener('change', function() {
								// Récupère toutes les catégories cochées
								var checked = Array.from($checkboxes).filter(function(cb) { return cb.checked; }).map(function(cb) { return cb.value; });
								updatePOIVisibilityOnMaps(checked);
							});
						});
					}
				} else {
					attemptsGoogle++;
					if (attemptsGoogle < maxWaitGoogle) {
						setTimeout(waitForGoogleMaps, 100);
					}
				}
			}
			// Initialisation automatique des checkboxes POI si présents
			document.addEventListener('DOMContentLoaded', function() {
				var $checkboxes = document.querySelectorAll('.ps-nearby-places-checkbox input[type="checkbox"]');
				if ($checkboxes.length && Array.isArray(poiTypes)) {
					$checkboxes.forEach(function(cb) {
						cb.checked = poiTypes.indexOf(cb.value) !== -1;
					});
				}
			});
			waitForGoogleMaps();
		}
	};
})(Drupal, drupalSettings);
