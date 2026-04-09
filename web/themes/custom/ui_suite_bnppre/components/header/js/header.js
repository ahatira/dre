(function (Drupal, once) {
  const STICKY_ENTER_THRESHOLD = 90;
  const STICKY_LEAVE_THRESHOLD = 64;

  Drupal.behaviors.psThemeHeader = {
    attach(context) {
      once('ps-theme-header', '[data-header]', context).forEach((header) => {
        const menuToggle = header.querySelector('[data-header-button-toggle]');
        const closeToggle = header.querySelector('[data-header-button-close]');
        const offcanvasElement = header.querySelector('[data-header-panel]');

        if (!menuToggle || !closeToggle || !offcanvasElement) {
          return;
        }

        const getOffcanvasInstance = () => {
          if (!window.bootstrap || !window.bootstrap.Offcanvas) {
            return null;
          }
          return window.bootstrap.Offcanvas.getOrCreateInstance(offcanvasElement);
        };

        const isDesktop = () => window.innerWidth >= 992;

        const syncDesktopControls = () => {
          const isOpen = header.classList.contains('is-open');
          menuToggle.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
          closeToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        };

        const updateHeader = () => {
          const desktop = isDesktop();

          if (desktop) {
            const sticky = header.classList.contains('is-sticky');

            if (!sticky && window.scrollY > STICKY_ENTER_THRESHOLD) {
              header.classList.add('is-sticky');
            }

            if (sticky && window.scrollY < STICKY_LEAVE_THRESHOLD) {
              header.classList.remove('is-sticky');
            }
          }
          else {
            header.classList.remove('is-sticky');
          }

          const offcanvas = getOffcanvasInstance();
          if (desktop && offcanvasElement.classList.contains('show') && offcanvas) {
            offcanvas.hide();
          }

          if (!desktop || !header.classList.contains('is-sticky')) {
            header.classList.remove('is-open');
          }

          syncDesktopControls();
        };

        window.addEventListener('scroll', updateHeader, { passive: true });
        window.addEventListener('resize', updateHeader);

        offcanvasElement.addEventListener('shown.bs.offcanvas', () => {
          syncDesktopControls();
        });

        offcanvasElement.addEventListener('hidden.bs.offcanvas', () => {
          syncDesktopControls();
        });

        menuToggle.addEventListener('click', (event) => {
          const desktop = isDesktop();

          if (desktop && header.classList.contains('is-sticky')) {
            event.preventDefault();
            header.classList.add('is-open');
            syncDesktopControls();
            return;
          }

          if (!desktop) {
            event.preventDefault();
            const offcanvas = getOffcanvasInstance();
            if (offcanvas) {
              offcanvas.show();
            }
          }
        });

        closeToggle.addEventListener('click', (event) => {
          if (isDesktop() && header.classList.contains('is-sticky')) {
            event.preventDefault();
            header.classList.remove('is-open');
            syncDesktopControls();
          }
        });

        updateHeader();
      });
    },
  };
})(Drupal, once);
