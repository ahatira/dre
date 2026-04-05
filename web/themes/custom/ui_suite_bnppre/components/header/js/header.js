(function (Drupal, once) {
  const DESKTOP_QUERY = '(min-width: 992px)';

  Drupal.behaviors.psThemeHeader = {
    attach(context) {
      once('ps-theme-header', '[data-header]', context).forEach((header) => {
        const toggle = header.querySelector('[data-header-toggle]');
        const close = header.querySelector('[data-header-close]');
        const panel = header.querySelector('[data-header-panel]');
        const media = window.matchMedia(DESKTOP_QUERY);
        const previewMode = header.dataset.headerPreviewMode || '';
        const previewOpen = header.dataset.headerPreviewOpen === 'true';
        const isStoriesContext = Boolean(header.closest('.ui_patterns_component__stories, .ui_patterns_story'));
        const stickyEnterOffset = 88;
        const stickyExitOffset = 64;
        const scrollContainer = header.closest('[data-off-canvas-main-canvas]');
        const rootScroller = document.scrollingElement || document.documentElement;
        let desktopOffsetHeight = 0;

        const setHeaderOffset = (height) => {
          document.body.style.setProperty('--ps-header-offset', `${Math.max(0, height)}px`);
        };

        const refreshDesktopOffset = () => {
          if (!media.matches || previewMode || isStoriesContext) {
            desktopOffsetHeight = 0;
            setHeaderOffset(0);
            return;
          }

          const nextHeight = Math.ceil(header.getBoundingClientRect().height);
          desktopOffsetHeight = Math.max(desktopOffsetHeight, nextHeight);
          setHeaderOffset(desktopOffsetHeight);
        };

        const applyDesktopOffset = () => {
          if (!media.matches || previewMode || isStoriesContext) {
            setHeaderOffset(0);
            return;
          }

          setHeaderOffset(desktopOffsetHeight);
        };

        const syncHeaderOffsetDeferred = () => {
          window.requestAnimationFrame(() => {
            refreshDesktopOffset();
          });
        };

        const getScrollTop = () => {
          const windowScrollTop = window.scrollY || window.pageYOffset || 0;
          const rootScrollTop = rootScroller ? (rootScroller.scrollTop || 0) : 0;
          const bodyScrollTop = document.body ? (document.body.scrollTop || 0) : 0;

          if (!scrollContainer) {
            return Math.max(windowScrollTop, rootScrollTop, bodyScrollTop);
          }

          return Math.max(windowScrollTop, rootScrollTop, bodyScrollTop, scrollContainer.scrollTop || 0);
        };

        if (!toggle || !close || !panel) {
          return;
        }

        if (typeof ResizeObserver === 'function') {
          const observer = new ResizeObserver(() => {
            refreshDesktopOffset();
          });
          observer.observe(header);
        }

        // Keep body offset in sync after animated sticky/non-sticky transitions.
        header.addEventListener('transitionend', syncHeaderOffsetDeferred);

        // Stories and forced preview modes must stay stable and not react to page scroll.
        if (previewMode || isStoriesContext) {
          const isPreviewMobile = previewMode
            ? previewMode !== 'desktop'
            : !media.matches;
          const isPanelOpen = previewMode ? previewOpen : false;

          header.classList.toggle('is-mobile', isPreviewMobile);
          header.classList.remove('is-sticky');
          header.classList.toggle('is-open', isPanelOpen);

          const panelVisible = isPreviewMobile ? isPanelOpen : true;
          toggle.setAttribute('aria-expanded', panelVisible ? 'true' : 'false');
          panel.setAttribute('aria-hidden', panelVisible ? 'false' : 'true');
          close.hidden = false;
          close.tabIndex = isPanelOpen ? 0 : -1;
          close.setAttribute('aria-hidden', isPanelOpen ? 'false' : 'true');
          refreshDesktopOffset();

          return;
        }

        let isSticky = false;
        let isOpen = false;
        let ticking = false;

        const markStickySwitch = () => {
          header.classList.add('is-sticky-switching');

          window.requestAnimationFrame(() => {
            window.requestAnimationFrame(() => {
              header.classList.remove('is-sticky-switching');
            });
          });
        };

        const syncDom = () => {
          header.classList.toggle('is-mobile', !media.matches);
          header.classList.toggle('is-sticky', isSticky);
          header.classList.toggle('is-open', isOpen);

          const panelVisible = (!media.matches && isOpen) || (media.matches && (!isSticky || isOpen));

          toggle.setAttribute('aria-expanded', panelVisible ? 'true' : 'false');
          panel.setAttribute('aria-hidden', panelVisible ? 'false' : 'true');

          close.hidden = false;
          close.tabIndex = isOpen ? 0 : -1;
          close.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
          applyDesktopOffset();
        };

        const closePanel = () => {
          isOpen = false;
          syncDom();
        };

        const openPanel = () => {
          isOpen = true;
          syncDom();
        };

        const togglePanel = () => {
          if (media.matches && !isSticky) {
            return;
          }

          if (isOpen) {
            closePanel();
          }
          else {
            openPanel();
          }
        };

        const refreshSticky = () => {
          if (!media.matches) {
            if (isSticky) {
              markStickySwitch();
              isSticky = false;
              syncDom();
            }
            return;
          }

          const scrollTop = getScrollTop();
          const nextSticky = isSticky
            ? scrollTop > stickyExitOffset
            : scrollTop > stickyEnterOffset;

          if (nextSticky === isSticky) {
            return;
          }

          markStickySwitch();
          isSticky = nextSticky;

          if (!isSticky) {
            isOpen = false;
          }

          syncDom();
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

        const onResize = () => {
          if (media.matches) {
            if (!isSticky) {
              isOpen = false;
            }
          }
          else {
            isSticky = false;
          }

          if (media.matches) {
            desktopOffsetHeight = 0;
          }

          syncDom();
          refreshSticky();
          refreshDesktopOffset();
        };

        const onDocumentClick = (event) => {
          if (!isOpen) {
            return;
          }

          if (!header.contains(event.target)) {
            closePanel();
          }
        };

        const onKeydown = (event) => {
          if (event.key === 'Escape' && isOpen) {
            closePanel();
            toggle.focus();
          }
        };

        toggle.addEventListener('click', togglePanel);
        close.addEventListener('click', closePanel);
        document.addEventListener('click', onDocumentClick);
        document.addEventListener('keydown', onKeydown);
        window.addEventListener('scroll', onScroll, { passive: true });
        if (rootScroller && rootScroller !== window && rootScroller !== scrollContainer) {
          rootScroller.addEventListener('scroll', onScroll, { passive: true });
        }

        if (scrollContainer) {
          scrollContainer.addEventListener('scroll', onScroll, { passive: true });
        }

        if (typeof media.addEventListener === 'function') {
          media.addEventListener('change', onResize);
        }
        else if (typeof media.addListener === 'function') {
          media.addListener(onResize);
        }

        refreshDesktopOffset();
        syncDom();
        refreshSticky();
        syncHeaderOffsetDeferred();
      });
    },
  };
})(Drupal, once);
