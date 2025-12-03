/**
 * Collapse Behavior - Bootstrap-Inspired
 *
 * Handles expand/collapse with smooth state-class transitions.
 * Uses .is-collapsing during animation, .is-expanded when open.
 * Dispatches events for external coordination (accordion single-open).
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

        const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        const show = () => {
          // Start: remove hidden, set starting height
          panel.removeAttribute('hidden');
          panel.style.height = '0px';

          // Add transitioning class
          collapse.classList.add('is-collapsing');
          collapse.classList.remove('is-expanded');

          // Force reflow
          void panel.offsetHeight;

          // Set target height
          const targetHeight = panel.scrollHeight;
          panel.style.height = `${targetHeight}px`;

          const onEnd = (e) => {
            if (e.propertyName !== 'height') {
              return;
            }

            panel.removeEventListener('transitionend', onEnd);
            collapse.classList.remove('is-collapsing');
            collapse.classList.add('is-expanded');
            panel.style.height = '';

            // Dispatch completion event
            collapse.dispatchEvent(
              new CustomEvent('collapse:shown', { bubbles: true, detail: { collapse } })
            );
          };

          if (prefersReducedMotion) {
            collapse.classList.remove('is-collapsing');
            collapse.classList.add('is-expanded');
            panel.style.height = '';
            collapse.dispatchEvent(
              new CustomEvent('collapse:shown', { bubbles: true, detail: { collapse } })
            );
          } else {
            panel.addEventListener('transitionend', onEnd);
          }

          // Dispatch show event immediately (Bootstrap pattern)
          collapse.dispatchEvent(
            new CustomEvent('collapse:show', { bubbles: true, detail: { collapse } })
          );
        };

        const hide = () => {
          // Capture current height
          panel.style.height = `${panel.scrollHeight}px`;

          // Force reflow
          void panel.offsetHeight;

          // Add transitioning class
          collapse.classList.add('is-collapsing');
          collapse.classList.remove('is-expanded');

          // Animate to 0
          panel.style.height = '0px';

          const onEnd = (e) => {
            if (e.propertyName !== 'height') {
              return;
            }

            panel.removeEventListener('transitionend', onEnd);
            collapse.classList.remove('is-collapsing');
            panel.setAttribute('hidden', '');
            panel.style.height = '';

            // Dispatch completion event
            collapse.dispatchEvent(
              new CustomEvent('collapse:hidden', { bubbles: true, detail: { collapse } })
            );
          };

          if (prefersReducedMotion) {
            collapse.classList.remove('is-collapsing');
            panel.setAttribute('hidden', '');
            panel.style.height = '';
            collapse.dispatchEvent(
              new CustomEvent('collapse:hidden', { bubbles: true, detail: { collapse } })
            );
          } else {
            panel.addEventListener('transitionend', onEnd);
          }

          // Dispatch hide event immediately (Bootstrap pattern)
          collapse.dispatchEvent(
            new CustomEvent('collapse:hide', { bubbles: true, detail: { collapse } })
          );
        };

        const toggle = () => {
          const isExpanded = collapse.classList.contains('is-expanded');

          // Update ARIA
          trigger.setAttribute('aria-expanded', !isExpanded);

          if (isExpanded) {
            hide();
          } else {
            show();
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
          const currentState = collapse.classList.contains('is-expanded');

          if (targetState === undefined || targetState === currentState) {
            return;
          }

          // Update ARIA
          trigger.setAttribute('aria-expanded', targetState);

          // Trigger transition
          if (targetState) {
            show();
          } else {
            hide();
          }
        });

        // Initialize
        const initiallyExpanded = trigger.getAttribute('aria-expanded') === 'true';
        if (initiallyExpanded) {
          collapse.classList.add('is-expanded');
          panel.removeAttribute('hidden');
        } else {
          panel.setAttribute('hidden', '');
        }
      });
    },
  };
})(Drupal, once);
