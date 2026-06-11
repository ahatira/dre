/**
 * @file
 * Header search — toggle full-width panel below the header bar.
 */
(function (Drupal, once) {
  'use strict';

  Drupal.behaviors.psHeaderSearch = {
    attach(context) {
      once('ps-header-search-panel', '[data-ps-header-search-panel]', context).forEach((panel) => {
        const toggles = document.querySelectorAll('[data-ps-header-search-toggle]');
        const header = document.querySelector('[data-ps-site-header]');
        const panelId = panel.id || 'ps-header-search-panel';

        if (toggles.length === 0) {
          return;
        }

        const setOpen = (open) => {
          panel.hidden = !open;
          toggles.forEach((toggle) => {
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
          });
          if (header) {
            header.classList.toggle('ps-site-header--search-open', open);
          }
          if (open) {
            const input = panel.querySelector(
              'input[type="search"], input[name="keys"], input[name="locality[]"]',
            );
            if (input) {
              input.focus();
            }
          }
        };

        toggles.forEach((toggle) => {
          toggle.setAttribute('aria-controls', panelId);
          toggle.addEventListener('click', (event) => {
            event.preventDefault();
            event.stopPropagation();
            setOpen(panel.hidden);
          });
        });

        document.addEventListener('click', (event) => {
          if (panel.hidden) {
            return;
          }
          if (panel.contains(event.target)) {
            return;
          }
          if ([...toggles].some((toggle) => toggle.contains(event.target))) {
            return;
          }
          setOpen(false);
        });

        document.addEventListener('keydown', (event) => {
          if (event.key === 'Escape' && !panel.hidden) {
            setOpen(false);
          }
        });
      });
    },
  };
})(Drupal, once);
