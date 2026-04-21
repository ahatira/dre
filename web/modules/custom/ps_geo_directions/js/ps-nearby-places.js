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

			// Fonction pour appliquer le style de masquage des POI natifs
			// Polling pour attendre l'initialisation de la carte si besoin
			function hideNativePOIWithRetry(maxTries, delay) {
				var tries = 0;
				function tryHide() {
					var maps = [];
					// 1. Support Geofield Map Formatter (cas principal Drupal)
					if (typeof Drupal !== 'undefined' && Drupal.geoFieldMapFormatter && Drupal.geoFieldMapFormatter.map_data) {
						var formatterMaps = Drupal.geoFieldMapFormatter.map_data;
						Object.keys(formatterMaps).forEach(function(mapid, idx) {
							var map = formatterMaps[mapid].map;
							if (map) {
								maps.push(map);
								if (debug) {
									console.log('[ps-nearby-places] Carte trouvée via Drupal.geoFieldMapFormatter.map_data, mapid:', mapid, map);
								}
							}
						});
					}
					// 2. Support window.geofieldMap (fallback)
					if (window.geofieldMap && Array.isArray(window.geofieldMap.maps)) {
						maps = maps.concat(window.geofieldMap.maps);
						if (debug) {
							console.log('[ps-nearby-places] Cartes trouvées dans window.geofieldMap.maps:', window.geofieldMap.maps.length);
						}
					} else if (window.geofieldMap && window.geofieldMap.map) {
						maps.push(window.geofieldMap.map);
						if (debug) {
							console.log('[ps-nearby-places] Carte trouvée dans window.geofieldMap.map');
						}
					}
					// 3. Support window.psGeoDirectionsMapInstance (fallback custom)
					if (window.psGeoDirectionsMapInstance) {
						maps.push(window.psGeoDirectionsMapInstance);
						if (debug) {
							console.log('[ps-nearby-places] Carte trouvée dans window.psGeoDirectionsMapInstance');
						}
					}

					if (!maps.length) {
						tries++;
						if (tries < maxTries) {
							if (debug) {
								console.warn('[ps-nearby-places] Aucun objet carte à traiter, nouvelle tentative dans ' + delay + 'ms (essai ' + tries + '/' + maxTries + ')');
							}
							setTimeout(tryHide, delay);
						} else if (debug) {
							console.error('[ps-nearby-places] Aucun objet carte trouvé après ' + maxTries + ' tentatives.');
						}
						return;
					}

					maps.forEach(function(map, idx) {
						if (!map) {
							if (debug) {
								console.warn('[ps-nearby-places] Carte nulle à l\'index', idx);
							}
							return;
						}
						if (debug) {
							console.log('[ps-nearby-places] Application du style pour masquer les POI natifs sur la carte index', idx, map);
						}
						var styles = [
							{
								featureType: 'poi',
								elementType: 'all',
								stylers: [{ visibility: 'off' }]
							}
						];
						try {
							map.setOptions({ styles: styles });
							if (debug) {
								console.log('[ps-nearby-places] Style appliqué (POI natifs masqués) sur la carte index', idx);
							}
						} catch (e) {
							if (debug) {
								console.error('[ps-nearby-places] Erreur lors de l\'application du style sur la carte index', idx, e);
							}
						}
					});
				}
				tryHide();
			}

			// Attente du chargement de Google Maps puis polling sur la carte
			var maxWaitGoogle = 30; // 30 tentatives max (~3s)
			var attemptsGoogle = 0;
			function waitForGoogleMaps() {
				if (typeof window.google !== 'undefined' && typeof window.google.maps !== 'undefined') {
					if (debug) {
						console.log('[ps-nearby-places] Google Maps API détectée, application du masquage POI.');
					}
					// On lance le polling sur la carte (jusqu'à 30 tentatives, 200ms)
					hideNativePOIWithRetry(30, 200);
				} else {
					attemptsGoogle++;
					if (attemptsGoogle < maxWaitGoogle) {
						if (debug && attemptsGoogle === 1) {
							console.log('[ps-nearby-places] Google Maps API non dispo, attente...');
						}
						setTimeout(waitForGoogleMaps, 100);
					} else if (debug) {
						console.warn('[ps-nearby-places] Google Maps API non chargée après attente.');
					}
				}
			}
			waitForGoogleMaps();
		}
	};
})(Drupal, drupalSettings);
