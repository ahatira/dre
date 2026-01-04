/**
 * Footer Menu Links Block - Collapse functionality
 * Transforms heading into collapsible trigger on mobile (< 1024px)
 */

((Drupal, once) => {
  Drupal.behaviors.footerMenuLinks = {
    attach(context) {
      const blocks = once('footer-menu-links', '.ps-footer-menu-links', context);

      blocks.forEach((block) => {
        const heading = block.querySelector('.ps-heading');
        const panel = block.querySelector('.ps-footer-menu-links__panel');

        if (!heading || !panel) {
          return;
        }

        // Media query for mobile/desktop detection
        const mediaQuery = window.matchMedia('(max-width: 1023px)');

        const applyMobileBehavior = (matches) => {
          if (matches) {
            // Mobile: make heading clickable
            heading.setAttribute('role', 'button');
            heading.setAttribute('tabindex', '0');
            heading.setAttribute('aria-expanded', 'false');
            heading.setAttribute('aria-controls', panel.id || 'footer-menu-links-panel');

            // Add event listeners
            heading.addEventListener('click', togglePanel);
            heading.addEventListener('keydown', handleKeydown);
          } else {
            // Desktop: remove interactive behavior
            heading.removeAttribute('role');
            heading.removeAttribute('tabindex');
            heading.removeAttribute('aria-expanded');
            heading.removeAttribute('aria-controls');
            panel.removeAttribute('aria-expanded');

            heading.removeEventListener('click', togglePanel);
            heading.removeEventListener('keydown', handleKeydown);
          }
        };

        const togglePanel = () => {
          const isExpanded = heading.getAttribute('aria-expanded') === 'true';
          heading.setAttribute('aria-expanded', !isExpanded);
          panel.setAttribute('aria-expanded', !isExpanded);
        };

        const handleKeydown = (e) => {
          if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            togglePanel();
          } else if (e.key === 'Escape' && heading.getAttribute('aria-expanded') === 'true') {
            heading.setAttribute('aria-expanded', 'false');
            panel.setAttribute('aria-expanded', 'false');
          }
        };

        // Initial setup
        applyMobileBehavior(mediaQuery.matches);

        // Listen for viewport changes
        mediaQuery.addEventListener('change', (e) => applyMobileBehavior(e.matches));
      });
    },
  };
})(Drupal, once);
