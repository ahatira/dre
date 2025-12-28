/**
 * @file
 * PS Navigation Menu - Interactive behavior
 *
 * Handles:
 * - Desktop: click behavior for submenus (when behavior="click")
 * - Mobile: accordion behavior (close siblings when opening)
 * - Keyboard navigation (Enter, Space, Escape, Arrow keys)
 * - Focus management and accessibility
 * - ARIA state updates
 */

((Drupal, once) => {
  /**
   * Navigation Menu behavior
   */
  Drupal.behaviors.psNavigationMenu = {
    attach: (context) => {
      // Find all navigation menus in context
      const menus = once('ps-navigation-menu', '.ps-navigation-menu', context);

      menus.forEach((menu) => {
        const behavior = menu.dataset.behavior || 'hover';
        const isAccordion = menu.dataset.accordion === 'true';

        // Initialize all toggle buttons
        const toggleButtons = menu.querySelectorAll('.ps-navigation-menu__toggle');

        toggleButtons.forEach((button) => {
          const menuItem = button.closest('.ps-navigation-menu__item');
          const submenu = menuItem.querySelector('.ps-navigation-menu__list--submenu');

          if (!submenu) {
            return;
          }

          // Click handler for toggle button
          button.addEventListener('click', (event) => {
            event.preventDefault();
            event.stopPropagation();

            const isExpanded = button.getAttribute('aria-expanded') === 'true';

            // Close all siblings if accordion mode
            if (isAccordion && !isExpanded) {
              closeAllSiblings(menuItem);
            }

            // Toggle current item
            toggleSubmenu(menuItem, button, submenu, !isExpanded);
          });

          // Keyboard navigation
          button.addEventListener('keydown', (event) => {
            handleKeyboardNavigation(event, menuItem, button, submenu);
          });

          // Link click in submenu (close submenu after navigation)
          const submenuLinks = submenu.querySelectorAll('.ps-navigation-menu__link');
          submenuLinks.forEach((link) => {
            link.addEventListener('click', () => {
              // Close submenu after link is clicked (for SPA navigation)
              setTimeout(() => {
                toggleSubmenu(menuItem, button, submenu, false);
              }, 100);
            });
          });
        });

        // Desktop click behavior: close submenu when clicking outside
        if (behavior === 'click') {
          document.addEventListener('click', (event) => {
            if (!menu.contains(event.target)) {
              closeAllSubmenus(menu);
            }
          });
        }

        // Mobile: close menu on Escape
        document.addEventListener('keydown', (event) => {
          if (event.key === 'Escape') {
            closeAllSubmenus(menu);
          }
        });
      });
    },
  };

  /**
   * Toggle submenu visibility
   *
   * @param {HTMLElement} menuItem - Menu item element
   * @param {HTMLElement} button - Toggle button
   * @param {HTMLElement} submenu - Submenu list
   * @param {boolean} expanded - Expanded state
   */
  function toggleSubmenu(menuItem, button, submenu, expanded) {
    button.setAttribute('aria-expanded', expanded ? 'true' : 'false');

    if (expanded) {
      menuItem.classList.add('ps-navigation-menu__item--expanded');
      submenu.style.display = 'block';

      // Focus first link in submenu
      const firstLink = submenu.querySelector('.ps-navigation-menu__link');
      if (firstLink) {
        setTimeout(() => firstLink.focus(), 50);
      }
    } else {
      menuItem.classList.remove('ps-navigation-menu__item--expanded');
      submenu.style.display = 'none';

      // Return focus to toggle button
      button.focus();
    }
  }

  /**
   * Close all sibling submenus (accordion mode)
   *
   * @param {HTMLElement} currentItem - Current menu item to keep open
   */
  function closeAllSiblings(currentItem) {
    const parent = currentItem.parentElement;
    if (!parent) {
      return;
    }

    const siblings = parent.querySelectorAll('.ps-navigation-menu__item--expanded');

    siblings.forEach((sibling) => {
      if (sibling !== currentItem) {
        const button = sibling.querySelector('.ps-navigation-menu__toggle');
        const submenu = sibling.querySelector('.ps-navigation-menu__list--submenu');

        if (button && submenu) {
          toggleSubmenu(sibling, button, submenu, false);
        }
      }
    });
  }

  /**
   * Close all submenus in the menu
   *
   * @param {HTMLElement} menu - Navigation menu element
   */
  function closeAllSubmenus(menu) {
    const expandedItems = menu.querySelectorAll('.ps-navigation-menu__item--expanded');

    expandedItems.forEach((item) => {
      const button = item.querySelector('.ps-navigation-menu__toggle');
      const submenu = item.querySelector('.ps-navigation-menu__list--submenu');

      if (button && submenu) {
        toggleSubmenu(item, button, submenu, false);
      }
    });
  }

  /**
   * Handle keyboard navigation
   *
   * @param {KeyboardEvent} event - Keyboard event
   * @param {HTMLElement} menuItem - Menu item element
   * @param {HTMLElement} button - Toggle button
   * @param {HTMLElement} submenu - Submenu list
   */
  function handleKeyboardNavigation(event, menuItem, button, submenu) {
    const isExpanded = button.getAttribute('aria-expanded') === 'true';
    const { key } = event;

    if (key === 'Enter' || key === ' ') {
      event.preventDefault();
      toggleSubmenu(menuItem, button, submenu, !isExpanded);
      return;
    }

    if (key === 'Escape' && isExpanded) {
      event.preventDefault();
      toggleSubmenu(menuItem, button, submenu, false);
      return;
    }

    if (key === 'ArrowDown' && isExpanded) {
      handleArrowDown(event, submenu);
      return;
    }

    if (key === 'ArrowUp' && isExpanded) {
      handleArrowUp(event, submenu);
    }
  }

  /**
   * Handle ArrowDown key navigation
   *
   * @param {KeyboardEvent} event - Keyboard event
   * @param {HTMLElement} submenu - Submenu list
   */
  function handleArrowDown(event, submenu) {
    event.preventDefault();
    const firstLink = submenu.querySelector('.ps-navigation-menu__link');
    if (firstLink) {
      firstLink.focus();
    }
  }

  /**
   * Handle ArrowUp key navigation
   *
   * @param {KeyboardEvent} event - Keyboard event
   * @param {HTMLElement} submenu - Submenu list
   */
  function handleArrowUp(event, submenu) {
    event.preventDefault();
    const links = Array.from(submenu.querySelectorAll('.ps-navigation-menu__link'));
    const lastLink = links[links.length - 1];
    if (lastLink) {
      lastLink.focus();
    }
  }
})(Drupal, once);
