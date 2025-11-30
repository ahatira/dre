/**
 * Accordion Component - Vanilla JS for Storybook
 * Handles expand/collapse interaction with ARIA support
 */

(() => {
  /**
   * Toggle accordion item state
   * @param {HTMLElement} trigger - Button element
   * @param {HTMLElement} root - Accordion root element
   * @param {boolean} singleOpen - Only one item open at a time
   */
  function toggleAccordionItem(trigger, root, singleOpen) {
    const isExpanded = trigger.getAttribute('aria-expanded') === 'true';
    const panelId = trigger.getAttribute('aria-controls');
    const panel = root.querySelector(`#${panelId}`);
    const item = trigger.closest('.ps-accordion__item');

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
          if (otherPanel) {
            otherPanel.hidden = true;
          }
          btn.closest('.ps-accordion__item')?.classList.remove('ps-accordion__item--open');
        }
      });
    }

    // Toggle current item
    trigger.setAttribute('aria-expanded', String(!isExpanded));
    panel.hidden = isExpanded;
    item.classList.toggle('ps-accordion__item--open', !isExpanded);
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
          root.querySelectorAll('[data-accordion-trigger]').forEach((btn) => {
            const panelId = btn.getAttribute('aria-controls');
            const panel = root.querySelector(`#${panelId}`);
            btn.setAttribute('aria-expanded', 'false');
            if (panel) {
              panel.hidden = true;
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
