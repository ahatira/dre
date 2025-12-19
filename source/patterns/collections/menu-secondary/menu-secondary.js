/**
 * Menu Secondary behavior
 * Handles user menu dropdown accessibility: toggle, click-outside, escape, focus.
 */

(() => {
  Drupal.behaviors.psMenuSecondary = {
    attach: (context) => {
      const wrappers = context.querySelectorAll('.ps-menu-secondary__user-wrapper');
      wrappers.forEach((wrapper) => {
        const trigger = wrapper.querySelector('.ps-menu-secondary__user-trigger');
        const dropdown = wrapper.querySelector('.ps-menu-secondary__dropdown');
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

        once('ps-menu-secondary-user', trigger, () => {
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
          if (!wrapper.contains(e.target)) {
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
