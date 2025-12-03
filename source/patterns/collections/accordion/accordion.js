/**
 * Accordion Orchestration Behavior
 *
 * Coordinates multiple Collapse elements with single-open logic.
 * Listens to collapse:show events and closes other items when needed.
 */

((Drupal, once) => {
  Drupal.behaviors.psAccordion = {
    attach(context) {
      const accordions = once('ps-accordion', '[data-accordion]', context);

      accordions.forEach((accordion) => {
        const singleOpen = accordion.getAttribute('data-single-open') === 'true';

        if (!singleOpen) {
          return; // No coordination needed for multi-open
        }

        // Listen for collapse:show events from child collapses
        accordion.addEventListener('collapse:show', (e) => {
          const expandedCollapse = e.detail.collapse;

          // Close all other collapses
          // Note: data-accordion-item is set on the root .ps-collapse element itself
          // so we must not look for a descendant .ps-collapse
          const collapses = accordion.querySelectorAll('[data-accordion-item].ps-collapse');
          collapses.forEach((collapse) => {
            if (collapse !== expandedCollapse) {
              // Trigger external close via custom event
              collapse.dispatchEvent(
                new CustomEvent('collapse:external-toggle', {
                  detail: { expanded: false },
                })
              );
            }
          });
        });
      });
    },
  };
})(Drupal, once);
