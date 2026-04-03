(function (Drupal, once, drupalSettings) {
  const DESKTOP_QUERY = '(min-width: 992px)';
  const FLAG_BY_LANGUAGE = {
    en: '🇬🇧',
    fr: '🇫🇷',
    de: '🇩🇪',
    es: '🇪🇸',
    it: '🇮🇹',
    nl: '🇳🇱',
    pt: '🇵🇹',
  };

  const getLanguageCode = (link) => {
    const raw = (
      link.getAttribute('hreflang') ||
      link.getAttribute('lang') ||
      link.textContent ||
      ''
    ).trim().toLowerCase();

    if (!raw) {
      return '';
    }

    return raw.slice(0, 2);
  };

  const getFlag = (langCode) => FLAG_BY_LANGUAGE[langCode] || '🌐';

  const getLanguageLabelMode = () => (
    drupalSettings?.uiSuiteBnppre?.header?.languageLabelMode || 'code_capitalized'
  );

  const getLanguageName = (link) => {
    const raw = (link.textContent || '').trim();

    return raw.replace(/^[A-Z]{2}\s*[-–:]?\s*/u, '').trim() || raw;
  };

  const buildLanguageLabel = (link, langCode) => {
    const languageName = getLanguageName(link);
    const uppercaseCode = (langCode || '').toUpperCase();
    const capitalizedCode = uppercaseCode ? `${uppercaseCode.charAt(0)}${uppercaseCode.slice(1).toLowerCase()}` : '';
    const mode = getLanguageLabelMode();

    if (mode === 'name') {
      return languageName;
    }

    if (mode === 'code_capitalized') {
      return capitalizedCode || languageName;
    }

    if (mode === 'code_name') {
      return languageName ? `${uppercaseCode} - ${languageName}` : uppercaseCode;
    }

    return uppercaseCode || languageName;
  };

  const getLanguageIconNode = (link) => {
    const icon = link.querySelector('img.language-icon');

    if (!icon) {
      return null;
    }

    const clone = icon.cloneNode(true);
    clone.classList.add('ps-language-flag-img');
    clone.setAttribute('aria-hidden', 'true');
    clone.removeAttribute('title');
    clone.removeAttribute('alt');

    return clone;
  };

  const enhanceLanguageSwitcher = (header) => {
    const switchers = header.querySelectorAll('.ps-header__switcher .language-switcher-language-url');

    switchers.forEach((switcher) => {
      if (switcher.dataset.enhanced === 'true') {
        return;
      }

      const linksList = switcher.querySelector('.links');

      if (!linksList) {
        return;
      }

      const links = Array.from(linksList.querySelectorAll('a[href]'));

      if (links.length < 2) {
        return;
      }

      links.forEach((link) => {
        const langCode = getLanguageCode(link);
        const label = buildLanguageLabel(link, langCode);
        const existingIcon = getLanguageIconNode(link);

        link.classList.add('ps-language-link');
        link.dataset.langCode = langCode;
        link.dataset.langLabel = label;

        const labelNode = document.createElement('span');
        labelNode.className = 'ps-language-label';
        labelNode.textContent = label;

        if (existingIcon) {
          link.replaceChildren(existingIcon, labelNode);
        }
        else {
          const flag = document.createElement('span');
          flag.className = 'ps-language-flag';
          flag.textContent = getFlag(langCode);
          flag.setAttribute('aria-hidden', 'true');
          link.replaceChildren(flag, labelNode);
        }
      });

      const activeLink = links.find((link) => (
        link.classList.contains('is-active') ||
        link.getAttribute('aria-current') === 'page' ||
        link.getAttribute('aria-current') === 'true'
      )) || links[0];

      const trigger = document.createElement('button');
      trigger.type = 'button';
      trigger.className = 'ps-language-dropdown__trigger';
      trigger.setAttribute('aria-expanded', 'false');
      trigger.setAttribute('aria-label', 'Change language');

      const activeIconNode = getLanguageIconNode(activeLink);
      const activeIconMarkup = activeIconNode ? activeIconNode.outerHTML : `<span class="ps-language-flag" aria-hidden="true">${getFlag(activeLink.dataset.langCode || '')}</span>`;

      trigger.innerHTML = [
        '<span class="ps-language-dropdown__current">',
        activeIconMarkup,
        `<span class="ps-language-code">${activeLink.dataset.langLabel || ''}</span>`,
        '</span>',
        '<span class="ps-language-dropdown__chevron" aria-hidden="true"></span>',
      ].join('');

      const wrapper = document.createElement('div');
      wrapper.className = 'ps-language-dropdown';

      linksList.classList.add('ps-language-dropdown__menu');
      linksList.setAttribute('role', 'menu');

      switcher.insertBefore(wrapper, linksList);
      wrapper.appendChild(trigger);
      wrapper.appendChild(linksList);

      const close = () => {
        wrapper.classList.remove('is-open');
        trigger.setAttribute('aria-expanded', 'false');
      };

      const toggle = () => {
        const isOpen = wrapper.classList.toggle('is-open');
        trigger.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
      };

      trigger.addEventListener('click', (event) => {
        event.preventDefault();
        toggle();
      });

      document.addEventListener('click', (event) => {
        if (!wrapper.contains(event.target)) {
          close();
        }
      });

      document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
          close();
        }
      });

      switcher.dataset.enhanced = 'true';
    });
  };

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

          enhanceLanguageSwitcher(header);
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

        enhanceLanguageSwitcher(header);

        refreshDesktopOffset();
        syncDom();
        refreshSticky();
        syncHeaderOffsetDeferred();
      });
    },
  };
})(Drupal, once, drupalSettings);
