(function (Drupal, once) {
  'use strict';

  const MOBILE_QUERY = '(max-width: 991.98px)';

  Drupal.behaviors.psHomepageMarketStudiesCarousel = {
    attach(context) {
      once('ps-homepage-market-studies-carousel', '.ps-homepage-market-studies__carousel', context).forEach((root) => {
        const viewport = root.querySelector('.ps-homepage-market-studies__viewport');
        const track = root.querySelector('.ps-homepage-market-studies__track');
        if (!viewport || !track) {
          return;
        }

        const mobileQuery = window.matchMedia(MOBILE_QUERY);

        const syncLayout = () => {
          if (!mobileQuery.matches || track.children.length <= 1) {
            track.style.removeProperty('scroll-padding-inline');
            return;
          }

          const firstItem = track.querySelector('.ps-homepage-market-studies__item');
          if (!firstItem) {
            return;
          }

          const peek = Math.max(0, (track.clientWidth - firstItem.getBoundingClientRect().width) / 2);
          track.style.scrollPaddingInline = `${peek}px`;
        };

        track.addEventListener('scroll', () => {}, { passive: true });
        if (typeof mobileQuery.addEventListener === 'function') {
          mobileQuery.addEventListener('change', syncLayout);
        }
        else {
          mobileQuery.addListener(syncLayout);
        }
        window.addEventListener('resize', syncLayout, { passive: true });

        syncLayout();
      });
    },
  };
})(Drupal, once);
