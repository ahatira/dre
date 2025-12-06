/**
 * Language Selector - Accessible dropdown behavior
 *
 * Provides keyboard navigation and interaction for language selection.
 * Progressive enhancement: works with native <select> fallback if JS disabled.
 */

export class PsLanguageSelector {
  constructor(root, options = {}) {
    this.root = root;
    this.options = { ...PsLanguageSelector.defaults, ...options };

    // DOM references
    this.button = root.querySelector('.ps-language-selector__button');
    this.list = root.querySelector('.ps-language-selector__list');
    this.nativeSelect = root.querySelector('.ps-language-selector__native');

    // Options (only enabled ones)
    this.options_elements = this.list
      ? Array.from(
          this.list.querySelectorAll('.ps-language-selector__option:not([aria-disabled="true"])')
        )
      : [];

    // Current selected index
    this.currentIndex = this.options_elements.findIndex(
      (opt) => opt.getAttribute('aria-selected') === 'true'
    );

    if (this.currentIndex === -1) {
      this.currentIndex = 0;
    }

    // AbortController for cleanup
    this.controllers = [];
    this.initialized = false;
  }

  static defaults = {
    closeOnSelect: true,
    closeOnOutsideClick: true,
    closeOnEscape: true,
  };

  init() {
    if (this.initialized || !this.button || !this.list) {
      return;
    }
    this.initialized = true;

    const ac = new AbortController();
    this.controllers.push(ac);

    // Toggle dropdown on button click
    this.button.addEventListener('click', this.toggle.bind(this), {
      signal: ac.signal,
    });

    // Select option on click
    this.options_elements.forEach((option, index) => {
      option.addEventListener(
        'click',
        () => {
          this.selectOption(index);
          if (this.options.closeOnSelect) {
            this.close();
          }
        },
        { signal: ac.signal }
      );
    });

    // Keyboard navigation on button
    this.button.addEventListener('keydown', this.handleButtonKeydown.bind(this), {
      signal: ac.signal,
    });

    // Keyboard navigation on list
    this.list.addEventListener('keydown', this.handleListKeydown.bind(this), {
      signal: ac.signal,
    });

    // Close on outside click
    if (this.options.closeOnOutsideClick) {
      document.addEventListener('click', this.handleOutsideClick.bind(this), {
        signal: ac.signal,
      });
    }

    // Close on Escape
    if (this.options.closeOnEscape) {
      this.root.addEventListener('keydown', this.handleEscape.bind(this), {
        signal: ac.signal,
      });
    }
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
    // Update ARIA selected state
    this.options_elements.forEach((opt, i) => {
      opt.setAttribute('aria-selected', i === index ? 'true' : 'false');
    });

    // Update current index
    this.currentIndex = index;

    // Update button label
    const selectedOption = this.options_elements[index];
    const labelElement = this.button.querySelector('.ps-language-selector__label');
    const flagElement = this.button.querySelector('.ps-flag');

    if (labelElement) {
      const optionLabel = selectedOption.querySelector('.ps-language-selector__label');
      if (optionLabel) {
        labelElement.textContent = optionLabel.textContent.trim();
      }
    }

    // Update button flag (replace entire flag element)
    if (flagElement) {
      const optionFlag = selectedOption.querySelector('.ps-flag');
      if (optionFlag) {
        flagElement.replaceWith(optionFlag.cloneNode(true));
      }
    }

    // Update native select
    const value = selectedOption.dataset.value;
    if (this.nativeSelect && value) {
      this.nativeSelect.value = value;

      // Trigger change event for form handling
      const changeEvent = new Event('change', { bubbles: true });
      this.nativeSelect.dispatchEvent(changeEvent);
    }

    // Handle URL navigation if data-url exists
    const url = selectedOption.dataset.url;
    if (url) {
      // Dispatch custom event before navigation (allows cancellation)
      const navigationEvent = new CustomEvent('ps-language-selector:navigate', {
        detail: { url, value, option: selectedOption },
        cancelable: true,
      });
      this.root.dispatchEvent(navigationEvent);

      if (!navigationEvent.defaultPrevented) {
        window.location.href = url;
      }
    }
  }

  focusOption(index) {
    if (this.options_elements[index]) {
      this.options_elements[index].focus();
    }
  }

  handleButtonKeydown(e) {
    const isExpanded = this.button.getAttribute('aria-expanded') === 'true';

    // Open on Enter, Space, or Arrow keys
    if (!isExpanded && ['Enter', ' ', 'ArrowDown', 'ArrowUp'].includes(e.key)) {
      e.preventDefault();
      this.open();
      return;
    }
  }

