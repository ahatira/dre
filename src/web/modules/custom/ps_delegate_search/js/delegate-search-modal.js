(function ($, Drupal) {
  'use strict';

  /**
   * Behavior for delegate search modal.
   *
   * The modal is handled automatically by webform.dialog via the webform-dialog class.
   * This file is reserved for any custom behaviors if needed.
   */
  Drupal.behaviors.psDelegateSearchModal = {
    attach: function (context, settings) {
      // La modal est gérée par webform.dialog automatiquement via la classe webform-dialog
      // Ce fichier est réservé pour les comportements personnalisés si nécessaire.

      // Exemple : fermer la modal après soumission réussie
      $(context).find('.webform-dialog').once('delegate-search').each(function () {
        // L'ouverture/fermeture est gérée par webform.dialog
      });
    }
  };

})(jQuery, Drupal);
