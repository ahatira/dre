((Drupal, once) => {
  Drupal.ui_suite_bnppre_carousel = Drupal.ui_suite_bnppre_carousel || {};

  /**
   * Update toolbar active state for grouped media buttons.
   *
   * @param {HTMLElement} carousel
   *   Carousel root element.
   */
  function syncToolbarState(carousel) {
    const buttons = Array.from(
      carousel.querySelectorAll('[data-carousel-toolbar-item]'),
    );
    if (!buttons.length) {
      return;
    }

    const slides = Array.from(carousel.querySelectorAll('.carousel-item'));
    const activeIndex = slides.findIndex((slide) =>
      slide.classList.contains('active'),
    );

    if (activeIndex < 0) {
      return;
    }

    buttons.forEach((button, index) => {
      const start = Number.parseInt(button.dataset.slideIndex || '0', 10);
      const next = buttons[index + 1];
      const end = next
        ? Number.parseInt(next.dataset.slideIndex || String(start), 10) - 1
        : slides.length - 1;
      const isActive = activeIndex >= start && activeIndex <= end;

      button.classList.toggle('is-active', isActive);
      button.setAttribute('aria-selected', isActive ? 'true' : 'false');
      button.tabIndex = isActive ? 0 : -1;
    });
  }

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

      once('carousel-toolbar-state', '.carousel[data-carousel-toolbar="true"]', context)
        .forEach((carousel) => {
          syncToolbarState(carousel);
          carousel.addEventListener('slid.bs.carousel', () => {
            syncToolbarState(carousel);
          });
        });
    },
  };
})(Drupal, once);