  handleListKeydown(e) {
    switch (e.key) {
      case 'ArrowDown':
        e.preventDefault();
        this.navigateOptions(1);
        break;

      case 'ArrowUp':
        e.preventDefault();
        this.navigateOptions(-1);
        break;

      case 'Home':
        e.preventDefault();
        this.focusOption(0);
        this.currentIndex = 0;
        break;

      case 'End': {
        e.preventDefault();
        const lastIndex = this.options_elements.length - 1;
        this.focusOption(lastIndex);
        this.currentIndex = lastIndex;
        break;
      }

      case 'Enter':
      case ' ':
        e.preventDefault();
        this.selectOption(this.currentIndex);
        if (this.options.closeOnSelect) {
          this.close();
          this.button.focus();
        }
        break;

      case 'Escape':
        // Handled by handleEscape
        break;

      default:
        // Letter key: focus first option starting with that letter
        if (e.key.length === 1 && /[a-z]/i.test(e.key)) {
          this.navigateByLetter(e.key);
        }
        break;
    }
  }

  navigateOptions(direction) {
    let newIndex = this.currentIndex + direction;

    // Wrap around
    if (newIndex < 0) {
      newIndex = this.options_elements.length - 1;
    } else if (newIndex >= this.options_elements.length) {
      newIndex = 0;
    }

    this.currentIndex = newIndex;
    this.focusOption(newIndex);
  }

  navigateByLetter(letter) {
    const lowerLetter = letter.toLowerCase();

    // Search from current index + 1
    let foundIndex = -1;
    for (let i = this.currentIndex + 1; i < this.options_elements.length; i++) {
      const text = this.options_elements[i].textContent.trim().toLowerCase();
      if (text.startsWith(lowerLetter)) {
        foundIndex = i;
        break;
      }
    }

    // If not found, search from beginning
    if (foundIndex === -1) {
      for (let i = 0; i <= this.currentIndex; i++) {
        const text = this.options_elements[i].textContent.trim().toLowerCase();
        if (text.startsWith(lowerLetter)) {
          foundIndex = i;
          break;
        }
      }
    }

    if (foundIndex !== -1) {
      this.currentIndex = foundIndex;
      this.focusOption(foundIndex);
    }
  }

  handleOutsideClick(e) {
    if (!this.root.contains(e.target)) {
      this.close();
    }
  }

  handleEscape(e) {
    if (e.key === 'Escape') {
      const isExpanded = this.button.getAttribute('aria-expanded') === 'true';
      if (isExpanded) {
        e.preventDefault();
        e.stopPropagation();
        this.close();
        this.button.focus();
      }
    }
  }

  destroy() {
    this.controllers.forEach((c) => c.abort());
    this.controllers = [];
    this.initialized = false;
  }
}

/**
 * Drupal Behavior for Language Selector
 */
if (typeof Drupal !== 'undefined') {
  Drupal.behaviors.psLanguageSelector = {
    attach(context) {
      const globalConfig =
        typeof drupalSettings !== 'undefined'
          ? drupalSettings?.psTheme?.components?.languageSelector || {}
          : {};

      const elements = context.querySelectorAll('.ps-language-selector');

      elements.forEach((root) => {
        // Use Drupal's once if available, otherwise check manual flag
        if (typeof once !== 'undefined') {
          once('psLanguageSelector', root).forEach((el) => {
            initLanguageSelector(el, globalConfig);
          });
        } else {
          if (!root.__psInstance) {
            initLanguageSelector(root, globalConfig);
          }
        }
      });
    },

    detach(context, _settings, trigger) {
      // Only cleanup on unload (AJAX/BigPipe fragment removal)
      if (trigger !== 'unload') {
        return;
      }

      context.querySelectorAll('.ps-language-selector').forEach((root) => {
        if (root.__psInstance) {
          root.__psInstance.destroy();
          root.__psInstance = null;
        }
      });
    },
  };
}

/**
 * Helper function to initialize language selector
 */
function initLanguageSelector(root, globalConfig = {}) {
  // Skip if already initialized
  if (root.__psInstance) {
    return;
  }

  // Parse local configuration from data attributes
  const localConfig = {
    closeOnSelect:
      root.dataset.closeOnSelect !== undefined ? root.dataset.closeOnSelect === 'true' : undefined,
    closeOnOutsideClick:
      root.dataset.closeOnOutsideClick !== undefined
        ? root.dataset.closeOnOutsideClick === 'true'
        : undefined,
    closeOnEscape:
      root.dataset.closeOnEscape !== undefined ? root.dataset.closeOnEscape === 'true' : undefined,
  };

  // Merge: local overrides global overrides defaults
  const instance = new PsLanguageSelector(root, {
    ...globalConfig,
    ...localConfig,
  });
  instance.init();

  // Store instance for cleanup
  root.__psInstance = instance;
}

/**
 * Auto-initialize for Storybook (non-Drupal environments)
 */
if (typeof Drupal === 'undefined') {
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
      document.querySelectorAll('.ps-language-selector').forEach((root) => {
        initLanguageSelector(root);
      });
    });
  } else {
    // DOM already loaded
    document.querySelectorAll('.ps-language-selector').forEach((root) => {
      initLanguageSelector(root);
    });
  }
}
