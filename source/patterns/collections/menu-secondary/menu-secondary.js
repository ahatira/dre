/**
 * Menu Secondary behavior
 * Handles dropdown accessibility: toggle, click-outside, escape, keyboard.
 */

(() => {
  Drupal.behaviors.psMenuSecondary = {
    attach: (context) => {
      const items = context.querySelectorAll('.ps-menu-secondary__item--has-dropdown');
      items.forEach((item) => {
        const trigger = item.querySelector('.ps-menu-secondary__link--dropdown');
        const dropdown = item.querySelector('.ps-menu-secondary__dropdown');
        if (!trigger || !dropdown) {
          return;
        }

        const openMenu = () => {
          dropdown.hidden = false;
          trigger.setAttribute('aria-expanded', 'true');
        };
        const closeMenu = () => {
          dropdown.hidden = true;
          trigger.setAttribute('aria-expanded', 'false');
        };

        once('ps-menu-secondary-dropdown', trigger, () => {
          trigger.addEventListener('click', (e) => {
            e.preventDefault();
            const expanded = trigger.getAttribute('aria-expanded') === 'true';
            if (expanded) {
              closeMenu();
            } else {
              openMenu();
            }
          });
        });

        // Click outside to close
        document.addEventListener('click', (e) => {
          if (!item.contains(e.target)) {
            closeMenu();
          }
        });

        // Escape key to close
        document.addEventListener('keydown', (e) => {
          if (e.key === 'Escape') {
            closeMenu();
            trigger.focus();
          }
        });
      });
    },
  };
})();
