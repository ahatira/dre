/**
 * @file
 * Mega-menu — full-width panels (BNPPRE-style hover/click).
 *
 * Works with advanced_mega_menu (ps-mega-menu templates). Navigation is
 * rendered twice (mobile + desktop); only the visible instance is interactive.
 */
(function (Drupal) {
  'use strict';

  const DESKTOP_QUERY = '(min-width: 992px)';

  Drupal.behaviors.psMegaMenu = {
    attach(context) {
      const menus = once('ps-mega-menu', '[data-ps-mega-menu]', context);
      menus.forEach((menu) => {
        const triggers = menu.querySelectorAll('[data-ps-mega-trigger]');
        const panelsContainer = menu.querySelector('.ps-mega-menu__panels');
        const desktopQuery = window.matchMedia(DESKTOP_QUERY);
        let closeTimer = null;

        if (!panelsContainer || triggers.length === 0) {
          return;
        }

        /** Navigation is rendered twice (mobile + desktop); only one is active. */
        const isActiveInstance = () => {
          const inMobilePanel = Boolean(menu.closest('.ps-site-header__mobile-panel'));
          return desktopQuery.matches ? !inMobilePanel : inMobilePanel;
        };

        const getPanel = (panelId) => {
          if (!panelId) {
            return null;
          }
          return menu.querySelector(`#${CSS.escape(panelId)}`);
        };

        const getPanels = () => menu.querySelectorAll('.ps-mega-menu__panel');

        const layoutPanels = () => {
          if (!isActiveInstance()) {
            return;
          }
          const isMobile = !desktopQuery.matches;

          if (!isMobile) {
            if (panelsContainer.parentElement !== menu) {
              menu.appendChild(panelsContainer);
            }
            triggers.forEach((trigger) => {
              const panel = getPanel(trigger.getAttribute('aria-controls'));
              if (panel && panel.parentElement !== panelsContainer) {
                panelsContainer.appendChild(panel);
              }
            });
            return;
          }

          triggers.forEach((trigger) => {
            const panel = getPanel(trigger.getAttribute('aria-controls'));
            const item = trigger.closest('.ps-mega-menu__item');
            if (!panel || !item || panel.parentElement === item) {
              return;
            }
            item.appendChild(panel);
          });
        };

        layoutPanels();
        desktopQuery.addEventListener('change', () => {
          closeAll();
          layoutPanels();
        });

        const closeAll = () => {
          if (!isActiveInstance()) {
            return;
          }
          triggers.forEach((trigger) => {
            trigger.setAttribute('aria-expanded', 'false');
            trigger.classList.remove('is-open');
          });
          getPanels().forEach((panel) => {
            panel.hidden = true;
            panel.classList.remove('is-open');
          });
          menu.classList.remove('ps-mega-menu--open');
        };

        const openPanel = (trigger, panel) => {
          if (!isActiveInstance() || !panel) {
            return;
          }
          closeAll();
          trigger.setAttribute('aria-expanded', 'true');
          trigger.classList.add('is-open');
          panel.hidden = false;
          panel.classList.add('is-open');
          menu.classList.add('ps-mega-menu--open');
        };

        const isWithinMenu = (target) => {
          return menu.contains(target) || panelsContainer.contains(target);
        };

        triggers.forEach((trigger) => {
          trigger.addEventListener('click', (event) => {
            if (!isActiveInstance()) {
              return;
            }
            const panel = getPanel(trigger.getAttribute('aria-controls'));
            if (!panel) {
              return;
            }
            event.preventDefault();
            event.stopPropagation();
            if (panel.classList.contains('is-open')) {
              closeAll();
            }
            else {
              openPanel(trigger, panel);
            }
          });

          trigger.addEventListener('mouseenter', () => {
            if (!desktopQuery.matches || !isActiveInstance()) {
              return;
            }
            const panel = getPanel(trigger.getAttribute('aria-controls'));
            if (!panel) {
              return;
            }
            window.clearTimeout(closeTimer);
            openPanel(trigger, panel);
          });

          trigger.addEventListener('mouseleave', () => {
            if (!desktopQuery.matches || !isActiveInstance()) {
              return;
            }
            closeTimer = window.setTimeout(closeAll, 180);
          });
        });

        getPanels().forEach((panel) => {
          panel.addEventListener('mouseenter', () => {
            if (!desktopQuery.matches || !isActiveInstance()) {
              return;
            }
            window.clearTimeout(closeTimer);
          });

          panel.addEventListener('mouseleave', () => {
            if (!desktopQuery.matches || !isActiveInstance()) {
              return;
            }
            closeTimer = window.setTimeout(closeAll, 180);
          });
        });

        document.addEventListener('keydown', (event) => {
          if (event.key === 'Escape') {
            closeAll();
          }
        });

        document.addEventListener('click', (event) => {
          if (!isActiveInstance()) {
            return;
          }
          if (!isWithinMenu(event.target)) {
            closeAll();
          }
        });
      });
    },
  };
})(Drupal);
