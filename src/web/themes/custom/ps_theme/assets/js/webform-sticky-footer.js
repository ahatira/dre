/**
 * @file
 * Sticky state for webform footers (submit + urgency help).
 */
(function (Drupal, once) {
  'use strict';

  /**
   * Marks sticky footer when it is pinned during scroll.
   */
  Drupal.behaviors.psWebformStickyFooter = {
    attach(context) {
      once('ps-webform-sticky-footer', '.ps-webform-sticky-footer', context).forEach((footer) => {
        const scrollRoot = footer.closest('.offcanvas-body, .modal-body, .ui-dialog-content')
          || null;
        const sentinel = document.createElement('div');
        sentinel.className = 'ps-webform-sticky-footer__sentinel';
        sentinel.setAttribute('aria-hidden', 'true');
        footer.parentElement?.insertBefore(sentinel, footer);

        const observer = new IntersectionObserver(
          ([entry]) => {
            footer.classList.toggle('is-stuck', !entry.isIntersecting);
          },
          {
            root: scrollRoot,
            threshold: [1],
          },
        );

        observer.observe(sentinel);
      });
    },
  };
})(Drupal, once);
