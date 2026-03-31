((Drupal, once) => {
  Drupal.ui_suite_bnppre_accordion =
    Drupal.ui_suite_bnppre_accordion || {};

  /**
   * Set data attribute for the keep open feature.
   *
   * @type {Drupal~behavior}
   *
   * @prop {Drupal~behaviorAttach} attach
   *   Attaches the behaviors for the keep open feature.
   */
  Drupal.behaviors.ui_suite_bnppre_accordion = {
    attach(context) {
      once(
        'accordion-keep-open',
        ".accordion[data-usb-keep-open='false']",
        context,
      ).forEach((accordion) => {
        const accordionId = accordion.getAttribute('id');

        once(
          'accordion-item-keep-open',
          '.js-accordion-keep-open',
          accordion,
        ).forEach((accordionItem) => {
          accordionItem.setAttribute('data-bs-parent', `#${accordionId}`);
        });
      });
    },
  };
})(Drupal, once);
