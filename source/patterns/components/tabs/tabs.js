/**
 * Tabs behavior - Accessible tablist with keyboard navigation
 *
 * Features:
 * - Roving tabindex with arrow key navigation (Left/Right, Home/End)
 * - Click/keyboard activation updates aria-selected, tabindex, and panel visibility
 * - Activation mode via data-activation: 'auto' (activate on focus) | 'manual'
 *   Default: 'auto' when not specified
 * - Supports vertical orientation via data-orientation="vertical" (ArrowUp/ArrowDown)
 */

class PsTabsWrapper {
  constructor(root) {
    this.root = root;
    this.tablist = root.querySelector('[role="tablist"]');
    this.tabs = Array.from(root.querySelectorAll('[data-tab][role="tab"]'));
    this.panels = Array.from(root.querySelectorAll('[data-tabpanel][role="tabpanel"]'));
    // Default activation is 'auto' unless explicitly set to 'manual'
    this.activation = root.dataset.activation === 'manual' ? 'manual' : 'auto';
    // Orientation can be 'horizontal' (default) or 'vertical'
    this.orientation = root.dataset.orientation === 'vertical' ? 'vertical' : 'horizontal';

    // Build mapping by controlled ids
    this.idToIndex = new Map();
    this.tabs.forEach((tab, index) => {
      const controls = tab.getAttribute('aria-controls');
      if (controls) {
        this.idToIndex.set(controls, index);
      }
    });

    // Infer initial active index from aria-selected="true"
    let activeIndex = this.tabs.findIndex((t) => t.getAttribute('aria-selected') === 'true');
    if (activeIndex < 0) {
      activeIndex = 0;
    }
    this.activeIndex = activeIndex;

    // Store bound handlers for cleanup
    this.handleKeydown = this.onKeydown.bind(this);
    this.handleTabClick = [];
    this.handleTabFocus = [];
  }

  init() {
    if (!this.tablist || this.tabs.length === 0 || this.panels.length === 0) {
      return;
    }

    // Reflect orientation in ARIA
    if (this.orientation === 'vertical') {
      this.tablist.setAttribute('aria-orientation', 'vertical');
    } else {
      this.tablist.removeAttribute('aria-orientation');
    }

    // Ensure proper tabindex setup
    this.tabs.forEach((tab, index) => {
      const disabled = tab.hasAttribute('disabled') || tab.classList.contains('is-disabled');
      if (disabled) {
        tab.setAttribute('tabindex', '-1');
        return;
      }
      tab.setAttribute('tabindex', index === this.activeIndex ? '0' : '-1');
    });

    // Bind events with stored handlers for cleanup
    this.tablist.addEventListener('keydown', this.handleKeydown);

    this.tabs.forEach((tab, index) => {
      const clickHandler = (e) => {
        e.preventDefault();
        if (this.isDisabled(index)) {
          return;
        }
        this.activate(index, true);
      };

      const focusHandler = () => {
        if (this.activation === 'auto' && !this.isDisabled(index)) {
          this.activate(index, false);
        }
      };

      tab.addEventListener('click', clickHandler);
      tab.addEventListener('focus', focusHandler);

      // Store handlers for cleanup
      this.handleTabClick[index] = clickHandler;
      this.handleTabFocus[index] = focusHandler;
    });

    // Initial render to sync classes/hidden
    this.activate(this.activeIndex, false);
  }

  isDisabled(index) {
    const tab = this.tabs[index];
    return tab.hasAttribute('disabled') || tab.classList.contains('is-disabled');
  }

  activate(index, focus) {
    if (index < 0 || index >= this.tabs.length) {
      return;
    }

    this.tabs.forEach((tab, i) => {
      const selected = i === index;
      tab.setAttribute('aria-selected', selected ? 'true' : 'false');
      tab.setAttribute('tabindex', selected ? '0' : '-1');
      tab.classList.toggle('is-selected', selected);
    });

    this.panels.forEach((panel, i) => {
      const selected = i === index;
      if (selected) {
        panel.removeAttribute('hidden');
        panel.classList.add('is-selected');
      } else {
        panel.setAttribute('hidden', '');
        panel.classList.remove('is-selected');
      }
    });

    this.activeIndex = index;
    if (focus) {
      this.tabs[index].focus();
    }
  }

