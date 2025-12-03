/**
 * Accordion Orchestration Behavior - Bootstrap-Inspired
 *
 * Coordinates multiple Collapse elements with single-open logic.
 * Listens to collapse:show events and triggers smooth animated closing of others.
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

          // Close all other collapses with smooth animation (Bootstrap style)
          const collapses = accordion.querySelectorAll('[data-accordion-item].ps-collapse');
          collapses.forEach((collapse) => {
            if (collapse !== expandedCollapse && collapse.classList.contains('is-expanded')) {
              // Trigger external close with animation
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
