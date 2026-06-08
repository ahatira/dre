/**
 * @file
 * Stellar header — fixed slot (static CSS padding) + compact/expand toggles.
 */
(function (Drupal) {
  'use strict';

  const DESKTOP_QUERY = '(min-width: 992px)';

  /**
   * Sync header top offset below Gin navigation top bar (editor preview).
   *
   * --drupal-displace-offset-top only accounts for Admin Toolbar; the Gin bar
   * below it wraps on mobile and must be included in --ps-editor-offset-top.
   */
  const syncEditorPreviewOffset = () => {
    const root = document.documentElement;
    if (!root.classList.contains('ps-editor-preview')) {
      return;
    }

    const ginBar = document.querySelector('.gin--navigation-top-bar');
    const adminBar = document.querySelector('.admin-toolbar-control-bar');
    let top = 0;

    if (ginBar) {
      top = ginBar.getBoundingClientRect().bottom;
    }
    else if (adminBar) {
      top = adminBar.getBoundingClientRect().bottom;
    }
    else {
      const raw = getComputedStyle(root).getPropertyValue('--drupal-displace-offset-top').trim();
      top = Number.parseFloat(raw) || 0;
    }

    root.style.setProperty('--ps-editor-offset-top', `${top}px`);
    syncMobileEditorHeaderPin();
  };

  /**
   * Mobile editor preview — pin site header to viewport top once page scrolls.
   */
  const syncMobileEditorHeaderPin = () => {
    const root = document.documentElement;
    const header = document.querySelector('[data-ps-site-header]');
    if (!header) {
      return;
    }

    if (!root.classList.contains('ps-editor-preview') || window.matchMedia(DESKTOP_QUERY).matches) {
      header.classList.remove('ps-site-header--editor-scroll-pinned');
      return;
    }

    const editorOffset = Number.parseFloat(
      getComputedStyle(root).getPropertyValue('--ps-editor-offset-top'),
    ) || 0;
    const adminBar = document.querySelector('.gin--navigation-top-bar, .admin-toolbar-control-bar');
    const adminChromeGone = !adminBar || adminBar.getBoundingClientRect().bottom <= 0;
    const resultsScroll = document.querySelector('.js-ps-search-results-scroll');
    const resultsScrolled = resultsScroll ? resultsScroll.scrollTop > 0 : false;
    const pinned = adminChromeGone
      || window.scrollY > Math.max(0, editorOffset - 1)
      || resultsScrolled;

    header.classList.toggle('ps-site-header--editor-scroll-pinned', pinned);
  };

  Drupal.behaviors.psEditorPreviewOffset = {
    attach() {
      if (once('ps-editor-preview-offset', 'html', document).length === 0) {
        return;
      }

      syncEditorPreviewOffset();
      syncMobileEditorHeaderPin();

      window.addEventListener('resize', syncEditorPreviewOffset, { passive: true });
      window.addEventListener('scroll', syncMobileEditorHeaderPin, { passive: true });

      document.querySelectorAll('.js-ps-search-results-scroll').forEach(function (scrollEl) {
        if (once('ps-editor-header-pin-results-scroll', scrollEl).length === 0) {
          return;
        }
        scrollEl.addEventListener('scroll', syncMobileEditorHeaderPin, { passive: true });
      });

      if (typeof ResizeObserver === 'undefined') {
        return;
      }

      const observer = new ResizeObserver(syncEditorPreviewOffset);
      document.querySelectorAll('.gin--navigation-top-bar, .admin-toolbar-control-bar').forEach((bar) => {
        observer.observe(bar);
      });
    },
  };
  const STICKY_SCROLL_OFFSET_ENTER = 200;
  const STICKY_SCROLL_OFFSET_LEAVE = 128;
  const MOTION_DURATION_MS = 360;

  Drupal.behaviors.psSiteHeader = {
    attach(context) {
      context.querySelectorAll('[data-ps-site-header]').forEach((header) => {
        const mainBar = header.querySelector('.ps-site-header__main-bar');
        const mobilePanel = header.querySelector('.ps-site-header__mobile-panel');

        if (!mainBar || !mobilePanel) {
          return;
        }

        if (once('ps-site-header-sticky', header).length === 0) {
          return;
        }

        const mobileToggler = header.querySelector('[data-ps-mobile-toggle]');
        const compactToggler = header.querySelector('[data-ps-compact-toggle]');

        const desktopQuery = window.matchMedia(DESKTOP_QUERY);
        let scrollTicking = false;
        let lastScrollY = window.scrollY;
        let expandedAt = 0;

        const syncMainBarAccessibility = (isCompact, isExpanded) => {
          const hidden = isCompact && !isExpanded;
          mainBar.setAttribute('aria-hidden', hidden ? 'true' : 'false');
        };

        const syncMobilePanelAccessibility = (isOpen) => {
          mobilePanel.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
        };

        const setMenuOpen = (open) => {
          header.classList.toggle('ps-site-header--is-menu-open', open);
          const mobileMenuOpen = open && !desktopQuery.matches;
          document.documentElement.classList.toggle('ps-mobile-menu-open', mobileMenuOpen);
          document.body.classList.toggle('ps-mobile-menu-open', mobileMenuOpen);
          if (mobileToggler) {
            mobileToggler.setAttribute('aria-expanded', open ? 'true' : 'false');
          }
          syncMobilePanelAccessibility(open);
          if (!open) {
            closeMegaMenus();
          }
        };

        const setExpanded = (expanded) => {
          const isCompact = header.classList.contains('ps-site-header--is-compact');
          header.classList.toggle('ps-site-header--is-expanded', expanded);
          if (expanded) {
            expandedAt = Date.now();
            lastScrollY = window.scrollY;
          }
          if (compactToggler) {
            compactToggler.setAttribute('aria-expanded', expanded ? 'true' : 'false');
          }
          syncMainBarAccessibility(isCompact, expanded);
        };

        const closeMegaMenus = () => {
          header.querySelectorAll('[data-ps-mega-menu].ps-mega-menu--open').forEach((menu) => {
            menu.classList.remove('ps-mega-menu--open');
            menu.querySelectorAll('[data-ps-mega-trigger]').forEach((trigger) => {
              trigger.setAttribute('aria-expanded', 'false');
              trigger.classList.remove('is-open');
            });
            const panelsContainer = menu.querySelector('.ps-mega-menu__panels');
            (panelsContainer?.querySelectorAll('.ps-mega-menu__panel') ?? []).forEach((panel) => {
              panel.hidden = true;
              panel.classList.remove('is-open');
            });
          });
        };

        const setCompact = (isCompact) => {
          const wasCompact = header.classList.contains('ps-site-header--is-compact');
          if (wasCompact === isCompact) {
            return;
          }

          header.classList.toggle('ps-site-header--is-compact', isCompact);

          if (isCompact) {
            closeMegaMenus();
          }

          if (!isCompact) {
            setExpanded(false);
          }

          syncMainBarAccessibility(
            isCompact,
            header.classList.contains('ps-site-header--is-expanded'),
          );
        };

        const shouldBeCompact = (currentScrollY) => {
          const isCompact = header.classList.contains('ps-site-header--is-compact');
          if (isCompact) {
            return currentScrollY >= STICKY_SCROLL_OFFSET_LEAVE;
          }
          return currentScrollY >= STICKY_SCROLL_OFFSET_ENTER;
        };

        const updateStickyState = () => {
          if (!desktopQuery.matches) {
            setCompact(false);
            return;
          }

          const currentScrollY = window.scrollY;
          const hasScrolled = Math.abs(currentScrollY - lastScrollY) > 5;

          setCompact(shouldBeCompact(currentScrollY));

          if (
            hasScrolled
            && header.classList.contains('ps-site-header--is-compact')
            && header.classList.contains('ps-site-header--is-expanded')
            && Date.now() - expandedAt > MOTION_DURATION_MS + 80
          ) {
            setExpanded(false);
          }

          lastScrollY = currentScrollY;
        };

        const onScroll = () => {
          if (scrollTicking) {
            return;
          }
          scrollTicking = true;
          window.requestAnimationFrame(() => {
            updateStickyState();
            scrollTicking = false;
          });
        };

        const bindMobileToggle = () => {
          if (!mobileToggler) {
            return;
          }
          mobileToggler.addEventListener('click', (event) => {
            event.stopPropagation();
            if (desktopQuery.matches) {
              return;
            }
            setMenuOpen(!header.classList.contains('ps-site-header--is-menu-open'));
          });
        };

        const bindCompactToggle = () => {
          if (!compactToggler) {
            return;
          }
          compactToggler.addEventListener('click', (event) => {
            event.stopPropagation();
            if (!header.classList.contains('ps-site-header--is-compact')) {
              return;
            }
            setExpanded(!header.classList.contains('ps-site-header--is-expanded'));
          });
        };

        const enableDesktopMode = () => {
          setMenuOpen(false);
          lastScrollY = window.scrollY;
          updateStickyState();
          window.addEventListener('scroll', onScroll, { passive: true });
        };

        const disableDesktopMode = () => {
          window.removeEventListener('scroll', onScroll);
          setCompact(false);
          setExpanded(false);
        };

        const updateMode = () => {
          if (desktopQuery.matches) {
            enableDesktopMode();
          }
          else {
            disableDesktopMode();
            setExpanded(false);
          }
        };

        bindMobileToggle();
        bindCompactToggle();
        updateMode();
        syncMainBarAccessibility(false, false);
        syncMobilePanelAccessibility(false);

        document.addEventListener('click', (event) => {
          if (header.classList.contains('ps-site-header--is-menu-open') && !header.contains(event.target)) {
            setMenuOpen(false);
          }

          if (!header.classList.contains('ps-site-header--is-expanded')) {
            return;
          }
          if (header.contains(event.target)) {
            return;
          }
          setExpanded(false);
        });

        document.addEventListener('keydown', (event) => {
          if (event.key === 'Escape' && header.classList.contains('ps-site-header--is-menu-open')) {
            setMenuOpen(false);
          }
        });

        if (typeof desktopQuery.addEventListener === 'function') {
          desktopQuery.addEventListener('change', updateMode);
        }
        else {
          desktopQuery.addListener(updateMode);
        }

        header.style.setProperty('--ps-header-motion-duration', `${MOTION_DURATION_MS}ms`);
      });
    },
  };
})(Drupal);
