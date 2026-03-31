((Drupal, once) => {
  Drupal.ui_suite_bnppre_carousel = Drupal.ui_suite_bnppre_carousel || {};

  /**
   * Set active class on the first item.
   *
   * @type {Drupal~behavior}
   *
   * @prop {Drupal~behaviorAttach} attach
   *   Attaches the behaviors for the active class.
   */
  Drupal.behaviors.ui_suite_bnppre_carousel = {
    attach(context) {
      once('carousel-active', '.carousel', context).forEach((carousel) => {
        const items = carousel.querySelectorAll(
          '.carousel-inner .carousel-item',
        );
        if (items.length === 0) {
          return;
        }

        items.item(0).classList.add('active');
      });
    },
  };
})(Drupal, once);
