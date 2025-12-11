/**
 * Card Offer Slide - Auto-fit Text Behavior
 *
 * Dynamically reduces font-size of .ps-card-offer-slide__header
 * to fit content on a single line when it overflows.
 *
 * Ultra-lightweight: ~20 lines, no dependencies, runs once on load.
 */

((Drupal, once) => {
  Drupal.behaviors.psCardOfferSlide = {
    attach(context) {
      const headers = once('psCardOfferSlideAutofit', '.ps-card-offer-slide__header', context);

      headers.forEach((header) => {
        // Get container width
        const containerWidth = header.parentElement.clientWidth;
        const originalFontSize = parseFloat(window.getComputedStyle(header).fontSize);
        // Wrap content in a span to measure natural width
        const wrapper = document.createElement('span');
        wrapper.style.cssText = 'display: inline-block; white-space: nowrap;';
        while (header.firstChild) {
          wrapper.appendChild(header.firstChild);
        }
        header.appendChild(wrapper);
        // Measure natural width
        const naturalWidth = wrapper.offsetWidth;

        // Simple ratio-based calculation
        if (naturalWidth > containerWidth) {
          const ratio = containerWidth / naturalWidth;
          const targetSize = Math.max(8, originalFontSize * ratio * 0.95); // 5% safety margin
          header.style.fontSize = `${targetSize}px`;
        }
      });
    },
  };
})(Drupal, once);
