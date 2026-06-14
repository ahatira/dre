/**
 * @file
 * Expert journey stepper — crossfade like bnppre.fr slice-accompagnement.
 *
 * bnppre calls fadeOut("slow") on all panels then fadeIn("slow") on the target
 * without waiting — both animations run in parallel (~600ms).
 */

(function (Drupal, once) {
  'use strict';

  const FADE_MS = 600;

  Drupal.behaviors.psExpertSteps = {
    attach(context) {
      once('ps-expert-steps', '.ps-expert-steps', context).forEach((root) => {
        const navList = root.querySelector('.ps-expert-steps__nav-list');
        const navItems = root.querySelectorAll('.ps-expert-steps__nav-item');
        const panels = root.querySelectorAll('.ps-expert-steps__panel');
        if (!navItems.length || !panels.length) {
          return;
        }

        let isAnimating = false;

        const setNavState = (index) => {
          if (navList) {
            navList.classList.add('is-interacted');
          }

          navItems.forEach((item, itemIndex) => {
            const isActive = itemIndex === index;
            item.classList.toggle('is-active', isActive);
            if (isActive) {
              item.setAttribute('aria-current', 'step');
            }
            else {
              item.removeAttribute('aria-current');
            }
          });
        };

        const hidePanel = (panel) => {
          panel.classList.remove('is-active', 'is-leaving');
          panel.style.visibility = '';
          panel.setAttribute('aria-hidden', 'true');
        };

        const activate = (index) => {
          if (isAnimating || index < 0 || index >= panels.length) {
            return;
          }

          const currentIndex = [...panels].findIndex(
            (panel) => panel.classList.contains('is-active'),
          );
          if (currentIndex === index) {
            return;
          }

          const outgoing = currentIndex >= 0 ? panels[currentIndex] : null;
          const incoming = panels[index];

          if (!outgoing) {
            setNavState(index);
            incoming.style.visibility = 'visible';
            incoming.classList.add('is-active');
            incoming.setAttribute('aria-hidden', 'false');
            return;
          }

          isAnimating = true;
          setNavState(index);

          incoming.classList.remove('is-active');
          incoming.style.visibility = 'visible';
          incoming.setAttribute('aria-hidden', 'false');

          outgoing.classList.remove('is-leaving');
          outgoing.classList.add('is-active');
          outgoing.setAttribute('aria-hidden', 'false');

          window.requestAnimationFrame(() => {
            window.requestAnimationFrame(() => {
              outgoing.classList.remove('is-active');
              outgoing.classList.add('is-leaving');
              outgoing.setAttribute('aria-hidden', 'true');

              incoming.classList.add('is-active');
            });
          });

          window.setTimeout(() => {
            hidePanel(outgoing);
            isAnimating = false;
          }, FADE_MS);
        };

        navItems.forEach((item, index) => {
          item.addEventListener('click', () => activate(index));
        });
      });
    },
  };
})(Drupal, once);
