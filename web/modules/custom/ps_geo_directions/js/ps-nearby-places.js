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

			// Fonction pour appliquer dynamiquement le style de POI selon les catégories cochées
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

				// Génère le style Google Maps selon les catégories cochées
				var styles = [];
				// Si aucune catégorie cochée, on masque tout
				if (!categories.length) {
					styles.push({ featureType: 'poi', elementType: 'all', stylers: [{ visibility: 'off' }] });
				} else {
					// On masque tout sauf les catégories cochées
					styles.push({ featureType: 'poi', elementType: 'all', stylers: [{ visibility: 'off' }] });
					categories.forEach(function(cat) {
						styles.push({ featureType: 'poi.' + cat, elementType: 'all', stylers: [{ visibility: 'on' }] });
					});
				}
				maps.forEach(function(map) {
					try {
						map.setOptions({ styles: styles });
					} catch (e) {}
				});
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