  onKeydown(e) {
    const key = e.key;
    const max = this.tabs.length - 1;
    let nextIndex = this.activeIndex;

    switch (key) {
      case 'ArrowDown':
      case 'Down':
        if (this.orientation === 'vertical') {
          e.preventDefault();
          nextIndex = this.findNextEnabled(this.activeIndex + 1, +1);
          break;
        }
        return;
      case 'ArrowUp':
      case 'Up':
        if (this.orientation === 'vertical') {
          e.preventDefault();
          nextIndex = this.findNextEnabled(this.activeIndex - 1, -1);
          break;
        }
        return;
      case 'ArrowRight':
      case 'Right':
        e.preventDefault();
        nextIndex = this.findNextEnabled(this.activeIndex + 1, +1);
        break;
      case 'ArrowLeft':
      case 'Left':
        e.preventDefault();
        nextIndex = this.findNextEnabled(this.activeIndex - 1, -1);
        break;
      case 'Home':
        e.preventDefault();
        nextIndex = this.findNextEnabled(0, +1);
        break;
      case 'End':
        e.preventDefault();
        nextIndex = this.findNextEnabled(max, -1);
        break;
      case 'Enter':
      case ' ':
        e.preventDefault();
        this.activate(this.activeIndex, true);
        return;
      default:
        return;
    }

    if (nextIndex !== this.activeIndex && nextIndex >= 0) {
      // Move focus to the new tab; activate depending on mode
      this.tabs[this.activeIndex].setAttribute('tabindex', '-1');
      this.tabs[nextIndex].setAttribute('tabindex', '0');
      this.tabs[nextIndex].focus();
      if (this.activation === 'auto') {
        this.activate(nextIndex, false);
      } else {
        this.activeIndex = nextIndex; // focus roving; wait for Enter/Space or click
      }
    }
  }

  findNextEnabled(start, step) {
    const len = this.tabs.length;
    let i = start;
    while (i >= 0 && i < len) {
      if (!this.isDisabled(i)) {
        return i;
      }
      i += step;
    }
    // If nothing found in direction, wrap
    i = step > 0 ? 0 : len - 1;
    while (i >= 0 && i < len) {
      if (!this.isDisabled(i)) {
        return i;
      }
      i += step;
    }
    return this.activeIndex;
  }

  destroy() {
    // Remove event listeners to prevent memory leaks
    if (this.tablist && this.handleKeydown) {
      this.tablist.removeEventListener('keydown', this.handleKeydown);
    }

    this.tabs.forEach((tab, index) => {
      if (this.handleTabClick[index]) {
        tab.removeEventListener('click', this.handleTabClick[index]);
      }
      if (this.handleTabFocus[index]) {
        tab.removeEventListener('focus', this.handleTabFocus[index]);
      }
    });

    // Clear stored handlers
    this.handleTabClick = [];
    this.handleTabFocus = [];
  }
}

// Drupal behavior wrapper
if (typeof Drupal !== 'undefined') {
  Drupal.behaviors.psTabs = {
    attach(context) {
      const roots = context.querySelectorAll('[data-tabs]');
      roots.forEach((root) => {
        if (typeof once !== 'undefined') {
          once('ps-tabs', root).forEach((el) => {
            const wrapper = new PsTabsWrapper(el);
            wrapper.init();
            el.psTabsWrapper = wrapper;
          });
        } else {
          if (!root.dataset.psTabsInitialized) {
            const wrapper = new PsTabsWrapper(root);
            wrapper.init();
            root.dataset.psTabsInitialized = 'true';
            root.psTabsWrapper = wrapper;
          }
        }
      });
    },

    detach(context, _settings, trigger) {
      if (trigger === 'unload') {
        const roots = context.querySelectorAll('[data-tabs]');
        roots.forEach((root) => {
          if (root.psTabsWrapper) {
            root.psTabsWrapper.destroy();
            delete root.psTabsWrapper;
          }
        });
      }
    },
  };
}

// Export for non-Drupal environments
if (typeof module !== 'undefined' && module.exports) {
  module.exports = { PsTabsWrapper };
}
