/**
 * @file
 * Read More component behavior.
 *
 * JavaScript-powered content expander for WYSIWYG content.
 * Measures content height and controls visibility via max-height transitions.
 *
 * Features:
 * - Dynamic height measurement (supports complex WYSIWYG content)
 * - Smooth max-height transitions
 * - Auto-hide toggle if content shorter than maxHeight
 * - Dynamic label update (Voir plus ↔ Voir moins)
 * - aria-expanded state management
 * - Chevron icon rotation (handled by CSS)
 * - Configurable via data attributes
 *
 * @see source/patterns/components/read-more/read-more.twig
 */

((Drupal, once) => {
  /**
   * Read More behavior.
   *
   * @type {Drupal~behavior}
   *
   * @prop {Drupal~behaviorAttach} attach
   *   Attaches read-more toggle functionality with height measurement.
   */
  Drupal.behaviors.readMore = {
    attach(context) {
      // Process all read-more components once
      once('read-more', '[data-read-more]', context).forEach((container) => {
        const toggle = container.querySelector('[data-read-more-toggle]');
        const label = container.querySelector('[data-read-more-label]');
        const content = container.querySelector('[data-read-more-content]');

        if (!toggle || !label || !content) {
          return;
        }

        // Get configuration from data attributes
        const maxHeight = parseInt(container.dataset.maxHeight) || 150;
        const expandLabel = container.dataset.expandLabel || 'Voir plus';
        const collapseLabel = container.dataset.collapseLabel || 'Voir moins';

        // Measure actual content height
        const measureHeight = () => {
          // Temporarily remove max-height to measure full content
          const originalMaxHeight = content.style.maxHeight;
          content.style.maxHeight = 'none';
          const fullHeight = content.scrollHeight;
          content.style.maxHeight = originalMaxHeight;
          return fullHeight;
        };

        const fullHeight = measureHeight();

        // Hide toggle if content is shorter than maxHeight
        if (fullHeight <= maxHeight) {
          toggle.style.display = 'none';
          content.style.maxHeight = 'none';
          container.classList.add('ps-read-more--expanded'); // Remove gradient overlay
          return;
        }

        // Show toggle and set initial height based on expanded state
        toggle.style.display = '';
        const isInitiallyExpanded = container.classList.contains('ps-read-more--expanded');
        content.style.maxHeight = isInitiallyExpanded ? 'none' : `${maxHeight}px`;

        // Store heights for smooth transitions
        const collapsedHeight = maxHeight;
        const expandedHeight = fullHeight;

        // Toggle click handler
        toggle.addEventListener('click', () => {
          const isExpanded = container.classList.contains('ps-read-more--expanded');

          if (isExpanded) {
            // Collapse: Set explicit height then transition to collapsed
            content.style.maxHeight = `${expandedHeight}px`;
            // Force reflow for smooth transition
            void content.offsetHeight;
            content.style.maxHeight = `${collapsedHeight}px`;

            container.classList.remove('ps-read-more--expanded');
            toggle.setAttribute('aria-expanded', 'false');
            label.textContent = expandLabel;
          } else {
            // Expand: Transition to full height
            content.style.maxHeight = `${expandedHeight}px`;

            container.classList.add('ps-read-more--expanded');
            toggle.setAttribute('aria-expanded', 'true');
            label.textContent = collapseLabel;

            // After transition, set to 'none' for flexibility (content can grow)
            const transitionDuration = parseFloat(
              window.getComputedStyle(content).transitionDuration
            );
            setTimeout(() => {
              if (container.classList.contains('ps-read-more--expanded')) {
                content.style.maxHeight = 'none';
              }
            }, transitionDuration * 1000);
          }
        });

        // Re-measure on window resize (debounced)
        let resizeTimeout;
        window.addEventListener('resize', () => {
          clearTimeout(resizeTimeout);
          resizeTimeout = setTimeout(() => {
            const newFullHeight = measureHeight();
            const isExpanded = container.classList.contains('ps-read-more--expanded');

            if (isExpanded) {
              content.style.maxHeight = `${newFullHeight}px`;
            }
          }, 250);
        });
      });
    },
  };
})(Drupal, once);
