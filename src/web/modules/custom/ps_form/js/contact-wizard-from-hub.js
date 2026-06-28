/**
 * @file
 * Back button on direct contact webforms opened from the hub.
 */
(function (Drupal, drupalSettings, once) {
  'use strict';

  Drupal.behaviors.psFormContactWizardFromHub = {
    attach(context) {
      once('ps-form-contact-from-hub', 'form.ps-contact-wizard--from-hub', context).forEach((form) => {
        const backButton = form.querySelector('.ps-contact-hub-back');
        if (!backButton) {
          return;
        }

        backButton.addEventListener('click', (event) => {
          event.preventDefault();

          const settings = drupalSettings.psForm || {};
          const url = Drupal.psFormContactDisplay.resolveLocalizedPath(settings.hubPath || '/form/contact');
          const title = settings.hubTitle || '';

          Drupal.psFormContactDisplay.openContactForm(url, title);
        });
      });
    },
  };
})(Drupal, drupalSettings, once);
