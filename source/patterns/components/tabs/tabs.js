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

((Drupal, once) => {
  /**
   * Tabs wrapper class
   */
  function PsTabsWrapper(element) {
    this.element = element;
    this.tablist = element.querySelector('[role="tablist"]');
    this.tabs = Array.from(element.querySelectorAll('[role="tab"]'));
    this.panels = Array.from(element.querySelectorAll('[role="tabpanel"]'));

    // Configuration
    this.activation = element.dataset.activation === 'manual' ? 'manual' : 'auto';
    this.orientation = element.dataset.orientation === 'vertical' ? 'vertical' : 'horizontal';

    // Find initial active tab
    this.currentIndex = this.tabs.findIndex((tab) => tab.getAttribute('aria-selected') === 'true');
    if (this.currentIndex === -1) {
      this.currentIndex = this.findFirstEnabled();
    }
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
    this.tablist.addEventListener('keydown', (e) => {
      this.handleKeydown(e);
    });

    // Bind click events
    this.tabs.forEach((tab, index) => {
      tab.addEventListener('click', () => {
        if (!this.isDisabled(tab)) {
          this.selectTab(index);
        }
      });
    });

    // Bind focus events for auto-activation
    if (this.activation === 'auto') {
      this.tabs.forEach((tab, index) => {
        tab.addEventListener('focus', () => {
          if (!this.isDisabled(tab)) {
            this.selectTab(index);
          }
        });
      });
    }

    // Show initial panel
    this.selectTab(this.currentIndex);
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
  };

  PsTabsWrapper.prototype.focusTab = function (index) {
    if (index >= 0 && index < this.tabs.length) {
      this.tabs[index].focus();
    }
  };

  PsTabsWrapper.prototype.getTargetIndexForKey = function (key) {
    var isHorizontal = this.orientation === 'horizontal';
    var isVertical = this.orientation === 'vertical';

    if ((key === 'ArrowLeft' || key === 'Left') && isHorizontal) {
      return this.findNextEnabled(this.currentIndex, -1);
    }
    if ((key === 'ArrowRight' || key === 'Right') && isHorizontal) {
      return this.findNextEnabled(this.currentIndex, 1);
    }
    if ((key === 'ArrowUp' || key === 'Up') && isVertical) {
      return this.findNextEnabled(this.currentIndex, -1);
    }
    if ((key === 'ArrowDown' || key === 'Down') && isVertical) {
      return this.findNextEnabled(this.currentIndex, 1);
    }
    if (key === 'Home') {
      return this.findNextEnabled(-1, 1);
    }
    if (key === 'End') {
      return this.findNextEnabled(this.tabs.length, -1);
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

  PsTabsWrapper.prototype.destroy = () => {
    // Cleanup would go here if needed
  };

  /**
   * Drupal behavior for tabs
   */
  Drupal.behaviors.psTabs = {
    attach: (context, _settings) => {
      once('ps-tabs', '[data-tabs]', context).forEach((element) => {
        var wrapper = new PsTabsWrapper(element);
        wrapper.init();
        element.psTabsWrapper = wrapper;
      });
    },

    detach: (context, _settings, trigger) => {
      var tabsElements;
      if (trigger === 'unload') {
        tabsElements = context.querySelectorAll('[data-tabs]');
        tabsElements.forEach((element) => {
          if (element.psTabsWrapper) {
            element.psTabsWrapper.destroy();
            delete element.psTabsWrapper;
          }
        });
      }
    },
  };
})(Drupal, once);
