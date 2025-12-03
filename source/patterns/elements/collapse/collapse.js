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

        // Helpers
        const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        const setExpandedAria = (state) => {
          trigger.setAttribute('aria-expanded', state);
          collapse.classList.toggle('is-expanded', state);
        };

        const animateOpen = () => {
          // Make panel visible and prepare for animation
          panel.removeAttribute('hidden');
          panel.style.opacity = '0';
          panel.style.height = '0px';
          // Force reflow
          void panel.offsetHeight;
          const target = panel.scrollHeight;
          panel.style.height = `${target}px`;
          panel.style.opacity = '1';

          const onEnd = (e) => {
            if (e.propertyName !== 'height') {
              return;
            }
            panel.style.height = 'auto';
            panel.removeEventListener('transitionend', onEnd);
            collapse.dispatchEvent(
              new CustomEvent('collapse:show', { bubbles: true, detail: { collapse } })
            );
          };
          panel.addEventListener('transitionend', onEnd);
        };

        const animateClose = () => {
          // If currently 'auto', fix to pixel value then animate to 0
          const currentAuto = panel.style.height === '' || panel.style.height === 'auto';
          if (currentAuto) {
            panel.style.height = `${panel.scrollHeight}px`;
          }
          // Force reflow
          void panel.offsetHeight;
          panel.style.opacity = '0';
          panel.style.height = '0px';

          const onEnd = (e) => {
            if (e.propertyName !== 'height') {
              return;
            }
            panel.setAttribute('hidden', '');
            panel.style.height = '';
            panel.removeEventListener('transitionend', onEnd);
            collapse.dispatchEvent(
              new CustomEvent('collapse:hide', { bubbles: true, detail: { collapse } })
            );
          };
          panel.addEventListener('transitionend', onEnd);
        };

        // Toggle expanded state with animation
        const toggle = () => {
          const isExpanded = trigger.getAttribute('aria-expanded') === 'true';
          const newState = !isExpanded;
          setExpandedAria(newState);

          if (prefersReducedMotion) {
            if (newState) {
              panel.removeAttribute('hidden');
              collapse.dispatchEvent(
                new CustomEvent('collapse:show', { bubbles: true, detail: { collapse } })
              );
            } else {
              panel.setAttribute('hidden', '');
              collapse.dispatchEvent(
                new CustomEvent('collapse:hide', { bubbles: true, detail: { collapse } })
              );
            }
            return;
          }

          if (newState) {
            animateOpen();
          } else {
            animateClose();
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

        // Initialize panel state for smooth animation on first toggle
        const initiallyExpanded = trigger.getAttribute('aria-expanded') === 'true';
        if (initiallyExpanded) {
          // Expanded at load → set height to auto after measuring
          panel.removeAttribute('hidden');
          if (!prefersReducedMotion) {
            panel.style.height = `${panel.scrollHeight}px`;
            void panel.offsetHeight;
            panel.style.height = 'auto';
            panel.style.opacity = '1';
          }
        } else {
          // Collapsed at load
          panel.setAttribute('hidden', '');
          if (!prefersReducedMotion) {
            panel.style.height = '';
            panel.style.opacity = '0';
          }
        }
      });
    },
  };
})(Drupal, once);
