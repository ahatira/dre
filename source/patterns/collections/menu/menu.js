/**
 * @file
 * Menu Component JavaScript - Handles interactive behavior
 *
 * Features:
 * - Mobile accordion (one item open at a time)
 * - Keyboard navigation (Arrow keys, Enter, Escape)
 * - Desktop hover/click toggle
 * - ARIA state management
 */

((Drupal) => {
  /**
   * Menu behavior
   */
  Drupal.behaviors.menu = {
    attach: (context, _settings) => {
      const menus = context.querySelectorAll('.menu');

      menus.forEach((menu) => {
        // Skip if already initialized
        if (menu.dataset.menuInitialized === 'true') {
          return;
        }
        menu.dataset.menuInitialized = 'true';

        // Get all expandable items (items with submenus)
        const expandableItems = menu.querySelectorAll('[data-menu-item-expandable="true"]');

        expandableItems.forEach((item) => {
          const toggle = item.querySelector('[data-menu-toggle="true"]');
          const submenu = item.querySelector('.menu__list--submenu');

          if (!toggle || !submenu) {
            return;
          }

          // Click handler for toggle
          toggle.addEventListener('click', (e) => {
            e.preventDefault();
            toggleSubmenu(item, toggle, submenu, menu);
          });

          // Keyboard navigation
          toggle.addEventListener('keydown', (e) => {
            handleKeyboardNavigation(e, item, toggle, submenu, menu);
          });

          // Desktop: close submenu when clicking outside
          if (window.matchMedia('(min-width: 768px)').matches) {
            document.addEventListener('click', (e) => {
              if (!item.contains(e.target)) {
                closeSubmenu(item, toggle, submenu);
              }
            });
          }
        });
      });
    },
  };

  /**
   * Toggle submenu open/closed
   */
  function toggleSubmenu(item, toggle, submenu, menu) {
    const isExpanded = item.classList.contains('menu__item--expanded');

    if (isExpanded) {
      closeSubmenu(item, toggle, submenu);
    } else {
      openSubmenu(item, toggle, submenu, menu);
    }
  }

  /**
   * Open submenu
   */
  function openSubmenu(item, toggle, submenu, _menu) {
    // Mobile: close other items at same level (accordion behavior)
    if (window.matchMedia('(max-width: 767px)').matches) {
      const parentList = item.parentElement;
      const siblings = parentList.querySelectorAll(':scope > .menu__item--has-children');

      siblings.forEach((sibling) => {
        if (sibling !== item && sibling.classList.contains('menu__item--expanded')) {
          const siblingToggle = sibling.querySelector('[data-menu-toggle="true"]');
          const siblingSubmenu = sibling.querySelector('.menu__list--submenu');
          closeSubmenu(sibling, siblingToggle, siblingSubmenu);
        }
      });
    }

    // Open this item
    item.classList.add('menu__item--expanded');
    item.classList.remove('menu__item--collapsed');
    toggle.setAttribute('aria-expanded', 'true');
    submenu.style.display = 'flex';
  }

  /**
   * Close submenu
   */
  function closeSubmenu(item, toggle, submenu) {
    item.classList.remove('menu__item--expanded');
    item.classList.add('menu__item--collapsed');
    toggle.setAttribute('aria-expanded', 'false');
    submenu.style.display = 'none';
  }

  /**
   * Keyboard navigation handler
   */
  function handleKeyboardNavigation(e, item, toggle, submenu, menu) {
    const isExpanded = item.classList.contains('menu__item--expanded');

    switch (e.key) {
      case 'Enter':
      case ' ': // Space
        e.preventDefault();
        toggleSubmenu(item, toggle, submenu, menu);
        break;

      case 'Escape':
        if (isExpanded) {
          e.preventDefault();
          closeSubmenu(item, toggle, submenu);
          toggle.focus();
        }
        break;

      case 'ArrowDown': {
        e.preventDefault();
        if (isExpanded) {
          // Focus first item in submenu
          const firstLink = submenu.querySelector('.menu__link');
          if (firstLink) {
            firstLink.focus();
          }
        } else {
          // Move to next sibling
          const nextItem = item.nextElementSibling;
          if (nextItem) {
            const nextToggle =
              nextItem.querySelector('[data-menu-toggle="true"]') ||
              nextItem.querySelector('.menu__link');
            if (nextToggle) {
              nextToggle.focus();
            }
          }
        }
        break;
      }

      case 'ArrowUp': {
        e.preventDefault();
        // Move to previous sibling
        const prevItem = item.previousElementSibling;
        if (prevItem) {
          const prevToggle =
            prevItem.querySelector('[data-menu-toggle="true"]') ||
            prevItem.querySelector('.menu__link');
          if (prevToggle) {
            prevToggle.focus();
          }
        }
        break;
      }

      case 'ArrowRight':
        e.preventDefault();
        if (!isExpanded) {
          openSubmenu(item, toggle, submenu, menu);
        }
        break;

      case 'ArrowLeft':
        e.preventDefault();
        if (isExpanded) {
          closeSubmenu(item, toggle, submenu);
        }
        break;
    }
  }

  /**
   * Close all submenus on window resize (prevent state issues)
   */
  let resizeTimer;
  window.addEventListener('resize', () => {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(() => {
      const allItems = document.querySelectorAll('.menu__item--has-children');
      allItems.forEach((item) => {
        const toggle = item.querySelector('[data-menu-toggle="true"]');
        const submenu = item.querySelector('.menu__list--submenu');
        if (toggle && submenu) {
          closeSubmenu(item, toggle, submenu);
        }
      });
    }, 250);
  });
})(Drupal);
