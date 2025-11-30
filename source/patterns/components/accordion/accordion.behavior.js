((Drupal, once) => {
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

  Drupal.behaviors.psAccordion = {
    attach(context) {
      const accordions = once('ps-accordion', '[data-accordion]', context);

      accordions.forEach((root) => {
        const singleOpen = root.getAttribute('data-single-open') === 'true';

        // Delegated event handling for all triggers within this accordion
        once('ps-accordion-delegated', root).forEach(() => {
          root.addEventListener('click', (evt) => {
            const trigger = evt.target.closest('[data-accordion-trigger]');
            if (!trigger) {
              return;
            }

            evt.preventDefault();
            toggleAccordionItem(trigger, root, singleOpen);
          });
        });

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
    },
  };
})(Drupal, once);
