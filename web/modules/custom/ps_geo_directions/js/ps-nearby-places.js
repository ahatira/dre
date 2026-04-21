(function (Drupal, drupalSettings) {
  Drupal.behaviors.psNearbyPlaces = {
    attach: function (context, settings) {
      // Retrieve options from drupalSettings
      var origin = settings.ps_geo_directions?.origin || null;
      var enableDebug = settings.ps_geo_directions?.enable_debug || false;

      // Correction : cibler chaque case à cocher individuellement
      var checkboxes = document.querySelectorAll('.ps-geo-directions-poi-types input[type="checkbox"]');
      checkboxes.forEach(function (checkbox) {
        checkbox.addEventListener('change', function () {
          if (enableDebug) {
            // Affiche la valeur et l'état de la case
            console.log('Option sélectionné:', checkbox.value, checkbox.checked);
          }
        });
      });
    }
  };
})(Drupal, drupalSettings);
