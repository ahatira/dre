(function (Drupal, once) {
  const STICKY_ENTER_THRESHOLD = 84;
  const STICKY_EXIT_THRESHOLD = 56;
  const DESKTOP_QUERY = '(min-width: 992px)';

  Drupal.behaviors.psThemeHeader = {
    attach(context) {
      once('ps-theme-header', '[data-header]', context).forEach((header) => {
        const desktopOpenButton = header.querySelector('[data-header-open-desktop]');
        const desktopCloseButton = header.querySelector('[data-header-close-desktop]');
        const toggleButton = header.querySelector('[data-header-trigger]');
        const mobileCloseButton = header.querySelector('[data-header-close-mobile]');
        const panel = header.querySelector('[data-header-panel]');
        const media = window.matchMedia(DESKTOP_QUERY);

        if (!toggleButton || !mobileCloseButton || !panel || !desktopOpenButton || !desktopCloseButton) {
          return;
        }

        let isSticky = false;
        let isDesktopOpen = false;
        let ticking = false;

        const isDesktop = () => media.matches;

        const isExpanded = () => panel.classList.contains('show');

        const syncState = () => {
          const stickyActive = isDesktop() && isSticky;
          const openActive = isDesktop() ? isDesktopOpen : isExpanded();
          const mobileActive = !isDesktop();

          header.classList.toggle('is-sticky', stickyActive);
          header.classList.toggle('is-open', openActive);

          toggleButton.setAttribute('aria-expanded', openActive ? 'true' : 'false');
          panel.setAttribute('aria-hidden', openActive ? 'false' : 'true');

          const showMobileMenu = mobileActive && !openActive;
          const showMobileClose = mobileActive && openActive;
          const showDesktopOpen = stickyActive && !openActive;
          const showDesktopClose = stickyActive && openActive;

          // Mobile menu toggle (hamburger icon)
          if (!showMobileMenu) {
            toggleButton.blur();
          }
          toggleButton.setAttribute('aria-hidden', showMobileMenu ? 'false' : 'true');
          toggleButton.tabIndex = showMobileMenu ? 0 : -1;
          if (showMobileMenu) {
            toggleButton.removeAttribute('inert');
          } else {
            toggleButton.setAttribute('inert', '');
          }

          // Mobile close button
          if (!showMobileClose) {
            mobileCloseButton.blur();
          }
          mobileCloseButton.setAttribute('aria-hidden', showMobileClose ? 'false' : 'true');
          mobileCloseButton.tabIndex = showMobileClose ? 0 : -1;
          if (showMobileClose) {
            mobileCloseButton.removeAttribute('inert');
          } else {
            mobileCloseButton.setAttribute('inert', '');
          }

          // Desktop open button (hamburger in sticky header)
          if (!showDesktopOpen) {
            desktopOpenButton.blur();
          }
          desktopOpenButton.setAttribute('aria-hidden', showDesktopOpen ? 'false' : 'true');
          desktopOpenButton.tabIndex = showDesktopOpen ? 0 : -1;
          if (showDesktopOpen) {
            desktopOpenButton.removeAttribute('inert');
          } else {
            desktopOpenButton.setAttribute('inert', '');
          }

          // Desktop close button
          if (!showDesktopClose) {
            desktopCloseButton.blur();
          }
          desktopCloseButton.setAttribute('aria-hidden', showDesktopClose ? 'false' : 'true');
          desktopCloseButton.tabIndex = showDesktopClose ? 0 : -1;
          if (showDesktopClose) {
            desktopCloseButton.removeAttribute('inert');
          } else {
            desktopCloseButton.setAttribute('inert', '');
          }
        };

        const refreshSticky = () => {
          if (!isDesktop()) {
            if (isSticky) {
              isSticky = false;
            }
            isDesktopOpen = false;
            syncState();
            return;
          }

          const scrollTop = window.scrollY || window.pageYOffset || 0;
          const nextSticky = isSticky
            ? scrollTop > STICKY_EXIT_THRESHOLD
            : scrollTop > STICKY_ENTER_THRESHOLD;
          if (nextSticky === isSticky) {
            return;
          }

          isSticky = nextSticky;
          if (!isSticky) {
            isDesktopOpen = false;
          }
          syncState();
        };

        const onScroll = () => {
          if (ticking) {
            return;
          }

          ticking = true;
          window.requestAnimationFrame(() => {
            refreshSticky();
            ticking = false;
          });
        };

        panel.addEventListener('show.bs.collapse', () => {
          syncState();
        });

        panel.addEventListener('shown.bs.collapse', () => {
          syncState();
        });

        panel.addEventListener('hide.bs.collapse', () => {
          syncState();
        });

        panel.addEventListener('hidden.bs.collapse', () => {
          syncState();
        });

        desktopOpenButton.addEventListener('click', (event) => {
          event.preventDefault();
          if (!isDesktop() || !isSticky) {
            return;
          }

          isDesktopOpen = true;
          syncState();
        });

        desktopCloseButton.addEventListener('click', (event) => {
          event.preventDefault();
          if (!isDesktop()) {
            return;
          }

          isDesktopOpen = false;
          syncState();
        });

        window.addEventListener('scroll', onScroll, { passive: true });
        window.addEventListener('resize', refreshSticky);

        if (typeof media.addEventListener === 'function') {
          media.addEventListener('change', refreshSticky);
        }
        else if (typeof media.addListener === 'function') {
          media.addListener(refreshSticky);
        }

        syncState();
        refreshSticky();
      });
    },
  };
})(Drupal, once);
