/**
 * Header Layout - Mobile Menu Toggle & Sticky Behavior
 *
 * Simplified approach:
 * - Toggle .is-open class on header for mobile menu visibility
 * - CSS handles all presentation (slide animation, overlay, layout changes)
 * - Minimal JS: class toggle + ARIA state management
 * - Sticky header on scroll
 *
 * @requires Drupal core/once
 * @see header.twig
 * @see header.css
 */

((Drupal, once) => {
  /**
   * Header component behavior
   */
  Drupal.behaviors.psHeader = {
    attach: (context) => {
      const headers = once('ps-header', '[data-component="header"]', context);

      headers.forEach((header) => {
        new HeaderComponent(header);
      });
    },
  };

  /**
   * HeaderComponent class
   */
  class HeaderComponent {
    constructor(element) {
      this.header = element;
      this.toggle = element.querySelector('[data-header-toggle]');
      this.nav = element.querySelector('.ps-header__panel');

      this.isOpen = false;
      this.isSticky = false;
      this.scrollThreshold = 100;

      // Bind methods for reuse and cleanup
      this.handleToggleClick = this.toggleMenu.bind(this);
      this.handleOverlayClick = this.closeMenu.bind(this);
      this.handleEscapeKey = this.handleEscapeKey.bind(this);
      this.handleScroll = null;

      this.init();
    }

    init() {
      if (this.toggle && this.nav) {
        this.initMobileMenu();
      }

      if (this.header.classList.contains('ps-header--sticky')) {
        this.initStickyBehavior();
      }
    }

    /**
     * Clean up event listeners on component destroy
     */
    destroy() {
      // Remove click event listeners
      if (this.toggle) {
        this.toggle.removeEventListener('click', this.handleToggleClick);
      }

      // Remove overlay click (on ::after pseudo-element, listen on header)
      this.header.removeEventListener('click', this.handleOverlayClick);

      // Remove keyboard listeners
      document.removeEventListener('keydown', this.handleEscapeKey);

      // Remove scroll listener
      if (this.handleScroll) {
        window.removeEventListener('scroll', this.handleScroll, { passive: true });
      }
    }

    /**
     * Initialize mobile menu functionality
     */
    initMobileMenu() {
      // Toggle button click
      this.toggle.addEventListener('click', this.handleToggleClick);

      // Overlay click (clicks on header when menu is open)
      this.header.addEventListener('click', (e) => {
        // If click is on overlay (outside nav) and menu is open
        if (this.isOpen && !this.nav.contains(e.target) && !this.toggle.contains(e.target)) {
          this.closeMenu();
        }
      });

      // ESC key to close
      document.addEventListener('keydown', this.handleEscapeKey);
    }

    /**
     * Toggle mobile menu open/close
     */
    toggleMenu() {
      if (this.isOpen) {
        this.closeMenu();
      } else {
        this.openMenu();
      }
    }

    /**
     * Open mobile menu
     */
    openMenu() {
      this.isOpen = true;

      // Add class to header (CSS handles rest)
      this.header.classList.add('is-open');

      // Update ARIA state
      this.toggle.setAttribute('aria-expanded', 'true');

      // Lock body scroll
      document.body.style.overflow = 'hidden';
    }

    /**
     * Close mobile menu
     */
    closeMenu() {
      this.isOpen = false;

      // Remove class from header
      this.header.classList.remove('is-open');

      // Update ARIA state
      this.toggle.setAttribute('aria-expanded', 'false');

      // Unlock body scroll
      document.body.style.overflow = '';
    }

    /**
     * Handle escape key to close menu
     */
    handleEscapeKey(e) {
      if (e.key === 'Escape' && this.isOpen) {
        this.closeMenu();
      }
    }

    /**
     * Initialize sticky header behavior
     */
    initStickyBehavior() {
      let _lastScrollY = window.scrollY;
      let ticking = false;

      const updateStickyState = () => {
        const currentScrollY = window.scrollY;

        if (currentScrollY > this.scrollThreshold) {
          if (!this.isSticky) {
            this.header.classList.add('is-sticky');
            this.isSticky = true;
          }
        } else {
          if (this.isSticky) {
            this.header.classList.remove('is-sticky');
            this.isSticky = false;
          }
        }

        _lastScrollY = currentScrollY;
        ticking = false;
      };

      const onScroll = () => {
        if (!ticking) {
          window.requestAnimationFrame(updateStickyState);
          ticking = true;
        }
      };

      // Listen to scroll events
      this.handleScroll = onScroll;
      window.addEventListener('scroll', this.handleScroll, { passive: true });

      // Initial check
      updateStickyState();
    }
  }
})(Drupal, once);
