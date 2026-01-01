/**
 * User Account Block - Interactive dropdown menu
 * Handles toggle/close on click/escape/outside click
 */

export class UserAccountComponent {
  constructor(element) {
    this.element = element;
    this.toggle = element.querySelector('[data-user-account-toggle]');
    this.menu = element.querySelector('[data-user-account-menu]');

    if (!this.toggle || !this.menu) {
      return;
    }

    this.isOpen = false;
    this.init();
  }

  init() {
    this.toggle.addEventListener('click', () => this.toggleMenu());
    document.addEventListener('click', (e) => this.handleOutsideClick(e));
    document.addEventListener('keydown', (e) => this.handleEscapeKey(e));
  }

  toggleMenu() {
    this.isOpen ? this.closeMenu() : this.openMenu();
  }

  openMenu() {
    this.isOpen = true;
    this.toggle.setAttribute('aria-expanded', 'true');
    this.menu.hidden = false;
  }

  closeMenu() {
    this.isOpen = false;
    this.toggle.setAttribute('aria-expanded', 'false');
    this.menu.hidden = true;
  }

  handleOutsideClick(event) {
    if (this.isOpen && !this.element.contains(event.target)) {
      this.closeMenu();
    }
  }

  handleEscapeKey(event) {
    if (this.isOpen && event.key === 'Escape') {
      this.closeMenu();
      this.toggle.focus();
    }
  }
}

// Auto-init on DOM ready
if (typeof document !== 'undefined') {
  document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-user-account]').forEach((element) => {
      new UserAccountComponent(element);
    });
  });
}

export default UserAccountComponent;
