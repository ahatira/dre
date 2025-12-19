/**
 * HeaderActions behavior
 * Handles user menu dropdown accessibility: toggle, click-outside, escape, focus.
 */

(() => {
  Drupal.behaviors.psHeaderActions = {
    attach: (context) => {
      const wrappers = context.querySelectorAll('.ps-header-actions__user-wrapper');
      wrappers.forEach((wrapper) => {
        const trigger = wrapper.querySelector('.ps-header-actions__user');
        const menu = wrapper.querySelector('.ps-header-actions__user-menu');
        if (!trigger || !menu) {
          return;
        }

        const openMenu = () => {
          menu.hidden = false;
          trigger.setAttribute('aria-expanded', 'true');
        };
        const closeMenu = () => {
          menu.hidden = true;
          trigger.setAttribute('aria-expanded', 'false');
        };

        once('ps-header-actions-user', trigger, () => {
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
