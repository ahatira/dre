(function (Drupal, once) {
  'use strict';

  const SCROLL_EPSILON = 2;
  const MOBILE_QUERY = '(max-width: 991.98px)';

  Drupal.behaviors.psHomepageNewsCarousel = {
    attach(context) {
      once('ps-homepage-news-carousel', '.ps-homepage-news__carousel', context).forEach((root) => {
        const viewport = root.querySelector('.ps-homepage-news__viewport');
        const track = viewport.querySelector('.ps-homepage-news__view > div')
          || viewport.querySelector('.ps-homepage-news__track')
          || viewport.querySelector('.view-content')
          || viewport.querySelector('.views-row')?.parentElement;
        const dotsRoot = root.querySelector('.ps-homepage-news__dots');
        if (!viewport || !track) {
          return;
        }

        const items = [...track.querySelectorAll('.views-row')];
        const mobileQuery = window.matchMedia(MOBILE_QUERY);
        let dots = [];

        const scrollStep = () => {
          const item = items[0];
          if (!item) {
            return 0;
          }
          const gap = Number.parseFloat(getComputedStyle(track).columnGap || getComputedStyle(track).gap || '0') || 0;
          return item.getBoundingClientRect().width + gap;
        };

        const activeDotIndex = () => {
          if (items.length === 0) {
            return 0;
          }
          const step = scrollStep();
          if (step <= 0) {
            return 0;
          }
          return Math.min(items.length - 1, Math.round(track.scrollLeft / step));
        };

        const syncDots = () => {
          if (dots.length === 0) {
            return;
          }
          const active = activeDotIndex();
          dots.forEach((dot, index) => {
            dot.classList.toggle('is-active', index === active);
            dot.setAttribute('aria-selected', index === active ? 'true' : 'false');
          });
        };

        const buildDots = () => {
          if (!dotsRoot) {
            return;
          }
          dotsRoot.textContent = '';
          dots = [];
          if (!mobileQuery.matches || items.length <= 1) {
            dotsRoot.hidden = true;
            return;
          }
          dotsRoot.hidden = false;
          items.forEach((item, index) => {
            const dot = document.createElement('button');
            dot.type = 'button';
            dot.className = 'ps-homepage-news__dot';
            dot.setAttribute('role', 'tab');
            dot.setAttribute('aria-label', Drupal.t('Go to article @number', { '@number': index + 1 }));
            dot.addEventListener('click', () => {
              const step = scrollStep();
              track.scrollTo({ left: step * index, behavior: 'smooth' });
            });
            dotsRoot.appendChild(dot);
            dots.push(dot);
          });
          syncDots();
        };

        const syncLayout = () => {
          buildDots();
          syncDots();
        };

        track.addEventListener('scroll', syncDots, { passive: true });
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
