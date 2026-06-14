(function (Drupal, once) {
  'use strict';

  Drupal.behaviors.psHomepageOffersCarousel = {
    attach(context) {
      once('ps-homepage-offers-carousel', '.ps-homepage-offers-carousel', context).forEach((root) => {
        const track = root.querySelector('.ps-homepage-offers-carousel__track');
        const prev = root.querySelector('[data-carousel-prev]');
        const next = root.querySelector('[data-carousel-next]');
        if (!track) {
          return;
        }

        const scrollByCard = (direction) => {
          const item = track.querySelector('.ps-homepage-offers-carousel__item');
          if (!item) {
            return;
          }
          const gap = parseFloat(getComputedStyle(track).columnGap || getComputedStyle(track).gap || '0') || 0;
          const amount = item.getBoundingClientRect().width + gap;
          track.scrollBy({ left: direction * amount, behavior: 'smooth' });
        };

        if (prev) {
          prev.addEventListener('click', () => scrollByCard(-1));
        }
        if (next) {
          next.addEventListener('click', () => scrollByCard(1));
        }
      });
    },
  };
})(Drupal, once);
