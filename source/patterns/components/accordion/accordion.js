/**
 * Accordion Component - Vanilla JS for Storybook
 * Handles expand/collapse interaction with ARIA support
 */

(() => {
  /**
   * Animation durations (in ms)
   */
  const ANIMATION_DURATION = {
    slide: 300,
    fade: 250,
    scale: 300,
    none: 0,
  };

  /**
   * Animate panel opening with selected animation type
   * @param {HTMLElement} panel - Panel to open
   * @param {string} animation - Animation type
   */
  function openPanel(panel, animation = 'slide') {
    if (animation === 'none') {
      panel.hidden = false;
      return;
    }

    // Prepare for animation
    panel.hidden = false;
    panel.style.overflow = 'hidden';

    if (animation === 'slide') {
      // Get final height
      const height = panel.scrollHeight;
      panel.style.height = '0px';
      panel.style.opacity = '1';

      // Force reflow
      panel.offsetHeight;

      // Animate
      panel.style.transition = `height ${ANIMATION_DURATION.slide}ms var(--ease-out-2, cubic-bezier(0.16, 1, 0.3, 1))`;
      panel.style.height = `${height}px`;

      // Cleanup after animation
      setTimeout(() => {
        panel.style.height = '';
        panel.style.overflow = '';
        panel.style.transition = '';
      }, ANIMATION_DURATION.slide);
    } else if (animation === 'fade') {
      panel.style.opacity = '0';
      panel.style.transition = `opacity ${ANIMATION_DURATION.fade}ms var(--ease-out-2, cubic-bezier(0.16, 1, 0.3, 1))`;

      // Force reflow
      panel.offsetHeight;

      panel.style.opacity = '1';

      setTimeout(() => {
        panel.style.opacity = '';
        panel.style.overflow = '';
        panel.style.transition = '';
      }, ANIMATION_DURATION.fade);
    } else if (animation === 'scale') {
      panel.style.opacity = '0';
      panel.style.transform = 'scaleY(0.95)';
      panel.style.transformOrigin = 'top';
      panel.style.transition = `opacity ${ANIMATION_DURATION.scale}ms var(--ease-out-2, cubic-bezier(0.16, 1, 0.3, 1)), transform ${ANIMATION_DURATION.scale}ms var(--ease-out-2, cubic-bezier(0.16, 1, 0.3, 1))`;

      // Force reflow
      panel.offsetHeight;

      panel.style.opacity = '1';
      panel.style.transform = 'scaleY(1)';

      setTimeout(() => {
        panel.style.opacity = '';
        panel.style.transform = '';
        panel.style.transformOrigin = '';
        panel.style.overflow = '';
        panel.style.transition = '';
      }, ANIMATION_DURATION.scale);
    }
  }

  /**
   * Animate panel closing with selected animation type
   * @param {HTMLElement} panel - Panel to close
   * @param {string} animation - Animation type
   */
  function closePanel(panel, animation = 'slide') {
    if (animation === 'none') {
      panel.hidden = true;
      return;
    }

    panel.style.overflow = 'hidden';

    if (animation === 'slide') {
      const height = panel.scrollHeight;
      panel.style.height = `${height}px`;

      // Force reflow
      panel.offsetHeight;

      panel.style.transition = `height ${ANIMATION_DURATION.slide}ms var(--ease-out-2, cubic-bezier(0.16, 1, 0.3, 1))`;
      panel.style.height = '0px';

      setTimeout(() => {
        panel.hidden = true;
        panel.style.height = '';
        panel.style.overflow = '';
        panel.style.transition = '';
      }, ANIMATION_DURATION.slide);
    } else if (animation === 'fade') {
      panel.style.transition = `opacity ${ANIMATION_DURATION.fade}ms var(--ease-out-2, cubic-bezier(0.16, 1, 0.3, 1))`;
      panel.style.opacity = '0';

      setTimeout(() => {
        panel.hidden = true;
        panel.style.opacity = '';
        panel.style.overflow = '';
        panel.style.transition = '';
      }, ANIMATION_DURATION.fade);
    } else if (animation === 'scale') {
      panel.style.transformOrigin = 'top';
      panel.style.transition = `opacity ${ANIMATION_DURATION.scale}ms var(--ease-out-2, cubic-bezier(0.16, 1, 0.3, 1)), transform ${ANIMATION_DURATION.scale}ms var(--ease-out-2, cubic-bezier(0.16, 1, 0.3, 1))`;
      panel.style.opacity = '0';
      panel.style.transform = 'scaleY(0.95)';

      setTimeout(() => {
        panel.hidden = true;
        panel.style.opacity = '';
        panel.style.transform = '';
        panel.style.transformOrigin = '';
        panel.style.overflow = '';
        panel.style.transition = '';
      }, ANIMATION_DURATION.scale);
    }
  }

  /**
   * Toggle accordion item state with animation
   * @param {HTMLElement} trigger - Button element
   * @param {HTMLElement} root - Accordion root element
   * @param {boolean} singleOpen - Only one item open at a time
   */
  function toggleAccordionItem(trigger, root, singleOpen) {
    const isExpanded = trigger.getAttribute('aria-expanded') === 'true';
    const panelId = trigger.getAttribute('aria-controls');
    const panel = root.querySelector(`#${panelId}`);
    const item = trigger.closest('.ps-accordion__item');
    const animation = root.getAttribute('data-animation') || 'slide';

    if (!panel || !item) {
      return;
    }

    // Close all items if singleOpen is enabled and we're opening a new one
    if (singleOpen && !isExpanded) {
      root.querySelectorAll('[data-accordion-trigger]').forEach((btn) => {
        if (btn !== trigger) {
          btn.setAttribute('aria-expanded', 'false');
          const otherPanelId = btn.getAttribute('aria-controls');
          const otherPanel = root.querySelector(`#${otherPanelId}`);
          if (otherPanel && !otherPanel.hidden) {
            closePanel(otherPanel, animation);
          }
          btn.closest('.ps-accordion__item')?.classList.remove('ps-accordion__item--open');
        }
      });
    }

    // Toggle current item with animation
    if (isExpanded) {
      trigger.setAttribute('aria-expanded', 'false');
      closePanel(panel, animation);
      item.classList.remove('ps-accordion__item--open');
    } else {
      trigger.setAttribute('aria-expanded', 'true');
      openPanel(panel, animation);
      item.classList.add('ps-accordion__item--open');
    }
  }

  /**
   * Initialize all accordions on the page
   */
  function initAccordions() {
    const accordions = document.querySelectorAll('[data-accordion]');

    accordions.forEach((root) => {
      // Skip if already initialized
      if (root.dataset.accordionInitialized === 'true') {
        return;
      }

      const singleOpen = root.getAttribute('data-single-open') === 'true';

      // Delegated event handling for all triggers within this accordion
      root.addEventListener('click', (evt) => {
        const trigger = evt.target.closest('[data-accordion-trigger]');
        if (!trigger) {
          return;
        }

        evt.preventDefault();
        toggleAccordionItem(trigger, root, singleOpen);
      });

      // Mark as initialized
      root.dataset.accordionInitialized = 'true';

      // Expose programmatic API
      root.PSAccordion = {
        openItem: (index) => {
          const triggers = Array.from(root.querySelectorAll('[data-accordion-trigger]'));
          if (triggers[index]) {
            toggleAccordionItem(triggers[index], root, singleOpen);
          }
        },
        closeAll: () => {
          const animation = root.getAttribute('data-animation') || 'slide';
          root.querySelectorAll('[data-accordion-trigger]').forEach((btn) => {
            const panelId = btn.getAttribute('aria-controls');
            const panel = root.querySelector(`#${panelId}`);
            btn.setAttribute('aria-expanded', 'false');
            if (panel && !panel.hidden) {
              closePanel(panel, animation);
            }
            btn.closest('.ps-accordion__item')?.classList.remove('ps-accordion__item--open');
          });
        },
      };
    });
  }

  /**
   * Expose global API
   */
  window.PSAccordion = {
    init: initAccordions,
    toggle: (accordionElement, itemIndex) => {
      if (accordionElement?.PSAccordion) {
        accordionElement.PSAccordion.openItem(itemIndex);
      }
    },
  };

  // Auto-initialize on DOM ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAccordions);
  } else {
    initAccordions();
  }

  // Re-initialize for dynamically added accordions (e.g., Storybook hot reload)
  const observer = new MutationObserver(() => {
    // Simple: just re-init all accordions when DOM changes
    initAccordions();
  });

  observer.observe(document.body, {
    childList: true,
    subtree: true,
  });
})();
