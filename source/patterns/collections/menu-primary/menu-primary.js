/**
 * Primary Menu - Mobile Toggle Enhancement
 *
 * Drupal behavior for mobile accordion (click to expand).
 * Desktop uses CSS-only hover/focus-within (no JS needed).
 *
 * @package PS Theme
 */

((Drupal, once) => {
  /**
   * Global resize handler (one instance for all menus)
   */
  let resizeTimeout;
  let isResizeHandlerAttached = false;

  function handleGlobalResize() {
    clearTimeout(resizeTimeout);
    resizeTimeout = setTimeout(() => {
      if (window.innerWidth >= 1024) {
        // Remove mobile states on all menus when switching to desktop
        document.querySelectorAll('.ps-menu-primary .is-expanded').forEach((item) => {
          item.classList.remove('is-expanded');
          const trigger = item.querySelector('[aria-haspopup="true"]');
          if (trigger) {
            trigger.setAttribute('aria-expanded', 'false');
          }
        });
      }
    }, 250);
  }

  /**
   * Handle link click
   *
   * @param {Event} e - Click event
   */
  function handleLinkClick(e) {
    const link = e.currentTarget;

    // Mobile behavior: first click opens, second click navigates
    if (window.innerWidth < 1024) {
      const parentItem = link.closest('li');
      const isExpanded = parentItem.classList.contains('is-expanded');

      // If not expanded yet, prevent navigation and open submenu
      if (!isExpanded) {
        e.preventDefault();
        openSubmenu(link, parentItem);
      }
      // Second click: allow default navigation
    } else {
      // Desktop: prevent click navigation, CSS hover handles visibility
      e.preventDefault();
    }
  }

  /**
   * Open submenu and add back button
   *
   * @param {HTMLElement} link - Link element
   * @param {HTMLElement} parentItem - Parent li element
   */
  function openSubmenu(link, parentItem) {
    parentItem.classList.add('is-expanded');
    link.setAttribute('aria-expanded', 'true');

    // Add back button to level 1 flyovers only
    const submenu = link.nextElementSibling;
    if (submenu) {
      const rootMenu = parentItem.closest('.ps-menu-primary')?.querySelector('.menu');
      if (rootMenu && parentItem.parentElement === rootMenu) {
        addBackButton(submenu, parentItem, link);
      }
    }
  }

  /**
   * Add dynamic back button to flyover submenu
   *
   * @param {HTMLElement} submenu - Submenu ul element
   * @param {HTMLElement} parentItem - Parent li element
   * @param {HTMLElement} trigger - Trigger link element
   */
  function addBackButton(submenu, parentItem, trigger) {
    // Check if back button already exists
    if (submenu.querySelector('.mobile-menu-back-item-dynamic')) {
      return;
    }

    const backItem = document.createElement('li');
    backItem.className = 'mobile-menu-back-item mobile-menu-back-item-dynamic';

    const backLink = document.createElement('a');
    backLink.href = '#';
    backLink.className = 'menu-link mobile-menu-back-link';
    backLink.textContent = Drupal.t('Back');

    backLink.addEventListener('click', (e) => {
      e.preventDefault();
      closeSubmenu(parentItem, trigger);
    });

    backItem.appendChild(backLink);
    submenu.insertBefore(backItem, submenu.firstChild);
  }

  /**
   * Close submenu and remove dynamic back button
   *
   * @param {HTMLElement} parentItem - Parent li element
   * @param {HTMLElement} trigger - Trigger link element
   */
  function closeSubmenu(parentItem, trigger) {
    parentItem.classList.remove('is-expanded');
    trigger.setAttribute('aria-expanded', 'false');

    // Remove dynamic back button after CSS transition
    const dynamicBackBtn = parentItem.querySelector('.mobile-menu-back-item-dynamic');
    if (dynamicBackBtn) {
      setTimeout(() => {
        dynamicBackBtn.remove();
      }, 300);
    }
  }

  /**
   * Handle static back button click
   *
   * @param {Event} e - Click event
   */
  function handleBackClick(e) {
    e.preventDefault();

    const backBtn = e.currentTarget;
    const flyoverPanel = backBtn.closest('ul');
    const parentItem = flyoverPanel?.previousElementSibling?.closest('li');

    if (parentItem) {
      const trigger = parentItem.querySelector('[aria-haspopup="true"]');
      if (trigger) {
        closeSubmenu(parentItem, trigger);
      }
    }
  }

  /**
   * Drupal behavior for primary menu
   */
  Drupal.behaviors.menuPrimary = {
    attach(context, _settings) {
      // Use once() to prevent double initialization
      const menus = once('ps-menu-primary', '.ps-menu-primary', context);

      menus.forEach((menu) => {
        // Attach click handlers to links with submenus
        const itemsWithChildren = menu.querySelectorAll('[aria-haspopup="true"]');
        itemsWithChildren.forEach((link) => {
          link.addEventListener('click', handleLinkClick);
        });

        // Attach click handlers to static back buttons (from Twig template)
        const backButtons = menu.querySelectorAll('.mobile-menu-back-link');
        backButtons.forEach((backBtn) => {
          backBtn.addEventListener('click', handleBackClick);
        });
      });

      // Attach global resize handler once
      if (!isResizeHandlerAttached && menus.length > 0) {
        window.addEventListener('resize', handleGlobalResize);
        isResizeHandlerAttached = true;
      }
    },

    detach(context, _settings, trigger) {
      // Clean up on detach (AJAX, BigPipe, etc.)
      if (trigger === 'unload') {
        const menus = once.remove('ps-menu-primary', '.ps-menu-primary', context);

        menus.forEach((menu) => {
          // Remove event listeners from links
          const itemsWithChildren = menu.querySelectorAll('[aria-haspopup="true"]');
          itemsWithChildren.forEach((link) => {
            link.removeEventListener('click', handleLinkClick);
          });

          // Remove event listeners from back buttons
          const backButtons = menu.querySelectorAll('.mobile-menu-back-link');
          backButtons.forEach((backBtn) => {
            backBtn.removeEventListener('click', handleBackClick);
          });

          // Remove dynamic back buttons
          const dynamicBackButtons = menu.querySelectorAll('.mobile-menu-back-item-dynamic');
          dynamicBackButtons.forEach((btn) => btn.remove());

          // Reset expanded states
          menu.querySelectorAll('.is-expanded').forEach((item) => {
            item.classList.remove('is-expanded');
          });
        });
      }
    },
  };
})(Drupal, once);
