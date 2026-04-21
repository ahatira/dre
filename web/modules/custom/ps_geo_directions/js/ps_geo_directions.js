((Drupal, once, $, drupalSettings) => {
  Drupal.behaviors.psGeoDirections = {
    attach(context) {
      let lastAddress = null;
      let lastOrigin = null;
      let lastMode = 'DRIVING';
      const calculateRoute = (address, mode) => {
        // Contrôle debug
        const debugEnabled = drupalSettings.ps_geo_directions && drupalSettings.ps_geo_directions.enable_debug;
        const originSetting = drupalSettings.ps_geo_directions && drupalSettings.ps_geo_directions.origin;
        if (debugEnabled) console.log('[PS Directions] Adresse sélectionnée:', address);
        if (debugEnabled) console.log('[PS Directions] Origin from config:', originSetting);
        const map = window.psGeoDirectionsMapInstance || null;
        // 1. Parser originSetting
        let originCoords = null;
        if (originSetting && typeof originSetting === 'string' && originSetting.includes(',')) {
          const [lat, lng] = originSetting.split(',').map(Number);
          originCoords = { lat, lng };
        }
        // 2. Géocoder l’adresse sélectionnée
        if (originCoords && typeof google !== 'undefined' && google.maps && google.maps.Geocoder) {
          const geocoder = new google.maps.Geocoder();
          geocoder.geocode({ address: address }, function(results, status) {
            if (status === 'OK' && results[0]) {
              const destination = results[0].geometry.location;
              // 3. Calculer l’itinéraire
              const directionsService = new google.maps.DirectionsService();
              if (window.psGeoDirectionsRenderer) {
                window.psGeoDirectionsRenderer.setMap(null);
                window.psGeoDirectionsRenderer = null;
              }
              window.psGeoDirectionsRenderer = new google.maps.DirectionsRenderer();
              // Attache à la carte
              let foundMap = null;
              if (typeof Drupal !== 'undefined' && Drupal.geoFieldMapFormatter && Drupal.geoFieldMapFormatter.map_data) {
                Object.keys(Drupal.geoFieldMapFormatter.map_data).forEach(function(key) {
                  if (
                    Drupal.geoFieldMapFormatter.map_data[key] &&
                    Drupal.geoFieldMapFormatter.map_data[key].map &&
                    typeof Drupal.geoFieldMapFormatter.map_data[key].map.setCenter === 'function'
                  ) {
                    foundMap = Drupal.geoFieldMapFormatter.map_data[key].map;
                  }
                });
                if (foundMap) {
                  window.psGeoDirectionsRenderer.setMap(foundMap);
                  // Expose la carte dans window pour les autres scripts (ex: ps-nearby-places.js)
                  window.psGeoDirectionsMapInstance = foundMap;
                  if (debugEnabled){
                    console.log('[ps-nearby-places] MAP trouvé');
                    console.log(foundMap);
                    console.log('[PS Directions] Carte exposée dans window.psGeoDirectionsMapInstance');
                  }
                }
              }

              const directionsRenderer = window.psGeoDirectionsRenderer;
              directionsService.route({
                origin: originCoords,
                destination: destination,
                travelMode: google.maps.TravelMode[mode || 'DRIVING']
              }, function(response, status) {
                if (status === 'OK') {
                  directionsRenderer.setDirections(response);
                  if (debugEnabled) console.log('[PS Directions] Itinéraire trouvé', response);
                  // Résumé d’itinéraire
                  const route = response.routes[0];
                  const leg = route.legs[0];
                  let summary = '';
                  if (leg && route) {
                    // Temps
                    const duration = leg.duration ? leg.duration.text : '';
                    // Distance
                    const distance = leg.distance ? leg.distance.text : '';
                    // Via
                    let via = '';
                    if (route.summary) {
                      via = route.summary;
                    } else if (leg.via_waypoint && leg.via_waypoint.length > 0) {
                      via = leg.via_waypoint.map(v => v.location).join(', ');
                    }
                    summary = `<strong>${duration}</strong> (${distance})`;
                    if (via) {
                      summary += `<br><strong>Via</strong> ${via}`;
                    }
                  }
                  $('.ps-geo-directions__summary').html(summary);
                } else {
                  if (debugEnabled) console.log('[PS Directions] Erreur DirectionsService:', status);
                  $('.ps-geo-directions__summary').html('');
                }
              });
            } else {
              if (debugEnabled) console.log('[PS Directions] Erreur geocoding:', status);
            }
          });
        } else {
          if (debugEnabled) console.log('[PS Directions] Impossible de calculer l’itinéraire (origin ou Google Maps non dispo)');
        }
      };

      // Initialisation autocomplete
      const fields = once('ps-geo-directions', '.ps-geo-directions-address', context);
      fields.forEach(el => {
        $(el).autocomplete({
          minLength: 3,
          source: (request, response) => {
            Drupal.geoFieldMapGeocoder.geocode(
              request.term,
              'googlemaps_offer',
              {}
            ).then(data => {
              if (!Array.isArray(data)) {
                console.warn('ps_geo_directions: data est undefined ou non-array', data);
                response([]);
                return;
              }
              response(data.map(item => ({
                label: item.formatted_address || item.value,
                value: item.formatted_address || item.value,
                lat: item.latitude || (item.geometry && item.geometry.location && item.geometry.location.lat),
                lng: item.longitude || (item.geometry && item.geometry.location && item.geometry.location.lng)
              })));
            }).catch(() => response([]));
          },
          select: (event, ui) => {
            lastAddress = ui.item.value;
            lastMode = $('input.ps-geo-directions-mode:checked').val() || 'DRIVING';
            // Affiche les modes de transport après sélection d'une adresse
            $('.ps-geo-directions-mode-wrapper').show();
            calculateRoute(lastAddress, lastMode);
          }
        });
      });

      // Masque les modes de transport au chargement
      $('.ps-geo-directions-mode-wrapper').hide();
      // Déclare modeInputs AVANT utilisation
      const modeInputs = once('ps-geo-directions-mode', 'input.ps-geo-directions-mode', context);
      modeInputs.forEach(input => {
        $(input).on('change', function() {
          // On recalcule l’itinéraire avec la dernière adresse sélectionnée
          lastMode = $(this).val();
          if (lastAddress) {
            calculateRoute(lastAddress, lastMode);
          }
        });
      });
    }
  };
})(Drupal, once, jQuery, drupalSettings);
