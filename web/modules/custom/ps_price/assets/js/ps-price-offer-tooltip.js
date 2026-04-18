(function (Drupal, once) {
  'use strict';

  Drupal.behaviors.psPriceOfferTooltip = {
    attach: function (context) {
      once('ps-price-offer-tooltip', '[data-bs-toggle="tooltip"]', context).forEach(function (element) {
        if (typeof window.bootstrap === 'undefined' || typeof window.bootstrap.Tooltip === 'undefined') {
          return;
        }

        window.bootstrap.Tooltip.getOrCreateInstance(element);
      });
    },
  };
})(Drupal, once);
