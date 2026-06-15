(function (Drupal, once) {
  'use strict';

  const SCROLL_EPSILON = 2;
  const MOBILE_QUERY = '(max-width: 991.98px)';

  Drupal.behaviors.psHomepageOffersCarousel = {
    attach(context) {
      once('ps-homepage-offers-carousel', '.ps-homepage-offers-carousel', context).forEach((root) => {
        const viewport = root.querySelector('.ps-homepage-offers-carousel__viewport');
        const track = root.querySelector('.ps-homepage-offers-carousel__track');
        const dotsRoot = root.querySelector('.ps-homepage-offers-carousel__dots');
        const prev = root.querySelector('[data-carousel-prev]');
        const next = root.querySelector('[data-carousel-next]');
        if (!viewport || !track) {
          return;
        }

        const items = [...track.querySelectorAll('.ps-homepage-offers-carousel__item')];
        const mobileQuery = window.matchMedia(MOBILE_QUERY);
        const autoplayEnabled = viewport.dataset.autoplay === 'true';
        const autoplayInterval = Number.parseInt(viewport.dataset.autoplayInterval || '5000', 10) || 5000;
        let autoplayTimer = null;
        let autoplayPaused = false;
        let dots = [];

        const scrollStep = () => {
          const item = items[0];
          if (!item) {
            return 0;
          }
          const gap = Number.parseFloat(getComputedStyle(track).columnGap || getComputedStyle(track).gap || '0') || 0;
          return item.getBoundingClientRect().width + gap;
        };

        const syncControls = () => {
          const maxScroll = track.scrollWidth - track.clientWidth;
          const atStart = track.scrollLeft <= SCROLL_EPSILON;
          const atEnd = track.scrollLeft >= maxScroll - SCROLL_EPSILON;
          if (prev) {
            prev.disabled = atStart;
          }
          if (next) {
            next.disabled = atEnd || maxScroll <= SCROLL_EPSILON;
          }
          syncDots();
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
          items.forEach((item, index) => {
            const dot = document.createElement('button');
            dot.type = 'button';
            dot.className = 'ps-homepage-offers-carousel__dot';
            dot.setAttribute('role', 'tab');
            dot.setAttribute('aria-label', Drupal.t('Go to offer @number', { '@number': index + 1 }));
            dot.addEventListener('click', () => {
              const step = scrollStep();
              track.scrollTo({ left: step * index, behavior: 'smooth' });
              pauseAutoplay();
            });
            dotsRoot.appendChild(dot);
            dots.push(dot);
          });
          syncDots();
        };

        const scrollByCard = (direction) => {
          const amount = scrollStep();
          if (amount <= 0) {
            return;
          }
          track.scrollBy({ left: direction * amount, behavior: 'smooth' });
        };

        const stopAutoplay = () => {
          if (autoplayTimer !== null) {
            window.clearInterval(autoplayTimer);
            autoplayTimer = null;
          }
        };

        const startAutoplay = () => {
          stopAutoplay();
          if (!autoplayEnabled || autoplayPaused || mobileQuery.matches) {
            return;
          }
          autoplayTimer = window.setInterval(() => {
            const maxScroll = track.scrollWidth - track.clientWidth;
            if (track.scrollLeft >= maxScroll - SCROLL_EPSILON) {
              track.scrollTo({ left: 0, behavior: 'smooth' });
            }
            else {
              scrollByCard(1);
            }
          }, autoplayInterval);
        };

        const pauseAutoplay = () => {
          autoplayPaused = true;
          stopAutoplay();
        };

        const resumeAutoplay = () => {
          autoplayPaused = false;
          startAutoplay();
        };

        if (prev) {
          prev.addEventListener('click', () => {
            scrollByCard(-1);
            pauseAutoplay();
          });
        }

        if (next) {
          next.addEventListener('click', () => {
            scrollByCard(1);
            pauseAutoplay();
          });
        }

        track.addEventListener('scroll', syncControls, { passive: true });
        window.addEventListener('resize', () => {
          syncControls();
          startAutoplay();
        }, { passive: true });

        root.addEventListener('mouseenter', pauseAutoplay);
        root.addEventListener('mouseleave', resumeAutoplay);
        root.addEventListener('focusin', pauseAutoplay);
        root.addEventListener('focusout', (event) => {
          if (!root.contains(event.relatedTarget)) {
            resumeAutoplay();
          }
        });
        track.addEventListener('touchstart', pauseAutoplay, { passive: true });
        track.addEventListener('touchend', resumeAutoplay, { passive: true });

        buildDots();
        syncControls();
        startAutoplay();
      });
    },
  };
})(Drupal, once);
