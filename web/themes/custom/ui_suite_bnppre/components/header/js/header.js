(function (Drupal, once) {
  const STICKY_ENTER_THRESHOLD = 90;
  const STICKY_EXIT_THRESHOLD = 70;
  const DESKTOP_QUERY = '(min-width: 992px)';

  const FOCUSABLE_SELECTOR = [
    'a[href]',
    'button:not([disabled])',
    'input:not([disabled])',
    'select:not([disabled])',
    'textarea:not([disabled])',
    '[tabindex]:not([tabindex="-1"])',
  ].join(',');

  Drupal.behaviors.psThemeHeader = {
    attach(context) {
      once('ps-theme-header', '[data-header]', context).forEach((header) => {
        const openButton = header.querySelector('[data-header-open]');
        const closeButton = header.querySelector('[data-header-close]');
        const panel = header.querySelector('[data-header-panel]');
        const media = window.matchMedia(DESKTOP_QUERY);

        if (!openButton || !closeButton || !panel) {
          return;
        }

        let isSticky = false;
        let isOpen = false;
        let ticking = false;

        const isDesktop = () => media.matches;

        const setButtonVisibility = (button, visible) => {
          button.classList.toggle('is-visible', visible);
          button.setAttribute('aria-hidden', visible ? 'false' : 'true');
          button.tabIndex = visible ? 0 : -1;
        };

        const setFocusTrap = (event) => {
          if (!isOpen || event.key !== 'Tab') {
            return;
          }

          const focusable = Array.from(panel.querySelectorAll(FOCUSABLE_SELECTOR));
          if (focusable.length === 0) {
            event.preventDefault();
            closeButton.focus();
            return;
          }

          const first = focusable[0];
          const last = focusable[focusable.length - 1];
          const active = document.activeElement;

          if (event.shiftKey && (active === first || active === closeButton)) {
            event.preventDefault();
            last.focus();
          }
          else if (!event.shiftKey && active === last) {
            event.preventDefault();
            closeButton.focus();
          }
        };

        const syncA11yState = () => {
          const panelCollapsed = isDesktop()
            ? (isSticky && !isOpen)
            : !isOpen;
          const expanded = !panelCollapsed;
          const showOpenButton = isDesktop()
            ? (isSticky && !isOpen)
            : !isOpen;
          const showCloseButton = isOpen;

          openButton.setAttribute('aria-expanded', expanded ? 'true' : 'false');
          panel.setAttribute('aria-hidden', panelCollapsed ? 'true' : 'false');

          setButtonVisibility(openButton, showOpenButton);
          setButtonVisibility(closeButton, showCloseButton);
        };

        const syncState = () => {
          const stickyActive = isDesktop() && isSticky;
          const openActive = isDesktop() ? (isSticky && isOpen) : isOpen;

          header.classList.toggle('is-mobile', !isDesktop());
          header.classList.toggle('is-sticky', stickyActive);
          header.classList.toggle('is-open', openActive);
          syncA11yState();
        };

        const refreshSticky = () => {
          if (!isDesktop()) {
            if (isSticky) {
              isSticky = false;
              syncState();
            }
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
            isOpen = false;
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

        const openMenu = () => {
          if (isDesktop() && !isSticky) {
            return;
          }

          isOpen = true;
          syncState();

          const firstFocusable = panel.querySelector(FOCUSABLE_SELECTOR);
          if (firstFocusable) {
            firstFocusable.focus();
          }
          else {
            closeButton.focus();
          }
        };

        const closeMenu = () => {
          isOpen = false;
          syncState();
          openButton.focus();
        };

        const onKeydown = (event) => {
          if (event.key === 'Escape' && isSticky && isOpen) {
            event.preventDefault();
            closeMenu();
            return;
          }

          setFocusTrap(event);
        };

        openButton.addEventListener('click', openMenu);
        closeButton.addEventListener('click', closeMenu);
        document.addEventListener('keydown', onKeydown);
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
