/**
 * Dropdown behavior - Accessible select with keyboard navigation
 */

class PsDropdownWrapper {
  constructor(element) {
    this.element = element;
    this.button = element.querySelector('[data-dropdown-button]');
    this.list = element.querySelector('[data-dropdown-list]');
    this.nativeSelect = element.querySelector('.ps-dropdown__native');

    // Only initialize options if list exists
    this.options = this.list
      ? Array.from(
          this.list.querySelectorAll('.ps-dropdown__option:not(.ps-dropdown__option--disabled)')
        )
      : [];
    this.currentIndex = this.options.findIndex(
      (opt) => opt.getAttribute('aria-selected') === 'true'
    );

    if (this.currentIndex === -1) {
      this.currentIndex = 0;
    }
  }

  init() {
    if (!this.button || !this.list) {
      return;
    }

    // Toggle dropdown
    this.button.addEventListener('click', () => this.toggle());

    // Select option on click
    this.options.forEach((option, index) => {
      option.addEventListener('click', () => {
        this.selectOption(index);
        this.close();
      });
    });

    // Keyboard navigation
    this.button.addEventListener('keydown', (e) => this.handleButtonKeydown(e));
    this.list.addEventListener('keydown', (e) => this.handleListKeydown(e));

    // Close on outside click
    document.addEventListener('click', (e) => {
      if (!this.element.contains(e.target)) {
        this.close();
      }
    });

    // Close on Escape
    this.element.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        this.close();
        this.button.focus();
      }
    });
  }

  toggle() {
    const isExpanded = this.button.getAttribute('aria-expanded') === 'true';
    isExpanded ? this.close() : this.open();
  }

  open() {
    this.list.hidden = false;
    this.button.setAttribute('aria-expanded', 'true');
    this.focusOption(this.currentIndex);
  }

  close() {
    this.list.hidden = true;
    this.button.setAttribute('aria-expanded', 'false');
  }

  selectOption(index) {
    // Update UI
    this.options.forEach((opt, i) => {
      opt.setAttribute('aria-selected', i === index ? 'true' : 'false');
    });

    // Update button label
    const selectedOption = this.options[index];
    const labelElement = this.button.querySelector('.ps-dropdown__label');
    if (labelElement) {
      labelElement.textContent = selectedOption.textContent.trim();
    }

    // Update native select
    const value = selectedOption.dataset.value;
    if (this.nativeSelect && value) {
      this.nativeSelect.value = value;

      // Trigger change event for form handling
      const changeEvent = new Event('change', { bubbles: true });
      this.nativeSelect.dispatchEvent(changeEvent);
    }

    this.currentIndex = index;
  }

  focusOption(index) {
    if (index >= 0 && index < this.options.length) {
      this.options[index].focus();
    }
  }

  handleButtonKeydown(e) {
    const isExpanded = this.button.getAttribute('aria-expanded') === 'true';

    switch (e.key) {
      case 'ArrowDown':
      case 'ArrowUp':
      case 'Enter':
      case ' ':
        e.preventDefault();
        if (!isExpanded) {
          this.open();
        }
        break;
    }
  }

  handleListKeydown(e) {
    switch (e.key) {
      case 'ArrowDown':
        e.preventDefault();
        this.currentIndex = Math.min(this.currentIndex + 1, this.options.length - 1);
        this.focusOption(this.currentIndex);
        break;

      case 'ArrowUp':
        e.preventDefault();
        this.currentIndex = Math.max(this.currentIndex - 1, 0);
        this.focusOption(this.currentIndex);
        break;

      case 'Enter':
      case ' ':
        e.preventDefault();
        this.selectOption(this.currentIndex);
        this.close();
        this.button.focus();
        break;

      case 'Home':
        e.preventDefault();
        this.currentIndex = 0;
        this.focusOption(this.currentIndex);
        break;

      case 'End':
        e.preventDefault();
        this.currentIndex = this.options.length - 1;
        this.focusOption(this.currentIndex);
        break;

      case 'Tab':
        this.close();
        break;
    }
  }

  destroy() {
    // Cleanup would go here if needed
  }
}

/**
 * Drupal behavior for dropdown
 */
if (typeof Drupal !== 'undefined') {
  Drupal.behaviors.psDropdown = {
    attach(context) {
      const dropdowns = context.querySelectorAll('[data-dropdown]');

      dropdowns.forEach((element) => {
        if (typeof once !== 'undefined') {
          once('ps-dropdown', element).forEach((el) => {
            const wrapper = new PsDropdownWrapper(el);
            wrapper.init();
            el.psDropdownWrapper = wrapper;
          });
        } else {
          if (!element.dataset.dropdownInitialized) {
            const wrapper = new PsDropdownWrapper(element);
            wrapper.init();
            element.dataset.dropdownInitialized = 'true';
            element.psDropdownWrapper = wrapper;
          }
        }
      });
    },

    detach(context, _settings, trigger) {
      if (trigger === 'unload') {
        const dropdowns = context.querySelectorAll('[data-dropdown]');
        dropdowns.forEach((element) => {
          if (element.psDropdownWrapper) {
            element.psDropdownWrapper.destroy();
            delete element.psDropdownWrapper;
          }
        });
      }
    },
  };
}

// Export for non-Drupal environments
if (typeof module !== 'undefined' && module.exports) {
  module.exports = { PsDropdownWrapper };
}
