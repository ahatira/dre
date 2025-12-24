/**
 * Primary Menu - Mobile Toggle Enhancement
 *
 * Minimal JavaScript for mobile accordion (click to expand).
 * Desktop uses CSS-only hover/focus-within (no JS needed).
 *
 * Uses .is-expanded class matching menu.html pattern.
 *
 * @package PS Theme
 */

(() => {
  /**
   * Handle link click
   */
  function handleLinkClick(e) {
    const link = e.currentTarget;

    // Mobile behavior: first click opens, second click navigates
    if (window.innerWidth < 768) {
      const parentItem = link.closest('li');
      const isExpanded = parentItem.classList.contains('is-expanded');

      // If not expanded yet, prevent navigation and open submenu
      if (!isExpanded) {
        e.preventDefault();
        openSubmenu(link, parentItem);
      }
    } else {
      // Desktop: prevent click navigation, hover handles visibility
      e.preventDefault();
    }
  }

  /**
   * Open submenu and add back button
   */
  function openSubmenu(link, parentItem) {
    parentItem.classList.add('is-expanded');
    link.setAttribute('aria-expanded', 'true');

    // Add back button to level 1 flyovers
    const submenu = link.nextElementSibling;
    if (submenu) {
      const rootMenu = parentItem.closest('.ps-menu-primary')?.querySelector('.menu');
      if (rootMenu && parentItem.parentElement === rootMenu) {
      addBackButton(submenu, parentItem, link);
      }
    }
  }

  /**
   * Add back button as first menu item in flyover
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
    backLink.textContent = 'Back';

    backLink.addEventListener('click', (e) => {
      e.preventDefault();
      closeSubmenu(parentItem, trigger);
    });

    backItem.appendChild(backLink);

    // Insert at the beginning of submenu
    submenu.insertBefore(backItem, submenu.firstChild);
  }

  /**
   * Close submenu
   */
  function closeSubmenu(parentItem, trigger) {
    parentItem.classList.remove('is-expanded');
    trigger.setAttribute('aria-expanded', 'false');

    // Remove dynamic back button after transition
    const dynamicBackBtn = parentItem.querySelector('.mobile-menu-back-item-dynamic');
    if (dynamicBackBtn) {
      setTimeout(() => {
        dynamicBackBtn.remove();
      }, 300);
    }
  }

  /**
   * Handle back button click
   */
  function handleBackClick(e) {
    e.preventDefault();

    const backBtn = e.currentTarget;
    const flyoverPanel = backBtn.closest('ul');
    const parentItem = flyoverPanel ? flyoverPanel.previousElementSibling?.closest('li') : null;

    if (parentItem) {
      const trigger = parentItem.querySelector('[aria-haspopup="true"]');
      if (trigger) {
        closeSubmenu(parentItem, trigger);
      }
    }
  }

  /**
   * Initialize menu behavior
   */
  function initMenu(context = document) {
    const menus = context.querySelectorAll('.ps-menu-primary');

    menus.forEach((menu) => {
      // Skip if already initialized
      if (menu.dataset.menuInitialized) {
        return;
      }
      menu.dataset.menuInitialized = 'true';

      const itemsWithChildren = menu.querySelectorAll('[aria-haspopup="true"]');

      itemsWithChildren.forEach((link) => {
        link.addEventListener('click', handleLinkClick);
      });

      // Mobile back buttons: close flyover
      const backButtons = menu.querySelectorAll('.mobile-menu-back-link');
      backButtons.forEach((backBtn) => {
        backBtn.addEventListener('click', handleBackClick);
      });

      // Re-attach on resize (desktop → mobile transition)
      let resizeTimeout;
      window.addEventListener('resize', () => {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(() => {
          if (window.innerWidth >= 768) {
            // Remove mobile classes on desktop
            menu.querySelectorAll('.is-expanded').forEach((item) => {
              item.classList.remove('is-expanded');
            });
          }
        }, 250);
      });
    });
  }

  // Drupal behavior (for Drupal integration)
  if (typeof Drupal !== 'undefined' && Drupal.behaviors) {
    Drupal.behaviors.menuPrimary = {
      attach: (context) => {
        initMenu(context);
      },
    };
  }

  // Standalone initialization (for Storybook)
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => initMenu());
  } else {
    initMenu();
  }
})();
