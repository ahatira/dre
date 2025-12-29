/**
 * @file
 * Menu Primary JavaScript - Mobile accordion behavior
 *
 * Features:
 * - Mobile accordion (toggle submenu visibility)
 * - One item open at a time per level
 * - Desktop: pure CSS hover (no JavaScript)
 */

/**
 * Initialize menu behavior
 */
const initMenuPrimary = (context = document) => {
  const menus = context.querySelectorAll('.menu-primary');

  menus.forEach((menu) => {
    // Skip if already initialized
    if (menu.dataset.menuPrimaryInit === 'true') {
      return;
    }
    menu.dataset.menuPrimaryInit = 'true';

    // Get all items with children
    const itemsWithChildren = menu.querySelectorAll('.menu-primary__item--has-children');

    itemsWithChildren.forEach((item) => {
      const link = item.querySelector('.menu-primary__link');

      if (!link) {
        return;
      }

      // Click handler for mobile accordion
      link.addEventListener('click', (e) => {
        // Only on mobile (< 768px)
        if (window.matchMedia('(min-width: 768px)').matches) {
          return; // Let default link behavior on desktop
        }

        e.preventDefault();

        const isOpen = item.classList.contains('is-open');

        // Close siblings at same level (accordion behavior)
        const parent = item.parentElement;
        const siblings = parent.querySelectorAll(':scope > .menu-primary__item--has-children');
        siblings.forEach((sibling) => {
          if (sibling !== item) {
            sibling.classList.remove('is-open');
          }
        });

        // Toggle current item
        if (isOpen) {
          item.classList.remove('is-open');
        } else {
          item.classList.add('is-open');
        }
      });
    });
  });
};

/**
 * Drupal behavior
 */
if (typeof Drupal !== 'undefined' && Drupal.behaviors) {
  Drupal.behaviors.menuPrimary = {
    attach: initMenuPrimary,
  };
} else {
  // Standalone initialization (Storybook, static HTML)
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => initMenuPrimary());
  } else {
    initMenuPrimary();
  }
}

/**
 * Close all submenus on resize to prevent state issues
 */
let resizeTimer;
window.addEventListener('resize', () => {
  clearTimeout(resizeTimer);
  resizeTimer = setTimeout(() => {
    const allItems = document.querySelectorAll('.menu-primary__item--has-children');
    allItems.forEach((item) => {
      item.classList.remove('is-open');
    });
  }, 250);
});
