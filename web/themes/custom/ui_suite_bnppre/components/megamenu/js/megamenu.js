(function (Drupal, once) {
  const DESKTOP_BREAKPOINT = 992;
  const HIDE_DELAY = 120;

  Drupal.behaviors.psThemeMegamenu = {
    attach(context) {
      once('ps-theme-megamenu', '.ps-megamenu', context).forEach((menu) => {
        const dropdownItems = Array.from(menu.querySelectorAll('.ps-megamenu__item.dropdown'));
        if (!dropdownItems.length || !window.bootstrap || !window.bootstrap.Dropdown) {
          return;
        }

        const isDesktop = () => window.innerWidth >= DESKTOP_BREAKPOINT;

        const getInstance = (item) => {
          const toggle = item.querySelector('.ps-megamenu__toggle[data-bs-toggle="dropdown"]');
          return toggle ? window.bootstrap.Dropdown.getOrCreateInstance(toggle) : null;
        };

        const closeAll = (exceptItem = null) => {
          dropdownItems.forEach((item) => {
            if (exceptItem && item === exceptItem) {
              return;
            }
            const instance = getInstance(item);
            if (instance) {
              instance.hide();
            }
          });
        };

        dropdownItems.forEach((item) => {
          let hideTimer = null;

          const clearHideTimer = () => {
            if (hideTimer) {
              window.clearTimeout(hideTimer);
              hideTimer = null;
            }
          };

          const showItem = () => {
            if (!isDesktop()) {
              return;
            }
            clearHideTimer();
            closeAll(item);
            const instance = getInstance(item);
            if (instance) {
              instance.show();
            }
          };

          const hideItem = () => {
            clearHideTimer();
            hideTimer = window.setTimeout(() => {
              if (item.matches(':hover') || item.contains(document.activeElement)) {
                return;
              }
              const instance = getInstance(item);
              if (instance) {
                instance.hide();
              }
            }, HIDE_DELAY);
          };

          item.addEventListener('mouseenter', showItem);
          item.addEventListener('mouseleave', hideItem);
          item.addEventListener('focusin', showItem);
          item.addEventListener('focusout', hideItem);
        });

        menu.addEventListener('keydown', (event) => {
          if (event.key === 'Escape') {
            closeAll();
          }
        });

        window.addEventListener('resize', () => {
          if (!isDesktop()) {
            closeAll();
          }
        });
      });
    },
  };
})(Drupal, once);
