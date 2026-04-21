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
							var checkboxToPlacesType = {
								'transports': 'transit_station',
								'parkings': 'parking',
								'restaurants': 'restaurant',
								'hotels': 'lodging'
							};

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
									// Afficher les POI de ce type
									maps.forEach(function(map) {
										showPOICategory(map, type);
									});
								} else {
									// Supprimer les markers de ce type
									maps.forEach(function(map) {
										hidePOICategory(map, type);
									});
								}
							});
						}

						// Affiche les POI d'une catégorie (type Google Places)
						function showPOICategory(map, type) {
							if (!window.google || !window.google.maps || !window.google.maps.places) return;
							var service = new google.maps.places.PlacesService(map);
							var center = map.getCenter();
							var radius = 1000; // Peut être passé via drupalSettings
							service.nearbySearch({
								location: center,
								radius: radius,
								type: type
							}, function(results, status) {
								if (status !== google.maps.places.PlacesServiceStatus.OK) return;
								window.psNearbyPlacesMarkers[type] = window.psNearbyPlacesMarkers[type] || [];
								results.forEach(function(place) {
									if (!place.geometry || !place.geometry.location) return;
									var marker = new google.maps.Marker({
										position: place.geometry.location,
										map: map,
										title: place.name || ''
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
			waitForGoogleMaps();
		}
	};
})(Drupal, drupalSettings);
