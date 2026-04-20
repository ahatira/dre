







((Drupal, once, $, drupalSettings) => {
  Drupal.behaviors.psGeoDirections = {
    attach(context) {
      const fields = once('ps-geo-directions', '.ps-geo-directions-address', context);
      fields.forEach(el => {
        $(el).autocomplete({
          minLength: 3,
          source: (request, response) => {
            // Utilise la lib geofield_map côté JS pour la requête
            Drupal.geoFieldMapGeocoder.geocode(
              request.term,
              'googlemaps_offer', // provider
              {} // options
            ).then(data => {
              response(data.map(item => ({
                label: item.formatted_address || item.value,
                value: item.formatted_address || item.value,
                lat: item.latitude || (item.geometry && item.geometry.location && item.geometry.location.lat),
                lng: item.longitude || (item.geometry && item.geometry.location && item.geometry.location.lng)
              })));
            }).catch(() => response([]));
          },
          select: (event, ui) => {
            // Ici tu peux utiliser ui.item.lat, ui.item.lng
            console.log('[PS Directions] sélectionné:', ui.item);
          }
        });
      });
    }
  };
})(Drupal, once, jQuery, drupalSettings);
