(function (Drupal, once) {
  'use strict';

  Drupal.behaviors.psOfferDetail = {
    attach(context) {
      once('ps-offer-mobile-actions', '.ps-offer-detail', context).forEach((offer) => {
        const bar = offer.querySelector('.ps-offer-agent-card__mobile-actions');
        if (!bar) {
          return;
        }

        if (bar.parentElement !== offer) {
          offer.appendChild(bar);
        }

        const mobileQuery = window.matchMedia('(max-width: 991.98px)');
        const updateBarPosition = () => {
          if (!mobileQuery.matches) {
            bar.classList.remove('is-fixed', 'is-contained');
            return;
          }

          const offerRect = offer.getBoundingClientRect();
          if (offerRect.bottom > window.innerHeight) {
            bar.classList.add('is-fixed');
            bar.classList.remove('is-contained');
            return;
          }

          bar.classList.remove('is-fixed');
          bar.classList.add('is-contained');
        };

        updateBarPosition();
        window.addEventListener('scroll', updateBarPosition, { passive: true });
        window.addEventListener('resize', updateBarPosition);
        mobileQuery.addEventListener('change', updateBarPosition);
      });

      once('ps-offer-budget-info', '[data-ps-budget-info]', context).forEach((trigger) => {
        const tooltip = trigger.closest('.ps-offer-budget__tooltip');
        const panel = tooltip?.querySelector('.ps-offer-budget__tooltip-panel');
        if (!tooltip || !panel) {
          return;
        }

        const closePanel = () => {
          panel.classList.add('is-hidden');
          trigger.setAttribute('aria-expanded', 'false');
        };

        const openPanel = () => {
          panel.classList.remove('is-hidden');
          trigger.setAttribute('aria-expanded', 'true');
        };

        trigger.addEventListener('click', (event) => {
          event.preventDefault();
          event.stopPropagation();
          if (panel.classList.contains('is-hidden')) {
            openPanel();
          }
          else {
            closePanel();
          }
        });

        document.addEventListener('click', (event) => {
          if (!tooltip.contains(event.target)) {
            closePanel();
          }
        });

        document.addEventListener('keydown', (event) => {
          if (event.key === 'Escape') {
            closePanel();
            trigger.focus();
          }
        });
      });

      once('ps-offer-read-more', '.ps-offer-description--collapsible', context).forEach((wrapper) => {
        const clip = wrapper.querySelector('.ps-offer-description__clip');
        const content = wrapper.querySelector('.ps-offer-description__content');
        const fade = wrapper.querySelector('.ps-offer-description__fade');
        const toggle = wrapper.querySelector('[data-ps-read-more]');
        if (!clip || !content || !toggle) {
          return;
        }

        const readMoreLabel = toggle.getAttribute('data-read-more-label') || 'Read more';
        const readLessLabel = toggle.getAttribute('data-read-less-label') || 'Read less';
        const toggleLabel = toggle.querySelector('.ps-offer-description__toggle-label');
        const collapsedHeight = clip.getBoundingClientRect().height;
        const fullHeight = content.scrollHeight;

        if (fullHeight <= collapsedHeight + 1) {
          wrapper.classList.add('is-fully-visible');
          toggle.hidden = true;
          if (fade) {
            fade.hidden = true;
          }
          return;
        }

        clip.style.maxHeight = `${Math.ceil(collapsedHeight)}px`;
        wrapper.style.setProperty('--ps-offer-description-expanded-height', `${fullHeight}px`);

        toggle.addEventListener('click', () => {
          const expanded = wrapper.classList.toggle('is-expanded');
          clip.style.maxHeight = expanded ? `${fullHeight}px` : `${Math.ceil(collapsedHeight)}px`;
          if (toggleLabel) {
            toggleLabel.textContent = expanded ? readLessLabel : readMoreLabel;
          }
          toggle.setAttribute('aria-expanded', expanded ? 'true' : 'false');
        });
      });
    },
  };
})(Drupal, once);
