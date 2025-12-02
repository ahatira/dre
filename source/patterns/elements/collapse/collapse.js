/**
 * Collapse Behavior
 *
 * Handles expand/collapse interaction for single disclosure items.
 * Dispatches custom events for external coordination (e.g., accordion single-open).
 */

((Drupal, once) => {
  Drupal.behaviors.collapse = {
    attach(context) {
      const collapses = once('collapse', '.ps-collapse', context);

      collapses.forEach((collapse) => {
        const trigger = collapse.querySelector('.ps-collapse__trigger');
        const panel = collapse.querySelector('.ps-collapse__panel');

        if (!trigger || !panel) {
          return;
        }

        // Toggle expanded state
        const toggle = () => {
          const isExpanded = trigger.getAttribute('aria-expanded') === 'true';
          const newState = !isExpanded;

          trigger.setAttribute('aria-expanded', newState);
          collapse.classList.toggle('is-expanded', newState);

          if (newState) {
            panel.removeAttribute('hidden');
            // Dispatch show event for coordination
            collapse.dispatchEvent(
              new CustomEvent('collapse:show', {
                bubbles: true,
                detail: { collapse },
              })
            );
          } else {
            panel.setAttribute('hidden', '');
            // Dispatch hide event
            collapse.dispatchEvent(
              new CustomEvent('collapse:hide', {
                bubbles: true,
                detail: { collapse },
              })
            );
          }
        };

        // Click handler
        trigger.addEventListener('click', toggle);

        // Keyboard: Enter/Space
        trigger.addEventListener('keydown', (e) => {
          if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            toggle();
          }
        });

        // External control via custom event
        collapse.addEventListener('collapse:external-toggle', (e) => {
          const targetState = e.detail?.expanded;
          const currentState = trigger.getAttribute('aria-expanded') === 'true';

          if (targetState !== undefined && targetState !== currentState) {
            toggle();
          }
        });
      });
    },
  };
})(Drupal, once);
