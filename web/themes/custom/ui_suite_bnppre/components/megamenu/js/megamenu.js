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

        const getMenu = (item) => item.querySelector('.ps-megamenu__menu');

        const closeMobileItem = (item) => {
          const toggle = item.querySelector('.ps-megamenu__toggle[data-bs-toggle="dropdown"]');
          const menu = getMenu(item);

          item.classList.remove('is-open');
          if (menu) {
            menu.classList.remove('show');
          }
          if (toggle) {
            toggle.classList.remove('show');
            toggle.setAttribute('aria-expanded', 'false');
          }
        };

        const openMobileItem = (item) => {
          const toggle = item.querySelector('.ps-megamenu__toggle[data-bs-toggle="dropdown"]');
          const menu = getMenu(item);

          item.classList.add('is-open');
          if (menu) {
            menu.classList.add('show');
          }
          if (toggle) {
            toggle.classList.add('show');
            toggle.setAttribute('aria-expanded', 'true');
          }
        };

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

        if (!isDesktop()) {
          dropdownItems.forEach((item) => {
            closeMobileItem(item);
          });
        }

        dropdownItems.forEach((item) => {
          let hideTimer = null;
          const toggle = item.querySelector('.ps-megamenu__toggle[data-bs-toggle="dropdown"]');

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

          if (toggle) {
            toggle.addEventListener('click', (event) => {
              if (isDesktop()) {
                return;
              }

              event.preventDefault();
              event.stopImmediatePropagation();

              const isOpen = item.classList.contains('is-open');
              dropdownItems.forEach((otherItem) => {
                if (otherItem !== item) {
                  closeMobileItem(otherItem);
                }
              });

              if (isOpen) {
                closeMobileItem(item);
              }
              else {
                openMobileItem(item);
              }
            }, true);
          }
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
          else {
            dropdownItems.forEach((item) => {
              closeMobileItem(item);
            });
          }
        });
      });
    },
  };
})(Drupal, once);
