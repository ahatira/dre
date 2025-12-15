/**
 * Tabs behavior - Accessible tablist with keyboard navigation
 *
 * Implements WAI-ARIA Tabs pattern with:
 * - Roving tabindex navigation (Arrow keys, Home/End)
 * - Auto activation (default) or manual activation mode
 * - Horizontal (default) or vertical orientation
 * - Proper ARIA attributes management
 * - Full keyboard support with disabled tab handling
 */

((Drupal, _once) => {
  /**
   * Tabs wrapper class
   */
  function PsTabsWrapper(element) {
    this.element = element;
    this.tablist = element.querySelector('[role="tablist"]');
    this.tabs = Array.from(element.querySelectorAll('[role="tab"]'));
    this.panels = Array.from(element.querySelectorAll('[role="tabpanel"]'));
    this.indicator = element.querySelector('.ps-tabs__indicator');

    // Configuration
    this.activation = element.dataset.activation === 'manual' ? 'manual' : 'auto';
    this.orientation = element.dataset.orientation === 'vertical' ? 'vertical' : 'horizontal';

    // Find initial active tab
    this.currentIndex = this.tabs.findIndex((tab) => tab.getAttribute('aria-selected') === 'true');
    if (this.currentIndex === -1) {
      this.currentIndex = this.findFirstEnabled();
    }

    // Store bound handlers for cleanup
    this.boundHandlers = {
      keydown: null,
      clicks: [],
      focuses: [],
    };
  }

  PsTabsWrapper.prototype.init = function () {
    if (!this.tablist || this.tabs.length === 0 || this.panels.length === 0) {
      return;
    }

    // Set ARIA orientation for vertical tabs
    if (this.orientation === 'vertical') {
      this.tablist.setAttribute('aria-orientation', 'vertical');
    }
    this.tabs.forEach((tab, index) => {
      if (this.isDisabled(tab)) {
        tab.setAttribute('tabindex', '-1');
      } else {
        tab.setAttribute('tabindex', index === this.currentIndex ? '0' : '-1');
      }
    });

    // Bind keyboard navigation
    this.boundHandlers.keydown = (e) => {
      this.handleKeydown(e);
    };
    this.tablist.addEventListener('keydown', this.boundHandlers.keydown);

    // Bind click events
    this.tabs.forEach((tab, index) => {
      var clickHandler = () => {
        if (!this.isDisabled(tab)) {
          this.selectTab(index);
        }
      };
      tab.addEventListener('click', clickHandler);
      this.boundHandlers.clicks.push({ tab: tab, handler: clickHandler });
    });

    // Bind focus events for auto-activation
    if (this.activation === 'auto') {
      this.tabs.forEach((tab, index) => {
        var focusHandler = () => {
          if (!this.isDisabled(tab)) {
            this.selectTab(index);
          }
        };
        tab.addEventListener('focus', focusHandler);
        this.boundHandlers.focuses.push({ tab: tab, handler: focusHandler });
      });
    }

    // Show initial panel
    this.selectTab(this.currentIndex);

    // Position indicator with a slight delay to ensure DOM is ready
    setTimeout(() => {
      this.updateIndicator(this.currentIndex);
    }, 50);
  };

  /**
   * Update animated indicator position
   * @param {number} index - Target tab index
   */
  PsTabsWrapper.prototype.updateIndicator = function (index) {
    if (!this.indicator) {
      return;
    }

    var activeTab = this.tabs[index];
    if (!activeTab) {
      return;
    }

    var tablistRect = this.tablist.getBoundingClientRect();
    var tabRect = activeTab.getBoundingClientRect();
    var top, height, left, width;

    if (this.orientation === 'vertical') {
      // Vertical mode: animate height and translateY
      top = tabRect.top - tablistRect.top;
      height = tabRect.height;
      this.indicator.style.height = `${height}px`;
      this.indicator.style.transform = `translateY(${top}px)`;
    } else {
      // Horizontal mode: animate width and translateX
      left = tabRect.left - tablistRect.left;
      width = tabRect.width;
      this.indicator.style.width = `${width}px`;
      this.indicator.style.transform = `translateX(${left}px)`;
    }
  };

  PsTabsWrapper.prototype.isDisabled = (tab) =>
    tab.hasAttribute('disabled') || tab.classList.contains('is-disabled');

  PsTabsWrapper.prototype.findFirstEnabled = function () {
    var index = this.tabs.findIndex((tab) => !this.isDisabled(tab));
    return index !== -1 ? index : 0;
  };

  PsTabsWrapper.prototype.findNextEnabled = function (startIndex, direction) {
    var length = this.tabs.length;
    var index = startIndex;
    var i;

    // Search in direction
    for (i = 0; i < length; i++) {
      index = (index + direction + length) % length;
      if (!this.isDisabled(this.tabs[index])) {
        return index;
      }
    }

    // If no enabled tab found, return current
    return this.currentIndex;
  };

  PsTabsWrapper.prototype.selectTab = function (index) {
    if (index < 0 || index >= this.tabs.length || this.isDisabled(this.tabs[index])) {
      return;
    }

    // Update all tabs
    this.tabs.forEach((tab, i) => {
      var isSelected = i === index;
      tab.setAttribute('aria-selected', isSelected ? 'true' : 'false');
      tab.setAttribute('tabindex', isSelected ? '0' : '-1');
      tab.classList.toggle('is-selected', isSelected);
    });

    // Update all panels
    this.panels.forEach((panel, i) => {
      var isSelected = i === index;
      if (isSelected) {
        panel.removeAttribute('hidden');
        panel.classList.add('is-selected');
      } else {
        panel.setAttribute('hidden', '');
        panel.classList.remove('is-selected');
      }
    });

    this.currentIndex = index;

    // Update indicator position
    this.updateIndicator(index);
  };

  PsTabsWrapper.prototype.focusTab = function (index) {
    if (index >= 0 && index < this.tabs.length) {
      this.tabs[index].focus();
    }
  };

  /**
   * Get navigation direction from key press.
   * @param {string} key - Key pressed.
   * @returns {number} - Direction (1: forward, -1: backward, 0: no move).
   */
  PsTabsWrapper.prototype.getDirection = function (key) {
    var isHorizontal = this.orientation === 'horizontal';
    var isVertical = this.orientation === 'vertical';

    if (
      (key === 'ArrowRight' && isHorizontal) ||
      (key === 'Right' && isHorizontal) ||
      (key === 'ArrowDown' && isVertical) ||
      (key === 'Down' && isVertical)
    ) {
      return 1;
    }
    if (
      (key === 'ArrowLeft' && isHorizontal) ||
      (key === 'Left' && isHorizontal) ||
      (key === 'ArrowUp' && isVertical) ||
      (key === 'Up' && isVertical)
    ) {
      return -1;
    }
    return 0;
  };

  PsTabsWrapper.prototype.getTargetIndexForKey = function (key) {
    // Home/End keys
    if (key === 'Home') {
      return this.findNextEnabled(-1, 1);
    }
    if (key === 'End') {
      return this.findNextEnabled(this.tabs.length, -1);
    }

    // Arrow navigation
    var direction = this.getDirection(key);
    if (direction !== 0) {
      return this.findNextEnabled(this.currentIndex, direction);
    }

    return null;
  };

  PsTabsWrapper.prototype.handleKeydown = function (e) {
    // Handle Enter/Space for manual activation
    if (e.key === 'Enter' || e.key === ' ') {
      e.preventDefault();
      if (this.activation === 'manual') {
        this.selectTab(this.currentIndex);
      }
      return;
    }

    // Get target index for navigation keys
    var targetIndex = this.getTargetIndexForKey(e.key);
    if (targetIndex !== null) {
      e.preventDefault();
      this.handleTabNavigation(targetIndex);
    }
  };

  PsTabsWrapper.prototype.handleTabNavigation = function (targetIndex) {
    if (targetIndex !== null && targetIndex !== this.currentIndex) {
      // Update tabindex for roving focus
      this.tabs[this.currentIndex].setAttribute('tabindex', '-1');
      this.tabs[targetIndex].setAttribute('tabindex', '0');
      this.focusTab(targetIndex);

      if (this.activation === 'auto') {
        this.selectTab(targetIndex);
      } else {
        // Manual mode: just move focus, don't activate
        this.currentIndex = targetIndex;
      }
    }
  };

  PsTabsWrapper.prototype.destroy = function () {
    // Remove keydown listener from tablist
    if (this.boundHandlers.keydown) {
      this.tablist.removeEventListener('keydown', this.boundHandlers.keydown);
    }

    // Remove click listeners
    this.boundHandlers.clicks.forEach((item) => {
      item.tab.removeEventListener('click', item.handler);
    });

    // Remove focus listeners
    this.boundHandlers.focuses.forEach((item) => {
      item.tab.removeEventListener('focus', item.handler);
    });

    // Clear stored handlers
    this.boundHandlers.keydown = null;
    this.boundHandlers.clicks = [];
    this.boundHandlers.focuses = [];
  };

  /**
   * Drupal behavior for tabs
   */
  Drupal.behaviors.psTabs = {
    attach: (context, _settings) => {
      var tabsElements = context.querySelectorAll('[data-tabs]');

      tabsElements.forEach((element) => {
        // Clean up existing instance if any
        if (element.psTabsWrapper) {
          element.psTabsWrapper.destroy();
          delete element.psTabsWrapper;
        }

        // Remove once marker to allow re-initialization (Storybook support)
        element.removeAttribute('data-once-ps-tabs');

        // Initialize new instance
        var wrapper = new PsTabsWrapper(element);
        wrapper.init();
        element.psTabsWrapper = wrapper;

        // Mark as initialized by once
        element.setAttribute('data-once-ps-tabs', '');
      });
    },

    detach: (context, _settings, _trigger) => {
      var tabsElements = context.querySelectorAll('[data-tabs]');
      tabsElements.forEach((element) => {
        if (element.psTabsWrapper) {
          element.psTabsWrapper.destroy();
          delete element.psTabsWrapper;
        }
      });
    },
  };
})(Drupal, once); // eslint-disable-line no-undef
